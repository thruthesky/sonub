<?php
// CLI에서는 127.0.0.1, 도커 환경에서는 컨테이너 이름으로 연결
// PHP CLI 테스트: 127.0.0.1 (호스트 머신에서 실행)
// PHP-FPM (웹): sonub-mariadb (도커 컨테이너 내부에서 실행)
if (!defined('DB_HOST')) {
    define('DB_HOST', (php_sapi_name() === 'cli') ? '127.0.0.1' : 'sonub-mariadb');
    define('DB_NAME', 'sonub');
    define('DB_USER', 'sonub');
    define('DB_PASSWORD', 'asdf');
}
