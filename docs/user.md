# 사용자 관리 (User Management)

## 목차
- [개요](#개요)
- [사용자 테이블 구조](#사용자-테이블-구조)
- [사용자 함수](#사용자-함수)
  - [create_user_record](#create_user_record)
    - [세션 ID 쿠키 자동 설정](#세션-id-쿠키-자동-설정)
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
            'display_name' => '홍길동',
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
            <h3><?= htmlspecialchars($user->display_name) ?></h3>
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
      "display_name": "홍길동",
      "created_at": 1759646876,
      "updated_at": 1759646876,
      "birthday": 631152000,
      "gender": "M",
      "photo_url": "/uploads/profile/123.jpg"
    },
    {
      "id": 2,
      "firebase_uid": "def456uvw",
      "display_name": "김영희",
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
                        <h5><?= htmlspecialchars($user->display_name) ?></h5>
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
                    <td><?= htmlspecialchars($user->display_name) ?></td>
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
ALTER TABLE users ADD INDEX idx_display_name (display_name);

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
<div id="profile-app"
     data-other-user-id="<?= $user->id ?>"
     data-is-me="<?= $is_me ? 'true' : 'false' ?>"
     data-my-user-id="<?= login() ? login()->id : 0 ?>">

    <!-- 프로필 정보 -->
    <h1><?= htmlspecialchars($user->display_name) ?></h1>

    <!-- 친구 추가 버튼 (다른 사용자인 경우만 표시) -->
    <?php if (!$is_me): ?>
        <button @click="requestFriend"
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

    // 데이터 속성에서 초기 데이터 가져오기
    const otherUserId = parseInt(appElement.dataset.otherUserId) || 0;
    const isMe = appElement.dataset.isMe === 'true';
    const myUserId = parseInt(appElement.dataset.myUserId) || 0;

    Vue.createApp({
        data() {
            return {
                requesting: false,
                isFriend: false,
                otherUserId: otherUserId,
                myUserId: myUserId,
                isMe: isMe
            };
        },
        methods: {
            async requestFriend() {
                if (!this.myUserId) {
                    alert('로그인이 필요합니다.');
                    return;
                }

                try {
                    this.requesting = true;

                    await func('request_friend', {
                        me: this.myUserId,
                        other: this.otherUserId,
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

#### 주의사항

1. **Firebase 인증 필수**: `auth: true` 파라미터를 항상 포함해야 합니다.
2. **로그인 확인**: 친구 요청 전에 사용자가 로그인했는지 확인합니다.
3. **중복 요청 방지**: 요청 중 상태(`requesting`)를 사용하여 버튼을 비활성화합니다.
4. **에러 처리**: `try-catch`를 사용하여 에러를 적절히 처리합니다.
5. **자기 자신 확인**: 자기 자신에게는 친구 요청을 보낼 수 없습니다.

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
