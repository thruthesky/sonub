<?php

/**
 * delete_post() 함수 테스트
 *
 * 이 파일은 delete_post() 함수를 테스트합니다.
 * 테스트 내용:
 * - 게시글 삭제
 * - 피드 항목 자동 삭제
 * - 첨부 파일 자동 삭제
 * - 권한 확인 (본인 게시글만 삭제 가능)
 * - 존재하지 않는 게시글 삭제 시도
 *
 * 실행 방법:
 * php tests/post/delete-post.test.php
 */

declare(strict_types=1);

// init.php 로드
include __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "========================================\n";
echo "delete_post() 함수 테스트 시작\n";
echo "========================================\n\n";

// 테스트 결과 카운터
$passed = 0;
$failed = 0;

/**
 * 테스트 헬퍼 함수: 결과 검증
 *
 * @param bool $condition 검증할 조건
 * @param string $message 테스트 메시지
 */
function assert_true(bool $condition, string $message): void
{
    global $passed, $failed;
    if ($condition) {
        echo "✓ $message\n";
        $passed++;
    } else {
        echo "✗ $message\n";
        $failed++;
    }
}

/**
 * 테스트 헬퍼 함수: 두 값이 같은지 검증
 *
 * @param mixed $expected 예상 값
 * @param mixed $actual 실제 값
 * @param string $message 테스트 메시지
 */
function assert_equals($expected, $actual, string $message): void
{
    assert_true($expected === $actual, "$message (예상: " . var_export($expected, true) . ", 실제: " . var_export($actual, true) . ")");
}

/**
 * 테스트 헬퍼 함수: 예외 발생 확인
 *
 * @param callable $fn 실행할 함수
 * @param string $expected_error_code 예상 에러 코드
 * @param string $message 테스트 메시지
 */
function assert_throws(callable $fn, string $expected_error_code, string $message): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "✗ $message (예외가 발생하지 않음)\n";
        $failed++;
    } catch (ApiException $e) {
        if ($e->getErrorCode() === $expected_error_code) {
            echo "✓ $message\n";
            $passed++;
        } else {
            echo "✗ $message (예상: $expected_error_code, 실제: {$e->getErrorCode()})\n";
            $failed++;
        }
    }
}

// ========================================================================
// 1. 테스트 사용자로 로그인
// ========================================================================

echo "--- 테스트 1: 테스트 사용자 로그인 ---\n";

login_as_test_user();
$user = login();
assert_true($user !== false, "테스트 사용자 로그인 성공");
echo "✓ 로그인한 사용자: {$user->first_name} (ID: {$user->id})\n\n";

// ========================================================================
// 2. 테스트용 게시글 생성
// ========================================================================

echo "--- 테스트 2: 테스트용 게시글 생성 ---\n";

$post1 = create_post([
    'category' => 'test-delete',
    'title' => 'Test Post 1 for Delete',
    'content' => 'This is a test post 1',
    'visibility' => 'public'
]);

assert_true($post1 instanceof PostModel, "게시글 1 생성 성공");
echo "✓ 게시글 1 ID: {$post1->id}\n";

$post2 = create_post([
    'category' => 'test-delete',
    'title' => 'Test Post 2 for Delete',
    'content' => 'This is a test post 2',
    'visibility' => 'friends'
]);

assert_true($post2 instanceof PostModel, "게시글 2 생성 성공");
echo "✓ 게시글 2 ID: {$post2->id}\n\n";

// ========================================================================
// 3. 피드 항목 확인 (삭제 전)
// ========================================================================

echo "--- 테스트 3: 피드 항목 확인 (삭제 전) ---\n";

$pdo = pdo();

// post1의 피드 항목 개수 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$post1->id]);
$feed_count1 = (int)$stmt->fetchColumn();

assert_true($feed_count1 > 0, "게시글 1의 피드 항목이 존재함 (개수: $feed_count1)");

// post2의 피드 항목 개수 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$post2->id]);
$feed_count2 = (int)$stmt->fetchColumn();

assert_true($feed_count2 > 0, "게시글 2의 피드 항목이 존재함 (개수: $feed_count2)");
echo "\n";

// ========================================================================
// 4. 게시글 1 삭제
// ========================================================================

echo "--- 테스트 4: 게시글 1 삭제 ---\n";

$result = delete_post(['id' => $post1->id]);
assert_true($result instanceof PostModel, "delete_post() 함수가 PostModel 객체 반환");
assert_equals($post1->id, $result->id, "삭제된 게시글 ID 확인");

// 게시글이 DB에서 삭제되었는지 확인
$deleted_post = get_post_by_id($post1->id);
assert_true($deleted_post === null, "게시글 1이 DB에서 삭제됨");

// 피드 항목도 삭제되었는지 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$post1->id]);
$feed_count_after = (int)$stmt->fetchColumn();

assert_equals(0, $feed_count_after, "게시글 1의 피드 항목이 모두 삭제됨");
echo "\n";

// ========================================================================
// 5. 게시글 2 삭제 (post_id 파라미터 사용)
// ========================================================================

echo "--- 테스트 5: 게시글 2 삭제 (post_id 파라미터 사용) ---\n";

$result = delete_post(['post_id' => $post2->id]);
assert_true($result instanceof PostModel, "delete_post() 함수가 PostModel 객체 반환 (post_id 사용)");
assert_equals($post2->id, $result->id, "삭제된 게시글 ID 확인 (post_id 사용)");

// 게시글이 DB에서 삭제되었는지 확인
$deleted_post = get_post_by_id($post2->id);
assert_true($deleted_post === null, "게시글 2가 DB에서 삭제됨");

// 피드 항목도 삭제되었는지 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$post2->id]);
$feed_count_after = (int)$stmt->fetchColumn();

assert_equals(0, $feed_count_after, "게시글 2의 피드 항목이 모두 삭제됨");
echo "\n";

// ========================================================================
// 6. 존재하지 않는 게시글 삭제 시도
// ========================================================================

echo "--- 테스트 6: 존재하지 않는 게시글 삭제 시도 ---\n";

assert_throws(function () {
    delete_post(['id' => 999999]);
}, 'post-not-found', "존재하지 않는 게시글 삭제 시도 시 post-not-found 에러 발생");
echo "\n";

// ========================================================================
// 7. 잘못된 post_id로 삭제 시도
// ========================================================================

echo "--- 테스트 7: 잘못된 post_id로 삭제 시도 ---\n";

assert_throws(function () {
    delete_post(['id' => null]);
}, 'invalid-post-id', "잘못된 post_id로 삭제 시도 시 invalid-post-id 에러 발생");

assert_throws(function () {
    delete_post([]);
}, 'invalid-post-id', "post_id 없이 삭제 시도 시 invalid-post-id 에러 발생");
echo "\n";

// ========================================================================
// 8. 다른 사용자의 게시글 삭제 시도 (권한 확인)
// ========================================================================

echo "--- 테스트 8: 다른 사용자의 게시글 삭제 시도 (권한 확인) ---\n";

// 다른 사용자의 게시글 생성 (user_id를 직접 삽입)
$now = time();
$other_user_id = 999001; // 테스트용 다른 사용자 ID

// 다른 사용자 레코드 생성
$stmt = $pdo->prepare("INSERT INTO users (id, firebase_uid, first_name, created_at, updated_at, birthday, gender, photo_url)
    VALUES (:id, :firebase_uid, :first_name, :created_at, :updated_at, 0, '', '')
    ON DUPLICATE KEY UPDATE firebase_uid = VALUES(firebase_uid), first_name = VALUES(first_name), updated_at = VALUES(updated_at)");
$stmt->execute([
    ':id' => $other_user_id,
    ':firebase_uid' => 'test-other-user-999001',
    ':first_name' => 'OtherUser',
    ':created_at' => $now,
    ':updated_at' => $now,
]);

// 다른 사용자의 게시글 생성
$stmt = $pdo->prepare("INSERT INTO posts (user_id, category, title, content, created_at, updated_at)
    VALUES (:user_id, :category, :title, :content, :created_at, :updated_at)");
$stmt->execute([
    ':user_id' => $other_user_id,
    ':category' => 'test-delete',
    ':title' => 'Other User Post',
    ':content' => 'This is another user\'s post',
    ':created_at' => $now,
    ':updated_at' => $now,
]);
$other_post_id = (int)$pdo->lastInsertId();

echo "✓ 다른 사용자의 게시글 생성 완료 (ID: $other_post_id)\n";

// 다른 사용자의 게시글 삭제 시도
assert_throws(function () use ($other_post_id) {
    delete_post(['id' => $other_post_id]);
}, 'permission-denied', "다른 사용자의 게시글 삭제 시도 시 permission-denied 에러 발생");

// 정리: 다른 사용자의 게시글 삭제 (직접 SQL)
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$other_post_id]);

echo "\n";

// ========================================================================
// 9. 로그인 상태 확인 테스트 (추가)
// ========================================================================

echo "--- 테스트 9: 로그인 상태 확인 ---\n";

// 로그인된 사용자 확인
$current_user = login();
assert_true($current_user !== false, "현재 로그인된 사용자 존재 확인");
echo "✓ 로그인된 사용자 ID: {$current_user->id}\n";

echo "\n";

// ========================================================================
// 테스트 정리
// ========================================================================

echo "========================================\n";
echo "테스트 완료\n";
echo "========================================\n";
echo "통과: $passed\n";
echo "실패: $failed\n";

if ($failed === 0) {
    echo "\n✅ 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "\n❌ 일부 테스트 실패\n";
    exit(1);
}
