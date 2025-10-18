Sonub (sonub.com) 웹사이트 개발 가이드라인 및 규칙

이 문서는 Sonub 웹사이트 개발 시 반드시 준수해야 할 가이드라인과 규칙을 명시합니다.

# 개발 환경

**필수: Sonub 프로젝트는 Docker 기반으로 동작합니다**

## Docker 컨테이너 구성

- `sonub-nginx`: Nginx 웹 서버
- `sonub-php`: PHP-FPM 서버
- `sonub-mariadb`: MariaDB 데이터베이스 서버

## PHP 명령어 실행

**필수: PHP 명령어는 반드시 `docker exec sonub-php` 명령 사용**

```bash
# PHP Unit Test 실행
docker exec sonub-php php /sonub/tests/db/db.connection.test.php

# Playwright E2E Test 실행 (호스트 환경)
npx playwright test tests/playwright/e2e/user-login.spec.ts
```

---

# 표준 워크플로우

## 1단계: 문서 검토 및 개발자 통보 (필수)

1. **관련 문서 검색**: `docs/**/*.md` 내의 관련 문서를 즉시 검색
2. **개발자에게 통보**:
   ```
   📋 참고 문서: docs/xxx.md
   🔤 인코딩: UTF-8로 모든 파일 생성/수정
   🌐 언어: 모든 주석 및 문서는 한국어로 작성
   ```
3. **문서 읽기 및 분석**: 기존 패턴, 규칙 및 요구사항 이해
4. **개발 중 참조**: 개발하는 동안 문서 지속적으로 참조

## 2단계: UTF-8 인코딩 확인 및 선언 (필수)

```
✅ UTF-8 인코딩으로 파일 생성/수정을 시작합니다.
✅ 모든 한글 텍스트, 주석, 문서가 올바르게 표시되도록 보장합니다.
```

## 3단계: 개발 작업 수행

위의 1단계와 2단계를 완료한 후에만 실제 개발 작업을 진행합니다.

---

# "update" 명령 워크플로우

개발자가 **"update"**라고 요청하면, 현재 파일을 **모든 표준 코딩 가이드라인에 맞게 전면적으로 업데이트**합니다.

## "update" 명령이 포함하는 작업

- **필수**: "l10n" (다국어 번역) 포함. `t()->inject()` 함수로 4개 국어 번역 추가
- **필수**: CLAUDE.md의 모든 워크플로우 및 가이드라인에 맞게 수정
- **필수**: `docs/**/*.md` 참고하여 표준에 맞게 업데이트
- **필수**: 테스트 작성 및 실행하여 검증
- **필수**: PHP 파일 수정 시 자동 배포

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
- 페이지 파일(`./page/**/*.php`): 외부 `.css`, `.js` 파일로 분리
- 위젯 파일(`./widgets/**/*.php`): `<style>`, `<script>` 태그 내 작성
- `Vue.createApp()`, `Vue.ref()` 등 Vue 객체 직접 사용
- `ready(() => { ... })` 래퍼 필수
- API 호출 시 `func()` 함수 사용

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

### 5단계: 자동 배포 (PHP 파일 수정 시만)
- **PHP 파일 수정 시**: 자동 배포 실행
- **기타 파일만 수정 시**: 배포 생략
- `./deploy.sh` 실행 (내부적으로 테스트 자동 실행 및 검증)

### 6단계: 완료 보고
```
✅ 코드 업데이트 완료
✅ 코딩 가이드라인 준수 완료
✅ 다국어 번역 추가 완료
✅ UTF-8 인코딩 확인 완료
✅ 자동 배포 완료 (PHP 파일 수정 시) / 배포 생략 (기타 파일)
```

---

# "문서 수정" 명령 워크플로우

개발자가 **"문서"**, **"문서 수정"** 요청 시, `docs/**/*.md` 문서를 찾아 적절한 위치에 추가/수정합니다.

## 워크플로우 단계

### 1단계: 문서 검색 및 분석
- 요청 내용 분석: 주제, 카테고리 파악
- 관련 문서 검색 및 내용 읽기
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

### 5단계: UTF-8 인코딩 확인

### 6단계: 완료 보고
```
✅ 문서 수정 완료
✅ 배포 생략 (문서 파일만 수정)
```

---

# "디자인 수정" 명령 워크플로우

개발자가 **"디자인 수정"** 요청 시, `docs/design-guideline.md`에 맞게 전면 재작업합니다.

## 디자인 수정 포함 작업
- `docs/design-guideline.md` 문서 읽기
- 심플하고 단조로운 디자인
- Bootstrap 레이아웃 작성
- 페이지 파일: CSS 외부 파일 분리
- 위젯/함수: `<style>` 태그 내 작성

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

### 4단계: 자동 배포 (PHP 파일 수정 시만)

### 5단계: 완료 보고

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
- 페이지별 CSS/JS: 자동 로드됨

**절대 금지**:
- 페이지 파일에서 `init.php`, Bootstrap, Vue.js 등 중복 로드 금지
- `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` 태그 금지

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

# CSS 및 JavaScript 파일 분리 규칙

## 페이지 파일 (`./page/**/*.php`)
- CSS: 외부 `.css` 파일로 분리 (Bootstrap Layout Utility 클래스 제외)
- JavaScript: 외부 `.js` 파일로 분리
- `layout.php`에서 자동 로드

## 위젯 파일 (`./widgets/**/*.php`)
- CSS: `<style>` 태그 내 작성
- JavaScript: `<script>` 태그 내 작성
- CSS 클래스명은 위젯별로 고유하게 작성

상세 예제는 `docs/coding-guideline.md` 참조

---

# JavaScript 프레임워크 - Vue.js 3.x

- PHP MPA 방식으로 Vue.js 3.x CDN 사용
- 페이지 이동 시 자동 정리 (MPA 장점)
- 각 페이지의 Vue 인스턴스는 독립적

---

# 문서 및 목차 관리

- `*.md` 파일 편집 시 문서 상단에 목차(ToC) 추가
- 문서 구조 변경 시 목차 업데이트
- 목차는 모든 주요 제목(##)과 부제목(###) 반영

---

# 테스트 가이드라인

**필수: 테스트 작업 시 `docs/test.md` 문서 먼저 읽기**

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
```bash
# PHP Unit Test
docker exec sonub-php php /sonub/tests/db/db.connection.test.php

# Playwright E2E Test
npx playwright test tests/playwright/e2e/user-login.spec.ts
```

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

# 보안 규칙

**필수**: IP 주소 및 민감 정보 노출 절대 금지

## 보호 대상
- 프로덕션 서버 IP 주소
- 데이터베이스 접속 정보
- API 키 및 시크릿
- SSH 키 및 인증 정보

## AI 응답 시 마스킹
```
✅ 프로덕션 서버 업데이트 완료
```

❌ 잘못된 예: "프로덕션 서버(68.183.185.185) 업데이트 완료"

---

# "배포" 명령 워크플로우

**필수: PHP 파일 수정 완료 후 개발자가 요청하지 않아도 자동 배포**

## 자동 배포 규칙
- **PHP 파일 수정 시**: 자동 배포 실행
- **기타 파일만 수정 시**: 배포 생략 (Markdown, CSS, JavaScript, 테스트)

## 배포 스크립트 동작
`./deploy.sh` 실행 시:
1. 모든 테스트 자동 실행
2. 테스트 성공 확인
3. 버전 업데이트, Git 커밋/푸시
4. SSH로 프로덕션 서버 업데이트

## 배포 워크플로우 단계

### 1단계: 배포 전 확인
- PHP 파일 수정 여부 확인
- UTF-8 인코딩 확인 완료

### 2단계: 배포 스크립트 실행 (PHP 파일 수정 시만)
```bash
./deploy.sh
```

### 3단계: 배포 결과 확인

### 4단계: 완료 보고
```
✅ 자동 배포 완료
✅ 버전: YYYY-MM-DD-HH-MM-SS
✅ 모든 테스트 통과
✅ Git 커밋 및 푸시 완료
✅ 프로덕션 서버 업데이트 완료
```

## 배포 자동 실행 규칙

**자동 배포 실행**:
1. PHP 코드 수정 완료 시
2. PHP 파일 생성 완료 시
3. 개발자가 "배포" 명시적 요청 시

**배포 제외**:
1. 문서 수정 시 (`*.md`)
2. CSS 수정 시 (`*.css`)
3. JavaScript 수정 시 (`*.js`)
4. 테스트 작성 시

**절대 금지**:
- "배포할까요?" 묻기 금지
- PHP 파일 수정 후 배포 생략 금지
- 개발자 요청 대기 금지

---

# 요약

AI는 다음 순서로 작업합니다:

1. **문서 검토**: `docs/**/*.md` 읽기
2. **개발자 통보**: 참고 문서, UTF-8 인코딩 선언
3. **코드 작성**: 가이드라인에 맞게 작성
4. **테스트**: `./tests` 폴더에 작성 및 실행
5. **UTF-8 확인**: `file -I` 명령
6. **자동 배포**: PHP 파일 수정 시 `./deploy.sh` 실행
7. **완료 보고**: 개발자에게 통보

**핵심 원칙**:
- 모든 작업 전 관련 문서 읽기
- UTF-8 인코딩 필수
- 한국어 주석 및 문서
- PHP 파일 수정 시 자동 배포
- 테스트 필수 작성 및 실행
