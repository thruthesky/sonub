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

4. **기술 표준**:
   - Svelte 5.4.1 + SvelteKit 버전 `"@sveltejs/kit": "^2.47.1"` 사용
   - TypeScript 엄격 모드(strict mode) 필수
   - UTF-8 인코딩 필수 (BOM 제외)
   - 최소 80% 코드 커버리지
   - 모든 HTML 콘텐츠는 영어로 작성 필수
   - 모든 한글 주석/문서는 UTF-8로 작성

---

# Specifications Index
This document provides a detailed index of all specifications related to the sonub project. Each specification is listed with its title, description, and relevant metadata extracted from its YAML header.

## Design and Styling

### Sonub Design Workflow
- **File**: [sonub-design-workflow.md](./sonub-design-workflow.md)
- **Title**: Sonub Design Workflow
- **Description**: TailwindCSS와 shadcn-svelte를 사용한 디자인 워크플로우 가이드라인
- **Version**: 1.0.0
- **Step**: 10
- **Priority**: *
- **Dependencies**:
  - sonub-setup-tailwind.md
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
  - sonub-setup-tailwind.md
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
- **Status**: ⚠️ 문서 내용이 아직 작성되지 않았습니다. 좌측 사이드바 세부 명세가 필요하면 개발자에게 요청하세요.

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

### Shadcn-Svelte Setup
- **File**: [sonub-setup-shadcn.md](./sonub-setup-shadcn.md)
- **Title**: SvelteKit 프로젝트 shadcn-svelte 설치 명세서
- **Description**: SvelteKit 프로젝트에 shadcn-svelte UI 컴포넌트 라이브러리 설치 및 설정 명세서
- **Version**: 1.1.0
- **Step**: 25
- **Priority**: *
- **Dependencies**:
  - sonub-setup-svelte.md
  - sonub-setup-tailwind.md
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
  - Realtime Database
  - Firebase Storage
  - Firebase Analytics
- **주요 구현 사항**:
  - SvelteKit 환경 변수 사용 (`$env/static/public`)
  - SSR 대응 (nullable 타입, 브라우저 환경 체크)
  - 환경 변수 디버깅 로그
- **설치된 패키지**:
  - firebase@11.0.0 이상

### Firebase Realtime Database Guide
- **File**: [sonub-firebase-database.md](./sonub-firebase-database.md)
- **Title**: Firebase Realtime Database 구조 가이드
- **Description**: 사용자 정보, user-props, 친구 관계 등 전체 RTDB 스키마와 클라이언트/백엔드 역할 분리를 정의
- **Version**: 1.0.0
- **Step**: (미정)
- **Priority**: (미정)
- **Dependencies**: 없음
- **Tags**: firebase, realtime-database, data-model, architecture
- **주요 내용**:
  - `/users/{uid}`, `/user-props/*`, friends/followers/following 구조 정의
  - 클라이언트/서버 책임 구분 및 자동 동기화 원칙
  - Flat Style, 속성 분리, Cloud Functions 연계 철학
  - Firebase Auth vs RTDB 필드 차이 및 주의사항

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

## User Management

### Sonub User Overview
- **File**: [sonub-user-overview.md](./sonub-user-overview.md)
- **Title**: 사용자 관리 체계 및 프로필 관리 명세서
- **Description**: Firebase Authentication과 Realtime Database를 활용한 사용자 관리 시스템 설계 및 구현 명세서
- **Version**: 1.0.0
- **Step**: 40
- **Priority**: **
- **Dependencies**:
  - sonub-user-login.md
  - sonub-setup-firebase.md
- **Tags**: user-management, profile, firebase, authentication
- **관련 세부 명세**:
  - 사용자 프로필 정보 구조
  - Firebase Storage 및 Realtime Database 저장소 설계
  - 실시간 프로필 업데이트 기능

### Sonub User Profile
- **File**: [sonub-user-profile.md](./sonub-user-profile.md)
- **Title**: 사용자 프로필 사진 업로드 및 관리 명세서
- **Description**: 프로필 사진 저장소 구조, Firebase Storage 업로드, URL 관리 및 실시간 동기화 구현
- **Version**: 1.0.0
- **Step**: 45
- **Priority**: **
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
- **Tags**: user-profile, firebase-storage, photo-upload, svelte5
- **Files**:
  - `src/lib/components/user-profile.svelte` - 프로필 관리 컴포넌트
  - `src/lib/utils/profile-helpers.ts` - 프로필 관리 헬퍼 함수
- **구현된 기능**:
  - 프로필 사진 업로드 (JPEG, PNG, WebP 지원)
  - 파일 유효성 검증 (크기, 형식)
  - 다운로드 URL 생성 및 저장
  - 미리보기 기능
  - 사용자 정보 검증 (displayName, gender, dateOfBirth)

### Sonub User Profile Sync
- **File**: [sonub-user-profile-sync.md](./sonub-user-profile-sync.md)
- **Title**: Sonub User Profile Sync
- **Description**: Firebase Auth 로그인 직후 `/users/{uid}`에 displayName과 photoUrl을 자동 저장하는 동기화 플로우
- **Version**: 1.0.0
- **Step**: 15
- **Priority**: ***
- **Dependencies**:
  - sonub-setup-firebase.md
  - sonub-firebase-database.md
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
  - sonub-firebase-database.md
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

### Sonub Admin Test User Creation
- **File**: [sonub-admin-test-create-user-accounts.md](./sonub-admin-test-create-user-accounts.md)
- **Title**: Sonub Admin Test User Creation
- **Description**: `/admin/test/create-users`에서 100명의 임시 사용자를 일괄 생성하는 기능 명세
- **Version**: 1.0.0
- **Step**: 65
- **Priority**: **
- **Dependencies**:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-design-layout.md
- **Tags**: admin, test-user, firebase, rtdb, svelte5
- **주요 기능**:
  - `/admin` 상단 메뉴(대시보드/테스트/사용자목록) 공유 레이아웃
  - 버튼 한 번으로 100명 생성, displayName/email/gender/birthYear 랜덤 제공
  - `isTemporary` 플래그와 생성/삭제 진행률 표시, 추후 권한 검증 확장 여지

## Chat System

### Sonub Chat Overview
- **File**: [sonub-chat-overview.md](./sonub-chat-overview.md)
- **Title**: 채팅 및 게시판 통합 시스템 개요
- **Description**: 게시판과 실시간 채팅을 동일한 Flat 구조에서 운영하기 위한 데이터 모델과 권한 체계
- **Version**: 1.0.0
- **Step**: 20
- **Priority**: ***
- **Dependencies**:
  - sonub-firebase-database.md
- **Tags**: chat, messaging, board, realtime, firebase-rtdb
- **주요 내용**:
  - 채팅방/서브채팅방 타입, owner/moderator/member 역할
  - `/chat-messages/{messageId}` Flat 구조, Order 필드 기반 정렬 전략
  - 게시판 통합, 메시지 타입, 생명주기 및 Cloud Functions 활용 지침

### Sonub Chat Message
- **File**: [sonub-chat-message.md](./sonub-chat-message.md)
- **Status**: ⚠️ 작성되지 않은 문서입니다. 채팅 메시지 세부 명세가 필요하면 개발자에게 추가 지시를 요청하세요.

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


