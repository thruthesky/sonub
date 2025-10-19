<?php

/**
 * 친구 요청 카운트 함수 테스트
 *
 * count_friend_requests_sent() 및 count_friend_requests_received() 함수를 테스트합니다.
 * 무방향 1행 모델에서 requested_by 필드를 사용하여 올바르게 카운트하는지 검증합니다.
 */

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "친구 요청 카운트 함수 테스트 시작\n";
echo "========================================\n\n";

// 테스트 사용자 생성
$pdo = pdo();

// 기존 테스트 데이터 정리
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN (1001, 1002, 1003) OR user_id_b IN (1001, 1002, 1003)");
$pdo->exec("DELETE FROM users WHERE id IN (1001, 1002, 1003)");

// 테스트 사용자 생성
$now = time();
$pdo->exec("INSERT INTO users (id, firebase_uid, display_name, created_at, updated_at) VALUES
    (1001, 'test-uid-1001', 'User 1001', $now, $now),
    (1002, 'test-uid-1002', 'User 1002', $now, $now),
    (1003, 'test-uid-1003', 'User 1003', $now, $now)
");

echo "✓ 테스트 사용자 생성 완료 (1001, 1002, 1003)\n\n";

// ============================================================================
// 테스트 1: 초기 상태 - 친구 요청이 없는 경우
// ============================================================================
echo "테스트 1: 초기 상태 - 친구 요청이 없는 경우\n";

$sent_count = count_friend_requests_sent(['me' => 1001]);
$received_count = count_friend_requests_received(['me' => 1001]);

if ($sent_count === 0 && $received_count === 0) {
    echo "✓ 통과: 초기 상태에서 보낸/받은 요청 수가 모두 0입니다.\n";
    echo "  - 보낸 요청: $sent_count\n";
    echo "  - 받은 요청: $received_count\n\n";
} else {
    echo "✗ 실패: 초기 상태에서 요청 수가 0이 아닙니다.\n";
    echo "  - 보낸 요청: $sent_count (예상: 0)\n";
    echo "  - 받은 요청: $received_count (예상: 0)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: User 1001이 User 1002에게 친구 요청 보내기
// ============================================================================
echo "테스트 2: User 1001이 User 1002에게 친구 요청 보내기\n";

request_friend(['me' => 1001, 'other' => 1002]);

$sent_count_1001 = count_friend_requests_sent(['me' => 1001]);
$received_count_1001 = count_friend_requests_received(['me' => 1001]);
$sent_count_1002 = count_friend_requests_sent(['me' => 1002]);
$received_count_1002 = count_friend_requests_received(['me' => 1002]);

if (
    $sent_count_1001 === 1 &&
    $received_count_1001 === 0 &&
    $sent_count_1002 === 0 &&
    $received_count_1002 === 1
) {
    echo "✓ 통과: User 1001의 보낸 요청 1건, User 1002의 받은 요청 1건\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001\n";
    echo "  - User 1001 받은 요청: $received_count_1001\n";
    echo "  - User 1002 보낸 요청: $sent_count_1002\n";
    echo "  - User 1002 받은 요청: $received_count_1002\n\n";
} else {
    echo "✗ 실패: 요청 카운트가 예상과 다릅니다.\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001 (예상: 1)\n";
    echo "  - User 1001 받은 요청: $received_count_1001 (예상: 0)\n";
    echo "  - User 1002 보낸 요청: $sent_count_1002 (예상: 0)\n";
    echo "  - User 1002 받은 요청: $received_count_1002 (예상: 1)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: User 1003이 User 1001에게 친구 요청 보내기
// ============================================================================
echo "테스트 3: User 1003이 User 1001에게 친구 요청 보내기\n";

request_friend(['me' => 1003, 'other' => 1001]);

$sent_count_1001 = count_friend_requests_sent(['me' => 1001]);
$received_count_1001 = count_friend_requests_received(['me' => 1001]);
$sent_count_1003 = count_friend_requests_sent(['me' => 1003]);
$received_count_1003 = count_friend_requests_received(['me' => 1003]);

if (
    $sent_count_1001 === 1 &&
    $received_count_1001 === 1 &&
    $sent_count_1003 === 1 &&
    $received_count_1003 === 0
) {
    echo "✓ 통과: User 1001의 보낸/받은 요청이 각각 1건\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001\n";
    echo "  - User 1001 받은 요청: $received_count_1001\n";
    echo "  - User 1003 보낸 요청: $sent_count_1003\n";
    echo "  - User 1003 받은 요청: $received_count_1003\n\n";
} else {
    echo "✗ 실패: 요청 카운트가 예상과 다릅니다.\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001 (예상: 1)\n";
    echo "  - User 1001 받은 요청: $received_count_1001 (예상: 1)\n";
    echo "  - User 1003 보낸 요청: $sent_count_1003 (예상: 1)\n";
    echo "  - User 1003 받은 요청: $received_count_1003 (예상: 0)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: User 1001이 User 1002의 친구 요청 수락
// ============================================================================
echo "테스트 4: User 1002가 User 1001의 친구 요청 수락 (pending -> accepted)\n";

accept_friend(['me' => 1002, 'other' => 1001]);

$sent_count_1001 = count_friend_requests_sent(['me' => 1001]);
$received_count_1001 = count_friend_requests_received(['me' => 1001]);
$sent_count_1002 = count_friend_requests_sent(['me' => 1002]);
$received_count_1002 = count_friend_requests_received(['me' => 1002]);

if (
    $sent_count_1001 === 0 &&
    $received_count_1001 === 1 &&
    $sent_count_1002 === 0 &&
    $received_count_1002 === 0
) {
    echo "✓ 통과: User 1001-1002의 친구 요청이 accepted 상태로 변경됨 (카운트에서 제외)\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001\n";
    echo "  - User 1001 받은 요청: $received_count_1001 (User 1003의 요청)\n";
    echo "  - User 1002 보낸 요청: $sent_count_1002\n";
    echo "  - User 1002 받은 요청: $received_count_1002\n\n";
} else {
    echo "✗ 실패: 요청 카운트가 예상과 다릅니다.\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001 (예상: 0)\n";
    echo "  - User 1001 받은 요청: $received_count_1001 (예상: 1)\n";
    echo "  - User 1002 보낸 요청: $sent_count_1002 (예상: 0)\n";
    echo "  - User 1002 받은 요청: $received_count_1002 (예상: 0)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: User 1001이 User 1002에게 다시 친구 요청 보내기 (이미 친구 상태)
// ============================================================================
echo "테스트 5: User 1001이 User 1003의 친구 요청 수락\n";

accept_friend(['me' => 1001, 'other' => 1003]);

$sent_count_1001 = count_friend_requests_sent(['me' => 1001]);
$received_count_1001 = count_friend_requests_received(['me' => 1001]);

if ($sent_count_1001 === 0 && $received_count_1001 === 0) {
    echo "✓ 통과: 모든 pending 요청이 accepted 상태로 변경됨 (카운트 모두 0)\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001\n";
    echo "  - User 1001 받은 요청: $received_count_1001\n\n";
} else {
    echo "✗ 실패: 요청 카운트가 예상과 다릅니다.\n";
    echo "  - User 1001 보낸 요청: $sent_count_1001 (예상: 0)\n";
    echo "  - User 1001 받은 요청: $received_count_1001 (예상: 0)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 정리
// ============================================================================
echo "테스트 정리 중...\n";

$pdo->exec("DELETE FROM friendships WHERE user_id_a IN (1001, 1002, 1003) OR user_id_b IN (1001, 1002, 1003)");
$pdo->exec("DELETE FROM users WHERE id IN (1001, 1002, 1003)");

echo "✓ 테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 완료
// ============================================================================
echo "========================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "========================================\n";
