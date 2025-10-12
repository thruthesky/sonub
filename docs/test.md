# PHP 테스트 가이드

## 목차
- [테스트 환경](#테스트-환경)
  - [라우팅 규칙 (E2E 테스트 필수 숙지!)](#라우팅-규칙-e2e-테스트-필수-숙지)
- [테스트 종류](#테스트-종류)
- [테스트 파일 저장 위치 - 최강력 규칙](#테스트-파일-저장-위치---최강력-규칙)
- [테스트 코드 작성 시 필수 사항](#테스트-코드-작성-시-필수-사항)
- [테스트 실행 방법](#테스트-실행-방법)
- [데이터베이스 설정 자동 선택](#데이터베이스-설정-자동-선택)
- [PHP Unit Test 작성 규칙](#php-unit-test-작성-규칙)
- [PHP End-to-End (E2E) Test 작성 규칙](#php-end-to-end-e2e-test-작성-규칙)

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

Sonub 프로젝트에서는 두 가지 방식의 테스트를 사용합니다:

### 1. PHP Unit Test (단위 테스트)

**대상**: 함수, 로직, 클래스 메서드 등의 내부 동작

- 외부 라이브러리 없이 순수 PHP로 작성
- `php tests/xxx/xxx.test.php` 형식으로 CLI에서 직접 실행
- 함수의 입력/출력, 비즈니스 로직 검증
- 데이터베이스 쿼리 결과 검증

**사용 시기**: "함수 테스트", "로직 테스트", "DB 쿼리 테스트" 요청 시

### 2. PHP End-to-End (E2E) Test (통합 테스트)

**대상**: 페이지, UI 요소, 사용자 시나리오

- 외부 라이브러리 없이 순수 PHP로 작성
- `curl` 또는 `file_get_contents()` 함수를 사용하여 HTTP 요청
- `https://local.sonub.com/`에 실제 요청을 보내 응답 검증
- HTML 응답, HTTP 상태 코드, 페이지 콘텐츠 검증

**사용 시기**: "페이지 테스트", "UI 테스트", "요소 테스트" 요청 시

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

// 테스트 2: 로그인 폼 요소 확인
echo "🧪 테스트 2: 로그인 폼 요소 확인\n";

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

