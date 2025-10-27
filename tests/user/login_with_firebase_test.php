<?php

/**
 * login_with_firebase 함수 테스트 (phone_number 필수 파라미터 추가)
 *
 * 테스트 시나리오:
 * 1. firebase_uid 누락 - 에러
 * 2. phone_number 누락 - 에러
 * 3. 신규 사용자 생성 (firebase_uid + phone_number)
 * 4. 기존 사용자 로그인 (phone_number 일치)
 * 5. 기존 사용자 로그인 실패 (phone_number 불일치)
 * 6. 세션 쿠키 검증
 */

require_once __DIR__ . '/../../init.php';

echo "🧪 login_with_firebase 함수 테스트 시작\n\n";

// ============================================
// 테스트 데이터 준비
// ============================================
$testFirebaseUid = 'test_firebase_' . time() . '_' . rand(1000, 9999);
$testPhoneNumber = '010-' . rand(1000, 9999) . '-' . rand(1000, 9999);
echo "테스트 데이터:\n";
echo "  - Firebase UID: $testFirebaseUid\n";
echo "  - Phone Number: $testPhoneNumber\n";
echo "\n";

// ============================================
// 테스트 1: firebase_uid 누락 에러
// ============================================
echo "📝 테스트 1: firebase_uid 누락\n";
try {
    $result = login_with_firebase([]);
    echo "❌ 실패: 에러가 발생하지 않음\n";
    exit(1);
} catch (ApiException $e) {
    echo "✅ 성공: ApiException 발생 (예상된 에러)\n";
    echo "   에러 메시지: " . $e->getMessage() . "\n";
}
echo "\n";

// ============================================
// 테스트 2: phone_number 누락 에러
// ============================================
echo "📝 테스트 2: phone_number 누락\n";
try {
    $result = login_with_firebase(['firebase_uid' => $testFirebaseUid]);
    echo "❌ 실패: 에러가 발생하지 않음\n";
    exit(1);
} catch (ApiException $e) {
    echo "✅ 성공: ApiException 발생 (예상된 에러)\n";
    echo "   에러 메시지: " . $e->getMessage() . "\n";
}
echo "\n";

// ============================================
// 테스트 3: 신규 사용자 생성
// ============================================
echo "📝 테스트 3: 신규 사용자 생성 (firebase_uid + phone_number)\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'phone_number' => $testPhoneNumber,
        'first_name' => '테스트',
        'last_name' => '사용자',
        'gender' => 'M'
    ]);

    if (!isset($result['id']) || empty($result['id'])) {
        echo "❌ 실패: 사용자 ID가 없음\n";
        exit(1);
    }

    if ($result['firebase_uid'] !== $testFirebaseUid) {
        echo "❌ 실패: Firebase UID 불일치\n";
        exit(1);
    }

    if ($result['phone_number'] !== $testPhoneNumber) {
        echo "❌ 실패: 전화번호 불일치\n";
        exit(1);
    }

    $createdUserId = $result['id'];
    echo "✅ 성공: 신규 사용자 생성됨\n";
    echo "   - 사용자 ID: " . $result['id'] . "\n";
    echo "   - Firebase UID: " . $result['firebase_uid'] . "\n";
    echo "   - 전화번호: " . $result['phone_number'] . "\n";
    echo "   - 이름: " . $result['first_name'] . " " . $result['last_name'] . "\n";
} catch (ApiException $e) {
    echo "❌ 실패: ApiException 발생\n";
    echo "   에러: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 4: 기존 사용자 로그인 (phone_number 일치)
// ============================================
echo "📝 테스트 4: 기존 사용자 로그인 (phone_number 일치)\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'phone_number' => $testPhoneNumber,
        'first_name' => '다른',
        'last_name' => '이름'
    ]);

    if ($result['id'] !== $createdUserId) {
        echo "❌ 실패: 다른 사용자 ID가 반환됨 (중복 생성)\n";
        echo "   기존 ID: $createdUserId\n";
        echo "   반환된 ID: " . $result['id'] . "\n";
        exit(1);
    }

    if ($result['phone_number'] !== $testPhoneNumber) {
        echo "❌ 실패: 전화번호 불일치\n";
        exit(1);
    }

    echo "✅ 성공: 기존 사용자 반환됨 (중복 생성 없음)\n";
    echo "   - 사용자 ID: " . $result['id'] . "\n";
    echo "   - 전화번호: " . $result['phone_number'] . "\n";
} catch (ApiException $e) {
    echo "❌ 실패: ApiException 발생\n";
    echo "   에러: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 5: 기존 사용자 로그인 실패 (phone_number 불일치)
// ============================================
echo "📝 테스트 5: 기존 사용자 로그인 실패 (phone_number 불일치)\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid,
        'phone_number' => '010-9999-9999'  // 다른 전화번호
    ]);

    // phone-number-mismatch 에러인지 확인
    if (isset($result['error_code']) && $result['error_code'] === 'phone-number-mismatch') {
        echo "✅ 성공: phone-number-mismatch 에러 발생\n";
        echo "   - 에러 코드: " . $result['error_code'] . "\n";
        echo "   - 에러 메시지: " . $result['error_message'] . "\n";
    } else {
        echo "❌ 실패: 예상된 에러가 발생하지 않음\n";
        echo "   반환값: " . json_encode($result) . "\n";
        exit(1);
    }
} catch (ApiException $e) {
    // phone-number-mismatch는 ApiException으로 throw됨
    if (strpos($e->getMessage(), '전화번호가 일치하지 않습니다') !== false) {
        echo "✅ 성공: phone-number-mismatch 에러 발생 (ApiException)\n";
        echo "   - 에러 메시지: " . $e->getMessage() . "\n";
    } else {
        echo "❌ 실패: 다른 ApiException 발생\n";
        echo "   에러: " . $e->getMessage() . "\n";
        exit(1);
    }
}
echo "\n";

// ============================================
// 테스트 6: 세션 쿠키 검증
// ============================================
echo "📝 테스트 6: 세션 쿠키 설정 검증\n";
$sessionBefore = isset($_COOKIE[SESSION_ID]) ? $_COOKIE[SESSION_ID] : null;

try {
    $testFirebaseUid2 = 'test_firebase_cookie_' . time();
    $testPhoneNumber2 = '010-8888-8888';

    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid2,
        'phone_number' => $testPhoneNumber2,
        'first_name' => '쿠키',
        'last_name' => '테스트'
    ]);

    $sessionAfter = isset($_COOKIE[SESSION_ID]) ? $_COOKIE[SESSION_ID] : null;

    if ($sessionAfter) {
        echo "✅ 성공: 세션 쿠키가 설정됨\n";
        echo "   - 쿠키 이름: " . SESSION_ID . "\n";
        echo "   - 사용자 ID: " . $result['id'] . "\n";
    } else {
        echo "⚠️  주의: 세션 쿠키 미설정 (header 제약 가능성)\n";
    }

    $createdUserId2 = $result['id'];
} catch (ApiException $e) {
    echo "❌ 실패: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 정리: 테스트 데이터 삭제
// ============================================
echo "테스트 정리: 생성된 데이터 삭제\n";
try {
    db()->delete()->from('users')->where('id = ?', [$createdUserId])->execute();
    if (isset($createdUserId2)) {
        db()->delete()->from('users')->where('id = ?', [$createdUserId2])->execute();
    }
    echo "✅ 테스트 데이터 삭제 완료\n";
} catch (Exception $e) {
    echo "⚠️  데이터 삭제 중 오류: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎉 모든 테스트 완료!\n";
