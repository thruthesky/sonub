<?php

/**
 * 글/댓글 수정/삭제 제한 테스트
 *
 * 이 테스트는 다음 시나리오를 검증합니다:
 * 1. 댓글이 있는 글은 수정/삭제 불가
 * 2. 자식 댓글이 있는 댓글은 수정/삭제 불가
 *
 * 실행 방법:
 * php tests/post-and-comment/post-and-comment.edit-delete-restriction.test.php
 */

require_once __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

// ============================================================================
// 테스트 헬퍼 함수
// ============================================================================

/**
 * 테스트 결과 출력
 */
function test_result(string $test_name, bool $passed, string $message = ''): void
{
    $status = $passed ? '✅ PASS' : '❌ FAIL';
    echo "$status - $test_name";
    if ($message) {
        echo " ($message)";
    }
    echo "\n";

    if (!$passed) {
        // 테스트 실패 시 즉시 종료
        exit(1);
    }
}

/**
 * 테스트용 글 생성
 */
function create_test_post(int $user_id, string $title = 'Test Post'): int
{
    $pdo = pdo();
    $now = time();

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, category, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, 'Test content', 'test', $now, $now]);

    return (int)$pdo->lastInsertId();
}

/**
 * 테스트용 댓글 생성
 */
function create_test_comment(int $post_id, int $user_id, int $parent_id = 0, string $content = 'Test comment'): int
{
    $pdo = pdo();
    $now = time();

    // depth와 sort 계산
    $depth = 0;
    $sort = $now;

    if ($parent_id > 0) {
        $stmt = $pdo->prepare("SELECT depth, sort FROM comments WHERE id = ?");
        $stmt->execute([$parent_id]);
        $parent = $stmt->fetch(PDO::FETCH_OBJ);

        if ($parent) {
            $depth = $parent->depth + 1;
            // 간단한 sort 계산 (실제 로직은 더 복잡할 수 있음)
            $sort = $parent->sort + 0.001;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, parent_id, content, depth, sort, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $parent_id, $content, $depth, $sort, $now, $now]);

    return (int)$pdo->lastInsertId();
}

/**
 * 테스트 데이터 정리
 */
function cleanup_test_data(int $post_id): void
{
    $pdo = pdo();

    // 댓글 삭제
    $stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt->execute([$post_id]);

    // 글 삭제
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
}

// ============================================================================
// 테스트 시작
// ============================================================================

echo "\n";
echo "========================================\n";
echo "글/댓글 수정/삭제 제한 테스트 시작\n";
echo "========================================\n\n";

// 테스트 사용자 준비
login_as_test_user('banana');
$test_user = login();

echo "테스트 사용자: {$test_user->first_name} (ID: {$test_user->id})\n\n";

// ============================================================================
// 테스트 1: 글 수정 - 댓글이 없는 경우 (성공해야 함)
// ============================================================================
echo "--- 테스트 1: 글 수정 - 댓글이 없는 경우 ---\n";

$post_id_1 = create_test_post($test_user->id, 'Post without comments');

try {
    $updated_post = update_post([
        'id' => $post_id_1,
        'title' => 'Updated title',
        'content' => 'Updated content'
    ]);

    test_result(
        '댓글이 없는 글 수정',
        $updated_post->title === 'Updated title',
        '글 제목이 성공적으로 수정됨'
    );
} catch (Exception $e) {
    test_result('댓글이 없는 글 수정', false, $e->getMessage());
}

cleanup_test_data($post_id_1);

// ============================================================================
// 테스트 2: 글 수정 - 댓글이 있는 경우 (실패해야 함)
// ============================================================================
echo "\n--- 테스트 2: 글 수정 - 댓글이 있는 경우 ---\n";

$post_id_2 = create_test_post($test_user->id, 'Post with comments');
$comment_id_2 = create_test_comment($post_id_2, $test_user->id);

try {
    update_post([
        'id' => $post_id_2,
        'title' => 'Should not update'
    ]);

    test_result('댓글이 있는 글 수정 (실패 예상)', false, '에러가 발생하지 않음 - 버그!');
} catch (Exception $e) {
    $is_correct_error = str_contains($e->getMessage(), '댓글이 있는 경우') ||
                        str_contains($e->getMessage(), 'post-has-comments') ||
                        str_contains($e->getMessage(), 'Cannot update post with existing comments');

    test_result(
        '댓글이 있는 글 수정 차단',
        $is_correct_error,
        $is_correct_error ? '올바른 에러 발생' : "예상과 다른 에러: {$e->getMessage()}"
    );
}

cleanup_test_data($post_id_2);

// ============================================================================
// 테스트 3: 글 삭제 - 댓글이 없는 경우 (성공해야 함)
// ============================================================================
echo "\n--- 테스트 3: 글 삭제 - 댓글이 없는 경우 ---\n";

$post_id_3 = create_test_post($test_user->id, 'Post to delete');

try {
    $deleted_post = delete_post(['id' => $post_id_3]);

    test_result(
        '댓글이 없는 글 삭제',
        $deleted_post !== null,
        '글이 성공적으로 삭제됨'
    );
} catch (Exception $e) {
    test_result('댓글이 없는 글 삭제', false, $e->getMessage());
}

// ============================================================================
// 테스트 4: 글 삭제 - 댓글이 있는 경우 (실패해야 함)
// ============================================================================
echo "\n--- 테스트 4: 글 삭제 - 댓글이 있는 경우 ---\n";

$post_id_4 = create_test_post($test_user->id, 'Post with comments to delete');
$comment_id_4 = create_test_comment($post_id_4, $test_user->id);

try {
    delete_post(['id' => $post_id_4]);

    test_result('댓글이 있는 글 삭제 (실패 예상)', false, '에러가 발생하지 않음 - 버그!');
} catch (Exception $e) {
    $is_correct_error = str_contains($e->getMessage(), '댓글이 있는 경우') ||
                        str_contains($e->getMessage(), 'post-has-comments') ||
                        str_contains($e->getMessage(), 'Cannot delete post with existing comments');

    test_result(
        '댓글이 있는 글 삭제 차단',
        $is_correct_error,
        $is_correct_error ? '올바른 에러 발생' : "예상과 다른 에러: {$e->getMessage()}"
    );
}

cleanup_test_data($post_id_4);

// ============================================================================
// 테스트 5: 댓글 수정 - 자식 댓글이 없는 경우 (성공해야 함)
// ============================================================================
echo "\n--- 테스트 5: 댓글 수정 - 자식 댓글이 없는 경우 ---\n";

$post_id_5 = create_test_post($test_user->id, 'Post for comment test');
$comment_id_5 = create_test_comment($post_id_5, $test_user->id, 0, 'Original comment');

try {
    $updated_comment = update_comment([
        'comment_id' => $comment_id_5,
        'content' => 'Updated comment content'
    ]);

    test_result(
        '자식 댓글이 없는 댓글 수정',
        $updated_comment->content === 'Updated comment content',
        '댓글 내용이 성공적으로 수정됨'
    );
} catch (Exception $e) {
    test_result('자식 댓글이 없는 댓글 수정', false, $e->getMessage());
}

cleanup_test_data($post_id_5);

// ============================================================================
// 테스트 6: 댓글 수정 - 자식 댓글이 있는 경우 (실패해야 함)
// ============================================================================
echo "\n--- 테스트 6: 댓글 수정 - 자식 댓글이 있는 경우 ---\n";

$post_id_6 = create_test_post($test_user->id, 'Post for parent comment test');
$parent_comment_id_6 = create_test_comment($post_id_6, $test_user->id, 0, 'Parent comment');
$child_comment_id_6 = create_test_comment($post_id_6, $test_user->id, $parent_comment_id_6, 'Child comment');

try {
    update_comment([
        'comment_id' => $parent_comment_id_6,
        'content' => 'Should not update'
    ]);

    test_result('자식 댓글이 있는 댓글 수정 (실패 예상)', false, '에러가 발생하지 않음 - 버그!');
} catch (Exception $e) {
    $is_correct_error = str_contains($e->getMessage(), '하위 댓글이 있는 경우') ||
                        str_contains($e->getMessage(), 'comment-has-children') ||
                        str_contains($e->getMessage(), 'child');

    test_result(
        '자식 댓글이 있는 댓글 수정 차단',
        $is_correct_error,
        $is_correct_error ? '올바른 에러 발생' : "예상과 다른 에러: {$e->getMessage()}"
    );
}

cleanup_test_data($post_id_6);

// ============================================================================
// 테스트 7: 댓글 삭제 - 자식 댓글이 없는 경우 (성공해야 함)
// ============================================================================
echo "\n--- 테스트 7: 댓글 삭제 - 자식 댓글이 없는 경우 ---\n";

$post_id_7 = create_test_post($test_user->id, 'Post for comment deletion test');
$comment_id_7 = create_test_comment($post_id_7, $test_user->id, 0, 'Comment to delete');

try {
    $result = delete_comment(['comment_id' => $comment_id_7]);

    test_result(
        '자식 댓글이 없는 댓글 삭제',
        $result === true,
        '댓글이 성공적으로 삭제됨'
    );
} catch (Exception $e) {
    test_result('자식 댓글이 없는 댓글 삭제', false, $e->getMessage());
}

cleanup_test_data($post_id_7);

// ============================================================================
// 테스트 8: 댓글 삭제 - 자식 댓글이 있는 경우 (실패해야 함)
// ============================================================================
echo "\n--- 테스트 8: 댓글 삭제 - 자식 댓글이 있는 경우 ---\n";

$post_id_8 = create_test_post($test_user->id, 'Post for parent comment deletion test');
$parent_comment_id_8 = create_test_comment($post_id_8, $test_user->id, 0, 'Parent comment to delete');
$child_comment_id_8 = create_test_comment($post_id_8, $test_user->id, $parent_comment_id_8, 'Child comment');

try {
    delete_comment(['comment_id' => $parent_comment_id_8]);

    test_result('자식 댓글이 있는 댓글 삭제 (실패 예상)', false, '에러가 발생하지 않음 - 버그!');
} catch (Exception $e) {
    $is_correct_error = str_contains($e->getMessage(), '하위 댓글이 있는 경우') ||
                        str_contains($e->getMessage(), 'comment-has-children') ||
                        str_contains($e->getMessage(), 'child');

    test_result(
        '자식 댓글이 있는 댓글 삭제 차단',
        $is_correct_error,
        $is_correct_error ? '올바른 에러 발생' : "예상과 다른 에러: {$e->getMessage()}"
    );
}

cleanup_test_data($post_id_8);

// ============================================================================
// 테스트 9: 복잡한 시나리오 - 글 → 댓글 → 답글 구조
// ============================================================================
echo "\n--- 테스트 9: 복잡한 시나리오 - 글 → 댓글 → 답글 구조 ---\n";

$post_id_9 = create_test_post($test_user->id, 'Complex scenario post');
$comment_id_9_1 = create_test_comment($post_id_9, $test_user->id, 0, 'First comment');
$comment_id_9_2 = create_test_comment($post_id_9, $test_user->id, 0, 'Second comment');
$reply_id_9_1 = create_test_comment($post_id_9, $test_user->id, $comment_id_9_1, 'Reply to first comment');

// 9-1: 답글이 있는 댓글은 수정 불가
try {
    update_comment([
        'comment_id' => $comment_id_9_1,
        'content' => 'Should not update'
    ]);
    test_result('답글이 있는 첫 번째 댓글 수정 차단 (실패 예상)', false, '에러가 발생하지 않음');
} catch (Exception $e) {
    $is_correct = str_contains($e->getMessage(), '하위 댓글') ||
                  str_contains($e->getMessage(), 'comment-has-children') ||
                  str_contains($e->getMessage(), 'child');
    test_result('답글이 있는 첫 번째 댓글 수정 차단', $is_correct, '올바른 에러 발생');
}

// 9-2: 답글이 없는 댓글은 수정 가능
try {
    $updated = update_comment([
        'comment_id' => $comment_id_9_2,
        'content' => 'Updated second comment'
    ]);
    test_result('답글이 없는 두 번째 댓글 수정', $updated->content === 'Updated second comment', '정상 수정됨');
} catch (Exception $e) {
    test_result('답글이 없는 두 번째 댓글 수정', false, $e->getMessage());
}

// 9-3: 답글 자체는 수정 가능 (자식이 없으므로)
try {
    $updated_reply = update_comment([
        'comment_id' => $reply_id_9_1,
        'content' => 'Updated reply'
    ]);
    test_result('답글 수정', $updated_reply->content === 'Updated reply', '정상 수정됨');
} catch (Exception $e) {
    test_result('답글 수정', false, $e->getMessage());
}

// 9-4: 댓글이 있는 글은 수정 불가
try {
    update_post([
        'id' => $post_id_9,
        'title' => 'Should not update'
    ]);
    test_result('댓글이 있는 글 수정 차단 (실패 예상)', false, '에러가 발생하지 않음');
} catch (Exception $e) {
    $is_correct = str_contains($e->getMessage(), '댓글이 있는 경우') ||
                  str_contains($e->getMessage(), 'post-has-comments') ||
                  str_contains($e->getMessage(), 'Cannot update post with existing comments');
    test_result('댓글이 있는 글 수정 차단', $is_correct, '올바른 에러 발생');
}

cleanup_test_data($post_id_9);

// ============================================================================
// 테스트 완료
// ============================================================================

echo "\n";
echo "========================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "========================================\n";
echo "\n";
echo "총 테스트 수: 12개\n";
echo "성공: 12개\n";
echo "실패: 0개\n";
echo "\n";
