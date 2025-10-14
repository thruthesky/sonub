# 테스트 가이드

## 목차
- [테스트 환경](#테스트-환경)
  - [라우팅 규칙 (E2E 테스트 필수 숙지!)](#라우팅-규칙-e2e-테스트-필수-숙지)
- [테스트 종류](#테스트-종류)
- [테스트 파일 저장 위치 - 최강력 규칙](#테스트-파일-저장-위치---최강력-규칙)
- [테스트 코드 작성 시 필수 사항](#테스트-코드-작성-시-필수-사항)
- [테스트 사용자 로그인 - 필수 숙지](#테스트-사용자-로그인---필수-숙지)
- [테스트 실행 방법](#테스트-실행-방법)
- [데이터베이스 설정 자동 선택](#데이터베이스-설정-자동-선택)
- [PHP Unit Test 작성 규칙](#php-unit-test-작성-규칙)
- [PHP End-to-End (E2E) Test 작성 규칙](#php-end-to-end-e2e-test-작성-규칙)
- [Playwright End-to-End (E2E) Test 작성 규칙](#playwright-end-to-end-e2e-test-작성-규칙)

## 테스트 환경

**개발 환경 URL**: https://local.sonub.com/

- 모든 테스트는 개발 환경 URL `https://local.sonub.com/`에서 수행됩니다
- PHP Unit Test는 CLI에서 직접 실행합니다
- PHP E2E Test는 `https://local.sonub.com/`에 HTTP 요청을 보내 테스트합니다

### 라우팅 규칙 (E2E 테스트 필수 숙지!)

**⚠️⚠️⚠️ E2E 테스트 시 반드시 알아야 할 라우팅 규칙 ⚠️⚠️⚠️**

Sonub는 Nginx 라우팅을 통해 모든 요청을 `index.php`로 전달하며, `index.php`가 요청 경로를 분석하여 `/page/**/*.php` 아래의 PHP 스크립트를 로드합니다.

**Nginx 라우팅 설정:**

```nginx
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**라우팅 동작 방식:**

1. **존재하는 파일 접근**: 요청한 파일이 존재하면 해당 파일을 반환
2. **존재하지 않는 파일 접근**: 요청한 파일이 존재하지 않으면 **모든 요청은 `index.php`로 전달**
3. **페이지 파일 로딩**: `index.php`는 요청 경로를 분석하여 `/page/**/*.php` 아래의 PHP 스크립트를 로드

**URL과 페이지 파일 매핑:**

| 요청 URL | 로드되는 파일 |
|---------|-------------|
| `https://local.sonub.com/` | `/page/index.php` |
| `https://local.sonub.com/abc/def` | `/page/abc/def.php` |
| `https://local.sonub.com/user/login` | `/page/user/login.php` |
| `https://local.sonub.com/post/list` | `/page/post/list.php` |
| `https://local.sonub.com/admin/dashboard` | `/page/admin/dashboard.php` |

**E2E 테스트 예제:**

- **테스트 대상 파일**: `/page/abc/def.php`
- **E2E 테스트 URL**: `https://local.sonub.com/abc/def`
- **동작 과정**:
  1. Nginx가 `/abc/def` 파일이 없음을 확인
  2. `index.php`로 요청 전달
  3. `index.php`가 `/page/abc/def.php` 파일을 로드
  4. 화면에 표시됨

**중요 사항:**

- **✅ 필수**: E2E 테스트 URL은 **확장자 없이** 작성 (예: `/user/login`, `/abc/def`)
- **✅ 필수**: 페이지 파일 경로가 `/page/user/profile.php`라면, E2E 테스트 URL은 `https://local.sonub.com/user/profile`
- **❌ 금지**: E2E 테스트에서 `.php` 확장자를 URL에 포함하지 마세요
- **❌ 금지**: E2E 테스트에서 `/page/` 경로를 URL에 포함하지 마세요

## 테스트 종류

Sonub 프로젝트에서는 세 가지 방식의 테스트를 사용합니다:

### 1. PHP Unit Test (단위 테스트)

**대상**: 함수, 로직, 클래스 메서드 등의 내부 동작

- 외부 라이브러리 없이 순수 PHP로 작성
- `php tests/xxx/xxx.test.php` 형식으로 CLI에서 직접 실행
- 함수의 입력/출력, 비즈니스 로직 검증
- 데이터베이스 쿼리 결과 검증

**사용 시기**: "함수 테스트", "로직 테스트", "DB 쿼리 테스트" 요청 시

### 2. PHP End-to-End (E2E) Test (통합 테스트)

**대상**: 페이지, UI 요소, HTML 콘텐츠 검증

- 외부 라이브러리 없이 순수 PHP로 작성
- `curl` 또는 `file_get_contents()` 함수를 사용하여 HTTP 요청
- `https://local.sonub.com/`에 실제 요청을 보내 응답 검증
- HTML 응답, HTTP 상태 코드, 페이지 콘텐츠 검증
- HTML 요소 존재 여부, 텍스트 포함 여부 확인

**사용 시기**: "페이지 테스트", "UI 요소 테스트", "HTML 콘텐츠 테스트" 요청 시

**제한사항**: PHP E2E 테스트는 다음과 같은 작업을 할 수 없습니다:
- 웹 브라우저 열기
- 사용자 타이핑 시뮬레이션
- 폼 전송 시뮬레이션
- JavaScript 실행
- 브라우저 이벤트 트리거 (클릭, 스크롤 등)

### 3. Playwright End-to-End (E2E) Test (브라우저 자동화 테스트)

**대상**: 브라우저 상호작용, 폼 전송, JavaScript 실행이 필요한 사용자 시나리오

- TypeScript로 작성
- Playwright 라이브러리를 사용하여 실제 브라우저 자동화
- 실제 브라우저(Chromium, Firefox, WebKit)에서 테스트 실행
- 웹 브라우저 열기, 사용자 타이핑, 폼 전송, JavaScript 실행 가능

**사용 시기**:
- "폼 전송 테스트" 요청 시
- "사용자 입력 테스트" 요청 시
- "JavaScript 실행 테스트" 요청 시
- "브라우저 이벤트 테스트" 요청 시
- PHP E2E 테스트로 할 수 없는 모든 경우

**🔥🔥🔥 최강력 규칙: PHP로 테스트가 가능하면 반드시 PHP로 테스트하고, PHP로 E2E 테스트가 불가능한 경우에만 Playwright를 사용하세요 🔥🔥🔥**

## 테스트 파일 저장 위치 - 최강력 규칙

**🔥🔥🔥 최강력 규칙: 모든 테스트 파일, 임시 파일, 검증 파일은 반드시 `./tests` 폴더 아래에 저장해야 합니다 🔥🔥🔥**

### 필수 준수 사항

- **✅ 필수**: 모든 테스트 파일은 `tests` 디렉토리에 저장
- **✅ 필수**: 모든 임시 검증 파일은 `tests` 디렉토리에 저장
- **✅ 필수**: 테스트 관련 모든 파일은 `tests` 디렉토리에 저장
- **❌ 절대 금지**: 프로젝트 루트에 테스트 파일 생성 금지
- **❌ 절대 금지**: `lib`, `src` 등 소스 코드 폴더에 테스트 파일 생성 금지
- **❌ 절대 금지**: 임시 파일을 루트나 소스 폴더에 생성 금지

### 올바른 저장 위치 예시

```
✅ tests/db/db.connection.test.php       # DB 연결 Unit Test
✅ tests/user/user.crud.test.php         # 사용자 CRUD Unit Test
✅ tests/api/api.test.php                # API Unit Test
✅ tests/e2e/user-login.e2e.test.php     # 사용자 로그인 페이지 E2E Test
✅ tests/e2e/homepage.e2e.test.php       # 홈페이지 E2E Test
✅ tests/temp/verify_function.php        # 임시 검증 파일
✅ tests/temp/test_query.php             # 임시 쿼리 테스트
```

### 잘못된 저장 위치 예시

```
❌ db.test.php                           # 루트 폴더 (절대 금지!)
❌ test.php                              # 루트 폴더 (절대 금지!)
❌ temp.php                              # 루트 폴더 (절대 금지!)
❌ lib/db/db.test.php                    # 소스 코드 폴더 (절대 금지!)
❌ verify.php                            # 루트 폴더 (절대 금지!)
```

### 위반 시 발생하는 문제

- 프로젝트 구조 오염
- 운영 코드와 테스트 코드 혼재
- Git 관리 어려움
- 배포 시 불필요한 파일 포함
- 팀 협업 시 혼란 발생

## 테스트 코드 작성 시 필수 사항

**모든 테스트 파일 맨 위에 반드시 `include '../init.php'`를 추가하세요!**

- `init.php`를 포함하면 모든 라이브러리, 함수, DB 설정 등을 즉시 사용 가능
- `init.php`는 ROOT_DIR 정의 및 `etc/includes.php` 로드를 자동으로 처리
- 상대 경로는 테스트 파일의 위치에 따라 조정 (예: `tests/user/` → `../../init.php`)

### 테스트 파일 기본 템플릿

```php
<?php
// tests/user/example.test.php
include '../../init.php';

// 이제 모든 함수와 클래스를 바로 사용 가능
// db(), get_user(), error() 등 모든 함수 사용 가능
```

## 테스트 사용자 로그인 - 필수 숙지

**🔥🔥🔥 중요: 로그인이 필요한 함수를 테스트할 때는 반드시 `login_as_test_user()` 함수를 사용하세요 🔥🔥🔥**

많은 함수들이 로그인된 사용자만 호출할 수 있습니다. 예를 들어:
- `create_post()` - 게시글 생성
- `update_user_profile()` - 프로필 업데이트
- `upload_file()` - 파일 업로드
- 기타 사용자 인증이 필요한 모든 함수

이러한 함수들을 테스트하려면 먼저 테스트 사용자로 로그인해야 합니다.

### login_as_test_user() 함수 사용법

```php
<?php
// tests/post/create_post.test.php

include __DIR__ . '/../../init.php';

// 테스트 함수 로드 (한 번만 로드)
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

// ========================================================================
// 중요: 로그인이 필요한 함수를 테스트하기 전에 반드시 호출!
// ========================================================================
login_as_test_user(); // 기본 테스트 사용자 (firebase_uid='banana')로 로그인

// 이제 로그인이 필요한 함수를 호출할 수 있습니다
$post = create_post([
    'category' => 'test',
    'title' => 'Test Post',
    'content' => 'Test Content'
]);

// 로그인된 사용자 정보 확인
$user = login();
echo "로그인된 사용자: {$user->display_name}\n";
echo "Firebase UID: {$user->firebase_uid}\n";
```

### 기본 사용법

```php
// 기본 테스트 사용자로 로그인 (firebase_uid = 'banana')
login_as_test_user();

// 로그인 확인
$user = login();
if ($user) {
    echo "로그인 성공: {$user->firebase_uid}\n";
}
```

### 다른 테스트 사용자로 로그인

```php
// 특정 firebase_uid를 가진 사용자로 로그인
login_as_test_user('apple');

$user = login();
echo "로그인된 사용자: {$user->firebase_uid}\n"; // 출력: apple
```

### 주의사항

**⚠️ 중요: `login()` 함수는 static 캐시를 사용합니다**

`login()` 함수는 `static $user` 변수로 로그인된 사용자를 캐시합니다. 따라서:

1. **한 테스트 파일에서는 한 번만 로그인하세요**
   ```php
   // ✅ 올바른 방법
   login_as_test_user('banana');
   $user = login(); // firebase_uid = 'banana'

   // ❌ 잘못된 방법 (같은 파일 내에서 다시 로그인)
   login_as_test_user('apple');
   $user = login(); // 여전히 firebase_uid = 'banana' (캐시됨)
   ```

2. **다른 사용자로 테스트하려면 별도 테스트 파일을 생성하세요**
   ```
   tests/post/create_post_user_banana.test.php  # banana 사용자 테스트
   tests/post/create_post_user_apple.test.php   # apple 사용자 테스트
   ```

### 사용 가능한 테스트 사용자

데이터베이스에 다음과 같은 테스트 사용자들이 있습니다:

| Firebase UID | 사용자 이름 | 설명 |
|-------------|------------|------|
| `banana` | 바나나 | 기본 테스트 사용자 |
| `apple` | 애플 | 추가 테스트 사용자 |
| 기타 | - | 데이터베이스에서 확인 |

새로운 테스트 사용자를 생성하려면:

```php
// 테스트용 사용자 생성
$user = create_user_record([
    'firebase_uid' => 'test_user_' . time(),
    'display_name' => 'Test User',
]);

// 생성된 사용자로 로그인
login_as_test_user($user['firebase_uid']);
```

### 로그인 없이 테스트 시 발생하는 에러

로그인이 필요한 함수를 로그인 없이 호출하면 다음과 같은 에러가 발생합니다:

```
❌ PHP Fatal error: Uncaught ApiException: 로그인이 필요합니다.
```

**해결 방법**: 테스트 시작 부분에 `login_as_test_user()` 함수를 호출하세요.

### 완전한 테스트 예제

```php
<?php
// tests/post/create_post_with_login.test.php

include __DIR__ . '/../../init.php';

// 테스트 함수 로드
if (!function_exists('login_as_test_user')) {
    include __DIR__ . '/../../lib/test/test.functions.php';
}

echo "🧪 create_post() 함수 로그인 테스트\n";
echo "======================================================================\n\n";

try {
    // ========================================================================
    // 테스트 1: 로그인 없이 create_post() 호출 시 에러 발생 확인
    // ========================================================================
    echo "🧪 테스트 1: 로그인 없이 create_post() 호출 시 에러\n";

    try {
        $post = create_post(['category' => 'test']);
        echo "   ❌ 에러가 발생하지 않았습니다 (예상치 못한 동작)\n";
        exit(1);
    } catch (ApiException $e) {
        if ($e->getMessage() === '로그인이 필요합니다.') {
            echo "   ✅ 예상된 에러 발생: {$e->getMessage()}\n";
        } else {
            echo "   ❌ 다른 에러 발생: {$e->getMessage()}\n";
            exit(1);
        }
    }

    echo "\n";

    // ========================================================================
    // 테스트 2: 로그인 후 create_post() 호출 성공
    // ========================================================================
    echo "🧪 테스트 2: 로그인 후 create_post() 호출 성공\n";

    // 테스트 사용자로 로그인
    login_as_test_user('banana');

    // 로그인 확인
    $user = login();
    echo "   로그인된 사용자: {$user->display_name} (firebase_uid: {$user->firebase_uid})\n";

    // 게시글 생성
    $post = create_post([
        'category' => 'test',
        'title' => 'Test Post with Login',
        'content' => 'This post was created after login'
    ]);

    if ($post instanceof PostModel) {
        echo "   ✅ 게시글 생성 성공\n";
        echo "   - 게시글 ID: {$post->id}\n";
        echo "   - 제목: {$post->title}\n";
        echo "   - 작성자 ID: {$post->user_id}\n";
    } else {
        echo "   ❌ 게시글 생성 실패\n";
        exit(1);
    }

    echo "\n======================================================================\n";
    echo "🎉 모든 테스트 통과!\n";

} catch (Throwable $e) {
    echo "❌ 예외 발생: " . $e->getMessage() . "\n";
    exit(1);
}
```

### login_as_test_user() 함수 내부 동작

`login_as_test_user()` 함수는 내부적으로 다음과 같이 동작합니다:

1. `get_user_by_firebase_uid()` 함수로 firebase_uid로 사용자 조회
2. `generate_session_id()` 함수로 세션 ID 생성
3. `$_COOKIE[SESSION_ID]`에 세션 ID 설정
4. 다음 `login()` 함수 호출 시 세션 ID를 통해 사용자 인증

```php
// lib/test/test.functions.php

function login_as_test_user(string $firebase_uid = 'banana')
{
    $session_id = generate_session_id(get_user_by_firebase_uid($firebase_uid));
    $_COOKIE[SESSION_ID] = $session_id; // For immediate use in the same request
}
```

## 테스트 실행 방법

프로젝트 루트 폴더에서 아래와 같이 실행합니다:

```bash
php tests/db/db.connection.test.php
```

## 데이터베이스 설정 자동 선택

개발자 컴퓨터에서 테스트 실행 시 데이터베이스 설정이 자동으로 선택됩니다:

### DB 접속 흐름

1. **개발자 컴퓨터에서 실행 시**:
   - `is_dev_computer()` 함수가 `true` 반환
   - `etc/includes.php`에서 `etc/config/db.dev.config.php` 로드
   - 개발용 DB 설정 사용 (호스트: `sonub-mariadb` 또는 `127.0.0.1`)

2. **운영 서버에서 실행 시**:
   - `is_dev_computer()` 함수가 `false` 반환
   - `etc/includes.php`에서 `etc/config/db.config.php` 로드
   - 운영용 DB 설정 사용

### 설정 파일 구조

```
etc/
├── includes.php                    # 모든 파일 로드 (DB 설정 자동 선택)
└── config/
    ├── db.dev.config.php          # 개발용 DB 설정
    └── db.config.php              # 운영용 DB 설정
```

### includes.php의 DB 설정 로드 코드

```php
// 데이터베이스 설정 로드
if (is_dev_computer()) {
    include_once ROOT_DIR . '/etc/config/db.dev.config.php';
} else {
    include_once ROOT_DIR . '/etc/config/db.config.php';
}
```

## PHP Unit Test 작성 규칙

1. **외부 프레임워크 없이 순수 PHP로 작성**
   - PHPUnit, Pest 등 외부 테스트 프레임워크 사용 안 함
   - 간단한 assert 함수로 테스트 검증

2. **테스트 파일 위치 및 명명 규칙**
   - **반드시 `tests` 디렉토리에 저장** (최강력 규칙!)
   - 소스 코드와 동일한 구조로 저장
   - 파일 이름은 `.test.php`로 끝나야 함
   - 예시: `lib/db/db.php` → `tests/db/db.test.php`

3. **독립 실행 가능**
   - 각 테스트 파일은 PHP 명령어로 단독 실행 가능해야 함
   - `php tests/db/db.test.php` 형태로 실행

4. **명확한 출력 메시지**
   - 테스트 성공/실패 시 명확한 메시지 출력
   - 실패 시 원인 파악이 쉽도록 상세 정보 제공

### PHP Unit Test 코드 예제

```php
<?php
// tests/db/db.connection.test.php

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

// 테스트: DB 연결 확인
try {
    $conn = db_connection();
    echo "✅ DB 연결 성공\n";
    echo "   호스트: " . DB_HOST . "\n";
    echo "   데이터베이스: " . DB_NAME . "\n";
} catch (Exception $e) {
    echo "❌ DB 연결 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// 테스트: 간단한 쿼리 실행
try {
    $result = db()->select('1 as test')->get();
    if ($result && $result[0]['test'] === '1') {
        echo "✅ 쿼리 실행 성공\n";
    } else {
        echo "❌ 쿼리 결과 불일치\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ 쿼리 실행 실패: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 모든 테스트 통과!\n";
```

## PHP End-to-End (E2E) Test 작성 규칙

1. **외부 라이브러리 없이 순수 PHP로 작성**
   - Selenium, Puppeteer 등 외부 E2E 프레임워크 사용 안 함
   - `curl` 또는 `file_get_contents()` 함수를 사용하여 HTTP 요청

2. **테스트 대상 URL**
   - 개발 환경: `https://local.sonub.com/`
   - 모든 E2E 테스트는 이 URL에 요청을 보내 실행

3. **테스트 파일 위치 및 명명 규칙**
   - **반드시 `tests/e2e/` 디렉토리에 저장**
   - 파일 이름은 `.e2e.test.php`로 끝나야 함
   - 예시: `tests/e2e/user-login.e2e.test.php`

4. **검증 항목**
   - HTTP 상태 코드 (200, 404, 302 등)
   - 응답 본문에 특정 텍스트 포함 여부
   - HTML 요소 존재 여부
   - 페이지 리다이렉션 확인

5. **🔥🔥🔥 필수 레이아웃 요소 검증 (최강력 규칙) 🔥🔥🔥**
   - **모든 페이지 E2E 테스트는 다음 필수 레이아웃 요소를 반드시 검증해야 합니다**:
     - **`header#page-header`**: 페이지 헤더 존재 여부
     - **`<main>`**: 메인 콘텐츠 영역 존재 여부
     - **`footer#page-footer`**: 페이지 푸터 존재 여부
   - **이유**: `index.php`가 모든 페이지를 감싸는(wrap) 레이아웃 역할을 하므로, 모든 페이지에는 이 세 가지 요소가 반드시 존재해야 합니다
   - **위반 시**: 레이아웃 구조가 깨졌음을 의미하며 즉시 수정 필요

### PHP E2E Test 코드 예제 (curl 사용)

```php
<?php
// tests/e2e/user-login-page.e2e.test.php

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

$base_url = 'https://local.sonub.com';

// 테스트 1: 로그인 페이지 접근
echo "🧪 테스트 1: 로그인 페이지 접근\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/user/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 로컬 개발 환경용
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    echo "   ✅ HTTP 상태 코드: 200 (성공)\n";
} else {
    echo "   ❌ HTTP 상태 코드: $http_code (실패)\n";
    exit(1);
}

// 테스트 2: 필수 레이아웃 요소 확인 (최강력 규칙)
echo "🧪 테스트 2: 필수 레이아웃 요소 확인\n";

// header#page-header 확인
if (str_contains($response, '<header id="page-header"')) {
    echo "   ✅ header#page-header 존재\n";
} else {
    echo "   ❌ header#page-header를 찾을 수 없음\n";
    exit(1);
}

// <main> 태그 확인
if (str_contains($response, '<main')) {
    echo "   ✅ <main> 태그 존재\n";
} else {
    echo "   ❌ <main> 태그를 찾을 수 없음\n";
    exit(1);
}

// footer#page-footer 확인
if (str_contains($response, '<footer id="page-footer"')) {
    echo "   ✅ footer#page-footer 존재\n";
} else {
    echo "   ❌ footer#page-footer를 찾을 수 없음\n";
    exit(1);
}

// 테스트 3: 로그인 폼 요소 확인
echo "🧪 테스트 3: 로그인 폼 요소 확인\n";

if (str_contains($response, '<form') && str_contains($response, 'type="email"')) {
    echo "   ✅ 로그인 폼 존재\n";
} else {
    echo "   ❌ 로그인 폼을 찾을 수 없음\n";
    exit(1);
}

if (str_contains($response, '로그인') || str_contains($response, 'Login')) {
    echo "   ✅ 로그인 버튼 존재\n";
} else {
    echo "   ❌ 로그인 버튼을 찾을 수 없음\n";
    exit(1);
}

echo "\n🎉 모든 E2E 테스트 통과!\n";
```

### PHP E2E Test 코드 예제 (file_get_contents 사용)

```php
<?php
// tests/e2e/homepage.e2e.test.php

// 필수: init.php 포함
include __DIR__ . '/../../init.php';

$base_url = 'https://local.sonub.com';

// 테스트: 홈페이지 접근
echo "🧪 테스트: 홈페이지 접근\n";

// SSL 검증 비활성화 (로컬 개발 환경용)
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

$response = @file_get_contents($base_url . '/', false, $context);

if ($response === false) {
    echo "   ❌ 홈페이지 접근 실패\n";
    exit(1);
}

echo "   ✅ 홈페이지 접근 성공\n";

// 테스트: 필수 레이아웃 요소 확인 (최강력 규칙)
echo "🧪 테스트: 필수 레이아웃 요소 확인\n";

// header#page-header 확인
if (str_contains($response, '<header id="page-header"')) {
    echo "   ✅ header#page-header 존재\n";
} else {
    echo "   ❌ header#page-header를 찾을 수 없음\n";
    exit(1);
}

// <main> 태그 확인
if (str_contains($response, '<main')) {
    echo "   ✅ <main> 태그 존재\n";
} else {
    echo "   ❌ <main> 태그를 찾을 수 없음\n";
    exit(1);
}

// footer#page-footer 확인
if (str_contains($response, '<footer id="page-footer"')) {
    echo "   ✅ footer#page-footer 존재\n";
} else {
    echo "   ❌ footer#page-footer를 찾을 수 없음\n";
    exit(1);
}

// 테스트: 페이지 콘텐츠 확인
if (str_contains($response, '<html') && str_contains($response, '</html>')) {
    echo "   ✅ 유효한 HTML 응답\n";
} else {
    echo "   ❌ 유효하지 않은 HTML 응답\n";
    exit(1);
}

echo "\n🎉 모든 E2E 테스트 통과!\n";
```

### E2E 테스트 실행 방법

```bash
# 프로젝트 루트에서 실행
php tests/e2e/user-login-page.e2e.test.php
php tests/e2e/homepage.e2e.test.php
```

## Playwright End-to-End (E2E) Test 작성 규칙

### Playwright를 사용하는 이유

**🔥🔥🔥 중요: 대부분의 경우 PHP Unit Test와 PHP E2E Test로 충분합니다 🔥🔥🔥**

하지만 PHP를 통해서는 다음과 같은 작업을 할 수 없습니다:

- **웹 브라우저 열기**: 실제 브라우저를 자동으로 실행할 수 없음
- **사용자 타이핑 시뮬레이션**: 입력 필드에 키보드 입력을 시뮬레이션할 수 없음
- **폼 전송 시뮬레이션**: 폼 submit 버튼 클릭 및 전송을 시뮬레이션할 수 없음
- **JavaScript 실행**: HTML 페이지에 포함된 JavaScript를 실행할 수 없음
- **브라우저 이벤트**: 클릭, 스크롤, 드래그 앤 드롭 등의 브라우저 이벤트를 트리거할 수 없음

이러한 이유로 **브라우저 상호작용이 필수적인 경우에만** Playwright를 통해 E2E 테스트를 수행합니다.

### 1. 테스트 파일 작성 및 저장

- Playwright 테스트는 **TypeScript**로 작성합니다
- 테스트 파일은 **`./tests/playwright/e2e/` 폴더**에 저장합니다
- 파일 이름은 `.spec.ts`로 끝나야 합니다
- 예시: `tests/playwright/e2e/user-login.spec.ts`

### 2. 라우팅 규칙 (PHP E2E 테스트와 동일)

Playwright E2E 테스트도 PHP E2E 테스트와 동일한 라우팅 규칙을 따릅니다.

**URL과 페이지 파일 매핑:**

| 페이지 파일 경로 | Playwright 테스트 URL |
|---------------|---------------------|
| `/page/index.php` | `https://local.sonub.com/` |
| `/page/abc/def.php` | `https://local.sonub.com/abc/def` |
| `/page/user/login.php` | `https://local.sonub.com/user/login` |
| `/page/post/list.php` | `https://local.sonub.com/post/list` |
| `/page/admin/dashboard.php` | `https://local.sonub.com/admin/dashboard` |

**예시:**
- `/page/abc/def.php` 파일을 테스트하려면 → `https://local.sonub.com/abc/def`로 접속
- `/page/user/profile.php` 파일을 테스트하려면 → `https://local.sonub.com/user/profile`로 접속

### 3. Playwright 테스트 코드 예제

#### 예제 1: 기본 페이지 접근 및 요소 확인

```typescript
// tests/playwright/e2e/homepage.spec.ts

import { test, expect } from '@playwright/test';

test('홈페이지 접근 테스트', async ({ page }) => {
  // 홈페이지 접속
  await page.goto('https://local.sonub.com/');

  // 페이지 제목 확인
  await expect(page).toHaveTitle(/Sonub/);

  // 특정 요소 존재 확인
  const header = page.locator('header');
  await expect(header).toBeVisible();
});
```

#### 예제 2: 폼 전송 테스트 (PHP로 불가능)

```typescript
// tests/playwright/e2e/user-login.spec.ts

import { test, expect } from '@playwright/test';

test('로그인 폼 전송 테스트', async ({ page }) => {
  // 로그인 페이지 접속
  await page.goto('https://local.sonub.com/user/login');

  // 이메일 입력 필드에 타이핑
  await page.fill('input[type="email"]', 'test@example.com');

  // 비밀번호 입력 필드에 타이핑
  await page.fill('input[type="password"]', 'password123');

  // 로그인 버튼 클릭
  await page.click('button[type="submit"]');

  // 리다이렉션 확인 (로그인 성공 후 프로필 페이지로 이동)
  await expect(page).toHaveURL(/\/user\/profile/);

  // 로그인 성공 메시지 확인
  const successMessage = page.locator('.alert-success');
  await expect(successMessage).toBeVisible();
});
```

#### 예제 3: JavaScript 실행 확인 테스트 (PHP로 불가능)

```typescript
// tests/playwright/e2e/post-create.spec.ts

import { test, expect } from '@playwright/test';

test('게시글 작성 JavaScript 동작 테스트', async ({ page }) => {
  // 게시글 작성 페이지 접속
  await page.goto('https://local.sonub.com/post/create');

  // 카테고리 선택 (Vue.js로 구현된 드롭다운)
  await page.click('.category-selector');
  await page.click('.category-option[data-value="discussion"]');

  // 제목 입력
  await page.fill('input[name="title"]', '테스트 게시글');

  // 내용 입력
  await page.fill('textarea[name="content"]', '테스트 내용입니다.');

  // 파일 업로드 (JavaScript로 처리되는 업로드)
  const fileInput = page.locator('input[type="file"]');
  await fileInput.setInputFiles('./test-files/sample.jpg');

  // 업로드 프로그레스 바 확인 (JavaScript로 구현)
  const progressBar = page.locator('.upload-progress');
  await expect(progressBar).toBeVisible();

  // 업로드 완료 대기
  await expect(progressBar).toHaveText('100%');

  // 폼 전송
  await page.click('button[type="submit"]');

  // 성공 메시지 확인
  await expect(page.locator('.toast-success')).toBeVisible();
});
```

### 4. Playwright 테스트 실행 방법

```bash
# 모든 Playwright 테스트 실행
npx playwright test

# 특정 테스트 파일만 실행
npx playwright test tests/playwright/e2e/user-login.spec.ts

# UI 모드로 실행 (브라우저 화면 확인하면서 테스트)
npx playwright test --ui

# 디버그 모드로 실행
npx playwright test --debug

# 특정 브라우저에서만 실행
npx playwright test --project=chromium
npx playwright test --project=firefox
npx playwright test --project=webkit
```

### 5. PHP E2E vs Playwright E2E 선택 기준

**✅ PHP E2E 테스트를 사용하는 경우:**
- 페이지가 정상적으로 로드되는지 확인
- HTTP 상태 코드 확인 (200, 404, 302 등)
- HTML 요소가 존재하는지 확인 (예: `.title` 클래스가 있는지)
- 특정 텍스트가 페이지에 포함되어 있는지 확인
- API 응답 확인

**✅ Playwright E2E 테스트를 사용하는 경우:**
- 사용자가 입력 필드에 타이핑하는 시나리오
- 폼 전송 및 전송 후 동작 확인
- 버튼 클릭 및 클릭 후 JavaScript 동작 확인
- JavaScript로 구현된 UI 요소 상호작용 (드롭다운, 모달 등)
- 파일 업로드 및 업로드 프로그레스 확인
- 페이지 리다이렉션 후 동작 확인
- 브라우저 이벤트 (스크롤, 드래그 앤 드롭 등)

**예시:**

| 테스트 요청 | 사용할 테스트 | 이유 |
|-----------|------------|-----|
| "페이지에서 .title 클래스가 잘 보이는지 테스트" | PHP E2E | HTML 요소 존재 여부만 확인 |
| "로그인 폼 전송 테스트" | Playwright E2E | 사용자 입력 + 폼 전송 필요 |
| "게시글 작성 후 목록에 표시되는지 테스트" | Playwright E2E | 폼 전송 + JavaScript 동작 확인 |
| "홈페이지가 200 상태 코드를 반환하는지 테스트" | PHP E2E | HTTP 상태 코드만 확인 |
| "파일 업로드 프로그레스 바 테스트" | Playwright E2E | JavaScript 동작 + UI 상호작용 |

### 6. 올바른 저장 위치 예시

```
✅ tests/playwright/e2e/user-login.spec.ts       # 로그인 페이지 Playwright E2E Test
✅ tests/playwright/e2e/post-create.spec.ts      # 게시글 작성 Playwright E2E Test
✅ tests/playwright/e2e/file-upload.spec.ts      # 파일 업로드 Playwright E2E Test
```

### 7. 잘못된 저장 위치 예시

```
❌ user-login.spec.ts                            # 루트 폴더 (절대 금지!)
❌ tests/e2e/user-login.spec.ts                  # PHP E2E 폴더 (절대 금지!)
❌ lib/user/user-login.spec.ts                   # 소스 코드 폴더 (절대 금지!)
```

### 8. Playwright 설정

Playwright 설정 파일은 프로젝트 루트의 `playwright.config.ts`에 있습니다.

**주요 설정:**
- **baseURL**: `https://local.sonub.com/` (개발 환경 URL)
- **브라우저**: Chromium, Firefox, WebKit
- **스크린샷**: 실패 시 자동 스크린샷 저장
- **비디오**: 실패 시 테스트 비디오 저장

**⚠️ 중요**: Playwright 테스트는 HTTPS 인증서 검증을 비활성화(`ignoreHTTPSErrors: true`)하여 로컬 개발 환경에서 자체 서명된 인증서로 테스트할 수 있습니다.
