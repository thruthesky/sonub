---
name: sonub-firestore-list-view-qa
version: 2.0.0
description: FirestoreListView 무한 스크롤 QA를 위한 테스트 데이터 생성 절차
author: Codex Agent
email: noreply@openai.com
step: 65
priority: '*'
dependencies:
  - sonub-firebase-database-structure.md
  - sonub-firebase-database-list-view.md
tags:
  - test
  - firestore
  - infinite-scroll
  - admin
---

## 1. 개요

이 문서는 Firestore 기반 `FirestoreListView` 컴포넌트의 무한 스크롤 동작을 검증하기 위해 샘플 데이터를 생성/삭제하는 절차를 정의합니다.  
- 실제 데이터는 모두 **Firestore `users` 컬렉션**에 저장되며, `test: true` 플래그로 식별됩니다.  
- 생성 스크립트와 UI는 모두 TypeScript/Firestorm 기반으로 구현되어 있습니다.

## 2. 테스트 데이터 스키마

샘플 사용자는 `src/lib/utils/test-user-generator.ts`의 `TestUser` 인터페이스와 동일한 구조를 따릅니다.

| 필드 | 타입 | 설명 | 비고 |
|------|------|------|------|
| `uid` | `string` | `test_${timestamp}_${index}_${random}` 형식의 고유 ID | `generateTestUserId()` |
| `displayName` | `string` | `"테스트 사용자 001 (MM-DD HH:MM)"` 형식 | 타임스탬프 포함 |
| `email` | `string` | `test.user.001@example.com` 형식 | 고유 값 |
| `photoUrl` | `string` | `https://picsum.photos/seed/{uid}/400/400` | FirestoreListView 썸네일 검증용 |
| `gender` | `'M' \| 'F'` | 무작위 성별 | RTDB의 `'male'/'female'` 대신 Firestore에서 사용 |
| `birthYearMonthDay` | `number` | `YYYYMMDD` 숫자 (예: 19930415) | Cloud Functions가 파생 필드 생성 |
| `createdAt` / `updatedAt` | `number` | `Date.now() + i*1000` | 순차 오프셋 |
| `isTemporary` | `boolean` | 항상 `true` | 기존 관리자 UI 호환 |
| `test` | `boolean` | 항상 `true` | Firestore 삭제 스크립트 필터 |

## 3. 생성/삭제 도구

### 3.1 CLI 스크립트 (firebase/test)

| 파일 | 설명 | 명령 |
|------|------|------|
| `create-sample-users.ts` | Firebase Admin SDK로 Firestore `users` 컬렉션에 100명 생성. `picsum.photos` 이미지, 무작위 성별/생년월일 포함. | `cd firebase/test && npx tsx create-sample-users.ts` |
| `delete-sample-users.ts` | `where('test', '==', true)` 조건으로 최근 스크립트가 생성한 문서 삭제. | `cd firebase/test && npx tsx delete-sample-users.ts` |
| `delete-old-sample-users.ts` | ID가 `test_`로 시작하는 기존 RTDB 시대 사용자 문서를 전체 조회 후 삭제. | `cd firebase/test && npx tsx delete-old-sample-users.ts` |

각 스크립트는 `firebase/test/firebase-service-account-key.json`을 사용하며, Firestore Admin 권한이 있는 환경에서만 실행해야 합니다.

### 3.2 관리자 UI `/admin/test/create-test-data`

- **파일**: `src/routes/admin/test/create-test-data/+page.svelte`
- **로직**: `generateTestUsers()` + `saveTestUsersToFirebase()`를 호출해 Firestore에 저장합니다.
- **진행률 표시**: `saveTestUsersToFirebase`는 각 문서를 저장할 때 `onProgress(index, total)` 콜백을 호출합니다.
- **완료 후 처리**: 별도 목록 페이지로 이동하지 않으며 QA가 직접 `/user/list`에서 확인합니다.

## 4. FirestoreListView QA 절차

### 4.1 `/user/list`

1. **데이터 준비**: CLI 스크립트 또는 관리자 UI로 최소 50개 이상의 테스트 사용자를 생성합니다.
2. **기본 정렬 검증**: `전체 회원` 옵션에서 `pageSize=15`, `orderByField=createdAt`, `orderDirection=desc`가 적용되는지 확인합니다 (`src/routes/user/list/+page.svelte`).
3. **검색 필터**:  
   - 검색 다이얼로그에서 `"테스트 사용자 00"` 입력 → `displayNameLowerCase` prefix 쿼리가 적용되는지 확인.  
   - 스크롤 바닥에 도달하면 `distanceFromBottom < threshold` 조건으로 추가 페이지가 로드되는지 확인.  
4. **선택적 필터**: `사진있는 회원`, `사진있는 여성`, `사진있는 남성` 옵션이 Firestore 파생 필드(`sort_recent*`)를 사용해 정상 정렬되는지 확인.

### 4.2 `/chat/room`

1. **채팅방 입장**: 1:1 채팅에서 메시지를 50개 이상 보내 FirestoreListView가 `scrollTrigger='top'` 모드로 동작하는지 확인합니다.  
2. **스크롤 복원**: 이전 메시지를 로드할 때 `scrollRestoreInfo` 로직이 적용되어 스크롤이 튀지 않는지 확인합니다.  
3. **실시간 갱신**: 새 메시지가 전송되면 `collectionUnsubscribe`가 추가 문서를 감지하고 자동으로 배열에 삽입되는지 확인합니다.

### 4.3 기타 페이지

향후 FirestoreListView를 사용하는 페이지가 추가되면 위 절차를 복제해 QA 시나리오를 작성합니다. 모든 페이지는 `FirestoreListView.svelte`와 동일한 props를 사용해야 합니다.

## 5. 정리/삭제 정책

- QA 완료 후 **반드시 `delete-sample-users.ts` 또는 관리자 UI 삭제 기능**으로 테스트 사용자(`test: true`)를 정리합니다.
- 기존 RTDB 시대에 생성한 사용자(ID가 `test_`로 시작하지만 `test` 플래그가 없는 경우)는 `delete-old-sample-users.ts`로 제거합니다.
- Firestore 콘솔에서 수동으로 삭제하지 말고, 항상 스크립트나 관리자 UI를 사용하여 감사 기록과 통일성을 유지합니다.

## 6. 작업 이력 (SED Log)

| 날짜 | 작업자 | 내용 |
| ---- | ------ | ---- |
| 2025-11-16 | Codex Agent | Firestore 기반 테스트 데이터 생성/삭제 스크립트(`create-sample-users.ts`, `delete-sample-users.ts`, `delete-old-sample-users.ts`) 도입에 맞춰 문서 전면 개정. 구 RTDB `/test/data` 경로 관련 내용 제거, FirestoreListView QA 절차 추가. |
