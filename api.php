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
        // 에러 응답 처리
        if (isset($res['error_code'])) {
            http_response_code($res['error_response_code'] ?? 400);
            $res['func'] = $func_name;
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            // 정상 응답 처리
            $res['func'] = $func_name;
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(500);
        echo json_encode(error('response-not-array-or-object'), JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(error('exception', $e->getMessage() . ' func: ' . $func_name, ['trace' => $e->getTraceAsString()]), JSON_UNESCAPED_UNICODE);
}
