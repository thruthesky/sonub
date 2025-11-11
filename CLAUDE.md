# Workflow

- [ ] 모든 소스 코드의 주석과 모든 문서는 한국어로 작성해야 합니다. 반드시 utf-8 인코딩을 사용해야 합니다.
- [ ] 반드시 https://sedai.dev/instructions 및 ./specs/index.md 파일을 먼저 읽고 이해해야 합니다.
- [ ] 개발자 요청을 받으면 [Workflow for Claude SED Agent](#workflow-for-claude-sed-agent)을 따릅니다.
- [ ] 관련된 사양 문서를 모두 읽고 YAML 헤더를 구문 분석합니다.
- [ ] 모든 개발작업이 끝나면, 반드시 작업한 내용을 요약하여 ./specs/\*.md 파일에 SED 형식에 맞게 기록해야 합니다.
- [ ] 가능한 모든 경우에서 Explore subagents 기능을 사용하여 high throughness 로 작업을 수행합니다.
- [ ] 개발자가 요청을 하면, 모든 경우(개발 작업)에서 최대한의 subagents 를 사용하여 병렬(Parallel)로 작업을 작업을 수행합니다.
- [ ] Firebase Cloud Functions 코드 `./firebase/functions/src` 작업이 끝난 다음 항상 `npm run deploy` 명령을 수행하여 배포를 합니다.
- [ ] 작업이 끝난 다음 항상 `npm run check` 명령을 수행하여 소스 코드를 검사하고, 발견된 모든 문제를 수정합니다.
- [ ] 작업이 끝난 다음 항상 개발자에게 아래의 체크리스트를 보여주고 다음 작업을 물어보세요.
  - [ ] 결과가 잘못되어 처음부터 완전히 새로운 개념으로 다시 작업하기
  - [ ] 현재 작업의 수행 이유, 과정, 로직, 결과에 대해서 SED 사양을 엄격히 준수하여 ./specs/\*.md 파일에 업데이트하기
  - [ ] Git push 하기
  - [ ] 다음 작업 추천하기
- [ ] 만약, 임시 파일을 생성해야한다면, 반드시 `./tmp` 폴더에 생성해야 합니다.
- [ ] 개발자가 요청을 하면, 모든 경우(개발 작업)에서 최대한의 SED specifications 를 엄격히 준수하여 작업을 수행합니다.
  - [ ] 항상, **반드시** 작업을 수행할 때 마다 먼저,
    - [ ] [SED DTOC](./specs/index.md) 를 참고하고, 이 문서에서 적당한
    - [ ] 세부 스펙 문서를 읽고, 그 개념을 이해하고,
    - [ ] 세버 스펙의 워크플로에 따라서 작업을 해야 합니다.
- [ ] 개발자가 요청을 하면, 본 문서의 각 항목들을 엄격히 준수하여 작업을 수행합니다.
  - [ ] Svelte 의 Tailwind CSS 스타일링은 아래의 [CSS 스타일링](#css-스타일링) 섹션을 따릅니다.
  - [ ] Svelte 의 paraglide i18n 다국어 처리는 아래의 [다국어 (i18n) 처리](#다국어-i18n-처리) 섹션을 따릅니다.
  - [ ] 모든 UI/UX 작업(코딩)을 할 때에 svelte-shadcn 과 Tailwind CSS 를 씁니다.

## CSS 스타일링

개발자가 `css` 와 같이 요청을 하면, 아래의 규칙을 따라 업데이트를 해 주세요.

Svelte 의 Tailwind CSS 스타일링은 아래의 규칙을 따릅니다:

1. Tailwind CSS 유틸리티 클래스를 통해서만 CSS Styling 디자인 작업을 합니다.
   1. 인라인 `class="..."`를 사용는 것과
   2. `<style> ... { @apply ...; } </style>` 방식만 사용합니다.
   3. `<style> ... </style>` 내에 직접 Pure (Native) CSS 속성을 작성하지 않습니다.
2. Layout 관련된 Tailwind CSS utility class 는 `< class='...'>` 와 같이 inline class 에 직접 적용합니다.
   1. 예를 들면, `flex`, `grid`, `margin`, `padding`, `width`, `height`, `position` 등 레이아웃 관련된 클래스들은 인라인으로 직접 작성합니다.
   2. 그 외 (색상, 폰트, 여백, 테두리 등) 스타일 관련된 Tailwind CSS utility class 는 `<style> ... { @apply ...; } </style>` 방식을 사용하여 스타일링을 적용합니다.
3. [Svelte 전용 문법](./specs/sonub-design-tailwindcss.md#32-svelte-전용-문법) 섹션에서 설명한 것처럼 깔끔한 구조를 유지해주세요.
4. 오직, Light mode 만 사용합니다. Dark mode 는 사용하지 않습니다.
5. 그리고 반드시 [Sonub Tailwind CSS - SED Spec](./specs/sonub-setup-tailwind.md) 문서를 참고하여 스타일링 작업을 합니다.

## 다국어 (i18n) 처리

개발자가 `다국어`, `i18n`, `l10n` 와 같이 요청을 하면 아래의 과정을 따라서 다국어를 적용합니다:

1. 소스 코드에서 하드코딩된 모든 문자열을 찾아냅니다.
2. 각 문자열에 대해 고유한 키를 생성합니다. (예: `환영_인사`, `네트워크_에러`) 키는 반드시 한글로 합니다.
3. `./messages/*.json` 에 각 언어별로 JSON 파일을 업데이트합니다.
   - 예: `messages/ko.json`, `messages/en.json`, `messages/ja.json`, `messages/zh.json`
4. 소스 코드에서 하드코딩된 문자열을 제거하고, 다국어 라이브러리를 사용하여 해당 키로 문자열을 불러오도록 수정합니다.
   - 예: `import * as m from '$lib/paraglide/messages.js';` 후 `m.환영_인사()` 와 같이 사용해서 i18n 번역 텍스트 데이터 불러와 다국어 번역 적용

## 빌드 버전 업데이트

개발자가 **"버전 업데이트"** 또는 **"빌드 버전 업데이트"** 요청 시:

1. `src/lib/version.ts` 파일의 `BUILD_VERSION` 값을 현재 날짜와 시간으로 업데이트합니다.
2. 형식: `YY. MM. DD. h:mmAM/PM` (예: "25. 11. 09. 8:49PM")
3. 날짜 형식 규칙:
   - 년도(YY): 2자리 (예: 25 = 2025년)
   - 월(MM): 2자리, 앞에 0 패딩 (예: 01, 02, ... 11, 12)
   - 일(DD): 2자리, 앞에 0 패딩 (예: 01, 02, ... 31)
   - 시간: 12시간 형식 (1-12), 분은 2자리 (예: 8:49PM, 11:05AM)
   - AM/PM: 대문자
   - 구분자: 마침표(.) + 공백
4. 업데이트 후 개발자에게 새 버전을 알려줍니다.

**예시:**

```typescript
export const BUILD_VERSION = '25. 11. 09. 8:49PM';
```

# Workflow for Claude SED Agent

- [ ] Learn SED Methodology: Read the following pages and understand SED methodology:
  - https://sedai.dev
  - https://sedai.dev/principles
  - https://sedai.dev/philosophy

- [ ] Always Consult Specifications First: Before starting any development task, read https://sedai.dev/instructions and ./specs/index.md

- [ ] Strict Specification Obedience:
  - Follow specifications exactly, even if they appear incorrect
  - Never implement features not defined in specifications
  - Never modify specifications directly during implementation
  - If specifications are unclear, ask for clarification rather than guessing

- [ ] Error Reporting Protocol: When critical errors are detected in specifications:
  - Halt development immediately
  - Report the issue to developers with specific details
  - Recommend specification improvements
  - Wait for specification updates before continuing

- [ ] Ask for Clarification When Needed: When specifications are ambiguous or incomplete:
  - Request clarification from the developer instead of making assumptions
  - Ask for specific details about logic, source code, styles, or any other information needed
  - Never proceed with implementation based on guesswork or inference
  - Ensure all details are explicitly documented before continuing development
