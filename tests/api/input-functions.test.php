<?php

/**
 * HTTP 입력 함수 테스트 (inject_http_input, http_params, http_param)
 *
 * lib/api/input.functions.php의 다음 함수들을 테스트합니다:
 * - inject_http_input(): HTTP 입력에 값 주입
 * - http_params(): 파라미터 조회 (전체 입력 조회 지원)
 * - http_param(): 기본값과 함께 파라미터 조회
 *
 * @package Sonub
 * @subpackage Tests
 */

// ========================================================================
// 초기화: 테스트 환경 설정
// ========================================================================

// 프로젝트 루트 디렉토리 정의 (이미 정의되었을 수 있음)
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__FILE__, 3));
}

// init.php 로드 (모든 필수 기능 초기화)
// init.php에서 이미 로드되었는지 확인
if (!function_exists('db')) {
    require ROOT_DIR . '/init.php';
}

// ========================================================================
// 테스트 결과를 저장할 배열
// ========================================================================

$results = [];
$passed = 0;
$failed = 0;

// ========================================================================
// 테스트 헬퍼 함수
// ========================================================================

/**
 * 단일 테스트 실행
 */
function test($description, $assertion, $expectedValue = null, $actualValue = null) {
    global $passed, $failed, $results;

    if ($assertion) {
        $passed++;
        $results[] = "✅ PASS: $description";
    } else {
        $failed++;
        $actualOutput = is_array($actualValue) ? json_encode($actualValue, JSON_UNESCAPED_UNICODE) : var_export($actualValue, true);
        $expectedOutput = is_array($expectedValue) ? json_encode($expectedValue, JSON_UNESCAPED_UNICODE) : var_export($expectedValue, true);
        $results[] = "❌ FAIL: $description\n   Expected: $expectedOutput\n   Actual: $actualOutput";
    }
}

// ========================================================================
// 테스트 1: inject_http_input() - 값 주입 테스트
// ========================================================================

echo "\n📋 [테스트 1] inject_http_input() - 값 주입\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 초기 HTTP 입력 초기화
global $__in;
$__in = [];

// 1-1: 문자열 값 주입
inject_http_input('username', 'john_doe');
test(
    "문자열 값을 주입하고 조회",
    http_param('username') === 'john_doe',
    'john_doe',
    http_param('username')
);

// 1-2: 숫자 값 주입
inject_http_input('user_id', 123);
test(
    "숫자 값을 주입하고 조회",
    http_param('user_id') === 123,
    123,
    http_param('user_id')
);

// 1-3: 배열 값 주입
$arrayValue = ['name' => 'John', 'age' => 30];
inject_http_input('user_data', $arrayValue);
test(
    "배열 값을 주입하고 조회",
    http_param('user_data') === $arrayValue,
    $arrayValue,
    http_param('user_data')
);

// 1-4: null 값 주입
inject_http_input('nullable_field', null);
test(
    "null 값을 주입하고 조회",
    http_param('nullable_field') === null,
    null,
    http_param('nullable_field')
);

// 1-5: 기존 값 덮어쓰기
inject_http_input('category', 'original');
inject_http_input('category', 'updated');
test(
    "기존 값을 덮어쓰고 조회",
    http_param('category') === 'updated',
    'updated',
    http_param('category')
);

// ========================================================================
// 테스트 2: http_params() - 전체 입력 조회
// ========================================================================

echo "\n📋 [테스트 2] http_params() - 전체 입력 조회\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 2-1: 파라미터명 미지정 시 전체 입력 배열 반환
$allInputs = http_params();
test(
    "파라미터명 미지정 시 전체 입력 배열 반환",
    is_array($allInputs) && count($allInputs) > 0,
    true,
    is_array($allInputs) && count($allInputs) > 0
);

// 2-2: 주입된 모든 값이 포함되는지 확인
test(
    "전체 입력에 username이 포함됨",
    isset($allInputs['username']) && $allInputs['username'] === 'john_doe',
    true,
    isset($allInputs['username']) && $allInputs['username'] === 'john_doe'
);

test(
    "전체 입력에 user_id가 포함됨",
    isset($allInputs['user_id']) && $allInputs['user_id'] === 123,
    true,
    isset($allInputs['user_id']) && $allInputs['user_id'] === 123
);

// ========================================================================
// 테스트 3: http_params() - 특수 문자열 변환 ('null', 'undefined', '')
// ========================================================================

echo "\n📋 [테스트 3] http_params() - 특수 문자열 변환\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 3-1: 문자열 'null'은 null로 변환
inject_http_input('json_null_field', 'null');
test(
    "문자열 'null'은 null로 변환됨",
    http_params('json_null_field') === null,
    null,
    http_params('json_null_field')
);

// 3-2: 문자열 'undefined'는 null로 변환
inject_http_input('undefined_field', 'undefined');
test(
    "문자열 'undefined'는 null로 변환됨",
    http_params('undefined_field') === null,
    null,
    http_params('undefined_field')
);

// 3-3: 빈 문자열은 null로 변환
inject_http_input('empty_field', '');
test(
    "빈 문자열 ''은 null로 변환됨",
    http_params('empty_field') === null,
    null,
    http_params('empty_field')
);

// 3-4: 문자열 'false'는 그대로 반환됨 (falsy하지 않음)
inject_http_input('false_string', 'false');
test(
    "문자열 'false'는 그대로 반환됨",
    http_params('false_string') === 'false',
    'false',
    http_params('false_string')
);

// 3-5: 문자열 '0'은 그대로 반환됨 (falsy하지 않음)
inject_http_input('zero_string', '0');
test(
    "문자열 '0'은 그대로 반환됨",
    http_params('zero_string') === '0',
    '0',
    http_params('zero_string')
);

// ========================================================================
// 테스트 4: http_params() - 존재하지 않는 파라미터
// ========================================================================

echo "\n📋 [테스트 4] http_params() - 존재하지 않는 파라미터\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 4-1: 존재하지 않는 파라미터는 null 반환
test(
    "존재하지 않는 파라미터는 null 반환",
    http_params('nonexistent') === null,
    null,
    http_params('nonexistent')
);

// 4-2: 존재하지 않는 파라미터에 http_param() 사용 시 기본값 반환
test(
    "존재하지 않는 파라미터에 기본값 'default' 반환",
    http_param('nonexistent', 'default') === 'default',
    'default',
    http_param('nonexistent', 'default')
);

// 4-3: 존재하지 않는 파라미터에 기본값 0 반환
test(
    "존재하지 않는 파라미터에 기본값 0 반환",
    http_param('nonexistent', 0) === 0,
    0,
    http_param('nonexistent', 0)
);

// ========================================================================
// 테스트 5: http_param() - 기본값 메커니즘
// ========================================================================

echo "\n📋 [테스트 5] http_param() - 기본값 메커니즘\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 5-1: 파라미터 존재 시 기본값 무시
inject_http_input('has_value', 'actual-value');
test(
    "파라미터 존재 시 기본값을 무시하고 실제 값 반환",
    http_param('has_value', 'default-value') === 'actual-value',
    'actual-value',
    http_param('has_value', 'default-value')
);

// 5-2: 파라미터가 null일 때 기본값 반환
inject_http_input('null_value', null);
test(
    "파라미터가 null일 때 기본값 반환",
    http_param('null_value', 'default-value') === 'default-value',
    'default-value',
    http_param('null_value', 'default-value')
);

// 5-3: 파라미터가 특수 문자열 'null'일 때 기본값 반환
inject_http_input('string_null', 'null');
test(
    "파라미터가 'null' 문자열일 때 기본값 반환",
    http_param('string_null', 'default-value') === 'default-value',
    'default-value',
    http_param('string_null', 'default-value')
);

// ========================================================================
// 테스트 6: 실제 시나리오 테스트
// ========================================================================

echo "\n📋 [테스트 6] 실제 시나리오 테스트\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// 6-1: API 요청 파라미터 시뮬레이션
$__in = []; // 초기화
inject_http_input('category', 'discussion');
inject_http_input('page', '1');
inject_http_input('limit', '10');

$category = http_param('category', 'story');
$page = (int)http_param('page', '1');
$limit = (int)http_param('limit', '5');

test(
    "API 파라미터: category가 올바르게 설정됨",
    $category === 'discussion',
    'discussion',
    $category
);

test(
    "API 파라미터: page가 올바르게 설정됨",
    $page === 1,
    1,
    $page
);

test(
    "API 파라미터: limit이 올바르게 설정됨",
    $limit === 10,
    10,
    $limit
);

// 6-2: 선택적 파라미터 처리
$__in = [];
inject_http_input('title', 'My Post');
// content는 주입하지 않음 (선택적 파라미터)

$title = http_param('title');
$content = http_param('content', 'No content provided');

test(
    "선택적 파라미터: 제공된 title 반환",
    $title === 'My Post',
    'My Post',
    $title
);

test(
    "선택적 파라미터: 없는 content에 기본값 반환",
    $content === 'No content provided',
    'No content provided',
    $content
);

// ========================================================================
// 테스트 결과 출력
// ========================================================================

echo "\n📊 테스트 결과\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

foreach ($results as $result) {
    echo "$result\n";
}

echo "\n📈 종합 결과\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ PASS: $passed\n";
echo "❌ FAIL: $failed\n";
echo "🎯 TOTAL: " . ($passed + $failed) . "\n";

if ($failed === 0) {
    echo "\n🎉 모든 테스트에 통과했습니다!\n";
    exit(0);
} else {
    echo "\n⚠️  일부 테스트가 실패했습니다.\n";
    exit(1);
}
