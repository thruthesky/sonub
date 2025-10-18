<?php
/**
 * 친구 및 피드 기능 웹 기반 테스트
 *
 * 이 파일은 브라우저에서 friend-and-feed.functions.php의 모든 함수를 테스트합니다.
 *
 * 접근 URL: https://local.sonub.com/test/friend-and-feed-test
 */

echo "<h1>친구 및 피드 기능 테스트</h1>";
echo "<pre>";

// 테스트 결과 카운터
$passed = 0;
$failed = 0;

/**
 * 테스트 헬퍼 함수: 결과 검증
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
 * 테스트 헬퍼 함수: 예외 발생 확인
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

/**
 * 테스트 헬퍼 함수: 친구 관계 단일 행 조회
 */
function fetch_friendship(int $u1, int $u2): ?array
{
    $pdo = pdo();
    [$a, $b] = friend_pair($u1, $u2);
    $stmt = $pdo->prepare("SELECT * FROM friendships WHERE user_id_a = :a AND user_id_b = :b");
    $stmt->execute([':a' => $a, ':b' => $b]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

// ============================================================================
// 테스트 데이터 준비
// ============================================================================

echo "========================================\n";
echo "친구 및 피드 기능 테스트 시작\n";
echo "========================================\n\n";

echo "테스트 데이터 준비...\n";

// 실제 존재하는 사용자 ID 가져오기 (최대 5명)
$pdo = pdo();
$stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 5");
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($users) < 3) {
    echo "⚠ 테스트를 위해서는 최소 3명의 사용자가 필요합니다.\n";
    echo "현재 사용자 수: " . count($users) . "\n";
    exit;
}

// 테스트용 사용자 ID (실제 DB에 존재하는 ID 사용)
$u1 = $users[0];
$u2 = $users[1];
$u3 = $users[2];
$u4 = count($users) > 3 ? $users[3] : $users[0];
$u5 = count($users) > 4 ? $users[4] : $users[1];

echo "테스트용 사용자 ID: " . implode(', ', [$u1, $u2, $u3, $u4, $u5]) . "\n";

// 기존 친구 관계 삭제 (테스트용 사용자들)
$user_ids = implode(',', [$u1, $u2, $u3, $u4, $u5]);
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN ($user_ids) OR user_id_b IN ($user_ids)");
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id IN ($user_ids)");

echo "테스트 데이터 준비 완료\n\n";

// ============================================================================
// 테스트 1: friend_pair() 내부 헬퍼 함수
// ============================================================================

echo "테스트 1: friend_pair() 함수\n";
echo "----------------------------------------\n";

$pair1 = friend_pair(10, 5);
assert_true($pair1[0] === 5 && $pair1[1] === 10, "friend_pair(10, 5) = [5, 10]");

$pair2 = friend_pair(3, 8);
assert_true($pair2[0] === 3 && $pair2[1] === 8, "friend_pair(3, 8) = [3, 8]");

echo "\n";

// ============================================================================
// 테스트 2: request_friend() 함수
// ============================================================================

echo "테스트 2: request_friend() 함수\n";
echo "----------------------------------------\n";

// 정상 케이스: 친구 요청 생성
$result = request_friend(['me' => $u1, 'other' => $u2]);
assert_true(isset($result['message']), "친구 요청 성공: 메시지 반환");

// DB 확인
$friendship = fetch_friendship($u1, $u2);
assert_true($friendship !== false, "친구 요청이 DB에 저장됨");
assert_true($friendship['status'] === 'pending', "친구 요청 상태가 'pending'");
assert_true((int)$friendship['requested_by'] === $u1, "요청자 ID가 올바름");

// 에러 케이스: invalid-me
assert_throws(
    fn() => request_friend(['me' => 0, 'other' => $u2]),
    'invalid-me',
    "me가 0일 때 'invalid-me' 에러 발생"
);

// 에러 케이스: same-user
assert_throws(
    fn() => request_friend(['me' => $u1, 'other' => $u1]),
    'same-user',
    "me와 other가 같을 때 'same-user' 에러 발생"
);

echo "\n";

// ============================================================================
// 테스트 3: accept_friend() 함수
// ============================================================================

echo "테스트 3: accept_friend() 함수\n";
echo "----------------------------------------\n";

// 정상 케이스: 친구 요청 수락
$result = accept_friend(['me' => $u2, 'other' => $u1]);
assert_true(isset($result['success']) && $result['success'] === true, "친구 요청 수락 성공");

// DB 확인
$friendship = fetch_friendship($u1, $u2);
assert_true($friendship['status'] === 'accepted', "친구 요청 상태가 'accepted'로 변경됨");

echo "\n";

// ============================================================================
// 테스트 4: get_friend_ids() 함수
// ============================================================================

echo "테스트 4: get_friend_ids() 함수\n";
echo "----------------------------------------\n";

// 친구 목록 조회
$result = get_friend_ids(['me' => $u1]);
assert_true(isset($result['friend_ids']), "friend_ids 키 존재");
assert_true(count($result['friend_ids']) === 1, "친구 목록에 1명 존재");
assert_true(in_array($u2, $result['friend_ids']), "친구 ID가 올바름");

echo "\n";

// ============================================================================
// 테스트 5: remove_friend() 함수
// ============================================================================

echo "테스트 5: remove_friend() 함수\n";
echo "----------------------------------------\n";

// 정상 케이스: 친구 관계 삭제
$result = remove_friend(['me' => $u1, 'other' => $u2]);
assert_true(isset($result['success']) && $result['success'] === true, "친구 관계 삭제 성공");

// DB 확인
$friendship = fetch_friendship($u1, $u2);
assert_true($friendship === null, "친구 관계가 DB에서 삭제됨");

// 친구 목록 조회하면 비어있어야 함
$result = get_friend_ids(['me' => $u1]);
assert_true(count($result['friend_ids']) === 0, "친구 목록이 비어있음");

echo "\n";

// ============================================================================
// 테스트 6: 복합 시나리오 - 여러 친구
// ============================================================================

echo "테스트 6: 복합 시나리오 - 여러 친구\n";
echo "----------------------------------------\n";

// 사용자 u1이 여러 명에게 친구 요청
request_friend(['me' => $u1, 'other' => $u3]);
request_friend(['me' => $u1, 'other' => $u4]);
request_friend(['me' => $u1, 'other' => $u5]);

// 모두 수락
accept_friend(['me' => $u3, 'other' => $u1]);
accept_friend(['me' => $u4, 'other' => $u1]);
accept_friend(['me' => $u5, 'other' => $u1]);

// 친구 목록 확인
$result = get_friend_ids(['me' => $u1]);
assert_true(count($result['friend_ids']) === 3, "친구 목록에 3명 존재");
assert_true(
    in_array($u3, $result['friend_ids']) &&
    in_array($u4, $result['friend_ids']) &&
    in_array($u5, $result['friend_ids']),
    "모든 친구 ID가 올바름"
);

echo "\n";

// ============================================================================
// 테스트 정리
// ============================================================================

echo "테스트 데이터 정리...\n";
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN ($user_ids) OR user_id_b IN ($user_ids)");
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id IN ($user_ids)");
echo "테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 결과 출력
// ============================================================================

echo "========================================\n";
echo "테스트 결과\n";
echo "========================================\n";
echo "통과: $passed\n";
echo "실패: $failed\n";
echo "========================================\n\n";

if ($failed === 0) {
    echo "✅ 모든 테스트 통과!\n";
} else {
    echo "❌ 일부 테스트 실패\n";
}

echo "</pre>";
