<?php
/**
 * Font Awesome 아이콘 디버그 테스트
 *
 * 실제 렌더링된 HTML을 출력하여 Font Awesome 아이콘이 왜 표시되지 않는지 확인
 */

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

$base_url = 'https://local.sonub.com';

echo "🔍 Font Awesome 아이콘 디버그 테스트\n";
echo "==========================================\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/post/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo "❌ 페이지 접근 실패: HTTP $http_code\n";
    exit(1);
}

// Font Awesome 관련 HTML 추출
echo "📋 Font Awesome CSS 로드 확인:\n";
echo "==========================================\n";
preg_match_all('/<link[^>]*fontawesome[^>]*>/i', $response, $css_matches);
if (!empty($css_matches[0])) {
    foreach ($css_matches[0] as $css) {
        echo htmlspecialchars($css) . "\n";
    }
} else {
    echo "❌ Font Awesome CSS를 찾을 수 없음\n";
}

echo "\n📋 fa-solid, fa-camera 관련 HTML 추출:\n";
echo "==========================================\n";

// fa-camera가 포함된 줄 찾기
$lines = explode("\n", $response);
foreach ($lines as $line) {
    if (str_contains($line, 'fa-camera') || str_contains($line, 'fa-solid')) {
        echo trim($line) . "\n";
    }
}

// 아이콘 요소 패턴 검색
echo "\n📋 <i> 태그 검색:\n";
echo "==========================================\n";
preg_match_all('/<i[^>]*class="[^"]*fa-[^"]*"[^>]*>/i', $response, $icon_matches);
if (!empty($icon_matches[0])) {
    foreach ($icon_matches[0] as $icon) {
        echo htmlspecialchars($icon) . "\n";
    }
} else {
    echo "❌ Font Awesome 아이콘 요소를 찾을 수 없음\n";
}

// 전체 HTML에서 위젯 폼 부분 추출
echo "\n📋 게시물 작성 폼 HTML:\n";
echo "==========================================\n";
preg_match('/<form[^>]*>.*?<\/form>/s', $response, $form_matches);
if (!empty($form_matches[0])) {
    $form_html = $form_matches[0];
    // 가독성을 위해 일부만 출력
    $form_preview = substr($form_html, 0, 500);
    echo htmlspecialchars($form_preview);
    if (strlen($form_html) > 500) {
        echo "\n... (폼 HTML이 더 있음)\n";
    }
} else {
    echo "❌ <form> 태그를 찾을 수 없음\n";
}

// Font Awesome 스크립트 확인
echo "\n\n📋 Font Awesome 스크립트 확인:\n";
echo "==========================================\n";
preg_match_all('/<script[^>]*fontawesome[^>]*>/i', $response, $script_matches);
if (!empty($script_matches[0])) {
    foreach ($script_matches[0] as $script) {
        echo htmlspecialchars($script) . "\n";
    }
} else {
    echo "⚠️  Font Awesome 스크립트를 찾을 수 없음 (선택 사항)\n";
}

echo "\n==========================================\n";
echo "✅ 디버그 정보 출력 완료\n";
