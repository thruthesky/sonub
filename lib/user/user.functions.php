<?php



/**
 * 사용자가 로그인했는지 확인하고, 로그인한 사용자의 정보를 반환합니다.
 * 
 * @return UserModel | null
 * 
 * @example
 * ```php
 * login(); // returns the whole array if the user has logged in. or empty array.
 * login('phone_number'); // returns the phone number of the logged-in user
 * ```
 * ```html
 * Welcome <?= login()->nickname ?>,
 * <?php if (login()): ?>
 *  <a href="<?= href()->admin->index ?>">Admin</a>
 * <?php else: ?>
 *  <a href="<?= href()->login ?>">Login</a>
 * <?php endif; ?>
 * ```
 */
function login(): ?UserModel
{
    static $user = null;


    if (empty($user) && isset($_COOKIE[SESSION_ID])) {
        $session_id = $_COOKIE[SESSION_ID];
        $session_parts = explode('-', $session_id);

        // print_r($session_parts);
        if (count($session_parts) === 2) {
            // You can add additional checks here, e.g., verifying user ID or token
            // $key = $session_parts[0];
            $id = $session_parts[1];
            try {
                // 로그인을 했지만, 로그인한 사용자 자신의 정보가 없는 경우, null 을 리턴. 다시 로그인하도록 유도.
                $_user = get_user(['id' => (int) $id]);
            } catch (Exception $e) {
                return null;
            }
            if (empty($_user)) return null;


            $generated_session_id = generate_session_id($_user);
            if ($generated_session_id !== $session_id) {
                $_user = null;
            } else {
                $user = new UserModel($_user);
            }
        }
    }

    return $user;
}


/**
 * 
 * 
 * Firebase 인증 후 사용자 로그인 처리 (사용자 생성 또는 기존 사용자 로그인)
 *
 * ## 개요
 * Firebase 로그인 후 호출되는 함수입니다.
 * - **기존 사용자**: 세션 쿠키를 설정하고 사용자 정보를 반환합니다
 * - **신규 사용자**: users 테이블에 새 레코드를 생성하고, 세션 쿠키를 설정한 후 사용자 정보를 반환합니다
 *
 * 이 함수는 사용자 생성 시 **자동으로 세션 ID 쿠키를 설정**합니다:
 * - 쿠키 이름: `sonub_session_id`
 * - 유효기간: 1년 (365일)
 * - 경로: `/` (전체 사이트)
 * - 형식: `{user_id}-{firebase_uid}-{hash}`
 * - 예시: `1-abc123xyz-e5d8f2a1b3c4...`
 *
 * ## 흐름
 * 1. Firebase UID와 전화번호를 파라미터로 받음 (필수)
 * 2. Firebase UID로 기존 사용자 조회
 * 3. 기존 사용자 존재:
 *    - 전화번호 일치 여부 확인 (보안: 기존 사용자의 phone_number와 요청한 phone_number 비교)
 *    - 전화번호 일치: 세션 쿠키 설정 후 사용자 정보 반환
 *    - 전화번호 불일치: 에러 반환 (`phone-number-mismatch`)
 * 4. 신규 사용자:
 *    - users 테이블에 새 레코드 생성 (firebase_uid, phone_number, first_name, last_name 등)
 *    - 세션 쿠키 설정 (새로 생성된 사용자)
 *    - 생성된 사용자 정보 반환
 *
 * ## API 호출 예제
 *
 * ```bash
 * # Firebase 로그인 후 사용자 생성/조회
 * curl -X POST https://local.sonub.com/api.php \
 *   -H "Content-Type: application/json" \
 *   -d '{
 *     "func": "login_with_firebase",
 *     "firebase_uid": "abc123xyz",
 *     "phone_number": "010-1234-5678",
 *     "first_name": "길동",
 *     "last_name": "홍",
 *     "middle_name": "",
 *     "birthday": 631152000,
 *     "gender": "M"
 *   }'
 * ```
 *
 * ## 응답 예제
 *
 * **성공 응답 (신규 사용자 생성)**:
 * ```json
 * {
 *   "id": 1,
 *   "firebase_uid": "abc123xyz",
 *   "phone_number": "010-1234-5678",
 *   "first_name": "길동",
 *   "last_name": "홍",
 *   "middle_name": "",
 *   "created_at": 1759646876,
 *   "updated_at": 1759646876,
 *   "birthday": 631152000,
 *   "gender": "M",
 *   "photo_url": "",
 *   "func": "login_with_firebase"
 * }
 * ```
 *
 * **응답 헤더 (세션 쿠키 자동 설정)**:
 * ```
 * Set-Cookie: sonub_session_id=1-abc123xyz-e5d8f2a1b3c4...; Max-Age=31536000; Path=/
 * ```
 *
 * **에러 응답 (Firebase UID 누락)**:
 * ```json
 * {
 *   "error_code": "input-firebase-uid-empty",
 *   "error_message": "firebase_uid 파라미터가 비어있습니다.",
 *   "func": "login_with_firebase"
 * }
 * ```
 *
 * **에러 응답 (전화번호 누락)**:
 * ```json
 * {
 *   "error_code": "input-phone-number-empty",
 *   "error_message": "phone_number 파라미터가 비어있습니다.",
 *   "func": "login_with_firebase"
 * }
 * ```
 *
 * **에러 응답 (전화번호 불일치)**:
 * ```json
 * {
 *   "error_code": "phone-number-mismatch",
 *   "error_message": "전화번호가 일치하지 않습니다. Firebase UID와 일치하는 기존 사용자의 전화번호와 요청한 전화번호가 다릅니다.",
 *   "func": "login_with_firebase"
 * }
 * ```
 *
 * ## JavaScript에서 사용 예제
 *
 * ```javascript
 * // Firebase 로그인 후 API 호출
 * firebase.auth().onAuthStateChanged(async (user) => {
 *     if (user) {
 *         try {
 *             // 서버에 사용자 생성/조회 요청
 *             const result = await func('login_with_firebase', {
 *                 firebase_uid: user.uid,
 *                 phone_number: user.phoneNumber || '',
 *                 first_name: user.displayName?.split(' ')[0] || '',
 *                 last_name: user.displayName?.split(' ')[1] || '',
 *                 birthday: 0,
 *                 gender: ''
 *             });
 *
 *             console.log('로그인 성공:', result.id);
 *             // 세션 쿠키가 자동으로 설정됨 - 추가 작업 불필요
 *
 *         } catch (error) {
 *             console.error('로그인 실패:', error);
 *         }
 *     }
 * });
 * ```
 *
 * @param array $params Firebase 로그인에 필요한 파라미터
 * - `firebase_uid` (string, 필수): Firebase 로그인에서 받은 UID
 * - `phone_number` (string, 필수): 사용자 전화번호. ⚠️ 비밀번호처럼 민감한 정보이므로 절대로 타 회원에게 노출되어서는 안 됩니다
 * - `first_name` (string, 선택): 사용자 이름. 기본값: ''
 * - `last_name` (string, 선택): 사용자 성. 기본값: ''
 * - `middle_name` (string, 선택): 중간 이름. 기본값: ''
 * - `birthday` (int, 선택): 생년월일 (Unix timestamp). 기본값: 0
 * - `gender` (string, 선택): 성별 ('M' 또는 'F'). 기본값: ''
 * - `photo_url` (string, 선택): 프로필 사진 URL. 기본값: ''
 *
 * @return array|mixed 기존 사용자인 경우 사용자 정보 배열, 신규 사용자 생성 시 생성된 사용자 정보 배열
 * - `id` (int): 사용자 ID
 * - `firebase_uid` (string): Firebase UID
 * - `phone_number` (string): 사용자 전화번호
 * - `first_name` (string): 사용자 이름
 * - `last_name` (string): 사용자 성
 * - `middle_name` (string): 중간 이름
 * - `created_at` (int): 생성일 (Unix timestamp)
 * - `updated_at` (int): 수정일 (Unix timestamp)
 * - `birthday` (int): 생년월일 (Unix timestamp)
 * - `gender` (string): 성별
 * - `photo_url` (string): 프로필 사진 URL
 *
 * @throws ApiException Firebase UID 또는 전화번호가 누락된 경우, 또는 전화번호가 일치하지 않는 경우
 *
 * @example
 * ```php
 * // PHP에서 직접 호출
 * $user = login_with_firebase([
 *     'firebase_uid' => 'abc123xyz',
 *     'phone_number' => '010-1234-5678',
 *     'first_name' => '길동',
 *     'last_name' => '홍',
 *     'gender' => 'M'
 * ]);
 *
 * // 세션 쿠키가 자동으로 설정됨
 * echo $user['id']; // 1
 * echo $user['phone_number']; // 010-1234-5678
 * ```
 */
function login_with_firebase(array $params): array
{
    // ## 파라미터 검증: Firebase UID 필수
    $firebase_uid = $params['firebase_uid'] ?? '';
    if (empty($firebase_uid)) {
        return error('input-firebase-uid-empty', 'firebase_uid 파라미터가 비어있습니다.');
    }

    // ## 파라미터 검증: 전화번호 필수
    $phone_number = $params['phone_number'] ?? '';
    if (empty($phone_number)) {
        return error('input-phone-number-empty', 'phone_number 파라미터가 비어있습니다.');
    }

    // ## Step 1: Firebase UID로 기존 사용자 조회
    // 이미 등록된 사용자인지 확인합니다
    $existing = db()->select('*')
        ->from('users')
        ->where('firebase_uid = ?', [$firebase_uid])
        ->first();

    // 기존 사용자 존재: 전화번호 일치 여부 확인
    if ($existing) {
        // 전화번호가 일치해야 함 (보안상의 이유)
        if ($existing['phone_number'] !== $phone_number) {
            return error('phone-number-mismatch', '전화번호가 일치하지 않습니다. Firebase UID와 일치하는 기존 사용자의 전화번호와 요청한 전화번호가 다릅니다.');
        }
        // 전화번호 일치: 세션 쿠키 설정 후 반환
        set_session_cookie($existing);
        return $existing;
    }

    // ## Step 2: 신규 사용자 레코드 생성
    // 현재 시간 (Unix timestamp)
    $now = time();

    // 사용자 정보 데이터 준비
    // 필수: firebase_uid, phone_number, created_at, updated_at
    // 선택: first_name, last_name, middle_name, birthday, gender, photo_url
    $data = [
        'firebase_uid' => $firebase_uid,
        'phone_number' => $phone_number,                     // 전화번호 (필수)
        'first_name' => $params['first_name'] ?? '',        // 이름
        'last_name' => $params['last_name'] ?? '',          // 성
        'middle_name' => $params['middle_name'] ?? '',      // 중간 이름
        'created_at' => $now,                                // 생성일 (Unix timestamp)
        'updated_at' => $now,                                // 수정일 (Unix timestamp)
        'birthday' => (int)($params['birthday'] ?? 0),      // 생년월일 (Unix timestamp, 기본: 0)
        'gender' => $params['gender'] ?? '',                // 성별 ('M' 또는 'F')
        'photo_url' => $params['photo_url'] ?? '',          // 프로필 사진 URL
    ];

    // users 테이블에 새 레코드 생성
    $insertId = db()->insert($data)->intoUsers();

    // ## Step 3: 생성된 사용자 정보 조회 및 반환
    // 생성된 사용자 ID로 완전한 사용자 정보를 조회합니다
    $user = db()->select('*')->from('users')->where('id = ?', [$insertId])->first();

    // 세션 ID 쿠키 설정
    // 자동으로 sonub_session_id 쿠키가 설정되며, 유효기간은 1년입니다
    set_session_cookie($user);

    return $user;
}


function is_admin(): bool
{
    $user = login();
    if (empty($user)) {
        return false;
    }

    return false;
}
