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
        'first_name' => '테스트사용자',
        'last_name' => '',
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
        'first_name' => '새로운이름'
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
// 테스트 2: first_name만 업데이트
// ============================================
echo "테스트 2: first_name만 업데이트\n";
try {
    $newFirstName = '업데이트된이름_' . time();
    $result = update_user_profile([
        'first_name' => $newFirstName
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 프로필 업데이트 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 결과 검증
    if ($result['first_name'] !== $newFirstName) {
        echo "❌ first_name이 업데이트되지 않았습니다\n";
        echo "   기대값: " . $newFirstName . "\n";
        echo "   실제값: " . $result['first_name'] . "\n";
        exit(1);
    }

    // updated_at이 업데이트되었는지 확인
    if ($result['updated_at'] < $testUser['updated_at']) {
        echo "❌ updated_at이 업데이트되지 않았습니다\n";
        echo "   이전 updated_at: " . $testUser['updated_at'] . "\n";
        echo "   현재 updated_at: " . $result['updated_at'] . "\n";
        exit(1);
    }

    echo "✅ first_name 업데이트 성공\n";
    echo "   새 이름: " . $result['first_name'] . "\n";
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
    $finalFirstName = '최종이름_' . time();
    $finalLastName = '최종';
    $finalBirthday = strtotime('2000-12-25');
    $finalGender = 'M';

    $result = update_user_profile([
        'first_name' => $finalFirstName,
        'last_name' => $finalLastName,
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
    if ($result['first_name'] !== $finalFirstName) {
        echo "❌ first_name이 업데이트되지 않았습니다\n";
        exit(1);
    }

    if ($result['last_name'] !== $finalLastName) {
        echo "❌ last_name이 업데이트되지 않았습니다\n";
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
    echo "   표시 이름: " . $result['first_name'] . " " . $result['last_name'] . "\n";
    echo "   생년월일: " . date('Y-m-d', $result['birthday']) . "\n";
    echo "   성별: " . $result['gender'] . "\n";
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 5: 이름 중복 검사 (테스트 생략 - 이름은 중복 가능)
// ============================================
echo "테스트 5: 이름 중복 검사 (이름은 중복 가능하므로 테스트 생략)\n";
echo "✅ 이름 중복은 허용되므로 테스트를 건너뜁니다.\n";
echo "\n";

// ============================================
// 테스트 6: 빈 값 전달 시 업데이트 안 됨
// ============================================
echo "테스트 6: 빈 first_name 전달 시 업데이트 안 됨\n";
try {
    // 현재 first_name 저장
    $currentUser = db()->select('*')
        ->from('users')
        ->where('id = ?', [$createdUserId])
        ->first();
    $currentFirstName = $currentUser['first_name'];

    // 빈 first_name으로 업데이트 시도
    $result = update_user_profile([
        'first_name' => ''
    ]);

    // first_name이 변경되지 않았는지 확인
    if ($result['first_name'] === $currentFirstName) {
        echo "✅ 빈 값 전달 시 업데이트 안 됨 확인\n";
        echo "   first_name 유지: " . $result['first_name'] . "\n";
    } else {
        echo "❌ first_name이 변경되었습니다\n";
        echo "   이전: " . $currentFirstName . "\n";
        echo "   현재: " . $result['first_name'] . "\n";
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
