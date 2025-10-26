<?php
/**
 * update_comment() 및 delete_comment() 함수 테스트
 *
 * 이 테스트는 다음 항목들을 검증합니다:
 * 1. update_comment() 함수의 모든 에러 케이스 및 정상 동작
 * 2. delete_comment() 함수의 모든 에러 케이스 및 정상 동작
 * 3. 댓글 삭제 후 comment_count 자동 업데이트 확인
 */

// init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 update_comment() 및 delete_comment() 함수 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 준비: 테스트용 게시글 및 댓글 생성
    // ========================================================================
    echo "📋 테스트 준비: 테스트용 게시글 및 댓글 생성\n";

    // 테스트 사용자 1 (banana)로 로그인
    login_as_test_user('banana');
    $user1 = login();
    echo "   ✅ 사용자 1 로그인: {$user1->first_name} (firebase_uid: {$user1->firebase_uid})\n";

    // 테스트용 게시글 생성
    $post = create_post([
        'category' => 'test',
        'title' => 'Test Post for Comment Update/Delete',
        'content' => 'This is a test post.'
    ]);
    echo "   ✅ 테스트 게시글 생성: ID={$post->id}\n";

    // 테스트용 댓글 생성
    $comment = create_comment([
        'post_id' => $post->id,
        'content' => 'Original comment content'
    ]);
    echo "   ✅ 테스트 댓글 생성: ID={$comment->id}, 내용='{$comment->content}'\n";

    echo "\n";

    // ========================================================================
    // update_comment() 함수 테스트
    // ========================================================================
    echo "🧪 update_comment() 함수 테스트\n";
    echo "----------------------------------------------------------------------\n";

    // ------------------------------------------------------------------------
    // 테스트 1: comment_id가 0인 경우 에러
    // ------------------------------------------------------------------------
    echo "🧪 테스트 1: comment_id가 0인 경우 에러 (invalid-comment-id)\n";
    try {
        update_comment(['comment_id' => 0, 'content' => 'Updated content']);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getErrorCode() === 'invalid-comment-id') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
            exit(1);
        }
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 2: comment_id가 음수인 경우 에러
    // ------------------------------------------------------------------------
    echo "🧪 테스트 2: comment_id가 음수인 경우 에러 (invalid-comment-id)\n";
    try {
        update_comment(['comment_id' => -1, 'content' => 'Updated content']);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getErrorCode() === 'invalid-comment-id') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
            exit(1);
        }
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 3: 댓글 내용이 비어있는 경우 에러
    // ------------------------------------------------------------------------
    echo "🧪 테스트 3: 댓글 내용이 비어있는 경우 에러 (empty-comment-content)\n";
    try {
        update_comment(['comment_id' => $comment->id, 'content' => '']);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getErrorCode() === 'empty-comment-content') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
            exit(1);
        }
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 4: 존재하지 않는 댓글 ID인 경우 에러
    // ------------------------------------------------------------------------
    echo "🧪 테스트 4: 존재하지 않는 댓글 ID인 경우 에러 (comment-not-found)\n";
    try {
        update_comment(['comment_id' => 999999, 'content' => 'Updated content']);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getErrorCode() === 'comment-not-found') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
            exit(1);
        }
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 5: 정상적인 댓글 수정
    // ------------------------------------------------------------------------
    echo "🧪 테스트 5: 정상적인 댓글 수정\n";
    $updated_comment = update_comment([
        'comment_id' => $comment->id,
        'content' => 'Updated comment content'
    ]);

    if ($updated_comment->content === 'Updated comment content') {
        echo "   ✅ 댓글 수정 성공\n";
        echo "   - 댓글 ID: {$updated_comment->id}\n";
        echo "   - 수정된 내용: '{$updated_comment->content}'\n";
    } else {
        echo "   ❌ 댓글 수정 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // delete_comment() 함수 테스트
    // ========================================================================
    echo "🧪 delete_comment() 함수 테스트\n";
    echo "----------------------------------------------------------------------\n";

    // ------------------------------------------------------------------------
    // 테스트 6: 새로운 댓글 생성 (삭제 테스트용)
    // ------------------------------------------------------------------------
    echo "📋 테스트 준비: 삭제 테스트용 댓글 생성\n";
    $comment_to_delete = create_comment([
        'post_id' => $post->id,
        'content' => 'Comment to be deleted'
    ]);
    echo "   ✅ 테스트 댓글 생성: ID={$comment_to_delete->id}\n";

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 7: comment_id가 0인 경우 에러
    // ------------------------------------------------------------------------
    echo "🧪 테스트 7: comment_id가 0인 경우 에러 (invalid-comment-id)\n";
    try {
        delete_comment(['comment_id' => 0]);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getErrorCode() === 'invalid-comment-id') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
            exit(1);
        }
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 8: 존재하지 않는 댓글 ID인 경우 에러
    // ------------------------------------------------------------------------
    echo "🧪 테스트 8: 존재하지 않는 댓글 ID인 경우 에러 (comment-not-found)\n";
    try {
        delete_comment(['comment_id' => 999999]);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getErrorCode() === 'comment-not-found') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()} (코드: {$e->getErrorCode()})\n";
            exit(1);
        }
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 9: 정상적인 댓글 삭제
    // ------------------------------------------------------------------------
    echo "🧪 테스트 9: 정상적인 댓글 삭제\n";

    // 삭제 전 댓글 수 확인
    $post_before_delete = get_post(['post_id' => $post->id]);
    $comment_count_before = $post_before_delete->comment_count;
    echo "   삭제 전 게시글의 댓글 수: {$comment_count_before}\n";

    // 댓글 삭제
    $result = delete_comment(['comment_id' => $comment_to_delete->id]);

    if ($result === true) {
        echo "   ✅ 댓글 삭제 성공\n";
    } else {
        echo "   ❌ 댓글 삭제 실패\n";
        exit(1);
    }

    // 삭제된 댓글이 실제로 DB에서 삭제되었는지 확인
    $deleted_comment = get_comment(['comment_id' => $comment_to_delete->id]);
    if ($deleted_comment === null) {
        echo "   ✅ 댓글이 DB에서 삭제되었음을 확인\n";
    } else {
        echo "   ❌ 댓글이 여전히 DB에 존재합니다\n";
        exit(1);
    }

    echo "\n";

    // ------------------------------------------------------------------------
    // 테스트 10: 댓글 삭제 후 comment_count 자동 업데이트 확인
    // ------------------------------------------------------------------------
    echo "🧪 테스트 10: 댓글 삭제 후 comment_count 자동 업데이트 확인\n";

    // 삭제 후 댓글 수 확인
    $post_after_delete = get_post(['post_id' => $post->id]);
    $comment_count_after = $post_after_delete->comment_count;
    echo "   삭제 후 게시글의 댓글 수: {$comment_count_after}\n";

    // comment_count가 1 감소했는지 확인
    if ($comment_count_after === $comment_count_before - 1) {
        echo "   ✅ comment_count가 1 감소했습니다 ({$comment_count_before} → {$comment_count_after})\n";
    } else {
        echo "   ❌ comment_count가 예상과 다릅니다 (기대: " . ($comment_count_before - 1) . ", 실제: {$comment_count_after})\n";
        exit(1);
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 테스트 통과!\n";

} catch (Throwable $e) {
    echo "\n❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   파일: " . $e->getFile() . "\n";
    echo "   라인: " . $e->getLine() . "\n";
    echo "   스택 트레이스:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
