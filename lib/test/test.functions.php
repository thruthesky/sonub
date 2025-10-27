<?php

/**
 * 사용자 로그인 테스트 함수
 * 이 함수는 테스트 환경에서만 사용됩니다.
 * 지정된 Firebase UID를 가진 사용자를 로그인 상태로 만듭니다.
 */
function login_as_test_user(string $firebase_uid = 'banana')
{
    $session_id = generate_session_id(get_user_by_firebase_uid($firebase_uid));
    $_COOKIE[SESSION_ID] = $session_id; // For immediate use in the same request
}



/**
 * 테스트용 사용자 계정 생성
 *
 * 이 함수는 개발/테스트 환경에서 미리 정의된 테스트 사용자 계정들을 데이터베이스에 생성합니다.
 * config()->testAccounts()에서 제공하는 계정 정보를 사용하여 users 테이블에 INSERT ... ON DUPLICATE KEY UPDATE 쿼리를 실행합니다.
 *
 * **중요**: 이 함수는 테스트 환경에서만 사용됩니다.
 *
 * @return array 생성된 사용자 정보 배열 (각 사용자의 ID, firebase_uid, first_name 포함)
 *
 * @example
 * // 테스트 사용자 생성
 * $users = create_test_users();
 * // [
 * //     'apple' => ['id' => 102, 'firebase_uid' => 'apple', 'first_name' => 'Apple', ...],
 * //     'banana' => ['id' => 101, 'firebase_uid' => 'banana', 'first_name' => 'Banana', ...],
 * //     ...
 * // ]
 *
 * @throws PDOException
 */
function create_test_users(): array
{
    // config()->testAccounts()에서 테스트 계정 정보 가져오기
    $testAccounts = config()->testAccounts();

    // 생성된 사용자 정보 저장
    $createdUsers = [];

    // 현재 시간을 created_at 값으로 사용 (기존 SQL의 1620000000 대신)
    $createdAt = time();

    // 각 테스트 계정에 대해 사용자 생성
    foreach ($testAccounts as $firebaseUid => $accountInfo) {
        // INSERT ... ON DUPLICATE KEY UPDATE 쿼리 실행
        $pdo = pdo();
        $sql = "
            INSERT INTO users (firebase_uid, first_name, phone_number, created_at)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                first_name = VALUES(first_name),
                phone_number = VALUES(phone_number)
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $firebaseUid,
                $accountInfo['first_name'],
                $accountInfo['phone_number'],
                $createdAt,
            ]);

            // 사용자 조회 (ID와 기타 정보 포함)
            $selectSql = "SELECT * FROM users WHERE firebase_uid = ?";
            $selectStmt = $pdo->prepare($selectSql);
            $selectStmt->execute([$firebaseUid]);
            $user = $selectStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $createdUsers[$firebaseUid] = $user;
            }
        } catch (PDOException $e) {
            throw new PDOException("테스트 사용자 생성 실패 (firebase_uid: $firebaseUid): " . $e->getMessage());
        }
    }

    return $createdUsers;
}
