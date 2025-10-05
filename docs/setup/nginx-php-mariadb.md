Sonub 웹사이트 개발을 위한 Nginx, PHP, MariaDB 설치 가이드:
- 이 문서는 Ubuntu Linux 환경에서 Nginx, PHP, MariaDB를 설치하고 구성하는 방법에 대한 지침을 제공합니다.

## 1. 시스템 업데이트
```bash
sudo apt update
sudo apt upgrade -y
```
## 2. Nginx 설치
```bash
sudo apt install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```
## 3. PHP 설치
```bash
sudo apt install php-fpm php-mysql -y
```
## 4. MariaDB 설치
```bash
sudo apt install mariadb-server -y
sudo systemctl start mariadb
sudo systemctl enable mariadb
```

## 5. Nginx 구성 - 모든 요청을 index.php로 라우팅

### 5.1 Nginx 구성 파일 생성
`/etc/nginx/sites-available/sonub.com.conf` 파일을 생성하고 프로젝트의 구성 파일을 포함합니다.

```nginx
# /etc/nginx/sites-available/sonub.com.conf
# Sonub 프로젝트의 nginx 구성을 포함하는 메인 구성 파일

# 프로젝트 디렉토리에서 nginx 구성 파일 포함
# <sonub-project>를 실제 프로젝트 경로로 교체하세요
# 예시: /home/user/sonub, /var/www/sonub 등
include <sonub-project>/etc/nginx/sonub.com.conf;
```

실제 Nginx 구성은 `<sonub-project>/etc/nginx/sonub.com.conf`의 프로젝트 저장소에서 관리됩니다:

```nginx
# <sonub-project>/etc/nginx/sonub.com.conf
# 실제 Sonub 서버 구성 파일 (프로젝트 저장소에서 관리)

server {
    listen 80;
    server_name sonub.com www.sonub.com;
    root /var/www/app/public;  # index.php가 위치한 디렉토리 경로
    index index.php;

    # 정적 파일 및 디렉토리 처리 후 index.php로 라우팅
    location / {
        # try_files 작업 순서:
        # 1. $uri - 요청 경로가 실제 파일이면 해당 파일 제공 (예: /style.css, /image.jpg)
        # 2. $uri/ - 요청 경로가 실제 디렉토리이면 해당 디렉토리의 인덱스 파일 제공
        # 3. /index.php?$query_string - 위의 경우가 아니면 쿼리 문자열을 보존하면서 index.php로 전달
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM 구성
    location ~ \.php$ {
        include fastcgi_params;
        # SCRIPT_FILENAME: PHP-FPM에 전달할 실제 스크립트 파일 경로
        # $document_root = /var/www/app/public
        # $fastcgi_script_name = /index.php
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;  # 라우팅에 필요할 때 사용
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;  # PHP 버전에 따라 조정
        fastcgi_read_timeout 120s;
    }

    # 보안: 숨김 파일 차단 (.git, .env 등)
    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

**이 접근 방식의 이점:**
- Nginx 구성을 Git으로 버전 관리할 수 있습니다
- 팀 구성원이 동일한 구성을 쉽게 공유할 수 있습니다
- 직접 서버 액세스 없이 Git을 통해 구성 변경을 배포할 수 있습니다

### 5.2 구성 활성화
```bash
# 심볼릭 링크 생성
sudo ln -s /etc/nginx/sites-available/sonub.com.conf /etc/nginx/sites-enabled/

# Nginx 구성 테스트
sudo nginx -t

# Nginx 리로드
sudo systemctl reload nginx
```

## 6. PHP 라우팅 구현

### 6.1 index.php 예제
모든 요청이 index.php로 전달되므로 PHP에서 라우팅을 처리해야 합니다.

```php
<?php
// index.php - 모든 요청의 진입점

// 요청 URI에서 경로 부분만 추출 (쿼리 문자열 제외)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 라우팅 테이블 정의
$routes = [
    '/' => 'home.php',
    '/about' => 'about.php',
    '/contact' => 'contact.php',
    '/post/list' => 'post/list.php',
];

// 동적 라우팅 처리 (예: /post/123)
if (preg_match('#^/post/(\d+)$#', $path, $matches)) {
    $postId = $matches[1];
    require_once 'post/view.php';
    exit;
}

// 정적 라우팅 처리
if (isset($routes[$path])) {
    require_once $routes[$path];
} else {
    // 404 페이지 처리
    http_response_code(404);
    require_once '404.php';
}
```

### 6.2 고급 라우팅 예제 - MVC 패턴
```php
<?php
// index.php - MVC 패턴을 사용한 라우팅

// 오토로더 설정 (Composer 사용 시)
require_once __DIR__ . '/../vendor/autoload.php';

// 요청 정보 분석
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Router 클래스 사용
class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch($method, $uri) {
        // 정확한 일치 확인
        if (isset($this->routes[$method][$uri])) {
            return call_user_func($this->routes[$method][$uri]);
        }

        // 패턴 매칭 (예: /post/{id})
        foreach ($this->routes[$method] as $pattern => $handler) {
            $pattern = str_replace('{id}', '(\d+)', $pattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);  // 첫 번째 요소 제거 (전체 일치)
                return call_user_func_array($handler, $matches);
            }
        }

        // 404 처리
        http_response_code(404);
        echo "404 페이지를 찾을 수 없습니다";
    }
}

// 라우터 인스턴스 생성 및 라우트 정의
$router = new Router();

// GET 라우트
$router->get('/', function() {
    echo "홈페이지";
});

$router->get('/about', function() {
    echo "회사 소개";
});

$router->get('/post/{id}', function($id) {
    echo "게시물 #" . $id . " 보기";
});

// POST 라우트
$router->post('/login', function() {
    // 로그인 처리
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // ... 인증 로직
});

// 라우팅 실행
$router->dispatch($requestMethod, $requestUri);
```

### 6.3 URL 매개변수 및 쿼리 문자열 처리
```php
<?php
// 쿼리 문자열은 $_GET을 통해 자동으로 처리됩니다
// 예: /search?q=php&category=tutorial

$searchQuery = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

// PATH_INFO 사용 시
// 예: /api/users/123/posts
// Nginx에서 fastcgi_param PATH_INFO 설정 필요
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$segments = explode('/', trim($pathInfo, '/'));
```

## 7. 디렉토리 구조 예제
```
/var/www/app/
├── public/           # 웹 루트 (Nginx document root)
│   ├── index.php    # 모든 요청의 진입점
│   ├── css/         # 정적 CSS 파일
│   ├── js/          # 정적 JS 파일
│   └── images/      # 정적 이미지 파일
├── src/             # PHP 소스 코드
│   ├── Controllers/ # 컨트롤러
│   ├── Models/      # 모델
│   └── Views/       # 뷰 템플릿
├── vendor/          # Composer 의존성
└── config/          # 구성 파일
```

## 8. 중요 사항

1. **정적 파일 처리**: CSS, JS, 이미지와 같은 정적 파일은 try_files의 첫 번째 옵션($uri)에서 처리되며 PHP를 거치지 않습니다.

2. **보안 고려 사항**:
   - `.env`, `.git`과 같은 민감한 파일은 웹 루트 외부에 배치하거나 Nginx에서 차단하세요
   - 직접 액세스를 방지하기 위해 public 디렉토리 외부에 PHP 파일을 배치하세요
   - 사용자 입력을 검증하고 SQL 인젝션을 방지하세요

3. **성능 최적화**:
   - 정적 파일은 Nginx에서 직접 제공되므로 빠릅니다
   - PHP 오토로더를 사용하여 필요한 파일만 로드하세요
   - 캐싱 헤더를 사용하여 정적 리소스 캐싱을 활용하세요

4. **디버깅 팁**:
   ```php
   // 요청 정보 확인
   echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
   echo "PATH: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "<br>";
   echo "QUERY_STRING: " . $_SERVER['QUERY_STRING'] . "<br>";
   ```
