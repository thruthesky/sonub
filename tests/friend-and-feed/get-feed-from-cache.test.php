<?php
/**
 * get_feed_from_cache 함수 테스트
 *
 * 실행 방법:
 * php tests/friend-and-feed/get-feed-from-cache.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "get_feed_from_cache() 함수 테스트 시작\n";
echo "========================================\n\n";

// 테스트 카운터
$test_count = 0;
$passed = 0;
$failed = 0;

/**
 * 테스트 함수
 */
function test_assert(bool $condition, string $message): void
{
    global $test_count, $passed, $failed;
    $test_count++;
    if ($condition) {
        echo "✓ 테스트 {$test_count}: {$message}\n";
        $passed++;
    } else {
        echo "✗ 테스트 {$test_count}: {$message}\n";
        $failed++;
    }
}

$pdo = pdo();

// ============================================================================
// 테스트 데이터 준비
// ============================================================================
echo "테스트 데이터 준비 중...\n";
echo "----------------------------------------\n";

// 기존 사용자 가져오기
$stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 5");
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($users) < 5) {
    echo "❌ 테스트를 위한 사용자가 부족합니다. 최소 5명의 사용자가 필요합니다.\n";
    exit(1);
}

$u1 = (int)$users[0]; // 피드를 받을 사용자
$u2 = (int)$users[1]; // 친구 1
$u3 = (int)$users[2]; // 친구 2
$u4 = (int)$users[3]; // 친구 3
$u5 = (int)$users[4]; // 비친구

echo "✓ 사용자 준비 완료: u1={$u1}, u2={$u2}, u3={$u3}, u4={$u4}, u5={$u5}\n";

// 기존 테스트 데이터 정리
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id IN ($u1, $u2, $u3, $u4, $u5)");
$pdo->exec("DELETE FROM posts WHERE user_id IN ($u1, $u2, $u3, $u4, $u5) AND title LIKE 'TEST_%'");

echo "✓ 기존 테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 1: 빈 피드 조회
// ============================================================================
echo "테스트 1: 빈 피드 조회\n";
echo "----------------------------------------\n";

$feed = get_feed_from_cache($u1, 20, 0);
test_assert(is_array($feed), "빈 피드는 배열을 반환해야 함");
test_assert(count($feed) === 0, "피드 항목이 없어야 함");

echo "\n";

// ============================================================================
// 테스트 2: 피드 항목 생성 및 조회
// ============================================================================
echo "테스트 2: 피드 항목 생성 및 조회\n";
echo "----------------------------------------\n";

// 게시글 생성 (직접 DB INSERT)
$now = time();

// post1
$stmt_insert = $pdo->prepare("INSERT INTO posts (user_id, title, content, category, visibility, files, created_at, updated_at) VALUES (?, ?, ?, ?, 'public', '', ?, ?)");
$stmt_insert->execute([$u2, 'TEST_POST_1', 'Test content 1', 'discussion', $now, $now]);
$post1 = ['id' => (int)$pdo->lastInsertId()];

// post2
$stmt_insert->execute([$u3, 'TEST_POST_2', 'Test content 2', 'discussion', $now, $now]);
$post2 = ['id' => (int)$pdo->lastInsertId()];

// post3
$stmt_insert->execute([$u4, 'TEST_POST_3', 'Test content 3', 'discussion', $now, $now]);
$post3 = ['id' => (int)$pdo->lastInsertId()];

echo "✓ 게시글 3개 생성 완료: post1={$post1['id']}, post2={$post2['id']}, post3={$post3['id']}\n";

// feed_entries에 직접 삽입 (캐시 시뮬레이션)
$stmt = $pdo->prepare("
    INSERT INTO feed_entries (receiver_id, post_id, post_author_id, created_at)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([$u1, $post1['id'], $u2, $now - 300]); // 5분 전
$stmt->execute([$u1, $post2['id'], $u3, $now - 200]); // 3분 20초 전
$stmt->execute([$u1, $post3['id'], $u4, $now - 100]); // 1분 40초 전

echo "✓ feed_entries에 피드 항목 3개 삽입 완료\n";

// 피드 조회
$feed = get_feed_from_cache($u1, 20, 0);
test_assert(is_array($feed), "피드는 배열을 반환해야 함");
test_assert(count($feed) === 3, "피드 항목이 3개여야 함 (실제: " . count($feed) . "개)");

// 최신순 정렬 확인 (created_at DESC)
if (count($feed) === 3) {
    test_assert(
        (int)$feed[0]['post_id'] === $post3['id'],
        "첫 번째 항목은 post3(가장 최신)이어야 함"
    );
    test_assert(
        (int)$feed[1]['post_id'] === $post2['id'],
        "두 번째 항목은 post2여야 함"
    );
    test_assert(
        (int)$feed[2]['post_id'] === $post1['id'],
        "세 번째 항목은 post1(가장 오래됨)이어야 함"
    );
}

// 반환 데이터 구조 확인
if (count($feed) > 0) {
    $first_item = $feed[0];
    test_assert(isset($first_item['post_id']), "post_id 필드가 존재해야 함");
    test_assert(isset($first_item['post_author_id']), "post_author_id 필드가 존재해야 함");
    test_assert(isset($first_item['created_at']), "created_at 필드가 존재해야 함");
    test_assert(
        (int)$first_item['post_author_id'] === $u4,
        "post_author_id가 올바른 작성자 ID여야 함"
    );
}

echo "\n";

// ============================================================================
// 테스트 3: LIMIT 파라미터 확인
// ============================================================================
echo "테스트 3: LIMIT 파라미터 확인\n";
echo "----------------------------------------\n";

// LIMIT 1
$feed_limit_1 = get_feed_from_cache($u1, 1, 0);
test_assert(count($feed_limit_1) === 1, "LIMIT 1일 때 1개 항목만 반환해야 함");
test_assert(
    (int)$feed_limit_1[0]['post_id'] === $post3['id'],
    "LIMIT 1일 때 가장 최신 항목을 반환해야 함"
);

// LIMIT 2
$feed_limit_2 = get_feed_from_cache($u1, 2, 0);
test_assert(count($feed_limit_2) === 2, "LIMIT 2일 때 2개 항목만 반환해야 함");

echo "\n";

// ============================================================================
// 테스트 4: OFFSET 파라미터 확인
// ============================================================================
echo "테스트 4: OFFSET 파라미터 확인\n";
echo "----------------------------------------\n";

// OFFSET 0 (기본값)
$feed_offset_0 = get_feed_from_cache($u1, 2, 0);
test_assert(count($feed_offset_0) === 2, "OFFSET 0일 때 첫 2개 항목 반환");
test_assert(
    (int)$feed_offset_0[0]['post_id'] === $post3['id'],
    "첫 번째 항목은 post3이어야 함"
);

// OFFSET 1
$feed_offset_1 = get_feed_from_cache($u1, 2, 1);
test_assert(count($feed_offset_1) === 2, "OFFSET 1일 때 2개 항목 반환");
test_assert(
    (int)$feed_offset_1[0]['post_id'] === $post2['id'],
    "OFFSET 1일 때 첫 번째 항목은 post2여야 함"
);
test_assert(
    (int)$feed_offset_1[1]['post_id'] === $post1['id'],
    "OFFSET 1일 때 두 번째 항목은 post1이어야 함"
);

// OFFSET 2
$feed_offset_2 = get_feed_from_cache($u1, 2, 2);
test_assert(count($feed_offset_2) === 1, "OFFSET 2일 때 1개 항목만 반환");
test_assert(
    (int)$feed_offset_2[0]['post_id'] === $post1['id'],
    "OFFSET 2일 때 항목은 post1이어야 함"
);

// OFFSET 3 (범위 초과)
$feed_offset_3 = get_feed_from_cache($u1, 2, 3);
test_assert(count($feed_offset_3) === 0, "OFFSET 3일 때 빈 배열 반환");

echo "\n";

// ============================================================================
// 테스트 5: 다른 사용자의 피드는 조회되지 않아야 함
// ============================================================================
echo "테스트 5: 사용자 격리 확인\n";
echo "----------------------------------------\n";

// u1의 피드는 3개
$feed_u1 = get_feed_from_cache($u1, 20, 0);
test_assert(count($feed_u1) === 3, "u1의 피드는 3개여야 함");

// u2의 피드는 0개 (아직 feed_entries에 없음)
$feed_u2 = get_feed_from_cache($u2, 20, 0);
test_assert(count($feed_u2) === 0, "u2의 피드는 0개여야 함 (u1의 피드와 격리)");

// u5의 피드도 0개
$feed_u5 = get_feed_from_cache($u5, 20, 0);
test_assert(count($feed_u5) === 0, "u5의 피드는 0개여야 함");

echo "\n";

// ============================================================================
// 테스트 6: 대량 피드 조회 (페이징 시나리오)
// ============================================================================
echo "테스트 6: 대량 피드 조회 (페이징)\n";
echo "----------------------------------------\n";

// 추가 피드 항목 생성 (총 10개)
for ($i = 4; $i <= 10; $i++) {
    // 게시글 직접 INSERT
    $stmt_insert->execute([$u2, "TEST_POST_{$i}", "Test content {$i}", 'discussion', $now, $now]);
    $post_id = (int)$pdo->lastInsertId();

    // feed_entries에 추가
    $stmt->execute([$u1, $post_id, $u2, $now - (1000 - $i * 10)]);
}

echo "✓ 추가 피드 항목 7개 생성 완료 (총 10개)\n";

// 전체 조회
$feed_all = get_feed_from_cache($u1, 100, 0);
test_assert(count($feed_all) === 10, "총 10개 피드 항목이 조회되어야 함 (실제: " . count($feed_all) . "개)");

// 페이지 1 (0-4)
$page1 = get_feed_from_cache($u1, 5, 0);
test_assert(count($page1) === 5, "페이지 1은 5개 항목 반환");

// 페이지 2 (5-9)
$page2 = get_feed_from_cache($u1, 5, 5);
test_assert(count($page2) === 5, "페이지 2는 5개 항목 반환");

// 페이지 3 (10-14, 범위 초과)
$page3 = get_feed_from_cache($u1, 5, 10);
test_assert(count($page3) === 0, "페이지 3은 빈 배열 반환");

echo "\n";

// ============================================================================
// 테스트 7: created_at 정렬 확인
// ============================================================================
echo "테스트 7: created_at 정렬 확인 (DESC)\n";
echo "----------------------------------------\n";

$feed_sorted = get_feed_from_cache($u1, 10, 0);
$is_sorted = true;
for ($i = 0; $i < count($feed_sorted) - 1; $i++) {
    if ((int)$feed_sorted[$i]['created_at'] < (int)$feed_sorted[$i + 1]['created_at']) {
        $is_sorted = false;
        break;
    }
}

test_assert($is_sorted, "피드 항목이 created_at DESC 순으로 정렬되어야 함");

echo "\n";

// ============================================================================
// 테스트 정리
// ============================================================================
echo "테스트 정리 중...\n";
echo "----------------------------------------\n";

// 테스트 데이터 삭제
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id IN ($u1, $u2, $u3, $u4, $u5)");
$pdo->exec("DELETE FROM posts WHERE user_id IN ($u1, $u2, $u3, $u4, $u5) AND title LIKE 'TEST_%'");

echo "✓ 테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 결과 요약
// ============================================================================
echo "========================================\n";
echo "테스트 결과 요약\n";
echo "========================================\n";
echo "총 테스트: {$test_count}개\n";
echo "통과: {$passed}개\n";
echo "실패: {$failed}개\n";
echo "========================================\n";

if ($failed === 0) {
    echo "✅ 모든 테스트 통과!\n";
    exit(0);
} else {
    echo "❌ {$failed}개 테스트 실패\n";
    exit(1);
}
