# 사용자 관리 (User Management)

## 목차
- [개요](#개요)
- [사용자 테이블 구조](#사용자-테이블-구조)
- [사용자 함수](#사용자-함수)
  - [create_user_record](#create_user_record)
    - [세션 ID 쿠키 자동 설정](#세션-id-쿠키-자동-설정)
  - [get_user](#get_user)
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
| `display_name` | varchar(64) | 사용자 표시 이름 | UNIQUE, NOT NULL |
| `created_at` | int(10) unsigned | 생성 시각 (timestamp) | NOT NULL |
| `updated_at` | int(10) unsigned | 수정 시각 (timestamp) | NOT NULL, DEFAULT 0 |
| `birthday` | int(10) unsigned | 생년월일 (timestamp) | NOT NULL, DEFAULT 0 |
| `gender` | char(1) | 성별 ('M' 또는 'F') | NOT NULL |

## 사용자 함수

### create_user_record

사용자 레코드를 생성합니다. Firebase에 로그인했지만 `users` 테이블에 레코드가 없는 경우 호출합니다.

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
| `display_name` | string | ❌ | 사용자 표시 이름 (없으면 firebase_uid 사용) |
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
    "display_name": "홍길동",
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
  "display_name": "홍길동",
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
    'display_name' => '홍길동',
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
  "display_name": "홍길동",
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
    echo "사용자 이름: " . $user['display_name'];
}
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
                display_name: user.displayName || user.uid
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
    echo "표시 이름: " . $user['display_name'] . "\n";
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

## 테스트

테스트 파일 위치: `tests/user/create_user_record.test.php`, `tests/user/get_user.test.php`

```bash
# 사용자 생성 테스트
php tests/user/create_user_record.test.php

# 사용자 조회 테스트
php tests/user/get_user.test.php
```
