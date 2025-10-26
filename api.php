<?php

/**
 * API 엔드포인트
 * - HTTP 요청에서 'func' 파라미터를 읽어 해당 함수를 호출하고 JSON 응답을 반환합니다.
 * - 함수가 존재하지 않거나 허용되지 않은 경우, 적절한 에러 응답을 반환합니다.
 * - 함수 호출 시 모든 HTTP 파라미터를 배열로 전달합니다.
 * - 함수가 숫자, 문자열, 불리언을 리턴한 경우, 이를 배열의 'data' 속성으로 감싸서 일관된 JSON 응답을 제공합니다.
 * - 함수가 Model 객체를 리턴한 경우, toArray() 메서드를 호출하여 배열로 변환 후 JSON 인코딩합니다.
 * - ApiException이 throw된 경우, 이를 catch하여 JSON 에러 응답으로 변환합니다.
 * 참고: lib/etc/api_exception.php
 * 참고: [Sonub API 문서](./.claude/skills/sonub-api/SKILL.md)
 * 
 * 
 */
const API_CALL = true;
const ROOT_DIR = __DIR__;
include_once ROOT_DIR . '/etc/includes.php';
header('Content-Type: application/json; charset=utf-8');

$func_name = http_params('func');
if ($func_name === null) {
    http_response_code(400);
    $error_response = [
        'error_code' => 'no-function-specified',
        'error_message' => 'Function name is required',
        'error_data' => [],
        'error_response_code' => 400
    ];
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
    exit;
}
if (!function_exists($func_name)) {
    http_response_code(400);
    $error_response = [
        'error_code' => 'function-not-exists',
        'error_message' => "Function '{$func_name}' does not exist",
        'error_data' => ['function' => $func_name],
        'error_response_code' => 400
    ];
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
    exit;
}


// ====================================================================
// 허용된 함수인지 확인
// ====================================================================
$defined = get_defined_functions();
$user_funcs = $defined['user'];
if (!in_array($func_name, $user_funcs)) {
    error("function-not-allowed", "허용되지 않은 함수입니다: $func_name", response_code: 403);
}



try {
    // ====================================================================
    // 함수 호출
    // ====================================================================
    // http_params()로 모든 HTTP 파라미터를 전달하여 함수 실행
    $res = $func_name(http_params());

    // ====================================================================
    // 리턴 타입 검증
    // ====================================================================
    // 함수가 숫자, 문자열, 불리언을 리턴한 경우,
    // 이를 배열에 `data` 속성으로 저장하여 일관된 JSON 응답 구조를 유지합니다.
    if (is_numeric($res) || is_string($res) || is_bool($res)) {
        $res = ['data' => $res];
    }

    // ====================================================================
    // 객체를 배열로 변환 (Model 객체 지원)
    // ====================================================================
    // API 함수가 UserModel, PostModel 등의 객체를 리턴한 경우,
    // JSON 인코딩 전에 배열로 변환해야 합니다.
    //
    // 변환 규칙:
    // 1. toArray() 메서드가 있는 경우 (UserModel, PostModel 등):
    //    - toArray() 메서드를 호출하여 배열로 변환
    //    - 이 메서드는 객체의 모든 데이터를 배열 형태로 반환합니다
    //
    // 2. toArray() 메서드가 없는 경우 (stdClass, 일반 객체 등):
    //    - get_object_vars()를 사용하여 public 프로퍼티만 배열로 변환
    //    - stdClass의 경우 모든 프로퍼티가 public이므로 정상 변환됩니다
    //
    // 예제:
    // - create_post() 함수가 PostModel 객체 리턴
    //   → PostModel->toArray() 호출
    //   → ['id' => 1, 'title' => '제목', 'content' => '내용', ...]
    //
    // - get_user_by_id() 함수가 UserModel 객체 리턴
    //   → UserModel->toArray() 호출
    //   → ['idx' => 1, 'email' => 'user@example.com', ...]
    if (is_object($res)) {
        if (method_exists($res, 'toArray')) {
            // Model 클래스: toArray() 메서드 호출
            // UserModel, PostModel 등 모든 Model 클래스는 toArray() 메서드를 구현해야 합니다.
            $res = $res->toArray();
        } else {
            // 일반 객체 (stdClass 등): get_object_vars()로 배열 변환
            // 이 함수는 객체의 public 프로퍼티만 반환합니다.
            $res = get_object_vars($res);
        }
    }

    // ====================================================================
    // 정상 응답 처리
    // ====================================================================
    // 함수 이름 추가 (어떤 함수가 실행되었는지 표시)
    // $res['func'] = $func_name;

    // JSON 응답 출력
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
} catch (ApiException $e) {
    // ====================================================================
    // API 에러 처리 (error() 함수를 통해 throw된 에러)
    // ====================================================================
    // error() 함수로 throw된 ApiException을 catch하여 JSON 에러 응답으로 변환

    // HTTP 상태 코드 설정
    http_response_code($e->getErrorResponseCode());

    // 에러 응답 생성
    $error_response = $e->toArray();
    $error_response['func'] = $func_name;

    // JSON 응답 출력
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    // ====================================================================
    // 예상치 못한 예외 처리
    // ====================================================================
    // ApiException이 아닌 다른 예외 (치명적 오류, 시스템 에러 등)
    http_response_code(500);

    $error_response = [
        'error_code' => 'exception',
        'error_message' => $e->getMessage(),
        'error_data' => [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ],
        'error_response_code' => 500,
        'func' => $func_name
    ];

    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
}
