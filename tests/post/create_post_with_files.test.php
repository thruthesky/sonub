<?php

/**
 * create_post() 함수의 files 항목 테스트
 *
 * 이 테스트는 create_post() 함수에서 files 항목이 올바르게 저장되는지 확인합니다.
 * - 외부 URL (https://)
 * - 로컬 파일 경로 (/var/uploads/)
 * - 콤마로 구분된 여러 파일
 */

include __DIR__ . '/../../init.php';

// ========================================================================
// 테스트 준비
// ========================================================================

echo "\n========================================\n";
echo "create_post() files 항목 테스트 시작\n";
echo "========================================\n\n";

// 테스트 결과 카운터
$total = 0;
$passed = 0;
$failed = 0;

// ========================================================================
// 테스트 1: 외부 URL 파일 첨부
// ========================================================================
echo "테스트 1: 외부 URL 파일 첨부\n";
$total++;

try {
    // 테스트용 사용자 로그인
    login_as_test_user();

    // 외부 URL 파일 첨부
    $post = create_post([
        'category' => 'test',
        'title' => '외부 URL 파일 첨부 테스트',
        'content' => '외부 URL 파일 첨부 테스트입니다.',
        'files' => 'https://example.com/photo.jpg'
    ]);

    // 검증: PostModel 객체 반환 확인
    if (!($post instanceof PostModel)) {
        throw new Exception('PostModel 객체가 반환되지 않았습니다.');
    }

    // 검증: files 필드 확인
    if ($post->files !== 'https://example.com/photo.jpg') {
        throw new Exception("files 값이 올바르지 않습니다. 예상: 'https://example.com/photo.jpg', 실제: '{$post->files}'");
    }

    echo "✅ 통과: 외부 URL 파일이 올바르게 저장되었습니다.\n";
    $passed++;
} catch (Throwable $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    $failed++;
}

// ========================================================================
// 테스트 2: 로컬 파일 경로 첨부
// ========================================================================
echo "\n테스트 2: 로컬 파일 경로 첨부\n";
$total++;

try {
    // 테스트용 사용자 로그인
    login_as_test_user();

    // 로컬 파일 경로 첨부
    $post = create_post([
        'category' => 'test',
        'title' => '로컬 파일 경로 첨부 테스트',
        'content' => '로컬 파일 경로 첨부 테스트입니다.',
        'files' => '/var/uploads/123/document.pdf'
    ]);

    // 검증: PostModel 객체 반환 확인
    if (!($post instanceof PostModel)) {
        throw new Exception('PostModel 객체가 반환되지 않았습니다.');
    }

    // 검증: files 필드 확인
    if ($post->files !== '/var/uploads/123/document.pdf') {
        throw new Exception("files 값이 올바르지 않습니다. 예상: '/var/uploads/123/document.pdf', 실제: '{$post->files}'");
    }

    echo "✅ 통과: 로컬 파일 경로가 올바르게 저장되었습니다.\n";
    $passed++;
} catch (Throwable $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    $failed++;
}

// ========================================================================
// 테스트 3: 콤마로 구분된 여러 파일 첨부
// ========================================================================
echo "\n테스트 3: 콤마로 구분된 여러 파일 첨부\n";
$total++;

try {
    // 테스트용 사용자 로그인
    login_as_test_user();

    // 콤마로 구분된 여러 파일 첨부
    $filesString = 'https://abc.com/def/photo.jpg,/var/uploads/345/another-file.zip';
    $post = create_post([
        'category' => 'test',
        'title' => '여러 파일 첨부 테스트',
        'content' => '여러 파일 첨부 테스트입니다.',
        'files' => $filesString
    ]);

    // 검증: PostModel 객체 반환 확인
    if (!($post instanceof PostModel)) {
        throw new Exception('PostModel 객체가 반환되지 않았습니다.');
    }

    // 검증: files 필드 확인
    if ($post->files !== $filesString) {
        throw new Exception("files 값이 올바르지 않습니다. 예상: '{$filesString}', 실제: '{$post->files}'");
    }

    // 추가 검증: 파일을 배열로 분리하여 확인
    $filesArray = explode(',', $post->files);
    if (count($filesArray) !== 2) {
        throw new Exception("files 배열 개수가 올바르지 않습니다. 예상: 2, 실제: " . count($filesArray));
    }

    if ($filesArray[0] !== 'https://abc.com/def/photo.jpg') {
        throw new Exception("첫 번째 파일이 올바르지 않습니다. 예상: 'https://abc.com/def/photo.jpg', 실제: '{$filesArray[0]}'");
    }

    if ($filesArray[1] !== '/var/uploads/345/another-file.zip') {
        throw new Exception("두 번째 파일이 올바르지 않습니다. 예상: '/var/uploads/345/another-file.zip', 실제: '{$filesArray[1]}'");
    }

    echo "✅ 통과: 여러 파일이 올바르게 저장되었습니다.\n";
    echo "   - 파일 1: {$filesArray[0]}\n";
    echo "   - 파일 2: {$filesArray[1]}\n";
    $passed++;
} catch (Throwable $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    $failed++;
}

// ========================================================================
// 테스트 4: files 없이 게시글 생성 (선택 사항)
// ========================================================================
echo "\n테스트 4: files 없이 게시글 생성 (선택 사항)\n";
$total++;

try {
    // 테스트용 사용자 로그인
    login_as_test_user();

    // files 없이 게시글 생성
    $post = create_post([
        'category' => 'test',
        'title' => 'files 없는 게시글',
        'content' => 'files 없는 게시글 테스트입니다.'
    ]);

    // 검증: PostModel 객체 반환 확인
    if (!($post instanceof PostModel)) {
        throw new Exception('PostModel 객체가 반환되지 않았습니다.');
    }

    // 검증: files 필드가 빈 문자열인지 확인
    if ($post->files !== '') {
        throw new Exception("files 값이 빈 문자열이어야 합니다. 실제: '{$post->files}'");
    }

    echo "✅ 통과: files 없이 게시글이 올바르게 생성되었습니다.\n";
    $passed++;
} catch (Throwable $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    $failed++;
}

// ========================================================================
// 테스트 5: 빈 files 문자열로 게시글 생성
// ========================================================================
echo "\n테스트 5: 빈 files 문자열로 게시글 생성\n";
$total++;

try {
    // 테스트용 사용자 로그인
    login_as_test_user();

    // 빈 files 문자열로 게시글 생성
    $post = create_post([
        'category' => 'test',
        'title' => '빈 files 문자열 게시글',
        'content' => '빈 files 문자열 테스트입니다.',
        'files' => ''
    ]);

    // 검증: PostModel 객체 반환 확인
    if (!($post instanceof PostModel)) {
        throw new Exception('PostModel 객체가 반환되지 않았습니다.');
    }

    // 검증: files 필드가 빈 문자열인지 확인
    if ($post->files !== '') {
        throw new Exception("files 값이 빈 문자열이어야 합니다. 실제: '{$post->files}'");
    }

    echo "✅ 통과: 빈 files 문자열로 게시글이 올바르게 생성되었습니다.\n";
    $passed++;
} catch (Throwable $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    $failed++;
}

// ========================================================================
// 테스트 6: 3개 이상의 파일 첨부
// ========================================================================
echo "\n테스트 6: 3개 이상의 파일 첨부\n";
$total++;

try {
    // 테스트용 사용자 로그인
    login_as_test_user();

    // 3개 이상의 파일 첨부
    $filesString = 'https://example.com/photo1.jpg,/var/uploads/100/doc.pdf,https://cdn.example.com/video.mp4,/var/uploads/200/archive.zip';
    $post = create_post([
        'category' => 'test',
        'title' => '3개 이상 파일 첨부 테스트',
        'content' => '3개 이상 파일 첨부 테스트입니다.',
        'files' => $filesString
    ]);

    // 검증: PostModel 객체 반환 확인
    if (!($post instanceof PostModel)) {
        throw new Exception('PostModel 객체가 반환되지 않았습니다.');
    }

    // 검증: files 필드 확인
    if ($post->files !== $filesString) {
        throw new Exception("files 값이 올바르지 않습니다.");
    }

    // 추가 검증: 파일을 배열로 분리하여 확인
    $filesArray = explode(',', $post->files);
    if (count($filesArray) !== 4) {
        throw new Exception("files 배열 개수가 올바르지 않습니다. 예상: 4, 실제: " . count($filesArray));
    }

    echo "✅ 통과: 3개 이상의 파일이 올바르게 저장되었습니다.\n";
    echo "   - 파일 개수: " . count($filesArray) . "\n";
    $passed++;
} catch (Throwable $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    $failed++;
}

// ========================================================================
// 테스트 결과 출력
// ========================================================================
echo "\n========================================\n";
echo "테스트 결과\n";
echo "========================================\n";
echo "총 테스트: {$total}\n";
echo "통과: {$passed}\n";
echo "실패: {$failed}\n";
echo "성공률: " . round(($passed / $total) * 100, 2) . "%\n";
echo "========================================\n";

// 실패한 테스트가 있으면 종료 코드 1 반환
if ($failed > 0) {
    exit(1);
}
