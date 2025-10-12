<?php
/**
 * create_post() 함수 검증 테스트
 *
 * 테스트 항목:
 * 1. 로그인 없이 호출 시 에러 반환
 * 2. category 없이 호출 시 에러 반환
 * 3. category만으로 게시글 생성 (title/content 없이)
 * 4. 모든 필드로 게시글 생성
 */

// init.php 로드 (상대 경로: tests/post → ../../)
include_once __DIR__ . '/../../init.php';

echo "=== create_post() 함수 검증 테스트 시작 ===\n\n";

// ========================================================================
// 테스트 1: 로그인 없이 호출 (에러 예상)
// ========================================================================
echo "테스트 1: 로그인 없이 호출\n";
$result = create_post(['category' => 'test', 'title' => 'Test', 'content' => 'Test']);

if (isset($result['error_code']) && $result['error_code'] === 'login-required') {
    echo "✅ 통과: 로그인 필수 에러 반환\n";
    echo "   에러 코드: {$result['error_code']}\n";
    echo "   에러 메시지: {$result['error_message']}\n";
} else {
    echo "❌ 실패: 로그인 에러가 발생하지 않음\n";
    echo "   결과: " . print_r($result, true) . "\n";
}
echo "\n";

// ========================================================================
// 테스트 2: 기존 사용자로 로그인 (임시 테스트용)
// ========================================================================
echo "테스트 2: 기존 사용자로 로그인 (user_id=1)\n";

// DB에서 사용자 조회
$user = db()->select('*')
    ->from('users')
    ->where('id = ?', [1])
    ->first();

if ($user) {
    // 배열을 UserModel 객체로 변환
    $user_model = new UserModel($user);

    echo "✅ 통과: 기존 사용자 조회 성공\n";
    echo "   사용자 ID: {$user_model->id}\n";

    // 세션 ID 생성 및 쿠키 설정 (login() 함수가 인식하도록)
    $session_id = generate_session_id($user);
    $_COOKIE[SESSION_ID] = $session_id;

    // login() 함수 테스트
    $logged_in_user = login();
    if ($logged_in_user) {
        echo "   로그인 성공: {$logged_in_user->id}\n";
    } else {
        echo "❌ 실패: 로그인 함수가 사용자를 인식하지 못함\n";
        exit(1);
    }
} else {
    echo "❌ 실패: 사용자 조회 실패 (id=1이 DB에 없음)\n";
    echo "   users 테이블에 최소 1개의 사용자가 필요합니다.\n";
    exit(1);
}
echo "\n";

// ========================================================================
// 테스트 3: category 없이 호출 (에러 예상)
// ========================================================================
echo "테스트 3: category 없이 호출\n";
$result = create_post(['title' => 'Test', 'content' => 'Test']);

if (isset($result['error_code']) && $result['error_code'] === 'category-required') {
    echo "✅ 통과: category 필수 에러 반환\n";
    echo "   에러 코드: {$result['error_code']}\n";
    echo "   에러 메시지: {$result['error_message']}\n";
} else {
    echo "❌ 실패: category 필수 에러가 발생하지 않음\n";
    echo "   결과: " . print_r($result, true) . "\n";
}
echo "\n";

// ========================================================================
// 테스트 4: category만으로 게시글 생성 (title/content 없이)
// ========================================================================
echo "테스트 4: category만으로 게시글 생성\n";
$result = create_post(['category' => 'discussion']);

if ($result instanceof PostModel) {
    echo "✅ 통과: category만으로 게시글 생성 성공\n";
    echo "   게시글 ID: {$result->id}\n";
    echo "   사용자 ID: {$result->user_id}\n";
    echo "   카테고리: {$result->category}\n";
    echo "   제목: '{$result->title}' (빈 문자열)\n";
    echo "   내용: '{$result->content}' (빈 문자열)\n";
    echo "   생성 시간: {$result->created_at} (" . date('Y-m-d H:i:s', $result->created_at) . ")\n";
} else {
    echo "❌ 실패: category만으로 게시글 생성 실패\n";
    echo "   결과: " . print_r($result, true) . "\n";
}
echo "\n";

// ========================================================================
// 테스트 5: 모든 필드로 게시글 생성
// ========================================================================
echo "테스트 5: 모든 필드로 게시글 생성\n";
$result = create_post([
    'category' => 'qna',
    'title' => '테스트 게시글 제목',
    'content' => '테스트 게시글 내용입니다.',
]);

if ($result instanceof PostModel) {
    echo "✅ 통과: 모든 필드로 게시글 생성 성공\n";
    echo "   게시글 ID: {$result->id}\n";
    echo "   사용자 ID: {$result->user_id}\n";
    echo "   카테고리: {$result->category}\n";
    echo "   제목: {$result->title}\n";
    echo "   내용: {$result->content}\n";
    echo "   생성 시간: {$result->created_at} (" . date('Y-m-d H:i:s', $result->created_at) . ")\n";
} else {
    echo "❌ 실패: 모든 필드로 게시글 생성 실패\n";
    echo "   결과: " . print_r($result, true) . "\n";
}
echo "\n";

echo "=== 모든 테스트 완료 ===\n";
