<?php
/**
 * get_user() 함수 테스트
 *
 * 실행 방법: php tests/user/get_user.test.php
 */

// 필수: init.php 포함
include __DIR__ . '/../../init.php';

echo "=== get_user() 함수 테스트 시작 ===\n\n";

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

// 테스트 2: users 테이블 존재 확인
echo "테스트 2: users 테이블 존재 확인\n";
try {
    $result = db()->select('COUNT(*) as cnt')->from('users')->first();
    echo "✅ users 테이블 존재 (레코드 수: " . $result['cnt'] . ")\n\n";
} catch (Exception $e) {
    echo "❌ users 테이블 조회 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// 테스트 3: get_user() 함수 호출 - 존재하지 않는 ID
echo "테스트 3: get_user() 함수 호출 - 존재하지 않는 ID (99999)\n";
try {
    $user = get_user(['id' => 99999]);
    if ($user === null) {
        echo "✅ null 반환 성공 (사용자 없음)\n\n";
    } else {
        echo "❌ 예상과 다른 결과: " . print_r($user, true) . "\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ get_user() 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// 테스트 4: get_user() 함수 호출 - ID 파라미터 누락
echo "테스트 4: get_user() 함수 호출 - ID 파라미터 누락\n";
$result = get_user([]);
if (isset($result['error_code']) && $result['error_code'] === 'input-id-empty') {
    echo "✅ 예상된 오류 반환: " . $result['error_message'] . "\n\n";
} else {
    echo "❌ 예상과 다른 결과: " . print_r($result, true) . "\n";
    exit(1);
}

// 테스트 5: 테스트 데이터 생성 후 조회
echo "테스트 5: 테스트 사용자 생성 및 조회\n";
try {
    // 테스트 사용자 데이터 삽입
    $testUid = 'test_uid_' . time();
    $testDisplayName = '테스트사용자_' . time();
    $insertId = db()
        ->insert([
            'firebase_uid' => $testUid,
            'display_name' => $testDisplayName,
            'created_at' => time(),
            'updated_at' => time(),
            'birthday' => 0,
            'gender' => 'M'
        ])
        ->into('users');

    echo "✅ 테스트 사용자 생성 성공 (ID: {$insertId})\n";

    // get_user()로 조회
    $user = get_user(['id' => $insertId]);

    if ($user && $user['firebase_uid'] === $testUid && $user['display_name'] === $testDisplayName) {
        echo "✅ get_user() 조회 성공\n";
        echo "   ID: " . $user['id'] . "\n";
        echo "   Firebase UID: " . $user['firebase_uid'] . "\n";
        echo "   Display Name: " . $user['display_name'] . "\n";
    } else {
        echo "❌ 조회된 데이터가 일치하지 않음\n";
        print_r($user);
        exit(1);
    }

    // 테스트 데이터 삭제
    db()->delete()->from('users')->where('id = ?', [$insertId])->execute();
    echo "✅ 테스트 데이터 삭제 완료\n\n";

} catch (Exception $e) {
    echo "❌ 테스트 실패: " . $e->getMessage() . "\n";
    exit(1);
}

echo "🎉 모든 테스트 통과!\n";
