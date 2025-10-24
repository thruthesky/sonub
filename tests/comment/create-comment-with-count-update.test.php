<?php

/**
 * create_comment()와 comment_count 자동 업데이트 통합 테스트
 *
 * 이 테스트는 create_comment() 함수 호출 시
 * 게시글의 comment_count가 자동으로 업데이트되는지 검증합니다.
 *
 * 실행 방법:
 * php tests/comment/create-comment-with-count-update.test.php
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 create_comment()와 comment_count 자동 업데이트 통합 테스트\n";
echo str_repeat("=", 70) . "\n\n";

// ============================================================================
// 테스트 준비
// ============================================================================
echo "📋 테스트 준비: 테스트용 게시글 생성\n";

login_as_test_user();
$user = login();
echo "   ✅ 테스트 사용자 로그인 완료 (ID: {$user->id})\n";

$test_post = create_post([
    'title' => 'Test Post for Auto Count Update ' . time(),
    'content' => 'Testing automatic comment count update',
    'category' => 'test'
]);
echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n\n";

// ============================================================================
// 테스트 1: 초기 상태 확인
// ============================================================================
echo "🧪 테스트 1: 초기 상태 확인\n";

$post = get_post_by_id($test_post->id);
echo "   📝 초기 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 0) {
    echo "   ✅ 통과: 초기 comment_count가 0\n\n";
} else {
    echo "   ❌ 실패: 초기 comment_count가 0이 아님\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: 첫 댓글 생성 시 자동 업데이트
// ============================================================================
echo "🧪 테스트 2: 첫 댓글 생성 시 자동 업데이트\n";

$comment1 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'First comment - should auto-update count'
]);
echo "   📝 첫 댓글 생성 (ID: {$comment1->id})\n";

// update_post_comment_count()를 호출하지 않고 바로 조회
$post = get_post_by_id($test_post->id);
echo "   📝 자동 업데이트 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 1) {
    echo "   ✅ 통과: comment_count가 자동으로 1로 업데이트됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 1이 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: 여러 댓글 생성 시 연속 자동 업데이트
// ============================================================================
echo "🧪 테스트 3: 여러 댓글 생성 시 연속 자동 업데이트\n";

$comment2 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Second comment'
]);
$post = get_post_by_id($test_post->id);
echo "   📝 두 번째 댓글 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count !== 2) {
    echo "   ❌ 실패: comment_count가 2가 아님\n\n";
    exit(1);
}

$comment3 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'Third comment'
]);
$post = get_post_by_id($test_post->id);
echo "   📝 세 번째 댓글 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 3) {
    echo "   ✅ 통과: comment_count가 연속으로 자동 업데이트됨 (0 → 1 → 2 → 3)\n\n";
} else {
    echo "   ❌ 실패: comment_count가 3이 아님\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: 대댓글 생성 시에도 자동 업데이트
// ============================================================================
echo "🧪 테스트 4: 대댓글 생성 시에도 자동 업데이트\n";

$reply1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'Reply to first comment'
]);
$post = get_post_by_id($test_post->id);
echo "   📝 대댓글 생성 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 4) {
    echo "   ✅ 통과: 대댓글도 comment_count에 포함됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 4가 아님\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: 손자 댓글도 자동 업데이트
// ============================================================================
echo "🧪 테스트 5: 손자 댓글도 자동 업데이트\n";

$nested_reply = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $reply1->id,
    'content' => 'Nested reply'
]);
$post = get_post_by_id($test_post->id);
echo "   📝 손자 댓글 생성 후 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 5) {
    echo "   ✅ 통과: 손자 댓글도 comment_count에 포함됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 5가 아님\n\n";
    exit(1);
}

// ============================================================================
// 테스트 6: 대규모 댓글 생성 시 성능 확인
// ============================================================================
echo "🧪 테스트 6: 대규모 댓글 생성 시 성능 확인 (20개)\n";

$start_time = microtime(true);
for ($i = 1; $i <= 20; $i++) {
    create_comment([
        'post_id' => $test_post->id,
        'content' => "Comment {$i}"
    ]);
}
$end_time = microtime(true);
$duration = round(($end_time - $start_time) * 1000, 2);

$post = get_post_by_id($test_post->id);
echo "   📝 20개 댓글 생성 후 comment_count: {$post->comment_count}\n";
echo "   📝 소요 시간: {$duration}ms\n";

if ($post->comment_count === 25) {
    echo "   ✅ 통과: 대규모 댓글 생성 시에도 정확히 업데이트됨\n\n";
} else {
    echo "   ❌ 실패: comment_count가 25가 아님 (actual: {$post->comment_count})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 7: count_comments() 함수와 일치 확인
// ============================================================================
echo "🧪 테스트 7: count_comments() 함수와 일치 확인\n";

$post = get_post_by_id($test_post->id);
$manual_count = count_comments(['post_id' => $test_post->id]);

echo "   📝 post->comment_count: {$post->comment_count}\n";
echo "   📝 count_comments(): {$manual_count}\n";

if ($post->comment_count === $manual_count) {
    echo "   ✅ 통과: comment_count와 count_comments() 결과가 정확히 일치\n\n";
} else {
    echo "   ❌ 실패: 값이 일치하지 않음\n\n";
    exit(1);
}

// ============================================================================
// 테스트 정리
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
echo "✅ 모든 통합 테스트 통과!\n";
echo "   - create_comment() 호출 시 comment_count가 자동 업데이트됨\n";
echo "   - 루트 댓글, 대댓글, 손자 댓글 모두 올바르게 카운트됨\n";
echo "   - 대규모 댓글 생성 시에도 정확하게 작동함\n";
echo str_repeat("=", 70) . "\n";
