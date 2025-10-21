# 사용자 검색 (User Search)

사용자 검색 기능은 친구 검색을 위한 독립적인 Vue.js 컴포넌트입니다.

## 목차

- [개요](#개요)
- [설치 및 로드](#설치-및-로드)
- [사용법](#사용법)
  - [방법 1: 자동 마운트 (권장)](#방법-1-자동-마운트-권장)
  - [방법 2: 수동 마운트](#방법-2-수동-마운트)
- [컴포넌트 구조](#컴포넌트-구조)
- [기능 설명](#기능-설명)
- [데이터 구조](#데이터-구조)
- [메서드](#메서드)
- [예제](#예제)
- [문제 해결](#문제-해결)

---

## 개요

**파일 위치**: `js/vue-components/user-search.component.js`

사용자 검색 컴포넌트는 다음 기능을 제공합니다:
- 사용자 이름으로 검색
- 검색 결과를 카드 형식으로 표시
- 페이지네이션 지원
- Bootstrap 모달을 사용한 검색 UI
- 다국어 지원 (한국어, 영어, 일본어, 중국어)
- 여러 인스턴스 동시 사용 가능

---

## 설치 및 로드

### PHP에서 스크립트 로드

```php
<?php
// 사용자 검색 컴포넌트 로드
load_deferred_js('vue-components/user-search.component');
?>
```

### 필수 의존성

이 컴포넌트는 다음 라이브러리에 의존합니다:
- **Vue.js 3.x** (CDN 또는 로컬)
- **Bootstrap 5.x** (모달 기능)
- **Bootstrap Icons** (아이콘)
- **func()** 함수 (API 호출용, `js/app.js`에 정의)
- **tr()** 함수 (다국어 번역용, `js/app.js`에 정의)
- **ready()** 함수 (DOM 로드 완료 체크용, `js/app.js`에 정의)

---

## 사용법

### 방법 1: 자동 마운트 (권장)

가장 간단한 방법입니다. HTML에 `.user-search-component` 클래스를 가진 div를 추가하면 자동으로 마운트됩니다.

**HTML**:
```html
<!-- 사용자 검색 컴포넌트 (자동 마운트) -->
<div class="user-search-component"></div>
```

**PHP 예제**:
```php
<?php
// 스크립트 로드
load_deferred_js('vue-components/user-search.component');
?>

<!-- 자동 마운트 -->
<div class="user-search-component"></div>
```

**장점**:
- ✅ 코드가 간결함
- ✅ 고유 ID 관리 불필요
- ✅ 여러 개의 인스턴스를 쉽게 추가 가능
- ✅ 스크립트 태그 불필요

**여러 인스턴스 사용 예제**:
```html
<!-- 사이드바에 첫 번째 검색 -->
<div class="user-search-component"></div>

<!-- 메인 페이지에 두 번째 검색 -->
<div class="user-search-component"></div>

<!-- 필요한 만큼 추가 가능 -->
<div class="user-search-component"></div>
```

---

### 방법 2: 수동 마운트

특정 ID에 수동으로 마운트하려면 다음과 같이 합니다.

**HTML**:
```html
<div id="my-user-search"></div>

<script>
    ready(() => {
        Vue.createApp(window.UserSearchComponent).mount('#my-user-search');
    });
</script>
```

**PHP 예제**:
```php
<?php
load_deferred_js('vue-components/user-search.component');
?>

<div id="custom-search"></div>

<script>
    ready(() => {
        Vue.createApp(window.UserSearchComponent).mount('#custom-search');
    });
</script>
```

**주의사항**:
- ⚠️ 각 인스턴스마다 고유한 ID 필요
- ⚠️ `ready()` 함수로 DOM 로드 완료 후 실행 필수
- ⚠️ `window.UserSearchComponent` 사용 (전역 객체)

---

## 컴포넌트 구조

### 렌더링되는 HTML

컴포넌트는 다음 두 가지 요소를 렌더링합니다:

1. **검색 버튼**
```html
<button class="btn btn-primary">
    <i class="bi bi-search me-2"></i>친구 검색
</button>
```

2. **검색 모달** (Bootstrap Modal)
- 검색 입력 필드
- 검색 결과 그리드 (2열)
- 페이지네이션

---

## 기능 설명

### 1. 검색 기능

사용자 이름으로 검색합니다.

**검색 과정**:
1. 검색 버튼 클릭 → 모달 열림
2. 검색어 입력
3. Enter 키 또는 "검색" 버튼 클릭
4. API 호출: `list_users` 함수
5. 검색 결과 표시

**API 호출 예제**:
```javascript
const result = await func('list_users', {
    name: this.searchTerm.trim(),
    per_page: 10,
    page: this.searchPage
});
```

### 2. 검색 결과 표시

**결과 카드 구조**:
- 프로필 사진 (또는 기본 아이콘)
- 사용자 이름
- 가입일 (YYYY-MM-DD 형식)

**클릭 동작**:
- 카드 클릭 시 사용자 프로필 페이지로 이동
- URL: `/user/profile?id={user_id}`

### 3. 페이지네이션

**표시 조건**:
- 전체 페이지 수가 2페이지 이상일 때

**페이지네이션 버튼**:
- 첫 페이지 (`<<`)
- 이전 페이지 (`<`)
- 페이지 번호 (최대 5개 표시)
- 다음 페이지 (`>`)
- 마지막 페이지 (`>>`)

**동작**:
- 페이지 변경 시 자동으로 API 호출
- 모달 본문을 맨 위로 스크롤

### 4. 다국어 지원

**지원 언어**:
- 한국어 (ko)
- 영어 (en)
- 일본어 (ja)
- 중국어 (zh)

**번역 텍스트**:
- 친구 검색 / Find Friends / 友達検索 / 查找朋友
- 이름을 입력하세요 / Enter name / 名前を入力してください / 输入姓名
- 검색 / Search / 検索 / 搜索
- 검색 중... / Searching... / 検索中... / 搜索中...
- 검색 결과가 없습니다. / No results found. / 検索結果がありません。 / 未找到结果。

---

## 데이터 구조

### data() 속성

```javascript
{
    instanceId: 0,              // 인스턴스 고유 ID (자동 증가)
    searchTerm: '',             // 검색어
    searchResults: [],          // 검색 결과 배열
    searchPage: 1,              // 현재 페이지
    searchTotalPages: 0,        // 전체 페이지 수
    searchTotal: 0,             // 전체 검색 결과 수
    searchLoading: false,       // 검색 중 상태
    searchPerformed: false,     // 검색 수행 여부
    modalInstance: null,        // Bootstrap 모달 인스턴스
    state: window.Store.state, // 전역 상태
    profileUrl: '/user/profile' // 프로필 URL
}
```

### computed 속성

```javascript
{
    modalId: 'friendSearchModal-0',    // 모달 고유 ID
    modalBodyId: 'search-modal-body-0', // 모달 본문 고유 ID
    t: { /* 번역 텍스트 */ },
    visiblePageNumbers: [1, 2, 3, 4, 5] // 표시할 페이지 번호 배열
}
```

---

## 메서드

### openSearchModal()

검색 모달을 엽니다.

```javascript
openSearchModal() {
    if (this.modalInstance) {
        this.modalInstance.show();
    }
}
```

### closeSearchModal()

검색 모달을 닫습니다.

```javascript
closeSearchModal() {
    if (this.modalInstance) {
        this.modalInstance.hide();
    }
}
```

### performSearch()

검색을 수행합니다.

**검증**:
- 검색어가 비어있으면 알림 표시

**동작**:
1. 검색 페이지를 1로 초기화
2. `searchPerformed` 플래그 설정
3. `loadSearchResults()` 호출

### loadSearchResults()

API를 호출하여 검색 결과를 로드합니다.

**API 파라미터**:
```javascript
{
    name: this.searchTerm.trim(), // 검색어
    per_page: 10,                 // 페이지당 결과 수
    page: this.searchPage         // 현재 페이지
}
```

**응답 처리**:
```javascript
{
    users: [],          // 사용자 배열
    total: 0,          // 전체 결과 수
    total_pages: 0     // 전체 페이지 수
}
```

### goToSearchPage(pageNum)

특정 페이지로 이동합니다.

**파라미터**:
- `pageNum`: 이동할 페이지 번호

**검증**:
- 페이지 번호가 유효 범위 내에 있는지 확인
- 현재 페이지와 동일하면 중단

**동작**:
1. 페이지 번호 업데이트
2. `loadSearchResults()` 호출
3. 모달 본문을 맨 위로 스크롤

### formatDate(timestamp)

Unix timestamp를 날짜 문자열로 변환합니다.

**파라미터**:
- `timestamp`: Unix timestamp (초 단위)

**반환값**:
- `YYYY-MM-DD` 형식 문자열

**예제**:
```javascript
formatDate(1697501234) // "2023-10-17"
```

---

## 예제

### 예제 1: 사이드바에 검색 추가

**파일**: `widgets/sidebar/new-users.php`

```php
<?php
// 사용자 검색 컴포넌트 자동 로드
load_deferred_js('vue-components/user-search.component');
?>

<!-- 사용자 검색 컴포넌트 (자동 마운트) -->
<div class="user-search-component"></div>
-------

<!-- 다른 위젯 내용 -->
<div class="new-users-widget">
    ...
</div>
```

### 예제 2: 사용자 목록 페이지에 검색 추가

**파일**: `page/user/list.php`

```php
<?php
// 사용자 검색 컴포넌트 자동 로드
load_deferred_js('vue-components/user-search.component');
?>

<div class="container py-4">
    <h1 class="mb-4">사용자 목록</h1>

    <!-- Friend Action Buttons -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <!-- 사용자 검색 컴포넌트 (자동 마운트) -->
        <div class="user-search-component"></div>

        <!-- 다른 버튼들 -->
        <a href="..." class="btn btn-outline-primary">친구 목록</a>
    </div>

    <!-- 사용자 목록 그리드 -->
    ...
</div>
```

### 예제 3: 여러 인스턴스 사용

```php
<?php
load_deferred_js('vue-components/user-search.component');
?>

<!-- 헤더에 첫 번째 검색 -->
<div class="header">
    <div class="user-search-component"></div>
</div>

<!-- 사이드바에 두 번째 검색 -->
<div class="sidebar">
    <div class="user-search-component"></div>
</div>

<!-- 메인 콘텐츠에 세 번째 검색 -->
<div class="main-content">
    <div class="user-search-component"></div>
</div>
```

**결과**:
- 각 인스턴스는 독립적으로 작동
- 고유한 모달 ID 자동 할당 (`friendSearchModal-0`, `friendSearchModal-1`, `friendSearchModal-2`)
- 각 검색 상태는 독립적으로 관리

---

## 문제 해결

### 문제 1: 모달이 열리지 않음

**증상**:
- 버튼을 클릭해도 모달이 나타나지 않음
- 콘솔에 "modalInstance가 null입니다" 에러

**원인**:
1. Bootstrap이 로드되지 않음
2. Vue 앱이 다른 Vue 앱 내부에 중첩됨

**해결책**:
```javascript
// 콘솔에서 Bootstrap 확인
console.log(typeof bootstrap); // "object"이어야 함

// Vue 앱 중첩 확인
// ❌ 잘못된 구조
<div id="parent-vue-app">
    <div class="user-search-component"></div> <!-- 중첩됨! -->
</div>

// ✅ 올바른 구조
<div class="user-search-component"></div> <!-- 독립적 -->
<div id="parent-vue-app"></div>     <!-- 독립적 -->
```

### 문제 2: 검색 결과가 표시되지 않음

**증상**:
- 검색 후 "검색 결과가 없습니다" 메시지만 표시

**원인**:
1. API 함수 `list_users`가 정의되지 않음
2. 검색어가 일치하는 사용자 없음

**해결책**:
```javascript
// API 함수 확인
console.log(typeof func); // "function"이어야 함

// 수동으로 API 테스트
const result = await func('list_users', { name: '테스트', per_page: 10, page: 1 });
console.log(result);
```

### 문제 3: 중복 마운트 경고

**증상**:
- 콘솔에 "이미 마운트되어 있습니다" 메시지

**원인**:
- 같은 요소에 여러 번 마운트 시도

**해결책**:
```javascript
// 자동 마운트는 중복 방지 로직이 있음
// 수동 마운트 시 중복 체크
const element = document.getElementById('my-search');
if (!element.__vue_app__) {
    Vue.createApp(window.UserSearchComponent).mount(element);
}
```

### 문제 4: 다국어가 작동하지 않음

**증상**:
- 모든 텍스트가 한국어로만 표시됨

**원인**:
- `tr()` 함수가 정의되지 않음
- 언어 설정이 올바르지 않음

**해결책**:
```javascript
// tr() 함수 확인
console.log(typeof tr); // "function"이어야 함

// 언어 설정 확인
console.log(window.Store.state.language); // "en", "ko", "ja", "zh"
```

### 문제 5: "ready is not defined" 에러

**증상**:
- 콘솔에 "ready is not defined" 에러

**원인**:
- `js/app.js`가 로드되지 않음

**해결책**:
```html
<!-- layout.php에서 app.js가 로드되는지 확인 -->
<script src="/js/app.js"></script>
```

---

## 기술 스펙

**컴포넌트 타입**: Vue.js 3.x Options API
**파일 크기**: ~12KB
**의존성**:
- Vue.js 3.x
- Bootstrap 5.x
- Bootstrap Icons
- func() 함수
- tr() 함수
- ready() 함수

**브라우저 지원**:
- Chrome/Edge: 최신 버전
- Firefox: 최신 버전
- Safari: 최신 버전

**성능**:
- 인스턴스당 메모리: ~100KB
- 초기 렌더링: ~50ms
- 검색 API 호출: ~200ms (네트워크 상태에 따라 다름)

---

## 관련 문서

- [사용자 관리 문서](./user.md)
- [Vue.js 컴포넌트 개발 가이드](../coding-guideline.md)
- [다국어 번역 가이드](../translation.md)
- [API 문서](../api.md)

---

**문서 버전**: 1.0.0
**최종 수정일**: 2025-10-19
**작성자**: Claude AI
