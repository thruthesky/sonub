---
name: sonub-api
description:
  - API First 설계 철학을 따르는 Sonub.Com 웹사이트의 API 엔드포인트, 요청/응답 형식, 인증 방법, 오류 처리 등을 제공
  - 사용자 정보, 게시글, 댓글 등을 조회, 검색, 생성, 수정, 삭제할 때 사용
  - 글 생성(create_post), 댓글 생성(create_comment) 등 주요 기능을 구현하는 예제 코드와 가이드 포함
  - 명령줄 스크립트(create_posts.sh, list_users.sh, list_posts.sh)를 통해 API를 간편하게 호출
  - 배치 처리, 페이지네이션, 필터링, 이미지 첨부 등 고급 기능 지원
  - 테스트 계정 자동 로그인, 세션 관리, 에러 처리 방법 등 실무 예제 제공
---

# Sonub API 스킬 개요

본 스킬은 Sonub.Com의 API 엔드포인트, 요청 및 응답 형식, 인증 방법, 오류 처리 등에 대한 상세한 설명과 함께 다양한 기능을 구현하는 예제 코드를 포함하고 있습니다. 웹/앱에서 Sonub API를 사용하려는 경우, SONUB, API, 백엔드, 정보 저장, DB 정보 읽기 등의 요청에서 본 스킬을 사용합니다.

Sonub는 **API First** 설계 철학을 따르는 웹 애플리케이션입니다. 모든 핵심 기능은 API를 통해 접근 가능하며, RESTful 클라이언트에서 직접 호출할 수 있습니다.


## 목차

- [Sonub API 스킬 개요](#sonub-api-스킬-개요)
  - [목차](#목차)
  - [API First 설계 철학](#api-first-설계-철학)
    - [API 함수 반환 형식 규칙](#api-함수-반환-형식-규칙)
  - [LIB 폴더 구조](#lib-폴더-구조)
  - [사용 가능한 API 함수 목록](#사용-가능한-api-함수-목록)
    - [게시글 관리 (Post Management) - 7개 함수](#게시글-관리-post-management---7개-함수)
    - [댓글 관리 (Comment Management) - 5개 함수](#댓글-관리-comment-management---5개-함수)
    - [사용자 관리 (User Management) - 7개 함수](#사용자-관리-user-management---7개-함수)
    - [친구 \& 피드 (Friend \& Feed) - 12개 함수](#친구--피드-friend--feed---12개-함수)
    - [파일 작업 (File Operations) - 1개 함수](#파일-작업-file-operations---1개-함수)
    - [언어 설정 (Language Settings) - 1개 함수](#언어-설정-language-settings---1개-함수)
    - [인증 (Authentication) - 1개 함수](#인증-authentication---1개-함수)
  - [글 생성 시 필수 지침](#-글-생성-시-필수-지침)
  - [Scripts - 명령줄 API 호출 도구](#scripts---명령줄-api-호출-도구)
    - [사전 요구사항](#사전-요구사항)
    - [환경 변수](#환경-변수)
    - [list\_users.sh - 사용자 목록 조회](#list_userssh---사용자-목록-조회)
    - [create\_posts.sh - 게시글 생성](#create_postssh---게시글-생성)
    - [create\_post() API 함수 상세 가이드](#create_post-api-함수-상세-가이드)
    - [list\_posts.sh - 게시글 목록 조회](#list_postssh---게시글-목록-조회)
    - [스크립트 개발 가이드](#스크립트-개발-가이드)
  - [API 프로토콜 상세 가이드](#api-프로토콜-상세-가이드)
  - [보안 고려사항](#보안-고려사항)


## API First 설계 철학

**Sonub는 API First 클래스 시스템입니다:**

- ✅ **모든 PHP 함수는 API를 통해 직접 호출 가능하다**
- ✅ **모든 PHP 함수는 배열, 객체 또는 단일 값(스칼라)을 리턴할 수 있으며, 클라이언트에게 JSON으로 리턴한다**
- ✅ **단일 값(숫자, 문자열, 불리언)을 리턴하는 경우, api.php가 자동으로 `['data' => 값, 'func' => '함수명']` 형태로 변환한다**
- ✅ **모든 함수는 에러 발생 시 `error()` 함수를 호출하여 `ApiException`을 throw한다**
- ✅ **`api.php`에서 try/catch 블록으로 `ApiException`을 catch하여 JSON 에러 응답으로 변환한다**
- ✅ **Model 객체(UserModel, PostModel 등)를 리턴하는 경우, 반드시 toArray() 메서드를 구현해야 한다**
- ✅ RESTful 클라이언트가 API를 통해 모든 기능에 접근 가능
- ✅ 프론트엔드와 백엔드가 명확히 분리됨
- ✅ 모바일 앱, 웹 앱, 서드파티 서비스 등 다양한 클라이언트 지원
- ✅ 개발자가 요청하면, PHP 에서 존재하는 함수를 찾아서, 클라이언트 `func('PHP함수이름', {파라미터})` 형태로 호출 해야 합니다.

### API 함수 반환 형식 규칙

**🔥🔥🔥 2025-01-19 업데이트: api.php가 단일 값 자동 변환 지원 🔥🔥🔥**

이제 PHP 함수가 단일 값(숫자, 문자열, 불리언)을 리턴하면, `api.php`가 자동으로 `['data' => 값, 'func' => '함수명']` 형태로 변환합니다.

**배열/객체 반환 (직접 반환):**
- 여러 데이터를 포함하는 배열: 직접 반환
- 객체 배열: 직접 반환
- 복수 필드를 가진 연관 배열: 직접 반환

```php
// ✅ 올바른 예: 배열 직접 반환
function get_friends(array $input): array {
    // ...
    return $friends;  // [['id' => 1, ...], ['id' => 2, ...]]
}

// ✅ 올바른 예: 친구 ID 배열 직접 반환
function get_friend_ids(array $input): array {
    // ...
    return $friend_ids;  // [1, 2, 3, 4, 5]
}

// ✅ 올바른 예: 복수 필드 연관 배열
function request_friend(array $input): array {
    // ...
    return ['message' => '친구 요청을 보냈습니다', 'success' => true];
}
```

**단일 값(스칼라) 반환:**
- 단일 문자열, 숫자, 불리언 값: **직접 반환 가능** (api.php가 자동 변환)

```php
// ✅ 방법 1: 단일 값 직접 반환 (권장 - api.php가 자동 변환)
function get_user_count(): int {
    return 42;  // api.php가 자동으로 ['data' => 42, 'func' => 'get_user_count']로 변환
}

// ✅ 방법 2: 수동으로 ['data' => ...] 형태로 반환 (기존 방식 - 여전히 지원)
function get_app_version(): array {
    return ['data' => '2025-10-18-17-35-04'];  // 수동으로 래핑
}

// ✅ 올바른 예: 불리언 직접 반환
function check_email_exists(array $input): bool {
    $email = $input['email'] ?? '';
    // ... 이메일 존재 여부 확인 ...
    return true;  // api.php가 자동으로 ['data' => true, 'func' => 'check_email_exists']로 변환
}

// ✅ 올바른 예: 문자열 직접 반환
function get_welcome_message(): string {
    return 'Welcome to Sonub!';  // api.php가 자동으로 ['data' => 'Welcome to Sonub!', 'func' => 'get_welcome_message']로 변환
}
```

**JavaScript에서 사용:**
```javascript
// 배열 직접 반환 함수
const friends = await func('get_friends', { me: 5, limit: 10 });
console.log(friends);  // 친구 배열 (배열이 그대로 리턴됨)

const friendIds = await func('get_friend_ids', { me: 5 });
console.log(friendIds);  // [1, 2, 3, 4, 5] (배열이 그대로 리턴됨)

// 단일 값 반환 함수 (api.php가 자동 변환)
const result = await func('get_user_count');
console.log(result.data);  // 42
console.log(result.func);  // 'get_user_count'

const version = await func('get_app_version');
console.log(version.data);  // '2025-10-18-17-35-04'

const emailExists = await func('check_email_exists', { email: 'test@example.com' });
console.log(emailExists.data);  // true
console.log(emailExists.func);  // 'check_email_exists'

const message = await func('get_welcome_message');
console.log(message.data);  // 'Welcome to Sonub!'
```

**api.php 자동 변환 로직:**
```php
// api.php 내부 처리
$res = $func_name(http_params());

// 단일 값(숫자, 문자열, 불리언)인 경우 자동으로 ['data' => 값] 형태로 변환
if (is_numeric($res) || is_string($res) || is_bool($res)) {
    $res = ['data' => $res];
}

// 'func' 필드 자동 추가
$res['func'] = $func_name;

// JSON 응답 출력
echo json_encode($res, JSON_UNESCAPED_UNICODE);
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

---

## 사용 가능한 API 함수 목록

Sonub는 총 **33개의 API 함수**를 제공합니다. 모든 함수는 `function xxx(array $input)` 시그니처를 가지며, JavaScript에서 `func('함수명', {파라미터})`로 호출할 수 있습니다.

**📖 상세 문서:** [Sonub API 엔드포인트 목록](sonub-api-endpoints.md) - 각 함수의 파라미터, 반환값, 사용 예제, 에러 처리 등 상세 정보

### 게시글 관리 (Post Management) - 7개 함수

**게시글 생성, 조회, 수정, 삭제 기능**

- **`get_post`**: 게시글 ID로 단일 게시글 조회. 작성자 정보 포함.
- **`create_post`**: 새 게시글 생성. 카테고리, 제목, 내용, 공개 범위(public/friends/private) 설정. Fan-out on Write 지원.
- **`update_post`**: 기존 게시글 수정. 본인의 게시글만 수정 가능.
- **`list_posts`**: 게시글 목록 조회. 페이지네이션, 카테고리/사용자/공개 범위 필터링 지원.
- **`search_posts`**: 제목 또는 내용으로 게시글 검색. 카테고리 필터링 가능.
- **`delete_post`**: 게시글 삭제. 본인의 게시글만 삭제 가능. 관련 피드 항목도 자동 삭제.
- **`delete_file_from_post`**: 게시글에서 특정 파일 제거.

📖 [게시글 관리 API 상세 문서](sonub-api-endpoints.md#게시글-관리-post-management)

### 댓글 관리 (Comment Management) - 5개 함수

**게시글 댓글 생성, 조회, 수정, 삭제. 중첩 댓글(대댓글) 지원**

- **`get_comment`**: 댓글 ID로 단일 댓글 조회.
- **`get_comments`**: 특정 게시글의 모든 댓글 조회. 중첩 댓글 포함.
- **`create_comment`**: 새 댓글 생성. 대댓글(parent_id) 지원. 게시글 comment_count 자동 업데이트.
- **`update_comment`**: 댓글 내용 수정. 본인의 댓글만 수정 가능.
- **`delete_comment`**: 댓글 삭제. 본인의 댓글만 삭제 가능. 자식 댓글도 함께 삭제.

📖 [댓글 관리 API 상세 문서](sonub-api-endpoints.md#댓글-관리-comment-management)

### 사용자 관리 (User Management) - 7개 함수

**사용자 프로필 생성, 조회, 수정, 검색**

- **`create_user_record`**: Firebase UID 기반 사용자 레코드 생성.
- **`get_user`**: ID 또는 Firebase UID로 사용자 조회.
- **`update_user_profile`**: 특정 사용자 프로필 수정. 본인 또는 관리자만 가능.
- **`update_my_profile`**: 로그인한 사용자의 프로필 수정 (ID 불필요).
- **`list_users`**: 사용자 목록 조회. 페이지네이션, 성별/나이 필터링 지원.
- **`get_users`**: 여러 사용자의 정보를 한 번에 조회 (ID 배열).
- **`search_users`**: 이름, 이메일, 전화번호로 사용자 검색.

📖 [사용자 관리 API 상세 문서](sonub-api-endpoints.md#사용자-관리-user-management)

### 친구 & 피드 (Friend & Feed) - 12개 함수

**친구 관계 관리 및 친구 피드 시스템**

**친구 요청:**
- **`request_friend`**: 친구 요청 보내기. 양방향 레코드 생성.
- **`accept_friend`**: 친구 요청 수락. 양쪽 모두 'accepted' 상태로 변경.
- **`remove_friend`**: 친구 관계 삭제. 양방향 레코드 삭제.
- **`reject_friend`**: 친구 요청 거절. 요청 레코드 삭제.
- **`cancel_friend_request`**: 보낸 친구 요청 취소.

**친구 조회:**
- **`get_friend_ids`**: 친구의 사용자 ID 배열 조회.
- **`count_friend_requests_sent`**: 보낸 친구 요청 수 조회.
- **`get_friend_requests_sent`**: 보낸 친구 요청 목록 조회.
- **`count_friend_requests_received`**: 받은 친구 요청 수 조회.
- **`get_friend_requests_received`**: 받은 친구 요청 목록 조회.
- **`get_friends`**: 친구 목록 조회 (사용자 정보 포함).

**피드:**
- **`get_posts_from_feed_entries`**: 피드에서 게시글 목록 조회. 친구가 작성한 게시글 포함. Fan-out on Write 패턴.

📖 [친구 & 피드 API 상세 문서](sonub-api-endpoints.md#친구--피드-friend--feed)

### 파일 작업 (File Operations) - 1개 함수

**파일 업로드 및 삭제**

- **`file_delete`**: 파일 삭제. 본인의 파일만 삭제 가능. 물리적 파일도 함께 삭제.

📖 [파일 작업 API 상세 문서](sonub-api-endpoints.md#파일-작업-file-operations)

### 언어 설정 (Language Settings) - 1개 함수

**사용자 언어 설정**

- **`set_language`**: 사용자의 언어 설정 변경 (en, ko, ja, zh). 쿠키에 저장.

📖 [언어 설정 API 상세 문서](sonub-api-endpoints.md#언어-설정-language-settings)

### 인증 (Authentication) - 1개 함수

**Firebase Authentication 통합 로그인**

- **`login_with_firebase`**: Firebase ID Token으로 로그인. 사용자가 없으면 자동 생성. PHP 세션 쿠키 설정.

📖 [인증 API 상세 문서](sonub-api-endpoints.md#인증-authentication)

---

## Scripts - 명령줄 API 호출 도구

Sonub API를 명령줄에서 간편하게 테스트하고 호출할 수 있는 Bash 스크립트를 제공합니다. 모든 스크립트는 `.claude/skills/sonub-api/scripts/` 디렉토리에 위치합니다.

### 사전 요구사항

- **curl**: HTTP 요청을 보내기 위해 필요
- **jq**: JSON 응답을 보기 좋게 포맷팅 (선택사항)

```bash
# macOS
brew install jq

# Ubuntu/Debian
sudo apt-get install jq

# CentOS/RHEL
sudo yum install jq
```

### 환경 변수

모든 스크립트는 `API_URL` 환경 변수를 지원합니다:

```bash
# 기본값 (설정하지 않으면 https://local.sonub.com/api.php 사용)
export API_URL="https://sonub.com/api.php"

# 또는 각 명령에서 --url 옵션 사용
./list_users.sh --url https://sonub.com/api.php
```

---

### list_users.sh - 사용자 목록 조회

**위치**: `.claude/skills/sonub-api/scripts/list_users.sh`

**설명**: `list_users()` API 함수를 호출하여 사용자 목록을 조회합니다. 페이지네이션, 성별 필터, 나이 필터 등을 지원합니다.

**사용법:**

```bash
# 기본 사용 (10명의 사용자 조회)
./list_users.sh

# 20명의 사용자 조회
./list_users.sh --limit 20

# 2페이지 조회
./list_users.sh --limit 10 --page 2

# 여성 사용자만 조회
./list_users.sh --gender F --limit 10

# 남성 사용자만 조회
./list_users.sh --gender M --limit 15

# 20세에서 30세 사이의 사용자 조회
./list_users.sh --age-min 20 --age-max 30

# 정렬 순서 지정
./list_users.sh --order "name ASC" --limit 20

# 프로덕션 서버에서 조회
./list_users.sh --url https://sonub.com/api.php --limit 5

# 도움말 표시
./list_users.sh --help
```

**옵션:**

| 옵션 | 설명 | 기본값 |
|------|------|--------|
| `--limit N` | 페이지당 사용자 수 (최대: 100) | 10 |
| `--page N` | 페이지 번호 | 1 |
| `--gender [M\|F]` | 성별 필터 (M: 남성, F: 여성) | (없음) |
| `--age-min N` | 최소 나이 | (없음) |
| `--age-max N` | 최대 나이 | (없음) |
| `--order STR` | 정렬 순서 | "created_at DESC" |
| `--url URL` | API URL | https://local.sonub.com/api.php |
| `-h, --help` | 도움말 표시 | - |

**응답 예제:**

```json
{
  "users": [
    {
      "id": 1,
      "name": "홍길동",
      "email": "hong@example.com",
      "gender": "M",
      "birthday": "1990-01-01",
      "created_at": 1640000000
    },
    {
      "id": 2,
      "name": "김영희",
      "email": "kim@example.com",
      "gender": "F",
      "birthday": "1992-05-15",
      "created_at": 1640001000
    }
  ],
  "total": 150,
  "page": 1,
  "limit": 10,
  "func": "list_users"
}
```

**실용 예제:**

```bash
# 여성 사용자 20-25세 조회
./list_users.sh --gender F --age-min 20 --age-max 25 --limit 20

# 최신 가입자 50명 조회
./list_users.sh --order "created_at DESC" --limit 50

# 3페이지의 남성 사용자 조회
./list_users.sh --gender M --page 3 --limit 10
```

---

## 🔥 글 생성 시 필수 지침

**⚠️ 중요**: Sonub에 글(게시글)을 생성할 때는 **반드시 이미 만들어져 있는 `create_posts.sh` 스크립트를 사용**해야 합니다.

### ❌ 절대 금지 사항

- **새로운 코드 작성 금지**: 별도의 bash 스크립트나 새로운 코드를 작성하지 마세요
- **코드 복사 금지**: 기존 create_posts.sh를 복사하여 새로운 파일을 만들지 마세요
- **직접 API 호출 금지**: curl이나 fetch를 직접 작성하여 API를 호출하지 마세요

### ✅ 올바른 방법

**반드시 `create_posts.sh`의 옵션 파라미터를 활용하여 실행하세요:**

```bash
# 기본 형식
./create_posts.sh [옵션]

# 옵션 종류
--count N              # 생성할 게시글 수 지정
--user NAME            # 테스트 계정 선택
--api-url URL          # API 서버 선택 (로컬/프로덕션)
```

### 예제

```bash
# ✅ 올바른 예: create_posts.sh의 옵션을 활용하여 실행
./create_posts.sh --count 5 --user banana --api-url https://sonub.com/api.php

# ✅ 올바른 예: 프로덕션 서버에서 10개 게시글 생성
./create_posts.sh --count 10 --api-url https://sonub.com/api.php

# ❌ 잘못된 예: 새로운 bash 스크립트 작성 (금지!)
# bash create_new_posts.sh   <- 절대 금지!

# ❌ 잘못된 예: 직접 curl 호출 (금지!)
# curl -X POST https://sonub.com/api.php ...   <- 절대 금지!
```

### 옵션 파라미터 활용

필요한 옵션만 조합하여 사용하세요:

| 옵션 | 설명 | 예제 |
|------|------|------|
| `--count N` | 생성할 게시글 수 (1-50) | `--count 5` |
| `--user NAME` | 테스트 계정 (apple, banana, cherry, ...) | `--user banana` |
| `--api-url URL` | API 서버 URL | `--api-url https://sonub.com/api.php` |

### 지원하는 테스트 계정

| 계정 | 사용 예시 |
|------|---------|
| apple, banana, cherry, durian, elderberry | `--user apple` |
| fig, grape, honeydew, jackfruit, kiwi | `--user grape` |
| lemon, mango | `--user mango` |

**기억하세요: `create_posts.sh` 스크립트는 이미 완벽하게 구현되어 있습니다. 옵션 파라미터만 조정하여 필요한 대로 사용하세요!** ✨

---

### create_posts.sh - 게시글 생성

**위치**: `.claude/skills/sonub-api/scripts/create_posts.sh`

**설명**: `create_post()` API 함수를 호출하여 새로운 게시글을 생성합니다. 테스트 계정으로 자동 로그인하고, 한 번에 여러 개의 게시글을 생성할 수 있습니다. 이미지, 카테고리 등을 지정하여 유연하게 게시글을 생성할 수 있습니다.

**주요 기능:**
- 12개 테스트 계정 지원 (apple, banana, cherry, durian, elderberry, fig, grape, honeydew, jackfruit, kiwi, lemon, mango)
- 25개 이상의 카테고리 지원 (커뮤니티, 장터, 뉴스, 부동산, 구인구직)
- 자동 로그인 및 세션 쿠키 관리
- 이미지 자동 첨부 (picsum.photos)
- 배치 처리 지원 (한 번에 최대 50개까지 생성)
- **bash 기본 명령어만 사용** (외부 패키지에 의존하지 않음)

**사용법:**

```bash
# 도움말 표시 (모든 옵션 및 사용 가능한 카테고리 확인)
./create_posts.sh --help

# 기본 사용 (banana 계정으로 3개 게시글 생성, 랜덤 카테고리)
./create_posts.sh

# 프로덕션 서버에서 5개 게시글 생성 (discussion 카테고리)
./create_posts.sh --count 5 --category discussion --api-url https://sonub.com/api.php

# apple 계정으로 10개 게시글 생성 (qna 카테고리)
./create_posts.sh --count 10 --user apple --category qna

# 로컬 환경에서 cherry 계정으로 discussion 카테고리 3개 게시글 생성
./create_posts.sh --user cherry --category discussion --api-url https://local.sonub.com/api.php

# 도움말 표시 (모든 카테고리 목록 확인)
./create_posts.sh --help
```

**옵션:**

| 옵션 | 설명 | 기본값 | 예제 |
|------|------|--------|------|
| `--count N` | 생성할 게시글 수 (범위: 1-50) | 3 | `--count 10` |
| `--user NAME` | 테스트 계정명 | banana | `--user apple` |
| `--category CAT` | 카테고리 지정 (옵션) | 랜덤 | `--category discussion` |
| `--api-url URL` | API URL | https://sonub.com/api.php | `--api-url https://local.sonub.com/api.php` |
| `-h, --help` | 도움말 및 모든 카테고리 목록 표시 | - | `--help` |

**사용 가능한 카테고리:**

`--help` 명령으로 모든 사용 가능한 카테고리를 확인할 수 있습니다:

```bash
./create_posts.sh --help
```

**커뮤니티 (community):**
- `discussion` (자유토론)
- `qna` (질문과답변)
- `story` (나의 이야기)
- `relationships` (관계)
- `fitness` (운동)
- `beauty` (뷰티)
- `cooking` (요리)
- `pets` (반려동물)
- `parenting` (육아)

**장터 (buyandsell):**
- `electronics` (전자제품)
- `fashion` (패션)
- `furniture` (가구)
- `books` (책)
- `sports-equipment` (스포츠용품)
- `vehicles` (차량)
- `real-estate` (부동산)

**뉴스 (news):**
- `technology` (기술)
- `business` (비즈니스)
- `ai` (인공지능)
- `movies` (영화)
- `drama` (드라마)
- `music` (음악)

**부동산 (realestate):**
- `buy` (구매)
- `sell` (판매)
- `rent` (임대)

**구인구직 (jobs):**
- `full-time` (전일제)
- `part-time` (시간제)
- `freelance` (프리랜서)

**테스트 계정 매핑:**

| 계정명 | 전화번호 | 로그인 |
|--------|---------|--------|
| apple | +11234567890 | `apple@test.com:12345a,*` |
| banana | +11234567891 | `banana@test.com:12345a,*` |
| cherry | +11234567892 | `cherry@test.com:12345a,*` |
| durian | +11234567893 | `durian@test.com:12345a,*` |
| elderberry | +11234567894 | `elderberry@test.com:12345a,*` |
| fig | +11234567895 | `fig@test.com:12345a,*` |
| grape | +11234567896 | `grape@test.com:12345a,*` |
| honeydew | +11234567897 | `honeydew@test.com:12345a,*` |
| jackfruit | +11234567898 | `jackfruit@test.com:12345a,*` |
| kiwi | +11234567899 | `kiwi@test.com:12345a,*` |
| lemon | +11234567900 | `lemon@test.com:12345a,*` |
| mango | +11234567901 | `mango@test.com:12345a,*` |

**실행 예제:**

```bash
# 프로덕션 서버에 "바나나 챠챠 {n}" 형식 게시글 5개 생성
./create_posts.sh --count 5 --api-url https://sonub.com/api.php

# 로컬 환경에서 기본 설정으로 3개 게시글 생성
./create_posts.sh --api-url https://local.sonub.com/api.php

# apple 계정으로 프로덕션 서버에 20개 게시글 생성
./create_posts.sh --count 20 --user apple --api-url https://sonub.com/api.php
```

**응답 예제:**

```
==========================================
Create Multiple Posts - Sonub API Script
==========================================

Configuration:
  API URL: https://sonub.com/api.php
  Posts to create: 5
  Test user: banana
  Phone: +11234567891

Step 1: Logging in with test account...

✓ Login successful!
  User ID: 101
  Name: Banana

Step 2: Creating posts...

  ✓ Post #1 created (ID: 97, Category: discussion, Images: 4)
  ✓ Post #2 created (ID: 98, Category: qna, Images: 2)
  ✓ Post #3 created (ID: 99, Category: discussion, Images: 7)
  ✓ Post #4 created (ID: 100, Category: qna, Images: 3)
  ✓ Post #5 created (ID: 101, Category: discussion, Images: 5)

==========================================
Results
==========================================

Total requests: 5
Successful: 5
Failed: 0

Done!
```

---

### create_post() API 함수 상세 가이드

**함수명**: `create_post`

**설명**: 새로운 게시글을 생성합니다. 로그인된 사용자만 사용 가능하며, 자동으로 현재 사용자를 게시글 작성자로 설정합니다.

**필수 파라미터:**

| 파라미터 | 타입 | 설명 |
|---------|------|------|
| `func` | string | API 함수명 (`create_post` 고정) |
| `title` | string | 게시글 제목 (필수) |
| `content` | string | 게시글 내용 (필수) |
| `category` | string | 카테고리 (필수): `discussion`, `qna`, `my-wall` |
| `visibility` | string | 공개 범위 (필수): `public`, `friends`, `private` |

**선택 파라미터:**

| 파라미터 | 타입 | 설명 |
|---------|------|------|
| `files` | string | 파일 URL (쉼표로 구분) |
| `tags` | string | 태그 (쉼표로 구분) |

**Bash를 통한 직접 호출 예제:**

```bash
#!/bin/bash

API_URL="https://sonub.com/api.php"
COOKIE_JAR=$(mktemp)

# Step 1: 테스트 계정으로 로그인
LOGIN_JSON=$(jq -n \
  --arg func "login_with_firebase" \
  --arg firebase_uid "banana" \
  --arg phone_number "+11234567891" \
  '{func: $func, firebase_uid: $firebase_uid, phone_number: $phone_number}')

curl -s -k -c "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$LOGIN_JSON"

echo "✓ 로그인 완료"

# Step 2: 게시글 생성
TITLE="새로운 토론 주제"
CONTENT="이것은 새로운 게시글입니다.\n\n내용을 자유롭게 작성할 수 있습니다."

POST_JSON=$(jq -n \
  --arg func "create_post" \
  --arg title "$TITLE" \
  --arg content "$CONTENT" \
  --arg category "discussion" \
  --arg visibility "public" \
  '{func: $func, title: $title, content: $content, category: $category, visibility: $visibility}')

RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$POST_JSON")

POST_ID=$(echo "$RESPONSE" | jq -r '.id')
echo "✓ 게시글 생성 완료 (ID: $POST_ID)"

# 정리
rm -f "$COOKIE_JAR"
```

**JavaScript를 통한 호출 예제:**

```javascript
// 1. Firebase 로그인
const user = await firebase.auth().signInWithEmailAndPassword(
  'banana@test.com',
  '12345a'
);

// 2. 게시글 생성 (로그인 상태)
const result = await func('create_post', {
  title: '새로운 토론 주제',
  content: '이것은 새로운 게시글입니다.\n\n내용을 자유롭게 작성할 수 있습니다.',
  category: 'discussion',
  visibility: 'public'
});

console.log('게시글 생성 완료:', result.id);
```

**이미지 첨부 예제:**

```bash
# picsum.photos에서 랜덤 이미지 3개 첨부
IMAGE_URLS="https://picsum.photos/400/300?random=1,https://picsum.photos/400/300?random=2,https://picsum.photos/400/300?random=3"

POST_JSON=$(jq -n \
  --arg func "create_post" \
  --arg title "이미지가 있는 게시글" \
  --arg content "멋진 이미지들이 포함된 게시글입니다." \
  --arg category "discussion" \
  --arg visibility "public" \
  --arg files "$IMAGE_URLS" \
  '{func: $func, title: $title, content: $content, category: $category, visibility: $visibility, files: $files}')

curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$POST_JSON"
```

**에러 처리 예제:**

```bash
POST_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$POST_JSON")

# 에러 확인
if echo "$POST_RESPONSE" | grep -q "error_code"; then
  ERROR_CODE=$(echo "$POST_RESPONSE" | jq -r '.error_code')
  ERROR_MSG=$(echo "$POST_RESPONSE" | jq -r '.error_message')
  echo "❌ 게시글 생성 실패"
  echo "   에러: $ERROR_CODE - $ERROR_MSG"
  exit 1
fi

# 성공
POST_ID=$(echo "$POST_RESPONSE" | jq -r '.id')
echo "✓ 게시글 생성 완료 (ID: $POST_ID)"
```

**일반적인 에러 코드:**

| 에러 코드 | 설명 | 해결 방법 |
|---------|------|---------|
| `input-title-empty` | 제목이 비어있음 | title 파라미터 확인 |
| `input-content-empty` | 내용이 비어있음 | content 파라미터 확인 |
| `input-category-empty` | 카테고리가 지정되지 않음 | category 파라미터 지정 |
| `input-visibility-empty` | 공개 범위가 지정되지 않음 | visibility 파라미터 지정 |
| `category-not-found` | 유효하지 않은 카테고리 | 유효한 카테고리 사용 (discussion, qna, my-wall) |
| `not-logged-in` | 로그인하지 않음 | 먼저 login_with_firebase로 로그인 |

---

### list_posts.sh - 게시글 목록 조회

**위치**: `.claude/skills/sonub-api/scripts/list_posts.sh`

**설명**: `list_posts()` API 함수를 호출하여 게시글 목록을 조회합니다. 카테고리, 사용자, 공개 범위 필터를 지원합니다.

**사용법:**

```bash
# 기본 사용 (최신 게시글 10개 조회)
./list_posts.sh

# 'discussion' 카테고리 게시글 20개 조회
./list_posts.sh --category discussion --limit 20

# 특정 사용자(ID: 5)의 게시글 조회
./list_posts.sh --user-id 5 --limit 10

# 공개(public) 게시글만 조회
./list_posts.sh --visibility public --limit 15

# 2페이지 조회
./list_posts.sh --category qna --page 2 --limit 10

# 정렬 순서 지정 (오래된 순)
./list_posts.sh --order "created_at ASC" --limit 20

# 'my-wall' 카테고리의 친구 공개 게시글
./list_posts.sh --category my-wall --visibility friends

# 프로덕션 서버에서 조회
./list_posts.sh --url https://sonub.com/api.php --category discussion

# 도움말 표시
./list_posts.sh --help
```

**옵션:**

| 옵션 | 설명 | 기본값 |
|------|------|--------|
| `--category STR` | 카테고리 필터 (예: discussion, qna, my-wall) | (없음) |
| `--user-id N` | 특정 사용자의 게시글만 조회 | (없음) |
| `--visibility STR` | 공개 범위 필터 (public, friends, private) | (없음) |
| `--limit N` | 페이지당 게시글 수 (최대: 100) | 10 |
| `--page N` | 페이지 번호 | 1 |
| `--order STR` | 정렬 순서 | "created_at DESC" |
| `--url URL` | API URL | https://local.sonub.com/api.php |
| `-h, --help` | 도움말 표시 | - |

**응답 예제:**

```json
{
  "posts": [
    {
      "id": 123,
      "user_id": 5,
      "category": "discussion",
      "title": "Vue.js 3 질문",
      "content": "Vue.js 3에서 Composition API 사용법을...",
      "visibility": "public",
      "created_at": 1640000000,
      "updated_at": 1640000000,
      "user": {
        "id": 5,
        "name": "홍길동",
        "profile_photo_url": "https://..."
      }
    }
  ],
  "total": 250,
  "page": 1,
  "limit": 10,
  "total_pages": 25,
  "func": "list_posts"
}
```

**실용 예제:**

```bash
# 'discussion' 카테고리의 최신 게시글 30개
./list_posts.sh --category discussion --limit 30

# 사용자 ID 10의 모든 게시글
./list_posts.sh --user-id 10 --limit 50

# 'qna' 카테고리의 공개 게시글만
./list_posts.sh --category qna --visibility public --limit 20

# 'my-wall' 카테고리의 2페이지
./list_posts.sh --category my-wall --page 2

# 오래된 순으로 게시글 조회
./list_posts.sh --category discussion --order "created_at ASC"
```

---

### 스크립트 개발 가이드

**새 스크립트 추가하기:**

1. `.claude/skills/sonub-api/scripts/` 디렉토리에 새 `.sh` 파일 생성
2. 기존 스크립트(`list_users.sh` 또는 `list_posts.sh`)를 템플릿으로 사용
3. API 함수에 맞게 옵션 및 파라미터 수정
4. 실행 권한 부여: `chmod +x script_name.sh`
5. `SKILL.md`에 사용법 문서 추가

**스크립트 구조:**

```bash
#!/bin/bash
# 1. 주석 및 사용법 설명
# 2. 기본값 설정
# 3. 도움말 함수
# 4. 인자 파싱 (while 루프)
# 5. JSON 페이로드 생성
# 6. curl을 통한 API 호출
# 7. jq를 통한 JSON 포맷팅
```

**cURL 옵션 설명:**

- `-X POST`: HTTP POST 메서드 사용
- `-H "Content-Type: application/json"`: JSON 콘텐츠 타입 헤더
- `-d "$JSON_PAYLOAD"`: JSON 페이로드 전송
- `-s`: Silent 모드 (진행 상황 숨기기)
- `-k`: SSL 인증서 검증 무시 (로컬 개발 환경용)

**jq 사용법:**

```bash
# JSON 응답 포맷팅
curl ... | jq '.'

# 특정 필드만 추출
curl ... | jq '.users[].name'

# 조건부 필터링
curl ... | jq '.users[] | select(.gender == "F")'
```

---

## API 프로토콜 상세 가이드

**API 동작 방식, 함수 호출 방법, 입출력 형식, 에러 처리** 등 API 프로토콜에 대한 상세한 내용은 별도 문서를 참조하세요:

📖 **[Sonub API 프로토콜 가이드](sonub-api-protocol.md)**

**주요 내용:**
- API 동작 방식 (클라이언트 → api.php → LIB 함수)
- api.php 상세 동작 방식 (동적 함수 호출, 응답 처리, 예외 처리)
- API 엔드포인트 (GET/POST/JSON)
- func() 헬퍼 함수 (권장 API 호출 방법)
- API 호출 예제 (cURL, Fetch API, JavaScript)
- 에러 처리 (에러 응답 형식, 에러 코드, 모범 사례)

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

- [Sonub API 프로토콜 가이드](sonub-api-protocol.md)
- [Sonub API 엔드포인트 목록](sonub-api-endpoints.md)
- [코딩 가이드라인](../../docs/coding-guideline.md)
- [데이터베이스 가이드](../../docs/database.md)
- [번역 가이드](../../docs/translation.md)
