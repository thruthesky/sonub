<?php
/**
 * Test get_comments() function with 'last' parameter
 *
 * 이 테스트는 get_comments()의 'last' 파라미터가 올바르게 동작하는지 검증합니다.
 * - 마지막 N개의 댓글을 올바른 순서로 가져오는지 확인
 * - 계층 구조(답글)가 있는 경우에도 올바르게 동작하는지 확인
 */

require_once __DIR__ . '/../../init.php';

// 테스트용 사용자 로그인
login_as_test_user();
$user = login();
if (!$user) {
    echo "❌ 테스트 사용자로 로그인할 수 없습니다.\n";
    exit(1);
}

echo "=== get_comments() 'last' 파라미터 테스트 ===\n\n";

// 테스트 1: 댓글이 없는 게시물 생성
echo "테스트 1: 댓글이 없는 게시물\n";
$post1 = create_post([
    'category' => 'test',
    'title' => 'Test Post for Comments - ' . time(),
    'content' => 'Testing get_comments with last parameter',
]);
echo "✅ 게시물 생성: ID = {$post1->id}\n";

// 테스트 2: 6개의 최상위 댓글 생성 (답글 없음)
echo "\n테스트 2: 6개의 최상위 댓글 생성\n";
$comment_ids = [];
for ($i = 1; $i <= 6; $i++) {
    $comment = create_comment([
        'post_id' => $post1->id,
        'content' => "Comment $i",
    ]);
    $comment_ids[$i] = $comment->id;
    echo "✅ 댓글 $i 생성: ID = {$comment->id}, sort = {$comment->sort}\n";
    usleep(100000); // 0.1초 대기 (sort 값이 달라지도록)
}

// 테스트 3: 전체 댓글 가져오기 (순서 확인)
echo "\n테스트 3: 전체 댓글 가져오기\n";
$all_comments = get_comments(['post_id' => $post1->id, 'limit' => 999]);
echo "전체 댓글 수: " . count($all_comments) . "\n";
echo "순서:\n";
foreach ($all_comments as $idx => $comment) {
    echo "  [$idx] ID={$comment->id}, content='{$comment->content}', sort={$comment->sort}\n";
}

// 테스트 4: 마지막 5개 댓글 가져오기
echo "\n테스트 4: 마지막 5개 댓글 가져오기 (last=5)\n";
$last_5_comments = get_comments(['post_id' => $post1->id, 'last' => 5]);
echo "가져온 댓글 수: " . count($last_5_comments) . "\n";
echo "순서:\n";
foreach ($last_5_comments as $idx => $comment) {
    echo "  [$idx] ID={$comment->id}, content='{$comment->content}', sort={$comment->sort}\n";
}

// 검증: Comment 2~6이 순서대로 나와야 함
$expected_contents = ['Comment 2', 'Comment 3', 'Comment 4', 'Comment 5', 'Comment 6'];
$actual_contents = array_map(fn($c) => $c->content, $last_5_comments);

if ($expected_contents === $actual_contents) {
    echo "✅ 테스트 4 통과: 마지막 5개 댓글이 올바른 순서로 반환됨\n";
} else {
    echo "❌ 테스트 4 실패:\n";
    echo "   예상: " . implode(', ', $expected_contents) . "\n";
    echo "   실제: " . implode(', ', $actual_contents) . "\n";
}

// 테스트 5: 마지막 3개 댓글 가져오기
echo "\n테스트 5: 마지막 3개 댓글 가져오기 (last=3)\n";
$last_3_comments = get_comments(['post_id' => $post1->id, 'last' => 3]);
echo "가져온 댓글 수: " . count($last_3_comments) . "\n";
echo "순서:\n";
foreach ($last_3_comments as $idx => $comment) {
    echo "  [$idx] ID={$comment->id}, content='{$comment->content}', sort={$comment->sort}\n";
}

// 검증: Comment 4~6이 순서대로 나와야 함
$expected_contents_3 = ['Comment 4', 'Comment 5', 'Comment 6'];
$actual_contents_3 = array_map(fn($c) => $c->content, $last_3_comments);

if ($expected_contents_3 === $actual_contents_3) {
    echo "✅ 테스트 5 통과: 마지막 3개 댓글이 올바른 순서로 반환됨\n";
} else {
    echo "❌ 테스트 5 실패:\n";
    echo "   예상: " . implode(', ', $expected_contents_3) . "\n";
    echo "   실제: " . implode(', ', $actual_contents_3) . "\n";
}

// 테스트 6: 계층 구조가 있는 경우 (답글 추가)
echo "\n테스트 6: 답글이 있는 경우 테스트\n";
$post2 = create_post([
    'category' => 'test',
    'title' => 'Test Post with Replies - ' . time(),
    'content' => 'Testing replies',
]);
echo "✅ 게시물 생성: ID = {$post2->id}\n";

// Comment 1
$c1 = create_comment(['post_id' => $post2->id, 'content' => 'Comment 1']);
echo "✅ Comment 1: ID={$c1->id}, depth={$c1->depth}, sort={$c1->sort}\n";
usleep(100000);

// Comment 2 (Comment 1의 답글)
$c2 = create_comment(['post_id' => $post2->id, 'parent_id' => $c1->id, 'content' => 'Comment 2 (reply to 1)']);
echo "✅ Comment 2: ID={$c2->id}, depth={$c2->depth}, sort={$c2->sort}\n";
usleep(100000);

// Comment 3 (Comment 2의 답글)
$c3 = create_comment(['post_id' => $post2->id, 'parent_id' => $c2->id, 'content' => 'Comment 3 (reply to 2)']);
echo "✅ Comment 3: ID={$c3->id}, depth={$c3->depth}, sort={$c3->sort}\n";
usleep(100000);

// Comment 4 (최상위)
$c4 = create_comment(['post_id' => $post2->id, 'content' => 'Comment 4']);
echo "✅ Comment 4: ID={$c4->id}, depth={$c4->depth}, sort={$c4->sort}\n";
usleep(100000);

// Comment 5 (Comment 4의 답글)
$c5 = create_comment(['post_id' => $post2->id, 'parent_id' => $c4->id, 'content' => 'Comment 5 (reply to 4)']);
echo "✅ Comment 5: ID={$c5->id}, depth={$c5->depth}, sort={$c5->sort}\n";
usleep(100000);

// Comment 6 (최상위)
$c6 = create_comment(['post_id' => $post2->id, 'content' => 'Comment 6']);
echo "✅ Comment 6: ID={$c6->id}, depth={$c6->depth}, sort={$c6->sort}\n";

// 전체 댓글 확인
echo "\n전체 댓글 구조:\n";
$all_comments_2 = get_comments(['post_id' => $post2->id, 'limit' => 999]);
foreach ($all_comments_2 as $idx => $comment) {
    $indent = str_repeat('  ', max(0, $comment->depth));
    echo "  [$idx] {$indent}ID={$comment->id}, content='{$comment->content}', depth={$comment->depth}, sort={$comment->sort}\n";
}

// 마지막 3개 가져오기
echo "\n마지막 3개 댓글 가져오기 (last=3):\n";
$last_3_with_replies = get_comments(['post_id' => $post2->id, 'last' => 3]);
echo "가져온 댓글 수: " . count($last_3_with_replies) . "\n";
foreach ($last_3_with_replies as $idx => $comment) {
    $indent = str_repeat('  ', max(0, $comment->depth));
    echo "  [$idx] {$indent}ID={$comment->id}, content='{$comment->content}', depth={$comment->depth}, sort={$comment->sort}\n";
}

// 검증: sort 값이 가장 큰 3개가 올바른 순서로 나와야 함
$all_sorts = array_map(fn($c) => $c->sort, $all_comments_2);
rsort($all_sorts); // 내림차순 정렬
$expected_sorts = array_slice($all_sorts, 0, 3); // 상위 3개
rsort($expected_sorts); // 다시 내림차순
$expected_sorts = array_reverse($expected_sorts); // 오름차순으로 변환

$actual_sorts = array_map(fn($c) => $c->sort, $last_3_with_replies);

if ($expected_sorts === $actual_sorts) {
    echo "✅ 테스트 6 통과: 계층 구조에서도 마지막 3개 댓글이 올바르게 반환됨\n";
} else {
    echo "❌ 테스트 6 실패:\n";
    echo "   예상 sort 값: " . implode(', ', $expected_sorts) . "\n";
    echo "   실제 sort 값: " . implode(', ', $actual_sorts) . "\n";
}

// 정리: 테스트 게시물 삭제
echo "\n=== 테스트 정리 ===\n";
delete_post(['id' => $post1->id]);
echo "✅ 테스트 게시물 1 삭제: ID = {$post1->id}\n";
delete_post(['id' => $post2->id]);
echo "✅ 테스트 게시물 2 삭제: ID = {$post2->id}\n";

echo "\n=== 테스트 완료 ===\n";
