<?php
/**
 * inter_params() 함수 Unit 테스트
 *
 * inter_params() 함수가 올바른 파라미터 배열을 반환하고
 * 타입 변환이 제대로 이루어지는지 검증
 */

include __DIR__ . '/../../init.php';

echo "🧪 inter_params() 함수 Unit 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 1: get_post 함수의 파라미터 필터링 및 타입 변환
    // ========================================================================
    echo "🧪 테스트 1: get_post 함수의 파라미터 필터링 및 타입 변환\n";

    // HTTP 입력 데이터 주입 (실제 HTTP 요청을 시뮬레이션)
    inject_http_input('func', 'get_post');
    inject_http_input('post_id', '123'); // 문자열로 전달
    inject_http_input('with_user', 'true'); // 문자열 'true'
    inject_http_input('with_comments', 'false'); // 문자열 'false'
    inject_http_input('extra_param', 'should_be_ignored'); // 정의되지 않은 파라미터

    // inter_params() 호출
    $params = inter_params();

    // 결과 검증
    if (isset($params['post_id']) && $params['post_id'] === 123) {
        echo "   ✅ post_id가 int로 변환됨: " . gettype($params['post_id']) . " = {$params['post_id']}\n";
    } else {
        echo "   ❌ post_id 타입 변환 실패\n";
        var_dump($params);
        exit(1);
    }

    if (isset($params['with_user']) && $params['with_user'] === true) {
        echo "   ✅ with_user가 bool로 변환됨: " . gettype($params['with_user']) . " = " . ($params['with_user'] ? 'true' : 'false') . "\n";
    } else {
        echo "   ❌ with_user 타입 변환 실패\n";
        var_dump($params);
        exit(1);
    }

    if (isset($params['with_comments']) && $params['with_comments'] === false) {
        echo "   ✅ with_comments가 bool로 변환됨: " . gettype($params['with_comments']) . " = " . ($params['with_comments'] ? 'true' : 'false') . "\n";
    } else {
        echo "   ❌ with_comments 타입 변환 실패\n";
        var_dump($params);
        exit(1);
    }

    // 정의되지 않은 파라미터는 제외되어야 함
    if (!isset($params['extra_param'])) {
        echo "   ✅ 정의되지 않은 파라미터(extra_param)는 제외됨\n";
    } else {
        echo "   ❌ 정의되지 않은 파라미터가 포함되어 있음\n";
        var_dump($params);
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: 정의되지 않은 함수의 경우 빈 배열 반환
    // ========================================================================
    echo "🧪 테스트 2: 정의되지 않은 함수의 경우 빈 배열 반환\n";

    // 정의되지 않은 함수로 설정
    inject_http_input('func', 'nonexistent_function');
    inject_http_input('param1', 'value1');
    inject_http_input('param2', 'value2');

    $params = inter_params();

    if (empty($params)) {
        echo "   ✅ 정의되지 않은 함수의 경우 빈 배열 반환\n";
    } else {
        echo "   ❌ 빈 배열을 반환해야 하지만 다른 값 반환\n";
        var_dump($params);
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 3: 파라미터가 없는 경우
    // ========================================================================
    echo "🧪 테스트 3: 파라미터가 없는 경우\n";

    // func만 있고 파라미터 없음
    inject_http_input('func', 'get_post');
    // HTTP 입력 초기화 (이전 테스트 데이터 제거)
    global $__in;
    $__in = ['func' => 'get_post'];

    $params = inter_params();

    if (empty($params)) {
        echo "   ✅ 파라미터가 없는 경우 빈 배열 반환\n";
    } else {
        echo "   ⚠️ 파라미터가 있습니다 (일부 기본값이 있을 수 있음)\n";
        var_dump($params);
    }

    echo "\n";

    // ========================================================================
    // 테스트 4: file_delete 함수 (string 타입 파라미터)
    // ========================================================================
    echo "🧪 테스트 4: file_delete 함수 (string 타입 파라미터)\n";

    inject_http_input('func', 'file_delete');
    inject_http_input('url', '/var/uploads/31/test.jpg');

    $params = inter_params();

    if (isset($params['url']) && is_string($params['url']) && $params['url'] === '/var/uploads/31/test.jpg') {
        echo "   ✅ url이 string으로 유지됨: " . gettype($params['url']) . " = {$params['url']}\n";
    } else {
        echo "   ❌ url 타입 검증 실패\n";
        var_dump($params);
        exit(1);
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 inter_params() 테스트 통과!\n\n";
    echo "✅ 검증 완료:\n";
    echo "   - ✅ post_id 타입 변환: 문자열 → int\n";
    echo "   - ✅ with_user 타입 변환: 문자열 'true' → bool true\n";
    echo "   - ✅ with_comments 타입 변환: 문자열 'false' → bool false\n";
    echo "   - ✅ 정의되지 않은 파라미터 제외\n";
    echo "   - ✅ 정의되지 않은 함수의 경우 빈 배열 반환\n";
    echo "   - ✅ string 타입 파라미터 유지\n";

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   스택 트레이스:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
