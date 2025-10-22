<?php

/**
 * fanout_to_follower() 함수 테스트
 *
 * 테스트 시나리오:
 * 1. 사용자 A(follower)와 B(followed) 생성
 * 2. B가 여러 개의 게시글 작성 (예: 5개)
 * 3. A가 B에게 친구 요청 전송 (pending 상태)
 * 4. fanout_to_follower(A, B) 함수 호출
 * 5. A의 feed_entries에 B의 최근 글들이 전파되었는지 확인
 * 6. get_feed_entries()로 A의 피드 조회 시 B의 글들이 포함되는지 확인
 * 7. 최대 100개 제한 테스트 (B가 150개 글 작성 시 100개만 전파되는지 확인)
 *
 * 실행 방법:
 * php tests/friend-and-feed/fanout-to-follower.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

/**
 * 테스트 헬퍼 함수: 임시 사용자 생성
 *
 * @param string $firebaseUid Firebase UID
 * @param string $firstName 이름
 * @return int 생성된 사용자 ID
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
 * 테스트 헬퍼 함수: 임시 게시글 생성 (fanout 없이)
 *
 * @param int $userId 작성자 ID
 * @param string $title 제목
 * @param string $content 내용
 * @return int 생성된 게시글 ID
 */
function create_test_post_without_fanout(int $userId, string $title, string $content): int
{
    $pdo = pdo();
    $now = time();

    // posts 테이블에만 삽입 (fanout 없음)
    $sql = "INSERT INTO posts (user_id, category, title, content, visibility, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, 'discussion', $title, $content, 'public', $now, $now]);
    $postId = (int)$pdo->lastInsertId();

    return $postId;
}

/**
 * 테스트 헬퍼 함수: feed_entries 테이블에서 특정 수신자의 피드 개수 확인
 *
 * @param int $receiverId 수신자 ID
 * @param int $authorId 작성자 ID
 * @return int 피드 개수
 */
function count_feed_entries(int $receiverId, int $authorId): int
{
    $pdo = pdo();
    $sql = "SELECT COUNT(*) FROM feed_entries
            WHERE receiver_id = ? AND post_author_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$receiverId, $authorId]);
    return (int)$stmt->fetchColumn();
}

/**
 * 테스트 헬퍼 함수: 테스트 데이터 정리
 *
 * @param array $userIds 삭제할 사용자 ID 배열
 * @param array $postIds 삭제할 게시글 ID 배열
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
echo "테스트: fanout_to_follower() 함수 검증\n";
echo "========================================\n\n";

$userIds = [];
$postIds = [];

try {
    // 1단계: 사용자 생성 (A: follower, B: followed)
    echo "[1단계] 사용자 생성 중...\n";
    $aliceId = create_test_user('test_alice_' . time(), 'Alice');
    $bobId = create_test_user('test_bob_' . time(), 'Bob');
    $userIds[] = $aliceId;
    $userIds[] = $bobId;
    echo "✅ Alice (follower) 생성 완료 (ID: {$aliceId})\n";
    echo "✅ Bob (followed) 생성 완료 (ID: {$bobId})\n\n";

    // 2단계: Bob이 여러 개의 게시글 작성 (5개)
    echo "[2단계] Bob이 게시글 5개 작성 중...\n";
    $bobPostIds = [];
    for ($i = 1; $i <= 5; $i++) {
        $postId = create_test_post_without_fanout($bobId, "Bob의 게시글 #{$i}", "내용 #{$i}");
        $postIds[] = $postId;
        $bobPostIds[] = $postId;
    }
    echo "✅ Bob의 게시글 5개 생성 완료\n";
    echo "   게시글 IDs: " . implode(', ', $bobPostIds) . "\n\n";

    // 3단계: Alice가 Bob에게 친구 요청 전송 (pending 상태)
    echo "[3단계] Alice가 Bob에게 친구 요청 전송 중...\n";
    request_friend(['me' => $aliceId, 'other' => $bobId]);
    echo "✅ 친구 요청 전송 완료 (pending 상태)\n\n";

    // 4단계: fanout_to_follower() 함수 호출
    echo "[4단계] fanout_to_follower({$aliceId}, {$bobId}) 함수 호출 중...\n";
    fanout_to_follower($aliceId, $bobId);
    echo "✅ fanout_to_follower() 함수 호출 완료\n\n";

    // 5단계: Alice의 feed_entries에 Bob의 글들이 전파되었는지 확인
    echo "[5단계] Alice의 feed_entries에 Bob의 글들이 전파되었는지 확인 중...\n";
    $feedCount = count_feed_entries($aliceId, $bobId);

    if ($feedCount === 5) {
        echo "✅ Alice의 feed_entries에 Bob의 글 5개가 전파되었습니다.\n";
        echo "   (receiver_id: {$aliceId}, post_author_id: {$bobId}, count: {$feedCount})\n\n";
    } else {
        echo "❌ Alice의 feed_entries에 전파된 글 개수가 예상과 다릅니다.\n";
        echo "   예상: 5개, 실제: {$feedCount}개\n\n";
        throw new Exception("전파된 글 개수가 예상과 다릅니다.");
    }

    // 6단계: get_feed_entries()로 Alice의 피드 조회 시 Bob의 글들이 포함되는지 확인
    echo "[6단계] get_feed_entries()로 Alice의 피드 조회 중...\n";
    $feed = get_feed_entries(['me' => $aliceId, 'limit' => 20, 'offset' => 0]);

    $bobPostCount = 0;
    foreach ($feed as $item) {
        if ($item['author_id'] === $bobId) {
            $bobPostCount++;
        }
    }

    if ($bobPostCount === 5) {
        echo "✅ get_feed_entries() 결과에 Bob의 글 5개가 포함되어 있습니다.\n";
        echo "   (author_id: {$bobId}, count: {$bobPostCount})\n\n";
    } else {
        echo "❌ get_feed_entries() 결과에 Bob의 글 개수가 예상과 다릅니다.\n";
        echo "   예상: 5개, 실제: {$bobPostCount}개\n";
        echo "   피드 조회 결과:\n";
        var_dump($feed);
        throw new Exception("피드에 포함된 Bob의 글 개수가 예상과 다릅니다.");
    }

    // 7단계: 최대 100개 제한 테스트
    echo "[7단계] 최대 100개 제한 테스트 중...\n";
    echo "   Charlie가 150개 글 작성 후 fanout_to_follower() 호출 시 100개만 전파되는지 확인\n";

    // Charlie 생성
    $charlieId = create_test_user('test_charlie_' . time(), 'Charlie');
    $userIds[] = $charlieId;
    echo "   - Charlie 생성 완료 (ID: {$charlieId})\n";

    // Charlie가 150개 글 작성
    echo "   - Charlie가 게시글 150개 작성 중... (시간이 걸릴 수 있습니다)\n";
    $charliePostIds = [];
    for ($i = 1; $i <= 150; $i++) {
        $postId = create_test_post_without_fanout($charlieId, "Charlie의 게시글 #{$i}", "내용 #{$i}");
        $postIds[] = $postId;
        $charliePostIds[] = $postId;

        // 진행 상황 표시 (매 30개마다)
        if ($i % 30 === 0) {
            echo "   - {$i}/150 게시글 작성 완료...\n";
        }
    }
    echo "   - Charlie의 게시글 150개 생성 완료\n";

    // Alice가 Charlie에게 친구 요청 전송
    request_friend(['me' => $aliceId, 'other' => $charlieId]);
    echo "   - Alice가 Charlie에게 친구 요청 전송 완료\n";

    // fanout_to_follower() 호출
    fanout_to_follower($aliceId, $charlieId);
    echo "   - fanout_to_follower({$aliceId}, {$charlieId}) 함수 호출 완료\n";

    // Alice의 feed_entries에 Charlie의 글이 100개만 전파되었는지 확인
    $charlieFeedCount = count_feed_entries($aliceId, $charlieId);

    if ($charlieFeedCount === 100) {
        echo "✅ Alice의 feed_entries에 Charlie의 글 100개만 전파되었습니다.\n";
        echo "   (최대 100개 제한이 정상 작동함)\n";
        echo "   (receiver_id: {$aliceId}, post_author_id: {$charlieId}, count: {$charlieFeedCount})\n\n";
    } else {
        echo "❌ Alice의 feed_entries에 전파된 Charlie의 글 개수가 예상과 다릅니다.\n";
        echo "   예상: 100개, 실제: {$charlieFeedCount}개\n\n";
        throw new Exception("최대 100개 제한이 작동하지 않습니다.");
    }

    // 테스트 성공
    echo "========================================\n";
    echo "✅ 모든 테스트 통과!\n";
    echo "========================================\n\n";

    echo "📋 테스트 요약:\n";
    echo "   1. fanout_to_follower(): 기본 동작 (5개 글 전파) ✅\n";
    echo "   2. get_feed_entries(): follower 피드 조회 ✅\n";
    echo "   3. fanout_to_follower(): 최대 100개 제한 ✅\n\n";

    echo "🎯 결론:\n";
    echo "   - follower가 followed의 최근 글을 자신의 피드에 가져올 수 있습니다.\n";
    echo "   - 최대 100개 제한이 정상적으로 작동합니다.\n";
    echo "   - pending 상태에서 follower는 followed의 글을 볼 수 있습니다.\n\n";
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
