<?php
const ROOT_DIR = __DIR__;
include_once ROOT_DIR . '/etc/includes.php';
header('Content-Type: application/json; charset=utf-8');

$func_name = http_params('func');
if ($func_name === null) {
    http_response_code(400);
    echo json_encode(error('no-function-specified', 'Function name is required'), JSON_UNESCAPED_UNICODE);
    exit;
}
if (!function_exists($func_name)) {
    http_response_code(400);
    echo json_encode(error('function-not-exists', $func_name), JSON_UNESCAPED_UNICODE);
    exit;
}
try {
    // 함수 호출
    $res = $func_name(http_params());
    if (is_array($res) || is_object($res)) {
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
        // 에러 응답 처리
        // ====================================================================
        if (isset($res['error_code'])) {
            // HTTP 상태 코드 설정 (기본값: 400)
            http_response_code($res['error_response_code'] ?? 400);

            // 함수 이름 추가 (어떤 함수가 실행되었는지 표시)
            $res['func'] = $func_name;

            // JSON 응답 출력
            echo json_encode($res, JSON_UNESCAPED_UNICODE);

            // 중복 출력 방지를 위해 종료
            exit;
        }

        // ====================================================================
        // 정상 응답 처리
        // ====================================================================
        // 함수 이름 추가 (어떤 함수가 실행되었는지 표시)
        $res['func'] = $func_name;

        // JSON 응답 출력
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    } else {
        // ====================================================================
        // 리턴 타입 에러
        // ====================================================================
        // 함수가 배열이나 객체가 아닌 값을 리턴한 경우 (문자열, 숫자 등)
        http_response_code(500);
        echo json_encode(error('response-not-array-or-object'), JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    // ========================================================================
    // 예외 처리
    // ========================================================================
    // 함수 실행 중 발생한 모든 예외를 캐치하여 JSON 에러 응답으로 변환
    http_response_code(500);
    echo json_encode(error('exception', $e->getMessage() . ' func: ' . $func_name, ['trace' => $e->getTraceAsString()]), JSON_UNESCAPED_UNICODE);
}
