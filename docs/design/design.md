Sonub 웹사이트 개발을 위한 디자인 가이드라인
================================================

## 목차
- [개요](#개요)
- [페이지 구조 및 파일 로딩](#페이지-구조-및-파일-로딩)
- [디자인 철학](#디자인-철학)
- [Bootstrap 가이드라인](#bootstrap-가이드라인)
- [CSS 및 JavaScript 파일 분리 규칙](#css-및-javascript-파일-분리-규칙)
- [아이콘 사용 가이드라인](#아이콘-사용-가이드라인)

## 개요
- 이 문서는 Sonub 웹사이트 개발을 위한 디자인 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.

## 페이지 구조 및 파일 로딩

### MPA (Multi-Page Application) 구조

Sonub는 PHP 기반 MPA 방식을 사용하며, **모든 필수 리소스는 `index.php`에서 자동으로 로드됩니다.**

### index.php 레이아웃 구조

**🔥🔥🔥 최강력 규칙: `index.php`는 모든 페이지를 감싸는(wrap) 레이아웃 역할을 합니다 🔥🔥🔥**

`index.php`는 다음과 같은 구조로 모든 페이지를 감싸고 있습니다:

```html
<!DOCTYPE html>
<html>
<head>
    <!-- CSS 프레임워크, 아이콘, 커스텀 스타일 로드 -->
</head>
<body>
    <!-- Header (네비게이션) -->
    <header id="page-header">
        <!-- 헤더 내용 -->
    </header>

    <!-- Main Content Area -->
    <main>
        <?php include page() ?>  <!-- 개별 페이지 파일이 여기에 포함됨 -->
    </main>

    <!-- Footer -->
    <footer id="page-footer">
        <!-- 푸터 내용 -->
    </footer>

    <!-- JavaScript 라이브러리 로드 -->
</body>
</html>
```

**✅ 필수 규칙:**
- **모든 페이지에는 `header#page-header`, `<main>`, `footer#page-footer`가 반드시 존재합니다**
- 개별 페이지 파일(`./page/**/*.php`)은 `<main>` 태그 안에 포함됩니다
- 개별 페이지 파일에서는 페이지 고유 콘텐츠만 작성합니다
- `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` 태그는 `index.php`에 이미 존재하므로 개별 페이지에서 사용하지 마세요

### 자동 로드되는 리소스

`index.php`에서 다음 항목들이 자동으로 로드되므로 **개별 페이지에서 중복 로드하지 마세요**:

#### 1. 초기화 및 설정
- ✅ `init.php` - 프로젝트 초기화 및 설정
- ✅ ROOT_DIR 상수 정의
- ✅ 모든 라이브러리 자동 로드

#### 2. CSS 프레임워크 및 아이콘
- ✅ Bootstrap 5.3.8 CSS
- ✅ Bootstrap Icons 1.13.1
- ✅ Font Awesome 7.1 (Pro)
- ✅ Google Fonts (Noto Sans KR)
- ✅ `/css/app.css` - 커스텀 스타일

#### 3. JavaScript 라이브러리
- ✅ Vue.js 3.x (글로벌 빌드)
- ✅ Axios
- ✅ Bootstrap 5.3.8 JS
- ✅ `/js/app.js` - 커스텀 스크립트

#### 4. 레이아웃 구조
- ✅ Header (네비게이션)
- ✅ Footer
- ✅ 사이드바 (좌/우)
- ✅ 메인 컨텐츠 영역

#### 5. Firebase 설정
- ✅ Firebase Authentication 설정
- ✅ Firebase 초기화 코드

### 페이지 PHP 파일 작성 규칙

**🔥 최강력 규칙: 페이지 파일에서는 페이지 고유 콘텐츠만 작성하세요**

#### ✅ 올바른 페이지 파일 예제

```php
<?php
// page/user/profile-edit.php

// 페이지 고유 로직만 작성
$user = login();
if (!$user) {
    header('Location: /page/user/login.php');
    exit;
}
?>

<!-- 페이지 고유 HTML만 작성 -->
<div class="container my-5">
    <h1>프로필 수정</h1>
    <form>
        <!-- 폼 내용 -->
    </form>
</div>

<!-- 페이지 고유 JavaScript만 작성 -->
<script>
const { createApp } = Vue;
createApp({
    // Vue 앱 코드
}).mount('#app');
</script>
```

#### ❌ 잘못된 페이지 파일 예제

```php
<?php
// ❌ 금지: init.php는 이미 index.php에서 로드됨
include '../../init.php';

// ❌ 금지: head.php는 필요 없음
include ROOT_DIR . '/etc/boot/head.php';
?>

<!DOCTYPE html>  <!-- ❌ 금지: HTML 태그는 이미 index.php에 있음 -->
<html>
<head>
    <!-- ❌ 금지: 아래는 이미 index.php에서 로드됨 -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <script src="/js/vue.global.js"></script>
</head>
<body>
    <!-- 페이지 내용 -->
</body>
</html>

<?php
// ❌ 금지: foot.php는 필요 없음
include ROOT_DIR . '/etc/boot/foot.php';
?>
```

### 페이지별 커스텀 리소스

페이지별 고유한 CSS/JS가 필요한 경우:

1. **페이지별 CSS**: `/page/user/profile-edit.css` 생성 → 자동 로드됨
2. **페이지별 JS**: `/page/user/profile-edit.js` 생성 → 자동 로드됨

**중요**: `index.php`의 다음 코드가 자동으로 페이지별 파일을 로드합니다:
```php
<?php include_page_css() ?>
<?php include_page_js() ?>
```

### 요약

| 항목 | index.php에서 로드 | 페이지 파일에서 할 일 |
|------|-------------------|---------------------|
| init.php | ✅ 자동 로드 | ❌ 로드하지 마세요 |
| Bootstrap CSS/JS | ✅ 자동 로드 | ❌ 로드하지 마세요 |
| Vue.js | ✅ 자동 로드 | ❌ 로드하지 마세요 |
| Header/Footer | ✅ 자동 로드 | ❌ 로드하지 마세요 |
| 페이지 콘텐츠 | ❌ 없음 | ✅ 작성하세요 |
| 페이지별 Vue 앱 | ❌ 없음 | ✅ 작성하세요 |

## 디자인 철학

### 라이트 모드만 지원
- **중요**: Sonub 웹사이트는 **라이트 모드만** 지원합니다
- 다크 모드 기능이나 다크 모드 전용 스타일을 **절대** 구현하지 마세요
- 모든 디자인 결정은 라이트 모드 외관에 최적화되어야 합니다

### 디자인 핵심 원칙

**🔥🔥🔥 최강력 규칙: Sonub 디자인은 심플하고 단조로우며 현대적이어야 합니다 🔥🔥🔥**

#### 1. 심플하고 단조로운 디자인
- **✅ 필수**: 절대 화려한 디자인을 하지 마세요
- **✅ 필수**: 복잡한 구조나 과도한 장식 금지
- **✅ 필수**: 미니멀한 디자인 추구

#### 2. 현대적이고 단순한 구조
- **✅ 필수**: 세련되면서도 아주 단순한 구조로 작성
- **✅ 필수**: 최신 디자인 트렌드를 따르되 복잡도는 최소화

#### 3. Bootstrap 레이아웃 필수 사용
- **✅ 필수**: 반드시 Bootstrap으로 레이아웃을 작성합니다
- **✅ 필수**: 레이아웃 관련 유틸리티 클래스는 인라인 `class=''` 속성으로 작성
- **✅ 필수**: 레이아웃과 관련 없는 Bootstrap 유틸리티 클래스는 별도 CSS 파일로 분리
- **Bootstrap 레이아웃 유틸리티 클래스 예시**:
  - 컨테이너: `container`, `container-fluid`
  - 그리드: `row`, `col`, `col-md-6`, `offset-md-2`
  - Flexbox: `d-flex`, `flex-column`, `gap-3`, `justify-content-center`, `align-items-center`
  - 간격: `mb-3`, `mt-4`, `p-2`, `px-3`, `py-4`

#### 4. CSS 파일 분리 규칙
- **✅ 페이지 파일 (`./page/**/*.php`)**: CSS를 반드시 외부 `.css` 파일로 분리
  - 파일 위치: `./page/**/*.css` (페이지 파일과 같은 폴더)
  - 자동 로드: `index.php`에서 자동으로 로드됨
- **✅ 위젯/함수 파일**: CSS를 `<style>` 태그 내에 작성
  - 위젯의 독립성 유지
  - 재사용성 향상

#### 5. Shadow 최소화
- **✅ 필수**: 가능한 shadow를 추가하지 마세요
- **✅ 허용**: 꼭 필요한 경우 매우 미세한 shadow만 사용
  - 예: `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);`

#### 6. 단순한 색상
- **✅ 필수**: Bootstrap 기본 색상 변수 사용
  - `var(--bs-primary)`, `var(--bs-secondary)`, `var(--bs-light)`, `var(--bs-dark)`
  - `var(--bs-border-color)`, `var(--bs-emphasis-color)`, `var(--bs-body-color)`
- **❌ 금지**: HEX 색상 코드나 커스텀 색상 최소화

#### 7. 충분한 여백
- **✅ 필수**: 요소 간 여백을 충분히 주어서 여유 있는 디자인
- **✅ 필수**: 페이지 가장자리(왼쪽/오른쪽) 여백은 작게
  - 예: `container-fluid px-2` 또는 `container px-3`

### 디자인 수정 체크리스트

**페이지 파일 (`./page/**/*.php`) 디자인 수정 시**:
- [ ] Bootstrap 레이아웃 유틸리티 클래스로 레이아웃 작성 (`d-flex`, `gap-3`, `mb-4` 등)
- [ ] 레이아웃과 관련 없는 스타일은 외부 `.css` 파일로 분리
- [ ] 심플하고 단조로운 디자인 적용
- [ ] Shadow 최소화 또는 제거
- [ ] Bootstrap 기본 색상 변수 사용
- [ ] 충분한 여백 적용
- [ ] 페이지 가장자리 여백 최소화 (`px-2`, `px-3`)

**위젯/함수 파일 디자인 수정 시**:
- [ ] Bootstrap 레이아웃 유틸리티 클래스로 레이아웃 작성
- [ ] CSS는 `<style>` 태그 내에 작성
- [ ] 위젯 고유의 CSS 클래스명 사용 (충돌 방지)
- [ ] 심플하고 단조로운 디자인 적용
- [ ] Shadow 최소화 또는 제거
- [ ] Bootstrap 기본 색상 변수 사용
- [ ] 충분한 여백 적용

### 디자인 예제

**✅ 올바른 예제 - 페이지 파일**:

```php
<!-- ./page/user/profile.php -->
<?php
$user = login();
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 유틸리티 클래스 -->
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- profile-header 클래스는 profile.css에 정의 -->
            <div class="profile-header mb-4">
                <h1><?= $user->name ?></h1>
                <p><?= $user->bio ?></p>
            </div>

            <!-- profile-content 클래스는 profile.css에 정의 -->
            <div class="profile-content">
                <p>콘텐츠...</p>
            </div>
        </div>
    </div>
</div>
```

**외부 CSS 파일 (`./page/user/profile.css`)**:

```css
/* ✅ 올바른 방법: 심플하고 단조로운 디자인 */

/* 프로필 헤더 - 미니멀한 스타일 */
.profile-header {
    background-color: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 2rem;
    /* shadow 없음 - 심플한 디자인 */
}

.profile-header h1 {
    color: var(--bs-emphasis-color);
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.profile-header p {
    color: var(--bs-body-color);
    margin: 0;
}

/* 프로필 콘텐츠 - 단순한 구조 */
.profile-content {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 2rem;
}

.profile-content p {
    color: var(--bs-body-color);
    line-height: 1.6;
}
```

**✅ 올바른 예제 - 위젯 파일**:

```php
<!-- ./widgets/post/post-card.php -->
<div class="d-flex flex-column gap-3 post-card-widget mb-3">
    <h3><?= $post->title ?></h3>
    <p><?= $post->content ?></p>
</div>

<style>
/* ✅ 올바른 방법: 위젯 CSS는 <style> 태그 내 작성 */

/* 심플하고 단조로운 디자인 */
.post-card-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.5rem;
    /* shadow 없음 - 미니멀한 디자인 */
}

.post-card-widget h3 {
    font-size: 1.25rem;
    color: var(--bs-emphasis-color);
    margin-bottom: 0.75rem;
}

.post-card-widget p {
    color: var(--bs-body-color);
    line-height: 1.6;
    margin: 0;
}
</style>
```

**❌ 잘못된 예제 - 화려하고 복잡한 디자인 (절대 금지)**:

```css
/* ❌ 절대 금지: 화려하고 복잡한 디자인 */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(0, 0, 0, 0.2);
    border-radius: 20px;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
    animation: rotate 10s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
```

## Bootstrap 가이드라인

**📚 상세 내용은 [Bootstrap 5 가이드라인](./bootstrap.md) 문서를 참조하세요**

Bootstrap 5 관련 상세 가이드라인 (Utility CSS, 색상, 컴포넌트, 반응형 디자인 등)은 별도 문서로 분리되었습니다.

**주요 내용:**
- ✅ Bootstrap Utility CSS 클래스 사용법
- ✅ 색상 가이드라인 및 커스텀 색상 사용 금지
- ✅ Bootstrap 컴포넌트 사용법
- ✅ 반응형 디자인 가이드라인
- ✅ 실제 예제 및 안티패턴

**바로가기:** [docs/design/bootstrap.md](./bootstrap.md)

---

## CSS 및 JavaScript 파일 분리 규칙

**🔥🔥🔥 최강력 규칙: 페이지와 위젯에서 CSS/JS 관리 방식이 다릅니다 🔥🔥🔥**

Sonub 프로젝트에서는 **페이지 파일**과 **위젯 파일**의 CSS 및 JavaScript 관리 방식이 명확히 구분됩니다.

### 페이지 파일 (`./page/**/*.php`)

**페이지 파일은 전체 페이지의 콘텐츠를 담당하며, CSS와 JavaScript를 외부 파일로 분리합니다.**

#### CSS 분리 규칙

**✅ 필수: 페이지 파일에서는 CSS를 반드시 외부 파일로 분리해야 합니다**

1. **레이아웃 관련 CSS (HTML에 직접 작성)**
   - Bootstrap 5.3.8 유틸리티 클래스 사용
   - `class="d-flex flex-column gap-3 align-items-center"` 형태
   - 레이아웃, 포지션, 정렬, 간격 등

2. **스타일 관련 CSS (외부 파일로 분리)**
   - 페이지 파일과 같은 폴더에 `.css` 파일 생성
   - 색상, 보더, 폰트, 효과 등의 스타일
   - `index.php`에서 자동으로 로드됨

**❌ 절대 금지:**
- 페이지 파일 내에 `<style>` 태그 사용 금지
- 인라인 `style` 속성 사용 금지 (디자인 관련)

**예제 - 페이지 파일:**

```php
<!-- ./page/user/profile.php -->
<?php
// 페이지 고유 로직
$user = login();
if (!$user) {
    header('Location: /user/login');
    exit;
}
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 클래스 -->
<div class="container my-5">
    <div class="d-flex flex-column gap-4">
        <!-- profile-header 클래스는 profile.css에 정의 -->
        <div class="profile-header">
            <img src="<?= $user->photo ?>" alt="프로필" class="profile-photo">
            <h1 class="profile-name"><?= $user->name ?></h1>
            <p class="profile-bio"><?= $user->bio ?></p>
        </div>

        <!-- profile-stats 클래스는 profile.css에 정의 -->
        <div class="d-flex justify-content-around profile-stats">
            <div class="stat-item">
                <span class="stat-number"><?= $user->post_count ?></span>
                <span class="stat-label">게시글</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $user->follower_count ?></span>
                <span class="stat-label">팔로워</span>
            </div>
        </div>
    </div>
</div>

<!-- ❌ 금지: <style> 태그 사용 금지 -->
<!-- ❌ 금지: <script> 태그 사용 금지 -->
```

**예제 - CSS 파일:**

```css
/* ./page/user/profile.css */
/* ✅ 올바른 방법: 스타일은 외부 CSS 파일에 정의 */

/* 프로필 헤더 */
.profile-header {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    object-fit: cover;
}

.profile-name {
    color: white;
    font-size: 2rem;
    font-weight: bold;
    margin-top: 20px;
    margin-bottom: 10px;
}

.profile-bio {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.6;
}

/* 프로필 통계 */
.profile-stats {
    background-color: var(--bs-light);
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: var(--bs-primary);
}

.stat-label {
    display: block;
    font-size: 0.9rem;
    color: var(--bs-secondary);
    margin-top: 5px;
}
```

#### JavaScript 분리 규칙

**✅ 필수: 페이지 파일에서는 JavaScript를 반드시 외부 파일로 분리해야 합니다**

- 페이지 파일과 같은 폴더에 `.js` 파일 생성
- Vue 앱, 이벤트 핸들러 등 모든 JavaScript 코드
- `index.php`에서 `defer` 속성으로 자동 로드됨

**❌ 절대 금지:**
- 페이지 파일 내에 `<script>` 태그 사용 금지

**예제 - JavaScript 파일:**

```javascript
// ./page/user/profile.js
// ✅ 올바른 방법: JavaScript는 외부 JS 파일에 정의

ready(() => {
    Vue.createApp({
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
                    const response = await func('get_user_profile', {
                        user_id: <?= $user->id ?>,
                    });

                    if (response.error_code) {
                        this.error = response.error_message;
                        return;
                    }

                    this.user = response;
                } catch (err) {
                    this.error = '프로필을 불러올 수 없습니다.';
                } finally {
                    this.loading = false;
                }
            },

            async followUser() {
                await func('follow_user', {
                    user_id: this.user.id,
                    auth: true,
                });
                this.user.follower_count++;
            },
        },
        mounted() {
            this.loadProfile();
        },
    }).mount('#profile-app');
});
```

---

### 위젯 파일 (`./widgets/**/*.php`)

**위젯 파일은 재사용 가능한 컴포넌트이며, CSS와 JavaScript를 같은 PHP 파일 내에 작성합니다.**

#### 위젯의 특징

- 여러 페이지에서 재사용되는 독립적인 UI 컴포넌트
- 자체 완결성: 하나의 파일에 HTML, CSS, JavaScript가 모두 포함
- 이식성: 다른 페이지에서 `include`만 하면 즉시 사용 가능

#### CSS 작성 규칙

**✅ 필수: 위젯의 CSS는 `<style>` 태그 내에 작성합니다**

1. **위젯 고유의 스타일만 작성**
   - 전역 스타일 금지
   - 위젯 내부 요소에만 영향을 주는 스타일

2. **고유한 CSS 클래스 이름 사용**
   - 다른 위젯과 충돌하지 않도록 고유한 접두사 사용
   - 예: `.post-card-widget`, `.comment-widget`, `.user-badge-widget`

3. **Bootstrap 변수 사용**
   - `var(--bs-primary)`, `var(--bs-light)` 등 Bootstrap CSS 변수 활용

**예제 - 위젯 파일:**

```php
<!-- ./widgets/post/post-card.php -->
<?php
/**
 * 게시물 카드 위젯
 *
 * 재사용 가능한 게시물 카드 컴포넌트
 *
 * @param array $post 게시물 데이터
 *   - id: 게시물 ID
 *   - title: 제목
 *   - content: 내용
 *   - author: 작성자
 *   - created_at: 작성 시간
 *   - likes: 좋아요 수
 */

$post_id = $post['id'];
$unique_id = "post-card-{$post_id}";
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 클래스 -->
<article class="d-flex flex-column gap-3 post-card-widget" id="<?= $unique_id ?>">
    <!-- 게시물 헤더 -->
    <header class="d-flex align-items-center gap-2 post-card-header">
        <img :src="author.photo" alt="프로필" class="author-photo">
        <div class="flex-grow-1">
            <h5 class="post-author-name">{{ author.name }}</h5>
            <time class="post-time">{{ formatTime(post.created_at) }}</time>
        </div>
        <button class="btn-more">⋮</button>
    </header>

    <!-- 게시물 본문 -->
    <div class="post-card-body">
        <h3 class="post-title">{{ post.title }}</h3>
        <p class="post-content">{{ post.content }}</p>
    </div>

    <!-- 게시물 푸터 -->
    <footer class="d-flex align-items-center gap-3 post-card-footer">
        <button @click="likePost" class="btn-action">
            <i class="fa-solid fa-heart" :class="{ liked: isLiked }"></i>
            <span>{{ likes }}</span>
        </button>
        <button class="btn-action">
            <i class="fa-solid fa-comment"></i>
            <span>{{ comments }}</span>
        </button>
        <button class="btn-action">
            <i class="fa-solid fa-share"></i>
        </button>
    </footer>
</article>

<!-- ✅ 올바른 방법: 위젯의 CSS는 <style> 태그 내에 작성 -->
<style>
/* 게시물 카드 위젯 전용 스타일 */

.post-card-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.3s ease;
}

.post-card-widget:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* 헤더 */
.post-card-header {
    padding-bottom: 15px;
    border-bottom: 1px solid var(--bs-border-color);
}

.author-photo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.post-author-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    margin: 0;
}

.post-time {
    font-size: 0.85rem;
    color: var(--bs-secondary-color);
}

.btn-more {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--bs-secondary-color);
    cursor: pointer;
    padding: 5px 10px;
}

.btn-more:hover {
    color: var(--bs-emphasis-color);
}

/* 본문 */
.post-card-body {
    padding: 15px 0;
}

.post-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--bs-emphasis-color);
    margin-bottom: 10px;
}

.post-content {
    font-size: 1rem;
    color: var(--bs-body-color);
    line-height: 1.6;
    margin: 0;
}

/* 푸터 */
.post-card-footer {
    padding-top: 15px;
    border-top: 1px solid var(--bs-border-color);
}

.btn-action {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--bs-secondary-color);
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background-color: var(--bs-light);
    color: var(--bs-primary);
}

.btn-action i.liked {
    color: var(--bs-danger);
}

.btn-action span {
    font-size: 0.9rem;
}
</style>

<!-- ✅ 올바른 방법: 위젯의 JavaScript는 <script> 태그 내에 작성 -->
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                post: <?= json_encode($post, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                author: <?= json_encode($post['author'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                likes: <?= $post['likes'] ?>,
                comments: <?= $post['comments'] ?? 0 ?>,
                isLiked: false,
            };
        },
        methods: {
            async likePost() {
                try {
                    await func('like_post', {
                        post_id: this.post.id,
                        auth: true,
                    });

                    if (this.isLiked) {
                        this.likes--;
                        this.isLiked = false;
                    } else {
                        this.likes++;
                        this.isLiked = true;
                    }
                } catch (err) {
                    console.error('좋아요 실패:', err);
                }
            },

            formatTime(timestamp) {
                // 시간 포맷팅 로직
                const date = new Date(timestamp * 1000);
                return date.toLocaleDateString('ko-KR');
            },
        },
        mounted() {
            // 초기화 로직
        },
    }).mount('#<?= $unique_id ?>');
});
</script>
```

#### JavaScript 작성 규칙

**✅ 필수: 위젯의 JavaScript는 `<script>` 태그 내에 작성합니다**

1. **고유한 ID로 Vue 앱 마운트**
   - 위젯마다 고유한 ID 사용
   - `uniqid()` 함수 활용하여 충돌 방지

2. **ready() 함수 래퍼 사용**
   - 모든 JavaScript는 `ready(() => { ... })` 내부에 작성
   - DOM 로드 완료 후 실행 보장

3. **위젯 고유의 로직만 작성**
   - 전역 변수 생성 금지
   - 다른 위젯에 영향을 주지 않도록 독립적으로 작성

---

### 페이지 vs 위젯 비교표

| 항목 | 페이지 (`./page/**/*.php`) | 위젯 (`./widgets/**/*.php`) |
|------|---------------------------|---------------------------|
| **목적** | 전체 페이지 콘텐츠 | 재사용 가능한 UI 컴포넌트 |
| **CSS 위치** | ✅ 외부 파일 (`./page/**/*.css`) | ✅ 같은 파일 내 `<style>` 태그 |
| **JS 위치** | ✅ 외부 파일 (`./page/**/*.js`) | ✅ 같은 파일 내 `<script>` 태그 |
| **자동 로드** | ✅ `index.php`에서 자동 로드 | ❌ 수동 `include` 필요 |
| **Bootstrap 클래스** | ✅ 레이아웃에 사용 | ✅ 레이아웃에 사용 |
| **`<style>` 태그** | ❌ 절대 금지 | ✅ 필수 사용 |
| **`<script>` 태그** | ❌ 절대 금지 | ✅ 필수 사용 |
| **파일 개수** | 최소 3개 (`.php`, `.css`, `.js`) | 1개 (`.php`) |
| **이식성** | 낮음 (CSS/JS 파일 함께 관리) | 높음 (단일 파일) |
| **재사용성** | 낮음 (페이지 전용) | 높음 (여러 페이지에서 사용) |
| **CSS 범위** | 페이지 전체 | 위젯 내부만 |
| **Vue 앱 마운트** | 페이지 고유 ID | 위젯 고유 ID (uniqid() 사용) |

---

### 사용 시나리오

#### 페이지 파일을 사용해야 하는 경우

- 사용자 프로필 페이지 (`/page/user/profile.php`)
- 게시글 목록 페이지 (`/page/post/list.php`)
- 설정 페이지 (`/page/user/settings.php`)
- 로그인 페이지 (`/page/user/login.php`)

**특징:**
- 페이지 고유의 레이아웃과 스타일
- 한 URL에 대응하는 전체 페이지
- CSS/JS 파일이 페이지별로 자동 로드됨

#### 위젯 파일을 사용해야 하는 경우

- 게시물 카드 (`/widgets/post/post-card.php`)
- 댓글 목록 (`/widgets/comment/comment-list.php`)
- 사용자 배지 (`/widgets/user/user-badge.php`)
- 알림 팝업 (`/widgets/notification/notification-popup.php`)

**특징:**
- 여러 페이지에서 재사용
- 독립적인 UI 컴포넌트
- 하나의 파일에 모든 코드 포함

---

### 위반 시 결과

#### 페이지 파일에서 `<style>` 또는 `<script>` 태그 사용 시

**문제점:**
- 자동 로딩 시스템과 충돌
- CSS/JS 파일이 중복 로드됨
- 페이지별 파일 관리 원칙 위반
- 코드 구조가 일관되지 않음
- 유지보수 어려움

**결과:**
- CSS 스타일이 예상과 다르게 적용될 수 있음
- JavaScript가 두 번 실행되어 버그 발생
- 페이지 로딩 속도 저하

#### 위젯 파일에서 외부 CSS/JS 파일 사용 시

**문제점:**
- 위젯을 다른 페이지에서 재사용할 때 CSS/JS 파일도 함께 관리해야 함
- 위젯의 독립성 저하
- 파일 관리가 복잡해짐
- 위젯 이식성 감소

**결과:**
- 위젯을 `include`할 때 CSS/JS 파일 경로 문제 발생
- 위젯 재사용이 어려워짐
- 여러 파일을 함께 복사해야 하는 불편함

---

### 작업 체크리스트

#### 페이지 파일 작업 시

- [ ] 페이지 파일 (`./page/**/*.php`)에 HTML과 PHP 로직만 작성
- [ ] 레이아웃은 Bootstrap 유틸리티 클래스 사용 (`class="d-flex gap-3"`)
- [ ] 스타일은 동일한 폴더의 `.css` 파일에 작성
- [ ] JavaScript는 동일한 폴더의 `.js` 파일에 작성
- [ ] 페이지 파일 내에 `<style>` 태그 없음
- [ ] 페이지 파일 내에 `<script>` 태그 없음
- [ ] CSS 파일이 자동 로드되는지 확인 (`index.php` 확인)
- [ ] JS 파일이 자동 로드되는지 확인 (`index.php` 확인)

#### 위젯 파일 작업 시

- [ ] 위젯 파일 (`./widgets/**/*.php`)에 HTML, CSS, JS 모두 포함
- [ ] 레이아웃은 Bootstrap 유틸리티 클래스 사용
- [ ] CSS는 `<style>` 태그 내에 작성
- [ ] JavaScript는 `<script>` 태그 내에 작성
- [ ] 위젯 고유의 CSS 클래스명 사용 (충돌 방지)
- [ ] Vue 앱은 고유한 ID로 마운트 (`uniqid()` 사용)
- [ ] `ready()` 함수 래퍼 사용
- [ ] 다른 페이지에서 `include`하여 재사용 가능한지 확인

---

## 아이콘 사용 가이드라인

**🔥🔥🔥 최강력 규칙: 아이콘을 추가할 때는 반드시 Font Awesome 7.1 Pro를 먼저 사용해야 합니다 🔥🔥🔥**

### 사용 가능한 아이콘 라이브러리

Sonub는 두 가지 아이콘 라이브러리를 제공합니다:

1. **Font Awesome 7.1 (Pro)** - **우선순위 1순위**
2. **Bootstrap Icons 1.13.1** - **우선순위 2순위**

**✅ 필수**: 두 아이콘 라이브러리 모두 `index.php`에서 자동으로 로드되며 **즉시 사용 가능**합니다.

**⚠️⚠️⚠️ 중요: 아이콘 선택 우선순위**

1. **먼저 Font Awesome 7.1 Pro에서 찾기** - 대부분의 경우 Font Awesome에서 원하는 아이콘을 찾을 수 있습니다
2. **Font Awesome에 없는 경우에만 Bootstrap Icons 사용**
3. **절대 금지**: Font Awesome에 해당 아이콘이 있는데도 Bootstrap Icons를 사용하는 것은 위반입니다

### Font Awesome 7.1 Pro 사용법 (우선 사용)

**🔥 최우선 사용: 아이콘이 필요할 때는 항상 Font Awesome Pro를 먼저 확인하세요**

```html
<!-- Solid 스타일 (가장 굵은 스타일) -->
<i class="fa-solid fa-house"></i>
<i class="fa-solid fa-user"></i>
<i class="fa-solid fa-envelope"></i>

<!-- Regular 스타일 (중간 굵기) -->
<i class="fa-regular fa-heart"></i>
<i class="fa-regular fa-star"></i>

<!-- Light 스타일 (얇은 스타일 - Pro 전용) -->
<i class="fa-light fa-house"></i>
<i class="fa-light fa-user"></i>

<!-- Thin 스타일 (가장 얇은 스타일 - Pro 전용) -->
<i class="fa-thin fa-house"></i>
<i class="fa-thin fa-user"></i>

<!-- Duotone 스타일 (두 가지 색상 - Pro 전용) -->
<i class="fa-duotone fa-house"></i>
<i class="fa-duotone fa-user"></i>

<!-- Brands 스타일 (브랜드 로고) -->
<i class="fa-brands fa-facebook"></i>
<i class="fa-brands fa-twitter"></i>

<!-- 크기 조정 -->
<i class="fa-solid fa-house fa-2x"></i>
<i class="fa-solid fa-house fa-3x"></i>
<i class="fa-solid fa-house fa-lg"></i>

<!-- 색상 적용 -->
<i class="fa-solid fa-house text-primary"></i>
<i class="fa-solid fa-heart text-danger"></i>

<!-- 애니메이션 (Pro 전용) -->
<i class="fa-solid fa-spinner fa-spin"></i>
<i class="fa-solid fa-heart fa-beat"></i>
```

**Font Awesome 7.1 Pro의 장점:**

- **더 많은 아이콘**: 30,000개 이상의 아이콘 (Free 버전은 2,000개)
- **더 많은 스타일**: solid, regular, light, thin, duotone (Free는 solid, regular, brands만)
- **더 나은 품질**: 더 정교하고 일관된 디자인
- **Pro 전용 기능**: 애니메이션, 듀오톤, 레이어링 등

### Bootstrap Icons 사용법 (보조 사용)

**⚠️ 주의: Font Awesome Pro에 원하는 아이콘이 없는 경우에만 사용하세요**

```html
<!-- 기본 아이콘 -->
<i class="bi bi-house"></i>
<i class="bi bi-person"></i>
<i class="bi bi-envelope"></i>

<!-- 채워진 아이콘 -->
<i class="bi bi-heart-fill"></i>
<i class="bi bi-star-fill"></i>

<!-- 크기 조정 -->
<i class="bi bi-house fs-1"></i>
<i class="bi bi-house fs-3"></i>
<i class="bi bi-house fs-5"></i>

<!-- 색상 적용 -->
<i class="bi bi-house text-primary"></i>
<i class="bi bi-heart text-danger"></i>
```

### 아이콘 선택 가이드

**🔥🔥🔥 필수 준수: 아이콘 선택 순서**

1. **먼저 Font Awesome 7.1 Pro에서 찾기** (최우선)
   - 30,000개 이상의 아이콘에서 대부분의 요구사항 충족
   - 다양한 스타일 (solid, regular, light, thin, duotone) 활용
   - 브랜드 로고가 필요한 경우 (fa-brands)
   - 애니메이션 효과가 필요한 경우
   - 더 정교하고 일관된 디자인이 필요한 경우

2. **Font Awesome에 없는 경우에만 Bootstrap Icons 사용** (보조)
   - Font Awesome Pro에서 원하는 아이콘을 찾을 수 없는 경우
   - Bootstrap 고유의 특수한 아이콘이 필요한 경우

**❌ 절대 금지:**
- Font Awesome Pro에 해당 아이콘이 있는데도 Bootstrap Icons를 사용하는 것
- 같은 의미의 아이콘을 두 라이브러리에서 혼용하는 것

### 아이콘 사용 예제

**✅ 올바른 예제: Font Awesome Pro 우선 사용**

```html
<!-- 버튼과 함께 사용 - Font Awesome Pro 우선 -->
<button class="btn btn-primary">
  <i class="fa-solid fa-plus me-2"></i>추가
</button>

<button class="btn btn-danger">
  <i class="fa-solid fa-trash me-2"></i>삭제
</button>

<!-- 네비게이션에서 사용 - Font Awesome Pro 우선 -->
<nav>
  <a href="#" class="nav-link">
    <i class="fa-solid fa-house me-1"></i>홈
  </a>
  <a href="#" class="nav-link">
    <i class="fa-solid fa-user me-1"></i>프로필
  </a>
  <a href="#" class="nav-link">
    <i class="fa-solid fa-envelope me-1"></i>메시지
  </a>
</nav>

<!-- 알림 메시지에서 사용 - Font Awesome Pro 우선 -->
<div class="alert alert-success">
  <i class="fa-solid fa-check-circle me-2"></i>
  성공적으로 저장되었습니다.
</div>

<div class="alert alert-danger">
  <i class="fa-solid fa-exclamation-triangle me-2"></i>
  오류가 발생했습니다.
</div>

<!-- Pro 전용 스타일 활용 예제 -->
<button class="btn btn-outline-primary">
  <i class="fa-light fa-heart me-2"></i>좋아요 (Light 스타일)
</button>

<div class="card">
  <div class="card-body">
    <i class="fa-duotone fa-house fa-3x text-primary"></i>
    <p>듀오톤 스타일 아이콘</p>
  </div>
</div>

<!-- 로딩 애니메이션 -->
<button class="btn btn-primary" disabled>
  <i class="fa-solid fa-spinner fa-spin me-2"></i>로딩 중...
</button>
```

**❌ 잘못된 예제: Font Awesome에 있는 아이콘을 Bootstrap Icons로 사용**

```html
<!-- ❌ 금지: Font Awesome에 fa-house가 있는데 bi-house 사용 -->
<a href="#" class="nav-link">
  <i class="bi bi-house me-1"></i>홈
</a>

<!-- ✅ 올바른 방법: Font Awesome 사용 -->
<a href="#" class="nav-link">
  <i class="fa-solid fa-house me-1"></i>홈
</a>
```

### 주의사항 및 필수 규칙

**🔥🔥🔥 최강력 규칙:**

- **✅ 필수**: 아이콘이 필요할 때는 **항상 Font Awesome 7.1 Pro를 먼저 확인**
- **✅ 필수**: Font Awesome Pro에 없는 경우에만 Bootstrap Icons 사용
- **✅ 권장**: 프로젝트 전체에서 일관된 아이콘 스타일 사용
- **✅ 권장**: 같은 기능에는 동일한 아이콘 사용
- **✅ 권장**: Font Awesome Pro의 다양한 스타일 활용 (solid, regular, light, thin, duotone)

**❌ 절대 금지:**

- **❌ 금지**: Font Awesome Pro에 해당 아이콘이 있는데도 Bootstrap Icons를 사용하는 것
- **❌ 금지**: 동일한 의미를 가진 두 개의 다른 아이콘 혼용 금지
- **❌ 금지**: 아이콘 라이브러리를 별도로 추가 로드하지 마세요 (이미 로드됨)

**위반 시 결과:**

- 프로젝트 일관성 저하
- Font Awesome Pro 구독 비용 낭비
- 더 나은 아이콘을 사용할 기회 상실
