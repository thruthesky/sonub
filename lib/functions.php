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
 * 개발자 컴퓨터에서 실행되는지 확인을 한다.
 * 
 * 주의:
 * - CLI 에서 실행되면, false 를 리턴한다. 이것은 is_localhost() 에서도 마찬가지로 CLI 명령어에서는 false 를 리턴한다.
 * - HTTP_HOST 가 정의되지 않았으면 false 를 리턴한다.
 * 
 * Determins whether the session is running in developer's computer.
 * @return bool true if the system wherein the PHP script is running is window.
 */
function is_dev_computer(): bool
{
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
