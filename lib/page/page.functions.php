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


function get_module_path()
{
    $page = page();
    return str_replace(".php", '.module.php', $page);
}

/**
 * Returns the HTML link tag for a CSS file in the page folder based on the current URL, if it exists.
 * @return string
 * page_css() returns
 * - "https://local.sonub.com/" -> '<link href="/page/index.css" rel="stylesheet">' if the file exists, otherwise ''
 * - "https://local.sonub.com/about" -> '<link href="/page/about.css" rel="stylesheet">' if the file exists, otherwise ''
 * - "https://local.sonub.com/user/login" -> '<link href="/page/user/login.css" rel="stylesheet">' if the file exists, otherwise ''
 */
function load_page_css()
{
    $script = page();
    $path = str_replace('.php', '.css', $script);
    $uri = str_replace(ROOT_DIR, '', $path); // Convert to relative path
    $uri = $uri . '?v=' . APP_VERSION; // Cache busting with app version
    if (file_exists($path)) {
        echo '<link href="' . $uri . '" rel="stylesheet">';
    }
}

/**
 * Returns the HTML script tag for a JS file in the page folder based on the current URL, if it exists.
 * @return string
 * page_js() returns
 * - "https://local.sonub.com/" -> '<script defer src="/page/index.js"></script>' if the file exists, otherwise ''
 * - "https://local.sonub.com/about" -> '<script defer src="/page/about.js"></script>' if the file exists, otherwise ''
 * - "https://local.sonub.com/user/login" -> '<script defer src="/page/user/login.js"></script>' if the file exists, otherwise ''
 */
function load_page_js()
{
    $script = page();
    $path = str_replace('.php', '.js', $script);
    $uri = str_replace(ROOT_DIR, '', $path); // Convert to relative path
    if (file_exists($path)) {
        echo '<script defer src="' . $uri . '"></script>';
    }
}





/**
 * Global array to hold deferred scripts
 */
$global_deferred_scripts = [];

/**
 * Include JavaScript files as deferred
 *
 * @param string $path
 * - If the file path begin with `/', it must be the complete JavaScript path.
 * 
 * @param int|null $priority
 * - If this value is set,
 *  - The JavaScript will be injected before the closing </body> tag in the priority order.
 * - 0 means the lowest priority.
 * - 9 means the highest priority.
 * - If this value is not given, the `<script src=...>` will be injected immediately into the HTML.
 */
function load_deferred_js(string $path, int $priority = 0)
{
    global $global_deferred_scripts;

    if (!isset($global_deferred_scripts[$priority])) {
        $global_deferred_scripts[$priority] = [];
    }

    // 중복 로드 방지: 같은 경로가 이미 추가되어 있으면 추가하지 않음
    if (!in_array($path, $global_deferred_scripts[$priority], true)) {
        $global_deferred_scripts[$priority][] = $path;
    }
}


function get_deferred_script_tag(string $path): string
{
    // If the file path begins with '/', it is a complete path.
    if (strpos($path, '/') === 0) {
        return '<script defer src="' . $path . '?v=' . APP_VERSION . '"></script>';
    } else {
        return '<script defer src="/js/' . $path . '.js?v=' . APP_VERSION . '"></script>';
    }
}
