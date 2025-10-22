# PHP 코딩 가이드라인

## 목차

- [개요](#개요)
- [일반 코딩 표준](#일반-코딩-표준)
  - [API 함수 규칙](#api-함수-규칙)
  - [에러 처리 표준](#에러-처리-표준)
- [모듈 로딩](#모듈-로딩)

---

## 개요

Sonub의 PHP 개발 시 준수해야 할 코딩 표준과 규칙입니다.

PHP 개발 관련 자세한 내용은 다음 문서를 참조하세요:
- 데이터베이스 개발: [docs/database.md](./database.md)
- 다국어 번역: [docs/translation.md](./translation.md)
- 테스트 작성: [docs/test.md](./test.md)

이 문서는 다음 내용을 포함합니다:

- **일반 코딩 표준**: 들여쓰기, UTF-8 인코딩, 주석 작성 규칙
- **API 함수 규칙**: 모든 API 함수는 배열 파라미터 하나만 받아야 함
- **에러 처리 표준**: `error()` 함수를 사용한 표준화된 에러 처리
- **모듈 로딩**: 페이지별 전처리 작업을 위한 모듈 시스템

---

## 일반 코딩 표준

- 들여쓰기는 탭 대신 4개의 공백을 사용합니다.
- BOM 없는 UTF-8 인코딩을 사용합니다.
- 복잡한 로직에는 주석과 문서화를 작성합니다.
- 모든 주석과 문서는 **반드시 한국어**로 작성합니다.

### API 함수 규칙

**🔥🔥🔥 최강력 규칙: 모든 함수는 API 호출이 될 수 있으며, API 함수는 반드시 배열 파라미터 하나만 받아야 합니다 🔥🔥🔥**

Sonub에서는 모든 글로벌 함수가 잠재적으로 API 엔드포인트가 될 수 있습니다. `api.php`를 통해 JavaScript에서 PHP 함수를 직접 호출할 수 있기 때문입니다.

**핵심 규칙:**
- API로 호출될 수 있는 모든 함수는 **배열 파라미터 하나**만 받아야 합니다
- 배열 파라미터는 `$input` 또는 `$params`로 명명합니다
- 함수 내부에서 배열 키로 필요한 값을 추출합니다

**올바른 예제:**
```php
function request_friend(array $input): array
{
    $me = (int)($input['me'] ?? 0);
    $other = (int)($input['other'] ?? 0);
    
    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }
    
    return ['message' => '친구 요청을 보냈습니다'];
}
```

**잘못된 예제 (절대 금지):**
```php
// ❌ 여러 개의 스칼라 파라미터
function request_friend(int $me, int $other): void {
    // JavaScript에서 호출 시 에러 발생!
}
```

상세한 내용은 coding-guideline.md의 이전 버전 또는 Git 히스토리를 참조하세요.

### 에러 처리 표준

**🔥🔥🔥 최강력 규칙: 모든 에러는 반드시 `error()` 함수를 사용하여 throw해야 합니다 🔥🔥🔥**

- 모든 함수에서 에러가 발생하면 `error()` 함수를 호출하여 `ApiException`을 throw
- `error()` 함수는 `lib/functions.php`에 정의되어 있음
- `api.php`에서 try/catch 블록으로 `ApiException`을 catch하여 JSON 에러 응답으로 변환

**함수 시그니처:**
```php
function error(
    string $code = 'unknown', 
    string $message = '', 
    array $data = [], 
    int $response_code = 400
): never
```

**올바른 사용 예제:**
```php
function getUserInfo(array $params): array {
    $user_id = $params['user_id'] ?? null;
    
    if (empty($user_id)) {
        error('invalid-user-id', '사용자 ID가 유효하지 않습니다');
    }
    
    $user = db()->select('*')->from('users')->where('id = ?', [$user_id])->first();
    
    if (!$user) {
        error('user-not-found', '사용자를 찾을 수 없습니다', ['user_id' => $user_id], 404);
    }
    
    return $user;
}
```

**일반적인 에러 코드:**
- `invalid-input`: 잘못된 입력값
- `user-not-found`: 사용자를 찾을 수 없음
- `permission-denied`: 권한 부족
- `already-exists`: 이미 존재함
- `database-error`: 데이터베이스 오류

---

## 모듈 로딩

**⚠️⚠️⚠️ 중요: 페이지별 전처리 작업을 위한 모듈 시스템 ⚠️⚠️⚠️**

### 모듈이란?

- **정의**: 각 페이지 스크립트가 실행되기 이전에 실행되는 코드 파일
- **용도**: PHP 헤더 설정, 쿠키 처리, 인증 확인 등 각종 전처리 작업 수행
- **실행 시점**: 페이지 콘텐츠가 로드되기 이전에 `layout.php`에서 자동으로 로드됨

### 모듈 파일 명명 규칙

- **파일명 패턴**: `*.module.php`
- **파일 위치**: 페이지 스크립트 파일과 동일한 폴더
- **자동 로딩**: `layout.php`가 페이지 경로를 기반으로 모듈 파일을 자동으로 검색하고 include

### 예제: 로그인 확인 모듈

```php
<?php
// page/user/profile.module.php

// 로그인 확인
$user = login();

if (!$user) {
    header('Location: /user/login');
    exit;
}

// 로그인한 사용자 정보를 페이지에서 사용할 수 있도록 설정
$profile_user = $user;
```

```php
<?php
// page/user/profile.php

// profile.module.php에서 로그인을 확인했으므로
// 이 파일에서는 $profile_user를 안전하게 사용 가능
?>
<div class="container">
    <h1><?= $profile_user->displayFullName() ?>님의 프로필</h1>
</div>
```

### 모듈 사용 시 주의사항

**✅ 모듈에서 할 수 있는 작업:**
- HTTP 헤더 설정 (`header()` 함수)
- 쿠키 설정/삭제 (`setcookie()` 함수)
- 인증 확인 및 리다이렉트
- 권한 검증
- 페이지에서 사용할 데이터 사전 로드

**❌ 모듈에서 하지 말아야 할 작업:**
- HTML 출력 (헤더 전송 후 출력 불가)
- 페이지 레이아웃 관련 작업
- JavaScript나 CSS 코드 삽입

### 모듈이 필요한 경우

- 페이지 접근 전 로그인 확인이 필요한 경우
- 관리자 권한 등 특정 권한 검증이 필요한 경우
- 페이지 로드 전 리다이렉트가 필요한 경우
- HTTP 헤더나 쿠키 설정이 필요한 경우

---
