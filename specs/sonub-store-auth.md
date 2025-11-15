---
name: sonub-store-auth
version: 2.0.0
description: Firestore 기반 인증 상태/관리자 권한 스토어 명세
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-setup-firebase.md
  - sonub-user-profile-sync.md
tags:
  - firestore
  - authentication
  - store
  - svelte5
---

# AuthStore (`src/lib/stores/auth.svelte.ts`)

## 1. 책임

1. Firebase Auth 세션(`onAuthStateChanged`)을 단일 위치에서 감시
2. Firestore `users/{uid}` 문서를 최소한의 정보로 동기화 (displayName, photoUrl, languageCode)
3. Firestore `system/settings` 문서에서 `admins` 객체를 읽어 관리자 목록 유지
4. 상태(`user`, `loading`, `initialized`, `adminList`)를 Svelte runes `$state`로 노출

RTDB는 사용하지 않는다.

## 2. 상태 타입

```ts
export interface AuthState {
  user: User | null;
  loading: boolean;
  initialized: boolean;
  adminList: string[];
}
```

- `adminList`: `system/settings` 문서의 `admins` 객체 키를 배열로 변환 (true 값만 포함)
- `isAuthenticated`, `isAdmin` 등의 getter는 내부 `_state`를 참조

## 3. 프로필 동기화

`syncUserProfile(user: User)` 함수는 다음 규칙을 따른다.

| 상황 | 동작 |
|------|------|
| Firestore 문서 없음 | `setDoc('users/{uid}', { uid, displayName?, photoUrl?, languageCode? })` |
| 문서 존재 | `updateDoc`으로 비어 있는 필드만 채움 |
| photoUrl | `existingData.photoUrl?.trim()`이 falsy일 때만 Auth의 `photoURL` 저장 |
| displayName | 문서에 없고 `user.displayName`이 있을 때만 저장 |
| languageCode | 문서에 없으면 `detectBrowserLanguage()`로 저장 |

Cloud Functions가 나머지 파생 필드를 관리하므로 클라이언트는 기본 필드만 작성한다.

## 4. 관리자 권한 확인

```ts
const settingsRef = doc(db, 'system/settings');
const snapshot = await getDoc(settingsRef);
const adminsObj = snapshot.data()?.admins;
this._state.adminList = Object.keys(adminsObj ?? {}).filter(uid => adminsObj[uid] === true);
```

- 관리자 여부 확인은 `authStore.adminList.includes(currentUid)`로 판단
- Firestore 규칙(추후 작업)에서 `system/settings` 문서는 관리자만 쓰기 가능하도록 구성해야 한다.

## 5. 리스너 초기화

```ts
onAuthStateChanged(auth, async (user) => {
  this._state.user = user;
  if (user) {
    await this.syncUserProfile(user);
    await this.loadAdminList();
  } else {
    this._state.adminList = [];
  }
  this._state.loading = false;
  this._state.initialized = true;
});
```

- SSR 환경에서는 `window` 체크를 통해 리스너를 등록하지 않는다.
- `loading`은 최초 `onAuthStateChanged` 콜백이 실행될 때 `false`가 된다.

## 6. 사용 예시

```svelte
<script lang="ts">
  import { authStore } from '$lib/stores/auth.svelte';
</script>

{#if authStore.loading}
  <Spinner />
{:else if authStore.isAuthenticated}
  <p>{authStore.user?.displayName}</p>
  {#if authStore.isAdmin}
    <AdminButton />
  {/if}
{:else}
  <SignInButtons />
{/if}
```

## 7. 체크리스트

- [x] Firestore 초기화(`db`)가 없으면 경고 로그만 출력
- [x] 로그인 시점마다 관리자 목록을 새로 로드 (추후 캐싱 필요 시 store 레벨에서 처리)
- [x] `adminList`는 항상 배열로 유지 (Firestore 문서에 필드가 없을 경우 빈 배열)
- [x] Firestore 컬렉션/문서 이름은 모두 kebab-case (`users`, `system/settings`)

## 8. 참고 파일

- `src/lib/stores/auth.svelte.ts`
- `firebase/functions/src/handlers/user.handler.ts`
