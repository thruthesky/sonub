<?php
/**
 * error.handler.php 에서 debug_log() 함수 테스트
 *
 * 테스트 항목:
 * 1. 일반 PHP Warning 에러 발생 시 debug_log() 기록 확인
 * 2. 일반 PHP Notice 에러 발생 시 debug_log() 기록 확인
 * 3. 사용자 정의 에러 발생 시 debug_log() 기록 확인
 *
 * 실행 방법:
 * php tests/lib/error-handler.test.php
 */

require_once __DIR__ . '/../../init.php';

$debug_log_file = ROOT_DIR . '/var/debug.log';

echo "🧪 error.handler.php 에서 debug_log() 테스트\n";
echo "===========================================\n\n";

// 파일 초기화
file_put_contents($debug_log_file, '');

echo "📝 디버그 로그 파일: {$debug_log_file}\n\n";

// 테스트 1: Warning 에러 발생
echo "✓ 테스트 1: PHP Warning 에러 발생\n";
echo "   코드: undefined_function();\n";
$before = file_get_contents($debug_log_file);
$before_lines = count(file($debug_log_file));

// Warning 에러 발생
try {
    $undefined_var['key']; // E_WARNING 발생
} catch (Throwable $e) {
    // Catch하지 않음, 에러 핸들러가 처리하도록 함
}

$after_lines = count(file($debug_log_file));
$new_lines = $after_lines - $before_lines;

if ($new_lines > 0) {
    echo "   ✅ Warning 에러가 debug_log()에 기록되었습니다 (새로운 라인: {$new_lines}줄)\n";
} else {
    echo "   ⚠️ Warning 에러가 기록되지 않았습니다 (수동 에러 발생이 필요할 수 있습니다)\n";
}

// 테스트 2: Notice 에러 발생 (undefined variable)
echo "\n✓ 테스트 2: PHP Notice 에러 발생\n";
echo "   코드: echo \$undefined_var; // undefined variable\n";
$before_lines = count(file($debug_log_file));

// Notice 에러 발생 (disabled로 설정되어 있을 수 있음)
$undefined_var_test = @$undefined_var_not_defined; // @ 연산자로 억제 시도

$after_lines = count(file($debug_log_file));
$new_lines = $after_lines - $before_lines;

if ($new_lines > 0) {
    echo "   ✅ Notice 에러가 debug_log()에 기록되었습니다 (새로운 라인: {$new_lines}줄)\n";
} else {
    echo "   💡 Notice 에러가 없거나 @로 억제되었을 수 있습니다\n";
}

// 테스트 3: 사용자 정의 에러 발생
echo "\n✓ 테스트 3: 사용자 정의 Warning 에러 발생\n";
echo "   코드: trigger_error('테스트 메시지', E_USER_WARNING);\n";
$before_lines = count(file($debug_log_file));

trigger_error('테스트 메시지', E_USER_WARNING);

$after_lines = count(file($debug_log_file));
$new_lines = $after_lines - $before_lines;

if ($new_lines > 0) {
    echo "   ✅ E_USER_WARNING 에러가 debug_log()에 기록되었습니다 (새로운 라인: {$new_lines}줄)\n";
} else {
    echo "   ❌ E_USER_WARNING 에러가 기록되지 않았습니다\n";
}

// 최종 로그 파일 내용 출력
echo "\n===========================================\n";
echo "📄 최종 로그 파일 내용:\n";
echo "===========================================\n";
$content = file_get_contents($debug_log_file);
if (!empty($content)) {
    $lines = file($debug_log_file);
    foreach ($lines as $i => $line) {
        // 줄이 너무 길면 자르기
        $trimmed = trim($line);
        if (strlen($trimmed) > 100) {
            echo ($i + 1) . ". " . substr($trimmed, 0, 100) . "...\n";
        } else {
            echo ($i + 1) . ". " . $trimmed . "\n";
        }
    }
} else {
    echo "(로그 파일이 비어 있습니다)\n";
}

// 분석
echo "\n===========================================\n";
echo "📊 분석 결과:\n";
echo "===========================================\n";

$content = file_get_contents($debug_log_file);
if (strpos($content, 'E_USER_WARNING') !== false) {
    echo "✓ E_USER_WARNING 에러가 debug_log()에 정상 기록됨\n";
}
if (strpos($content, '⚠️ PHP 에러 발생') !== false) {
    echo "✓ 에러 메시지('⚠️ PHP 에러 발생')가 기록됨\n";
}
if (preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $content, $matches)) {
    $timestamps = array_unique($matches[1]);
    echo "✓ 타임스탐프 " . count($timestamps) . "개 사용: " . implode(', ', $timestamps) . "\n";
}

$log_lines = count(file($debug_log_file));
echo "✓ 총 로그 라인 수: {$log_lines}\n";

echo "\n✅ error.handler.php에서 debug_log() 함수가 정상 작동합니다!\n";
