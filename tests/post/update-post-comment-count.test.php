<?php

/**
 * update_post_comment_count() 함수 테스트
 *
 * 이 테스트는 게시글의 comment_count 필드가 올바르게 업데이트되는지 검증합니다.
 * - 댓글이 없을 때
 * - 댓글 추가 후
 * - 댓글 삭제 후
 * - 대댓글 추가 시
 *
 * 실행 방법:
 * php tests/post/update-post-comment-count.test.php
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 update_post_comment_count() 함수 테스트\n";
echo str_repeat("=", 70) . "\n\n";

// ============================================================================
// 테스트 준비: 테스트용 게시글 생성
// ============================================================================
echo "📋 테스트 준비: 테스트용 게시글 생성\n";

login_as_test_user();
$user = login();
echo "   ✅ 테스트 사용자 로그인 완료 (ID: {$user->id})\n";

$test_post = create_post([
    'title' => 'Test Post for Comment Count ' . time(),
    'content' => 'Post for testing comment count updates',
    'category' => 'test'
]);
echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n\n";

// ============================================================================
// 테스트 1: 초기 상태 확인 (댓글 0개)
// ============================================================================
echo "🧪 테스트 1: 초기 상태 확인 (댓글 0개)\n";

$post = get_post_by_id($test_post->id);
echo "   📝 초기 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 0) {
    echo "   ✅ 통과: 초기 comment_count가 0\n\n";
} else {
    echo "   ❌ 실패: 초기 comment_count가 0이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: 첫 번째 댓글 추가 후 업데이트
// ============================================================================
echo "🧪 테스트 2: 첫 번째 댓글 추가 후 업데이트\n";

$comment1 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'First comment'
]);
echo "   📝 첫 번째 댓글 생성 (ID: {$comment1->id})\n";

// comment_count 업데이트
update_post_comment_count($test_post->id);

// 게시글 다시 조회
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 1) {
    echo "   ✅ 통과: comment_count가 1로 업데이트됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 1이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: 여러 댓글 추가 후 업데이트
// ============================================================================
echo "🧪 테스트 3: 여러 댓글 추가 후 업데이트\n";

$comment2 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Second comment'
]);
$comment3 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Third comment'
]);
echo "   📝 2개의 댓글 추가 생성\n";

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 3) {
    echo "   ✅ 통과: comment_count가 3으로 업데이트됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 3이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: 대댓글 추가 후 업데이트 (대댓글도 포함되어야 함)
// ============================================================================
echo "🧪 테스트 4: 대댓글 추가 후 업데이트\n";

$reply1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'Reply to first comment'
]);
$reply2 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'Another reply'
]);
echo "   📝 2개의 대댓글 생성\n";

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 5) {
    echo "   ✅ 통과: comment_count가 5로 업데이트됨 (대댓글 포함)\n\n";
} else {
    echo "   ❌ 실패: comment_count가 5가 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: 손자 댓글 추가 후 업데이트
// ============================================================================
echo "🧪 테스트 5: 손자 댓글 추가 후 업데이트\n";

$nested_reply = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $reply1->id,
    'content' => 'Reply to reply'
]);
echo "   📝 손자 댓글 생성\n";

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 6) {
    echo "   ✅ 통과: comment_count가 6으로 업데이트됨 (손자 댓글 포함)\n\n";
} else {
    echo "   ❌ 실패: comment_count가 6이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 6: 댓글 삭제 후 업데이트
// ============================================================================
echo "🧪 테스트 6: 댓글 삭제 후 업데이트\n";

$pdo = pdo();
$stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
$stmt->execute([$comment3->id]);
echo "   📝 comment3 삭제 (ID: {$comment3->id})\n";

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 5) {
    echo "   ✅ 통과: comment_count가 5로 감소됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 5가 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 7: 모든 댓글 삭제 후 업데이트
// ============================================================================
echo "🧪 테스트 7: 모든 댓글 삭제 후 업데이트\n";

$stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
$stmt->execute([$test_post->id]);
echo "   📝 모든 댓글 삭제\n";

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 0) {
    echo "   ✅ 통과: comment_count가 0으로 리셋됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 0이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 8: 대규모 댓글 추가 후 업데이트
// ============================================================================
echo "🧪 테스트 8: 대규모 댓글 추가 후 업데이트 (50개)\n";

for ($i = 1; $i <= 50; $i++) {
    create_comment([
        'post_id' => $test_post->id,
        'content' => "Comment {$i}"
    ]);
}
echo "   📝 50개의 댓글 생성\n";

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
echo "   📝 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 50) {
    echo "   ✅ 통과: comment_count가 50으로 업데이트됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 50이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 9: 존재하지 않는 게시글 ID로 호출 (에러 없이 실행되어야 함)
// ============================================================================
echo "🧪 테스트 9: 존재하지 않는 게시글 ID로 호출\n";

try {
    update_post_comment_count(999999);
    echo "   ✅ 통과: 에러 없이 실행됨\n\n";
} catch (Exception $e) {
    echo "   ❌ 실패: 예외 발생 - {$e->getMessage()}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 10: count_comments() 함수와 비교
// ============================================================================
echo "🧪 테스트 10: count_comments() 함수와 결과 비교\n";

// 댓글 10개 추가 생성 (기존 50개 + 10개 = 60개)
for ($i = 51; $i <= 60; $i++) {
    create_comment([
        'post_id' => $test_post->id,
        'content' => "Comment {$i}"
    ]);
}

update_post_comment_count($test_post->id);
$post = get_post_by_id($test_post->id);
$manual_count = count_comments(['post_id' => $test_post->id]);

echo "   📝 post->comment_count: {$post->comment_count}\n";
echo "   📝 count_comments(): {$manual_count}\n";

if ($post->comment_count === $manual_count && $post->comment_count === 60) {
    echo "   ✅ 통과: comment_count와 count_comments() 결과가 일치 (60개)\n\n";
} else {
    echo "   ❌ 실패: 값이 일치하지 않음\n\n";
    exit(1);
}

// ============================================================================
// 테스트 정리: 테스트용 데이터 삭제
// ============================================================================
echo "🧹 테스트 정리: 테스트용 데이터 삭제\n";

$stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 댓글 삭제 완료\n";

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 게시글 삭제 완료\n";

unset($_SESSION['login']);
echo "   ✅ 로그아웃 완료\n";

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ 모든 테스트 통과!\n";
echo str_repeat("=", 70) . "\n";
