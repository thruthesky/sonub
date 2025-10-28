<?php
/**
 * get_post API 엔드포인트 테스트
 *
 * api.php의 spread operator가 제대로 작동하는지 검증
 */

include __DIR__ . '/../../init.php';

// 테스트 사용자 로그인
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 get_post API 엔드포인트 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 준비: 테스트 게시글 생성
    // ========================================================================
    echo "🧪 테스트 준비: 테스트 게시글 생성\n";

    // 테스트 사용자로 로그인
    login_as_test_user('banana');
    $user = login();
    echo "   로그인된 사용자: {$user->first_name} {$user->last_name}\n";

    // 테스트 게시글 생성
    $testPost = create_post([
        'category' => 'discussion',
        'title' => 'API 엔드포인트 테스트 게시글',
        'content' => 'API 호출을 통해 게시글을 조회하는 테스트입니다.',
    ]);

    if ($testPost instanceof PostModel) {
        $postId = $testPost->id;
        echo "   ✅ 테스트 게시글 생성 성공 (ID: {$postId})\n";
    } else {
        echo "   ❌ 테스트 게시글 생성 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 1: API 호출 (기본 파라미터)
    // ========================================================================
    echo "🧪 테스트 1: API 호출 (post_id만 전달)\n";

    $url = "https://local.sonub.com/api.php?func=get_post&post_id={$postId}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 로컬 개발 환경용
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        echo "   ✅ HTTP 상태 코드: 200 (성공)\n";
    } else {
        echo "   ❌ HTTP 상태 코드: {$http_code} (실패)\n";
        echo "   응답 내용: {$response}\n";
        exit(1);
    }

    $data = json_decode($response, true);

    if ($data && isset($data['id']) && $data['id'] === $postId) {
        echo "   ✅ 게시글 ID 일치: {$data['id']}\n";
        echo "   - 제목: {$data['title']}\n";
    } else {
        echo "   ❌ 게시글 ID 불일치 또는 응답 형식 오류\n";
        echo "   응답 내용: {$response}\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: API 호출 (with_user=true)
    // ========================================================================
    echo "🧪 테스트 2: API 호출 (with_user=true)\n";

    $url = "https://local.sonub.com/api.php?func=get_post&post_id={$postId}&with_user=true";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        echo "   ✅ HTTP 상태 코드: 200 (성공)\n";
    } else {
        echo "   ❌ HTTP 상태 코드: {$http_code} (실패)\n";
        exit(1);
    }

    $data = json_decode($response, true);

    if ($data && isset($data['author']) && isset($data['author']['first_name'])) {
        echo "   ✅ 사용자 정보 포함됨 (author.first_name: {$data['author']['first_name']})\n";
    } else {
        echo "   ❌ 사용자 정보가 없음\n";
        echo "   응답 내용: {$response}\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 3: API 호출 (with_user=true, with_comments=true)
    // ========================================================================
    echo "🧪 테스트 3: API 호출 (with_user=true, with_comments=true)\n";

    $url = "https://local.sonub.com/api.php?func=get_post&post_id={$postId}&with_user=true&with_comments=true";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        echo "   ✅ HTTP 상태 코드: 200 (성공)\n";
    } else {
        echo "   ❌ HTTP 상태 코드: {$http_code} (실패)\n";
        exit(1);
    }

    $data = json_decode($response, true);

    if ($data && isset($data['author']) && isset($data['comments'])) {
        echo "   ✅ 사용자 정보와 댓글 모두 포함됨\n";
        echo "   - author.first_name: {$data['author']['first_name']}\n";
        echo "   - comments 개수: " . count($data['comments']) . "\n";
    } else {
        echo "   ❌ 사용자 정보 또는 댓글이 없음\n";
        echo "   응답 내용: {$response}\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 4: 타입 변환 확인 (post_id가 문자열로 전달되어도 int로 변환)
    // ========================================================================
    echo "🧪 테스트 4: 타입 변환 확인 (post_id 문자열 → int)\n";

    // post_id를 문자열로 전달
    $url = "https://local.sonub.com/api.php?func=get_post&post_id={$postId}&with_user=false&with_comments=false";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        echo "   ✅ HTTP 상태 코드: 200 (성공)\n";
        echo "   ✅ post_id가 문자열로 전달되어도 int로 변환됨 (타입 에러 없음)\n";
    } else {
        echo "   ❌ HTTP 상태 코드: {$http_code} (실패)\n";
        echo "   응답 내용: {$response}\n";
        exit(1);
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 API 엔드포인트 테스트 통과!\n\n";
    echo "✅ 검증 완료:\n";
    echo "   - ✅ api.php의 spread operator가 정상 작동\n";
    echo "   - ✅ inter_params()가 올바른 파라미터 배열 반환\n";
    echo "   - ✅ post_id 타입 변환 (문자열 → int)\n";
    echo "   - ✅ with_user, with_comments 불리언 파라미터 정상 전달\n";

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   스택 트레이스:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
