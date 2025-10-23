<?php

/**
 * delete_post_from_feed_entries 함수 테스트
 *
 * 이 파일은 delete_post_from_feed_entries() 함수를 테스트합니다.
 * 테스트 내용:
 * - feed_entries 테이블에서 특정 게시글 삭제
 * - 삭제된 행 수 확인
 * - 존재하지 않는 게시글 삭제 시 0 반환 확인
 *
 * 실행 방법:
 * php tests/friend-and-feed/delete-post-from-feed-entries.test.php
 */

declare(strict_types=1);

// init.php 로드
include __DIR__ . '/../../init.php';

echo "========================================\n";
echo "delete_post_from_feed_entries 함수 테스트 시작\n";
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
    assert_true($expected === $actual, "$message (예상: $expected, 실제: $actual)");
}

// ========================================================================
// 1. 테스트 데이터 준비
// ========================================================================

echo "--- 테스트 1: 테스트 데이터 준비 ---\n";

$pdo = pdo();
$now = time();

// 테스트용 사용자 3명 생성
$testUser1 = 999001;
$testUser2 = 999002;
$testUser3 = 999003;

foreach ([$testUser1, $testUser2, $testUser3] as $userId) {
    $firebaseUid = "test-user-$userId";
    $firstName = "TestUser{$userId}";

    $stmt = $pdo->prepare("INSERT INTO users (id, firebase_uid, first_name, created_at, updated_at, birthday, gender, photo_url)
        VALUES (:id, :firebase_uid, :first_name, :created_at, :updated_at, 0, '', '')
        ON DUPLICATE KEY UPDATE firebase_uid = VALUES(firebase_uid), first_name = VALUES(first_name), updated_at = VALUES(updated_at)");
    $stmt->execute([
        ':id' => $userId,
        ':firebase_uid' => $firebaseUid,
        ':first_name' => $firstName,
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);
}

echo "✓ 테스트 사용자 3명 생성 완료 (ID: $testUser1, $testUser2, $testUser3)\n";

// 테스트용 게시글 생성
$testPostId = 999001;
$stmt = $pdo->prepare("INSERT INTO posts (id, user_id, category, title, content, created_at, updated_at)
    VALUES (:id, :user_id, :category, :title, :content, :created_at, :updated_at)
    ON DUPLICATE KEY UPDATE category = VALUES(category), title = VALUES(title), content = VALUES(content), updated_at = VALUES(updated_at)");
$stmt->execute([
    ':id' => $testPostId,
    ':user_id' => $testUser1,
    ':category' => 'test-delete-feed',
    ':title' => 'Test Post for Delete Feed Entries',
    ':content' => 'This is a test post',
    ':created_at' => $now,
    ':updated_at' => $now,
]);

echo "✓ 테스트 게시글 생성 완료 (ID: $testPostId)\n";

// 테스트용 피드 항목 3개 생성 (3명의 사용자에게 전파)
$stmt = $pdo->prepare("INSERT INTO feed_entries (receiver_id, post_id, post_author_id, created_at)
    VALUES (:receiver_id, :post_id, :post_author_id, :created_at)
    ON DUPLICATE KEY UPDATE created_at = VALUES(created_at)");

foreach ([$testUser1, $testUser2, $testUser3] as $userId) {
    $stmt->execute([
        ':receiver_id' => $userId,
        ':post_id' => $testPostId,
        ':post_author_id' => $testUser1,
        ':created_at' => $now,
    ]);
}

echo "✓ 테스트 피드 항목 3개 생성 완료 (receiver_id: $testUser1, $testUser2, $testUser3, post_id: $testPostId)\n\n";

// ========================================================================
// 2. feed_entries 테이블에 피드 항목이 정상적으로 추가되었는지 확인
// ========================================================================

echo "--- 테스트 2: 피드 항목 존재 확인 ---\n";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$testPostId]);
$count = (int)$stmt->fetchColumn();

assert_equals(3, $count, "피드 항목 3개가 정상적으로 추가됨");
echo "\n";

// ========================================================================
// 3. delete_post_from_feed_entries 함수 호출하여 피드 항목 삭제
// ========================================================================

echo "--- 테스트 3: delete_post_from_feed_entries 함수 호출 ---\n";

$deletedCount = delete_post_from_feed_entries($testPostId);

assert_equals(3, $deletedCount, "delete_post_from_feed_entries() 함수가 3개의 피드 항목을 삭제함");
echo "\n";

// ========================================================================
// 4. feed_entries 테이블에서 피드 항목이 모두 삭제되었는지 확인
// ========================================================================

echo "--- 테스트 4: 피드 항목 삭제 확인 ---\n";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$testPostId]);
$count = (int)$stmt->fetchColumn();

assert_equals(0, $count, "피드 항목이 모두 삭제됨");
echo "\n";

// ========================================================================
// 5. 존재하지 않는 게시글 삭제 시 0 반환 확인
// ========================================================================

echo "--- 테스트 5: 존재하지 않는 게시글 삭제 시 0 반환 확인 ---\n";

$nonExistentPostId = 999999;
$deletedCount = delete_post_from_feed_entries($nonExistentPostId);

assert_equals(0, $deletedCount, "존재하지 않는 게시글 삭제 시 0 반환");
echo "\n";

// ========================================================================
// 6. 이미 삭제된 게시글을 다시 삭제 시도
// ========================================================================

echo "--- 테스트 6: 이미 삭제된 게시글을 다시 삭제 시도 ---\n";

$deletedCount = delete_post_from_feed_entries($testPostId);

assert_equals(0, $deletedCount, "이미 삭제된 게시글을 다시 삭제 시도 시 0 반환");
echo "\n";

// ========================================================================
// 7. 다중 사용자 피드 항목 삭제 테스트 (복잡한 시나리오)
// ========================================================================

echo "--- 테스트 7: 다중 사용자 피드 항목 삭제 테스트 ---\n";

// 테스트용 게시글 2개 생성
$testPostId2 = 999002;
$testPostId3 = 999003;

foreach ([$testPostId2, $testPostId3] as $postId) {
    $stmt = $pdo->prepare("INSERT INTO posts (id, user_id, category, title, content, created_at, updated_at)
        VALUES (:id, :user_id, :category, :title, :content, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE category = VALUES(category), title = VALUES(title), content = VALUES(content), updated_at = VALUES(updated_at)");
    $stmt->execute([
        ':id' => $postId,
        ':user_id' => $testUser1,
        ':category' => 'test-delete-feed',
        ':title' => "Test Post $postId",
        ':content' => "This is test post $postId",
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);
}

echo "✓ 테스트 게시글 2개 추가 생성 완료 (ID: $testPostId2, $testPostId3)\n";

// 테스트용 피드 항목 생성
// - testPostId2: testUser1, testUser2에게 전파 (2개)
// - testPostId3: testUser1, testUser2, testUser3에게 전파 (3개)
$stmt = $pdo->prepare("INSERT INTO feed_entries (receiver_id, post_id, post_author_id, created_at)
    VALUES (:receiver_id, :post_id, :post_author_id, :created_at)
    ON DUPLICATE KEY UPDATE created_at = VALUES(created_at)");

foreach ([$testUser1, $testUser2] as $userId) {
    $stmt->execute([
        ':receiver_id' => $userId,
        ':post_id' => $testPostId2,
        ':post_author_id' => $testUser1,
        ':created_at' => $now,
    ]);
}

foreach ([$testUser1, $testUser2, $testUser3] as $userId) {
    $stmt->execute([
        ':receiver_id' => $userId,
        ':post_id' => $testPostId3,
        ':post_author_id' => $testUser1,
        ':created_at' => $now,
    ]);
}

echo "✓ 테스트 피드 항목 생성 완료 (post_id $testPostId2: 2개, post_id $testPostId3: 3개)\n";

// testPostId2 삭제
$deletedCount = delete_post_from_feed_entries($testPostId2);
assert_equals(2, $deletedCount, "게시글 {$testPostId2}의 피드 항목 2개 삭제");

// testPostId3 삭제
$deletedCount = delete_post_from_feed_entries($testPostId3);
assert_equals(3, $deletedCount, "게시글 {$testPostId3}의 피드 항목 3개 삭제");

// 삭제 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id IN (?, ?)");
$stmt->execute([$testPostId2, $testPostId3]);
$count = (int)$stmt->fetchColumn();

assert_equals(0, $count, "모든 피드 항목이 정상적으로 삭제됨");
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
