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

    // 이미 존재하는 사용자인지 확인
    $existing = db()->select('*')
        ->from('users')
        ->where('firebase_uid = ?', [$input['firebase_uid']])
        ->first();

    if ($existing) {
        return error('user-already-exists', '이미 존재하는 사용자입니다.', ['firebase_uid' => $input['firebase_uid']]);
    }

    // 데이터 준비
    // display_name이 없으면 firebase_uid를 사용 (UNIQUE 제약 조건)
    $displayName = $input['display_name'] ?? $input['firebase_uid'];

    $data = [
        'firebase_uid' => $input['firebase_uid'],
        'display_name' => $displayName,
        'created_at' => time(),
        'updated_at' => time(),
        'birthday' => (int)($input['birthday'] ?? 0),
        'gender' => $input['gender'] ?? '',
    ];

    // 사용자 레코드 생성
    $insertId = db()->insert($data)->into('users');

    // 생성된 사용자 정보 반환
    $user = db()->select('*')->from('users')->where('id = ?', [$insertId])->first();
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
    $id = (int)($input['id'] ?? 0);
    if ($id === 0) {
        return error('input-id-empty', 'id 파라미터가 비어있습니다.');
    }

    $results = db()->select('*')->from('users')->where("id = ?", [$id])->get();
    if ($results && count($results) > 0) {
        return $results[0];
    }
    return error('user-not-found', '사용자를 찾을 수 없습니다.', ['id' => $id]);
}
