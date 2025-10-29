# E2E 테스트 및 웹 UI/UX 디버깅 기술 문서

이 문서는 Playwright MCP와 Chrome DevTools MCP를 사용하여 Vibe 프로젝트의 E2E 테스트와 웹 UI/UX 디버깅을 수행하는 방법을 안내합니다.

---

## 목차

1. [개요](#개요)
2. [Playwright MCP 사용 가이드](#playwright-mcp-사용-가이드)
3. [Chrome DevTools MCP 사용 가이드](#chrome-devtools-mcp-사용-가이드)
4. [E2E 테스트 작성하기](#e2e-테스트-작성하기)
5. [UI/UX 디버깅 기법](#uiux-디버깅-기법)
6. [실전 예제](#실전-예제)
7. [트러블슈팅](#트러블슈팅)

---

## 개요

### Playwright MCP vs Chrome DevTools MCP

| 기능 | Playwright MCP | Chrome DevTools MCP |
|------|---|---|
| **주 용도** | 자동화된 E2E 테스트 작성 | 실시간 브라우저 디버깅 |
| **사용 시기** | 회귀 테스트, CI/CD 파이프라인 | 개발 중 문제 해결, UI 검증 |
| **브라우저 제어** | 프로그래밍 방식 | 명령어 기반 |
| **비용** | 무료 | 무료 |
| **학습곡선** | 중간 (자연어 명령 지원) | 낮음 (직관적 UI) |

### Vibe 프로젝트에서의 활용

- **Playwright MCP**: 회원가입, 로그인, 게시판 CRUD 등의 자동화 테스트
- **Chrome DevTools MCP**: 인증 플로우 검증, CSS 스타일 확인, 성능 측정

---

## Playwright MCP 사용 가이드

### 설치 및 초기 설정

Playwright MCP는 Claude Code에 기본으로 통합되어 있습니다.

```bash
# 필요한 브라우저 설치 (처음 사용 시)
npx playwright install chromium
```

### 기본 명령어

#### 1. 새 브라우저 세션 시작

사용자: Playwright를 사용하여 localhost:3000을 열어줄래?

Playwright는 자동으로 브라우저를 열고 URL로 이동합니다.

#### 2. 페이지 네비게이션

```typescript
// 특정 URL로 이동
await browser_navigate("http://localhost:3000");

// 뒤로 가기
await browser_navigate_back();
```

#### 3. 요소 선택 및 상호작용

```typescript
// 스냅샷 획득 (현재 페이지의 접근성 정보)
await browser_snapshot();

// 텍스트 입력
await browser_type(
  element: "이메일 입력 필드",
  ref: "INPUT_REF_001",
  text: "user@example.com"
);

// 클릭
await browser_click(
  element: "로그인 버튼",
  ref: "BUTTON_REF_002"
);

// 옵션 선택 (드롭다운)
await browser_select_option(
  element: "카테고리 선택",
  ref: "SELECT_REF_003",
  values: ["공지사항"]
);

// 체크박스 토글
await browser_fill_form(
  fields: [
    {
      name: "약관 동의",
      type: "checkbox",
      ref: "CHECKBOX_REF_004",
      value: "true"
    }
  ]
);
```

#### 4. 대기 및 검증

```typescript
// 특정 텍스트 나타날 때까지 대기
await browser_wait_for(
  text: "로그인 성공",
  time: 5 // 초 단위
);

// 텍스트 사라질 때까지 대기
await browser_wait_for(
  textGone: "로딩 중...",
  time: 3
);

// 시간 경과 대기
await browser_wait_for(time: 2);
```

#### 5. 파일 업로드

```typescript
// 파일 선택 입력 요소에 파일 업로드
await browser_file_upload(
  paths: ["/path/to/image.png", "/path/to/document.pdf"]
);
```

#### 6. JavaScript 실행

```typescript
// 페이지 제목 가져오기
const title = await browser_evaluate(
  function: "() => { return document.title }"
);

// 특정 요소의 텍스트 가져오기
const elementText = await browser_evaluate(
  element: "게시판 제목",
  ref: "H1_REF_005",
  function: "(element) => { return element.innerText }"
);

// 현재 URL 가져오기
const url = await browser_evaluate(
  function: "() => { return window.location.href }"
);
```

#### 7. 네트워크 요청 검사

```typescript
// 모든 네트워크 요청 조회
const requests = await browser_network_requests();

// 요청 상세 정보 확인
for (const request of requests) {
  console.log(`${request.method} ${request.url}`);
  console.log(`Status: ${request.status}`);
}
```

#### 8. 스크린샷 및 스냅샷

```typescript
// 스크린샷 저장
await browser_take_screenshot(
  filename: "test-result.png"
);

// 전체 페이지 스크린샷
await browser_take_screenshot(
  fullPage: true,
  filename: "full-page.png"
);

// 특정 요소 스크린샷
await browser_take_screenshot(
  element: "로그인 폼",
  ref: "FORM_REF_006",
  filename: "login-form.png"
);

// 접근성 스냅샷 (권장)
await browser_snapshot();
```

#### 9. 드래그 앤 드롭

```typescript
// 요소를 다른 요소로 드래그
await browser_drag(
  startElement: "할 일 아이템 1",
  startRef: "TODO_REF_001",
  endElement: "완료된 목록",
  endRef: "LIST_REF_002"
);
```

#### 10. 마우스 호버

```typescript
await browser_hover(
  element: "메뉴 항목",
  ref: "MENU_REF_003"
);
```

#### 11. 콘솔 메시지 확인

```typescript
// 모든 콘솔 메시지 확인
const messages = await browser_console_messages();

// 에러만 확인
const errors = await browser_console_messages(onlyErrors: true);
```

#### 12. 탭 관리

```typescript
// 탭 목록 조회
const tabs = await browser_tabs(action: "list");

// 새 탭 열기
await browser_tabs(action: "new");

// 특정 탭 선택
await browser_tabs(action: "select", index: 1);

// 탭 닫기
await browser_tabs(action: "close", index: 1);
```

#### 13. 브라우저 창 크기 조정

```typescript
// 모바일 크기로 조정
await browser_resize(width: 375, height: 667);

// 태블릿 크기로 조정
await browser_resize(width: 768, height: 1024);

// 데스크톱 크기로 조정
await browser_resize(width: 1920, height: 1080);
```

#### 14. 대화상자 처리

```typescript
// 알림(alert) 수락
await browser_handle_dialog(accept: true);

// 확인(confirm) 취소
await browser_handle_dialog(accept: false);

// 프롬프트(prompt) 답변 입력
await browser_handle_dialog(
  accept: true,
  promptText: "사용자가 입력할 텍스트"
);
```

---

## Chrome DevTools MCP 사용 가이드

### 기본 개념

Chrome DevTools MCP는 Chrome/Chromium 기반 브라우저의 개발자 도구를 제어합니다.

### 주요 명령어

#### 1. 페이지 네비게이션

```typescript
// URL로 이동
await navigate_page(url: "http://localhost:3000");

// 뒤로 가기
await navigate_page_history(navigate: "back");

// 앞으로 가기
await navigate_page_history(navigate: "forward");

// 타임아웃 설정
await navigate_page(
  url: "http://localhost:3000",
  timeout: 5000 // 5초
);
```

#### 2. 페이지 스냅샷 및 스크린샷

```typescript
// 접근성 트리 스냅샷 (권장)
await take_snapshot(verbose: false);

// 상세 스냅샷 (모든 접근성 정보 포함)
await take_snapshot(verbose: true);

// 전체 페이지 스크린샷
await take_screenshot(fullPage: true);

// 특정 영역 스크린샷
await take_screenshot(
  uid: "ELEMENT_UID",
  filePath: "/path/to/screenshot.png"
);

// JPEG 포맷으로 저장
await take_screenshot(
  format: "jpeg",
  quality: 85
);
```

#### 3. 요소 선택 및 클릭

```typescript
// 요소 클릭
await click(uid: "BUTTON_UID");

// 더블 클릭
await click(uid: "BUTTON_UID", dblClick: true);

// 우클릭
await click(uid: "BUTTON_UID", button: "right");

// Shift 키와 함께 클릭
await click(
  uid: "ELEMENT_UID",
  modifiers: ["Shift"]
);

// Ctrl(또는 Cmd) + 클릭
await click(
  uid: "ELEMENT_UID",
  modifiers: ["ControlOrMeta"]
);
```

#### 4. 텍스트 입력 및 폼 작성

```typescript
// 단일 필드 입력
await fill(uid: "INPUT_UID", value: "입력할 텍스트");

// 여러 필드 동시 입력
await fill_form(
  elements: [
    { uid: "EMAIL_UID", value: "user@example.com" },
    { uid: "PASSWORD_UID", value: "password123" },
    { uid: "NAME_UID", value: "홍길동" }
  ]
);
```

#### 5. JavaScript 실행

```typescript
// 페이지 제목 가져오기
const title = await evaluate_script(
  function: "() => { return document.title }"
);

// 함수에 인자 전달
const text = await evaluate_script(
  function: "(el) => { return el.innerText }",
  args: [{ uid: "ELEMENT_UID" }]
);

// 비동기 함수 실행
const data = await evaluate_script(
  function: "async () => { return await fetch('/api/data').then(r => r.json()) }"
);
```

#### 6. 파일 업로드

```typescript
// 파일 입력 요소를 통해 파일 업로드
await upload_file(
  uid: "FILE_INPUT_UID",
  filePath: "/path/to/file.jpg"
);
```

#### 7. 드래그 앤 드롭

```typescript
// 요소를 다른 요소로 드래그
await drag(
  from_uid: "SOURCE_ELEMENT_UID",
  to_uid: "TARGET_ELEMENT_UID"
);
```

#### 8. 마우스 호버

```typescript
await hover(uid: "ELEMENT_UID");
```

#### 9. 콘솔 메시지 조회

```typescript
// 모든 콘솔 메시지 조회
const messages = await list_console_messages();

// 특정 타입만 조회 (error, warn, info, log 등)
const errors = await list_console_messages(
  types: ["error"]
);

// 최근 10개만 조회
const recentMessages = await list_console_messages(pageSize: 10);

// 특정 메시지 상세 정보
const message = await get_console_message(msgid: 5);
```

#### 10. 네트워크 요청 조회

```typescript
// 모든 네트워크 요청 조회
const requests = await list_network_requests();

// 특정 리소스 타입만 조회
const fetchRequests = await list_network_requests(
  resourceTypes: ["fetch", "xhr"]
);

// 최근 20개 조회
const recentRequests = await list_network_requests(pageSize: 20);

// 특정 요청 상세 정보
const request = await get_network_request(reqid: 3);

// 응답 본문 확인
console.log(request.responseBody);
```

#### 11. 성능 트레이싱

```typescript
// 성능 추적 시작 (페이지 자동 리로드)
await performance_start_trace(
  reload: true,
  autoStop: true // 페이지 로드 완료 시 자동 중지
);

// 수동으로 중지
await performance_stop_trace();

// 성능 인사이트 분석
await performance_analyze_insight(
  insightName: "LCPBreakdown"
);
```

#### 12. 네트워크 에뮬레이션

```typescript
// 느린 3G 시뮬레이션
await emulate_network(throttlingOption: "Slow 3G");

// 오프라인 모드
await emulate_network(throttlingOption: "Offline");

// 정상 네트워크
await emulate_network(throttlingOption: "No emulation");
```

#### 13. CPU 스로틀링

```typescript
// 4배 느리게 (저사양 기기 시뮬레이션)
await emulate_cpu(throttlingRate: 4);

// 정상 속도
await emulate_cpu(throttlingRate: 1);
```

#### 14. 대기

```typescript
// 특정 텍스트 나타날 때까지 대기
await wait_for(
  text: "로그인 성공",
  timeout: 5000 // 5초
);
```

#### 15. 페이지 관리

```typescript
// 열린 페이지 목록 조회
const pages = await list_pages();

// 특정 페이지 선택
await select_page(pageIdx: 0);

// 새 페이지 열기
await new_page(url: "http://localhost:3000");

// 페이지 닫기
await close_page(pageIdx: 1);
```

---

## E2E 테스트 작성하기

### 회원가입 테스트 예제

```typescript
// 1. 개발 서버가 실행 중인지 확인
// npm run dev

// 2. 회원가입 페이지로 이동
await browser_navigate("http://localhost:3000/auth/signup");

// 3. 페이지 로드 대기
await browser_wait_for(text: "회원가입", time: 5);

// 4. 현재 페이지 상태 스냅샷 (어떤 요소가 있는지 확인)
await browser_snapshot();

// 5. 스냅샷 결과에서 각 입력 필드의 ref 확인 후 입력
// (실제 ref는 스냅샷 결과에서 확인)
await browser_fill_form(
  fields: [
    {
      name: "이메일",
      type: "textbox",
      ref: "input-email", // 스냅샷에서 확인한 ref
      value: "test@example.com"
    },
    {
      name: "비밀번호",
      type: "textbox",
      ref: "input-password",
      value: "Test@12345"
    },
    {
      name: "이름",
      type: "textbox",
      ref: "input-name",
      value: "테스트 사용자"
    }
  ]
);

// 6. 회원가입 버튼 클릭
await browser_click(
  element: "회원가입 버튼",
  ref: "button-signup"
);

// 7. 가입 완료 메시지 대기
await browser_wait_for(text: "가입이 완료되었습니다", time: 5);

// 8. 스크린샷 저장
await browser_take_screenshot(filename: "signup-success.png");

// 9. 결과 검증
const url = await browser_evaluate(
  function: "() => { return window.location.href }"
);
console.log("최종 URL:", url); // /auth/login 이어야 함
```

### 로그인 테스트 예제

```typescript
// 1. 로그인 페이지로 이동
await browser_navigate("http://localhost:3000/auth/login");

// 2. 페이지 스냅샷으로 요소 확인
await browser_snapshot();

// 3. 로그인 정보 입력
await browser_fill_form(
  fields: [
    {
      name: "이메일",
      type: "textbox",
      ref: "input-email",
      value: "test@example.com"
    },
    {
      name: "비밀번호",
      type: "textbox",
      ref: "input-password",
      value: "Test@12345"
    }
  ]
);

// 4. 로그인 버튼 클릭
await browser_click(
  element: "로그인 버튼",
  ref: "button-login"
);

// 5. 로그인 완료 대기
await browser_wait_for(text: "홈", time: 5);

// 6. 로그인 성공 확인
const url = await browser_evaluate(
  function: "() => { return window.location.href }"
);
console.log("로그인 후 URL:", url); // / 이어야 함

// 7. 사용자 정보 확인 (localstorage, sessionStorage 등)
const userInfo = await browser_evaluate(
  function: "() => { return localStorage.getItem('user') }"
);
console.log("저장된 사용자 정보:", userInfo);
```

---

## UI/UX 디버깅 기법

### 1. 반응형 디자인 테스트

```typescript
// 모바일 크기로 테스트
await browser_resize(width: 375, height: 667);

// 모바일에서 로그인 페이지 확인
await browser_navigate("http://localhost:3000/auth/login");
await browser_take_screenshot(
  fullPage: true,
  filename: "login-mobile.png"
);

// 태블릿 크기로 테스트
await browser_resize(width: 768, height: 1024);
await browser_take_screenshot(
  fullPage: true,
  filename: "login-tablet.png"
);

// 데스크톱 크기로 테스트
await browser_resize(width: 1920, height: 1080);
await browser_take_screenshot(
  fullPage: true,
  filename: "login-desktop.png"
);
```

### 2. 접근성(A11y) 확인

```typescript
// 시맨틱 HTML 구조 확인
const snapshot = await browser_snapshot(verbose: true);

// 모든 요소가 접근 가능한지 확인
// - 버튼, 링크에 올바른 role 속성
// - 입력 필드에 label 태그
// - 이미지에 alt 텍스트
// - 색상 대비 검사
```

### 3. 네트워크 요청 분석

```typescript
// Chrome DevTools 사용
// 1. 페이지 열기 (개발자 도구로 모든 요청 기록)
await navigate_page(url: "http://localhost:3000/auth/login");

// 2. 로그인 시뮬레이션
await fill_form(elements: [ /* ... */ ]);
await click(uid: "button-login");

// 3. 네트워크 요청 분석
const requests = await list_network_requests();

// 4. API 요청 필터링
const apiRequests = requests.filter(r => r.url.includes('/api'));

// 5. 실패한 요청 확인
const failedRequests = requests.filter(r => r.status >= 400);

for (const request of failedRequests) {
  const details = await get_network_request(reqid: request.id);
  console.log(`실패한 요청: ${request.method} ${request.url}`);
  console.log(`상태 코드: ${request.status}`);
  console.log(`응답: ${details.responseBody}`);
}
```

### 4. 콘솔 에러 확인

```typescript
// 페이지에서 발생한 모든 JavaScript 에러 확인
await navigate_page(url: "http://localhost:3000/auth/login");

// 로그인 플로우 실행
await fill_form(elements: [ /* ... */ ]);
await click(uid: "button-login");
await wait_for(text: "홈", timeout: 5000);

// 콘솔 에러 확인
const errors = await list_console_messages(types: ["error"]);

for (const error of errors) {
  const details = await get_console_message(msgid: error.id);
  console.error("JavaScript 에러:", details.message);
}

// 경고도 확인
const warnings = await list_console_messages(types: ["warn"]);
```

---

## 트러블슈팅

### 문제 1: "요소를 찾을 수 없음" 에러

**원인**: 스냅샷에서 확인한 ref가 변경되었거나 요소가 아직 로드되지 않음

**해결책**:
```typescript
// 1. 항상 최신 스냅샷 획득
await browser_snapshot();

// 2. 요소 로드 대기
await browser_wait_for(text: "로그인 버튼이 포함된 텍스트", time: 5);

// 3. 요소 존재 확인
const element = await browser_evaluate(
  function: "() => {
    return document.querySelector('button[type=\"submit\"]') ? true : false;
  }"
);

if (element) {
  // 클릭
} else {
  console.error("요소를 찾을 수 없습니다");
}
```

### 문제 2: 타임아웃 에러

**원인**: 로딩이 오래 걸리거나 특정 이벤트가 발생하지 않음

**해결책**:
```typescript
// 1. 타임아웃 증가
await browser_wait_for(text: "로그인", time: 10); // 10초 대기

// 2. 대신 다른 요소 대기
await browser_wait_for(text: "에러 메시지", time: 5); // 실패 시 에러 확인

// 3. 콘솔 에러 확인
const errors = await browser_console_messages(onlyErrors: true);
console.log("콘솔 에러:", errors);

// 4. 네트워크 요청 확인
const failedRequests = await browser_network_requests();
for (const req of failedRequests) {
  console.log(`${req.method} ${req.url} - Status: ${req.status}`);
}
```

### 문제 3: 인증 상태 문제

**원인**: Firebase 인증 토큰이 만료되었거나 설정이 잘못됨

**해결책**:
```typescript
// 1. 현재 인증 상태 확인
const authState = await browser_evaluate(
  function: "() => {
    return {
      localStorageUser: localStorage.getItem('user'),
      currentUser: JSON.stringify(window.firebase?.auth?.currentUser || null)
    };
  }"
);
console.log("인증 상태:", authState);

// 2. Firebase 초기화 확인
const firebaseStatus = await browser_evaluate(
  function: "() => {
    return {
      firebaseLoaded: typeof window.firebase !== 'undefined',
      authInitialized: typeof window.firebase?.auth?.currentUser !== 'undefined'
    };
  }"
);

// 3. 환경 변수 확인
// .env.local에 NEXT_PUBLIC_FIREBASE_* 설정 확인

// 4. 개발 서버 재시작
// npm run dev
```

---

## 성능 최적화 팁

### 1. 불필요한 스냅샷 제거
```typescript
// ❌ 나쁜 예: 매 단계마다 스냅샷
await browser_navigate("...");
await browser_snapshot();
await browser_fill_form(...);
await browser_snapshot();
await browser_click(...);
await browser_snapshot();

// ✅ 좋은 예: 필요할 때만 스냅샷
await browser_navigate("...");
await browser_snapshot(); // 한 번만
await browser_fill_form(...);
await browser_click(...);
```

### 2. 효율적인 대기
```typescript
// ❌ 나쁜 예: 고정 시간 대기
await browser_wait_for(time: 5);

// ✅ 좋은 예: 특정 요소 대기
await browser_wait_for(text: "로드 완료", time: 5);
```

---

## 참고 자료

- Playwright 공식 문서: https://playwright.dev
- Chrome DevTools 공식 문서: https://developer.chrome.com/docs/devtools
- Firebase 테스트 가이드: https://firebase.google.com/docs/testing
- 웹 접근성 검사: https://www.w3.org/WAI/test-evaluate

---

**마지막 업데이트**: 2025년 10월 27일
**작성자**: Claude Code Skill Documentation
**상태**: 활성
**인코딩**: UTF-8
