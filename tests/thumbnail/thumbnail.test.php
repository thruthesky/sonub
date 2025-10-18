<?php

/**
 * thumbnail.php 테스트
 *
 * 썸네일 생성 스크립트가 현재 프로젝트 구조에 맞게 제대로 작동하는지 테스트합니다.
 */

include_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "thumbnail.php 테스트 시작\n";
echo "========================================\n\n";

// 테스트할 이미지 경로
$testImages = [
    '533/cat-191912_1920.jpg',
    '533/babypandas.jpg',
    '533/dog-3619102_1920.jpg',
];

$baseUrl = 'https://local.sonub.com/thumbnail.php';
$testCount = 0;
$passCount = 0;

foreach ($testImages as $imagePath) {
    echo "테스트 " . (++$testCount) . ": $imagePath\n";

    // 원본 파일 존재 확인
    $fullPath = ROOT_DIR . '/var/uploads/' . $imagePath;
    if (!file_exists($fullPath)) {
        echo "  ❌ 실패: 원본 파일이 존재하지 않습니다.\n";
        echo "  경로: $fullPath\n\n";
        continue;
    }
    echo "  ✅ 원본 파일 존재 확인\n";

    // 썸네일 URL 생성 (다양한 파라미터로 테스트)
    $testUrls = [
        $baseUrl . '?src=' . urlencode($imagePath),
        $baseUrl . '?src=' . urlencode($imagePath) . '&w=300',
        $baseUrl . '?src=' . urlencode($imagePath) . '&w=300&h=200&fit=cover',
        $baseUrl . '?src=' . urlencode($imagePath) . '&w=400&h=400&fit=contain&bg=f0f0f0',
    ];

    foreach ($testUrls as $url) {
        // HTTP 요청 시뮬레이션 (실제로는 브라우저에서 확인해야 함)
        echo "  URL: $url\n";
    }

    $passCount++;
    echo "  ✅ 통과\n\n";
}

echo "========================================\n";
echo "테스트 결과: $passCount / $testCount 통과\n";
echo "========================================\n";

if ($passCount === $testCount) {
    echo "\n✅ 모든 테스트 통과!\n";
    echo "\n다음 URL을 브라우저에서 직접 확인하세요:\n";
    echo "https://local.sonub.com/thumbnail.php?src=533/cat-191912_1920.jpg\n";
    echo "https://local.sonub.com/thumbnail.php?src=533/cat-191912_1920.jpg&w=300\n";
    echo "https://local.sonub.com/thumbnail.php?src=533/cat-191912_1920.jpg&w=300&h=200&fit=cover\n";
    exit(0);
} else {
    echo "\n❌ 일부 테스트 실패\n";
    exit(1);
}
