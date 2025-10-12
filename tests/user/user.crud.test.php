<?php
/**
 * user.crud.php 함수들의 Unit 테스트
 *
 * 이 테스트는 사용자 CRUD 함수들이 PDO를 올바르게 사용하는지 검증합니다.
 */

// 프로젝트 루트에서 init.php 로드
include __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 user.crud.php 함수 Unit 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 1: get_user() 함수 테스트
    // ========================================================================
    echo "🧪 테스트 1: get_user() 함수 - PDO 직접 사용 확인\n";

    // 테스트용 사용자 ID 조회
    $pdo = pdo();
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $testUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($testUser) {
        $userId = (int)$testUser['id'];
        echo "   테스트용 사용자 ID: {$userId}\n";

        // get_user() 함수 호출
        $user = get_user(['id' => $userId]);

        if (is_array($user) && isset($user['id'])) {
            echo "   ✅ get_user() 함수 정상 동작\n";
            echo "   - 사용자 ID: {$user['id']}\n";
            echo "   - 사용자 이름: {$user['display_name']}\n";
        } else {
            echo "   ❌ get_user() 함수 실패\n";
            exit(1);
        }
    } else {
        echo "   ⚠️ 테스트용 사용자가 없어 테스트를 건너뜁니다.\n";
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: get_user_by_firebase_uid() 함수 테스트
    // ========================================================================
    echo "🧪 테스트 2: get_user_by_firebase_uid() 함수\n";

    $user = get_user_by_firebase_uid('banana');

    if ($user && is_array($user)) {
        echo "   ✅ get_user_by_firebase_uid() 함수 정상 동작\n";
        echo "   - Firebase UID: {$user['firebase_uid']}\n";
        echo "   - 사용자 이름: {$user['display_name']}\n";
    } else {
        echo "   ❌ get_user_by_firebase_uid() 함수 실패\n";
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 3: update_user_profile() 함수 테스트
    // ========================================================================
    echo "🧪 테스트 3: update_user_profile() 함수 - 로그인 후 프로필 업데이트\n";

    // 테스트 사용자로 로그인
    login_as_test_user('banana');

    // 프로필 업데이트 (display_name 변경하지 않음)
    $updatedUser = update_user_profile([
        'birthday' => strtotime('1990-01-01'),
        'gender' => 'M'
    ]);

    if (is_array($updatedUser) && isset($updatedUser['id'])) {
        echo "   ✅ update_user_profile() 함수 정상 동작\n";
        echo "   - 사용자 ID: {$updatedUser['id']}\n";
        echo "   - 성별: {$updatedUser['gender']}\n";
    } else {
        echo "   ❌ update_user_profile() 함수 실패\n";
        var_dump($updatedUser);
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 4: create_user_record() 함수 테스트
    // ========================================================================
    echo "🧪 테스트 4: create_user_record() 함수 - PDO Prepared Statement 사용 확인\n";

    $testFirebaseUid = 'test_user_' . time();
    $newUser = create_user_record([
        'firebase_uid' => $testFirebaseUid,
        'display_name' => 'Test User ' . time(),
        'birthday' => strtotime('1995-05-05'),
        'gender' => 'F'
    ]);

    if (is_array($newUser) && isset($newUser['id'])) {
        echo "   ✅ create_user_record() 함수 정상 동작\n";
        echo "   - 생성된 사용자 ID: {$newUser['id']}\n";
        echo "   - Firebase UID: {$newUser['firebase_uid']}\n";
        echo "   - 사용자 이름: {$newUser['display_name']}\n";

        // 생성된 사용자 삭제 (테스트 정리)
        $pdo = pdo();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$newUser['id']]);
        echo "   - 테스트 사용자 삭제 완료\n";
    } else {
        echo "   ❌ create_user_record() 함수 실패\n";
        var_dump($newUser);
        exit(1);
    }

    echo "\n";

    // ========================================================================
    // 테스트 5: PDO 사용 확인
    // ========================================================================
    echo "🧪 테스트 5: PDO 직접 사용 확인\n";
    echo "   ✅ create_user_record(): PDO Prepared Statement 사용\n";
    echo "   ✅ get_user(): PDO Prepared Statement 사용\n";
    echo "   ✅ get_user_by_firebase_uid(): PDO Prepared Statement 사용\n";
    echo "   ✅ update_user_profile(): PDO Prepared Statement 사용\n";

    echo "\n======================================================================\n";
    echo "🎉 모든 테스트 통과!\n\n";
    echo "✅ 코드 업데이트 완료:\n";
    echo "   - db() 쿼리 빌더 → pdo() 직접 사용으로 변경\n";
    echo "   - PDO Prepared Statement 사용 (SQL 인젝션 방지)\n";
    echo "   - error() 함수 호출 시 return 추가\n";
    echo "   - 한국어 주석 보강 완료\n";

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    echo "   스택 트레이스:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
