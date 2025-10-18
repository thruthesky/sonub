<?php

/**
 * thumbnail() 함수 URL 인코딩 테스트
 *
 * 이 테스트는 thumbnail() 함수가 URL 인코딩 없이 올바르게 URL을 생성하는지,
 * 그리고 thumbnail.php가 URL 디코딩을 올바르게 처리하는지 검증합니다.
 *
 * 실행 방법:
 * php tests/file/thumbnail-url-encoding.test.php
 */

include __DIR__ . '/../../init.php';

/**
 * 테스트 결과를 출력하는 헬퍼 함수
 */
function assert_test(string $name, bool $condition, string $message = ''): void
{
    if ($condition) {
        echo "✅ PASS: {$name}\n";
    } else {
        echo "❌ FAIL: {$name}\n";
        if ($message) {
            echo "   └─ {$message}\n";
        }
    }
}

echo "=== thumbnail() 함수 URL 인코딩 테스트 시작 ===\n\n";

$passCount = 0;
$totalCount = 0;

// 테스트 1: 기본 사용 (URL 인코딩 안함)
$totalCount++;
$url = thumbnail('/var/uploads/533/cat.jpg');
$expected = '/thumbnail.php?src=/var/uploads/533/cat.jpg';
$condition = $url === $expected;
assert_test('기본 사용 (URL 인코딩 안함)', $condition, "예상: {$expected}, 실제: {$url}");
if ($condition) $passCount++;

// 테스트 2: 너비 파라미터
$totalCount++;
$url = thumbnail('/var/uploads/533/cat.jpg', 300);
$expected = '/thumbnail.php?src=/var/uploads/533/cat.jpg&w=300';
$condition = $url === $expected;
assert_test('너비 파라미터 (w=300)', $condition, "예상: {$expected}, 실제: {$url}");
if ($condition) $passCount++;

// 테스트 3: 너비+높이 파라미터
$totalCount++;
$url = thumbnail('/var/uploads/533/cat.jpg', 300, 200);
$expected = '/thumbnail.php?src=/var/uploads/533/cat.jpg&w=300&h=200';
$condition = $url === $expected;
assert_test('너비+높이 (w=300, h=200)', $condition, "예상: {$expected}, 실제: {$url}");
if ($condition) $passCount++;

// 테스트 4: 실제 파일로 HTTP 요청 테스트 (URL 인코딩 안된 경로)
$totalCount++;
$testFile = ROOT_DIR . '/var/uploads/31/post-image-68f0b9d4c7dd6.jpg';
if (file_exists($testFile)) {
    $thumbnailUrl = thumbnail('/var/uploads/31/post-image-68f0b9d4c7dd6.jpg', 100);
    $fullUrl = 'https://local.sonub.com' . $thumbnailUrl;

    // curl로 HTTP 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $error = curl_error($ch);
    curl_close($ch);

    $condition = $httpCode === 200 && str_contains($contentType ?? '', 'image/webp');
    assert_test('HTTP 요청 (URL 인코딩 안된 경로)', $condition, "HTTP {$httpCode}, Content-Type: {$contentType}, URL: {$fullUrl}" . ($error ? ", Error: {$error}" : ''));
    if ($condition) $passCount++;
} else {
    echo "⏭️  SKIP: HTTP 요청 테스트 (테스트 파일이 없음: {$testFile})\n";
}

// 테스트 5: 실제 파일로 HTTP 요청 테스트 (URL 인코딩된 경로)
$totalCount++;
if (file_exists($testFile)) {
    // urlencode된 URL로 직접 요청
    $encodedPath = urlencode('/var/uploads/31/post-image-68f0b9d4c7dd6.jpg');
    $fullUrl = 'https://local.sonub.com/thumbnail.php?src=' . $encodedPath . '&w=100';

    // curl로 HTTP 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $error = curl_error($ch);
    curl_close($ch);

    $condition = $httpCode === 200 && str_contains($contentType ?? '', 'image/webp');
    assert_test('HTTP 요청 (URL 인코딩된 경로)', $condition, "HTTP {$httpCode}, Content-Type: {$contentType}, URL: {$fullUrl}" . ($error ? ", Error: {$error}" : ''));
    if ($condition) $passCount++;
} else {
    echo "⏭️  SKIP: HTTP 요청 테스트 (URL 인코딩) (테스트 파일이 없음: {$testFile})\n";
}

// 테스트 6: 특수 문자가 포함된 파일명 (공백, 한글 등)
$totalCount++;
$url = thumbnail('/var/uploads/533/사진 test.jpg', 200);
$expected = '/thumbnail.php?src=/var/uploads/533/사진 test.jpg&w=200';
$condition = $url === $expected;
assert_test('특수 문자 파일명 (공백, 한글)', $condition, "예상: {$expected}, 실제: {$url}");
if ($condition) $passCount++;

// 테스트 7: 모든 파라미터 지정
$totalCount++;
$url = thumbnail('/var/uploads/533/cat.jpg', 300, 200, 'cover', 90, 'ffffff');
$expected = '/thumbnail.php?src=/var/uploads/533/cat.jpg&w=300&h=200&fit=cover&q=90&bg=ffffff';
$condition = $url === $expected;
assert_test('모든 파라미터 지정', $condition, "예상: {$expected}, 실제: {$url}");
if ($condition) $passCount++;

// 테스트 8: var/uploads/ 경로 없음 (외부 URL)
$totalCount++;
$externalUrl = 'https://example.com/image.jpg';
$url = thumbnail($externalUrl);
$condition = $url === $externalUrl;
assert_test('외부 URL (var/uploads/ 없음)', $condition, "예상: {$externalUrl}, 실제: {$url}");
if ($condition) $passCount++;

echo "\n총 테스트: {$totalCount}\n";
echo "통과: {$passCount}\n";
echo "실패: " . ($totalCount - $passCount) . "\n\n";

if ($passCount === $totalCount) {
    echo "✅ 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "❌ 일부 테스트 실패\n";
    exit(1);
}
