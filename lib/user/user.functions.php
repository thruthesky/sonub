<?php



/**
 * Check if a user is logged in and optionally return a specific user field.
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
            $_user = get_user(['id' => (int) $id]);
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
 * Firebase 인증 후 사용자 로그인 처리
 *
 * @param array $params 로그인에 필요한 파라미터
 * - $params['firebase_uid']: Firebase UID (필수)
 * - $params['display_name']: 표시 이름 (선택, 기본값: firebase_uid의 앞 3글자)
 * - $params['birthday']: 생일 (선택, 기본값: 0)
 * - $params['gender']: 성별 (선택, 기본값: '')
 */
function login_with_firebase(array $params)
{
    $firebase_uid = $params['firebase_uid'] ?? '';
    if (empty($firebase_uid)) {
        return error('input-firebase-uid-empty', 'firebase_uid 파라미터가 비어있습니다.');
    }

    // 이미 존재하는 사용자인지 확인
    $existing = db()->select('*')
        ->from('users')
        ->where('firebase_uid = ?', [$firebase_uid])
        ->first();

    if ($existing) {
        set_session_cookie($existing);
        return $existing;
    }

    // 데이터 준비

    $data = [
        'firebase_uid' => $firebase_uid,
        'created_at' => time(),
        'updated_at' => time(),
        'birthday' => (int)($params['birthday'] ?? 0),
        'gender' => $params['gender'] ?? '',
    ];

    // 사용자 레코드 생성
    $insertId = db()->insert($data)->intoUsers();

    // 생성된 사용자 정보 반환
    $user = db()->select('*')->from('users')->where('id = ?', [$insertId])->first();
    set_session_cookie($user);
    return $user;
}
