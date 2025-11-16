---
name: sonub
version: 1.0.0
description: Detailed table of contents for sonub specifications
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
step: 20
---

## 🔥 MUST-FOLLOW: SED (Spec-Exact Development) 지침

모든 개발자와 AI는 반드시 다음 SED 지침을 따라야 합니다:
📖 **필독**: https://sedai.dev/instructions 을 읽고 SED instructions 지침을 잘 이해하고 항상 이 지침대로 수행해 주세요.

### SED 핵심 원칙 (반드시 따를 것)

1. **인덱스 기반 개발**: 항상 `specs/index.md`부터 읽어 프로젝트 구조를 파악한 후 관련 사양 문서를 순차적으로 읽습니다.

2. **종속성 해결 (Dependencies First)**: YAML 헤더의 `dependencies` 필드를 분석하여 의존 사양을 먼저 로드하고, `step` 필드로 정의된 순서대로 개발을 진행합니다. (낮은 숫자 먼저 실행)

3. **사양 준수 (Spec is Absolute)**:
   - 사양에 명시된 것만 구현합니다
   - 명시되지 않은 기능은 절대 구현하지 않습니다
   - 모호한 부분은 해석하지 말고 개발자에게 명확히 요청합니다
   - 사양 해석 없음, 가정 없음

4. **기술 스택**:
   - **프레임워크**: 
     - Svelte `^5.41.0`
     - SvelteKit `^2.47.1`
     - Vite `^7.1.10`
   - **언어**: 
     - TypeScript `^5.9.3` (엄격 모드 필수)
   - **스타일링**: 
     - TailwindCSS `^4.1.14`
     - @tailwindcss/forms `^0.5.10`
     - @tailwindcss/typography `^0.5.19`
     - @tailwindcss/vite `^4.1.14`
   - **UI 컴포넌트**: 
     - shadcn-svelte `^1.0.10` (Button, Card, Alert 등)
     - clsx `^2.1.1`
     - tailwind-merge `^3.3.1`
   - **백엔드**: 
     - Firebase `^12.5.0` (Authentication, Firestore, Storage, Cloud Functions)
   - **다국어**: 
     - @inlang/paraglide-js `^2.4.0` (ko, ja, zh, en 지원)
   - **테스트**: 
     - Vitest `^4.0.5` (유닛 테스트)
     - Playwright `^1.56.1` (E2E 테스트)
   - **코드 품질**:
     - ESLint `^9.38.0`
     - Prettier `^3.6.2`
     - 최소 80% 코드 커버리지
   - **인코딩**: UTF-8 필수 (BOM 제외)
   - **콘텐츠 규칙**: 
     - 모든 HTML 콘텐츠는 영어로 작성 필수
     - 모든 한글 주석/문서는 UTF-8로 작성
   - **디자인 정책**:
     - Light Mode Only (다크 모드 미지원)
     - 모든 클릭 가능한 요소에 `cursor-pointer` 적용
   - **라우팅 규칙**:
     - **로그인한 사용자 자신의 정보**: `/my/xxx` 경로 사용
       - 예: `/my/profile` (내 프로필 수정), `/my/reports` (내 신고 목록)
       - 본인의 데이터를 조회하고 수정하는 모든 페이지에 적용
     - **다른 사용자 정보 조회**: `/user/xxx/${uid}` 경로 사용
       - 예: `/user/profile/${uid}` (다른 사용자 프로필 조회)
       - 읽기 전용 또는 제한된 권한으로 다른 사용자 정보를 조회

### UI/UX 개발 공통 규칙
- 모든 UI/UX 구현은 **Tailwind CSS 유틸리티 + svelte-shadcn** 컴포넌트 조합으로 작성한다. (출처: `CLAUDE.md`)
- 버튼, 다이얼로그, 카드 등 상호작용 요소는 `shadcn-svelte` 컴포넌트를 우선 사용하고, 커스텀 스타일은 Tailwind로 오버레이한다.
- 디자인 시스템 계층을 우회하는 CSS/HTML 구현은 금지되며, 새 컴포넌트가 필요하면 shadcn-svelte 생성기를 통해 추가한 뒤 재사용한다.

---

## ⭐️ Start Here – 필수 선행 문서

- **프로젝트 전반 개요는 반드시 [sonub-project-overview.md](./sonub-project-overview.md)를 먼저 읽습니다.**
  - Sonub의 목적, 핵심 기능 범위, 저작권/문의 채널 안내
  - 클라이언트·서버·shared 모듈 구조와 Pure Functions 공유 전략
  - 개발/배포/운영 지침과 shared 폴더 사용 패턴
- 해당 문서를 읽지 않으면 아래 세부 스펙의 컨텍스트를 이해할 수 없으므로, 모든 작업자는 `specs/index.md → sonub-project-overview.md → 관련 세부 문서` 순으로 학습합니다.

---

# Specifications Index
This document provides a detailed index of all specifications related to the sonub project. Each specification is listed with its title, description, and relevant metadata extracted from its YAML header.

## Foundation

### Sonub Project Overview
- **File**: [sonub-project-overview.md](./sonub-project-overview.md)
- **Title**: Sonub 프로젝트 개요
- **Description**: Sonub의 비전, 기능 범위, 모듈 구조(client/server/shared) 및 shared 순수 함수 사용 지침을 총괄적으로 정리한 문서
- **Version**: 1.0.0
- **Step**: 5
- **Priority**: "***"
- **Tags**: overview, architecture, shared, setup, guidance
- **핵심 내용**:
  - Social Network Hub(Sonub)의 목표와 제공 기능(프로필/친구/채팅/피드/알림)
  - SvelteKit + Firebase 기반 프로젝트 셋업 지침과 디렉터리 구조
  - shared 순수 함수 철학, 사용 예시(`shared/date.pure-functions.ts`, `shared/chat.pure-functions.ts`)
  - 개발·배포·운영·유지보수 가이드 및 피드백 경로(GitHub Issues)

## Design and Styling

### Sonub Design Workflow
- **File**: [sonub-design-workflow.md](./sonub-design-workflow.md)
- **Title**: Sonub Design Workflow
- **Description**: TailwindCSS와 shadcn-svelte를 사용한 디자인 워크플로우 가이드라인
- **Version**: 1.0.0
- **Step**: 10
- **Priority**: *
- **Dependencies**:
  - sonub-setup-tailwindcss.md
  - sonub-setup-shadcn.md
- **Tags**: design, tailwindcss, shadcn, ui, styling

### Sonub Design Guideline
- **File**: [sonub-design-guideline.md](./sonub-design-guideline.md)
- **Title**: Sonub Design Guideline
- **Description**: Light Mode 전용 테마와 모든 클릭 요소에 `cursor: pointer`를 강제하는 인터랙션 정책을 정의한 문서
- **Version**: 1.0.0
- **Step**: 15
- **Priority**: *
- **Dependencies**:
  - sonub-design-workflow.md
  - sonub-setup-tailwindcss.md
- **Tags**: design, ui, theme, interaction, cursor
- **핵심 정책**:
  - 시스템 설정과 상관없이 Light Mode만 지원 (`color-scheme: light`)
  - 다크 모드 토글/스타일 미구현, `dark:` 변형 사용 금지
  - 클릭 가능한 모든 요소에 `cursor-pointer` 적용, 비활성 상태 커서 명시
  - QA 체크리스트로 모드/커서 상태를 수동 검증

### Sonub Design Layout
- **File**: [sonub-design-layout.md](./sonub-design-layout.md)
- **Title**: Sonub Design Layout - 레이아웃, 탑바 및 사이드바 구조
- **Description**: Sonub 프로젝트의 레이아웃, 탑바 및 사이드바 구조 구현 명세서
- **Version**: 1.1.0
- **Step**: 20
- **Priority**: **
- **Dependencies**:
  - sonub-design-workflow.md
  - sonub-user-login.md
  - sonub-setup-shadcn.md
- **Tags**: layout, topbar, sidebar, navigation, ui, authentication, svelte5
- **Files**:
  - `src/routes/+layout.svelte` - 전역 레이아웃 (3컬럼 구조)
  - `src/lib/components/top-bar.svelte` - 탑바 컴포넌트
  - `src/lib/components/left-sidebar.svelte` - 좌측 사이드바 컴포넌트 (데스크톱만)
  - `src/lib/components/right-sidebar.svelte` - 우측 사이드바 컴포넌트 (데스크톱만)
  - `src/routes/+page.svelte` - 홈페이지
- **구현된 기능**:
  - 전역 레이아웃 구조 (3컬럼: 좌측/중앙/우측)
  - 반응형 탑바 (모바일/태블릿/데스크톱)
  - 반응형 사이드바 (데스크톱 lg 이상에서만 표시)
  - 사용자 인증 상태 기반 네비게이션
  - 로그인/로그아웃 기능
  - Sticky 포지셔닝 (사이드바 스크롤 고정)
  - Light Mode Only 정책 (다크 모드 미지원)
  - 접근성 고려

### Sonub Design Left Sidebar
- **File**: [sonub-design-leftsidebar.md](./sonub-design-leftsidebar.md)
- **Title**: Sonub 좌측 사이드바 명세서
- **Description**: 좌측 사이드바 컴포넌트 구현 명세서 - 메뉴, 언어 선택, 빌드 버전 표시
- **Version**: 1.0.0
- **Step**: 25
- **Priority**: **
- **Dependencies**:
  - sonub-design-layout.md
  - sonub-i18n-paraglide.md
  - sonub-setup-shadcn.md
- **Tags**: sidebar, navigation, i18n, ui, svelte5
- **Files**:
  - `src/lib/components/left-sidebar.svelte` - 좌측 사이드바 컴포넌트
- **구현된 기능**:
  - 메뉴 네비게이션 (홈, 소개, 제품, 연락)
  - 최근 활동 카드 (향후 확장 예정)
  - 언어 선택 드롭다운 (en, ko, ja, zh)
  - 빌드 버전 표시
  - Sticky 포지셔닝
  - Light Mode Only 스타일링
  - 반응형 디자인 (데스크톱만)

### Sonub Design Right Sidebar
- **File**: [sonub-design-rightsidebar.md](./sonub-design-rightsidebar.md)
- **Status**: ⚠️ 문서 내용이 아직 작성되지 않았습니다. 우측 사이드바 명세가 필요하면 개발자에게 요청하세요.

### Sonub Menu Page
- **File**: [sonub-menu-page.md](./sonub-menu-page.md)
- **Title**: Sonub Menu Page
- **Description**: 메뉴 페이지 구현 명세서 - 사용자 계정 및 설정 관리를 위한 메뉴 페이지 구현
- **Version**: 1.0.0
- **Step**: 23
- **Priority**: *
- **Dependencies**:
  - sonub-design-layout.md
  - sonub-setup-shadcn.md
  - sonub-user-login.md
- **Tags**: menu, ui, authentication, account, navigation, svelte5
- **Files**:
  - `src/routes/menu/+page.svelte` - 메뉴 페이지
  - `src/lib/components/top-bar.svelte` - 탑바 (메뉴 아이콘 추가)
- **구현된 기능**:
  - 탑바 우상단 메뉴 아이콘
  - 인증 상태 기반 메뉴 표시
  - 사용자 프로필 정보 표시 (사진, 이름, 이메일)
  - 회원 정보 수정 링크
  - 로그아웃 기능
  - 관리자 페이지 링크 (관리자만)
  - Light Mode Only 스타일링
  - 접근성 지원 (ARIA 라벨, 의미론적 HTML)

### Sonub Design Components
- **File**: [sonub-design-components.md](./sonub-design-components.md)
- **Title**: Sonub Design Components
- **Description**: Light Mode 전용 UI 컴포넌트(Button, Card, Alert)의 설계 지침과 사용 예시를 정리한 문서
- **Version**: 1.1.0
- **Step**: 35
- **Priority**: '**'
- **Dependencies**:
  - sonub-design-workflow.md
  - sonub-design-guideline.md
  - sonub-setup-shadcn.md
  - sonub-setup-tailwindcss.md
- **Tags**: ui-components, shadcn-svelte, tailwindcss, light-mode, svelte5
- **주요 내용**:
  - `src/lib/components/ui/button`, `card`, `alert` 구조 설명
  - Svelte 5 runes, `cn()` 헬퍼, Light Mode-only 정책을 반영한 구현 패턴
  - 버튼의 variant/size, disabled 링크 접근성, 아이콘 사이즈 자동화 규칙
  - Card/Alert 조합 예시 및 관리자 페이지 적용 사례

### Tailwind CSS Setup
- **File**: [sonub-setup-tailwindcss.md](./sonub-setup-tailwindcss.md)
- **Title**: SvelteKit 프로젝트 Tailwind CSS 설치 및 설정 명세서
- **Description**: Tailwind CSS 4.x를 SvelteKit 5에 설치하고 Light Mode 전용 정책에 맞춰 구성하는 절차
- **Version**: 1.2.0
- **Step**: 15
- **Priority**: **
- **Dependencies**:
  - sonub-setup-svelte.md
- **Tags**: tailwindcss, styling, css, light-mode, installation
- **핵심 내용**:
  - `npx sv create` 단계에서 Tailwind 애드온 선택 및 후속 수동 설치 절차
  - `@tailwindcss/vite` 플러그인, forms/typography 플러그인 설정과 Vite 통합
  - Light Mode 전용 설정( `color-scheme: light`, `dark:` 변형 금지 )과 Prettier 플러그인 구성
  - `npm run check` 기반 검증 및 문제 해결 가이드
- **관련 문서**: `sonub-design-tailwindcss.md` (사용 패턴), `sonub-design-workflow.md`

### Shadcn-Svelte Setup
- **File**: [sonub-setup-shadcn.md](./sonub-setup-shadcn.md)
- **Title**: SvelteKit 프로젝트 shadcn-svelte 설치 명세서
- **Description**: SvelteKit 프로젝트에 shadcn-svelte UI 컴포넌트 라이브러리 설치 및 설정 명세서
- **Version**: 1.1.0
- **Step**: 25
- **Priority**: *
- **Dependencies**:
  - sonub-setup-svelte.md
  - sonub-setup-tailwindcss.md
- **Tags**: shadcn-svelte, ui, components, 라이브러리, 설정, 수동구현
- **구현된 컴포넌트**:
  - Button 컴포넌트 (6 variants, 4 sizes)
  - Card 컴포넌트 (Header, Title, Description, Content, Footer)
  - Alert 컴포넌트 (default, destructive variants)
- **설치된 패키지**:
  - clsx@2.1.0
  - tailwind-merge@2.2.1

## Backend Services

### Firebase Setup
- **File**: [sonub-setup-firebase.md](./sonub-setup-firebase.md)
- **Title**: Firebase JS SDK 설치 및 설정 명세서
- **Description**: SvelteKit 프로젝트에 Firebase JS SDK 설치 및 설정 명세서
- **Version**: 1.1.0
- **Step**: 20
- **Priority**: **
- **Dependencies**:
  - sonub-setup-svelte.md
- **Tags**: firebase, backend, database, authentication, storage, 설정, SSR
- **Files**:
  - `src/lib/firebase.ts` - Firebase 초기화 및 서비스 인스턴스
  - `src/lib/types/firebase.ts` - Firebase 타입 정의
  - `.env` - 환경 변수 설정
- **구현된 서비스**:
  - Firebase Authentication (SSR 대응)
  - Firestore Database
  - Firestore
  - Firebase Storage
  - Firebase Analytics
- **주요 구현 사항**:
  - SvelteKit 환경 변수 사용 (`$env/static/public`)
  - SSR 대응 (nullable 타입, 브라우저 환경 체크)
  - 환경 변수 디버깅 로그
- **설치된 패키지**:
  - firebase@11.0.0 이상

### Firebase Authentication Example
- **File**: [sonub-firebase-auth.md](./sonub-firebase-auth.md)
- **Title**: Firebase Authentication Demo Spec
- **Description**: `sonub-setup-firebase.md`에서 분리된 Authentication 샘플 코드 명세. Google 로그인/로그아웃 흐름과 onAuthStateChanged 패턴을 정의
- **Version**: 1.0.0
- **Step**: 40
- **Priority**: *
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-user-login.md
- **Tags**: firebase, authentication, login, example
- **Files**:
  - `src/routes/demo/auth-example/+page.svelte` - Firebase Auth 데모 페이지
- **구현된 기능**:
  - GoogleAuthProvider 기반 로그인/로그아웃 버튼
  - onAuthStateChanged 구독 및 상태 메시지 처리
  - 프로필 이미지/UID 표시, 오류 상태 피드백
- **비고**: 실제 로그인 UI/UX 명세는 `sonub-user-login.md`를 따른다.

### Firebase Storage Upload Example
- **File**: [sonub-firebase-storage.md](./sonub-firebase-storage.md)
- **Title**: Firebase Storage 업로드/목록/삭제 예제
- **Description**: Storage 업로드 툴 전체 코드를 정의. 진행률, 취소, 목록, 삭제 플로우를 포함
- **Version**: 1.0.0
- **Step**: 45
- **Priority**: *
- **Dependencies**:
  - sonub-setup-firebase.md
- **Tags**: firebase, storage, upload, example
- **Files**:
  - `src/routes/upload/+page.svelte` - 파일 업로드 페이지
- **구현된 기능**:
  - UID 기반 경로에 파일 업로드 (uploadBytesResumable)
  - 업로드 취소/진행률 UI, 최근 업로드 URL 표시
  - listAll + getMetadata 조합으로 목록 정렬 및 삭제 기능
- **검증**:
  - 로그인 → 파일 업로드 → 목록/삭제 순으로 수동 테스트

### Firestore Database Structure
- **File**: [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md)
- **Title**: Firestore 데이터 구조 가이드
- **Description**: `users`, `chats`, `chat-room-passwords`, `system/stats` 등 Firestore 스키마와 책임 분리를 정의한 기준 문서
- **Version**: 2.0.0
- **Step**: (미정)
- **Priority**: (미정)
- **Dependencies**: 없음
- **Tags**: firebase, firestore, schema, architecture
- **주요 내용**:
  - 컬렉션/서브컬렉션 구조, Cloud Functions 책임, 시스템 컬렉션 설명
  - 사용자 파생 필드, 채팅방, 메시지, FCM 토큰, system/stats 등 정의
  - 관련 문서 링크, 검증 체크리스트

### Firestore Utilities
- **File**: [sonub-firebase-realtime-database.md](./sonub-firebase-realtime-database.md)
- **Title**: Firestore 유틸리티 라이브러리
- **Description**: Svelte 5 runes 기반 Firestore 스토어/CRUD/쿼리/배치 명세
- **Version**: 1.0.0
- **Step**: 30
- **Priority**: ***
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
- **Tags**: firebase, firestore, svelte, store, utility
- **Files**:
  - `src/lib/stores/firestore.svelte.ts`
- **제공 기능**:
  - `createFirestoreStore`, CRUD/쿼리/배치 헬퍼
  - TypeScript 제네릭 지원 및 Firebase Emulator 테스트 절차

### Firestore Store Specification
- **File**: [sonub-store-database.md](./sonub-store-database.md)
- **Title**: Firestore 스토어
- **Description**: Firestore 실시간 스토어/CRUD/배치 유틸리티
- **Version**: 2.0.0
- **Step**: 46
- **Priority**: ***
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
- **Tags**: firebase, firestore, svelte5, store, crud, utilities
- **Files**:
  - `src/lib/stores/firestore.svelte.ts`
- **핵심 기능**:
  - `createFirestoreStore<T>()` 실시간 문서 구독
  - `writeDocument`, `updateDocument`, `deleteDocument`, `addDocument`, `readDocument`
  - `buildQuery`, `runQuery`, `executeBatch`

### 🔥 FirestoreListView Component
- **File**: [sonub-firebase-database-list-view.md](./sonub-firebase-database-list-view.md)
- **Title**: FirestoreListView 컴포넌트 무한 스크롤 가이드
- **Description**: Firestore 데이터 목록/채팅 메시지 등에 사용하는 표준 컴포넌트
- **Version**: 3.0.0
- **Step**: 30
- **Priority**: *** (최우선)
- **Dependencies**:
  - sonub-firebase-database-structure.md
- **Tags**: firebase, firestore, infinite-scroll, list-view, svelte5
- **Files**:
  - `src/lib/components/FirestoreListView.svelte`
- **🔥 핵심 원칙 (반드시 준수)**:
  - Firestore 기반 목록은 모두 FirestoreListView를 사용한다.
  - 무한 스크롤/실시간 동기화/메모리 관리가 자동으로 처리됨
- **주요 기능**:
  - 양방향 무한 스크롤 (`scrollTrigger`: 'top' 또는 'bottom`)
  - 실시간 `onSnapshot` 기반 페이지네이션, `startAfter`/`endBefore` 커서 지원
  - 자동 메모리 관리 (리스너 자동 해제)
  - `whereFilters` + `orderByField` 조합으로 서버 필터링
  - 공개 메서드: `refresh()`, `scrollToTop()`, `scrollToBottom()`
  - snippet 시스템으로 빈/로딩/에러 상태 커스터마이징
- **사용 예시**:
  ```svelte
  <FirestoreListView
    path="users"
    pageSize={20}
    orderByField="createdAt"
    orderDirection="desc"
    scrollTrigger="bottom"
    pageSize={20}
  >
    {#snippet item(itemData)}
      <div>{itemData.data.displayName}</div>
    {/snippet}
  </DatabaseListView>
  ```
- **⚠️ 주의사항**:
  - orderBy 필드가 모든 노드에 존재해야 합니다
  - 컨테이너 스크롤 사용 시 명시적인 높이 설정 필요
  - pageSize는 10~30 권장

### Firebase Cloud Functions Guide
- **File**: [sonub-firebase-cloudfunctions.md](./sonub-firebase-cloudfunctions.md)
- **Title**: Firebase Cloud Functions 개발 가이드
- **Description**: Gen 2 Cloud Functions 설정, 트리거, 테스트 전략을 정리한 백엔드 자동화 명세
- **Version**: 1.0.0
- **Step**: (미정)
- **Priority**: (미정)
- **Dependencies**: 없음
- **Tags**: cloud-functions, backend, automation, firebase
- **주요 내용**:
  - 게시글/댓글/좋아요/신고/사용자 트리거 처리
  - `handleUserCreate`, `handleLikeCreate` 등 3레이어(handlers/utils) 구조
  - `setGlobalOptions`로 maxInstances 제한, asia-southeast1 리전 고정
  - firebase-functions-test 기반 단위/통합 테스트 가이드

### Firebase Security Rules
- **File**: [sonub-firebase-security.md](./sonub-firebase-security.md)
- **Title**: Firebase RTDB & Storage 보안 규칙
- **Description**: `/users/{uid}` 데이터와 Storage `users/{userId}/profile` 경로를 보호하는 샘플 규칙 정의
- **Version**: (미정)
- **Step**: (미정)
- **Priority**: (미정)
- **Dependencies**: 없음
- **Tags**: security, firebase, rules
- **주요 규칙**:
  - RTDB: 모든 사용자가 읽기 가능, 본인만 쓰기 가능, 필수 필드 검증
  - Storage: 프로필 폴더 read-all, write/delete는 본인만 허용
  - 규칙 스니펫을 그대로 Firebase 콘솔에 적용하여 최소 권한 원칙 충족

## Internationalization

### Paraglide Minimal Setup
- **File**: [sonub-setup-paraglide.md](./sonub-setup-paraglide.md)
- **Title**: Paraglide 최소 설정 가이드
- **Description**: Paraglide i18n을 필수 요소만으로 연결하기 위한 프로젝트/빌드/런타임 설정 절차
- **Version**: 1.0.0
- **Step**: 15
- **Priority**: **
- **Dependencies**:
  - sonub-setup-svelte.md
- **Tags**: i18n, paraglide, setup, configuration, localization
- **핵심 내용**:
  - `project.inlang/settings.json`에서 message-format 플러그인만 사용하는 최소 구성
  - `vite.config.ts` 내 단일 `paraglideVitePlugin` 호출과 `outputStructure: 'locale-modules'`
  - `src/hooks.server.ts`의 `paraglideMiddleware` 래핑과 `src/app.html`의 `%paraglide.lang%` 치환
  - 자동 생성 산출물 관리 및 수동 타입 파일 제거, 검증 체크리스트 포함
- **사용 가이드**: 번역 키 작성/사용 흐름은 `sonub-i18n-paraglide.md`를 참고

### Sonub i18n Paraglide
- **File**: [sonub-i18n-paraglide.md](./sonub-i18n-paraglide.md)
- **Title**: Paraglide-JS 기반 i18n 다국어 지원 시스템
- **Description**: Paraglide-JS와 Inlang 메시지를 사용해 ko/ja/zh/en 다국어 UI를 제공하는 방법을 정의
- **Version**: 1.0.0
- **Step**: 15
- **Priority**: **
- **Dependencies**:
  - sonub-setup-svelte.md
- **Tags**: i18n, paraglide-js, localization, inlang, sveltekit5
- **구현 요소**:
  - `messages/*.json` 원본과 `src/lib/paraglide` 자동 생성 파일 구조
  - 쿠키/스토어를 통한 로케일 감지 및 SSR 초기화
  - Paraglide 명령 실행, 타입 안전 메시지 사용 예시
  - 지원 언어: en (기본), ko, ja, zh

## Utility Functions

### Sonub Functions Overview
- **File**: [sonub-functions-overview.md](./sonub-functions-overview.md)
- **Title**: Sonub Pure Functions 운영 규칙
- **Description**: 순수 함수만을 `src/lib/functions/*.functions.ts`에 배치하기 위한 공통 규칙과 검증 체크리스트
- **Version**: 1.0.0
- **Step**: 25
- **Priority**: **
- **Dependencies**:
  - sonub-setup-svelte.md
- **Tags**: functions, architecture, guidelines
- **핵심 내용**:
  - 도메인별 `*.functions.ts` 네이밍, Named Export 강제, 외부 상태 의존 금지
  - 추가 함수 작성 시 문서화·테스트 지침과 검증 체크리스트 제공

### Chat Pure Functions
- **File**: [sonub-functions-chat-functions.md](./sonub-functions-chat-functions.md)
- **Title**: Chat Pure Functions 명세
- **Description**: `src/lib/functions/chat.functions.ts`에서 관리하는 1:1 채팅용 순수 함수 목록
- **Version**: 1.0.0
- **Step**: 30
- **Priority**: *
- **Dependencies**:
  - sonub-functions-overview.md
- **Tags**: chat, functions
- **주요 함수**:
  - `buildSingleRoomId(a, b)` → 두 UID를 정렬해 `single-{uidA}-{uidB}` 형식 roomId 생성
  - 사용처와 검증 포인트, 클릭 시 `/chat/room` 진입 전략 명시

### Date Functions
- **File**: [sonub-functions-date-functions.md](./sonub-functions-date-functions.md)
- **Title**: 날짜·시간 순수 함수 명세
- **Description**: `src/lib/functions/date.functions.ts` 기반 날짜/시간 포맷 함수와 Intl 사용 가이드
- **Version**: 1.2.0
- **Step**: 30
- **Priority**: *
- **Dependencies**:
  - sonub-functions-overview.md
- **Tags**: date, time, functions
- **주요 내용**:
  - `formatLongDate(timestamp)`/`formatShortDate(timestamp)` dual 포맷 전략
  - Intl.DateTimeFormat/RelativeTimeFormat 활용법, SSR·타임존 처리 팁

## User Authentication

### Sonub User Login
- **File**: [sonub-user-login.md](./sonub-user-login.md)
- **Title**: Sonub User Login - Google 및 Apple 소셜 로그인
- **Description**: Firebase를 사용한 Google 및 Apple 소셜 로그인 기능 구현 명세서
- **Version**: 1.1.0
- **Step**: 30
- **Priority**: **
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
- **Tags**: firebase, authentication, google-login, apple-login, oauth, svelte5
- **Files**:
  - `src/routes/user/login/+page.svelte` - 로그인 페이지
  - `src/lib/components/user-login.svelte` - 로그인 컴포넌트
  - `src/lib/stores/auth.svelte.ts` - 인증 상태 관리 스토어
  - `src/lib/utils/auth-helpers.ts` - 인증 헬퍼 함수
- **구현된 기능**:
  - Google OAuth 2.0 로그인
  - Apple Sign In 로그인
  - 다국어 지원 (ko, ja, zh, en)
  - 세션 관리 및 에러 핸들링
- **설치된 패키지**:
  - firebase@12.5.0
  - clsx@2.1.0
  - tailwind-merge@2.2.1

### Auth Store Specification
- **File**: [sonub-store-auth.md](./sonub-store-auth.md)
- **Title**: 인증 스토어 (AuthStore)
- **Description**: Firebase Authentication 상태 관리 스토어 - onAuthStateChanged 리스너, 프로필 동기화, 관리자 권한 관리
- **Version**: 1.0.0
- **Step**: 45
- **Priority**: ***
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-auth.md
  - sonub-firebase-realtime-database.md
  - sonub-firebase-database-structure.md
- **Tags**: firebase, authentication, auth, svelte5, store, state-management, rtdb, admin, profile-sync
- **Files**:
  - `src/lib/stores/auth.svelte.ts`
- **핵심 기능**:
  - `onAuthStateChanged` 리스너로 자동 세션 관리
  - `syncUserProfile()` - Firebase Auth → RTDB 프로필 자동 동기화
  - `loadAdminList()` - `/system/settings/admins` 경로에서 관리자 목록 로드
  - `isAdmin` getter - 관리자 권한 확인
  - AuthState 타입 정의 (user, loading, initialized, adminList)
  - 전체 소스 코드 및 사용 예제 포함

## User Management

### Sonub User Overview
- **File**: [sonub-user-overview.md](./sonub-user-overview.md)
- **Title**: 사용자 관리 체계 및 프로필 관리 명세서
- **Description**: Firebase Authentication과 Firestore를 활용한 사용자 관리 시스템 설계 및 구현 명세서
- **Version**: 1.0.0
- **Step**: 40
- **Priority**: **
- **Dependencies**:
  - sonub-user-login.md
  - sonub-setup-firebase.md
- **Tags**: user-management, profile, firebase, authentication
- **관련 세부 명세**:
  - 사용자 프로필 정보 구조
  - Firebase Storage 및 Firestore 저장소 설계
  - 실시간 프로필 업데이트 기능

### Sonub User Profile Store
- **File**: [sonub-store-user-profile.md](./sonub-store-user-profile.md)
- **Title**: 사용자 프로필 중앙 캐시 스토어
- **Description**: RTDB `/users/{uid}` 데이터를 단일 스토어로 관리해 Avatar·TopBar 등에서 일관된 프로필 정보를 제공하는 명세
- **Version**: 1.0.0
- **Step**: 44
- **Priority**: **
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-user-avatar.md
- **Tags**: firebase, rtdb, store, cache, svelte5
- **Files**:
  - `src/lib/stores/user-profile.svelte.ts`
- **핵심 기능**:
  - Map 기반 캐시와 단일 `onValue` 리스너로 중복 구독 제거 및 실시간 동기화
  - `getProfile`, `isLoading`, `getError` API와 Svelte 5 runes 반응성 패턴
  - Avatar/TopBar/RightSidebar/프로필 페이지와 연동된 photoUrl·displayName 공유
  - 구독 해제, 오류 처리, QA 체크리스트 및 향후 확장 아이디어

### Sonub User Profile Sync
- **File**: [sonub-user-profile-sync.md](./sonub-user-profile-sync.md)
- **Title**: Sonub User Profile Sync
- **Description**: Firebase Auth 로그인 직후 `/users/{uid}`에 displayName과 photoUrl을 자동 저장하는 동기화 플로우
- **Version**: 1.0.0
- **Step**: 15
- **Priority**: ***
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-user-login.md
  - sonub-user-props.md
- **Tags**: authentication, database, sync, firebase, rtdb
- **핵심 구현**:
  - `syncAuthProfileToRtdb()` 헬퍼 및 Google/Apple 로그인 연동
  - `photoURL` → `photoUrl` 변환, null-safe 저장
  - `onAuthStateChanged`/Cloud Functions와의 연계로 user-props 자동 갱신
  - 에러 로깅, QA 시나리오, Emulator 검증 절차

### Sonub User Public Profile
- **File**: [sonub-user-public-profile.md](./sonub-user-public-profile.md)
- **Status**: ⚠️ 문서가 비어 있습니다. 공개 프로필 레이아웃/데이터 명세가 필요하면 개발자에게 추가 지침을 요청하세요.

### Sonub My Profile
- **File**: [sonub-my-profile.md](./sonub-my-profile.md)
- **Title**: 사용자 프로필 수정 페이지
- **Description**: `/my/profile`에서 프로필 사진 업로드, 닉네임·성별·생년월일을 수정하는 UI/데이터 명세
- **Version**: 2.0.0
- **Step**: 50
- **Priority**: **
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-firebase-security.md
  - sonub-design-components.md
- **Tags**: user-profile, firebase-storage, profile-edit, svelte5
- **Files**:
  - `src/routes/my/profile/+page.svelte`
- **주요 기능**:
  - Firebase Storage 업로드/삭제, RTDB `/users/{uid}` 업데이트
  - 닉네임/성별/생년월일 검증 로직
  - Alert/Card/Button 기반 Light Mode UI
  - 로그인 사용자만 접근

### Sonub Friend Overview
- **File**: [sonub-friend-overview.md](./sonub-friend-overview.md)
- **Title**: 친구·팔로잉/팔로워 관계 및 홈 피드 연동 개요
- **Description**: 친구 관계를 `/user-following`·`/user-followers`로 관리하고, 팔로잉한 사용자의 게시물과 채팅 활동을 홈 피드/알림에 반영하는 전체 워크플로 명세
- **Version**: 1.0.0
- **Step**: 55
- **Priority**: **
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-firebase-database-structure.md
  - sonub-store-user-profile.md
- **Tags**: friends, following, follower, feed, social
- **핵심 내용**:
  - `/user-following/{uid}/{targetUid}`·`/user-followers/{uid}/{followerUid}` 구조와 Cloud Functions 동기화
  - 팔로우/언팔로우 버튼 UX, Chat 탭 "친구" 메뉴, 친구 찾기 CTA 명세
  - 팔로잉 사용자의 메시지·포스트를 `/user-feed/{uid}`에 fan-out하고 FCM/알림을 발송하는 절차
  - QA 체크리스트: 관계 데이터 일관성, 피드 노출, 알림 중복 방지

### Sonub User Props
- **File**: [sonub-user-props.md](./sonub-user-props.md)
- **Title**: 사용자 속성 분리 및 대량 조회 최적화 명세서
- **Description**: 사용자 데이터 최적화를 위한 속성 분리 전략, user-props 구조 설계, Cloud Functions 자동 동기화
- **Version**: 1.0.0
- **Step**: 50
- **Priority**: *
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
- **Tags**: user-props, database-optimization, firebase-realtime, performance
- **구현된 기능**:
  - user-props 분리 구조 설계
  - 대량 사용자 조회 최적화
  - displayName, photoUrl, createdAt, updatedAt, gender, birthYear 등 속성 분리
  - Cloud Functions를 통한 자동 동기화
  - 선택적 조회를 통한 데이터 전송량 최소화

## Admin Management

### Sonub Admin Dashboard
- **File**: [sonub-admin-dashboard.md](./sonub-admin-dashboard.md)
- **Title**: Sonub Admin Dashboard
- **Description**: 관리자 대시보드에서 사용자/게시글/댓글/신고/통계를 한 화면에서 관리하는 절차
- **Version**: 1.0.0
- **Step**: (미정)
- **Priority**: (미정)
- **Dependencies**: 없음
- **Tags**: admin, dashboard, moderation
- **주요 내용**:
  - `/admin` 경로 전용 관리자 레이아웃과 상단 메뉴 구조
  - 사용자·글·댓글·신고·통계 섹션별 요구사항
  - Firebase Cloud Functions에 관리자 UID를 등록해 접근 제어

### Sonub Admin Report Management
- **File**: [sonub-admin-report-management.md](./sonub-admin-report-management.md)
- **Title**: Sonub Admin Report Management - 신고 관리 기능
- **Description**: 관리자/사용자 신고 목록 페이지, 타입 정의, 서비스 API, i18n을 포함한 전체 신고 처리 명세
- **Version**: 1.0.0
- **Step**: 40
- **Priority**: **
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-user-login.md
  - sonub-design-workflow.md
  - sonub-setup-svelte.md
- **Tags**: admin, report, moderation, firebase, sveltekit5
- **핵심 요소**:
  - `/admin/reports` 와 `/my/reports` 페이지 구성, DatabaseListView 파라미터 정의
  - `ReportType`, `ReportReason`, `removeReport()` 등 타입/서비스 명세
  - 메뉴/라우팅, 다국어 메시지, 유닛·E2E 테스트 계획

### Sonub Admin Report
- **File**: [sonub-admin-report.md](./sonub-admin-report.md)
- **Title**: 신고 목록 표시 기능 (Admin & User Report List)
- **Description**: 관리자 신고 목록 페이지 및 사용자 신고 목록 페이지 구현 명세서 - 신고된 게시글/댓글을 관리하고 사용자가 자신의 신고를 추적할 수 있는 기능
- **Version**: 1.0.0
- **Step**: 60
- **Priority**: *
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
- **Tags**: admin, report, firebase, list-view, svelte5
- **구현 페이지**:
  - `/admin/reports` - 관리자 신고 목록 (모든 신고 조회)
  - `/my/reports` - 사용자 신고 목록 (자신의 신고만 조회)
- **핵심 기능**:
  - 신고 목록 조회 및 필터링
  - 신고된 게시글/댓글로 이동
  - 신고 취소 기능
  - 실시간 데이터 동기화
  - 신고 사유 다국어 지원

### Sonub Admin Test User Management
- **File**: [sonub-admin-test-create-user-accounts.md](./sonub-admin-test-create-user-accounts.md)
- **Title**: Sonub Admin Test User Management
- **Description**: `/admin/users`는 테스트 사용자 목록/삭제 전용, `/admin/test/create-test-data`는 테스트 사용자/테스트 데이터 생성 전용으로 분리된 명세
- **Version**: 2.1.0
- **Step**: 65
- **Priority**: **
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-design-layout.md
- **Tags**: admin, test-user, firebase, rtdb, svelte5
- **주요 기능**:
  - 상단 탭 공유 레이아웃 (`/admin/+layout.svelte`)
  - `/admin/test/create-test-data`에서 테스트 사용자 100명 생성 카드와 테스트 데이터 생성 카드 제공
  - `/admin/users`에서 목록 조회, 개별 및 일괄 삭제 UI 제공 및 빈 상태 링크를 통한 생성 페이지 안내

---

## Complete Source Code Specs

이 섹션에는 Sonub 프로젝트의 **모든 소스 코드**를 SED 스펙 형식으로 변환한 문서들이 포함되어 있습니다.
각 스펙 문서는 원본 소스 코드의 전체 내용을 포함하고 있어, **스펙만으로도 프로젝트를 완전히 재현**할 수 있습니다.

### 📊 통계

- **총 스펙 문서**: 459개
- **마지막 업데이트**: 2025-11-15
- **경로**: `./specs/repository/`
- **인코딩**: UTF-8 (BOM 제외)
- **언어**: 한국어 (주석 및 설명)

### 📁 디렉토리 구조

모든 스펙 문서는 `./specs/repository/` 디렉토리에 **원본 파일 구조를 그대로 유지**하여 저장되어 있습니다.

```
specs/repository/
├── src/                          # SvelteKit 소스 코드 (140+ 파일)
│   ├── routes/                   # 라우트 페이지 (30+ 파일)
│   ├── lib/
│   │   ├── components/           # UI 컴포넌트 (70+ 파일)
│   │   ├── stores/               # Svelte 스토어 (3개)
│   │   ├── functions/            # 순수 함수 (2개)
│   │   ├── utils/                # 유틸리티 (4개)
│   │   └── paraglide/            # 다국어 메시지 (200+ 파일)
│   ├── app.css                   # 메인 CSS
│   ├── app.html                  # HTML 템플릿
│   └── hooks.server.ts           # 서버 훅
├── firebase/                     # Firebase 설정 및 Functions (50+ 파일)
│   ├── functions/
│   │   ├── src/                  # Cloud Functions 소스
│   │   ├── test/                 # 테스트 파일
│   │   └── lib/                  # 빌드 산출물
│   └── firebase.json             # Firebase 설정
├── shared/                       # Shared 순수 함수 (2개)
├── messages/                     # 다국어 원본 메시지 (4개)
├── e2e/                          # E2E 테스트 (1개)
└── [설정 파일들]                 # 루트 설정 파일들 (10+ 개)
```

### 📚 주요 카테고리

#### 1. Svelte 컴포넌트
- **라우트 페이지**: `src/routes/**/*.svelte.md` (30+ 파일)
  - 홈, 로그인, 프로필, 채팅, 관리자 등
- **UI 컴포넌트**: `src/lib/components/ui/**/*.svelte.md` (45+ 파일)
  - Button, Card, Dialog, Dropdown Menu, Alert 등
- **레이아웃**: `src/lib/components/*.svelte.md` (3개)
  - Top Bar, Left Sidebar, Right Sidebar
- **기능 컴포넌트**: `src/lib/components/**/*.svelte.md` (10+ 파일)
  - DatabaseListView, ChatCreateDialog, UserSearchDialog 등

#### 2. Firebase Cloud Functions
- **핸들러**: `firebase/functions/src/handlers/*.ts.md` (2개)
- **유틸리티**: `firebase/functions/src/utils/*.ts.md` (5개)
- **타입 정의**: `firebase/functions/src/types/*.ts.md` (1개)
- **테스트**: `firebase/functions/test/**/*.ts.md` (6개)

#### 3. 설정 파일
- **루트 설정**: `package.json.md`, `tsconfig.json.md`, `vite.config.ts.md` 등 (7개)
- **Firebase 설정**: `firebase/*.json.md` (3개)
- **Functions 설정**: `firebase/functions/*.json.md` (4개)

#### 4. 다국어 및 i18n
- **원본 메시지**: `messages/*.json.md` (4개: ko, en, ja, zh)
- **생성된 메시지**: `src/lib/paraglide/messages/*.js.md` (200+ 파일)

#### 5. 순수 함수 및 유틸리티
- **Shared 함수**: `shared/*.ts.md` (2개)
- **라이브러리 함수**: `src/lib/functions/*.ts.md` (2개)
- **유틸리티**: `src/lib/utils/*.ts.md` (4개)

#### 6. 스토어
- **Svelte 스토어**: `src/lib/stores/*.svelte.ts.md` (3개)
  - auth.svelte.ts, database.svelte.ts, user-profile.svelte.ts

#### 7. 테스트
- **E2E 테스트**: `e2e/*.test.ts.md` (1개)
- **유닛 테스트**: `src/**/*.spec.ts.md` (2개)
- **Functions 테스트**: `firebase/functions/test/**/*.test.ts.md` (6개)

### 🔍 스펙 문서 찾기

#### 방법 1: 직접 경로 접근
원본 파일 경로에 `.md`를 추가하여 스펙 문서를 찾을 수 있습니다.

**예시:**
- 원본: `src/lib/components/ui/button/button.svelte`
- 스펙: `specs/repository/src/lib/components/ui/button/button.svelte.md`

#### 방법 2: 명령어로 검색
```bash
# 특정 파일 스펙 찾기
find specs/repository -name "button.svelte.md"

# 카테고리별 스펙 개수 확인
find specs/repository/src/routes -name "*.md" | wc -l

# 모든 스펙 파일 목록
find specs/repository -name "*.md" | sort
```

### 📖 스펙 문서 형식

모든 스펙 문서는 다음과 같은 SED 형식을 따릅니다:

```markdown
---
name: [파일명]
description: [파일의 목적과 역할]
version: 1.0.0
type: [svelte-component | firebase-function | configuration | css | etc]
category: [세부 카테고리]
original_path: [원본 파일 경로]
---

# [파일명]

## 개요
[파일의 목적과 주요 기능 설명]

## 소스 코드
```[언어]
[실제 소스 코드 전체 - 100% 원본 그대로]
```

## 주요 기능
(수동으로 업데이트 필요)

## 관련 파일
(수동으로 업데이트 필요)
```

### 💡 스펙 문서 활용 방법

#### 1. **AI 기반 바이브 코딩 (Vibe Coding)**
스펙 문서를 AI에게 제공하여 프로젝트를 자동으로 생성하거나 재현할 수 있습니다.

```bash
# 예: AI에게 전체 프로젝트 스펙 제공
cat specs/repository/src/**/*.md | ai-coder --output ./new-project
```

#### 2. **프로젝트 완전 재현**
스펙 문서의 소스 코드를 그대로 사용하여 프로젝트를 재현할 수 있습니다.

```python
# 스펙 문서에서 소스 코드를 추출하는 스크립트 예시
import re
from pathlib import Path

def extract_source_from_spec(spec_file):
    content = spec_file.read_text(encoding='utf-8')
    # ```언어 블록에서 소스 코드 추출
    matches = re.findall(r'```(\w+)\n(.*?)\n```', content, re.DOTALL)
    return matches[0][1] if matches else None
```

#### 3. **문서화 및 이해**
각 파일의 목적과 기능을 명확히 이해할 수 있습니다.

#### 4. **버전 관리**
스펙 문서를 통해 소스 코드의 변경 이력을 추적할 수 있습니다.

### 🔄 스펙 업데이트

소스 코드가 변경되면 다음 명령어로 스펙을 업데이트할 수 있습니다:

```bash
# 스펙 재생성 스크립트 실행
python3 tmp/generate-specs.py
```

이 스크립트는:
- 기존 `specs/repository/` 폴더를 삭제하지 않음
- 모든 소스 파일을 다시 스캔
- 각 파일의 스펙 문서를 재생성
- UTF-8 인코딩으로 저장

### ⚠️ 주의사항

1. **인코딩**: 모든 스펙 문서는 UTF-8 (BOM 제외) 인코딩을 사용합니다.
2. **언어**: 모든 설명과 주석은 한국어로 작성되어 있습니다.
3. **완전성**: 각 스펙 문서는 원본 소스 코드의 전체 내용을 포함합니다.
4. **자동 생성**: 스펙 문서는 자동으로 생성되므로, 수동으로 편집하지 마세요. 대신 원본 소스 코드를 수정하고 스펙을 재생성하세요.

### 🎯 사용 예시

#### 예시 1: 특정 컴포넌트 스펙 확인
```bash
# Button 컴포넌트 스펙 보기
cat specs/repository/src/lib/components/ui/button/button.svelte.md
```

#### 예시 2: 모든 라우트 페이지 스펙 찾기
```bash
# 모든 라우트 페이지 스펙 목록
find specs/repository/src/routes -name "+page.svelte.md"
```

#### 예시 3: Firebase Functions 스펙 확인
```bash
# 모든 Cloud Functions 스펙 목록
find specs/repository/firebase/functions/src -name "*.ts.md"
```

### 📋 전체 파일 목록

다음은 `./specs/repository/` 폴더에 생성된 **모든 459개 스펙 파일**의 전체 목록입니다.
각 파일은 원본 소스 코드의 전체 내용을 포함하고 있습니다.

#### 1. 루트 설정 파일 (7개)

- [components.json](./repository/components.json.md) - shadcn-svelte 컴포넌트 설정
- [eslint.config.js](./repository/eslint.config.js.md) - ESLint 설정
- [package.json](./repository/package.json.md) - 프로젝트 종속성 및 스크립트
- [playwright.config.ts](./repository/playwright.config.ts.md) - Playwright E2E 테스트 설정
- [svelte.config.js](./repository/svelte.config.js.md) - SvelteKit 설정
- [tsconfig.json](./repository/tsconfig.json.md) - TypeScript 컴파일러 설정
- [vite.config.ts](./repository/vite.config.ts.md) - Vite 빌드 도구 설정

#### 2. E2E 테스트 (1개)

- [e2e/demo.test.ts](./repository/e2e/demo.test.ts.md) - Playwright E2E 데모 테스트

#### 3. Firebase Admin (2개)

- [firebase/admin/package.json](./repository/firebase/admin/package.json.md) - Admin SDK 패키지 설정
- [firebase/admin/send-a-message.ts](./repository/firebase/admin/send-a-message.ts.md) - FCM 푸시 알림 전송 스크립트

#### 4. Firebase 설정 (3개)

- [firebase/cors.json](./repository/firebase/cors.json.md) - Firebase Storage CORS 설정
- (RTDB 규칙 문서는 사용하지 않으며, Firestore 규칙은 `firebase/firestore.rules`를 참조합니다.)
- [firebase/firebase.json](./repository/firebase/firebase.json.md) - Firebase 프로젝트 설정

#### 5. Firebase Cloud Functions (34개)

**핸들러 (4개)**
- [firebase/functions/src/handlers/chat.handler.ts](./repository/firebase/functions/src/handlers/chat.handler.ts.md) - 채팅 메시지 생성 핸들러
- [firebase/functions/src/handlers/chat.new-message-count.handler.ts](./repository/firebase/functions/src/handlers/chat.new-message-count.handler.ts.md) - 새 메시지 개수 집계 핸들러
- [firebase/functions/src/handlers/chat.password-verification.handler.ts](./repository/firebase/functions/src/handlers/chat.password-verification.handler.ts.md) - 채팅방 비밀번호 검증 핸들러
- [firebase/functions/src/handlers/user.handler.ts](./repository/firebase/functions/src/handlers/user.handler.ts.md) - 사용자 생성/수정 핸들러

**유틸리티 (6개)**
- [firebase/functions/src/utils/comment.utils.ts](./repository/firebase/functions/src/utils/comment.utils.ts.md) - 댓글 유틸리티
- [firebase/functions/src/utils/fcm.utils.ts](./repository/firebase/functions/src/utils/fcm.utils.ts.md) - FCM 푸시 알림 유틸리티
- [firebase/functions/src/utils/like.utils.ts](./repository/firebase/functions/src/utils/like.utils.ts.md) - 좋아요 유틸리티
- [firebase/functions/src/utils/post.utils.ts](./repository/firebase/functions/src/utils/post.utils.ts.md) - 게시글 유틸리티
- [firebase/functions/src/utils/report.utils.ts](./repository/firebase/functions/src/utils/report.utils.ts.md) - 신고 유틸리티

**테스트 (9개)**
- [firebase/functions/test/integration/onLike.test.ts](./repository/firebase/functions/test/integration/onLike.test.ts.md) - 좋아요 통합 테스트
- [firebase/functions/test/integration/onPostCreate.test.ts](./repository/firebase/functions/test/integration/onPostCreate.test.ts.md) - 게시글 생성 통합 테스트
- [firebase/functions/test/integration/test-setup.ts](./repository/firebase/functions/test/integration/test-setup.ts.md) - 테스트 환경 설정
- [firebase/functions/test/unit/comment.handler.test.ts](./repository/firebase/functions/test/unit/comment.handler.test.ts.md) - 댓글 핸들러 단위 테스트
- [firebase/functions/test/unit/like.utils.test.ts](./repository/firebase/functions/test/unit/like.utils.test.ts.md) - 좋아요 유틸리티 단위 테스트
- [firebase/functions/test/unit/user.handler.test.ts](./repository/firebase/functions/test/unit/user.handler.test.ts.md) - 사용자 핸들러 단위 테스트

**설정 및 기타 (6개)**
- [firebase/functions/package.json](./repository/firebase/functions/package.json.md) - Cloud Functions 패키지 설정
- [firebase/functions/scripts/generate-sample-posts.ts](./repository/firebase/functions/scripts/generate-sample-posts.ts.md) - 샘플 게시글 생성 스크립트
- [firebase/functions/src/i18n.ts](./repository/firebase/functions/src/i18n.ts.md) - Cloud Functions i18n 설정
- [firebase/functions/src/index.ts](./repository/firebase/functions/src/index.ts.md) - Cloud Functions 메인 인덱스
- [firebase/functions/src/types/index.ts](./repository/firebase/functions/src/types/index.ts.md) - Cloud Functions 타입 정의
- [firebase/functions/tsconfig.dev.json](./repository/firebase/functions/tsconfig.dev.json.md) - 개발 TypeScript 설정
- [firebase/functions/tsconfig.json](./repository/firebase/functions/tsconfig.json.md) - Cloud Functions TypeScript 설정

#### 6. 다국어 메시지 (4개)

- [messages/en.json](./repository/messages/en.json.md) - 영어 메시지
- [messages/ja.json](./repository/messages/ja.json.md) - 일본어 메시지
- [messages/ko.json](./repository/messages/ko.json.md) - 한국어 메시지
- [messages/zh.json](./repository/messages/zh.json.md) - 중국어 메시지

#### 7. Shared 순수 함수 (2개)

- [shared/chat.pure-functions.ts](./repository/shared/chat.pure-functions.ts.md) - 채팅 순수 함수 (클라이언트/서버 공유)
- [shared/date.pure-functions.ts](./repository/shared/date.pure-functions.ts.md) - 날짜 순수 함수 (클라이언트/서버 공유)

#### 8. Source - App 코어 (6개)

- [src/app.css](./repository/src/app.css.md) - 전역 CSS 스타일
- [src/app.d.ts](./repository/src/app.d.ts.md) - SvelteKit 앱 타입 정의
- [src/app.html](./repository/src/app.html.md) - HTML 템플릿
- [src/demo.spec.ts](./repository/src/demo.spec.ts.md) - 데모 단위 테스트
- [src/hooks.server.ts](./repository/src/hooks.server.ts.md) - 서버 훅 (paraglide 미들웨어)

#### 9. Source - Components (약 110개)

**레이아웃 컴포넌트 (4개)**
- [src/lib/components/DatabaseListView.svelte](./repository/src/lib/components/DatabaseListView.svelte.md) - RTDB 무한 스크롤 리스트뷰 (모든 목록 표시에 사용)
- [src/lib/components/FcmPermissionGate.svelte](./repository/src/lib/components/FcmPermissionGate.svelte.md) - FCM 권한 게이트 컴포넌트
- [src/lib/components/left-sidebar.svelte](./repository/src/lib/components/left-sidebar.svelte.md) - 좌측 사이드바 (메뉴, 언어 선택)
- [src/lib/components/right-sidebar.svelte](./repository/src/lib/components/right-sidebar.svelte.md) - 우측 사이드바
- [src/lib/components/top-bar.svelte](./repository/src/lib/components/top-bar.svelte.md) - 탑바 (로고, 네비게이션, 로그인)

**관리자 컴포넌트 (1개)**
- [src/lib/components/admin-menu.svelte](./repository/src/lib/components/admin-menu.svelte.md) - 관리자 메뉴

**채팅 컴포넌트 (8개)**
- [src/lib/components/chat/ChatCreateDialog.svelte](./repository/src/lib/components/chat/ChatCreateDialog.svelte.md) - 채팅방 생성 다이얼로그
- [src/lib/components/chat/ChatFavoritesDialog.svelte](./repository/src/lib/components/chat/ChatFavoritesDialog.svelte.md) - 즐겨찾기 채팅방 다이얼로그
- [src/lib/components/chat/ChatInvitationList.svelte](./repository/src/lib/components/chat/ChatInvitationList.svelte.md) - 채팅 초대 목록
- [src/lib/components/chat/ChatListItem.svelte](./repository/src/lib/components/chat/ChatListItem.svelte.md) - 채팅 목록 아이템
- [src/lib/components/chat/ChatListMenu.svelte](./repository/src/lib/components/chat/ChatListMenu.svelte.md) - 채팅 목록 메뉴
- [src/lib/components/chat/room-password-prompt.svelte](./repository/src/lib/components/chat/room-password-prompt.svelte.md) - 채팅방 비밀번호 입력 프롬프트
- [src/lib/components/chat/room-password-setting.svelte](./repository/src/lib/components/chat/room-password-setting.svelte.md) - 채팅방 비밀번호 설정

**개발자 컴포넌트 (1개)**
- [src/lib/components/dev/dev-icon.svelte](./repository/src/lib/components/dev/dev-icon.svelte.md) - 개발자 아이콘

**사용자 컴포넌트 (4개)**
- [src/lib/components/under-construction.svelte](./repository/src/lib/components/under-construction.svelte.md) - 공사중 페이지
- [src/lib/components/user-login.svelte](./repository/src/lib/components/user-login.svelte.md) - 로그인 컴포넌트
- [src/lib/components/user/UserSearchDialog.svelte](./repository/src/lib/components/user/UserSearchDialog.svelte.md) - 사용자 검색 다이얼로그
- [src/lib/components/user/avatar.svelte](./repository/src/lib/components/user/avatar.svelte.md) - 사용자 아바타

**UI 컴포넌트 (45개 - shadcn-svelte 기반)**

Alert 컴포넌트 (4개):
- [src/lib/components/ui/alert/alert-description.svelte](./repository/src/lib/components/ui/alert/alert-description.svelte.md)
- [src/lib/components/ui/alert/alert-title.svelte](./repository/src/lib/components/ui/alert/alert-title.svelte.md)
- [src/lib/components/ui/alert/alert.svelte](./repository/src/lib/components/ui/alert/alert.svelte.md)
- [src/lib/components/ui/alert/index.ts](./repository/src/lib/components/ui/alert/index.ts.md)

Button 컴포넌트 (2개):
- [src/lib/components/ui/button/button.svelte](./repository/src/lib/components/ui/button/button.svelte.md)
- [src/lib/components/ui/button/index.ts](./repository/src/lib/components/ui/button/index.ts.md)

Card 컴포넌트 (7개):
- [src/lib/components/ui/card/card-content.svelte](./repository/src/lib/components/ui/card/card-content.svelte.md)
- [src/lib/components/ui/card/card-description.svelte](./repository/src/lib/components/ui/card/card-description.svelte.md)
- [src/lib/components/ui/card/card-footer.svelte](./repository/src/lib/components/ui/card/card-footer.svelte.md)
- [src/lib/components/ui/card/card-header.svelte](./repository/src/lib/components/ui/card/card-header.svelte.md)
- [src/lib/components/ui/card/card-title.svelte](./repository/src/lib/components/ui/card/card-title.svelte.md)
- [src/lib/components/ui/card/card.svelte](./repository/src/lib/components/ui/card/card.svelte.md)
- [src/lib/components/ui/card/index.ts](./repository/src/lib/components/ui/card/index.ts.md)

Dialog 컴포넌트 (8개):
- [src/lib/components/ui/dialog/context.ts](./repository/src/lib/components/ui/dialog/context.ts.md)
- [src/lib/components/ui/dialog/dialog-content.svelte](./repository/src/lib/components/ui/dialog/dialog-content.svelte.md)
- [src/lib/components/ui/dialog/dialog-description.svelte](./repository/src/lib/components/ui/dialog/dialog-description.svelte.md)
- [src/lib/components/ui/dialog/dialog-footer.svelte](./repository/src/lib/components/ui/dialog/dialog-footer.svelte.md)
- [src/lib/components/ui/dialog/dialog-header.svelte](./repository/src/lib/components/ui/dialog/dialog-header.svelte.md)
- [src/lib/components/ui/dialog/dialog-title.svelte](./repository/src/lib/components/ui/dialog/dialog-title.svelte.md)
- [src/lib/components/ui/dialog/dialog.svelte](./repository/src/lib/components/ui/dialog/dialog.svelte.md)
- [src/lib/components/ui/dialog/index.ts](./repository/src/lib/components/ui/dialog/index.ts.md)

Dropdown Menu 컴포넌트 (15개):
- [src/lib/components/ui/dropdown-menu/dropdown-menu-checkbox-group.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-checkbox-group.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-checkbox-item.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-checkbox-item.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-content.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-content.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-group-heading.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-group-heading.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-group.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-group.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-item.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-item.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-label.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-label.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-radio-group.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-radio-group.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-radio-item.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-radio-item.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-separator.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-separator.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-shortcut.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-shortcut.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-sub-content.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-sub-content.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-sub-trigger.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-sub-trigger.svelte.md)
- [src/lib/components/ui/dropdown-menu/dropdown-menu-trigger.svelte](./repository/src/lib/components/ui/dropdown-menu/dropdown-menu-trigger.svelte.md)
- [src/lib/components/ui/dropdown-menu/index.ts](./repository/src/lib/components/ui/dropdown-menu/index.ts.md)

Input 컴포넌트 (2개):
- [src/lib/components/ui/input/index.ts](./repository/src/lib/components/ui/input/index.ts.md)
- [src/lib/components/ui/input/input.svelte](./repository/src/lib/components/ui/input/input.svelte.md)

Label 컴포넌트 (2개):
- [src/lib/components/ui/label/index.ts](./repository/src/lib/components/ui/label/index.ts.md)
- [src/lib/components/ui/label/label.svelte](./repository/src/lib/components/ui/label/label.svelte.md)

Switch 컴포넌트 (2개):
- [src/lib/components/ui/switch/index.ts](./repository/src/lib/components/ui/switch/index.ts.md)
- [src/lib/components/ui/switch/switch.svelte](./repository/src/lib/components/ui/switch/switch.svelte.md)

#### 10. Source - Functions (3개)

- [src/lib/fcm.ts](./repository/src/lib/fcm.ts.md) - FCM (Firebase Cloud Messaging) 클라이언트 초기화 및 토큰 관리
- [src/lib/firebase.ts](./repository/src/lib/firebase.ts.md) - Firebase 초기화 및 서비스 인스턴스
- [src/lib/functions/chat.functions.ts](./repository/src/lib/functions/chat.functions.ts.md) - 채팅 순수 함수
- [src/lib/functions/date.functions.ts](./repository/src/lib/functions/date.functions.ts.md) - 날짜 순수 함수
- [src/lib/functions/storage.functions.ts](./repository/src/lib/functions/storage.functions.ts.md) - Firebase Storage 업로드 함수
- [src/lib/index.ts](./repository/src/lib/index.ts.md) - 라이브러리 메인 인덱스
- [src/lib/utils.ts](./repository/src/lib/utils.ts.md) - 공통 유틸리티 (cn 함수 등)

#### 11. Source - Paraglide (자동 생성된 다국어 메시지 파일, 약 250개)

**메인 파일 (6개)**
- [src/lib/paraglide/messages.js](./repository/src/lib/paraglide/messages.js.md) - 메시지 메인 인덱스
- [src/lib/paraglide/messages/_index.js](./repository/src/lib/paraglide/messages/_index.js.md) - 메시지 인덱스
- [src/lib/paraglide/messages/en.js](./repository/src/lib/paraglide/messages/en.js.md) - 영어 메시지
- [src/lib/paraglide/messages/ja.js](./repository/src/lib/paraglide/messages/ja.js.md) - 일본어 메시지
- [src/lib/paraglide/messages/ko.js](./repository/src/lib/paraglide/messages/ko.js.md) - 한국어 메시지
- [src/lib/paraglide/messages/zh.js](./repository/src/lib/paraglide/messages/zh.js.md) - 중국어 메시지
- [src/lib/paraglide/registry.js](./repository/src/lib/paraglide/registry.js.md) - Paraglide 레지스트리
- [src/lib/paraglide/runtime.js](./repository/src/lib/paraglide/runtime.js.md) - Paraglide 런타임
- [src/lib/paraglide/server.js](./repository/src/lib/paraglide/server.js.md) - Paraglide 서버 유틸리티

**개별 메시지 파일 (약 240개 - src/paraglide/messages/)**
- 모든 다국어 메시지 키별 자동 생성 파일 (예: `about.js`, `helloworld1.js`, `authsignin2.js` 등)
- 전체 목록은 `find specs/repository/src/paraglide/messages -name "*.js.md"` 명령으로 확인 가능

#### 12. Source - Routes (31개)

**메인 레이아웃 (2개)**
- [src/routes/+layout.svelte](./repository/src/routes/+layout.svelte.md) - 전역 레이아웃 (3컬럼 구조)
- [src/routes/+page.svelte](./repository/src/routes/+page.svelte.md) - 홈페이지

**관리자 페이지 (6개)**
- [src/routes/admin/+layout.svelte](./repository/src/routes/admin/+layout.svelte.md) - 관리자 레이아웃
- [src/routes/admin/dashboard/+page.svelte](./repository/src/routes/admin/dashboard/+page.svelte.md) - 관리자 대시보드
- [src/routes/admin/reports/+page.svelte](./repository/src/routes/admin/reports/+page.svelte.md) - 신고 관리 페이지
- [src/routes/admin/test/+page.svelte](./repository/src/routes/admin/test/+page.svelte.md) - 테스트 관리 페이지
- [src/routes/admin/test/create-test-data/+page.svelte](./repository/src/routes/admin/test/create-test-data/+page.svelte.md) - 테스트 데이터 생성 페이지
- [src/routes/admin/users/+page.svelte](./repository/src/routes/admin/users/+page.svelte.md) - 사용자 관리 페이지

**채팅 페이지 (5개)**
- [src/routes/chat/group-chat-list/+page.svelte](./repository/src/routes/chat/group-chat-list/+page.svelte.md) - 그룹 채팅 목록
- [src/routes/chat/list/+page.svelte](./repository/src/routes/chat/list/+page.svelte.md) - 전체 채팅 목록
- [src/routes/chat/open-chat-list/+page.svelte](./repository/src/routes/chat/open-chat-list/+page.svelte.md) - 오픈 채팅 목록
- [src/routes/chat/room/+layout.svelte](./repository/src/routes/chat/room/+layout.svelte.md) - 채팅방 레이아웃
- [src/routes/chat/room/+page.svelte](./repository/src/routes/chat/room/+page.svelte.md) - 채팅방 페이지
- [src/routes/chat/room/ChatRoomListItem.svelte](./repository/src/routes/chat/room/ChatRoomListItem.svelte.md) - 채팅방 메시지 아이템

**데모 페이지 (2개)**
- [src/routes/demo/+page.svelte](./repository/src/routes/demo/+page.svelte.md) - 데모 페이지
- [src/routes/demo/paraglide/+page.svelte](./repository/src/routes/demo/paraglide/+page.svelte.md) - Paraglide 데모 페이지

**개발자 페이지 (2개)**
- [src/routes/dev/plan/+page.svelte](./repository/src/routes/dev/plan/+page.svelte.md) - 개발 계획 페이지
- [src/routes/dev/test/database-list-view/+page.svelte](./repository/src/routes/dev/test/database-list-view/+page.svelte.md) - DatabaseListView 테스트 페이지

**FCM 테스트 (1개)**
- [src/routes/fcm-test/+page.svelte](./repository/src/routes/fcm-test/+page.svelte.md) - FCM 푸시 알림 테스트 페이지

**메뉴 페이지 (1개)**
- [src/routes/menu/+page.svelte](./repository/src/routes/menu/+page.svelte.md) - 메뉴 페이지

**마이 페이지 (3개)**
- [src/routes/my/+layout.svelte](./repository/src/routes/my/+layout.svelte.md) - 마이 레이아웃
- [src/routes/my/profile/+page.svelte](./repository/src/routes/my/profile/+page.svelte.md) - 내 프로필 수정 페이지
- [src/routes/my/reports/+page.svelte](./repository/src/routes/my/reports/+page.svelte.md) - 내 신고 목록 페이지

**게시글 페이지 (1개)**
- [src/routes/post/list/+page.svelte](./repository/src/routes/post/list/+page.svelte.md) - 게시글 목록

**설정 페이지 (1개)**
- [src/routes/settings/fcm/permission/+page.svelte](./repository/src/routes/settings/fcm/permission/+page.svelte.md) - FCM 권한 설정 페이지

**통계 페이지 (1개)**
- [src/routes/stats/+page.svelte](./repository/src/routes/stats/+page.svelte.md) - 통계 페이지

**사용자 페이지 (3개)**
- [src/routes/user/list/+page.svelte](./repository/src/routes/user/list/+page.svelte.md) - 사용자 목록
- [src/routes/user/login/+page.svelte](./repository/src/routes/user/login/+page.svelte.md) - 로그인 페이지
- [src/routes/user/profile/+page.svelte](./repository/src/routes/user/profile/+page.svelte.md) - 사용자 프로필 페이지

**테스트 (1개)**
- [src/routes/page.svelte.spec.ts](./repository/src/routes/page.svelte.spec.ts.md) - 페이지 단위 테스트

#### 13. Source - Stores (3개)

- [src/lib/stores/auth.svelte.ts](./repository/src/lib/stores/auth.svelte.ts.md) - 인증 상태 관리 스토어
- [src/lib/stores/database.svelte.ts](./repository/src/lib/stores/database.svelte.ts.md) - Firebase RTDB 유틸리티 스토어
- [src/lib/stores/user-profile.svelte.ts](./repository/src/lib/stores/user-profile.svelte.ts.md) - 사용자 프로필 중앙 캐시 스토어

#### 14. Source - Utils (4개)

- [src/lib/utils/admin-service.ts](./repository/src/lib/utils/admin-service.ts.md) - 관리자 서비스
- [src/lib/utils/auth-helpers.ts](./repository/src/lib/utils/auth-helpers.ts.md) - 인증 헬퍼 함수
- [src/lib/utils/test-user-generator.ts](./repository/src/lib/utils/test-user-generator.ts.md) - 테스트 사용자 생성 유틸리티
- [src/lib/version.ts](./repository/src/lib/version.ts.md) - 빌드 버전 정보

#### 15. Source - Types (1개)

- [src/lib/types/chat.types.ts](./repository/src/lib/types/chat.types.ts.md) - 채팅 타입 정의

#### 16. Static 파일 (1개)

- [static/firebase-messaging-sw.js](./repository/static/firebase-messaging-sw.js.md) - Firebase Cloud Messaging 서비스 워커

#### 17. Storybook (6개)

- [src/stories/Button.stories.svelte](./repository/src/stories/Button.stories.svelte.md) - Button 스토리
- [src/stories/Button.svelte](./repository/src/stories/Button.svelte.md) - Button 컴포넌트
- [src/stories/Header.stories.svelte](./repository/src/stories/Header.stories.svelte.md) - Header 스토리
- [src/stories/Header.svelte](./repository/src/stories/Header.svelte.md) - Header 컴포넌트
- [src/stories/Page.stories.svelte](./repository/src/stories/Page.stories.svelte.md) - Page 스토리
- [src/stories/Page.svelte](./repository/src/stories/Page.svelte.md) - Page 컴포넌트
- [src/stories/button.css](./repository/src/stories/button.css.md) - Button CSS
- [src/stories/header.css](./repository/src/stories/header.css.md) - Header CSS
- [src/stories/page.css](./repository/src/stories/page.css.md) - Page CSS

### 💡 파일 목록 활용 팁

1. **특정 파일 빠르게 찾기**: Ctrl+F 또는 Cmd+F로 파일명 검색
2. **카테고리별 탐색**: 위 목록의 카테고리를 참고하여 관련 파일 그룹 확인
3. **링크 클릭**: 각 파일명을 클릭하면 해당 스펙 문서로 이동
4. **AI 활용**: 전체 목록을 AI에게 제공하여 프로젝트 구조 이해 및 자동 생성 지원

---

## Firebase Functions

### Firebase Cloud Functions Triggers
- **File**: [sonub-firebase-functions-index.md](./sonub-firebase-functions-index.md)
- **Title**: Firebase Cloud Functions Triggers
- **Description**: Gen 2 Functions에서 `/users/{uid}` 및 `/chat-messages/{messageId}` 트리거를 정의하고 전역 옵션을 설정한 인덱스 명세
- **Version**: 1.0.0
- **Step**: (미정)
- **Priority**: *
- **Dependencies**:
  - sonub-firebase-cloudfunctions.md
- **Tags**: firebase, cloud-functions, triggers, backend
- **주요 내용**:
  - `setGlobalOptions({ region: 'asia-southeast1', maxInstances: 10 })` 설정
  - `onUserCreate`, `onUserUpdate`, `onChatMessageCreate` 트리거 정의와 처리 목적

### Firebase Cloud Functions - User Handler
- **File**: [sonub-firebase-functions-user-handler.md](./sonub-firebase-functions-user-handler.md)
- **Title**: Firebase Cloud Functions - User Handler
- **Description**: 사용자 생성/수정 시 호출되는 비즈니스 로직 핸들러 명세 (`handleUserCreate`, `handleUserUpdate`)
- **Version**: 1.0.0
- **Step**: (미정)
- **Priority**: *
- **Dependencies**:
  - sonub-firebase-functions-index.md
- **Tags**: firebase, cloud-functions, user, handler
- **주요 내용**:
  - createdAt 자동 생성, displayNameLowerCase/updatedAt 조건부 갱신
  - before/after 데이터를 이용한 변경 감지 및 로깅 전략

## Chat System

### Sonub Chat Overview
- **File**: [sonub-chat-overview.md](./sonub-chat-overview.md)
- **Title**: 채팅 및 게시판 통합 시스템 개요
- **Description**: 게시판과 실시간 채팅을 동일한 Flat 구조에서 운영하기 위한 데이터 모델과 권한 체계
- **Version**: 1.0.0
- **Step**: 20
- **Priority**: ***
- **Dependencies**:
  - sonub-firebase-database-structure.md
- **Tags**: chat, messaging, board, realtime, firebase-rtdb
- **주요 내용**:
  - 채팅방/서브채팅방 타입, owner/moderator/member 역할
  - `/chat-messages/{messageId}` Flat 구조, Order 필드 기반 정렬 전략
  - 게시판 통합, 메시지 타입, 생명주기 및 Cloud Functions 활용 지침

### Sonub Chat Message
- **File**: [sonub-chat-message.md](./sonub-chat-message.md)
- **Status**: ⚠️ 작성되지 않은 문서입니다. 채팅 메시지 세부 명세가 필요하면 개발자에게 추가 지시를 요청하세요.

### Sonub Chat File Attachment
- **File**: [sonub-chat-file-attachment.md](./sonub-chat-file-attachment.md)
- **Title**: 채팅 파일 첨부 기능 (Chat File Attachment)
- **Description**: 채팅 메시지에 파일 첨부 기능 - 이미지, 동영상, 문서 등 다중 파일 업로드 및 표시
- **Version**: 1.2.0
- **Step**: 55
- **Priority**: **
- **Dependencies**:
  - sonub-chat-room.md
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-design-workflow.md
- **Tags**: chat, file-upload, firebase-storage, attachment, svelte5, realtime, instant-upload, video-controls, file-size-limit, file-extension-display, filename-extension-extraction, circular-progress, drag-drop, animation
- **Files**:
  - `src/lib/types/chat.types.ts` - 채팅 메시지 및 파일 업로드 타입
  - `src/lib/functions/storage.functions.ts` - Firebase Storage 업로드 함수
  - `src/routes/chat/room/+page.svelte` - 채팅방 페이지 (파일 첨부 UI)
  - `firebase/storage.rules` - Firebase Storage 보안 규칙
- **주요 기능**:
  - **파일 선택 즉시 업로드** (v1.1.0) - 사용자가 업로드 성공 즉시 확인 가능
  - **동영상 재생 컨트롤러** (v1.1.1) - 미리보기에서 동영상 재생/일시정지/볼륨 조절 가능
  - **파일 타입별 크기 제한** (v1.1.2) - .mp4 동영상은 24MB, 그 외 파일은 10MB까지 허용
  - **파일 확장자 중앙 표시** (v1.1.3) - PDF, TXT, DOC 등 확장자를 크게 중앙에 표시
  - **파일명 확장자 추출 함수** (v1.1.4) - `getExtensionFromFilename()` 함수로 버그 수정
  - **원형 프로그레스바 진행률** (v1.2.0) - SVG 원형 프로그레스바와 부드러운 애니메이션
  - **드래그 앤 드롭 업로드** (v1.2.0) - 파일을 채팅창에 드래그하여 간편하게 업로드
  - 다중 파일 업로드 (이미지, 동영상, 문서, 압축파일)
  - 파일 미리보기 Grid UI (반응형, 정사각형 비율)
  - 파일 삭제 기능 (Firebase Storage에서 실제 삭제)
  - RTDB에 URL만 저장하여 용량 최소화 (60-70% 절감)
  - 메시지 버블 내 첨부파일 표시 (이미지/동영상/일반파일)
  - Firebase Storage 경로: `/users/{uid}/chat-files/{roomId}/{timestamp}-{filename}`
  - 최대 파일 크기: .mp4 동영상 24MB, 그 외 10MB
- **구현 완료**: ✅ 2025-11-14

### Sonub Chat Notification Sound
- **File**: [sonub-chat-notification-sound.md](./sonub-chat-notification-sound.md)
- **Title**: 채팅 새 메시지 알림음 시스템
- **Description**: 사용자별 읽지 않은 채팅 메시지 개수 집계 및 실시간 알림음 재생 시스템
- **Version**: 1.0.0
- **Step**: 52
- **Priority**: **
- **Dependencies**:
  - sonub-chat-overview.md
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-firebase-realtime-database.md
  - sonub-store-database.md
- **Tags**: chat, notification, sound, realtime, firebase-rtdb, cloud-functions, svelte5, broadcast-channel
- **Files**:
  - `firebase/functions/src/handlers/chat.new-message.handler.ts` - newMessageCount 집계 Cloud Function
  - `src/lib/components/top-bar.svelte` - 새 메시지 배지 UI
  - `src/routes/+layout.svelte` - 전역 알림음 시스템
  - `static/sound/new-message.mp3` - 알림음 파일
- **주요 기능**:
  - `/chat-joins/{uid}/{roomId}/newMessageCount` 자동 집계 → `/users/{uid}/newMessageCount`
  - Cloud Functions로 증가/감소/재계산 처리 (데이터 일관성 보장)
  - TopBar에 빨간색 배지로 읽지 않은 메시지 수 실시간 표시 (99+ 제한)
  - newMessageCount 증가 시 알림음 자동 재생
  - 채팅방 페이지에서는 알림음 재생 안 함
  - BroadcastChannel로 다중 탭 중복 재생 방지
  - 500ms 디바운스로 빠른 연속 재생 방지
  - rtdbStore()를 통한 실시간 구독 및 Svelte 5 runes 기반 반응형 UI
- **구현 완료**: ✅ 2025-11-14

### Sonub Chat Room Password
- **File**: [sonub-chat-room-password.md](./sonub-chat-room-password.md)
- **Title**: 채팅방 비밀번호 보호 기능
- **Description**: 그룹/오픈 채팅방에 비밀번호를 설정하여 입장을 제한하는 기능
- **Version**: 1.0.0
- **Step**: 53
- **Priority**: **
- **Dependencies**:
  - sonub-chat-overview.md
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
- **Tags**: chat, password, security, firestore, cloud-functions, svelte5, authentication
- **Files**:
  - `firebase/functions/src/handlers/chat.password-verification.handler.ts` - 비밀번호 검증 Cloud Function
  - `firebase/firestore.rules` - Firebase Security Rules (password 보호 로직)
  - `src/lib/components/chat/room-password-setting.svelte` - 비밀번호 설정 UI (Owner 전용)
  - `src/lib/components/chat/room-password-prompt.svelte` - 비밀번호 입력 모달
  - `src/routes/chat/room/+page.svelte` - 채팅방 입장 로직 수정
  - `messages/*.json` - 다국어 지원 (ko, en, ja, zh)
- **주요 기능**:
  - 채팅방 Owner만 비밀번호 설정/해제 가능
  - Plain text 비밀번호 저장 (최소 4자)
  - `/chat-room-passwords/{roomId}/password` 경로에 비밀번호 저장
  - `/chat-rooms/{roomId}/password: true` 플래그로 비밀번호 활성화
  - Cloud Functions를 통한 서버 측 비밀번호 검증
  - `/chat-room-passwords/{roomId}/try/{uid}`에 비밀번호 입력 시 검증 트리거
  - 검증 성공 시 `/chat-rooms/{roomId}/members/{uid}: true` 자동 추가
  - 10초 폴링으로 검증 결과 실시간 확인
  - Firebase Security Rules로 비밀번호 있는 방 입장 차단 (Owner 제외)
  - shadcn-svelte Dialog, Input, Switch 컴포넌트 활용
  - 다국어 지원 (chatPasswordSettings, chatPasswordVerifying 등)
- **구현 완료**: ✅ 2025-11-14

## Deployment

### Sonub Deploy Workflow
- **File**: [sonub-deploy-workflow.md](./sonub-deploy-workflow.md)
- **Title**: GitHub 푸시 기반 자동 배포 워크플로우 명세서
- **Description**: GitHub repository에 코드를 푸시하면 Dokploy에서 webhook 이벤트를 받아 자동으로 빌드하고 프로덕션 사이트를 업데이트하는 CI/CD 워크플로우
- **Version**: 1.0.0
- **Step**: 100
- **Priority**: **
- **Dependencies**:
  - sonub-setup-svelte.md
- **Tags**: deployment, github, dokploy, ci-cd, production
- **배포 프로세스**:
  - GitHub repository에 코드 푸시
  - GitHub Webhook 이벤트 발생
  - Dokploy Webhook 수신 및 자동 빌드 시작
  - 의존성 설치, SvelteKit 빌드, 테스트 실행
  - 프로덕션 서버 배포
  - 헬스 체크 및 모니터링
- **주요 기능**:
  - 자동 배포 (CI/CD)
  - 빌드 성공/실패 처리
  - 자동 롤백 메커니즘
  - 배포 로그 및 모니터링
  - 헬스 체크 자동화


