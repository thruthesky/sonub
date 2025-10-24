<?php

/**
 * delete_post() 함수 댓글 삭제 테스트
 *
 * 이 테스트는 게시글 삭제 시 관련된 모든 댓글이 함께 삭제되는지 검증합니다.
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 delete_post() 함수 댓글 삭제 테스트\n";
echo "======================================================================\n\n";

// ============================================================================
// 테스트 준비: 테스트용 게시글 및 댓글 생성
// ============================================================================
echo "📋 테스트 준비: 테스트용 게시글 및 댓글 생성\n";

// 테스트 사용자로 로그인
login_as_test_user();
$user = login();
echo "   ✅ 테스트 사용자 로그인 완료 (ID: {$user->id})\n";

// 테스트용 게시글 생성
$test_post = create_post([
    'category' => 'discussion',
    'title' => 'Test Post for delete ' . time(),
    'content' => 'Test content for delete testing'
]);
echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n";

// ============================================================================
// 테스트 1: 댓글이 없는 게시글 삭제
// ============================================================================
echo "\n🧪 테스트 1: 댓글이 없는 게시글 삭제\n";

$post_id = $test_post->id;

// 삭제 전 게시글 존재 확인
$post = get_post_by_id($post_id);
if ($post) {
    echo "   ✅ 삭제 전 게시글 존재 확인\n";
} else {
    echo "   ❌ 실패: 게시글이 존재하지 않음\n\n";
    exit(1);
}

// 게시글 삭제
delete_post(['id' => $post_id]);
echo "   ✅ 게시글 삭제 완료\n";

// 삭제 후 게시글 확인
$post = get_post_by_id($post_id);
if ($post === null) {
    echo "   ✅ 통과: 게시글이 정상적으로 삭제됨\n\n";
} else {
    echo "   ❌ 실패: 게시글이 여전히 존재함\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: 댓글이 있는 게시글 삭제
// ============================================================================
echo "🧪 테스트 2: 댓글이 있는 게시글 삭제\n";

// 새 게시글 생성
$test_post2 = create_post([
    'category' => 'discussion',
    'title' => 'Test Post 2 for delete ' . time(),
    'content' => 'Test content 2'
]);
$post_id2 = $test_post2->id;
echo "   ✅ 새 게시글 생성 완료 (ID: {$post_id2})\n";

// 5개의 댓글 생성
$comment_ids = [];
for ($i = 1; $i <= 5; $i++) {
    $comment = create_comment([
        'post_id' => $post_id2,
        'content' => "Comment {$i}"
    ]);
    $comment_ids[] = $comment->id;
}
echo "   ✅ 5개의 댓글 생성 완료\n";

// 삭제 전 댓글 수 확인
$comment_count_before = count_comments(['post_id' => $post_id2]);
echo "   📝 삭제 전 댓글 수: {$comment_count_before}\n";

if ($comment_count_before === 5) {
    echo "   ✅ 통과: 5개의 댓글 확인\n";
} else {
    echo "   ❌ 실패: 예상 5개, 실제 {$comment_count_before}개\n\n";
    exit(1);
}

// 게시글의 comment_count 확인
$post = get_post_by_id($post_id2);
echo "   📝 게시글 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 5) {
    echo "   ✅ 통과: comment_count가 5로 올바르게 설정됨\n";
} else {
    echo "   ❌ 실패: 예상 5, 실제 {$post->comment_count}\n\n";
    exit(1);
}

// 게시글 삭제
delete_post(['id' => $post_id2]);
echo "   ✅ 게시글 삭제 완료\n";

// 삭제 후 댓글 수 확인
$comment_count_after = count_comments(['post_id' => $post_id2]);
echo "   📝 삭제 후 댓글 수: {$comment_count_after}\n";

if ($comment_count_after === 0) {
    echo "   ✅ 통과: 모든 댓글이 삭제됨\n";
} else {
    echo "   ❌ 실패: {$comment_count_after}개의 댓글이 남아있음\n\n";
    exit(1);
}

// 삭제 후 게시글 확인
$post = get_post_by_id($post_id2);
if ($post === null) {
    echo "   ✅ 통과: 게시글이 정상적으로 삭제됨\n\n";
} else {
    echo "   ❌ 실패: 게시글이 여전히 존재함\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: 대댓글이 있는 게시글 삭제
// ============================================================================
echo "🧪 테스트 3: 대댓글이 있는 게시글 삭제\n";

// 새 게시글 생성
$test_post3 = create_post([
    'category' => 'discussion',
    'title' => 'Test Post 3 for delete ' . time(),
    'content' => 'Test content 3'
]);
$post_id3 = $test_post3->id;
echo "   ✅ 새 게시글 생성 완료 (ID: {$post_id3})\n";

// 루트 댓글 3개 생성
$root_comments = [];
for ($i = 1; $i <= 3; $i++) {
    $comment = create_comment([
        'post_id' => $post_id3,
        'content' => "Root Comment {$i}"
    ]);
    $root_comments[] = $comment;
}
echo "   ✅ 3개의 루트 댓글 생성 완료\n";

// 첫 번째 루트 댓글에 대댓글 2개 추가
for ($i = 1; $i <= 2; $i++) {
    create_comment([
        'post_id' => $post_id3,
        'parent_id' => $root_comments[0]->id,
        'content' => "Reply {$i} to Root Comment 1"
    ]);
}
echo "   ✅ 2개의 대댓글 생성 완료\n";

// 삭제 전 댓글 수 확인 (총 5개: 루트 3개 + 대댓글 2개)
$comment_count_before = count_comments(['post_id' => $post_id3]);
echo "   📝 삭제 전 댓글 수: {$comment_count_before}\n";

if ($comment_count_before === 5) {
    echo "   ✅ 통과: 5개의 댓글 확인 (루트 3 + 대댓글 2)\n";
} else {
    echo "   ❌ 실패: 예상 5개, 실제 {$comment_count_before}개\n\n";
    exit(1);
}

// 게시글의 comment_count 확인
$post = get_post_by_id($post_id3);
echo "   📝 게시글 comment_count: {$post->comment_count}\n";

if ($post->comment_count === 5) {
    echo "   ✅ 통과: comment_count가 5로 올바르게 설정됨\n";
} else {
    echo "   ❌ 실패: 예상 5, 실제 {$post->comment_count}\n\n";
    exit(1);
}

// 게시글 삭제
delete_post(['id' => $post_id3]);
echo "   ✅ 게시글 삭제 완료\n";

// 삭제 후 댓글 수 확인
$comment_count_after = count_comments(['post_id' => $post_id3]);
echo "   📝 삭제 후 댓글 수: {$comment_count_after}\n";

if ($comment_count_after === 0) {
    echo "   ✅ 통과: 모든 댓글이 삭제됨 (루트 + 대댓글)\n";
} else {
    echo "   ❌ 실패: {$comment_count_after}개의 댓글이 남아있음\n\n";
    exit(1);
}

// 삭제 후 게시글 확인
$post = get_post_by_id($post_id3);
if ($post === null) {
    echo "   ✅ 통과: 게시글이 정상적으로 삭제됨\n\n";
} else {
    echo "   ❌ 실패: 게시글이 여전히 존재함\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: 많은 댓글이 있는 게시글 삭제 (성능 테스트)
// ============================================================================
echo "🧪 테스트 4: 많은 댓글이 있는 게시글 삭제 (50개 댓글)\n";

// 새 게시글 생성
$test_post4 = create_post([
    'category' => 'discussion',
    'title' => 'Test Post 4 for delete ' . time(),
    'content' => 'Test content 4'
]);
$post_id4 = $test_post4->id;
echo "   ✅ 새 게시글 생성 완료 (ID: {$post_id4})\n";

// 50개의 댓글 생성
$start_time = microtime(true);
for ($i = 1; $i <= 50; $i++) {
    create_comment([
        'post_id' => $post_id4,
        'content' => "Comment {$i}"
    ]);
}
$creation_time = (microtime(true) - $start_time) * 1000;
echo "   ✅ 50개의 댓글 생성 완료 (소요 시간: " . number_format($creation_time, 2) . "ms)\n";

// 삭제 전 댓글 수 확인
$comment_count_before = count_comments(['post_id' => $post_id4]);
echo "   📝 삭제 전 댓글 수: {$comment_count_before}\n";

if ($comment_count_before === 50) {
    echo "   ✅ 통과: 50개의 댓글 확인\n";
} else {
    echo "   ❌ 실패: 예상 50개, 실제 {$comment_count_before}개\n\n";
    exit(1);
}

// 게시글 삭제 (시간 측정)
$start_time = microtime(true);
delete_post(['id' => $post_id4]);
$deletion_time = (microtime(true) - $start_time) * 1000;
echo "   ✅ 게시글 삭제 완료 (소요 시간: " . number_format($deletion_time, 2) . "ms)\n";

// 삭제 후 댓글 수 확인
$comment_count_after = count_comments(['post_id' => $post_id4]);
echo "   📝 삭제 후 댓글 수: {$comment_count_after}\n";

if ($comment_count_after === 0) {
    echo "   ✅ 통과: 모든 댓글이 삭제됨\n";
} else {
    echo "   ❌ 실패: {$comment_count_after}개의 댓글이 남아있음\n\n";
    exit(1);
}

// 삭제 후 게시글 확인
$post = get_post_by_id($post_id4);
if ($post === null) {
    echo "   ✅ 통과: 게시글이 정상적으로 삭제됨\n\n";
} else {
    echo "   ❌ 실패: 게시글이 여전히 존재함\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: 개별 댓글 ID로 댓글 삭제 확인
// ============================================================================
echo "🧪 테스트 5: 개별 댓글 ID로 댓글 삭제 확인\n";

// 새 게시글 생성
$test_post5 = create_post([
    'category' => 'discussion',
    'title' => 'Test Post 5 for delete ' . time(),
    'content' => 'Test content 5'
]);
$post_id5 = $test_post5->id;
echo "   ✅ 새 게시글 생성 완료 (ID: {$post_id5})\n";

// 3개의 댓글 생성하고 ID 저장
$comment1 = create_comment(['post_id' => $post_id5, 'content' => 'Comment 1']);
$comment2 = create_comment(['post_id' => $post_id5, 'content' => 'Comment 2']);
$comment3 = create_comment(['post_id' => $post_id5, 'content' => 'Comment 3']);
echo "   ✅ 3개의 댓글 생성 완료\n";
echo "   📝 댓글 ID: {$comment1->id}, {$comment2->id}, {$comment3->id}\n";

// 게시글 삭제
delete_post(['id' => $post_id5]);
echo "   ✅ 게시글 삭제 완료\n";

// 각 댓글 ID로 직접 확인
$pdo = pdo();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE id IN (?, ?, ?)");
$stmt->execute([$comment1->id, $comment2->id, $comment3->id]);
$remaining_comments = $stmt->fetchColumn();

echo "   📝 삭제 후 남아있는 댓글 수: {$remaining_comments}\n";

if ($remaining_comments === 0) {
    echo "   ✅ 통과: 모든 댓글이 완전히 삭제됨\n\n";
} else {
    echo "   ❌ 실패: {$remaining_comments}개의 댓글이 남아있음\n\n";
    exit(1);
}

// 로그아웃
unset($_SESSION['login']);

echo "======================================================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "======================================================================\n";
