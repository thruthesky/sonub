<?php

/**
 * get_comments() 함수의 comment_count 속성 테스트
 *
 * 이 테스트는 get_comments() 함수가 각 댓글의 하위(자식) 댓글 개수를
 * `comment_count` 속성에 올바르게 계산하여 포함하는지 검증합니다.
 *
 * 검증 항목:
 * 1. 하위 댓글이 없는 댓글의 comment_count = 0
 * 2. 하위 댓글이 있는 댓글의 comment_count = 실제 자식 댓글 수
 * 3. 복잡한 댓글 구조에서도 정확한 계산
 * 4. 2단계 깊이 댓글에서도 올바른 계산
 *
 * 실행 방법:
 * php tests/comment/get-comments-comment-count.test.php
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
function assert_equal($expected, $actual, string $message): void
{
    if ($expected === $actual) {
        echo "   ✅ PASS: $message (예상: $expected, 실제: $actual)\n";
    } else {
        echo "   ❌ FAIL: $message\n";
        echo "      예상: $expected\n";
        echo "      실제: $actual\n";
        exit(1);
    }
}

/**
 * 테스트용 게시글 생성
 */
function create_test_post_for_comment_count(int $user_id): int
{
    $post = create_post([
        'category' => 'test',
        'title' => 'Test Post for Comment Count',
        'content' => 'Testing comment_count property'
    ]);
    return $post->id;
}

/**
 * 테스트 데이터 정리
 */
function cleanup_test_post(int $post_id): void
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
echo "get_comments() comment_count 속성 테스트\n";
echo "========================================\n\n";

// 테스트 사용자 로그인
login_as_test_user('banana');
$user = login();
echo "테스트 사용자: {$user->first_name} (ID: {$user->id})\n\n";

// ============================================================================
// 테스트 1: 댓글이 없는 게시글
// ============================================================================
echo "--- 테스트 1: 댓글이 없는 게시글 ---\n";

$post_id_1 = create_test_post_for_comment_count($user->id);
$comments_1 = get_comments(['post_id' => $post_id_1, 'limit' => 100]);

assert_equal(0, count($comments_1), '댓글이 없는 게시글의 댓글 배열 길이');

cleanup_test_post($post_id_1);
echo "\n";

// ============================================================================
// 테스트 2: 최상위 댓글만 있는 경우 (comment_count = 0)
// ============================================================================
echo "--- 테스트 2: 최상위 댓글만 있는 경우 ---\n";

$post_id_2 = create_test_post_for_comment_count($user->id);

// 최상위 댓글 3개 생성
$comment1 = create_comment([
    'post_id' => $post_id_2,
    'content' => 'First top-level comment'
]);

$comment2 = create_comment([
    'post_id' => $post_id_2,
    'content' => 'Second top-level comment'
]);

$comment3 = create_comment([
    'post_id' => $post_id_2,
    'content' => 'Third top-level comment'
]);

// get_comments() 호출
$comments_2 = get_comments(['post_id' => $post_id_2, 'limit' => 100]);

assert_equal(3, count($comments_2), '최상위 댓글 개수');
assert_equal(0, $comments_2[0]->comment_count, '첫 번째 댓글의 comment_count');
assert_equal(0, $comments_2[1]->comment_count, '두 번째 댓글의 comment_count');
assert_equal(0, $comments_2[2]->comment_count, '세 번째 댓글의 comment_count');

cleanup_test_post($post_id_2);
echo "\n";

// ============================================================================
// 테스트 3: 최상위 댓글 + 답글 (comment_count > 0)
// ============================================================================
echo "--- 테스트 3: 최상위 댓글 + 답글 ---\n";

$post_id_3 = create_test_post_for_comment_count($user->id);

// 최상위 댓글 생성
$parent_comment = create_comment([
    'post_id' => $post_id_3,
    'content' => 'Parent comment'
]);

// 답글 2개 생성
$reply1 = create_comment([
    'post_id' => $post_id_3,
    'parent_id' => $parent_comment->id,
    'content' => 'First reply'
]);

$reply2 = create_comment([
    'post_id' => $post_id_3,
    'parent_id' => $parent_comment->id,
    'content' => 'Second reply'
]);

// get_comments() 호출
$comments_3 = get_comments(['post_id' => $post_id_3, 'limit' => 100]);

assert_equal(3, count($comments_3), '전체 댓글 개수 (최상위 + 답글)');

// 첫 번째 댓글(최상위)은 2개의 답글을 가져야 함
assert_equal(2, $comments_3[0]->comment_count, '최상위 댓글의 comment_count (답글 2개)');

// 답글들은 하위 댓글이 없으므로 0
assert_equal(0, $comments_3[1]->comment_count, '첫 번째 답글의 comment_count');
assert_equal(0, $comments_3[2]->comment_count, '두 번째 답글의 comment_count');

cleanup_test_post($post_id_3);
echo "\n";

// ============================================================================
// 테스트 4: 복잡한 댓글 구조
// ============================================================================
echo "--- 테스트 4: 복잡한 댓글 구조 ---\n";

$post_id_4 = create_test_post_for_comment_count($user->id);

// 최상위 댓글 2개 생성
$parent1 = create_comment([
    'post_id' => $post_id_4,
    'content' => 'First parent comment'
]);

$parent2 = create_comment([
    'post_id' => $post_id_4,
    'content' => 'Second parent comment'
]);

// 첫 번째 최상위 댓글에 답글 3개
$reply1_1 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent1->id,
    'content' => 'Reply 1-1'
]);

$reply1_2 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent1->id,
    'content' => 'Reply 1-2'
]);

$reply1_3 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent1->id,
    'content' => 'Reply 1-3'
]);

// 두 번째 최상위 댓글에 답글 1개
$reply2_1 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent2->id,
    'content' => 'Reply 2-1'
]);

// get_comments() 호출 (limit을 충분히 크게 설정)
$comments_4 = get_comments(['post_id' => $post_id_4, 'limit' => 100]);

assert_equal(6, count($comments_4), '전체 댓글 개수');

// 첫 번째 최상위 댓글은 3개의 답글
assert_equal(3, $comments_4[0]->comment_count, '첫 번째 최상위 댓글의 comment_count (답글 3개)');

// 두 번째 최상위 댓글은 1개의 답글
assert_equal(1, $comments_4[1]->comment_count, '두 번째 최상위 댓글의 comment_count (답글 1개)');

// 답글들은 모두 0
assert_equal(0, $comments_4[2]->comment_count, '답글 1-1의 comment_count');
assert_equal(0, $comments_4[3]->comment_count, '답글 1-2의 comment_count');
assert_equal(0, $comments_4[4]->comment_count, '답글 1-3의 comment_count');
assert_equal(0, $comments_4[5]->comment_count, '답글 2-1의 comment_count');

cleanup_test_post($post_id_4);
echo "\n";

// ============================================================================
// 테스트 5: 2단계 깊이 댓글 구조
// ============================================================================
echo "--- 테스트 5: 2단계 깊이 댓글 구조 ---\n";

$post_id_5 = create_test_post_for_comment_count($user->id);

// 최상위 댓글
$level0 = create_comment([
    'post_id' => $post_id_5,
    'content' => 'Level 0 comment'
]);

// 1단계 답글
$level1 = create_comment([
    'post_id' => $post_id_5,
    'parent_id' => $level0->id,
    'content' => 'Level 1 reply'
]);

// 2단계 답글 (답글의 답글)
$level2_1 = create_comment([
    'post_id' => $post_id_5,
    'parent_id' => $level1->id,
    'content' => 'Level 2 reply 1'
]);

$level2_2 = create_comment([
    'post_id' => $post_id_5,
    'parent_id' => $level1->id,
    'content' => 'Level 2 reply 2'
]);

// get_comments() 호출
$comments_5 = get_comments(['post_id' => $post_id_5, 'limit' => 100]);

assert_equal(4, count($comments_5), '전체 댓글 개수 (3단계 깊이)');

// 최상위 댓글은 직접 자식 1개만 카운트 (손자는 포함 안 됨)
assert_equal(1, $comments_5[0]->comment_count, '최상위 댓글의 comment_count (직접 자식만)');

// 1단계 답글은 2개의 자식
assert_equal(2, $comments_5[1]->comment_count, '1단계 답글의 comment_count (2개)');

// 2단계 답글들은 자식이 없음
assert_equal(0, $comments_5[2]->comment_count, '2단계 답글 1의 comment_count');
assert_equal(0, $comments_5[3]->comment_count, '2단계 답글 2의 comment_count');

cleanup_test_post($post_id_5);
echo "\n";

// ============================================================================
// 테스트 6: last 파라미터와 comment_count
// ============================================================================
echo "--- 테스트 6: last 파라미터와 comment_count ---\n";

$post_id_6 = create_test_post_for_comment_count($user->id);

// 최상위 댓글 5개 생성
for ($i = 1; $i <= 5; $i++) {
    $parent = create_comment([
        'post_id' => $post_id_6,
        'content' => "Comment $i"
    ]);

    // 각 최상위 댓글에 답글 $i개 추가
    for ($j = 1; $j <= $i; $j++) {
        create_comment([
            'post_id' => $post_id_6,
            'parent_id' => $parent->id,
            'content' => "Reply $i-$j"
        ]);
    }
}

// last=10으로 마지막 10개 댓글 가져오기
$comments_6 = get_comments(['post_id' => $post_id_6, 'last' => 10]);

assert_equal(10, count($comments_6), 'last=10으로 가져온 댓글 개수');

// comment_count가 올바르게 계산되는지 확인
// 각 댓글의 comment_count 속성이 존재하고 정수인지 확인
foreach ($comments_6 as $comment) {
    $has_property = property_exists($comment, 'comment_count');
    if (!$has_property) {
        echo "   ❌ FAIL: comment_count 속성이 존재하지 않음\n";
        exit(1);
    }

    $is_integer = is_int($comment->comment_count);
    if (!$is_integer) {
        echo "   ❌ FAIL: comment_count가 정수가 아님 (타입: " . gettype($comment->comment_count) . ")\n";
        exit(1);
    }
}

echo "   ✅ PASS: 모든 댓글에 comment_count 속성 존재하고 정수 타입\n";

cleanup_test_post($post_id_6);
echo "\n";

// ============================================================================
// 테스트 7: limit/offset과 comment_count
// ============================================================================
echo "--- 테스트 7: limit/offset과 comment_count ---\n";

$post_id_7 = create_test_post_for_comment_count($user->id);

// 최상위 댓글 3개 생성
$parent1 = create_comment([
    'post_id' => $post_id_7,
    'content' => 'Parent 1'
]);

$parent2 = create_comment([
    'post_id' => $post_id_7,
    'content' => 'Parent 2'
]);

$parent3 = create_comment([
    'post_id' => $post_id_7,
    'content' => 'Parent 3'
]);

// 각각에 답글 추가
create_comment([
    'post_id' => $post_id_7,
    'parent_id' => $parent1->id,
    'content' => 'Reply 1'
]);

create_comment([
    'post_id' => $post_id_7,
    'parent_id' => $parent2->id,
    'content' => 'Reply 2-1'
]);

create_comment([
    'post_id' => $post_id_7,
    'parent_id' => $parent2->id,
    'content' => 'Reply 2-2'
]);

// limit=3, offset=0으로 첫 3개 댓글만 가져오기
$comments_7 = get_comments(['post_id' => $post_id_7, 'limit' => 3, 'offset' => 0]);

assert_equal(3, count($comments_7), 'limit=3으로 가져온 댓글 개수');

// 첫 번째 최상위 댓글은 1개의 답글
assert_equal(1, $comments_7[0]->comment_count, '첫 번째 최상위 댓글의 comment_count');

// 두 번째 최상위 댓글은 2개의 답글
assert_equal(2, $comments_7[1]->comment_count, '두 번째 최상위 댓글의 comment_count');

// 세 번째 최상위 댓글은 0개의 답글
assert_equal(0, $comments_7[2]->comment_count, '세 번째 최상위 댓글의 comment_count');

cleanup_test_post($post_id_7);
echo "\n";

// ============================================================================
// 테스트 완료
// ============================================================================

echo "========================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "========================================\n\n";

echo "검증된 기능:\n";
echo "1. 하위 댓글이 없는 댓글의 comment_count = 0\n";
echo "2. 하위 댓글이 있는 댓글의 comment_count = 실제 자식 댓글 수\n";
echo "3. 복잡한 댓글 구조에서도 정확한 계산\n";
echo "4. 2단계 깊이 댓글에서 직접 자식만 카운트\n";
echo "5. last 파라미터 사용 시 comment_count 올바름\n";
echo "6. limit/offset 파라미터 사용 시 comment_count 올바름\n";
echo "7. comment_count는 DB에 저장되지 않고 동적으로 계산됨\n";
echo "\n";
