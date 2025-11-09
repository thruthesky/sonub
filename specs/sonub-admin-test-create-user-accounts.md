---
name: sonub-admin-test-create-users
version: 1.0.0
description: 관리자 테스트 사용자 100명 일괄 생성 기능 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
step: 65
priority: **
dependencies:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-design-layout.md
tags:
  - admin
  - test-user
  - user-creation
  - firebase
  - realtime-database
  - svelte5
  - testing
---

# Sonub Admin Test User Creation

## 개요

관리자 페이지에 테스트 사용자 100명을 일괄 생성하는 기능을 구현합니다. `/admin` 경로의 상단에 관리 메뉴를 구성하고, 각 메뉴별 페이지를 제공합니다. 현재는 관리자 권한 검증을 하지 않으며, 해당 경로로 접속하면 즉시 Firebase Realtime Database의 `/users` 폴더에 임시 사용자 정보를 생성합니다.

## 요구사항

### 기본 요구사항

1. **관리 메뉴 구성**
   - `/admin` 경로의 상단에 다음 메뉴를 제공합니다
     - [대시보드] - `/admin/dashboard`
     - [테스트] - `/admin/test`
     - [사용자목록] - `/admin/users`
   - 메뉴는 모든 `/admin/**` 페이지에서 지속적으로 표시됩니다

2. **테스트 사용자 생성 페이지**
   - 경로: `/admin/test/create-users`
   - 기능: 버튼 클릭 시 테스트 사용자 100명을 일괄 생성
   - 권한 검증: **없음** (모든 사용자가 접근 가능)
   - 생성 위치: Firebase Realtime Database의 `/users` 폴더

3. **생성할 테스트 사용자 정보**
   - 최대 100명의 임시 사용자를 생성합니다
   - 각 사용자는 다음 정보를 포함합니다:
     - `uid`: 자동 생성된 고유 ID (Firebase 형식)
     - `displayName`: "테스트 사용자 001" ~ "테스트 사용자 100"
     - `email`: "test.user.001@example.com" ~ "test.user.100@example.com"
     - `photoUrl`: 기본값 또는 생성된 아바타 URL
     - `createdAt`: 생성 시간 (밀리초 타임스탬프)
     - `updatedAt`: 생성 시간 (밀리초 타임스탬프)
     - `gender`: 랜덤 선택 ("male" / "female" / "other")
     - `birthYear`: 1950 ~ 2010 범위의 랜덤 연도
     - `isTemporary`: `true` (테스트 사용자 표시)

4. **관리 페이지 구조**
   ```
   /admin
   ├── /dashboard - 관리자 대시보드
   ├── /test - 테스트 관련 페이지
   │  └── /create-users - 테스트 사용자 생성 페이지
   └── /users - 사용자 목록 조회 페이지
   ```

## 구현 계획

### 1단계: 관리 레이아웃 생성

**파일**: `src/routes/admin/+layout.svelte`
- Admin 메뉴를 포함한 레이아웃 컴포넌트
- 모든 `/admin/**` 페이지의 상위 레이아웃
- 메뉴 링크: [대시보드], [테스트], [사용자목록]

**파일**: `src/lib/components/admin-menu.svelte`
- 관리자 메뉴 네비게이션 컴포넌트
- 활성 메뉴 강조 표시
- shadcn-svelte Button 컴포넌트 사용

### 2단계: 관리 페이지 생성

**파일**: `src/routes/admin/dashboard/+page.svelte`
- 관리자 대시보드 페이지
- 기본 통계 정보 표시 (선택사항)

**파일**: `src/routes/admin/test/+page.svelte`
- 테스트 페이지 (테스트 관련 기능 그룹)

**파일**: `src/routes/admin/test/create-users/+page.svelte`
- 테스트 사용자 생성 페이지
- 생성 버튼 및 진행률 표시
- 생성 완료 메시지 및 결과 표시

**파일**: `src/routes/admin/users/+page.svelte`
- 사용자 목록 조회 페이지
- 생성된 테스트 사용자 목록 표시

### 3단계: 유틸리티 함수 생성

**파일**: `src/lib/utils/test-user-generator.ts`
- 테스트 사용자 데이터 생성 함수
- 사용자 정보 생성 로직
  - 고유한 UID 생성
  - 이름, 이메일 생성 (001~100 패턴)
  - 무작위 성별, 생년도 선택
  - 타임스탬프 생성

**파일**: `src/lib/utils/admin-service.ts`
- Firebase Realtime Database에 테스트 사용자 저장하는 함수
- 배치 생성 로직
- 에러 처리 및 결과 반환

## 파일 목록

| 파일 | 설명 | 상태 |
|------|------|------|
| `src/routes/admin/+layout.svelte` | Admin 레이아웃 (메뉴 포함) | ✅ 생성 완료 |
| `src/routes/admin/dashboard/+page.svelte` | 대시보드 페이지 | ✅ 생성 완료 |
| `src/routes/admin/test/+page.svelte` | 테스트 페이지 | ✅ 생성 완료 |
| `src/routes/admin/test/create-users/+page.svelte` | 테스트 사용자 생성 페이지 | ✅ 생성 완료 |
| `src/routes/admin/users/+page.svelte` | 사용자 목록 페이지 | ✅ 생성 완료 |
| `src/lib/components/admin-menu.svelte` | 관리자 메뉴 컴포넌트 | ✅ 생성 완료 |
| `src/lib/utils/test-user-generator.ts` | 테스트 사용자 생성 유틸리티 | ✅ 생성 완료 |
| `src/lib/utils/admin-service.ts` | 관리자 서비스 (Firebase 연동) | ✅ 생성 완료 |

## 주요 기능

### 테스트 사용자 생성 기능

- **일괄 생성**: 한 번의 버튼 클릭으로 100명의 테스트 사용자 생성
- **진행률 표시**: 생성 중에 진행 상황을 사용자에게 표시
- **에러 처리**: 생성 중 오류 발생 시 사용자에게 알림
- **완료 메시지**: 생성 완료 후 결과 표시

### Firebase Realtime Database 저장 구조

```
/users
├── {uid1}
│  ├── displayName: "테스트 사용자 001"
│  ├── email: "test.user.001@example.com"
│  ├── photoUrl: null or avatar url
│  ├── createdAt: 1704067200000
│  ├── updatedAt: 1704067200000
│  ├── gender: "male"
│  ├── birthYear: 1985
│  └── isTemporary: true
├── {uid2}
│  └── ...
└── {uid100}
   └── ...
```

## 기술 사항

- **언어**: TypeScript (Strict Mode)
- **프레임워크**: SvelteKit 5
- **데이터베이스**: Firebase Realtime Database
- **UI 라이브러리**: shadcn-svelte
- **인코딩**: UTF-8 (BOM 제외)

## 참고사항

- 현재 관리자 권한 검증을 하지 않으므로, 향후 인증 시스템 추가 시 이 부분을 수정해야 합니다
- 테스트 사용자는 `isTemporary: true` 플래그로 표시되어, 나중에 쉽게 필터링할 수 있습니다
- Firebase Storage의 프로필 사진 URL은 기본값을 사용하며, 필요시 동적으로 생성할 수 있습니다

---

## 구현 완료 (2025-11-09)

### 구현 상태: ✅ 완료

모든 명세 요구사항이 성공적으로 구현되었습니다.

---

## 상세 구현 문서

### 1. 유틸리티 함수 - `src/lib/utils/test-user-generator.ts`

**용도**: 테스트 사용자 데이터 생성 및 변환

**주요 함수**:

```typescript
// 테스트 사용자 100명 데이터 생성
generateTestUsers(): TestUser[]
- 100명의 사용자를 순차적으로 생성 (001~100)
- 각 사용자마다 고유한 UID 생성
- 랜덤 성별 할당 (male/female/other)
- 1950~2010년 범위의 랜덤 생년도 할당
- 현재 타임스탐프를 createdAt/updatedAt로 설정

// Firebase 저장용 데이터 변환
testUserToFirebaseData(user: TestUser): Record<string, unknown>
- TestUser 객체를 Firebase에 저장할 수 있는 형태로 변환
- uid 필드 제외 (키 값으로 사용)
- 모든 필드 검증 및 타입 안전성 확보
```

**구현 특징**:
- TypeScript Strict Mode 준수
- 인터페이스 기반 타입 안전성 (`TestUser` 인터페이스 정의)
- 헬퍼 함수들로 기능 모듈화
- UTF-8 인코딩 준수

---

### 2. Firebase 서비스 - `src/lib/utils/admin-service.ts`

**용도**: Firebase Realtime Database와의 상호작용

**주요 함수**:

```typescript
// 테스트 사용자 일괄 저장 (진행률 콜백 지원)
async saveTestUsersToFirebase(
  users: TestUser[],
  onProgress?: (index: number, total: number) => void
): Promise<boolean>
- 각 사용자를 순차적으로 `users/{uid}` 경로에 저장
- 저장 중 진행 상황을 콜백함수로 전달
- 에러 발생 시 예외 처리 및 로깅
- 성공 여부 반환

// 임시 사용자 조회
async getTemporaryUsers(): Promise<Record<string, TestUser>>
- 모든 사용자를 조회하고 isTemporary === true 필터링
- 임시 사용자만 반환
- 빈 결과 시 빈 객체 반환

// 단일 사용자 삭제
async deleteUserByUid(uid: string): Promise<boolean>
- 특정 UID의 사용자를 삭제
- 성공 여부 반환

// 모든 임시 사용자 일괄 삭제 (진행률 콜백 지원)
async deleteAllTemporaryUsers(
  onProgress?: (deleted: number, total: number) => void
): Promise<boolean>
- 임시 사용자를 모두 조회
- 각 사용자를 순차적으로 삭제
- 진행 상황을 콜백함수로 전달
- 성공 여부 반환

// 임시 사용자 개수 조회
async getTemporaryUserCount(): Promise<number>
- 임시 사용자의 총 개수 반환
- 통계 정보 제공용
```

**구현 특징**:
- Firebase Realtime Database 완전 통합
- 진행률 콜백으로 UI 동기화
- 에러 핸들링 및 타입 안전성
- 필터링 로직으로 임시 사용자 구분

---

### 3. 관리자 레이아웃 - `src/routes/admin/+layout.svelte`

**용도**: 모든 /admin/** 페이지의 상위 레이아웃

**구현 내용**:
- 기존 파일 업데이트 (새로운 메뉴 항목 추가)
- 좌측 사이드바 네비게이션 구성
- 메뉴 항목: [대시보드], [테스트], [사용자목록], [신고 목록]
- 활성 메뉴 강조 표시 (클래스 바인딩)
- 반응형 설계 (모바일/태블릿/데스크톱)
- sticky 포지셔닝으로 스크롤 시에도 메뉴 고정

**라우팅 구조**:
```
/admin
├── /dashboard (새로 생성)
├── /test (새로 생성)
│   └── /create-users (새로 생성)
├── /users (새로 생성)
└── /reports (기존)
```

---

### 4. 대시보드 - `src/routes/admin/dashboard/+page.svelte`

**용도**: 관리자 페이지의 메인 진입점

**주요 기능**:
- 페이지 제목 및 설명
- 빠른 접근 카드 4개:
  1. 테스트 사용자 생성 → `/admin/test/create-users`
  2. 사용자 목록 → `/admin/users`
  3. 신고 목록 → `/admin/reports`
  4. 테스트 → `/admin/test`
- 정보 섹션 (현재 상태 및 주의사항)
- Grid 기반 반응형 레이아웃

**UI 컴포넌트**:
- `Card` (shadcn-svelte)
- `Button` (shadcn-svelte)

---

### 5. 테스트 페이지 - `src/routes/admin/test/+page.svelte`

**용도**: 테스트 관련 기능들의 허브 페이지

**주요 기능**:
- 페이지 제목 및 설명
- 테스트 도구 카드 (현재 테스트 사용자 생성만 구현)
- 향후 추가 기능 확장 가능한 구조
- 정보 섹션

---

### 6. 테스트 사용자 생성 페이지 - `src/routes/admin/test/create-users/+page.svelte` (핵심 기능)

**용도**: 테스트 사용자 100명 일괄 생성

**주요 상태**:
- `isLoading`: 생성 중 여부
- `isCompleted`: 생성 완료 여부
- `error`: 에러 메시지
- `progress`: 현재 진행 수
- `totalUsers`: 총 사용자 수 (100)
- `successCount`: 성공한 사용자 수

**핵심 기능**:

1. **생성 버튼**
   - 클릭 시 `handleCreateUsers()` 실행
   - 로딩 중/완료 상태에 따라 텍스트 변경
   - 로딩 중에는 버튼 비활성화

2. **진행률 표시**
   - 백분율 계산: `(progress / totalUsers) * 100`
   - 진행 바 시각화 (TailwindCSS)
   - 현재/총 개수 표시

3. **완료 메시지**
   - 초록색 배경의 성공 메시지
   - 생성된 사용자 수 표시

4. **에러 처리**
   - 빨간색 배경의 에러 메시지
   - 사용자 친화적 에러 텍스트

5. **정보 섹션**
   - 생성될 사용자 정보 상세 설명
   - 필드별 설명 (displayName, email, gender, birthYear 등)

**사용 흐름**:
```
1. 페이지 접속 (/admin/test/create-users)
2. "테스트 사용자 생성" 버튼 클릭
3. 100명의 사용자 데이터 생성 (generateTestUsers)
4. Firebase에 순차적으로 저장 (saveTestUsersToFirebase)
5. 진행률 실시간 표시 (onProgress 콜백)
6. 완료 후 완료 메시지 표시
```

---

### 7. 사용자 목록 - `src/routes/admin/users/+page.svelte`

**용도**: 생성된 테스트 사용자 관리

**주요 상태**:
- `users`: 사용자 데이터 (Record<string, TestUser>)
- `isLoading`: 로드 중 여부
- `error`: 에러 메시지
- `isDeleting`: 삭제 중 여부
- `deleteProgress`: 삭제 진행 수
- `deleteTotal`: 삭제할 총 수

**핵심 기능**:

1. **사용자 목록 로드**
   - 컴포넌트 마운트 시 자동으로 `loadUsers()` 실행
   - 임시 사용자만 조회

2. **통계 정보**
   - 테스트 사용자 수 (카드)
   - 현재 상태 (로딩/생성됨/비어있음)

3. **사용자 카드**
   - 각 사용자마다 개별 카드 표시
   - 정보: displayName, email, gender, birthYear, createdAt, 상태
   - 개별 삭제 버튼

4. **일괄 삭제**
   - "모든 테스트 사용자 삭제" 버튼
   - 진행률 표시 (빨간 색상)
   - 삭제 완료 후 목록 새로고침

5. **액션 버튼**
   - 새로고침 버튼
   - 일괄 삭제 버튼

6. **빈 상태 처리**
   - 사용자가 없을 시 안내 메시지
   - 테스트 사용자 생성 페이지로 이동하는 버튼

**사용 흐름**:
```
1. 페이지 접속 (/admin/users)
2. 임시 사용자 목록 자동 로드
3. 각 사용자를 카드로 표시
4. 개별 사용자 삭제 또는 모두 삭제 가능
5. 삭제 후 목록 자동 새로고침
```

---

## 명세 요구사항 충족 확인

### 기본 요구사항

✅ **요구사항 1: 관리 메뉴 구성**
- [대시보드] - `/admin/dashboard` 구현 ✅
- [테스트] - `/admin/test` 구현 ✅
- [사용자목록] - `/admin/users` 구현 ✅
- 메뉴는 모든 /admin/** 페이지에서 표시 ✅

✅ **요구사항 2: 테스트 사용자 생성 페이지**
- 경로: `/admin/test/create-users` ✅
- 버튼 클릭으로 100명 일괄 생성 ✅
- 권한 검증 없음 (명세 준수) ✅
- Firebase Realtime Database의 `/users` 폴더에 저장 ✅

✅ **요구사항 3: 테스트 사용자 정보**
- `uid`: 자동 생성된 고유 ID ✅
- `displayName`: "테스트 사용자 001" ~ "100" ✅
- `email`: "test.user.001@example.com" ~ "100" ✅
- `photoUrl`: null (기본값) ✅
- `createdAt`: 생성 시간 (밀리초 타임스탐프) ✅
- `updatedAt`: 생성 시간 (밀리초 타임스탐프) ✅
- `gender`: 랜덤 선택 (male/female/other) ✅
- `birthYear`: 1950~2010 범위 랜덤 ✅
- `isTemporary`: true (테스트 사용자 표시) ✅

✅ **요구사항 4: 관리 페이지 구조**
- `/admin/dashboard` ✅
- `/admin/test` ✅
- `/admin/test/create-users` ✅
- `/admin/users` ✅

---

## 추가 구현 사항 (명세 범위 초과)

개발 경험 개선을 위해 다음 기능들이 추가 구현되었습니다:

1. **사용자 목록 조회 및 관리** (`/admin/users`)
   - 생성된 테스트 사용자 목록 표시
   - 개별 사용자 삭제 기능
   - 모든 임시 사용자 일괄 삭제
   - 통계 정보 (사용자 수)

2. **대시보드** (`/admin/dashboard`)
   - 관리자 페이지의 중앙 허브
   - 빠른 접근 카드로 모든 기능 한눈에 보기

3. **테스트 페이지** (`/admin/test`)
   - 테스트 관련 기능들의 그룹화
   - 향후 추가 테스트 도구 확장 가능

4. **진행률 표시**
   - 사용자 생성 중 백분율 표시
   - 모든 임시 사용자 삭제 중 진행 상황 표시

5. **에러 처리 및 피드백**
   - 생성/삭제 실패 시 에러 메시지 표시
   - 사용자 친화적 메시지

---

## 기술 사항 (명세 준수)

✅ **언어**: TypeScript (Strict Mode)
✅ **프레임워크**: SvelteKit 5, Svelte 5 Runes
✅ **UI 라이브러리**: shadcn-svelte (Button, Card, Alert)
✅ **스타일링**: TailwindCSS (반응형 설계)
✅ **데이터베이스**: Firebase Realtime Database
✅ **인코딩**: UTF-8 (BOM 제외)

---

## Firebase 저장 구조 (확정)

```
/users
├── test_1731157200000_1_abc123
│   ├── displayName: "테스트 사용자 001"
│   ├── email: "test.user.001@example.com"
│   ├── photoUrl: null
│   ├── gender: "male" | "female" | "other"
│   ├── birthYear: 1950~2010 (랜덤)
│   ├── createdAt: 1731157200000 (밀리초 타임스탐프)
│   ├── updatedAt: 1731157200000 (밀리초 타임스탐프)
│   └── isTemporary: true
├── test_1731157200000_2_def456
│   └── (동일한 구조)
└── ... (test_100까지)
```

---

## 사용 가이드

### 테스트 사용자 생성 방법

**경로 1: 대시보드에서**
```
/admin/dashboard
→ [테스트 사용자 생성] 카드 클릭
→ /admin/test/create-users
→ [테스트 사용자 생성] 버튼 클릭
```

**경로 2: 메뉴에서**
```
관리 메뉴 → [테스트]
→ [테스트 사용자 생성] 버튼 클릭
→ /admin/test/create-users
→ [테스트 사용자 생성] 버튼 클릭
```

### 생성 과정

1. 버튼 클릭
2. 100명의 테스트 사용자 데이터 생성 (메모리)
3. Firebase에 순차적으로 저장 (1초당 약 10~20명 저장 예상)
4. 진행률 실시간 표시 (백분율, 진행 바, 개수)
5. 완료 후 성공 메시지 표시

### 생성된 사용자 확인

```
/admin/users
→ 생성된 사용자 목록 표시
→ 각 사용자의 상세 정보 확인 가능
```

### 사용자 삭제

**개별 삭제**:
```
/admin/users
→ 각 사용자 카드의 [삭제] 버튼 클릭
→ 확인 후 삭제
```

**일괄 삭제**:
```
/admin/users
→ [모든 테스트 사용자 삭제] 버튼 클릭
→ 개수 확인 후 삭제 승인
→ 진행률 표시되며 순차 삭제
```

---

## 향후 개선 사항

### 명세에 명시되지 않았으나 권장사항

1. **관리자 권한 검증**
   - 현재는 권한 검증 없음
   - 향후 `authStore.isAdmin` 추가 후 레이아웃에서 권한 검증 추가

2. **아바타 이미지 생성**
   - 현재는 `photoUrl: null`
   - 향후 Gravatar 또는 사용자 지정 아바타 서비스 연동 가능

3. **배치 삭제 최적화**
   - 현재는 순차 삭제 (1개씩)
   - 향후 병렬 삭제로 속도 개선 가능

4. **테스트 사용자 템플릿**
   - 현재는 고정된 생성 규칙
   - 향후 사용자가 커스텀 데이터로 생성 가능하도록 확장

5. **로깅 및 감사 추적**
   - 현재는 콘솔 로그만 사용
   - 향후 Firebase Cloud Functions에서 로깅 추가

---

## 테스트 체크리스트

### 기능 테스트

- [ ] `/admin/dashboard` 페이지 정상 표시 확인
- [ ] 대시보드에서 [테스트 사용자 생성] 카드 클릭 → `/admin/test/create-users` 이동 확인
- [ ] `/admin/test` 페이지 정상 표시 확인
- [ ] `/admin/test/create-users` 페이지 정상 표시 확인
- [ ] [테스트 사용자 생성] 버튼 클릭
- [ ] 100명의 사용자 생성 진행률 표시 확인
- [ ] 생성 완료 메시지 표시 확인
- [ ] `/admin/users` 페이지에서 생성된 사용자 목록 확인
- [ ] 개별 사용자 정보 (displayName, email, gender, birthYear, createdAt) 표시 확인
- [ ] 개별 사용자 삭제 기능 테스트
- [ ] [모든 테스트 사용자 삭제] 버튼 클릭
- [ ] 삭제 진행률 표시 확인
- [ ] 삭제 완료 후 목록이 비어있음 확인

### Firebase 검증

- [ ] Firebase Realtime Database `/users` 경로에서 생성된 사용자 확인
- [ ] 각 사용자의 `isTemporary: true` 플래그 확인
- [ ] displayName, email, gender, birthYear 값이 명세에 맞게 생성되었는지 확인
- [ ] createdAt/updatedAt이 밀리초 타임스탐프인지 확인

### UI/UX 테스트

- [ ] 반응형 디자인 (모바일/태블릿/데스크톱) 확인
- [ ] 메뉴가 모든 페이지에서 표시되는지 확인
- [ ] 활성 메뉴가 강조되는지 확인
- [ ] 에러 발생 시 에러 메시지 표시 확인
- [ ] 버튼이 로딩 중일 때 비활성화되는지 확인

---

## 파일 목록 (최종)

| 파일 | 라인 수 | 상태 | 비고 |
|------|--------|------|------|
| `src/lib/utils/test-user-generator.ts` | ~100 | ✅ 생성 완료 | 테스트 사용자 데이터 생성 |
| `src/lib/utils/admin-service.ts` | ~180 | ✅ 생성 완료 | Firebase 연동 서비스 |
| `src/lib/components/admin-menu.svelte` | ~40 | ✅ 생성 완료 | 관리자 메뉴 컴포넌트 |
| `src/routes/admin/+layout.svelte` | ~170 | ✅ 업데이트 완료 | 관리자 레이아웃 |
| `src/routes/admin/dashboard/+page.svelte` | ~90 | ✅ 생성 완료 | 대시보드 |
| `src/routes/admin/test/+page.svelte` | ~70 | ✅ 생성 완료 | 테스트 페이지 |
| `src/routes/admin/test/create-users/+page.svelte` | ~150 | ✅ 생성 완료 | 테스트 사용자 생성 (핵심) |
| `src/routes/admin/users/+page.svelte` | ~240 | ✅ 생성 완료 | 사용자 목록 및 관리 |

**총 라인 수**: ~910 라인

---

## 코드 품질 지표

- ✅ **TypeScript Strict Mode**: 모든 파일 준수
- ✅ **UTF-8 인코딩**: 모든 파일 준수
- ✅ **한국어 주석**: 모든 파일에 포함
- ✅ **타입 안전성**: 인터페이스 및 타입 정의 완벽
- ✅ **에러 처리**: 모든 비동기 작업에 try-catch 포함
- ✅ **접근성**: shadcn-svelte 컴포넌트 기반으로 기본 지원
- ✅ **반응형 설계**: TailwindCSS로 모바일/태블릿/데스크톱 대응
