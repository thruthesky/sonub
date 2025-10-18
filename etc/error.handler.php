<?php
// /www/etc/php-error-to-alert.php


// 개발용: 브라우저에 PHP 기본 에러화면이 나오지 않게
ini_set('display_errors', '0');     // ★ 중요: 이게 켜져 있으면 PHP의 기본 Fatal 메시지가 먼저 출력됨
ini_set('log_errors', '1');         // 에러는 로그로 남기기
error_reporting(E_ALL);

// (선택) 내용이 섞이지 않도록 출력 버퍼 시작
if (php_sapi_name() !== 'cli' && !headers_sent()) {
    ob_start();
}

// 일반 에러(Warning/Notice 등)
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // 에러가 @로 억제된 경우 무시
    if (!(error_reporting() & $errno)) return false;

    $msg = "[PHP Error:$errno] $errstr in $errfile:$errline";

    // HTML 에러 메시지 표시
    echo "<div class='alert alert-danger m-3 font-monospace shadow-sm'>";
    echo "<strong class='d-block mb-2'>⚠️ PHP Error [$errno]</strong>";
    echo "<div class='mb-2'>" . htmlspecialchars($errstr) . "</div>";
    echo "<small class='text-body-secondary'>File: " . htmlspecialchars($errfile) . " (Line: $errline)</small>";
    echo "</div>";

    // JavaScript alert도 함께 표시. 앞에 '"> 를 추가해서, JavaScript 가 잘 실행되도록 함.
    echo "'\"></script><script>alert(" . json_encode($msg, JSON_UNESCAPED_UNICODE) . ");</script>";

    // 기본 핸들러로 넘기지 않음
    return true;
});

// Fatal 에러 처리
register_shutdown_function(function () {
    $e = error_get_last();
    if (!$e) return;

    if (defined('API_CALL') && API_CALL === true) {
        // API 호출인 경우 JSON 형식으로 에러 반환
        error('e_fatal', 'Fatal Error: ' . $e['message'], response_code: 500);
    }

    // 치명적 에러 유형만 선별
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($e['type'], $fatalTypes, true)) return;

    $msg = "[Fatal] {$e['message']} in {$e['file']}:{$e['line']}";

    // 가능하면 이전에 출력된 내용을 지워서 PHP 기본 메시지/반쯤 그려진 페이지를 제거
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // 최소한의 HTML 보장 (문서가 전혀 없더라도 alert가 뜨게)
    header('Content-Type: text/html; charset=UTF-8', true);
    echo "<!doctype html><html><head><meta charset='utf-8'>";
    include __DIR__ . '/php-hot-reload-client.php';
    echo "</head><body>";

    // HTML 에러 메시지 표시
    echo "<div class='alert alert-danger mx-auto my-4 p-4 font-monospace shadow' style='max-width:800px;'>";
    echo "<h2 class='h4 mb-3'>🚨 Fatal Error</h2>";
    echo "<p class='mb-2'>" . htmlspecialchars($e['message']) . "</p>";
    echo "<p class='mb-1 small text-body-secondary'>File: " . htmlspecialchars($e['file']) . "</p>";
    echo "<p class='mb-0 small text-body-secondary'>Line: " . $e['line'] . "</p>";
    echo "</div>";

    // JavaScript alert도 표시
    echo "<script>alert(" . json_encode($msg, JSON_UNESCAPED_UNICODE) . ");</script>";
    echo "</body></html>";
});
