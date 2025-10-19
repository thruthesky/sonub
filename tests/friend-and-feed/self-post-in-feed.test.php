<?php
/**
 * 본인 게시글이 본인 피드에 표시되는지 테스트
 *
 * 테스트 시나리오:
 * 1. 사용자 생성 (Alice)
 * 2. Alice가 게시글 작성
 * 3. fanout_post_to_friends() 함수가 본인에게도 피드 전파하는지 확인 (feed_entries 테이블)
 * 4. get_hybrid_feed() 함수로 본인 피드 조회 시 본인 게시글이 포함되는지 확인
 * 5. 캐시가 비어있을 때도 읽기 조인 경로에서 본인 게시글을 조회하는지 확인
 *
 * 실행 방법:
 * php tests/friend-and-feed/self-post-in-feed.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

/**
 * 테스트 헬퍼 함수: 임시 사용자 생성
 *
 * @param string $firebaseUid Firebase UID
 * @param string $displayName 표시 이름
 * @return int 생성된 사용자 ID
 */
function create_test_user(string $firebaseUid, string $displayName): int
{
    $pdo = pdo();
    $sql = "INSERT INTO users (firebase_uid, display_name, created_at, updated_at)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $now = time();
    $stmt->execute([$firebaseUid, $displayName, $now, $now]);
    return (int)$pdo->lastInsertId();
}

/**
 * 테스트 헬퍼 함수: 임시 게시글 생성 (fanout 포함)
 *
 * @param int $userId 작성자 ID
 * @param string $title 제목
 * @param string $content 내용
 * @return int 생성된 게시글 ID
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

    // fanout_post_to_friends 함수 호출 (본인 포함 피드 전파)
    fanout_post_to_friends($userId, $postId, $now);

    return $postId;
}

/**
 * 테스트 헬퍼 함수: feed_entries 테이블에서 특정 수신자의 피드 확인
 *
 * @param int $receiverId 수신자 ID
 * @param int $postId 게시글 ID
 * @return bool 피드에 존재하면 true
 */
function check_feed_entry_exists(int $receiverId, int $postId): bool
{
    $pdo = pdo();
    $sql = "SELECT 1 FROM feed_entries
            WHERE receiver_id = ? AND post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$receiverId, $postId]);
    return (bool)$stmt->fetchColumn();
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
echo "테스트: 본인 게시글이 본인 피드에 표시되는지 확인\n";
echo "========================================\n\n";

$userIds = [];
$postIds = [];

try {
    // 1단계: 사용자 생성 (Alice)
    echo "[1단계] 사용자 생성 중...\n";
    $aliceId = create_test_user('test_alice_' . time(), 'Alice');
    $userIds[] = $aliceId;
    echo "✅ Alice 생성 완료 (ID: {$aliceId})\n\n";

    // 2단계: Alice가 게시글 작성 (fanout 자동 실행)
    echo "[2단계] Alice가 게시글 작성 중...\n";
    $postId = create_test_post_with_fanout($aliceId, 'Alice의 첫 게시글', '안녕하세요!');
    $postIds[] = $postId;
    echo "✅ 게시글 생성 완료 (ID: {$postId})\n\n";

    // 3단계: feed_entries 테이블에서 본인에게 피드가 전파되었는지 확인
    echo "[3단계] feed_entries 테이블에서 본인 피드 전파 확인 중...\n";
    $feedEntryExists = check_feed_entry_exists($aliceId, $postId);

    if ($feedEntryExists) {
        echo "✅ feed_entries 테이블에 본인 피드가 존재합니다.\n";
        echo "   (receiver_id: {$aliceId}, post_id: {$postId})\n\n";
    } else {
        echo "❌ feed_entries 테이블에 본인 피드가 존재하지 않습니다.\n";
        echo "   fanout_post_to_friends() 함수의 본인 피드 전파 로직을 확인하세요.\n\n";
        throw new Exception('feed_entries 테이블에 본인 피드가 없습니다.');
    }

    // 4단계: get_hybrid_feed() 함수로 본인 피드 조회 (캐시 경로)
    echo "[4단계] get_hybrid_feed() 함수로 본인 피드 조회 (캐시 경로) 중...\n";
    $feed = get_hybrid_feed(['me' => $aliceId, 'limit' => 20, 'offset' => 0]);

    $foundInFeed = false;
    foreach ($feed as $item) {
        if ($item['post_id'] === $postId) {
            $foundInFeed = true;
            break;
        }
    }

    if ($foundInFeed) {
        echo "✅ get_hybrid_feed() 결과에 본인 게시글이 포함되어 있습니다.\n";
        echo "   (post_id: {$postId}, author_id: {$aliceId})\n\n";
    } else {
        echo "❌ get_hybrid_feed() 결과에 본인 게시글이 포함되어 있지 않습니다.\n";
        echo "   피드 조회 결과:\n";
        var_dump($feed);
        throw new Exception('get_hybrid_feed() 결과에 본인 게시글이 없습니다.');
    }

    // 5단계: 캐시 삭제 후 읽기 조인 경로에서 본인 게시글 조회 확인
    echo "[5단계] 캐시 삭제 후 읽기 조인 경로에서 본인 게시글 조회 확인 중...\n";

    // 캐시 삭제
    $pdo = pdo();
    $sql = "DELETE FROM feed_entries WHERE receiver_id = ? AND post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$aliceId, $postId]);
    echo "   - feed_entries 캐시 삭제 완료\n";

    // 캐시가 비어있는 상태에서 get_hybrid_feed() 호출
    $feedAfterCacheClear = get_hybrid_feed(['me' => $aliceId, 'limit' => 20, 'offset' => 0]);

    $foundInFeedAfterCacheClear = false;
    foreach ($feedAfterCacheClear as $item) {
        if ($item['post_id'] === $postId) {
            $foundInFeedAfterCacheClear = true;
            break;
        }
    }

    if ($foundInFeedAfterCacheClear) {
        echo "✅ 캐시가 비어있을 때도 읽기 조인 경로에서 본인 게시글을 조회합니다.\n";
        echo "   (post_id: {$postId}, author_id: {$aliceId})\n\n";
    } else {
        echo "❌ 캐시가 비어있을 때 읽기 조인 경로에서 본인 게시글을 조회하지 못했습니다.\n";
        echo "   get_hybrid_feed() 함수의 friend_ids에 본인 ID 추가 로직을 확인하세요.\n";
        echo "   피드 조회 결과:\n";
        var_dump($feedAfterCacheClear);
        throw new Exception('캐시 누락 시 본인 게시글을 조회하지 못했습니다.');
    }

    // 테스트 성공
    echo "========================================\n";
    echo "✅ 모든 테스트 통과!\n";
    echo "========================================\n\n";

    echo "📋 테스트 요약:\n";
    echo "   1. fanout_post_to_friends(): 본인에게 피드 전파 ✅\n";
    echo "   2. get_hybrid_feed() (캐시 경로): 본인 게시글 조회 ✅\n";
    echo "   3. get_hybrid_feed() (읽기 조인 경로): 본인 게시글 조회 ✅\n\n";

    echo "🎯 결론:\n";
    echo "   - 본인이 작성한 게시글이 index.php에서 정상적으로 표시됩니다.\n";
    echo "   - Fan-out on write + 읽기 보충 패턴이 완벽하게 구현되었습니다.\n\n";
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
