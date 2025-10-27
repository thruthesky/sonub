<?php

/**
 * 실제 이미지가 포함된 게시글 생성 및 로드 테스트
 *
 * 스크립트: create_posts.sh에서 생성한 게시글들이
 * 실제 Picsum 이미지를 포함하고 있는지 테스트합니다.
 *
 * @package Sonub
 * @subpackage Tests
 */

// 초기화
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__FILE__, 3));
}

if (!function_exists('db')) {
    require ROOT_DIR . '/init.php';
}

// ========================================================================
// 테스트 결과 저장
// ========================================================================

$results = [];
$passed = 0;
$failed = 0;

// 테스트 헬퍼 함수
function test_assertion($description, $assertion, $expectedValue = null, $actualValue = null) {
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
// 테스트 1: API에서 게시글 조회 및 이미지 URL 확인
// ========================================================================

echo "\n📋 [테스트 1] API에서 게시글 조회 및 이미지 URL 확인\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// API 호출 (curl 사용)
$curlCmd = 'curl -s -k "https://local.sonub.com/api.php" -X POST -H "Content-Type: application/json" -d \'{"func":"list_posts","category":"discussion","limit":5}\'';
$apiResponse = shell_exec($curlCmd);

test_assertion(
    "API 응답이 유효한 JSON",
    json_decode($apiResponse, true) !== null,
    true,
    json_decode($apiResponse, true) !== null
);

$responseData = json_decode($apiResponse, true);

if ($responseData && isset($responseData['posts'])) {
    $posts = $responseData['posts'];

    test_assertion(
        "최소 1개 이상의 게시글이 반환됨",
        count($posts) > 0,
        true,
        count($posts) > 0
    );

    // ========================================================================
    // 테스트 2: 게시글의 이미지 URL이 Picsum인지 확인
    // ========================================================================

    echo "\n📋 [테스트 2] 게시글의 이미지 URL이 Picsum 형식인지 확인\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $postWithImages = null;
    $picsumImageCount = 0;
    $totalImageCount = 0;

    foreach ($posts as $post) {
        if (isset($post['files']) && is_array($post['files']) && count($post['files']) > 0) {
            $postWithImages = $post;
            $totalImageCount += count($post['files']);

            foreach ($post['files'] as $imageUrl) {
                if (strpos($imageUrl, 'picsum.photos') !== false) {
                    $picsumImageCount++;
                }
            }
        }
    }

    test_assertion(
        "이미지를 포함한 게시글이 존재함",
        $postWithImages !== null,
        true,
        $postWithImages !== null
    );

    if ($postWithImages) {
        test_assertion(
            "게시글의 이미지가 Picsum 형식임",
            $picsumImageCount > 0,
            true,
            $picsumImageCount > 0
        );

        echo "  게시글 ID: {$postWithImages['id']}\n";
        echo "  이미지 개수: " . count($postWithImages['files']) . "개\n";
        echo "  Picsum 이미지: {$picsumImageCount}개\n";

        // ========================================================================
        // 테스트 3: Picsum URL 형식 검증
        // ========================================================================

        echo "\n📋 [테스트 3] Picsum URL 형식 검증\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        foreach ($postWithImages['files'] as $imageUrl) {
            // Picsum URL 형식: https://picsum.photos/{width}/{height}?random={id}
            $picsumPattern = '/^https:\/\/picsum\.photos\/\d+\/\d+\?random=\d+/';
            $isPicsumUrl = preg_match($picsumPattern, $imageUrl) === 1;

            test_assertion(
                "이미지 URL이 Picsum 형식 (https://picsum.photos/{width}/{height}?random={id})",
                $isPicsumUrl,
                true,
                $isPicsumUrl
            );

            // 이미지 크기 추출
            if (preg_match('/picsum\.photos\/(\d+)\/(\d+)/', $imageUrl, $matches)) {
                $width = $matches[1];
                $height = $matches[2];
                echo "  이미지 크기: {$width}x{$height}\n";

                // 크기가 합리적인지 확인
                test_assertion(
                    "이미지 너비가 200px 이상 1000px 이하",
                    $width >= 200 && $width <= 1000,
                    true,
                    $width >= 200 && $width <= 1000
                );

                test_assertion(
                    "이미지 높이가 150px 이상 1000px 이하",
                    $height >= 150 && $height <= 1000,
                    true,
                    $height >= 150 && $height <= 1000
                );
            }
        }

        // ========================================================================
        // 테스트 4: 이미지 URL 접근성 확인
        // ========================================================================

        echo "\n📋 [테스트 4] 이미지 URL이 실제로 접근 가능한지 확인\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        $imageUrl = $postWithImages['files'][0];
        $headerCmd = "curl -s -k -I '$imageUrl' | head -1";
        $headerResponse = shell_exec($headerCmd);

        $isAccessible = strpos($headerResponse, '200') !== false || strpos($headerResponse, '302') !== false;

        test_assertion(
            "이미지 URL에 HTTP 200 또는 302 응답",
            $isAccessible,
            true,
            $isAccessible
        );

        if ($headerResponse) {
            echo "  응답 헤더: " . trim($headerResponse) . "\n";
        }
    }
}

// ========================================================================
// 테스트 5: 카테고리별 게시글 이미지 다양성
// ========================================================================

echo "\n📋 [테스트 5] 다양한 카테고리에서 게시글 생성 확인\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$categoriesToTest = ['discussion', 'story', 'cooking'];
$categoryResults = [];

foreach ($categoriesToTest as $category) {
    $curlCmd = "curl -s -k 'https://local.sonub.com/api.php' -X POST -H 'Content-Type: application/json' -d '{\"func\":\"list_posts\",\"category\":\"$category\",\"limit\":3}'";
    $response = shell_exec($curlCmd);
    $data = json_decode($response, true);

    if ($data && isset($data['posts'])) {
        $postsInCategory = count($data['posts']);
        $postsWithImages = 0;

        foreach ($data['posts'] as $post) {
            if (isset($post['files']) && is_array($post['files']) && count($post['files']) > 0) {
                // Picsum 이미지가 있는지 확인
                foreach ($post['files'] as $imageUrl) {
                    if (strpos($imageUrl, 'picsum.photos') !== false) {
                        $postsWithImages++;
                        break;
                    }
                }
            }
        }

        $categoryResults[$category] = [
            'total_posts' => $postsInCategory,
            'posts_with_images' => $postsWithImages
        ];

        echo "  카테고리: $category\n";
        echo "    총 게시글: $postsInCategory개\n";
        echo "    이미지 포함: $postsWithImages개\n";
    }
}

test_assertion(
    "모든 카테고리에서 게시글이 생성됨",
    count($categoryResults) === count($categoriesToTest),
    count($categoriesToTest),
    count($categoryResults)
);

// ========================================================================
// 테스트 6: 더미 이미지가 아닌 실제 이미지인지 확인
// ========================================================================

echo "\n📋 [테스트 6] DummyImage(더미) vs Picsum(실제) 이미지 확인\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$dummyImageCount = 0;
$picsumImageCount = 0;

$curlCmd = 'curl -s -k "https://local.sonub.com/api.php" -X POST -H "Content-Type: application/json" -d \'{"func":"list_posts","limit":10}\'';
$allPostsResponse = shell_exec($curlCmd);
$allPostsData = json_decode($allPostsResponse, true);

if ($allPostsData && isset($allPostsData['posts'])) {
    foreach ($allPostsData['posts'] as $post) {
        if (isset($post['files']) && is_array($post['files'])) {
            foreach ($post['files'] as $imageUrl) {
                if (strpos($imageUrl, 'dummyimage.com') !== false) {
                    $dummyImageCount++;
                } elseif (strpos($imageUrl, 'picsum.photos') !== false) {
                    $picsumImageCount++;
                }
            }
        }
    }
}

echo "  총 게시글 수: " . count($allPostsData['posts']) . "\n";
echo "  DummyImage(더미): {$dummyImageCount}개\n";
echo "  Picsum(실제): {$picsumImageCount}개\n";

test_assertion(
    "실제 Picsum 이미지가 사용되고 있음",
    $picsumImageCount > 0,
    true,
    $picsumImageCount > 0
);

test_assertion(
    "더 이상 DummyImage(더미)가 사용되지 않음",
    $dummyImageCount === 0,
    0,
    $dummyImageCount
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
