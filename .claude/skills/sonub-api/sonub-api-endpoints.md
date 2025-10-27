# Sonub API 엔드포인트 목록

이 문서는 Sonub.Com에서 사용 가능한 모든 API 함수의 상세 사양을 제공합니다.

## 목차

- [개요](#개요)
- [게시글 관리 (Post Management)](#게시글-관리-post-management)
- [댓글 관리 (Comment Management)](#댓글-관리-comment-management)
- [사용자 관리 (User Management)](#사용자-관리-user-management)
- [친구 & 피드 (Friend & Feed)](#친구--피드-friend--feed)
- [파일 작업 (File Operations)](#파일-작업-file-operations)
- [언어 설정 (Language Settings)](#언어-설정-language-settings)
- [인증 (Authentication)](#인증-authentication)

---

## 개요

**API 함수 호출 방법:**

모든 API 함수는 `function xxx(array $input)` 시그니처를 가지며, 다음과 같이 호출할 수 있습니다:

```javascript
// JavaScript에서 func() 헬퍼 함수 사용 (권장)
const result = await func('function_name', { param1: 'value1', param2: 'value2' });
```

**API 프로토콜 상세:**
- API 동작 방식, 에러 처리, 입출력 형식 등은 [Sonub API 프로토콜 가이드](sonub-api-protocol.md) 참조

---

## 게시글 관리 (Post Management)

게시글 생성, 조회, 수정, 삭제 등 게시글 관리 기능을 제공합니다.

### get_post

**설명:** 게시글 ID로 단일 게시글을 조회합니다.

**함수 시그니처:**
```php
function get_post(array $input): ?PostModel
```

**파라미터:**
- `id` (int, required): 조회할 게시글 ID

**반환값:**
- `PostModel|null`: 게시글 객체 또는 null (게시글이 없는 경우)
- PostModel 필드: `id`, `user_id`, `category`, `title`, `content`, `visibility`, `created_at`, `updated_at`, `files`, `user` (작성자 정보)

**사용 예제:**

**JavaScript:**
```javascript
// 게시글 ID 123 조회
const post = await func('get_post', { id: 123 });
console.log(post.title);
console.log(post.user.name); // 작성자 이름
```

**PHP:**
```php
$post = get_post(['id' => 123]);
echo $post->title;
```

**에러:**
- `post-not-found`: 게시글을 찾을 수 없음

**관련 함수:**
- `list_posts`: 게시글 목록 조회
- `search_posts`: 게시글 검색

---

### create_post

**설명:** 새로운 게시글을 생성합니다. 인증 필요.

**함수 시그니처:**
```php
function create_post(array $input): PostModel
```

**파라미터:**
- `category` (string, required): 게시글 카테고리 (예: 'discussion', 'qna', 'my-wall')
- `title` (string, optional): 게시글 제목
- `content` (string, optional): 게시글 내용
- `visibility` (string, optional): 공개 범위 ('public', 'friends', 'private', 기본값: 'public')
- `files` (array, optional): 첨부 파일 ID 배열

**반환값:**
- `PostModel`: 생성된 게시글 객체

**사용 예제:**

**JavaScript:**
```javascript
// 공개 게시글 생성
const post = await func('create_post', {
    category: 'discussion',
    title: '새 게시글',
    content: '게시글 내용입니다',
    visibility: 'public',
    auth: true  // Firebase 인증 필요
});

// 친구 공개 게시글 (파일 첨부)
const post = await func('create_post', {
    category: 'my-wall',
    content: '친구들에게만 보이는 글',
    visibility: 'friends',
    files: [101, 102],  // 파일 ID 배열
    auth: true
});
```

**특별 기능:**
- **Fan-out on Write**: `visibility`가 'friends'인 경우, 모든 친구의 피드에 자동으로 전파됨
- **파일 첨부**: `files` 배열에 파일 ID 전달 시 게시글과 연결

**에러:**
- `not-logged-in`: 로그인 필요
- `category-required`: 카테고리 필수
- `invalid-visibility`: 잘못된 공개 범위

**관련 함수:**
- `update_post`: 게시글 수정
- `delete_post`: 게시글 삭제

---

### update_post

**설명:** 기존 게시글을 수정합니다. 본인의 게시글만 수정 가능.

**함수 시그니처:**
```php
function update_post(array $input): PostModel
```

**파라미터:**
- `id` (int, required): 수정할 게시글 ID
- `title` (string, optional): 새 제목
- `content` (string, optional): 새 내용
- `visibility` (string, optional): 새 공개 범위
- `category` (string, optional): 새 카테고리

**반환값:**
- `PostModel`: 수정된 게시글 객체

**사용 예제:**

**JavaScript:**
```javascript
// 게시글 제목 및 내용 수정
const post = await func('update_post', {
    id: 123,
    title: '수정된 제목',
    content: '수정된 내용',
    auth: true
});
```

**에러:**
- `not-logged-in`: 로그인 필요
- `post-not-found`: 게시글을 찾을 수 없음
- `not-authorized`: 본인의 게시글만 수정 가능

**관련 함수:**
- `create_post`: 게시글 생성
- `delete_post`: 게시글 삭제

---

### list_posts

**설명:** 게시글 목록을 조회합니다. 페이지네이션 및 필터링 지원.

**함수 시그니처:**
```php
function list_posts(array $filters = []): PostListModel
```

**파라미터:**
- `category` (string, optional): 카테고리 필터
- `user_id` (int, optional): 특정 사용자의 게시글만 조회
- `visibility` (string, optional): 공개 범위 필터
- `limit` (int, optional): 페이지당 게시글 수 (기본값: 10, 최대: 100)
- `page` (int, optional): 페이지 번호 (기본값: 1)
- `order` (string, optional): 정렬 순서 ('created_at DESC', 'created_at ASC', 기본값: 'created_at DESC')

**반환값:**
- `PostListModel`: 게시글 목록 및 페이지네이션 정보
  - `posts` (array): 게시글 배열
  - `total` (int): 전체 게시글 수
  - `page` (int): 현재 페이지
  - `limit` (int): 페이지당 게시글 수
  - `total_pages` (int): 전체 페이지 수

**사용 예제:**

**JavaScript:**
```javascript
// 'discussion' 카테고리 게시글 10개 조회
const result = await func('list_posts', {
    category: 'discussion',
    limit: 10,
    page: 1
});
console.log(result.posts);      // 게시글 배열
console.log(result.total);      // 전체 게시글 수
console.log(result.total_pages); // 전체 페이지 수

// 특정 사용자의 게시글만 조회
const userPosts = await func('list_posts', {
    user_id: 5,
    limit: 20
});
```

**에러:**
- 에러 없음 (빈 배열 반환 가능)

**관련 함수:**
- `search_posts`: 게시글 검색
- `get_post`: 단일 게시글 조회

---

### search_posts

**설명:** 제목 또는 내용으로 게시글을 검색합니다.

**함수 시그니처:**
```php
function search_posts(array $filters): PostListModel
```

**파라미터:**
- `query` (string, required): 검색 키워드
- `category` (string, optional): 카테고리 필터
- `limit` (int, optional): 페이지당 게시글 수 (기본값: 10)
- `page` (int, optional): 페이지 번호 (기본값: 1)

**반환값:**
- `PostListModel`: 검색된 게시글 목록

**사용 예제:**

**JavaScript:**
```javascript
// 'Vue.js' 키워드 검색
const result = await func('search_posts', {
    query: 'Vue.js',
    category: 'discussion',
    limit: 10
});

console.log(result.posts);
```

**에러:**
- `query-required`: 검색 키워드 필수

**관련 함수:**
- `list_posts`: 게시글 목록 조회

---

### delete_post

**설명:** 게시글을 삭제합니다. 본인의 게시글만 삭제 가능.

**함수 시그니처:**
```php
function delete_post(array $params): PostModel
```

**파라미터:**
- `id` (int, required): 삭제할 게시글 ID

**반환값:**
- `PostModel`: 삭제된 게시글 객체

**사용 예제:**

**JavaScript:**
```javascript
// 게시글 삭제
const deletedPost = await func('delete_post', {
    id: 123,
    auth: true
});
```

**특별 기능:**
- 게시글 삭제 시 관련 피드 항목도 자동 삭제
- 게시글에 첨부된 파일은 자동 삭제되지 않음 (별도 삭제 필요)

**에러:**
- `not-logged-in`: 로그인 필요
- `post-not-found`: 게시글을 찾을 수 없음
- `not-authorized`: 본인의 게시글만 삭제 가능

**관련 함수:**
- `delete_file_from_post`: 게시글에서 파일 삭제

---

### delete_file_from_post

**설명:** 게시글에서 특정 파일을 제거합니다.

**함수 시그니처:**
```php
function delete_file_from_post(array $input): PostModel
```

**파라미터:**
- `post_id` (int, required): 게시글 ID
- `file_id` (int, required): 삭제할 파일 ID

**반환값:**
- `PostModel`: 업데이트된 게시글 객체

**사용 예제:**

**JavaScript:**
```javascript
// 게시글에서 파일 제거
const post = await func('delete_file_from_post', {
    post_id: 123,
    file_id: 456,
    auth: true
});
```

**에러:**
- `not-logged-in`: 로그인 필요
- `post-not-found`: 게시글을 찾을 수 없음
- `not-authorized`: 본인의 게시글만 수정 가능

**관련 함수:**
- `delete_post`: 게시글 삭제
- `file_delete`: 파일 완전 삭제

---

## 댓글 관리 (Comment Management)

게시글에 대한 댓글 생성, 조회, 수정, 삭제 기능을 제공합니다. 중첩 댓글(대댓글) 지원.

### get_comment

**설명:** 댓글 ID로 단일 댓글을 조회합니다.

**함수 시그니처:**
```php
function get_comment(array $input): CommentModel|null
```

**파라미터:**
- `id` (int, required): 조회할 댓글 ID

**반환값:**
- `CommentModel|null`: 댓글 객체 또는 null
- CommentModel 필드: `id`, `post_id`, `user_id`, `parent_id`, `content`, `depth`, `created_at`, `updated_at`, `user`

**사용 예제:**

**JavaScript:**
```javascript
// 댓글 조회
const comment = await func('get_comment', { id: 789 });
console.log(comment.content);
console.log(comment.user.name); // 작성자 이름
```

**에러:**
- `comment-not-found`: 댓글을 찾을 수 없음

**관련 함수:**
- `get_comments`: 게시글의 모든 댓글 조회

---

### get_comments

**설명:** 특정 게시글의 모든 댓글을 조회합니다. 중첩 댓글 포함.

**함수 시그니처:**
```php
function get_comments(array $input): array
```

**파라미터:**
- `post_id` (int, required): 게시글 ID
- `limit` (int, optional): 최대 댓글 수 (기본값: 100)
- `order` (string, optional): 정렬 순서 (기본값: 'created_at ASC')

**반환값:**
- `array`: 댓글 배열 (CommentModel 객체들)

**사용 예제:**

**JavaScript:**
```javascript
// 게시글의 모든 댓글 조회
const comments = await func('get_comments', {
    post_id: 123,
    limit: 50
});

comments.forEach(comment => {
    console.log(`${comment.user.name}: ${comment.content}`);
    if (comment.depth > 0) {
        console.log('  -> 대댓글');
    }
});
```

**에러:**
- `post-id-required`: 게시글 ID 필수

**관련 함수:**
- `create_comment`: 댓글 생성
- `get_comment`: 단일 댓글 조회

---

### create_comment

**설명:** 새로운 댓글을 생성합니다. 대댓글(중첩 댓글) 지원. 인증 필요.

**함수 시그니처:**
```php
function create_comment(array $input): CommentModel
```

**파라미터:**
- `post_id` (int, required): 게시글 ID
- `content` (string, required): 댓글 내용
- `parent_id` (int, optional): 부모 댓글 ID (대댓글인 경우)

**반환값:**
- `CommentModel`: 생성된 댓글 객체

**사용 예제:**

**JavaScript:**
```javascript
// 일반 댓글 생성
const comment = await func('create_comment', {
    post_id: 123,
    content: '좋은 게시글이네요!',
    auth: true
});

// 대댓글 생성
const reply = await func('create_comment', {
    post_id: 123,
    parent_id: 789,  // 부모 댓글 ID
    content: '감사합니다!',
    auth: true
});
```

**특별 기능:**
- **자동 depth 계산**: parent_id가 있으면 부모 댓글의 depth + 1로 설정
- **게시글 comment_count 업데이트**: 댓글 생성 시 게시글의 댓글 수 자동 증가

**에러:**
- `not-logged-in`: 로그인 필요
- `post-id-required`: 게시글 ID 필수
- `content-required`: 댓글 내용 필수
- `parent-comment-not-found`: 부모 댓글을 찾을 수 없음

**관련 함수:**
- `update_comment`: 댓글 수정
- `delete_comment`: 댓글 삭제

---

### update_comment

**설명:** 기존 댓글을 수정합니다. 본인의 댓글만 수정 가능.

**함수 시그니처:**
```php
function update_comment(array $input): CommentModel
```

**파라미터:**
- `id` (int, required): 수정할 댓글 ID
- `content` (string, required): 새 댓글 내용

**반환값:**
- `CommentModel`: 수정된 댓글 객체

**사용 예제:**

**JavaScript:**
```javascript
// 댓글 내용 수정
const comment = await func('update_comment', {
    id: 789,
    content: '수정된 댓글 내용',
    auth: true
});
```

**에러:**
- `not-logged-in`: 로그인 필요
- `comment-not-found`: 댓글을 찾을 수 없음
- `not-authorized`: 본인의 댓글만 수정 가능
- `content-required`: 댓글 내용 필수

**관련 함수:**
- `create_comment`: 댓글 생성
- `delete_comment`: 댓글 삭제

---

### delete_comment

**설명:** 댓글을 삭제합니다. 본인의 댓글만 삭제 가능.

**함수 시그니처:**
```php
function delete_comment(array $input): bool
```

**파라미터:**
- `id` (int, required): 삭제할 댓글 ID

**반환값:**
- `bool`: 삭제 성공 여부

**사용 예제:**

**JavaScript:**
```javascript
// 댓글 삭제
const success = await func('delete_comment', {
    id: 789,
    auth: true
});

if (success) {
    console.log('댓글이 삭제되었습니다');
}
```

**특별 기능:**
- **게시글 comment_count 업데이트**: 댓글 삭제 시 게시글의 댓글 수 자동 감소
- **자식 댓글 처리**: 대댓글이 있는 댓글 삭제 시 자식 댓글도 함께 삭제

**에러:**
- `not-logged-in`: 로그인 필요
- `comment-not-found`: 댓글을 찾을 수 없음
- `not-authorized`: 본인의 댓글만 삭제 가능

**관련 함수:**
- `create_comment`: 댓글 생성
- `update_comment`: 댓글 수정

---

## 사용자 관리 (User Management)

사용자 프로필 생성, 조회, 수정, 검색 기능을 제공합니다.

### create_user_record

**설명:** 새로운 사용자 레코드를 생성합니다. Firebase UID 기반.

**함수 시그니처:**
```php
function create_user_record(array $input): array
```

**파라미터:**
- `firebase_uid` (string, required): Firebase UID
- `name` (string, optional): 사용자 이름
- `email` (string, optional): 이메일 주소
- `phone_number` (string, optional): 전화번호
- `gender` (string, optional): 성별 ('M', 'F')
- `birthday` (string, optional): 생년월일 (YYYY-MM-DD)
- `profile_photo_url` (string, optional): 프로필 사진 URL

**반환값:**
- `array`: 생성된 사용자 데이터

**사용 예제:**

**JavaScript:**
```javascript
// Firebase 인증 후 사용자 레코드 생성
const user = await func('create_user_record', {
    firebase_uid: 'abc123',
    name: '홍길동',
    email: 'hong@example.com',
    phone_number: '01012345678'
});
```

**에러:**
- `firebase-uid-required`: Firebase UID 필수
- `user-already-exists`: 이미 존재하는 사용자

**관련 함수:**
- `login_with_firebase`: Firebase 로그인
- `update_user_profile`: 프로필 수정

---

### get_user

**설명:** 사용자 정보를 조회합니다.

**함수 시그니처:**
```php
function get_user(array $input): array
```

**파라미터:**
- `id` (int, optional): 사용자 ID
- `firebase_uid` (string, optional): Firebase UID
- 둘 중 하나는 필수

**반환값:**
- `array`: 사용자 데이터 (비밀번호 제외)

**사용 예제:**

**JavaScript:**
```javascript
// ID로 사용자 조회
const user = await func('get_user', { id: 123 });

// Firebase UID로 조회
const user = await func('get_user', { firebase_uid: 'abc123' });
```

**에러:**
- `user-not-found`: 사용자를 찾을 수 없음
- `id-or-firebase-uid-required`: ID 또는 Firebase UID 필수

**관련 함수:**
- `list_users`: 사용자 목록 조회
- `search_users`: 사용자 검색

---

### update_user_profile

**설명:** 사용자 프로필을 수정합니다. 관리자 또는 본인만 수정 가능.

**함수 시그니처:**
```php
function update_user_profile(array $input): array
```

**파라미터:**
- `id` (int, required): 수정할 사용자 ID
- `name` (string, optional): 새 이름
- `email` (string, optional): 새 이메일
- `phone_number` (string, optional): 새 전화번호
- `gender` (string, optional): 새 성별
- `birthday` (string, optional): 새 생년월일
- `profile_photo_url` (string, optional): 새 프로필 사진 URL

**반환값:**
- `array`: 수정된 사용자 데이터

**사용 예제:**

**JavaScript:**
```javascript
// 프로필 수정
const user = await func('update_user_profile', {
    id: 123,
    name: '새 이름',
    profile_photo_url: 'https://example.com/photo.jpg',
    auth: true
});
```

**에러:**
- `not-logged-in`: 로그인 필요
- `user-not-found`: 사용자를 찾을 수 없음
- `not-authorized`: 본인 또는 관리자만 수정 가능

**관련 함수:**
- `update_my_profile`: 내 프로필 수정
- `get_user`: 사용자 조회

---

### update_my_profile

**설명:** 로그인한 사용자의 프로필을 수정합니다.

**함수 시그니처:**
```php
function update_my_profile(array $input): array
```

**파라미터:**
- `name` (string, optional): 새 이름
- `email` (string, optional): 새 이메일
- `phone_number` (string, optional): 새 전화번호
- `gender` (string, optional): 새 성별
- `birthday` (string, optional): 새 생년월일
- `profile_photo_url` (string, optional): 새 프로필 사진 URL

**반환값:**
- `array`: 수정된 사용자 데이터

**사용 예제:**

**JavaScript:**
```javascript
// 내 프로필 수정 (ID 불필요)
const user = await func('update_my_profile', {
    name: '새 이름',
    birthday: '1990-01-01',
    auth: true
});
```

**에러:**
- `not-logged-in`: 로그인 필요

**관련 함수:**
- `update_user_profile`: 특정 사용자 프로필 수정

---

### list_users

**설명:** 사용자 목록을 조회합니다. 페이지네이션 및 필터링 지원.

**함수 시그니처:**
```php
function list_users(array $input): array
```

**파라미터:**
- `limit` (int, optional): 페이지당 사용자 수 (기본값: 10, 최대: 100)
- `page` (int, optional): 페이지 번호 (기본값: 1)
- `gender` (string, optional): 성별 필터 ('M', 'F')
- `age_min` (int, optional): 최소 나이
- `age_max` (int, optional): 최대 나이
- `order` (string, optional): 정렬 순서 (기본값: 'created_at DESC')

**반환값:**
- `array`: 사용자 목록 및 페이지네이션 정보
  - `users` (array): 사용자 배열
  - `total` (int): 전체 사용자 수
  - `page` (int): 현재 페이지
  - `limit` (int): 페이지당 사용자 수

**사용 예제:**

**JavaScript:**
```javascript
// 사용자 목록 조회
const result = await func('list_users', {
    limit: 20,
    page: 1
});

// 여성 사용자만 조회
const femaleUsers = await func('list_users', {
    gender: 'F',
    limit: 10
});

// 20-30세 사용자 조회
const youngUsers = await func('list_users', {
    age_min: 20,
    age_max: 30,
    limit: 50
});
```

**에러:**
- 에러 없음 (빈 배열 반환 가능)

**관련 함수:**
- `search_users`: 사용자 검색
- `get_user`: 단일 사용자 조회

---

### get_users

**설명:** 여러 사용자의 정보를 한 번에 조회합니다.

**함수 시그니처:**
```php
function get_users(array $input): array
```

**파라미터:**
- `ids` (array, required): 사용자 ID 배열

**반환값:**
- `array`: 사용자 배열

**사용 예제:**

**JavaScript:**
```javascript
// 여러 사용자 정보 조회
const users = await func('get_users', {
    ids: [1, 2, 3, 5, 10]
});

users.forEach(user => {
    console.log(user.name);
});
```

**에러:**
- `ids-required`: 사용자 ID 배열 필수

**관련 함수:**
- `get_user`: 단일 사용자 조회

---

### search_users

**설명:** 이름, 이메일, 전화번호로 사용자를 검색합니다.

**함수 시그니처:**
```php
function search_users(array $input): array
```

**파라미터:**
- `query` (string, required): 검색 키워드
- `limit` (int, optional): 최대 결과 수 (기본값: 10)
- `page` (int, optional): 페이지 번호 (기본값: 1)

**반환값:**
- `array`: 검색된 사용자 배열

**사용 예제:**

**JavaScript:**
```javascript
// 이름으로 사용자 검색
const users = await func('search_users', {
    query: '홍길동',
    limit: 10
});
```

**에러:**
- `query-required`: 검색 키워드 필수

**관련 함수:**
- `list_users`: 사용자 목록 조회

---

## 친구 & 피드 (Friend & Feed)

친구 관계 관리 및 친구 피드 시스템을 제공합니다.

### request_friend

**설명:** 친구 요청을 보냅니다. 인증 필요.

**함수 시그니처:**
```php
function request_friend(array $input): array
```

**파라미터:**
- `to` (int, required): 친구 요청을 받을 사용자 ID

**반환값:**
- `array`: 친구 요청 정보
  - `message` (string): 성공 메시지
  - `success` (bool): true

**사용 예제:**

**JavaScript:**
```javascript
// 사용자 ID 10에게 친구 요청
const result = await func('request_friend', {
    to: 10,
    auth: true
});

console.log(result.message); // "친구 요청을 보냈습니다"
```

**특별 기능:**
- 중복 요청 방지: 이미 요청을 보낸 경우 에러 반환
- 양방향 레코드 생성: from → to 및 to → from 두 개의 레코드 생성
- 상태: `from`측은 'pending', `to`측은 'received'

**에러:**
- `not-logged-in`: 로그인 필요
- `to-required`: 받는 사용자 ID 필수
- `cannot-request-yourself`: 자기 자신에게 요청 불가
- `already-friends`: 이미 친구 관계
- `already-requested`: 이미 요청을 보냄

**관련 함수:**
- `accept_friend`: 친구 요청 수락
- `reject_friend`: 친구 요청 거절
- `cancel_friend_request`: 친구 요청 취소

---

### accept_friend

**설명:** 받은 친구 요청을 수락합니다. 인증 필요.

**함수 시그니처:**
```php
function accept_friend(array $input): array
```

**파라미터:**
- `from` (int, required): 친구 요청을 보낸 사용자 ID

**반환값:**
- `array`: 친구 관계 정보
  - `message` (string): 성공 메시지
  - `success` (bool): true

**사용 예제:**

**JavaScript:**
```javascript
// 사용자 ID 5의 친구 요청 수락
const result = await func('accept_friend', {
    from: 5,
    auth: true
});

console.log(result.message); // "친구 요청을 수락했습니다"
```

**특별 기능:**
- 양방향 상태 업데이트: 양쪽 모두 'accepted' 상태로 변경
- `accepted_at` 타임스탬프 기록

**에러:**
- `not-logged-in`: 로그인 필요
- `from-required`: 보낸 사용자 ID 필수
- `friend-request-not-found`: 친구 요청을 찾을 수 없음

**관련 함수:**
- `request_friend`: 친구 요청
- `reject_friend`: 친구 요청 거절
- `get_friends`: 친구 목록 조회

---

### remove_friend

**설명:** 친구 관계를 삭제합니다. 인증 필요.

**함수 시그니처:**
```php
function remove_friend(array $input): array
```

**파라미터:**
- `friend_id` (int, required): 삭제할 친구의 사용자 ID

**반환값:**
- `array`: 삭제 결과
  - `message` (string): 성공 메시지
  - `success` (bool): true

**사용 예제:**

**JavaScript:**
```javascript
// 친구 삭제
const result = await func('remove_friend', {
    friend_id: 10,
    auth: true
});
```

**특별 기능:**
- 양방향 레코드 삭제: 양쪽 모두 삭제됨

**에러:**
- `not-logged-in`: 로그인 필요
- `friend-id-required`: 친구 ID 필수
- `not-friends`: 친구 관계가 아님

**관련 함수:**
- `get_friends`: 친구 목록 조회

---

### reject_friend

**설명:** 받은 친구 요청을 거절합니다. 인증 필요.

**함수 시그니처:**
```php
function reject_friend(array $input): array
```

**파라미터:**
- `from` (int, required): 친구 요청을 보낸 사용자 ID

**반환값:**
- `array`: 거절 결과
  - `message` (string): 성공 메시지
  - `success` (bool): true

**사용 예제:**

**JavaScript:**
```javascript
// 친구 요청 거절
const result = await func('reject_friend', {
    from: 5,
    auth: true
});
```

**특별 기능:**
- 양방향 레코드 삭제: 요청 레코드가 완전히 삭제됨

**에러:**
- `not-logged-in`: 로그인 필요
- `from-required`: 보낸 사용자 ID 필수
- `friend-request-not-found`: 친구 요청을 찾을 수 없음

**관련 함수:**
- `accept_friend`: 친구 요청 수락
- `request_friend`: 친구 요청

---

### cancel_friend_request

**설명:** 보낸 친구 요청을 취소합니다. 인증 필요.

**함수 시그니처:**
```php
function cancel_friend_request(array $input): array
```

**파라미터:**
- `to` (int, required): 요청을 보낸 대상 사용자 ID

**반환값:**
- `array`: 취소 결과
  - `message` (string): 성공 메시지
  - `success` (bool): true

**사용 예제:**

**JavaScript:**
```javascript
// 보낸 친구 요청 취소
const result = await func('cancel_friend_request', {
    to: 10,
    auth: true
});
```

**특별 기능:**
- 양방향 레코드 삭제

**에러:**
- `not-logged-in`: 로그인 필요
- `to-required`: 받는 사용자 ID 필수
- `friend-request-not-found`: 친구 요청을 찾을 수 없음

**관련 함수:**
- `request_friend`: 친구 요청
- `get_friend_requests_sent`: 보낸 요청 목록

---

### get_friend_ids

**설명:** 친구의 사용자 ID 배열을 조회합니다.

**함수 시그니처:**
```php
function get_friend_ids(array $input): array
```

**파라미터:**
- `me` (int, required): 내 사용자 ID

**반환값:**
- `array`: 친구 ID 배열 (숫자 배열)

**사용 예제:**

**JavaScript:**
```javascript
// 내 친구 ID 목록 조회
const friendIds = await func('get_friend_ids', { me: 5 });
console.log(friendIds); // [1, 2, 3, 10, 15]
```

**에러:**
- `me-required`: 사용자 ID 필수

**관련 함수:**
- `get_friends`: 친구 정보 조회

---

### count_friend_requests_sent

**설명:** 보낸 친구 요청 수를 조회합니다.

**함수 시그니처:**
```php
function count_friend_requests_sent(array $input): int
```

**파라미터:**
- `me` (int, required): 내 사용자 ID

**반환값:**
- `int`: 보낸 요청 수

**사용 예제:**

**JavaScript:**
```javascript
// 보낸 요청 수 조회
const count = await func('count_friend_requests_sent', { me: 5 });
console.log(count.data); // 3 (api.php가 자동으로 {data: 3, func: '...'}로 변환)
```

**에러:**
- `me-required`: 사용자 ID 필수

**관련 함수:**
- `get_friend_requests_sent`: 보낸 요청 목록

---

### get_friend_requests_sent

**설명:** 보낸 친구 요청 목록을 조회합니다.

**함수 시그니처:**
```php
function get_friend_requests_sent(array $input): array
```

**파라미터:**
- `me` (int, required): 내 사용자 ID
- `limit` (int, optional): 최대 개수 (기본값: 10)
- `page` (int, optional): 페이지 번호 (기본값: 1)

**반환값:**
- `array`: 친구 요청 배열 (사용자 정보 포함)

**사용 예제:**

**JavaScript:**
```javascript
// 보낸 요청 목록 조회
const requests = await func('get_friend_requests_sent', {
    me: 5,
    limit: 10
});

requests.forEach(req => {
    console.log(`${req.name}에게 요청을 보냄`);
});
```

**에러:**
- `me-required`: 사용자 ID 필수

**관련 함수:**
- `cancel_friend_request`: 요청 취소

---

### count_friend_requests_received

**설명:** 받은 친구 요청 수를 조회합니다.

**함수 시그니처:**
```php
function count_friend_requests_received(array $input): int
```

**파라미터:**
- `me` (int, required): 내 사용자 ID

**반환값:**
- `int`: 받은 요청 수

**사용 예제:**

**JavaScript:**
```javascript
// 받은 요청 수 조회
const count = await func('count_friend_requests_received', { me: 5 });
console.log(count.data); // 2
```

**에러:**
- `me-required`: 사용자 ID 필수

**관련 함수:**
- `get_friend_requests_received`: 받은 요청 목록

---

### get_friend_requests_received

**설명:** 받은 친구 요청 목록을 조회합니다.

**함수 시그니처:**
```php
function get_friend_requests_received(array $input): array
```

**파라미터:**
- `me` (int, required): 내 사용자 ID
- `limit` (int, optional): 최대 개수 (기본값: 10)
- `page` (int, optional): 페이지 번호 (기본값: 1)

**반환값:**
- `array`: 친구 요청 배열 (요청 보낸 사용자 정보 포함)

**사용 예제:**

**JavaScript:**
```javascript
// 받은 요청 목록 조회
const requests = await func('get_friend_requests_received', {
    me: 5,
    limit: 10
});

requests.forEach(req => {
    console.log(`${req.name}님이 친구 요청을 보냄`);
});
```

**에러:**
- `me-required`: 사용자 ID 필수

**관련 함수:**
- `accept_friend`: 요청 수락
- `reject_friend`: 요청 거절

---

### get_friends

**설명:** 친구 목록을 조회합니다. 사용자 정보 포함.

**함수 시그니처:**
```php
function get_friends(array $input): array
```

**파라미터:**
- `me` (int, required): 내 사용자 ID
- `limit` (int, optional): 최대 개수 (기본값: 10)
- `page` (int, optional): 페이지 번호 (기본값: 1)

**반환값:**
- `array`: 친구 배열 (사용자 정보 포함)

**사용 예제:**

**JavaScript:**
```javascript
// 친구 목록 조회
const friends = await func('get_friends', {
    me: 5,
    limit: 20
});

friends.forEach(friend => {
    console.log(friend.name);
});
```

**에러:**
- `me-required`: 사용자 ID 필수

**관련 함수:**
- `get_friend_ids`: 친구 ID만 조회
- `remove_friend`: 친구 삭제

---

### get_posts_from_feed_entries

**설명:** 피드에서 게시글 목록을 조회합니다. 친구가 작성한 게시글 포함.

**함수 시그니처:**
```php
function get_posts_from_feed_entries(array $input): array
```

**파라미터:**
- `user_id` (int, required): 사용자 ID
- `limit` (int, optional): 최대 개수 (기본값: 10)
- `offset` (int, optional): 오프셋 (기본값: 0)

**반환값:**
- `array`: 게시글 배열 (PostModel 객체들)

**사용 예제:**

**JavaScript:**
```javascript
// 내 피드 조회 (친구들의 게시글)
const posts = await func('get_posts_from_feed_entries', {
    user_id: 5,
    limit: 20,
    offset: 0
});

posts.forEach(post => {
    console.log(`${post.user.name}: ${post.title}`);
});
```

**특별 기능:**
- **Fan-out on Write**: 친구가 게시글을 작성하면 자동으로 피드에 추가됨
- **시간순 정렬**: 최신 게시글이 먼저 표시됨

**에러:**
- `user-id-required`: 사용자 ID 필수

**관련 함수:**
- `list_posts`: 일반 게시글 목록
- `get_friends`: 친구 목록

---

## 파일 작업 (File Operations)

파일 업로드 및 삭제 기능을 제공합니다.

### file_delete

**설명:** 파일을 삭제합니다. 본인의 파일만 삭제 가능. 인증 필요.

**함수 시그니처:**
```php
function file_delete(array $params): array
```

**파라미터:**
- `id` (int, required): 삭제할 파일 ID

**반환값:**
- `array`: 삭제 결과
  - `message` (string): 성공 메시지
  - `success` (bool): true

**사용 예제:**

**JavaScript:**
```javascript
// 파일 삭제
const result = await func('file_delete', {
    id: 456,
    auth: true
});

console.log(result.message); // "파일이 삭제되었습니다"
```

**특별 기능:**
- 물리적 파일 삭제: 서버의 실제 파일도 함께 삭제됨
- 권한 확인: 본인의 파일만 삭제 가능

**에러:**
- `not-logged-in`: 로그인 필요
- `file-not-found`: 파일을 찾을 수 없음
- `not-authorized`: 본인의 파일만 삭제 가능

**관련 함수:**
- `delete_file_from_post`: 게시글에서 파일 제거

---

## 언어 설정 (Language Settings)

사용자 언어 설정 기능을 제공합니다.

### set_language

**설명:** 사용자의 언어 설정을 변경합니다.

**함수 시그니처:**
```php
function set_language(array $params): array
```

**파라미터:**
- `lang` (string, required): 언어 코드 ('en', 'ko', 'ja', 'zh')

**반환값:**
- `array`: 언어 설정 결과
  - `message` (string): 성공 메시지
  - `lang` (string): 설정된 언어 코드

**사용 예제:**

**JavaScript:**
```javascript
// 언어를 한국어로 변경
const result = await func('set_language', { lang: 'ko' });
console.log(result.lang); // 'ko'

// 언어를 영어로 변경
await func('set_language', { lang: 'en' });

// 지원 언어: en, ko, ja, zh
```

**특별 기능:**
- 쿠키 저장: 언어 설정이 쿠키에 저장됨
- 세션 유지: 로그인하지 않아도 설정 유지

**에러:**
- `lang-required`: 언어 코드 필수
- `invalid-language`: 지원하지 않는 언어

**관련 함수:**
- `tr()`: JavaScript에서 다국어 번역

---

## 인증 (Authentication)

Firebase Authentication 통합 로그인 기능을 제공합니다.

### login_with_firebase

**설명:** Firebase ID Token으로 로그인합니다. 사용자가 없으면 자동으로 생성됩니다.

**함수 시그니처:**
```php
function login_with_firebase(array $params): array
```

**파라미터:**
- `id_token` (string, required): Firebase ID Token

**반환값:**
- `array`: 로그인 결과
  - `user` (array): 사용자 정보
  - `created` (bool): 새로 생성된 사용자인지 여부

**사용 예제:**

**JavaScript:**
```javascript
// Firebase 로그인 후 백엔드 세션 생성
const userCredential = await firebase.auth().signInWithEmailAndPassword(email, password);
const idToken = await userCredential.user.getIdToken();

const result = await func('login_with_firebase', {
    id_token: idToken
});

console.log(result.user); // 사용자 정보
if (result.created) {
    console.log('새로운 사용자가 생성되었습니다');
}
```

**특별 기능:**
- **자동 사용자 생성**: Firebase UID로 사용자가 없으면 자동 생성
- **세션 쿠키 설정**: PHP 세션 쿠키가 자동으로 설정됨
- **Firebase 사용자 정보 동기화**: Firebase의 이메일, 전화번호, 프로필 사진 등 자동 동기화

**에러:**
- `id-token-required`: ID Token 필수
- `invalid-id-token`: 잘못된 ID Token
- `firebase-error`: Firebase 인증 오류

**관련 함수:**
- `create_user_record`: 사용자 레코드 생성
- `get_user`: 사용자 조회

---

### create_test_users

**설명:** 테스트용 계정들을 데이터베이스에 자동으로 생성합니다. 개발 및 테스트 환경에서만 사용합니다.

**함수 시그니처:**
```php
function create_test_users(array $input = []): array
```

**파라미터:**
- 파라미터 없음 (선택 사항): 모든 테스트 계정이 자동으로 생성됩니다

**반환값:**
- `array`: 생성된 테스트 사용자들의 배열
  - 각 사용자: `{ id, firebase_uid, first_name, email, phone_number, created_at, ... }`

**생성되는 테스트 계정 목록:**

| 계정명 | Firebase UID | 이메일 | 전화번호 |
|--------|-------------|---------|----------|
| Apple | apple | apple@test.com | +11234567890 |
| Banana | banana | banana@test.com | +11234567891 |
| Cherry | cherry | cherry@test.com | +11234567892 |
| Durian | durian | durian@test.com | +11234567893 |
| Elderberry | elderberry | elderberry@test.com | +11234567894 |
| Fig | fig | fig@test.com | +11234567895 |
| Grape | grape | grape@test.com | +11234567896 |
| Honeydew | honeydew | honeydew@test.com | +11234567897 |
| Jackfruit | jackfruit | jackfruit@test.com | +11234567898 |
| Kiwi | kiwi | kiwi@test.com | +11234567899 |
| Lemon | lemon | lemon@test.com | +11234567900 |
| Mango | mango | mango@test.com | +11234567901 |

**모든 계정의 패스워드:** `12345a,*`

**사용 예제:**

**JavaScript:**
```javascript
// 테스트 계정 생성
const result = await func('create_test_users', {});
console.log('생성된 계정 수:', Object.keys(result).length);

// 생성된 계정 확인
Object.entries(result).forEach(([uid, user]) => {
    console.log(`${uid}: ${user.first_name} (${user.email})`);
});
```

**PHP:**
```php
$users = create_test_users([]);
foreach ($users as $firebaseUid => $user) {
    echo $user['first_name'] . ' - ' . $user['email'] . "\n";
}
```

**cURL (POST 요청):**
```bash
curl -X POST "https://sonub.com/api.php" \
  -H "Content-Type: application/json" \
  -d '{"func":"create_test_users"}'
```

**cURL (GET 요청):**
```bash
curl "https://sonub.com/api.php?func=create_test_users"
```

**특별 기능:**
- **자동 생성**: 이미 존재하는 계정은 업데이트, 새 계정은 자동 생성
- **배치 처리**: 12개 모든 계정을 한 번에 생성
- **개발 환경 최적화**: 로컬 테스트에 필요한 모든 계정을 빠르게 준비
- **테스트 스크립트 통합**: `create_posts.sh` 등 스크립트에서 이 계정들을 사용

**사용 시나리오:**

1. **로컬 개발 시작:**
   ```bash
   # 테스트 계정 자동 생성
   curl "https://local.sonub.com/api.php?func=create_test_users"
   ```

2. **테스트 데이터 준비:**
   ```bash
   # 테스트 계정 생성 후, create_posts.sh로 테스트 게시글 생성
   curl "https://local.sonub.com/api.php?func=create_test_users"
   ./create_posts.sh --count 10 --user banana
   ```

3. **테스트 계정 재설정:**
   ```javascript
   // 웹 콘솔에서 테스트 계정 재생성
   await func('create_test_users', {});
   // 모든 테스트 계정이 최신 상태로 업데이트됨
   ```

**관련 함수:**
- `login_with_firebase`: 테스트 계정으로 로그인
- `create_post`: 테스트 계정으로 게시글 생성
- `list_posts`: 생성된 게시글 확인

**관련 스크립트:**
- `create_posts.sh`: 테스트 계정으로 대량 게시글 생성

---

## 추가 정보

**API 호출 모범 사례:**

1. **Firebase 인증 포함**: 인증이 필요한 API는 반드시 `auth: true` 파라미터 추가
2. **에러 처리**: 모든 API 호출은 try-catch로 감싸기
3. **타입 체크**: 파라미터 타입을 확인하고 올바른 값 전달
4. **페이지네이션**: 대량 데이터 조회 시 limit/page 파라미터 사용
5. **최적화**: 불필요한 API 호출 최소화

**에러 처리 예제:**

```javascript
try {
    const post = await func('create_post', {
        category: 'discussion',
        title: '제목',
        content: '내용',
        auth: true
    });
    console.log('게시글 생성 성공:', post.id);
} catch (error) {
    console.error('에러:', error.message);
    // 에러 코드별 처리
    if (error.code === 'not-logged-in') {
        alert('로그인이 필요합니다');
    } else if (error.code === 'category-required') {
        alert('카테고리를 선택해주세요');
    }
}
```

**참고 문서:**
- [Sonub API 프로토콜 가이드](sonub-api-protocol.md) - API 동작 방식, 에러 처리
- [SKILL.md](SKILL.md) - API First 설계 철학, LIB 폴더 구조
