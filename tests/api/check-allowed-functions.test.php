<?php
/**
 * check_if_allowed_functions() 함수 테스트
 *
 * 테스트 항목:
 * 1. 허용된 함수명 확인 (정상 통과)
 * 2. 허용되지 않은 함수명 확인 (403 에러 발생)
 * 3. func 파라미터 없음 (null) 확인
 * 4. debug_log()에 함수 검증 결과 기록
 *
 * 실행 방법:
 * php tests/api/check-allowed-functions.test.php
 */

require_once __DIR__ . '/../../init.php';

$debug_log_file = ROOT_DIR . '/var/debug.log';

echo "🧪 check_if_allowed_functions() 함수 테스트\n";
echo "==========================================\n\n";

// 파일 초기화
file_put_contents($debug_log_file, '');

// 테스트할 함수 목록 가져오기
$allowed_functions = config()->api->allowed_functions();
echo "📝 허용된 함수 목록: " . implode(', ', $allowed_functions) . "\n";
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

// 테스트 1: 허용된 함수명 확인
echo "✓ 테스트 1: 허용된 함수명 확인\n";
if (count($allowed_functions) > 0) {
    $allowed_func = $allowed_functions[0];
    echo "   코드: inject_http_input('func', '{$allowed_func}');\n";
    echo "         check_if_allowed_functions();\n";

    inject_http_input('func', $allowed_func);
    try {
        check_if_allowed_functions();
        echo "   ✅ 허용된 함수 '{$allowed_func}'가 정상 통과됨\n";
    } catch (ApiException $e) {
        echo "   ❌ 예상치 못한 에러: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️  허용된 함수가 없음\n";
}

// 테스트 2: 허용되지 않은 함수명 확인
echo "\n✓ 테스트 2: 허용되지 않은 함수명 확인\n";
echo "   코드: inject_http_input('func', 'undefined-function');\n";
echo "         check_if_allowed_functions();\n";

inject_http_input('func', 'undefined-function');
ob_start(); // 출력 버퍼 시작 (에러 메시지 캡처)
try {
    check_if_allowed_functions();
    ob_end_clean();
    echo "   ❌ 에러가 발생하지 않았습니다 (예상: 403 에러)\n";
} catch (ApiException $e) {
    ob_end_clean();
    echo "   ✅ 예상대로 403 에러 발생\n";
    echo "   에러 코드: " . $e->getErrorCode() . "\n";
    echo "   에러 메시지: " . $e->getErrorMessage() . "\n";
    echo "   HTTP 상태: " . $e->getErrorResponseCode() . "\n";
}

// 테스트 3: func 파라미터 없음
echo "\n✓ 테스트 3: func 파라미터 없음\n";
echo "   코드: inject_http_input('func', null);\n";
echo "         check_if_allowed_functions();\n";

inject_http_input('func', null);
ob_start(); // 출력 버퍼 시작 (에러 메시지 캡처)
try {
    check_if_allowed_functions();
    ob_end_clean();
    echo "   ❌ 에러가 발생하지 않았습니다 (예상: 403 에러)\n";
} catch (ApiException $e) {
    ob_end_clean();
    echo "   ✅ 예상대로 403 에러 발생\n";
    echo "   에러 코드: " . $e->getErrorCode() . "\n";
    echo "   에러 메시지: " . $e->getErrorMessage() . "\n";
    echo "   HTTP 상태: " . $e->getErrorResponseCode() . "\n";
}

// 최종 로그 파일 확인
echo "\n==========================================\n";
echo "📄 debug_log() 파일 내용 확인:\n";
echo "==========================================\n";

$content = file_get_contents($debug_log_file);
$lines = file($debug_log_file);

if (empty($content)) {
    echo "(로그 파일이 비어 있습니다)\n";
} else {
    echo "총 로그 라인: " . count($lines) . "\n\n";
    foreach ($lines as $i => $line) {
        $trimmed = rtrim($line);
        if (strlen($trimmed) > 100) {
            printf("%3d. %s...\n", ($i + 1), substr($trimmed, 0, 100));
        } else {
            printf("%3d. %s\n", ($i + 1), $trimmed);
        }
    }
}

// 최종 검증
echo "\n==========================================\n";
echo "🔍 검증 결과:\n";
echo "==========================================\n";

$lines = file($debug_log_file);
$timestamp_count = count_timestamp_lines($lines);

echo "✓ 타임스탐프 라인 수: " . $timestamp_count . "\n";
echo "✓ 총 로그 라인 수: " . count($lines) . "\n";

if ($timestamp_count === 1) {
    echo "✅ debug_log()의 세션 타임스탐프 기록이 정상 작동합니다.\n";
} else if ($timestamp_count === 0) {
    echo "ℹ️  check_if_allowed_functions()가 아직 debug_log()를 호출하지 않았습니다.\n";
} else {
    echo "⚠️  타임스탐프가 예상보다 많습니다.\n";
}

echo "\n✅ 테스트 완료!\n";
