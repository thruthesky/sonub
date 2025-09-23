<?php

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', realpath(__DIR__ . '/../../'));
}

/**
 * Returns the full path of a file in the etc folder.
 *
 * @param string $path The relative path within the etc folder, without the .php extension.
 * @return string The full path to the specified file.
 */
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
    } else if (str_contains($host, '192.168.')) {
        return true;
    } else if (str_contains($host, 'local.sonub.com')) {
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
 * Checks if running in terminal/CLI environment.
 * @return bool
 */
function is_cli(): bool
{
    return php_sapi_name() === 'cli';
}
