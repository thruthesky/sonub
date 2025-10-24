<?php

/**
 * get_comment_info() 함수 테스트
 *
 * 이 테스트는 댓글 작성에 필요한 정보(parent_sort, depth, no_of_comments)를
 * 올바르게 가져오는지 검증합니다.
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 get_comment_info() 함수 테스트\n";
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
    'title' => 'Test Post for get_comment_info ' . time(),
    'content' => 'Test content for comment info testing'
]);
echo "   ✅ 테스트용 게시글 생성 완료 (ID: {$test_post->id})\n\n";

// ============================================================================
// 테스트 1: 첫 번째 루트 댓글 정보 (댓글이 하나도 없을 때)
// ============================================================================
echo "🧪 테스트 1: 첫 번째 루트 댓글 정보 (댓글이 하나도 없을 때)\n";

$info = get_comment_info($test_post->id, 0);

echo "   📝 parent_sort: " . ($info['parent_sort'] ?? 'null') . "\n";
echo "   📝 depth: {$info['depth']}\n";
echo "   📝 no_of_comments: {$info['no_of_comments']}\n";

if ($info['parent_sort'] === null) {
    echo "   ✅ 통과: parent_sort가 null (루트 댓글)\n";
} else {
    echo "   ❌ 실패: parent_sort가 null이 아님\n\n";
    exit(1);
}

if ($info['depth'] === 0) {
    echo "   ✅ 통과: depth가 0 (첫 번째 레벨)\n";
} else {
    echo "   ❌ 실패: depth가 0이 아님 (actual: {$info['depth']})\n\n";
    exit(1);
}

if ($info['no_of_comments'] === 1) {
    echo "   ✅ 통과: no_of_comments가 1 (첫 번째 댓글)\n\n";
} else {
    echo "   ❌ 실패: no_of_comments가 1이 아님 (actual: {$info['no_of_comments']})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: 루트 댓글 1개 생성 후, 두 번째 루트 댓글 정보
// ============================================================================
echo "🧪 테스트 2: 루트 댓글 1개 생성 후, 두 번째 루트 댓글 정보\n";

// 첫 번째 루트 댓글 생성
$comment1 = create_comment([
    'post_id' => $test_post->id,
    'content' => 'First root comment'
]);
echo "   📝 첫 번째 댓글 생성 (ID: {$comment1->id}, sort: {$comment1->sort})\n";

// 두 번째 루트 댓글 정보 가져오기
$info = get_comment_info($test_post->id, 0);

echo "   📝 parent_sort: " . ($info['parent_sort'] ?? 'null') . "\n";
echo "   📝 depth: {$info['depth']}\n";
echo "   📝 no_of_comments: {$info['no_of_comments']}\n";

if ($info['parent_sort'] === null) {
    echo "   ✅ 통과: parent_sort가 null (루트 댓글)\n";
} else {
    echo "   ❌ 실패: parent_sort가 null이 아님\n\n";
    exit(1);
}

if ($info['depth'] === 0) {
    echo "   ✅ 통과: depth가 0 (첫 번째 레벨)\n";
} else {
    echo "   ❌ 실패: depth가 0이 아님\n\n";
    exit(1);
}

if ($info['no_of_comments'] === 2) {
    echo "   ✅ 통과: no_of_comments가 2 (두 번째 댓글)\n\n";
} else {
    echo "   ❌ 실패: no_of_comments가 2가 아님 (actual: {$info['no_of_comments']})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: 첫 번째 댓글의 첫 대댓글 정보
// ============================================================================
echo "🧪 테스트 3: 첫 번째 댓글의 첫 대댓글 정보\n";

$info = get_comment_info($test_post->id, $comment1->id);

echo "   📝 parent_sort: {$info['parent_sort']}\n";
echo "   📝 depth: {$info['depth']}\n";
echo "   📝 no_of_comments: {$info['no_of_comments']}\n";

if ($info['parent_sort'] === $comment1->sort) {
    echo "   ✅ 통과: parent_sort가 부모 댓글의 sort와 일치\n";
} else {
    echo "   ❌ 실패: parent_sort가 부모 댓글의 sort와 불일치\n";
    echo "   📝 예상: {$comment1->sort}\n";
    echo "   📝 실제: {$info['parent_sort']}\n\n";
    exit(1);
}

if ($info['depth'] === 1) {
    echo "   ✅ 통과: depth가 1 (두 번째 레벨)\n";
} else {
    echo "   ❌ 실패: depth가 1이 아님 (actual: {$info['depth']})\n\n";
    exit(1);
}

if ($info['no_of_comments'] === 1) {
    echo "   ✅ 통과: no_of_comments가 1 (첫 번째 대댓글)\n\n";
} else {
    echo "   ❌ 실패: no_of_comments가 1이 아님 (actual: {$info['no_of_comments']})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: 대댓글 1개 생성 후, 두 번째 대댓글 정보
// ============================================================================
echo "🧪 테스트 4: 대댓글 1개 생성 후, 두 번째 대댓글 정보\n";

// 첫 번째 대댓글 생성
$reply1 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'First reply'
]);
echo "   📝 첫 번째 대댓글 생성 (ID: {$reply1->id}, sort: {$reply1->sort})\n";

// 두 번째 대댓글 정보 가져오기
$info = get_comment_info($test_post->id, $comment1->id);

echo "   📝 parent_sort: {$info['parent_sort']}\n";
echo "   📝 depth: {$info['depth']}\n";
echo "   📝 no_of_comments: {$info['no_of_comments']}\n";

if ($info['parent_sort'] === $comment1->sort) {
    echo "   ✅ 통과: parent_sort가 부모 댓글의 sort와 일치\n";
} else {
    echo "   ❌ 실패: parent_sort가 부모 댓글의 sort와 불일치\n\n";
    exit(1);
}

if ($info['depth'] === 1) {
    echo "   ✅ 통과: depth가 1 (두 번째 레벨)\n";
} else {
    echo "   ❌ 실패: depth가 1이 아님\n\n";
    exit(1);
}

if ($info['no_of_comments'] === 2) {
    echo "   ✅ 통과: no_of_comments가 2 (두 번째 대댓글)\n\n";
} else {
    echo "   ❌ 실패: no_of_comments가 2가 아님 (actual: {$info['no_of_comments']})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: 대댓글의 대댓글 (손자 댓글) 정보
// ============================================================================
echo "🧪 테스트 5: 대댓글의 대댓글 (손자 댓글) 정보\n";

$info = get_comment_info($test_post->id, $reply1->id);

echo "   📝 parent_sort: {$info['parent_sort']}\n";
echo "   📝 depth: {$info['depth']}\n";
echo "   📝 no_of_comments: {$info['no_of_comments']}\n";

if ($info['parent_sort'] === $reply1->sort) {
    echo "   ✅ 통과: parent_sort가 부모 댓글의 sort와 일치\n";
} else {
    echo "   ❌ 실패: parent_sort가 부모 댓글의 sort와 불일치\n\n";
    exit(1);
}

if ($info['depth'] === 2) {
    echo "   ✅ 통과: depth가 2 (세 번째 레벨)\n";
} else {
    echo "   ❌ 실패: depth가 2가 아님 (actual: {$info['depth']})\n\n";
    exit(1);
}

if ($info['no_of_comments'] === 1) {
    echo "   ✅ 통과: no_of_comments가 1 (첫 번째 손자 댓글)\n\n";
} else {
    echo "   ❌ 실패: no_of_comments가 1이 아님 (actual: {$info['no_of_comments']})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 6: get_comment_sort()와 통합 테스트
// ============================================================================
echo "🧪 테스트 6: get_comment_sort()와 통합 테스트\n";

// 새로운 루트 댓글 정보로 sort 생성
$info = get_comment_info($test_post->id, 0);
$sort = get_comment_sort($info['parent_sort'], $info['depth'], $info['no_of_comments']);

echo "   📝 생성된 sort: {$sort}\n";

// 첫 번째 댓글 sort는 0001,... 이었으므로, 두 번째는 0002,... 여야 함
$expected_prefix = '0002,';
if (str_starts_with($sort, $expected_prefix)) {
    echo "   ✅ 통과: sort가 올바르게 생성됨 (0002,... 시작)\n\n";
} else {
    echo "   ❌ 실패: sort가 올바르게 생성되지 않음\n";
    echo "   📝 예상 시작: {$expected_prefix}\n";
    echo "   📝 실제: {$sort}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 7: 실제 댓글 생성과 비교
// ============================================================================
echo "🧪 테스트 7: 실제 댓글 생성과 비교\n";

// get_comment_info()로 정보 가져오기
$info = get_comment_info($test_post->id, $comment1->id);
$expected_sort = get_comment_sort($info['parent_sort'], $info['depth'], $info['no_of_comments']);

// 실제 댓글 생성
$reply2 = create_comment([
    'post_id' => $test_post->id,
    'parent_id' => $comment1->id,
    'content' => 'Second reply'
]);

echo "   📝 예상 sort: {$expected_sort}\n";
echo "   📝 실제 sort: {$reply2->sort}\n";

if ($reply2->sort === $expected_sort) {
    echo "   ✅ 통과: get_comment_info()와 create_comment()의 sort가 일치\n\n";
} else {
    echo "   ❌ 실패: sort가 불일치\n\n";
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
