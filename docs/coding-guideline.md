# Sonub 코딩 가이드라인

## 목차

- [Sonub 코딩 가이드라인](#sonub-코딩-가이드라인)
  - [목차](#목차)
  - [개요](#개요)
    - [명칭](#명칭)
  - [일반 코딩 표준](#일반-코딩-표준)
    - [에러 처리 표준](#에러-처리-표준)
      - [error() 함수 시그니처](#error-함수-시그니처)
      - [에러 응답 형식](#에러-응답-형식)
      - [올바른 에러 처리 예제](#올바른-에러-처리-예제)
      - [에러 체크 예제](#에러-체크-예제)
      - [잘못된 에러 처리 예제 (절대 금지)](#잘못된-에러-처리-예제-절대-금지)
      - [일반적인 에러 코드 규칙](#일반적인-에러-코드-규칙)
      - [HTTP 응답 코드 가이드](#http-응답-코드-가이드)
      - [위반 시 결과](#위반-시-결과)
  - [사용자 관리 및 검색](#사용자-관리-및-검색)
    - [개요](#개요-1)
    - [list_users() 함수](#list_users-함수)
  - [디자인 및 스타일링 표준](#디자인-및-스타일링-표준)
  - [JavaScript 프레임워크 - Vue.js 3.x](#javascript-프레임워크---vuejs-3x)
    - [Vue.js 사용 방식](#vuejs-사용-방식)
    - [MPA 방식의 장점](#mpa-방식의-장점)
    - [자동 리소스 로딩](#자동-리소스-로딩)
      - [자동 로딩 규칙](#자동-로딩-규칙)
      - [JavaScript에서 즉시 사용 가능한 객체](#javascript에서-즉시-사용-가능한-객체)
      - [자동 로딩 활용 가이드](#자동-로딩-활용-가이드)
      - [주의사항](#주의사항)
    - [Vue.js 기본 사용법](#vuejs-기본-사용법)
      - [기본 Vue 앱 생성](#기본-vue-앱-생성)
      - [HTML에서 Vue 사용](#html에서-vue-사용)
      - [컴포넌트 정의 및 사용](#컴포넌트-정의-및-사용)
      - [Firebase와 함께 사용](#firebase와-함께-사용)
      - [중요 사항](#중요-사항)
  - [프레임워크 및 라이브러리 저장 가이드라인](#프레임워크-및-라이브러리-저장-가이드라인)
    - [완전한 프레임워크 패키지](#완전한-프레임워크-패키지)
    - [단일 JavaScript 라이브러리](#단일-javascript-라이브러리)
  - [페이지별 CSS 및 JavaScript 자동 로딩](#페이지별-css-및-javascript-자동-로딩)
    - [개요](#개요-1)
    - [작동 방식](#작동-방식)
    - [사용 가이드라인](#사용-가이드라인)
      - [자동 포함](#자동-포함)
      - [파일 명명 규칙](#파일-명명-규칙)
      - [사용 시기](#사용-시기)
    - [예제](#예제)
      - [예제 1: 지원 페이지 만들기](#예제-1-지원-페이지-만들기)
      - [예제 2: 사용자 로그인 페이지](#예제-2-사용자-로그인-페이지)
      - [출력 예제](#출력-예제)
  - [공유 JavaScript 파일 로딩 - load_deferred_js()](#공유-javascript-파일-로딩---load_deferred_js)
    - [개요](#개요-2)
    - [필요한 이유](#필요한-이유)
    - [함수 위치 및 시그니처](#함수-위치-및-시그니처)
    - [사용 방법](#사용-방법)
      - [기본 사용법](#기본-사용법)
      - [실제 사용 예제](#실제-사용-예제)
    - [우선순위 시스템](#우선순위-시스템)
    - [잘못된 사용 예제 (절대 금지)](#잘못된-사용-예제-절대-금지)
    - [올바른 사용 예제](#올바른-사용-예제)
    - [장점](#장점)
    - [주의사항](#주의사항-1)
    - [사용 시나리오](#사용-시나리오)
  - [Firebase 통합 가이드라인](#firebase-통합-가이드라인)
    - [로딩 동작](#로딩-동작)
    - [JavaScript에서 사용](#javascript에서-사용)
    - [Vue.js에서 Firebase 실시간 업데이트](#vuejs에서-firebase-실시간-업데이트)
  - [라우팅 규칙](#라우팅-규칙)
    - [Nginx 라우팅 설정](#nginx-라우팅-설정)
    - [라우팅 동작 방식](#라우팅-동작-방식)
    - [URL과 페이지 파일 매핑](#url과-페이지-파일-매핑)
    - [라우팅 예제](#라우팅-예제)
    - [중요 사항](#중요-사항-1)
    - [라우팅 시스템의 장점](#라우팅-시스템의-장점)
  - [개발 시스템 시작](#개발-시스템-시작)
    - [빠른 시작 (필수 명령어)](#빠른-시작-필수-명령어)
    - [데이터베이스 관리](#데이터베이스-관리)
    - [Docker Compose 사용](#docker-compose-사용)
      - [사전 요구 사항](#사전-요구-사항)
      - [빠른 시작 명령어](#빠른-시작-명령어)
      - [로컬 개발 도메인 설정](#로컬-개발-도메인-설정)
      - [기본 구성](#기본-구성)
      - [디렉토리 구조](#디렉토리-구조)
      - [주요 기능](#주요-기능)
      - [문제 해결](#문제-해결)
    - [핫 리로드 개발 서버](#핫-리로드-개발-서버)
      - [기능](#기능)
      - [설정 및 사용](#설정-및-사용)
      - [구성](#구성)
      - [SSL 인증서](#ssl-인증서)
  - [레이아웃 파일 구조](#레이아웃-파일-구조)
    - [Sonub 메인 레이아웃 파일](#sonub-메인-레이아웃-파일)
    - [파일 구조 설명](#파일-구조-설명)
    - [레이아웃 작업 시 필수 규칙](#레이아웃-작업-시-필수-규칙)
    - [작업 예시](#작업-예시)
  - [URL 및 페이지 링크 관리 규칙](#url-및-페이지-링크-관리-규칙)
    - [URL 함수 필수 사용 규칙](#url-함수-필수-사용-규칙)
    - [제공되는 URL 함수 목록](#제공되는-url-함수-목록)
    - [올바른 URL 사용 예제](#올바른-url-사용-예제)
    - [잘못된 URL 사용 예제 (절대 금지)](#잘못된-url-사용-예제-절대-금지)
    - [위반 시 결과](#위반-시-결과-1)
    - [새로운 페이지 추가 시 절차](#새로운-페이지-추가-시-절차)
  - [CSS 및 디자인 규칙](#css-및-디자인-규칙)
    - [Sonub 기본 CSS 파일](#sonub-기본-css-파일)
    - [디자인 작업 시 필수 규칙](#디자인-작업-시-필수-규칙)
      - [1️⃣ 레이아웃 포지션 관련 (인라인 클래스 사용)](#1️⃣-레이아웃-포지션-관련-인라인-클래스-사용)
      - [2️⃣ 스타일 관련 (CSS 파일 사용)](#2️⃣-스타일-관련-css-파일-사용)
    - [CSS 및 JavaScript 파일 분리 규칙](#css-및-javascript-파일-분리-규칙)
      - [페이지 파일 (./page/**/*.php)](#페이지-파일-pagephp)
      - [위젯 파일 (./widgets/**/*.php)](#위젯-파일-widgetsphp)
      - [페이지 vs 위젯 비교표](#페이지-vs-위젯-비교표)
    - [올바른 CSS 사용 예제](#올바른-css-사용-예제)
    - [잘못된 CSS 사용 예제 (절대 금지)](#잘못된-css-사용-예제-절대-금지)
    - [위반 시 결과](#위반-시-결과-2)
    - [작업 체크리스트](#작업-체크리스트)
  - [Firebase 테스트 계정](#firebase-테스트-계정)
    - [테스트 계정 개요](#테스트-계정-개요)
    - [테스트 계정 특징](#테스트-계정-특징)
    - [테스트 계정 목록](#테스트-계정-목록)
    - [사용 예시](#사용-예시)
    - [주의사항](#주의사항-1)
    - [활용 시나리오](#활용-시나리오)
  - [필수 언어 사용 규칙](#필수-언어-사용-규칙)
    - [주석 및 텍스트 작성 규칙](#주석-및-텍스트-작성-규칙)
    - [예외 사항](#예외-사항)
    - [올바른 예제](#올바른-예제)
    - [잘못된 예제 (절대 금지)](#잘못된-예제-절대-금지)
    - [위반 시 결과](#위반-시-결과-3)
  - [모듈 로딩](#모듈-로딩)
    - [모듈이란?](#모듈이란)
    - [모듈 파일 명명 규칙](#모듈-파일-명명-규칙)
    - [파일 구조 예시](#파일-구조-예시)
    - [모듈 로딩 동작 방식](#모듈-로딩-동작-방식)
    - [모듈 사용 예제](#모듈-사용-예제)
      - [예제 1: 로그인 확인 모듈](#예제-1-로그인-확인-모듈)
      - [예제 2: 로그아웃 처리 모듈](#예제-2-로그아웃-처리-모듈)
      - [예제 3: 관리자 권한 확인 모듈](#예제-3-관리자-권한-확인-모듈)
      - [예제 4: 쿠키 및 헤더 설정 모듈](#예제-4-쿠키-및-헤더-설정-모듈)
      - [예제 5: JSON 응답 후 즉시 종료](#예제-5-json-응답-후-즉시-종료)
    - [모듈 사용 시 주의사항](#모듈-사용-시-주의사항)
    - [모듈이 필요한 경우](#모듈이-필요한-경우)
    - [모듈이 불필요한 경우](#모듈이-불필요한-경우)

---

## 개요

- Sonub는 Social Network Hub의 약자로, 소셜 네트워크 허브입니다.
- Sonub는 필고(PHILGO) 프로젝트의 일부로 개발되고 있습니다.
- 이 문서는 Sonub 웹사이트 개발 시 준수해야 할 코딩 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.

### 명칭

- 한글 명칭: 소너브
- 한글 약칭: 소넙
- 영문 명칭: Sonub
- 영문 약칭: Sonub

---

## 일반 코딩 표준

- 들여쓰기는 탭 대신 4개의 공백을 사용합니다.
- BOM 없는 UTF-8 인코딩을 사용합니다.
- 복잡한 로직에는 주석과 문서화를 작성합니다.
- 모든 주석과 문서는 **반드시 한국어**로 작성합니다.

### 에러 처리 표준

**🔥🔥🔥 최강력 규칙: 모든 에러는 반드시 `error()` 함수를 사용하여 throw해야 합니다 🔥🔥🔥**

- **✅ 필수**: 모든 함수에서 에러가 발생하면 **반드시** `error()` 함수를 호출하여 `ApiException`을 throw
- **✅ 필수**: `error()` 함수는 `lib/functions.php`에 정의되어 있으며, 내부적으로 `ApiException`을 throw
- **✅ 필수**: `ApiException` 클래스는 `lib/ApiException.php`에 정의되어 있음
- **✅ 필수**: `api.php`에서 try/catch 블록으로 `ApiException`을 catch하여 JSON 에러 응답으로 변환
- **✅ 필수**: 모든 API 함수는 에러 발생 시 `error()` 함수를 통해 예외를 throw
- **❌ 금지**: 에러 발생 시 직접 배열을 만들어 리턴하는 것 금지
- **❌ 금지**: `error()` 함수를 사용하지 않고 직접 Exception을 throw하는 것 금지

#### error() 함수 시그니처

```php
/**
 * API 에러를 throw하는 함수
 *
 * 모든 API 함수에서 에러 발생 시 이 함수를 호출하여 ApiException을 throw합니다.
 * api.php에서 이 Exception을 catch하여 JSON 에러 응답으로 변환합니다.
 *
 * @param string $code 에러 코드 (kebab-case 형식, 예: 'user-not-found', 'invalid-input')
 * @param string $message 에러 메시지 (사용자에게 표시될 메시지)
 * @param array $data 추가 에러 데이터 (선택사항)
 * @param int $response_code HTTP 응답 코드 (기본값: 400)
 * @return never 이 함수는 항상 Exception을 throw하므로 절대 반환하지 않습니다
 * @throws ApiException
 */
function error(string $code = 'unknown', string $message = '', array $data = [], int $response_code = 400): never
```

#### 에러 응답 형식

`error()` 함수를 호출하면 `ApiException`이 throw되고, `api.php`에서 catch하여 다음과 같은 형식의 JSON 응답을 생성합니다:

```json
{
    "error_code": "user-not-found",
    "error_message": "사용자를 찾을 수 없습니다",
    "error_data": {},
    "error_response_code": 404,
    "func": "getUserInfo"
}
```

#### 올바른 에러 처리 예제

```php
<?php
/**
 * 사용자 정보를 조회하는 함수
 *
 * @param array $params HTTP 파라미터 배열
 * @return array 성공 시 사용자 정보
 * @throws ApiException 에러 발생 시
 */
function getUserInfo($params) {
    $user_id = $params['user_id'] ?? null;

    // 입력값 검증 - 에러 발생 시 ApiException throw
    if (empty($user_id)) {
        error('invalid-user-id', '사용자 ID가 유효하지 않습니다');
    }

    // 데이터베이스에서 사용자 조회
    $user = db()->select('*')->from('users')->where('id = ?', [$user_id])->first();

    // 사용자가 없으면 ApiException throw
    if (!$user) {
        error('user-not-found', '사용자를 찾을 수 없습니다', ['user_id' => $user_id], 404);
    }

    // 성공 시 사용자 정보 리턴
    return $user;
}

/**
 * 게시글을 작성하는 함수
 *
 * @param array $params HTTP 파라미터 배열
 * @return array 성공 시 게시글 정보
 * @throws ApiException 에러 발생 시
 */
function createPost($params) {
    $title = $params['title'] ?? '';
    $content = $params['content'] ?? '';

    // 입력값 검증 - 에러 발생 시 ApiException throw
    if (empty($title)) {
        error('missing-title', '제목을 입력해주세요');
    }

    if (empty($content)) {
        error('missing-content', '내용을 입력해주세요');
    }

    // 제목 길이 검증
    if (strlen($title) > 100) {
        error('title-too-long', '제목은 100자를 초과할 수 없습니다', ['max_length' => 100]);
    }

    // 게시글 작성
    $post_id = db()->insert([
        'title' => $title,
        'content' => $content,
        'created_at' => time(),
    ])->into('posts');

    if (!$post_id) {
        error('create-failed', '게시글 작성에 실패했습니다', [], 500);
    }

    // 성공 시 게시글 정보 리턴
    return [
        'post_id' => $post_id,
        'title' => $title,
        'content' => $content,
    ];
}
?>
```

#### API 호출 흐름

**1. 클라이언트에서 API 호출:**

```javascript
// JavaScript에서 API 호출
const result = await func('getUserInfo', { user_id: 123 });
console.log('사용자:', result);
```

**2. api.php에서 함수 실행 및 에러 처리:**

```php
try {
    // getUserInfo() 함수 호출
    $res = getUserInfo(http_params());

    // 성공 시 JSON 응답
    echo json_encode(['func' => 'getUserInfo', ...$res]);
} catch (ApiException $e) {
    // error() 함수로 throw된 ApiException을 catch
    http_response_code($e->getErrorResponseCode());
    echo json_encode($e->toArray());
}
```

**3. 클라이언트에서 에러 처리:**

```javascript
try {
    const user = await func('getUserInfo', { user_id: 999 });
    console.log('사용자:', user);
} catch (error) {
    // error() 함수로 throw된 에러를 catch
    console.error('에러 코드:', error.code);
    console.error('에러 메시지:', error.message);
}
```

#### 잘못된 에러 처리 예제 (절대 금지)

```php
<?php
// ❌ 절대 금지: 직접 배열을 만들어 에러 리턴
function getUserInfo($params) {
    if (!$params['user_id']) {
        // error() 함수를 사용하지 않고 직접 배열 리턴 (잘못됨)
        return [
            'error' => true,
            'message' => '사용자 ID가 없습니다'
        ];
    }
}

// ❌ 절대 금지: error() 함수를 사용하지 않고 직접 Exception throw
function getUserInfo($params) {
    if (!$params['user_id']) {
        // error() 함수 대신 Exception을 직접 throw (잘못됨)
        throw new Exception('사용자 ID가 없습니다');
    }
}

// ❌ 절대 금지: null이나 false만 리턴
function getUserInfo($params) {
    if (!$params['user_id']) {
        // 에러 정보 없이 null만 리턴 (잘못됨)
        return null;
    }
}

// ❌ 절대 금지: die()나 exit() 사용
function getUserInfo($params) {
    if (!$params['user_id']) {
        // 스크립트 실행 중단 (잘못됨)
        die('사용자 ID가 없습니다');
    }
}

// ✅ 올바른 방법: error() 함수 사용
function getUserInfo($params) {
    if (!$params['user_id']) {
        // error() 함수로 ApiException throw
        error('invalid-user-id', '사용자 ID가 없습니다');
    }
}
?>
```

#### 일반적인 에러 코드 규칙

에러 코드는 다음 규칙을 따릅니다:

- **형식**: `kebab-case` (소문자와 하이픈 사용)
- **명확성**: 에러의 원인을 명확하게 표현
- **일관성**: 유사한 에러는 유사한 코드 사용

**일반적인 에러 코드 예시:**

- `invalid-input`: 잘못된 입력값
- `missing-parameter`: 필수 매개변수 누락
- `user-not-found`: 사용자를 찾을 수 없음
- `permission-denied`: 권한 부족
- `already-exists`: 이미 존재함
- `database-error`: 데이터베이스 오류
- `authentication-failed`: 인증 실패
- `session-expired`: 세션 만료

#### HTTP 응답 코드 가이드

`error()` 함수의 네 번째 매개변수는 HTTP 응답 코드입니다:

- `400`: Bad Request (잘못된 요청 - 기본값)
- `401`: Unauthorized (인증 실패)
- `403`: Forbidden (권한 부족)
- `404`: Not Found (리소스를 찾을 수 없음)
- `409`: Conflict (충돌 - 이미 존재함)
- `500`: Internal Server Error (서버 내부 오류)

#### 위반 시 결과

- 에러 형식이 일관되지 않아 처리가 어려움
- API 응답이 표준화되지 않음
- 에러 추적 및 디버깅이 어려움
- 클라이언트에서 에러 처리 로직이 복잡해짐

---

## 사용자 관리 및 검색

### 개요

사용자 관리 및 검색 기능은 `lib/user/user.crud.php`의 `list_users()` 함수를 통해 제공됩니다.

### list_users() 함수

사용자 목록을 조회하고 다양한 조건으로 필터링할 수 있는 함수입니다.

**기본 사용법:**

```php
// $_GET을 직접 전달하여 사용 (권장)
$result = list_users(array_merge($_GET, ['per_page' => 10]));

// 수동으로 파라미터 지정
$result = list_users([
    'gender' => 'M',           // 성별 필터 (M/F)
    'age_start' => 25,         // 시작 나이
    'age_end' => 35,           // 끝 나이
    'name' => '김',            // 이름 검색 (LIKE '김%')
    'page' => 1,               // 페이지 번호
    'per_page' => 10           // 페이지당 항목 수
]);
```

**반환값:**

```php
[
    'page' => 1,                    // 현재 페이지
    'per_page' => 10,               // 페이지당 항목 수
    'total' => 150,                 // 전체 사용자 수
    'total_pages' => 15,            // 전체 페이지 수
    'users' => [...]                // 사용자 배열
]
```

**필터링 옵션:**

1. **성별 필터**: `gender` - 'M' 또는 'F'
2. **나이 범위**: `age_start`, `age_end` - 만 나이 기준
3. **이름 검색**: `name` - 접두사 검색 (LIKE 'name%')
4. **페이지네이션**: `page`, `per_page` - 기본값: page=1, per_page=10

**상세 문서:**

- 자세한 사용법과 예제는 [docs/user.md](user.md)의 `list_users` 섹션을 참조하세요.
- 테스트 코드: `tests/user/list_users.test.php`

**예제 - 친구 찾기 페이지:**

```php
<?php
// $_GET을 직접 전달 - 매우 간편!
$result = list_users(array_merge($_GET, ['per_page' => 10]));

$users = $result['users'];
$total_count = $result['total'];
?>

<form method="get">
    <select name="gender">
        <option value="">전체</option>
        <option value="M">남성</option>
        <option value="F">여성</option>
    </select>

    <input type="number" name="age_start" placeholder="시작 나이">
    <input type="number" name="age_end" placeholder="끝 나이">
    <input type="text" name="name" placeholder="이름">

    <button type="submit">검색</button>
</form>

<!-- 사용자 목록 표시 -->
<?php foreach ($users as $user): ?>
    <div><?= htmlspecialchars($user['display_name']) ?></div>
<?php endforeach; ?>
```

---

## 디자인 및 스타일링 표준

- **라이트 모드만 지원**: Sonub 웹사이트는 라이트 모드만 지원합니다. 다크 모드 기능을 절대 구현하지 마세요.
- **Bootstrap 색상 사용**: 항상 Bootstrap의 기본 색상 클래스와 변수를 최대한 사용하세요.
- **커스텀 색상 금지**: 꼭 필요한 경우가 아니면 커스텀 HEX 코드나 CSS 색상 이름을 사용하지 마세요.
- **아이콘 라이브러리 - 필수 준수**:
  - **🔥🔥🔥 최강력 규칙: 아이콘을 추가할 때는 반드시 Font Awesome 7.1 Pro를 먼저 사용해야 합니다 🔥🔥🔥**
  - Font Awesome 7.1 (Pro)과 Bootstrap Icons 1.13.1을 함께 사용합니다.
  - 두 아이콘 라이브러리 모두 이미 로드되어 있으며 즉시 사용 가능합니다.
  - **✅ 우선순위 1순위**: Font Awesome 7.1 Pro - 먼저 확인하고 사용
  - **✅ 우선순위 2순위**: Bootstrap Icons - Font Awesome에 없는 경우에만 사용
  - Font Awesome: `<i class="fa-solid fa-house"></i>` 형식
  - Font Awesome Pro는 더 많은 아이콘과 스타일을 제공합니다 (solid, regular, light, thin, duotone 등)
  - Bootstrap Icons: `<i class="bi bi-house"></i>` 형식
  - **위반 금지**: Font Awesome에 해당 아이콘이 있는데 Bootstrap Icons를 사용하는 것은 금지
  - 자세한 사용법은 `docs/design-guideline.md`의 "아이콘 사용 가이드라인" 섹션 참조

---

## JavaScript 프레임워크 - Vue.js 3.x

### Vue.js 사용 방식

Sonub는 **Vue.js 3.x를 CDN 방식**으로 사용하며, **PHP MPA (Multi-Page Application)** 방식으로 동작합니다.

- **CDN 로딩**: Vue.js 3.x는 CDN을 통해 자동으로 모든 페이지에 로드됩니다
- **수동 추가 불필요**: 모든 페이지 하단에 Vue.js가 자동으로 포함되므로 별도로 추가할 필요가 없습니다
- **전역 사용 가능**: 모든 페이지에서 `Vue` 객체를 바로 사용할 수 있습니다

### MPA 방식의 장점

PHP MPA 방식으로 동작하므로 다음과 같은 특징이 있습니다:

- **자동 리셋**: 페이지 이동 시 모든 Vue 인스턴스와 이벤트 리스너가 자동으로 해제됩니다
- **이벤트 리스너 정리 불필요**: SPA와 달리 `beforeUnmount`나 `unmounted`에서 이벤트 리스너를 수동으로 해제할 필요가 없습니다
- **메모리 누수 방지**: 페이지 전환 시 브라우저가 자동으로 메모리를 정리합니다
- **단순한 상태 관리**: 각 페이지가 독립적이므로 복잡한 전역 상태 관리가 필요 없습니다

### 자동 리소스 로딩

**🔥🔥🔥 최강력 규칙: 페이지별 CSS/JS 파일은 자동으로 로드됩니다 🔥🔥🔥**

#### 자동 로딩 규칙

Sonub는 PHP 페이지 파일과 같은 폴더에 있는 CSS와 JavaScript 파일을 자동으로 로드합니다.

**로딩 위치:**

- **CSS 파일**: `<head>` 태그 안에 자동 포함
- **JavaScript 파일**: `<body>` 태그 시작 부분에 자동 포함 (`defer` 속성 사용)

**파일 명명 규칙:**

```
page/user/profile.php   ← PHP 페이지 파일
page/user/profile.css   ← 자동으로 로드됨 (같은 폴더, 같은 이름)
page/user/profile.js    ← 자동으로 로드됨 (같은 폴더, 같은 이름)
```

**예제:**

```
page/
├── index.php           ← 메인 페이지
├── index.css           ← 자동 로드
├── index.js            ← 자동 로드
├── user/
│   ├── login.php       ← 로그인 페이지
│   ├── login.css       ← 자동 로드
│   ├── login.js        ← 자동 로드
│   ├── profile.php     ← 프로필 페이지
│   ├── profile.css     ← 자동 로드
│   └── profile.js      ← 자동 로드
```

**생성되는 HTML:**

```html
<!DOCTYPE html>
<html>
  <head>
    <!-- 페이지별 CSS 자동 로드 -->
    <link href="/page/user/profile.css" rel="stylesheet" />
  </head>
  <body>
    <!-- Firebase SDK 로드 (최우선) -->
    <script src="https://www.gstatic.com/.../firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/.../firebase-auth-compat.js"></script>

    <!-- Firebase 초기화 -->
    <script>
      firebase.initializeApp({
        /* config */
      });
    </script>

    <!-- Axios.js 로드 (Firebase 다음) -->
    <script src="/js/axios.min.js"></script>

    <!-- Vue.js 로드 -->
    <script src="/js/vue.global.prod.js"></script>

    <!-- 페이지별 JavaScript 자동 로드 -->
    <script defer src="/page/user/profile.js"></script>

    <!-- 페이지 콘텐츠 -->
    <?php include page() ?>
  </body>
</html>
```

#### JavaScript에서 즉시 사용 가능한 객체

**⚠️⚠️⚠️ 중요: Firebase, Axios, Vue.js는 즉시 사용 가능합니다 ⚠️⚠️⚠️**

페이지별 JavaScript 파일(`profile.js`)에서 다음 객체들을 **즉시** 사용할 수 있습니다:

1. **Firebase**: 전역 객체로 즉시 사용 가능
2. **axios**: 전역 객체로 즉시 사용 가능
3. **Vue**: 전역 객체로 즉시 사용 가능

**로딩 순서:**

```
1. Firebase SDK 로드
2. Firebase 초기화
3. Axios.js 로드       ← Firebase 다음, Vue.js 이전
4. Vue.js 로드
5. 페이지별 JavaScript 로드 (defer)
```

**이유:**

- Firebase SDK, Axios.js, Vue.js는 `<body>` 태그 시작 부분에 **동기적으로** 로드됨
- 페이지별 JavaScript는 `defer` 속성으로 로드되어 DOM이 준비된 후 실행됨
- 따라서 별도의 대기 시간이나 초기화 체크가 **불필요**함

**Axios.js란?**

- HTTP 요청을 쉽게 만들 수 있는 Promise 기반 HTTP 클라이언트
- RESTful API 호출에 사용
- Sonub API (`api.php`)와 통신할 때 사용 가능

**예제 1: Firebase와 Vue.js 사용**

```javascript
// page/user/profile.js

// ✅ Firebase와 Vue.js를 즉시 사용 가능 - 별도의 초기화 체크 불필요!

const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      posts: [],
    };
  },
  methods: {
    async loadUserProfile() {
      // Firebase를 즉시 사용 가능
      const uid = firebase.auth().currentUser?.uid;
      if (!uid) {
        console.log("로그인이 필요합니다");
        return;
      }

      // Firestore에서 사용자 프로필 로드
      const doc = await firebase.firestore().collection("users").doc(uid).get();

      this.user = doc.data();
    },

    async loadUserPosts() {
      // Firestore에서 사용자 게시글 로드
      const snapshot = await firebase
        .firestore()
        .collection("posts")
        .where("uid", "==", this.user.uid)
        .orderBy("createdAt", "desc")
        .limit(10)
        .get();

      this.posts = snapshot.docs.map((doc) => ({
        id: doc.id,
        ...doc.data(),
      }));
    },
  },
  mounted() {
    // Firebase 인증 상태 확인
    firebase.auth().onAuthStateChanged((user) => {
      if (user) {
        console.log("사용자 로그인:", user.uid);
        this.loadUserProfile();
        this.loadUserPosts();
      } else {
        console.log("로그인 필요");
        window.location.href = "/login";
      }
    });
  },
}).mount("#app");
```

**예제 2: Axios로 API 호출**

```javascript
// page/user/settings.js

// ✅ axios를 즉시 사용 가능 - 별도의 초기화 체크 불필요!

const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      loading: false,
      error: null,
    };
  },
  methods: {
    async loadUserData() {
      try {
        this.loading = true;
        this.error = null;

        // Axios로 Sonub API 호출
        const response = await axios.get("/api.php", {
          params: {
            f: "getUserInfo",
            user_id: 123,
          },
        });

        // 에러 체크
        if (response.data.error_code) {
          this.error = response.data.error_message;
          return;
        }

        // 성공 시 데이터 저장
        this.user = response.data;
        console.log("함수:", response.data.func);
      } catch (err) {
        this.error = "사용자 정보 로드 실패: " + err.message;
      } finally {
        this.loading = false;
      }
    },

    async updateUserProfile(name, bio) {
      try {
        this.loading = true;
        this.error = null;

        // Axios로 POST 요청
        const response = await axios.post("/api.php?f=updateUserProfile", {
          user_id: this.user.user_id,
          name: name,
          bio: bio,
        });

        // 에러 체크
        if (response.data.error_code) {
          this.error = response.data.error_message;
          return;
        }

        // 성공
        console.log("프로필 업데이트 성공");
        this.user = response.data;
      } catch (err) {
        this.error = "프로필 업데이트 실패: " + err.message;
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.loadUserData();
  },
}).mount("#settings-app");
```

**예제 3: Firebase + Axios 함께 사용**

```javascript
// page/user/dashboard.js

// ✅ Firebase, axios, Vue.js 모두 즉시 사용 가능!

const { createApp } = Vue;

createApp({
  data() {
    return {
      firebaseUser: null,
      userData: null,
      stats: null,
      loading: true,
    };
  },
  methods: {
    async loadUserData(uid) {
      try {
        // Axios로 사용자 통계 가져오기
        const statsResponse = await axios.get("/api.php", {
          params: {
            f: "getUserStats",
            user_id: uid,
          },
        });

        if (!statsResponse.data.error_code) {
          this.stats = statsResponse.data;
        }

        // Firebase Firestore에서 사용자 정보 가져오기
        const doc = await firebase
          .firestore()
          .collection("users")
          .doc(uid)
          .get();

        this.userData = doc.data();
      } catch (err) {
        console.error("데이터 로드 실패:", err);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    // Firebase 인증 상태 확인
    firebase.auth().onAuthStateChanged((user) => {
      if (user) {
        this.firebaseUser = user;
        this.loadUserData(user.uid);
      } else {
        window.location.href = "/login";
      }
    });
  },
}).mount("#dashboard-app");
```

**Axios 주요 메서드:**

```javascript
// GET 요청
axios.get("/api.php?f=getUserInfo&user_id=123");
axios.get("/api.php", { params: { f: "getUserInfo", user_id: 123 } });

// POST 요청
axios.post("/api.php?f=createPost", {
  title: "제목",
  content: "내용",
});

// PUT 요청
axios.put("/api.php?f=updatePost", {
  post_id: 456,
  title: "수정된 제목",
});

// DELETE 요청
axios.delete("/api.php?f=deletePost&post_id=456");

// 헤더 설정
axios.get("/api.php?f=getData", {
  headers: {
    Authorization: "Bearer token123",
  },
});

// 타임아웃 설정
axios.get("/api.php?f=getData", {
  timeout: 5000, // 5초
});
```

**잘못된 예제 (불필요한 초기화 체크):**

```javascript
// ❌ 불필요한 코드 - Firebase, Axios, Vue.js는 이미 로드되어 있음!

// 불필요: Firebase 로드 대기
if (typeof firebase === "undefined") {
  console.error("Firebase가 로드되지 않았습니다");
}

// 불필요: Axios 로드 대기
if (typeof axios === "undefined") {
  console.error("Axios가 로드되지 않았습니다");
}

// 불필요: Vue 로드 대기
if (typeof Vue === "undefined") {
  console.error("Vue가 로드되지 않았습니다");
}

// 불필요: window.onload 이벤트
window.addEventListener("load", function () {
  // defer 속성으로 이미 DOM이 준비된 후 실행됨
  const { createApp } = Vue;
  // ...
});
```

#### 자동 로딩 활용 가이드

**1. 페이지별 스타일이 필요한 경우:**

```css
/* page/user/profile.css */

/* 프로필 페이지 전용 스타일 */
.profile-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info h1 {
  margin: 0;
  font-size: 24px;
  color: var(--bs-emphasis-color);
}
```

**2. 페이지별 로직이 필요한 경우:**

```javascript
// page/user/profile.js

const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      loading: true,
      error: null,
    };
  },
  methods: {
    async loadProfile() {
      try {
        this.loading = true;
        const user = firebase.auth().currentUser;

        if (!user) {
          this.error = "로그인이 필요합니다";
          return;
        }

        const doc = await firebase
          .firestore()
          .collection("users")
          .doc(user.uid)
          .get();

        if (!doc.exists) {
          this.error = "사용자 프로필을 찾을 수 없습니다";
          return;
        }

        this.user = doc.data();
      } catch (err) {
        this.error = "프로필 로드 실패: " + err.message;
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.loadProfile();
  },
}).mount("#profile-app");
```

**3. 해당 페이지 PHP 파일:**

```php
<!-- page/user/profile.php -->

<div id="profile-app">
  <!-- 로딩 상태 -->
  <div v-if="loading" class="text-center">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">로딩중...</span>
    </div>
  </div>

  <!-- 에러 상태 -->
  <div v-else-if="error" class="alert alert-danger">
    {{ error }}
  </div>

  <!-- 프로필 표시 -->
  <div v-else-if="user" class="profile-container">
    <div class="profile-header">
      <img :src="user.photoURL" alt="프로필 사진" class="profile-avatar">
      <div class="profile-info">
        <h1>{{ user.displayName }}</h1>
        <p class="text-muted">{{ user.email }}</p>
      </div>
    </div>

    <div class="profile-bio">
      <h3>자기소개</h3>
      <p>{{ user.bio || '자기소개가 없습니다' }}</p>
    </div>
  </div>
</div>
```

**자동으로 생성되는 HTML:**

페이지 접속 시 Sonub는 자동으로 다음을 생성합니다:

```html
<head>
  <!-- 페이지별 CSS 자동 추가 -->
  <link href="/page/user/profile.css" rel="stylesheet" />
</head>
<body>
  <!-- 1. Firebase SDK 자동 로드 (최우선) -->
  <script src="...firebase-app-compat.js"></script>
  <script src="...firebase-auth-compat.js"></script>
  <script src="...firebase-firestore-compat.js"></script>

  <!-- 2. Firebase 초기화 -->
  <script>
    firebase.initializeApp({...});
  </script>

  <!-- 3. Axios.js 자동 로드 -->
  <script src="/js/axios.min.js"></script>

  <!-- 4. Vue.js 자동 로드 -->
  <script src="/js/vue.global.prod.js"></script>

  <!-- 5. 페이지별 JavaScript 자동 추가 (defer) -->
  <script defer src="/page/user/profile.js"></script>

  <!-- 페이지 콘텐츠 -->
  <?php include page() ?>
</body>
```

#### 주의사항

**✅ 올바른 사용:**

- 파일명을 PHP 파일과 동일하게 유지
- **Firebase, Axios, Vue.js를 즉시 사용** - 별도의 초기화 체크 불필요
- `mounted()` 훅에서 Firebase/Axios 로직 작성
- Axios로 Sonub API (`api.php`) 호출 시 에러 체크 포함

**❌ 잘못된 사용:**

- 다른 이름의 CSS/JS 파일 생성 (자동 로드 안 됨)
- Firebase, Axios, Vue.js 초기화 대기 코드 작성 (불필요)
- 페이지별 JS 파일에서 다른 페이지의 DOM 접근 시도
- `window.onload` 또는 `DOMContentLoaded` 이벤트 사용 (불필요 - `defer` 속성으로 이미 처리됨)

**로딩 순서 요약:**

```
1. CSS 파일           → <head>에 로드
2. Firebase SDK       → <body> 시작 부분 (동기)
3. Firebase 초기화    → <body> 시작 부분 (동기)
4. Axios.js           → <body> 시작 부분 (동기)
5. Vue.js             → <body> 시작 부분 (동기)
6. 페이지별 JS        → <body> 시작 부분 (defer - DOM 준비 후 실행)
7. 페이지 콘텐츠      → include page()
```

따라서 **페이지별 JavaScript 파일에서 Firebase, axios, Vue 모두 즉시 사용 가능**합니다.

---

### Vue.js 기본 사용법

#### 기본 Vue 앱 생성

```javascript
// 각 페이지의 JavaScript 파일에서
const { createApp } = Vue;

createApp({
  data() {
    return {
      message: "안녕하세요, Sonub!",
      count: 0,
    };
  },
  methods: {
    increment() {
      this.count++;
    },
  },
  mounted() {
    console.log("Vue 앱이 마운트되었습니다");
  },
}).mount("#app");
```

#### HTML에서 Vue 사용

```html
<div id="app">
  <h1>{{ message }}</h1>
  <p>카운트: {{ count }}</p>
  <button @click="increment">증가</button>
</div>
```

#### 컴포넌트 정의 및 사용

```javascript
const { createApp } = Vue;

// 컴포넌트 정의
const UserCard = {
  props: ["user"],
  template: `
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{ user.name }}</h5>
        <p class="card-text">{{ user.email }}</p>
      </div>
    </div>
  `,
};

// 앱 생성 및 컴포넌트 등록
createApp({
  components: {
    UserCard,
  },
  data() {
    return {
      users: [
        { name: "홍길동", email: "hong@example.com" },
        { name: "김철수", email: "kim@example.com" },
      ],
    };
  },
}).mount("#app");
```

#### Firebase와 함께 사용

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      posts: [],
    };
  },
  methods: {
    async loadPosts() {
      // Firebase Firestore에서 게시글 로드
      const snapshot = await firebase
        .firestore()
        .collection("posts")
        .orderBy("createdAt", "desc")
        .limit(10)
        .get();

      this.posts = snapshot.docs.map((doc) => ({
        id: doc.id,
        ...doc.data(),
      }));
    },
  },
  mounted() {
    // 인증 상태 확인
    firebase.auth().onAuthStateChanged((user) => {
      this.user = user;
      if (user) {
        this.loadPosts();
      }
    });
  },
}).mount("#app");
```

#### 중요 사항

- **이벤트 리스너 해제 불필요**: MPA 방식이므로 페이지 이동 시 자동으로 모든 리스너가 정리됩니다
- **페이지별 독립적**: 각 페이지의 Vue 인스턴스는 완전히 독립적입니다
- **간단한 구조 유지**: 복잡한 라우팅이나 상태 관리 라이브러리 없이 간단하게 사용합니다

---

## 프레임워크 및 라이브러리 저장 가이드라인

### 완전한 프레임워크 패키지

완전한 프레임워크 파일(예: 모든 컴포넌트를 포함한 Bootstrap)을 다운로드할 때:

- 저장 위치: `etc/frameworks/<프레임워크-이름>/<프레임워크-이름>-x.x.x/`
- 예시: `etc/frameworks/bootstrap/bootstrap-5.3.0/`
- 이 구조는 버전 관리를 유지하고 여러 버전이 공존할 수 있게 합니다.

### 단일 JavaScript 라이브러리

단일 JavaScript 파일 라이브러리의 경우:

- 저장 위치: `/js/`에 직접 저장
- 예시: `/js/library-name-x.x.x.min.js`
- 라이브러리 버전을 추적하기 위해 버전이 포함된 파일명을 사용합니다.
- **참고**: Vue.js는 CDN으로 자동 로드되므로 별도로 저장하지 않습니다.

---

## 페이지별 CSS 및 JavaScript 자동 로딩

### 개요

Sonub는 `lib/page/page.functions.php`에 정의된 두 개의 헬퍼 함수를 통해 페이지별 CSS 및 JavaScript 파일의 자동 로딩 기능을 제공합니다. 이 함수들은 현재 제공되는 페이지에 해당하는 CSS 및 JS 파일을 자동으로 감지하고 포함시켜, 페이지별 자산을 수동으로 포함할 필요가 없습니다.

### 작동 방식

자동 로딩 시스템은 메인 `index.php` 파일에서 호출되는 두 개의 함수를 사용합니다:

1. **`include_page_css()`** - 페이지의 PHP 파일과 같은 디렉토리에 CSS 파일이 있으면 자동으로 포함
2. **`include_page_js()`** - 페이지의 PHP 파일과 같은 디렉토리에 JavaScript 파일이 있으면 자동으로 포함

이 함수들은 다음과 같이 작동합니다:

1. `page()` 함수를 사용하여 현재 페이지 경로를 결정
2. 같은 위치에 해당하는 `.css` 또는 `.js` 파일이 있는지 확인
3. 파일이 있으면 적절한 `<link>` 또는 `<script>` 태그를 자동으로 생성하고 출력

### 사용 가이드라인

#### 자동 포함

- **수동 개입 불필요**: 올바른 명명 규칙으로 CSS 또는 JS 파일을 생성하면 자동으로 로드됩니다
- **설정보다 규칙**: PHP 페이지 파일에 맞게 파일 이름만 지정하면 됩니다
- **깔끔하고 유지보수 가능**: 보일러플레이트 코드를 줄이고 포함을 잊어버리는 것을 방지합니다

#### 파일 명명 규칙

자동 로딩이 작동하려면 다음 명명 패턴을 따르세요:

**기본 페이지:**

- PHP 페이지: `/page/about.php`
- CSS 파일: `/page/about.css` (존재하면 자동 로드)
- JS 파일: `/page/about.js` (존재하면 defer 속성과 함께 자동 로드)

**중첩된 페이지:**

- PHP 페이지: `/page/user/login.php`
- CSS 파일: `/page/user/login.css`
- JS 파일: `/page/user/login.js`

#### 사용 시기

**다음과 같은 경우 페이지별 CSS/JS 자동 로딩을 항상 사용하세요:**

- 커스텀 스타일링 또는 JavaScript 기능이 필요한 새 페이지 생성 시
- 독립된 스타일이 필요한 기능별 인터페이스 구축 시
- 전역 스타일에 영향을 주지 않아야 하는 컴포넌트 개발 시
- 특정 라우트에만 적용되는 페이지별 JavaScript 작성 시

**장점:**

- 자동 코드 분할 - 필요할 때만 CSS/JS를 로드
- 성능 향상 - 필요한 자산만 로드하여 초기 페이지 로드를 줄임
- 유지보수성 향상 - 페이지별 코드를 PHP 파일과 함께 정리
- 설정 불필요 - 파일만 생성하면 작동

### 예제

#### 예제 1: 지원 페이지 만들기

`/page/support.php`에 새 지원 페이지를 만들 때:

```php
<!-- /page/support.php -->
<div class="support-container">
    <h1>고객 지원</h1>
    <!-- 페이지 콘텐츠 -->
</div>
```

해당 파일들을 간단히 생성:

```css
/* /page/support.css */
.support-container {
  padding: 20px;
  background: var(--bs-light);
}
```

```javascript
// /page/support.js
const { createApp } = Vue;

createApp({
  data() {
    return {
      message: "지원 페이지에 오신 것을 환영합니다",
      tickets: [],
    };
  },
  methods: {
    async loadTickets() {
      // 지원 티켓 로드
      console.log("지원 티켓을 로드합니다");
    },
  },
  mounted() {
    console.log("지원 페이지 로드됨");
    this.loadTickets();
  },
}).mount("#support-app");
```

이 파일들은 `/support` 접속 시 자동으로 포함됩니다.

#### 예제 2: 사용자 로그인 페이지

`/page/user/login.php`의 로그인 페이지:

```php
<!-- /page/user/login.php -->
<div id="login-app">
  <form @submit.prevent="handleLogin">
    <div class="mb-3">
      <label class="form-label">이메일</label>
      <input type="email" class="form-control" v-model="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">비밀번호</label>
      <input type="password" class="form-control" v-model="password" required>
    </div>
    <button type="submit" class="btn btn-primary">로그인</button>
    <p v-if="error" class="text-danger mt-2">{{ error }}</p>
  </form>
</div>
```

다음을 생성:

```css
/* /page/user/login.css */
#login-app form {
  max-width: 400px;
  margin: 0 auto;
}
```

```javascript
// /page/user/login.js
const { createApp } = Vue;

createApp({
  data() {
    return {
      email: "",
      password: "",
      error: null,
    };
  },
  methods: {
    async handleLogin() {
      try {
        // Firebase 인증
        await firebase
          .auth()
          .signInWithEmailAndPassword(this.email, this.password);
        // 로그인 성공 시 리다이렉트
        window.location.href = "/dashboard";
      } catch (err) {
        this.error = "로그인에 실패했습니다: " + err.message;
      }
    },
  },
}).mount("#login-app");
```

#### 출력 예제

`https://local.sonub.com/support` 접속 시, 시스템은 자동으로 다음을 생성합니다:

```html
<link href="/page/support.css" rel="stylesheet" />
<script defer src="/page/support.js"></script>
```

이 태그들은 `index.php`에서 호출되는 함수에 의해 페이지의 `<head>` 섹션에 자동으로 주입됩니다.

---

## 공유 JavaScript 파일 로딩 - load_deferred_js()

### 개요

`load_deferred_js()` 함수는 여러 페이지에서 공통으로 사용하는 JavaScript 파일을 중복 없이 한 번만 로드하기 위한 함수입니다.

**🔥🔥🔥 최강력 규칙: 여러 페이지에서 사용하는 JavaScript는 반드시 load_deferred_js() 함수를 사용해야 합니다 🔥🔥🔥**

### 필요한 이유

**문제 상황:**
- 동일한 JavaScript 파일을 여러 위젯이나 페이지에서 사용해야 하는 경우
- 각 위젯에서 `<script src="...">` 태그로 직접 로드하면 중복 로드 발생
- 중복 로드는 성능 저하와 예측 불가능한 동작을 유발

**해결 방법:**
- `load_deferred_js()` 함수를 사용하면 JavaScript 파일을 HTML 문서 맨 아래에서 **한 번만** 로드
- 여러 곳에서 호출해도 중복 로드 방지
- `defer` 속성으로 로드되어 페이지 렌더링을 차단하지 않음

### 함수 위치 및 시그니처

**파일 위치:** `lib/page/page.functions.php`

```php
/**
 * JavaScript 파일을 defer 속성으로 로드
 *
 * @param string $path JavaScript 파일 경로
 *   - '/'로 시작하면 완전한 경로로 간주
 *   - '/'로 시작하지 않으면 '/js/' 폴더에서 찾음
 *
 * @param int $priority 우선순위 (0-9)
 *   - 0: 가장 낮은 우선순위 (기본값)
 *   - 9: 가장 높은 우선순위
 *   - 우선순위가 높을수록 먼저 로드됨
 */
function load_deferred_js(string $path, int $priority = 0)
```

### 사용 방법

#### 기본 사용법

```php
<?php
// 위젯 또는 페이지 파일 상단에서 호출

// 예제 1: /js/ 폴더의 파일 로드 (확장자 제외)
load_deferred_js('file-upload');
// → <script defer src="/js/file-upload.js?v=..."></script>

// 예제 2: 전체 경로로 파일 로드
load_deferred_js('/lib/utils/helper.js');
// → <script defer src="/lib/utils/helper.js?v=..."></script>

// 예제 3: 우선순위 지정
load_deferred_js('jquery', 9);  // 가장 먼저 로드
load_deferred_js('app', 5);     // 중간 우선순위
load_deferred_js('plugin', 0);  // 가장 나중에 로드
?>
```

#### 실제 사용 예제

**파일 업로드 기능을 여러 위젯에서 사용하는 경우:**

```php
<!-- /widgets/post/post-list-create.php -->
<?php
/**
 * 게시물 작성 폼
 */

// 파일 업로드 JavaScript 로드 (중복 방지)
load_deferred_js('file-upload');
?>
<section id="post-list-create">
    <form>
        <input type="file" multiple onchange="handle_file_change(event, { id: 'display-files' })">
        <div id="display-files"></div>
    </form>
</section>
```

```php
<!-- /widgets/user/profile-photo-upload.php -->
<?php
/**
 * 프로필 사진 업로드 위젯
 */

// 동일한 파일 업로드 JavaScript 로드 (중복되지 않음)
load_deferred_js('file-upload');
?>
<section id="profile-photo-upload">
    <input type="file" onchange="handle_file_change(event, { id: 'profile-photo' })">
    <div id="profile-photo"></div>
</section>
```

**결과:**
- 두 위젯 모두 `load_deferred_js('file-upload')`를 호출
- 하지만 `/js/file-upload.js` 파일은 HTML 문서 맨 아래에서 **한 번만** 로드됨
- 중복 로드 방지 및 성능 향상

### 우선순위 시스템

JavaScript 로딩 순서가 중요한 경우 우선순위를 지정할 수 있습니다:

```php
<?php
// jQuery를 가장 먼저 로드 (우선순위 9)
load_deferred_js('jquery', 9);

// Vue.js를 두 번째로 로드 (우선순위 8)
load_deferred_js('vue', 8);

// 앱 스크립트를 세 번째로 로드 (우선순위 5)
load_deferred_js('app', 5);

// 플러그인을 마지막으로 로드 (우선순위 0, 기본값)
load_deferred_js('plugin');
?>
```

**생성되는 HTML:**

```html
<body>
  <!-- 페이지 콘텐츠 -->

  <!-- load_deferred_js()로 등록된 스크립트들이 우선순위 순서대로 출력 -->
  <script defer src="/js/jquery.js?v=..."></script>      <!-- 우선순위 9 -->
  <script defer src="/js/vue.js?v=..."></script>         <!-- 우선순위 8 -->
  <script defer src="/js/app.js?v=..."></script>         <!-- 우선순위 5 -->
  <script defer src="/js/plugin.js?v=..."></script>      <!-- 우선순위 0 -->
</body>
```

### 잘못된 사용 예제 (절대 금지)

```php
<!-- ❌ 절대 금지: 각 위젯에서 직접 <script> 태그 사용 -->

<!-- /widgets/post/post-list-create.php -->
<script defer src="/js/file-upload.js"></script>  <!-- 중복 로드 발생! -->

<!-- /widgets/user/profile-photo-upload.php -->
<script defer src="/js/file-upload.js"></script>  <!-- 중복 로드 발생! -->

<!-- 결과: 동일한 파일이 두 번 로드되어 성능 저하 및 오류 발생 -->
```

### 올바른 사용 예제

```php
<!-- ✅ 올바른 방법: load_deferred_js() 함수 사용 -->

<!-- /widgets/post/post-list-create.php -->
<?php load_deferred_js('file-upload'); ?>

<!-- /widgets/user/profile-photo-upload.php -->
<?php load_deferred_js('file-upload'); ?>

<!-- 결과: /js/file-upload.js 파일이 HTML 문서 맨 아래에서 한 번만 로드됨 -->
```

### 장점

1. **중복 로드 방지**: 동일한 파일을 여러 번 로드하지 않음
2. **성능 향상**: 불필요한 네트워크 요청 제거
3. **유지보수 용이**: 파일 경로를 한 곳에서 관리
4. **로딩 순서 제어**: 우선순위를 통해 로딩 순서 지정 가능
5. **캐시 버전 관리**: 자동으로 `?v=...` 파라미터 추가하여 캐시 관리

### 주의사항

- **✅ 필수**: 여러 페이지/위젯에서 사용하는 JavaScript는 반드시 `load_deferred_js()` 사용
- **✅ 필수**: 파일 경로가 `/js/` 폴더 외부인 경우 `/`로 시작하는 완전한 경로 사용
- **❌ 금지**: 동일한 JavaScript 파일을 `<script>` 태그로 직접 로드 금지
- **❌ 금지**: 페이지별 JavaScript 파일(`profile.js`)은 자동 로드되므로 `load_deferred_js()` 불필요

### 사용 시나리오

**사용해야 하는 경우:**
- 파일 업로드 기능을 여러 위젯에서 사용
- 시간 표시 함수를 여러 페이지에서 사용
- 공통 유틸리티 함수를 여러 컴포넌트에서 사용
- 서드파티 라이브러리를 여러 페이지에서 사용

**사용하지 않아도 되는 경우:**
- 페이지별 JavaScript 파일 (자동 로드됨)
- 전역 JavaScript 파일 (`/js/app.js` 등, 이미 `index.php`에서 로드됨)
- 한 페이지에서만 사용하는 스크립트

---

## Firebase 통합 가이드라인

### 로딩 동작

**⚠️⚠️⚠️ 중요: Firebase와 Vue.js의 로딩 순서 및 사용 가능 시점 ⚠️⚠️⚠️**

Firebase JavaScript SDK와 Vue.js는 페이지의 `<body>` 태그 상단에서 순차적으로 로드됩니다:

1. **Firebase SDK 로드 위치**: `<body>` 태그 시작 직후 즉시 로드
2. **Vue.js 로드 위치**: Firebase SDK 바로 다음에 로드
3. **로딩 방식**: 모두 동기적으로 로드 (defer 또는 async 속성 없음)

이는 다음을 의미합니다:

- ✅ Firebase는 페이지 어디서나 **즉시 사용 가능**합니다
- ✅ Vue.js도 페이지 어디서나 **즉시 사용 가능**합니다
- ✅ Vue.js에서 Firebase를 **즉시 사용 가능**합니다
- ✅ Vue.js의 `mounted()` 훅에서 Firebase를 **바로 호출 가능**합니다
- ✅ 별도의 대기 시간이나 초기화 체크가 **불필요**합니다

**로딩 순서 예시:**

```html
<body>
  <!-- 1. Firebase SDK 로드 (최우선) -->
  <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-firestore-compat.js"></script>

  <!-- 2. Firebase 초기화 -->
  <script>
    firebase.initializeApp({
      /* config */
    });
  </script>

  <!-- 3. Vue.js 로드 -->
  <script src="/js/vue.global.prod.js"></script>

  <!-- 이제 Firebase와 Vue.js 모두 즉시 사용 가능 -->
  <!-- 페이지 콘텐츠 -->
  <?php include page() ?>
</body>
```

### JavaScript에서 사용

Firebase는 즉시 로드되고 초기화되므로, Vue.js 앱 내에서 Firebase를 직접 사용할 수 있습니다:

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      users: [],
    };
  },
  methods: {
    async loadUsers() {
      // Firestore에서 사용자 목록 로드
      const snapshot = await firebase.firestore().collection("users").get();

      this.users = snapshot.docs.map((doc) => ({
        id: doc.id,
        ...doc.data(),
      }));
    },
  },
  mounted() {
    // Firebase 인증 상태 감지
    firebase.auth().onAuthStateChanged((user) => {
      this.user = user;
      if (user) {
        console.log("사용자 로그인:", user.uid);
        this.loadUsers();
      } else {
        console.log("사용자 로그아웃");
      }
    });
  },
}).mount("#app");
```

### Vue.js에서 Firebase 실시간 업데이트

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      notifications: [],
      unsubscribe: null,
    };
  },
  methods: {
    setupRealtimeListener() {
      // Firestore 실시간 리스너 설정
      this.unsubscribe = firebase
        .firestore()
        .collection("notifications")
        .where("userId", "==", this.user.uid)
        .orderBy("createdAt", "desc")
        .onSnapshot((snapshot) => {
          this.notifications = snapshot.docs.map((doc) => ({
            id: doc.id,
            ...doc.data(),
          }));
        });
    },
  },
  mounted() {
    firebase.auth().onAuthStateChanged((user) => {
      if (user) {
        this.user = user;
        this.setupRealtimeListener();
      }
    });
  },
  beforeUnmount() {
    // MPA 방식이므로 자동으로 정리되지만, 명시적으로 해제 가능
    if (this.unsubscribe) {
      this.unsubscribe();
    }
  },
}).mount("#app");
```

**중요 참고 사항:**

- Vue.js의 `mounted()` 훅에서 Firebase를 바로 사용할 수 있습니다
- MPA 방식이므로 페이지 이동 시 자동으로 리스너가 정리됩니다
- 필요시 `beforeUnmount()`에서 명시적으로 리스너를 해제할 수 있습니다
- Firebase 구성은 시스템에서 자동으로 처리되므로 재초기화하지 마세요

---

## 라우팅 규칙

**⚠️⚠️⚠️ 중요: Sonub의 라우팅 시스템 동작 방식 ⚠️⚠️⚠️**

### Nginx 라우팅 설정

Sonub는 Nginx 서버에서 다음과 같이 라우팅이 설정되어 있습니다:

```nginx
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 라우팅 동작 방식

1. **존재하는 파일 접근**: 요청한 파일이 존재하면 해당 파일을 반환
2. **존재하지 않는 파일 접근**: 요청한 파일이 존재하지 않으면 **모든 요청은 `index.php`로 전달**
3. **페이지 파일 로딩**: `index.php`는 요청 경로를 분석하여 `/page/**/*.php` 폴더 아래의 PHP 스크립트를 로드

### URL과 페이지 파일 매핑

| 요청 URL | 로드되는 파일 |
|---------|-------------|
| `/` | `/page/index.php` |
| `/abc/def` | `/page/abc/def.php` |
| `/user/login` | `/page/user/login.php` |
| `/post/list` | `/page/post/list.php` |
| `/admin/dashboard` | `/page/admin/dashboard.php` |

### 라우팅 예제

**예제 1: 사용자 로그인 페이지**

- **URL**: `https://sonub.com/user/login`
- **Nginx**: 파일 `/user/login`이 존재하지 않음 → `index.php`로 전달
- **index.php**: 요청 경로 `/user/login`을 분석하여 `/page/user/login.php`를 로드

**예제 2: 게시글 목록 페이지**

- **URL**: `https://sonub.com/post/list`
- **Nginx**: 파일 `/post/list`가 존재하지 않음 → `index.php`로 전달
- **index.php**: 요청 경로 `/post/list`을 분석하여 `/page/post/list.php`를 로드

**예제 3: 메인 페이지**

- **URL**: `https://sonub.com/`
- **Nginx**: 디렉토리 인덱스 → `index.php` 실행
- **index.php**: 요청 경로가 `/`이므로 `/page/index.php`를 로드

### 중요 사항

- **✅ 필수**: 모든 페이지 파일은 `/page/` 디렉토리 아래에 저장
- **✅ 필수**: URL 경로와 동일한 구조로 PHP 파일 생성
- **✅ 필수**: 페이지 링크는 반드시 `href()` 함수 사용 (자세한 내용은 [URL 및 페이지 링크 관리 규칙](#url-및-페이지-링크-관리-규칙) 참조)
- **❌ 금지**: `/page/` 디렉토리 외부에 페이지 파일 생성 금지
- **❌ 금지**: 하드코딩된 URL 경로 사용 금지

### 라우팅 시스템의 장점

1. **깔끔한 URL**: 확장자 없는 URL 사용 가능 (`/user/login` 대신 `/user/login.php`)
2. **단일 진입점**: 모든 요청이 `index.php`를 거쳐 공통 초기화 수행
3. **유연한 구조**: 디렉토리 구조에 따라 자유롭게 페이지 구성
4. **중앙 집중 관리**: `index.php`에서 라우팅, 인증, 설정 등을 통합 관리

---

## 개발 시스템 시작

### 빠른 시작 (필수 명령어)

Sonub 개발을 시작하려면 **두 개의 명령어를 실행**해야 합니다:

```bash
# 1. Docker 컨테이너 시작 (Nginx + PHP-FPM)
cd ~/apps/sonub/dev/docker
docker compose up -d

# 2. 핫 리로드 서버 시작 (자동 파일 변경 감지)
cd ~/apps/sonub
npm run dev
```

**중요**: 완전한 개발 환경을 구축하려면 두 명령어를 모두 실행해야 합니다.

- `docker compose up`: 웹 서버 및 PHP 런타임 환경 제공
- `npm run dev`: 파일 변경 시 브라우저 자동 새로고침

### 데이터베이스 관리

**phpMyAdmin 접속:**

- URL: http://local.sonub.com/dev/phpMyAdmin/index.php
- 로컬 개발 환경에서 MariaDB 데이터베이스를 관리할 수 있습니다
- Docker 컨테이너가 실행 중일 때 사용 가능합니다

### Docker Compose 사용

Sonub 개발 환경은 Docker Compose를 통해 Nginx와 PHP-FPM 서비스를 실행합니다.

#### 사전 요구 사항

- Docker 및 Docker Compose 설치 필요
- 프로젝트 위치: `~/apps/sonub/`
- 로컬 개발 도메인 설정 필요 (아래 참조)

#### 빠른 시작 명령어

```bash
# Docker 디렉토리로 이동
cd ~/apps/sonub/dev/docker

# 시스템 시작 (Nginx + PHP-FPM)
docker compose up -d

# 컨테이너 상태 확인
docker compose ps

# 로그 보기
docker compose logs -f

# 시스템 중지
docker compose down

# 시스템 재시작
docker compose restart

# 고아 컨테이너 제거와 함께 시작
docker compose up -d --remove-orphans
```

#### 로컬 개발 도메인 설정

localhost/127.0.0.1 대신 로컬 개발 도메인을 사용하려면 hosts 파일에 다음 항목을 추가하세요:

**macOS/Linux의 경우:**

```bash
sudo nano /etc/hosts
# 다음 줄 추가:
127.0.0.1 local.sonub.com
```

**Windows의 경우:**

```bash
# 관리자 권한으로 편집:
# C:\Windows\System32\drivers\etc\hosts
# 다음 줄 추가:
127.0.0.1 local.sonub.com
```

이 항목을 추가한 후 다음에서 개발 사이트에 접속할 수 있습니다:

- http://local.sonub.com:8080
- https://local.sonub.com:8443

#### 기본 구성

- **웹 루트**: `~/apps/sonub`
- **HTTP 포트**: 127.0.0.1:8080
- **HTTPS 포트**: 127.0.0.1:8443
- **PHP 버전**: 8.3-fpm (커스텀 빌드)
- **Nginx 버전**: alpine (최신)
- **네트워크**: sonub-network (브리지)
- **로컬 도메인**: local.sonub.com (hosts 파일 구성 필요)

#### 디렉토리 구조

```
dev/docker/
├── compose.yaml         # Docker Compose 구성 파일
├── php.dockerfile       # PHP-FPM 커스텀 이미지
├── etc/
│   ├── nginx/
│   │   └── nginx.conf  # Nginx 메인 구성
│   └── php.ini         # PHP 구성
└── var/
    ├── log/nginx/      # Nginx 로그
    └── logs/php/       # PHP 로그
```

#### 주요 기능

- Docker Compose를 통한 간편한 서비스 관리
- 자동 Nginx 및 PHP-FPM 통합
- 볼륨 마운트를 통한 실시간 코드 반영
- 외부 로그 파일 저장

#### 문제 해결

- 포트 충돌 발생 시 compose.yaml에서 포트 번호 변경
- `docker compose logs` 명령어로 오류 확인
- ERR_UNSAFE_PORT 오류 발생 시 다른 포트(예: 8080) 사용

### 핫 리로드 개발 서버

Sonub는 파일 변경 시 브라우저를 자동으로 새로고침하는 핫 리로드 개발 서버를 제공합니다.

#### 기능

- 파일 변경 시 자동 브라우저 새로고침
- 페이지 새로고침 없이 CSS 핫 스왑
- 로컬 SSL 인증서로 HTTPS 지원
- PHP, CSS, JS 및 템플릿 파일 감시

#### 설정 및 사용

```bash
# 의존성 설치 (최초 1회만)
npm install

# 핫 리로드 서버 시작
npm run dev
```

핫 리로드 서버는:

- https://localhost:3034에서 실행 (SSL 인증서가 없으면 http)
- 모든 프로젝트 파일의 변경 사항 모니터링
- PHP 파일 변경 시 브라우저 자동 새로고침
- 전체 페이지 새로고침 없이 CSS 파일 핫 스왑
- 콘솔에 파일 변경 알림 표시

#### 구성

핫 리로드 서버는 환경 변수로 구성할 수 있습니다:

- `DOMAIN`: 개발 도메인 (기본값: localhost)
- `PORT`: 소켓 서버 포트 (기본값: 3034)
- `USE_HTTPS`: HTTPS 활성화/비활성화 (기본값: true)

#### SSL 인증서

서버는 다음 위치의 mkcert로 생성된 SSL 인증서를 사용합니다:

- `./etc/server-settings/nginx/ssl/sonub/localhost-key.pem`
- `./etc/server-settings/nginx/ssl/sonub/localhost-cert.pem`

인증서를 찾을 수 없으면 서버가 자동으로 HTTP 모드로 전환됩니다.

---

## 레이아웃 파일 구조

**⚠️⚠️⚠️ 최강력 경고: Sonub의 레이아웃 파일은 반드시 `/sonub.php`를 참조해야 한다 ⚠️⚠️⚠️**

### Sonub 메인 레이아웃 파일

- **파일 위치**: `/sonub.php` (프로젝트 루트 폴더)
- **역할**: Sonub 애플리케이션의 **인덱스 파일이자 메인 레이아웃 파일**
- **중요**: 레이아웃 관련 모든 작업은 **반드시 `/sonub.php` 파일을 참조**해야 함

### 파일 구조 설명

```
/sonub.php                    ← 🔴 인덱스 & 레이아웃 파일 (메인)
├── Header Navigation         ← 상단 네비게이션 바
├── Main Content Area         ← 3단 레이아웃 (좌/중앙/우 사이드바)
│   ├── Left Sidebar          ← 왼쪽 사이드바 (로그인, 대시보드 등)
│   ├── Main Content          ← 중앙 콘텐츠 영역 (<?php include page() ?>)
│   └── Right Sidebar         ← 오른쪽 사이드바 (활동, 통계 등)
└── Footer                    ← 하단 푸터
```

### 레이아웃 작업 시 필수 규칙

1. **✅ 레이아웃 수정 요청 시**: 개발자나 AI가 "레이아웃", "layout", "헤더", "푸터", "사이드바" 등의 표현을 사용하면 **반드시 `/sonub.php` 파일을 참조**

2. **✅ 페이지 콘텐츠 수정 시**: 실제 페이지 콘텐츠는 `/apps/sonub/` 폴더 아래의 개별 파일 수정

3. **✅ 레이아웃 포함 사항**:

   - 상단 네비게이션 (Header Navigation)
   - 좌측 사이드바 (Left Sidebar) - 로그인, 대시보드, 프로필 등
   - 우측 사이드바 (Right Sidebar) - 활동, 통계 등
   - 푸터 (Footer)
   - Bootstrap, FontAwesome, Firebase 설정

4. **✅ 콘텐츠 로딩 방식**:
   ```php
   <main class="col-12 col-md-6 col-lg-8 p-4">
       <?php include page() ?>  ← 개별 페이지 콘텐츠가 여기에 로드됨
   </main>
   ```

### 작업 예시

| 요청 내용                 | 수정할 파일                            |
| ------------------------- | -------------------------------------- |
| "레이아웃을 수정해줘"     | `/sonub.php`                           |
| "헤더를 변경해줘"         | `/sonub.php` (Header Navigation 섹션)  |
| "사이드바를 업데이트해줘" | `/sonub.php` (Left/Right Sidebar 섹션) |
| "푸터를 수정해줘"         | `/sonub.php` (Footer 섹션)             |
| "메인 페이지 콘텐츠 수정" | `/apps/sonub/index.php`                |
| "게시글 목록 페이지 수정" | `/apps/sonub/posts.php`                |

**중요 사항:**

- **절대 원칙**: "레이아웃"이라는 표현이 나오면 **무조건 `/sonub.php`**
- AI 어시스턴트도 이 규칙을 따라야 함
- 레이아웃 파일은 오직 하나: `/sonub.php`

---

## URL 및 페이지 링크 관리 규칙

**⚠️⚠️⚠️ 최강력 경고: Sonub의 모든 URL, 페이지 링크, 이동은 반드시 전용 함수를 사용해야 한다 ⚠️⚠️⚠️**

### URL 함수 필수 사용 규칙

- **파일 위치**: `lib/sonub/sonub.page_url.functions.php`
- **✅ 필수**: Sonub 관련 모든 URL, 링크, 페이지 이동은 **반드시** 이 파일의 함수만 사용
- **✅ 필수**: 하드코딩된 URL 경로 사용 절대 금지
- **✅ 필수**: 새로운 페이지 추가 시 해당 파일에 함수 추가 후 사용
- **❌ 금지**: `/sonub/profile`, `/sonub/settings` 등 직접 경로 작성 금지
- **❌ 금지**: 문자열로 URL 조합하여 사용 금지

### 제공되는 URL 함수 목록

```php
// 프로필 관련
sonub_profile_page()           // 프로필 페이지
sonub_profile_edit_page()      // 프로필 편집 페이지

// 설정 관련
sonub_settings_page()          // 설정 페이지

// 추가 페이지 함수들은 lib/sonub/sonub.page_url.functions.php 참조
```

### 올바른 URL 사용 예제

```php
<!-- 프로필 페이지 링크 -->
<a href="/<?= sonub_profile_page() ?>">프로필</a>

<!-- 프로필 편집 페이지 링크 -->
<a href="/<?= sonub_profile_edit_page() ?>">프로필 편집</a>

<!-- 설정 페이지 링크 -->
<a href="/<?= sonub_settings_page() ?>">설정</a>

<!-- JavaScript에서 사용 -->
<script>
const profileUrl = "/<?= sonub_profile_page() ?>";
window.location.href = profileUrl;
</script>
```

### 잘못된 URL 사용 예제 (절대 금지)

```php
<!-- ❌ 절대 금지 - 하드코딩된 URL -->
<a href="/sonub/profile">프로필</a>
<a href="/apps/sonub/profile">프로필</a>

<!-- ❌ 절대 금지 - 문자열 조합 -->
<a href="<?= '/sonub/' . 'profile' ?>">프로필</a>

<!-- ❌ 절대 금지 - 직접 경로 작성 -->
<script>
window.location.href = "/sonub/settings";  // 금지!
</script>
```

### 위반 시 결과

- URL 변경 시 모든 파일을 찾아 수정해야 함
- 오타로 인한 링크 오류 발생
- 유지보수 극도로 어려워짐
- 일관성 없는 URL 체계

### 새로운 페이지 추가 시 절차

1. `lib/sonub/sonub.page_url.functions.php`에 새 함수 추가
2. 함수명 규칙: `sonub_{페이지명}_page()` 형식 사용
3. 함수는 경로 문자열만 반환 (슬래시 제외)
4. 모든 코드에서 해당 함수 사용

**예시 - 새 페이지 함수 추가:**

```php
// lib/sonub/sonub.page_url.functions.php

function sonub_friends_page()
{
    return 'friends';
}

function sonub_messages_page()
{
    return 'messages';
}
```

---

## CSS 및 디자인 규칙

**⚠️⚠️⚠️ 최강력 경고: Sonub의 모든 CSS와 디자인 작업은 반드시 정해진 규칙을 따라야 한다 ⚠️⚠️⚠️**

### Sonub 기본 CSS 파일

- **파일 위치**: `apps/sonub/sonub.css`
- **역할**: Sonub 홈페이지 및 전체 페이지에서 사용되는 **기본 CSS 파일**
- **✅ 필수**: Sonub 홈페이지 관련 작업 시 **반드시** `apps/sonub/sonub.css` 파일을 참고하고 수정
- **✅ 필수**: 모든 디자인 및 UI 수정 시 이 파일의 스타일을 우선 확인
- **❌ 금지**: 인라인 스타일로 모든 디자인을 처리하는 것 금지
- **❌ 금지**: 각 페이지마다 별도의 CSS 파일을 무분별하게 생성하는 것 금지

### 디자인 작업 시 필수 규칙

**🔥🔥🔥 초강력 규칙: 레이아웃과 스타일을 명확히 구분해야 합니다 🔥🔥🔥**

#### 1️⃣ 레이아웃 포지션 관련 (인라인 클래스 사용)

- **✅ 필수**: **Bootstrap 5.3.8 유틸리티 클래스**를 `class="..."` 속성에 직접 작성
- **적용 대상**:
  - 레이아웃 (Layout): `d-flex`, `flex-column`, `flex-row` 등
  - 포지션 (Position): `position-relative`, `position-absolute` 등
  - 위치 (Alignment): `align-items-center`, `justify-content-between` 등
  - 간격 (Spacing): `gap-2`, `gap-3` 등 (레이아웃 간격)
- **이유**: 레이아웃 구조를 HTML에서 직접 확인 가능하도록 하기 위함

#### 2️⃣ 스타일 관련 (CSS 파일 사용)

- **✅ 필수**: `apps/sonub/sonub.css` 파일에 클래스를 정의하고 사용
- **적용 대상**:
  - 마진/패딩 (디자인 목적): `.custom-spacing` 등
  - 색상 (Colors): `.custom-bg`, `.text-custom` 등
  - 보더 (Borders): `.custom-border`, `.rounded-custom` 등
  - 효과 (Effects): `.shadow-custom`, `.hover-effect` 등
  - 폰트 (Typography): `.custom-font`, `.text-style` 등
- **이유**: 재사용 가능한 스타일을 중앙 집중식으로 관리하기 위함

### CSS 및 JavaScript 파일 분리 규칙

**🔥🔥🔥 최강력 규칙: 페이지와 위젯에서 CSS/JS 파일 관리 방식이 다릅니다 🔥🔥🔥**

#### 페이지 파일 (`./page/**/*.php`)

**✅ 필수: 페이지 파일에서는 CSS를 반드시 외부 파일로 분리해야 합니다**

- **CSS 분리 규칙**:
  - **✅ 필수**: 레이아웃 관련 Bootstrap CSS 유틸리티 클래스는 HTML에 직접 작성
  - **✅ 필수**: 스타일 관련 CSS는 **반드시** `./page/**/*.css` 파일로 분리
  - **✅ 자동 로드**: 페이지별 CSS 파일은 `index.php`에서 자동으로 로드됨
  - **❌ 금지**: 페이지 파일 내에 `<style>` 태그 사용 금지 (레이아웃 관련 Bootstrap 클래스 제외)

- **JavaScript 분리 규칙**:
  - **✅ 필수**: JavaScript 코드는 **반드시** `./page/**/*.js` 파일로 분리
  - **✅ 자동 로드**: 페이지별 JS 파일은 `index.php`에서 자동으로 로드됨
  - **❌ 금지**: 페이지 파일 내에 `<script>` 태그로 JavaScript 작성 금지

**올바른 페이지 파일 구조:**

```php
<!-- ./page/user/profile.php -->
<?php
// 페이지 고유 로직만 작성
$user = login();
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 클래스, 스타일은 CSS 파일로 분리 -->
<div class="container my-5">  <!-- Bootstrap 유틸리티 클래스 -->
    <div class="d-flex flex-column gap-3">  <!-- Bootstrap 유틸리티 클래스 -->
        <div class="profile-card">  <!-- 스타일은 profile.css에 정의 -->
            <h1 class="profile-title"><?= $user->name ?></h1>
            <p class="profile-bio"><?= $user->bio ?></p>
        </div>
    </div>
</div>

<!-- ❌ 금지: <style> 태그 사용 금지 -->
<!-- ❌ 금지: <script> 태그 사용 금지 -->
```

```css
/* ./page/user/profile.css */
/* ✅ 올바른 방법: 스타일은 외부 CSS 파일에 정의 */

.profile-card {
  background-color: var(--bs-light);
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-title {
  color: var(--bs-primary);
  font-weight: bold;
  margin-bottom: 10px;
}

.profile-bio {
  color: var(--bs-body-color);
  line-height: 1.6;
}
```

```javascript
// ./page/user/profile.js
// ✅ 올바른 방법: JavaScript는 외부 JS 파일에 정의

Vue.createApp({
  data() {
    return {
      user: null,
    };
  },
  methods: {
    async loadProfile() {
      // 프로필 로드 로직
    },
  },
  mounted() {
    this.loadProfile();
  },
}).mount("#profile-app");
```

#### 위젯 파일 (`./widgets/**/*.php`)

**✅ 필수: 위젯 파일에서는 CSS와 JS를 모두 같은 PHP 파일 내에 작성합니다**

- **CSS 작성 규칙**:
  - **✅ 필수**: 위젯의 CSS는 `<style>` 태그 내에 작성
  - **✅ 권장**: 위젯 고유의 스타일만 작성 (전역 스타일 금지)
  - **✅ 권장**: 위젯별로 고유한 CSS 클래스 이름 사용 (충돌 방지)

- **JavaScript 작성 규칙**:
  - **✅ 필수**: 위젯의 JavaScript는 `<script>` 태그 내에 작성
  - **✅ 필수**: Vue 앱은 고유한 ID를 가진 컨테이너에 마운트
  - **✅ 권장**: 위젯 고유의 로직만 작성

**올바른 위젯 파일 구조:**

```php
<!-- ./widgets/post/post-card.php -->
<?php
/**
 * 게시물 카드 위젯
 *
 * @param array $post 게시물 데이터
 */
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 클래스 사용 -->
<div class="d-flex flex-column gap-2 post-card-widget" id="post-card-<?= $post['id'] ?>">
    <div class="post-card-header">
        <h3 class="post-card-title"><?= $post['title'] ?></h3>
    </div>
    <div class="post-card-body">
        <p class="post-card-content"><?= $post['content'] ?></p>
    </div>
</div>

<!-- ✅ 올바른 방법: 위젯의 CSS는 <style> 태그 내에 작성 -->
<style>
/* 위젯 고유 스타일 */
.post-card-widget {
    background-color: var(--bs-light);
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.post-card-header {
    border-bottom: 1px solid var(--bs-border-color);
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.post-card-title {
    color: var(--bs-primary);
    font-size: 1.2rem;
    font-weight: bold;
}

.post-card-content {
    color: var(--bs-body-color);
    line-height: 1.5;
}
</style>

<!-- ✅ 올바른 방법: 위젯의 JavaScript는 <script> 태그 내에 작성 -->
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                likes: <?= $post['likes'] ?>,
            };
        },
        methods: {
            async likePost() {
                // 좋아요 로직
                this.likes++;
            },
        },
    }).mount('#post-card-<?= $post['id'] ?>');
});
</script>
```

#### 페이지 vs 위젯 비교표

| 항목 | 페이지 (`./page/**/*.php`) | 위젯 (`./widgets/**/*.php`) |
|------|---------------------------|---------------------------|
| **CSS 위치** | ✅ 외부 파일 (`./page/**/*.css`) | ✅ 같은 파일 내 `<style>` 태그 |
| **JS 위치** | ✅ 외부 파일 (`./page/**/*.js`) | ✅ 같은 파일 내 `<script>` 태그 |
| **자동 로드** | ✅ `index.php`에서 자동 로드 | ❌ 수동 include 필요 |
| **Bootstrap 클래스** | ✅ 레이아웃에 사용 | ✅ 레이아웃에 사용 |
| **`<style>` 태그** | ❌ 절대 금지 | ✅ 필수 사용 |
| **`<script>` 태그** | ❌ 절대 금지 | ✅ 필수 사용 |
| **용도** | 전체 페이지 콘텐츠 | 재사용 가능한 컴포넌트 |

#### 위반 시 결과

**페이지 파일에서 `<style>` 또는 `<script>` 태그 사용 시:**
- 자동 로딩 시스템과 충돌
- CSS/JS 파일이 중복 로드됨
- 유지보수 어려움
- 코드 구조가 일관되지 않음

**위젯 파일에서 외부 CSS/JS 파일 사용 시:**
- 위젯을 다른 페이지에서 재사용할 때 CSS/JS 파일도 함께 관리해야 함
- 위젯의 독립성 저하
- 파일 관리가 복잡해짐

### 올바른 CSS 사용 예제

```html
<!-- ✅ 올바른 예: 레이아웃은 인라인 클래스, 스타일은 CSS 파일 -->
<div class="d-flex flex-column align-items-center gap-3 custom-container">
  <div class="position-relative custom-card">
    <h2 class="custom-title">제목</h2>
    <p class="custom-text">내용</p>
  </div>
</div>
```

```css
/* apps/sonub/sonub.css */

/* 커스텀 컨테이너 스타일 */
.custom-container {
  background-color: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  border-radius: 8px;
  padding: 20px;
}

/* 커스텀 카드 스타일 */
.custom-card {
  background-color: var(--bs-secondary-bg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 16px;
}

.custom-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* 커스텀 텍스트 스타일 */
.custom-title {
  color: var(--bs-emphasis-color);
  font-weight: bold;
  margin-bottom: 12px;
}

.custom-text {
  color: var(--bs-body-color);
  line-height: 1.6;
}
```

### 잘못된 CSS 사용 예제 (절대 금지)

```html
<!-- ❌ 절대 금지: 레이아웃과 스타일을 모두 인라인 스타일로 처리 -->
<div
  style="display: flex; flex-direction: column; align-items: center; background: white; padding: 20px; border: 1px solid #ccc;"
>
  <div
    style="position: relative; background: #f5f5f5; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 16px;"
  >
    <h2 style="color: #333; font-weight: bold; margin-bottom: 12px;">제목</h2>
    <p style="color: #666; line-height: 1.6;">내용</p>
  </div>
</div>

<!-- ❌ 절대 금지: 레이아웃을 CSS 파일로만 처리 -->
<div class="custom-layout-container">
  <div class="custom-layout-card">
    <h2 class="custom-title">제목</h2>
    <p class="custom-text">내용</p>
  </div>
</div>
```

```css
/* ❌ 절대 금지: 레이아웃을 CSS 파일에 정의 */
.custom-layout-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  /* 이런 레이아웃 속성은 Bootstrap 클래스로 처리해야 함 */
}

.custom-layout-card {
  position: relative;
  /* 포지션도 Bootstrap 클래스로 처리해야 함 */
}
```

### 위반 시 결과

- HTML 구조와 레이아웃의 분리로 인한 가독성 저하
- 레이아웃 수정 시 CSS 파일과 HTML 파일을 동시에 수정해야 하는 불편함
- Bootstrap의 반응형 유틸리티 클래스 활용 불가
- 코드 유지보수 어려움

### 작업 체크리스트

**1. 레이아웃 작업 시:**

- [ ] Bootstrap 5.3.8 유틸리티 클래스 사용 확인
- [ ] HTML `class` 속성에 직접 작성 확인
- [ ] 반응형 클래스 활용 확인 (예: `d-md-flex`, `col-lg-6`)

**2. 스타일 작업 시:**

- [ ] `apps/sonub/sonub.css` 파일에 클래스 정의 확인
- [ ] 재사용 가능한 클래스명 사용 확인
- [ ] Bootstrap CSS 변수 사용 확인 (`var(--bs-body-color)` 등)

**3. 디자인 완료 후:**

- [ ] 인라인 `style` 속성이 없는지 확인
- [ ] 레이아웃이 Bootstrap 클래스로 구성되었는지 확인
- [ ] 스타일이 CSS 파일에 정의되었는지 확인

---

## Firebase 테스트 계정

**⚠️⚠️⚠️ 개발 및 테스트용 Firebase 계정 정보 ⚠️⚠️⚠️**

### 테스트 계정 개요

Sonub 개발 환경에서는 Firebase 인증 테스트를 위한 사전 설정된 테스트 계정이 제공됩니다.

### 테스트 계정 특징

- **커스텀 UID**: 각 테스트 계정의 Firebase UID는 과일 이름으로 커스텀 설정되어 있습니다
- **공통 비밀번호**: 모든 테스트 계정의 비밀번호는 `12345a,*`로 동일합니다
- **이메일 로그인**: 이메일과 비밀번호로 로그인 가능합니다

### 테스트 계정 목록

| UID        | 이메일              | 전화번호     | 비밀번호   |
| ---------- | ------------------- | ------------ | ---------- |
| apple      | apple@test.com      | +11234567890 | `12345a,*` |
| banana     | banana@test.com     | +11234567891 | `12345a,*` |
| cherry     | cherry@test.com     | +11234567892 | `12345a,*` |
| durian     | durian@test.com     | +11234567893 | `12345a,*` |
| elderberry | elderberry@test.com | +11234567894 | `12345a,*` |
| fig        | fig@test.com        | +11234567895 | `12345a,*` |
| grape      | grape@test.com      | +11234567896 | `12345a,*` |
| honeydew   | honeydew@test.com   | +11234567897 | `12345a,*` |
| jackfruit  | jackfruit@test.com  | +11234567898 | `12345a,*` |
| kiwi       | kiwi@test.com       | +11234567899 | `12345a,*` |
| lemon      | lemon@test.com      | +11234567900 | `12345a,*` |
| mango      | mango@test.com      | +11234567901 | `12345a,*` |

### 사용 예시

**로그인 테스트:**

```javascript
// banana 계정으로 로그인
firebase
  .auth()
  .signInWithEmailAndPassword("banana@test.com", "12345a,*")
  .then((userCredential) => {
    console.log("로그인 성공:", userCredential.user.uid); // "banana"
  })
  .catch((error) => {
    console.error("로그인 실패:", error.message);
  });
```

**Vue.js에서 사용:**

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      email: "apple@test.com",
      password: "12345a,*",
      error: null,
    };
  },
  methods: {
    async testLogin() {
      try {
        const result = await firebase
          .auth()
          .signInWithEmailAndPassword(this.email, this.password);
        console.log("테스트 계정 로그인 성공:", result.user.uid);
      } catch (err) {
        this.error = "로그인 실패: " + err.message;
      }
    },
  },
}).mount("#app");
```

### 주의사항

- **개발 환경 전용**: 이 계정들은 개발 및 테스트 목적으로만 사용해야 합니다
- **운영 환경 금지**: 운영 환경에서는 절대 사용하지 마세요
- **비밀번호 변경 금지**: 테스트 계정의 비밀번호를 변경하지 마세요
- **UID 일관성**: Firebase UID가 과일 이름으로 고정되어 있어 테스트 데이터 생성 시 유용합니다

### 활용 시나리오

1. **사용자 인증 테스트**: 로그인/로그아웃 기능 테스트
2. **권한 관리 테스트**: 여러 사용자 계정으로 권한 확인
3. **소셜 기능 테스트**: 친구 추가, 팔로우 등 테스트
4. **알림 기능 테스트**: 사용자 간 알림 전송 테스트
5. **채팅 기능 테스트**: 다중 사용자 채팅 테스트

---

## 필수 언어 사용 규칙

**⚠️⚠️⚠️ 최강력 경고: 모든 주석과 텍스트는 반드시 한국어로 작성해야 한다 ⚠️⚠️⚠️**

### 주석 및 텍스트 작성 규칙

- **✅ 필수**: 모든 PHP, JavaScript, HTML, CSS 파일의 주석은 **반드시 한국어**로 작성
- **✅ 필수**: 변수명, 함수명, 클래스명 등 코드 자체는 영문 사용 (camelCase, snake_case)
- **✅ 필수**: 주석, 설명, 문서화는 **무조건 한국어**로 작성
- **✅ 필수**: 사용자에게 표시되는 모든 텍스트는 **한국어**로 작성
- **❌ 금지**: 주석을 영어로 작성하는 것 절대 금지
- **❌ 금지**: UI 텍스트를 영어로 하드코딩하는 것 절대 금지

### 예외 사항

- 코드 자체 (변수명, 함수명, 클래스명)
- 기술 용어 (API, REST, JSON 등)
- 파일명과 경로
- 라이브러리 및 프레임워크 관련 코드

### 올바른 예제

```php
<?php
/**
 * 사용자 정보를 조회하는 함수
 *
 * @param int $user_id 사용자 ID
 * @return array 사용자 정보 배열
 */
function getUserInfo($user_id) {
    // 데이터베이스에서 사용자 정보 조회
    $user = db()->get('users', $user_id);

    // 사용자 정보가 없으면 빈 배열 반환
    if (!$user) {
        return [];
    }

    // 사용자 정보 반환
    return $user;
}
?>
```

```javascript
/**
 * 게시글 목록을 로드하는 함수
 * @param {number} page - 페이지 번호
 * @returns {Promise} 게시글 목록 프로미스
 */
function loadPosts(page) {
  // API 호출하여 게시글 목록 가져오기
  return fetch(`/api/posts?page=${page}`)
    .then((response) => response.json())
    .catch((error) => {
      // 에러 발생 시 콘솔에 출력
      console.error("게시글 로드 실패:", error);
    });
}
```

```html
<!-- 사용자 프로필 카드 -->
<div class="card">
  <div class="card-body">
    <!-- 프로필 이미지 -->
    <img src="<?= $user->photo ?>" alt="프로필 사진" />

    <!-- 사용자 이름 -->
    <h5 class="card-title"><?= $user->name ?></h5>

    <!-- 자기소개 -->
    <p class="card-text"><?= $user->bio ?></p>
  </div>
</div>
```

### 잘못된 예제 (절대 금지)

```php
<?php
/**
 * Get user information
 *
 * @param int $user_id User ID
 * @return array User information array
 */
function getUserInfo($user_id) {
    // Get user from database  ❌ 영어 주석 금지
    $user = db()->get('users', $user_id);

    // Return empty array if user not found  ❌ 영어 주석 금지
    if (!$user) {
        return [];
    }

    return $user;
}
?>
```

```html
<!-- User profile card -->
❌ 영어 주석 금지
<div class="card">
  <div class="card-body">
    <h5>Welcome</h5>
    ❌ 영어 UI 텍스트 금지 <button>Click here</button> ❌ 영어 버튼 텍스트 금지
  </div>
</div>
```

### 위반 시 결과

- 코드 가독성 저하
- 팀 내 일관성 파괴
- 유지보수 어려움
- 협업 효율성 감소

---

## 모듈 로딩

**⚠️⚠️⚠️ 중요: 페이지별 전처리 작업을 위한 모듈 시스템 ⚠️⚠️⚠️**

### 모듈이란?

- **정의**: 각 페이지 스크립트가 실행되기 이전에 실행되는 코드 파일
- **용도**: PHP 헤더 설정, 쿠키 처리, 인증 확인 등 각종 전처리 작업 수행
- **실행 시점**: 페이지 콘텐츠가 로드되기 **이전**에 시작 스크립트(`index.php`)에서 자동으로 로드됨

### 모듈 파일 명명 규칙

- **파일명 패턴**: `*.module.php`
- **파일 위치**: 페이지 스크립트 파일과 동일한 폴더
- **자동 로딩**: `index.php`가 페이지 경로를 기반으로 모듈 파일을 자동으로 검색하고 include

### 파일 구조 예시

```
page/
├── user/
│   ├── profile.php          ← 페이지 파일
│   ├── profile.module.php   ← 모듈 파일 (페이지 실행 전 자동 로드)
│   ├── profile.css
│   ├── profile.js
│   ├── login.php
│   ├── login.module.php     ← 로그인 페이지 모듈
│   └── logout-submit.php
└── admin/
    ├── dashboard.php
    └── dashboard.module.php ← 관리자 페이지 모듈
```

### 모듈 로딩 동작 방식

1. **모듈 파일이 존재하는 경우**:
   - `index.php`가 페이지 경로에서 모듈 파일을 검색
   - 모듈 파일(`*.module.php`)을 먼저 `include`
   - 모듈 코드 실행 (헤더 설정, 쿠키 처리 등)
   - 이후 실제 페이지 파일을 로드하여 화면에 표시

2. **모듈 파일이 없는 경우**:
   - 모듈 로딩 단계를 건너뜀
   - 바로 페이지 파일을 로드하여 화면에 표시

### 모듈 사용 예제

#### 예제 1: 로그인 확인 모듈

```php
<?php
// page/user/profile.module.php

/**
 * 프로필 페이지 모듈
 *
 * 페이지 접근 전 로그인 상태를 확인하고,
 * 로그인하지 않은 경우 로그인 페이지로 리다이렉트
 */

// 로그인 확인
$user = login();

if (!$user) {
    // 로그인하지 않은 경우 로그인 페이지로 리다이렉트
    header('Location: /user/login');
    exit;
}

// 로그인한 사용자 정보를 페이지에서 사용할 수 있도록 설정
$profile_user = $user;
```

```php
<?php
// page/user/profile.php

/**
 * 프로필 페이지
 *
 * profile.module.php에서 로그인을 확인했으므로
 * 이 파일에서는 $profile_user를 안전하게 사용 가능
 */
?>

<div class="container">
    <h1><?= $profile_user->display_name ?>님의 프로필</h1>
    <p>이메일: <?= $profile_user->email ?></p>
</div>
```

#### 예제 2: 로그아웃 처리 모듈

```php
<?php
// page/user/logout-submit.module.php

/**
 * 로그아웃 처리 모듈
 *
 * 페이지가 로드되기 전에 세션을 정리하고 쿠키를 삭제
 */

// 로그인 확인
$user = login();

if ($user) {
    // 세션 쿠키 삭제
    clear_session_cookie();
}

// 메인 페이지로 리다이렉트
header('Location: /');
exit;
```

```php
<?php
// page/user/logout-submit.php

/**
 * 로그아웃 완료 페이지
 *
 * 실제로는 module에서 리다이렉트되므로 이 파일은 실행되지 않음
 * 하지만 페이지 파일은 존재해야 함
 */

echo '로그아웃 처리 중...';
```

#### 예제 3: 관리자 권한 확인 모듈

```php
<?php
// page/admin/dashboard.module.php

/**
 * 관리자 대시보드 모듈
 *
 * 관리자 권한 확인 및 권한 없는 경우 접근 차단
 */

// 로그인 확인
$user = login();

if (!$user) {
    // 로그인하지 않은 경우
    header('Location: /user/login');
    exit;
}

// 관리자 권한 확인
if (!is_admin($user)) {
    // 관리자가 아닌 경우 403 에러
    http_response_code(403);
    echo '접근 권한이 없습니다.';
    exit;
}

// 관리자 통계 데이터 사전 로드
$admin_stats = [
    'total_users' => db()->select('COUNT(*) as count')->from('users')->first()['count'],
    'total_posts' => db()->select('COUNT(*) as count')->from('posts')->first()['count'],
    'today_signups' => db()->select('COUNT(*) as count')
        ->from('users')
        ->where('created_at > ?', [strtotime('today')])
        ->first()['count'],
];
```

```php
<?php
// page/admin/dashboard.php

/**
 * 관리자 대시보드 페이지
 *
 * dashboard.module.php에서 권한을 확인했으므로
 * 이 파일에서는 $admin_stats를 안전하게 사용 가능
 */
?>

<div class="container">
    <h1>관리자 대시보드</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>총 사용자</h5>
                    <p class="h2"><?= number_format($admin_stats['total_users']) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>총 게시글</h5>
                    <p class="h2"><?= number_format($admin_stats['total_posts']) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>오늘 가입자</h5>
                    <p class="h2"><?= number_format($admin_stats['today_signups']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
```

#### 예제 4: 쿠키 및 헤더 설정 모듈

```php
<?php
// page/api/data.module.php

/**
 * API 데이터 페이지 모듈
 *
 * JSON 응답을 위한 헤더 설정 및 CORS 처리
 */

// JSON 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

// CORS 헤더 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// OPTIONS 요청 처리 (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// API 키 검증
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

if (empty($api_key)) {
    http_response_code(401);
    echo json_encode([
        'error' => 'API 키가 필요합니다',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// API 키 유효성 검증
$valid_key = verify_api_key($api_key);

if (!$valid_key) {
    http_response_code(403);
    echo json_encode([
        'error' => '유효하지 않은 API 키입니다',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
```

#### 예제 5: JSON 응답 후 즉시 종료

```php
<?php
// page/api/user-status.module.php

/**
 * 사용자 상태 API 모듈
 *
 * 사용자 상태를 JSON으로 반환하고 즉시 종료
 */

// JSON 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

// 로그인 확인
$user = login();

if (!$user) {
    // 로그인하지 않은 경우 JSON 응답 후 즉시 종료
    http_response_code(401);
    echo json_encode([
        'error' => 'unauthorized',
        'message' => '로그인이 필요합니다',
        'logged_in' => false,
    ], JSON_UNESCAPED_UNICODE);
    exit(); // 프로세스 즉시 종료 - 페이지 파일은 실행되지 않음
}

// 사용자 통계 정보 조회
$stats = db()->select('post_count, comment_count, follower_count')
    ->from('user_stats')
    ->where('user_id = ?', [$user->id])
    ->first();

// 성공 응답 후 즉시 종료
http_response_code(200);
echo json_encode([
    'success' => true,
    'logged_in' => true,
    'user' => [
        'id' => $user->id,
        'display_name' => $user->display_name,
        'email' => $user->email,
    ],
    'stats' => $stats,
], JSON_UNESCAPED_UNICODE);
exit(); // 프로세스 즉시 종료 - 페이지 파일은 실행되지 않음
```

```php
<?php
// page/api/user-status.php

/**
 * 이 파일은 실행되지 않습니다
 *
 * module 파일에서 exit()를 호출했으므로
 * 이 페이지 파일은 절대 실행되지 않습니다.
 * 그러나 페이지 파일은 반드시 존재해야 합니다.
 */

echo '이 메시지는 절대 표시되지 않습니다';
```

**사용 예제 (클라이언트):**

```javascript
// JavaScript에서 API 호출
fetch('/api/user-status')
    .then(response => response.json())
    .then(data => {
        if (data.logged_in) {
            console.log('사용자:', data.user.display_name);
            console.log('게시글 수:', data.stats.post_count);
        } else {
            console.log('로그인 필요:', data.message);
        }
    })
    .catch(error => {
        console.error('API 호출 실패:', error);
    });
```

### 모듈 사용 시 주의사항

**✅ 모듈에서 할 수 있는 작업:**

- HTTP 헤더 설정 (`header()` 함수)
- 쿠키 설정/삭제 (`setcookie()` 함수)
- 인증 확인 및 리다이렉트
- 권한 검증
- 페이지에서 사용할 데이터 사전 로드
- 세션 초기화 또는 정리
- 에러 핸들링 및 종료 (`exit`, `die`)

**❌ 모듈에서 하지 말아야 할 작업:**

- HTML 출력 (헤더 전송 후 출력 불가)
- `echo`, `print` 등 출력 함수 사용 (에러 메시지 제외)
- 페이지 레이아웃 관련 작업
- JavaScript나 CSS 코드 삽입

**중요 규칙:**

1. **헤더 전송 순서**: 모듈에서 `header()` 함수를 사용하는 경우, 어떤 출력도 먼저 발생하면 안 됨
2. **조기 종료**: 모듈에서 `exit`나 `die`를 호출하면 페이지 파일은 실행되지 않음
3. **변수 공유**: 모듈에서 설정한 변수는 페이지 파일에서 사용 가능
4. **파일명 일치**: 모듈 파일명의 기본 부분은 페이지 파일명과 일치해야 함 (예: `profile.module.php` ↔ `profile.php`)
5. **JSON 응답 및 즉시 종료**: 모듈에서 필요하다면 JSON 등으로 데이터를 클라이언트에 보내고, `exit()`를 호출해서 프로세스를 즉시 종료할 수 있음

### 모듈이 필요한 경우

**다음과 같은 상황에서 모듈 파일을 생성하세요:**

- 페이지 접근 전 로그인 확인이 필요한 경우
- 관리자 권한 등 특정 권한 검증이 필요한 경우
- 페이지 로드 전 리다이렉트가 필요한 경우
- HTTP 헤더나 쿠키 설정이 필요한 경우
- 페이지에서 사용할 데이터를 사전에 로드해야 하는 경우
- API 엔드포인트에서 인증 처리가 필요한 경우

### 모듈이 불필요한 경우

**다음과 같은 상황에서는 모듈 파일을 생성하지 마세요:**

- 단순히 HTML을 표시하는 페이지
- 전처리 작업이 필요 없는 정적 페이지
- Vue.js에서 클라이언트 사이드 인증을 처리하는 페이지
- 이미 다른 방식으로 인증이 처리되는 페이지

---
