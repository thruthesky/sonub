<?php
/**
 * create_post() API E2E 테스트 (간소화 버전)
 *
 * 테스트 항목:
 * 1. 로그인 없이 API 호출 (에러 예상)
 * 2. category 없이 API 호출 (에러 예상)
 * 3. category만으로 게시글 생성 API 호출
 * 4. 모든 필드로 게시글 생성 API 호출
 *
 * 주의: 실제 로그인 상태로 테스트하려면 쿠키를 전달해야 합니다.
 */

// init.php 로드 (상대 경로: tests/e2e → ../../)
include_once __DIR__ . '/../../init.php';

echo "=== create_post() API E2E 테스트 시작 ===\n\n";

// API 기본 URL
$base_url = 'https://local.sonub.com/api.php';

// ========================================================================
// 테스트 1: 로그인 없이 API 호출 (에러 예상)
// ========================================================================
echo "테스트 1: 로그인 없이 API 호출\n";

$url = $base_url . '?' . http_build_query([
    'func' => 'create_post',
    'category' => 'test',
    'title' => 'Test Post',
    'content' => 'Test Content',
]);

$response = file_get_contents($url, false, stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    'http' => ['ignore_errors' => true]
]));

$data = json_decode($response, true);

if (isset($data['error_code']) && $data['error_code'] === 'login-required') {
    echo "✅ 통과: 로그인 필수 에러 반환\n";
    echo "   에러 코드: {$data['error_code']}\n";
    echo "   에러 메시지: {$data['error_message']}\n";
} else {
    echo "❌ 실패: 로그인 에러가 발생하지 않음\n";
    echo "   응답: {$response}\n";
}
echo "\n";

// ========================================================================
// 테스트 2: 쿠키 생성 (로그인 상태 만들기)
// ========================================================================
echo "테스트 2: 쿠키 생성 (user_id=1로 로그인)\n";

// DB에서 사용자 조회
$user = db()->select('*')
    ->from('users')
    ->where('id = ?', [1])
    ->first();

if ($user) {
    // 세션 ID 생성
    $session_id = generate_session_id($user);
    echo "✅ 통과: 세션 ID 생성 성공\n";
    echo "   세션 ID: {$session_id}\n";
} else {
    echo "❌ 실패: 사용자 조회 실패 (id=1이 DB에 없음)\n";
    exit(1);
}
echo "\n";

// ========================================================================
// 테스트 3: 로그인 상태에서 category 없이 API 호출 (에러 예상)
// ========================================================================
echo "테스트 3: 로그인 상태에서 category 없이 API 호출\n";

$url = $base_url . '?' . http_build_query([
    'func' => 'create_post',
    'title' => 'Test Post',
    'content' => 'Test Content',
]);

$response = file_get_contents($url, false, stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    'http' => [
        'ignore_errors' => true,
        'header' => "Cookie: " . SESSION_ID . "={$session_id}\r\n"
    ]
]));

$data = json_decode($response, true);

if (isset($data['error_code']) && $data['error_code'] === 'category-required') {
    echo "✅ 통과: category 필수 에러 반환\n";
    echo "   에러 코드: {$data['error_code']}\n";
    echo "   에러 메시지: {$data['error_message']}\n";
} else {
    echo "❌ 실패: category 필수 에러가 발생하지 않음\n";
    echo "   응답: {$response}\n";
}
echo "\n";

// ========================================================================
// 테스트 4: category만으로 게시글 생성 API 호출
// ========================================================================
echo "테스트 4: category만으로 게시글 생성 (title/content 없이)\n";

$url = $base_url . '?' . http_build_query([
    'func' => 'create_post',
    'category' => 'discussion',
]);

$response = file_get_contents($url, false, stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    'http' => [
        'ignore_errors' => true,
        'header' => "Cookie: " . SESSION_ID . "={$session_id}\r\n"
    ]
]));

$data = json_decode($response, true);

if (isset($data['id']) && isset($data['category'])) {
    echo "✅ 통과: category만으로 게시글 생성 성공\n";
    echo "   게시글 ID: {$data['id']}\n";
    echo "   사용자 ID: {$data['user_id']}\n";
    echo "   카테고리: {$data['category']}\n";
    echo "   제목: '{$data['title']}' (빈 문자열)\n";
    echo "   내용: '{$data['content']}' (빈 문자열)\n";
} else {
    echo "❌ 실패: category만으로 게시글 생성 실패\n";
    echo "   응답: {$response}\n";
}
echo "\n";

// ========================================================================
// 테스트 5: 모든 필드로 게시글 생성 API 호출
// ========================================================================
echo "테스트 5: 모든 필드로 게시글 생성\n";

$url = $base_url . '?' . http_build_query([
    'func' => 'create_post',
    'category' => 'qna',
    'title' => '테스트 게시글 제목',
    'content' => '테스트 게시글 내용입니다.',
]);

$response = file_get_contents($url, false, stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    'http' => [
        'ignore_errors' => true,
        'header' => "Cookie: " . SESSION_ID . "={$session_id}\r\n"
    ]
]));

$data = json_decode($response, true);

if (isset($data['id']) && isset($data['category'])) {
    echo "✅ 통과: 모든 필드로 게시글 생성 성공\n";
    echo "   게시글 ID: {$data['id']}\n";
    echo "   사용자 ID: {$data['user_id']}\n";
    echo "   카테고리: {$data['category']}\n";
    echo "   제목: {$data['title']}\n";
    echo "   내용: {$data['content']}\n";
} else {
    echo "❌ 실패: 모든 필드로 게시글 생성 실패\n";
    echo "   응답: {$response}\n";
}
echo "\n";

echo "=== 모든 테스트 완료 ===\n";
