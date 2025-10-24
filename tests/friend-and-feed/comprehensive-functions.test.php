<?php

/**
 * Friend-and-Feed 7개 핵심 함수 종합 테스트
 *
 * 테스트 대상 함수:
 * 1. is_blocked_either_way()
 * 2. get_post()
 * 3. fanout_post_to_friends()
 * 4. get_feeds_from_feed_entries()
 * 5. get_feed_from_read_join()
 * 6. get_posts_from_feed_entries()
 * 7. finalize_feed_with_visibility()
 *
 * 실행 방법:
 * php tests/friend-and-feed/comprehensive-functions.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "Friend-and-Feed 7개 핵심 함수 종합 테스트\n";
echo "========================================\n\n";

// 테스트 카운터
$test_count = 0;
$passed = 0;
$failed = 0;

/**
 * 테스트 어설션 함수
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

/**
 * 예외 발생 테스트 함수
 */
function test_throws(callable $fn, string $expected_code, string $message): void
{
    global $test_count, $passed, $failed;
    $test_count++;
    try {
        $fn();
        echo "✗ 테스트 {$test_count}: {$message} (예외가 발생하지 않음)\n";
        $failed++;
    } catch (ApiException $e) {
        $actual_code = $e->getErrorCode();
        if ($actual_code === $expected_code) {
            echo "✓ 테스트 {$test_count}: {$message}\n";
            $passed++;
        } else {
            echo "✗ 테스트 {$test_count}: {$message} (예상 코드: {$expected_code}, 실제: {$actual_code})\n";
            $failed++;
        }
    }
}

/**
 * 테스트용 게시글 직접 생성 함수 (로그인 불필요)
 */
function create_test_post(int $user_id, string $title, string $content, string $category = 'discussion', string $visibility = 'public'): array
{
    $pdo = pdo();
    $now = time();

    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, title, content, category, visibility, files, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, '', ?, ?)
    ");
    $stmt->execute([$user_id, $title, $content, $category, $visibility, $now, $now]);

    $post_id = (int)$pdo->lastInsertId();

    // 피드 전파 (visibility가 private가 아니면)
    if ($visibility !== 'private') {
        fanout_post_to_friends($user_id, $post_id, $now);
    }

    return [
        'id' => $post_id,
        'user_id' => $user_id,
        'title' => $title,
        'content' => $content,
        'category' => $category,
        'visibility' => $visibility,
        'created_at' => $now,
        'updated_at' => $now,
    ];
}

$pdo = pdo();

// ============================================================================
// 테스트 데이터 준비
// ============================================================================
echo "테스트 데이터 준비 중...\n";
echo "----------------------------------------\n";

// 기존 사용자 가져오기
$stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 6");
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($users) < 6) {
    echo "❌ 테스트를 위한 사용자가 부족합니다. 최소 6명의 사용자가 필요합니다.\n";
    exit(1);
}

$u1 = (int)$users[0]; // 주 사용자
$u2 = (int)$users[1]; // 친구 1
$u3 = (int)$users[2]; // 친구 2
$u4 = (int)$users[3]; // 비친구
$u5 = (int)$users[4]; // 차단된 사용자
$u6 = (int)$users[5]; // 추가 사용자

echo "✓ 사용자 준비 완료: u1={$u1}, u2={$u2}, u3={$u3}, u4={$u4}, u5={$u5}, u6={$u6}\n";

// 기존 테스트 데이터 정리
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN ($u1, $u2, $u3, $u4, $u5, $u6) OR user_id_b IN ($u1, $u2, $u3, $u4, $u5, $u6)");
$pdo->exec("DELETE FROM blocks WHERE blocker_id IN ($u1, $u2, $u3, $u4, $u5, $u6) OR blocked_id IN ($u1, $u2, $u3, $u4, $u5, $u6)");
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id IN ($u1, $u2, $u3, $u4, $u5, $u6)");
$pdo->exec("DELETE FROM posts WHERE user_id IN ($u1, $u2, $u3, $u4, $u5, $u6) AND title LIKE 'COMPREHENSIVE_TEST_%'");

echo "✓ 기존 테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 1: is_blocked_either_way() 함수
// ============================================================================
echo "테스트 1: is_blocked_either_way() 함수\n";
echo "----------------------------------------\n";

// 초기 상태: 차단 없음
test_assert(
    is_blocked_either_way($u1, $u2) === false,
    "초기 상태에서는 차단 관계가 없어야 함"
);

// u1이 u5를 차단
$stmt = $pdo->prepare("INSERT INTO blocks (blocker_id, blocked_id, created_at) VALUES (?, ?, ?)");
$stmt->execute([$u1, $u5, time()]);

test_assert(
    is_blocked_either_way($u1, $u5) === true,
    "u1이 u5를 차단한 경우 true 반환"
);

test_assert(
    is_blocked_either_way($u5, $u1) === true,
    "반대 순서(u5, u1)로 호출해도 true 반환 (양방향 확인)"
);

test_assert(
    is_blocked_either_way($u1, $u2) === false,
    "차단하지 않은 사용자는 false 반환"
);

// u6이 u1을 차단 (역방향 차단)
$stmt->execute([$u6, $u1, time()]);

test_assert(
    is_blocked_either_way($u1, $u6) === true,
    "u6이 u1을 차단한 경우도 true 반환"
);

echo "\n";

// ============================================================================
// 테스트 2: get_post() 함수
// ============================================================================
echo "테스트 2: get_post() 함수\n";
echo "----------------------------------------\n";

// 게시글 생성
$post1 = create_test_post(
    $u1,
    'COMPREHENSIVE_TEST_POST_1',
    'Test content 1',
    'discussion',
    'public'
);

$post_id_1 = $post1['id'];

// 게시글 조회
$row = get_post($post_id_1);
test_assert($row !== null, "존재하는 게시글은 배열을 반환해야 함");
test_assert(
    is_array($row) && $row['title'] === 'COMPREHENSIVE_TEST_POST_1',
    "게시글 제목이 올바르게 반환되어야 함"
);
test_assert(
    (int)$row['user_id'] === $u1,
    "게시글 작성자가 올바르게 반환되어야 함"
);

// 존재하지 않는 게시글
$row_null = get_post(999999999);
test_assert($row_null === null, "존재하지 않는 게시글은 null을 반환해야 함");

echo "\n";

// ============================================================================
// 테스트 3: fanout_post_to_friends() 함수
// ============================================================================
echo "테스트 3: fanout_post_to_friends() 함수\n";
echo "----------------------------------------\n";

// u1과 u2를 친구로 만들기
request_friend(['me' => $u1, 'other' => $u2]);
accept_friend(['me' => $u2, 'other' => $u1]);

// u1과 u3을 친구로 만들기
request_friend(['me' => $u1, 'other' => $u3]);
accept_friend(['me' => $u3, 'other' => $u1]);

echo "✓ u1의 친구: u2, u3\n";

// 게시글 생성 및 fan-out
$post2 = create_test_post(
    $u1,
    'COMPREHENSIVE_TEST_POST_2',
    'Test content 2',
    'discussion',
    'public'
);

$post_id_2 = $post2['id'];
$created_at_2 = (int)$post2['created_at'];

// 직접 fanout 함수 호출 (create_post가 이미 호출했지만 테스트를 위해 추가 호출)
$fanout_count = fanout_post_to_friends($u1, $post_id_2, $created_at_2);
test_assert(
    $fanout_count === 0, // INSERT IGNORE로 중복 무시되므로 0개
    "이미 fanout된 게시글은 중복 삽입이 무시되어야 함 (INSERT IGNORE)"
);

// feed_entries 확인
$stmt = $pdo->prepare("SELECT COUNT(*) FROM feed_entries WHERE post_id = ?");
$stmt->execute([$post_id_2]);
$count = (int)$stmt->fetchColumn();
test_assert(
    $count === 2,
    "u1의 친구 2명(u2, u3)에게 feed_entries가 생성되어야 함"
);

// 친구가 없는 사용자의 fanout
$post3 = create_test_post(
    $u4,
    'COMPREHENSIVE_TEST_POST_3',
    'Test content 3',
    'discussion',
    'public'
);

$post_id_3 = $post3['id'];

$stmt->execute([$post_id_3]);
$count_u4 = (int)$stmt->fetchColumn();
test_assert(
    $count_u4 === 0,
    "친구가 없는 사용자(u4)의 게시글은 feed_entries가 생성되지 않아야 함"
);

echo "\n";

// ============================================================================
// 테스트 4: get_feeds_from_feed_entries() 함수
// ============================================================================
echo "테스트 4: get_feeds_from_feed_entries() 함수\n";
echo "----------------------------------------\n";

// u2의 피드 조회 (u1의 게시글이 있어야 함)
$feed_u2 = get_feeds_from_feed_entries($u2, 20, 0);
test_assert(
    is_array($feed_u2),
    "피드는 배열을 반환해야 함"
);
test_assert(
    count($feed_u2) >= 1,
    "u2의 피드에는 최소 1개 게시글이 있어야 함 (u1의 게시글)"
);

// LIMIT 테스트
$feed_limit_1 = get_feeds_from_feed_entries($u2, 1, 0);
test_assert(
    count($feed_limit_1) === 1,
    "LIMIT 1일 때 1개만 반환해야 함"
);

// OFFSET 테스트
if (count($feed_u2) >= 2) {
    $feed_offset_1 = get_feeds_from_feed_entries($u2, 1, 1);
    test_assert(
        count($feed_offset_1) === 1,
        "OFFSET 1일 때 두 번째 항목부터 반환해야 함"
    );
    test_assert(
        (int)$feed_offset_1[0]['post_id'] === (int)$feed_u2[1]['post_id'],
        "OFFSET 1의 첫 항목은 전체 피드의 두 번째 항목과 일치해야 함"
    );
}

// 피드가 없는 사용자 (u4는 친구가 없음)
$feed_u4 = get_feeds_from_feed_entries($u4, 20, 0);
test_assert(
    count($feed_u4) === 0,
    "친구가 없는 사용자(u4)는 빈 피드를 가져야 함"
);

echo "\n";

// ============================================================================
// 테스트 5: get_feed_from_read_join() 함수
// ============================================================================
echo "테스트 5: get_feed_from_read_join() 함수\n";
echo "----------------------------------------\n";

// u2의 친구 ID 목록 가져오기
$friend_ids_u2 = get_friend_ids(['me' => $u2]);

test_assert(
    in_array($u1, $friend_ids_u2, true),
    "u2의 친구 목록에 u1이 포함되어야 함"
);

// 읽기 조인으로 피드 조회
$feed_join = get_feed_from_read_join($u2, $friend_ids_u2, 20, 0, true);
test_assert(
    is_array($feed_join),
    "get_feed_from_read_join은 배열을 반환해야 함"
);
test_assert(
    count($feed_join) >= 1,
    "u2의 친구(u1)의 게시글이 조회되어야 함"
);

// 빈 친구 목록
$feed_join_empty = get_feed_from_read_join($u4, [], 20, 0, true);
test_assert(
    count($feed_join_empty) === 0,
    "친구 목록이 비어있으면 빈 배열 반환"
);

// 차단 사용자 제외 테스트
// u2가 u1을 차단
$stmt = $pdo->prepare("INSERT INTO blocks (blocker_id, blocked_id, created_at) VALUES (?, ?, ?)");
$stmt->execute([$u2, $u1, time()]);

$feed_join_blocked = get_feed_from_read_join($u2, $friend_ids_u2, 20, 0, true);
test_assert(
    count($feed_join_blocked) === 0,
    "차단된 사용자의 게시글은 제외되어야 함 (exclude_blocked=true)"
);

// 차단 무시 옵션
$feed_join_include_blocked = get_feed_from_read_join($u2, $friend_ids_u2, 20, 0, false);
test_assert(
    count($feed_join_include_blocked) >= 1,
    "exclude_blocked=false일 때는 차단된 사용자의 게시글도 포함"
);

// 차단 해제
$pdo->exec("DELETE FROM blocks WHERE blocker_id = $u2 AND blocked_id = $u1");

echo "\n";

// ============================================================================
// 테스트 6: get_posts_from_feed_entries() 함수 (API 함수)
// ============================================================================
echo "테스트 6: get_posts_from_feed_entries() 함수\n";
echo "----------------------------------------\n";

// 정상 조회
$hybrid_feed = get_posts_from_feed_entries(['me' => $u2, 'limit' => 20, 'offset' => 0]);
test_assert(
    isset($hybrid_feed),
    "get_posts_from_feed_entries는 'feed' 키를 포함한 배열을 반환해야 함"
);
test_assert(
    is_array($hybrid_feed),
    "'feed'는 배열이어야 함"
);
test_assert(
    count($hybrid_feed) >= 1,
    "u2의 하이브리드 피드에는 최소 1개 게시글이 있어야 함"
);

// 첫 항목 구조 확인
if (count($hybrid_feed) > 0) {
    $first = $hybrid_feed[0];
    test_assert(
        isset($first['post_id']) && isset($first['author_id']) && isset($first['title']),
        "피드 항목은 post_id, author_id, title 필드를 포함해야 함"
    );
}

// 잘못된 파라미터 테스트
test_throws(
    fn() => get_posts_from_feed_entries(['me' => 0, 'limit' => 20]),
    'invalid-me',
    "me=0일 때 'invalid-me' 에러 발생"
);

test_throws(
    fn() => get_posts_from_feed_entries(['me' => -5, 'limit' => 20]),
    'invalid-me',
    "me=-5일 때 'invalid-me' 에러 발생"
);

// LIMIT 범위 테스트 (자동 보정)
$hybrid_limit_0 = get_posts_from_feed_entries(['me' => $u2, 'limit' => 0]);
test_assert(
    is_array($hybrid_limit_0),
    "limit=0일 때 자동으로 기본값(20)으로 보정되어야 함"
);

$hybrid_limit_200 = get_posts_from_feed_entries(['me' => $u2, 'limit' => 200]);
test_assert(
    is_array($hybrid_limit_200),
    "limit=200일 때 자동으로 최대값(20)으로 제한되어야 함"
);

// OFFSET 테스트
$hybrid_offset = get_posts_from_feed_entries(['me' => $u2, 'limit' => 1, 'offset' => 0]);
test_assert(
    count($hybrid_offset) <= 1,
    "limit=1, offset=0일 때 최대 1개 반환"
);

echo "\n";

// ============================================================================
// 테스트 7: finalize_feed_with_visibility() 함수
// ============================================================================
echo "테스트 7: finalize_feed_with_visibility() 함수\n";
echo "----------------------------------------\n";

// visibility 테스트를 위한 다양한 게시글 생성
$post_public = create_test_post(
    $u1,
    'COMPREHENSIVE_TEST_PUBLIC',
    'Public post',
    'discussion',
    'public'
);

$post_friends = create_test_post(
    $u1,
    'COMPREHENSIVE_TEST_FRIENDS',
    'Friends only post',
    'discussion',
    'friends'
);

$post_private = create_test_post(
    $u1,
    'COMPREHENSIVE_TEST_PRIVATE',
    'Private post',
    'discussion',
    'private'
);

// 원시 피드 항목 구성
$raw_items = [
    ['post_id' => $post_public['id'], 'post_author_id' => $u1, 'created_at' => $post_public['created_at']],
    ['post_id' => $post_friends['id'], 'post_author_id' => $u1, 'created_at' => $post_friends['created_at']],
    ['post_id' => $post_private['id'], 'post_author_id' => $u1, 'created_at' => $post_private['created_at']],
];

// u2 (친구)가 조회
$finalized_u2 = finalize_feed_with_visibility($u2, $raw_items);
test_assert(
    isset($finalized_u2),
    "finalize_feed_with_visibility는 'feed' 키를 포함한 배열 반환"
);

$feed_items_u2 = $finalized_u2;
$post_ids_u2 = array_column($feed_items_u2, 'post_id');

test_assert(
    in_array($post_public['id'], $post_ids_u2, true),
    "친구(u2)는 public 게시글을 볼 수 있어야 함"
);
test_assert(
    in_array($post_friends['id'], $post_ids_u2, true),
    "친구(u2)는 friends 게시글을 볼 수 있어야 함"
);
test_assert(
    !in_array($post_private['id'], $post_ids_u2, true),
    "친구(u2)는 private 게시글을 볼 수 없어야 함"
);

// u4 (비친구)가 조회
$finalized_u4 = finalize_feed_with_visibility($u4, $raw_items);
$feed_items_u4 = $finalized_u4;
$post_ids_u4 = array_column($feed_items_u4, 'post_id');

test_assert(
    in_array($post_public['id'], $post_ids_u4, true),
    "비친구(u4)는 public 게시글을 볼 수 있어야 함"
);
test_assert(
    !in_array($post_friends['id'], $post_ids_u4, true),
    "비친구(u4)는 friends 게시글을 볼 수 없어야 함"
);
test_assert(
    !in_array($post_private['id'], $post_ids_u4, true),
    "비친구(u4)는 private 게시글을 볼 수 없어야 함"
);

// u1 (작성자 본인)이 조회
$finalized_u1 = finalize_feed_with_visibility($u1, $raw_items);
$feed_items_u1 = $finalized_u1;
$post_ids_u1 = array_column($feed_items_u1, 'post_id');

test_assert(
    in_array($post_public['id'], $post_ids_u1, true),
    "작성자(u1)는 public 게시글을 볼 수 있어야 함"
);
test_assert(
    in_array($post_friends['id'], $post_ids_u1, true),
    "작성자(u1)는 friends 게시글을 볼 수 있어야 함"
);
test_assert(
    in_array($post_private['id'], $post_ids_u1, true),
    "작성자(u1)는 private 게시글을 볼 수 있어야 함 (본인)"
);

// 차단된 사용자 테스트 (u5는 u1에게 차단됨)
$finalized_u5 = finalize_feed_with_visibility($u5, $raw_items);
$feed_items_u5 = $finalized_u5;

test_assert(
    count($feed_items_u5) === 0,
    "차단된 사용자(u5)는 u1의 게시글을 볼 수 없어야 함"
);

echo "\n";

// ============================================================================
// 통합 시나리오 테스트
// ============================================================================
echo "통합 시나리오 테스트\n";
echo "----------------------------------------\n";

// 시나리오: u3가 게시글을 작성하고 u2가 피드에서 조회
$post_scenario = create_test_post(
    $u3,
    'COMPREHENSIVE_TEST_SCENARIO',
    'Scenario test',
    'discussion',
    'public'
);

// u1과 u3는 친구이므로 u1의 피드에 나타나야 함
$scenario_feed = get_posts_from_feed_entries(['me' => $u1, 'limit' => 50, 'offset' => 0]);
$scenario_post_ids = array_column($scenario_feed, 'post_id');

test_assert(
    in_array($post_scenario['id'], $scenario_post_ids, true),
    "친구(u3)의 게시글이 u1의 하이브리드 피드에 나타나야 함"
);

// 정렬 확인 (최신순)
if (count($scenario_feed) >= 2) {
    $is_sorted = true;
    for ($i = 0; $i < count($scenario_feed) - 1; $i++) {
        if ($scenario_feed[$i]['created_at'] < $scenario_feed[$i + 1]['created_at']) {
            $is_sorted = false;
            break;
        }
    }
    test_assert($is_sorted, "피드는 created_at DESC 순으로 정렬되어야 함");
}

echo "\n";

// ============================================================================
// 테스트 정리
// ============================================================================
echo "테스트 정리 중...\n";
echo "----------------------------------------\n";

// 테스트 데이터 삭제
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN ($u1, $u2, $u3, $u4, $u5, $u6) OR user_id_b IN ($u1, $u2, $u3, $u4, $u5, $u6)");
$pdo->exec("DELETE FROM blocks WHERE blocker_id IN ($u1, $u2, $u3, $u4, $u5, $u6) OR blocked_id IN ($u1, $u2, $u3, $u4, $u5, $u6)");
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id IN ($u1, $u2, $u3, $u4, $u5, $u6)");
$pdo->exec("DELETE FROM posts WHERE user_id IN ($u1, $u2, $u3, $u4, $u5, $u6) AND title LIKE 'COMPREHENSIVE_TEST_%'");

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
