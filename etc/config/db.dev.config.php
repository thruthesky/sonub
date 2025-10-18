<?php
// Docker 환경에서는 항상 컨테이너 이름으로 연결
// Docker 내부 CLI 및 PHP-FPM 모두 sonub-mariadb 사용
if (!defined('DB_HOST')) {
    if (is_cli()) {
        define('DB_HOST', '127.0.0.1');
    } else {
        define('DB_HOST', 'sonub-mariadb');
    }
    define('DB_NAME', 'sonub');
    define('DB_USER', 'sonub');
    define('DB_PASSWORD', 'asdf');
}
