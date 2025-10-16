<?php

/**
 * 홈페이지 E2E 테스트
 *
 * 홈페이지 '/'가 정상적으로 로드되는지 확인하는 테스트입니다.
 *
 * 실행 방법:
 * php tests/e2e/deploy-tests/home.test.php
 */

// 프로젝트 루트의 init.php 로드
require_once __DIR__ . '/../../../init.php';

echo "========================================\n";
echo "홈페이지 E2E 테스트 시작\n";
echo "========================================\n\n";

// 테스트 URL
$test_url = config()->test->url;

echo "테스트 URL: $test_url\n\n";

try {
    // cURL을 사용하여 홈페이지 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 개발 환경용
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 개발 환경용

    // HTTP 응답 코드 가져오기
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // 에러 확인
    if ($error) {
        throw new Exception("cURL 에러: $error");
    }

    // 1. HTTP 상태 코드 확인
    echo "✓ 테스트 1: HTTP 상태 코드 확인\n";
    if ($http_code !== 200) {
        throw new Exception("HTTP 상태 코드가 200이 아닙니다. 실제: $http_code");
    }
    echo "  - HTTP 상태 코드: $http_code (OK)\n\n";

    // 2. 응답 본문이 비어있지 않은지 확인
    echo "✓ 테스트 2: 응답 본문 확인\n";
    if (empty($response)) {
        throw new Exception("응답 본문이 비어있습니다.");
    }
    echo "  - 응답 본문 길이: " . strlen($response) . " bytes\n\n";

    // 3. HTML 기본 구조 확인
    echo "✓ 테스트 3: HTML 기본 구조 확인\n";
    if (strpos($response, '<!DOCTYPE html>') === false) {
        throw new Exception("<!DOCTYPE html> 태그를 찾을 수 없습니다.");
    }
    echo "  - <!DOCTYPE html> 존재\n";

    if (strpos($response, '<html') === false) {
        throw new Exception("<html> 태그를 찾을 수 없습니다.");
    }
    echo "  - <html> 태그 존재\n";

    if (strpos($response, '</html>') === false) {
        throw new Exception("</html> 태그를 찾을 수 없습니다.");
    }
    echo "  - </html> 태그 존재\n\n";

    // 4. 타이틀 확인
    echo "✓ 테스트 4: 페이지 타이틀 확인\n";
    if (strpos($response, '<title>') === false) {
        throw new Exception("<title> 태그를 찾을 수 없습니다.");
    }

    preg_match('/<title>(.*?)<\/title>/s', $response, $matches);
    $title = $matches[1] ?? '(없음)';
    echo "  - 페이지 타이틀: $title\n\n";

    // 5. 필수 페이지 구조 확인
    echo "✓ 테스트 5: 필수 페이지 구조 확인\n";

    // #page-header 확인
    if (strpos($response, 'id="page-header"') === false) {
        throw new Exception("id='page-header' 요소를 찾을 수 없습니다.");
    }
    echo "  - #page-header 존재\n";

    // #page-footer 확인
    if (strpos($response, 'id="page-footer"') === false) {
        throw new Exception("id='page-footer' 요소를 찾을 수 없습니다.");
    }
    echo "  - #page-footer 존재\n";

    // <main> 태그 확인
    if (strpos($response, '<main') === false) {
        throw new Exception("<main> 태그를 찾을 수 없습니다.");
    }
    echo "  - <main> 태그 존재\n\n";

    // 6. 주요 콘텐츠 확인 (선택적)
    echo "✓ 테스트 6: 주요 콘텐츠 확인\n";

    // Bootstrap이 로드되는지 확인
    if (strpos($response, 'bootstrap') !== false) {
        echo "  - Bootstrap 로드 확인됨\n";
    }

    // Vue.js가 로드되는지 확인
    if (strpos($response, 'vue') !== false || strpos($response, 'Vue') !== false) {
        echo "  - Vue.js 로드 확인됨\n";
    }

    // 게시글 작성 위젯 확인
    if (strpos($response, 'post-list-create') !== false) {
        echo "  - 게시글 작성 위젯 확인됨\n";
    }

    // my-wall 영역 확인
    if (strpos($response, 'my-wall') !== false) {
        echo "  - my-wall 영역 확인됨\n";
    }

    echo "\n";

    // 모든 테스트 통과
    echo "========================================\n";
    echo "✅ 모든 테스트 통과!\n";
    echo "========================================\n";
} catch (Exception $e) {
    echo "\n========================================\n";
    echo "❌ 테스트 실패: " . $e->getMessage() . "\n";
    echo "========================================\n";
    exit(1);
}
