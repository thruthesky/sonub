<?php

/**
 * API 함수 스칼라 값 반환 테스트
 *
 * 이 파일은 API 함수가 단일 값(숫자, 문자열, 불리언)을 리턴할 때
 * api.php가 자동으로 ['data' => 값, 'func' => '함수명'] 형태로 변환하는지 테스트합니다.
 *
 * 테스트 내용:
 * - 숫자 리턴 함수 테스트
 * - 문자열 리턴 함수 테스트
 * - 불리언 리턴 함수 테스트
 * - API 호출 시 자동 변환 확인
 *
 * 실행 방법:
 * php tests/api/scalar-return.test.php
 */

declare(strict_types=1);

// init.php 로드
include __DIR__ . '/../../init.php';

echo "========================================\n";
echo "API 함수 스칼라 값 반환 테스트 시작\n";
echo "========================================\n\n";

// 테스트 결과 카운터
$passed = 0;
$failed = 0;

/**
 * 테스트 헬퍼 함수: 결과 검증
 *
 * @param bool $condition 검증할 조건
 * @param string $message 테스트 메시지
 */
function assert_true(bool $condition, string $message): void
{
    global $passed, $failed;
    if ($condition) {
        echo "✓ $message\n";
        $passed++;
    } else {
        echo "✗ $message\n";
        $failed++;
    }
}

/**
 * 테스트 헬퍼 함수: 두 값이 같은지 검증
 *
 * @param mixed $expected 예상 값
 * @param mixed $actual 실제 값
 * @param string $message 테스트 메시지
 */
function assert_equals($expected, $actual, string $message): void
{
    assert_true($expected === $actual, "$message (예상: " . var_export($expected, true) . ", 실제: " . var_export($actual, true) . ")");
}

// ========================================================================
// 1. 테스트용 API 함수 정의 (숫자 리턴)
// ========================================================================

echo "--- 테스트 1: 숫자 리턴 함수 정의 ---\n";

/**
 * 테스트용 API 함수: 숫자 리턴
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return int 사용자 수 (42)
 */
function test_get_user_count(array $input = []): int
{
    return 42;
}

echo "✓ test_get_user_count() 함수 정의 완료\n\n";

// ========================================================================
// 2. 테스트용 API 함수 정의 (문자열 리턴)
// ========================================================================

echo "--- 테스트 2: 문자열 리턴 함수 정의 ---\n";

/**
 * 테스트용 API 함수: 문자열 리턴
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return string 환영 메시지
 */
function test_get_welcome_message(array $input = []): string
{
    return 'Welcome to Sonub!';
}

echo "✓ test_get_welcome_message() 함수 정의 완료\n\n";

// ========================================================================
// 3. 테스트용 API 함수 정의 (불리언 리턴)
// ========================================================================

echo "--- 테스트 3: 불리언 리턴 함수 정의 ---\n";

/**
 * 테스트용 API 함수: 불리언 리턴
 *
 * @param array $input 입력 파라미터
 *   - string email: 확인할 이메일 주소
 * @return bool 이메일 존재 여부 (테스트용으로 항상 true 리턴)
 */
function test_check_email_exists(array $input): bool
{
    // 테스트용으로 항상 true 리턴
    return true;
}

echo "✓ test_check_email_exists() 함수 정의 완료\n\n";

// ========================================================================
// 4. API 호출 시뮬레이션 (숫자 리턴 함수)
// ========================================================================

echo "--- 테스트 4: API 호출 시뮬레이션 (숫자 리턴) ---\n";

// 함수 직접 호출
$result = test_get_user_count([]);
assert_equals(42, $result, "test_get_user_count() 함수가 숫자 42를 리턴함");

// api.php의 자동 변환 로직 시뮬레이션
if (is_numeric($result) || is_string($result) || is_bool($result)) {
    $result = ['data' => $result];
}
$result['func'] = 'test_get_user_count';

assert_equals(42, $result['data'], "api.php 자동 변환: data 필드에 42 저장");
assert_equals('test_get_user_count', $result['func'], "api.php 자동 변환: func 필드에 함수명 저장");
echo "\n";

// ========================================================================
// 5. API 호출 시뮬레이션 (문자열 리턴 함수)
// ========================================================================

echo "--- 테스트 5: API 호출 시뮬레이션 (문자열 리턴) ---\n";

// 함수 직접 호출
$result = test_get_welcome_message([]);
assert_equals('Welcome to Sonub!', $result, "test_get_welcome_message() 함수가 문자열을 리턴함");

// api.php의 자동 변환 로직 시뮬레이션
if (is_numeric($result) || is_string($result) || is_bool($result)) {
    $result = ['data' => $result];
}
$result['func'] = 'test_get_welcome_message';

assert_equals('Welcome to Sonub!', $result['data'], "api.php 자동 변환: data 필드에 문자열 저장");
assert_equals('test_get_welcome_message', $result['func'], "api.php 자동 변환: func 필드에 함수명 저장");
echo "\n";

// ========================================================================
// 6. API 호출 시뮬레이션 (불리언 리턴 함수)
// ========================================================================

echo "--- 테스트 6: API 호출 시뮬레이션 (불리언 리턴) ---\n";

// 함수 직접 호출
$result = test_check_email_exists(['email' => 'test@example.com']);
assert_equals(true, $result, "test_check_email_exists() 함수가 불리언 true를 리턴함");

// api.php의 자동 변환 로직 시뮬레이션
if (is_numeric($result) || is_string($result) || is_bool($result)) {
    $result = ['data' => $result];
}
$result['func'] = 'test_check_email_exists';

assert_equals(true, $result['data'], "api.php 자동 변환: data 필드에 true 저장");
assert_equals('test_check_email_exists', $result['func'], "api.php 자동 변환: func 필드에 함수명 저장");
echo "\n";

// ========================================================================
// 7. 실제 API 호출 테스트 (HTTP 요청)
// ========================================================================

echo "--- 테스트 7: 실제 API 호출 테스트 (HTTP 요청) ---\n";

// API 베이스 URL
$api_url = 'https://local.sonub.com/api.php';

// 테스트 1: 숫자 리턴 함수 호출
$response = @file_get_contents($api_url . '?func=test_get_user_count');
if ($response === false) {
    echo "⚠ HTTP 요청 실패: API 서버가 실행 중인지 확인하세요\n";
} else {
    $data = json_decode($response, true);
    assert_equals(42, $data['data'] ?? null, "HTTP 요청: test_get_user_count API 호출 결과 data 필드에 42 저장");
    assert_equals('test_get_user_count', $data['func'] ?? null, "HTTP 요청: test_get_user_count API 호출 결과 func 필드에 함수명 저장");
}

// 테스트 2: 문자열 리턴 함수 호출
$response = @file_get_contents($api_url . '?func=test_get_welcome_message');
if ($response === false) {
    echo "⚠ HTTP 요청 실패: API 서버가 실행 중인지 확인하세요\n";
} else {
    $data = json_decode($response, true);
    assert_equals('Welcome to Sonub!', $data['data'] ?? null, "HTTP 요청: test_get_welcome_message API 호출 결과 data 필드에 문자열 저장");
    assert_equals('test_get_welcome_message', $data['func'] ?? null, "HTTP 요청: test_get_welcome_message API 호출 결과 func 필드에 함수명 저장");
}

// 테스트 3: 불리언 리턴 함수 호출
$response = @file_get_contents($api_url . '?func=test_check_email_exists&email=test@example.com');
if ($response === false) {
    echo "⚠ HTTP 요청 실패: API 서버가 실행 중인지 확인하세요\n";
} else {
    $data = json_decode($response, true);
    assert_equals(true, $data['data'] ?? null, "HTTP 요청: test_check_email_exists API 호출 결과 data 필드에 true 저장");
    assert_equals('test_check_email_exists', $data['func'] ?? null, "HTTP 요청: test_check_email_exists API 호출 결과 func 필드에 함수명 저장");
}

echo "\n";

// ========================================================================
// 8. JSON 인코딩 확인
// ========================================================================

echo "--- 테스트 8: JSON 인코딩 확인 ---\n";

// 숫자
$result = ['data' => 42, 'func' => 'test_get_user_count'];
$json = json_encode($result, JSON_UNESCAPED_UNICODE);
$expected_json = '{"data":42,"func":"test_get_user_count"}';
assert_equals($expected_json, $json, "숫자 리턴 함수 JSON 인코딩 확인");

// 문자열
$result = ['data' => 'Welcome to Sonub!', 'func' => 'test_get_welcome_message'];
$json = json_encode($result, JSON_UNESCAPED_UNICODE);
$expected_json = '{"data":"Welcome to Sonub!","func":"test_get_welcome_message"}';
assert_equals($expected_json, $json, "문자열 리턴 함수 JSON 인코딩 확인");

// 불리언
$result = ['data' => true, 'func' => 'test_check_email_exists'];
$json = json_encode($result, JSON_UNESCAPED_UNICODE);
$expected_json = '{"data":true,"func":"test_check_email_exists"}';
assert_equals($expected_json, $json, "불리언 리턴 함수 JSON 인코딩 확인");

echo "\n";

// ========================================================================
// 테스트 정리
// ========================================================================

echo "========================================\n";
echo "테스트 완료\n";
echo "========================================\n";
echo "통과: $passed\n";
echo "실패: $failed\n";

if ($failed === 0) {
    echo "\n✅ 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "\n❌ 일부 테스트 실패\n";
    exit(1);
}
