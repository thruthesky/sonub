Sonub (sonub.com) 웹사이트 개발 가이드라인 및 규칙:
- 이 문서는 Sonub 웹사이트 개발 시 반드시 준수해야 할 가이드라인과 규칙을 명시합니다.

# 개발 환경

Sonub 프로젝트는 Docker 기반에 LEMP(Linux, Nginx, MySQL, PHP) 스택으로 동작합니다.

## Docker 컨테이너 구성

- `sonub-nginx`: Nginx 웹 서버
- `sonub-php`: PHP-FPM 서버
- `sonub-mariadb`: MariaDB 데이터베이스 서버

---

# 표준 워크플로우

## 1단계: 문서 검토 및 개발자 통보 (필수)

1. **관련 문서 검색**: [llms-txt.md](./docs/llms-txt.md) 를 즉시 읽고, 추가적으로 `docs/**/*.md` 내의 관련 문서를 즉시 검색
2. **개발자에게 통보**:
   ```
   📋 참고 문서: 개발자가 요청을 하면, 항상 그리고 반드시 [llms-txt.md](./docs/llms-txt.md) 문서를 먼저 읽고, 이 문서에서 제시하는 docs/xxx.md 세부 문서의 요약과 예제를 보고, 개발자가 요청하는 내용과 관련이 있는 문서를 1개 이상 참고하여 그 문서의 지침을 따른다.
      - 그리고 어떤 문서를 참고하는지 개발자에게 알려준다.
   🔤 인코딩: UTF-8로 모든 파일 생성/수정
   🌐 언어: 모든 주석 및 문서는 한국어로 작성
   ```

## 2단계: UTF-8 인코딩 확인 및 선언 (필수)

```
✅ UTF-8 인코딩으로 파일 생성/수정을 시작합니다.
✅ 모든 한글 텍스트, 주석, 문서가 올바르게 표시되도록 보장합니다.
```

## 3단계: 개발 작업 수행

위의 1단계와 2단계를 완료한 후에만 실제 개발 작업을 진행합니다.
특히, 반드시 [llms-txt.md](./docs/llms-txt.md) 를 먼저 참고로 개발자의 질문과 관련이 있는 문서를 찾아 읽고, 그 문서의 지침을 따라야 합니다.

---

# "update" 명령 워크플로우

개발자가 **"update"**라고 요청하면, 현재 파일을 **모든 표준 코딩 가이드라인에 맞게 전면적으로 업데이트**합니다.

## "update" 명령이 포함하는 작업

- **필수**: "l10n" (다국어 번역) 포함. `t()->inject()` 함수로 4개 국어 번역 추가
- **필수**: CLAUDE.md의 모든 워크플로우 및 가이드라인에 맞게 수정
- **필수**: `docs/**/*.md` 참고하여 표준에 맞게 업데이트
- **필수**: 테스트 작성 및 실행하여 검증

## "update" 워크플로우 단계

### 1단계: 관련 문서 검색 및 통보
- `docs/coding-guideline.md` - PHP, CSS, JavaScript 코딩 가이드라인
- `docs/database.md` - 데이터베이스 쿼리 작성 가이드라인
- `docs/test.md` - 테스트 작성 및 실행 가이드라인
- `docs/translation.md` - 다국어 번역 가이드라인

- `docs/design-guideline.md` - 디자인 및 UI 가이드라인

### 2단계: 코드 업데이트
**PHP 코드**:
- `pdo()` 함수로 PDO 직접 사용, 플레이스홀더(?)로 SQL 인젝션 방지
- `throw error()` 또는 `throw ApiException()` 사용
- PHPDoc 형식 문서화
- **API 함수**: 배열 파라미터 하나만 받기 (`function func(array $input): mixed`)

**CSS/JavaScript**:
- **스타일 디자인**: Bootstrap CSS Utility 클래스를 사용하여 **인라인 디자인** 필수
  - 페이지 파일(`./page/**/*.php`): Bootstrap utility 클래스로 인라인 스타일 작성
  - Bootstrap으로 불가능한 스타일만 `<style>` 태그 내에 작성 (가능한 Bootstrap 클래스 우선 사용)
- **JavaScript**: 모든 JavaScript는 해당 PHP 파일의 `<script>` 태그 내에 작성
  - 페이지 파일(`./page/**/*.php`): `<script>` 태그 내에 작성
  - 위젯 파일(`./widgets/**/*.php`): `<script>` 태그 내에 작성
  - **❌ 금지**: 외부 JavaScript 파일 분리 금지
- `Vue.createApp()` 등 Vue 객체 직접 사용하되, 컴포넌트는 별도의 변수로 분리해서 `Options API`로 작성
- `ready(() => { ... })` 래퍼 필수
- API 호출 시 `func()` 함수 사용
- JavaScript 에서 다국어 번역 시 `tr()` 함수 사용

**디자인 작업 시 참고 문서**:
- `docs/design/design.md` - 전체 디자인 가이드라인 및 원칙
- `docs/design/bootstrap.md` - Bootstrap CSS Utility 클래스 사용법 및 예제
- 디자인 관련 작업 시 위 문서들을 **반드시 먼저 읽고** 참고할 것

**다국어 번역**:
- 파일 하단에 `inject_[php_file_name]_language()` 함수 정의
- `t()->inject()` 함수로 4개 국어 번역 주입
- 키는 한글로 작성

**href() 함수**:
- 모든 페이지 링크는 `href()` 함수 사용

### 3단계: 테스트 작성 및 실행
1. **테스트 종류 선택**:
   - PHP Unit Test: 함수, 로직, DB 쿼리
   - PHP E2E Test: 페이지, UI 요소, HTML
   - Playwright E2E Test: 폼 전송, JavaScript 실행 (PHP 불가능한 경우만)

2. `docs/test.md` 참고하여 `./tests` 폴더에 테스트 작성

### 4단계: UTF-8 인코딩 확인
```bash
file -I [파일경로]
```


---

# "문서 수정" 명령 워크플로우

개발자가 **"문서"**, **"문서 수정"** 요청 시, `docs/**/*.md` 문서를 찾아 적절한 위치에 추가/수정합니다.

## 워크플로우 단계

### 1단계: 문서 검색 및 분석
- 요청 내용 분석: 주제, 카테고리 파악
- 관련 문서 검색 및 내용 읽기
- **문서 크기 확인**: 현재 라인 수 체크 (1,000 라인 제한)
- 적절한 위치 선정

### 2단계: 개발자 통보
```
📋 문서 수정 계획:
- 대상 문서: docs/xxx.md
- 추가/수정 위치: [섹션 이름]
```

### 3단계: 적절한 위치 선정 기준
- 주제별 그룹핑
- 논리적 순서
- 계층 구조 유지
- 중복 방지

### 4단계: 문서 업데이트
- 마크다운 형식 일관성 유지
- 한국어로 작성
- 목차 업데이트

### 5단계: 문서 크기 재확인
- **필수**: 수정 후 최종 라인 수 확인
- **1,000 라인 초과 시**: 개발자에게 강력 경고 및 분리 권장

### 6단계: UTF-8 인코딩 확인

### 7단계: 완료 보고
```
✅ 문서 수정 완료
✅ 대상 문서: docs/xxx.md
✅ 추가/수정 위치: [섹션 이름]
✅ 최종 라인 수: XXX 라인
✅ UTF-8 인코딩 확인 완료
```
---

# "디자인 수정" 명령 워크플로우

개발자가 **"디자인 수정"** 요청 시, `docs/design-guideline.md`에 맞게 전면 재작업합니다.

## 디자인 수정 포함 작업
- `docs/design-guideline.md` 문서 읽기
- 심플하고 단조로운 디자인
- Bootstrap 레이아웃 작성
- 페이지 파일: Bootstrap으로 불가능한 스타일만 `<style>` 태그 내 작성
- 위젯/함수: Bootstrap으로 불가능한 스타일만 `<style>` 태그 내 작성
- **❌ 금지**: 외부 CSS/JS 파일 분리 금지

## 워크플로우 단계

### 1단계: 문서 검토 및 통보
```
📋 참고 문서: docs/design-guideline.md
🎨 디자인 원칙: 심플, 단조, 현대적, Bootstrap 레이아웃
```

### 2단계: 디자인 수정
- Bootstrap 레이아웃 유틸리티 클래스 사용
- Shadow 최소화
- Bootstrap 기본 색상 변수 사용
- 충분한 여백

상세 예제는 `docs/design-guideline.md` 참조

### 3단계: UTF-8 인코딩 확인

---

# 파일 인코딩 및 언어 규칙

## UTF-8 인코딩 필수
- 모든 파일은 BOM 없는 UTF-8 인코딩으로 저장
- 작업 전/후 반드시 확인
- 확인: `file -I <파일경로>`

## 언어 규칙
- 모든 문서, 주석은 한국어로 작성
- 예외: 변수명, 함수명, 기술 용어

---

# 페이지 구조 및 파일 로딩 - MPA 방식

## 자동 로드 항목
- `init.php`, Bootstrap, Vue.js, Axios, Firebase, Font Awesome, `/css/app.css`, `/js/app.js`

**CSS/JavaScript 작성 규칙**:
- ✅ 모든 CSS는 `<style>` 태그 내에 작성 (Bootstrap으로 불가능한 경우만)
- ✅ 모든 JavaScript는 `<script>` 태그 내에 작성
- ❌ 외부 CSS/JS 파일 분리 금지

**절대 금지**:
- 페이지 파일에서 `init.php`, Bootstrap, Vue.js 등 중복 로드 금지
- `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` 태그 금지
- 외부 CSS/JS 파일 분리 금지

## href() 함수 필수 사용
모든 페이지 링크는 `href()` 함수 사용:
```php
<a href="<?= href()->user->login ?>">로그인</a>
<a href="<?= href()->post->list(1, 'discussion') ?>">토론 게시판</a>
```

상세 예제는 `docs/coding-guideline.md` 참조

---

# 디자인 및 Bootstrap 규칙

- 라이트 모드만 지원 (다크 모드 금지)
- Bootstrap 기본 색상 변수 최대한 활용
- 아이콘: Font Awesome 7.1 Pro 우선, Bootstrap Icons는 대체용

상세 예제는 `docs/design-guideline.md` 참조

---

# CSS 및 JavaScript 작성 규칙

**🔥🔥🔥 최강력 규칙: 모든 CSS/JavaScript는 페이지 파일 내부에 작성해야 합니다 🔥🔥🔥**

## 페이지 파일 (`./page/**/*.php`)
- **CSS**: Bootstrap Utility 클래스 우선 사용, 불가능한 경우만 `<style>` 태그 내 작성
- **JavaScript**: `<script>` 태그 내 작성
- **❌ 금지**: 외부 CSS/JS 파일 분리 금지

## 위젯 파일 (`./widgets/**/*.php`)
- **CSS**: Bootstrap Utility 클래스 우선 사용, 불가능한 경우만 `<style>` 태그 내 작성
- **JavaScript**: `<script>` 태그 내 작성
- **CSS 클래스명**: 위젯별로 고유하게 작성
- **❌ 금지**: 외부 CSS/JS 파일 분리 금지

상세 예제는 `docs/coding-guideline.md` 참조

---

# JavaScript 프레임워크 - Vue.js 3.x

- PHP MPA 방식으로 Vue.js 3.x CDN 사용
- 페이지 이동 시 자동 정리 (MPA 장점)
- 각 페이지의 Vue 인스턴스는 독립적

---

# 테스트 가이드라인

- 중요: PHP 테스트는 반드시 `php` 명령으로 직접 실행한다.

```bash
# ✅ 올바른 방법: PHP 테스트 직접 실행
php tests/db/db.connection.test.php
php tests/friend-and-feed/get-friends.test.php
php tests/xxx/yyy/zzz.test.php

# ❌ 잘못된 방법: docker exec 사용 금지
docker exec sonub-php php /sonub/tests/db/db.connection.test.php  # 절대 금지!
docker exec sonub-php php /sonub/tests/xxx/yyy/zzz.test.php      # 절대 금지!

# Playwright E2E Test 실행 (호스트 환경)
npx playwright test tests/playwright/e2e/user-login.spec.ts
```

**중요 사항:**
- PHP Unit Test와 PHP E2E Test는 **호스트 환경에서 직접 실행**
- `docker exec` 명령은 사용하지 않음
- 테스트 파일 경로는 상대 경로 사용 (예: `tests/xxx/yyy.test.php`)


**필수: 테스트 작업 시 `docs/test.md` 문서 먼저 읽기**

## 테스트용 로그인

**Playwright 및 Chrome DevTools MCP 테스트 시 SMS 인증 없이 로그인하기:**

```
URL: https://local.sonub.com/user/login
전화번호: banana@test.com:12345a,*
```

위 전화번호를 입력하고 "Send SMS Code" 버튼을 클릭하면 SMS 인증 없이 즉시 로그인됩니다.

**사용 예시 (Playwright):**
```typescript
await page.goto('https://local.sonub.com/user/login');
await page.fill('input[type="tel"]', 'banana@test.com:12345a,*');
await page.click('button:has-text("Send SMS Code")');
// SMS 입력 없이 자동 로그인 완료
```

**사용 예시 (Chrome DevTools MCP):**
```javascript
// 로그인 페이지로 이동
navigate_page('https://local.sonub.com/user/login');
// 전화번호 입력
fill(uid, 'banana@test.com:12345a,*');
// 전송 버튼 클릭 - SMS 입력 없이 자동 로그인
click(button_uid);
```

## 테스트 종류 자동 선택
1. **PHP Unit Test**: 함수, 로직, DB 쿼리
2. **PHP E2E Test**: 페이지, UI 요소, HTML
3. **Playwright E2E Test**: 폼 전송, JavaScript 실행 (PHP 불가능한 경우만)

## 테스트 파일 저장 위치
- PHP Unit Test: `tests/[module]/[module].test.php`
- PHP E2E Test: `tests/e2e/[page-name].e2e.test.php`
- Playwright E2E Test: `tests/playwright/e2e/[page-name].spec.ts`

**필수**: 모든 테스트 파일은 `./tests` 폴더에 저장

## 테스트 실행

**🔥🔥🔥 최강력 규칙: PHP 테스트는 호스트 환경에서 `php` 명령으로 직접 실행 🔥🔥🔥**

```bash
# ✅ 올바른 방법: PHP Unit Test 직접 실행
php tests/db/db.connection.test.php
php tests/friend-and-feed/get-friends.test.php
php tests/user/user.crud.test.php

# ✅ 올바른 방법: PHP E2E Test 직접 실행
php tests/e2e/homepage.e2e.test.php
php tests/e2e/user-login.e2e.test.php

# ✅ 올바른 방법: Playwright E2E Test
npx playwright test tests/playwright/e2e/user-login.spec.ts
```

**❌ 절대 금지: docker exec 명령 사용 금지**
```bash
# ❌ 잘못된 방법 - 절대 사용하지 마세요!
docker exec sonub-php php /sonub/tests/xxx/xxx.test.php
docker exec sonub-php php /sonub/tests/friend-and-feed/get-friends.test.php
```

**중요 사항:**
- PHP Unit Test와 PHP E2E Test는 **반드시 호스트 환경에서 직접 실행**
- `docker exec` 명령은 **절대 사용하지 않음**
- 테스트 파일 경로는 상대 경로 사용 (예: `tests/xxx/yyy.test.php`)
- 호스트 환경의 PHP가 Docker 컨테이너의 MariaDB에 연결됨

상세 예제는 `docs/test.md` 참조

---

# 데이터베이스 개발 가이드라인

**필수: 데이터베이스 작업 시 `docs/database.md` 문서 먼저 읽기**

## PDO 직접 사용 (최우선)
```php
$pdo = pdo();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
```

- `pdo()` 함수로 PDO 객체 획득
- 플레이스홀더(?)로 SQL 인젝션 방지
- `db()` 쿼리 빌더는 특별한 경우에만 사용
- mysqli 직접 사용 절대 금지

상세 예제는 `docs/database.md` 참조

---

# 다국어 번역 개발 가이드라인

**필수: 번역 작업 시 `docs/translation.md`의 Standard Workflow 따르기**

## 번역 규칙
- 각 PHP 파일 하단에 `inject_[php_file_name]_language()` 함수 정의
- `t()->inject()` 함수로 번역 텍스트 주입
- 4개 국어(ko, en, ja, zh) 필수
- 키는 한글로 작성

```php
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);

// 사용
<?= t()->이름 ?>
```

상세 예제는 `docs/translation.md` 참조

---

# JavaScript 특별 지침

## func() 함수로 API 호출
**필수**: JavaScript에서 PHP API 함수 호출 시 `func()` 함수 사용

```javascript
// PHP 함수 직접 호출
const user = await func('get_user_info', { user_id: 123 });

// Firebase 인증 포함
await func('create_post', {
    title: '게시글',
    auth: true
});
```

**절대 금지**: `axios.post('/api.php')` 또는 `fetch('/api.php')` 직접 호출

## tr() 함수로 다국어 번역
**필수**: JavaScript에서 다국어 번역이 필요한 경우 `tr()` 함수 사용

```javascript
// JavaScript에서 동적 번역
const message = tr({
    ko: '환영합니다',
    en: 'Welcome',
    ja: 'ようこそ',
    zh: '欢迎'
});
alert(message);
```

**장점:**
- ✅ 사용자 언어(`window.Store.state.lang`)에 맞게 자동 번역
- ✅ 동적 언어 전환 지원 (언어 변경 시 자동 업데이트)
- ✅ Vue.js computed property에서 사용 가능

**사용 시나리오:**
- JavaScript에서 동적으로 생성되는 메시지
- 사용자 액션에 따라 변경되는 텍스트
- Vue.js computed property에서 반응형 번역

**PHP tr() vs JavaScript tr():**
- PHP `tr()`: 서버 실행 시점 번역 (정적 텍스트)
- JavaScript `tr()`: 클라이언트 실행 시점 번역 (동적 텍스트)

상세 예제는 `docs/javascript.md` 참조

## JavaScript defer 로딩 및 ready() 래퍼
**필수**: 모든 JavaScript는 defer 로딩, `ready()` 래퍼 필수

```html
<script defer src="/js/app.js"></script>

<script>
ready(() => {
    Vue.createApp({...}).mount('#app');
});
</script>
```

## 사전 로드된 라이브러리
- Firebase JS SDK, Vue.js, Axios.js는 이미 로드됨
- 중복 로드 금지

## firebase_ready() 함수
Firebase 초기화가 필요한 경우 `firebase_ready()` 사용:

```javascript
firebase_ready(() => {
    firebase.auth().onAuthStateChanged((user) => {
        console.log(user);
    });
});
```

## Vue.js 규칙
- **절대 금지**: `const { createApp, ref } = Vue;` 구조 분해 할당
- **필수**: `Vue.createApp()`, `Vue.ref()` 등 Vue 객체 직접 사용
- **필수**: 고유한 ID 컨테이너에 마운트 (`body`, `html` 금지)

```javascript
Vue.createApp({
    setup() {
        const count = Vue.ref(0);
        return { count };
    }
}).mount('#app'); // 고유 ID에 마운트
```

상세 예제는 `docs/coding-guideline.md` 참조

---

# UTF-8 인코딩 규칙

**필수**: 모든 파일은 BOM 없는 UTF-8 인코딩으로 저장

## 확인 방법
```bash
file -I docs/api.md
# 출력: charset=utf-8
```

---

