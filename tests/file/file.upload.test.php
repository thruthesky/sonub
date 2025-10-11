<?php
/**
 * get_unique_filename() 함수 테스트
 *
 * 실행 방법: php tests/file/file.upload.test.php
 */

// init.php 로드 (프로젝트 루트 기준 상대 경로)
include __DIR__ . '/../../init.php';

echo "=== get_unique_filename() 함수 테스트 시작 ===\n\n";

// 테스트용 임시 디렉토리 생성
$test_dir = ROOT_DIR . '/tests/file/temp/';
if (!is_dir($test_dir)) {
    mkdir($test_dir, 0755, true);
    echo "✅ 테스트 디렉토리 생성: {$test_dir}\n\n";
}

// 기존 테스트 파일 정리
$files = glob($test_dir . '*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
echo "✅ 기존 테스트 파일 정리 완료\n\n";

// ==========================================
// 테스트 1: 중복 파일이 없는 경우
// ==========================================
echo "📋 테스트 1: 중복 파일이 없는 경우\n";
echo str_repeat('-', 50) . "\n";

$result1 = get_unique_filename($test_dir, 'test.jpg');
$expected1 = $test_dir . 'test.jpg';

if ($result1 === $expected1) {
    echo "✅ 성공: 원본 파일명 반환\n";
    echo "   결과: {$result1}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected1}\n";
    echo "   결과값: {$result1}\n";
}
echo "\n";

// ==========================================
// 테스트 2: 동일한 파일명이 1개 존재하는 경우
// ==========================================
echo "📋 테스트 2: 동일한 파일명이 1개 존재하는 경우\n";
echo str_repeat('-', 50) . "\n";

// test.jpg 파일 생성
file_put_contents($test_dir . 'test.jpg', 'test content');
echo "   생성된 파일: test.jpg\n";

$result2 = get_unique_filename($test_dir, 'test.jpg');
$expected2 = $test_dir . 'test-1.jpg';

if ($result2 === $expected2) {
    echo "✅ 성공: -1 추가된 파일명 반환\n";
    echo "   결과: {$result2}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected2}\n";
    echo "   결과값: {$result2}\n";
}
echo "\n";

// ==========================================
// 테스트 3: 동일한 파일명이 여러 개 존재하는 경우
// ==========================================
echo "📋 테스트 3: 동일한 파일명이 여러 개 존재하는 경우\n";
echo str_repeat('-', 50) . "\n";

// test-1.jpg, test-2.jpg 파일 생성
file_put_contents($test_dir . 'test-1.jpg', 'test content 1');
file_put_contents($test_dir . 'test-2.jpg', 'test content 2');
echo "   생성된 파일: test.jpg, test-1.jpg, test-2.jpg\n";

$result3 = get_unique_filename($test_dir, 'test.jpg');
$expected3 = $test_dir . 'test-3.jpg';

if ($result3 === $expected3) {
    echo "✅ 성공: -3 추가된 파일명 반환\n";
    echo "   결과: {$result3}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected3}\n";
    echo "   결과값: {$result3}\n";
}
echo "\n";

// ==========================================
// 테스트 4: 확장자가 없는 파일
// ==========================================
echo "📋 테스트 4: 확장자가 없는 파일\n";
echo str_repeat('-', 50) . "\n";

// README 파일 생성
file_put_contents($test_dir . 'README', 'readme content');
echo "   생성된 파일: README\n";

$result4 = get_unique_filename($test_dir, 'README');
$expected4 = $test_dir . 'README-1';

if ($result4 === $expected4) {
    echo "✅ 성공: 확장자 없는 파일에 -1 추가\n";
    echo "   결과: {$result4}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected4}\n";
    echo "   결과값: {$result4}\n";
}
echo "\n";

// ==========================================
// 테스트 5: 긴 확장자를 가진 파일
// ==========================================
echo "📋 테스트 5: 긴 확장자를 가진 파일\n";
echo str_repeat('-', 50) . "\n";

// archive.tar.gz 파일 생성
file_put_contents($test_dir . 'archive.tar.gz', 'archive content');
echo "   생성된 파일: archive.tar.gz\n";

$result5 = get_unique_filename($test_dir, 'archive.tar.gz');
$expected5 = $test_dir . 'archive.tar-1.gz';

// pathinfo()는 마지막 확장자만 인식하므로 .tar-1.gz가 됨
if ($result5 === $expected5) {
    echo "✅ 성공: 마지막 확장자 기준으로 처리\n";
    echo "   결과: {$result5}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected5}\n";
    echo "   결과값: {$result5}\n";
}
echo "\n";

// ==========================================
// 테스트 6: 한글 파일명
// ==========================================
echo "📋 테스트 6: 한글 파일명\n";
echo str_repeat('-', 50) . "\n";

// 사진.jpg 파일 생성
file_put_contents($test_dir . '사진.jpg', 'photo content');
echo "   생성된 파일: 사진.jpg\n";

$result6 = get_unique_filename($test_dir, '사진.jpg');
$expected6 = $test_dir . '사진-1.jpg';

if ($result6 === $expected6) {
    echo "✅ 성공: 한글 파일명에 -1 추가\n";
    echo "   결과: {$result6}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected6}\n";
    echo "   결과값: {$result6}\n";
}
echo "\n";

// ==========================================
// 테스트 7: 특수문자가 포함된 파일명
// ==========================================
echo "📋 테스트 7: 특수문자가 포함된 파일명\n";
echo str_repeat('-', 50) . "\n";

// my-photo_2024.jpg 파일 생성
file_put_contents($test_dir . 'my-photo_2024.jpg', 'photo content');
echo "   생성된 파일: my-photo_2024.jpg\n";

$result7 = get_unique_filename($test_dir, 'my-photo_2024.jpg');
$expected7 = $test_dir . 'my-photo_2024-1.jpg';

if ($result7 === $expected7) {
    echo "✅ 성공: 특수문자 파일명에 -1 추가\n";
    echo "   결과: {$result7}\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: {$expected7}\n";
    echo "   결과값: {$result7}\n";
}
echo "\n";

// ==========================================
// 테스트 8: 연속 호출 테스트 (실제 업로드 시뮬레이션)
// ==========================================
echo "📋 테스트 8: 연속 호출 테스트 (5개 파일 업로드 시뮬레이션)\n";
echo str_repeat('-', 50) . "\n";

$base_filename = 'document.pdf';
$uploaded_files = [];

for ($i = 0; $i < 5; $i++) {
    $unique_path = get_unique_filename($test_dir, $base_filename);
    file_put_contents($unique_path, "content {$i}");
    $uploaded_files[] = basename($unique_path);
    echo "   {$i}번째 업로드: " . basename($unique_path) . "\n";
}

$expected_files = ['document.pdf', 'document-1.pdf', 'document-2.pdf', 'document-3.pdf', 'document-4.pdf'];

if ($uploaded_files === $expected_files) {
    echo "✅ 성공: 모든 파일이 고유한 이름으로 저장됨\n";
} else {
    echo "❌ 실패\n";
    echo "   기대값: " . implode(', ', $expected_files) . "\n";
    echo "   결과값: " . implode(', ', $uploaded_files) . "\n";
}
echo "\n";

// ==========================================
// 테스트 완료 및 정리
// ==========================================
echo "=== 모든 테스트 완료 ===\n\n";

echo "📁 생성된 테스트 파일 목록:\n";
echo str_repeat('-', 50) . "\n";
$files = scandir($test_dir);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "   - {$file}\n";
    }
}
echo "\n";

// 테스트 파일 정리 여부 확인
echo "❓ 테스트 파일을 삭제하시겠습니까? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$cleanup = trim(strtolower($line));
fclose($handle);

if ($cleanup === 'y' || $cleanup === 'yes') {
    $files = glob($test_dir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir($test_dir);
    echo "✅ 테스트 디렉토리 및 파일 삭제 완료\n";
} else {
    echo "ℹ️  테스트 파일이 보존됩니다: {$test_dir}\n";
}

echo "\n테스트 종료.\n";
