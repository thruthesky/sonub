# Sonub API 문서

## 목차

- [Sonub API 문서](#sonub-api-문서)
  - [목차](#목차)
  - [개요](#개요)
  - [UTF-8 인코딩 필수 규칙](#utf-8-인코딩-필수-규칙)
  - [API First 설계 철학](#api-first-설계-철학)
  - [API 동작 방식](#api-동작-방식)
  - [api.php 상세 동작 방식](#apiphp-상세-동작-방식)
    - [개요](#개요-1)
    - [핵심 코드 구조](#핵심-코드-구조)
    - [단계별 동작 설명](#단계별-동작-설명)
    - [실제 사용 예제](#실제-사용-예제)
    - [http_params() 함수](#http_params-함수)
    - [func 필드 자동 추가](#func-필드-자동-추가)
    - [API 설계의 장점](#api-설계의-장점)
    - [주의사항](#주의사항)
  - [API 엔드포인트](#api-엔드포인트)
  - [LIB 폴더 구조](#lib-폴더-구조)
  - [API 호출 예제](#api-호출-예제)
    - [기본 요청](#기본-요청)
    - [사용자 관련 API](#사용자-관련-api)
    - [게시글 관련 API](#게시글-관련-api)
  - [에러 처리](#에러-처리)
  - [보안 고려사항](#보안-고려사항)

---

## 개요

Sonub는 **API First** 설계 철학을 따르는 웹 애플리케이션입니다. 모든 핵심 기능은 API를 통해 접근 가능하며, RESTful 클라이언트에서 직접 호출할 수 있습니다.

---

## API First 설계 철학

**Sonub는 API First 클래스 시스템입니다:**

- ✅ **모든 LIB 폴더의 함수는 API를 통해 직접 호출 가능**
- ✅ RESTful 클라이언트가 API를 통해 모든 기능에 접근 가능
- ✅ 프론트엔드와 백엔드가 명확히 분리됨
- ✅ 모바일 앱, 웹 앱, 서드파티 서비스 등 다양한 클라이언트 지원

---

## API 동작 방식

**핵심 구조:**

```
클라이언트 → api.php → LIB 폴더의 함수 → 응답 반환
```

1. **api.php**: 루트 폴더에 위치한 API 게이트웨이

   - 모든 RESTful 요청을 수신
   - 요청을 파싱하여 적절한 LIB 함수로 라우팅
   - 함수 실행 결과를 클라이언트에 반환

2. **LIB 폴더**: 모든 비즈니스 로직과 함수가 저장된 폴더

   - 각 함수는 독립적으로 실행 가능
   - API를 통해 직접 호출 가능
   - 재사용 가능한 모듈화된 구조

3. **응답**: JSON 형식으로 결과 반환
   - 성공 시: 함수 실행 결과
   - 실패 시: 에러 메시지 및 상태 코드

---

## api.php 상세 동작 방식

### 개요

**파일 위치:** `/api.php` (프로젝트 루트)

**핵심 개념:**

- URL 파라미터 `f`로 호출할 함수 이름을 지정
- 해당 함수가 동적으로 실행됨
- 모든 응답은 JSON 형식으로 리턴
- 예외가 발생하면 자동으로 에러 응답 생성

### 핵심 코드 구조

```php
<?php
const ROOT_DIR = __DIR__;
include_once ROOT_DIR . '/etc/includes.php';
header('Content-Type: application/json; charset=utf-8');

if (http_params('f')) {
    try {
        // 함수 호출
        $res = http_params('f')(http_params());
        if (is_array($res) || is_object($res)) {
            if (isset($res['error_code'])) {
                http_response_code($res['response_code'] ?? 400);
                echo json_encode($res, JSON_UNESCAPED_UNICODE);
            }
            $res['func'] = http_params('f');
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(error('response-not-array-or-object'), JSON_UNESCAPED_UNICODE);
        }
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(error('exception', $e->getMessage(), ['trace' => $e->getTraceAsString()]), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400);
    echo json_encode(error('no-function-specified'), JSON_UNESCAPED_UNICODE);
}
?>
```

### 단계별 동작 설명

#### 1단계: 헤더 설정

```php
header('Content-Type: application/json; charset=utf-8');
```

- 모든 응답이 UTF-8 인코딩된 JSON임을 클라이언트에 알림
- 한글 등 유니코드 문자를 올바르게 전송하기 위해 필수

#### 2단계: 함수 이름 확인

```php
if (http_params('f')) {
    // 함수 호출 로직
} else {
    // 에러: 함수 이름이 지정되지 않음
    http_response_code(400);
    echo json_encode(error('no-function-specified'), JSON_UNESCAPED_UNICODE);
}
```

- `http_params('f')`: URL 파라미터 `f`에서 호출할 함수 이름을 가져옴
- 함수 이름이 없으면 400 에러 리턴

#### 3단계: 동적 함수 호출

```php
$res = http_params('f')(http_params());
```

**🔥 이 한 줄이 API의 핵심입니다!**

- `http_params('f')`: 함수 이름을 문자열로 가져옴 (예: `'app_version'`)
- `http_params()`: 모든 HTTP 파라미터를 배열로 가져옴
- PHP의 **동적 함수 호출** 기능을 사용하여 실제 함수 실행

**예제:**

```php
// URL: https://local.sonub.com/api.php?f=app_version
// 실제 실행: app_version(['f' => 'app_version'])

// URL: https://local.sonub.com/api.php?f=getUserInfo&user_id=123
// 실제 실행: getUserInfo(['f' => 'getUserInfo', 'user_id' => '123'])
```

#### 4단계: 응답 처리

```php
if (is_array($res) || is_object($res)) {
    // 에러 체크
    if (isset($res['error_code'])) {
        http_response_code($res['response_code'] ?? 400);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    // 성공 응답에 함수 이름 추가
    $res['func'] = http_params('f');
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
}
```

**중요한 특징:**

- 함수가 배열이나 객체를 리턴하면 정상 처리
- 에러가 있으면 (`error_code` 필드 존재) HTTP 상태 코드 설정 후 즉시 응답
- **성공 응답에는 자동으로 `func` 필드 추가** - 어떤 함수가 실행되었는지 표시

**⚠️ 중요: 에러 응답 처리 방식**

코드에는 버그가 있어서 에러 응답도 두 번 출력됩니다:

```php
// 에러 응답을 먼저 출력
if (isset($res['error_code'])) {
    http_response_code($res['response_code'] ?? 400);
    echo json_encode($res, JSON_UNESCAPED_UNICODE);  // 첫 번째 출력
}

// 그런 다음 func 필드를 추가하고 다시 출력
$res['func'] = http_params('f');
echo json_encode($res, JSON_UNESCAPED_UNICODE);  // 두 번째 출력
```

따라서 에러가 발생하면 **두 개의 JSON 객체가 연속으로 출력**됩니다:

```json
{"error_code":"app-error","error_message":"에러 메시지","error_data":[],"error_response_code":400}{"error_code":"app-error","error_message":"에러 메시지","error_data":[],"error_response_code":400,"func":"app_error"}
```

**올바른 처리 방법:**

에러 응답 후에는 `return`이나 `exit`를 추가해야 합니다:

```php
if (isset($res['error_code'])) {
    http_response_code($res['response_code'] ?? 400);
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;  // 여기서 종료해야 함!
}
```

#### 5단계: 예외 처리

```php
catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(error('exception', $e->getMessage(), ['trace' => $e->getTraceAsString()]), JSON_UNESCAPED_UNICODE);
}
```

- 함수 실행 중 발생한 모든 예외를 캐치
- 예외 메시지와 스택 트레이스를 포함한 에러 응답 리턴
- HTTP 상태 코드 500 (Internal Server Error) 설정

### 실제 사용 예제

#### 예제 1: 정상 응답

**요청:**
```
GET https://local.sonub.com/api.php?f=app_version
```

**실행 과정:**
1. `http_params('f')` → `'app_version'`
2. `app_version()` 함수 호출 (lib/app/app.info.php)
3. 함수 리턴값: `['version' => '2025-10-03-21-32-20']`
4. **`func` 필드 자동 추가**: `['version' => '2025-10-03-21-32-20', 'func' => 'app_version']`

**응답:**
```json
{
    "func": "app_version",
    "version": "2025-10-03-21-32-20"
}
```

**HTTP 상태 코드:** 200

---

#### 예제 2: 에러 응답 (함수 없음)

**요청:**
```
GET https://local.sonub.com/api.php?f=wrongfunc
```

**실행 과정:**
1. `http_params('f')` → `'wrongfunc'`
2. `wrongfunc()` 함수 호출 시도
3. 함수가 존재하지 않아 예외 발생
4. `catch` 블록에서 예외 캐치

**응답:**
```json
{
    "error_code": "exception",
    "error_message": "Call to undefined function wrongfunc()",
    "error_data": {
        "trace": "#0 {main}"
    },
    "error_response_code": 400
}
```

**HTTP 상태 코드:** 500

---

#### 예제 3: 함수 이름 누락

**요청:**
```
GET https://local.sonub.com/api.php
```

**실행 과정:**
1. `http_params('f')` → `null` (파라미터 `f`가 없음)
2. `if (http_params('f'))` 조건이 `false`
3. `else` 블록 실행 - `no-function-specified` 에러 리턴

**응답:**
```json
{
    "error_code": "no-function-specified",
    "error_message": "",
    "error_data": [],
    "error_response_code": 400
}
```

**HTTP 상태 코드:** 400

**설명:**

- URL에 `f` 파라미터가 없으면 이 에러가 발생합니다
- 함수 이름을 지정하지 않았다는 의미입니다
- `error_message`가 빈 문자열인 것은 `error()` 함수 호출 시 메시지를 생략했기 때문입니다

---

#### 예제 4: 파라미터가 있는 함수 호출

**요청:**
```
GET https://local.sonub.com/api.php?f=getUserInfo&user_id=123
```

**실행 과정:**
1. `http_params('f')` → `'getUserInfo'`
2. `http_params()` → `['f' => 'getUserInfo', 'user_id' => '123']`
3. `getUserInfo(['f' => 'getUserInfo', 'user_id' => '123'])` 실행
4. 함수 내부에서 `http_params('user_id')`로 `123` 접근 가능

**응답 (성공 시):**
```json
{
    "func": "getUserInfo",
    "user_id": 123,
    "name": "홍길동",
    "email": "hong@example.com"
}
```

**HTTP 상태 코드:** 200

**응답 (에러 시):**
```json
{
    "error_code": "user-not-found",
    "error_message": "사용자를 찾을 수 없습니다",
    "error_data": {
        "user_id": 123
    },
    "error_response_code": 404
}
```

**HTTP 상태 코드:** 404

---

### http_params() 함수

**파일 위치:** `lib/api/input.functions.php`

**역할:**

- HTTP 요청에서 파라미터를 가져오는 통합 함수
- GET, POST, JSON 바디의 데이터를 모두 처리
- `$_REQUEST`와 JSON 입력을 자동으로 병합

**함수 시그니처:**

```php
function http_params(string $name = ''): mixed
```

**사용법:**

```php
// 모든 파라미터 가져오기
$all_params = http_params();
// ['f' => 'getUserInfo', 'user_id' => '123', 'name' => '홍길동']

// 특정 파라미터 가져오기
$user_id = http_params('user_id');  // '123'
$name = http_params('name');        // '홍길동'

// 존재하지 않는 파라미터
$email = http_params('email');      // null
```

**특별한 처리:**

- `'null'`, `'undefined'`, 빈 문자열(`''`)은 `null`로 변환
- `'0'`, `'false'`는 문자열 그대로 리턴

**내부 동작:**

```php
// JSON 입력 읽기
$json = @file_get_contents('php://input');
$decoded_json = json_decode($json, true);

// $_REQUEST와 JSON 데이터 병합
$in = [];
if ($decoded_json !== null) {
    $in = array_merge($_REQUEST, $decoded_json);
} else {
    $in = $_REQUEST;
}
```

### func 필드 자동 추가

**중요 특징:**

모든 성공 응답에는 `func` 필드가 자동으로 추가됩니다.

```php
$res['func'] = http_params('f');
```

**이유:**

- 클라이언트에서 어떤 함수가 실행되었는지 명확히 알 수 있음
- 디버깅과 로깅에 유용
- API 응답 추적이 쉬워짐

**예제:**

```php
// 함수 정의
function app_version(): array {
    return ['version' => '2025-10-03'];
}

// 요청: /api.php?f=app_version
// 함수 리턴값: ['version' => '2025-10-03']
// 최종 응답: ['version' => '2025-10-03', 'func' => 'app_version']
```

### API 설계의 장점

1. **간결성**: 새로운 API 엔드포인트를 추가하려면 함수만 만들면 됨
2. **일관성**: 모든 API가 동일한 방식으로 동작
3. **자동화**: 함수 이름이 자동으로 응답에 포함됨
4. **에러 처리**: 모든 예외가 자동으로 JSON 에러 응답으로 변환
5. **유연성**: GET, POST, JSON 바디 모두 지원
6. **개발 속도**: 복잡한 라우팅 설정 없이 빠르게 개발 가능

### 주의사항

**⚠️ 보안 고려사항:**

1. **함수 노출**: 모든 public 함수가 API로 노출될 수 있음
   - 민감한 함수는 인증 체크 필수
   - 내부 전용 함수는 `_`로 시작하는 등의 네이밍 규칙 사용

2. **입력 검증**: 함수 내부에서 반드시 입력값 검증 수행
   ```php
   function getUserInfo($params) {
       $user_id = http_params('user_id');
       if (empty($user_id)) {
           return error('invalid-user-id', '사용자 ID가 필요합니다');
       }
       // ... 나머지 로직
   }
   ```

3. **리턴 타입**: 함수는 반드시 배열이나 객체를 리턴해야 함
   ```php
   // ✅ 올바른 예
   function getUser() {
       return ['name' => '홍길동'];
   }

   // ❌ 잘못된 예
   function getUser() {
       return '홍길동';  // 문자열 리턴 시 에러
   }
   ```

4. **함수 이름**: 공개 API로 사용할 함수 이름은 명확하고 일관성 있게 작성
   ```php
   // ✅ 좋은 예
   function getUserProfile() { }
   function createPost() { }
   function deleteComment() { }

   // ❌ 나쁜 예
   function get() { }        // 너무 일반적
   function do_something() { }  // 모호함
   ```

---

## API 엔드포인트

**기본 엔드포인트:**

```
GET/POST https://sonub.com/api.php?f=함수명
```

**요청 방식:**

1. **GET 파라미터:**
   ```
   GET https://sonub.com/api.php?f=getUserInfo&user_id=123
   ```

2. **POST 파라미터:**
   ```
   POST https://sonub.com/api.php?f=createPost
   Body: title=제목&content=내용
   ```

3. **JSON 바디:**
   ```
   POST https://sonub.com/api.php?f=createPost
   Content-Type: application/json
   Body: {"title":"제목","content":"내용"}
   ```

**응답 형식 (성공):**

```json
{
  "func": "함수명",
  "데이터키1": "값1",
  "데이터키2": "값2"
}
```

**응답 형식 (에러):**

```json
{
  "error_code": "에러코드",
  "error_message": "에러 메시지",
  "error_data": {},
  "error_response_code": 400
}
```

---

## LIB 폴더 구조

**주요 LIB 폴더 및 파일:**

```
lib/
├── api/
│   └── input.functions.php    # API 입력 처리 함수
├── db/
│   ├── db.php                  # 데이터베이스 기본 함수
│   ├── entity.php              # 엔티티 관리 함수
│   ├── user.php                # 사용자 DB 함수
│   └── post.php                # 게시글 DB 함수
├── user/
│   └── crud.php                # 사용자 CRUD 함수
├── l10n/
│   ├── t.php                   # 번역 클래스
│   ├── texts.php               # 번역 텍스트 저장소
│   └── language.functions.php # 언어 관련 함수
├── page/
│   └── page.functions.php      # 페이지 관련 함수
├── href/
│   └── href.functions.php      # URL 생성 함수
├── debug/
│   └── debug.functions.php     # 디버깅 함수
└── functions.php               # 공통 유틸리티 함수
```

**API를 통해 호출 가능한 함수들:**

- **사용자 관리**: 회원가입, 로그인, 프로필 수정 등
- **게시글 관리**: 게시글 작성, 수정, 삭제, 조회 등
- **데이터베이스 작업**: CRUD 작업, 검색, 필터링 등
- **다국어 처리**: 번역 텍스트 조회 및 관리
- **유틸리티**: 파일 업로드, 이미지 처리 등

---

## API 호출 예제

### 기본 요청

**cURL 예제:**

```bash
curl -X POST https://sonub.com/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "function": "getUserInfo",
    "params": {
      "user_id": 123
    }
  }'
```

**JavaScript (Fetch API) 예제:**

```javascript
// API 호출 함수
async function callApi(functionName, params = {}) {
  const response = await fetch("/api.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      function: functionName,
      params: params,
    }),
  });

  return await response.json();
}

// 사용 예제
const result = await callApi("getUserInfo", { user_id: 123 });
console.log(result);
```

### 사용자 관련 API

**사용자 정보 조회:**

```javascript
// 사용자 정보 가져오기
const user = await callApi("getUserInfo", {
  user_id: 123,
});

console.log(user.data);
```

**사용자 프로필 업데이트:**

```javascript
// 프로필 업데이트
const result = await callApi("updateUserProfile", {
  user_id: 123,
  name: "홍길동",
  bio: "안녕하세요!",
});

if (result.success) {
  console.log("프로필 업데이트 성공");
}
```

### 게시글 관련 API

**게시글 목록 조회:**

```javascript
// 게시글 목록 가져오기
const posts = await callApi("getPosts", {
  page: 1,
  limit: 10,
  order: "desc",
});

console.log(posts.data);
```

**게시글 작성:**

```javascript
// 새 게시글 작성
const newPost = await callApi("createPost", {
  title: "새로운 게시글",
  content: "게시글 내용입니다.",
  category: "general",
});

if (newPost.success) {
  console.log("게시글 작성 완료:", newPost.data.post_id);
}
```

---

## 에러 처리

### 에러 응답 형식

Sonub API는 `error()` 함수를 통해 표준화된 에러 응답을 제공합니다.

**표준 에러 응답 형식:**

```json
{
  "error_code": "에러코드",
  "error_message": "에러 메시지",
  "error_data": {},
  "error_response_code": 400
}
```

**필드 설명:**

- `error_code`: kebab-case 형식의 에러 코드 (예: `user-not-found`)
- `error_message`: 사용자에게 표시할 에러 메시지
- `error_data`: 추가 에러 정보를 담은 객체
- `error_response_code`: HTTP 응답 코드 (400, 401, 403, 404, 500 등)

### 에러 응답 유형

#### 1. 함수 이름 누락 에러

**요청:**
```
GET https://sonub.com/api.php
```

**응답:**
```json
{
  "error_code": "no-function-specified",
  "error_message": "",
  "error_data": [],
  "error_response_code": 400
}
```

**HTTP 상태 코드:** 400

---

#### 2. 함수 실행 에러 (error() 함수 사용)

**요청:**
```
GET https://sonub.com/api.php?f=app_error
```

**응답 (현재 버그로 인해 두 번 출력됨):**
```json
{"error_code":"app-error","error_message":"에러 테스트용 간단한 API","error_data":[],"error_response_code":400}{"error_code":"app-error","error_message":"에러 테스트용 간단한 API","error_data":[],"error_response_code":400,"func":"app_error"}
```

**⚠️ 중요:**

현재 api.php 코드에 버그가 있어 에러 응답이 **두 번 연속으로 출력**됩니다:
1. 첫 번째 JSON: `error_code`가 있어서 바로 출력
2. 두 번째 JSON: `func` 필드가 추가되어 다시 출력

**클라이언트에서 처리 방법:**

두 개의 JSON 중 **첫 번째 JSON만 파싱**하거나, **두 번째 JSON (func 필드 포함)을 파싱**하세요.

```javascript
// 방법 1: 첫 번째 JSON만 파싱
const firstJson = responseText.split('}')[0] + '}';
const error = JSON.parse(firstJson);

// 방법 2: 두 번째 JSON 파싱 (func 필드 포함)
const jsonObjects = responseText.match(/\{[^}]+\}/g);
const errorWithFunc = JSON.parse(jsonObjects[1]);
```

**HTTP 상태 코드:** 400 (또는 error() 함수에서 지정한 코드)

---

#### 3. 예외 발생 에러 (함수 없음, 치명적 오류 등)

**요청:**
```
GET https://sonub.com/api.php?f=wrongfunc
```

**응답:**
```json
{
  "error_code": "exception",
  "error_message": "Call to undefined function wrongfunc()",
  "error_data": {
    "trace": "#0 {main}"
  },
  "error_response_code": 400
}
```

**HTTP 상태 코드:** 500

**설명:**

- 함수가 존재하지 않거나 치명적인 오류가 발생하면 예외가 발생합니다
- `catch (Throwable $e)` 블록에서 예외를 캐치하여 JSON 에러 응답을 생성합니다
- `error_data`에 스택 트레이스가 포함됩니다

---

### 일반적인 에러 코드

**시스템 에러:**

- `no-function-specified`: 함수 이름이 지정되지 않음 (HTTP 400)
- `exception`: 예외 발생 (HTTP 500)
- `response-not-array-or-object`: 함수가 배열이나 객체를 리턴하지 않음 (HTTP 500)

**애플리케이션 에러 (error() 함수 사용):**

- `invalid-input`: 잘못된 입력값 (HTTP 400)
- `missing-parameter`: 필수 매개변수 누락 (HTTP 400)
- `user-not-found`: 사용자를 찾을 수 없음 (HTTP 404)
- `permission-denied`: 권한 부족 (HTTP 403)
- `already-exists`: 이미 존재함 (HTTP 409)
- `database-error`: 데이터베이스 오류 (HTTP 500)
- `authentication-failed`: 인증 실패 (HTTP 401)
- `session-expired`: 세션 만료 (HTTP 401)

### 에러 확인 방법

**JavaScript에서 에러 확인:**

```javascript
// API 호출
const response = await fetch('/api.php?f=getUserInfo&user_id=123');
const result = await response.json();

// 방법 1: error_code 필드 확인
if (result.error_code) {
  console.error('에러 발생:', result.error_message);
  console.error('에러 코드:', result.error_code);
  console.error('추가 정보:', result.error_data);
  return;
}

// 방법 2: HTTP 상태 코드 확인
if (!response.ok) {
  console.error('HTTP 에러:', response.status);
  console.error('에러 응답:', result);
  return;
}

// 성공 처리
console.log('함수:', result.func);
console.log('데이터:', result);
```

**PHP에서 에러 확인:**

```php
<?php
// 함수 호출
$result = getUserInfo(123);

// error_code 필드 확인
if (isset($result['error_code'])) {
    echo "에러 발생: " . $result['error_message'];
    echo "에러 코드: " . $result['error_code'];
    return $result;  // 에러를 상위로 전파
}

// 성공 처리
echo "사용자: " . $result['name'];
?>
```

### 에러 처리 모범 사례

```javascript
/**
 * API 호출 헬퍼 함수 - 에러 처리 포함
 */
async function callApi(functionName, params = {}) {
  try {
    // API 요청
    const url = new URL('/api.php', window.location.origin);
    url.searchParams.set('f', functionName);

    Object.keys(params).forEach(key => {
      url.searchParams.set(key, params[key]);
    });

    const response = await fetch(url);
    const text = await response.text();

    // 두 개의 JSON이 연속으로 올 경우 첫 번째 JSON만 파싱
    // (api.php 버그 대응)
    let result;
    try {
      result = JSON.parse(text);
    } catch (e) {
      // JSON 파싱 실패 시 첫 번째 JSON 객체만 추출
      const firstJson = text.split('}')[0] + '}';
      result = JSON.parse(firstJson);
    }

    // 에러 확인
    if (result.error_code) {
      throw new Error(`[${result.error_code}] ${result.error_message}`);
    }

    return result;
  } catch (error) {
    console.error('API 호출 실패:', error);
    throw error;
  }
}

// 사용 예제
try {
  const user = await callApi('getUserInfo', { user_id: 123 });
  console.log('사용자:', user);
} catch (error) {
  console.error('사용자 정보를 가져올 수 없습니다:', error.message);
}
```

---

## 보안 고려사항

**API 보안 규칙:**

1. **인증 확인**

   - 민감한 작업은 반드시 인증된 사용자만 수행 가능
   - Firebase Authentication 토큰 검증

2. **권한 검사**

   - 각 함수는 사용자 권한을 확인해야 함
   - 본인의 데이터만 수정 가능

3. **입력 검증**

   - 모든 입력값은 서버 측에서 검증
   - SQL 인젝션, XSS 공격 방지

4. **HTTPS 사용**

   - 프로덕션 환경에서는 반드시 HTTPS 사용
   - API 키 및 민감한 정보 암호화

5. **Rate Limiting**
   - API 호출 횟수 제한
   - DDoS 공격 방지

**보안 체크리스트:**

- [ ] 모든 API 요청은 HTTPS를 통해 전송
- [ ] 인증 토큰은 안전하게 저장 및 전송
- [ ] 민감한 작업은 추가 인증 필요
- [ ] 입력값은 서버 측에서 검증
- [ ] 에러 메시지에 민감한 정보 포함 금지

---

**참고 문서:**

- [코딩 가이드라인](coding-guideline.md)
- [데이터베이스 가이드](db.md)
- [번역 가이드](translation.md)
