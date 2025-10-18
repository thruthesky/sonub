<?php

/**
 * 친구 및 피드 기능 테스트
 *
 * 이 파일은 friend-and-feed.functions.php의 모든 함수를 테스트합니다.
 *
 * 실행 방법:
 * docker exec sonub-php php /sonub/tests/friend-and-feed/friend-and-feed.test.php
 */

declare(strict_types=1);

// 프로젝트 루트 디렉토리로 이동 (도커/호스트 양쪽에서 동작)
$projectRoot = dirname(__DIR__, 2);
chdir($projectRoot);
require_once './init.php';

echo "========================================\n";
echo "친구 및 피드 기능 테스트 시작\n";
echo "========================================\n\n";

// 테스트 결과 카운터
$passed = 0;
$failed = 0;
$testPostIdFanout = 990001;
$testPostIdNoFriend = 990002;

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

/**
 * 테스트용 사용자 레코드 보장
 */
function ensure_test_user(PDO $pdo, int $userId, int $now): void
{
    $firebaseUid = "test-user-$userId";
    $displayName = "TestUser{$userId}";

    $stmt = $pdo->prepare("INSERT INTO users (id, firebase_uid, display_name, created_at, updated_at, birthday, gender, photo_url)
        VALUES (:id, :firebase_uid, :display_name, :created_at, :updated_at, 0, '', '')
        ON DUPLICATE KEY UPDATE firebase_uid = VALUES(firebase_uid), display_name = VALUES(display_name), updated_at = VALUES(updated_at)");
    $stmt->execute([
        ':id' => $userId,
        ':firebase_uid' => $firebaseUid,
        ':display_name' => $displayName,
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);
}

/**
 * 테스트용 게시글 레코드 보장
 */
function ensure_test_post(PDO $pdo, int $postId, int $authorId, int $createdAt): void
{
    $stmt = $pdo->prepare("INSERT INTO posts (id, user_id, category, title, content, visibility, files, created_at, updated_at)
        VALUES (:id, :user_id, 'discussion', :title, :content, 'public', '', :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), updated_at = VALUES(updated_at)");
    $stmt->execute([
        ':id' => $postId,
        ':user_id' => $authorId,
        ':title' => "테스트 게시글 {$postId}",
        ':content' => "fanout 테스트 본문 {$postId}",
        ':created_at' => $createdAt,
        ':updated_at' => $createdAt,
    ]);
}

/**
 * 테스트 헬퍼 함수: feed_entries 조회
 */
function fetch_feed_entries(int $receiverId, int $postId): array
{
    $pdo = pdo();
    $stmt = $pdo->prepare("SELECT * FROM feed_entries WHERE receiver_id = :rid AND post_id = :pid ORDER BY id ASC");
    $stmt->execute([':rid' => $receiverId, ':pid' => $postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// 테스트 데이터 준비
// ============================================================================

echo "테스트 데이터 준비...\n";

// 기존 테스트 데이터 삭제
$pdo = pdo();
$pdo->exec("DELETE FROM friendships WHERE user_id_a >= 1000 OR user_id_b >= 1000");
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id >= 1000");

// 테스트용 사용자 생성
$testUserIds = array_merge(range(1001, 1015), [1100, 1101, 1102, 1201, 1500, 1501]);
$now = time();
foreach ($testUserIds as $testUserId) {
    ensure_test_user($pdo, $testUserId, $now);
}

ensure_test_post($pdo, $testPostIdFanout, 1100, $now);
ensure_test_post($pdo, $testPostIdNoFriend, 1201, $now);

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

$pair3 = friend_pair(1, 1);
assert_true($pair3[0] === 1 && $pair3[1] === 1, "friend_pair(1, 1) = [1, 1]");

echo "\n";

// ============================================================================
// 테스트 2: request_friend() 함수
// ============================================================================

echo "테스트 2: request_friend() 함수\n";
echo "----------------------------------------\n";

// 정상 케이스: 친구 요청 생성
$result = request_friend(['me' => 1001, 'other' => 1002]);
assert_true(isset($result['message']), "친구 요청 성공: 메시지 반환");

// DB 확인
$stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id_a = 1001 AND user_id_b = 1002) OR (user_id_a = 1002 AND user_id_b = 1001)");
$stmt->execute();
$friendship = $stmt->fetch(PDO::FETCH_ASSOC);
assert_true($friendship !== false, "친구 요청이 DB에 저장됨");
assert_true($friendship['status'] === 'pending', "친구 요청 상태가 'pending'");
assert_true((int)$friendship['requested_by'] === 1001, "요청자 ID가 올바름");

// 에러 케이스: invalid-me
assert_throws(
    fn() => request_friend(['me' => 0, 'other' => 1002]),
    'invalid-me',
    "me가 0일 때 'invalid-me' 에러 발생"
);

// 에러 케이스: invalid-other
assert_throws(
    fn() => request_friend(['me' => 1001, 'other' => -1]),
    'invalid-other',
    "other가 음수일 때 'invalid-other' 에러 발생"
);

// 에러 케이스: same-user
assert_throws(
    fn() => request_friend(['me' => 1001, 'other' => 1001]),
    'same-user',
    "me와 other가 같을 때 'same-user' 에러 발생"
);

echo "\n";

// ============================================================================
// 테스트 3: get_friend_ids() 함수
// ============================================================================

echo "테스트 3: get_friend_ids() 함수\n";
echo "----------------------------------------\n";

// 아직 친구가 없는 경우
$result = get_friend_ids(['me' => 1001]);
assert_true(isset($result['friend_ids']), "friend_ids 키 존재");
assert_true(count($result['friend_ids']) === 0, "친구 없음: 빈 배열 반환");

// 에러 케이스: invalid-me
assert_throws(
    fn() => get_friend_ids(['me' => 0]),
    'invalid-me',
    "me가 0일 때 'invalid-me' 에러 발생"
);

echo "\n";

// ============================================================================
// 테스트 4: accept_friend() 함수
// ============================================================================

echo "테스트 4: accept_friend() 함수\n";
echo "----------------------------------------\n";

// 정상 케이스: 친구 요청 수락
$beforeFriendship = fetch_friendship(1001, 1002);
$beforeUpdatedAt = $beforeFriendship ? (int)$beforeFriendship['updated_at'] : 0;
$result = accept_friend(['me' => 1002, 'other' => 1001]);
assert_true(isset($result['success']) && $result['success'] === true, "친구 요청 수락 성공");

// DB 확인
$stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id_a = 1001 AND user_id_b = 1002) OR (user_id_a = 1002 AND user_id_b = 1001)");
$stmt->execute();
$friendship = $stmt->fetch(PDO::FETCH_ASSOC);
assert_true($friendship['status'] === 'accepted', "친구 요청 상태가 'accepted'로 변경됨");
assert_true((int)$friendship['updated_at'] >= $beforeUpdatedAt, "수락 시 updated_at 값이 갱신됨");

// 이제 친구 목록 조회하면 친구가 있어야 함
$result = get_friend_ids(['me' => 1001]);
assert_true(count($result['friend_ids']) === 1, "친구 목록에 1명 존재");
assert_true(in_array(1002, $result['friend_ids']), "친구 ID가 올바름");

// 반대편에서도 확인
$result = get_friend_ids(['me' => 1002]);
assert_true(count($result['friend_ids']) === 1, "반대편에서도 친구 1명 존재");
assert_true(in_array(1001, $result['friend_ids']), "반대편 친구 ID가 올바름");

// 에러 케이스: 이미 수락된 요청 다시 수락 시도
assert_throws(
    fn() => accept_friend(['me' => 1002, 'other' => 1001]),
    'no-pending-request',
    "이미 수락된 요청 다시 수락 시 'no-pending-request' 에러 발생"
);

// 에러 케이스: invalid-me
assert_throws(
    fn() => accept_friend(['me' => 0, 'other' => 1001]),
    'invalid-me',
    "me가 0일 때 'invalid-me' 에러 발생"
);

// 에러 케이스: invalid-other
assert_throws(
    fn() => accept_friend(['me' => 1002, 'other' => 0]),
    'invalid-other',
    "other가 0일 때 'invalid-other' 에러 발생"
);

// 에러 케이스: 친구 요청이 존재하지 않을 때
assert_throws(
    fn() => accept_friend(['me' => 1500, 'other' => 1501]),
    'no-pending-request',
    "존재하지 않는 친구 요청 수락 시 'no-pending-request' 에러 발생"
);

// 비즈니스 케이스: 요청자가 직접 수락하려고 하면 어떻게 되는지 확인
request_friend(['me' => 1010, 'other' => 1011]);
$selfAcceptResult = accept_friend(['me' => 1010, 'other' => 1011]);
assert_true($selfAcceptResult['success'] === true, "요청자가 직접 수락 시 현재 구현에서는 허용됨");
$friendship = fetch_friendship(1010, 1011);
assert_true($friendship !== null && $friendship['status'] === 'accepted', "요청자가 직접 수락한 경우에도 DB에 accepted로 반영됨");

// 수락 후 상태를 다시 pending으로 되돌리고 비-요청자가 수락하는 흐름 검증
request_friend(['me' => 1012, 'other' => 1013]);
$pending = fetch_friendship(1012, 1013);
assert_true($pending !== null && $pending['status'] === 'pending', "새로운 친구 요청이 pending 상태로 생성됨");

$acceptorResult = accept_friend(['me' => 1013, 'other' => 1012]);
assert_true($acceptorResult['success'] === true, "요청을 받은 사용자가 수락하면 성공");

// 거절 상태 시나리오: status가 pending이 아닐 때 수락 불가
$pdo->exec("INSERT INTO friendships (user_id_a, user_id_b, status, requested_by, created_at, updated_at)
            VALUES (1014, 1015, 'rejected', 1014, " . time() . ", " . time() . ")
            ON DUPLICATE KEY UPDATE status='rejected', requested_by=VALUES(requested_by), updated_at=VALUES(updated_at)");
assert_throws(
    fn() => accept_friend(['me' => 1015, 'other' => 1014]),
    'no-pending-request',
    "status가 rejected인 경우 수락 불가"
);

echo "\n";

// ============================================================================
// 테스트 5: remove_friend() 함수
// ============================================================================

echo "테스트 5: remove_friend() 함수\n";
echo "----------------------------------------\n";

// 정상 케이스: 친구 관계 삭제
$result = remove_friend(['me' => 1001, 'other' => 1002]);
assert_true(isset($result['success']) && $result['success'] === true, "친구 관계 삭제 성공");

// DB 확인
$stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id_a = 1001 AND user_id_b = 1002) OR (user_id_a = 1002 AND user_id_b = 1001)");
$stmt->execute();
$friendship = $stmt->fetch(PDO::FETCH_ASSOC);
assert_true($friendship === false, "친구 관계가 DB에서 삭제됨");

// 친구 목록 조회하면 비어있어야 함
$result = get_friend_ids(['me' => 1001]);
assert_true(count($result['friend_ids']) === 0, "친구 목록이 비어있음");

// 에러 케이스: 존재하지 않는 친구 관계 삭제 시도
assert_throws(
    fn() => remove_friend(['me' => 1001, 'other' => 1002]),
    'no-friendship',
    "존재하지 않는 친구 관계 삭제 시 'no-friendship' 에러 발생"
);

echo "\n";

// ============================================================================
// 테스트 6: 복합 시나리오 - 여러 친구
// ============================================================================

echo "테스트 6: 복합 시나리오 - 여러 친구\n";
echo "----------------------------------------\n";

// 사용자 1001이 여러 명에게 친구 요청
request_friend(['me' => 1001, 'other' => 1003]);
request_friend(['me' => 1001, 'other' => 1004]);
request_friend(['me' => 1001, 'other' => 1005]);

// 모두 수락
accept_friend(['me' => 1003, 'other' => 1001]);
accept_friend(['me' => 1004, 'other' => 1001]);
accept_friend(['me' => 1005, 'other' => 1001]);

// 친구 목록 확인
$result = get_friend_ids(['me' => 1001]);
assert_true(count($result['friend_ids']) === 3, "친구 목록에 3명 존재");
assert_true(
    in_array(1003, $result['friend_ids']) &&
        in_array(1004, $result['friend_ids']) &&
        in_array(1005, $result['friend_ids']),
    "모든 친구 ID가 올바름"
);

echo "\n";

// ============================================================================
// 테스트 7: is_blocked_either_way() 내부 함수 (차단 기능)
// ============================================================================

echo "테스트 7: is_blocked_either_way() 함수\n";
echo "----------------------------------------\n";

// 차단 없음
$blocked = is_blocked_either_way(1001, 1003);
assert_true($blocked === false, "차단 관계 없음");

// 차단 추가 (테스트용 - 실제로는 block_user() 함수 사용)
$pdo->exec("INSERT INTO blocks (blocker_id, blocked_id, created_at) VALUES (1001, 1006, " . time() . ")");

// 차단 확인
$blocked = is_blocked_either_way(1001, 1006);
assert_true($blocked === true, "1001이 1006을 차단함 (단방향)");

// 반대편에서도 확인 (양방향 체크)
$blocked = is_blocked_either_way(1006, 1001);
assert_true($blocked === true, "1006에서 확인해도 차단됨 (양방향 체크)");

// 정리
$pdo->exec("DELETE FROM blocks WHERE blocker_id >= 1000");

echo "\n";

// ============================================================================
// 테스트 8: get_post_row() 내부 함수
// ============================================================================

echo "테스트 8: get_post_row() 함수\n";
echo "----------------------------------------\n";

// 존재하지 않는 게시글
$post = get_post_row(999999);
assert_true($post === null, "존재하지 않는 게시글은 null 반환");

// 실제 게시글 조회 (첫 번째 게시글)
$stmt = $pdo->query("SELECT id FROM posts ORDER BY id ASC LIMIT 1");
$first_post = $stmt->fetch(PDO::FETCH_ASSOC);
if ($first_post) {
    $post_id = (int)$first_post['id'];
    $post = get_post_row($post_id);
    assert_true($post !== null, "실제 게시글 조회 성공");
    assert_true((int)$post['id'] === $post_id, "게시글 ID가 올바름");
} else {
    echo "⚠ 게시글이 없어 테스트 건너뜀\n";
}

echo "\n";

// ============================================================================
// 테스트 9: fanout_post_to_friends() 함수
// ============================================================================

echo "테스트 9: fanout_post_to_friends() 함수\n";
echo "----------------------------------------\n";

// 준비: 친구 관계 생성 (작성자 1100, 수신자 1101/1102)
request_friend(['me' => 1100, 'other' => 1101]);
accept_friend(['me' => 1101, 'other' => 1100]);
request_friend(['me' => 1100, 'other' => 1102]);
accept_friend(['me' => 1102, 'other' => 1100]);

// 기존 feed_entries 정리 (fan-out 검증을 위해 관련 데이터만 삭제)
$pdo->exec("DELETE FROM feed_entries WHERE post_id IN ({$testPostIdFanout}, {$testPostIdNoFriend})");

// 케이스 1: 친구 2명에게 fan-out
$createdAt = time();
$insertedRows = fanout_post_to_friends(1100, $testPostIdFanout, $createdAt);
assert_true($insertedRows === 2, "친구 2명에게 피드 전파");

$entries1101 = fetch_feed_entries(1101, $testPostIdFanout);
$entries1102 = fetch_feed_entries(1102, $testPostIdFanout);
assert_true(count($entries1101) === 1, "수신자 1101의 피드 기록 1개");
assert_true(count($entries1102) === 1, "수신자 1102의 피드 기록 1개");
assert_true((int)$entries1101[0]['post_author_id'] === 1100, "1101 레코드 작성자 확인");
assert_true((int)$entries1102[0]['post_author_id'] === 1100, "1102 레코드 작성자 확인");
assert_true((int)$entries1101[0]['created_at'] === $createdAt, "created_at 값 일치 (1101)");

// 케이스 2: 중복 fan-out 시 INSERT IGNORE로 인해 0건
$duplicateRows = fanout_post_to_friends(1100, $testPostIdFanout, $createdAt + 5);
assert_true($duplicateRows === 0, "중복 fan-out 시 신규 삽입 없음");
assert_true(count(fetch_feed_entries(1101, $testPostIdFanout)) === 1, "중복 호출 후 1101 피드 레코드 여전히 1개");

// 케이스 3: 친구가 없는 사용자는 0건 반환
$noFriendRows = fanout_post_to_friends(1201, $testPostIdNoFriend, $createdAt);
assert_true($noFriendRows === 0, "친구가 없으면 fan-out 결과 0");

echo "\n";

// ============================================================================
// 테스트 정리
// ============================================================================

echo "테스트 데이터 정리...\n";
$pdo->exec("DELETE FROM friendships WHERE user_id_a >= 1000 OR user_id_b >= 1000");
$pdo->exec("DELETE FROM feed_entries WHERE receiver_id >= 1000");
$pdo->exec("DELETE FROM blocks WHERE blocker_id >= 1000");
$pdo->exec("DELETE FROM posts WHERE id IN ({$testPostIdFanout}, {$testPostIdNoFriend})");
$pdo->exec("DELETE FROM users WHERE id >= 1000");
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
    exit(0);
} else {
    echo "❌ 일부 테스트 실패\n";
    exit(1);
}
