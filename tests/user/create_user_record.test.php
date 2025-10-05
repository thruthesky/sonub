<?php
/**
 * create_user_record() 함수 테스트
 *
 * 실행 방법: php tests/user/create_user_record.test.php
 */

// 필수: init.php 포함
include __DIR__ . '/../../init.php';

echo "=== create_user_record() 함수 테스트 시작 ===\n\n";

// 테스트 1: DB 연결 확인
echo "테스트 1: DB 연결 확인\n";
try {
    $conn = db_connection();
    echo "✅ DB 연결 성공\n";
    echo "   호스트: " . DB_HOST . "\n";
    echo "   데이터베이스: " . DB_NAME . "\n\n";
} catch (Exception $e) {
    echo "❌ DB 연결 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// 테스트 2: firebase_uid 누락 시 에러
echo "테스트 2: firebase_uid 누락 시 에러 반환\n";
$result = create_user_record([]);
if (isset($result['error_code']) && $result['error_code'] === 'firebase-uid-required') {
    echo "✅ 예상된 에러 반환: " . $result['error_message'] . "\n\n";
} else {
    echo "❌ 예상과 다른 결과: " . print_r($result, true) . "\n";
    exit(1);
}

// 테스트 3: 새 사용자 생성 성공
echo "테스트 3: 새 사용자 생성\n";
$testFirebaseUid = 'test_firebase_' . time();
$testDisplayName = '테스트사용자_' . time();

$newUser = create_user_record([
    'firebase_uid' => $testFirebaseUid,
    'display_name' => $testDisplayName,
    'birthday' => strtotime('1990-01-01'),
    'gender' => 'M'
]);

if (isset($newUser['error_code'])) {
    echo "❌ 사용자 생성 실패: " . $newUser['error_message'] . "\n";
    exit(1);
}

if ($newUser['firebase_uid'] === $testFirebaseUid && $newUser['display_name'] === $testDisplayName) {
    echo "✅ 사용자 생성 성공\n";
    echo "   ID: " . $newUser['id'] . "\n";
    echo "   Firebase UID: " . $newUser['firebase_uid'] . "\n";
    echo "   Display Name: " . $newUser['display_name'] . "\n";
    echo "   Birthday: " . $newUser['birthday'] . "\n";
    echo "   Gender: " . $newUser['gender'] . "\n\n";

    $createdUserId = $newUser['id'];
} else {
    echo "❌ 생성된 데이터가 일치하지 않음\n";
    print_r($newUser);
    exit(1);
}

// 테스트 4: 이미 존재하는 사용자 생성 시도
echo "테스트 4: 이미 존재하는 사용자 생성 시도\n";
$duplicateResult = create_user_record([
    'firebase_uid' => $testFirebaseUid,
    'display_name' => '중복사용자'
]);

if (isset($duplicateResult['error_code']) && $duplicateResult['error_code'] === 'user-already-exists') {
    echo "✅ 예상된 에러 반환: " . $duplicateResult['error_message'] . "\n\n";
} else {
    echo "❌ 중복 사용자 검증 실패\n";
    print_r($duplicateResult);
    exit(1);
}

// 테스트 5: 최소 정보로 사용자 생성 (firebase_uid만)
echo "테스트 5: 최소 정보로 사용자 생성 (firebase_uid만)\n";
$minimalFirebaseUid = 'test_minimal_' . time();

$minimalUser = create_user_record([
    'firebase_uid' => $minimalFirebaseUid
]);

if (isset($minimalUser['error_code'])) {
    echo "❌ 최소 정보 사용자 생성 실패: " . $minimalUser['error_message'] . "\n";
    exit(1);
}

if ($minimalUser['firebase_uid'] === $minimalFirebaseUid) {
    echo "✅ 최소 정보 사용자 생성 성공\n";
    echo "   ID: " . $minimalUser['id'] . "\n";
    echo "   Firebase UID: " . $minimalUser['firebase_uid'] . "\n";
    echo "   Display Name: " . ($minimalUser['display_name'] ?: '(비어있음)') . "\n\n";

    $minimalUserId = $minimalUser['id'];
} else {
    echo "❌ 생성된 데이터가 일치하지 않음\n";
    exit(1);
}

// 테스트 데이터 정리
echo "테스트 데이터 정리 중...\n";
try {
    // 생성된 테스트 사용자들 삭제
    db()->delete()->from('users')->where('id = ?', [$createdUserId])->execute();
    db()->delete()->from('users')->where('id = ?', [$minimalUserId])->execute();
    echo "✅ 테스트 데이터 삭제 완료\n\n";
} catch (Exception $e) {
    echo "⚠️  테스트 데이터 삭제 중 오류: " . $e->getMessage() . "\n\n";
}

echo "🎉 모든 테스트 통과!\n";
