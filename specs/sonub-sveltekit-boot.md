---
name: sonub-sveltekit-boot
version: 1.0.0
description: SvelteKit 부팅/초기화 파이프라인 및 Firebase 연동 절차를 텍스트 플로차트로 정리한 SED 명세
author: Codex Agent
email: noreply@openai.com
step: 15
priority: '**'
dependencies:
  - sonub-setup-svelte.md
  - sonub-setup-tailwind.md
  - sonub-setup-shadcn.md
  - sonub-setup-firebase.md
tags:
  - sveltekit
  - boot-sequence
  - firebase
  - tailwindcss
  - shadcn
  - env
---

## 1. 개요

- 본 문서는 **현재 Sonub 코드베이스** 기준으로 SvelteKit을 설치, 초기화, 실행하는 전 과정을 SED 형식으로 기록한다.
- 프로젝트가 의존하는 TailwindCSS, shadcn-svelte, Firebase 초기화, `.env` 로딩, 최상위 `+layout.svelte` 및 `+page.svelte` 렌더링 순서를 **텍스트 플로차트**로 정리하여 재사용 가능하도록 한다.
- 목적:
  1. 신규 기여자가 SvelteKit 부팅 순서를 빠르게 파악하고 재현할 수 있도록 한다.
  2. 동일한 세팅을 다른 프로젝트에도 그대로 복제할 수 있도록 단계별 지침과 코드 레퍼런스를 제공한다.

## 2. 설치 구성요소

| 영역 | 사용 라이브러리 / 파일 | 비고 |
| --- | --- | --- |
| 프레임워크 | `svelte@^5.41.0`, `@sveltejs/kit@^2.47.1`, `vite@^7.1.10` | `package.json` scripts: `dev`, `build`, `preview` |
| 스타일링 | `tailwindcss@^4.1.14`, `@tailwindcss/forms`, `@tailwindcss/typography`, `@tailwindcss/vite` | `tailwind.config`, `src/app.css` |
| UI 라이브러리 | `shadcn-svelte@^1.0.10`, `clsx`, `tailwind-merge` | Button/Card/Alert 등 컴포넌트 |
| 백엔드 SDK | `firebase@^12.5.0` | Auth/RTDB/Storage/Analytics |
| 번역 | `@inlang/paraglide-js@^2.4.0` | 다국어 메시지 로딩 |
| 테스트/품질 | `vitest`, `@playwright/test`, `eslint@^9.38.0`, `prettier@^3.6.2` | 최소 80% 커버리지 |

## 3. 환경 변수 및 Firebase 초기화

1. `.env` 파일에 `PUBLIC_FIREBASE_*` 키를 정의한다. (예: `PUBLIC_FIREBASE_API_KEY`, `PUBLIC_FIREBASE_DATABASE_URL` 등)
2. `src/lib/firebase.ts`에서 `$env/static/public`을 통해 값을 읽어 `firebaseConfig` 객체를 만든다.
3. `browser` 플래그로 클라이언트 환경 여부를 판별한 후 `initializeApp` 및 `getAuth`, `getDatabase`, `getStorage`, `getAnalytics` 등을 호출한다.
4. SSR 대비를 위해 **모든 Firebase 서비스는 `browser ? … : null`** 형태로 nullable 타입으로 export한다.

## 4. 텍스트 플로차트 (SvelteKit 부팅 파이프라인)

```
[시작]
  |
  v
[npm install]
  - svelte / @sveltejs/kit / vite 설치
  - tailwindcss & shadcn-svelte 의존성 설치
  - firebase SDK, paraglide, vitest 등 설치
  |
  v
[환경 구성]
  - .env 작성 (PUBLIC_FIREBASE_* ...)
  - tailwind.config / postcss.config / app.css 설정
  - shadcn 컴포넌트 generate (Button, Card, Alert)
  |
  v
[npm run dev]
  -> Vite 개발 서버 부팅
  -> SvelteKit + HMR 로드
  |
  v
[SvelteKit 부트]
 1) `src/app.d.ts` 등 타입 정의 로드
 2) `src/app.css` 주입 (Tailwind base/components/utilities)
 3) 전역 `+layout.svelte` 실행
      - TopBar, LeftSidebar, RightSidebar를 import
      - `<TopBar />`에서 Firebase Auth store를 구독
 4) 전역 `+page.svelte` 또는 라우터가 매칭한 페이지 로드
      - 예: 홈 (`src/routes/+page.svelte`)
      - 관리자 페이지 진입 시 `src/routes/admin/+layout.svelte` → 상단 탭 → `admin/dashboard/+page.svelte`
  |
  v
[Firebase 초기화]
  - `src/lib/firebase.ts`가 최초 import될 때 환경 변수 기반으로 `initializeApp`
  - 브라우저 환경에서만 Auth/RTDB/Storage/Analytics 인스턴스 생성
  - `authStore` (`src/lib/stores/auth.svelte.ts`)가 `onAuthStateChanged`로 사용자 세션 감시
  |
  v
[UI 라이브러리 초기화]
  - shadcn 컴포넌트는 Svelte 컴파일 시 자동 포함
  - Tailwind 클래스는 `@tailwind base; @tailwind components; @tailwind utilities;`를 통해 빌드
  - Paraglide는 필요 시 `@inlang/paraglide-js`에서 `loadLocale`을 호출해 메시지를 로드
  |
  v
[페이지 렌더링]
  - Router가 현재 URL에 맞는 `+page.svelte`를 실행
  - 필요한 경우 `load` 함수에서 데이터를 Fetch (예: DatabaseListView)
  - 컴포넌트 내에서 Firebase/RTDB/Storage를 사용하여 실시간 데이터 표시
  |
  v
[검증/빌드]
  - `npm run check` (svelte-check + 타입 검사)
  - `npm run test` (vitest + playwright)
  - `npm run build` → `svelte-kit build` 로 최종 번들 생성
[끝]
```

## 5. 단계별 상세 설명

### 5.1 npm 설치
- `npm install` 실행 시 `package.json`의 dependencies/devDependencies를 설치한다.
- shadcn 컴포넌트를 추가하려면 `npx shadcn-svelte add button card alert` 등을 수행해 Svelte 컴포넌트 파일을 생성한다.

### 5.2 Tailwind + shadcn 초기화
- `app.css`에서 Tailwind 지시자를 선언하고 Light Mode 전용 스타일 규칙을 추가한다.
- shadcn 컴포넌트는 Tailwind 클래스 기반으로 동작하므로 `tailwind.config.js`에 `content` 경로에 `src/**/*.{svelte,ts}`를 포함시킨다.

### 5.3 `.env` 및 Firebase
- `.env` 예시:
  ```
  PUBLIC_FIREBASE_API_KEY="..."
  PUBLIC_FIREBASE_AUTH_DOMAIN="sonub.firebaseapp.com"
  PUBLIC_FIREBASE_DATABASE_URL="https://sonub.firebaseio.com"
  ```
- Vite는 `PUBLIC_` prefix가 있는 변수만 클라이언트에서 접근 가능하다.
- `src/lib/firebase.ts`는 이 값을 사용하여 Firebase 앱을 초기화하고 필요한 서비스 인스턴스를 export한다.

### 5.4 전역 레이아웃 로딩
- `src/routes/+layout.svelte`는 TopBar/LeftSidebar/RightSidebar를 포함한 3컬럼 레이아웃을 구성한다.
- Layout 단계에서 Paraglide/스토어 등을 초기화하며, 이후 child page가 `{@render children()}`로 렌더링된다.

### 5.5 관리자 상단 탭
- `/src/routes/admin/+layout.svelte`는 상단 탭으로 관리자 네비게이션을 제공한다 (`대시보드`, `테스트`, `사용자목록`, `신고 목록`).
- 탭은 `<a>` 기반이며 Light Mode 스타일 (`tab-link`, `active` 상태)로 디자인되어 있다.

### 5.6 Firebase 데이터 흐름
- `authStore` (`src/lib/stores/auth.svelte.ts`)가 `onAuthStateChanged`로 현재 유저 정보를 갱신하고 파생 상태(`isAuthenticated`, `isAdmin`)를 제공한다.
- DatabaseListView 등 실시간 기능은 `src/lib/firebase.ts`에서 export한 `rtdb` 인스턴스를 사용한다.
- Storage 업로드 페이지(`src/routes/upload/+page.svelte`)는 `storage` 인스턴스를 사용해 파일을 업로드하고 목록을 표시한다.

### 5.7 실행 및 테스트
- 개발 서버: `npm run dev`
- 정적 검사: `npm run check`
- 유닛 테스트: `npm run test:unit`
- E2E 테스트: `npm run test:e2e`
- 빌드: `npm run build` (SvelteKit + Vite)

## 6. 재사용 가이드

1. **새 프로젝트도 동일한 순서**로 진행한다: npm 설치 → Tailwind/shadcn init → `.env` 작성 → Firebase 초기화 → 레이아웃 구성.
2. 플로차트를 체크리스트로 활용하여 빠진 단계가 없는지 검증한다.
3. `src/lib/firebase.ts`, `src/routes/+layout.svelte`, `src/routes/+page.svelte` 구조를 복사하여 다른 프로젝트에 적용하면 동일한 부트 경험을 재현할 수 있다.

## 7. 검증 방법

- `npm run dev` 실행 후 브라우저에서 `http://localhost:5173` 진입 시 TopBar/사이드바가 정상 표시되어야 한다.
- 관리자 페이지 `http://localhost:5173/admin/dashboard` 접근 시 상단 탭이 표시되고, Firebase Auth 상태에 따라 컨텐츠가 갱신되어야 한다.
- `.env` 값을 수정하면 새로고침 시 Firebase 초기화가 해당 값으로 재구성되는지 확인한다.

## 8. 작업 이력 (SED Log)

| 날짜 | 작업자 | 내용 |
| ---- | ------ | ---- |
| 2025-11-09 | Codex Agent | SvelteKit 부트 시퀀스, Tailwind/shadcn 초기화, `.env` 로딩, Firebase 초기화 과정을 텍스트 플로차트로 정리하여 문서 최초 작성 |
