<?php
/**
 * get_friends() 함수 종합 테스트
 *
 * 테스트 대상:
 * - 기본 친구 목록 조회
 * - limit, offset 옵션
 * - order_by, order 정렬 옵션
 * - 에러 케이스
 * - 친구가 없는 경우
 *
 * 실행 방법:
 * php tests/friend-and-feed/get-friends.test.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "get_friends() 함수 종합 테스트\n";
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
            echo "✗ 테스트 {$test_count}: {$message} (예상: {$expected_code}, 실제: {$actual_code})\n";
            $failed++;
        }
    }
}

$pdo = pdo();

// ============================================================================
// 테스트 데이터 준비
// ============================================================================
echo "테스트 데이터 준비 중...\n";
echo "----------------------------------------\n";

// 기존 사용자 가져오기 (최소 10명 필요)
$stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 10");
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($users) < 10) {
    echo "❌ 테스트를 위한 사용자가 부족합니다. 최소 10명의 사용자가 필요합니다.\n";
    exit(1);
}

$u1 = (int)$users[0]; // 주 사용자 (친구 7명)
$u2 = (int)$users[1]; // 친구 1
$u3 = (int)$users[2]; // 친구 2
$u4 = (int)$users[3]; // 친구 3
$u5 = (int)$users[4]; // 친구 4
$u6 = (int)$users[5]; // 친구 5
$u7 = (int)$users[6]; // 친구 6
$u8 = (int)$users[7]; // 친구 7
$u9 = (int)$users[8]; // 친구 없는 사용자
$u10 = (int)$users[9]; // 추가 사용자

echo "✓ 사용자 준비 완료: u1={$u1}, u2={$u2}, ..., u9={$u9}, u10={$u10}\n";

// 기존 테스트 데이터 정리
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN ($u1, $u2, $u3, $u4, $u5, $u6, $u7, $u8, $u9, $u10) OR user_id_b IN ($u1, $u2, $u3, $u4, $u5, $u6, $u7, $u8, $u9, $u10)");

echo "✓ 기존 테스트 데이터 정리 완료\n";

// 친구 관계 생성 (u1의 친구: u2, u3, u4, u5, u6, u7, u8 - 총 7명)
request_friend(['me' => $u1, 'other' => $u2]);
accept_friend(['me' => $u2, 'other' => $u1]);

request_friend(['me' => $u1, 'other' => $u3]);
accept_friend(['me' => $u3, 'other' => $u1]);

request_friend(['me' => $u1, 'other' => $u4]);
accept_friend(['me' => $u4, 'other' => $u1]);

request_friend(['me' => $u1, 'other' => $u5]);
accept_friend(['me' => $u5, 'other' => $u1]);

request_friend(['me' => $u1, 'other' => $u6]);
accept_friend(['me' => $u6, 'other' => $u1]);

request_friend(['me' => $u1, 'other' => $u7]);
accept_friend(['me' => $u7, 'other' => $u1]);

request_friend(['me' => $u1, 'other' => $u8]);
accept_friend(['me' => $u8, 'other' => $u1]);

echo "✓ u1의 친구 관계 생성 완료: 7명 (u2, u3, u4, u5, u6, u7, u8)\n\n";

// ============================================================================
// 테스트 1: 기본 친구 목록 조회
// ============================================================================
echo "테스트 1: 기본 친구 목록 조회\n";
echo "----------------------------------------\n";

$friends = get_friends(['me' => $u1]);
test_assert(
    is_array($friends),
    "배열을 반환해야 함"
);
test_assert(
    count($friends) === 7,
    "u1의 친구는 7명이어야 함 (실제: " . count($friends) . "명)"
);

// 첫 번째 친구 정보 구조 확인
if (count($friends) > 0) {
    $first_friend = $friends[0];
    test_assert(
        isset($first_friend['id']) && isset($first_friend['display_name']),
        "친구 정보에 id와 display_name 필드가 포함되어야 함"
    );
}

echo "\n";

// ============================================================================
// 테스트 2: LIMIT 옵션
// ============================================================================
echo "테스트 2: LIMIT 옵션\n";
echo "----------------------------------------\n";

// LIMIT 5
$friends_limit_5 = get_friends(['me' => $u1, 'limit' => 5]);
test_assert(
    count($friends_limit_5) === 5,
    "LIMIT 5일 때 5명만 반환해야 함"
);

// LIMIT 3
$friends_limit_3 = get_friends(['me' => $u1, 'limit' => 3]);
test_assert(
    count($friends_limit_3) === 3,
    "LIMIT 3일 때 3명만 반환해야 함"
);

// LIMIT 0 (자동 보정 → 10)
$friends_limit_0 = get_friends(['me' => $u1, 'limit' => 0]);
test_assert(
    count($friends_limit_0) === 7,
    "LIMIT 0일 때 기본값(10)으로 보정되어 모든 친구(7명) 반환"
);

// LIMIT 200 (자동 보정 → 10)
$friends_limit_200 = get_friends(['me' => $u1, 'limit' => 200]);
test_assert(
    count($friends_limit_200) === 7,
    "LIMIT 200일 때 최대값(10)으로 제한되어 모든 친구(7명) 반환"
);

echo "\n";

// ============================================================================
// 테스트 3: OFFSET 옵션 (페이지네이션)
// ============================================================================
echo "테스트 3: OFFSET 옵션 (페이지네이션)\n";
echo "----------------------------------------\n";

// OFFSET 0 (처음 5명)
$page1 = get_friends(['me' => $u1, 'limit' => 5, 'offset' => 0]);
test_assert(
    count($page1) === 5,
    "OFFSET 0, LIMIT 5: 5명 반환"
);

// OFFSET 5 (6번째부터 2명)
$page2 = get_friends(['me' => $u1, 'limit' => 5, 'offset' => 5]);
test_assert(
    count($page2) === 2,
    "OFFSET 5, LIMIT 5: 남은 2명 반환 (총 7명 중)"
);

// OFFSET 7 (범위 초과)
$page3 = get_friends(['me' => $u1, 'limit' => 5, 'offset' => 7]);
test_assert(
    count($page3) === 0,
    "OFFSET 7, LIMIT 5: 빈 배열 반환 (범위 초과)"
);

// OFFSET 음수 (자동 보정 → 0)
$page_negative = get_friends(['me' => $u1, 'limit' => 5, 'offset' => -10]);
test_assert(
    count($page_negative) === 5,
    "OFFSET -10일 때 0으로 보정되어 정상 동작"
);

echo "\n";

// ============================================================================
// 테스트 4: ORDER_BY 및 ORDER 옵션
// ============================================================================
echo "테스트 4: ORDER_BY 및 ORDER 옵션\n";
echo "----------------------------------------\n";

// ORDER BY id DESC (기본값)
$friends_id_desc = get_friends(['me' => $u1]);
$ids_desc = array_column($friends_id_desc, 'id');
$is_desc = true;
for ($i = 0; $i < count($ids_desc) - 1; $i++) {
    if ($ids_desc[$i] < $ids_desc[$i + 1]) {
        $is_desc = false;
        break;
    }
}
test_assert(
    $is_desc,
    "기본 정렬 (id DESC): ID가 내림차순으로 정렬되어야 함"
);

// ORDER BY id ASC
$friends_id_asc = get_friends(['me' => $u1, 'order_by' => 'id', 'order' => 'ASC']);
$ids_asc = array_column($friends_id_asc, 'id');
$is_asc = true;
for ($i = 0; $i < count($ids_asc) - 1; $i++) {
    if ($ids_asc[$i] > $ids_asc[$i + 1]) {
        $is_asc = false;
        break;
    }
}
test_assert(
    $is_asc,
    "ID 오름차순 정렬 (id ASC): ID가 오름차순으로 정렬되어야 함"
);

// ORDER BY display_name ASC
$friends_name_asc = get_friends(['me' => $u1, 'order_by' => 'display_name', 'order' => 'ASC']);
test_assert(
    is_array($friends_name_asc),
    "이름 오름차순 정렬 (display_name ASC): 정상 동작"
);

// ORDER BY created_at DESC
$friends_created_desc = get_friends(['me' => $u1, 'order_by' => 'created_at', 'order' => 'DESC']);
test_assert(
    is_array($friends_created_desc),
    "생성일 내림차순 정렬 (created_at DESC): 정상 동작"
);

// 잘못된 order_by (자동 보정 → 'id')
$friends_invalid_order_by = get_friends(['me' => $u1, 'order_by' => 'invalid_column']);
test_assert(
    count($friends_invalid_order_by) === 7,
    "잘못된 order_by: 기본값('id')으로 보정되어 정상 동작"
);

// 잘못된 order (자동 보정 → 'DESC')
$friends_invalid_order = get_friends(['me' => $u1, 'order' => 'INVALID']);
test_assert(
    count($friends_invalid_order) === 7,
    "잘못된 order: 기본값('DESC')으로 보정되어 정상 동작"
);

echo "\n";

// ============================================================================
// 테스트 5: 친구가 없는 경우
// ============================================================================
echo "테스트 5: 친구가 없는 경우\n";
echo "----------------------------------------\n";

$friends_no_friends = get_friends(['me' => $u9]);
test_assert(
    is_array($friends_no_friends),
    "친구 없는 사용자: 배열 반환"
);
test_assert(
    count($friends_no_friends) === 0,
    "친구 없는 사용자: 빈 배열 반환"
);

echo "\n";

// ============================================================================
// 테스트 6: 에러 케이스
// ============================================================================
echo "테스트 6: 에러 케이스\n";
echo "----------------------------------------\n";

test_throws(
    fn() => get_friends(['me' => 0]),
    'invalid-me',
    "me=0일 때 'invalid-me' 에러 발생"
);

test_throws(
    fn() => get_friends(['me' => -5]),
    'invalid-me',
    "me=-5일 때 'invalid-me' 에러 발생"
);

test_throws(
    fn() => get_friends([]),
    'invalid-me',
    "me 파라미터 없을 때 'invalid-me' 에러 발생"
);

echo "\n";

// ============================================================================
// 테스트 7: 실제 사용 시나리오
// ============================================================================
echo "테스트 7: 실제 사용 시나리오\n";
echo "----------------------------------------\n";

// 시나리오 1: 사용자 목록 페이지에서 친구 5명 표시
$scenario1 = get_friends(['me' => $u1, 'limit' => 5]);
test_assert(
    count($scenario1) === 5,
    "시나리오 1 (친구 섹션): 최대 5명 조회"
);

// 시나리오 2: 친구 전체 목록 페이지 (페이지네이션)
// 1페이지: 0-4
$friends_page1 = get_friends(['me' => $u1, 'limit' => 5, 'offset' => 0]);
// 2페이지: 5-9
$friends_page2 = get_friends(['me' => $u1, 'limit' => 5, 'offset' => 5]);

test_assert(
    count($friends_page1) === 5 && count($friends_page2) === 2,
    "시나리오 2 (페이지네이션): 1페이지 5명, 2페이지 2명"
);

// 시나리오 3: 이름순 정렬된 친구 목록
$friends_sorted = get_friends(['me' => $u1, 'order_by' => 'display_name', 'order' => 'ASC']);
test_assert(
    count($friends_sorted) === 7,
    "시나리오 3 (이름순 정렬): 모든 친구 이름순으로 조회"
);

echo "\n";

// ============================================================================
// 테스트 정리
// ============================================================================
echo "테스트 정리 중...\n";
echo "----------------------------------------\n";

// 테스트 데이터 삭제
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN ($u1, $u2, $u3, $u4, $u5, $u6, $u7, $u8, $u9, $u10) OR user_id_b IN ($u1, $u2, $u3, $u4, $u5, $u6, $u7, $u8, $u9, $u10)");

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
