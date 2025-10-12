<?php



/**
 * @deprecated 이 함수가 호출될 상황은 없다. 사용자가 로그인 직후, 반드시 `login_with_firebase` 함수를 호출하기 때문이다.
 * 사용자 레코드 생성
 *
 * Firebase에 로그인했지만 users 테이블에 레코드가 없는 경우 레코드를 생성합니다.
 * 사용자 생성 시 자동으로 세션 ID 쿠키를 설정합니다. (유효기간: 1년)
 *
 * @param array $input HTTP 입력 파라미터
 * - $input['firebase_uid']: (필수) Firebase UID
 * - $input['display_name']: (선택) 사용자 표시 이름
 * - $input['birthday']: (선택) 생년월일 (timestamp)
 * - $input['gender']: (선택) 성별 ('M' 또는 'F')
 * @return array 생성된 사용자 정보 배열 또는 에러 배열
 *
 * @example API 호출
 * POST /api.php
 * {
 *   "func": "create_user_record",
 *   "firebase_uid": "abc123xyz",
 *   "display_name": "홍길동"
 * }
 * 응답: {"id":1,"firebase_uid":"abc123xyz","display_name":"홍길동",...,"func":"create_user_record"}
 * + 세션 ID 쿠키 자동 설정: sonub_session_id=1-abc123xyz-...
 *
 * @example 직접 호출
 * $user = create_user_record(['firebase_uid' => 'abc123xyz', 'display_name' => '홍길동']);
 * echo $user['id']; // 1
 * // 세션 ID 쿠키가 자동으로 설정됨
 */
function create_user_record(array $input): array
{
    // 필수 파라미터 검증
    if (empty($input['firebase_uid'])) {
        return error('firebase-uid-required', 'firebase_uid는 필수 입력 값입니다.');
    }

    // PDO 인스턴스 가져오기
    $pdo = pdo();

    // 이미 존재하는 사용자인지 확인
    $stmt = $pdo->prepare("SELECT * FROM users WHERE firebase_uid = ? LIMIT 1");
    $stmt->execute([$input['firebase_uid']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        return error('user-already-exists', '이미 존재하는 사용자입니다.', ['firebase_uid' => $input['firebase_uid']]);
    }

    // 데이터 준비
    // display_name이 없으면 firebase_uid를 사용 (UNIQUE 제약 조건)
    $displayName = $input['display_name'] ?? $input['firebase_uid'];
    $now = time();

    // 사용자 레코드 생성 (PDO Prepared Statement 사용)
    $sql = "INSERT INTO users (firebase_uid, display_name, created_at, updated_at, birthday, gender)
            VALUES (:firebase_uid, :display_name, :created_at, :updated_at, :birthday, :gender)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':firebase_uid', $input['firebase_uid'], PDO::PARAM_STR);
    $stmt->bindValue(':display_name', $displayName, PDO::PARAM_STR);
    $stmt->bindValue(':created_at', $now, PDO::PARAM_INT);
    $stmt->bindValue(':updated_at', $now, PDO::PARAM_INT);
    $stmt->bindValue(':birthday', (int)($input['birthday'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':gender', $input['gender'] ?? '', PDO::PARAM_STR);
    $stmt->execute();

    // 생성된 사용자 ID
    $insertId = (int)$pdo->lastInsertId();

    // 생성된 사용자 정보 조회
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$insertId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 세션 쿠키 설정
    set_session_cookie($user);
    return $user;
}


/**
 * 사용자 정보 조회
 *
 * @param array $input HTTP 입력 파라미터
 * - $input['id']: 조회할 사용자 ID
 * @return array|null 사용자 정보 배열 또는 null (사용자가 없는 경우)
 *
 * @example API 호출
 * GET /api.php?f=get_user&id=1
 * 응답: {"id":1,"name":"홍길동","email":"test@example.com","func":"get_user"}
 *
 * @example 직접 호출
 * $user = get_user(['id' => 1]);
 * if ($user) {
 *     echo $user['name'];
 * }
 */
function get_user(array $input): array
{
    // ID 파라미터 검증
    $id = (int)($input['id'] ?? 0);
    if ($id === 0) {
        return error('input-id-empty', 'id 파라미터가 비어있습니다.');
    }

    // PDO로 사용자 조회 (Prepared Statement 사용)
    $pdo = pdo();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 사용자가 존재하면 반환
    if ($user) {
        return $user;
    }

    // 사용자를 찾지 못한 경우 에러 반환
    return error('user-not-found', '사용자를 찾을 수 없습니다.', ['id' => $id]);
}

/**
 * Firebase UID로 사용자 정보 조회
 *
 * @param string $firebase_uid 조회할 Firebase UID
 * @return array|null 사용자 정보 배열 또는 null (사용자가 없는 경우)
 *
 * @example 직접 호출
 * $user = get_user_by_firebase_uid('abc123xyz');
 * if ($user) {
 *     echo $user['display_name'];
 * }
 */
function get_user_by_firebase_uid(string $firebase_uid): ?array
{
    if (empty($firebase_uid)) {
        return null;
    }
    $pdo = pdo();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE firebase_uid = ? LIMIT 1");
    $stmt->execute([$firebase_uid]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results && count($results) > 0) {
        return $results[0];
    }
    return null;
}


/**
 * 로그인 한 사용자의 프로필 업데이트
 *
 * @param array $input HTTP 입력 파라미터
 * - $input['display_name']: (선택) 사용자 표시 이름
 * - $input['birthday']: (선택) 생년월일 (Unix timestamp)
 * - $input['gender']: (선택) 성별 ('M' 또는 'F')
 * @return array 업데이트된 사용자 정보 배열 또는 에러 배열
 *
 * @example API 호출
 * POST /api.php
 * {
 *   "func": "update_user_profile",
 *   "display_name": "홍길동",
 *   "birthday": 631152000,
 *   "gender": "M"
 * }
 * 응답: {"id":1,"firebase_uid":"abc123xyz","display_name":"홍길동",...,"func":"update_user_profile"}
 *
 * @example 직접 호출
 * $user = update_user_profile(['display_name' => '홍길동', 'gender' => 'M']);
 * echo $user['display_name']; // 홍길동
 */
function update_user_profile(array $input): array
{
    // 로그인 확인
    $currentUser = login();
    if (!$currentUser) {
        return error('user-not-logged-in', '로그인이 필요합니다.');
    }

    // PDO 인스턴스 가져오기
    $pdo = pdo();

    // 업데이트할 필드와 값을 저장할 배열
    $updateFields = [];
    $updateValues = [];

    // updated_at은 항상 업데이트
    $updateFields[] = 'updated_at = ?';
    $updateValues[] = time();

    // display_name이 제공된 경우
    if (isset($input['display_name']) && !empty($input['display_name'])) {
        $displayName = trim($input['display_name']);

        // display_name 중복 확인 (자기 자신은 제외)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE display_name = ? AND id != ?");
        $stmt->execute([$displayName, $currentUser->id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return error('display-name-already-exists', '이미 사용 중인 표시 이름입니다.', ['display_name' => $displayName]);
        }

        $updateFields[] = 'display_name = ?';
        $updateValues[] = $displayName;
    }

    // birthday가 제공된 경우
    if (isset($input['birthday'])) {
        $updateFields[] = 'birthday = ?';
        $updateValues[] = (int)$input['birthday'];
    }

    // gender가 제공된 경우
    if (isset($input['gender'])) {
        $updateFields[] = 'gender = ?';
        $updateValues[] = $input['gender'];
    }

    // photo_url이 제공된 경우
    if (isset($input['photo_url'])) {
        $updateFields[] = 'photo_url = ?';
        $updateValues[] = $input['photo_url'];
    }

    // WHERE 절용 user_id 추가
    $updateValues[] = $currentUser->id;

    // 데이터베이스 업데이트 (PDO Prepared Statement 사용)
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($updateValues);

    // 업데이트된 사용자 정보 반환
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$currentUser->id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
}


function update_my_profile(array $input): array
{
    return update_user_profile($input);
}




/**
 * 사용자 목록 조회 (페이징 및 필터링)
 *
 * @param array $input
 * - $input['page'] - 페이지 번호 (기본값: 1)
 * - $input['per_page'] - 페이지당 항목 수 (기본값: 10, 최대값: 100)
 * - $input['gender'] - (선택) 성별 필터 ('M' 또는 'F')
 * - $input['age_start'] - (선택) 시작 나이 (예: 24)
 * - $input['age_end'] - (선택) 끝 나이 (예: 32)
 * - $input['name'] - (선택) 이름 검색 (LIKE 'name%')
 *
 * @return array
 * - 'page' - 현재 페이지 번호
 * - 'per_page' - 페이지당 항목 수
 * - 'total' - 전체 사용자 수
 * - 'total_pages' - 전체 페이지 수
 * - 'users' - 사용자 배열
 *
 * @example 기본 목록 조회
 * $result = list_users(['page' => 1, 'per_page' => 10]);
 *
 * @example 성별 필터링
 * $result = list_users(['gender' => 'F', 'page' => 1]);
 *
 * @example 나이 범위 필터링
 * $result = list_users(['age_start' => 24, 'age_end' => 32, 'page' => 1]);
 *
 * @example 이름 검색
 * $result = list_users(['name' => '홍', 'page' => 1]);
 *
 * @example 복합 검색
 * $result = list_users([
 *     'gender' => 'F',
 *     'age_start' => 24,
 *     'age_end' => 32,
 *     'name' => '김',
 *     'page' => 1
 * ]);
 */
function list_users(array $input): array
{
    // ========================================================================
    // 1단계: 페이징 파라미터 처리 및 초기화
    // ========================================================================
    $page = isset($input['page']) && is_numeric($input['page']) ? max(1, (int)$input['page']) : 1;
    $per_page = isset($input['per_page']) && is_numeric($input['per_page']) ? (int)$input['per_page'] : 10;

    // per_page 범위 제한 (1-100)
    if ($per_page < 1 || $per_page > 100) {
        $per_page = 10;
    }
    $offset = ($page - 1) * $per_page;

    // ========================================================================
    // 2단계: 필터링 파라미터 처리 및 초기화
    // ========================================================================
    // 빈 문자열('')도 빈 값으로 처리
    $gender = isset($input['gender']) && $input['gender'] !== '' ? $input['gender'] : '';
    $age_start = isset($input['age_start']) && $input['age_start'] !== '' ? (int)$input['age_start'] : null;
    $age_end = isset($input['age_end']) && $input['age_end'] !== '' ? (int)$input['age_end'] : null;
    $name = isset($input['name']) && $input['name'] !== '' ? $input['name'] : '';

    // ========================================================================
    // 3단계: 동적 WHERE 조건 빌드
    // ========================================================================
    // PDO Prepared Statement를 위한 조건 배열과 파라미터 배열
    $conditions = [];
    $params = [];

    // 성별 필터 (M 또는 F만 허용)
    if ($gender !== '' && in_array($gender, ['M', 'F'])) {
        $conditions[] = 'gender = ?';
        $params[] = $gender;
    }

    // 이름 검색 (LIKE 'name%' - 앞부분 일치)
    if ($name !== '') {
        $conditions[] = 'display_name LIKE ?';
        $params[] = $name . '%';
    }

    // 나이 필터 (birthday Unix timestamp 기반)
    if ($age_start !== null || $age_end !== null) {
        $current_year = (int)date('Y');

        if ($age_start !== null && $age_end !== null) {
            // 시작 나이와 끝 나이 모두 지정 (예: 24세~32세)
            $birth_year_end = $current_year - $age_start;
            $birth_year_start = $current_year - $age_end;

            $conditions[] = 'YEAR(FROM_UNIXTIME(birthday)) BETWEEN ? AND ?';
            $params[] = $birth_year_start;
            $params[] = $birth_year_end;
        } elseif ($age_start !== null) {
            // 시작 나이만 지정 (예: 24세 이상)
            $birth_year_end = $current_year - $age_start;
            $conditions[] = 'YEAR(FROM_UNIXTIME(birthday)) <= ?';
            $params[] = $birth_year_end;
        } elseif ($age_end !== null) {
            // 끝 나이만 지정 (예: 32세 이하)
            $birth_year_start = $current_year - $age_end;
            $conditions[] = 'YEAR(FROM_UNIXTIME(birthday)) >= ?';
            $params[] = $birth_year_start;
        }
    }

    // ========================================================================
    // 4단계: PDO로 전체 사용자 수 계산
    // ========================================================================
    $pdo = pdo();

    // WHERE 절 생성
    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

    // 전체 사용자 수 계산 쿼리
    $countSql = "SELECT COUNT(*) as count FROM users {$whereClause}";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total_count = (int)$stmt->fetchColumn();

    // ========================================================================
    // 5단계: PDO로 사용자 목록 조회
    // ========================================================================
    // LIMIT와 OFFSET을 위한 파라미터 추가
    $listParams = $params; // 기존 WHERE 파라미터 복사
    $listParams[] = $per_page; // LIMIT
    $listParams[] = $offset;   // OFFSET

    // 사용자 목록 조회 쿼리
    $listSql = "SELECT * FROM users {$whereClause} ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($listSql);
    $stmt->execute($listParams);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ========================================================================
    // 6단계: 결과 반환
    // ========================================================================
    return [
        'page' => $page,
        'per_page' => $per_page,
        'total' => $total_count,
        'total_pages' => ceil($total_count / $per_page),
        'users' => $results,
    ];
}


function search_users(array $input): array
{
    return list_users($input);
}
