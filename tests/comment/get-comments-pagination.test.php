<?php

/**
 * get_comments() 함수 페이지네이션 테스트
 *
 * 이 테스트는 offset과 last 파라미터가 올바르게 작동하는지 검증합니다.
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 get_comments() 페이지네이션 테스트\n";
echo "======================================================================\n\n";

// ============================================================================
// 테스트 준비: 테스트용 게시글 및 댓글 생성
// ============================================================================
echo "📋 테스트 준비: 테스트용 게시글 및 10개의 댓글 생성\n";

// 테스트 사용자로 로그인
login_as_test_user();
$user = login();
echo "   ✅ 테스트 사용자 로그인 완료 (ID: {$user->id})\n";

// 테스트용 게시글 생성
$test_post = create_post([
    'category' => 'discussion',
    'title' => 'Test Post for get_comments pagination ' . time(),
    'content' => 'Test content for comment pagination testing'
]);
echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n";

// 10개의 루트 댓글 생성
$comments = [];
for ($i = 1; $i <= 10; $i++) {
    $comment = create_comment([
        'post_id' => $test_post->id,
        'content' => "Comment {$i}"
    ]);
    $comments[] = $comment;
}
echo "   ✅ 10개의 댓글 생성 완료\n";
echo "   📝 댓글 sort 순서:\n";
foreach ($comments as $i => $comment) {
    echo "      [{$i}] ID: {$comment->id}, sort: {$comment->sort}, content: {$comment->content}\n";
}
echo "\n";

// ============================================================================
// 테스트 1: 기본 limit 테스트 (offset 없이)
// ============================================================================
echo "🧪 테스트 1: 기본 limit 테스트 (처음 3개 가져오기)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 3
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 3) {
    echo "   ✅ 통과: 3개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 3개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 1' && $result[2]->content === 'Comment 3') {
    echo "   ✅ 통과: 올바른 순서 (Comment 1, 2, 3)\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: offset 테스트 (3번째부터 3개)
// ============================================================================
echo "🧪 테스트 2: offset 테스트 (3번째부터 3개 가져오기)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 3,
    'offset' => 3
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 3) {
    echo "   ✅ 통과: 3개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 3개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 4' && $result[2]->content === 'Comment 6') {
    echo "   ✅ 통과: 올바른 순서 (Comment 4, 5, 6)\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n";
    echo "   📝 예상: Comment 4, 5, 6\n";
    echo "   📝 실제: {$result[0]->content}, {$result[1]->content}, {$result[2]->content}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: offset 테스트 (6번째부터 3개)
// ============================================================================
echo "🧪 테스트 3: offset 테스트 (6번째부터 3개 가져오기)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 3,
    'offset' => 6
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 3) {
    echo "   ✅ 통과: 3개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 3개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 7' && $result[2]->content === 'Comment 9') {
    echo "   ✅ 통과: 올바른 순서 (Comment 7, 8, 9)\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: offset 테스트 (9번째부터 5개 요청, 1개만 반환되어야 함)
// ============================================================================
echo "🧪 테스트 4: offset 테스트 (9번째부터 5개 요청, 1개만 반환)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 5,
    'offset' => 9
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 1) {
    echo "   ✅ 통과: 1개의 댓글 반환 (나머지 없음)\n";
} else {
    echo "   ❌ 실패: 예상 1개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 10') {
    echo "   ✅ 통과: 올바른 댓글 (Comment 10)\n\n";
} else {
    echo "   ❌ 실패: 잘못된 댓글\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: last 파라미터 테스트 (최근 3개)
// ============================================================================
echo "🧪 테스트 5: last 파라미터 테스트 (최근 3개 가져오기)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'last' => 3
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 3) {
    echo "   ✅ 통과: 3개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 3개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

// last는 최근 3개를 가져오지만, 올바른 순서(ASC)로 반환되어야 함
if ($result[0]->content === 'Comment 8' && $result[2]->content === 'Comment 10') {
    echo "   ✅ 통과: 올바른 순서 (Comment 8, 9, 10)\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n";
    echo "   📝 예상: Comment 8, 9, 10\n";
    echo "   📝 실제: {$result[0]->content}, {$result[1]->content}, {$result[2]->content}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 6: last 파라미터 테스트 (최근 5개)
// ============================================================================
echo "🧪 테스트 6: last 파라미터 테스트 (최근 5개 가져오기)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'last' => 5
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 5) {
    echo "   ✅ 통과: 5개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 5개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 6' && $result[4]->content === 'Comment 10') {
    echo "   ✅ 통과: 올바른 순서 (Comment 6, 7, 8, 9, 10)\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 7: last 파라미터 테스트 (모든 댓글보다 많이 요청)
// ============================================================================
echo "🧪 테스트 7: last 파라미터 테스트 (20개 요청, 10개만 반환)\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'last' => 20
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";

if (count($result) === 10) {
    echo "   ✅ 통과: 10개의 댓글 반환 (전체 개수)\n";
} else {
    echo "   ❌ 실패: 예상 10개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 1' && $result[9]->content === 'Comment 10') {
    echo "   ✅ 통과: 올바른 순서 (Comment 1 ~ 10)\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 8: last가 limit/offset보다 우선하는지 확인
// ============================================================================
echo "🧪 테스트 8: last가 limit/offset보다 우선하는지 확인\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 100,
    'offset' => 5,
    'last' => 3
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";

if (count($result) === 3) {
    echo "   ✅ 통과: last 파라미터가 우선하여 3개 반환\n";
} else {
    echo "   ❌ 실패: 예상 3개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

if ($result[0]->content === 'Comment 8') {
    echo "   ✅ 통과: last 로직 적용됨 (Comment 8, 9, 10)\n\n";
} else {
    echo "   ❌ 실패: offset 로직이 적용됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 9: 대댓글이 포함된 경우 sort 순서 테스트
// ============================================================================
echo "🧪 테스트 9: 대댓글이 포함된 경우 sort 순서 테스트\n";

// 첫 번째 댓글에 대댓글 3개 추가
$reply1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comments[0]->id,
    'content' => 'Reply to Comment 1 - A'
]);
$reply2 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comments[0]->id,
    'content' => 'Reply to Comment 1 - B'
]);
$reply3 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comments[0]->id,
    'content' => 'Reply to Comment 1 - C'
]);

echo "   ✅ 대댓글 3개 추가 완료\n";

// 처음 5개 가져오기 (Comment 1 + 대댓글 3개 + Comment 2)
$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 5
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용 및 sort:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort}, depth: {$comment->depth})\n";
}

if (count($result) === 5) {
    echo "   ✅ 통과: 5개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 5개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

// sort 순서가 올바른지 확인
if (
    $result[0]->content === 'Comment 1' &&
    $result[1]->content === 'Reply to Comment 1 - A' &&
    $result[2]->content === 'Reply to Comment 1 - B' &&
    $result[3]->content === 'Reply to Comment 1 - C' &&
    $result[4]->content === 'Comment 2'
) {
    echo "   ✅ 통과: sort 순서가 올바름 (트리 구조 유지)\n\n";
} else {
    echo "   ❌ 실패: sort 순서가 잘못됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 10: last로 대댓글 포함 테스트
// ============================================================================
echo "🧪 테스트 10: last로 대댓글 포함하여 최근 5개 가져오기\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'last' => 5
]);

echo "   📝 가져온 댓글 수: " . count($result) . "\n";
echo "   📝 댓글 내용:\n";
foreach ($result as $i => $comment) {
    echo "      [{$i}] {$comment->content} (sort: {$comment->sort})\n";
}

if (count($result) === 5) {
    echo "   ✅ 통과: 5개의 댓글 반환\n";
} else {
    echo "   ❌ 실패: 예상 5개, 실제 " . count($result) . "개\n\n";
    exit(1);
}

// 최근 5개는 Comment 10, 9, 8, 7, 6이어야 하고, ASC 순서로 반환
if ($result[0]->content === 'Comment 6' && $result[4]->content === 'Comment 10') {
    echo "   ✅ 통과: 최근 5개가 올바른 순서로 반환됨\n\n";
} else {
    echo "   ❌ 실패: 순서가 잘못됨\n\n";
    exit(1);
}

// ============================================================================
// 테스트 11: 작성자 정보 포함 확인
// ============================================================================
echo "🧪 테스트 11: 작성자 정보 포함 확인\n";

$result = get_comments([
    'post_id' => $test_post->id,
    'limit' => 1
]);

if ($result[0]->author !== null) {
    echo "   ✅ 통과: author 정보 포함됨\n";
} else {
    echo "   ❌ 실패: author 정보 없음\n\n";
    exit(1);
}

if ($result[0]->author->first_name !== null) {
    echo "   ✅ 통과: author.first_name 포함됨\n";
    echo "   📝 작성자: {$result[0]->author->first_name}\n\n";
} else {
    echo "   ❌ 실패: author.first_name 없음\n\n";
    exit(1);
}

// ============================================================================
// 테스트 정리: 테스트용 데이터 삭제
// ============================================================================
echo "🧹 테스트 정리: 테스트용 데이터 삭제\n";

$pdo = pdo();
$stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 댓글 삭제 완료\n";

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$test_post->id]);
echo "   ✅ 게시글 삭제 완료\n";

// 로그아웃
unset($_SESSION['login']);

echo "\n======================================================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "======================================================================\n";
