<?php

/**
 * 댓글 시스템 종합 테스트
 *
 * 이 테스트는 댓글 시스템의 모든 기능을 광범위하고 세밀하게 테스트합니다.
 * - create_comment(), get_comments(), get_comment_info()
 * - depth 필드 저장 및 조회
 * - 다양한 계층 구조 시나리오
 * - 대규모 댓글 트리 생성
 * - 경계값 테스트 (최대 depth 등)
 *
 * 실행 방법:
 * php tests/comment/comprehensive-comment.test.php
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 댓글 시스템 종합 테스트\n";
echo str_repeat("=", 70) . "\n\n";

// 테스트 통계
$test_count = 0;
$passed_count = 0;

function assert_test($condition, $message, $details = '')
{
    global $test_count, $passed_count;
    $test_count++;

    if ($condition) {
        $passed_count++;
        echo "   ✅ 통과: {$message}\n";
        if ($details) {
            echo "      📝 {$details}\n";
        }
        return true;
    } else {
        echo "   ❌ 실패: {$message}\n";
        if ($details) {
            echo "      📝 {$details}\n";
        }
        return false;
    }
}

function fail_test($message)
{
    echo "\n❌ 테스트 실패: {$message}\n";
    echo "======================================================================\n";
    exit(1);
}

// ============================================================================
// 테스트 준비: 테스트용 게시글 생성
// ============================================================================
echo "📋 테스트 준비: 테스트용 게시글 생성\n";

login_as_test_user();
$user = login();
echo "   ✅ 테스트 사용자 로그인 완료 (ID: {$user->id})\n";

$test_post = create_post([
    'title' => 'Comprehensive Test Post ' . time(),
    'content' => 'Post for comprehensive comment testing',
    'category' => 'test'
]);
echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n\n";

// ============================================================================
// 테스트 그룹 1: 기본 CRUD 테스트
// ============================================================================
echo "🧪 테스트 그룹 1: 기본 CRUD 테스트\n";
echo str_repeat("-", 70) . "\n";

// 테스트 1-1: 빈 내용으로 댓글 작성 시도
try {
    create_comment(['post_id' => $test_post->id, 'content' => '']);
    fail_test("빈 내용으로 댓글이 생성됨");
} catch (Exception $e) {
    assert_test(true, "빈 내용으로 댓글 작성 시 예외 발생", $e->getMessage());
}

// 테스트 1-2: 첫 번째 루트 댓글 생성
$comment1 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Root comment 1'
]);
assert_test($comment1->depth === 0, "루트 댓글의 depth가 0", "depth: {$comment1->depth}");
assert_test($comment1->parent_id === 0, "루트 댓글의 parent_id가 0", "parent_id: {$comment1->parent_id}");
assert_test(str_starts_with($comment1->sort, '0001,'), "루트 댓글의 sort가 0001로 시작", "sort: {$comment1->sort}");
assert_test(!empty($comment1->author), "작성자 정보 포함", "first_name: {$comment1->author->first_name}");

// 테스트 1-3: 두 번째 루트 댓글 생성
$comment2 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Root comment 2'
]);
assert_test($comment2->depth === 0, "두 번째 루트 댓글의 depth가 0");
assert_test(str_starts_with($comment2->sort, '0002,'), "두 번째 루트 댓글의 sort가 0002로 시작");

// 테스트 1-4: 세 번째 루트 댓글 생성
$comment3 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Root comment 3'
]);
assert_test($comment3->depth === 0, "세 번째 루트 댓글의 depth가 0");
assert_test(str_starts_with($comment3->sort, '0003,'), "세 번째 루트 댓글의 sort가 0003로 시작");

echo "\n";

// ============================================================================
// 테스트 그룹 2: 대댓글 및 depth 테스트
// ============================================================================
echo "🧪 테스트 그룹 2: 대댓글 및 depth 테스트\n";
echo str_repeat("-", 70) . "\n";

// 테스트 2-1: comment1의 첫 번째 대댓글
$reply1_1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'Reply 1-1'
]);
assert_test($reply1_1->depth === 1, "대댓글의 depth가 1", "depth: {$reply1_1->depth}");
assert_test($reply1_1->parent_id === $comment1->id, "대댓글의 parent_id가 올바름");
assert_test($reply1_1->sort > $comment1->sort, "대댓글의 sort가 부모보다 큼");
assert_test(str_starts_with($reply1_1->sort, '0001,001,'), "대댓글 sort 패턴 확인", "sort: {$reply1_1->sort}");

// 테스트 2-2: comment1의 두 번째 대댓글
$reply1_2 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'Reply 1-2'
]);
assert_test($reply1_2->depth === 1, "두 번째 대댓글의 depth가 1");
assert_test(str_starts_with($reply1_2->sort, '0001,002,'), "두 번째 대댓글 sort 패턴 확인");
assert_test($reply1_2->sort > $reply1_1->sort, "두 번째 대댓글의 sort가 첫 번째보다 큼");

// 테스트 2-3: reply1_1의 대댓글 (손자 댓글)
$reply1_1_1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $reply1_1->id,
    'content' => 'Reply 1-1-1 (grandson)'
]);
assert_test($reply1_1_1->depth === 2, "손자 댓글의 depth가 2", "depth: {$reply1_1_1->depth}");
assert_test($reply1_1_1->parent_id === $reply1_1->id, "손자 댓글의 parent_id가 올바름");
assert_test(str_starts_with($reply1_1_1->sort, '0001,001,001,'), "손자 댓글 sort 패턴 확인");

// 테스트 2-4: comment2의 대댓글
$reply2_1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment2->id,
    'content' => 'Reply 2-1'
]);
assert_test($reply2_1->depth === 1, "다른 루트 댓글의 대댓글 depth가 1");
assert_test(str_starts_with($reply2_1->sort, '0002,001,'), "다른 루트의 대댓글 sort 패턴 확인");

echo "\n";

// ============================================================================
// 테스트 그룹 3: get_comments() 정렬 및 조회 테스트
// ============================================================================
echo "🧪 테스트 그룹 3: get_comments() 정렬 및 조회 테스트\n";
echo str_repeat("-", 70) . "\n";

$all_comments = get_comments(['post_id' => $test_post->id, 'limit' => 50]);
assert_test(count($all_comments) === 7, "전체 댓글 개수 확인", "expected: 7, actual: " . count($all_comments));

// 테스트 3-1: sort 순서 확인
$is_sorted = true;
for ($i = 1; $i < count($all_comments); $i++) {
    if ($all_comments[$i]->sort < $all_comments[$i - 1]->sort) {
        $is_sorted = false;
        break;
    }
}
assert_test($is_sorted, "댓글이 sort 순서대로 정렬됨");

// 테스트 3-2: depth 필드 확인
$root_count = 0;
$reply_count = 0;
$nested_count = 0;

foreach ($all_comments as $comment) {
    if ($comment->depth === 0) $root_count++;
    elseif ($comment->depth === 1) $reply_count++;
    elseif ($comment->depth === 2) $nested_count++;
}

assert_test($root_count === 3, "루트 댓글 개수 확인", "expected: 3, actual: {$root_count}");
assert_test($reply_count === 3, "대댓글 개수 확인", "expected: 3, actual: {$reply_count}");
assert_test($nested_count === 1, "손자 댓글 개수 확인", "expected: 1, actual: {$nested_count}");

// 테스트 3-3: 계층 구조 순서 확인
$expected_order = [
    $comment1->id,      // 0001,...
    $reply1_1->id,      // 0001,001,...
    $reply1_1_1->id,    // 0001,001,001,...
    $reply1_2->id,      // 0001,002,...
    $comment2->id,      // 0002,...
    $reply2_1->id,      // 0002,001,...
    $comment3->id       // 0003,...
];

$actual_order = array_map(fn($c) => $c->id, array_slice($all_comments, 0, 7));
$order_correct = ($expected_order === $actual_order);
assert_test($order_correct, "계층 구조 순서가 올바름",
    "expected: " . implode(', ', $expected_order) . "\n      actual: " . implode(', ', $actual_order));

echo "\n";

// ============================================================================
// 테스트 그룹 4: get_comment_info() 테스트
// ============================================================================
echo "🧪 테스트 그룹 4: get_comment_info() 함수 테스트\n";
echo str_repeat("-", 70) . "\n";

// 테스트 4-1: 네 번째 루트 댓글 정보
$info = get_comment_info($test_post->id, 0);
assert_test($info['parent_sort'] === null, "루트 댓글의 parent_sort가 null");
assert_test($info['depth'] === 0, "루트 댓글의 depth가 0");
assert_test($info['no_of_comments'] === 4, "네 번째 루트 댓글의 순서 번호가 4", "no_of_comments: {$info['no_of_comments']}");

// 테스트 4-2: comment1의 세 번째 대댓글 정보
$info = get_comment_info($test_post->id, $comment1->id);
assert_test($info['parent_sort'] === $comment1->sort, "대댓글의 parent_sort가 부모와 일치");
assert_test($info['depth'] === 1, "대댓글의 depth가 1");
assert_test($info['no_of_comments'] === 3, "세 번째 대댓글의 순서 번호가 3");

// 테스트 4-3: reply1_1의 두 번째 손자 댓글 정보
$info = get_comment_info($test_post->id, $reply1_1->id);
assert_test($info['depth'] === 2, "손자 댓글의 depth가 2");
assert_test($info['no_of_comments'] === 2, "두 번째 손자 댓글의 순서 번호가 2");

echo "\n";

// ============================================================================
// 테스트 그룹 5: 깊은 계층 구조 테스트 (최대 depth까지)
// ============================================================================
echo "🧪 테스트 그룹 5: 깊은 계층 구조 테스트\n";
echo str_repeat("-", 70) . "\n";

// depth 0부터 11까지 깊은 댓글 트리 생성
$deep_comments = [];
$deep_comments[0] = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Deep comment level 0'
]);

for ($i = 1; $i <= 11; $i++) {
    $deep_comments[$i] = create_comment([
        'post_id' => $test_post->id,
        'parent_id' => $deep_comments[$i - 1]->id,
        'content' => "Deep comment level {$i}"
    ]);
    assert_test($deep_comments[$i]->depth === $i, "Level {$i} depth 확인", "depth: {$deep_comments[$i]->depth}");
}

// 최대 depth (11) 확인
assert_test($deep_comments[11]->depth === 11, "최대 depth가 11", "depth: {$deep_comments[11]->depth}");

// depth 12 댓글 시도 (최대 depth를 초과하지만 생성은 가능, sort만 변경되지 않음)
$deep12 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $deep_comments[11]->id,
    'content' => "Deep comment level 12"
]);
assert_test($deep12->depth === 12, "Depth 12 댓글 생성 가능", "depth: {$deep12->depth}");

echo "\n";

// ============================================================================
// 테스트 그룹 6: 대규모 댓글 트리 테스트
// ============================================================================
echo "🧪 테스트 그룹 6: 대규모 댓글 트리 테스트\n";
echo str_repeat("-", 70) . "\n";

// 루트 댓글 1개에 자식 10개 생성
$parent = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Parent with 10 children'
]);

$children = [];
for ($i = 1; $i <= 10; $i++) {
    $children[] = create_comment([
        'post_id' => $test_post->id,
        'parent_id' => $parent->id,
        'content' => "Child {$i}"
    ]);
}

assert_test(count($children) === 10, "10개의 자식 댓글 생성");

// 모든 자식이 depth=1이고 parent_id가 올바른지 확인
$all_depth_1 = true;
$all_parent_correct = true;
for ($i = 0; $i < 10; $i++) {
    if ($children[$i]->depth !== 1) $all_depth_1 = false;
    if ($children[$i]->parent_id !== $parent->id) $all_parent_correct = false;
}
assert_test($all_depth_1, "모든 자식의 depth가 1");
assert_test($all_parent_correct, "모든 자식의 parent_id가 올바름");

// sort 순서 확인
$sorts_in_order = true;
for ($i = 1; $i < 10; $i++) {
    if ($children[$i]->sort <= $children[$i - 1]->sort) {
        $sorts_in_order = false;
        break;
    }
}
assert_test($sorts_in_order, "자식 댓글들의 sort가 순서대로 증가");

echo "\n";

// ============================================================================
// 테스트 그룹 7: 경계값 테스트
// ============================================================================
echo "🧪 테스트 그룹 7: 경계값 테스트\n";
echo str_repeat("-", 70) . "\n";

// 테스트 7-1: 매우 긴 내용의 댓글
$long_content = str_repeat('가나다라마바사아자차카타파하 ', 100);
$long_comment = create_comment([
    'post_id' => $test_post->id,
    'content' => $long_content
]);
// trim() 함수가 마지막 공백을 제거하므로 trim된 값과 비교
assert_test($long_comment->content === trim($long_content), "긴 내용의 댓글 저장 및 조회");

// 테스트 7-2: 특수문자 포함 댓글
$special_content = "Special chars: !@#$%^&*()_+-=[]{}|;':\",./<>?`~";
$special_comment = create_comment([
    'post_id' => $test_post->id,
    'content' => $special_content
]);
assert_test($special_comment->content === $special_content, "특수문자 포함 댓글 저장 및 조회");

// 테스트 7-3: 이모지 포함 댓글
$emoji_content = "Emoji test: 😀😃😄😁🎉🎊🔥💯✅❌";
$emoji_comment = create_comment([
    'post_id' => $test_post->id,
    'content' => $emoji_content
]);
assert_test($emoji_comment->content === $emoji_content, "이모지 포함 댓글 저장 및 조회");

echo "\n";

// ============================================================================
// 테스트 그룹 8: 복잡한 트리 구조 테스트
// ============================================================================
echo "🧪 테스트 그룹 8: 복잡한 트리 구조 테스트\n";
echo str_repeat("-", 70) . "\n";

/*
트리 구조:
root1
├─ child1_1
│  ├─ child1_1_1
│  └─ child1_1_2
└─ child1_2
   ├─ child1_2_1
   └─ child1_2_2
      └─ child1_2_2_1
*/

$root1 = create_comment(['post_id' => $test_post->id, 'content' => 'root1']);
$child1_1 = create_comment(['post_id' => $test_post->id, 'parent_id' => $root1->id, 'content' => 'child1_1']);
$child1_1_1 = create_comment(['post_id' => $test_post->id, 'parent_id' => $child1_1->id, 'content' => 'child1_1_1']);
$child1_1_2 = create_comment(['post_id' => $test_post->id, 'parent_id' => $child1_1->id, 'content' => 'child1_1_2']);
$child1_2 = create_comment(['post_id' => $test_post->id, 'parent_id' => $root1->id, 'content' => 'child1_2']);
$child1_2_1 = create_comment(['post_id' => $test_post->id, 'parent_id' => $child1_2->id, 'content' => 'child1_2_1']);
$child1_2_2 = create_comment(['post_id' => $test_post->id, 'parent_id' => $child1_2->id, 'content' => 'child1_2_2']);
$child1_2_2_1 = create_comment(['post_id' => $test_post->id, 'parent_id' => $child1_2_2->id, 'content' => 'child1_2_2_1']);

// sort 순서 확인
$tree_comments = [$root1, $child1_1, $child1_1_1, $child1_1_2, $child1_2, $child1_2_1, $child1_2_2, $child1_2_2_1];
$tree_sorts_correct = true;
for ($i = 1; $i < count($tree_comments); $i++) {
    if ($tree_comments[$i]->sort <= $tree_comments[$i - 1]->sort) {
        $tree_sorts_correct = false;
        break;
    }
}
assert_test($tree_sorts_correct, "복잡한 트리 구조의 sort 순서가 올바름");

// depth 확인
assert_test($root1->depth === 0, "root1 depth = 0");
assert_test($child1_1->depth === 1 && $child1_2->depth === 1, "child1_1, child1_2 depth = 1");
assert_test($child1_1_1->depth === 2 && $child1_1_2->depth === 2, "child1_1_1, child1_1_2 depth = 2");
assert_test($child1_2_1->depth === 2 && $child1_2_2->depth === 2, "child1_2_1, child1_2_2 depth = 2");
assert_test($child1_2_2_1->depth === 3, "child1_2_2_1 depth = 3");

echo "\n";

// ============================================================================
// 테스트 정리 및 결과 출력
// ============================================================================
echo "🧹 테스트 정리: 테스트용 데이터 삭제\n";

$pdo = pdo();
$stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 댓글 삭제 완료\n";

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 게시글 삭제 완료\n";

unset($_SESSION['login']);
echo "   ✅ 로그아웃 완료\n";

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ 테스트 완료!\n";
echo "   총 테스트: {$test_count}개\n";
echo "   통과: {$passed_count}개\n";
echo "   실패: " . ($test_count - $passed_count) . "개\n";
echo str_repeat("=", 70) . "\n";

if ($test_count === $passed_count) {
    echo "🎉 모든 테스트 통과!\n";
} else {
    echo "❌ 일부 테스트 실패\n";
    exit(1);
}
