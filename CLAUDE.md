Sonub (sonub.com) 웹사이트 개발 가이드라인 및 규칙

- 이 문서는 Sonub 웹사이트 개발 시 반드시 준수해야 할 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.
- 표준 워크플로우는 예외 없이 반드시 따라야 합니다.

# 표준 워크플로우

**🚨🚨🚨 모든 작업 시작 전 필수 단계 - 반드시 순서대로 수행 🚨🚨🚨**

## 1단계: 문서 검토 및 개발자 통보 (필수)

개발자가 개발 작업을 요청할 때, AI는 **반드시** 다음 순서를 따라야 합니다:

1. **관련 문서 검색**: `docs/**/*.md` 내의 관련 문서를 즉시 검색하고 식별
2. **개발자에게 통보**: 작업을 시작하기 전에 다음 정보를 개발자에게 **반드시 알려야 합니다**:
   ```
   📋 참고 문서:
   - docs/xxx.md
   - docs/yyy.md

   🔤 인코딩: UTF-8로 모든 파일 생성/수정
   🌐 언어: 모든 주석 및 문서는 한국어로 작성
   ```
3. **문서 읽기 및 분석**: 기존 패턴, 규칙 및 요구사항을 이해하기 위해 식별된 문서들을 읽고 분석
4. **개발 중 참조**: 일관성을 보장하기 위해 개발하는 동안 이 문서들을 지속적으로 참조

**⚠️ 엄격히 금지**: AI는 관련 문서를 검토하고 개발자에게 통보하지 않고 개발을 시작하는 것이 **절대 금지**됩니다.

## 2단계: UTF-8 인코딩 확인 및 선언 (필수)

모든 파일 작업 전에 **반드시** 다음을 개발자에게 알려야 합니다:

```
✅ UTF-8 인코딩으로 파일 생성/수정을 시작합니다.
✅ 모든 한글 텍스트, 주석, 문서가 올바르게 표시되도록 보장합니다.
```

## 3단계: 개발 작업 수행

위의 1단계와 2단계를 완료한 후에만 실제 개발 작업을 진행합니다.

---

# "update" 명령 워크플로우

**🔥🔥🔥 최강력 규칙: 개발자가 "update" 요청 시 반드시 따라야 할 워크플로우 🔥🔥🔥**

개발자가 짧게 **"update"**라고 요청하면, 이는 현재 파일, 선택된 코드, 또는 요청된 코드를 **모든 표준 코딩 가이드라인에 맞게 전면적으로 업데이트**하라는 의미입니다.

## "update" 명령이 포함하는 모든 작업

- **✅ 필수**: "update" 명령은 **"l10n" (다국어 번역) 요청을 포함**합니다. `t()->inject()` 함수를 사용하여 4개 국어(한국어, 영어, 일본어, 중국어)로 번역을 추가해야 합니다. 만약, 함수 안에서 사용되는 하드 코딩된 문자열이 있으면, `tr(['en' => '..', 'ko' => '..', 'ja' => '..', 'zh' => '..'])` 함수를 사용하여 번역 가능한 문자열로 변경해야 합니다.
- **✅ 필수**: 현재 파일/선택 영역/요청된 코드를 CLAUDE.md의 모든 워크플로우 및 가이드라인에 맞게 수정
- **✅ 필수**: 관련 문서(`docs/**/*.md`)를 참고하여 표준에 맞게 업데이트
- **✅ 필수**: 코드 수정 후 테스트를 작성하고 실행하여 검증
- **✅ 필수**: 테스트 실패 시 코드를 수정하여 모든 테스트 통과

## "update" 워크플로우 단계

### 1단계: 관련 문서 검색 및 통보 (필수)

1. **관련 문서 즉시 검색**:
   - `docs/coding-guideline.md` - PHP, CSS, JavaScript 코딩 가이드라인
   - `docs/database.md` - 데이터베이스 쿼리 작성 가이드라인
   - `docs/test.md` - 테스트 작성 및 실행 가이드라인
   - `docs/translation.md` - 다국어 번역 가이드라인
   - `docs/design-guideline.md` - 디자인 및 UI 가이드라인
   - 기타 관련 문서

2. **개발자에게 통보**:
   ```
   📋 참고 문서:
   - docs/coding-guideline.md
   - docs/database.md
   - docs/test.md
   - docs/translation.md

   🔤 인코딩: UTF-8로 모든 파일 생성/수정
   🌐 언어: 모든 주석 및 문서는 한국어로 작성
   ```

3. **문서 읽기 및 분석**: 모든 관련 문서를 읽고 코딩 표준을 파악

### 2단계: 코드 업데이트 (필수)

**✅ PHP 코드 업데이트**:
- [ ] 모든 데이터베이스 쿼리는 `pdo()` 함수를 사용하여 PDO 직접 사용
- [ ] 플레이스홀더(?)를 사용하여 SQL 인젝션 방지
- [ ] 에러 처리는 `throw error()` 또는 `throw ApiException()` 사용
- [ ] 모든 주석은 한국어로 작성
- [ ] PHPDoc 형식으로 함수 문서화

**✅ CSS 코드 업데이트**:
- [ ] 페이지 파일(`./page/**/*.php`): CSS를 외부 `.css` 파일로 분리
- [ ] 위젯 파일(`./widgets/**/*.php`): CSS를 같은 파일 내 `<style>` 태그로 작성
- [ ] Bootstrap 기본 색상 및 변수 최대한 활용
- [ ] 커스텀 색상 사용 최소화

**✅ JavaScript 코드 업데이트**:
- [ ] 페이지 파일(`./page/**/*.php`): JavaScript를 외부 `.js` 파일로 분리
- [ ] 위젯 파일(`./widgets/**/*.php`): JavaScript를 같은 파일 내 `<script>` 태그로 작성
- [ ] `Vue.createApp()`, `Vue.ref()` 등 Vue 객체 직접 사용 (구조 분해 할당 금지)
- [ ] `ready(() => { ... })` 래퍼로 감싸기
- [ ] API 호출 시 `func()` 함수 사용
- [ ] Firebase 사용 시 `firebase_ready()` 함수 사용

**✅ 다국어 번역 업데이트 (l10n)**:
- [ ] 파일 하단에 `inject_[php_file_name]_language()` 함수 정의
- [ ] `t()->inject()` 함수로 번역 텍스트 주입
- [ ] 4개 국어(한국어, 영어, 일본어, 중국어) 필수 번역
- [ ] 키는 반드시 한글로 작성
- [ ] `<?= t()->키이름 ?>` 형식으로 사용

**✅ href() 함수 사용**:
- [ ] 모든 페이지 링크는 `href()` 함수 사용
- [ ] 하드코딩된 URL 문자열 제거

**✅ 주석 및 문서화**:
- [ ] 모든 주석은 한국어로 작성
- [ ] 함수/메서드는 PHPDoc 또는 JSDoc으로 문서화

### 3단계: 테스트 작성 및 실행 (필수)

**🔥🔥🔥 최강력 규칙: 코드 업데이트 후 반드시 테스트를 작성하고 실행해야 합니다 🔥🔥🔥**

1. **테스트 종류 선택**:
   - **PHP Unit Test**: 함수, 로직, DB 쿼리 결과 검증
   - **PHP E2E Test**: 페이지, UI 요소, HTML 콘텐츠 검증
   - **Playwright E2E Test**: 폼 전송, JavaScript 실행, 브라우저 상호작용 (PHP로 불가능한 경우에만)

2. **테스트 작성**:
   - [ ] `docs/test.md` 문서를 참고하여 테스트 작성
   - [ ] 테스트 파일은 `./tests` 폴더 아래에 저장
   - [ ] PHP Unit Test: `tests/[module]/[module].test.php`
   - [ ] PHP E2E Test: `tests/e2e/[page-name].e2e.test.php`
   - [ ] Playwright E2E Test: `tests/playwright/e2e/[page-name].spec.ts`

3. **테스트 실행**:
   - [ ] PHP Unit Test: `php tests/[module]/[module].test.php`
   - [ ] PHP E2E Test: `php tests/e2e/[page-name].e2e.test.php`
   - [ ] Playwright E2E Test: `npx playwright test tests/playwright/e2e/[page-name].spec.ts`

4. **테스트 결과 확인 및 수정**:
   - [ ] ✅ 모든 테스트 통과: 업데이트 완료
   - [ ] ❌ 테스트 실패: 코드를 수정하여 모든 테스트 통과할 때까지 반복

### 4단계: UTF-8 인코딩 확인 (필수)

- [ ] 수정된 모든 파일의 인코딩을 확인:
  ```bash
  file -I [파일경로]
  ```
- [ ] 출력이 `charset=utf-8`인지 확인

### 5단계: 자동 배포 (필수)

**🔥🔥🔥 최강력 규칙: "update" 명령의 맨 마지막 단계는 항상 자동 배포입니다 🔥🔥🔥**

- [ ] **✅ 필수**: 개발자가 배포를 요청하지 않아도 무조건 자동으로 배포 실행
- [ ] 개발자에게 배포 시작 통보: "🚀 모든 작업이 완료되어 자동으로 배포를 시작합니다."
- [ ] 프로젝트 루트 폴더에서 `./deploy.sh` 실행
- [ ] 배포 스크립트가 내부적으로 모든 테스트를 자동으로 실행하고 검증
- [ ] 테스트 통과 시 버전 업데이트, Git 커밋/푸시, SSH 배포 자동 실행
- [ ] 배포 완료 또는 실패 결과를 개발자에게 보고

### 6단계: 개발자에게 완료 보고 (필수)

**개발자에게 다음 정보를 보고**:
```
✅ 코드 업데이트 완료
✅ PHP 코딩 가이드라인 준수 완료
✅ CSS/JavaScript 분리 규칙 준수 완료
✅ 데이터베이스 쿼리 업데이트 완료 (PDO 직접 사용)
✅ 다국어 번역 추가 완료 (4개 국어)
✅ 테스트 작성 및 실행 완료 (모든 테스트 통과)
✅ UTF-8 인코딩 확인 완료
✅ 자동 배포 완료
✅ 프로덕션 서버 업데이트 완료
```

## "update" 명령 예시

**사용자 요청**: "update"

**AI 수행 작업**:
1. 현재 열려 있는 파일 또는 선택된 코드 확인
2. 관련 문서(`docs/coding-guideline.md`, `docs/database.md`, `docs/test.md`, `docs/translation.md`) 읽기
3. PHP 코드 업데이트 (PDO 사용, 에러 처리, 주석)
4. CSS/JavaScript 분리 규칙 적용
5. 다국어 번역 추가 (4개 국어)
6. href() 함수로 URL 변경
7. 테스트 작성 (테스트 실행은 배포 스크립트가 자동으로 수행)
8. UTF-8 인코딩 확인
9. **🔥 자동 배포 시작 (개발자가 요청하지 않았지만 무조건 자동 실행) 🔥**
10. `./deploy.sh` 실행 (내부적으로 모든 테스트 자동 실행 및 검증)
11. 배포 완료 보고

**⚠️⚠️⚠️ 최강력 경고: "update" 명령은 단순한 코드 수정이 아니라, 모든 표준 가이드라인에 맞게 전면적으로 업데이트하고 테스트하여 검증한 후 자동으로 배포하는 것입니다 ⚠️⚠️⚠️**

---

# "디자인 수정" 명령 워크플로우

**🔥🔥🔥 최강력 규칙: 개발자가 "디자인 수정" 요청 시 반드시 따라야 할 워크플로우 🔥🔥🔥**

개발자가 **"디자인 수정"**이라고 요청하면, 이는 현재 파일 또는 선택된 코드의 디자인을 **Sonub 디자인 가이드라인에 맞게 전면적으로 재작업**하라는 의미입니다.

## "디자인 수정" 명령이 포함하는 모든 작업

- **✅ 필수**: `docs/design-guideline.md` 문서를 읽고 디자인 가이드라인 준수
- **✅ 필수**: 심플하면서 단조롭고 현대적이며 단순한 디자인
- **✅ 필수**: Bootstrap으로 레이아웃 작성
- **✅ 필수**: 세련되면서도 아주 단순한 구조로 작성
- **✅ 필수**: 페이지 파일인 경우 CSS를 외부 `.css` 파일로 분리
- **✅ 필수**: 위젯/함수인 경우 CSS를 `<style>` 태그 내에 작성

## "디자인 수정" 워크플로우 단계

### 1단계: 문서 검토 및 개발자 통보 (필수)

1. **`docs/design-guideline.md` 문서 읽기**:
   - 디자인 철학 이해
   - Bootstrap 색상 및 레이아웃 규칙 숙지
   - CSS/JavaScript 분리 규칙 확인

2. **개발자에게 통보**:
   ```
   📋 참고 문서:
   - docs/design-guideline.md

   🎨 디자인 원칙:
   - 심플하고 단조로운 디자인
   - 현대적이고 단순한 구조
   - Bootstrap 레이아웃 유틸리티 클래스 사용
   - 충분한 여백, 단순한 색상
   - Shadow 최소화

   🔤 인코딩: UTF-8로 모든 파일 생성/수정
   🌐 언어: 모든 주석 및 문서는 한국어로 작성
   ```

### 2단계: 디자인 수정 작업 (필수)

**✅ 디자인 철학 준수**:
- [ ] **심플하고 단조로운 디자인**: 절대 화려한 디자인을 하지 마세요
- [ ] **현대적이고 단순한 디자인**: 복잡한 구조 금지
- [ ] **세련되면서도 단순한 구조**: 미니멀한 디자인

**✅ Bootstrap 레이아웃 규칙**:
- [ ] **레이아웃은 반드시 Bootstrap으로 작성**: `container`, `row`, `col`, `d-flex`, `gap-*`, `mb-*`, `mt-*` 등
- [ ] **레이아웃 관련 유틸리티 클래스는 인라인 `class=''` 속성으로 작성**
- [ ] **레이아웃과 관련 없는 Bootstrap 유틸리티 클래스는 별도 CSS 파일로 분리**

**✅ CSS 파일 분리 규칙**:
- [ ] **페이지 파일 (`./page/**/*.php`)**: 반드시 외부 `.css` 파일 생성 (`./page/**/*.css`)
- [ ] **위젯/함수 파일**: `<style>` 태그 내에 CSS 작성

**✅ 디자인 세부 규칙**:
- [ ] **Shadow 최소화**: 가능한 shadow를 추가하지 마세요
- [ ] **단순한 색상**: Bootstrap 기본 색상 변수 사용 (`var(--bs-primary)`, `var(--bs-light)` 등)
- [ ] **충분한 여백**: 요소 간 여백을 충분히 주어서 여유 있는 디자인
- [ ] **페이지 가장자리 여백 최소화**: 왼쪽/오른쪽 가장자리 여백은 작게

**✅ 올바른 디자인 예제 - 페이지 파일**:

```php
<!-- ./page/user/profile.php -->
<?php
$user = login();
?>

<!-- ✅ 올바른 방법: 레이아웃은 Bootstrap 유틸리티 클래스 -->
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- profile-card 클래스는 profile.css에 정의 -->
            <div class="profile-card mb-4">
                <h1><?= $user->name ?></h1>
            </div>
        </div>
    </div>
</div>
```

**외부 CSS 파일 (`./page/user/profile.css`)**:

```css
/* ✅ 올바른 방법: 심플하고 단조로운 디자인 */
.profile-card {
    background-color: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 2rem;
    /* shadow 최소화 */
}

.profile-card h1 {
    color: var(--bs-emphasis-color);
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
```

**✅ 올바른 디자인 예제 - 위젯 파일**:

```php
<!-- ./widgets/post/post-card.php -->
<div class="d-flex flex-column gap-3 post-card-widget">
    <h3><?= $post->title ?></h3>
    <p><?= $post->content ?></p>
</div>

<style>
/* ✅ 올바른 방법: 위젯 CSS는 <style> 태그 내 작성 */
.post-card-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.5rem;
    /* shadow 없음 - 심플한 디자인 */
}

.post-card-widget h3 {
    font-size: 1.25rem;
    color: var(--bs-emphasis-color);
    margin-bottom: 0.75rem;
}

.post-card-widget p {
    color: var(--bs-body-color);
    line-height: 1.6;
}
</style>
```

### 3단계: UTF-8 인코딩 확인 (필수)

- [ ] 수정된 모든 파일의 인코딩을 확인:
  ```bash
  file -I [파일경로]
  ```
- [ ] 출력이 `charset=utf-8`인지 확인

### 4단계: 자동 배포 (필수)

**🔥🔥🔥 최강력 규칙: "디자인 수정" 명령의 맨 마지막 단계는 항상 자동 배포입니다 🔥🔥🔥**

- [ ] **✅ 필수**: 개발자가 배포를 요청하지 않아도 무조건 자동으로 배포 실행
- [ ] 개발자에게 배포 시작 통보: "🚀 모든 작업이 완료되어 자동으로 배포를 시작합니다."
- [ ] 프로젝트 루트 폴더에서 `./deploy.sh` 실행
- [ ] 배포 스크립트가 내부적으로 모든 테스트를 자동으로 실행하고 검증
- [ ] 테스트 통과 시 버전 업데이트, Git 커밋/푸시, SSH 배포 자동 실행
- [ ] 배포 완료 또는 실패 결과를 개발자에게 보고

### 5단계: 개발자에게 완료 보고 (필수)

**개발자에게 다음 정보를 보고**:
```
✅ 디자인 수정 완료
✅ 심플하고 단조로운 디자인 적용 완료
✅ Bootstrap 레이아웃 규칙 준수 완료
✅ CSS 파일 분리 규칙 준수 완료
✅ Shadow 최소화 및 단순한 색상 사용 완료
✅ 충분한 여백 적용 완료
✅ UTF-8 인코딩 확인 완료
✅ 자동 배포 완료
✅ 프로덕션 서버 업데이트 완료
```

## "디자인 수정" 명령 예시

**사용자 요청**: "디자인 수정"

**AI 수행 작업**:
1. `docs/design-guideline.md` 문서 읽기
2. 현재 열려 있는 파일 또는 선택된 코드 확인
3. 파일 타입 확인 (페이지 vs 위젯)
4. Bootstrap 레이아웃 유틸리티 클래스로 레이아웃 작성
5. 페이지 파일인 경우 외부 `.css` 파일 생성
6. 위젯/함수인 경우 `<style>` 태그에 CSS 작성
7. 심플하고 단조로운 디자인 적용
8. Shadow 제거 및 단순한 색상 사용
9. 충분한 여백 추가, 가장자리 여백 최소화
10. UTF-8 인코딩 확인
11. **🔥 자동 배포 시작 (개발자가 요청하지 않았지만 무조건 자동 실행) 🔥**
12. `./deploy.sh` 실행 (내부적으로 모든 테스트 자동 실행 및 검증)
13. 배포 완료 보고

**⚠️⚠️⚠️ 최강력 경고: "디자인 수정" 명령은 심플하고 단조로우며 현대적인 디자인을 적용한 후 자동으로 배포하는 것입니다. 절대 화려한 디자인을 하지 마세요 ⚠️⚠️⚠️**

---

- [ ] 이슈 생성: 작업을 시작하기 전에 git 커밋 메시지 규칙을 따르는 이슈를 생성합니다.
- [ ] **파일 인코딩 요구사항 - UTF-8 필수 (최우선 최강력 규칙)**:
  - [ ] **🔥🔥🔥🔥🔥 최강력 규칙: 모든 파일은 100% 예외 없이 반드시 UTF-8 인코딩으로 저장해야 합니다 🔥🔥🔥🔥🔥**
  - [ ] **모든 문서 파일(\*.md)은 반드시 BOM 없는 UTF-8 인코딩으로 저장**
  - [ ] **모든 소스 코드 파일(\*.php, \*.js, \*.css, \*.html, \*.vue)은 반드시 BOM 없는 UTF-8 인코딩으로 저장**
  - [ ] **모든 텍스트 파일(\*.txt, \*.json, \*.yaml, \*.yml)은 반드시 BOM 없는 UTF-8 인코딩으로 저장**
  - [ ] **파일 생성/수정 시 편집기의 인코딩 설정을 반드시 UTF-8로 확인**
  - [ ] **작업 시작 전 반드시 개발자에게 "UTF-8 인코딩으로 작업합니다"라고 명시적으로 알려야 함**
  - [ ] **작업 완료 후 반드시 `file -I <파일경로>` 명령으로 UTF-8 인코딩 확인**
  - [ ] **절대 금지**: EUC-KR, CP949, ISO-8859-1, ASCII, Latin-1 등 다른 인코딩 사용 절대 금지
  - [ ] **위반 시 즉시 발생하는 문제**:
    - 한글이 깨져서 표시됨 (예: "문서" → "�8", "프로필" → "���")
    - 파일을 읽을 수 없음
    - Git에서 충돌 발생
    - 웹사이트에서 한글이 제대로 표시되지 않음
    - 데이터베이스에 잘못된 데이터 저장
    - 사용자가 웹사이트를 이용할 수 없음
- [ ] **언어 요구사항 - 한국어만 사용**:
  - [ ] **모든 문서 내용(\*.md 파일)은 반드시 한국어로 작성**
  - [ ] **모든 소스 코드 주석은 반드시 한국어로 작성**
    - [ ] PHP, JavaScript, CSS 및 기타 모든 소스 코드의 주석은 한국어로 작성
    - [ ] 마크다운 문서 내 코드 예제의 주석도 한국어로 작성
    - [ ] 함수/메서드 문서화(PHPDoc, JSDoc 등)는 한국어로 작성
  - [ ] **모든 사용자 대면 텍스트 및 UI 요소는 한국어로 작성**
  - [ ] **예외사항**: 코드 자체(변수명, 함수명, 클래스명), 기술 용어, 파일 경로, 라이브러리/프레임워크 코드는 영문 사용 가능
  - [ ] **코드 예제**: 문서나 응답에서 코드 예제를 제공할 때 모든 주석과 설명 텍스트는 한국어로 작성
- [ ] **페이지 구조 및 파일 로딩 - MPA 방식**:
  - [ ] **🔥🔥🔥 최강력 규칙: 모든 필수 리소스는 `index.php`에서 자동으로 로드됩니다 🔥🔥🔥**
  - [ ] **절대 금지**: 페이지 파일에서 `init.php`, `head.php`, `foot.php` 등을 로드하지 마세요
  - [ ] **절대 금지**: 페이지 파일에서 Bootstrap, Vue.js, Axios 등을 중복 로드하지 마세요
  - [ ] **절대 금지**: 페이지 파일에서 `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` 태그를 사용하지 마세요
  - [ ] **필수 준수**: 페이지 파일에는 **페이지 고유 콘텐츠만** 작성하세요
  - [ ] **자동 로드되는 항목**:
    - [ ] `init.php` - 프로젝트 초기화 및 설정
    - [ ] Bootstrap 5.3.8 CSS/JS
    - [ ] Bootstrap Icons 1.13.1
    - [ ] Font Awesome 7.1 (Pro)
    - [ ] Vue.js 3.x (글로벌 빌드)
    - [ ] Axios
    - [ ] Firebase 설정
    - [ ] Header, Footer, 사이드바
    - [ ] `/css/app.css`, `/js/app.js`
  - [ ] **페이지별 커스텀 리소스**:
    - [ ] 페이지별 CSS: `/page/user/profile-edit.css` → 자동 로드됨
    - [ ] 페이지별 JS: `/page/user/profile-edit.js` → 자동 로드됨
  - [ ] **🔥🔥🔥 최강력 규칙: 페이지 링크 및 네비게이션 - href() 함수 필수 사용 🔥🔥🔥**:
    - [ ] **절대 준수**: 모든 페이지 링크 및 URL 생성 시 반드시 `href()` 함수를 사용해야 합니다
    - [ ] **href() 함수 위치**: `lib/href/href.functions.php`에 정의되어 있음
    - [ ] **절대 금지**: 하드코딩된 URL 문자열 사용 금지 (예: `'/user/login'`, `'/post/list'` 등)
    - [ ] **사용 예제**:
      ```php
      // ✅ 올바른 방법: href() 함수 사용 (일반 HTML)
      <a href="<?= href()->user->login ?>">로그인</a>
      <a href="<?= href()->user->profile_edit ?>">프로필 수정</a>
      <a href="<?= href()->post->list(1, 'discussion') ?>">토론 게시판</a>
      <a href="<?= href()->help->contact ?>">문의하기</a>
      <a href="<?= href()->home ?>">홈</a>

      // ✅ 올바른 방법: href() 함수 사용 (HEREDOC 내부)
      $home_url = href()->home;
      $contact_url = href()->admin->contact;
      echo <<<HTML
      <a href="{$home_url}">홈</a>
      <a href="{$contact_url}">관리자 문의</a>
      HTML;

      // 또는 직접 사용
      echo <<<HTML
      <a href="{href()->home}">홈</a>
      <a href="{href()->admin->contact}">관리자 문의</a>
      HTML;

      // ❌ 잘못된 방법: 하드코딩된 URL
      <a href="/user/login">로그인</a>
      <a href="/user/profile-edit">프로필 수정</a>
      <a href="/post/list?category=discussion">토론 게시판</a>
      ```
    - [ ] **href() 함수 구조**:
      - [ ] `href()->home`: 홈 페이지
      - [ ] `href()->user->login`: 로그인 페이지
      - [ ] `href()->user->register`: 회원가입 페이지
      - [ ] `href()->user->profile`: 프로필 페이지
      - [ ] `href()->user->profile_edit`: 프로필 수정 페이지
      - [ ] `href()->user->settings`: 설정 페이지
      - [ ] `href()->post->list($page, $category)`: 게시글 목록 (동적 파라미터)
      - [ ] `href()->post->create`: 게시글 작성
      - [ ] `href()->comment->list($idx_member)`: 댓글 목록
      - [ ] `href()->help->howto`: 사용 방법
      - [ ] `href()->help->guideline`: 가이드라인
      - [ ] `href()->help->terms_and_conditions`: 이용약관
      - [ ] `href()->help->privacy`: 개인정보처리방침
      - [ ] `href()->admin->contact`: 관리자 문의
      - [ ] `href()->admin->dashboard`: 관리자 대시보드
      - [ ] `href()->chat->rooms`: 채팅방 목록
    - [ ] **위반 시**: 코드 리뷰 거부, URL 변경 시 유지보수 어려움, 일관성 문제
  - [ ] **올바른 페이지 파일 구조**:
    ```php
    <?php
    // 페이지 고유 로직만 작성
    $user = login();
    ?>

    <!-- 페이지 고유 HTML만 작성 -->
    <div class="container">
        <h1>제목</h1>
    </div>

    <!-- 페이지 고유 Vue 앱만 작성 -->
    <script>
    const { createApp } = Vue;
    createApp({ /* ... */ }).mount('#app');
    </script>
    ```
- [ ] 디자인 작업 시 Bootstrap을 사용해야 합니다.
  - [ ] **라이트 모드만 지원**: Sonub 웹사이트는 라이트 모드만 지원합니다. 다크 모드 기능을 절대 구현하지 마세요.
  - [ ] 항상 Bootstrap의 기본 색상 또는 Bootstrap의 기본 색상 변수를 최대한 사용하세요.
  - [ ] 꼭 필요한 경우가 아니면 커스텀 색상, HEX 코드, 색상 단어 코드 사용을 피하세요.
  - [ ] **아이콘 사용 규칙 - 필수 준수**:
    - [ ] **🔥🔥🔥 최강력 규칙: 아이콘을 추가할 때는 반드시 Font Awesome 7.1 Pro를 먼저 사용해야 합니다 🔥🔥🔥**
    - [ ] Font Awesome 7.1 (Pro)과 Bootstrap Icons 1.13.1을 함께 사용합니다
    - [ ] 두 아이콘 라이브러리 모두 이미 로드되어 있으며 즉시 사용 가능합니다
    - [ ] **✅ 우선순위 1순위**: Font Awesome 7.1 Pro - 먼저 확인하고 사용
    - [ ] **✅ 우선순위 2순위**: Bootstrap Icons - Font Awesome에 없는 경우에만 사용
    - [ ] Font Awesome: `<i class="fa-solid fa-house"></i>` 형식 사용
    - [ ] Font Awesome Pro는 더 많은 아이콘과 스타일을 제공합니다 (solid, regular, light, thin, duotone 등)
    - [ ] Bootstrap Icons: `<i class="bi bi-house"></i>` 형식 사용
    - [ ] **위반 금지**: Font Awesome에 해당 아이콘이 있는데 Bootstrap Icons를 사용하는 것은 금지
  - [ ] **디자인 관련 작업 시 반드시 `docs/design-guideline.md` 문서를 먼저 읽어야 합니다**
- [ ] **🔥🔥🔥 CSS 및 JavaScript 파일 분리 규칙 - 최강력 필수 준수 🔥🔥🔥**
  - [ ] **⚠️⚠️⚠️ 최강력 경고: 페이지 파일(./page/)과 위젯 파일(./widgets/)의 CSS/JS 관리 방식이 완전히 다릅니다 ⚠️⚠️⚠️**
  - [ ] **🔥🔥🔥 페이지 파일(`./page/**/*.php`) CSS/JS 분리 규칙 - 절대 준수 🔥🔥🔥**:
    - [ ] **✅ 필수**: Bootstrap Layout Utility 클래스(container, row, col, mb-, mt- 등)를 **제외한** 모든 CSS는 반드시 외부 `.css` 파일로 분리해야 합니다
    - [ ] **✅ 필수**: 페이지별 CSS 파일은 `./page/**/*.css` 위치에 저장하며, 페이지 PHP 파일과 같은 위치에 동일한 이름으로 저장
    - [ ] **✅ 필수**: 페이지별 JavaScript 파일은 `./page/**/*.js` 위치에 저장하며, 페이지 PHP 파일과 같은 위치에 동일한 이름으로 저장
    - [ ] **✅ 필수**: CSS와 JS 파일은 `index.php`에서 자동으로 로드됩니다 - 수동으로 `<link>` 또는 `<script>` 태그 추가하지 마세요
    - [ ] **❌ 절대 금지**: 페이지 파일 내에 `<style>` 태그 사용 금지 (Bootstrap Layout Utility 클래스는 예외)
    - [ ] **❌ 절대 금지**: 페이지 파일 내에 인라인 `style=""` 속성 사용 금지 (Bootstrap 클래스로 대체)
    - [ ] **위반 시**: 코드 리뷰 거부, CSS 관리 어려움, 유지보수 불가
  - [ ] **🔥🔥🔥 위젯 파일(`./widgets/**/*.php`) CSS/JS 포함 규칙 - 절대 준수 🔥🔥🔥**:
    - [ ] **✅ 필수**: 위젯의 모든 CSS는 반드시 **같은 PHP 파일 내부**에 `<style>` 태그로 작성해야 합니다
    - [ ] **✅ 필수**: 위젯의 모든 JavaScript는 반드시 **같은 PHP 파일 내부**에 `<script>` 태그로 작성해야 합니다
    - [ ] **✅ 필수**: `<style>` 태그는 HTML 구조 직후에 배치
    - [ ] **✅ 필수**: `<script>` 태그는 `<style>` 태그 직후에 배치
    - [ ] **✅ 필수**: CSS 클래스명은 위젯별로 고유하게 작성 (예: `.post-card-widget`, `.comment-list-widget`)
    - [ ] **❌ 절대 금지**: 위젯 파일의 CSS를 외부 `.css` 파일로 분리하는 것은 절대 금지
    - [ ] **❌ 절대 금지**: 위젯 파일의 JavaScript를 외부 `.js` 파일로 분리하는 것은 절대 금지
    - [ ] **위반 시**: 위젯 재사용 불가, 의존성 관리 어려움, 코드 유지보수 불가
  - [ ] **페이지와 위젯의 차이점 요약**:
    - [ ] **페이지 파일 (`./page/**/*.php`)**: CSS → 외부 `.css` 파일, JS → 외부 `.js` 파일
    - [ ] **위젯 파일 (`./widgets/**/*.php`)**: CSS → 같은 파일 `<style>` 태그, JS → 같은 파일 `<script>` 태그
  - [ ] **올바른 예제 - 페이지 파일 (`./page/user/profile-edit.php`)**:
    ```php
    <?php
    // PHP 로직
    $user = login();
    ?>

    <!-- HTML: Bootstrap Layout Utility 클래스만 사용 -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="profile-card">
                    <h1 class="profile-title">프로필 수정</h1>
                </div>
            </div>
        </div>
    </div>
    ```
    **외부 CSS 파일 (`./page/user/profile-edit.css`)**:
    ```css
    .profile-card {
        background-color: white;
        border-radius: 8px;
        padding: 2rem;
    }
    .profile-title {
        font-size: 1.5rem;
        color: var(--bs-primary);
    }
    ```
  - [ ] **올바른 예제 - 위젯 파일 (`./widgets/post/post-card.php`)**:
    ```php
    <?php
    $unique_id = 'post-card-' . uniqid();
    ?>

    <div id="<?= $unique_id ?>" class="post-card-widget">
        <h2 class="post-card-title">게시글 제목</h2>
        <p class="post-card-content">게시글 내용...</p>
    </div>

    <style>
    .post-card-widget {
        background-color: white;
        border: 1px solid var(--bs-border-color);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .post-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .post-card-content {
        color: var(--bs-secondary);
    }
    </style>

    <script>
    ready(() => {
        Vue.createApp({
            setup() {
                const likes = Vue.ref(0);
                const addLike = () => { likes.value++; };
                return { likes, addLike };
            }
        }).mount('#<?= $unique_id ?>');
    });
    </script>
    ```
  - [ ] **잘못된 예제 (절대 금지) - 페이지 파일에 `<style>` 태그 사용**:
    ```php
    <!-- ❌ 절대 금지 -->
    <div class="profile-card">프로필</div>
    <style>
    .profile-card { background: white; }
    </style>
    ```
  - [ ] **잘못된 예제 (절대 금지) - 위젯 파일에서 CSS를 외부 파일로 분리**:
    ```php
    <!-- ❌ 절대 금지 -->
    <link rel="stylesheet" href="/widgets/post/post-card.css">
    <div class="post-card-widget">게시글</div>
    ```
- [ ] **JavaScript 프레임워크 - Vue.js 3.x**:
  - [ ] Sonub는 PHP MPA(Multi-Page Application) 방식으로 Vue.js 3.x를 CDN을 통해 사용합니다
  - [ ] Vue.js는 모든 페이지에 CDN을 통해 자동으로 로드됩니다 - 수동으로 추가하지 마세요
  - [ ] 각 페이지에서 `const { createApp } = Vue;`를 사용하여 Vue 앱을 생성하세요
  - [ ] **MPA 방식의 장점**: 페이지 이동 시 이벤트 리스너와 Vue 인스턴스가 자동으로 정리됩니다
  - [ ] **정리 작업 불필요**: SPA와 달리 `beforeUnmount`에서 이벤트 리스너를 수동으로 해제할 필요가 없습니다
  - [ ] 각 페이지의 Vue 인스턴스는 완전히 독립적입니다
  - [ ] 복잡한 라우팅이나 상태 관리 라이브러리 없이 간단한 구조를 유지하세요
- [ ] 문서(\*.md) 및 목차 관리
  - [ ] _.md 파일이나 docs/\*\*/_.md 파일을 편집할 때 문서 상단에 목차(ToC)를 반드시 추가해야 합니다.
  - [ ] 문서 구조가 변경될 때마다(섹션 추가, 제거, 이름 변경) 목차를 업데이트해야 합니다.
  - [ ] 목차는 문서의 모든 주요 제목(##)과 부제목(###)을 반영해야 합니다.
  - [ ] 목차를 실제 콘텐츠와 항상 동기화 상태로 유지하세요.
- [ ] **테스트 가이드라인 - 필수 준수**
  - [ ] **🔥🔥🔥 최강력 규칙: 테스트 관련 작업 시 반드시 `docs/test.md` 문서를 먼저 읽어야 합니다 🔥🔥🔥**
  - [ ] **테스트 환경**: 모든 테스트는 `https://local.sonub.com/` 개발 환경에서 수행
  - [ ] **🔥🔥🔥 테스트 종류 자동 선택 - 최강력 규칙 🔥🔥🔥**:
    - [ ] **1순위: PHP Unit Test** - "함수", "로직", "DB 쿼리" 테스트 요청 시 사용
    - [ ] **2순위: PHP E2E Test** - "페이지", "UI 요소", "HTML 콘텐츠" 테스트 요청 시 사용
    - [ ] **3순위 (최후 수단): Playwright E2E Test** - PHP로 불가능한 경우에만 사용
  - [ ] **🔥🔥🔥 Playwright 사용 판단 기준 - 반드시 따라야 합니다 🔥🔥🔥**:
    - [ ] **✅ Playwright를 사용해야 하는 경우**:
      - [ ] "폼 전송" 테스트 요청 시 (PHP는 폼 submit을 시뮬레이션할 수 없음)
      - [ ] "사용자 입력" 또는 "타이핑" 테스트 요청 시 (PHP는 키보드 입력을 시뮬레이션할 수 없음)
      - [ ] "버튼 클릭" 후 JavaScript 동작 테스트 요청 시 (PHP는 JavaScript를 실행할 수 없음)
      - [ ] "파일 업로드" 및 "업로드 프로그레스" 테스트 요청 시 (JavaScript로 구현된 기능)
      - [ ] "드래그 앤 드롭", "스크롤" 등 브라우저 이벤트 테스트 요청 시
      - [ ] "모달", "드롭다운" 등 JavaScript로 구현된 UI 컴포넌트 상호작용 테스트 요청 시
    - [ ] **✅ PHP E2E 테스트를 사용해야 하는 경우**:
      - [ ] "페이지에서 .title 클래스가 잘 보이는지" 테스트 요청 시 (HTML 요소 존재 여부만 확인)
      - [ ] "페이지가 200 상태 코드를 반환하는지" 테스트 요청 시
      - [ ] "특정 텍스트가 페이지에 포함되어 있는지" 테스트 요청 시
      - [ ] "HTML 구조가 올바른지" 테스트 요청 시
      - [ ] 브라우저 상호작용이 필요 없는 모든 페이지 테스트
    - [ ] **⚠️⚠️⚠️ 최강력 경고: PHP로 테스트가 가능하면 반드시 PHP로 테스트하고, PHP로 불가능한 경우에만 Playwright를 사용하세요 ⚠️⚠️⚠️**
  - [ ] **테스트 선택 예시**:
    - [ ] 사용자 요청: "로그인 폼 전송 테스트" → **Playwright E2E** (폼 전송 필요)
    - [ ] 사용자 요청: "페이지에서 .title 클래스가 잘 보이는지 테스트" → **PHP E2E** (HTML 요소 확인만 필요)
    - [ ] 사용자 요청: "게시글 작성 후 목록에 표시되는지 테스트" → **Playwright E2E** (폼 전송 + JavaScript 동작)
    - [ ] 사용자 요청: "홈페이지가 정상적으로 로드되는지 테스트" → **PHP E2E** (HTTP 상태 코드 확인)
    - [ ] 사용자 요청: "파일 업로드 프로그레스 바 테스트" → **Playwright E2E** (JavaScript 동작 + UI 상호작용)
    - [ ] 사용자 요청: "사용자 생성 함수 테스트" → **PHP Unit Test** (함수 로직만 테스트)
  - [ ] **🔥🔥🔥 최강력 규칙: 모든 테스트 파일, 임시 파일, 검증 파일은 반드시 `./tests` 폴더 아래에 저장해야 합니다 🔥🔥🔥**
  - [ ] **절대 금지**: 테스트 관련 파일을 프로젝트 루트나 `lib`, `src` 등 다른 폴더에 저장하는 것은 절대 금지
  - [ ] **위반 시**: 프로젝트 구조 오염, 운영 코드와 테스트 코드 혼재, Git 관리 어려움
  - [ ] **PHP Unit Test (단위 테스트)**:
    - [ ] 외부 테스트 프레임워크 없이 순수 PHP로 작성
    - [ ] 테스트 파일은 `tests` 디렉토리에 저장 (예: `tests/db/db.connection.test.php`)
    - [ ] 파일 이름은 `.test.php`로 끝나야 함
    - [ ] CLI에서 직접 실행: `php tests/db/db.test.php`
    - [ ] 함수, 로직, DB 쿼리 결과 검증
  - [ ] **PHP E2E Test (통합 테스트)**:
    - [ ] 외부 라이브러리 없이 순수 PHP로 작성
    - [ ] `curl` 또는 `file_get_contents()` 함수 사용
    - [ ] `https://local.sonub.com/`에 HTTP 요청을 보내 테스트
    - [ ] 테스트 파일은 `tests/e2e/` 디렉토리에 저장
    - [ ] 파일 이름은 `.e2e.test.php`로 끝나야 함 (예: `tests/e2e/user-login.e2e.test.php`)
    - [ ] HTTP 상태 코드, 응답 본문, HTML 요소 검증
  - [ ] **Playwright E2E Test (브라우저 자동화 테스트)**:
    - [ ] TypeScript로 작성
    - [ ] 테스트 파일은 `tests/playwright/e2e/` 디렉토리에 저장
    - [ ] 파일 이름은 `.spec.ts`로 끝나야 함 (예: `tests/playwright/e2e/user-login.spec.ts`)
    - [ ] 실제 브라우저에서 폼 전송, JavaScript 실행, 사용자 입력 시뮬레이션 가능
  - [ ] **테스트 파일 저장 위치 예시**:
    - [ ] ✅ 올바른 위치: `tests/db/db.connection.test.php` (PHP Unit Test)
    - [ ] ✅ 올바른 위치: `tests/user/user.crud.test.php` (PHP Unit Test)
    - [ ] ✅ 올바른 위치: `tests/e2e/user-login.e2e.test.php` (PHP E2E Test)
    - [ ] ✅ 올바른 위치: `tests/e2e/homepage.e2e.test.php` (PHP E2E Test)
    - [ ] ✅ 올바른 위치: `tests/playwright/e2e/user-login.spec.ts` (Playwright E2E Test)
    - [ ] ✅ 올바른 위치: `tests/playwright/e2e/post-create.spec.ts` (Playwright E2E Test)
    - [ ] ✅ 올바른 위치: `tests/temp/verify_api.php` (임시 검증 파일)
    - [ ] ❌ 잘못된 위치: `db.test.php` (루트 폴더)
    - [ ] ❌ 잘못된 위치: `lib/db/db.test.php` (소스 코드 폴더)
    - [ ] ❌ 잘못된 위치: `user-login.spec.ts` (루트 폴더)
    - [ ] ❌ 잘못된 위치: `temp.php` (루트 폴더)
  - [ ] **테스트 코드 작성 시 필수 사항**:
    - [ ] 모든 테스트 파일 맨 위에 반드시 `include '../init.php'` 추가 (상대 경로는 테스트 파일 위치에 따라 조정)
    - [ ] init.php를 포함하면 모든 라이브러리, 함수, DB 설정 등을 즉시 사용 가능
    - [ ] init.php는 ROOT_DIR 정의 및 etc/includes.php 로드를 자동으로 처리
  - [ ] **테스트 실행 방법**:
    - [ ] 프로젝트 루트 폴더에서 실행: `php tests/db/db.connection.test.php`
    - [ ] 개발자 컴퓨터에서는 `is_dev_computer()` 함수가 true를 반환하여 자동으로 개발 DB 설정 사용
    - [ ] DB 접속 흐름: `is_dev_computer()` → `etc/includes.php` → `etc/config/db.dev.config.php`
    - [ ] 운영 서버에서는 `is_dev_computer()` 함수가 false를 반환하여 `etc/config/db.config.php` 사용
- [ ] **데이터베이스 개발 가이드라인 - 최강력 필수 준수**
  - [ ] **🔥🔥🔥 최강력 규칙: 모든 데이터는 MariaDB에 저장해야 합니다 🔥🔥🔥**
  - [ ] **🔥🔥🔥 최강력 규칙: 데이터베이스 작업 시 반드시 `docs/database.md` 문서를 먼저 읽어야 합니다 🔥🔥🔥**
  - [ ] **🔥🔥🔥 최우선 필수 규칙: 모든 데이터베이스 작업은 `pdo()` 함수를 통해 PDO 객체를 얻어서 수행해야 합니다 🔥🔥🔥**
  - [ ] **✅ 최우선 권장**: `pdo()` 함수로 PDO 객체를 얻어 직접 SQL 쿼리 실행
  - [ ] **❌ 차선택**: `db()` 쿼리 빌더는 특별한 경우에만 사용하고, 가능한 사용하지 마세요
  - [ ] **절대 금지**: mysqli를 직접 사용하는 것은 절대 금지
  - [ ] **데이터베이스 작업 전 필수 확인 사항**:
    - [ ] `docs/database.md` 문서를 읽고 PDO 사용법 확인
    - [ ] `lib/db/db.php`의 `pdo()` 함수 예제 확인
    - [ ] 비슷한 기능의 기존 코드가 있는지 검색
  - [ ] **PHP 함수 작성 시 DB 입출력 규칙 (PDO 직접 사용)**:
    - [ ] 모든 SELECT 쿼리: `$pdo = pdo(); $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); $stmt->execute([$id]); $user = $stmt->fetch();`
    - [ ] 모든 INSERT 쿼리: `$pdo = pdo(); $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)"); $stmt->execute([$name, $email]); $id = $pdo->lastInsertId();`
    - [ ] 모든 UPDATE 쿼리: `$pdo = pdo(); $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?"); $stmt->execute([$name, $id]); $affected = $stmt->rowCount();`
    - [ ] 모든 DELETE 쿼리: `$pdo = pdo(); $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?"); $stmt->execute([$id]); $deleted = $stmt->rowCount();`
    - [ ] 플레이스홀더(?)를 사용하여 SQL 인젝션 방지
  - [ ] **데이터베이스 작업 예시**:
    - [ ] ✅ 올바른 방법 (최우선): `$pdo = pdo(); $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); $stmt->execute([$id]); $user = $stmt->fetch();`
    - [ ] ✅ 올바른 방법 (최우선): `$pdo = pdo(); $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)"); $stmt->execute([$name, $email]); $userId = $pdo->lastInsertId();`
    - [ ] ⚠️ 차선택: `db()->select('*')->from('users')->where('id = ?', [$id])->first()` (특별한 경우에만 사용)
    - [ ] ❌ 잘못된 방법: `mysqli_query($conn, "SELECT * FROM users WHERE id = $id")`
  - [ ] **사용자, 게시판, 댓글 등 모든 데이터 입출력**:
    - [ ] 사용자 데이터: `users` 테이블 사용
    - [ ] 게시글 데이터: `posts` 테이블 사용
    - [ ] 댓글 데이터: `comments` 테이블 사용
    - [ ] 모든 데이터 조회/생성/수정/삭제 시 `pdo()` 함수로 PDO 객체를 얻어 직접 쿼리 실행
  - [ ] **복잡한 쿼리의 경우**:
    - [ ] JOIN, GROUP BY, HAVING 등이 필요한 경우에도 `pdo()` 함수로 PDO 객체를 얻어 직접 SQL 작성
    - [ ] PDO는 모든 SQL 기능을 완벽하게 지원합니다
  - [ ] **쿼리 빌더 `db()` 사용 금지 (제한적 예외)**:
    - [ ] 쿼리 빌더는 다음과 같은 매우 제한적인 경우에만 사용:
      - [ ] 매우 복잡한 동적 쿼리 생성이 필요한 경우
      - [ ] 여러 조건이 동적으로 추가되어야 하는 검색 기능
      - [ ] 레거시 코드 유지보수
    - [ ] 위 경우가 아니면 반드시 PDO를 직접 사용하세요
  - [ ] **위반 시**: 코드 리뷰 거부, SQL 인젝션 취약점 발생, 데이터 일관성 문제
- [ ] **다국어 번역 개발 가이드라인 - 필수 준수**
  - [ ] **언어 번역 관련 작업을 요청받으면 반드시 `docs/translation.md`의 Standard Workflow를 따라야 합니다**
  - [ ] **절대 준수**: 모든 언어 번역은 각 PHP 파일 하단에 `inject_[php_file_name]_language()` 로컬 함수를 정의해야 합니다
  - [ ] 함수 내부에서 `t()->inject(...)` 함수를 통해 다국어 번역 텍스트를 주입해야 합니다
  - [ ] 예시: `index.php` 파일에서는 맨 아래에 `inject_index_language()` 함수를 정의하고, 파일 맨 위에서 이 함수를 호출하여 번역 텍스트를 주입합니다
  - [ ] **🔥🔥🔥 최강력 규칙: 모든 번역은 반드시 4개 국어(한국어, 영어, 일본어, 중국어)로 작성해야 합니다 🔥🔥🔥**
  - [ ] **필수 언어 및 순서**:
    - [ ] 1. 한국어 (ko)
    - [ ] 2. 영어 (en)
    - [ ] 3. 일본어 (ja)
    - [ ] 4. 중국어 (zh)
  - [ ] **필수 번역 방법**:
    - [ ] `t()->inject()` 함수를 사용하여 번역 텍스트 주입
    - [ ] 키는 **반드시 한글**이어야 함
    - [ ] 번역 텍스트 사용 시: `<?= t()->키이름 ?>` 형식 사용
  - [ ] **인라인 번역 예제**:
    ```php
    // 인라인 번역 (4개 국어 순서대로)
    <?= tr(['ko' => '환영합니다', 'en' => 'Welcome', 'ja' => 'ようこそ', 'zh' => '欢迎']) ?>
    ```
  - [ ] **t()->inject() 사용 예제**:
    ```php
    t()->inject([
        '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
        '나이' => ['ko' => '나이', 'en' => 'Age', 'ja' => '年齢', 'zh' => '年龄'],
    ]);

    // 사용
    <?= t()->이름 ?>
    <?= t()->나이 ?>
    ```
  - [ ] **위반 금지**: 이 워크플로우를 따르지 않는 번역 구현은 절대 금지됩니다
  - [ ] 번역 관련 작업 시작 전 반드시 `docs/translation.md` 문서를 읽고 이해해야 합니다

## JavaScript 특별 지침

- [ ] **🔥🔥🔥 API 호출 필수 규칙 - func() 함수 사용 🔥🔥🔥**
  - [ ] **✅ 필수: JavaScript에서 PHP API 함수를 호출할 때는 반드시 `func()` 함수를 사용해야 합니다**
  - [ ] `func()` 함수는 `/js/app.js`에 정의되어 있으며 모든 페이지에서 사용 가능합니다
  - [ ] **절대 금지**: `axios.post('/api.php')` 또는 `fetch('/api.php')` 직접 호출 금지
  - [ ] **func() 함수 장점**:
    - [ ] 자동 에러 처리 (alertOnError 옵션)
    - [ ] Firebase 인증 토큰 자동 전송 (auth 옵션)
    - [ ] 일관된 API 호출 패턴
    - [ ] 에러 코드 및 메시지 자동 추출

  - [ ] **func() 함수 사용 예제:**
    ```javascript
    // 기본 사용법
    const result = await func('update_user_profile', {
        display_name: '홍길동',
        gender: 'male',
        birthday: '1990-01-01'
    });

    // Firebase 인증 토큰 포함 (로그인 필요한 API)
    await func('login_with_firebase', {
        firebase_uid: user.uid,
        auth: true,           // Firebase ID 토큰 자동 전송
        alertOnError: true    // 에러 시 alert 표시
    });

    // 에러 알림 비활성화
    const response = await func('set_language', {
        language_code: 'ko',
        alertOnError: false   // 에러 시 alert 표시 안 함
    });

    // 에러 처리
    try {
        const user = await func('get_user_info', { user_id: 123 });
        console.log('사용자:', user);
    } catch (error) {
        console.error('에러 코드:', error.code);
        console.error('에러 메시지:', error.message);
    }
    ```

  - [ ] **func() 함수 파라미터:**
    - [ ] `name`: 호출할 PHP API 함수 이름 (필수)
    - [ ] `params`: 함수에 전달할 파라미터 객체 (선택)
    - [ ] `params.auth`: true로 설정 시 Firebase ID 토큰 자동 전송 (선택, 기본값: false)
    - [ ] `params.alertOnError`: true로 설정 시 에러 발생 시 alert 표시 (선택, 기본값: true)

  - [ ] **올바른 사용 예제:**
    ```javascript
    // ✅ 올바른 방법: func() 함수 사용
    const user = await func('get_user_info', { user_id: 123 });

    // ✅ 올바른 방법: Firebase 인증 포함
    await func('create_post', {
        title: '게시글 제목',
        content: '게시글 내용',
        auth: true
    });
    ```

  - [ ] **잘못된 사용 예제 (절대 금지):**
    ```javascript
    // ❌ 절대 금지: axios 직접 호출
    const response = await axios.post('/api.php', {
        func: 'get_user_info',
        user_id: 123
    });

    // ❌ 절대 금지: fetch 직접 호출
    const response = await fetch('/api.php?f=get_user_info&user_id=123');
    ```

- [ ] **🚨🚨🚨 초필수 JavaScript defer 로딩 규칙 - 절대 준수 🚨🚨🚨**
  - [ ] **⚠️⚠️⚠️ 최강력 경고: 모든 JavaScript는 반드시 defer 속성으로 로드해야 합니다 ⚠️⚠️⚠️**

  - [ ] **✅ 필수: 모든 JavaScript 파일은 반드시 defer 로딩**
    - [ ] jQuery, Bootstrap bundle.min.js
    - [ ] Vue.js, Firebase SDK, Axios.js
    - [ ] 모든 `/js/**/*.js` 파일
    - [ ] 모든 사용자 정의 JavaScript 파일
    - [ ] **예외 없음**: 어떤 JavaScript 파일도 일반 방식으로 로드 금지

  - [ ] **✅ 필수: ready() 함수 래퍼 필수 사용**
    - [ ] Vue.js와 Firebase가 defer로 로드되므로 초기화 대기 필요
    - [ ] 모든 JavaScript 코드는 **반드시** `ready(() => { ... })` 래퍼로 감싸기
    - [ ] DOM 로드 완료와 Vue.js/Firebase 초기화를 보장

  - [ ] **올바른 사용 예제:**
    ```html
    <!-- ✅ 올바른 방법: defer 속성 사용 -->
    <script defer src="/js/app.js"></script>
    <script defer src="/js/custom-script.js"></script>
    <script defer src="https://cdn.example.com/library.js"></script>

    <!-- ✅ 올바른 방법: ready() 함수 래퍼 사용 -->
    <script>
    ready(() => {
        // Vue.js 사용
        Vue.createApp({
            setup() {
                const count = Vue.ref(0);
                return { count };
            }
        }).mount('#app');

        // Firebase 사용
        const db = firebase.firestore();

        // jQuery 사용
        $('#button').on('click', function() {
            console.log('클릭됨');
        });
    });
    </script>
    ```

  - [ ] **잘못된 사용 예제 (절대 금지):**
    ```html
    <!-- ❌ 절대 금지: defer 없는 일반 로딩 -->
    <script src="/js/app.js"></script>
    <script src="https://cdn.example.com/library.js"></script>

    <!-- ❌ 절대 금지: ready() 래퍼 없이 직접 실행 -->
    <script>
    // Vue.js가 아직 로드되지 않아 에러 발생
    Vue.createApp({
        setup() {
            return {};
        }
    }).mount('#app');

    // Firebase가 아직 로드되지 않아 에러 발생
    const db = firebase.firestore();
    </script>
    ```

  - [ ] **🔴 위반 시 결과**:
    - [ ] "Vue is not defined" 에러 발생
    - [ ] "firebase is not defined" 에러 발생
    - [ ] DOM 요소를 찾지 못하는 에러 발생
    - [ ] 페이지 렌더링 블로킹으로 인한 성능 저하
    - [ ] 초기화 순서 문제로 인한 예측 불가능한 동작

- [ ] **🔥🔥🔥 초강력 알림: 사전 로드된 라이브러리 🔥🔥🔥**
  - [ ] **Firebase JS SDK**: body 태그 상단에 이미 로드되어 있음. 별도 로드 불필요
  - [ ] **Vue.js**: body 태그 상단에 이미 로드되어 있음. 즉시 사용 가능
  - [ ] **Axios.js**: body 태그 상단에 이미 로드되어 있음. 즉시 사용 가능
  - [ ] **✅ 필수**: 이 라이브러리들은 중복 로드하지 말 것
  - [ ] **✅ 필수**: HTML 어디서든 즉시 사용 가능

- [ ] **🚨🚨🚨 Firebase 초기화 필수 규칙 - firebase_ready() 함수 🚨🚨🚨**
  - [ ] **⚠️⚠️⚠️ 최강력 경고: Firebase 초기화가 필요한 경우 반드시 firebase_ready() 함수를 사용해야 한다 ⚠️⚠️⚠️**

  - [ ] **문제 상황**: Firebase는 `</body>` 직전에 초기화됨
    - [ ] Firebase를 초기화 전에 사용하려고 하면 "firebase is not defined" 에러 발생
    - [ ] `ready()` 함수는 모든 JavaScript 로드를 기다리지만, Firebase 초기화 완료는 보장하지 않음
    - [ ] 특히 `<head>` 태그 안이나 페이지 상단에서 Firebase를 사용하는 경우 문제 발생

  - [ ] **해결 방법**: `firebase_ready()` 함수 사용
    - [ ] `firebase_ready()`는 Firebase 로드 → 초기화 → 콜백 실행 순서를 보장
    - [ ] `<head>` 태그 안에서도 안전하게 Firebase 사용 가능
    - [ ] 내부적으로 `ready()` 함수를 통해 Firebase 로드 대기 후 초기화

  - [ ] **잘못된 사용 예제 (에러 발생):**
    ```html
    <script>
    // ❌ 절대 금지: Firebase 초기화 전에 사용
    ready(() => {
        firebase.auth().onAuthStateChanged((user) => {
            console.log(user);
        });
    });
    </script>

    <script>
    // Firebase 초기화 (늦게 실행됨)
    ready(() => {
        firebase.initializeApp({ /* config */ });
    });
    </script>
    ```

  - [ ] **올바른 사용 예제:**
    ```html
    <script>
    // ✅ 올바른 방법: firebase_ready() 사용
    firebase_ready(() => {
        // Firebase가 완전히 초기화된 후 실행됨
        firebase.auth().onAuthStateChanged((user) => {
            if (user) {
                console.log('로그인됨:', user.email);
            } else {
                console.log('로그아웃 상태');
            }
        });
    });
    </script>

    <!-- 로그아웃 예제 -->
    <script>
    firebase_ready(() => {
        // Firebase 초기화 완료 후 로그아웃 실행
        firebase.auth().signOut().then(() => {
            location.href = '/';
        });
    });
    </script>
    ```

  - [ ] **사용 시나리오**:
    - [ ] `<head>` 태그 안에서 Firebase 사용
    - [ ] 페이지 로드 즉시 Firebase 인증 상태 확인
    - [ ] 로그아웃 처리
    - [ ] Firebase 초기화가 필요한 모든 상황

  - [ ] **🔴 위반 시 결과**:
    - [ ] "firebase is not defined" 에러 발생
    - [ ] "Cannot read property 'auth' of undefined" 에러 발생
    - [ ] Firebase 기능이 작동하지 않음
    - [ ] 예측 불가능한 초기화 순서 문제

- [ ] **🚨🚨🚨 Vue.js 중복 선언 방지 필수 규칙 🚨🚨🚨**
  - [ ] **❌ 절대 금지**: `const { createApp, ref } = Vue;` 형태의 구조 분해 할당 사용 금지
  - [ ] **❌ 금지 이유**: 한 HTML 파일에서 여러 Vue 인스턴스 사용 시 "Identifier 'createApp' has already been declared" 오류 발생
  - [ ] **✅ 필수**: `Vue.createApp()`, `Vue.ref()`, `Vue.computed()` 등 Vue 객체를 직접 사용
  - [ ] **✅ 필수**: 모든 Vue Composition API 함수는 `Vue.` 접두사와 함께 사용

  - [ ] **올바른 사용 예제:**
    ```javascript
    // ✅ 올바른 방법 - Vue 객체 직접 사용
    Vue.createApp({
        setup() {
            const open = Vue.ref(false);
            const count = Vue.ref(0);
            const doubled = Vue.computed(() => count.value * 2);

            Vue.onMounted(() => {
                console.log('마운트됨');
            });

            return { open, count, doubled }
        }
    }).mount('#app');
    ```

  - [ ] **잘못된 사용 예제 (절대 금지):**
    ```javascript
    // ❌ 절대 금지 - 중복 선언 오류 발생
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const open = ref(false); // 다른 곳에서 이미 선언되었으면 에러
            return { open }
        }
    }).mount('#app');
    ```

- [ ] **🔥🔥🔥 Vue.js 마운트 대상 필수 규칙 🔥🔥🔥**
  - [ ] **❌ 절대 금지**: `Vue.createApp().mount('body')` - body에 직접 마운트 절대 금지
  - [ ] **❌ 금지 이유**:
    - [ ] 한 페이지에서 여러 Vue 앱 사용 시 충돌 발생
    - [ ] 다른 위젯이나 컴포넌트의 Vue 앱과 겹쳐서 에러 발생
    - [ ] "Cannot read properties of undefined" 에러 발생
  - [ ] **✅ 필수**: 항상 고유한 ID를 가진 특정 컨테이너에 마운트
  - [ ] **✅ 권장**: 각 위젯이나 컴포넌트는 독립적인 `<div id="unique-id">` 컨테이너 생성 후 마운트

  - [ ] **올바른 사용 예제:**
    ```javascript
    // ✅ 올바른 방법 - 고유한 ID를 가진 컨테이너에 마운트
    Vue.createApp({
        setup() {
            // ...
        }
    }).mount('#reminders-widget'); // 특정 ID에 마운트

    Vue.createApp({
        setup() {
            // ...
        }
    }).mount('#chat-widget'); // 다른 위젯은 별도 ID에 마운트
    ```

  - [ ] **잘못된 사용 예제 (절대 금지):**
    ```javascript
    // ❌ 절대 금지 - body에 마운트
    Vue.createApp({
        setup() {
            // ...
        }
    }).mount('body'); // 다른 Vue 앱과 충돌!

    // ❌ 절대 금지 - html에 마운트
    Vue.createApp({
        setup() {
            // ...
        }
    }).mount('html'); // 페이지 전체를 덮어씀!
    ```

  - [ ] **위젯/컴포넌트 구조 예시:**
    ```html
    <!-- PHP 위젯 파일 -->
    <div id="my-widget-<?= uniqid() ?>">
        <!-- 위젯 내용 -->
    </div>

    <script>
    Vue.createApp({
        setup() {
            // 위젯 로직
        }
    }).mount('#my-widget-<?= uniqid() ?>');
    </script>
    ```

  - [ ] **🔴 위반 시 결과:**
    - [ ] 여러 Vue 앱이 서로 충돌하여 렌더링 실패
    - [ ] "Cannot read properties of undefined" 타입 에러 발생
    - [ ] 이벤트 리스너가 null 요소에 등록되어 에러 발생
    - [ ] 페이지 전체가 작동하지 않을 수 있음

- [ ] **기타 JavaScript 지침**
  - [ ] Alpine.js x-data는 `<script>` 태그 내 별도 함수로 분리
  - [ ] JSON 데이터: `JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS` 플래그 사용
  - [ ] 시간 표시: `js/time.functions.js` 함수 사용

## UTF-8 인코딩 필수 규칙

**🔥🔥🔥 최강력 경고: 모든 문서와 소스 코드는 반드시 UTF-8 인코딩으로 작성해야 합니다 🔥🔥🔥**

### UTF-8 인코딩 규칙

- **✅ 필수**: 모든 문서(\*.md) 파일은 **반드시 UTF-8 인코딩**으로 저장
- **✅ 필수**: 모든 소스 코드(_.php, _.js, \*.css) 파일은 **반드시 UTF-8 인코딩**으로 저장
- **✅ 필수**: BOM(Byte Order Mark) 없는 UTF-8 사용
- **✅ 필수**: 파일 생성 시 편집기의 인코딩 설정을 UTF-8로 지정
- **❌ 금지**: EUC-KR, CP949, ISO-8859-1 등 다른 인코딩 사용 절대 금지
- **❌ 금지**: ASCII만 지원하는 에디터 사용 금지

### 인코딩 확인 방법

**macOS/Linux:**

```bash
# 파일 인코딩 확인
file -I docs/api.md

# 올바른 출력: charset=utf-8
```

**VSCode 설정:**

```json
{
  "files.encoding": "utf8",
  "files.autoGuessEncoding": false
}
```

### 위반 시 결과

- 한글이 깨져서 표시됨 (예: 문서 → �8)
- 파일을 읽을 수 없음
- Git에서 충돌 발생
- 웹사이트에서 한글이 제대로 표시되지 않음

**⚠️ 모든 파일 생성 및 수정 시 반드시 UTF-8 인코딩을 확인하세요! ⚠️**

---

# 보안 규칙 - IP 주소 및 민감 정보 보호

**🔥🔥🔥 최강력 보안 규칙: IP 주소 및 민감 정보 노출 절대 금지 🔥🔥🔥**

## IP 주소 표시 금지 규칙

- **❌ 절대 금지**: 프로덕션 서버의 IP 주소를 화면, 소스 코드, AI 응답에 표시하는 것은 절대 금지
- **❌ 절대 금지**: 배포 스크립트, 설정 파일, 문서에서 실제 IP 주소를 노출하지 마세요
- **✅ 필수**: IP 주소는 `[PRODUCTION_SERVER_IP]`, `[서버 IP]` 등의 플레이스홀더로 대체
- **✅ 필수**: AI 응답에서 IP 주소가 포함된 부분은 자동으로 마스킹 처리

## 보안 정보 보호 대상

다음 정보들은 절대로 노출되어서는 안 됩니다:

- [ ] **프로덕션 서버 IP 주소**: `68.183.185.185` 등 실제 IP는 절대 표시 금지
- [ ] **데이터베이스 접속 정보**: 호스트, 포트, 사용자명, 비밀번호
- [ ] **API 키 및 시크릿**: Firebase, AWS, 제3자 서비스 키
- [ ] **SSH 키 및 인증 정보**: 개인키, 공개키 경로
- [ ] **환경 변수 값**: `.env` 파일의 실제 값

## AI 응답 시 보안 처리 규칙

**배포 완료 보고 시**:
```
✅ 배포 완료
✅ 버전: YYYY-MM-DD-HH-MM-SS
✅ Git 커밋 및 푸시 완료
✅ 프로덕션 서버 업데이트 완료
```

**❌ 잘못된 예 (IP 주소 노출)**:
```
✅ 프로덕션 서버(68.183.185.185) 업데이트 완료
```

**✅ 올바른 예 (IP 주소 마스킹)**:
```
✅ 프로덕션 서버 업데이트 완료
```

## 위반 시 결과

- **보안 위협**: 서버 IP 노출로 인한 해킹 시도 위험 증가
- **무단 접근**: 민감한 정보 노출로 인한 보안 침해
- **데이터 유출**: 데이터베이스 및 API 정보 노출

**⚠️⚠️⚠️ 최강력 경고: 모든 보안 정보는 철저히 보호되어야 합니다 ⚠️⚠️⚠️**

---

# "배포" 명령 워크플로우

**🔥🔥🔥🔥🔥 초최강력 규칙: 모든 작업 완료 후 개발자가 요청하지 않아도 반드시 자동으로 배포해야 합니다 🔥🔥🔥🔥🔥**

## 자동 배포 필수 규칙

**⚠️⚠️⚠️ 절대 준수: AI는 모든 작업 완료 후 개발자가 배포를 요청하지 않아도 워크플로우의 맨 마지막 단계에서 반드시 자동으로 배포해야 합니다 ⚠️⚠️⚠️**

- **✅ 절대 필수**: 코드 수정, 파일 생성, 문서 업데이트 등 **모든 작업이 완료되면 워크플로우의 맨 마지막 단계에서 즉시 자동으로 배포**
- **✅ 절대 필수**: 개발자가 "배포"라고 명시적으로 요청하지 않아도 **작업 완료 후 무조건 자동 배포**
- **✅ 절대 필수**: 배포 여부를 개발자에게 물어보지 않고 **즉시 배포 실행**
- **✅ 절대 필수**: 모든 워크플로우(update, 디자인 수정, 코드 작성 등)의 **맨 마지막 단계는 항상 자동 배포**
- **❌ 절대 금지**: "배포할까요?"라고 묻는 것은 절대 금지
- **❌ 절대 금지**: 배포를 건너뛰거나 생략하는 것은 절대 금지
- **❌ 절대 금지**: 개발자가 배포를 요청할 때까지 기다리는 것은 절대 금지

## 배포 스크립트 동작 방식

**배포 스크립트 `./deploy.sh`는 다음 작업을 자동으로 수행합니다:**

1. **모든 테스트 자동 실행**: 배포 스크립트 내부에서 PHP Unit Test, PHP E2E Test 등 모든 테스트를 자동으로 실행
2. **테스트 성공 확인**: 모든 테스트가 통과해야만 배포 진행
3. **테스트 실패 시**: 배포를 중단하고 에러 메시지 출력
4. **테스트 성공 시**: 아래 배포 작업 자동 실행
   - 버전 업데이트 (`etc/app.version.php`)
   - Git 커밋 및 푸시
   - 프로덕션 서버에 SSH로 접속하여 `git pull` 실행

**✅ 중요**: AI는 배포 스크립트를 실행하기만 하면 됩니다. 테스트는 배포 스크립트 내부에서 자동으로 실행되고 검증됩니다.

## "배포" 명령이 실행되는 모든 경우

**다음과 같은 모든 경우에 AI는 반드시 개발자가 요청하지 않아도 자동으로 배포를 수행해야 합니다:**

1. **코드 수정 완료 시**: PHP, JavaScript, CSS 등 모든 코드 수정 후 → **즉시 자동 배포**
2. **파일 생성 완료 시**: 새로운 파일, 위젯, 페이지 생성 후 → **즉시 자동 배포**
3. **문서 업데이트 완료 시**: `*.md` 문서 수정 또는 생성 후 → **즉시 자동 배포**
4. **테스트 작성 완료 시**: 테스트 작성 후 (테스트 실행은 배포 스크립트가 수행) → **즉시 자동 배포**
5. **디자인 수정 완료 시**: CSS, HTML 구조 변경 후 → **즉시 자동 배포**
6. **번역 추가 완료 시**: 다국어 번역 텍스트 추가 후 → **즉시 자동 배포**
7. **설정 변경 완료 시**: 설정 파일 수정 후 → **즉시 자동 배포**
8. **기타 모든 작업 완료 시**: 위에 나열되지 않은 모든 작업 완료 후 → **즉시 자동 배포**

**✅ 필수**: 배포 스크립트 `./deploy.sh`를 프로젝트 루트 폴더에서 실행
**✅ 필수**: 배포 스크립트가 내부적으로 모든 테스트를 실행하고 검증
**✅ 필수**: 테스트 통과 시 버전 업데이트, Git 커밋/푸시, SSH를 통한 서버 업데이트를 자동으로 수행

## "배포" 워크플로우 단계

**🔥🔥🔥 최강력 규칙: 모든 작업의 맨 마지막 단계는 항상 자동 배포입니다 🔥🔥🔥**

### 1단계: 배포 전 확인 (필수)

1. **모든 작업 완료 확인**:
   - [ ] 코드 수정 완료
   - [ ] 테스트 작성 완료 (테스트 실행은 배포 스크립트가 자동으로 수행)
   - [ ] UTF-8 인코딩 확인 완료
   - [ ] 다국어 번역 추가 완료 (필요한 경우)

2. **개발자에게 배포 시작 통보 (필수)**:
   ```
   🚀 모든 작업이 완료되어 자동으로 배포를 시작합니다.
   📦 배포 스크립트: ./deploy.sh (내부적으로 모든 테스트 자동 실행 및 검증)
   ```

### 2단계: 배포 스크립트 실행 (필수)

**✅ 필수**: 개발자가 배포를 요청하지 않아도 무조건 자동으로 `./deploy.sh` 실행

**배포 명령어**:
```bash
./deploy.sh
```

**배포 스크립트가 자동으로 수행하는 작업**:
1. **모든 테스트 자동 실행**: PHP Unit Test, PHP E2E Test 등 모든 테스트를 자동으로 실행
2. **테스트 결과 검증**: 모든 테스트가 통과해야만 배포 계속 진행
3. **테스트 실패 시**: 배포 중단 및 에러 메시지 출력
4. **테스트 성공 시 아래 작업 자동 실행**:
   - **버전 업데이트**: `etc/app.version.php`의 `APP_VERSION` 상수를 현재 날짜-시간(`YYYY-MM-DD-HH-MM-SS`)으로 자동 업데이트
   - **Git 커밋**: 모든 변경사항을 "Commit for the release of the version: YYYY-MM-DD-HH-MM-SS" 메시지로 커밋
   - **Git 푸시**: 변경사항을 원격 저장소에 푸시
   - **SSH 배포**: 프로덕션 서버에 SSH로 접속하여 `git pull` 실행

### 3단계: 배포 결과 확인 (필수)

- [ ] 배포 스크립트가 성공적으로 실행되었는지 확인
- [ ] 테스트가 모두 통과했는지 확인
- [ ] 에러 없이 "✅ 배포 완료!" 메시지가 출력되었는지 확인
- [ ] 배포 실패 시 에러 메시지를 개발자에게 보고하고 배포 중단

### 4단계: 개발자에게 완료 보고 (필수)

**개발자에게 다음 정보를 보고**:
```
✅ 모든 작업 완료
✅ 자동 배포 완료
✅ 버전: YYYY-MM-DD-HH-MM-SS
✅ 모든 테스트 통과
✅ Git 커밋 및 푸시 완료
✅ 프로덕션 서버 업데이트 완료
```

## "배포" 명령 예시

**🔥🔥🔥 중요: 아래 모든 예시에서 개발자가 "배포"를 명시적으로 요청하지 않아도 AI는 작업 완료 후 자동으로 배포를 실행합니다 🔥🔥🔥**

### 예시 1: 개발자가 명시적으로 "배포" 요청

**사용자 요청**: "배포"

**AI 수행 작업**:
1. 모든 작업 완료 확인 (코드 수정, UTF-8 인코딩 확인)
2. 개발자에게 배포 시작 통보
3. 프로젝트 루트 폴더에서 `./deploy.sh` 실행 (테스트 자동 실행 및 검증 포함)
4. 배포 스크립트 실행 결과 확인
5. 개발자에게 배포 완료 보고

### 예시 2: 개발자가 "코드 수정" 요청 (배포 요청 없음)

**사용자 요청**: "사용자 프로필 수정 기능 추가"

**AI 수행 작업**:
1. 코드 수정 작업 수행
2. UTF-8 인코딩 확인
3. **작업 완료 후 자동으로 배포 시작 (개발자가 배포를 요청하지 않았지만 자동 실행)**
4. 개발자에게 배포 시작 통보: "🚀 모든 작업이 완료되어 자동으로 배포를 시작합니다."
5. `./deploy.sh` 실행 (테스트 자동 실행 및 검증 포함)
6. 개발자에게 "✅ 모든 작업 완료 ✅ 자동 배포 완료" 보고

### 예시 3: 개발자가 "문서 수정" 요청 (배포 요청 없음)

**사용자 요청**: "README.md 업데이트"

**AI 수행 작업**:
1. 문서 수정 작업 수행
2. UTF-8 인코딩 확인
3. **작업 완료 후 자동으로 배포 시작 (개발자가 배포를 요청하지 않았지만 자동 실행)**
4. 개발자에게 배포 시작 통보
5. `./deploy.sh` 실행 (테스트 자동 실행 및 검증 포함)
6. 개발자에게 배포 완료 보고

### 예시 4: 개발자가 "디자인 수정" 요청 (배포 요청 없음)

**사용자 요청**: "프로필 페이지 디자인 수정"

**AI 수행 작업**:
1. 디자인 수정 작업 수행
2. CSS/HTML 구조 변경
3. UTF-8 인코딩 확인
4. **작업 완료 후 자동으로 배포 시작 (개발자가 배포를 요청하지 않았지만 자동 실행)**
5. 개발자에게 배포 시작 통보
6. `./deploy.sh` 실행 (테스트 자동 실행 및 검증 포함)
7. 개발자에게 배포 완료 보고

## "배포" 명령 자동 실행 규칙

**🔥🔥🔥🔥🔥 초최강력 규칙: 모든 작업 완료 후 개발자의 명시적 요청 없이도 무조건 자동으로 배포를 수행합니다 🔥🔥🔥🔥🔥**

### 자동 배포가 실행되는 모든 경우

**✅ 필수**: 다음과 같은 **모든 경우**에 개발자가 "배포"라고 요청하지 않아도 **무조건 자동으로 배포**를 수행해야 합니다:

1. **코드 수정 작업 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포
2. **파일 생성 작업 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포
3. **문서 수정 작업 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포
4. **테스트 작성 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포 (테스트 실행은 배포 스크립트가 수행)
5. **디자인 수정 작업 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포
6. **번역 추가 작업 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포
7. **설정 변경 작업 완료 시**: 개발자가 배포를 요청하지 않아도 자동 배포
8. **개발자가 "배포"라고 명시적으로 요청한 경우**: 당연히 배포
9. **개발자가 "~하고 배포"라고 요청한 경우**: 자동 배포
10. **위에 나열되지 않은 모든 작업 완료 시**: 자동 배포

**❌ 절대 금지**:
- "배포할까요?"라고 묻는 것은 절대 금지
- 배포를 건너뛰는 것은 절대 금지
- 배포를 생략하는 것은 절대 금지
- 배포 여부를 개발자에게 확인하는 것은 절대 금지
- 개발자가 배포를 요청할 때까지 기다리는 것은 절대 금지

### 테스트 및 배포 관계

**⚠️⚠️⚠️ 최강력 경고: `./deploy.sh` 스크립트가 내부적으로 모든 테스트를 실행하고 검증합니다 ⚠️⚠️⚠️**

- **✅ 필수**: AI는 `./deploy.sh`를 실행하기만 하면 됩니다
- **✅ 자동 처리**: 배포 스크립트가 내부적으로 모든 테스트를 자동으로 실행
- **✅ 자동 검증**: 테스트 통과 시에만 배포 계속 진행
- **✅ 자동 중단**: 테스트 실패 시 배포 스크립트가 자동으로 배포 중단
- **✅ 간단한 역할**: AI는 작업 완료 후 `./deploy.sh`를 실행하고 결과를 보고하기만 하면 됩니다

### 배포 워크플로우 최종 요약

**🔥🔥🔥🔥🔥 초최강력 규칙 요약 🔥🔥🔥🔥🔥**

1. **모든 작업 완료 후**: 개발자가 요청하지 않아도 무조건 자동 배포
2. **배포 스크립트 실행**: `./deploy.sh` 명령만 실행하면 됨
3. **자동 테스트 실행**: 배포 스크립트가 내부적으로 모든 테스트 자동 실행 및 검증
4. **자동 배포**: 테스트 통과 시 버전 업데이트, Git 커밋/푸시, SSH 배포 자동 실행
5. **완료 보고**: 개발자에게 배포 완료 통보

**✅ AI의 역할**: 작업 완료 → `./deploy.sh` 실행 → 결과 보고 (끝!)

---
