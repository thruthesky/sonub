# Bootstrap 5 가이드라인

## 목차

- [개요](#개요)
- [Bootstrap Utility CSS 클래스 사용](#bootstrap-utility-css-클래스-사용)
  - [인라인 디자인 (권장)](#인라인-디자인-권장)
  - [별도 CSS 파일 분리](#별도-css-파일-분리)
- [페이지별 CSS 파일 관리](#페이지별-css-파일-관리)
  - [페이지 파일 (page/\*\*/\*.php)](#페이지-파일-pagephp)
  - [위젯 파일 (widgets/\*\*/\*.php)](#위젯-파일-widgetsphp)
- [Bootstrap Utility Class 가이드](#bootstrap-utility-class-가이드)
  - [레이아웃](#레이아웃)
  - [색상](#색상)
  - [타이포그래피](#타이포그래피)
  - [간격 (Spacing)](#간격-spacing)
  - [크기 (Sizing)](#크기-sizing)
- [실제 예제](#실제-예제)
  - [예제 1: 사용자 카드 (Bootstrap Utility만 사용)](#예제-1-사용자-카드-bootstrap-utility만-사용)
  - [예제 2: 게시글 목록 (Bootstrap Utility만 사용)](#예제-2-게시글-목록-bootstrap-utility만-사용)
  - [예제 3: 프로필 헤더 (일부 CSS 파일 사용)](#예제-3-프로필-헤더-일부-css-파일-사용)
- [안티패턴](#안티패턴)
  - [❌ 안티패턴 예제 1: 불필요한 CSS 파일 생성](#-안티패턴-예제-1-불필요한-css-파일-생성)
  - [❌ 안티패턴 예제 2: 레이아웃을 CSS로 작성](#-안티패턴-예제-2-레이아웃을-css로-작성)
  - [❌ 안티패턴 예제 3: 색상과 간격을 CSS로 작성](#-안티패턴-예제-3-색상과-간격을-css로-작성)
- [색상 가이드라인](#색상-가이드라인)
  - [Bootstrap 색상 사용](#bootstrap-색상-사용)
  - [커스텀 색상 사용 금지](#커스텀-색상-사용-금지)
- [Bootstrap 컴포넌트 사용법](#bootstrap-컴포넌트-사용법)
  - [컴포넌트 가이드라인](#컴포넌트-가이드라인)
  - [올바른 사용 예제](#올바른-사용-예제)
  - [반응형 디자인](#반응형-디자인)
- [요약](#요약)
  - [핵심 원칙](#핵심-원칙)
  - [참고 자료](#참고-자료)

---

## 개요

Sonub 프로젝트는 **Bootstrap 5 Utility CSS 클래스를 최우선으로 사용**합니다.

**핵심 원칙:**
- ✅ **Bootstrap Utility 클래스를 `class=...` 속성에 인라인으로 지정**
- ✅ **레이아웃, 색상, 간격, 크기 등 모든 스타일을 Utility 클래스로 작성**
- ✅ **별도 CSS 파일은 최소화** (Bootstrap으로 표현 불가능한 경우에만 사용)

---

## Bootstrap Utility CSS 클래스 사용

### 인라인 디자인 (권장)

**🔥🔥🔥 최강력 규칙: 가능한 모든 경우에 Bootstrap Utility CSS 클래스를 class="..." 속성에 인라인으로 지정하세요 🔥🔥🔥**

Bootstrap Utility 클래스는 레이아웃, 색상, 간격, 크기, 타이포그래피 등 대부분의 디자인 요소를 커버합니다.

**✅ 권장 예제:**

```html
<!-- ✅ Bootstrap Utility 클래스로 완전한 디자인 -->
<div class="container py-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-3 text-primary">사용자 프로필</h5>
                    <div class="d-flex align-items-center mb-2">
                        <img src="..." class="rounded-circle me-2" style="width: 50px; height: 50px; object-fit: cover;">
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

**장점:**
- ✅ Bootstrap 컴포넌트 컨벤션 준수
- ✅ 별도 CSS 파일 불필요 (유지보수 용이)
- ✅ 반응형 디자인 쉬운 구현 (Bootstrap 기반 클래스)
- ✅ 일관된 디자인 시스템 유지 (col-md-6, d-none d-md-block 등)

### 별도 CSS 파일 분리

**Bootstrap으로 표현 불가능한 스타일만** 별도 CSS 파일로 분리합니다.

**분리가 필요한 경우:**
- ⚠ Bootstrap에 없는 애니메이션
- ⚠ 복잡한 그라디언트 배경
- ⚠ 특수한 CSS 트랜지션
- ⚠ 복잡한 Hover 효과

**예제:**

```css
/* page/user/profile.css - Bootstrap으로 불가능한 스타일만 */

/* 특수 애니메이션 */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-card-enter {
    animation: fadeIn 0.3s ease-out;
}

/* 복잡한 그라디언트 */
.profile-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

---

## 페이지별 CSS 파일 관리

### 페이지 파일 (page/**/*.php)

페이지 파일에서 CSS를 분리하는 경우, **같은 폴더에 *.css 파일을 생성**하고 `load_page_css()` 함수로 자동 로드합니다.

**파일 구조:**
```
page/user/
├── profile.php        # 페이지 파일
└── profile.css        # CSS 파일 (필요시만 생성)
```

**page/user/profile.php:**
```php
<!-- Bootstrap Utility 클래스로 대부분의 디자인 작성 -->
<div class="container py-4">
    <div class="card shadow-sm profile-card-enter">
        <div class="card-body p-4">
            <div class="profile-header-gradient text-white p-3 rounded mb-3">
                <h3 class="mb-0"><?= htmlspecialchars($user->display_name) ?></h3>
            </div>
            <!-- 기타 내용 -->
        </div>
    </div>
</div>

<?php
// CSS 파일 자동 로드 (profile.css가 있으면 자동으로 <link> 태그 추가)
load_page_css();
?>
```

**page/user/profile.css:**
```css
/* Bootstrap으로 표현 불가능한 스타일만 */
.profile-card-enter {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.profile-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

**주의**: `load_page_css()` 함수는 페이지 파일의 경로와 같은 경로의 CSS 파일을 찾아서 자동으로 로드합니다.

### 위젯 파일 (widgets/**/*.php)

위젯 파일은 `<style>` 태그 내부에 CSS를 작성합니다.

**widgets/user/profile-card.php:**
```php
<div class="card shadow-sm profile-widget">
    <div class="card-body p-3">
        <!-- Bootstrap Utility 클래스로 디자인 -->
        <div class="d-flex align-items-center">
            <img src="..." class="rounded-circle me-2" style="width: 40px; height: 40px;">
            <h6 class="mb-0">홍길동</h6>
        </div>
    </div>
</div>

<style>
/* Bootstrap으로 표현 불가능한 스타일만 */
.profile-widget:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>
```

---

## Bootstrap Utility Class 가이드

### 레이아웃

**컨테이너:**
```html
<div class="container">고정 너비</div>
<div class="container-fluid">전체 너비</div>
```

**그리드:**
```html
<div class="row">
    <div class="col-md-6">50% (중간 화면 이상)</div>
    <div class="col-md-6">50% (중간 화면 이상)</div>
</div>

<div class="row g-3">간격 3 적용</div>
```

**Flexbox:**
```html
<div class="d-flex justify-content-between align-items-center">
    <span>왼쪽</span>
    <span>오른쪽</span>
</div>

<div class="d-flex flex-column">세로 정렬</div>
```

### 색상

**텍스트 색상:**
```html
<p class="text-primary">파란색</p>
<p class="text-success">초록색</p>
<p class="text-danger">빨간색</p>
<p class="text-muted">회색</p>
<p class="text-dark">검은색</p>
```

**배경 색상:**
```html
<div class="bg-primary text-white">파란 배경</div>
<div class="bg-light">밝은 배경</div>
<div class="bg-white">흰색 배경</div>
```

**투명도:**
```html
<div class="bg-primary bg-opacity-25">25% 투명도</div>
<div class="bg-primary bg-opacity-50">50% 투명도</div>
```

### 타이포그래피

**글자 크기:**
```html
<p class="fs-1">가장 큰 텍스트</p>
<p class="fs-5">중간 텍스트</p>
<p style="font-size: 0.875rem;">작은 텍스트 (14px)</p>
```

**글자 굵기:**
```html
<p class="fw-bold">굵게</p>
<p class="fw-normal">보통</p>
<p class="fw-light">얇게</p>
```

**텍스트 정렬:**
```html
<p class="text-start">왼쪽 정렬</p>
<p class="text-center">가운데 정렬</p>
<p class="text-end">오른쪽 정렬</p>
```

### 간격 (Spacing)

**Padding:**
```html
<div class="p-3">전체 패딩 간격 3</div>
<div class="px-4">좌우 간격 4</div>
<div class="py-2">상하 간격 2</div>
<div class="pt-5">상단 간격 5</div>
```

**Margin:**
```html
<div class="m-3">전체 바깥 여백 3</div>
<div class="mx-auto">좌우 자동 (가운데 정렬)</div>
<div class="mb-4">하단 여백 4</div>
```

**Gap (Flexbox, Grid):**
```html
<div class="d-flex gap-2">요소 간 간격 2</div>
<div class="row g-3">그리드 간격 3</div>
```

### 크기 (Sizing)

**너비:**
```html
<div class="w-25">25% 너비</div>
<div class="w-50">50% 너비</div>
<div class="w-100">100% 너비</div>
```

**높이:**
```html
<div class="h-25">25% 높이</div>
<div class="h-100">100% 높이</div>
```

**최소/최대 크기:**
```html
<div class="min-w-0">최소 너비 0</div>
<div class="min-vh-100">최소 높이 100vh</div>
```

---

## 실제 예제

### 예제 1: 사용자 카드 (Bootstrap Utility만 사용)

```html
<div class="card shadow-sm">
    <div class="card-body p-3 d-flex align-items-center">
        <!-- 프로필 사진 -->
        <img src="photo.jpg"
             class="rounded-circle me-2"
             style="width: 50px; height: 50px; object-fit: cover;">

        <!-- 사용자 정보 -->
        <div class="flex-grow-1 min-w-0">
            <h6 class="mb-0 text-truncate text-dark">홍길동</h6>
            <p class="mb-0 text-muted" style="font-size: 0.75rem;">2024-01-15</p>
        </div>

        <!-- 버튼 -->
        <button class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            친구 추가
        </button>
    </div>
</div>
```

### 예제 2: 게시글 목록 (Bootstrap Utility만 사용)

```html
<div class="container py-4">
    <h1 class="mb-4">게시글 목록</h1>

    <div class="row g-3">
        <!-- 게시글 카드 -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-2 text-primary">게시글 제목</h5>
                    <p class="card-text text-muted mb-3">게시글 내용...</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size: 0.875rem;">2024-01-15</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">자세히 보기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 예제 3: 프로필 헤더 (일부 CSS 파일 사용)

**HTML (Bootstrap Utility 중심):**
```html
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="profile-header-gradient text-white p-4 rounded-top">
            <div class="d-flex align-items-center">
                <img src="photo.jpg"
                     class="rounded-circle border border-3 border-white me-3"
                     style="width: 80px; height: 80px; object-fit: cover;">
                <div>
                    <h3 class="mb-1">홍길동</h3>
                    <p class="mb-0 opacity-75">hong@example.com</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- 프로필 내용 -->
        </div>
    </div>
</div>
```

**CSS (Bootstrap으로 불가능한 그라디언트만):**
```css
/* page/user/profile.css */
.profile-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

---

## 안티패턴

### ❌ 안티패턴 예제 1: 불필요한 CSS 파일 생성

```html
<!-- ❌ 잘못된 예: Bootstrap으로 가능한데 CSS 파일 사용 -->
<div class="user-card">
    <div class="user-card-body">
        <h6 class="user-name">홍길동</h6>
    </div>
</div>

<style>
/* ❌ Bootstrap Utility로 해결할 수 있는데도 별도 CSS 작성 */
.user-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.25rem;
}
.user-card-body {
    padding: 1rem;
}
.user-name {
    margin-bottom: 0;
    color: #212529;
}
</style>
```

**✅ 올바른 예제:**
```html
<!-- ✅ Bootstrap Utility 클래스 사용 -->
<div class="card shadow-sm">
    <div class="card-body p-3">
        <h6 class="mb-0 text-dark">홍길동</h6>
    </div>
</div>
```

### ❌ 안티패턴 예제 2: 레이아웃을 CSS로 작성

```html
<!-- ❌ 잘못된 예: Flexbox 레이아웃을 CSS로 작성 -->
<div class="profile-container">
    <div class="profile-left">왼쪽</div>
    <div class="profile-right">오른쪽</div>
</div>

<style>
.profile-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
```

**✅ 올바른 예제:**
```html
<!-- ✅ Bootstrap Utility 클래스 사용 -->
<div class="d-flex justify-content-between align-items-center">
    <div>왼쪽</div>
    <div>오른쪽</div>
</div>
```

### ❌ 안티패턴 예제 3: 색상과 간격을 CSS로 작성

```html
<!-- ❌ 잘못된 예: 색상과 간격을 CSS로 작성 -->
<div class="custom-alert">
    <p class="custom-text">알림 메시지</p>
</div>

<style>
.custom-alert {
    background-color: #d1ecf1;
    padding: 1rem;
    margin-bottom: 1rem;
}
.custom-text {
    color: #0c5460;
    margin-bottom: 0;
}
</style>
```

**✅ 올바른 예제:**
```html
<!-- ✅ Bootstrap Utility 클래스 사용 -->
<div class="alert alert-info mb-3">
    <p class="mb-0 text-info-emphasis">알림 메시지</p>
</div>
```

---

## 요약

### 핵심 원칙

1. **✅ Bootstrap Utility 클래스를 최우선으로 사용**
   - 레이아웃: `d-flex`, `row`, `col-*`, `container`
   - 색상: `text-*`, `bg-*`
   - 간격: `p-*`, `m-*`, `gap-*`
   - 크기: `w-*`, `h-*`

2. **✅ 별도 CSS 파일은 최소화**
   - Bootstrap으로 불가능한 스타일만 분리
   - 페이지: `load_page_css()` 함수로 자동 로드
   - 위젯: `<style>` 태그 사용

3. **✅ 반응형 디자인 쉽게 구현**
   - Bootstrap 기반 클래스 사용
   - Bootstrap 간격 쉽게 조정 (0~5)
   - Bootstrap 반응형 유틸리티 클래스 활용

### 참고 자료

- Bootstrap 5 공식 가이드: https://getbootstrap.com/docs/5.3/utilities/
- Bootstrap Icons: https://icons.getbootstrap.com/
- [docs/design-guideline.md](./design-guideline.md) - 디자인 가이드라인

---

## 색상 가이드라인

### Bootstrap 색상 사용

**✅ 필수: 항상 Bootstrap의 기본 색상 클래스와 변수를 사용하세요**

**권장하는 Bootstrap 색상 유틸리티:**
- **배경**: `bg-primary`, `bg-secondary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-light`, `bg-dark`, `bg-white`
- **텍스트**: `text-primary`, `text-secondary`, `text-success`, `text-danger`, `text-warning`, `text-info`, `text-light`, `text-dark`, `text-white`, `text-muted`
- **테두리**: `border-primary`, `border-secondary` 등

### 커스텀 색상 사용 금지

**❌ 금지:**
- HEX 색상 코드 사용 금지 (예: `#FF5733`)
- Bootstrap 팔레트 외의 CSS 색상 이름 사용 금지 (예: `color: red`)

**⚠️ 예외:**
- 브랜딩 요구사항에 꼭 필요한 경우에만 커스텀 색상 사용 가능

---

## Bootstrap 컴포넌트 사용법

### 컴포넌트 가이드라인

**핵심 원칙:**
- ✅ Bootstrap 컴포넌트를 과도한 커스터마이징 없이 그대로 사용
- ✅ 일관성을 위해 Bootstrap의 기본 스타일링 활용
- ✅ 간격, 크기, 레이아웃에는 Bootstrap 유틸리티 클래스 사용

### 올바른 사용 예제

**✅ 좋은 예: Bootstrap 색상 클래스 사용**
```html
<div class="bg-light text-dark p-3">
  <h1 class="text-primary">환영합니다</h1>
  <button class="btn btn-success">제출</button>
</div>
```

**❌ 나쁜 예: 커스텀 색상 사용**
```html
<div style="background-color: #f0f0f0; color: #333;">
  <h1 style="color: blue;">환영합니다</h1>
  <button style="background: green;">제출</button>
</div>
```

### 반응형 디자인

**필수 규칙:**
- ✅ 항상 Bootstrap의 반응형 그리드 시스템 사용
- ✅ 다양한 화면 크기에서 레이아웃 테스트
- ✅ Bootstrap의 반응형 유틸리티 클래스 활용

**반응형 그리드 예제:**
```html
<div class="container">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <!-- 모바일: 100%, 태블릿: 50%, 데스크톱: 33% -->
        </div>
    </div>
</div>
```

**반응형 유틸리티 예제:**
```html
<!-- 중간 화면 이상에서만 표시 -->
<div class="d-none d-md-block">태블릿/데스크톱에서만 보임</div>

<!-- 작은 화면에서만 표시 -->
<div class="d-block d-md-none">모바일에서만 보임</div>
```
