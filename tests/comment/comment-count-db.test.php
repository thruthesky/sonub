<?php

/**
 * comments.comment_count 필드 테스트
 *
 * 이 테스트는 댓글의 comment_count 필드가 DB에 올바르게 저장되고
 * 댓글 생성/삭제 시 자동으로 업데이트되는지 검증합니다.
 *
 * 검증 항목:
 * 1. 최상위 댓글 생성 시 comment_count = 0
 * 2. 답글 생성 시 부모 댓글의 comment_count 자동 증가
 * 3. 여러 답글 생성 시 정확한 카운트
 * 4. 답글 삭제 시 부모 댓글의 comment_count 자동 감소
 * 5. 2단계 깊이에서 직접 자식만 카운트 (손자는 포함 안 됨)
 * 6. get_comments()로 가져온 값이 DB 값과 일치
 *
 * 실행 방법:
 * php tests/comment/comment-count-db.test.php
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
 * DB에서 직접 comment_count 조회
 */
function get_comment_count_from_db(int $comment_id): int
{
    $pdo = pdo();
    $stmt = $pdo->prepare("SELECT comment_count FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    return (int)$stmt->fetchColumn();
}

/**
 * 테스트용 게시글 생성
 */
function create_test_post_for_count(): int
{
    $post = create_post([
        'category' => 'test',
        'title' => 'Test Post for Comment Count',
        'content' => 'Testing comment_count in DB'
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
echo "comments.comment_count DB 필드 테스트\n";
echo "========================================\n\n";

// 테스트 사용자 로그인
login_as_test_user('banana');
$user = login();
echo "테스트 사용자: {$user->first_name} (ID: {$user->id})\n\n";

// ============================================================================
// 테스트 1: 최상위 댓글 생성 시 comment_count = 0
// ============================================================================
echo "--- 테스트 1: 최상위 댓글 생성 시 comment_count = 0 ---\n";

$post_id_1 = create_test_post_for_count();

$comment1 = create_comment([
    'post_id' => $post_id_1,
    'content' => 'Top-level comment'
]);

$db_count = get_comment_count_from_db($comment1->id);
assert_equal(0, $db_count, 'DB의 최상위 댓글 comment_count');
assert_equal(0, $comment1->comment_count, 'get_comment() 반환값의 comment_count');

cleanup_test_post($post_id_1);
echo "\n";

// ============================================================================
// 테스트 2: 답글 생성 시 부모 comment_count 자동 증가
// ============================================================================
echo "--- 테스트 2: 답글 생성 시 부모 comment_count 자동 증가 ---\n";

$post_id_2 = create_test_post_for_count();

// 최상위 댓글
$parent = create_comment([
    'post_id' => $post_id_2,
    'content' => 'Parent comment'
]);

echo "   📌 최상위 댓글 생성 (ID: {$parent->id})\n";
assert_equal(0, get_comment_count_from_db($parent->id), '답글 없을 때 comment_count');

// 첫 번째 답글
$reply1 = create_comment([
    'post_id' => $post_id_2,
    'parent_id' => $parent->id,
    'content' => 'First reply'
]);

echo "   📌 첫 번째 답글 생성 (ID: {$reply1->id})\n";
assert_equal(1, get_comment_count_from_db($parent->id), '첫 답글 후 부모의 comment_count');

// 두 번째 답글
$reply2 = create_comment([
    'post_id' => $post_id_2,
    'parent_id' => $parent->id,
    'content' => 'Second reply'
]);

echo "   📌 두 번째 답글 생성 (ID: {$reply2->id})\n";
assert_equal(2, get_comment_count_from_db($parent->id), '두 번째 답글 후 부모의 comment_count');

// get_comments()로 가져온 값 확인
$comments = get_comments(['post_id' => $post_id_2, 'limit' => 100]);
$parent_from_list = array_values(array_filter($comments, fn($c) => $c->id === $parent->id))[0];
assert_equal(2, $parent_from_list->comment_count, 'get_comments()로 가져온 부모의 comment_count');

cleanup_test_post($post_id_2);
echo "\n";

// ============================================================================
// 테스트 3: 복잡한 구조에서 여러 답글
// ============================================================================
echo "--- 테스트 3: 복잡한 구조에서 여러 답글 ---\n";

$post_id_3 = create_test_post_for_count();

// 최상위 댓글 2개
$parent1 = create_comment([
    'post_id' => $post_id_3,
    'content' => 'Parent 1'
]);

$parent2 = create_comment([
    'post_id' => $post_id_3,
    'content' => 'Parent 2'
]);

// parent1에 답글 3개
for ($i = 1; $i <= 3; $i++) {
    create_comment([
        'post_id' => $post_id_3,
        'parent_id' => $parent1->id,
        'content' => "Reply 1-$i"
    ]);
}

// parent2에 답글 1개
create_comment([
    'post_id' => $post_id_3,
    'parent_id' => $parent2->id,
    'content' => 'Reply 2-1'
]);

assert_equal(3, get_comment_count_from_db($parent1->id), 'Parent 1의 comment_count (3개)');
assert_equal(1, get_comment_count_from_db($parent2->id), 'Parent 2의 comment_count (1개)');

cleanup_test_post($post_id_3);
echo "\n";

// ============================================================================
// 테스트 4: 답글 삭제 시 부모 comment_count 자동 감소
// ============================================================================
echo "--- 테스트 4: 답글 삭제 시 부모 comment_count 자동 감소 ---\n";

$post_id_4 = create_test_post_for_count();

$parent = create_comment([
    'post_id' => $post_id_4,
    'content' => 'Parent'
]);

// 답글 3개 생성
$reply1 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent->id,
    'content' => 'Reply 1'
]);

$reply2 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent->id,
    'content' => 'Reply 2'
]);

$reply3 = create_comment([
    'post_id' => $post_id_4,
    'parent_id' => $parent->id,
    'content' => 'Reply 3'
]);

echo "   📌 답글 3개 생성 완료\n";
assert_equal(3, get_comment_count_from_db($parent->id), '답글 3개 후 부모의 comment_count');

// 첫 번째 답글 삭제
delete_comment(['comment_id' => $reply1->id]);
echo "   📌 첫 번째 답글 삭제\n";
assert_equal(2, get_comment_count_from_db($parent->id), '첫 답글 삭제 후 부모의 comment_count');

// 두 번째 답글 삭제
delete_comment(['comment_id' => $reply2->id]);
echo "   📌 두 번째 답글 삭제\n";
assert_equal(1, get_comment_count_from_db($parent->id), '두 번째 답글 삭제 후 부모의 comment_count');

// 세 번째 답글 삭제
delete_comment(['comment_id' => $reply3->id]);
echo "   📌 세 번째 답글 삭제\n";
assert_equal(0, get_comment_count_from_db($parent->id), '모든 답글 삭제 후 부모의 comment_count');

cleanup_test_post($post_id_4);
echo "\n";

// ============================================================================
// 테스트 5: 2단계 깊이 - 직접 자식만 카운트
// ============================================================================
echo "--- 테스트 5: 2단계 깊이 - 직접 자식만 카운트 ---\n";

$post_id_5 = create_test_post_for_count();

// 최상위 댓글 (level 0)
$level0 = create_comment([
    'post_id' => $post_id_5,
    'content' => 'Level 0'
]);

// 1단계 답글 (level 1)
$level1 = create_comment([
    'post_id' => $post_id_5,
    'parent_id' => $level0->id,
    'content' => 'Level 1'
]);

echo "   📌 Level 0 → Level 1 구조 생성\n";
assert_equal(1, get_comment_count_from_db($level0->id), 'Level 0의 comment_count (직접 자식 1개)');
assert_equal(0, get_comment_count_from_db($level1->id), 'Level 1의 comment_count (자식 없음)');

// 2단계 답글 2개 (level 2) - level1의 자식
$level2_1 = create_comment([
    'post_id' => $post_id_5,
    'parent_id' => $level1->id,
    'content' => 'Level 2-1'
]);

$level2_2 = create_comment([
    'post_id' => $post_id_5,
    'parent_id' => $level1->id,
    'content' => 'Level 2-2'
]);

echo "   📌 Level 1에 Level 2 자식 2개 추가\n";
assert_equal(1, get_comment_count_from_db($level0->id), 'Level 0의 comment_count (여전히 1, 손자는 카운트 안 됨)');
assert_equal(2, get_comment_count_from_db($level1->id), 'Level 1의 comment_count (직접 자식 2개)');

cleanup_test_post($post_id_5);
echo "\n";

// ============================================================================
// 테스트 6: get_comments()와 DB 값 일치 확인
// ============================================================================
echo "--- 테스트 6: get_comments()와 DB 값 일치 확인 ---\n";

$post_id_6 = create_test_post_for_count();

// 다양한 구조 생성
$p1 = create_comment(['post_id' => $post_id_6, 'content' => 'P1']);
$p2 = create_comment(['post_id' => $post_id_6, 'content' => 'P2']);
$p3 = create_comment(['post_id' => $post_id_6, 'content' => 'P3']);

create_comment(['post_id' => $post_id_6, 'parent_id' => $p1->id, 'content' => 'P1-R1']);
create_comment(['post_id' => $post_id_6, 'parent_id' => $p1->id, 'content' => 'P1-R2']);

create_comment(['post_id' => $post_id_6, 'parent_id' => $p2->id, 'content' => 'P2-R1']);

// get_comments()로 가져오기
$comments = get_comments(['post_id' => $post_id_6, 'limit' => 100]);

foreach ($comments as $comment) {
    $db_count = get_comment_count_from_db($comment->id);
    assert_equal($db_count, $comment->comment_count, "댓글 ID {$comment->id}의 comment_count 일치");
}

echo "   ✅ 모든 댓글의 get_comments() 값과 DB 값 일치\n";

cleanup_test_post($post_id_6);
echo "\n";

// ============================================================================
// 테스트 완료
// ============================================================================

echo "========================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "========================================\n\n";

echo "검증 완료:\n";
echo "1. ✅ 최상위 댓글 생성 시 comment_count = 0\n";
echo "2. ✅ 답글 생성 시 부모 comment_count 자동 증가\n";
echo "3. ✅ 여러 답글의 정확한 카운트\n";
echo "4. ✅ 답글 삭제 시 부모 comment_count 자동 감소\n";
echo "5. ✅ 2단계 깊이에서 직접 자식만 카운트 (손자 제외)\n";
echo "6. ✅ get_comments() 값과 DB 값 일치\n";
echo "7. ✅ comment_count는 DB에 저장되며 자동 업데이트됨\n";
echo "\n";
