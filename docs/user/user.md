# 사용자 관리 (User Management)

## 목차
- [개요](#개요)
- [사용자 테이블 구조](#사용자-테이블-구조)
- [사용자 함수](#사용자-함수)
  - [login_with_firebase](#login_with_firebase)
    - [세션 ID 쿠키 자동 설정](#세션-id-쿠키-자동-설정)
  - [create_user_record](#create_user_record)
    - [세션 ID 쿠키 자동 설정](#세션-id-쿠키-자동-설정-1)
  - [get_user](#get_user)
  - [list_users](#list_users)
    - [페이지네이션](#페이지네이션)
    - [필터링 옵션](#필터링-옵션)
    - [$_GET 직접 전달](#get-직접-전달)
- [친구 관리](#친구-관리)
  - [친구 추가 (request_friend)](#친구-추가-request_friend)
    - [JavaScript에서 친구 추가하기](#javascript에서-친구-추가하기)
- [사용 예제](#사용-예제)
- [테스트](#테스트)

## 개요

Sonub의 사용자 관리 시스템은 Firebase Authentication과 MariaDB를 함께 사용합니다.

- **Firebase Authentication**: 사용자 인증 (로그인/로그아웃)
- **MariaDB `users` 테이블**: 사용자 프로필 정보 저장

### 사용자 생성 흐름

1. 사용자가 Firebase로 로그인
2. Firebase UID를 받아옴
3. `create_user_record()` 함수로 `users` 테이블에 레코드 생성
4. 사용자 정보 저장 및 관리

## 사용자 테이블 구조

### users 테이블

| 필드 | 타입 | 설명 | 제약 조건 |
|------|------|------|-----------|
| `id` | int(10) unsigned | 사용자 고유 ID | PRIMARY KEY, AUTO_INCREMENT |
| `firebase_uid` | varchar(128) | Firebase UID | UNIQUE, NOT NULL |
| `first_name` | varchar(64) | 이름 | NOT NULL |
| `last_name` | varchar(64) | 성 | NOT NULL |
| `middle_name` | varchar(64) | 중간 이름 | NULL |
| `created_at` | int(10) unsigned | 생성 시각 (timestamp) | NOT NULL |
| `updated_at` | int(10) unsigned | 수정 시각 (timestamp) | NOT NULL, DEFAULT 0 |
| `birthday` | int(10) unsigned | 생년월일 (timestamp) | NOT NULL, DEFAULT 0 |
| `gender` | char(1) | 성별 ('M' 또는 'F') | NOT NULL |

## 사용자 함수

### login_with_firebase

Firebase 인증 후 **사용자 생성 또는 기존 사용자 로그인**을 처리하는 함수입니다.

이 함수는 사용자 관리의 가장 중요한 함수로, Firebase 로그인 직후 즉시 호출되어야 합니다:
- **기존 사용자**: 세션 쿠키를 설정하고 사용자 정보를 반환합니다
- **신규 사용자**: users 테이블에 새 레코드를 생성하고, 세션 쿠키를 설정한 후 사용자 정보를 반환합니다

**파일 위치**: `lib/user/user.functions.php`

**🔥 중요**: 이 함수는 사용자 생성 시 **자동으로 세션 ID 쿠키를 설정**합니다.

#### 함수 흐름

1. **Firebase UID 검증**: 필수 파라미터 `firebase_uid` 확인
2. **전화번호 검증**: 필수 파라미터 `phone_number` 확인
3. **기존 사용자 조회**: Firebase UID로 users 테이블 검색
4. **기존 사용자 존재**:
   - 세션 쿠키 설정 (이미 생성되어 있음)
   - 사용자 정보 반환
5. **신규 사용자**:
   - users 테이블에 새 레코드 생성
   - 모든 파라미터 (phone_number, first_name, last_name, birthday 등) 저장
   - 세션 쿠키 설정
   - 생성된 사용자 정보 반환

#### 세션 ID 쿠키 자동 설정

사용자 생성 또는 기존 사용자 로그인 시 다음과 같은 세션 쿠키가 자동으로 설정됩니다:

| 항목 | 값 |
|------|--------|
| **쿠키 이름** | `sonub_session_id` |
| **유효기간** | 1년 (365일) |
| **경로** | `/` (전체 사이트) |
| **형식** | `{user_id}-{firebase_uid}-{hash}` |
| **예시** | `1-abc123xyz-e5d8f2a1b3c4...` |

이 쿠키는 서버에서 사용자 인증 상태를 유지하는 데 사용됩니다. 클라이언트는 별도로 쿠키를 설정할 필요가 없습니다.

#### 파라미터

| 파라미터 | 타입 | 필수 | 기본값 | 설명 |
|----------|------|------|--------|------|
| `firebase_uid` | string | ✅ | - | Firebase 로그인에서 받은 UID |
| `phone_number` | string | ✅ | - | 사용자 전화번호 |
| `first_name` | string | ❌ | '' | 사용자 이름 |
| `last_name` | string | ❌ | '' | 사용자 성 |
| `middle_name` | string | ❌ | '' | 중간 이름 |
| `birthday` | int | ❌ | 0 | 생년월일 (Unix timestamp) |
| `gender` | string | ❌ | '' | 성별 ('M' 또는 'F') |
| `photo_url` | string | ❌ | '' | 프로필 사진 URL |

#### 반환값

**성공**: 사용자 정보 배열
- `id` (int): 사용자 ID
- `firebase_uid` (string): Firebase UID
- `phone_number` (string): 사용자 전화번호
- `first_name` (string): 사용자 이름
- `last_name` (string): 사용자 성
- `middle_name` (string): 중간 이름
- `created_at` (int): 생성일 (Unix timestamp)
- `updated_at` (int): 수정일 (Unix timestamp)
- `birthday` (int): 생년월일 (Unix timestamp)
- `gender` (string): 성별
- `photo_url` (string): 프로필 사진 URL

**실패**: 에러 배열
- `error_code` (string): 에러 코드
- `error_message` (string): 에러 메시지

#### 에러 코드

| 에러 코드 | 설명 |
|-----------|------|
| `input-firebase-uid-empty` | firebase_uid 파라미터가 누락됨 |
| `input-phone-number-empty` | phone_number 파라미터가 누락됨 |
| `phone-number-mismatch` | 기존 사용자의 전화번호와 요청한 전화번호가 일치하지 않음 (보안상 에러) |

#### API 호출 예제

```bash
# Firebase 로그인 후 사용자 생성/조회
curl -X POST https://local.sonub.com/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "func": "login_with_firebase",
    "firebase_uid": "abc123xyz",
    "phone_number": "010-1234-5678",
    "first_name": "길동",
    "last_name": "홍",
    "middle_name": "",
    "birthday": 631152000,
    "gender": "M"
  }'
```

#### 응답 예제

**성공 응답 (신규 사용자 생성)** (HTTP 200):
```json
{
  "id": 1,
  "firebase_uid": "abc123xyz",
  "phone_number": "010-1234-5678",
  "first_name": "길동",
  "last_name": "홍",
  "middle_name": "",
  "created_at": 1759646876,
  "updated_at": 1759646876,
  "birthday": 631152000,
  "gender": "M",
  "photo_url": "",
  "func": "login_with_firebase"
}
```

**응답 헤더 (세션 쿠키 자동 설정)**:
```
Set-Cookie: sonub_session_id=1-abc123xyz-e5d8f2a1b3c4...; Max-Age=31536000; Path=/
```

**에러 응답 (Firebase UID 누락)** (HTTP 400):
```json
{
  "error_code": "input-firebase-uid-empty",
  "error_message": "firebase_uid 파라미터가 비어있습니다.",
  "func": "login_with_firebase"
}
```

**에러 응답 (전화번호 누락)** (HTTP 400):
```json
{
  "error_code": "input-phone-number-empty",
  "error_message": "phone_number 파라미터가 비어있습니다.",
  "func": "login_with_firebase"
}
```

**에러 응답 (전화번호 불일치)** (HTTP 400):
```json
{
  "error_code": "phone-number-mismatch",
  "error_message": "전화번호가 일치하지 않습니다. Firebase UID와 일치하는 기존 사용자의 전화번호와 요청한 전화번호가 다릅니다.",
  "func": "login_with_firebase"
}
```

#### PHP 직접 호출 예제

```php
// Firebase 로그인 후 API 호출
$user = login_with_firebase([
    'firebase_uid' => 'abc123xyz',
    'phone_number' => '010-1234-5678',
    'first_name' => '길동',
    'last_name' => '홍',
    'gender' => 'M'
]);

// 세션 쿠키가 자동으로 설정됨
echo "사용자 ID: " . $user['id'];  // 1
echo "이름: " . $user['first_name'] . " " . $user['last_name'];  // 길동 홍
echo "전화: " . $user['phone_number'];  // 010-1234-5678

// 다음 요청부터 login() 함수로 로그인 정보 확인 가능
$logged_user = login();
echo $logged_user->first_name;  // 길동
echo $logged_user->phone_number;  // 010-1234-5678
```

#### JavaScript에서 사용 예제

```javascript
// Firebase 로그인 후 API 호출
firebase.auth().onAuthStateChanged(async (user) => {
    if (user) {
        try {
            // 서버에 사용자 생성/조회 요청
            const result = await func('login_with_firebase', {
                firebase_uid: user.uid,
                first_name: user.displayName?.split(' ')[0] || '',
                last_name: user.displayName?.split(' ')[1] || '',
                birthday: 0,
                gender: ''
            });

            console.log('로그인 성공:', result.id);
            console.log('사용자 이름:', result.first_name, result.last_name);

            // 세션 쿠키가 자동으로 설정됨 - 추가 작업 불필요
            // 이후 모든 요청에 자동으로 세션 쿠키가 포함됨

        } catch (error) {
            console.error('로그인 실패:', error);
        }
    }
});
```

#### 주의사항

1. **Firebase 로그인 필수**: `login_with_firebase`는 Firebase 인증 후 즉시 호출되어야 합니다
2. **firebase_uid 필수**: `firebase_uid` 파라미터는 반드시 포함되어야 합니다
3. **phone_number 필수 및 보안**: `phone_number` 파라미터는 반드시 포함되어야 합니다
   - ⚠️ **중요**: `phone_number`는 **비밀번호처럼 매우 민감한 정보**입니다
   - 🔒 **절대로 타 회원에게 노출되어서는 안 됩니다**
   - API 응답에서 phone_number를 전달할 때는 로그인한 사용자 자신의 정보만 포함되어야 합니다
   - 타 사용자의 프로필, 목록, 검색 결과 등에서 절대로 phone_number를 노출하지 마세요
   - 기존 사용자의 phone_number가 변경되면 에러 (`phone-number-mismatch`)가 발생하여 보안을 유지합니다
4. **세션 쿠키 자동 설정**: 함수 호출 후 자동으로 세션 쿠키가 설정되므로, 클라이언트에서 별도의 쿠키 설정 작업이 필요 없습니다
5. **중복 사용자 방지**: Firebase UID가 이미 존재하면 phone_number 일치 여부를 확인합니다 (일치하지 않으면 에러)
6. **선택 파라미터**: `first_name`, `last_name` 등은 선택 파라미터이므로, Firebase 로그인 정보가 불충분한 경우 빈 값으로 전달해도 됩니다

#### phone_number 기록 및 변경 불가 규칙

**🔥 최강력 규칙**: `login_with_firebase` 함수로 최초 로그인 시 기록된 `phone_number`는 **절대로 변경될 수 없습니다**.

##### 규칙 설명

- **최초 기록**: 사용자가 처음 Firebase로 로그인하여 `login_with_firebase`를 호출할 때, 전달된 `phone_number`가 `users` 테이블에 저장됩니다
- **불변 정보**: 한번 저장된 `phone_number`는 웹사이트의 프로필 수정, 계정 설정 등에서 절대로 변경될 수 없습니다
- **보안**: 이 규칙은 **계정 탈취 방지**를 위한 중요한 보안 정책입니다
- **재로그인**: 같은 `firebase_uid`로 재로그인할 때는 **동일한 `phone_number`를 전달해야 합니다** (다를 경우 `phone-number-mismatch` 에러 발생)

##### 예시: Firebase와 phone_number의 관계

```
사용자: 홍길동
├─ Firebase UID: abc123xyz (Firebase에서 관리)
└─ phone_number: 010-1234-5678 (login_with_firebase에서 최초 저장, 절대 변경 불가)

홍길동이 로그인할 때마다:
1. Firebase 인증 → firebase_uid 받음 (abc123xyz)
2. phone_number 전달: 010-1234-5678
3. 일치하므로 로그인 성공

만약 phone_number를 다르게 전달하면:
1. Firebase 인증 → firebase_uid 받음 (abc123xyz)
2. phone_number 전달: 010-9999-9999 (다른 번호!)
3. ❌ phone-number-mismatch 에러 발생
```

##### Email/Password 로그인의 경우

**email/password로 기존 사용자를 생성했거나 테스트하는 경우**, `phone_number` 필드가 없을 수 있습니다.

이 경우 다음과 같이 처리하세요:

1. **임의로 phone_number 생성**:
   ```bash
   # 예: '12345a,*'를 phone_number로 설정
   ```

2. **데이터베이스 직접 업데이트**:
   ```sql
   -- 테스트용 사용자에 phone_number 설정
   UPDATE users
   SET phone_number = '12345a,*'
   WHERE firebase_uid = 'banana';
   ```

3. **이후 로그인 시 phone_number 전달**:
   ```bash
   # API 호출 시 위에서 설정한 phone_number 사용
   curl "https://local.sonub.com/api.php?func=login_with_firebase&firebase_uid=banana&phone_number=12345a,*"
   ```

##### 테스트 시나리오

**테스트 사용자 설정:**

```bash
# 1단계: email/password로 사용자 생성 (phone_number 없음)
# UI에서 회원가입하거나 직접 INSERT

# 2단계: 임의의 phone_number 설정
mysql -u root sonub << EOF
UPDATE users SET phone_number = '12345a,*' WHERE firebase_uid = 'banana';
EOF

# 3단계: 테스트 로그인
curl "https://local.sonub.com/api.php?func=login_with_firebase&firebase_uid=banana&phone_number=12345a,*"

# 응답: 세션 쿠키와 함께 사용자 정보 반환
```

**Python/JavaScript 테스트:**

```javascript
// JavaScript에서 테스트
const result = await func('login_with_firebase', {
    firebase_uid: 'banana',
    phone_number: '12345a,*',  // 데이터베이스에 설정한 값
    first_name: '홍',
    last_name: '길동'
});
```

##### 주의사항

1. **🔥 절대 금지**: 기존 사용자의 `phone_number` 필드를 UI를 통해 수정하는 기능을 추가하지 마세요
2. **🔑 보안 필수**: `phone_number`는 **비밀번호처럼 취급**되어야 합니다
3. **📝 테스트용만**: email/password 사용자에 phone_number를 수동 설정하는 것은 **테스트 목적에만** 사용하세요
4. **✅ Firebase 권장**: 실제 사용자는 Firebase 로그인 시 `phone_number`를 함께 전달하는 방식을 권장합니다

#### 실제 사용 흐름

```
사용자 -> Firebase 로그인 -> 클라이언트에 Firebase ID Token 전달
                          -> API 호출: login_with_firebase(firebase_uid)
                          -> 서버에서 사용자 생성/조회
                          -> 세션 쿠키 자동 설정
                          -> 클라이언트에 사용자 정보 반환
                          -> 이후 모든 요청에 세션 쿠키 포함
```

---

### create_user_record

⚠️ **주의**: 이 함수는 **더 이상 권장되지 않습니다 (deprecated)**. 대신 [`login_with_firebase`](#login_with_firebase) 함수를 사용하세요.

사용자 레코드를 생성합니다. Firebase에 로그인했지만 `users` 테이블에 레코드가 없는 경우 호출합니다.

**참고**: `login_with_firebase` 함수가 `create_user_record`의 역할을 모두 포함하고 더 나은 기능을 제공하므로, 새로운 코드에서는 반드시 `login_with_firebase`를 사용하세요.

**🔥 중요**: 이 함수는 사용자 레코드 생성 시 **자동으로 세션 ID 쿠키를 설정**합니다.

**파일 위치**: `lib/user/user.crud.php`

#### 세션 ID 쿠키 자동 설정

사용자 레코드가 성공적으로 생성되면 다음과 같은 세션 쿠키가 자동으로 설정됩니다:

| 항목 | 값 |
|------|-----|
| **쿠키 이름** | `sonub_session_id` |
| **유효기간** | 1년 (365일) |
| **경로** | `/` (전체 사이트) |
| **형식** | `{user_id}-{firebase_uid}-{hash}` |
| **예시** | `1-abc123xyz-e5d8f2a1b3c4...` |

이 쿠키는 서버에서 사용자 인증 상태를 유지하는 데 사용됩니다. 클라이언트는 별도로 쿠키를 설정할 필요가 없습니다.

#### 파라미터

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `firebase_uid` | string | ✅ | Firebase UID |
| `first_name` | string | ❌ | 이름 |
| `last_name` | string | ❌ | 성 |
| `middle_name` | string | ❌ | 중간 이름 |
| `birthday` | int | ❌ | 생년월일 (Unix timestamp) |
| `gender` | string | ❌ | 성별 ('M' 또는 'F') |

#### 반환값

- **성공**: 생성된 사용자 정보 배열
- **실패**: 에러 배열 (`error_code`, `error_message`, `error_data`)

#### 에러 코드

| 에러 코드 | 설명 |
|-----------|------|
| `firebase-uid-required` | firebase_uid 파라미터가 누락됨 |
| `user-already-exists` | 이미 존재하는 사용자 (firebase_uid 중복) |

#### API 호출 예제

```bash
# 필수 정보만 포함
curl -X POST https://local.sonub.com/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "func": "create_user_record",
    "firebase_uid": "abc123xyz"
  }'

# 전체 정보 포함
curl -X POST https://local.sonub.com/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "func": "create_user_record",
    "firebase_uid": "abc123xyz",
    "first_name": "길동",
    "last_name": "홍",
    "middle_name": "",
    "birthday": 631152000,
    "gender": "M"
  }'
```

#### 응답 예제

**성공 응답** (HTTP 200):
```json
{
  "id": 1,
  "firebase_uid": "abc123xyz",
  "first_name": "길동",
  "last_name": "홍",
  "middle_name": "",
  "created_at": 1759646876,
  "updated_at": 1759646876,
  "birthday": 631152000,
  "gender": "M",
  "func": "create_user_record"
}
```

**응답 헤더** (쿠키 자동 설정):
```
Set-Cookie: sonub_session_id=1-abc123xyz-e5d8f2a1b3c4...; Max-Age=31536000; Path=/
```

**에러 응답** (HTTP 400):
```json
{
  "error_code": "user-already-exists",
  "error_message": "이미 존재하는 사용자입니다.",
  "error_data": {
    "firebase_uid": "abc123xyz"
  },
  "error_response_code": 400,
  "func": "create_user_record"
}
```

#### PHP 직접 호출 예제

```php
// 최소 정보로 사용자 생성
$user = create_user_record([
    'firebase_uid' => 'abc123xyz'
]);

// 전체 정보로 사용자 생성
$user = create_user_record([
    'firebase_uid' => 'abc123xyz',
    'first_name' => '길동',
    'last_name' => '홍',
    'middle_name' => '',
    'birthday' => strtotime('1990-01-01'),
    'gender' => 'M'
]);

// 에러 처리
if (isset($user['error_code'])) {
    echo "에러: " . $user['error_message'];
} else {
    echo "사용자 ID: " . $user['id'];
}
```

### get_user

사용자 ID로 사용자 정보를 조회합니다.

**파일 위치**: `lib/user/user.crud.php`

#### 파라미터

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `id` | int | ✅ | 사용자 ID |

#### 반환값

- **성공**: 사용자 정보 배열
- **실패**: 에러 배열 (`error_code`, `error_message`, `error_data`)

#### 에러 코드

| 에러 코드 | 설명 |
|-----------|------|
| `input-id-empty` | id 파라미터가 누락되거나 0임 |
| `user-not-found` | 해당 ID의 사용자를 찾을 수 없음 |

#### API 호출 예제

```bash
curl -X GET "https://local.sonub.com/api.php?f=get_user&id=1"
```

#### 응답 예제

```json
{
  "id": 1,
  "firebase_uid": "abc123xyz",
  "first_name": "길동",
  "last_name": "홍",
  "middle_name": "",
  "created_at": 1759646876,
  "updated_at": 1759646876,
  "birthday": 631152000,
  "gender": "M",
  "func": "get_user"
}
```

#### PHP 직접 호출 예제

```php
$user = get_user(['id' => 1]);

if (isset($user['error_code'])) {
    echo "에러: " . $user['error_message'];
} else {
    echo "사용자 이름: " . $user['first_name'] . " " . $user['last_name'];
}
```

### list_users

사용자 목록을 조회합니다. 페이지네이션과 다양한 필터링 옵션을 지원합니다.

**🔥 중요**: 이 함수는 **$_GET을 직접 전달**할 수 있도록 설계되어 매우 간편하게 사용할 수 있습니다.

**파일 위치**: `lib/user/user.crud.php`

#### 파라미터

| 파라미터 | 타입 | 필수 | 기본값 | 설명 |
|----------|------|------|--------|------|
| `page` | int | ❌ | 1 | 페이지 번호 (1부터 시작) |
| `per_page` | int | ❌ | 10 | 페이지당 사용자 수 (최소 1, 최대 100) |
| `gender` | string | ❌ | '' | 성별 필터 ('M' 또는 'F') |
| `age_start` | int | ❌ | null | 시작 나이 (예: 24) |
| `age_end` | int | ❌ | null | 끝 나이 (예: 32) |
| `name` | string | ❌ | '' | 이름 검색 (LIKE 'name%' 방식) |

#### 반환값

```php
[
    'page' => 1,              // 현재 페이지 번호
    'per_page' => 10,         // 페이지당 항목 수
    'total' => 145,           // 전체 사용자 수 (필터링 적용 후)
    'total_pages' => 15,      // 전체 페이지 수
    'users' => [              // 사용자 배열
        [
            'id' => 1,
            'firebase_uid' => 'abc123',
            'first_name' => '길동',
            'last_name' => '홍',
            'middle_name' => '',
            'created_at' => 1759646876,
            'updated_at' => 1759646876,
            'birthday' => 631152000,
            'gender' => 'M',
            'photo_url' => '/uploads/...'
        ],
        // ... 더 많은 사용자
    ]
]
```

#### 페이지네이션

페이지네이션은 `page`와 `per_page` 파라미터로 제어합니다.

**기본 페이지네이션 예제:**

```php
// 1페이지 조회 (10명)
$result = list_users(['page' => 1, 'per_page' => 10]);

// 2페이지 조회 (10명)
$result = list_users(['page' => 2, 'per_page' => 10]);

// 페이지당 20명씩 조회
$result = list_users(['page' => 1, 'per_page' => 20]);
```

**페이지네이션 정보 활용:**

```php
$result = list_users(['page' => 1, 'per_page' => 10]);

echo "현재 페이지: {$result['page']}\n";
echo "전체 사용자 수: {$result['total']}\n";
echo "전체 페이지 수: {$result['total_pages']}\n";
echo "이번 페이지 사용자 수: " . count($result['users']) . "\n";

// 다음 페이지 존재 여부
$has_next = $result['page'] < $result['total_pages'];
echo "다음 페이지: " . ($has_next ? '있음' : '없음') . "\n";
```

#### 필터링 옵션

##### 1. 성별 필터링

```php
// 남성만 조회
$males = list_users(['gender' => 'M', 'page' => 1]);

// 여성만 조회
$females = list_users(['gender' => 'F', 'page' => 1]);
```

##### 2. 나이 범위 필터링

나이는 `birthday` 필드를 기반으로 계산됩니다.

```php
// 24세 ~ 32세 사용자 조회
$result = list_users([
    'age_start' => 24,
    'age_end' => 32,
    'page' => 1
]);

// 30세 이상 사용자 조회
$result = list_users([
    'age_start' => 30,
    'page' => 1
]);

// 40세 이하 사용자 조회
$result = list_users([
    'age_end' => 40,
    'page' => 1
]);
```

**나이 계산 방식:**

- 현재 연도 - 출생 연도 = 나이
- `age_start => 24, age_end => 32`는 SQL에서 `YEAR(FROM_UNIXTIME(birthday)) BETWEEN 1993 AND 2001`로 변환됩니다 (2025년 기준)

##### 3. 이름 검색

이름은 **접두어 검색** 방식 (`LIKE 'name%'`)으로 동작합니다.

```php
// '김'으로 시작하는 사용자 조회
$result = list_users(['name' => '김', 'page' => 1]);
// 결과: 김철수, 김영희, 김민수 등

// '홍길'로 시작하는 사용자 조회
$result = list_users(['name' => '홍길', 'page' => 1]);
// 결과: 홍길동, 홍길순 등
```

**중요**: 부분 검색이 아닌 **접두어 검색**입니다.
- ✅ '김철수'는 'name=김'으로 검색 가능
- ❌ '김철수'는 'name=철수'로 검색 불가능

##### 4. 복합 필터링

여러 필터를 동시에 사용할 수 있습니다.

```php
// 여성 + 25~35세 + 이름 '이'로 시작
$result = list_users([
    'gender' => 'F',
    'age_start' => 25,
    'age_end' => 35,
    'name' => '이',
    'page' => 1,
    'per_page' => 20
]);

// 남성 + 30세 이상
$result = list_users([
    'gender' => 'M',
    'age_start' => 30,
    'page' => 1
]);
```

#### $_GET 직접 전달

`list_users()` 함수는 **$_GET을 직접 전달**할 수 있도록 설계되어 있어 매우 간편합니다.

**PHP 페이지에서 사용:**

```php
<?php
// page/friend/find-friend.php

// $_GET을 직접 전달하고 per_page만 지정
$result = list_users(array_merge($_GET, ['per_page' => 10]));

// 결과 사용
$users = $result['users'];
$total_count = $result['total'];
$total_pages = $result['total_pages'];
$page = $result['page'];
?>

<!-- HTML에서 사용 -->
<div class="container">
    <h1>사용자 목록 (전체 <?= number_format($total_count) ?>명)</h1>

    <?php foreach ($users as $user_data): ?>
        <?php $user = new UserModel($user_data); ?>
        <div class="user-card">
            <h3><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></h3>
            <p>성별: <?= $user->gender === 'M' ? '남성' : '여성' ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

**URL 쿼리 파라미터 예제:**

```
# 기본 목록 (1페이지)
https://local.sonub.com/?page=friend/find-friend

# 2페이지
https://local.sonub.com/?page=friend/find-friend&page=2

# 여성만 필터링
https://local.sonub.com/?page=friend/find-friend&gender=F

# 25~35세 사용자
https://local.sonub.com/?page=friend/find-friend&age_start=25&age_end=35

# 이름 검색
https://local.sonub.com/?page=friend/find-friend&name=김

# 복합 필터링
https://local.sonub.com/?page=friend/find-friend&gender=F&age_start=25&age_end=35&name=이
```

**자동 파라미터 처리:**

`list_users()` 함수는 다음을 자동으로 처리합니다:

- ✅ 빈 문자열 (`''`) 처리
- ✅ `null` 값 처리
- ✅ 숫자 형태 문자열 변환 (`'25'` → `25`)
- ✅ 페이지 범위 검증 (`page >= 1`)
- ✅ per_page 범위 검증 (`1 <= per_page <= 100`)

따라서 별도의 입력값 검증이 **불필요**합니다.

#### API 호출 예제

```bash
# 기본 목록 조회
curl "https://local.sonub.com/api.php?f=list_users&page=1&per_page=10"

# 성별 필터링
curl "https://local.sonub.com/api.php?f=list_users&gender=F&page=1"

# 나이 범위 필터링
curl "https://local.sonub.com/api.php?f=list_users&age_start=24&age_end=32&page=1"

# 이름 검색
curl "https://local.sonub.com/api.php?f=list_users&name=김&page=1"

# 복합 필터링
curl "https://local.sonub.com/api.php?f=list_users&gender=F&age_start=25&age_end=35&name=이&page=1"
```

#### 응답 예제

```json
{
  "page": 1,
  "per_page": 10,
  "total": 145,
  "total_pages": 15,
  "users": [
    {
      "id": 1,
      "firebase_uid": "abc123xyz",
      "first_name": "길동",
      "last_name": "홍",
      "middle_name": "",
      "created_at": 1759646876,
      "updated_at": 1759646876,
      "birthday": 631152000,
      "gender": "M",
      "photo_url": "/uploads/profile/123.jpg"
    },
    {
      "id": 2,
      "firebase_uid": "def456uvw",
      "first_name": "영희",
      "last_name": "김",
      "middle_name": "",
      "created_at": 1759646900,
      "updated_at": 1759646900,
      "birthday": 725846400,
      "gender": "F",
      "photo_url": ""
    }
    // ... 8명 더
  ],
  "func": "list_users"
}
```

#### 실전 사용 예제

##### 예제 1: 친구 찾기 페이지

```php
<?php
// page/friend/find-friend.php

// $_GET을 그대로 전달 (매우 간편!)
$result = list_users(array_merge($_GET, ['per_page' => 10]));

$users = $result['users'];
$total_count = $result['total'];
$total_pages = $result['total_pages'];
$page = $result['page'];

// 화면 표시용 검색 파라미터
$gender = $_GET['gender'] ?? '';
$age_start = $_GET['age_start'] ?? '';
$age_end = $_GET['age_end'] ?? '';
$name = $_GET['name'] ?? '';
?>

<div class="container my-5">
    <h1 class="mb-4">친구 찾기</h1>

    <!-- 검색 필터 -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="">
                <input type="hidden" name="page" value="friend/find-friend">

                <div class="row g-2">
                    <div class="col-md-3">
                        <select class="form-select" name="gender">
                            <option value="">전체</option>
                            <option value="M" <?= $gender === 'M' ? 'selected' : '' ?>>남성</option>
                            <option value="F" <?= $gender === 'F' ? 'selected' : '' ?>>여성</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="number" class="form-control" name="age_start"
                               placeholder="시작 나이" value="<?= $age_start ?>">
                    </div>

                    <div class="col-md-3">
                        <input type="number" class="form-control" name="age_end"
                               placeholder="끝 나이" value="<?= $age_end ?>">
                    </div>

                    <div class="col-md-3">
                        <input type="text" class="form-control" name="name"
                               placeholder="이름" value="<?= htmlspecialchars($name) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-2">검색</button>
            </form>
        </div>
    </div>

    <!-- 검색 결과 -->
    <p class="text-muted">전체 <?= number_format($total_count) ?>명</p>

    <div class="row g-3">
        <?php foreach ($users as $user_data): ?>
            <?php $user = new UserModel($user_data); ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></h5>
                        <p class="text-muted">
                            <?= $user->gender === 'M' ? '남성' : '여성' ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 페이지네이션 -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=friend/find-friend&<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
```

##### 예제 2: JavaScript로 사용자 목록 로드

```javascript
// page/friend/friend-list.js

const { createApp } = Vue;

createApp({
  data() {
    return {
      users: [],
      total: 0,
      total_pages: 0,
      page: 1,
      per_page: 10,
      loading: false,

      // 필터
      gender: '',
      age_start: '',
      age_end: '',
      name: ''
    };
  },
  methods: {
    async loadUsers() {
      try {
        this.loading = true;

        // Axios로 API 호출
        const response = await axios.get('/api.php', {
          params: {
            f: 'list_users',
            page: this.page,
            per_page: this.per_page,
            gender: this.gender,
            age_start: this.age_start,
            age_end: this.age_end,
            name: this.name
          }
        });

        // 에러 체크
        if (response.data.error_code) {
          alert(response.data.error_message);
          return;
        }

        // 결과 저장
        this.users = response.data.users;
        this.total = response.data.total;
        this.total_pages = response.data.total_pages;
        this.page = response.data.page;

      } catch (err) {
        console.error('사용자 목록 로드 실패:', err);
        alert('사용자 목록을 불러오는데 실패했습니다.');
      } finally {
        this.loading = false;
      }
    },

    // 검색
    search() {
      this.page = 1; // 검색 시 1페이지로 초기화
      this.loadUsers();
    },

    // 페이지 이동
    goToPage(pageNumber) {
      this.page = pageNumber;
      this.loadUsers();
    },

    // 필터 초기화
    resetFilters() {
      this.gender = '';
      this.age_start = '';
      this.age_end = '';
      this.name = '';
      this.page = 1;
      this.loadUsers();
    }
  },
  mounted() {
    // 초기 로드
    this.loadUsers();
  }
}).mount('#app');
```

##### 예제 3: 관리자 대시보드

```php
<?php
// page/admin/users.php

// 관리자 권한 확인
$admin = login();
if (!$admin || $admin->role !== 'admin') {
    header('Location: /');
    exit;
}

// 사용자 목록 조회
$result = list_users(array_merge($_GET, ['per_page' => 50]));
?>

<div class="container my-5">
    <h1>사용자 관리</h1>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>전체 사용자</h5>
                    <h2><?= number_format($result['total']) ?>명</h2>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>이름</th>
                <th>성별</th>
                <th>가입일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result['users'] as $user_data): ?>
                <?php $user = new UserModel($user_data); ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></td>
                    <td><?= $user->gender === 'M' ? '남성' : '여성' ?></td>
                    <td><?= date('Y-m-d', $user->created_at) ?></td>
                    <td>
                        <a href="?page=admin/user-detail&id=<?= $user->id ?>">상세</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

#### 주의사항

1. **페이지 번호**: `page`는 1부터 시작합니다 (0이 아님)
2. **per_page 제한**: 최소 1, 최대 100으로 자동 제한됩니다
3. **birthday 필드**: 나이 필터는 `birthday` 필드가 0이 아닌 사용자만 대상으로 합니다
4. **이름 검색**: 접두어 검색 방식 (`LIKE 'name%'`)이므로 부분 검색은 불가능합니다
5. **성능**: 큰 데이터셋의 경우 인덱스를 추가하는 것을 권장합니다

**권장 인덱스:**

```sql
-- 성별 필터링 성능 향상
ALTER TABLE users ADD INDEX idx_gender (gender);

-- 생년월일 필터링 성능 향상
ALTER TABLE users ADD INDEX idx_birthday (birthday);

-- 이름 검색 성능 향상
ALTER TABLE users ADD INDEX idx_first_name (first_name);
ALTER TABLE users ADD INDEX idx_last_name (last_name);

-- 복합 인덱스 (성별 + 생년월일)
ALTER TABLE users ADD INDEX idx_gender_birthday (gender, birthday);
```

## 사용 예제

### 1. Firebase 로그인 후 사용자 레코드 생성 (세션 쿠키 자동 설정)

```javascript
// JavaScript (클라이언트)
firebase.auth().onAuthStateChanged(async (user) => {
    if (user) {
        // Firebase에 로그인 성공
        try {
            // 서버에 사용자 레코드 생성 요청
            // 성공 시 서버가 자동으로 세션 ID 쿠키를 설정합니다
            const result = await func('create_user_record', {
                firebase_uid: user.uid,
                first_name: user.displayName?.split(' ')[0] || '',
                last_name: user.displayName?.split(' ')[1] || '',
                middle_name: ''
            });

            console.log('사용자 ID:', result.id);
            console.log('세션 쿠키가 자동으로 설정되었습니다');

            // 이후 모든 요청에 세션 쿠키가 자동으로 포함됩니다
            // 별도의 쿠키 설정 작업이 필요하지 않습니다
        } catch (error) {
            if (error.code === 'user-already-exists') {
                // 이미 존재하는 사용자 - 정상
                // 세션 쿠키는 설정되지 않습니다 (이미 존재)
                console.log('기존 사용자');
            } else {
                console.error('사용자 생성 실패:', error);
            }
        }
    }
});
```

### 2. 사용자 정보 조회

```php
// PHP
$userId = 1;
$user = get_user(['id' => $userId]);

if (!isset($user['error_code'])) {
    echo "Firebase UID: " . $user['firebase_uid'] . "\n";
    echo "이름: " . $user['first_name'] . " " . $user['last_name'] . "\n";
    echo "생년월일: " . date('Y-m-d', $user['birthday']) . "\n";
    echo "성별: " . ($user['gender'] === 'M' ? '남성' : '여성') . "\n";
}
```

### 3. 중복 사용자 방지

`create_user_record()` 함수는 자동으로 중복을 검사합니다:

```php
// 같은 firebase_uid로 두 번 호출
$user1 = create_user_record(['firebase_uid' => 'abc123']);
// 성공: {"id": 1, "firebase_uid": "abc123", ...}

$user2 = create_user_record(['firebase_uid' => 'abc123']);
// 에러: {"error_code": "user-already-exists", "error_message": "이미 존재하는 사용자입니다.", ...}
```

## 친구 관리

### 친구 추가 (request_friend)

다른 사용자에게 친구 요청을 보냅니다. 이미 요청이 존재하면 `updated_at`만 갱신됩니다.

**파일 위치**: `lib/friend-and-feed/friend-and-feed.functions.php`

#### 파라미터

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `me` | int | ✅ | 요청을 보내는 사용자 ID |
| `other` | int | ✅ | 요청을 받는 사용자 ID |
| `auth` | bool | ✅ | Firebase 인증 포함 여부 (항상 `true`) |

#### 반환값

- **성공**: `{'message': '친구 요청을 보냈습니다', 'success': true}`
- **실패**: 에러 배열 (`error_code`, `error_message`)

#### 에러 코드

| 에러 코드 | 설명 |
|-----------|------|
| `invalid-me` | me 파라미터가 유효하지 않음 |
| `invalid-other` | other 파라미터가 유효하지 않음 |
| `same-user` | 자기 자신에게 친구 요청을 보낼 수 없음 |

#### API 호출 예제

```bash
# cURL로 친구 요청 보내기
curl -X POST https://local.sonub.com/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "func": "request_friend",
    "me": 1,
    "other": 31,
    "auth": true
  }'
```

#### 응답 예제

**성공 응답** (HTTP 200):
```json
{
  "message": "친구 요청을 보냈습니다",
  "success": true,
  "func": "request_friend"
}
```

**에러 응답** (HTTP 400):
```json
{
  "error_code": "same-user",
  "error_message": "자기 자신에게 친구 요청을 보낼 수 없습니다",
  "error_response_code": 400,
  "func": "request_friend"
}
```

### JavaScript에서 친구 추가하기

JavaScript에서는 `func()` 함수를 사용하여 친구 요청을 보냅니다.

#### Vue.js 예제

```javascript
// Vue.js 컴포넌트에서 친구 추가
ready(() => {
    Vue.createApp({
        data() {
            return {
                myUserId: 1,        // 로그인한 사용자 ID
                otherUserId: 31,    // 친구 요청을 보낼 사용자 ID
                requesting: false,  // 요청 중 상태
                isFriend: false     // 친구 여부
            };
        },
        methods: {
            async requestFriend() {
                // 로그인 확인
                if (!this.myUserId) {
                    alert('로그인이 필요합니다.');
                    window.location.href = '/user/login';
                    return;
                }

                // 자기 자신에게 친구 요청 방지
                if (this.otherUserId === this.myUserId) {
                    alert('자기 자신에게는 친구 요청을 보낼 수 없습니다.');
                    return;
                }

                try {
                    // 요청 중 상태 설정
                    this.requesting = true;

                    // API 호출: request_friend 함수 사용
                    await func('request_friend', {
                        me: this.myUserId,
                        other: this.otherUserId,
                        auth: true // Firebase 인증 포함
                    });

                    // 성공: 친구 상태 업데이트
                    this.isFriend = true;
                    alert('친구 요청을 보냈습니다.');

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    const errorMessage = error.message || error.error_message || '친구 요청에 실패했습니다.';
                    alert(`친구 요청 실패: ${errorMessage}`);
                } finally {
                    this.requesting = false;
                }
            }
        }
    }).mount('#app');
});
```

#### 일반 JavaScript 예제

```javascript
// 일반 JavaScript에서 친구 추가
ready(async () => {
    const btnAddFriend = document.getElementById('btn-add-friend');

    btnAddFriend.addEventListener('click', async () => {
        try {
            btnAddFriend.disabled = true;

            // 로그인한 사용자 ID (예시)
            const myUserId = 1;
            const otherUserId = 31;

            // API 호출
            const result = await func('request_friend', {
                me: myUserId,
                other: otherUserId,
                auth: true
            });

            alert(result.message); // "친구 요청을 보냈습니다"

        } catch (error) {
            console.error('친구 요청 실패:', error);
            alert('친구 요청에 실패했습니다.');
            btnAddFriend.disabled = false;
        }
    });
});
```

#### 프로필 페이지 예제

실제 프로필 페이지에서는 다음과 같이 구현합니다:

**page/user/profile.php** (PHP):
```php
<?php
// 사용자 정보 로드
$user_id = http_param('id') ?? login()->id ?? 0;
$user_data = get_user(['id' => $user_id]);
$user = new UserModel($user_data);
$is_me = login() && login()->id === $user->id;
?>

<!-- Vue.js 앱 컨테이너 -->
<div id="profile-app">
    <!-- 프로필 정보 -->
    <h1><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></h1>

    <!-- 친구 추가 버튼 (다른 사용자인 경우만 표시) -->
    <?php if (!$is_me): ?>
        <button @click="requestFriend(<?= $user->id ?>)"
                class="btn-add-friend"
                :disabled="requesting || isFriend">
            <span v-if="requesting">
                <span class="spinner-border spinner-border-sm"></span>
                요청 중...
            </span>
            <span v-else-if="isFriend">
                <i class="bi bi-check-circle"></i> 친구 요청을 보냈습니다
            </span>
            <span v-else>
                <i class="bi bi-person-plus"></i> 친구 추가
            </span>
        </button>
    <?php endif; ?>
</div>
```

**page/user/profile.js** (JavaScript):
```javascript
ready(() => {
    const appElement = document.getElementById('profile-app');
    if (!appElement) return;

    Vue.createApp({
        data() {
            return {
                requesting: false,
                isFriend: false
            };
        },
        methods: {
            async requestFriend(otherUserId) {
                // 로그인 확인 - window.Store.user에서 로그인한 사용자 정보 가져오기
                if (!window.Store || !window.Store.user || !window.Store.user.id) {
                    alert('로그인이 필요합니다.');
                    window.location.href = '/user/login';
                    return;
                }

                const myUserId = window.Store.user.id;

                // 자기 자신에게 친구 요청 방지
                if (otherUserId === myUserId) {
                    alert('자기 자신에게는 친구 요청을 보낼 수 없습니다.');
                    return;
                }

                try {
                    this.requesting = true;

                    await func('request_friend', {
                        me: myUserId,
                        other: otherUserId,
                        auth: true
                    });

                    this.isFriend = true;
                    alert('친구 요청을 보냈습니다.');

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    alert('친구 요청에 실패했습니다.');
                } finally {
                    this.requesting = false;
                }
            }
        }
    }).mount('#profile-app');
});
```

#### window.Store 사용하기

**🔥 중요**: Sonub에서는 **로그인한 사용자 정보를 `window.Store.user`에서 가져옵니다**.

##### Store 구조

```javascript
window.Store = {
    user: {
        id: 1,                    // 사용자 ID
        firebase_uid: 'abc123',   // Firebase UID
        first_name: '길동',       // 이름
        last_name: '홍',          // 성
        middle_name: '',          // 중간 이름
        gender: 'M',              // 성별
        birthday: 631152000,      // 생년월일 (Unix timestamp)
        photo_url: '/uploads/...' // 프로필 사진 URL
        // ... 기타 사용자 정보
    }
};
```

##### Store 사용 예제

**로그인 확인:**
```javascript
// ✅ 올바른 방법: window.Store.user 사용
if (!window.Store || !window.Store.user || !window.Store.user.id) {
    alert('로그인이 필요합니다.');
    window.location.href = '/user/login';
    return;
}

const myUserId = window.Store.user.id;
```

**❌ 잘못된 방법:**
```javascript
// ❌ data 속성으로 전달하지 마세요
const myUserId = parseInt(appElement.dataset.myUserId) || 0;

// ❌ PHP에서 직접 출력하지 마세요
const myUserId = <?= login() ? login()->id : 0 ?>;
```

##### Store 사용의 장점

1. **중앙 관리**: 모든 페이지에서 일관된 방식으로 사용자 정보 접근
2. **간단한 코드**: data 속성이나 초기화 코드가 필요 없음
3. **동적 업데이트**: 사용자 정보가 변경되면 자동으로 반영
4. **타입 안정성**: 항상 동일한 구조의 객체 사용

##### 프로필 페이지 구현 비교

**❌ 잘못된 방법 (data 속성 사용):**
```php
<!-- PHP: 복잡한 data 속성 -->
<div id="profile-app"
     data-other-user-id="<?= $user->id ?>"
     data-my-user-id="<?= login() ? login()->id : 0 ?>">
    <button @click="requestFriend">친구 추가</button>
</div>
```

```javascript
// JavaScript: data 속성에서 값 추출 (불필요한 코드)
const appElement = document.getElementById('profile-app');
const otherUserId = parseInt(appElement.dataset.otherUserId) || 0;
const myUserId = parseInt(appElement.dataset.myUserId) || 0;

Vue.createApp({
    data() {
        return {
            otherUserId: otherUserId,  // 불필요한 data
            myUserId: myUserId         // 불필요한 data
        };
    },
    methods: {
        async requestFriend() {
            await func('request_friend', {
                me: this.myUserId,      // data에서 가져옴
                other: this.otherUserId
            });
        }
    }
});
```

**✅ 올바른 방법 (Store 사용):**
```php
<!-- PHP: 간단한 구조, data 속성 불필요 -->
<div id="profile-app">
    <button @click="requestFriend(<?= $user->id ?>)">친구 추가</button>
</div>
```

```javascript
// JavaScript: 간단하고 명확한 코드
Vue.createApp({
    data() {
        return {
            requesting: false,
            isFriend: false
            // otherUserId, myUserId 불필요!
        };
    },
    methods: {
        async requestFriend(otherUserId) {
            // window.Store.user에서 직접 가져옴
            const myUserId = window.Store.user.id;

            await func('request_friend', {
                me: myUserId,
                other: otherUserId
            });
        }
    }
});
```

##### 사용 시 주의사항

1. **항상 null 체크**: `window.Store`와 `window.Store.user`가 존재하는지 확인
2. **로그인 여부 확인**: `window.Store.user.id`가 있는지 확인
3. **파라미터 전달**: 다른 사용자 ID는 함수 파라미터로 전달

```javascript
// ✅ 올바른 null 체크
if (!window.Store || !window.Store.user || !window.Store.user.id) {
    alert('로그인이 필요합니다.');
    return;
}

// ✅ 안전하게 사용자 ID 가져오기
const myUserId = window.Store.user.id;
```

#### 주의사항

1. **Firebase 인증 필수**: `auth: true` 파라미터를 항상 포함해야 합니다.
2. **로그인 확인**: `window.Store.user`를 사용하여 로그인 상태를 확인합니다.
3. **중복 요청 방지**: 요청 중 상태(`requesting`)를 사용하여 버튼을 비활성화합니다.
4. **에러 처리**: `try-catch`를 사용하여 에러를 적절히 처리합니다.
5. **자기 자신 확인**: 자기 자신에게는 친구 요청을 보낼 수 없습니다.
6. **Store 사용**: 로그인한 사용자 정보는 항상 `window.Store.user`에서 가져옵니다.

## 사용자 검색 컴포넌트

**🔥🔥🔥 최강력 규칙: 사용자 검색 UI가 필요한 경우 `<div class="user-search"></div>`만 추가하면 됩니다 🔥🔥🔥**

### 개요

사용자 검색 컴포넌트는 독립적인 Vue.js 컴포넌트로, 페이지에 `<div class="user-search"></div>` 한 줄만 추가하면 자동으로 **검색 버튼**과 **Bootstrap 모달 검색 UI**가 모두 생성됩니다.

**파일 위치**: `js/user-search.js`

### 특징

- ✅ **완전한 독립 컴포넌트**: 검색 버튼 + 모달 HTML 템플릿, Vue.js 로직, 다국어 번역이 모두 포함됨
- ✅ **간단한 사용법**: `<div class="user-search"></div>` 한 줄만 추가
- ✅ **여러 개 사용 가능**: 한 페이지에 여러 개의 검색 컴포넌트를 독립적으로 사용 가능
- ✅ **자동 UI 생성**: 검색 버튼 + Bootstrap 모달, 검색 폼, 페이지네이션 자동 생성
- ✅ **다국어 지원**: JavaScript `tr()` 함수로 4개 국어 지원 (ko, en, ja, zh)

### 사용 방법

#### 1단계: HTML에 컨테이너 추가

페이지 아무 곳에나 `<div class="user-search"></div>`를 추가하면 자동으로 검색 버튼과 모달이 생성됩니다:

```php
<!-- page/user/list.php -->
<div id="user-list-app" class="container py-4">
    <!-- 사용자 목록 UI -->
    <h1>사용자 목록</h1>
    <!-- ... -->
</div>

<!-- 사용자 검색 컴포넌트 추가 (한 줄만!) -->
<!-- 이 한 줄이 자동으로 "친구 검색" 버튼과 모달 UI를 모두 생성합니다 -->
<div class="user-search"></div>
```

**여러 개 사용하기:**

```php
<!-- 페이지 상단에 하나 -->
<div class="user-search"></div>

<!-- 사용자 목록 -->
<div class="row g-3">
    <!-- ... -->
</div>

<!-- 페이지 하단에 또 하나 -->
<div class="user-search"></div>
```

각 `<div class="user-search"></div>`는 독립적인 Vue.js 앱으로 마운트되며, 각각 고유한 검색 버튼과 모달을 가집니다.

#### 2단계: 스크립트 로드 (자동)

`js/user-search.js` 스크립트가 자동으로 로드되면 다음이 자동으로 실행됩니다:

1. 모든 `.user-search` 요소 감지
2. 각 요소에 대해 독립적인 Vue.js 앱 생성 및 마운트
3. **검색 버튼 자동 생성** (버튼 클릭 시 모달 열림)
4. Bootstrap 모달 UI 자동 생성 (각 인스턴스마다 고유 ID 부여)

#### 3단계: 검색 사용

자동 생성된 "친구 검색" 버튼을 클릭하면 모달이 열립니다.

### 완전한 예제

**예제 1: 자동 생성된 버튼 사용 (가장 간단)**

```php
<?php
// page/user/find-friend.php

$result = list_users(['per_page' => 20, 'page' => 1]);
$users = $result['users'];
?>

<div class="container py-4">
    <h1>친구 찾기</h1>

    <!-- 사용자 검색 컴포넌트 (한 줄만 추가!) -->
    <!-- 자동으로 "친구 검색" 버튼이 생성되며, 클릭 시 모달이 열립니다 -->
    <div class="user-search"></div>

    <!-- 사용자 목록 -->
    <div class="row g-3 mt-4">
        <?php foreach ($users as $user_data): ?>
            <?php $user = new UserModel($user_data); ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($user->first_name . ' ' . $user->last_name) ?></h5>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

**예제 2: 커스텀 버튼과 함께 사용**

```php
<?php
// page/user/list.php
?>

<div class="container py-4">
    <!-- 페이지 헤더 -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>친구 목록</h1>
        <!-- 커스텀 검색 버튼 -->
        <button class="btn btn-success" onclick="window.openFriendSearchModal()">
            <i class="bi bi-search"></i> 친구 검색
        </button>
    </div>

    <!-- 사용자 목록 -->
    <div class="row g-3">
        <!-- ... -->
    </div>
</div>

<!-- 사용자 검색 컴포넌트 (모달만 생성, 버튼은 위에서 커스텀 버튼 사용) -->
<div class="user-search"></div>
```

### 생성되는 UI

`js/user-search.js` 스크립트가 자동으로 다음 UI를 생성합니다:

1. **검색 버튼**: "친구 검색" 버튼 (클릭 시 모달 열림)
2. **Bootstrap 모달**: 검색 인터페이스
3. **검색 입력 폼**: 이름 검색 input + 검색 버튼
4. **검색 결과 그리드**: 2열 그리드로 사용자 카드 표시
5. **페이지네이션**: 검색 결과가 10개를 넘으면 자동으로 페이지네이션 표시
6. **다국어 지원**: 사용자 언어에 맞게 자동 번역

### 다국어 지원

컴포넌트는 JavaScript `tr()` 함수를 사용하여 4개 국어를 지원합니다:

```javascript
// js/user-search.js 내부
computed: {
    t() {
        return {
            친구_검색: tr({ ko: '친구 검색', en: 'Find Friends', ja: '友達検索', zh: '查找朋友' }),
            이름을_입력하세요: tr({ ko: '이름을 입력하세요', en: 'Enter name', ja: '名前を入力してください', zh: '输入姓名' }),
            검색: tr({ ko: '검색', en: 'Search', ja: '検索', zh: '搜索' }),
            검색_중: tr({ ko: '검색 중...', en: 'Searching...', ja: '検索中...', zh: '搜索中...' }),
            검색_결과가_없습니다: tr({ ko: '검색 결과가 없습니다.', en: 'No results found.', ja: '検索結果がありません。', zh: '未找到结果。' }),
            검색어를_입력해주세요: tr({ ko: '검색어를 입력해주세요', en: 'Please enter a search term', ja: '検索キーワードを入力してください', zh: '请输入搜索关键词' }),
            검색에_실패했습니다: tr({ ko: '검색에 실패했습니다', en: 'Search failed', ja: '検索に失敗しました', zh: '搜索失败' })
        };
    }
}
```

### 전역 함수

컴포넌트가 마운트되면 자동으로 전역 함수를 등록합니다:

#### window.openFriendSearchModal()

검색 모달을 엽니다.

**사용 예제:**

```html
<!-- HTML 버튼 -->
<button onclick="window.openFriendSearchModal()">친구 검색</button>
```

```javascript
// JavaScript에서 호출
document.getElementById('btn-search').addEventListener('click', () => {
    window.openFriendSearchModal();
});
```

```javascript
// Vue.js에서 호출
Vue.createApp({
    methods: {
        openSearch() {
            window.openFriendSearchModal();
        }
    }
}).mount('#app');
```

### 검색 동작

1. **검색 수행**: 사용자가 이름을 입력하고 검색 버튼 클릭
2. **API 호출**: `list_users()` 함수 호출하여 결과 조회
3. **결과 표시**: 2열 그리드로 사용자 카드 표시
4. **페이지네이션**: 검색 결과가 10개를 넘으면 자동으로 페이지네이션 표시
5. **프로필 이동**: 사용자 카드를 클릭하면 프로필 페이지로 이동 (`/user/profile?id=...`)

### 주의사항

1. **✅ 필수**: `<div class="user-search"></div>` 요소가 페이지에 존재해야 함
2. **✅ 필수**: `js/user-search.js` 스크립트가 로드되어야 함 (자동 로드)
3. **✅ 권장**: Bootstrap 5.x 및 Bootstrap Icons 사용
4. **✅ 여러 개 사용 가능**: 한 페이지에 여러 개의 `<div class="user-search"></div>` 추가 가능 (각각 독립적으로 동작)
5. **❌ 금지**: `<div class="user-search">` 요소 내부에 다른 HTML 추가 금지 (컴포넌트가 자동으로 생성)

### 장점

1. **✅ 간단한 사용법**: HTML 한 줄만 추가하면 완전한 검색 UI 생성
2. **✅ 재사용 가능**: 여러 페이지에서 동일한 검색 UI 사용 가능
3. **✅ 독립성**: 다른 Vue.js 앱과 충돌 없이 독립적으로 동작
4. **✅ 유지보수 용이**: UI 수정이 필요하면 `js/user-search.js` 파일만 수정
5. **✅ 다국어 지원**: 사용자 언어에 맞게 자동 번역

### 실전 사용 예제

#### 예제 1: 단일 컴포넌트

```php
<!-- page/friend/find-friend.php -->
<div class="container">
    <h1>친구 찾기</h1>
</div>

<!-- 사용자 검색 컴포넌트 -->
<div class="user-search"></div>
```

#### 예제 2: 여러 개의 컴포넌트

```php
<!-- page/user/list.php -->

<!-- 페이지 상단 검색 -->
<div class="user-search"></div>

<div id="user-list-app" class="container">
    <!-- Vue.js 사용자 목록 앱 -->
    <div v-for="user in users" :key="user.id">
        <h5>{{ user.first_name }} {{ user.last_name }}</h5>
    </div>
</div>

<!-- 페이지 하단 검색 (독립적으로 동작) -->
<div class="user-search"></div>
```

---

## 테스트

테스트 파일 위치:
- `tests/user/create_user_record.test.php` - 사용자 생성 테스트
- `tests/user/get_user.test.php` - 사용자 조회 테스트
- `tests/user/list_users.test.php` - 사용자 목록 조회 테스트
- `tests/friend-and-feed/friend-and-feed.test.php` - 친구 관리 테스트

```bash
# 사용자 생성 테스트
php tests/user/create_user_record.test.php

# 사용자 조회 테스트
php tests/user/get_user.test.php

# 사용자 목록 조회 테스트 (페이지네이션 및 필터링)
php tests/user/list_users.test.php

# 친구 관리 테스트
php tests/friend-and-feed/friend-and-feed.test.php
```
