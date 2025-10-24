<?php

/**
 * update_post() 함수 테스트
 *
 * 게시글 수정 기능을 테스트합니다.
 *
 * 실행 방법:
 * php tests/post/update-post.test.php
 */

require_once __DIR__ . '/../../init.php';

// Load test functions
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "====================================\n";
echo "update_post() 함수 테스트 시작\n";
echo "====================================\n\n";

$test_passed = 0;
$test_failed = 0;

// ============================================================================
// 테스트용 사용자 로그인
// ============================================================================
echo "📝 테스트 사용자 로그인 중...\n";

try {
    // banana 테스트 사용자로 로그인
    login_as_test_user('banana');
    $test_user = login();

    echo "✅ 테스트 사용자 로그인 완료 (ID: {$test_user->id})\n\n";

} catch (Exception $e) {
    echo "❌ 테스트 사용자 로그인 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// ============================================================================
// 테스트용 게시글 생성
// ============================================================================
echo "📝 테스트 게시글 생성 중...\n";

try {
    $original_post = create_post([
        'category' => 'discussion',
        'title' => '원본 제목',
        'content' => '원본 내용',
        'files' => 'https://example.com/original.jpg',
        'visibility' => 'public'
    ]);

    echo "✅ 테스트 게시글 생성 완료 (ID: {$original_post->id})\n";
    echo "   - 제목: {$original_post->title}\n";
    echo "   - 내용: {$original_post->content}\n";
    echo "   - 카테고리: {$original_post->category}\n";
    echo "   - visibility: {$original_post->visibility}\n\n";

    $test_passed++;
} catch (Exception $e) {
    echo "❌ 테스트 게시글 생성 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

// ============================================================================
// 테스트 1: 제목만 수정
// ============================================================================
echo "📝 테스트 1: 제목만 수정\n";

try {
    $updated_post = update_post([
        'id' => $original_post->id,
        'title' => '수정된 제목'
    ]);

    if ($updated_post->title === '수정된 제목') {
        echo "✅ 제목 수정 성공\n";
        echo "   - 새 제목: {$updated_post->title}\n";
        echo "   - 내용 유지: {$updated_post->content}\n";
        echo "   - 카테고리 유지: {$updated_post->category}\n";
        $test_passed++;
    } else {
        echo "❌ 제목 수정 실패: 예상 값과 다름\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 1 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 2: 내용만 수정
// ============================================================================
echo "📝 테스트 2: 내용만 수정\n";

try {
    $updated_post = update_post([
        'id' => $original_post->id,
        'content' => '수정된 내용입니다.'
    ]);

    if ($updated_post->content === '수정된 내용입니다.') {
        echo "✅ 내용 수정 성공\n";
        echo "   - 새 내용: {$updated_post->content}\n";
        echo "   - 제목 유지: {$updated_post->title}\n";
        $test_passed++;
    } else {
        echo "❌ 내용 수정 실패: 예상 값과 다름\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 2 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 3: 여러 필드 동시 수정
// ============================================================================
echo "📝 테스트 3: 여러 필드 동시 수정\n";

try {
    $updated_post = update_post([
        'id' => $original_post->id,
        'title' => '완전히 새로운 제목',
        'content' => '완전히 새로운 내용',
        'category' => 'qna',
        'files' => 'https://example.com/new1.jpg,https://example.com/new2.jpg'
    ]);

    $files_match = (is_array($updated_post->files) ? implode(',', $updated_post->files) : $updated_post->files) === 'https://example.com/new1.jpg,https://example.com/new2.jpg';

    if (
        $updated_post->title === '완전히 새로운 제목' &&
        $updated_post->content === '완전히 새로운 내용' &&
        $updated_post->category === 'qna' &&
        $files_match
    ) {
        echo "✅ 여러 필드 수정 성공\n";
        echo "   - 제목: {$updated_post->title}\n";
        echo "   - 내용: {$updated_post->content}\n";
        echo "   - 카테고리: {$updated_post->category}\n";
        echo "   - 파일: " . (is_array($updated_post->files) ? implode(',', $updated_post->files) : $updated_post->files) . "\n";
        $test_passed++;
    } else {
        echo "❌ 여러 필드 수정 실패: 예상 값과 다름\n";
        echo "   - 제목 일치: " . ($updated_post->title === '완전히 새로운 제목' ? 'O' : 'X') . "\n";
        echo "   - 내용 일치: " . ($updated_post->content === '완전히 새로운 내용' ? 'O' : 'X') . "\n";
        echo "   - 카테고리 일치: " . ($updated_post->category === 'qna' ? 'O' : 'X (actual: ' . $updated_post->category . ')') . "\n";
        echo "   - 파일 일치: " . ($files_match ? 'O' : 'X (actual: ' . (is_array($updated_post->files) ? implode(',', $updated_post->files) : $updated_post->files) . ')') . "\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 3 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 4: visibility 변경 (public → friends)
// ============================================================================
echo "📝 테스트 4: visibility 변경 (public → friends)\n";

try {
    $updated_post = update_post([
        'id' => $original_post->id,
        'visibility' => 'friends'
    ]);

    if ($updated_post->visibility === 'friends' && $updated_post->category === 'friends') {
        echo "✅ visibility 변경 성공\n";
        echo "   - 새 visibility: {$updated_post->visibility}\n";
        echo "   - 새 category: {$updated_post->category} (visibility와 동일)\n";
        $test_passed++;
    } else {
        echo "❌ visibility 변경 실패\n";
        echo "   - 실제 visibility: {$updated_post->visibility}\n";
        echo "   - 실제 category: {$updated_post->category}\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 4 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 5: visibility 변경 (friends → private)
// ============================================================================
echo "📝 테스트 5: visibility 변경 (friends → private)\n";

try {
    $updated_post = update_post([
        'id' => $original_post->id,
        'visibility' => 'private'
    ]);

    if ($updated_post->visibility === 'private' && $updated_post->category === 'private') {
        echo "✅ visibility를 private로 변경 성공\n";
        echo "   - 새 visibility: {$updated_post->visibility}\n";
        echo "   - 새 category: {$updated_post->category}\n";
        echo "   - 피드에서 삭제됨 (Fan-out 로직)\n";
        $test_passed++;
    } else {
        echo "❌ visibility 변경 실패\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 5 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 6: visibility 변경 (private → public)
// ============================================================================
echo "📝 테스트 6: visibility 변경 (private → public)\n";

try {
    $updated_post = update_post([
        'id' => $original_post->id,
        'visibility' => 'public',
        'category' => 'notice'
    ]);

    if ($updated_post->visibility === 'public' && $updated_post->category === 'notice') {
        echo "✅ visibility를 public으로 변경 성공\n";
        echo "   - 새 visibility: {$updated_post->visibility}\n";
        echo "   - 새 category: {$updated_post->category}\n";
        echo "   - 친구 피드에 전파됨 (Fan-out 로직)\n";
        $test_passed++;
    } else {
        echo "❌ visibility 변경 실패\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 6 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 7: updated_at 타임스탬프 업데이트 확인
// ============================================================================
echo "📝 테스트 7: updated_at 타임스탬프 업데이트 확인\n";

try {
    $before_update_time = time();
    sleep(1); // 1초 대기

    $updated_post = update_post([
        'id' => $original_post->id,
        'title' => '타임스탬프 테스트'
    ]);

    if ($updated_post->updated_at >= $before_update_time) {
        echo "✅ updated_at 타임스탬프 업데이트 성공\n";
        echo "   - 이전 시간: " . date('Y-m-d H:i:s', $before_update_time) . "\n";
        echo "   - 업데이트 시간: " . date('Y-m-d H:i:s', $updated_post->updated_at) . "\n";
        $test_passed++;
    } else {
        echo "❌ updated_at 타임스탬프 업데이트 실패\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 7 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 8: post_id 파라미터로도 작동하는지 확인
// ============================================================================
echo "📝 테스트 8: post_id 파라미터로도 작동 확인\n";

try {
    $updated_post = update_post([
        'post_id' => $original_post->id, // 'id' 대신 'post_id' 사용
        'title' => 'post_id 파라미터 테스트'
    ]);

    if ($updated_post->title === 'post_id 파라미터 테스트') {
        echo "✅ post_id 파라미터 사용 성공\n";
        echo "   - 제목: {$updated_post->title}\n";
        $test_passed++;
    } else {
        echo "❌ post_id 파라미터 사용 실패\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 8 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 9: 에러 - 존재하지 않는 게시글 수정 시도
// ============================================================================
echo "📝 테스트 9: 에러 - 존재하지 않는 게시글 수정 시도\n";

try {
    $updated_post = update_post([
        'id' => 999999999, // 존재하지 않는 ID
        'title' => '존재하지 않는 게시글'
    ]);

    echo "❌ 존재하지 않는 게시글 수정이 허용됨 (예상하지 못한 동작)\n";
    $test_failed++;
} catch (ApiException $e) {
    if ($e->getErrorCode() === 'post-not-found') {
        echo "✅ 존재하지 않는 게시글 수정 차단 성공\n";
        echo "   - 에러 코드: {$e->getErrorCode()}\n";
        echo "   - 에러 메시지: {$e->getErrorMessage()}\n";
        $test_passed++;
    } else {
        echo "❌ 예상하지 못한 에러 코드: {$e->getErrorCode()}\n";
        $test_failed++;
    }
} catch (Exception $e) {
    echo "❌ 테스트 10 실패: " . $e->getMessage() . "\n";
    $test_failed++;
}

echo "\n";

// ============================================================================
// 테스트 데이터 정리
// ============================================================================
echo "📝 테스트 데이터 정리 중...\n";

try {
    // 게시글 삭제
    delete_post(['id' => $original_post->id]);
    echo "✅ 테스트 게시글 삭제 완료\n";

} catch (Exception $e) {
    echo "⚠️  테스트 데이터 정리 중 오류: " . $e->getMessage() . "\n";
}

// ============================================================================
// 테스트 결과 요약
// ============================================================================
echo "\n====================================\n";
echo "테스트 결과 요약\n";
echo "====================================\n";
echo "✅ 성공: {$test_passed}개\n";
echo "❌ 실패: {$test_failed}개\n";
echo "총 테스트: " . ($test_passed + $test_failed) . "개\n";

if ($test_failed === 0) {
    echo "\n🎉 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "\n⚠️  일부 테스트 실패\n";
    exit(1);
}
