---
name: sonub-api
description: |
  API First 설계 철학을 따르는 Sonub.Com 웹사이트의 API 엔드포인트, 요청/응답 형식, 인증 방법, 오류 처리 등을 제공하며, |
  사용자 정보, 게시글, 댓글 등을 조회, 검색, 생성, 수정, 삭제할 때 사용하고, |
  테스트 계정 자동 생성(create_test_users), 테스트 계정으로 로그인, 테스트 계정으로 글 생성 등 테스트 계정으로 작업할 때 활용하며, |
  글 생성(create_post), 댓글 생성(create_comment) 등 주요 기능을 구현하는 예제 코드와 가이드를 포함하고, |
  명령줄 스크립트(create_posts.sh, list_users.sh, list_posts.sh)를 통해 API를 간편하게 호출할 수 있으며, |
  배치 처리, 페이지네이션, 필터링, 이미지 첨부 등 고급 기능을 지원하고, |
  테스트 계정 자동 생성 및 관리, 세션 관리, 에러 처리 방법 등 실무 예제를 제공합니다.
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
    - [인증 (Authentication) - 2개 함수](#인증-authentication---2개-함수)
  - [Scripts - 명령줄 API 호출 도구](#scripts---명령줄-api-호출-도구)
    - [빠른 시작](#빠른-시작)
    - [상세 문서](#상세-문서)
  - [실전 예제 (Practical Examples)](#실전-예제-practical-examples)
    - [내가 쓴 글 삭제하기](#내가-쓴-글-삭제하기)
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

### 인증 (Authentication) - 2개 함수

**Firebase Authentication 통합 로그인 및 테스트 계정 관리**

- **`login_with_firebase`**: Firebase ID Token으로 로그인. 사용자가 없으면 자동 생성. PHP 세션 쿠키 설정.
- **`create_test_users`**: 테스트용 계정들(apple, banana, cherry 등 12개)을 데이터베이스에 자동으로 생성. 개발 및 프로덕션 환경에서 사용.

**빠른 참고:**
- 테스트 계정 자동 생성: `curl "https://sonub.com/api.php?func=create_test_users"`
- 테스트 계정 로그인: `banana@test.com:12345a,*`
- 테스트 계정으로 게시글 생성: `./create_posts.sh --user banana --count 10`

📖 [인증 API 상세 문서](sonub-api-endpoints.md#인증-authentication)

---

## Scripts - 명령줄 API 호출 도구

Sonub API를 명령줄에서 간편하게 테스트하고 호출할 수 있는 Bash 스크립트를 제공합니다.

### 빠른 시작

모든 스크립트는 `.claude/skills/sonub-api/scripts/` 디렉토리에 위치합니다.

**주요 스크립트:**
- **`list_users.sh`**: 사용자 목록 조회 (페이지네이션, 필터 지원)
- **`create_posts.sh`**: 게시글 대량 생성 (12개 테스트 계정, 25개+ 카테고리)
- **`list_posts.sh`**: 게시글 목록 조회 (카테고리, 사용자, 공개 범위 필터)

**필수 도구:**
```bash
# curl (일반적으로 기본 설치됨)
# jq 설치 (선택사항)
brew install jq  # macOS
sudo apt-get install jq  # Ubuntu/Debian
```

**예제:**
```bash
# 사용자 목록 조회
./list_users.sh --limit 20

# 게시글 생성 (banana 계정으로 5개)
./create_posts.sh --count 5 --user banana

# 게시글 목록 조회
./list_posts.sh --category discussion --limit 30
```

### 상세 문서

**📖 모든 스크립트의 상세한 사용 방법, 옵션, 예제, 그리고 개발 가이드는 다음 문서를 참조하세요:**

**👉 [Sonub Scripts 상세 가이드](sonub-scripts.md)**

해당 문서에는 다음 내용이 포함되어 있습니다:

1. **각 스크립트의 상세 사용 방법**
   - 모든 옵션 설명 및 예제
   - 카테고리 목록 (25개+)
   - 테스트 계정 매핑

2. **API 함수 직접 호출 가이드**
   - Bash, JavaScript 예제
   - 이미지 첨부, 에러 처리

3. **스크립트 개발 및 확장 방법**
   - 새 스크립트 작성 가이드
   - cURL, jq 사용법
   - 문제 해결

---

## 실전 예제 (Practical Examples)

### 내가 쓴 글 삭제하기

**목표:** 로그인한 사용자가 자신이 작성한 게시글을 삭제합니다.

**필요한 API 함수:**
1. `login_with_firebase` - 사용자 로그인
2. `list_posts` - 내가 작성한 글 목록 조회
3. `delete_post` - 게시글 삭제

#### 단계별 프로세스

**1단계: 로그인하여 세션 정보 얻기**

로그인하면 다음 정보를 얻을 수 있습니다:
- **회원 번호 (ID)**: 데이터베이스의 사용자를 식별하는 고유 번호
- **`sonub_session_id` 쿠키**: 인증된 세션을 나타내는 쿠키 값

```bash
# 로그인 요청
curl -s -k -c cookies.txt -X POST "https://local.sonub.com/api.php" \
  -H "Content-Type: application/json" \
  -d '{
    "func": "login_with_firebase",
    "firebase_uid": "banana",
    "phone_number": "+11234567891"
  }'

# 응답 예제:
# {
#   "id": 31,
#   "first_name": "Banana",
#   "email": "banana@example.com",
#   "func": "login_with_firebase"
# }

# 쿠키가 cookies.txt 파일에 저장됨
```

**2단계: 내가 작성한 글 목록 조회**

로그인 후 쿠키를 사용하여 내가 작성한 글을 조회합니다:

```bash
# 내가 작성한 게시글 목록 조회
curl -s -k -b cookies.txt -X POST "https://local.sonub.com/api.php" \
  -H "Content-Type: application/json" \
  -d '{
    "func": "list_posts",
    "limit": 10
  }'

# 응답 예제:
# {
#   "posts": [
#     {
#       "id": 999157,
#       "title": "게시글 5 - 2025-10-27 15:38:48",
#       "content": "자동 생성된 테스트 게시글입니다.",
#       "category": "discussion",
#       "user_id": 31,
#       "author": {
#         "first_name": "Banana",
#         "photo_url": "/var/uploads/31/바나나-1.jpg"
#       },
#       ...
#     },
#     ...
#   ],
#   "isEmpty": false,
#   "isLastPage": true,
#   "func": "list_posts"
# }
```

**3단계: 글 삭제하기**

조회한 게시글의 ID를 사용하여 글을 삭제합니다:

```bash
# 단일 게시글 삭제
curl -s -k -b cookies.txt -X POST "https://local.sonub.com/api.php" \
  -H "Content-Type: application/json" \
  -d '{
    "func": "delete_post",
    "id": 999157
  }'

# 응답 예제:
# {
#   "success": true,
#   "message": "게시글이 삭제되었습니다.",
#   "func": "delete_post"
# }
```

#### 완전한 자동화 스크립트 예제

여러 글을 한 번에 삭제하는 스크립트:

```bash
#!/bin/bash

API_URL="https://local.sonub.com/api.php"
COOKIE_JAR="cookies.txt"
TEST_USER="banana"
TEST_PHONE="+11234567891"

# 1단계: 로그인
echo "1단계: 로그인 중..."
LOGIN_RESPONSE=$(curl -s -k -c "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "{\"func\": \"login_with_firebase\", \"firebase_uid\": \"$TEST_USER\", \"phone_number\": \"$TEST_PHONE\"}")

USER_ID=$(echo "$LOGIN_RESPONSE" | grep -o '"id":[0-9]*' | cut -d: -f2)
echo "✓ 로그인 성공! (사용자 ID: $USER_ID)"

# 2단계: 내가 작성한 글 목록 조회
echo "2단계: 내가 작성한 글 목록 조회 중..."
LIST_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d '{"func": "list_posts", "limit": 10}')

# 게시글 ID 추출 (grep + cut 사용)
POST_IDS=$(echo "$LIST_RESPONSE" | grep -o '"id":[0-9]*' | cut -d: -f2 | head -10)

echo "발견된 게시글 ID들: $POST_IDS"

# 3단계: 각 글 삭제
echo "3단계: 글 삭제 중..."
for POST_ID in $POST_IDS; do
  DELETE_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
    -H "Content-Type: application/json" \
    -d "{\"func\": \"delete_post\", \"id\": $POST_ID}")

  if echo "$DELETE_RESPONSE" | grep -q '"success":true'; then
    echo "  ✓ 게시글 #$POST_ID 삭제 완료"
  else
    echo "  ✗ 게시글 #$POST_ID 삭제 실패"
  fi
done

# 정리
rm -f "$COOKIE_JAR"
echo "완료!"
```

#### 중요 사항

⚠️ **주의:**
- `delete_post()` 함수는 **본인이 작성한 글만 삭제 가능**합니다
- 다른 사용자가 작성한 글은 삭제할 수 없습니다 (권한 오류 발생)
- 한 번 삭제된 글은 복구할 수 없으므로 신중하게 삭제하세요
- 게시글을 삭제하면 관련된 댓글도 함께 삭제됩니다

✅ **권장 사항:**
- 프로덕션 환경에서는 실수 방지를 위해 삭제 전 확인 절차를 추가하세요
- 중요한 글을 삭제하기 전에 백업하는 것이 좋습니다
- 에러 처리를 충분히 구현하여 실패한 삭제 작업을 감지하세요

#### 관련 API 함수

- 📖 [`delete_post()`](sonub-api-endpoints.md#delete_post) - 게시글 삭제 상세 문서
- 📖 [`list_posts()`](sonub-api-endpoints.md#list_posts) - 게시글 목록 조회 상세 문서
- 📖 [`login_with_firebase()`](sonub-api-endpoints.md#login_with_firebase) - 로그인 상세 문서

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
