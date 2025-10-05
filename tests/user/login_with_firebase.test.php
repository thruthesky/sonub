<?php
// tests/user/login_with_firebase.test.php

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

echo "=== login_with_firebase() 함수 테스트 시작 ===\n\n";

// 테스트용 고유 Firebase UID 생성
$testFirebaseUid = 'test_firebase_' . time() . '_' . rand(1000, 9999);

// ============================================
// 테스트 1: firebase_uid 파라미터 없음 - 에러 반환
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
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 2: 새 사용자 생성 (필수 정보만)
// ============================================
echo "테스트 2: 새 사용자 생성 (필수 정보만)\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid
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

    // display_name이 올바른 형식인지 확인 (firebase_uid의 앞 3글자-타임스탬프)
    $displayNamePattern = '/^' . preg_quote(substr($testFirebaseUid, 0, 3), '/') . '-\d+$/';
    if (!preg_match($displayNamePattern, $result['display_name'])) {
        echo "❌ display_name이 기본값(firebase_uid의 앞 3글자-타임스탬프)으로 설정되지 않았습니다\n";
        echo "   기대 패턴: " . substr($testFirebaseUid, 0, 3) . "-[타임스탬프]\n";
        echo "   실제값: " . $result['display_name'] . "\n";
        exit(1);
    }

    echo "✅ 새 사용자 생성 성공\n";
    echo "   사용자 ID: " . $result['id'] . "\n";
    echo "   Firebase UID: " . $result['firebase_uid'] . "\n";
    echo "   표시 이름: " . $result['display_name'] . "\n";
    echo "   생성 시각: " . $result['created_at'] . " (" . date('Y-m-d H:i:s', $result['created_at']) . ")\n";

    // 생성된 사용자 ID 저장 (이후 테스트용)
    $createdUserId = $result['id'];
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 3: 기존 사용자 재로그인 - 새 레코드 생성 안 됨
// ============================================
echo "테스트 3: 기존 사용자 재로그인\n";
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid
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
} catch (Exception $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// ============================================
// 테스트 4: 새 사용자 생성 (전체 정보 포함)
// ============================================
echo "테스트 4: 새 사용자 생성 (전체 정보 포함)\n";
$testFirebaseUid2 = 'test_firebase_full_' . time() . '_' . rand(1000, 9999);
try {
    $result = login_with_firebase([
        'firebase_uid' => $testFirebaseUid2,
        'display_name' => '홍길동',
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

    if ($result['display_name'] !== '홍길동') {
        echo "❌ display_name이 일치하지 않습니다\n";
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

    echo "✅ 전체 정보 포함 사용자 생성 성공\n";
    echo "   사용자 ID: " . $result['id'] . "\n";
    echo "   Firebase UID: " . $result['firebase_uid'] . "\n";
    echo "   표시 이름: " . $result['display_name'] . "\n";
    echo "   생년월일: " . date('Y-m-d', $result['birthday']) . "\n";
    echo "   성별: " . $result['gender'] . "\n";

    // 생성된 사용자 ID 저장 (정리용)
    $createdUserId2 = $result['id'];
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

    echo "✅ 테스트 데이터 삭제 완료\n";
    echo "   삭제된 레코드 수: " . ($deleted1 + $deleted2) . "\n";
} catch (Exception $e) {
    echo "⚠️  테스트 데이터 삭제 중 오류: " . $e->getMessage() . "\n";
}
echo "\n";

echo "🎉 모든 테스트 통과!\n";
