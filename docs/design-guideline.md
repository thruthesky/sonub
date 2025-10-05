Sonub 웹사이트 개발을 위한 디자인 가이드라인
================================================

## 목차
- [개요](#개요)
- [페이지 구조 및 파일 로딩](#페이지-구조-및-파일-로딩)
- [디자인 철학](#디자인-철학)
- [색상 가이드라인](#색상-가이드라인)
- [Bootstrap 사용법](#bootstrap-사용법)

## 개요
- 이 문서는 Sonub 웹사이트 개발을 위한 디자인 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.

## 페이지 구조 및 파일 로딩

### MPA (Multi-Page Application) 구조

Sonub는 PHP 기반 MPA 방식을 사용하며, **모든 필수 리소스는 `index.php`에서 자동으로 로드됩니다.**

### 자동 로드되는 리소스

`index.php`에서 다음 항목들이 자동으로 로드되므로 **개별 페이지에서 중복 로드하지 마세요**:

#### 1. 초기화 및 설정
- ✅ `init.php` - 프로젝트 초기화 및 설정
- ✅ ROOT_DIR 상수 정의
- ✅ 모든 라이브러리 자동 로드

#### 2. CSS 프레임워크
- ✅ Bootstrap 5.3.8 CSS
- ✅ Bootstrap Icons 1.13.1
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

## 색상 가이드라인

### Bootstrap 색상 사용
- **항상** Bootstrap의 기본 색상 클래스와 변수를 사용하세요
- **권장하는** Bootstrap 색상 유틸리티:
  - 배경: `bg-primary`, `bg-secondary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-light`, `bg-dark`, `bg-white`
  - 텍스트: `text-primary`, `text-secondary`, `text-success`, `text-danger`, `text-warning`, `text-info`, `text-light`, `text-dark`, `text-white`, `text-muted`
  - 테두리: `border-primary`, `border-secondary` 등

### 커스텀 색상 사용 금지
- HEX 색상 코드를 **사용하지 마세요** (예: `#FF5733`)
- Bootstrap 팔레트 외의 CSS 색상 이름을 **사용하지 마세요** (예: `color: red`)
- **예외**: 브랜딩 요구사항에 꼭 필요한 경우에만 커스텀 색상 사용

## Bootstrap 사용법

### 컴포넌트 가이드라인
- Bootstrap 컴포넌트를 과도한 커스터마이징 없이 그대로 사용하세요
- 일관성을 위해 Bootstrap의 기본 스타일링을 활용하세요
- 간격, 크기, 레이아웃에는 Bootstrap 유틸리티 클래스를 사용하세요

### 올바른 사용 예제
```html
<!-- 좋은 예: Bootstrap 색상 클래스 사용 -->
<div class="bg-light text-dark p-3">
  <h1 class="text-primary">환영합니다</h1>
  <button class="btn btn-success">제출</button>
</div>

<!-- 나쁜 예: 커스텀 색상 사용 -->
<div style="background-color: #f0f0f0; color: #333;">
  <h1 style="color: blue;">환영합니다</h1>
  <button style="background: green;">제출</button>
</div>
```

### 반응형 디자인
- 항상 Bootstrap의 반응형 그리드 시스템을 사용하세요
- 다양한 화면 크기에서 레이아웃을 테스트하세요
- Bootstrap의 반응형 유틸리티 클래스를 사용하세요
