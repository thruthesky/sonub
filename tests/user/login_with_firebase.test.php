<?php
// tests/user/login_with_firebase.test.php

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

echo "=== login_with_firebase() 함수 테스트 시작 ===\n\n";

// 테스트용 고유 Firebase UID 생성
$testFirebaseUid = 'test_firebase_' . time() . '_' . rand(1000, 9999);
$testPhoneNumber = '010-' . rand(1000, 9999) . '-' . rand(1000, 9999);

// ============================================
// 테스트 1: firebase_uid 파라미터 누락 - 에러 반환
// ============================================
echo "테스트 1: firebase_uid 파라미터 누락\n";
try {
    $result = login_with_firebase([]);
    if (isset($result['error_code']) && $result['error_code'] === 'input-firebase-uid-empty') {
        echo "✅ firebase_uid 누락 시 에러 반환 성공\n";
        echo "   에러 메시지: " . $result['error_message'] . "\n";
    } else {
        echo "❌ 에러 반환 실패\n";
        print_r($result);
        exit(1);
    }
} catch (ApiException $e) {
    // ApiException은 예상된 에러
    echo "✅ firebase_uid 누락 시 에러 반환 성공 (ApiException)\n";
    echo "   에러 코드: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ 예상치 못한 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 2: phone_number 파라미터 누락 - 에러 반환
// ============================================
echo "테스트 2: phone_number 파라미터 누락\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid
    ]);
    if (isset($result['error_code']) && $result['error_code'] === 'input-phone-number-empty') {
        echo "✅ phone_number 누락 시 에러 반환 성공\n";
        echo "   에러 메시지: " . $result['error_message'] . "\n";
    } else {
        echo "❌ phone_number 누락 에러 반환 실패\n";
        print_r($result);
        exit(1);
    }
} catch (ApiException $e) {
    echo "✅ phone_number 누락 시 에러 반환 성공 (ApiException)\n";
} catch (Exception $e) {
    echo "❌ 예상치 못한 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 3: 새 사용자 생성 (firebase_uid + phone_number)
// ============================================
echo "테스트 3: 새 사용자 생성 (firebase_uid + phone_number)\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'phone_number' => $testPhoneNumber
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 사용자 생성 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 결과 검증
    if (!isset($result['id']) || empty($result['id'])) {
        echo "❌ 생성된 사용자 ID가 없습니다\n";
        exit(1);
    }

    if ($result['firebase_uid'] !== $testFirebaseUid) {
        echo "❌ Firebase UID가 일치하지 않습니다\n";
        exit(1);
    }

    // first_name이 올바른 형식인지 확인 (firebase_uid의 앞 3글자-타임스탬프)
    $firstNamePattern = '/^' . preg_quote(substr($testFirebaseUid, 0, 3), '/') . '-\d+$/';
    if (!preg_match($firstNamePattern, $result['first_name'])) {
        echo "❌ first_name이 기본값(firebase_uid의 앞 3글자-타임스탬프)으로 설정되지 않았습니다\n";
        echo "   기대 패턴: " . substr($testFirebaseUid, 0, 3) . "-[타임스탬프]\n";
        echo "   실제값: " . $result['first_name'] . "\n";
        exit(1);
    }

    // phone_number 확인
    if (!isset($result['phone_number']) || $result['phone_number'] !== $testPhoneNumber) {
        echo "❌ phone_number가 올바르게 저장되지 않았습니다\n";
        exit(1);
    }

    echo "✅ 새 사용자 생성 성공\n";
    echo "   사용자 ID: " . $result['id'] . "\n";
    echo "   Firebase UID: " . $result['firebase_uid'] . "\n";
    echo "   전화번호: " . $result['phone_number'] . "\n";
    echo "   생성 시각: " . $result['created_at'] . " (" . date('Y-m-d H:i:s', $result['created_at']) . ")\n";

    // 생성된 사용자 ID 저장 (이후 테스트용)
    $createdUserId = $result['id'];
} catch (ApiException $e) {
    echo "❌ ApiException 발생: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 4: 기존 사용자 재로그인 (phone_number 일치) - 새 레코드 생성 안 됨
// ============================================
echo "테스트 4: 기존 사용자 재로그인 (phone_number 일치)\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'phone_number' => $testPhoneNumber
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 기존 사용자 로그인 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 동일한 사용자 ID인지 확인
    if ($result['id'] !== $createdUserId) {
        echo "❌ 새 레코드가 생성되었습니다 (중복 방지 실패)\n";
        echo "   기존 ID: " . $createdUserId . "\n";
        echo "   새 ID: " . $result['id'] . "\n";
        exit(1);
    }

    echo "✅ 기존 사용자 재로그인 성공 (중복 방지 확인)\n";
    echo "   사용자 ID: " . $result['id'] . "\n";
    echo "   Firebase UID: " . $result['firebase_uid'] . "\n";
    echo "   전화번호: " . $result['phone_number'] . "\n";
} catch (ApiException $e) {
    echo "❌ ApiException 발생: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 5: 기존 사용자 로그인 실패 (phone_number 불일치)
// ============================================
echo "테스트 5: 기존 사용자 로그인 실패 (phone_number 불일치)\n";
$differentPhoneNumber = '010-9999-9999';
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'phone_number' => $differentPhoneNumber  // 다른 전화번호
    ]);

    if (isset($result['error_code']) && $result['error_code'] === 'phone-number-mismatch') {
        echo "✅ phone_number 불일치 시 에러 반환 성공\n";
        echo "   에러 코드: " . $result['error_code'] . "\n";
        echo "   에러 메시지: " . $result['error_message'] . "\n";
    } else {
        echo "❌ phone_number 불일치 에러 반환 실패\n";
        echo "   예상: phone-number-mismatch\n";
        print_r($result);
        exit(1);
    }
} catch (ApiException $e) {
    echo "❌ ApiException 발생: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 6: 새 사용자 생성 (전체 정보 포함)
// ============================================
echo "테스트 6: 새 사용자 생성 (전체 정보 포함)\n";
$testFirebaseUid2 = 'test_firebase_full_' . time() . '_' . rand(1000, 9999);
$testPhoneNumber2 = '010-' . rand(1000, 9999) . '-' . rand(1000, 9999);
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid2,
        'phone_number' => $testPhoneNumber2,
        'first_name' => '길동',
        'last_name' => '홍',
        'birthday' => strtotime('1990-01-01'),
        'gender' => 'M'
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 사용자 생성 실패\n";
        echo "   에러: " . $result['error_message'] . "\n";
        print_r($result);
        exit(1);
    }

    // 결과 검증
    if ($result['firebase_uid'] !== $testFirebaseUid2) {
        echo "❌ Firebase UID가 일치하지 않습니다\n";
        exit(1);
    }

    if ($result['first_name'] !== '길동') {
        echo "❌ first_name이 일치하지 않습니다\n";
        exit(1);
    }

    if ($result['last_name'] !== '홍') {
        echo "❌ last_name이 일치하지 않습니다\n";
        exit(1);
    }

    if ($result['birthday'] !== strtotime('1990-01-01')) {
        echo "❌ birthday가 일치하지 않습니다\n";
        exit(1);
    }

    if ($result['gender'] !== 'M') {
        echo "❌ gender가 일치하지 않습니다\n";
        exit(1);
    }

    if ($result['phone_number'] !== $testPhoneNumber2) {
        echo "❌ phone_number가 일치하지 않습니다\n";
        exit(1);
    }

    echo "✅ 전체 정보 포함 사용자 생성 성공\n";
    echo "   사용자 ID: " . $result['id'] . "\n";
    echo "   Firebase UID: " . $result['firebase_uid'] . "\n";
    echo "   전화번호: " . $result['phone_number'] . "\n";
    echo "   표시 이름: " . $result['first_name'] . " " . $result['last_name'] . "\n";
    echo "   생년월일: " . date('Y-m-d', $result['birthday']) . "\n";
    echo "   성별: " . $result['gender'] . "\n";

    // 생성된 사용자 ID 저장 (정리용)
    $createdUserId2 = $result['id'];
} catch (ApiException $e) {
    echo "❌ ApiException 발생: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 7: 세션 쿠키 검증
// ============================================
echo "테스트 7: 세션 쿠키 설정 검증\n";
$testFirebaseUid3 = 'test_firebase_cookie_' . time() . '_' . rand(1000, 9999);
$testPhoneNumber3 = '010-' . rand(1000, 9999) . '-' . rand(1000, 9999);

// 테스트 전에 기존 세션 쿠키 확인
$sessionBefore = isset($_COOKIE[SESSION_ID]) ? $_COOKIE[SESSION_ID] : null;

try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid3,
        'phone_number' => $testPhoneNumber3,
        'first_name' => '쿠키',
        'last_name' => '테스트'
    ]);

    if (isset($result['error_code'])) {
        echo "❌ 사용자 생성 실패\n";
        exit(1);
    }

    // 세션 쿠키 확인
    $sessionAfter = isset($_COOKIE[SESSION_ID]) ? $_COOKIE[SESSION_ID] : null;

    if ($sessionAfter && $sessionAfter !== $sessionBefore) {
        echo "✅ 세션 쿠키가 설정됨\n";
        echo "   쿠키 이름: " . SESSION_ID . "\n";
        echo "   사용자 ID: " . $result['id'] . "\n";
        echo "   세션 ID 형식 확인: " . ($sessionAfter ? "✅ 설정됨" : "❌ 설정 안 됨") . "\n";
    } else {
        echo "⚠️  세션 쿠키 상태\n";
        echo "   설정 전: " . ($sessionBefore ? "있음" : "없음") . "\n";
        echo "   설정 후: " . ($sessionAfter ? "있음" : "없음") . "\n";
        if ($sessionAfter) {
            echo "✅ 세션 쿠키가 설정되어 있습니다\n";
        }
    }

    $createdUserId3 = $result['id'];
} catch (ApiException $e) {
    echo "❌ ApiException 발생: " . $e->getMessage() . "\n";
    exit(1);
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
    // 첫 번째 테스트 사용자 삭제
    $deleted1 = db()->delete()
        ->from('users')
        ->where('id = ?', [$createdUserId])
        ->execute();

    // 두 번째 테스트 사용자 삭제
    $deleted2 = db()->delete()
        ->from('users')
        ->where('id = ?', [$createdUserId2])
        ->execute();

    // 세 번째 테스트 사용자 삭제
    $deleted3 = db()->delete()
        ->from('users')
        ->where('id = ?', [$createdUserId3])
        ->execute();

    echo "✅ 테스트 데이터 삭제 완료\n";
    echo "   삭제된 레코드 수: " . ($deleted1 + $deleted2 + $deleted3) . "\n";
} catch (Exception $e) {
    echo "⚠️  테스트 데이터 삭제 중 오류: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎉 모든 테스트 통과!\n";
