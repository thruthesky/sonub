---
name: sonub-firebase-auth
version: 1.0.0
description: Firebase Authentication 연동 및 예제 구현 명세
author: Codex Agent
email: noreply@openai.com
step: 40
priority: '*'
dependencies:
  - sonub-setup-firebase.md
  - sonub-user-login.md
  - sonub-firebase-database-structure.md
tags:
  - firebase
  - authentication
  - login
  - example
---

## 1. 개요

- 이 문서는 Firebase Authentication 예제를 전담하기 위해 `sonub-setup-firebase.md`에서 분리되었습니다.
- 로그인/로그아웃 UI 및 상세 UX 요구사항은 [sonub-user-login.md](./sonub-user-login.md)를 기준으로 하며, 본 문서는 **Firebase SDK 사용 패턴**과 **샘플 코드**를 제공하는 데 집중합니다.

## 2. Authentication 예제

### 2.1 목적

- 최소한의 코드로 로그인 상태를 확인하고 Google 로그인 버튼을 제공하는 Svelte 페이지 예제를 정의합니다.
- onAuthStateChanged 구독, `signInWithPopup`, `signOut`의 기본 흐름을 보여 줍니다.

### 2.2 파일 경로

- **파일:** `src/routes/demo/auth-example/+page.svelte`

### 2.3 구현 코드

```svelte
<script lang="ts">
	import { onMount } from 'svelte';
	import { auth } from '$lib/firebase';
	import {
		GoogleAuthProvider,
		signInWithPopup,
		signOut,
		onAuthStateChanged,
		type User
	} from 'firebase/auth';

	let currentUser: User | null = null;
	let statusMessage = '로그인이 필요합니다.';
	const provider = new GoogleAuthProvider();

	async function signInWithGoogle(): Promise<void> {
		if (!auth) return;

		try {
			await signInWithPopup(auth, provider);
			statusMessage = '로그인 성공';
		} catch (error: any) {
			statusMessage = `로그인 실패: ${error.message}`;
			console.error('Firebase Auth 로그인 오류:', error);
		}
	}

	async function handleSignOut(): Promise<void> {
		if (!auth) return;

		try {
			await signOut(auth);
			statusMessage = '로그아웃 완료';
		} catch (error: any) {
			statusMessage = `로그아웃 실패: ${error.message}`;
			console.error('Firebase Auth 로그아웃 오류:', error);
		}
	}

	onMount(() => {
		if (!auth) {
			statusMessage = '브라우저 환경에서만 인증을 사용할 수 있습니다.';
			return;
		}

		const unsubscribe = onAuthStateChanged(auth, (user) => {
			currentUser = user;
			statusMessage = user ? '로그인 상태 유지 중' : '로그인이 필요합니다.';
		});

		return () => unsubscribe();
	});
</script>

<div class="auth-demo">
	<h1>Firebase Authentication Demo</h1>
	<p class="status">{statusMessage}</p>

	{#if currentUser}
		<div class="profile">
			{#if currentUser.photoURL}
				<img src={currentUser.photoURL} alt="user avatar" />
			{/if}
			<div>
				<p>{currentUser.displayName ?? currentUser.email}</p>
				<p class="uid">{currentUser.uid}</p>
			</div>
		</div>
		<button class="logout" on:click={handleSignOut}>로그아웃</button>
	{:else}
		<button class="login" on:click={signInWithGoogle}>Google 로그인</button>
	{/if}
</div>

<style>
	.auth-demo {
		max-width: 420px;
		margin: 0 auto;
		padding: 2rem;
		border: 1px solid #e5e7eb;
		border-radius: 1rem;
		background-color: #fff;
	}

	.status {
		color: #4b5563;
		margin-bottom: 1rem;
	}

	.profile {
		display: flex;
		align-items: center;
		gap: 1rem;
		margin-bottom: 1rem;
	}

	.profile img {
		width: 48px;
		height: 48px;
		border-radius: 999px;
	}

	.uid {
		font-size: 0.75rem;
		color: #9ca3af;
	}

	button {
		width: 100%;
		padding: 0.75rem 1rem;
		border-radius: 0.5rem;
		border: none;
		font-weight: 600;
		cursor: pointer;
	}

	.login {
		background-color: #4285f4;
		color: #fff;
	}

	.logout {
		background-color: #374151;
		color: #fff;
	}
</style>
```

### 2.4 주요 포인트

- `auth` 객체는 브라우저 환경에서만 초기화되므로, 서버 렌더링 시에는 null일 수 있음을 주석으로 명시합니다.
- `onAuthStateChanged` 구독은 컴포넌트 언마운트 시 반드시 해제합니다.
- Google 로그인 외 다른 공급자를 사용할 경우 `provider` 객체만 교체하면 됩니다.

│    (auth.svelte.ts)                 │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 3. user 객체 확인                   │
│    - user === null → 로그아웃 처리  │
│    - user !== null → 4단계로 진행   │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 4. syncUserProfile(user) 호출       │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 5. RTDB에서 기존 데이터 조회        │
│    GET /users/{uid}                 │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 6. 조건 검사 및 업데이트 준비       │
│    - photoUrl 조건 체크             │
│    - displayName 조건 체크          │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 7. 업데이트할 데이터가 있으면       │
│    UPDATE /users/{uid}              │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 8. Cloud Functions 자동 트리거      │
│    (createdAt, updatedAt 업데이트)  │
└─────────────────────────────────────┘
```

#### 3.2.2 시스템 컴포넌트 구성

```
┌──────────────────────────────────────────────────────────┐
│ Firebase Authentication                                   │
│ - uid, email, photoURL, displayName                      │
└───────────────────┬──────────────────────────────────────┘
                    │
                    │ onAuthStateChanged
                    │
                    ↓
┌──────────────────────────────────────────────────────────┐
│ AuthStore (auth.svelte.ts)                               │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ initializeAuthListener()                             │ │
│ │   - onAuthStateChanged 등록                          │ │
│ │   - Firestore db 인스턴스 확인                       │ │
│ └──────────────────────────────────────────────────────┘ │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ syncUserProfile(user)                                │ │
│ │   - Firestore 문서(`users/{uid}`) 읽기               │ │
│ │   - 비어 있는 필드만 조건부 업데이트                 │ │
│ └──────────────────────────────────────────────────────┘ │
└───────────────────┬──────────────────────────────────────┘
                    │
                    │ setDoc / updateDoc
                    │
                    ↓
┌──────────────────────────────────────────────────────────┐
│ Cloud Firestore                                          │
│ 컬렉션: users                                            │
│ 문서: {uid}                                              │
│   - displayName (클라이언트)                             │
│   - photoUrl (클라이언트)                                │
│   - languageCode (클라이언트)                            │
│   - createdAt (Cloud Functions)                          │
│   - updatedAt (Cloud Functions)                          │
│   - displayNameLowerCase 등 파생 필드 (Cloud Functions)  │
└──────────────────────────────────────────────────────────┘
```

### 3.3 상세 구현

#### 3.3.1 파일 위치 및 수정 사항

**파일:** `src/lib/stores/auth.svelte.ts`

#### 3.3.2 Firestore API 사용

```typescript
import { doc, getDoc, setDoc, updateDoc } from 'firebase/firestore';
```

- 문서가 없으면 `setDoc(doc(db, 'users', user.uid), { uid, ...updates })`
- 문서가 있으면 `updateDoc()`으로 변경된 필드만 병합

#### 3.3.3 syncUserProfile() 메서드 개요

**상세 내용:**

`syncUserProfile()` 메서드의 전체 구현, 동작 원리, 사용 예제는 [sonub-store-auth.md](./sonub-store-auth.md)를 참조하세요.

**동기화 규칙 (간략):**
- **photoUrl**: Firestore에 값이 없거나 공백일 때만 Auth photoURL 저장
- **displayName**: 문서에 없을 때만 Auth displayName 저장
- **languageCode**: 문서에 없으면 브라우저 언어 코드 저장
- **덮어쓰기 방지**: 사용자가 수정한 프로필은 절대 덮어쓰지 않음
- **병합 업데이트**: 존재 여부에 따라 `setDoc(..., { merge: true })` 또는 `updateDoc` 사용

**핵심 로직 스케치:**
```typescript
const userRef = doc(db, `users/${user.uid}`);
const snapshot = await getDoc(userRef);
const existing = snapshot.data() ?? {};
const updates: Record<string, any> = {};

if (!existing.photoUrl?.trim() && user.photoURL) {
  updates.photoUrl = user.photoURL;
}
if (!existing.displayName && user.displayName) {
  updates.displayName = user.displayName;
}
if (!existing.languageCode) {
  updates.languageCode = detectBrowserLanguage();
}

if (Object.keys(updates).length > 0) {
  if (snapshot.exists()) {
    await updateDoc(userRef, updates);
  } else {
    await setDoc(userRef, { uid: user.uid, ...updates });
  }
}
```

#### 3.3.4 onAuthStateChanged 리스너 통합

AuthStore에서 `onAuthStateChanged` 리스너가 자동으로 `syncUserProfile()`을 호출합니다:

```typescript
onAuthStateChanged(auth, async (user) => {
	this._state.user = user;

	if (user) {
		await this.syncUserProfile(user);
		await this.loadAdminList();
	}

	this._state.loading = false;
	this._state.initialized = true;
});
```

**자세한 내용은 [sonub-store-auth.md](./sonub-store-auth.md)를 참조하세요.**

### 3.4 동작 시나리오

#### 3.4.1 신규 사용자 최초 로그인

```
1. 사용자가 "Google 로그인" 클릭
2. signInWithGoogle() 실행 → Firebase Auth 로그인 성공
3. onAuthStateChanged 트리거
   - user.uid = "abc123"
   - user.photoURL = "https://lh3.googleusercontent.com/..."
   - user.displayName = "홍길동"
4. syncUserProfile(user) 실행
   - GET /users/abc123 → null (신규 사용자)
   - existingData = {}
   - photoUrl 조건: !undefined && true → ✅ 저장
   - displayName 조건: !undefined && true → ✅ 저장
   - UPDATE /users/abc123
     {
       photoUrl: "https://lh3.googleusercontent.com/...",
       displayName: "홍길동"
     }
5. Cloud Functions 트리거
   - createdAt: 1699999999999
   - updatedAt: 1699999999999 자동 추가
```

#### 3.4.2 기존 사용자 재로그인

```
1. 사용자가 이미 RTDB에 프로필 저장되어 있음
   /users/abc123
   {
     photoUrl: "https://lh3.googleusercontent.com/old.jpg",
     displayName: "홍길동",
     createdAt: 1699999999999,
     updatedAt: 1699999999999
   }
2. onAuthStateChanged 트리거
3. syncUserProfile(user) 실행
   - GET /users/abc123 → 기존 데이터 조회
   - photoUrl 조건: !"https://...".trim() → ❌ 저장 안 함
   - displayName 조건: !"홍길동" → ❌ 저장 안 함
   - updates = {} (빈 객체)
   - "동기화할 프로필 정보 없음" 로그 출력
   - RTDB 업데이트 안 함 ✅
```

#### 3.4.3 photoUrl이 공백인 경우

```
1. 사용자가 수동으로 photoUrl을 삭제하거나 공백으로 설정
   /users/abc123
   {
     photoUrl: "   ",  // 공백만 있음
     displayName: "홍길동"
   }
2. 페이지 리프레시
3. syncUserProfile(user) 실행
   - photoUrl 조건: !"   ".trim() → !("") → true ✅
   - user.photoURL이 있으면 복원됨
   - UPDATE /users/abc123
     {
       photoUrl: "https://lh3.googleusercontent.com/..."
     }
```

#### 3.4.4 사용자가 프로필 사진을 변경한 경우

```
1. 사용자가 앱 내에서 프로필 사진을 커스텀 이미지로 변경
   /users/abc123
   {
     photoUrl: "https://custom-cdn.com/my-photo.jpg",
     displayName: "홍길동"
   }
2. 페이지 리프레시
3. syncUserProfile(user) 실행
   - photoUrl 조건: !"https://custom-cdn.com/my-photo.jpg".trim()
   - → !("https://...") → false ❌
   - 덮어쓰지 않음 ✅ (사용자의 커스텀 사진 보호)
```

### 3.5 전체 소스 코드

**파일:** `src/lib/stores/auth.svelte.ts`

```typescript
/**
 * 인증 상태 관리 스토어
 *
 * Svelte 5의 runes를 사용하여 Firebase Authentication 상태를 전역으로 관리합니다.
 */

import { auth, rtdb } from '$lib/firebase';
import { onAuthStateChanged, type User } from 'firebase/auth';
import { ref, get, update } from 'firebase/database';

/**
 * 인증 상태 타입 정의
 */
export interface AuthState {
	user: User | null; // 현재 로그인한 사용자 (null이면 비로그인)
	loading: boolean; // 인증 상태 확인 중 여부
	initialized: boolean; // 인증 시스템 초기화 완료 여부
	adminList: string[]; // 관리자 UID 배열 (/system/settings/admins)
}

/**
 * 인증 상태를 관리하는 클래스
 */
class AuthStore {
	// Svelte 5 runes를 사용한 반응형 상태
	private _state = $state<AuthState>({
		user: null,
		loading: true,
		initialized: false,
		adminList: []
	});

	constructor() {
		// 브라우저 환경에서만 Firebase 인증 상태 리스너 등록
		if (typeof window !== 'undefined') {
			this.initializeAuthListener();
		} else {
			// 서버 환경에서는 초기화만 완료 처리
			this._state.loading = false;
			this._state.initialized = true;
		}
	}

			// displayName: 없을 때만 동기화
			if (!existingData.displayName && user.displayName) {
				updates.displayName = user.displayName;
				console.log('displayName 동기화:', user.displayName);
			}

			// 업데이트할 항목이 있으면 RTDB에 저장
			if (Object.keys(updates).length > 0) {
				await update(userRef, updates);
				console.log('사용자 프로필 동기화 완료:', updates);
			} else {
				console.log('동기화할 프로필 정보 없음');
			}
		} catch (error) {
			console.error('사용자 프로필 동기화 실패:', error);
		}
	}

	/**
	 * Firebase 인증 상태 변경 리스너 초기화
	 */
	private initializeAuthListener() {
		if (!auth) {
			console.warn('Firebase Auth가 초기화되지 않았습니다.');
			this._state.loading = false;
			this._state.initialized = true;
			return;
		}

		onAuthStateChanged(auth, async (user) => {
			this._state.user = user;

			// 사용자 로그인 시 프로필 동기화 및 관리자 목록 로드
			if (user) {
				console.log('사용자 로그인됨:', user.uid);

				// Firebase Auth의 photoURL, displayName을 RTDB에 동기화
				await this.syncUserProfile(user);

				// 관리자 목록 로드
				await this.loadAdminList();
			} else {
				console.log('사용자 로그아웃됨');
				this._state.adminList = [];
			}

			this._state.loading = false;
			this._state.initialized = true;
		});
	}

	/**
	 * 현재 인증 상태 가져오기
	 */
	get state(): AuthState {
		return this._state;
	}

	/**
	 * 현재 사용자 가져오기
	 */
	get user(): User | null {
		return this._state.user;
	}

	/**
	 * 로딩 상태 가져오기
	 */
	get loading(): boolean {
		return this._state.loading;
	}

	/**
	 * 초기화 상태 가져오기
	 */
	get initialized(): boolean {
		return this._state.initialized;
	}

	/**
	 * 사용자가 로그인했는지 확인
	 */
	get isAuthenticated(): boolean {
		return this._state.user !== null;
	}

	/**
	 * 현재 사용자가 관리자인지 확인
	 * 로그인되어 있고, 관리자 목록에 포함되어 있으면 true
	 */
	get isAdmin(): boolean {
		return this.isAuthenticated && this._state.adminList.includes(this._state.user?.uid ?? '');
	}

	/**
	 * 관리자 목록 가져오기
	 */
	get adminList(): string[] {
		return this._state.adminList;
	}
}

/**
 * 인증 스토어 인스턴스 export
 *
 * 사용 예:
 * import { authStore } from '$lib/stores/auth.svelte';
 *
 * const user = authStore.user;
 * const isLoggedIn = authStore.isAuthenticated;
 */
export const authStore = new AuthStore();
```

### 3.6 주요 설계 결정 사항

#### 3.6.1 왜 onAuthStateChanged를 사용하는가?

**대안 검토:**
- **로그인 함수에서 직접 동기화**: `signInWithGoogle()` 내부에서 RTDB 저장
- **별도 유틸리티 함수**: 로그인 후 수동으로 `syncProfile()` 호출
- **Cloud Functions**: Auth Triggers 사용

**onAuthStateChanged 선택 이유:**
1. **중앙 집중화**: 모든 로그인 경로(Google, Apple, Email 등)에서 자동 동작
2. **새로고침 대응**: 페이지 리프레시 시에도 자동 실행
3. **상태 일관성**: Auth 상태와 RTDB 데이터가 항상 동기화됨
4. **유지보수성**: 한 곳에서만 관리, 로그인 함수 수정 불필요

#### 3.6.2 왜 update()를 사용하는가?

**set() vs update() 비교:**

```typescript
// ❌ 잘못된 방법: set() 사용
await set(userRef, {
    photoUrl: user.photoURL,
    displayName: user.displayName
});
// 결과: 기존 필드(createdAt, 커스텀 필드 등) 모두 삭제됨!

// ✅ 올바른 방법: update() 사용
await update(userRef, {
    photoUrl: user.photoURL,
    displayName: user.displayName
});
// 결과: 기존 필드 보존, 지정한 필드만 병합됨
```

**update() 선택 이유:**
- 기존 필드 보존 (createdAt, 커스텀 필드 등)
- 부분 업데이트로 네트워크 효율성 향상
- Cloud Functions 트리거 최소화

#### 3.6.3 왜 trim()을 사용하는가?

**photoUrl에만 trim() 적용:**

```typescript
// photoUrl: 공백 문자열도 "값 없음"으로 간주
if (!existingData.photoUrl?.trim() && user.photoURL)

// displayName: 빈 문자열만 "값 없음"으로 간주
if (!existingData.displayName && user.displayName)
```

**이유:**
- **photoUrl**: URL은 공백만 있으면 무효한 값 → 복원 필요
- **displayName**: 사용자가 의도적으로 이름을 지울 수 있음 → 복원 안 함

### 3.7 테스트 및 검증

#### 3.7.1 수동 테스트 체크리스트

1. **신규 사용자 테스트**
   - [ ] Google 로그인 → RTDB에 photoUrl, displayName 저장 확인
   - [ ] Apple 로그인 → RTDB에 photoUrl, displayName 저장 확인
   - [ ] Firebase Console에서 `/users/{uid}` 확인

2. **기존 사용자 테스트**
   - [ ] 이미 RTDB에 데이터가 있는 사용자 로그인
   - [ ] 기존 photoUrl 덮어쓰지 않는지 확인
   - [ ] 기존 displayName 덮어쓰지 않는지 확인

3. **공백 데이터 테스트**
   - [ ] Firebase Console에서 photoUrl을 `"   "` (공백)으로 설정
   - [ ] 페이지 리프레시
   - [ ] photoUrl이 Auth photoURL로 복원되는지 확인

4. **페이지 리프레시 테스트**
   - [ ] 로그인 상태에서 새로고침
   - [ ] 불필요한 업데이트가 발생하지 않는지 콘솔 로그 확인
   - [ ] "동기화할 프로필 정보 없음" 메시지 확인

5. **네트워크 오류 테스트**
   - [ ] 개발자 도구에서 오프라인 모드 활성화
   - [ ] 페이지 리프레시
   - [ ] 에러 로그 확인, 앱이 중단되지 않는지 확인

#### 3.7.2 콘솔 로그 예시

**신규 사용자 로그인:**
```
사용자 로그인됨: abc123
photoUrl 동기화: https://lh3.googleusercontent.com/...
displayName 동기화: 홍길동
사용자 프로필 동기화 완료: { photoUrl: "https://...", displayName: "홍길동" }
관리자 목록 로드됨: []
```

**기존 사용자 로그인:**
```
사용자 로그인됨: abc123
동기화할 프로필 정보 없음
관리자 목록 로드됨: []
```

### 3.8 향후 확장 가능성

#### 3.8.1 추가 필드 동기화

필요 시 다음 필드도 동기화 가능:

```typescript
// phoneNumber 동기화 (옵션)
if (!existingData.phoneNumber && user.phoneNumber) {
    updates.phoneNumber = user.phoneNumber;
}

// emailVerified 상태 동기화 (옵션)
if (user.emailVerified !== undefined) {
    updates.emailVerified = user.emailVerified;
}
```

#### 3.8.2 양방향 동기화

현재는 **Auth → RTDB 단방향 동기화**만 구현됨.
필요 시 **RTDB → Auth 동기화**도 가능:

```typescript
// RTDB의 displayName을 Auth에 동기화
if (existingData.displayName && existingData.displayName !== user.displayName) {
    await updateProfile(user, {
        displayName: existingData.displayName
    });
}
```

#### 3.8.3 프로필 변경 이벤트

사용자가 프로필을 수정할 때 이벤트 발행:

```typescript
// 프로필 변경 이벤트 타입
export interface ProfileUpdateEvent {
    uid: string;
    before: Partial<UserProfile>;
    after: Partial<UserProfile>;
    timestamp: number;
}

// 이벤트 저장
await set(ref(rtdb, `events/profile-updates/${Date.now()}`), {
    uid: user.uid,
    before: { photoUrl: existingData.photoUrl },
    after: { photoUrl: user.photoURL },
    timestamp: Date.now()
});
```

## 4. 테스트 및 참고 문서

- [sonub-user-login.md](./sonub-user-login.md): 실제 로그인 UI/UX 흐름 정의.
- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 앱 초기화 및 환경 변수 설정.
- 수동 검증 체크리스트:
  1. 로그인 성공 후 사용자 정보가 즉시 UI에 표시되는지 확인
  2. 새로 고침 시 로그인 상태가 유지되는지 확인
  3. 로그아웃 후 `currentUser`가 null이 되는지 확인
  4. **신규**: 신규 사용자 로그인 시 RTDB에 photoUrl, displayName 자동 저장 확인
  5. **신규**: 기존 사용자 로그인 시 기존 프로필 덮어쓰지 않는지 확인
  6. **신규**: photoUrl이 공백일 때 Auth photoURL로 복원되는지 확인
	/**
	 * Firestore에서 관리자 목록 로드
	 *
	 * `system/settings` 문서 구조:
	 * ```json
	 * { "admins": { "uid-user1": true, "uid-user2": true } }
	 * ```
	 */
	private async loadAdminList() {
		if (!db) {
			console.warn('Firebase Firestore가 초기화되지 않았습니다.');
			return;
		}

		try {
			const settingsRef = doc(db, 'system/settings');
			const snapshot = await getDoc(settingsRef);
			const adminsObj = snapshot.data()?.admins;

			this._state.adminList =
				adminsObj && typeof adminsObj === 'object'
					? Object.keys(adminsObj).filter((uid) => adminsObj[uid] === true)
					: [];
		} catch (error) {
			console.error('관리자 목록 로드 실패:', error);
			this._state.adminList = [];
		}
	}

	/**
	 * Firebase Auth 사용자 프로필을 Firestore에 동기화
	 *
	 * 동기화 규칙:
	 * - photoUrl: 문서에 값이 없거나 공백일 때만 Auth photoURL 저장
	 * - displayName: 문서에 없을 때만 Auth displayName 저장
	 * - languageCode: 문서에 없으면 브라우저 언어 저장
	 */
	private async syncUserProfile(user: User) {
		if (!db) {
			console.warn('Firebase Firestore가 초기화되지 않았습니다.');
			return;
		}

		try {
			const userRef = doc(db, `users/${user.uid}`);
			const snapshot = await getDoc(userRef);
			const existingData = snapshot.data() || {};
			const updates: Record<string, any> = {};

			if (!existingData.photoUrl?.trim() && user.photoURL) {
				updates.photoUrl = user.photoURL;
			}
			if (!existingData.displayName && user.displayName) {
				updates.displayName = user.displayName;
			}
			if (!existingData.languageCode) {
				updates.languageCode = this.detectBrowserLanguage();
			}

			if (Object.keys(updates).length > 0) {
				if (snapshot.exists()) {
					await updateDoc(userRef, updates);
				} else {
					await setDoc(userRef, { uid: user.uid, ...updates });
				}
			}
		} catch (error) {
			console.error('사용자 프로필 동기화 실패:', error);
		}
	}
