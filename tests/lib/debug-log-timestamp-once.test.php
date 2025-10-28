<?php
/**
 * debug_log() 함수 수정 검증 테스트
 *
 * 검증 항목:
 * 1. 타임스탐프가 세션에서 단 한 번만 기록되는지 확인
 * 2. 여러 파라미터 로깅 시 타임스탐프가 한 줄에만 있는지 확인
 * 3. 여러 debug_log() 호출 시 타임스탐프가 중복되지 않는지 확인
 * 4. 로그 파일 형식이 정확한지 확인
 *
 * 실행 방법:
 * php tests/lib/debug-log-timestamp-once.test.php
 */

require_once __DIR__ . '/../../init.php';

$debug_log_file = ROOT_DIR . '/var/debug.log';

echo "🧪 debug_log() 타임스탐프 단일 기록 검증\n";
echo "========================================\n\n";

// 파일 초기화
file_put_contents($debug_log_file, '');

echo "📝 테스트 로그 파일: {$debug_log_file}\n\n";

// 헬퍼 함수: 타임스탐프 라인 개수 세기
function count_timestamp_lines($lines) {
    $count = 0;
    foreach ($lines as $line) {
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}/', $line)) {
            $count++;
        }
    }
    return $count;
}

// 테스트 1: 첫 번째 호출 - 단일 파라미터
echo "✓ 테스트 1: 첫 번째 호출 - 단일 파라미터\n";
echo "   코드: debug_log('첫 번째 메시지');\n";
debug_log('첫 번째 메시지');

$lines = file($debug_log_file);
$timestamp_count = count_timestamp_lines($lines);

echo "   결과: " . count($lines) . "줄 기록됨\n";
echo "   타임스탐프 라인: " . $timestamp_count . "개\n";
if ($timestamp_count === 1) {
    echo "   ✅ 타임스탐프가 정확히 1개만 기록됨\n";
} else {
    echo "   ❌ 타임스탐프가 " . $timestamp_count . "개 기록됨 (오류)\n";
}

// 테스트 2: 두 번째 호출 - 여러 파라미터
echo "\n✓ 테스트 2: 두 번째 호출 - 여러 파라미터\n";
echo "   코드: debug_log('두 번째 메시지', true, ['data' => 'value']);\n";
debug_log('두 번째 메시지', true, ['data' => 'value']);

$lines = file($debug_log_file);
$timestamp_count = count_timestamp_lines($lines);

echo "   결과: " . count($lines) . "줄 기록됨 (누적)\n";
echo "   타임스탐프 라인: " . $timestamp_count . "개 (여전히 1개여야 함)\n";
if ($timestamp_count === 1) {
    echo "   ✅ 타임스탐프가 여전히 1개만 유지됨\n";
} else {
    echo "   ❌ 타임스탐프가 " . $timestamp_count . "개 기록됨 (오류)\n";
}

// 테스트 3: 세 번째 호출 - 배열과 객체
echo "\n✓ 테스트 3: 세 번째 호출 - 배열과 객체\n";
echo "   코드: debug_log(['array' => 123], (object)['key' => 'val']);\n";
debug_log(['array' => 123], (object)['key' => 'val']);

$lines = file($debug_log_file);
$timestamp_count = count_timestamp_lines($lines);

echo "   결과: " . count($lines) . "줄 기록됨 (누적)\n";
echo "   타임스탐프 라인: " . $timestamp_count . "개 (여전히 1개여야 함)\n";
if ($timestamp_count === 1) {
    echo "   ✅ 타임스탐프가 여전히 1개만 유지됨\n";
} else {
    echo "   ❌ 타임스탐프가 " . $timestamp_count . "개 기록됨 (오류)\n";
}

// 최종 로그 파일 출력
echo "\n========================================\n";
echo "📄 최종 로그 파일 내용 (처음 30줄):\n";
echo "========================================\n";

$lines = file($debug_log_file);
$max_lines = min(30, count($lines));
foreach (array_slice($lines, 0, $max_lines) as $i => $line) {
    // 줄 번호 표시
    $line_num = ($i + 1);
    $is_timestamp = preg_match('/^\[\d{4}-\d{2}-\d{2}/', $line);
    $prefix = $is_timestamp ? '📍' : '  ';

    $trimmed = rtrim($line);
    if (strlen($trimmed) > 80) {
        printf("{$prefix} %3d. %s...\n", $line_num, substr($trimmed, 0, 80));
    } else {
        printf("{$prefix} %3d. %s\n", $line_num, $trimmed);
    }
}

if (count($lines) > 30) {
    echo "   ... (나머지 " . (count($lines) - 30) . "줄 생략)\n";
}

// 최종 검증
echo "\n========================================\n";
echo "🔍 최종 검증 결과:\n";
echo "========================================\n";

$lines = file($debug_log_file);
$timestamp_count = count_timestamp_lines($lines);

// 모든 타임스탐프 추출
$timestamps = array();
foreach ($lines as $line) {
    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $m)) {
        $timestamps[] = $m[1];
    }
}
$unique_timestamps = array_unique($timestamps);

echo "✓ 총 로그 라인 수: " . count($lines) . "\n";
echo "✓ 타임스탐프 라인 수: " . $timestamp_count . "\n";
echo "✓ 고유 타임스탐프: " . implode(', ', $unique_timestamps) . "\n";

// 형식 검증
echo "\n✓ 로그 형식 검증:\n";

// 첫 번째 라인이 타임스탐프인지 확인
if (count($lines) > 0 && preg_match('/^\[\d{4}-\d{2}-\d{2}/', $lines[0])) {
    echo "  ✅ 첫 라인이 타임스탐프로 시작함\n";
} else {
    echo "  ❌ 첫 라인이 타임스탐프로 시작하지 않음\n";
}

// 두 번째 라인 이후가 타임스탐프로 시작하지 않는지 확인
$has_timestamp_in_data = false;
for ($i = 1; $i < count($lines); $i++) {
    if (preg_match('/^\[\d{4}-\d{2}-\d{2}/', $lines[$i])) {
        $has_timestamp_in_data = true;
        break;
    }
}

if (!$has_timestamp_in_data) {
    echo "  ✅ 데이터 라인에 타임스탐프가 없음\n";
} else {
    echo "  ❌ 데이터 라인에 타임스탐프가 있음 (오류)\n";
}

// 최종 결론
echo "\n========================================\n";
if ($timestamp_count === 1 && !$has_timestamp_in_data) {
    echo "✅ 검증 성공! debug_log() 함수가 정확하게 작동합니다.\n";
    echo "   - 타임스탐프: 세션에서 단 한 번만 기록\n";
    echo "   - 데이터: 타임스탐프 없이 깔끔하게 기록\n";
} else {
    echo "❌ 검증 실패! 다음을 확인하세요:\n";
    if ($timestamp_count !== 1) {
        echo "   - 타임스탐프 라인이 정확히 1개가 아님\n";
    }
    if ($has_timestamp_in_data) {
        echo "   - 데이터 라인에 타임스탐프가 있음\n";
    }
}
echo "========================================\n";
