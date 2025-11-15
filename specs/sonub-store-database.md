---
name: sonub-store-firestore
version: 2.0.0
description: Firestore 스토어/CRUD/쿼리 유틸리티 명세
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
tags:
  - firebase
  - firestore
  - store
  - crud
  - svelte5
---

# Firestore 스토어 (`src/lib/stores/firestore.svelte.ts`)

## 1. 목적

Cloud Firestore를 사용하면서 반복되는 문서 읽기/쓰기/실시간 구독 코드와 로딩/에러 상태를 단일 유틸리티로 통합합니다. Realtime Database 전용 스토어는 더 이상 사용하지 않습니다.

## 2. 상태 타입

```ts
export interface FirestoreStoreState<T> {
  data: T | null;
  loading: boolean;
  error: Error | null;
}

export interface FirestoreStore<T> extends Writable<FirestoreStoreState<T>> {
  unsubscribe: () => void;
}
```

## 3. 주요 함수

| 함수 | 설명 |
|------|------|
| `createFirestoreStore(path, defaultValue?)` | 지정 문서 경로를 `onSnapshot`으로 구독하여 `{ data, loading, error }` 상태를 반환 |
| `writeDocument(path, data, merge=false)` | 문서 전체 쓰기 (`setDoc`) |
| `updateDocument(path, updates)` | 문서 일부 필드 업데이트 (`updateDoc`) |
| `deleteDocument(path)` | 문서 삭제 |
| `addDocument(path, data)` | 컬렉션에 자동 ID 문서 추가, `{ success, id? }` 반환 |
| `readDocument(path, defaultValue?)` | `getDoc`으로 단발성 읽기 |
| `buildQuery(path, options)` / `runQuery(path, options)` | where/orderBy/limit/startAfter/endBefore 조합 지원 |
| `executeBatch(cb)` | `writeBatch`를 열어 콜백 내에서 set/update/delete 후 한 번에 커밋 |

## 4. 사용 예시

```ts
// 실시간 사용자 프로필
const profileStore = createFirestoreStore<UserData>(`users/${uid}`);

// 문서 쓰기
await writeDocument(`users/${uid}`, { displayName: 'Codex' }, true);

// 쿼리 실행
const recentUsers = await runQuery<UserData>('users', {
  orderByField: 'createdAt',
  orderDirection: 'desc',
  limit: 20
});
```

## 5. 에러 처리 규칙

- Firestore 인스턴스(`db`)가 없으면 함수는 `success: false`와 명확한 에러 메시지를 반환한다.
- `createFirestoreStore`는 `db` 미초기화 시 에러 상태를 즉시 반환하고, `unsubscribe()`는 no-op이다.

## 6. 작성 가이드

1. 경로 파싱은 `parsePath()`가 collection/document 차수를 계산해 자동으로 `doc()` 또는 `collection()`을 반환.
2. 문서 경로가 아닌 곳에서 `writeDocument` 등을 호출하면 명확한 에러를 던져 디버깅을 돕는다.
3. 모든 Firestore 경로는 kebab-case 컬렉션 규칙(`users`, `chats`, `chat-room-passwords`)을 따른다.

## 7. 참고

- 구현 파일: `src/lib/stores/firestore.svelte.ts`
- 타입 정의 참조: `src/lib/types/firestore.types.ts`
- Firestore 규칙: `firebase/firestore.rules`
