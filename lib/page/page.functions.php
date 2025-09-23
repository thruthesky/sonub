<?php


/**
 * Returns the relative path of a file in the page folder based on the current URL.
 * @return string 
 * page() returns
 * - "https://local.sonub.com/" -> './page/index.php' 
 * - "https://local.sonub.com/about" -> './page/about.php'
 * - "https://local.sonub.com/user/login" -> './page/user/login.php'
 */
function page()
{
    $uri = $_SERVER['REQUEST_URI'];
    $uri = strtok($uri, '?'); // Remove query string
    $uri = rtrim($uri, '/'); // Remove trailing slash
    if ($uri === '') {
        $uri = '/index';
    }
    $path = ROOT_DIR . '/page' . $uri . '.php';
    if (file_exists($path)) {
        return $path;
    } else {
        return ROOT_DIR . '/page/404.php';
    }
}
