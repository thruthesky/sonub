<?php
/**
 * 친구 요청 거절 및 취소 테스트
 *
 * 테스트 항목:
 * 1. reject_friend() - 친구 요청 거절
 * 2. cancel_friend_request() - 친구 요청 취소 및 피드 삭제
 *
 * 실행 방법:
 * php tests/friend-and-feed/reject-cancel-friend.test.php
 */

declare(strict_types=1);

// 프로젝트 루트 디렉토리로 이동
$projectRoot = dirname(__DIR__, 2);
chdir($projectRoot);
require_once './init.php';

echo "========================================\n";
echo "친구 요청 거절 및 취소 테스트 시작\n";
echo "========================================\n\n";

/**
 * 테스트 사용자 생성 헬퍼 함수
 */
function create_test_user(string $display_name): array
{
    $pdo = pdo();
    $firebase_uid = 'test_' . uniqid() . '_' . time();
    $created_at = time();

    $sql = "INSERT INTO users (firebase_uid, display_name, created_at, updated_at)
            VALUES (:firebase_uid, :display_name, :created_at, :updated_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':firebase_uid' => $firebase_uid,
        ':display_name' => $display_name,
        ':created_at' => $created_at,
        ':updated_at' => $created_at
    ]);

    $user_id = (int)$pdo->lastInsertId();

    return [
        'id' => $user_id,
        'firebase_uid' => $firebase_uid,
        'display_name' => $display_name,
        'created_at' => $created_at
    ];
}

/**
 * 테스트 사용자 삭제 헬퍼 함수
 */
function delete_test_user(int $user_id): void
{
    $pdo = pdo();
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
}

/**
 * 테스트 게시글 생성 헬퍼 함수
 */
function create_test_post(array $data): array
{
    $pdo = pdo();
    $user_id = $data['user_id'];
    $category = $data['category'] ?? 'test';
    $title = $data['title'] ?? '';
    $content = $data['content'] ?? '';
    $visibility = $data['visibility'] ?? 'public';
    $created_at = time();

    $sql = "INSERT INTO posts (user_id, category, title, content, visibility, created_at, updated_at)
            VALUES (:user_id, :category, :title, :content, :visibility, :created_at, :updated_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':category' => $category,
        ':title' => $title,
        ':content' => $content,
        ':visibility' => $visibility,
        ':created_at' => $created_at,
        ':updated_at' => $created_at
    ]);

    $post_id = (int)$pdo->lastInsertId();

    // 피드 전파 (fanout_post_to_friends 호출)
    fanout_post_to_friends($user_id, $post_id, $created_at);

    return [
        'id' => $post_id,
        'user_id' => $user_id,
        'category' => $category,
        'title' => $title,
        'content' => $content,
        'visibility' => $visibility,
        'created_at' => $created_at
    ];
}

/**
 * 테스트 게시글 삭제 헬퍼 함수
 */
function delete_test_post(int $post_id): void
{
    $pdo = pdo();
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
}

// 테스트 사용자 생성
$user1 = create_test_user('reject_test_user1_' . time());
$user2 = create_test_user('reject_test_user2_' . time());

echo "✅ 테스트 사용자 생성 완료\n";
echo "  - User1 ID: {$user1['id']}, Display Name: {$user1['display_name']}\n";
echo "  - User2 ID: {$user2['id']}, Display Name: {$user2['display_name']}\n\n";

// =============================================================================
// 테스트 1: reject_friend() - 친구 요청 거절
// =============================================================================
echo "--- 테스트 1: reject_friend() ---\n";

// 1단계: User1이 User2에게 친구 요청
request_friend(['me' => $user1['id'], 'other' => $user2['id']]);
echo "✅ User1이 User2에게 친구 요청 보냄 (pending)\n";

// 2단계: User1이 글 작성 (User2 피드에 전파됨)
$post1 = create_test_post([
    'category' => 'test',
    'title' => '테스트 글 1',
    'content' => 'reject 테스트용 글',
    'user_id' => $user1['id'],
    'visibility' => 'public'
]);
echo "✅ User1이 글 작성 (post_id: {$post1['id']})\n";

// 3단계: User2 피드에 User1의 글이 있는지 확인
$pdo = pdo();
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM feed_entries WHERE receiver_id = ? AND post_id = ?");
$stmt->execute([$user2['id'], $post1['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result['count'] > 0) {
    echo "✅ User2 피드에 User1의 글 전파 확인 (pending 상태에서도 전파됨)\n";
} else {
    die("❌ 오류: User2 피드에 User1의 글이 전파되지 않음\n");
}

// 4단계: User2가 친구 요청 거절
reject_friend(['me' => $user2['id'], 'other' => $user1['id']]);
echo "✅ User2가 User1의 친구 요청 거절 (rejected)\n";

// 5단계: 기존 feed_entries는 유지되는지 확인
$stmt->execute([$user2['id'], $post1['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result['count'] > 0) {
    echo "✅ reject 후에도 기존 피드 캐시는 유지됨\n";
} else {
    die("❌ 오류: reject 후 기존 피드 캐시가 삭제됨\n");
}

// 6단계: User1이 새 글 작성
$post2 = create_test_post([
    'category' => 'test',
    'title' => '테스트 글 2',
    'content' => 'reject 후 작성한 글',
    'user_id' => $user1['id'],
    'visibility' => 'public'
]);
echo "✅ User1이 reject 후 새 글 작성 (post_id: {$post2['id']})\n";

// 7단계: User2 피드에 새 글이 전파되지 않았는지 확인
$stmt->execute([$user2['id'], $post2['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result['count'] == 0) {
    echo "✅ reject 후 새 글은 User2 피드에 전파되지 않음\n";
} else {
    die("❌ 오류: reject 후에도 새 글이 전파됨\n");
}

echo "\n";

// =============================================================================
// 테스트 2: cancel_friend_request() - 친구 요청 취소 및 피드 삭제
// =============================================================================
echo "--- 테스트 2: cancel_friend_request() ---\n";

// 테스트 사용자 추가 생성
$user3 = create_test_user('cancel_test_user3_' . time());
$user4 = create_test_user('cancel_test_user4_' . time());

echo "✅ 테스트 사용자 생성 완료\n";
echo "  - User3 ID: {$user3['id']}, Display Name: {$user3['display_name']}\n";
echo "  - User4 ID: {$user4['id']}, Display Name: {$user4['display_name']}\n\n";

// 1단계: User3이 User4에게 친구 요청
request_friend(['me' => $user3['id'], 'other' => $user4['id']]);
echo "✅ User3이 User4에게 친구 요청 보냄 (pending)\n";

// 2단계: User3이 글 작성
$post3 = create_test_post([
    'category' => 'test',
    'title' => '테스트 글 3',
    'content' => 'cancel 테스트용 User3 글',
    'user_id' => $user3['id'],
    'visibility' => 'public'
]);
echo "✅ User3이 글 작성 (post_id: {$post3['id']})\n";

// 3단계: User4도 글 작성
$post4 = create_test_post([
    'category' => 'test',
    'title' => '테스트 글 4',
    'content' => 'cancel 테스트용 User4 글',
    'user_id' => $user4['id'],
    'visibility' => 'public'
]);
echo "✅ User4도 글 작성 (post_id: {$post4['id']})\n";

// 4단계: 서로의 피드에 글이 전파되었는지 확인
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM feed_entries WHERE receiver_id = ? AND post_author_id = ?");

$stmt->execute([$user4['id'], $user3['id']]);
$result1 = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt->execute([$user3['id'], $user4['id']]);
$result2 = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result1['count'] > 0 && $result2['count'] > 0) {
    echo "✅ User3과 User4의 피드에 서로의 글이 전파됨\n";
} else {
    die("❌ 오류: 피드 전파 실패\n");
}

// 5단계: User3이 친구 요청 취소
cancel_friend_request(['me' => $user3['id'], 'other' => $user4['id']]);
echo "✅ User3이 친구 요청 취소 (cancel)\n";

// 6단계: friendship이 삭제되었는지 확인
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM friendships WHERE (user_id_a = ? AND user_id_b = ?) OR (user_id_a = ? AND user_id_b = ?)");
$a = min($user3['id'], $user4['id']);
$b = max($user3['id'], $user4['id']);
$stmt->execute([$a, $b, $a, $b]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    echo "✅ friendship 삭제 확인\n";
} else {
    die("❌ 오류: friendship이 삭제되지 않음\n");
}

// 7단계: feed_entries가 삭제되었는지 확인
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM feed_entries WHERE (receiver_id = ? AND post_author_id = ?) OR (receiver_id = ? AND post_author_id = ?)");
$stmt->execute([$user3['id'], $user4['id'], $user4['id'], $user3['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    echo "✅ feed_entries 삭제 확인 (서로의 피드에서 모두 삭제됨)\n";
} else {
    die("❌ 오류: feed_entries가 삭제되지 않음 (count: {$result['count']})\n");
}

echo "\n";

// =============================================================================
// 테스트 정리
// =============================================================================
echo "--- 테스트 정리 ---\n";

// 테스트 게시글 삭제
delete_test_post($post1['id']);
delete_test_post($post2['id']);
delete_test_post($post3['id']);
delete_test_post($post4['id']);
echo "✅ 테스트 게시글 삭제 완료\n";

// 테스트 사용자 삭제
delete_test_user($user1['id']);
delete_test_user($user2['id']);
delete_test_user($user3['id']);
delete_test_user($user4['id']);
echo "✅ 테스트 사용자 삭제 완료\n";

echo "\n========================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "========================================\n";
