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
function error(string $code = 'unknown', string $message = '', array $data = [], int $response_code = 400): void
{
    $ret = [
        'error_code' => $code,
        'error_message' => $message,
        'error_data' => $data,
        'error_response_code' => $response_code,
    ];

    debug_log(
        '🚨 API 에러 발생',
        ...$ret
    );
    // API 에러 응답 전송
    http_response_code($response_code);
    echo json_encode($ret);
    exit;
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



function error_if_not_logged_in(): void
{
    if (!login()) {
        error('login-required', tr([
            'en' => 'Login is required.',
            'ko' => '로그인이 필요합니다.',
            'ja' => 'ログインが必要です。',
            'zh' => '需要登录。'
        ]));
    }
}


function error_if_empty(mixed $value, string $code,  string $message = 'Value is required.', array $data = []): void
{
    if (empty($value)) {
        error($code, $message, $data, response_code: 401);
    }
}


/**
 * 디버그 정보를 파일에 기록하는 함수
 *
 * 문자열, 배열, 객체 등 다양한 데이터 타입을 지원하며, 여러 파라미터를 동시에 기록할 수 있습니다.
 * 모든 파라미터는 JSON 포맷으로 **한 라인씩** ./var/debug.log 파일에 기록됩니다.
 *
 * 중요: 타임스탐프는 **현재 세션(스크립트 실행)에서 단 한 번만** 파일의 맨 앞에 기록됩니다.
 * 이후 모든 debug_log() 호출의 각 파라미터는 JSON 포맷으로 한 라인씩 기록됩니다.
 * 이를 통해 로그 파일이 깔끔하고 읽기 편하고 파싱 가능하게 유지됩니다.
 *
 * Rest operator(...)를 사용하여 가변 개수의 파라미터를 받으며,
 * 각 파라미터는 JSON 포맷의 한 라인으로 기록됩니다.
 *
 * @param mixed ...$args 기록할 데이터들. 문자열, 배열, 객체 등 어떤 타입이든 가능하며, 여러 개를 한 번에 전달 가능합니다.
 *
 * @return void
 *
 * @example 단일 문자열 로깅
 * ```php
 * debug_log('API 호출 시작');
 * // 파일 내용:
 * // [2025-10-28 04:20:01]
 * // "API 호출 시작"
 * ```
 *
 * @example 여러 파라미터 로깅 (각 파라미터는 한 라인씩 JSON 포맷)
 * ```php
 * debug_log('API 호출 시작', true, ['user_id' => 123]);
 * // 파일 내용:
 * // [2025-10-28 04:20:01]
 * // "API 호출 시작"
 * // true
 * // {"user_id":123}
 * ```
 *
 * @example 배열 로깅
 * ```php
 * $params = ['user_id' => 123, 'action' => 'login'];
 * debug_log('파라미터:', $params);
 * // 배열이 JSON 한 라인으로 출력됨
 * // "파라미터:"
 * // {"user_id":123,"action":"login"}
 * ```
 *
 * @example 객체 로깅
 * ```php
 * $data = (object) ['name' => '테스트', 'value' => 42];
 * debug_log('사용자 데이터:', $data);
 * // 객체가 JSON 한 라인으로 출력됨
 * // "사용자 데이터:"
 * // {"name":"테스트","value":42}
 * ```
 */
function debug_log(mixed ...$args): void
{
    // 루트 폴더의 ./var/debug.log 파일에 기록
    $log_file = ROOT_DIR . '/var/debug.log';

    // 정적 변수: 세션 타임스탐프와 기록 여부 추적
    // - $session_timestamp: 세션 시작 시의 타임스탐프 저장
    // - $timestamp_written: 타임스탐프가 파일에 기록되었는지 추적
    static $session_timestamp = null;
    static $timestamp_written = false;

    // 세션 첫 호출 시 타임스탐프 생성
    if ($session_timestamp === null) {
        $session_timestamp = date('Y-m-d H:i:s');
    }

    // 세션 처음 한 번만 타임스탐프를 파일에 기록
    if (!$timestamp_written) {
        file_put_contents($log_file, "[$session_timestamp]\n", FILE_APPEND);
        $timestamp_written = true;
    }

    // rest operator로 받은 모든 파라미터를 순회하며 한 라인씩 기록
    foreach ($args as $data) {
        // JSON 포맷으로 변환하여 한 라인으로 기록
        // JSON_UNESCAPED_UNICODE: 한글 등 유니코드 문자를 그대로 표시
        // JSON_UNESCAPED_SLASHES: 슬래시를 이스케이프하지 않음
        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // 타임스탐프 없이 JSON 데이터만 기록 (한 라인)
        $log_entry = "$json_data\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}
