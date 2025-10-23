<?php

/**
 * API 스칼라 값 반환 테스트용 함수들
 *
 * 이 파일은 api.php가 단일 값(숫자, 문자열, 불리언)을 자동으로
 * ['data' => 값, 'func' => '함수명'] 형태로 변환하는지 테스트하기 위한 함수들입니다.
 */

/**
 * 테스트용 API 함수: 숫자 리턴
 *
 * api.php가 이 함수의 리턴값을 자동으로 ['data' => 42, 'func' => 'test_get_user_count'] 형태로 변환합니다.
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return int 사용자 수 (42)
 *
 * @example
 * // JavaScript에서 호출
 * const result = await func('test_get_user_count');
 * console.log(result.data);  // 42
 * console.log(result.func);  // 'test_get_user_count'
 */
function test_get_user_count(array $input = []): int
{
    return 42;
}

/**
 * 테스트용 API 함수: 문자열 리턴
 *
 * api.php가 이 함수의 리턴값을 자동으로 ['data' => 'Welcome to Sonub!', 'func' => 'test_get_welcome_message'] 형태로 변환합니다.
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return string 환영 메시지
 *
 * @example
 * // JavaScript에서 호출
 * const result = await func('test_get_welcome_message');
 * console.log(result.data);  // 'Welcome to Sonub!'
 * console.log(result.func);  // 'test_get_welcome_message'
 */
function test_get_welcome_message(array $input = []): string
{
    return 'Welcome to Sonub!';
}

/**
 * 테스트용 API 함수: 불리언 리턴
 *
 * api.php가 이 함수의 리턴값을 자동으로 ['data' => true, 'func' => 'test_check_email_exists'] 형태로 변환합니다.
 *
 * @param array $input 입력 파라미터
 *   - string email: 확인할 이메일 주소 (테스트용으로 무시됨)
 * @return bool 이메일 존재 여부 (테스트용으로 항상 true 리턴)
 *
 * @example
 * // JavaScript에서 호출
 * const result = await func('test_check_email_exists', { email: 'test@example.com' });
 * console.log(result.data);  // true
 * console.log(result.func);  // 'test_check_email_exists'
 */
function test_check_email_exists(array $input): bool
{
    // 테스트용으로 항상 true 리턴
    return true;
}

/**
 * 테스트용 API 함수: 숫자 0 리턴
 *
 * api.php가 이 함수의 리턴값을 자동으로 ['data' => 0, 'func' => 'test_get_zero'] 형태로 변환합니다.
 * 숫자 0이 올바르게 변환되는지 확인하기 위한 테스트 함수입니다.
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return int 숫자 0
 *
 * @example
 * // JavaScript에서 호출
 * const result = await func('test_get_zero');
 * console.log(result.data);  // 0
 * console.log(result.func);  // 'test_get_zero'
 */
function test_get_zero(array $input = []): int
{
    return 0;
}

/**
 * 테스트용 API 함수: 빈 문자열 리턴
 *
 * api.php가 이 함수의 리턴값을 자동으로 ['data' => '', 'func' => 'test_get_empty_string'] 형태로 변환합니다.
 * 빈 문자열이 올바르게 변환되는지 확인하기 위한 테스트 함수입니다.
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return string 빈 문자열
 *
 * @example
 * // JavaScript에서 호출
 * const result = await func('test_get_empty_string');
 * console.log(result.data);  // ''
 * console.log(result.func);  // 'test_get_empty_string'
 */
function test_get_empty_string(array $input = []): string
{
    return '';
}

/**
 * 테스트용 API 함수: false 리턴
 *
 * api.php가 이 함수의 리턴값을 자동으로 ['data' => false, 'func' => 'test_get_false'] 형태로 변환합니다.
 * 불리언 false가 올바르게 변환되는지 확인하기 위한 테스트 함수입니다.
 *
 * @param array $input 입력 파라미터 (사용 안 함)
 * @return bool 불리언 false
 *
 * @example
 * // JavaScript에서 호출
 * const result = await func('test_get_false');
 * console.log(result.data);  // false
 * console.log(result.func);  // 'test_get_false'
 */
function test_get_false(array $input = []): bool
{
    return false;
}
