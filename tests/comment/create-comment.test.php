<?php

/**
 * create_comment() 함수 Unit 테스트
 *
 * 테스트 내용:
 * 1. 로그인 없이 댓글 작성 시도 (예외 발생 확인)
 * 2. 빈 내용으로 댓글 작성 시도 (예외 발생 확인)
 * 3. 정상적인 댓글 작성
 * 4. CommentModel 객체 반환 확인
 * 5. 작성자 정보 포함 확인 (first_name, photo_url, firebase_uid)
 *
 * 실행 방법:
 * php tests/comment/create-comment.test.php
 */

include __DIR__ . '/../../init.php';

echo "🧪 create_comment() 함수 테스트\n";
echo str_repeat("=", 70) . "\n\n";

// ============================================================================
// 테스트 준비: 테스트용 게시글 생성
// ============================================================================
echo "📋 테스트 준비: 테스트용 게시글 생성\n";

// 테스트 사용자로 로그인
login_as_test_user();
$user = login();
echo "   ✅ 테스트 사용자 로그인 완료 (ID: {$user->id})\n";

// 테스트용 게시글 생성
$test_post = create_post([
    'title' => 'Test Post for Comments ' . time(),
    'content' => 'This is a test post for comment testing',
    'category' => 'test'
]);

if (!$test_post || !$test_post->id) {
    echo "   ❌ 실패: 테스트용 게시글 생성 실패\n";
    exit(1);
}

echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n\n";

// ============================================================================
// 테스트 1: 빈 내용으로 댓글 작성 시도 (예외 발생 확인)
// ============================================================================
echo "🧪 테스트 1: 빈 내용으로 댓글 작성 시도\n";

try {
    create_comment([
        'post_id' => $test_post->id,
        'content' => ''
    ]);
    echo "   ❌ 실패: 예외가 발생하지 않음\n\n";
    exit(1);
} catch (Exception $e) {
    echo "   ✅ 통과: 예외 발생 - " . $e->getMessage() . "\n\n";
}

// ============================================================================
// 테스트 2: 정상적인 댓글 작성
// ============================================================================
echo "🧪 테스트 2: 정상적인 댓글 작성\n";

$comment_content = 'This is a test comment ' . time();
$comment = create_comment([
    'post_id' => $test_post->id,
    'content' => $comment_content
]);

if (!$comment) {
    echo "   ❌ 실패: null 반환\n\n";
    exit(1);
}

echo "   ✅ 댓글 생성 성공\n";
echo "   📝 Comment ID: {$comment->id}\n";
echo "   📝 Post ID: {$comment->post_id}\n";
echo "   📝 User ID: {$comment->user_id}\n";
echo "   📝 Content: {$comment->content}\n\n";

// ============================================================================
// 테스트 3: CommentModel 객체 반환 확인
// ============================================================================
echo "🧪 테스트 3: CommentModel 객체 반환 확인\n";

if ($comment instanceof CommentModel) {
    echo "   ✅ 통과: CommentModel 객체 반환\n\n";
} else {
    echo "   ❌ 실패: CommentModel 객체가 아님\n";
    echo "   📝 타입: " . gettype($comment) . "\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: parent_id와 sort 필드 확인
// ============================================================================
echo "🧪 테스트 4: parent_id와 sort 필드 확인\n";

if ($comment->parent_id === 0) {
    echo "   ✅ 통과: parent_id가 0 (루트 댓글)\n";
} else {
    echo "   ❌ 실패: parent_id가 0이 아님 (expected: 0, actual: {$comment->parent_id})\n\n";
    exit(1);
}

if (!empty($comment->sort)) {
    echo "   ✅ 통과: sort 필드가 존재함 ({$comment->sort})\n";
} else {
    echo "   ❌ 실패: sort 필드가 비어있음\n\n";
    exit(1);
}

// sort 필드가 올바른 형식인지 확인 (0000,000,000,...)
if (preg_match('/^\d{4}(,\d{3})*$/', $comment->sort)) {
    echo "   ✅ 통과: sort 필드 형식이 올바름\n\n";
} else {
    echo "   ❌ 실패: sort 필드 형식이 잘못됨 ({$comment->sort})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: 작성자 정보 포함 확인 (author 객체)
// ============================================================================
echo "🧪 테스트 5: 작성자 정보 포함 확인\n";

$has_author = isset($comment->author) && $comment->author !== null;
$has_first_name = $has_author && isset($comment->author->first_name);
$has_firebase_uid = $has_author && isset($comment->author->firebase_uid);

echo "   📝 author: " . ($has_author ? "✅ 있음" : "❌ 없음") . "\n";
echo "   📝 author->first_name: " . ($has_first_name ? "✅ 있음 ({$comment->author->first_name})" : "❌ 없음") . "\n";
echo "   📝 author->firebase_uid: " . ($has_firebase_uid ? "✅ 있음" : "❌ 없음") . "\n";

if ($has_author && $has_first_name && $has_firebase_uid) {
    echo "   ✅ 통과: 작성자 정보 포함됨\n\n";
} else {
    echo "   ❌ 실패: 작성자 정보 누락\n\n";
    exit(1);
}

// ============================================================================
// 테스트 6: 댓글 내용 일치 확인
// ============================================================================
echo "🧪 테스트 6: 댓글 내용 일치 확인\n";

if ($comment->content === $comment_content) {
    echo "   ✅ 통과: 댓글 내용 일치\n\n";
} else {
    echo "   ❌ 실패: 댓글 내용 불일치\n";
    echo "   📝 예상: {$comment_content}\n";
    echo "   📝 실제: {$comment->content}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 7: get_comments() 함수로 조회 확인
// ============================================================================
echo "🧪 테스트 7: get_comments() 함수로 조회 확인\n";

$comments = get_comments(['post_id' => $test_post->id]);

if (!is_array($comments)) {
    echo "   ❌ 실패: 배열이 아님\n\n";
    exit(1);
}

if (count($comments) === 0) {
    echo "   ❌ 실패: 댓글이 조회되지 않음\n\n";
    exit(1);
}

echo "   ✅ 통과: 댓글 조회 성공 (총 " . count($comments) . "개)\n";
echo "   📝 첫 번째 댓글 ID: {$comments[0]->id}\n";
echo "   📝 첫 번째 댓글 내용: {$comments[0]->content}\n\n";

// ============================================================================
// 테스트 8: 여러 댓글 작성 및 순서 확인 (sort 기준)
// ============================================================================
echo "🧪 테스트 8: 여러 댓글 작성 및 순서 확인 (sort 기준)\n";

$comment2 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Second comment ' . time()
]);

$comment3 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Third comment ' . time()
]);

$all_comments = get_comments(['post_id' => $test_post->id]);

if (count($all_comments) === 3) {
    echo "   ✅ 통과: 3개의 댓글 생성됨\n";
} else {
    echo "   ❌ 실패: 예상 3개, 실제 " . count($all_comments) . "개\n\n";
    exit(1);
}

// 순서 확인 (sort ASC 정렬이므로 sort 값이 작은 순서)
if ($all_comments[0]->id === $comment->id &&
    $all_comments[1]->id === $comment2->id &&
    $all_comments[2]->id === $comment3->id) {
    echo "   ✅ 통과: 댓글 순서 정확 (sort 순)\n";
    echo "   📝 Comment 1 sort: {$all_comments[0]->sort}\n";
    echo "   📝 Comment 2 sort: {$all_comments[1]->sort}\n";
    echo "   📝 Comment 3 sort: {$all_comments[2]->sort}\n\n";
} else {
    echo "   ❌ 실패: 댓글 순서 불일치\n";
    echo "   📝 예상 순서: {$comment->id}, {$comment2->id}, {$comment3->id}\n";
    echo "   📝 실제 순서: {$all_comments[0]->id}, {$all_comments[1]->id}, {$all_comments[2]->id}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 9: 대댓글 (reply) 작성 및 sort 확인
// ============================================================================
echo "🧪 테스트 9: 대댓글 (reply) 작성 및 sort 확인\n";

// 첫 번째 댓글에 대댓글 작성
$reply1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment->id,
    'content' => 'Reply to first comment ' . time()
]);

echo "   ✅ 대댓글 생성 성공\n";
echo "   📝 Reply ID: {$reply1->id}\n";
echo "   📝 Parent ID: {$reply1->parent_id}\n";
echo "   📝 Reply sort: {$reply1->sort}\n";
echo "   📝 Parent sort: {$comment->sort}\n";

// parent_id 확인
if ($reply1->parent_id === $comment->id) {
    echo "   ✅ 통과: parent_id가 올바름\n";
} else {
    echo "   ❌ 실패: parent_id 불일치 (expected: {$comment->id}, actual: {$reply1->parent_id})\n\n";
    exit(1);
}

// sort 값이 부모보다 큰지 확인 (sort 문자열 비교)
if ($reply1->sort > $comment->sort) {
    echo "   ✅ 통과: 대댓글 sort가 부모 댓글 sort보다 큼\n\n";
} else {
    echo "   ❌ 실패: 대댓글 sort가 부모 댓글 sort보다 작거나 같음\n\n";
    exit(1);
}

// ============================================================================
// 테스트 10: 깊은 대댓글 (nested reply) 작성 및 sort 확인
// ============================================================================
echo "🧪 테스트 10: 깊은 대댓글 (nested reply) 작성 및 sort 확인\n";

// 대댓글에 또 다른 대댓글 작성
$reply2 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $reply1->id,
    'content' => 'Reply to reply ' . time()
]);

echo "   ✅ 깊은 대댓글 생성 성공\n";
echo "   📝 Reply2 ID: {$reply2->id}\n";
echo "   📝 Reply2 Parent ID: {$reply2->parent_id}\n";
echo "   📝 Reply2 sort: {$reply2->sort}\n";
echo "   📝 Reply1 sort: {$reply1->sort}\n";

// parent_id 확인
if ($reply2->parent_id === $reply1->id) {
    echo "   ✅ 통과: parent_id가 올바름\n";
} else {
    echo "   ❌ 실패: parent_id 불일치\n\n";
    exit(1);
}

// sort 값이 부모보다 큰지 확인
if ($reply2->sort > $reply1->sort) {
    echo "   ✅ 통과: 깊은 대댓글 sort가 부모 대댓글 sort보다 큼\n\n";
} else {
    echo "   ❌ 실패: 깊은 대댓글 sort가 부모 대댓글 sort보다 작거나 같음\n\n";
    exit(1);
}

// ============================================================================
// 테스트 정리: 테스트용 게시글 및 댓글 삭제
// ============================================================================
echo "🧹 테스트 정리: 테스트용 데이터 삭제\n";

// 댓글 삭제
$pdo = pdo();
$stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 댓글 삭제 완료\n";

// 게시글 삭제
delete_post(['id' => $test_post->id]);
echo "   ✅ 게시글 삭제 완료\n\n";

// ============================================================================
// 테스트 완료
// ============================================================================
echo str_repeat("=", 70) . "\n";
echo "✅ 모든 테스트 통과!\n";
echo str_repeat("=", 70) . "\n";
