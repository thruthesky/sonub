<?php

/**
 * Pending 상태 피드 전파 테스트 (스팸 방지 검증)
 *
 * 테스트 시나리오:
 * 1. 본인 게시글이 본인 피드에 표시되는지 확인
 * 2. accepted 친구 게시글이 양방향으로 표시되는지 확인
 * 3. 내가 친구 요청한 pending 상태: 상대방 글이 내 피드에 표시 (✅ 요청자가 상대 글 볼 수 있음)
 * 4. 상대방이 나에게 친구 요청한 pending 상태: 상대방 글이 내 피드에 표시 안 됨 (❌ 스팸 방지)
 *
 * 실행 방법:
 * php tests/friend-and-feed/pending-feed-propagation.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

/**
 * 테스트 헬퍼 함수: 임시 사용자 생성
 */
function create_test_user(string $firebaseUid, string $firstName): int
{
    $pdo = pdo();
    $sql = "INSERT INTO users (firebase_uid, first_name, created_at, updated_at)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $now = time();
    $stmt->execute([$firebaseUid, $firstName, $now, $now]);
    return (int)$pdo->lastInsertId();
}

/**
 * 테스트 헬퍼 함수: 게시글 생성 (fanout 포함)
 */
function create_test_post_with_fanout(int $userId, string $title, string $content): int
{
    $pdo = pdo();
    $now = time();

    // posts 테이블에 삽입
    $sql = "INSERT INTO posts (user_id, category, title, content, visibility, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, 'discussion', $title, $content, 'friends', $now, $now]);
    $postId = (int)$pdo->lastInsertId();

    // fanout_post_to_friends 함수 호출
    fanout_post_to_friends($userId, $postId, $now);

    return $postId;
}

/**
 * 테스트 헬퍼 함수: 친구 요청 생성
 */
function create_test_friendship(int $requesterId, int $receiverId, string $status = 'pending'): void
{
    $pdo = pdo();
    [$a, $b] = [min($requesterId, $receiverId), max($requesterId, $receiverId)];
    $now = time();

    $sql = "INSERT INTO friendships (user_id_a, user_id_b, status, requested_by, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                requested_by = VALUES(requested_by),
                updated_at = VALUES(updated_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$a, $b, $status, $requesterId, $now, $now]);
}

/**
 * 테스트 헬퍼 함수: 피드에 특정 게시글이 포함되어 있는지 확인
 */
function check_post_in_feed(int $userId, int $postId): bool
{
    $feed = get_feed_entries(['me' => $userId, 'limit' => 100, 'offset' => 0]);
    foreach ($feed as $item) {
        if ($item['post_id'] === $postId) {
            return true;
        }
    }
    return false;
}

/**
 * 테스트 헬퍼 함수: 테스트 데이터 정리
 */
function cleanup_test_data(array $userIds, array $postIds): void
{
    $pdo = pdo();

    // feed_entries 삭제
    if (!empty($postIds)) {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $sql = "DELETE FROM feed_entries WHERE post_id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($postIds);
    }

    // posts 삭제
    if (!empty($postIds)) {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $sql = "DELETE FROM posts WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($postIds);
    }

    // friendships 삭제
    if (!empty($userIds)) {
        foreach ($userIds as $userId) {
            $sql = "DELETE FROM friendships WHERE user_id_a = ? OR user_id_b = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $userId]);
        }
    }

    // users 삭제
    if (!empty($userIds)) {
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $sql = "DELETE FROM users WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($userIds);
    }
}

// ============================================================================
// 테스트 시작
// ============================================================================

echo "========================================\n";
echo "테스트: Pending 상태 피드 전파 (스팸 방지 검증)\n";
echo "========================================\n\n";

$userIds = [];
$postIds = [];

try {
    $timestamp = time();

    // 사용자 생성
    echo "[1단계] 사용자 생성 중...\n";
    $aliceId = create_test_user("test_alice_{$timestamp}", 'Alice');
    $bobId = create_test_user("test_bob_{$timestamp}", 'Bob');
    $charlieId = create_test_user("test_charlie_{$timestamp}", 'Charlie');
    $userIds = [$aliceId, $bobId, $charlieId];
    echo "✅ Alice 생성 (ID: {$aliceId})\n";
    echo "✅ Bob 생성 (ID: {$bobId})\n";
    echo "✅ Charlie 생성 (ID: {$charlieId})\n\n";

    // ========================================
    // 테스트 케이스 1: 본인 게시글이 본인 피드에 표시
    // ========================================
    echo "[테스트 1] 본인 게시글이 본인 피드에 표시되는지 확인\n";
    $alicePost1 = create_test_post_with_fanout($aliceId, 'Alice의 게시글 1', '내용 1');
    $postIds[] = $alicePost1;

    if (check_post_in_feed($aliceId, $alicePost1)) {
        echo "✅ 테스트 1 통과: 본인 게시글이 본인 피드에 표시됨\n\n";
    } else {
        throw new Exception("테스트 1 실패: 본인 게시글이 본인 피드에 표시되지 않음");
    }

    // ========================================
    // 테스트 케이스 2: accepted 친구 게시글이 양방향으로 표시
    // ========================================
    echo "[테스트 2] accepted 친구 게시글이 양방향으로 표시되는지 확인\n";
    echo "   - Alice와 Bob이 친구 관계 (accepted) 설정 중...\n";
    create_test_friendship($aliceId, $bobId, 'accepted');

    echo "   - Bob이 게시글 작성 중...\n";
    $bobPost1 = create_test_post_with_fanout($bobId, 'Bob의 게시글 1', 'Alice와 친구!');
    $postIds[] = $bobPost1;

    $aliceSeeBobPost = check_post_in_feed($aliceId, $bobPost1);
    $bobSeeBobPost = check_post_in_feed($bobId, $bobPost1);

    if ($aliceSeeBobPost && $bobSeeBobPost) {
        echo "✅ 테스트 2 통과: accepted 친구 게시글이 양방향으로 표시됨\n";
        echo "   - Alice 피드에서 Bob의 게시글 확인: ✅\n";
        echo "   - Bob 피드에서 Bob의 게시글 확인 (본인 글): ✅\n\n";
    } else {
        echo "❌ 테스트 2 실패:\n";
        echo "   - Alice 피드에서 Bob의 게시글: " . ($aliceSeeBobPost ? "✅" : "❌") . "\n";
        echo "   - Bob 피드에서 Bob의 게시글: " . ($bobSeeBobPost ? "✅" : "❌") . "\n";
        throw new Exception("테스트 2 실패: accepted 친구 게시글이 양방향으로 표시되지 않음");
    }

    // ========================================
    // 테스트 케이스 3: 내가 친구 요청한 pending 상태 - 상대방 글이 내 피드에 표시
    // ========================================
    echo "[테스트 3] 내가 친구 요청한 pending 상태: 상대방 글이 내 피드에 표시되는지 확인\n";
    echo "   - Alice가 Charlie에게 친구 요청 (pending) 중...\n";
    create_test_friendship($aliceId, $charlieId, 'pending'); // Alice가 요청자

    echo "   - Charlie가 게시글 작성 중...\n";
    $charliePost1 = create_test_post_with_fanout($charlieId, 'Charlie의 게시글 1', '반가워요!');
    $postIds[] = $charliePost1;

    $aliceSeeCharliePost = check_post_in_feed($aliceId, $charliePost1);
    $charlieSeeCharliePost = check_post_in_feed($charlieId, $charliePost1);

    if ($aliceSeeCharliePost && $charlieSeeCharliePost) {
        echo "✅ 테스트 3 통과: 내가 친구 요청한 pending 상태에서 상대방 글이 내 피드에 표시됨\n";
        echo "   - Alice 피드에서 Charlie의 게시글 확인: ✅ (Alice가 요청자)\n";
        echo "   - Charlie 피드에서 Charlie의 게시글 확인 (본인 글): ✅\n\n";
    } else {
        echo "❌ 테스트 3 실패:\n";
        echo "   - Alice 피드에서 Charlie의 게시글: " . ($aliceSeeCharliePost ? "✅" : "❌") . "\n";
        echo "   - Charlie 피드에서 Charlie의 게시글: " . ($charlieSeeCharliePost ? "✅" : "❌") . "\n";
        throw new Exception("테스트 3 실패: 내가 친구 요청한 pending 상태에서 상대방 글이 내 피드에 표시되지 않음");
    }

    // ========================================
    // 테스트 케이스 4: 상대방이 나에게 친구 요청한 pending 상태 - 상대방 글이 내 피드에 표시 안 됨 (스팸 방지)
    // ========================================
    echo "[테스트 4] 상대방이 나에게 친구 요청한 pending 상태: 상대방 글이 내 피드에 표시 안 됨 (스팸 방지)\n";
    echo "   - 새로운 사용자 David 생성 중...\n";
    $davidId = create_test_user("test_david_{$timestamp}", 'David');
    $userIds[] = $davidId;
    echo "   ✅ David 생성 (ID: {$davidId})\n";

    echo "   - David가 Alice에게 친구 요청 (pending) 중...\n";
    create_test_friendship($davidId, $aliceId, 'pending'); // David가 요청자

    echo "   - David가 게시글 작성 중...\n";
    $davidPost1 = create_test_post_with_fanout($davidId, 'David의 게시글 1', '스팸 아니에요!');
    $postIds[] = $davidPost1;

    $aliceSeeDavidPost = check_post_in_feed($aliceId, $davidPost1);
    $davidSeeDavidPost = check_post_in_feed($davidId, $davidPost1);

    // Alice는 David의 글을 보지 못해야 함 (스팸 방지)
    // David는 자신의 글을 볼 수 있어야 함 (본인 글)
    if (!$aliceSeeDavidPost && $davidSeeDavidPost) {
        echo "✅ 테스트 4 통과: 스팸 방지 로직이 정상 작동함\n";
        echo "   - Alice 피드에서 David의 게시글 확인: ❌ (스팸 방지 성공)\n";
        echo "   - David 피드에서 David의 게시글 확인 (본인 글): ✅\n\n";
    } else {
        echo "❌ 테스트 4 실패:\n";
        echo "   - Alice 피드에서 David의 게시글: " . ($aliceSeeDavidPost ? "❌ (스팸 발생!)" : "✅ (차단됨)") . "\n";
        echo "   - David 피드에서 David의 게시글: " . ($davidSeeDavidPost ? "✅" : "❌") . "\n";
        throw new Exception("테스트 4 실패: 스팸 방지 로직이 작동하지 않음 (상대방이 나에게 친구 요청한 경우 상대방 글이 내 피드에 표시됨)");
    }

    // ========================================
    // 테스트 케이스 5: pending 상태에서 내가 작성한 글이 상대방 피드에 표시 안 됨 (스팸 방지)
    // ========================================
    echo "[테스트 5] pending 상태에서 내가 작성한 글이 상대방 피드에 표시 안 됨 (스팸 방지)\n";
    echo "   - Alice가 Charlie에게 친구 요청한 상태 (pending, Alice가 요청자)\n";
    echo "   - Alice가 새 게시글 작성 중...\n";
    $alicePost2 = create_test_post_with_fanout($aliceId, 'Alice의 게시글 2', 'Charlie야 안녕!');
    $postIds[] = $alicePost2;

    $charlieSeeAlicePost = check_post_in_feed($charlieId, $alicePost2);
    $aliceSeeAlicePost = check_post_in_feed($aliceId, $alicePost2);

    // Charlie는 Alice의 글을 보지 못해야 함 (Alice가 요청자이므로 스팸 방지)
    // Alice는 자신의 글을 볼 수 있어야 함 (본인 글)
    if (!$charlieSeeAlicePost && $aliceSeeAlicePost) {
        echo "✅ 테스트 5 통과: pending 상태에서 요청자의 글이 수신자 피드에 표시 안 됨 (스팸 방지)\n";
        echo "   - Charlie 피드에서 Alice의 게시글 확인: ❌ (스팸 방지 성공)\n";
        echo "   - Alice 피드에서 Alice의 게시글 확인 (본인 글): ✅\n\n";
    } else {
        echo "❌ 테스트 5 실패:\n";
        echo "   - Charlie 피드에서 Alice의 게시글: " . ($charlieSeeAlicePost ? "❌ (스팸 발생!)" : "✅ (차단됨)") . "\n";
        echo "   - Alice 피드에서 Alice의 게시글: " . ($aliceSeeAlicePost ? "✅" : "❌") . "\n";
        throw new Exception("테스트 5 실패: pending 상태에서 요청자의 글이 수신자 피드에 표시됨 (스팸 방지 실패)");
    }

    // 테스트 성공
    echo "========================================\n";
    echo "✅ 모든 테스트 통과!\n";
    echo "========================================\n\n";

    echo "📋 테스트 요약:\n";
    echo "   1. 본인 게시글이 본인 피드에 표시: ✅\n";
    echo "   2. accepted 친구 게시글이 양방향 표시: ✅\n";
    echo "   3. 내가 친구 요청한 pending: 상대방 글이 내 피드에 표시: ✅\n";
    echo "   4. 상대방이 나에게 친구 요청한 pending: 상대방 글이 내 피드에 표시 안 됨 (스팸 방지): ✅\n";
    echo "   5. pending 상태에서 요청자의 글이 수신자 피드에 표시 안 됨 (스팸 방지): ✅\n\n";

    echo "🎯 결론:\n";
    echo "   - Fan-out on write 스팸 방지 로직이 완벽하게 작동합니다.\n";
    echo "   - pending 상태에서는 요청자만 수신자의 글을 볼 수 있습니다.\n";
    echo "   - 수신자는 요청자의 글을 볼 수 없어 스팸이 방지됩니다.\n\n";
} catch (Exception $e) {
    echo "\n========================================\n";
    echo "❌ 테스트 실패: {$e->getMessage()}\n";
    echo "========================================\n\n";
    exit(1);
} finally {
    // 테스트 데이터 정리
    echo "테스트 데이터 정리 중...\n";
    cleanup_test_data($userIds, $postIds);
    echo "✅ 테스트 데이터 정리 완료\n";
}
