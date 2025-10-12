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
