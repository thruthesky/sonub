<?php

/**
 * thumbnail() 함수 테스트
 *
 * lib/file/file.functions.php의 thumbnail() 함수를 테스트합니다.
 */

include __DIR__ . '/../../init.php';

echo "=== thumbnail() 함수 테스트 시작 ===\n\n";

// 테스트 카운터
$totalTests = 0;
$passedTests = 0;

/**
 * 테스트 함수
 */
function test(string $name, string $actual, string $expected): void
{
    global $totalTests, $passedTests;
    $totalTests++;

    if ($actual === $expected) {
        $passedTests++;
        echo "✅ PASS: $name\n";
        echo "   결과: $actual\n\n";
    } else {
        echo "❌ FAIL: $name\n";
        echo "   예상: $expected\n";
        echo "   실제: $actual\n\n";
    }
}

// 테스트 1: 기본 사용 (파라미터 없음)
$result = thumbnail('/var/uploads/533/cat.jpg');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg');
test('기본 사용 (파라미터 없음)', $result, $expected);

// 테스트 2: 너비만 지정
$result = thumbnail('/var/uploads/533/cat.jpg', 300);
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300';
test('너비만 지정 (w=300)', $result, $expected);

// 테스트 3: 너비+높이 지정
$result = thumbnail('/var/uploads/533/cat.jpg', 300, 200);
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300&h=200';
test('너비+높이 지정 (w=300, h=200)', $result, $expected);

// 테스트 4: cover 모드
$result = thumbnail('/var/uploads/533/cat.jpg', 300, 200, 'cover');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300&h=200&fit=cover';
test('cover 모드', $result, $expected);

// 테스트 5: contain 모드 + 배경색
$result = thumbnail('/var/uploads/533/cat.jpg', 400, 400, 'contain', null, 'f0f0f0');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=400&h=400&fit=contain&bg=f0f0f0';
test('contain 모드 + 배경색', $result, $expected);

// 테스트 6: scale 모드
$result = thumbnail('/var/uploads/533/cat.jpg', 600, 600, 'scale');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=600&h=600&fit=scale';
test('scale 모드', $result, $expected);

// 테스트 7: 고품질 이미지 (q=95)
$result = thumbnail('/var/uploads/533/artwork.jpg', 800, null, null, 95);
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/artwork.jpg') . '&w=800&q=95';
test('고품질 이미지 (q=95)', $result, $expected);

// 테스트 8: 모든 옵션 지정
$result = thumbnail('/var/uploads/533/cat.jpg', 300, 200, 'cover', 90, 'ffffff');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300&h=200&fit=cover&q=90&bg=ffffff';
test('모든 옵션 지정', $result, $expected);

// 테스트 9: var/uploads/ 경로 없음 (원본 URL 그대로 반환)
$result = thumbnail('https://example.com/image.jpg');
$expected = 'https://example.com/image.jpg';
test('var/uploads/ 경로 없음 (원본 반환)', $result, $expected);

// 테스트 10: 유효하지 않은 fit 모드 (무시됨)
$result = thumbnail('/var/uploads/533/cat.jpg', 300, 200, 'invalid');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300&h=200';
test('유효하지 않은 fit 모드 (무시됨)', $result, $expected);

// 테스트 11: 유효하지 않은 품질 (무시됨)
$result = thumbnail('/var/uploads/533/cat.jpg', 300, null, null, 150);
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300';
test('유효하지 않은 품질 값 (무시됨)', $result, $expected);

// 테스트 12: 유효하지 않은 배경색 (무시됨)
$result = thumbnail('/var/uploads/533/cat.jpg', 300, null, 'contain', null, 'invalid');
$expected = get_thumbnail_url() . '?src=' . urlencode('/var/uploads/533/cat.jpg') . '&w=300&fit=contain';
test('유효하지 않은 배경색 (무시됨)', $result, $expected);

// 결과 요약
echo "\n=== 테스트 결과 요약 ===\n";
echo "총 테스트: $totalTests\n";
echo "통과: $passedTests\n";
echo "실패: " . ($totalTests - $passedTests) . "\n";

if ($totalTests === $passedTests) {
    echo "\n✅ 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "\n❌ 일부 테스트 실패\n";
    exit(1);
}
