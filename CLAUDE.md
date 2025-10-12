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
  - [ ] **디자인 관련 작업 시 반드시 `docs/design-guideline.md` 문서를 먼저 읽어야 합니다**
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
- [ ] PHP 단위 테스트 가이드라인
  - [ ] **🔥🔥🔥 최강력 규칙: 모든 테스트 파일, 임시 파일, 검증 파일은 반드시 `./tests` 폴더 아래에 저장해야 합니다 🔥🔥🔥**
  - [ ] **절대 금지**: 테스트 관련 파일을 프로젝트 루트나 `lib`, `src` 등 다른 폴더에 저장하는 것은 절대 금지
  - [ ] **위반 시**: 프로젝트 구조 오염, 운영 코드와 테스트 코드 혼재, Git 관리 어려움
  - [ ] 모든 PHP 단위 테스트는 외부 테스트 프레임워크 없이 순수 PHP로 작성해야 합니다.
  - [ ] 테스트 파일은 소스 코드와 동일한 구조로 `tests` 디렉토리에 저장해야 합니다.
  - [ ] 테스트 파일 이름은 `.test.php`로 끝나야 합니다 (예: `db.php` 테스트를 위한 `db.test.php`).
  - [ ] 각 테스트 파일은 PHP 명령어로 독립적으로 실행 가능해야 합니다: `php tests/db/db.test.php`
  - [ ] 간단한 단언문과 명확한 출력 메시지를 사용하여 테스트 결과를 표시하세요.
  - [ ] **테스트 파일 저장 위치 예시**:
    - [ ] ✅ 올바른 위치: `tests/db/db.connection.test.php`
    - [ ] ✅ 올바른 위치: `tests/user/user.crud.test.php`
    - [ ] ✅ 올바른 위치: `tests/temp/verify_api.php`
    - [ ] ❌ 잘못된 위치: `db.test.php` (루트 폴더)
    - [ ] ❌ 잘못된 위치: `lib/db/db.test.php` (소스 코드 폴더)
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
  - [ ] **절대 준수**: 모든 데이터베이스 쿼리는 `lib/db/db.php`의 쿼리 빌더를 사용해야 합니다
  - [ ] **절대 금지**: 직접 SQL 문자열을 작성하거나 mysqli를 직접 사용하는 것은 절대 금지 (복잡한 쿼리 제외)
  - [ ] **데이터베이스 작업 전 필수 확인 사항**:
    - [ ] `docs/database.md` 문서를 읽고 쿼리 빌더 사용법 확인
    - [ ] `lib/db/db.php`의 메서드 예제 확인
    - [ ] 비슷한 기능의 기존 코드가 있는지 검색
  - [ ] **PHP 함수 작성 시 DB 입출력 규칙**:
    - [ ] 모든 SELECT 쿼리: `db()->select()->from()->where()->get()` 패턴 사용
    - [ ] 모든 INSERT 쿼리: `db()->insert($data)->into($table)` 패턴 사용
    - [ ] 모든 UPDATE 쿼리: `db()->update($data)->table($table)->where()->execute()` 패턴 사용
    - [ ] 모든 DELETE 쿼리: `db()->delete()->from($table)->where()->execute()` 패턴 사용
    - [ ] 플레이스홀더(?)를 사용하여 SQL 인젝션 방지
  - [ ] **데이터베이스 작업 예시**:
    - [ ] ✅ 올바른 방법: `db()->select('*')->from('users')->where('id = ?', [$id])->first()`
    - [ ] ✅ 올바른 방법: `db()->insert(['name' => $name, 'email' => $email])->into('users')`
    - [ ] ❌ 잘못된 방법: `mysqli_query($conn, "SELECT * FROM users WHERE id = $id")`
    - [ ] ❌ 잘못된 방법: 직접 SQL 문자열 작성 (복잡한 쿼리 제외)
  - [ ] **사용자, 게시판, 댓글 등 모든 데이터 입출력**:
    - [ ] 사용자 데이터: `users` 테이블 사용
    - [ ] 게시글 데이터: `posts` 테이블 사용
    - [ ] 댓글 데이터: `comments` 테이블 사용
    - [ ] 모든 데이터 조회/생성/수정/삭제 시 `db()` 쿼리 빌더 사용
  - [ ] **복잡한 쿼리의 경우**:
    - [ ] JOIN, GROUP BY, HAVING 등이 필요한 경우에만 `db()->query()` 사용
    - [ ] 그 외에는 반드시 쿼리 빌더 메서드 사용
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
