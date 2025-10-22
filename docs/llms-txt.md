# Sonub 웹사이트 개발 문서 (LLMs.txt)

> Sonub는 Sonub Network Hub SNS 웹사이트입니다.

## 개요

이 문서는 `docs/` 폴더의 모든 문서에 대한 요약과 기본 예제를 제공하여, LLM이 빠르게 필요한 문서를 찾고 참조할 수 있도록 돕습니다.

**중요사항**:
- 이 문서는 각 문서의 핵심 내용과 예제만 포함합니다
- 상세한 내용은 각 문서를 직접 참조하세요
- 문서 경로는 `docs/` 폴더 기준입니다

---

## 📚 문서 카테고리

### 🔧 핵심 개발 가이드
1. [coding-guideline.md](#coding-guidelinemd) - 전체 코딩 표준 및 규칙
2. [php.md](#phpmd) - PHP 코딩 표준
3. [database.md](#databasemd) - 데이터베이스 접근 및 PDO 사용법
4. [javascript.md](#javascriptmd) - JavaScript, Vue.js 사용법
5. [api.md](#apimd) - API First 설계 및 func() 함수
6. [test.md](#testmd) - PHP Unit/E2E 테스트, Playwright E2E 테스트

### 🎨 디자인 및 UI
7. [design/design.md](#designdesignmd) - 디자인 철학 및 MPA 구조
8. [design/bootstrap.md](#designbootstrapmd) - Bootstrap 5 Utility 클래스 사용법

### 🌐 다국어 및 번역
9. [translation.md](#translationmd) - 다국어 번역 Standard Workflow

### 🚀 기능별 가이드
10. [file-upload.md](#file-uploadmd) - 파일 업로드 기능
11. [friends-and-feeds.md](#friends-and-feedsmd) - 친구 관계 및 피드 시스템
12. [javascript-infinite-scroll.md](#javascript-infinite-scrollmd) - InfiniteScroll 라이브러리

### 👤 사용자 관리
13. [user/user.md](#userusermd) - 사용자 CRUD 및 친구 관리
14. [user/user.search.md](#userusersearchmd) - Vue.js 사용자 검색 컴포넌트

### ⚙️ 환경 설정
15. [setup/nginx-php-mariadb.md](#setupnginx-php-mariadbmd) - Docker LEMP 스택 설정
16. [php-hot-reload.md](#php-hot-reloadmd) - PHP 핫 리로드 개발 서버

### 📢 마케팅
17. [marketing/marketing-strategies-and-plans.md](#marketingmarketing-strategies-and-plansmd) - 마케팅 전략 및 계획
18. [marketing/sonub-facebook.md](#marketingsonub-facebookmd) - Facebook 마케팅

---

## 📖 문서 상세 요약

### coding-guideline.md

**경로**: `docs/coding-guideline.md`

**핵심 내용**:
- Sonub 웹사이트 전체 코딩 표준 및 규칙
- PHP, CSS, JavaScript 통합 가이드라인
- URL 함수 `href()` 필수 사용 규칙
- CSS 및 JavaScript 파일 분리 규칙
- Firebase 통합 가이드라인
- 라우팅 규칙 및 레이아웃 파일 구조
- **페이지 PHP 스크립트 구조**: 페이지 파일과 부분 파일 관리
- 페이지별 CSS/JavaScript 자동 로딩 시스템
- `load_deferred_js()` 함수 사용법

**주요 예제**:
```php
// href() 함수로 URL 생성
<a href="<?= href()->user->login ?>">로그인</a>
<a href="<?= href()->post->list(1, 'discussion') ?>">토론 게시판</a>

// 페이지 부분 파일 사용
// page/user/login.php (메인 페이지)
<?php include 'login.buttons.php'; ?>

// page/user/login.buttons.php (부분 파일)
<div class="button-group">
    <button type="submit">로그인</button>
</div>

// load_deferred_js() 함수로 공유 JavaScript 로드
<?php load_deferred_js('vue-components/user-search.component'); ?>
```

**참조**: 모든 개발 작업 시작 전 반드시 읽어야 할 기본 가이드라인

---

### php.md

**경로**: `docs/php.md`

**핵심 내용**:
- PHP 일반 코딩 표준 (들여쓰기, UTF-8 인코딩, 주석)
- **API 함수 규칙**: 모든 함수는 배열 파라미터 하나만 받아야 함
- **에러 처리 표준**: `error()` 함수로 `ApiException` throw
- 모듈 로딩 시스템

**주요 예제**:
```php
// ✅ 올바른 API 함수
function request_friend(array $input): array {
    $me = (int)($input['me'] ?? 0);
    $other = (int)($input['other'] ?? 0);

    if ($me <= 0) {
        error('invalid-me', '유효하지 않은 사용자 ID입니다');
    }

    return ['message' => '친구 요청을 보냈습니다'];
}

// ❌ 잘못된 예: 여러 스칼라 파라미터
function request_friend(int $me, int $other): void {
    // JavaScript에서 호출 시 에러 발생!
}
```

**참조**: PHP 개발 시 API 함수 규칙 및 에러 처리 방법

---

### database.md

**경로**: `docs/database.md`

**핵심 내용**:
- **PDO 직접 사용 최우선 권장**
- `pdo()` 함수로 PDO 객체 획득
- Prepared Statement로 SQL 인젝션 방지
- 쿼리 빌더는 차선택 (복잡한 경우에만)
- 데이터베이스 스키마 파일: `etc/db-schema/sonub-database-schema.sql`

**주요 예제**:
```php
// ✅ PDO 직접 사용 (최우선 권장)
$pdo = pdo();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

// ✅ PDO INSERT
$stmt = $pdo->prepare("INSERT INTO users (firebase_uid, first_name, last_name) VALUES (?, ?, ?)");
$stmt->execute([$firebase_uid, $first_name, $last_name]);
$user_id = $pdo->lastInsertId();

// 차선택: 쿼리 빌더 (복잡한 경우)
$users = db()->table('users')->where('age', '>', 18)->get();
```

**참조**: 데이터베이스 쿼리 작성 전 반드시 스키마 파일 확인

---

### javascript.md

**경로**: `docs/javascript.md`

**핵심 내용**:
- **JavaScript는 페이지 내 `<script>` 태그로 작성** (외부 `.js` 파일 분리 금지)
- `ready()` 래퍼 함수 필수
- `window.Store.state` 전역 상태 관리 (Vue.js Reactivity)
- **다국어 번역**: PHP `tr()` 함수 우선 권장, JavaScript `tr()` 함수는 특별한 경우에만
- `func()` 함수로 API 호출
- Vue.js Options API 사용, 구조 분해 할당 금지

**주요 예제**:
```php
<!-- page/user/profile.php -->
<div id="app">
    <p v-if="state.login">환영합니다, {{ state.login.first_name }} {{ state.login.last_name }}님!</p>
</div>

<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.Store.state
            };
        },
        methods: {
            async loadUser() {
                // ✅ func() 함수로 API 호출
                const user = await func('get_user_info', { user_id: 123 });

                // ✅ PHP tr() 함수로 번역 텍스트 주입 (권장)
                alert('<?= tr('로그인이 필요합니다') ?>');
            }
        }
    }).mount('#app');
});
</script>
```

**참조**: JavaScript 및 Vue.js 개발 시 필수 규칙

---

### api.md

**경로**: `docs/api.md`

**핵심 내용**:
- **API First 설계 철학**: 모든 함수는 API를 통해 호출 가능
- `api.php`가 모든 API 요청을 동적 라우팅
- `func()` 헬퍼 함수로 API 호출 (JavaScript)
- 에러 처리: `error()` 함수로 `ApiException` throw
- 함수 반환 형식: 배열/객체는 직접 반환, 스칼라 값은 `['data' => ...]` 형태

**주요 예제**:
```javascript
// ✅ func() 함수로 API 호출
const user = await func('get_user_info', { user_id: 123 });

// ✅ Firebase 인증 포함
const post = await func('create_post', {
    title: '게시글',
    content: '내용',
    auth: true
});
```

```php
// ✅ API 함수 정의
function get_user_info(array $input): array {
    $user_id = $input['user_id'] ?? null;

    if (empty($user_id)) {
        error('invalid-user-id', '사용자 ID가 유효하지 않습니다');
    }

    $user = db()->table('users')->where('id', $user_id)->first();
    return $user;
}
```

**참조**: API 설계 및 func() 함수 사용법

---

### test.md

**경로**: `docs/test.md`

**핵심 내용**:
- **테스트 종류**: PHP Unit Test, PHP E2E Test, Playwright E2E Test
- **🔥 최강력 규칙**: PHP 테스트는 호스트 환경에서 `php` 명령으로 직접 실행
- **❌ 절대 금지**: `docker exec sonub-php` 명령 사용 금지
- 테스트 파일 위치: `tests/[module]/[module].test.php`
- 라우팅 규칙: E2E 테스트 URL은 확장자 없이 작성
- 테스트 로그인: `banana@test.com:12345a,*` (SMS 인증 없이 로그인)

**주요 예제**:
```bash
# ✅ PHP Unit Test 실행
php tests/db/db.connection.test.php
php tests/friend-and-feed/get-friends.test.php

# ✅ PHP E2E Test 실행
php tests/e2e/homepage.e2e.test.php

# ✅ Playwright E2E Test 실행
npx playwright test tests/playwright/e2e/user-login.spec.ts

# ❌ 절대 금지
docker exec sonub-php php /sonub/tests/xxx.test.php
```

**참조**: 테스트 작성 및 실행 방법

---

### design/design.md

**경로**: `docs/design/design.md`

**핵심 내용**:
- **MPA (Multi-Page Application) 구조**
- `index.php`가 모든 페이지를 감싸는 레이아웃
- 자동 로드: Bootstrap, Vue.js, Axios, Firebase, Font Awesome, `/css/app.css`, `/js/app.js`
- 개별 페이지에서 `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` 태그 금지
- 디자인 철학: 심플, 단조, 현대적
- 아이콘: Font Awesome 7.1 Pro 우선, Bootstrap Icons는 대체용

**주요 예제**:
```html
<!-- page/user/profile.php -->
<!-- ❌ 금지: <!DOCTYPE html>, <html>, <head>, <body> 태그 사용 금지 -->

<!-- ✅ 올바른 방법: 페이지 고유 콘텐츠만 작성 -->
<div class="container py-4">
    <h1>사용자 프로필</h1>
    <!-- 페이지 콘텐츠 -->
</div>

<script>
ready(() => {
    // Vue.js 앱 초기화
});
</script>
```

**참조**: 페이지 구조 및 자동 로드 리소스

---

### design/bootstrap.md

**경로**: `docs/design/bootstrap.md`

**핵심 내용**:
- **Bootstrap 5 Utility 클래스 최우선 사용**
- 레이아웃, 색상, 간격, 크기 등 모든 스타일을 Utility 클래스로 작성
- 별도 CSS 파일은 최소화 (Bootstrap으로 표현 불가능한 경우에만)
- Bootstrap 기본 색상 변수 사용 (`$primary`, `$secondary` 등)
- 커스텀 색상 사용 금지

**주요 예제**:
```html
<!-- ✅ Bootstrap Utility 클래스로 완전한 디자인 -->
<div class="container py-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-3 text-primary">사용자 프로필</h5>
                    <div class="d-flex align-items-center mb-2">
                        <img src="..." class="rounded-circle me-2"
                             style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-dark">홍길동</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">2024-01-15</p>
                        </div>
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-person-plus me-1"></i>
                            친구 추가
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

**참조**: Bootstrap Utility 클래스 사용법 및 디자인 패턴

---

### translation.md

**경로**: `docs/translation.md`

**핵심 내용**:
- **지원 언어**: 한국어(ko), 영어(en), 일본어(ja), 중국어(zh) 4개 필수
- **Standard Workflow**: 각 PHP 파일 하단에 `inject_[php_file_name]_language()` 함수 정의
- `t()->inject()` 함수로 번역 텍스트 주입
- **키는 한글**로 작성
- `<?= t()->키이름 ?>` 형식으로 출력

**주요 예제**:
```php
<?php
// 1. 번역 함수 호출 (파일 맨 위)
inject_user_profile_language();
?>

<h1><?= t()->사용자_프로필 ?></h1>
<p><?= t()->환영합니다 ?></p>

<?php
// 2. 번역 함수 정의 (파일 맨 아래)
function inject_user_profile_language() {
    t()->inject([
        '사용자_프로필' => ['ko' => '사용자 프로필', 'en' => 'User Profile', 'ja' => 'ユーザープロフィール', 'zh' => '用户资料'],
        '환영합니다' => ['ko' => '환영합니다', 'en' => 'Welcome', 'ja' => 'ようこそ', 'zh' => '欢迎'],
    ]);
}
?>
```

**참조**: 다국어 번역 작업 시 Standard Workflow 필수 준수

---

### file-upload.md

**경로**: `docs/file-upload.md`

**핵심 내용**:
- 파일 업로드 기능 전체 가이드
- **Single 모드**: `data-single="true"` 속성으로 단일 파일 업로드
- **자동 삭제 버튼**: `data-delete-icon="show"` 속성
- Progress Bar, 썸네일, Hidden Input 지원
- 콜백 함수: `on_uploaded`, `on_deleted`

**주요 예제**:
```html
<!-- 가장 간단한 예제 -->
<input type="file"
       accept="image/*"
       data-single="true"
       data-display="#photo-display"
       data-delete-icon="show">
<div id="photo-display" style="width: 200px; height: 200px;"></div>
```

**참조**: 파일 및 이미지 업로드 기능 구현

---

### friends-and-feeds.md

**경로**: `docs/friends-and-feeds.md`

**핵심 내용**:
- 친구 관계, 차단, 피드 캐시 데이터베이스 설계
- **무방향 1행 모델**: 친구 관계는 중복 방지
- **Fan-out on write** 전략: 쓰기 시 전파, 읽기 시 고속
- 테이블: `users`, `posts`, `comments`, `friendships`, `blocks`, `feed_entries`

**주요 예제**:
```sql
-- 친구 관계 조회
SELECT * FROM friendships
WHERE (user_id_1 = ? OR user_id_2 = ?)
  AND status = 'accepted';

-- 피드 조회
SELECT fe.*, p.*
FROM feed_entries fe
JOIN posts p ON fe.post_id = p.id
WHERE fe.user_id = ?
ORDER BY fe.created_at DESC;
```

**참조**: 친구 관계 및 피드 시스템 데이터베이스 설계

---

### javascript-infinite-scroll.md

**경로**: `docs/javascript-infinite-scroll.md`

**핵심 내용**:
- InfiniteScroll 라이브러리 사용 가이드
- 무한 스크롤 기능 구현
- `onScrolledToBottom`, `onScrolledToTop` 콜백
- 디바운싱, Threshold 지원

**주요 예제**:
```php
<?php load_deferred_js('infinite-scroll'); ?>

<script>
ready(() => {
    const scrollController = InfiniteScroll.init('body', {
        onScrolledToBottom: () => {
            console.log('하단 도달: 더 많은 데이터 로드');
            // 데이터 로드 로직
        },
        threshold: 10,              // 하단으로부터 10px
        debounceDelay: 100,         // 100ms 디바운스
        initialScrollToBottom: false
    });
});
</script>
```

**참조**: 무한 스크롤 기능 구현

---

### user/user.md

**경로**: `docs/user/user.md`

**핵심 내용**:
- 사용자 CRUD 함수: `create_user_record`, `get_user`, `list_users`
- 친구 관리: `request_friend`, `accept_friend`, `reject_friend`
- Firebase UID와 MariaDB 통합
- 세션 ID 쿠키 자동 설정

**주요 예제**:
```php
// 사용자 생성
$user = create_user_record([
    'firebase_uid' => 'abc123xyz',
    'first_name' => '길동',
    'last_name' => '홍',
    'birthday' => strtotime('1990-01-01'),
    'gender' => 'M'
]);

// 사용자 목록 조회
$users = list_users([
    'page' => 1,
    'limit' => 20,
    'search' => '홍길동'
]);
```

**참조**: 사용자 관리 및 친구 관리 함수

---

### user/user.search.md

**경로**: `docs/user/user.search.md`

**핵심 내용**:
- Vue.js 사용자 검색 컴포넌트
- 자동 마운트: `.user-search-component` 클래스
- Bootstrap 모달 사용
- 다국어 지원, 페이지네이션
- 여러 인스턴스 동시 사용 가능

**주요 예제**:
```php
<?php load_deferred_js('vue-components/user-search.component'); ?>

<!-- 자동 마운트 -->
<div class="user-search-component"></div>
```

**참조**: 사용자 검색 컴포넌트 사용법

---

### setup/nginx-php-mariadb.md

**경로**: `docs/setup/nginx-php-mariadb.md`

**핵심 내용**:
- Docker 기반 LEMP 스택 설정
- 컨테이너: `sonub-nginx`, `sonub-php`, `sonub-mariadb`
- `docker-compose.yml` 사용
- 로컬 도메인: `https://local.sonub.com/`

**주요 예제**:
```bash
# Docker Compose 시작
docker-compose up -d

# 컨테이너 확인
docker ps

# 로그 확인
docker logs sonub-nginx
```

**참조**: 개발 환경 설정

---

### php-hot-reload.md

**경로**: `docs/php-hot-reload.md`

**핵심 내용**:
- PHP 파일 변경 시 자동 새로고침
- `sonub.sh` 스크립트 사용
- HTTPS 지원 (mkcert)

**주요 예제**:
```bash
# 핫 리로드 서버 시작
./sonub.sh
```

**참조**: 핫 리로드 개발 서버 사용법

---

### marketing/marketing-strategies-and-plans.md

**경로**: `docs/marketing/marketing-strategies-and-plans.md`

**핵심 내용**:
- Sonub 마케팅 전략 및 계획

**참조**: 마케팅 전략

---

### marketing/sonub-facebook.md

**경로**: `docs/marketing/sonub-facebook.md`

**핵심 내용**:
- Facebook 마케팅 가이드

**참조**: Facebook 마케팅

---

## 🔍 빠른 검색 가이드

### 코딩 표준을 찾고 싶다면?
- [coding-guideline.md](#coding-guidelinemd) - 전체 코딩 표준
- [php.md](#phpmd) - PHP 코딩 표준
- [javascript.md](#javascriptmd) - JavaScript 코딩 표준

### 데이터베이스 작업을 하려면?
- [database.md](#databasemd) - PDO 사용법, 쿼리 작성
- [friends-and-feeds.md](#friends-and-feedsmd) - 친구 관계 및 피드 테이블 설계

### API 개발을 하려면?
- [api.md](#apimd) - API First 설계, func() 함수
- [php.md](#phpmd) - API 함수 규칙

### UI/디자인 작업을 하려면?
- [design/design.md](#designdesignmd) - 디자인 철학, MPA 구조
- [design/bootstrap.md](#designbootstrapmd) - Bootstrap 5 Utility 클래스

### 다국어 번역을 추가하려면?
- [translation.md](#translationmd) - Standard Workflow, t()->inject() 사용법

### 테스트를 작성하려면?
- [test.md](#testmd) - PHP Unit/E2E 테스트, Playwright E2E 테스트

### 특정 기능을 구현하려면?
- [file-upload.md](#file-uploadmd) - 파일 업로드
- [javascript-infinite-scroll.md](#javascript-infinite-scrollmd) - 무한 스크롤
- [user/user.md](#userusermd) - 사용자 관리
- [user/user.search.md](#userusersearchmd) - 사용자 검색 컴포넌트

---

## 📝 문서 작성 규칙

1. **UTF-8 인코딩 필수**: 모든 문서는 BOM 없는 UTF-8 인코딩으로 저장
2. **문서 크기 제한**: 각 문서는 최대 1,000 라인까지만 작성
3. **한국어 작성**: 모든 문서는 한국어로 작성
4. **목차 포함**: 모든 주요 문서는 목차(ToC) 포함

---

## 🔗 관련 문서

- **CLAUDE.md**: AI 어시스턴트를 위한 전체 개발 가이드라인
- **README.md**: 프로젝트 개요 및 시작 가이드

---

**마지막 업데이트**: 2025-01-19
