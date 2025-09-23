<?php


function etc_folder(string $path): string
{
    return ROOT_DIR . '/etc/' . $path . '.php';
}

/**
 * Determines whether the session is running on developer's computer.
 *
 * Note:
 * - Returns false when running in CLI. This is also the same for is_philgo_domain() which returns false in CLI commands.
 * - Returns false if HTTP_HOST is not defined.
 *
 * @return bool true if the system wherein the PHP script is running is on localhost.
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
    } else if (str_contains($host, 'local.philgo.com')) {
        return true;
    } else if (str_contains($host, '192.168.')) {
        return true;
    } else if (in_array($host, ['apple.philgo.com', 'banana.philgo.com', 'cherry.philgo.com', 'durian.philgo.com', 'fig.philgo.com', 'yomama.philgo.com'])) {
        return true;
    }
    return false;
}


function is_dev_computer(): bool
{
    return is_localhost();
}

/**
 * Checks if running in terminal/CLI environment.
 * @return bool
 */
function is_cli(): bool
{
    return php_sapi_name() === 'cli';
}
