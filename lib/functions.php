<?php


/**
 * 개발자 컴퓨터에서 실행되는지 확인을 한다.
 * 
 * 주의:
 * - CLI 에서 실행되면, false 를 리턴한다. 이것은 is_philgo_domain() 에서도 마찬가지로 CLI 명령어에서는 false 를 리턴한다.
 * - HTTP_HOST 가 정의되지 않았으면 false 를 리턴한다.
 * 
 * Determins whether the session is running in developer's computer.
 * @return bool true if the system wherein the PHP script is running is window.
 */
function is_localhost(): bool
{
    if (is_cli()) return false;
    if (!isset($_SERVER['HTTP_HOST'])) return false;

    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, ':') !== false) {
        $host = explode(':', $host)[0];
    }

    if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
        return true;
    } else if (str_contains($host, 'local.')) {
        return true;
    } else if (str_contains($host, '192.')) {
        return true;
    } else if (str_contains($host, '172.')) {
        return true;
    } else if (in_array($host, ['apple.philgo.com', 'banana.philgo.com', 'cherry.philgo.com', 'durian.philgo.com', 'fig.philgo.com', 'yomama.philgo.com'])) {
        return true;
    }
    return false;
}

/**
 * Checks if running in MacOS.
 * @return bool
 */
function is_mac_os(): bool
{
    return str_starts_with(PHP_OS_FAMILY, 'Darwin');
}
/**
 * Checks if running in Windows.
 * @return bool
 */
function is_windows(): bool
{
    return str_starts_with(PHP_OS_FAMILY, 'Windows');
}


/**
 * Checks if running on developer's computer.
 * @return bool
 * - Return false if the script is running in MacOS or Windows.
 */
function is_dev_computer(): bool
{
    if (is_mac_os() || is_windows()) {
        return true;
    }
    return is_localhost();
}



/**
 * 터미널, CLI 에서 실행되고 있는지 확인한다.
 * @return bool
 */
function is_cli(): bool
{
    return php_sapi_name() === 'cli';
}


/**
 * API 에러를 throw하는 함수
 *
 * 모든 API 함수에서 에러 발생 시 이 함수를 호출하여 ApiException을 throw합니다.
 * api.php에서 이 Exception을 catch하여 JSON 에러 응답으로 변환합니다.
 *
 * @param string $code 에러 코드 (kebab-case 형식, 예: 'user-not-found', 'invalid-input')
 * @param string $message 에러 메시지 (사용자에게 표시될 메시지)
 * @param array $data 추가 에러 데이터 (선택사항)
 * @param int $response_code HTTP 응답 코드 (기본값: 400)
 * @return never 이 함수는 항상 Exception을 throw하므로 절대 반환하지 않습니다
 * @throws ApiException
 *
 * @example 기본 사용법
 * ```php
 * if ($user_id === null) {
 *     error('invalid-user-id', '사용자 ID가 필요합니다.');
 * }
 * ```
 *
 * @example HTTP 상태 코드 지정
 * ```php
 * if (!$user) {
 *     error('user-not-found', '사용자를 찾을 수 없습니다.', [], 404);
 * }
 * ```
 *
 * @example 추가 데이터 포함
 * ```php
 * error(
 *     'validation-failed',
 *     '입력값 검증 실패',
 *     ['field' => 'email', 'value' => $email],
 *     400
 * );
 * ```
 */
function error(string $code = 'unknown', string $message = '', array $data = [], int $response_code = 400): never
{
    throw new ApiException($code, $message, $data, $response_code);
}


function is_index_page(): bool
{
    $uri = $_SERVER['REQUEST_URI'];
    $uri = strtok($uri, '?'); // Remove query string
    $uri = rtrim($uri, '/'); // Remove trailing slash
    if ($uri === '' || $uri === '/' || $uri === '/index' || $uri === '/index.php') {
        return true;
    }
    return false;
}

function is_logout_page(): bool
{
    $uri = $_SERVER['REQUEST_URI'];
    $uri = strtok($uri, '?'); // Remove query string
    $uri = rtrim($uri, '/'); // Remove trailing slash
    if ($uri === '/user/logout-submit') {
        return true;
    }
    return false;
}



/**
 * Generate a unique session ID for the user.
 * 
 * @param array $user The user data
 * - $user['id'] is the index number of the user
 * - $user['firebase_uid'] is the Firebase UID of the user
 * - $user['phone_number'] is the phone number of the user
 */
function generate_session_id(array $user)
{
    if (empty($user['id']) || empty($user['firebase_uid'])) {
        throw new InvalidArgumentException("User data must include id and firebase_uid.");
    }
    $salt = "---secret_salt: withcenter philgo v6 server key: WA113A,*lvptB---";
    $session_id = md5($salt . $user['id'] . $user['firebase_uid']  . ($user['phone_number'] ?? '')) . '-' . $user['id'];
    return $session_id;
}


/**
 * Sets the session cookie for the user.
 * 
 * @param array $user The user data
 * - $user['id'] is the index number of the user
 * - $user['firebase_uid'] is the Firebase UID of the user
 * - $user['phone_number'] is the phone number of the user
 */
function set_session_cookie(array $user)
{
    if (empty($user['id']) || empty($user['firebase_uid'])) {
        throw new InvalidArgumentException("User data must include id and firebase_uid.");
    }
    $session_id = generate_session_id($user);
    // Set cookie for 365 days
    setcookie(
        SESSION_ID,
        $session_id,
        time() + (365 * 24 * 60 * 60),
        path: '/',
    );
}

function clear_session_cookie()
{
    setcookie(
        SESSION_ID,
        '',
        time() - 3600,
        path: '/',
    );
}
