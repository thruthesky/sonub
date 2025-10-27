# Sonub API 프로토콜 가이드

본 문서는 Sonub API의 동작 방식, 함수 호출 방법, 입출력 형식, 에러 처리 등 API 프로토콜에 대한 상세한 설명을 제공합니다.

## 목차

- [API 동작 방식](#api-동작-방식)
- [api.php 상세 동작 방식](#apiphp-상세-동작-방식)
  - [개요](#개요)
  - [핵심 코드 구조](#핵심-코드-구조)
  - [단계별 동작 설명](#단계별-동작-설명)
  - [실제 사용 예제](#실제-사용-예제)
  - [http_params() 함수](#http_params-함수)
  - [func 필드 자동 추가](#func-필드-자동-추가)
  - [API 설계의 장점](#api-설계의-장점)
  - [주의사항](#주의사항)
- [API 엔드포인트](#api-엔드포인트)
- [func() 헬퍼 함수 (권장)](#func-헬퍼-함수-권장)
  - [개요](#개요-1)
  - [함수 시그니처](#함수-시그니처)
  - [기본 사용법](#기본-사용법)
  - [실제 사용 예제](#실제-사용-예제-1)
  - [Vue.js에서 사용하기](#vuejs에서-사용하기)
  - [func() 함수 내부 동작](#func-함수-내부-동작)
  - [주의사항](#주의사항-1)
- [API 호출 예제](#api-호출-예제)
  - [기본 요청](#기본-요청)
  - [사용자 관련 API](#사용자-관련-api)
  - [게시글 관련 API](#게시글-관련-api)
- [에러 처리](#에러-처리)
  - [에러 응답 형식](#에러-응답-형식)
  - [에러 응답 유형](#에러-응답-유형)
  - [일반적인 에러 코드](#일반적인-에러-코드)
  - [에러 확인 방법](#에러-확인-방법)
  - [에러 처리 모범 사례](#에러-처리-모범-사례)

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

- URL 파라미터 `func`로 호출할 함수 이름을 지정
- 해당 함수가 동적으로 실행됨
- 모든 응답은 JSON 형식으로 리턴
- 예외가 발생하면 자동으로 에러 응답 생성

### 핵심 코드 구조

```php
<?php
const ROOT_DIR = __DIR__;
include_once ROOT_DIR . '/etc/includes.php';
header('Content-Type: application/json; charset=utf-8');

$func_name = http_params('func');
if ($func_name === null) {
    http_response_code(400);
    $error_response = [
        'error_code' => 'no-function-specified',
        'error_message' => 'Function name is required',
        'error_data' => [],
        'error_response_code' => 400
    ];
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
    exit;
}
if (!function_exists($func_name)) {
    http_response_code(400);
    $error_response = [
        'error_code' => 'function-not-exists',
        'error_message' => "Function '{$func_name}' does not exist",
        'error_data' => ['function' => $func_name],
        'error_response_code' => 400
    ];
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
    exit;
}
try {
    // 함수 호출
    $res = $func_name(http_params());

    // 리턴 타입 검증 및 변환
    // 단일 값(숫자, 문자열, 불리언)인 경우 ['data' => 값] 형태로 변환
    if (is_numeric($res) || is_string($res) || is_bool($res)) {
        $res = ['data' => $res];
    }

    // 객체를 배열로 변환 (Model 객체 지원)
    if (is_object($res)) {
        if (method_exists($res, 'toArray')) {
            $res = $res->toArray();
        } else {
            $res = get_object_vars($res);
        }
    }

    // 정상 응답 처리
    $res['func'] = $func_name;
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
} catch (ApiException $e) {
    // API 에러 처리 (error() 함수로 throw된 에러)
    http_response_code($e->getErrorResponseCode());
    $error_response = $e->toArray();
    $error_response['func'] = $func_name;
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    // 예상치 못한 예외 처리
    http_response_code(500);
    $error_response = [
        'error_code' => 'exception',
        'error_message' => $e->getMessage(),
        'error_data' => [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ],
        'error_response_code' => 500,
        'func' => $func_name
    ];
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
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
$func_name = http_params('func');
if ($func_name === null) {
    // 에러: 함수 이름이 지정되지 않음
    http_response_code(400);
    echo json_encode(error('no-function-specified'), JSON_UNESCAPED_UNICODE);
}
```

- `http_params('func')`: URL 파라미터 `func`에서 호출할 함수 이름을 가져옴
- 함수 이름이 없으면 400 에러 리턴

#### 3단계: 동적 함수 호출

```php
$res = $func_name(http_params());
```

**🔥 이 한 줄이 API의 핵심입니다!**

- `$func_name`: 함수 이름을 문자열로 가져옴 (예: `'app_version'`)
- `http_params()`: 모든 HTTP 파라미터를 배열로 가져옴
- PHP의 **동적 함수 호출** 기능을 사용하여 실제 함수 실행

**예제:**

```php
// URL: https://local.sonub.com/api.php?func=app_version
// 실제 실행: app_version(['func' => 'app_version'])

// URL: https://local.sonub.com/api.php?func=getUserInfo&user_id=123
// 실제 실행: getUserInfo(['func' => 'getUserInfo', 'user_id' => '123'])
```

#### 4단계: 응답 처리

```php
// 단일 값(숫자, 문자열, 불리언)인 경우 ['data' => 값] 형태로 변환
if (is_numeric($res) || is_string($res) || is_bool($res)) {
    $res = ['data' => $res];
}

// 객체를 배열로 변환 (Model 객체 지원)
if (is_object($res)) {
    if (method_exists($res, 'toArray')) {
        $res = $res->toArray();
    } else {
        $res = get_object_vars($res);
    }
}

// 성공 응답에 함수 이름 추가
$res['func'] = $func_name;
echo json_encode($res, JSON_UNESCAPED_UNICODE);
```

**중요한 특징:**

- 함수가 배열이나 객체를 리턴하면 정상 처리
- 단일 값(숫자, 문자열, 불리언)은 자동으로 `['data' => 값]` 형태로 변환
- Model 객체는 `toArray()` 메서드를 통해 배열로 변환
- **성공 응답에는 자동으로 `func` 필드 추가** - 어떤 함수가 실행되었는지 표시

#### 5단계: 예외 처리

```php
catch (ApiException $e) {
    // API 에러 처리 (error() 함수로 throw된 에러)
    http_response_code($e->getErrorResponseCode());
    $error_response = $e->toArray();
    $error_response['func'] = $func_name;
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    // 예상치 못한 예외 처리
    http_response_code(500);
    echo json_encode(error('exception', $e->getMessage(), ['trace' => $e->getTraceAsString()]), JSON_UNESCAPED_UNICODE);
}
```

- 함수 실행 중 발생한 모든 예외를 캐치
- `ApiException`: `error()` 함수로 throw된 예외 (정상적인 에러 처리)
- `Throwable`: 예상치 못한 예외 (함수 없음, 치명적 오류 등)
- 예외 메시지와 스택 트레이스를 포함한 에러 응답 리턴
- HTTP 상태 코드 설정 (ApiException: 지정된 코드, Throwable: 500)

### 실제 사용 예제

#### 예제 1: 정상 응답

**요청:**
```
GET https://local.sonub.com/api.php?func=app_version
```

**실행 과정:**
1. `http_params('func')` → `'app_version'`
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
GET https://local.sonub.com/api.php?func=wrongfunc
```

**실행 과정:**
1. `http_params('func')` → `'wrongfunc'`
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
    "error_response_code": 500
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
1. `http_params('func')` → `null` (파라미터 `func`가 없음)
2. 함수 이름 누락 에러 리턴

**응답:**
```json
{
    "error_code": "no-function-specified",
    "error_message": "Function name is required",
    "error_data": [],
    "error_response_code": 400
}
```

**HTTP 상태 코드:** 400

**설명:**

- URL에 `func` 파라미터가 없으면 이 에러가 발생합니다
- 함수 이름을 지정하지 않았다는 의미입니다

---

#### 예제 4: 파라미터가 있는 함수 호출

**요청:**
```
GET https://local.sonub.com/api.php?func=getUserInfo&user_id=123
```

**실행 과정:**
1. `http_params('func')` → `'getUserInfo'`
2. `http_params()` → `['func' => 'getUserInfo', 'user_id' => '123']`
3. `getUserInfo(['func' => 'getUserInfo', 'user_id' => '123'])` 실행
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
// ['func' => 'getUserInfo', 'user_id' => '123', 'name' => '홍길동']

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
$res['func'] = $func_name;
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

// 요청: /api.php?func=app_version
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
           throw error('invalid-user-id', '사용자 ID가 필요합니다');
       }
       // ... 나머지 로직
   }
   ```

3. **리턴 타입**: 함수는 배열, 객체 또는 단일 값(숫자, 문자열, 불리언)을 리턴할 수 있음
   ```php
   // ✅ 올바른 예 1: 배열 리턴
   function getUser() {
       return ['name' => '홍길동', 'email' => 'hong@example.com'];
   }

   // ✅ 올바른 예 2: Model 객체 리턴 (toArray() 메서드 필수)
   function getUserById($params) {
       $id = http_params('user_id');
       return get_user_by_id($id);  // UserModel 객체 리턴
   }

   // ✅ 올바른 예 3: 단일 값 리턴 (api.php가 자동 변환)
   function getUserCount() {
       return 42;  // api.php가 자동으로 ['data' => 42, 'func' => 'getUserCount']로 변환
   }

   // ✅ 올바른 예 4: 문자열 리턴 (api.php가 자동 변환)
   function getWelcomeMessage() {
       return 'Welcome!';  // api.php가 자동으로 ['data' => 'Welcome!', 'func' => 'getWelcomeMessage']로 변환
   }

   // ✅ 올바른 예 5: 불리언 리턴 (api.php가 자동 변환)
   function checkEmailExists($params) {
       $email = http_params('email');
       return true;  // api.php가 자동으로 ['data' => true, 'func' => 'checkEmailExists']로 변환
   }
   ```

4. **Model 객체 리턴 시 toArray() 메서드 필수**: Model 클래스는 반드시 toArray() 메서드를 구현해야 함

   **배경:**
   - API 함수가 UserModel, PostModel 등의 객체를 리턴하면 `api.php`가 자동으로 `toArray()` 메서드를 호출합니다
   - `toArray()` 메서드는 객체의 모든 데이터를 배열로 변환하여 JSON 인코딩이 가능하도록 합니다
   - `toArray()` 메서드가 없는 객체는 `get_object_vars()`로 public 프로퍼티만 배열로 변환됩니다

   **api.php의 자동 변환 로직:**
   ```php
   // 객체를 배열로 자동 변환
   if (is_object($res)) {
       if (method_exists($res, 'toArray')) {
           // Model 클래스: toArray() 메서드 호출
           $res = $res->toArray();
       } else {
           // 일반 객체: get_object_vars()로 public 프로퍼티만 배열 변환
           $res = get_object_vars($res);
       }
   }
   ```

   **Model 클래스 예제:**
   ```php
   // ✅ 올바른 예: toArray() 메서드 구현
   class UserModel {
       private int $idx;
       private string $email;
       private string $first_name;
       private string $last_name;
       private string $middle_name;
       private int $created_at;

       public function __construct(array $data) {
           $this->idx = $data['idx'] ?? 0;
           $this->email = $data['email'] ?? '';
           $this->first_name = $data['first_name'] ?? '';
           $this->last_name = $data['last_name'] ?? '';
           $this->middle_name = $data['middle_name'] ?? '';
           $this->created_at = $data['created_at'] ?? 0;
       }

       /**
        * 객체 데이터를 배열로 변환
        * API 응답을 위해 필수 메서드
        */
       public function toArray(): array {
           return [
               'idx' => $this->idx,
               'email' => $this->email,
               'first_name' => $this->first_name,
               'last_name' => $this->last_name,
               'middle_name' => $this->middle_name,
               'created_at' => $this->created_at
           ];
       }
   }
   ```

5. **함수 이름**: 공개 API로 사용할 함수 이름은 명확하고 일관성 있게 작성
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
GET/POST https://sonub.com/api.php?func=함수명
```

**요청 방식:**

1. **GET 파라미터:**
   ```
   GET https://sonub.com/api.php?func=getUserInfo&user_id=123
   ```

2. **POST 파라미터:**
   ```
   POST https://sonub.com/api.php?func=createPost
   Body: title=제목&content=내용
   ```

3. **JSON 바디:**
   ```
   POST https://sonub.com/api.php?func=createPost
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

## func() 헬퍼 함수 (권장)

**⭐️ Sonub에서 JavaScript로 API를 호출하는 가장 권장되는 방법입니다!**

### 개요

`func()` 함수는 `/js/app.js`에 정의된 API 호출 헬퍼 함수로, 모든 페이지에서 즉시 사용 가능합니다.

**🔥🔥🔥 최강력 핵심 개념: func() 함수는 PHP 함수를 직접 호출합니다 🔥🔥🔥**

func() 함수는 단순한 API 호출 함수가 아니라, **JavaScript에서 PHP 함수를 직접 호출하는 것과 동일**하게 작동합니다.

**핵심 원리:**

```javascript
// JavaScript 코드
const result = await func('list_posts', {
    category: 'discussion',
    limit: 10
});

// ↓ 실제로는 다음 PHP 함수가 실행됩니다
// PHP 코드
function list_posts($params) {
    $category = $params['category'];  // 'discussion'
    $limit = $params['limit'];        // 10

    // ... 게시글 목록 조회 로직 ...

    return [
        'posts' => $posts,
        'total' => $total
    ];
}

// ↓ PHP 함수의 리턴값이 JavaScript로 그대로 전달됩니다
// JavaScript에서 받은 result
{
    func: 'list_posts',  // API에서 자동 추가
    posts: [...],
    total: 100
}
```

**중요 규칙:**

1. **입력값 일치**: JavaScript에서 전달하는 파라미터는 PHP 함수가 받는 `$params` 배열의 키/값과 정확히 일치해야 합니다
2. **출력값 확인**: PHP 함수가 리턴하는 배열/객체 구조를 미리 확인하고 JavaScript에서 올바르게 사용해야 합니다
3. **PHP 함수 조회 필수**: func() 함수 사용 전 반드시 해당 PHP 함수의 정의를 찾아서 읽어야 합니다
4. **타입 주의**: PHP와 JavaScript 간 타입 변환(숫자, 문자열, null 등)에 유의해야 합니다

**예제: PHP 함수 정의 확인 → JavaScript 호출**

```php
// 📁 lib/post/post.functions.php
/**
 * 게시글 목록 조회
 * @param array $params - 파라미터 배열
 *   - string category: 카테고리 (선택)
 *   - int limit: 한 페이지당 개수 (기본값: 20)
 *   - int page: 페이지 번호 (기본값: 1)
 * @return array - 게시글 목록 및 메타 정보
 *   - array posts: PostModel 객체 배열
 *   - int total: 전체 게시글 수
 *   - int current_page: 현재 페이지
 */
function list_posts($params) {
    $category = http_params('category');
    $limit = http_params('limit') ?? 20;
    $page = http_params('page') ?? 1;

    // ... DB 조회 로직 ...

    return [
        'posts' => $posts,
        'total' => $total,
        'current_page' => $page
    ];
}
```

```javascript
// JavaScript: PHP 함수 정의를 확인한 후 호출
const result = await func('list_posts', {
    category: 'discussion',  // PHP의 http_params('category')로 전달됨
    limit: 10,               // PHP의 http_params('limit')로 전달됨
    page: 1                  // PHP의 http_params('page')로 전달됨
});

// PHP 함수의 리턴값과 동일한 구조
console.log(result.posts);        // PostModel 배열
console.log(result.total);        // 전체 개수
console.log(result.current_page); // 현재 페이지
```

**왜 func()를 사용해야 하나요?**

- ✅ **PHP 함수 직접 호출**: JavaScript에서 PHP 함수를 마치 JavaScript 함수처럼 호출 가능
- ✅ **자동 에러 처리**: 에러 발생 시 자동으로 사용자에게 알림 (옵션)
- ✅ **Firebase 인증 자동 처리**: 로그인이 필요한 API 호출 시 ID 토큰 자동 전송
- ✅ **일관된 호출 패턴**: 모든 API 호출이 동일한 방식으로 작동
- ✅ **간결한 코드**: Axios 설정 없이 함수 이름과 파라미터만 전달
- ✅ **에러 정보 자동 추출**: error_code, error_message를 자동으로 파싱
- ✅ **타입 안전성**: PHP 함수 정의를 확인하면 입력/출력 구조를 명확히 알 수 있음

### 함수 시그니처

```javascript
async function func(name, params = {})
```

**파라미터:**

- `name` (string, 필수): 호출할 PHP API 함수 이름
- `params` (object, 선택): 함수에 전달할 파라미터 객체
  - `auth` (boolean, 선택): true로 설정 시 Firebase ID 토큰 자동 전송 (기본값: false)
  - `alertOnError` (boolean, 선택): true로 설정 시 에러 발생 시 alert 표시 (기본값: true)
  - 그 외 모든 파라미터는 PHP 함수에 전달됨

**리턴값:**

- 성공 시: API 함수의 응답 데이터 (객체)
- 실패 시: Error 객체 throw (try-catch로 처리 필요)

### 기본 사용법

**1. 간단한 API 호출**

```javascript
// 사용자 정보 조회
const user = await func('get_user_info', { user_id: 123 });
console.log('사용자:', user);

// 프로필 업데이트
const result = await func('update_user_profile', {
    first_name: '길동',
    last_name: '홍',
    middle_name: '',
    gender: 'male',
    birthday: '1990-01-01'
});

if (result.success) {
    console.log('프로필 업데이트 완료');
}
```

**2. Firebase 인증이 필요한 API 호출**

```javascript
// Firebase 로그인 (ID 토큰 자동 전송)
await func('login_with_firebase', {
    firebase_uid: user.uid,
    auth: true,           // Firebase ID 토큰 자동 전송
    alertOnError: true    // 에러 시 alert 표시
});

// 게시글 작성 (로그인 필요)
await func('create_post', {
    title: '게시글 제목',
    content: '게시글 내용',
    auth: true            // 로그인한 사용자의 ID 토큰 전송
});
```

**3. 에러 처리**

```javascript
// 기본 에러 처리 (alertOnError: true가 기본값)
// 에러 발생 시 자동으로 alert 표시
const user = await func('get_user_info', { user_id: 999 });

// 에러 알림 비활성화
const response = await func('set_language', {
    language_code: 'ko',
    alertOnError: false   // 에러 시 alert 표시 안 함
});

// try-catch로 직접 에러 처리
try {
    const user = await func('get_user_info', { user_id: 999 });
    console.log('사용자:', user);
} catch (error) {
    console.error('에러 코드:', error.code);
    console.error('에러 메시지:', error.message);
    console.error('원본 에러:', error.originalError);
}
```

### 실제 사용 예제

**예제 1: 언어 선택**

```javascript
/**
 * 언어 선택 및 저장
 * @param {string} languageCode - 선택된 언어 코드 (en, ko, ja, zh)
 */
async selectLanguage(languageCode) {
    try {
        // API 호출하여 언어 선택 저장
        const response = await func('set_language', {
            language_code: languageCode,
            alertOnError: true
        });

        if (response.success) {
            // 현재 언어 표시 업데이트
            this.currentLanguage = languageCode;

            // 페이지 새로고침하여 언어 변경 적용
            window.location.reload();
        }
    } catch (error) {
        console.error('언어 선택 중 오류 발생:', error);
    }
}
```

**예제 2: 사용자 프로필 업데이트**

```javascript
async updateProfile() {
    // 생년월일 변환
    const birthday = this.form.birthday
        ? `${this.form.birthday.year}-${String(this.form.birthday.month).padStart(2, '0')}-${String(this.form.birthday.day).padStart(2, '0')}`
        : '';

    try {
        // 프로필 업데이트 API 호출
        const result = await func('update_user_profile', {
            first_name: this.form.firstName.trim(),
            last_name: this.form.lastName.trim(),
            middle_name: this.form.middleName.trim(),
            gender: this.form.gender,
            birthday: birthday,
            auth: true,           // Firebase 인증 토큰 전송
            alertOnError: true    // 에러 시 alert 표시
        });

        if (result.success) {
            alert('프로필이 업데이트되었습니다!');
            window.location.href = '/';
        }
    } catch (error) {
        console.error('프로필 업데이트 실패:', error);
    }
}
```

**예제 3: Firebase 로그인**

```javascript
// Firebase 인증 상태 변경 감지
firebase.auth().onAuthStateChanged(async (user) => {
    if (user) {
        try {
            // Firebase UID로 Sonub 로그인
            await func('login_with_firebase', {
                firebase_uid: user.uid,
                email: user.email,
                auth: true,
                alertOnError: true
            });

            console.log('로그인 성공:', user.email);
        } catch (error) {
            console.error('로그인 실패:', error);
        }
    } else {
        console.log('로그아웃 상태');
    }
});
```

**예제 4: 파일 업로드**

```javascript
async uploadFile(file) {
    const formData = new FormData();
    formData.append('userfile', file);

    try {
        // func() 함수 대신 axios 직접 사용 (FormData의 경우)
        const response = await axios.post('/api.php?func=file_upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.error_code) {
            throw new Error(response.data.error_message);
        }

        console.log('업로드 성공:', response.data.url);
        return response.data;
    } catch (error) {
        console.error('파일 업로드 실패:', error);
        throw error;
    }
}
```

### Vue.js에서 사용하기

```javascript
ready(() => {
    Vue.createApp({
        setup() {
            const user = Vue.ref(null);
            const loading = Vue.ref(false);
            const error = Vue.ref(null);

            // 사용자 정보 로드
            const loadUser = async (userId) => {
                loading.value = true;
                error.value = null;

                try {
                    // func() 함수로 API 호출
                    const data = await func('get_user_info', {
                        user_id: userId,
                        alertOnError: false  // Vue에서 직접 에러 처리
                    });

                    user.value = data;
                } catch (err) {
                    error.value = '사용자 정보를 가져올 수 없습니다.';
                    console.error(err);
                } finally {
                    loading.value = false;
                }
            };

            // 프로필 업데이트
            const updateProfile = async (firstName, lastName, middleName, gender, birthday) => {
                loading.value = true;
                error.value = null;

                try {
                    const result = await func('update_user_profile', {
                        first_name: firstName,
                        last_name: lastName,
                        middle_name: middleName,
                        gender: gender,
                        birthday: birthday,
                        auth: true,
                        alertOnError: false
                    });

                    if (result.success) {
                        alert('프로필이 업데이트되었습니다!');
                        return true;
                    }
                } catch (err) {
                    error.value = '프로필 업데이트에 실패했습니다.';
                    console.error(err);
                    return false;
                } finally {
                    loading.value = false;
                }
            };

            // 컴포넌트 마운트 시 실행
            Vue.onMounted(() => {
                loadUser(123);
            });

            return {
                user,
                loading,
                error,
                loadUser,
                updateProfile
            };
        }
    }).mount('#app');
});
```

### func() 함수 내부 동작

**소스 코드 (`/js/app.js`):**

```javascript
/**
 * API 함수 호출
 * @param {string} name - 호출할 함수 이름
 * @param {object} params - 파라미터 객체
 *   - auth: true - 현재 로그인한 사용자의 ID 토큰을 'idToken'에 포함
 *   - alertOnError: true - 오류 발생 시 alert()로 알림 (기본값: true)
 * @returns {Promise<object>} API 응답 데이터
 */
async function func(name, params = {}) {
    // 함수 이름을 params.func에 설정
    params.func = name;

    // alertOnError 기본값 설정 (기본값: true)
    const alertOnError = params.alertOnError !== undefined ? params.alertOnError : true;

    // Firebase 인증 토큰 추가
    if (params.auth) {
        params.idToken = await firebase.auth().currentUser.getIdToken(true);
        delete params.auth;  // auth 필드는 제거
    }

    try {
        // Axios로 API 호출
        const res = await axios.post('/api.php', params);

        // 에러 코드가 있으면 에러 throw
        if (res.data.error_code) {
            throw new Error(res.data.error_code + ': ' + res.data.error_message);
        }

        // 성공 시 응답 데이터 리턴
        return res.data;
    } catch (error) {
        // 에러 코드와 메시지 추출
        let errorCode = 'unknown-error';
        let errorMessage = error.message;

        if (error.response && error.response.data) {
            // 서버에서 반환한 에러 정보
            errorCode = error.response.data.error_code || errorCode;
            errorMessage = error.response.data.error_message || errorMessage;
        } else if (error.message) {
            // Error 객체에서 추출한 에러 메시지
            const match = error.message.match(/^([^:]+):\s*(.+)$/);
            if (match) {
                errorCode = match[1];
                errorMessage = match[2];
            }
        }

        // 콘솔에 에러 로그
        console.error(`Error occurred while calling ${name}:`, {
            errorCode,
            errorMessage,
            fullError: error
        });

        // alertOnError가 true일 때 사용자에게 에러 표시
        if (alertOnError) {
            alert(`Error: ${errorCode}\n${errorMessage}`);
        }

        // 에러 코드와 메시지를 포함하여 에러 던지기
        const customError = new Error(errorMessage);
        customError.code = errorCode;
        customError.originalError = error;
        throw customError;
    }
}
```

### 주의사항

1. **🔥 PHP 함수 정의 필수 확인**: func() 함수 사용 전 반드시 호출할 PHP 함수의 정의를 찾아서 읽어야 합니다
   - **입력 파라미터 확인**: PHP 함수가 기대하는 파라미터 이름과 타입을 정확히 파악
   - **출력 구조 확인**: PHP 함수가 리턴하는 배열/객체 구조를 미리 확인
   - **PHPDoc 참고**: 함수 문서화 주석을 읽고 입력/출력 형식을 이해
   - **예제**:
     ```javascript
     // ❌ 잘못된 방법: PHP 함수 확인 없이 호출
     const posts = await func('list_posts', { limit: 10 });
     console.log(posts.items);  // ← PHP 함수는 'posts' 키를 사용하는데 'items'를 참조

     // ✅ 올바른 방법: PHP 함수 정의를 확인한 후 호출
     // PHP: return ['posts' => $posts, 'total' => $total];
     const result = await func('list_posts', { limit: 10 });
     console.log(result.posts);   // ← 올바른 키 사용
     console.log(result.total);
     ```

2. **입력값과 출력값 일치**: JavaScript와 PHP 간 데이터 구조가 정확히 일치해야 합니다
   - **입력**: JavaScript 파라미터 → PHP `http_params()` 함수로 접근
   - **출력**: PHP 리턴값 → JavaScript 변수에 그대로 전달
   - **타입 변환 주의**: 숫자, 문자열, null, 배열 등 타입이 올바르게 변환되는지 확인

3. **func 필드 자동 설정**: `params.func`는 자동으로 설정되므로 직접 설정하지 마세요

4. **auth 필드 제거**: `params.auth`는 처리 후 자동으로 제거됩니다

5. **alertOnError 기본값**: 기본값이 `true`이므로 에러 시 alert가 자동으로 표시됩니다

6. **Firebase 로그인 필요**: `auth: true`를 사용하려면 Firebase에 로그인되어 있어야 합니다

7. **try-catch 권장**: 에러 처리를 직접 하려면 try-catch를 사용하세요

8. **개발 워크플로우**:
   ```
   1. PHP 함수 정의 찾기 (lib/**/*.php)
   2. PHPDoc 주석 읽고 입력/출력 확인
   3. JavaScript에서 func() 호출
   4. PHP 함수의 리턴 구조에 맞게 데이터 사용
   ```

---

## API 호출 예제

### 기본 요청

**cURL 예제:**

```bash
curl -X POST https://sonub.com/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "func": "get_user",
    "id": 123
  }'
```

**JavaScript (func() 함수 사용 - 권장):**

```javascript
// func() 함수로 API 호출 (권장 방법)
const user = await func('get_user', { id: 123 });
console.log(user);
```

**JavaScript (Fetch API 직접 사용):**

```javascript
// Fetch API를 직접 사용하는 방법 (특수한 경우에만 사용)
const response = await fetch("/api.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    func: "get_user",
    id: 123
  }),
});

const result = await response.json();

// 에러 확인
if (result.error_code) {
  console.error('에러:', result.error_message);
} else {
  console.log('사용자:', result);
}
```

### 사용자 관련 API

**사용자 정보 조회:**

```javascript
// 사용자 정보 가져오기
const user = await func('get_user', { id: 123 });
console.log(user.name);
console.log(user.email);
```

**사용자 프로필 업데이트:**

```javascript
// 프로필 업데이트 (인증 필요)
const result = await func('update_user_profile', {
  id: 123,
  name: '홍길동',
  auth: true  // Firebase 인증 토큰 자동 전송
});

if (result.success) {
  console.log('프로필 업데이트 성공');
}
```

### 게시글 관련 API

**게시글 목록 조회:**

```javascript
// 게시글 목록 가져오기
const result = await func('list_posts', {
  category: 'discussion',
  limit: 10,
  page: 1
});

console.log(result.posts);    // 게시글 배열
console.log(result.total);    // 전체 게시글 수
```

**게시글 작성:**

```javascript
// 새 게시글 작성 (인증 필요)
const post = await func('create_post', {
  category: 'discussion',
  title: '새로운 게시글',
  content: '게시글 내용입니다.',
  auth: true  // Firebase 인증 토큰 자동 전송
});

console.log('게시글 작성 완료:', post.id);
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
- `error_message`: 사용자에게 표시할 에러 메시지 (다국어 번역 지원)
- `error_data`: 추가 에러 정보를 담은 객체
- `error_response_code`: HTTP 응답 코드 (400, 401, 403, 404, 500 등)

**다국어 번역 지원:**

API는 `tr()` 함수를 사용하여 에러 메시지를 사용자의 언어로 자동 번역합니다.

```php
// 에러 메시지를 다국어로 제공
throw error('user-not-found', tr('user-not-found', '사용자를 찾을 수 없습니다.'));
```

클라이언트는 사용자의 현재 언어 설정에 따라 번역된 에러 메시지를 받게 됩니다:

```json
{
  "error_code": "user-not-found",
  "error_message": "User not found",  // 영어 사용자
  "error_data": {},
  "error_response_code": 404
}
```

```json
{
  "error_code": "user-not-found",
  "error_message": "사용자를 찾을 수 없습니다.",  // 한국어 사용자
  "error_data": {},
  "error_response_code": 404
}
```

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
  "error_message": "Function name is required",
  "error_data": [],
  "error_response_code": 400
}
```

**HTTP 상태 코드:** 400

---

#### 2. 함수 실행 에러 (error() 함수 사용)

**요청:**
```
GET https://sonub.com/api.php?func=app_error
```

**응답:**
```json
{
  "error_code": "app-error",
  "error_message": "에러 테스트용 간단한 API",
  "error_data": [],
  "error_response_code": 400,
  "func": "app_error"
}
```

**HTTP 상태 코드:** 400 (또는 error() 함수에서 지정한 코드)

---

#### 3. 예외 발생 에러 (함수 없음, 치명적 오류 등)

**요청:**
```
GET https://sonub.com/api.php?func=wrongfunc
```

**응답:**
```json
{
  "error_code": "exception",
  "error_message": "Call to undefined function wrongfunc()",
  "error_data": {
    "trace": "#0 {main}"
  },
  "error_response_code": 500
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
- `function-not-exists`: 함수가 존재하지 않음 (HTTP 400)
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
const response = await fetch('/api.php?func=getUserInfo&user_id=123');
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

**방법 1: func() 함수 사용 (권장)**

```javascript
// func() 함수는 자동으로 에러 처리를 제공합니다
try {
  // alertOnError: true (기본값) - 에러 시 자동 alert 표시
  const user = await func('get_user', { id: 123 });
  console.log('사용자:', user);
} catch (error) {
  console.error('에러 코드:', error.code);
  console.error('에러 메시지:', error.message);
}

// alertOnError: false - 에러를 직접 처리
try {
  const user = await func('get_user', {
    id: 123,
    alertOnError: false  // alert 표시 안 함
  });
  console.log('사용자:', user);
} catch (error) {
  // 커스텀 에러 처리
  if (error.code === 'user-not-found') {
    console.log('사용자를 찾을 수 없습니다. 새로 생성하시겠습니까?');
  } else {
    console.error('알 수 없는 에러:', error.message);
  }
}
```

**방법 2: Fetch API 직접 사용 (특수한 경우)**

`func()` 함수가 지원하지 않는 특수한 경우(FormData 업로드 등)에만 Fetch API를 직접 사용합니다.

```javascript
// 예제: 파일 업로드 (FormData 사용)
async function uploadFile(file) {
  const formData = new FormData();
  formData.append('func', 'file_upload');
  formData.append('userfile', file);

  try {
    const response = await fetch('/api.php', {
      method: 'POST',
      body: formData  // Content-Type 헤더는 자동 설정됨
    });

    const result = await response.json();

    // 에러 확인
    if (result.error_code) {
      throw new Error(`[${result.error_code}] ${result.error_message}`);
    }

    return result;
  } catch (error) {
    console.error('파일 업로드 실패:', error);
    throw error;
  }
}

// 일반적인 API 호출은 func() 함수 사용 (권장)
try {
  const user = await func('get_user', { id: 123 });
  console.log('사용자:', user);
} catch (error) {
  console.error('사용자 정보를 가져올 수 없습니다:', error.message);
}
```

**Vue.js에서 에러 처리:**

```javascript
Vue.createApp({
  setup() {
    const user = Vue.ref(null);
    const error = Vue.ref(null);
    const loading = Vue.ref(false);

    const loadUser = async (userId) => {
      loading.value = true;
      error.value = null;

      try {
        user.value = await func('get_user', {
          id: userId,
          alertOnError: false  // Vue에서 직접 에러 처리
        });
      } catch (err) {
        error.value = '사용자 정보를 가져올 수 없습니다.';
        console.error(err);
      } finally {
        loading.value = false;
      }
    };

    return { user, error, loading, loadUser };
  }
}).mount('#app');
```

---

**참고 문서:**

- [Sonub API 엔드포인트 목록](sonub-api-endpoints.md)
- [코딩 가이드라인](../../docs/coding-guideline.md)
- [데이터베이스 가이드](../../docs/database.md)
- [번역 가이드](../../docs/translation.md)
