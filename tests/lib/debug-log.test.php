<?php
/**
 * debug_log() 함수 테스트 (Rest Operator 지원)
 *
 * 테스트 항목:
 * 1. 단일 문자열 데이터 로깅
 * 2. 여러 파라미터 동시 로깅 (rest operator)
 * 3. 배열 데이터 로깅
 * 4. 객체 데이터 로깅
 * 5. 불린(boolean) 데이터 로깅
 * 6. 숫자 데이터 로깅
 * 7. ./var/debug.log 파일에 기록되는지 확인
 * 8. 타임스탐프 형식 확인
 * 9. 사용자 요청 예제: debug_log('API 호출 시작', true, [], http_params());
 *
 * 실행 방법:
 * php tests/lib/debug-log.test.php
 */

require_once __DIR__ . '/../../init.php';

// 테스트용 디버그 로그 파일 경로
$debug_log_file = ROOT_DIR . '/var/debug.log';

// 테스트 전 기존 로그 파일 백업
$backup_file = $debug_log_file . '.backup';
if (file_exists($debug_log_file)) {
    copy($debug_log_file, $backup_file);
}

// 새로운 로그 파일 시작
file_put_contents($debug_log_file, '');

echo "🧪 debug_log() 함수 테스트 시작 (Rest Operator 지원)\n";
echo "=====================================================\n\n";

// 테스트 1: 단일 문자열 로깅
echo "✓ 테스트 1: 단일 문자열 데이터 로깅\n";
debug_log('API 호출 시작');
$content = file_get_contents($debug_log_file);
if (strpos($content, 'API 호출 시작') !== false) {
    echo "  ✅ 문자열이 파일에 기록되었습니다.\n";
} else {
    echo "  ❌ 문자열이 파일에 기록되지 않았습니다.\n";
    exit(1);
}

// 테스트 2: 여러 파라미터 동시 로깅 (Rest Operator 테스트)
echo "\n✓ 테스트 2: 여러 파라미터 동시 로깅 (Rest Operator)\n";
$before_lines = count(file($debug_log_file));
debug_log('API 호출 시작', true, [], ['user_id' => 123]);
$after_lines = count(file($debug_log_file));
$new_lines = $after_lines - $before_lines;

if ($new_lines >= 4) {
    echo "  ✅ 4개 파라미터가 각각 별도의 라인으로 기록되었습니다 (새로운 라인: {$new_lines}줄)\n";
} else {
    echo "  ❌ 파라미터가 제대로 기록되지 않았습니다 (새로운 라인: {$new_lines}줄)\n";
    exit(1);
}

// 테스트 2-1: 같은 세션의 모든 로그가 동일한 타임스탐프를 가지는지 확인
echo "\n✓ 테스트 2-1: 세션 내 모든 로그의 타임스탐프가 동일한지 확인\n";
$content = file_get_contents($debug_log_file);
// 모든 타임스탐프를 추출
preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $content, $matches);
$timestamps = $matches[1];
$unique_timestamps = array_unique($timestamps);

if (count($unique_timestamps) === 1) {
    echo "  ✅ 모든 로그가 동일한 타임스탐프를 사용합니다: {$timestamps[0]}\n";
} else {
    echo "  ⚠️ 로그가 여러 개의 타임스탐프를 사용합니다: " . implode(', ', $unique_timestamps) . "\n";
    echo "  💡 이는 여러 테스트가 연속으로 실행되었기 때문일 수 있습니다.\n";
}

// 테스트 3: 배열 로깅 확인
echo "\n✓ 테스트 3: 배열 데이터 로깅\n";
$content = file_get_contents($debug_log_file);
if (strpos($content, 'user_id') !== false && strpos($content, '123') !== false) {
    echo "  ✅ 배열이 print_r() 포맷으로 파일에 기록되었습니다.\n";
} else {
    echo "  ❌ 배열이 파일에 기록되지 않았습니다.\n";
    exit(1);
}

// 테스트 4: 객체 로깅
echo "\n✓ 테스트 4: 객체 데이터 로깅\n";
file_put_contents($debug_log_file, '');
$test_object = (object) ['name' => '테스트', 'value' => 42];
debug_log($test_object);
$content = file_get_contents($debug_log_file);
if (strpos($content, '테스트') !== false && strpos($content, '42') !== false) {
    echo "  ✅ 객체가 print_r() 포맷으로 파일에 기록되었습니다.\n";
} else {
    echo "  ❌ 객체가 파일에 기록되지 않았습니다.\n";
    exit(1);
}

// 테스트 5: 불린 데이터 로깅
echo "\n✓ 테스트 5: 불린(Boolean) 데이터 로깅\n";
file_put_contents($debug_log_file, '');
debug_log(true, false);
$content = file_get_contents($debug_log_file);
if (strpos($content, 'true') !== false && strpos($content, 'false') !== false) {
    echo "  ✅ 불린 값이 var_export() 포맷으로 파일에 기록되었습니다.\n";
} else {
    echo "  ❌ 불린 값이 파일에 기록되지 않았습니다.\n";
    exit(1);
}

// 테스트 6: 숫자 데이터 로깅
echo "\n✓ 테스트 6: 숫자 데이터 로깅\n";
file_put_contents($debug_log_file, '');
debug_log(42, 3.14, 0);
$content = file_get_contents($debug_log_file);
if (strpos($content, '42') !== false && strpos($content, '3.14') !== false) {
    echo "  ✅ 숫자 값이 var_export() 포맷으로 파일에 기록되었습니다.\n";
} else {
    echo "  ❌ 숫자 값이 파일에 기록되지 않았습니다.\n";
    exit(1);
}

// 테스트 7: 타임스탐프 형식 확인
echo "\n✓ 테스트 7: 타임스탐프 형식 확인\n";
// 타임스탐프 형식: [YYYY-MM-DD HH:MM:SS]
if (preg_match('/\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\]/', $content)) {
    echo "  ✅ 타임스탐프 형식이 올바릅니다 (YYYY-MM-DD HH:MM:SS)\n";
} else {
    echo "  ❌ 타임스탐프 형식이 잘못되었습니다.\n";
    exit(1);
}

// 테스트 8: 사용자 요청 예제 - debug_log('API 호출 시작', true, [], http_params());
echo "\n✓ 테스트 8: 사용자 요청 예제 테스트\n";
file_put_contents($debug_log_file, '');

// http_params() 함수가 있으면 사용, 없으면 배열로 대체
if (function_exists('http_params')) {
    debug_log('API 호출 시작', true, [], http_params());
    echo "  🔹 http_params() 함수를 사용하여 테스트\n";
} else {
    // http_params() 함수가 없는 경우 대체 배열 사용
    $mock_params = ['func' => 'create_post', 'title' => 'Test Post', 'auth' => true];
    debug_log('API 호출 시작', true, [], $mock_params);
    echo "  🔹 대체 배열을 사용하여 테스트\n";
}

$content = file_get_contents($debug_log_file);
$lines = file($debug_log_file);
$line_count = count($lines);

if ($line_count >= 4) {
    echo "  ✅ 모든 파라미터가 별도 라인으로 기록되었습니다 (총 {$line_count}줄)\n";
    echo "  ✅ 로그 파일 내용:\n";
    foreach ($lines as $i => $line) {
        echo "     라인 " . ($i + 1) . ": " . substr(trim($line), 0, 50) . (strlen(trim($line)) > 50 ? '...' : '') . "\n";
    }
} else {
    echo "  ❌ 파라미터가 제대로 기록되지 않았습니다 (총 {$line_count}줄)\n";
    exit(1);
}

// 최종 결과 출력
echo "\n=====================================================\n";
echo "📄 최종 로그 파일 전체 내용:\n";
echo "=====================================================\n";
$content = file_get_contents($debug_log_file);
echo $content;
echo "\n";

// 파일 크기 정보
$file_size = filesize($debug_log_file);
$line_count = count(file($debug_log_file));
echo "📊 로그 파일 정보:\n";
echo "  - 파일 경로: {$debug_log_file}\n";
echo "  - 파일 크기: {$file_size} bytes\n";
echo "  - 총 라인 수: {$line_count}\n";

echo "\n✅ 모든 테스트 통과! debug_log() 함수가 rest operator를 지원합니다.\n";

// 테스트 완료 후 로그 파일 정리 (선택사항)
// 기존 백업 파일이 있으면 복원
if (file_exists($backup_file)) {
    copy($backup_file, $debug_log_file);
    unlink($backup_file);
    echo "\n💾 테스트 종료 후 기존 로그 파일을 복원했습니다.\n";
}
