<?php
/**
 * Font Awesome 아이콘 표시 E2E 테스트
 *
 * 테스트 대상: /widgets/post/post-list-create.php
 * 테스트 목적: Font Awesome 아이콘이 웹 브라우저에서 올바르게 표시되는지 확인
 */

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

$base_url = 'https://local.sonub.com';

echo "🧪 Font Awesome 아이콘 표시 E2E 테스트\n";
echo "==========================================\n\n";

// 테스트 1: 게시물 목록 페이지 접근
echo "🧪 테스트 1: 게시물 목록 페이지 접근\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/post/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 로컬 개발 환경용
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "   ✅ HTTP 상태 코드: 200 (성공)\n";
} else {
    echo "   ❌ HTTP 상태 코드: $http_code (실패)\n";
    exit(1);
}

// 테스트 2: Font Awesome CSS 파일 로드 확인
echo "\n🧪 테스트 2: Font Awesome CSS 파일 로드 확인\n";

if (str_contains($response, 'fontawesome') || str_contains($response, 'font-awesome')) {
    echo "   ✅ Font Awesome CSS 파일이 페이지에 포함됨\n";
} else {
    echo "   ❌ Font Awesome CSS 파일을 찾을 수 없음\n";
    echo "   ⚠️  Font Awesome이 로드되지 않았을 수 있습니다\n";
}

// 테스트 3: Font Awesome 아이콘 요소 확인 (fa-camera)
echo "\n🧪 테스트 3: Font Awesome 아이콘 요소 확인 (fa-camera)\n";

if (str_contains($response, 'fa-solid fa-camera')) {
    echo "   ✅ Font Awesome 아이콘 클래스 발견: 'fa-solid fa-camera'\n";
} else {
    echo "   ❌ Font Awesome 아이콘 클래스를 찾을 수 없음\n";
    exit(1);
}

// 테스트 4: Font Awesome 아이콘 HTML 구조 확인
echo "\n🧪 테스트 4: Font Awesome 아이콘 HTML 구조 확인\n";

if (str_contains($response, '<i class="fa-solid fa-camera"')) {
    echo "   ✅ Font Awesome 아이콘 HTML 구조 정상\n";
} else {
    echo "   ❌ Font Awesome 아이콘 HTML 구조를 찾을 수 없음\n";
    exit(1);
}

// 테스트 5: Font Awesome CDN 또는 로컬 파일 확인
echo "\n🧪 테스트 5: Font Awesome CDN 또는 로컬 파일 확인\n";

$font_awesome_patterns = [
    'cdnjs.cloudflare.com/ajax/libs/font-awesome',
    'cdn.jsdelivr.net/npm/@fortawesome',
    'use.fontawesome.com',
    '/css/fontawesome',
    '/font-awesome',
    'all.min.css',
    'fontawesome.min.css'
];

$found_font_awesome = false;
foreach ($font_awesome_patterns as $pattern) {
    if (str_contains($response, $pattern)) {
        echo "   ✅ Font Awesome 리소스 발견: '$pattern'\n";
        $found_font_awesome = true;
        break;
    }
}

if (!$found_font_awesome) {
    echo "   ❌ Font Awesome 리소스를 찾을 수 없음\n";
    echo "   ⚠️  Font Awesome CSS 파일이 로드되지 않았습니다!\n";
    echo "\n";
    echo "해결 방법:\n";
    echo "1. index.php 또는 head.php에 Font Awesome CDN을 추가하세요:\n";
    echo "   <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css\">\n";
    echo "2. 또는 Font Awesome 7.1 Pro를 로드하세요 (CLAUDE.md에서 언급됨):\n";
    echo "   <link rel=\"stylesheet\" href=\"/path/to/fontawesome-7.1/css/all.min.css\">\n";
    exit(1);
}

// 테스트 6: 웹폰트 파일 경로 확인
echo "\n🧪 테스트 6: Font Awesome 웹폰트 경로 확인\n";

if (str_contains($response, 'webfonts') || str_contains($response, 'fonts')) {
    echo "   ✅ 웹폰트 경로 발견\n";
} else {
    echo "   ⚠️  웹폰트 경로를 찾을 수 없음 (선택 사항)\n";
}

// 테스트 결과 출력
echo "\n==========================================\n";
echo "🎉 모든 E2E 테스트 통과!\n\n";

echo "📝 추가 확인 사항:\n";
echo "1. 웹 브라우저 개발자 도구(F12)를 열어 Console 탭에서 에러 확인\n";
echo "2. Network 탭에서 Font Awesome CSS 파일이 200 상태로 로드되는지 확인\n";
echo "3. Network 탭에서 웹폰트 파일(.woff, .woff2)이 로드되는지 확인\n";
echo "4. CSS 파일의 경로가 올바른지 확인 (404 에러 여부)\n";
echo "5. CORS 정책으로 인해 CDN에서 폰트 로드가 차단되는지 확인\n";
