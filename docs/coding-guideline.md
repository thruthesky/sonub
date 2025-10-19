# Sonub 코딩 가이드라인

## 목차

- [Sonub 코딩 가이드라인](#sonub-코딩-가이드라인)
  - [목차](#목차)
  - [개요](#개요)
    - [명칭](#명칭)
  - [PHP 코딩 가이드라인](#php-코딩-가이드라인)
  - [사용자 관리 및 검색](#사용자-관리-및-검색)
    - [개요](#개요-1)
    - [list_users() 함수](#list_users-함수)
  - [디자인 및 스타일링 표준](#디자인-및-스타일링-표준)
  - [JavaScript 프레임워크 - Vue.js 3.x](#javascript-프레임워크---vuejs-3x)
    - [Vue.js 사용 방식](#vuejs-사용-방식)
    - [MPA 방식의 장점](#mpa-방식의-장점)
    - [자동 리소스 로딩](#자동-리소스-로딩)
      - [자동 로딩 규칙](#자동-로딩-규칙)
      - [JavaScript에서 즉시 사용 가능한 객체](#javascript에서-즉시-사용-가능한-객체)
      - [자동 로딩 활용 가이드](#자동-로딩-활용-가이드)
      - [주의사항](#주의사항)
    - [Vue.js 기본 사용법](#vuejs-기본-사용법)
      - [기본 Vue 앱 생성](#기본-vue-앱-생성)
      - [HTML에서 Vue 사용](#html에서-vue-사용)
      - [컴포넌트 정의 및 사용](#컴포넌트-정의-및-사용)
      - [Firebase와 함께 사용](#firebase와-함께-사용)
      - [중요 사항](#중요-사항)
  - [프레임워크 및 라이브러리 저장 가이드라인](#프레임워크-및-라이브러리-저장-가이드라인)
    - [완전한 프레임워크 패키지](#완전한-프레임워크-패키지)
    - [단일 JavaScript 라이브러리](#단일-javascript-라이브러리)
  - [페이지별 CSS 및 JavaScript 자동 로딩](#페이지별-css-및-javascript-자동-로딩)
    - [개요](#개요-1)
    - [작동 방식](#작동-방식)
    - [사용 가이드라인](#사용-가이드라인)
      - [자동 포함](#자동-포함)
      - [파일 명명 규칙](#파일-명명-규칙)
      - [사용 시기](#사용-시기)
    - [예제](#예제)
      - [예제 1: 지원 페이지 만들기](#예제-1-지원-페이지-만들기)
      - [예제 2: 사용자 로그인 페이지](#예제-2-사용자-로그인-페이지)
      - [출력 예제](#출력-예제)
  - [공유 JavaScript 파일 로딩 - load_deferred_js()](#공유-javascript-파일-로딩---load_deferred_js)
    - [개요](#개요-2)
    - [필요한 이유](#필요한-이유)
    - [함수 위치 및 시그니처](#함수-위치-및-시그니처)
    - [사용 방법](#사용-방법)
      - [기본 사용법](#기본-사용법)
      - [실제 사용 예제](#실제-사용-예제)
    - [우선순위 시스템](#우선순위-시스템)
    - [잘못된 사용 예제 (절대 금지)](#잘못된-사용-예제-절대-금지)
    - [올바른 사용 예제](#올바른-사용-예제)
    - [장점](#장점)
    - [주의사항](#주의사항-1)
    - [사용 시나리오](#사용-시나리오)
  - [Firebase 통합 가이드라인](#firebase-통합-가이드라인)
    - [로딩 동작](#로딩-동작)
    - [JavaScript에서 사용](#javascript에서-사용)
    - [Vue.js에서 Firebase 실시간 업데이트](#vuejs에서-firebase-실시간-업데이트)
  - [라우팅 규칙](#라우팅-규칙)
    - [Nginx 라우팅 설정](#nginx-라우팅-설정)
    - [라우팅 동작 방식](#라우팅-동작-방식)
    - [URL과 페이지 파일 매핑](#url과-페이지-파일-매핑)
    - [라우팅 예제](#라우팅-예제)
    - [중요 사항](#중요-사항-1)
    - [라우팅 시스템의 장점](#라우팅-시스템의-장점)
  - [개발 시스템 시작](#개발-시스템-시작)
    - [빠른 시작 (필수 명령어)](#빠른-시작-필수-명령어)
    - [데이터베이스 관리](#데이터베이스-관리)
    - [Docker Compose 사용](#docker-compose-사용)
      - [사전 요구 사항](#사전-요구-사항)
      - [빠른 시작 명령어](#빠른-시작-명령어)
      - [로컬 개발 도메인 설정](#로컬-개발-도메인-설정)
      - [기본 구성](#기본-구성)
      - [디렉토리 구조](#디렉토리-구조)
      - [주요 기능](#주요-기능)
      - [문제 해결](#문제-해결)
    - [핫 리로드 개발 서버](#핫-리로드-개발-서버)
      - [기능](#기능)
      - [설정 및 사용](#설정-및-사용)
      - [구성](#구성)
      - [SSL 인증서](#ssl-인증서)
  - [레이아웃 파일 구조](#레이아웃-파일-구조)
    - [Sonub 메인 레이아웃 파일](#sonub-메인-레이아웃-파일)
    - [파일 구조 설명](#파일-구조-설명)
    - [레이아웃 작업 시 필수 규칙](#레이아웃-작업-시-필수-규칙)
    - [작업 예시](#작업-예시)
  - [URL 및 페이지 링크 관리 규칙](#url-및-페이지-링크-관리-규칙)
    - [URL 함수 필수 사용 규칙](#url-함수-필수-사용-규칙)
    - [제공되는 URL 함수 목록](#제공되는-url-함수-목록)
    - [올바른 URL 사용 예제](#올바른-url-사용-예제)
    - [잘못된 URL 사용 예제 (절대 금지)](#잘못된-url-사용-예제-절대-금지)
    - [위반 시 결과](#위반-시-결과-1)
    - [새로운 페이지 추가 시 절차](#새로운-페이지-추가-시-절차)
  - [CSS 및 디자인 규칙](#css-및-디자인-규칙)
    - [Sonub 기본 CSS 파일](#sonub-기본-css-파일)
    - [디자인 작업 시 필수 규칙](#디자인-작업-시-필수-규칙)
      - [1️⃣ 레이아웃 포지션 관련 (인라인 클래스 사용)](#1️⃣-레이아웃-포지션-관련-인라인-클래스-사용)
      - [2️⃣ 스타일 관련 (CSS 파일 사용)](#2️⃣-스타일-관련-css-파일-사용)
    - [CSS 및 JavaScript 파일 분리 규칙](#css-및-javascript-파일-분리-규칙)
      - [페이지 파일 (./page/**/*.php)](#페이지-파일-pagephp)
      - [위젯 파일 (./widgets/**/*.php)](#위젯-파일-widgetsphp)
      - [페이지 vs 위젯 비교표](#페이지-vs-위젯-비교표)
    - [올바른 CSS 사용 예제](#올바른-css-사용-예제)
    - [잘못된 CSS 사용 예제 (절대 금지)](#잘못된-css-사용-예제-절대-금지)
    - [위반 시 결과](#위반-시-결과-2)
    - [작업 체크리스트](#작업-체크리스트)
  - [Firebase 테스트 계정](#firebase-테스트-계정)
    - [테스트 계정 개요](#테스트-계정-개요)
    - [테스트 계정 특징](#테스트-계정-특징)
    - [테스트 계정 목록](#테스트-계정-목록)
    - [사용 예시](#사용-예시)
    - [주의사항](#주의사항-1)
    - [활용 시나리오](#활용-시나리오)
  - [필수 언어 사용 규칙](#필수-언어-사용-규칙)
    - [주석 및 텍스트 작성 규칙](#주석-및-텍스트-작성-규칙)
    - [예외 사항](#예외-사항)
    - [올바른 예제](#올바른-예제)
    - [잘못된 예제 (절대 금지)](#잘못된-예제-절대-금지)
    - [위반 시 결과](#위반-시-결과-3)

---

## 개요

- Sonub는 Social Network Hub의 약자로, 소셜 네트워크 허브입니다.
- Sonub는 필고(PHILGO) 프로젝트의 일부로 개발되고 있습니다.
- 이 문서는 Sonub 웹사이트 개발 시 준수해야 할 코딩 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.

### 명칭

- 한글 명칭: 소너브
- 한글 약칭: 소넙
- 영문 명칭: Sonub
- 영문 약칭: Sonub

---

## PHP 코딩 가이드라인

PHP 코딩 표준, API 함수 규칙, 에러 처리, 모듈 로딩 등 PHP 개발 관련 자세한 내용은 [docs/php.md](./php.md)를 참조하세요.

**주요 내용:**
- 일반 코딩 표준 (들여쓰기, UTF-8 인코딩, 주석)
- API 함수 규칙 (배열 파라미터 하나만 받기)
- 에러 처리 표준 (error() 함수 사용)
- 모듈 로딩 시스템

---

## 사용자 관리 및 검색

### 개요

사용자 관리 및 검색 기능은 `lib/user/user.crud.php`의 `list_users()` 함수를 통해 제공됩니다.

### list_users() 함수

사용자 목록을 조회하고 다양한 조건으로 필터링할 수 있는 함수입니다.

**기본 사용법:**

```php
// $_GET을 직접 전달하여 사용 (권장)
$result = list_users(array_merge($_GET, ['per_page' => 10]));

// 수동으로 파라미터 지정
$result = list_users([
    'gender' => 'M',           // 성별 필터 (M/F)
    'age_start' => 25,         // 시작 나이
    'age_end' => 35,           // 끝 나이
    'name' => '김',            // 이름 검색 (LIKE '김%')
    'page' => 1,               // 페이지 번호
    'per_page' => 10           // 페이지당 항목 수
]);
```

**반환값:**

```php
[
    'page' => 1,                    // 현재 페이지
    'per_page' => 10,               // 페이지당 항목 수
    'total' => 150,                 // 전체 사용자 수
    'total_pages' => 15,            // 전체 페이지 수
    'users' => [...]                // 사용자 배열
]
```

**필터링 옵션:**

1. **성별 필터**: `gender` - 'M' 또는 'F'
2. **나이 범위**: `age_start`, `age_end` - 만 나이 기준
3. **이름 검색**: `name` - 접두사 검색 (LIKE 'name%')
4. **페이지네이션**: `page`, `per_page` - 기본값: page=1, per_page=10

**상세 문서:**

- 자세한 사용법과 예제는 [docs/user.md](user.md)의 `list_users` 섹션을 참조하세요.
- 테스트 코드: `tests/user/list_users.test.php`

**예제 - 친구 찾기 페이지:**

```php
<?php
// $_GET을 직접 전달 - 매우 간편!
$result = list_users(array_merge($_GET, ['per_page' => 10]));

$users = $result['users'];
$total_count = $result['total'];
?>

<form method="get">
    <select name="gender">
        <option value="">전체</option>
        <option value="M">남성</option>
        <option value="F">여성</option>
    </select>

    <input type="number" name="age_start" placeholder="시작 나이">
    <input type="number" name="age_end" placeholder="끝 나이">
    <input type="text" name="name" placeholder="이름">

    <button type="submit">검색</button>
</form>

<!-- 사용자 목록 표시 -->
<?php foreach ($users as $user): ?>
    <div><?= htmlspecialchars($user['display_name']) ?></div>
<?php endforeach; ?>
```

---

## 디자인 및 스타일링 표준

- **라이트 모드만 지원**: Sonub 웹사이트는 라이트 모드만 지원합니다. 다크 모드 기능을 절대 구현하지 마세요.
- **Bootstrap 색상 사용**: 항상 Bootstrap의 기본 색상 클래스와 변수를 최대한 사용하세요.
- **커스텀 색상 금지**: 꼭 필요한 경우가 아니면 커스텀 HEX 코드나 CSS 색상 이름을 사용하지 마세요.
- **아이콘 라이브러리 - 필수 준수**:
  - **🔥🔥🔥 최강력 규칙: 아이콘을 추가할 때는 반드시 Font Awesome 7.1 Pro를 먼저 사용해야 합니다 🔥🔥🔥**
  - Font Awesome 7.1 (Pro)과 Bootstrap Icons 1.13.1을 함께 사용합니다.
  - 두 아이콘 라이브러리 모두 이미 로드되어 있으며 즉시 사용 가능합니다.
  - **✅ 우선순위 1순위**: Font Awesome 7.1 Pro - 먼저 확인하고 사용
  - **✅ 우선순위 2순위**: Bootstrap Icons - Font Awesome에 없는 경우에만 사용
  - Font Awesome: `<i class="fa-solid fa-house"></i>` 형식
  - Font Awesome Pro는 더 많은 아이콘과 스타일을 제공합니다 (solid, regular, light, thin, duotone 등)
  - Bootstrap Icons: `<i class="bi bi-house"></i>` 형식
  - **위반 금지**: Font Awesome에 해당 아이콘이 있는데 Bootstrap Icons를 사용하는 것은 금지
  - 자세한 사용법은 `docs/design-guideline.md`의 "아이콘 사용 가이드라인" 섹션 참조

---

## JavaScript 및 Vue.js 가이드라인

JavaScript, Vue.js, window.AppStore.state, window.t, window.hrefs 등의 사용법에 대한 자세한 내용은 [docs/javascript.md](./javascript.md)를 참조하세요.

**주요 내용:**
- window.AppStore.state - 전역 상태 관리 (Vue.js Reactivity Proxy)
- window.t - 다국어 번역
- window.hrefs - 페이지 URL 라우팅
- ready() 함수 - 모든 전역 객체는 반드시 ready() 안에서 사용
- Vue.js 사용 방법 및 예제

---

## 프레임워크 및 라이브러리 저장 가이드라인

### 완전한 프레임워크 패키지

완전한 프레임워크 파일(예: 모든 컴포넌트를 포함한 Bootstrap)을 다운로드할 때:

- 저장 위치: `etc/frameworks/<프레임워크-이름>/<프레임워크-이름>-x.x.x/`
- 예시: `etc/frameworks/bootstrap/bootstrap-5.3.0/`
- 이 구조는 버전 관리를 유지하고 여러 버전이 공존할 수 있게 합니다.

### 단일 JavaScript 라이브러리

단일 JavaScript 파일 라이브러리의 경우:

- 저장 위치: `/js/`에 직접 저장
- 예시: `/js/library-name-x.x.x.min.js`
- 라이브러리 버전을 추적하기 위해 버전이 포함된 파일명을 사용합니다.
- **참고**: Vue.js는 CDN으로 자동 로드되므로 별도로 저장하지 않습니다.

---

## 페이지별 CSS 및 JavaScript 자동 로딩

### 개요

Sonub는 `lib/page/page.functions.php`에 정의된 두 개의 헬퍼 함수를 통해 페이지별 CSS 및 JavaScript 파일의 자동 로딩 기능을 제공합니다. 이 함수들은 현재 제공되는 페이지에 해당하는 CSS 및 JS 파일을 자동으로 감지하고 포함시켜, 페이지별 자산을 수동으로 포함할 필요가 없습니다.

### 작동 방식

자동 로딩 시스템은 메인 `layout.php` 파일에서 호출되는 두 개의 함수를 사용합니다:

1. **`include_page_css()`** - 페이지의 PHP 파일과 같은 디렉토리에 CSS 파일이 있으면 자동으로 포함
2. **`include_page_js()`** - 페이지의 PHP 파일과 같은 디렉토리에 JavaScript 파일이 있으면 자동으로 포함

이 함수들은 다음과 같이 작동합니다:

1. `page()` 함수를 사용하여 현재 페이지 경로를 결정
2. 같은 위치에 해당하는 `.css` 또는 `.js` 파일이 있는지 확인
3. 파일이 있으면 적절한 `<link>` 또는 `<script>` 태그를 자동으로 생성하고 출력

### 사용 가이드라인

#### 자동 포함

- **수동 개입 불필요**: 올바른 명명 규칙으로 CSS 또는 JS 파일을 생성하면 자동으로 로드됩니다
- **설정보다 규칙**: PHP 페이지 파일에 맞게 파일 이름만 지정하면 됩니다
- **깔끔하고 유지보수 가능**: 보일러플레이트 코드를 줄이고 포함을 잊어버리는 것을 방지합니다

#### 파일 명명 규칙

자동 로딩이 작동하려면 다음 명명 패턴을 따르세요:

**기본 페이지:**

- PHP 페이지: `/page/about.php`
- CSS 파일: `/page/about.css` (존재하면 자동 로드)
- JS 파일: `/page/about.js` (존재하면 defer 속성과 함께 자동 로드)

**중첩된 페이지:**

- PHP 페이지: `/page/user/login.php`
- CSS 파일: `/page/user/login.css`
- JS 파일: `/page/user/login.js`

#### 사용 시기

**다음과 같은 경우 페이지별 CSS/JS 자동 로딩을 항상 사용하세요:**

- 커스텀 스타일링 또는 JavaScript 기능이 필요한 새 페이지 생성 시
- 독립된 스타일이 필요한 기능별 인터페이스 구축 시
- 전역 스타일에 영향을 주지 않아야 하는 컴포넌트 개발 시
- 특정 라우트에만 적용되는 페이지별 JavaScript 작성 시

**장점:**

- 자동 코드 분할 - 필요할 때만 CSS/JS를 로드
- 성능 향상 - 필요한 자산만 로드하여 초기 페이지 로드를 줄임
- 유지보수성 향상 - 페이지별 코드를 PHP 파일과 함께 정리
- 설정 불필요 - 파일만 생성하면 작동

### 예제

#### 예제 1: 지원 페이지 만들기

`/page/support.php`에 새 지원 페이지를 만들 때:

```php
<!-- /page/support.php -->
<div class="support-container">
    <h1>고객 지원</h1>
    <!-- 페이지 콘텐츠 -->
</div>
```

해당 파일들을 간단히 생성:

```css
/* /page/support.css */
.support-container {
  padding: 20px;
  background: var(--bs-light);
}
```

```javascript
// /page/support.js
const { createApp } = Vue;

createApp({
  data() {
    return {
      message: "지원 페이지에 오신 것을 환영합니다",
      tickets: [],
    };
  },
  methods: {
    async loadTickets() {
      // 지원 티켓 로드
      console.log("지원 티켓을 로드합니다");
    },
  },
  mounted() {
    console.log("지원 페이지 로드됨");
    this.loadTickets();
  },
}).mount("#support-app");
```

이 파일들은 `/support` 접속 시 자동으로 포함됩니다.

#### 예제 2: 사용자 로그인 페이지

`/page/user/login.php`의 로그인 페이지:

```php
<!-- /page/user/login.php -->
<div id="login-app">
  <form @submit.prevent="handleLogin">
    <div class="mb-3">
      <label class="form-label">이메일</label>
      <input type="email" class="form-control" v-model="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">비밀번호</label>
      <input type="password" class="form-control" v-model="password" required>
    </div>
    <button type="submit" class="btn btn-primary">로그인</button>
    <p v-if="error" class="text-danger mt-2">{{ error }}</p>
  </form>
</div>
```

다음을 생성:

```css
/* /page/user/login.css */
#login-app form {
  max-width: 400px;
  margin: 0 auto;
}
```

```javascript
// /page/user/login.js
const { createApp } = Vue;

createApp({
  data() {
    return {
      email: "",
      password: "",
      error: null,
    };
  },
  methods: {
    async handleLogin() {
      try {
        // Firebase 인증
        await firebase
          .auth()
          .signInWithEmailAndPassword(this.email, this.password);
        // 로그인 성공 시 리다이렉트
        window.location.href = "/dashboard";
      } catch (err) {
        this.error = "로그인에 실패했습니다: " + err.message;
      }
    },
  },
}).mount("#login-app");
```

#### 출력 예제

`https://local.sonub.com/support` 접속 시, 시스템은 자동으로 다음을 생성합니다:

```html
<link href="/page/support.css" rel="stylesheet" />
<script defer src="/page/support.js"></script>
```

이 태그들은 `layout.php`에서 호출되는 함수에 의해 페이지의 `<head>` 섹션에 자동으로 주입됩니다.

---

## 공유 JavaScript 파일 로딩 - load_deferred_js()

### 개요

`load_deferred_js()` 함수는 여러 페이지에서 공통으로 사용하는 JavaScript 파일을 중복 없이 한 번만 로드하기 위한 함수입니다.

**🔥🔥🔥 최강력 규칙: 여러 페이지에서 사용하는 JavaScript는 반드시 load_deferred_js() 함수를 사용해야 합니다 🔥🔥🔥**

### 필요한 이유

**문제 상황:**
- 동일한 JavaScript 파일을 여러 위젯이나 페이지에서 사용해야 하는 경우
- 각 위젯에서 `<script src="...">` 태그로 직접 로드하면 중복 로드 발생
- 중복 로드는 성능 저하와 예측 불가능한 동작을 유발

**해결 방법:**
- `load_deferred_js()` 함수를 사용하면 JavaScript 파일을 HTML 문서 맨 아래에서 **한 번만** 로드
- 여러 곳에서 호출해도 중복 로드 방지
- `defer` 속성으로 로드되어 페이지 렌더링을 차단하지 않음

### 함수 위치 및 시그니처

**파일 위치:** `lib/page/page.functions.php`

```php
/**
 * JavaScript 파일을 defer 속성으로 로드
 *
 * @param string $path JavaScript 파일 경로
 *   - '/'로 시작하면 완전한 경로로 간주
 *   - '/'로 시작하지 않으면 '/js/' 폴더에서 찾음
 *
 * @param int $priority 우선순위 (0-9)
 *   - 0: 가장 낮은 우선순위 (기본값)
 *   - 9: 가장 높은 우선순위
 *   - 우선순위가 높을수록 먼저 로드됨
 */
function load_deferred_js(string $path, int $priority = 0)
```

### 사용 방법

#### 기본 사용법

```php
<?php
// 위젯 또는 페이지 파일 상단에서 호출

// 예제 1: /js/ 폴더의 파일 로드 (확장자 제외)
load_deferred_js('file-upload');
// → <script defer src="/js/file-upload.js?v=..."></script>

// 예제 2: 전체 경로로 파일 로드
load_deferred_js('/lib/utils/helper.js');
// → <script defer src="/lib/utils/helper.js?v=..."></script>

// 예제 3: 우선순위 지정
load_deferred_js('jquery', 9);  // 가장 먼저 로드
load_deferred_js('app', 5);     // 중간 우선순위
load_deferred_js('plugin', 0);  // 가장 나중에 로드
?>
```

#### 실제 사용 예제

**파일 업로드 기능을 여러 위젯에서 사용하는 경우:**

```php
<!-- /widgets/post/post-list-create.php -->
<?php
/**
 * 게시물 작성 폼
 */

// 파일 업로드 JavaScript 로드 (중복 방지)
load_deferred_js('file-upload');
?>
<section id="post-list-create">
    <form>
        <input type="file" multiple onchange="handle_file_change(event, { id: 'display-files' })">
        <div id="display-files"></div>
    </form>
</section>
```

```php
<!-- /widgets/user/profile-photo-upload.php -->
<?php
/**
 * 프로필 사진 업로드 위젯
 */

// 동일한 파일 업로드 JavaScript 로드 (중복되지 않음)
load_deferred_js('file-upload');
?>
<section id="profile-photo-upload">
    <input type="file" onchange="handle_file_change(event, { id: 'profile-photo' })">
    <div id="profile-photo"></div>
</section>
```

**결과:**
- 두 위젯 모두 `load_deferred_js('file-upload')`를 호출
- 하지만 `/js/file-upload.js` 파일은 HTML 문서 맨 아래에서 **한 번만** 로드됨
- 중복 로드 방지 및 성능 향상

### 우선순위 시스템

JavaScript 로딩 순서가 중요한 경우 우선순위를 지정할 수 있습니다:

```php
<?php
// jQuery를 가장 먼저 로드 (우선순위 9)
load_deferred_js('jquery', 9);

// Vue.js를 두 번째로 로드 (우선순위 8)
load_deferred_js('vue', 8);

// 앱 스크립트를 세 번째로 로드 (우선순위 5)
load_deferred_js('app', 5);

// 플러그인을 마지막으로 로드 (우선순위 0, 기본값)
load_deferred_js('plugin');
?>
```

**생성되는 HTML:**

```html
<body>
  <!-- 페이지 콘텐츠 -->

  <!-- load_deferred_js()로 등록된 스크립트들이 우선순위 순서대로 출력 -->
  <script defer src="/js/jquery.js?v=..."></script>      <!-- 우선순위 9 -->
  <script defer src="/js/vue.js?v=..."></script>         <!-- 우선순위 8 -->
  <script defer src="/js/app.js?v=..."></script>         <!-- 우선순위 5 -->
  <script defer src="/js/plugin.js?v=..."></script>      <!-- 우선순위 0 -->
</body>
```

### 잘못된 사용 예제 (절대 금지)

```php
<!-- ❌ 절대 금지: 각 위젯에서 직접 <script> 태그 사용 -->

<!-- /widgets/post/post-list-create.php -->
<script defer src="/js/file-upload.js"></script>  <!-- 중복 로드 발생! -->

<!-- /widgets/user/profile-photo-upload.php -->
<script defer src="/js/file-upload.js"></script>  <!-- 중복 로드 발생! -->

<!-- 결과: 동일한 파일이 두 번 로드되어 성능 저하 및 오류 발생 -->
```

### 올바른 사용 예제

```php
<!-- ✅ 올바른 방법: load_deferred_js() 함수 사용 -->

<!-- /widgets/post/post-list-create.php -->
<?php load_deferred_js('file-upload'); ?>

<!-- /widgets/user/profile-photo-upload.php -->
<?php load_deferred_js('file-upload'); ?>

<!-- 결과: /js/file-upload.js 파일이 HTML 문서 맨 아래에서 한 번만 로드됨 -->
```

### 장점

1. **중복 로드 방지**: 동일한 파일을 여러 번 로드하지 않음
2. **성능 향상**: 불필요한 네트워크 요청 제거
3. **유지보수 용이**: 파일 경로를 한 곳에서 관리
4. **로딩 순서 제어**: 우선순위를 통해 로딩 순서 지정 가능
5. **캐시 버전 관리**: 자동으로 `?v=...` 파라미터 추가하여 캐시 관리

### 주의사항

- **✅ 필수**: 여러 페이지/위젯에서 사용하는 JavaScript는 반드시 `load_deferred_js()` 사용
- **✅ 필수**: 파일 경로가 `/js/` 폴더 외부인 경우 `/`로 시작하는 완전한 경로 사용
- **❌ 금지**: 동일한 JavaScript 파일을 `<script>` 태그로 직접 로드 금지
- **❌ 금지**: 페이지별 JavaScript 파일(`profile.js`)은 자동 로드되므로 `load_deferred_js()` 불필요

### 사용 시나리오

**사용해야 하는 경우:**
- 파일 업로드 기능을 여러 위젯에서 사용
- 시간 표시 함수를 여러 페이지에서 사용
- 공통 유틸리티 함수를 여러 컴포넌트에서 사용
- 서드파티 라이브러리를 여러 페이지에서 사용

**사용하지 않아도 되는 경우:**
- 페이지별 JavaScript 파일 (자동 로드됨)
- 전역 JavaScript 파일 (`/js/app.js` 등, 이미 `layout.php`에서 로드됨)
- 한 페이지에서만 사용하는 스크립트

---

## Firebase 통합 가이드라인

### 로딩 동작

**⚠️⚠️⚠️ 중요: Firebase와 Vue.js의 로딩 순서 및 사용 가능 시점 ⚠️⚠️⚠️**

Firebase JavaScript SDK와 Vue.js는 페이지의 `<body>` 태그 상단에서 순차적으로 로드됩니다:

1. **Firebase SDK 로드 위치**: `<body>` 태그 시작 직후 즉시 로드
2. **Vue.js 로드 위치**: Firebase SDK 바로 다음에 로드
3. **로딩 방식**: 모두 동기적으로 로드 (defer 또는 async 속성 없음)

이는 다음을 의미합니다:

- ✅ Firebase는 페이지 어디서나 **즉시 사용 가능**합니다
- ✅ Vue.js도 페이지 어디서나 **즉시 사용 가능**합니다
- ✅ Vue.js에서 Firebase를 **즉시 사용 가능**합니다
- ✅ Vue.js의 `mounted()` 훅에서 Firebase를 **바로 호출 가능**합니다
- ✅ 별도의 대기 시간이나 초기화 체크가 **불필요**합니다

**로딩 순서 예시:**

```html
<body>
  <!-- 1. Firebase SDK 로드 (최우선) -->
  <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-firestore-compat.js"></script>

  <!-- 2. Firebase 초기화 -->
  <script>
    firebase.initializeApp({
      /* config */
    });
  </script>

  <!-- 3. Vue.js 로드 -->
  <script src="/js/vue.global.prod.js"></script>

  <!-- 이제 Firebase와 Vue.js 모두 즉시 사용 가능 -->
  <!-- 페이지 콘텐츠 -->
  <?php include page() ?>
</body>
```

### JavaScript에서 사용

Firebase는 즉시 로드되고 초기화되므로, Vue.js 앱 내에서 Firebase를 직접 사용할 수 있습니다:

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      users: [],
    };
  },
  methods: {
    async loadUsers() {
      // Firestore에서 사용자 목록 로드
      const snapshot = await firebase.firestore().collection("users").get();

      this.users = snapshot.docs.map((doc) => ({
        id: doc.id,
        ...doc.data(),
      }));
    },
  },
  mounted() {
    // Firebase 인증 상태 감지
    firebase.auth().onAuthStateChanged((user) => {
      this.user = user;
      if (user) {
        console.log("사용자 로그인:", user.uid);
        this.loadUsers();
      } else {
        console.log("사용자 로그아웃");
      }
    });
  },
}).mount("#app");
```

### Vue.js에서 Firebase 실시간 업데이트

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      notifications: [],
      unsubscribe: null,
    };
  },
  methods: {
    setupRealtimeListener() {
      // Firestore 실시간 리스너 설정
      this.unsubscribe = firebase
        .firestore()
        .collection("notifications")
        .where("userId", "==", this.user.uid)
        .orderBy("createdAt", "desc")
        .onSnapshot((snapshot) => {
          this.notifications = snapshot.docs.map((doc) => ({
            id: doc.id,
            ...doc.data(),
          }));
        });
    },
  },
  mounted() {
    firebase.auth().onAuthStateChanged((user) => {
      if (user) {
        this.user = user;
        this.setupRealtimeListener();
      }
    });
  },
  beforeUnmount() {
    // MPA 방식이므로 자동으로 정리되지만, 명시적으로 해제 가능
    if (this.unsubscribe) {
      this.unsubscribe();
    }
  },
}).mount("#app");
```

**중요 참고 사항:**

- Vue.js의 `mounted()` 훅에서 Firebase를 바로 사용할 수 있습니다
- MPA 방식이므로 페이지 이동 시 자동으로 리스너가 정리됩니다
- 필요시 `beforeUnmount()`에서 명시적으로 리스너를 해제할 수 있습니다
- Firebase 구성은 시스템에서 자동으로 처리되므로 재초기화하지 마세요

---

## 라우팅 규칙

**⚠️⚠️⚠️ 중요: Sonub의 라우팅 시스템 동작 방식 ⚠️⚠️⚠️**

### Nginx 라우팅 설정

Sonub는 Nginx 서버에서 다음과 같이 라우팅이 설정되어 있습니다:

```nginx
index layout.php;

location / {
    try_files $uri $uri/ /layout.php?$query_string;
}
```

**🔥🔥🔥 중요 변경사항: 시작 페이지가 `index.php`에서 `layout.php`로 변경되었습니다 🔥🔥🔥**

- **이전**: 루트 폴더의 `index.php`가 모든 요청을 처리
- **현재**: 루트 폴더의 `layout.php`가 모든 요청을 처리하는 메인 진입점

### 라우팅 동작 방식

1. **존재하는 파일 접근**: 요청한 파일이 존재하면 해당 파일을 반환
2. **존재하지 않는 파일 접근**: 요청한 파일이 존재하지 않으면 **모든 요청은 `layout.php`로 전달**
3. **페이지 파일 로딩**: `layout.php`는 요청 경로를 분석하여 `/page/**/*.php` 폴더 아래의 PHP 스크립트를 로드

### URL과 페이지 파일 매핑

| 요청 URL | 로드되는 파일 |
|---------|-------------|
| `/` | `/page/index.php` |
| `/abc/def` | `/page/abc/def.php` |
| `/user/login` | `/page/user/login.php` |
| `/post/list` | `/page/post/list.php` |
| `/admin/dashboard` | `/page/admin/dashboard.php` |

### 라우팅 예제

**예제 1: 사용자 로그인 페이지**

- **URL**: `https://sonub.com/user/login`
- **Nginx**: 파일 `/user/login`이 존재하지 않음 → `layout.php`로 전달
- **layout.php**: 요청 경로 `/user/login`을 분석하여 `/page/user/login.php`를 로드

**예제 2: 게시글 목록 페이지**

- **URL**: `https://sonub.com/post/list`
- **Nginx**: 파일 `/post/list`가 존재하지 않음 → `layout.php`로 전달
- **layout.php**: 요청 경로 `/post/list`을 분석하여 `/page/post/list.php`를 로드

**예제 3: 메인 페이지**

- **URL**: `https://sonub.com/`
- **Nginx**: 디렉토리 인덱스 → `layout.php` 실행
- **layout.php**: 요청 경로가 `/`이므로 `/page/index.php`를 로드

### 중요 사항

- **✅ 필수**: 모든 페이지 파일은 `/page/` 디렉토리 아래에 저장
- **✅ 필수**: URL 경로와 동일한 구조로 PHP 파일 생성
- **✅ 필수**: 페이지 링크는 반드시 `href()` 함수 사용 (자세한 내용은 [URL 및 페이지 링크 관리 규칙](#url-및-페이지-링크-관리-규칙) 참조)
- **❌ 금지**: `/page/` 디렉토리 외부에 페이지 파일 생성 금지
- **❌ 금지**: 하드코딩된 URL 경로 사용 금지

### 라우팅 시스템의 장점

1. **깔끔한 URL**: 확장자 없는 URL 사용 가능 (`/user/login` 대신 `/user/login.php`)
2. **단일 진입점**: 모든 요청이 `layout.php`를 거쳐 공통 초기화 수행
3. **유연한 구조**: 디렉토리 구조에 따라 자유롭게 페이지 구성
4. **중앙 집중 관리**: `layout.php`에서 라우팅, 인증, 설정 등을 통합 관리

---

## 개발 시스템 시작

### 빠른 시작 (필수 명령어)

Sonub 개발을 시작하려면 **두 개의 명령어를 실행**해야 합니다:

```bash
# 1. Docker 컨테이너 시작 (Nginx + PHP-FPM)
cd ~/apps/sonub/dev/docker
docker compose up -d

# 2. 핫 리로드 서버 시작 (자동 파일 변경 감지)
cd ~/apps/sonub
npm run dev
```

**중요**: 완전한 개발 환경을 구축하려면 두 명령어를 모두 실행해야 합니다.

- `docker compose up`: 웹 서버 및 PHP 런타임 환경 제공
- `npm run dev`: 파일 변경 시 브라우저 자동 새로고침

### 데이터베이스 관리

**phpMyAdmin 접속:**

- URL: http://local.sonub.com/dev/phpMyAdmin/index.php
- 로컬 개발 환경에서 MariaDB 데이터베이스를 관리할 수 있습니다
- Docker 컨테이너가 실행 중일 때 사용 가능합니다

### Docker Compose 사용

Sonub 개발 환경은 Docker Compose를 통해 Nginx와 PHP-FPM 서비스를 실행합니다.

#### 사전 요구 사항

- Docker 및 Docker Compose 설치 필요
- 프로젝트 위치: `~/apps/sonub/`
- 로컬 개발 도메인 설정 필요 (아래 참조)

#### 빠른 시작 명령어

```bash
# Docker 디렉토리로 이동
cd ~/apps/sonub/dev/docker

# 시스템 시작 (Nginx + PHP-FPM)
docker compose up -d

# 컨테이너 상태 확인
docker compose ps

# 로그 보기
docker compose logs -f

# 시스템 중지
docker compose down

# 시스템 재시작
docker compose restart

# 고아 컨테이너 제거와 함께 시작
docker compose up -d --remove-orphans
```

#### 로컬 개발 도메인 설정

localhost/127.0.0.1 대신 로컬 개발 도메인을 사용하려면 hosts 파일에 다음 항목을 추가하세요:

**macOS/Linux의 경우:**

```bash
sudo nano /etc/hosts
# 다음 줄 추가:
127.0.0.1 local.sonub.com
```

**Windows의 경우:**

```bash
# 관리자 권한으로 편집:
# C:\Windows\System32\drivers\etc\hosts
# 다음 줄 추가:
127.0.0.1 local.sonub.com
```

이 항목을 추가한 후 다음에서 개발 사이트에 접속할 수 있습니다:

- http://local.sonub.com:8080
- https://local.sonub.com:8443

#### 기본 구성

- **웹 루트**: `~/apps/sonub`
- **HTTP 포트**: 127.0.0.1:8080
- **HTTPS 포트**: 127.0.0.1:8443
- **PHP 버전**: 8.3-fpm (커스텀 빌드)
- **Nginx 버전**: alpine (최신)
- **네트워크**: sonub-network (브리지)
- **로컬 도메인**: local.sonub.com (hosts 파일 구성 필요)

#### 디렉토리 구조

```
dev/docker/
├── compose.yaml         # Docker Compose 구성 파일
├── php.dockerfile       # PHP-FPM 커스텀 이미지
├── etc/
│   ├── nginx/
│   │   └── nginx.conf  # Nginx 메인 구성
│   └── php.ini         # PHP 구성
└── var/
    ├── log/nginx/      # Nginx 로그
    └── logs/php/       # PHP 로그
```

#### 주요 기능

- Docker Compose를 통한 간편한 서비스 관리
- 자동 Nginx 및 PHP-FPM 통합
- 볼륨 마운트를 통한 실시간 코드 반영
- 외부 로그 파일 저장

#### 문제 해결

- 포트 충돌 발생 시 compose.yaml에서 포트 번호 변경
- `docker compose logs` 명령어로 오류 확인
- ERR_UNSAFE_PORT 오류 발생 시 다른 포트(예: 8080) 사용

### 핫 리로드 개발 서버

Sonub는 파일 변경 시 브라우저를 자동으로 새로고침하는 핫 리로드 개발 서버를 제공합니다.

#### 기능

- 파일 변경 시 자동 브라우저 새로고침
- 페이지 새로고침 없이 CSS 핫 스왑
- 로컬 SSL 인증서로 HTTPS 지원
- PHP, CSS, JS 및 템플릿 파일 감시

#### 설정 및 사용

```bash
# 의존성 설치 (최초 1회만)
npm install

# 핫 리로드 서버 시작
npm run dev
```

핫 리로드 서버는:

- https://localhost:3034에서 실행 (SSL 인증서가 없으면 http)
- 모든 프로젝트 파일의 변경 사항 모니터링
- PHP 파일 변경 시 브라우저 자동 새로고침
- 전체 페이지 새로고침 없이 CSS 파일 핫 스왑
- 콘솔에 파일 변경 알림 표시

#### 구성

핫 리로드 서버는 환경 변수로 구성할 수 있습니다:

- `DOMAIN`: 개발 도메인 (기본값: localhost)
- `PORT`: 소켓 서버 포트 (기본값: 3034)
- `USE_HTTPS`: HTTPS 활성화/비활성화 (기본값: true)

#### SSL 인증서

서버는 다음 위치의 mkcert로 생성된 SSL 인증서를 사용합니다:

- `./etc/server-settings/nginx/ssl/sonub/localhost-key.pem`
- `./etc/server-settings/nginx/ssl/sonub/localhost-cert.pem`

인증서를 찾을 수 없으면 서버가 자동으로 HTTP 모드로 전환됩니다.

---

## 레이아웃 파일 구조

**⚠️⚠️⚠️ 최강력 경고: Sonub의 레이아웃 파일은 반드시 `/sonub.php`를 참조해야 한다 ⚠️⚠️⚠️**

### Sonub 메인 레이아웃 파일

- **파일 위치**: `/sonub.php` (프로젝트 루트 폴더)
- **역할**: Sonub 애플리케이션의 **인덱스 파일이자 메인 레이아웃 파일**
- **중요**: 레이아웃 관련 모든 작업은 **반드시 `/sonub.php` 파일을 참조**해야 함

### 파일 구조 설명

```
/sonub.php                    ← 🔴 인덱스 & 레이아웃 파일 (메인)
├── Header Navigation         ← 상단 네비게이션 바
├── Main Content Area         ← 3단 레이아웃 (좌/중앙/우 사이드바)
│   ├── Left Sidebar          ← 왼쪽 사이드바 (로그인, 대시보드 등)
│   ├── Main Content          ← 중앙 콘텐츠 영역 (<?php include page() ?>)
│   └── Right Sidebar         ← 오른쪽 사이드바 (활동, 통계 등)
└── Footer                    ← 하단 푸터
```

### 레이아웃 작업 시 필수 규칙

1. **✅ 레이아웃 수정 요청 시**: 개발자나 AI가 "레이아웃", "layout", "헤더", "푸터", "사이드바" 등의 표현을 사용하면 **반드시 `/sonub.php` 파일을 참조**

2. **✅ 페이지 콘텐츠 수정 시**: 실제 페이지 콘텐츠는 `/apps/sonub/` 폴더 아래의 개별 파일 수정

3. **✅ 레이아웃 포함 사항**:

   - 상단 네비게이션 (Header Navigation)
   - 좌측 사이드바 (Left Sidebar) - 로그인, 대시보드, 프로필 등
   - 우측 사이드바 (Right Sidebar) - 활동, 통계 등
   - 푸터 (Footer)
   - Bootstrap, FontAwesome, Firebase 설정

4. **✅ 콘텐츠 로딩 방식**:
   ```php
   <main class="col-12 col-md-6 col-lg-8 p-4">
       <?php include page() ?>  ← 개별 페이지 콘텐츠가 여기에 로드됨
   </main>
   ```

### 작업 예시

| 요청 내용                 | 수정할 파일                            |
| ------------------------- | -------------------------------------- |
| "레이아웃을 수정해줘"     | `/sonub.php`                           |
| "헤더를 변경해줘"         | `/sonub.php` (Header Navigation 섹션)  |
| "사이드바를 업데이트해줘" | `/sonub.php` (Left/Right Sidebar 섹션) |
| "푸터를 수정해줘"         | `/sonub.php` (Footer 섹션)             |
| "메인 페이지 콘텐츠 수정" | `/apps/sonub/index.php`                |
| "게시글 목록 페이지 수정" | `/apps/sonub/posts.php`                |

**중요 사항:**

- **절대 원칙**: "레이아웃"이라는 표현이 나오면 **무조건 `/sonub.php`**
- AI 어시스턴트도 이 규칙을 따라야 함
- 레이아웃 파일은 오직 하나: `/sonub.php`

---

## URL 및 페이지 링크 관리 규칙

**⚠️⚠️⚠️ 최강력 경고: Sonub의 모든 URL, 페이지 링크, 이동은 반드시 전용 함수를 사용해야 한다 ⚠️⚠️⚠️**

### URL 함수 필수 사용 규칙

- **파일 위치**: `lib/sonub/sonub.page_url.functions.php`
- **✅ 필수**: Sonub 관련 모든 URL, 링크, 페이지 이동은 **반드시** 이 파일의 함수만 사용
- **✅ 필수**: 하드코딩된 URL 경로 사용 절대 금지
- **✅ 필수**: 새로운 페이지 추가 시 해당 파일에 함수 추가 후 사용
- **❌ 금지**: `/sonub/profile`, `/sonub/settings` 등 직접 경로 작성 금지
- **❌ 금지**: 문자열로 URL 조합하여 사용 금지

### 제공되는 URL 함수 목록

```php
// 프로필 관련
sonub_profile_page()           // 프로필 페이지
sonub_profile_edit_page()      // 프로필 편집 페이지

// 설정 관련
sonub_settings_page()          // 설정 페이지

// 추가 페이지 함수들은 lib/sonub/sonub.page_url.functions.php 참조
```

### 올바른 URL 사용 예제

```php
<!-- 프로필 페이지 링크 -->
<a href="/<?= sonub_profile_page() ?>">프로필</a>

<!-- 프로필 편집 페이지 링크 -->
<a href="/<?= sonub_profile_edit_page() ?>">프로필 편집</a>

<!-- 설정 페이지 링크 -->
<a href="/<?= sonub_settings_page() ?>">설정</a>

<!-- JavaScript에서 사용 -->
<script>
const profileUrl = "/<?= sonub_profile_page() ?>";
window.location.href = profileUrl;
</script>
```

### 잘못된 URL 사용 예제 (절대 금지)

```php
<!-- ❌ 절대 금지 - 하드코딩된 URL -->
<a href="/sonub/profile">프로필</a>
<a href="/apps/sonub/profile">프로필</a>

<!-- ❌ 절대 금지 - 문자열 조합 -->
<a href="<?= '/sonub/' . 'profile' ?>">프로필</a>

<!-- ❌ 절대 금지 - 직접 경로 작성 -->
<script>
window.location.href = "/sonub/settings";  // 금지!
</script>
```

### 위반 시 결과

- URL 변경 시 모든 파일을 찾아 수정해야 함
- 오타로 인한 링크 오류 발생
- 유지보수 극도로 어려워짐
- 일관성 없는 URL 체계

### 새로운 페이지 추가 시 절차

1. `lib/sonub/sonub.page_url.functions.php`에 새 함수 추가
2. 함수명 규칙: `sonub_{페이지명}_page()` 형식 사용
3. 함수는 경로 문자열만 반환 (슬래시 제외)
4. 모든 코드에서 해당 함수 사용

**예시 - 새 페이지 함수 추가:**

```php
// lib/sonub/sonub.page_url.functions.php

function sonub_friends_page()
{
    return 'friends';
}

function sonub_messages_page()
{
    return 'messages';
}
```

---

## CSS 및 디자인 규칙

**⚠️⚠️⚠️ 최강력 경고: Sonub의 모든 CSS와 디자인 작업은 반드시 정해진 규칙을 따라야 한다 ⚠️⚠️⚠️**

### Sonub 기본 CSS 파일

- **파일 위치**: `apps/sonub/sonub.css`
- **역할**: Sonub 홈페이지 및 전체 페이지에서 사용되는 **기본 CSS 파일**
- **✅ 필수**: Sonub 홈페이지 관련 작업 시 **반드시** `apps/sonub/sonub.css` 파일을 참고하고 수정
- **✅ 필수**: 모든 디자인 및 UI 수정 시 이 파일의 스타일을 우선 확인
- **❌ 금지**: 인라인 스타일로 모든 디자인을 처리하는 것 금지
- **❌ 금지**: 각 페이지마다 별도의 CSS 파일을 무분별하게 생성하는 것 금지

### 디자인 작업 시 필수 규칙

**🔥🔥🔥 초강력 규칙: 레이아웃과 스타일을 명확히 구분해야 합니다 🔥🔥🔥**

#### 1️⃣ 레이아웃 포지션 관련 (인라인 클래스 사용)

- **✅ 필수**: **Bootstrap 5.3.8 유틸리티 클래스**를 `class="..."` 속성에 직접 작성
- **적용 대상**:
  - 레이아웃 (Layout): `d-flex`, `flex-column`, `flex-row` 등
  - 포지션 (Position): `position-relative`, `position-absolute` 등
  - 위치 (Alignment): `align-items-center`, `justify-content-between` 등
  - 간격 (Spacing): `gap-2`, `gap-3` 등 (레이아웃 간격)
- **이유**: 레이아웃 구조를 HTML에서 직접 확인 가능하도록 하기 위함

#### 2️⃣ 스타일 관련 (CSS 파일 사용)

- **✅ 필수**: `apps/sonub/sonub.css` 파일에 클래스를 정의하고 사용
- **적용 대상**:
  - 마진/패딩 (디자인 목적): `.custom-spacing` 등
  - 색상 (Colors): `.custom-bg`, `.text-custom` 등
  - 보더 (Borders): `.custom-border`, `.rounded-custom` 등
  - 효과 (Effects): `.shadow-custom`, `.hover-effect` 등
  - 폰트 (Typography): `.custom-font`, `.text-style` 등
- **이유**: 재사용 가능한 스타일을 중앙 집중식으로 관리하기 위함

### CSS 및 JavaScript 파일 분리 규칙

**🔥🔥🔥 최강력 규칙: 페이지와 위젯에서 CSS/JS 파일 관리 방식이 다릅니다 🔥🔥🔥**

#### 페이지 파일 (`./page/**/*.php`)

**✅ 필수: 페이지 파일에서는 CSS를 반드시 외부 파일로 분리해야 합니다**

- **CSS 분리 규칙**:
  - **✅ 필수**: 레이아웃 관련 Bootstrap CSS 유틸리티 클래스는 HTML에 직접 작성
  - **✅ 필수**: 스타일 관련 CSS는 **반드시** `./page/**/*.css` 파일로 분리
  - **✅ 자동 로드**: 페이지별 CSS 파일은 `layout.php`에서 자동으로 로드됨
  - **❌ 금지**: 페이지 파일 내에 `<style>` 태그 사용 금지 (레이아웃 관련 Bootstrap 클래스 제외)

- **JavaScript 분리 규칙**:
  - **✅ 필수**: JavaScript 코드는 **반드시** `./page/**/*.js` 파일로 분리
  - **✅ 자동 로드**: 페이지별 JS 파일은 `layout.php`에서 자동으로 로드됨
  - **❌ 금지**: 페이지 파일 내에 `<script>` 태그로 JavaScript 작성 금지

**올바른 페이지 파일 구조:**

```php
<!-- ./page/user/profile.php -->
<?php
// 페이지 고유 로직만 작성
$user = login();
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 클래스, 스타일은 CSS 파일로 분리 -->
<div class="container my-5">  <!-- Bootstrap 유틸리티 클래스 -->
    <div class="d-flex flex-column gap-3">  <!-- Bootstrap 유틸리티 클래스 -->
        <div class="profile-card">  <!-- 스타일은 profile.css에 정의 -->
            <h1 class="profile-title"><?= $user->name ?></h1>
            <p class="profile-bio"><?= $user->bio ?></p>
        </div>
    </div>
</div>

<!-- ❌ 금지: <style> 태그 사용 금지 -->
<!-- ❌ 금지: <script> 태그 사용 금지 -->
```

```css
/* ./page/user/profile.css */
/* ✅ 올바른 방법: 스타일은 외부 CSS 파일에 정의 */

.profile-card {
  background-color: var(--bs-light);
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.profile-title {
  color: var(--bs-primary);
  font-weight: bold;
  margin-bottom: 10px;
}

.profile-bio {
  color: var(--bs-body-color);
  line-height: 1.6;
}
```

```javascript
// ./page/user/profile.js
// ✅ 올바른 방법: JavaScript는 외부 JS 파일에 정의

Vue.createApp({
  data() {
    return {
      user: null,
    };
  },
  methods: {
    async loadProfile() {
      // 프로필 로드 로직
    },
  },
  mounted() {
    this.loadProfile();
  },
}).mount("#profile-app");
```

#### 위젯 파일 (`./widgets/**/*.php`)

**✅ 필수: 위젯 파일에서는 CSS와 JS를 모두 같은 PHP 파일 내에 작성합니다**

- **CSS 작성 규칙**:
  - **✅ 필수**: 위젯의 CSS는 `<style>` 태그 내에 작성
  - **✅ 권장**: 위젯 고유의 스타일만 작성 (전역 스타일 금지)
  - **✅ 권장**: 위젯별로 고유한 CSS 클래스 이름 사용 (충돌 방지)

- **JavaScript 작성 규칙**:
  - **✅ 필수**: 위젯의 JavaScript는 `<script>` 태그 내에 작성
  - **✅ 필수**: Vue 앱은 고유한 ID를 가진 컨테이너에 마운트
  - **✅ 권장**: 위젯 고유의 로직만 작성

**올바른 위젯 파일 구조:**

```php
<!-- ./widgets/post/post-card.php -->
<?php
/**
 * 게시물 카드 위젯
 *
 * @param array $post 게시물 데이터
 */
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 클래스 사용 -->
<div class="d-flex flex-column gap-2 post-card-widget" id="post-card-<?= $post['id'] ?>">
    <div class="post-card-header">
        <h3 class="post-card-title"><?= $post['title'] ?></h3>
    </div>
    <div class="post-card-body">
        <p class="post-card-content"><?= $post['content'] ?></p>
    </div>
</div>

<!-- ✅ 올바른 방법: 위젯의 CSS는 <style> 태그 내에 작성 -->
<style>
/* 위젯 고유 스타일 */
.post-card-widget {
    background-color: var(--bs-light);
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.post-card-header {
    border-bottom: 1px solid var(--bs-border-color);
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.post-card-title {
    color: var(--bs-primary);
    font-size: 1.2rem;
    font-weight: bold;
}

.post-card-content {
    color: var(--bs-body-color);
    line-height: 1.5;
}
</style>

<!-- ✅ 올바른 방법: 위젯의 JavaScript는 <script> 태그 내에 작성 -->
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                likes: <?= $post['likes'] ?>,
            };
        },
        methods: {
            async likePost() {
                // 좋아요 로직
                this.likes++;
            },
        },
    }).mount('#post-card-<?= $post['id'] ?>');
});
</script>
```

#### 페이지 vs 위젯 비교표

| 항목 | 페이지 (`./page/**/*.php`) | 위젯 (`./widgets/**/*.php`) |
|------|---------------------------|---------------------------|
| **CSS 위치** | ✅ 외부 파일 (`./page/**/*.css`) | ✅ 같은 파일 내 `<style>` 태그 |
| **JS 위치** | ✅ 외부 파일 (`./page/**/*.js`) | ✅ 같은 파일 내 `<script>` 태그 |
| **자동 로드** | ✅ `layout.php`에서 자동 로드 | ❌ 수동 include 필요 |
| **Bootstrap 클래스** | ✅ 레이아웃에 사용 | ✅ 레이아웃에 사용 |
| **`<style>` 태그** | ❌ 절대 금지 | ✅ 필수 사용 |
| **`<script>` 태그** | ❌ 절대 금지 | ✅ 필수 사용 |
| **용도** | 전체 페이지 콘텐츠 | 재사용 가능한 컴포넌트 |

#### 위반 시 결과

**페이지 파일에서 `<style>` 또는 `<script>` 태그 사용 시:**
- 자동 로딩 시스템과 충돌
- CSS/JS 파일이 중복 로드됨
- 유지보수 어려움
- 코드 구조가 일관되지 않음

**위젯 파일에서 외부 CSS/JS 파일 사용 시:**
- 위젯을 다른 페이지에서 재사용할 때 CSS/JS 파일도 함께 관리해야 함
- 위젯의 독립성 저하
- 파일 관리가 복잡해짐

### 올바른 CSS 사용 예제

```html
<!-- ✅ 올바른 예: 레이아웃은 인라인 클래스, 스타일은 CSS 파일 -->
<div class="d-flex flex-column align-items-center gap-3 custom-container">
  <div class="position-relative custom-card">
    <h2 class="custom-title">제목</h2>
    <p class="custom-text">내용</p>
  </div>
</div>
```

```css
/* apps/sonub/sonub.css */

/* 커스텀 컨테이너 스타일 */
.custom-container {
  background-color: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  border-radius: 8px;
  padding: 20px;
}

/* 커스텀 카드 스타일 */
.custom-card {
  background-color: var(--bs-secondary-bg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 16px;
}

.custom-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* 커스텀 텍스트 스타일 */
.custom-title {
  color: var(--bs-emphasis-color);
  font-weight: bold;
  margin-bottom: 12px;
}

.custom-text {
  color: var(--bs-body-color);
  line-height: 1.6;
}
```

### 잘못된 CSS 사용 예제 (절대 금지)

```html
<!-- ❌ 절대 금지: 레이아웃과 스타일을 모두 인라인 스타일로 처리 -->
<div
  style="display: flex; flex-direction: column; align-items: center; background: white; padding: 20px; border: 1px solid #ccc;"
>
  <div
    style="position: relative; background: #f5f5f5; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 16px;"
  >
    <h2 style="color: #333; font-weight: bold; margin-bottom: 12px;">제목</h2>
    <p style="color: #666; line-height: 1.6;">내용</p>
  </div>
</div>

<!-- ❌ 절대 금지: 레이아웃을 CSS 파일로만 처리 -->
<div class="custom-layout-container">
  <div class="custom-layout-card">
    <h2 class="custom-title">제목</h2>
    <p class="custom-text">내용</p>
  </div>
</div>
```

```css
/* ❌ 절대 금지: 레이아웃을 CSS 파일에 정의 */
.custom-layout-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  /* 이런 레이아웃 속성은 Bootstrap 클래스로 처리해야 함 */
}

.custom-layout-card {
  position: relative;
  /* 포지션도 Bootstrap 클래스로 처리해야 함 */
}
```

### 위반 시 결과

- HTML 구조와 레이아웃의 분리로 인한 가독성 저하
- 레이아웃 수정 시 CSS 파일과 HTML 파일을 동시에 수정해야 하는 불편함
- Bootstrap의 반응형 유틸리티 클래스 활용 불가
- 코드 유지보수 어려움

### 작업 체크리스트

**1. 레이아웃 작업 시:**

- [ ] Bootstrap 5.3.8 유틸리티 클래스 사용 확인
- [ ] HTML `class` 속성에 직접 작성 확인
- [ ] 반응형 클래스 활용 확인 (예: `d-md-flex`, `col-lg-6`)

**2. 스타일 작업 시:**

- [ ] `apps/sonub/sonub.css` 파일에 클래스 정의 확인
- [ ] 재사용 가능한 클래스명 사용 확인
- [ ] Bootstrap CSS 변수 사용 확인 (`var(--bs-body-color)` 등)

**3. 디자인 완료 후:**

- [ ] 인라인 `style` 속성이 없는지 확인
- [ ] 레이아웃이 Bootstrap 클래스로 구성되었는지 확인
- [ ] 스타일이 CSS 파일에 정의되었는지 확인

---

## Firebase 테스트 계정

**⚠️⚠️⚠️ 개발 및 테스트용 Firebase 계정 정보 ⚠️⚠️⚠️**

### 테스트 계정 개요

Sonub 개발 환경에서는 Firebase 인증 테스트를 위한 사전 설정된 테스트 계정이 제공됩니다.

### 테스트 계정 특징

- **커스텀 UID**: 각 테스트 계정의 Firebase UID는 과일 이름으로 커스텀 설정되어 있습니다
- **공통 비밀번호**: 모든 테스트 계정의 비밀번호는 `12345a,*`로 동일합니다
- **이메일 로그인**: 이메일과 비밀번호로 로그인 가능합니다

### 테스트 계정 목록

| UID        | 이메일              | 전화번호     | 비밀번호   |
| ---------- | ------------------- | ------------ | ---------- |
| apple      | apple@test.com      | +11234567890 | `12345a,*` |
| banana     | banana@test.com     | +11234567891 | `12345a,*` |
| cherry     | cherry@test.com     | +11234567892 | `12345a,*` |
| durian     | durian@test.com     | +11234567893 | `12345a,*` |
| elderberry | elderberry@test.com | +11234567894 | `12345a,*` |
| fig        | fig@test.com        | +11234567895 | `12345a,*` |
| grape      | grape@test.com      | +11234567896 | `12345a,*` |
| honeydew   | honeydew@test.com   | +11234567897 | `12345a,*` |
| jackfruit  | jackfruit@test.com  | +11234567898 | `12345a,*` |
| kiwi       | kiwi@test.com       | +11234567899 | `12345a,*` |
| lemon      | lemon@test.com      | +11234567900 | `12345a,*` |
| mango      | mango@test.com      | +11234567901 | `12345a,*` |

### 사용 예시

**로그인 테스트:**

```javascript
// banana 계정으로 로그인
firebase
  .auth()
  .signInWithEmailAndPassword("banana@test.com", "12345a,*")
  .then((userCredential) => {
    console.log("로그인 성공:", userCredential.user.uid); // "banana"
  })
  .catch((error) => {
    console.error("로그인 실패:", error.message);
  });
```

**Vue.js에서 사용:**

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      email: "apple@test.com",
      password: "12345a,*",
      error: null,
    };
  },
  methods: {
    async testLogin() {
      try {
        const result = await firebase
          .auth()
          .signInWithEmailAndPassword(this.email, this.password);
        console.log("테스트 계정 로그인 성공:", result.user.uid);
      } catch (err) {
        this.error = "로그인 실패: " + err.message;
      }
    },
  },
}).mount("#app");
```

### 주의사항

- **개발 환경 전용**: 이 계정들은 개발 및 테스트 목적으로만 사용해야 합니다
- **운영 환경 금지**: 운영 환경에서는 절대 사용하지 마세요
- **비밀번호 변경 금지**: 테스트 계정의 비밀번호를 변경하지 마세요
- **UID 일관성**: Firebase UID가 과일 이름으로 고정되어 있어 테스트 데이터 생성 시 유용합니다

### 활용 시나리오

1. **사용자 인증 테스트**: 로그인/로그아웃 기능 테스트
2. **권한 관리 테스트**: 여러 사용자 계정으로 권한 확인
3. **소셜 기능 테스트**: 친구 추가, 팔로우 등 테스트
4. **알림 기능 테스트**: 사용자 간 알림 전송 테스트
5. **채팅 기능 테스트**: 다중 사용자 채팅 테스트

---

## 필수 언어 사용 규칙

**⚠️⚠️⚠️ 최강력 경고: 모든 주석과 텍스트는 반드시 한국어로 작성해야 한다 ⚠️⚠️⚠️**

### 주석 및 텍스트 작성 규칙

- **✅ 필수**: 모든 PHP, JavaScript, HTML, CSS 파일의 주석은 **반드시 한국어**로 작성
- **✅ 필수**: 변수명, 함수명, 클래스명 등 코드 자체는 영문 사용 (camelCase, snake_case)
- **✅ 필수**: 주석, 설명, 문서화는 **무조건 한국어**로 작성
- **✅ 필수**: 사용자에게 표시되는 모든 텍스트는 **한국어**로 작성
- **❌ 금지**: 주석을 영어로 작성하는 것 절대 금지
- **❌ 금지**: UI 텍스트를 영어로 하드코딩하는 것 절대 금지

### 예외 사항

- 코드 자체 (변수명, 함수명, 클래스명)
- 기술 용어 (API, REST, JSON 등)
- 파일명과 경로
- 라이브러리 및 프레임워크 관련 코드

### 올바른 예제

```php
<?php
/**
 * 사용자 정보를 조회하는 함수
 *
 * @param int $user_id 사용자 ID
 * @return array 사용자 정보 배열
 */
function getUserInfo($user_id) {
    // 데이터베이스에서 사용자 정보 조회
    $user = db()->get('users', $user_id);

    // 사용자 정보가 없으면 빈 배열 반환
    if (!$user) {
        return [];
    }

    // 사용자 정보 반환
    return $user;
}
?>
```

```javascript
/**
 * 게시글 목록을 로드하는 함수
 * @param {number} page - 페이지 번호
 * @returns {Promise} 게시글 목록 프로미스
 */
function loadPosts(page) {
  // API 호출하여 게시글 목록 가져오기
  return fetch(`/api/posts?page=${page}`)
    .then((response) => response.json())
    .catch((error) => {
      // 에러 발생 시 콘솔에 출력
      console.error("게시글 로드 실패:", error);
    });
}
```

```html
<!-- 사용자 프로필 카드 -->
<div class="card">
  <div class="card-body">
    <!-- 프로필 이미지 -->
    <img src="<?= $user->photo ?>" alt="프로필 사진" />

    <!-- 사용자 이름 -->
    <h5 class="card-title"><?= $user->name ?></h5>

    <!-- 자기소개 -->
    <p class="card-text"><?= $user->bio ?></p>
  </div>
</div>
```

### 잘못된 예제 (절대 금지)

```php
<?php
/**
 * Get user information
 *
 * @param int $user_id User ID
 * @return array User information array
 */
function getUserInfo($user_id) {
    // Get user from database  ❌ 영어 주석 금지
    $user = db()->get('users', $user_id);

    // Return empty array if user not found  ❌ 영어 주석 금지
    if (!$user) {
        return [];
    }

    return $user;
}
?>
```

```html
<!-- User profile card -->
❌ 영어 주석 금지
<div class="card">
  <div class="card-body">
    <h5>Welcome</h5>
    ❌ 영어 UI 텍스트 금지 <button>Click here</button> ❌ 영어 버튼 텍스트 금지
  </div>
</div>
```

### 위반 시 결과

- 코드 가독성 저하
- 팀 내 일관성 파괴
- 유지보수 어려움
- 협업 효율성 감소

---
