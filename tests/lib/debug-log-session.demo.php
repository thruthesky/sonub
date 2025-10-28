<?php
/**
 * debug_log() 함수 세션 타임스탐프 데모
 *
 * 이 스크립트는 debug_log() 함수가 세션 내에서 한 번만 타임스탐프를 생성하는지 보여줍니다.
 * 같은 스크립트 실행 내에서 여러 번 debug_log()를 호출해도 모두 동일한 타임스탐프를 사용합니다.
 *
 * 실행 방법:
 * php tests/lib/debug-log-session.demo.php
 */

require_once __DIR__ . '/../../init.php';

$debug_log_file = ROOT_DIR . '/var/debug.log';

echo "🎯 debug_log() 함수 세션 타임스탐프 데모\n";
echo "==========================================\n\n";

echo "📝 로그 파일: {$debug_log_file}\n\n";

// 파일 초기화
file_put_contents($debug_log_file, '');

echo "🔹 단계 1: 첫 번째 debug_log() 호출\n";
echo "   코드: debug_log('첫 번째 메시지');\n";
sleep(1); // 시간 차이를 보기 위해 1초 대기
debug_log('첫 번째 메시지');
$content = file_get_contents($debug_log_file);
$timestamp1 = extract_timestamp($content);
echo "   결과: {$timestamp1}\n";
echo "   타임스탐프 기록됨\n\n";

echo "🔹 단계 2: 두 번째 debug_log() 호출 (1초 후)\n";
echo "   코드: debug_log('두 번째 메시지');\n";
sleep(1); // 시간 차이를 보기 위해 1초 대기
debug_log('두 번째 메시지');
$content = file_get_contents($debug_log_file);
$timestamp2 = extract_timestamp($content);
echo "   결과: {$timestamp2}\n";

if ($timestamp1 === $timestamp2) {
    echo "   ✅ 동일한 타임스탐프 사용! (세션 첫 호출 시의 타임스탐프 유지)\n\n";
} else {
    echo "   ❌ 다른 타임스탐프 사용 (예상과 다름)\n\n";
}

echo "🔹 단계 3: 세 번째 debug_log() 호출 (여러 파라미터)\n";
echo "   코드: debug_log('메시지', true, ['data' => 'value']);\n";
sleep(1); // 시간 차이를 보기 위해 1초 대기
debug_log('메시지', true, ['data' => 'value']);
$content = file_get_contents($debug_log_file);
$timestamp3 = extract_timestamp($content);
echo "   결과: {$timestamp3}\n";

if ($timestamp1 === $timestamp3) {
    echo "   ✅ 동일한 타임스탐프 사용! (세션 첫 호출 시의 타임스탐프 유지)\n\n";
} else {
    echo "   ❌ 다른 타임스탐프 사용 (예상과 다름)\n\n";
}

// 최종 로그 출력
echo "=========================================\n";
echo "📄 최종 로그 파일 내용:\n";
echo "=========================================\n";
$content = file_get_contents($debug_log_file);
$lines = file($debug_log_file);
foreach ($lines as $i => $line) {
    echo ($i + 1) . ". " . trim($line) . "\n";
}

echo "\n=========================================\n";
echo "📊 분석 결과:\n";
echo "=========================================\n";
preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $content, $matches);
$timestamps = $matches[1];
$unique_timestamps = array_unique($timestamps);
$log_count = count($lines) - 1; // 마지막 빈 줄 제외

echo "✓ 총 로그 엔트리 수: {$log_count}개\n";
echo "✓ 사용된 타임스탐프 수: " . count($unique_timestamps) . "개\n";
echo "✓ 타임스탐프: " . implode(', ', $unique_timestamps) . "\n";

if (count($unique_timestamps) === 1) {
    echo "\n✅ 성공! 세션 내의 모든 로그가 동일한 타임스탐프를 사용합니다.\n";
    echo "   이는 정적 변수를 통해 세션 첫 호출 시의 타임스탐프를 유지하기 때문입니다.\n";
} else {
    echo "\n⚠️ 경고: 여러 개의 다른 타임스탐프가 사용되었습니다.\n";
    echo "   이는 여러 세션(여러 PHP 스크립트 실행)에서 기록된 것일 수 있습니다.\n";
}

/**
 * 첫 번째 타임스탐프를 추출하는 헬퍼 함수
 */
function extract_timestamp(string $content): ?string
{
    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $content, $matches)) {
        return $matches[1];
    }
    return null;
}
