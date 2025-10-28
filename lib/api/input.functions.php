<?php

/**
 * @file lib/input.functions.php
 * @brief Input handling functions for the API.
 *
 * This file contains functions to handle input from the client, including merging
 * JSON input with POST/GET data.
 */


// Get JSON input
$__json         = @file_get_contents('php://input');
$__decoded_json = json_decode($__json, true);

// Merge POST/GET with JSON data, with proper null checking
$__in = [];
if ($__decoded_json !== null) {
    $__in = array_merge($_REQUEST, $__decoded_json);
} else {
    $__in = $_REQUEST;
}


/**
 * HTTP 입력 데이터에 값을 주입(추가 또는 덮어쓰기)합니다.
 *
 * 클라이언트로부터 받은 HTTP 입력(POST/GET/JSON)을 합병한 전역 변수 $__in에
 * 특정 키-값 쌍을 추가하거나 기존 값을 덮어씁니다.
 *
 * **사용 사례:**
 * - 테스트에서 특정 입력값을 설정할 때
 * - 미들웨어에서 요청 데이터를 추가로 주입할 때
 * - 기본값이나 계산된 값을 입력에 추가할 때
 *
 * **예제:**
 * ```php
 * // 클라이언트 입력에 user_id 추가
 * inject_http_input('user_id', 123);
 * $user_id = http_param('user_id');  // 123 반환
 *
 * // 기존 값 덮어쓰기
 * inject_http_input('category', 'updated-category');
 * $category = http_param('category');  // 'updated-category' 반환
 * ```
 *
 * @param string $name 주입할 입력 파라미터의 이름
 * @param mixed $value 주입할 값 (문자열, 숫자, 배열, null 등 모든 타입 가능)
 * @return void
 *
 * @see http_params() HTTP 입력에서 값을 조회
 * @see http_param() 기본값과 함께 HTTP 입력 조회
 */
function inject_http_input($name, $value): void
{
    global $__in;
    $__in[$name] = $value;
}



/**
 * HTTP 입력(POST/GET/JSON)에서 파라미터 값을 조회합니다.
 *
 * 클라이언트로부터 받은 모든 HTTP 입력을 합병한 전역 변수 $__in에서
 * 지정된 파라미터의 값을 반환합니다.
 *
 * **주의사항:**
 * - 문자열 'null', 'undefined', 빈 문자열('')은 null로 변환됨
 * - 문자열 'false', '0'은 그대로 반환됨 (falsy하지 않음)
 * - null 파라미터로 호출하면 전체 HTTP 입력 배열 반환
 *
 * **반환값:**
 * - 파라미터명 미지정: 전체 HTTP 입력 배열
 * - 파라미터 존재 + 유효한 값: 해당 값
 * - 파라미터 존재하나 'null'/'undefined'/''인 경우: null
 * - 파라미터 미존재: null
 *
 * **예제:**
 * ```php
 * // URL: /api.php?category=discussion&user_id=123
 * http_params('category');           // 'discussion'
 * http_params('user_id');            // '123' (문자열)
 * http_params('nonexistent');        // null
 * http_params('param?user_id=null'); // null (JSON에서 'null' 문자열로 전송된 경우)
 * http_params();                     // 전체 입력 배열
 * ```
 *
 * @param string $name 조회할 파라미터 이름. 빈 문자열이면 전체 입력 배열 반환
 * @return mixed 파라미터 값 또는 null. 파라미터명 미지정 시 배열
 *
 * @see inject_http_input() HTTP 입력에 값 주입
 * @see http_param() 기본값과 함께 HTTP 입력 조회
 */
function http_params(string $name = ''): mixed
{
    global $__in;

    if ($name === '') {
        return $__in;
    } else {
        if (isset($__in[$name])) {
            // 특정 문자열 값들은 null로 변환 (JSON null이 문자열 'null'로 전송되는 경우 처리)
            if ($__in[$name] === 'null' || $__in[$name] === 'undefined' || $__in[$name] === '') {
                return null;
            }
        }
        return $__in[$name] ?? null;
    }
}

/**
 * HTTP 입력에서 파라미터 값을 조회하며, 기본값을 제공합니다.
 *
 * http_params()와 동일하게 동작하되, 파라미터가 null인 경우 기본값을 반환합니다.
 *
 * **예제:**
 * ```php
 * // URL: /api.php?category=discussion
 * http_param('category');                        // 'discussion'
 * http_param('nonexistent');                     // null
 * http_param('nonexistent', 'default-category'); // 'default-category'
 * http_param('user_id', 1);                      // 1 (user_id가 없거나 null인 경우)
 * ```
 *
 * @param string $name 조회할 파라미터 이름. 빈 문자열이면 전체 입력 배열
 * @param mixed $default_value 파라미터가 null일 때 반환할 기본값
 * @return mixed 파라미터 값 또는 기본값
 *
 * @see http_params() 기본값 없이 HTTP 입력 조회
 */
function http_param(string $name = '', mixed $default_value = null): mixed
{
    return http_params($name) ?? $default_value;
}


/**
 * API 내부 함수 호출 시 사용할 HTTP 파라미터 배열을 반환합니다.
 *
 * inter_params()는 http_params()와 동일한 기능을 수행하며,
 * API 내부 함수 호출 시 모든 HTTP 파라미터를 배열 형태로 반환합니다.
 *
 * **예제:**
 * ```php
 * $params = inter_params();
 * $category = $params['category'] ?? 'default-category';
 * ```
 *
 * @return array 모든 HTTP 파라미터를 포함하는 연관 배열
 *
 * @see http_params() HTTP 입력에서 값을 조회
 */
function inter_params(): array
{
    return http_params();
}
