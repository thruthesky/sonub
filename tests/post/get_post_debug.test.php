<?php
/**
 * get_post() 함수 디버그 - with_user 파라미터 확인
 */

include __DIR__ . '/../../init.php';

if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 get_post() 함수 디버그 - with_user 파라미터\n";
echo "======================================================================\n\n";

try {
    // 테스트 사용자로 로그인
    login_as_test_user('banana');
    $user = login();
    echo "로그인된 사용자: {$user->first_name} {$user->last_name}\n\n";

    // 테스트 게시글 생성
    $testPost = create_post([
        'category' => 'discussion',
        'title' => '테스트 게시글',
        'content' => '사용자 정보 확인용 테스트 게시글',
    ]);

    $postId = $testPost->id;
    echo "테스트 게시글 ID: {$postId}\n\n";

    // ========================================================================
    // 1. with_user=false 호출
    // ========================================================================
    echo "1️⃣ with_user=false 호출:\n";
    $post = get_post(post_id: $postId, with_user: false);
    echo "반환된 PostModel 속성:\n";
    var_dump($post);
    echo "\n";

    // ========================================================================
    // 2. with_user=true 호출
    // ========================================================================
    echo "2️⃣ with_user=true 호출:\n";
    $post = get_post(post_id: $postId, with_user: true);
    echo "반환된 PostModel 속성:\n";
    var_dump($post);
    echo "\n";

    // ========================================================================
    // 3. 데이터베이스 쿼리 직접 확인
    // ========================================================================
    echo "3️⃣ 데이터베이스에서 직접 조회:\n";
    $pdo = pdo();
    $sql = "SELECT p.*, u.first_name, u.photo_url, u.firebase_uid
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$postId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "쿼리 결과:\n";
    var_dump($row);

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
