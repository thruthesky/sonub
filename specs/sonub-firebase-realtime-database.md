---
name: sonub-firestore-store
version: 2.0.0
description: Firestore 유틸리티 스토어 및 CRUD 함수 가이드
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
step: 30
priority: "***"
dependencies:
  - sonub-firebase-database-structure.md
  - sonub-firebase-security-rules.md
tags:
  - firestore
  - store
  - svelte
  - utility
---

# Firestore 유틸리티 스토어 (`src/lib/stores/firestore.svelte.ts`)

## 1. 개요

본 프로젝트는 **Cloud Firestore 전용 스토어/CRUD 헬퍼**를 사용합니다. 모든 기능은 `src/lib/stores/firestore.svelte.ts`에 구현되어 있으며, Svelte 5 runes와 Firestore SDK v11을 기반으로 합니다.

## 2. 주요 기능

| 기능 | 설명 | 관련 함수 |
|------|------|-----------|
| 문서 실시간 구독 | 특정 문서를 `{ data, loading, error }` 형태의 Svelte 스토어로 제공 | `createFirestoreStore`, `firestoreStore` |
| CRUD | 문서 쓰기/병합, 부분 업데이트, 삭제 | `writeDocument`, `updateDocument`, `deleteDocument` |
| 자동 ID 추가 | 컬렉션에 자동 ID로 문서 추가 | `addDocument` |
| 일회성 읽기 | 단일 문서를 읽고 결과/에러 반환 | `readDocument` |
| 쿼리 유틸리티 | where/orderBy/limit/startAfter/endBefore 조합으로 컬렉션 조회 | `buildQuery`, `runQuery` |
| 배치 연산 | 여러 문서를 한 번에 저장/업데이트 | `executeBatch` |
| 서버 타임스탬프 헬퍼 | 클라이언트 시간이 아닌 Firestore 서버 시간을 기록 | `serverTimestamp`, `Timestamp` (SDK 내장) |

> 모든 함수는 Firestore 초기화 상태(`db`)를 확인하고, 미초기화 시 명확한 에러 메시지를 반환합니다.

## 3. 실시간 스토어 사용법

```typescript
import { firestoreStore } from '$lib/stores/firestore.svelte';
import type { UserData } from '$lib/types/firestore.types';

const profile = firestoreStore<UserData>(`users/${uid}`);
```

- **Document 경로만 허용**: 경로 세그먼트 개수가 짝수인지 확인하며, Collection 경로가 전달되면 오류를 던집니다 (`parsePath()` 내부 로직).
- **상태 필드**
  - `data`: Firestore 문서 데이터 (`null` 가능)
  - `loading`: 구독 초기 로딩 여부
  - `error`: Firestore 에러 객체
- **구독 해제**: 반환 스토어에 `unsubscribe()` 메서드가 추가되어 컴포넌트 언마운트 시 정리할 수 있습니다.

## 4. CRUD 헬퍼

### 4.1 writeDocument(path, data, merge?)

- `setDoc`을 감싸는 함수.
- `merge=true`일 때 기존 데이터를 유지하면서 병합합니다.
- 경로가 문서 경로인지 자동 검증합니다.

### 4.2 updateDocument(path, updates)

- `updateDoc` 래퍼. 전달된 필드만 수정합니다.
- 문서가 존재하지 않으면 Firestore 에러를 그대로 전달합니다.

### 4.3 deleteDocument(path)

- `deleteDoc` 래퍼.
- Document 경로 여부를 검증합니다.

### 4.4 addDocument(path, data)

- Collection 경로에 새 문서를 추가합니다 (`addDoc`).
- 반환 타입은 `{ success: boolean; id?: string; error?: string }`.

### 4.5 readDocument(path, defaultValue?)

- `getDoc`을 사용해 단발성으로 문서를 읽습니다.
- 존재하지 않으면 `data: defaultValue ?? null`을 반환합니다.

## 5. 쿼리/배치 유틸리티

- `buildQuery(path, options)`는 where/orderBy/limit/startAfter/endBefore 조합을 구성합니다.
- `runQuery<T>(path, options)`는 `getDocs()` 결과를 `{ id, data }[]` 형태로 변환합니다.
- `executeBatch(cb)`는 `writeBatch`를 열어 콜백 안에서 `set`, `update`, `delete`를 수행하고 한 번에 commit합니다.

이 유틸리티들은 `src/lib/components/FirestoreListView.svelte`나 관리자 도구에서 Firestore를 직접 접근할 때 사용됩니다.

## 6. 예시: 관리자 목록 로드

`src/lib/stores/auth.svelte.ts`에서 Firestore 유틸리티를 직접 사용하지는 않지만, 동일한 패턴으로 `doc(db, 'system/settings')`를 읽어 `admins` 필드를 파싱합니다. 실시간이 필요할 경우 `firestoreStore<SystemSettings>('system/settings')`를 사용할 수 있습니다.

## 7. 마이그레이션 노트

- Firestore 외의 별도 실시간 스토어는 제공하지 않습니다. 모든 스펙/코드를 Firestore 기준으로 유지하세요.
- 보안 규칙은 `firebase/firestore.rules`와 `firebase/storage.rules`만 관리합니다.
- 클라이언트에서 실시간 데이터를 구독할 때는 반드시 문서 경로를 사용하고, 컬렉션 구독이 필요하면 `FirestoreListView` 또는 직접 `onSnapshot(query(...))` 패턴을 구현해 주세요.

## 8. 참고 경로

- **유틸리티 구현**: `src/lib/stores/firestore.svelte.ts`
- **타입 정의**: `src/lib/types/firestore.types.ts` (`FirestoreStoreState`, `DataOperationResult` 등)
- **사용 예시**:
  - 사용자 목록 페이지: `src/routes/user/list/+page.svelte`
  - 채팅방 UI: `src/routes/chat/room/+page.svelte`
  - 시스템 통계: `src/lib/components/right-sidebar.svelte`

Firestore 유틸리티를 확장하거나 함수 시그니처를 변경할 때는 이 문서를 반드시 업데이트하고, 실제 사용처에서 브레이킹 변경이 없는지 검증하세요.
