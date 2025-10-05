<?php
// tests/user/update_user_profile.test.php

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

echo "=== update_user_profile() 함수 테스트 시작 ===\n\n";

// 테스트용 사용자 생성
$testFirebaseUid = 'test_update_profile_' . time() . '_' . rand(1000, 9999);
$testUser = null;
$createdUserId = null;

// ============================================
// 준비: 테스트용 사용자 생성
// ============================================
echo "준비: 테스트용 사용자 생성\n";
try {
    $testUser = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'display_name' => '테스트사용자',
        'birthday' => strtotime('1990-01-01'),
        'gender' => 'M'
    ]);

    if (isset($testUser['error_code'])) {
        echo "❌ 테스트 사용자 생성 실패\n";
        print_r($testUser);
        exit(1);
    }

    $createdUserId = $testUser['id'];
    echo "✅ 테스트 사용자 생성 성공 (ID: {$createdUserId})\n";
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 1: 로그인하지 않은 상태에서 호출 - 에러 반환
// ============================================
echo "테스트 1: 로그인하지 않은 상태에서 프로필 업데이트\n";
try {
    // 세션 쿠키 제거 (로그아웃 시뮬레이션)
    unset($_COOKIE[SESSION_ID]);

    $result = update_user_profile([
        'display_name' => '새로운이름'
    ]);

    if (isset($result['error_code']) && $result['error_code'] === 'user-not-logged-in') {
        echo "✅ 로그인 필요 에러 반환 성공\n";
        echo "   에러 메시지: " . $result['error_message'] . "\n";
    } else {
        echo "❌ 에러 반환 실패\n";
        print_r($result);
        exit(1);
    }

    // 세션 쿠키 복원
    $_COOKIE[SESSION_ID] = generate_session_id($testUser);
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 2: display_name만 업데이트
// ============================================
echo "테스트 2: display_name만 업데이트\n";
try {
    $newDisplayName = '업데이트된이름_' . time();
    $result = update_user_profile([
        'display_name' => $newDisplayName
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 프로필 업데이트 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 결과 검증
    if ($result['display_name'] !== $newDisplayName) {
        echo "❌ display_name이 업데이트되지 않았습니다\n";
        echo "   기대값: " . $newDisplayName . "\n";
        echo "   실제값: " . $result['display_name'] . "\n";
        exit(1);
    }

    // updated_at이 업데이트되었는지 확인
    if ($result['updated_at'] < $testUser['updated_at']) {
        echo "❌ updated_at이 업데이트되지 않았습니다\n";
        echo "   이전 updated_at: " . $testUser['updated_at'] . "\n";
        echo "   현재 updated_at: " . $result['updated_at'] . "\n";
        exit(1);
    }

    echo "✅ display_name 업데이트 성공\n";
    echo "   새 이름: " . $result['display_name'] . "\n";
    echo "   업데이트 시각: " . date('Y-m-d H:i:s', $result['updated_at']) . "\n";
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 3: birthday와 gender 업데이트
// ============================================
echo "테스트 3: birthday와 gender 업데이트\n";
try {
    $newBirthday = strtotime('1995-05-15');
    $newGender = 'F';

    $result = update_user_profile([
        'birthday' => $newBirthday,
        'gender' => $newGender
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 프로필 업데이트 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 결과 검증
    if ($result['birthday'] !== $newBirthday) {
        echo "❌ birthday가 업데이트되지 않았습니다\n";
        echo "   기대값: " . $newBirthday . "\n";
        echo "   실제값: " . $result['birthday'] . "\n";
        exit(1);
    }

    if ($result['gender'] !== $newGender) {
        echo "❌ gender가 업데이트되지 않았습니다\n";
        echo "   기대값: " . $newGender . "\n";
        echo "   실제값: " . $result['gender'] . "\n";
        exit(1);
    }

    echo "✅ birthday와 gender 업데이트 성공\n";
    echo "   생년월일: " . date('Y-m-d', $result['birthday']) . "\n";
    echo "   성별: " . $result['gender'] . "\n";
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 4: 모든 필드 한 번에 업데이트
// ============================================
echo "테스트 4: 모든 필드 한 번에 업데이트\n";
try {
    $finalDisplayName = '최종이름_' . time();
    $finalBirthday = strtotime('2000-12-25');
    $finalGender = 'M';

    $result = update_user_profile([
        'display_name' => $finalDisplayName,
        'birthday' => $finalBirthday,
        'gender' => $finalGender
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 프로필 업데이트 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 결과 검증
    if ($result['display_name'] !== $finalDisplayName) {
        echo "❌ display_name이 업데이트되지 않았습니다\n";
        exit(1);
    }

    if ($result['birthday'] !== $finalBirthday) {
        echo "❌ birthday가 업데이트되지 않았습니다\n";
        exit(1);
    }

    if ($result['gender'] !== $finalGender) {
        echo "❌ gender가 업데이트되지 않았습니다\n";
        exit(1);
    }

    echo "✅ 모든 필드 업데이트 성공\n";
    echo "   표시 이름: " . $result['display_name'] . "\n";
    echo "   생년월일: " . date('Y-m-d', $result['birthday']) . "\n";
    echo "   성별: " . $result['gender'] . "\n";
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 5: display_name 중복 검사
// ============================================
echo "테스트 5: display_name 중복 검사\n";
try {
    // 다른 사용자 생성
    $testFirebaseUid2 = 'test_update_profile_2_' . time() . '_' . rand(1000, 9999);
    $testUser2 = login_with_firebase([
        'firebase_uid' => $testFirebaseUid2,
        'display_name' => '다른사용자_' . time()
    ]);

    if (isset($testUser2['error_code'])) {
        echo "❌ 두 번째 테스트 사용자 생성 실패\n";
        exit(1);
    }

    $createdUserId2 = $testUser2['id'];

    // 첫 번째 사용자의 세션으로 복원
    $_COOKIE[SESSION_ID] = generate_session_id($testUser);

    // 두 번째 사용자의 display_name으로 업데이트 시도
    $result = update_user_profile([
        'display_name' => $testUser2['display_name']
    ]);

    if (isset($result['error_code']) && $result['error_code'] === 'display-name-already-exists') {
        echo "✅ display_name 중복 검사 성공\n";
        echo "   에러 메시지: " . $result['error_message'] . "\n";
    } else {
        echo "❌ 중복 검사 실패 - 에러가 반환되지 않았습니다\n";
        print_r($result);
        exit(1);
    }

    // 두 번째 사용자 삭제
    db()->delete()
        ->from('users')
        ->where('id = ?', [$createdUserId2])
        ->execute();

} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 6: 빈 값 전달 시 업데이트 안 됨
// ============================================
echo "테스트 6: 빈 display_name 전달 시 업데이트 안 됨\n";
try {
    // 현재 display_name 저장
    $currentUser = db()->select('*')
        ->from('users')
        ->where('id = ?', [$createdUserId])
        ->first();
    $currentDisplayName = $currentUser['display_name'];

    // 빈 display_name으로 업데이트 시도
    $result = update_user_profile([
        'display_name' => ''
    ]);

    // display_name이 변경되지 않았는지 확인
    if ($result['display_name'] === $currentDisplayName) {
        echo "✅ 빈 값 전달 시 업데이트 안 됨 확인\n";
        echo "   display_name 유지: " . $result['display_name'] . "\n";
    } else {
        echo "❌ display_name이 변경되었습니다\n";
        echo "   이전: " . $currentDisplayName . "\n";
        echo "   현재: " . $result['display_name'] . "\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 정리: 생성된 테스트 데이터 삭제
// ============================================
echo "테스트 정리: 생성된 테스트 데이터 삭제\n";
try {
    $deleted = db()->delete()
        ->from('users')
        ->where('id = ?', [$createdUserId])
        ->execute();

    echo "✅ 테스트 데이터 삭제 완료\n";
    echo "   삭제된 레코드 수: " . $deleted . "\n";
} catch (Exception $e) {
    echo "⚠️  테스트 데이터 삭제 중 오류: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎉 모든 테스트 통과!\n";
