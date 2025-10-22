<?php

/**
 * get_friend_requests_received() 함수 테스트
 *
 * pending 상태의 받은 친구 요청 목록을 조회하는 함수를 테스트합니다.
 * 무방향 1행 모델에서 requested_by 필드를 사용하여 올바르게 조회하는지 검증합니다.
 */

require_once __DIR__ . '/../../init.php';

echo "========================================\n";
echo "get_friend_requests_received() 함수 테스트 시작\n";
echo "========================================\n\n";

// 테스트 사용자 생성
$pdo = pdo();

// 기존 테스트 데이터 정리
$pdo->exec("DELETE FROM friendships WHERE user_id_a IN (2001, 2002, 2003, 2004) OR user_id_b IN (2001, 2002, 2003, 2004)");
$pdo->exec("DELETE FROM users WHERE id IN (2001, 2002, 2003, 2004)");

// 테스트 사용자 생성
$now = time();
$pdo->exec("INSERT INTO users (id, firebase_uid, first_name, photo_url, created_at, updated_at) VALUES
    (2001, 'test-uid-2001', 'Alice', 'https://example.com/alice.jpg', $now, $now),
    (2002, 'test-uid-2002', 'Bob', 'https://example.com/bob.jpg', $now, $now),
    (2003, 'test-uid-2003', 'Charlie', 'https://example.com/charlie.jpg', $now, $now),
    (2004, 'test-uid-2004', 'David', 'https://example.com/david.jpg', $now, $now)
");

echo "✓ 테스트 사용자 생성 완료 (2001: Alice, 2002: Bob, 2003: Charlie, 2004: David)\n\n";

// ============================================================================
// 테스트 1: 초기 상태 - 받은 친구 요청이 없는 경우
// ============================================================================
echo "테스트 1: 초기 상태 - 받은 친구 요청이 없는 경우\n";

$requests = get_friend_requests_received(['me' => 2001]);

if (count($requests) === 0) {
    echo "✓ 통과: 받은 친구 요청이 없습니다.\n";
    echo "  - 요청 수: " . count($requests) . "\n\n";
} else {
    echo "✗ 실패: 초기 상태에서 받은 요청이 있습니다.\n";
    echo "  - 요청 수: " . count($requests) . " (예상: 0)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 2: Alice(2001)가 Bob(2002), Charlie(2003)로부터 친구 요청 받기
// ============================================================================
echo "테스트 2: Alice(2001)가 Bob(2002), Charlie(2003)로부터 친구 요청 받기\n";

// Bob → Alice
request_friend(['me' => 2002, 'other' => 2001]);
sleep(1); // created_at이 다르도록 1초 대기

// Charlie → Alice
request_friend(['me' => 2003, 'other' => 2001]);

$requests = get_friend_requests_received(['me' => 2001]);

if (count($requests) === 2) {
    echo "✓ 통과: Alice가 2건의 친구 요청을 받았습니다.\n";
    echo "  - 요청 수: " . count($requests) . "\n";
    echo "  - 첫 번째 요청자: {$requests[0]['first_name']} (ID: {$requests[0]['user_id']})\n";
    echo "  - 두 번째 요청자: {$requests[1]['first_name']} (ID: {$requests[1]['user_id']})\n\n";
} else {
    echo "✗ 실패: 요청 수가 예상과 다릅니다.\n";
    echo "  - 요청 수: " . count($requests) . " (예상: 2)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 3: 요청자 정보 검증
// ============================================================================
echo "테스트 3: 요청자 정보 검증\n";

$foundBob = false;
$foundCharlie = false;

foreach ($requests as $req) {
    $full_name = $req['first_name'] . ($req['last_name'] ? ' ' . $req['last_name'] : '');
    if ($req['user_id'] === 2002 && $req['first_name'] === 'Bob') {
        $foundBob = true;
        echo "  ✓ Bob의 요청 확인\n";
        echo "    - user_id: {$req['user_id']}\n";
        echo "    - first_name: {$req['first_name']}\n";
        echo "    - last_name: {$req['last_name']}\n";
        echo "    - photo_url: {$req['photo_url']}\n";
        echo "    - requested_by: {$req['requested_by']}\n";
    }
    if ($req['user_id'] === 2003 && $req['first_name'] === 'Charlie') {
        $foundCharlie = true;
        echo "  ✓ Charlie의 요청 확인\n";
        echo "    - user_id: {$req['user_id']}\n";
        echo "    - first_name: {$req['first_name']}\n";
        echo "    - last_name: {$req['last_name']}\n";
        echo "    - photo_url: {$req['photo_url']}\n";
        echo "    - requested_by: {$req['requested_by']}\n";
    }
}

if ($foundBob && $foundCharlie) {
    echo "✓ 통과: 모든 요청자 정보가 올바릅니다.\n\n";
} else {
    echo "✗ 실패: 요청자 정보가 올바르지 않습니다.\n";
    echo "  - Bob 발견: " . ($foundBob ? 'Yes' : 'No') . "\n";
    echo "  - Charlie 발견: " . ($foundCharlie ? 'Yes' : 'No') . "\n\n";
    exit(1);
}

// ============================================================================
// 테스트 4: 정렬 순서 테스트 (updated_at DESC - 기본값)
// ============================================================================
echo "테스트 4: 정렬 순서 테스트 (updated_at DESC - 기본값)\n";

// Charlie의 요청이 나중에 생성되었으므로 첫 번째로 나와야 함
if ($requests[0]['user_id'] === 2003 && $requests[1]['user_id'] === 2002) {
    echo "✓ 통과: 최신 요청이 먼저 표시됩니다.\n";
    echo "  - 첫 번째: Charlie (나중 요청)\n";
    echo "  - 두 번째: Bob (먼저 요청)\n\n";
} else {
    echo "✗ 실패: 정렬 순서가 올바르지 않습니다.\n";
    echo "  - 첫 번째: {$requests[0]['first_name']} (ID: {$requests[0]['user_id']})\n";
    echo "  - 두 번째: {$requests[1]['first_name']} (ID: {$requests[1]['user_id']})\n\n";
    exit(1);
}

// ============================================================================
// 테스트 5: 정렬 순서 변경 테스트 (display_name ASC)
// ============================================================================
echo "테스트 5: 정렬 순서 변경 테스트 (display_name ASC)\n";

$requests = get_friend_requests_received([
    'me' => 2001,
    'order_by' => 'first_name',
    'order' => 'ASC'
]);

// 알파벳 순서: Bob < Charlie
if ($requests[0]['first_name'] === 'Bob' && $requests[1]['first_name'] === 'Charlie') {
    echo "✓ 통과: 이름 오름차순 정렬이 올바릅니다.\n";
    echo "  - 첫 번째: {$requests[0]['first_name']}\n";
    echo "  - 두 번째: {$requests[1]['first_name']}\n\n";
} else {
    echo "✗ 실패: 정렬이 올바르지 않습니다.\n";
    echo "  - 첫 번째: {$requests[0]['first_name']}\n";
    echo "  - 두 번째: {$requests[1]['first_name']}\n\n";
    exit(1);
}

// ============================================================================
// 테스트 6: limit 파라미터 테스트
// ============================================================================
echo "테스트 6: limit 파라미터 테스트\n";

$requests = get_friend_requests_received([
    'me' => 2001,
    'limit' => 1
]);

if (count($requests) === 1) {
    echo "✓ 통과: limit=1로 1건만 조회됩니다.\n";
    echo "  - 조회된 요청 수: " . count($requests) . "\n\n";
} else {
    echo "✗ 실패: limit 파라미터가 작동하지 않습니다.\n";
    echo "  - 조회된 요청 수: " . count($requests) . " (예상: 1)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 7: offset 파라미터 테스트
// ============================================================================
echo "테스트 7: offset 파라미터 테스트\n";

$requests = get_friend_requests_received([
    'me' => 2001,
    'limit' => 1,
    'offset' => 1,
    'order_by' => 'first_name',
    'order' => 'ASC'
]);

if (count($requests) === 1 && $requests[0]['first_name'] === 'Charlie') {
    echo "✓ 통과: offset=1로 두 번째 요청만 조회됩니다.\n";
    echo "  - 조회된 요청자: {$requests[0]['first_name']}\n\n";
} else {
    echo "✗ 실패: offset 파라미터가 작동하지 않습니다.\n";
    echo "  - 조회된 요청 수: " . count($requests) . "\n";
    if (count($requests) > 0) {
        echo "  - 조회된 요청자: {$requests[0]['first_name']} (예상: Charlie)\n\n";
    }
    exit(1);
}

// ============================================================================
// 테스트 8: Alice가 David에게 친구 요청을 보낸 경우 (받은 요청 목록에 포함되지 않음)
// ============================================================================
echo "테스트 8: Alice가 David에게 친구 요청을 보낸 경우 (받은 요청 목록에 포함되지 않음)\n";

request_friend(['me' => 2001, 'other' => 2004]);

$requests = get_friend_requests_received(['me' => 2001]);

// Alice → David 요청은 받은 요청이 아니므로 여전히 2건이어야 함
if (count($requests) === 2) {
    echo "✓ 통과: 보낸 요청은 받은 요청 목록에 포함되지 않습니다.\n";
    echo "  - 받은 요청 수: " . count($requests) . " (Bob, Charlie만 포함)\n\n";
} else {
    echo "✗ 실패: 받은 요청 수가 예상과 다릅니다.\n";
    echo "  - 받은 요청 수: " . count($requests) . " (예상: 2)\n\n";
    exit(1);
}

// ============================================================================
// 테스트 9: Bob의 요청을 수락한 후 받은 요청 목록에서 제외되는지 확인
// ============================================================================
echo "테스트 9: Bob의 요청을 수락한 후 받은 요청 목록에서 제외되는지 확인\n";

accept_friend(['me' => 2001, 'other' => 2002]);

$requests = get_friend_requests_received(['me' => 2001]);

// Bob 요청 수락 후 Charlie만 남아야 함
if (count($requests) === 1 && $requests[0]['first_name'] === 'Charlie') {
    echo "✓ 통과: 수락된 요청은 목록에서 제외됩니다.\n";
    echo "  - 남은 요청 수: " . count($requests) . "\n";
    echo "  - 남은 요청자: {$requests[0]['first_name']}\n\n";
} else {
    echo "✗ 실패: 수락된 요청이 목록에서 제외되지 않았습니다.\n";
    echo "  - 남은 요청 수: " . count($requests) . " (예상: 1)\n";
    if (count($requests) > 0) {
        echo "  - 남은 요청자: {$requests[0]['first_name']} (예상: Charlie)\n\n";
    }
    exit(1);
}

// ============================================================================
// 테스트 정리
// ============================================================================
echo "테스트 정리 중...\n";

$pdo->exec("DELETE FROM friendships WHERE user_id_a IN (2001, 2002, 2003, 2004) OR user_id_b IN (2001, 2002, 2003, 2004)");
$pdo->exec("DELETE FROM users WHERE id IN (2001, 2002, 2003, 2004)");

echo "✓ 테스트 데이터 정리 완료\n\n";

// ============================================================================
// 테스트 완료
// ============================================================================
echo "========================================\n";
echo "✅ 모든 테스트 통과!\n";
echo "========================================\n";
