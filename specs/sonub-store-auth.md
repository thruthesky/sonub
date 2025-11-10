---
name: sonub-store-auth
version: 1.0.0
description: Firebase Authentication 상태 관리 스토어 - onAuthStateChanged 리스너, 프로필 동기화, 관리자 권한 관리
author: Claude Code
email: noreply@anthropic.com
homepage: https://github.com/thruthesky/
license: GPL-3.0
created: 2025-11-10
updated: 2025-11-10
step: 45
priority: "***"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-auth.md
  - sonub-firebase-realtime-database.md
  - sonub-firebase-database-structure.md
tags:
  - firebase
  - authentication
  - auth
  - svelte5
  - store
  - state-management
  - rtdb
  - admin
  - profile-sync
---

# 인증 스토어 (AuthStore)

## 1. 개요

### 1.1 목적과 배경

#### 1.1.1 해결하려는 문제

Firebase Authentication을 사용하는 SvelteKit 애플리케이션에서 다음과 같은 문제가 발생합니다:

1. **전역 인증 상태 관리**: 모든 컴포넌트에서 일관된 인증 상태 접근 필요
2. **Auth와 RTDB 프로필 동기화**: Google/Apple 로그인 시 Auth의 photoURL, displayName을 RTDB에 자동 동기화
3. **관리자 권한 확인**: 특정 사용자가 관리자인지 확인하는 중앙 로직 필요
4. **세션 상태 감지**: Firebase Auth의 세션 변경을 실시간으로 감지하여 UI 업데이트

**이 스토어가 해결하는 문제:**
- 중복된 `onAuthStateChanged` 리스너 제거 (단일 리스너)
- Auth와 RTDB 데이터를 자동으로 동기화 (로그인 시 한 번만)
- 관리자 권한 확인을 중앙화하여 RTDB 조회
- 로딩/초기화 상태를 통해 깜빡임 없는 UX 제공

#### 1.1.2 핵심 기능

1. **자동 세션 감지**
   - Firebase Auth의 `onAuthStateChanged` 리스너를 자동으로 등록
   - 로그인/로그아웃 시 상태 자동 업데이트

2. **프로필 동기화 (syncUserProfile)**
   - Firebase Auth → RTDB 단방향 동기화
   - 조건부 업데이트: photoUrl, displayName이 비어있을 때만 저장
   - 사용자가 수정한 프로필은 덮어쓰지 않음

3. **관리자 권한 관리**
   - `/system/settings/admins` 경로에서 관리자 UID 목록 로드
   - `isAdmin` getter로 현재 사용자가 관리자인지 확인
   - UI에서 관리자 전용 기능 제어

4. **로딩 및 초기화 상태**
   - `loading`: Firebase Auth 초기화 중인지 표시
   - `initialized`: 최소 한 번 이상 Auth 상태를 확인했는지 표시
   - SSR 환경에서 안전한 동작 보장

### 1.2 파일 위치

- **파일**: `src/lib/stores/auth.svelte.ts`
- **타입 정의**: 동일 파일 내 `AuthState` interface
- **의존성**: `$lib/firebase.ts`, Firebase SDK
- **환경**: Svelte 5, TypeScript

### 1.3 사전 요구사항

- ✅ Firebase JS SDK 설치 ([sonub-setup-firebase.md](./sonub-setup-firebase.md))
- ✅ Firebase Authentication 설정 완료
- ✅ Firebase Realtime Database 설정 완료
- ✅ Svelte 5 runes 이해 ($state, $derived)
- ✅ TypeScript 기본 지식

## 2. 타입 정의

### 2.1 AuthState Interface

```typescript
export interface AuthState {
	user: User | null;
	loading: boolean;
	initialized: boolean;
	adminList: string[];
}
```

**필드 설명:**

| 필드 | 타입 | 설명 |
|------|------|------|
| `user` | `User \| null` | Firebase Auth의 현재 사용자 객체. 비로그인 시 `null` |
| `loading` | `boolean` | Auth 초기화 중이면 `true`. 첫 상태 확인 완료 후 `false` |
| `initialized` | `boolean` | `onAuthStateChanged`가 최소 1회 실행되었으면 `true` |
| `adminList` | `string[]` | `/system/settings/admins`에서 로드한 관리자 UID 배열 |

**User 타입 (Firebase SDK):**

Firebase의 `User` 타입은 다음과 같은 주요 속성을 포함합니다:

```typescript
interface User {
	uid: string;
	email: string | null;
	displayName: string | null;
	photoURL: string | null;
	emailVerified: boolean;
	// ... 기타 속성
}
```

## 3. AuthStore 클래스 구현

### 3.1 클래스 구조

```typescript
class AuthStore {
	private _state = $state<AuthState>({
		user: null,
		loading: true,
		initialized: false,
		adminList: []
	});

	constructor() {
		this.initializeAuthListener();
	}

	// Getters
	get state(): Readonly<AuthState> { /* ... */ }
	get user(): User | null { /* ... */ }
	get loading(): boolean { /* ... */ }
	get initialized(): boolean { /* ... */ }
	get isAuthenticated(): boolean { /* ... */ }
	get isAdmin(): boolean { /* ... */ }
	get adminList(): string[] { /* ... */ }

	// Private methods
	private initializeAuthListener(): void { /* ... */ }
	private async syncUserProfile(user: User): Promise<void> { /* ... */ }
	private async loadAdminList(): Promise<void> { /* ... */ }
}

export const authStore = new AuthStore();
```

### 3.2 생성자 및 초기화

```typescript
constructor() {
	this.initializeAuthListener();
}
```

**역할:**
- 클래스 인스턴스 생성 시 `initializeAuthListener()` 자동 호출
- Firebase Auth 리스너를 즉시 등록하여 세션 감지 시작

### 3.3 Getters

#### 3.3.1 state

```typescript
get state(): Readonly<AuthState> {
	return this._state;
}
```

**용도:** 전체 상태 객체를 읽기 전용으로 반환

#### 3.3.2 user

```typescript
get user(): User | null {
	return this._state.user;
}
```

**용도:** 현재 로그인한 사용자 객체 반환. 비로그인 시 `null`

#### 3.3.3 loading

```typescript
get loading(): boolean {
	return this._state.loading;
}
```

**용도:** Auth 초기화 중인지 확인. UI에서 로딩 스피너 표시에 사용

#### 3.3.4 initialized

```typescript
get initialized(): boolean {
	return this._state.initialized;
}
```

**용도:** Auth 상태가 최소 한 번 확인되었는지 표시. 깜빡임 방지

#### 3.3.5 isAuthenticated

```typescript
get isAuthenticated(): boolean {
	return this._state.user !== null;
}
```

**용도:** 사용자가 로그인했는지 boolean으로 반환

#### 3.3.6 isAdmin

```typescript
get isAdmin(): boolean {
	return this.isAuthenticated && this._state.adminList.includes(this._state.user?.uid ?? '');
}
```

**용도:** 현재 사용자가 관리자인지 확인

**로직:**
1. 로그인 상태 확인 (`isAuthenticated`)
2. 사용자 UID가 `adminList`에 포함되어 있는지 확인
3. 둘 다 true일 때만 관리자로 인정

#### 3.3.7 adminList

```typescript
get adminList(): string[] {
	return this._state.adminList;
}
```

**용도:** 전체 관리자 UID 목록 반환

### 3.4 Private Methods

#### 3.4.1 initializeAuthListener()

```typescript
private initializeAuthListener(): void {
	if (typeof window === 'undefined' || !auth) {
		console.warn('[AuthStore] SSR 환경이거나 Firebase Auth가 초기화되지 않았습니다.');
		this._state.loading = false;
		this._state.initialized = true;
		return;
	}

	onAuthStateChanged(auth, async (user) => {
		console.log('[AuthStore] onAuthStateChanged 트리거됨:', user ? user.uid : 'null');

		this._state.user = user;

		if (user) {
			await this.syncUserProfile(user);
			await this.loadAdminList();
		}

		this._state.loading = false;
		this._state.initialized = true;
	});
}
```

**역할:**
1. SSR 환경 체크: `window` 객체가 없으면 실행 중단
2. Firebase Auth 인스턴스 확인: `auth`가 없으면 경고 로그
3. `onAuthStateChanged` 리스너 등록:
   - 로그인 시: `syncUserProfile()`, `loadAdminList()` 자동 실행
   - 로그아웃 시: `user`를 `null`로 설정
4. 초기화 완료 후 `loading`을 `false`로 변경

**중요 사항:**
- 이 메서드는 단 한 번만 실행됩니다 (생성자에서 호출)
- Firebase가 세션을 감지할 때마다 자동으로 콜백 실행
- 비동기 작업 (`syncUserProfile`, `loadAdminList`)이 완료될 때까지 대기

#### 3.4.2 syncUserProfile(user: User)

```typescript
private async syncUserProfile(user: User): Promise<void> {
	if (!rtdb) {
		console.warn('[AuthStore] RTDB가 초기화되지 않았습니다.');
		return;
	}

	try {
		const userRef = ref(rtdb, `users/${user.uid}`);
		const snapshot = await get(userRef);
		const existingData = snapshot.val() || {};

		const updates: Record<string, any> = {};

		// photoUrl 동기화: RTDB에 값이 없거나 공백이면 Auth photoURL 저장
		if (!existingData.photoUrl?.trim() && user.photoURL) {
			updates.photoUrl = user.photoURL;
		}

		// displayName 동기화: RTDB에 값이 없으면 Auth displayName 저장
		if (!existingData.displayName && user.displayName) {
			updates.displayName = user.displayName;
		}

		// 업데이트할 내용이 있으면 RTDB에 저장
		if (Object.keys(updates).length > 0) {
			await update(userRef, updates);
			console.log('[AuthStore] 프로필 동기화 완료:', updates);
		}
	} catch (error) {
		console.error('[AuthStore] 프로필 동기화 실패:', error);
	}
}
```

**역할:**
- Firebase Auth의 프로필 정보를 RTDB `/users/{uid}`에 조건부 저장
- 사용자가 이미 수정한 프로필은 덮어쓰지 않음

**동기화 규칙:**

| RTDB 필드 | Auth 필드 | 조건 |
|-----------|-----------|------|
| `photoUrl` | `photoURL` | RTDB 값이 없거나 공백(`!existingData.photoUrl?.trim()`)일 때만 |
| `displayName` | `displayName` | RTDB 값이 없을 때만 |

**동작 예시:**

**시나리오 1: 신규 사용자 (RTDB에 데이터 없음)**
```typescript
// Firebase Auth 데이터
user.uid = 'abc123'
user.photoURL = 'https://example.com/photo.jpg'
user.displayName = 'John Doe'

// RTDB 기존 데이터
existingData = {} // 빈 객체

// 결과: 둘 다 저장됨
updates = {
	photoUrl: 'https://example.com/photo.jpg',
	displayName: 'John Doe'
}
```

**시나리오 2: 기존 사용자 (프로필 수정함)**
```typescript
// Firebase Auth 데이터
user.photoURL = 'https://example.com/photo.jpg'
user.displayName = 'John Doe'

// RTDB 기존 데이터
existingData = {
	photoUrl: 'https://example.com/custom.jpg',
	displayName: 'Custom Name'
}

// 결과: 아무것도 저장되지 않음
updates = {} // 빈 객체
```

**시나리오 3: photoUrl이 공백인 경우**
```typescript
// RTDB 기존 데이터
existingData = {
	photoUrl: '   ', // 공백
	displayName: 'John Doe'
}

// 결과: photoUrl만 복원됨
updates = {
	photoUrl: 'https://example.com/photo.jpg'
}
```

**주의사항:**
- `set()` 대신 `update()` 사용: 기존 필드 보존
- `trim()` 체크: 공백 문자열도 "비어있음"으로 간주
- 에러 발생 시에도 앱이 중단되지 않도록 try-catch 처리

#### 3.4.3 loadAdminList()

```typescript
private async loadAdminList(): Promise<void> {
	if (!rtdb) {
		console.warn('[AuthStore] RTDB가 초기화되지 않았습니다.');
		return;
	}

	try {
		const adminsRef = ref(rtdb, '/system/settings/admins');
		const snapshot = await get(adminsRef);
		const adminData = snapshot.val();

		if (adminData && typeof adminData === 'object') {
			// adminData가 객체일 경우 키 배열로 변환
			this._state.adminList = Object.keys(adminData);
		} else if (Array.isArray(adminData)) {
			// adminData가 배열일 경우 그대로 사용
			this._state.adminList = adminData;
		} else {
			this._state.adminList = [];
		}

		console.log('[AuthStore] 관리자 목록 로드 완료:', this._state.adminList);
	} catch (error) {
		console.error('[AuthStore] 관리자 목록 로드 실패:', error);
		this._state.adminList = [];
	}
}
```

**역할:**
- RTDB `/system/settings/admins` 경로에서 관리자 UID 목록 로드
- 다양한 데이터 형식 지원 (객체 또는 배열)

**RTDB 데이터 구조 예시:**

**옵션 1: 객체 형식 (추천)**
```json
{
  "system": {
    "settings": {
      "admins": {
        "abc123": true,
        "def456": true,
        "ghi789": true
      }
    }
  }
}
```

**옵션 2: 배열 형식**
```json
{
  "system": {
    "settings": {
      "admins": ["abc123", "def456", "ghi789"]
    }
  }
}
```

**변환 로직:**
- 객체 형식: `Object.keys(adminData)` → `["abc123", "def456", "ghi789"]`
- 배열 형식: 그대로 사용
- 없거나 잘못된 형식: 빈 배열 `[]`

**보안 고려사항:**
- 이 데이터는 클라이언트에서 읽을 수 있으므로 UI 제어에만 사용
- 실제 권한 검증은 반드시 Firebase Security Rules 또는 Cloud Functions에서 수행
- 클라이언트의 `isAdmin` 체크는 UX 개선용일 뿐, 보안 메커니즘이 아님

## 4. 전체 소스 코드

**파일 경로:** `src/lib/stores/auth.svelte.ts`

```typescript
import { onAuthStateChanged, type User } from 'firebase/auth';
import { auth, rtdb } from '$lib/firebase';
import { ref, get, update } from 'firebase/database';

/**
 * 인증 상태 인터페이스
 */
export interface AuthState {
	user: User | null;
	loading: boolean;
	initialized: boolean;
	adminList: string[];
}

/**
 * 인증 상태 관리 스토어
 *
 * Firebase Authentication의 세션 상태를 관리하고,
 * RTDB와의 프로필 동기화 및 관리자 권한 확인 기능을 제공합니다.
 */
class AuthStore {
	private _state = $state<AuthState>({
		user: null,
		loading: true,
		initialized: false,
		adminList: []
	});

	constructor() {
		this.initializeAuthListener();
	}

	/**
	 * 전체 상태 반환 (읽기 전용)
	 */
	get state(): Readonly<AuthState> {
		return this._state;
	}

	/**
	 * 현재 로그인한 사용자 객체 반환
	 */
	get user(): User | null {
		return this._state.user;
	}

	/**
	 * Auth 초기화 중인지 확인
	 */
	get loading(): boolean {
		return this._state.loading;
	}

	/**
	 * Auth 상태가 최소 한 번 확인되었는지 확인
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
	 */
	get isAdmin(): boolean {
		return this.isAuthenticated && this._state.adminList.includes(this._state.user?.uid ?? '');
	}

	/**
	 * 관리자 UID 목록 반환
	 */
	get adminList(): string[] {
		return this._state.adminList;
	}

	/**
	 * Firebase Auth 리스너 초기화
	 *
	 * onAuthStateChanged를 등록하여 로그인/로그아웃 시
	 * 자동으로 상태를 업데이트하고 프로필을 동기화합니다.
	 */
	private initializeAuthListener(): void {
		if (typeof window === 'undefined' || !auth) {
			console.warn('[AuthStore] SSR 환경이거나 Firebase Auth가 초기화되지 않았습니다.');
			this._state.loading = false;
			this._state.initialized = true;
			return;
		}

		onAuthStateChanged(auth, async (user) => {
			console.log('[AuthStore] onAuthStateChanged 트리거됨:', user ? user.uid : 'null');

			this._state.user = user;

			if (user) {
				await this.syncUserProfile(user);
				await this.loadAdminList();
			}

			this._state.loading = false;
			this._state.initialized = true;
		});
	}

	/**
	 * Firebase Auth → RTDB 프로필 동기화
	 *
	 * @param user - Firebase Auth User 객체
	 *
	 * 동기화 규칙:
	 * - photoUrl: RTDB에 값이 없거나 공백이면 Auth photoURL 저장
	 * - displayName: RTDB에 값이 없으면 Auth displayName 저장
	 *
	 * 사용자가 이미 수정한 프로필은 덮어쓰지 않습니다.
	 */
	private async syncUserProfile(user: User): Promise<void> {
		if (!rtdb) {
			console.warn('[AuthStore] RTDB가 초기화되지 않았습니다.');
			return;
		}

		try {
			const userRef = ref(rtdb, `users/${user.uid}`);
			const snapshot = await get(userRef);
			const existingData = snapshot.val() || {};

			const updates: Record<string, any> = {};

			// photoUrl 동기화: RTDB에 값이 없거나 공백이면 Auth photoURL 저장
			if (!existingData.photoUrl?.trim() && user.photoURL) {
				updates.photoUrl = user.photoURL;
			}

			// displayName 동기화: RTDB에 값이 없으면 Auth displayName 저장
			if (!existingData.displayName && user.displayName) {
				updates.displayName = user.displayName;
			}

			// 업데이트할 내용이 있으면 RTDB에 저장
			if (Object.keys(updates).length > 0) {
				await update(userRef, updates);
				console.log('[AuthStore] 프로필 동기화 완료:', updates);
			}
		} catch (error) {
			console.error('[AuthStore] 프로필 동기화 실패:', error);
		}
	}

	/**
	 * 관리자 목록 로드
	 *
	 * RTDB `/system/settings/admins` 경로에서
	 * 관리자 UID 목록을 가져옵니다.
	 *
	 * 데이터 형식:
	 * - 객체: { "uid1": true, "uid2": true } → ["uid1", "uid2"]
	 * - 배열: ["uid1", "uid2"] → 그대로 사용
	 */
	private async loadAdminList(): Promise<void> {
		if (!rtdb) {
			console.warn('[AuthStore] RTDB가 초기화되지 않았습니다.');
			return;
		}

		try {
			const adminsRef = ref(rtdb, '/system/settings/admins');
			const snapshot = await get(adminsRef);
			const adminData = snapshot.val();

			if (adminData && typeof adminData === 'object') {
				// adminData가 객체일 경우 키 배열로 변환
				this._state.adminList = Object.keys(adminData);
			} else if (Array.isArray(adminData)) {
				// adminData가 배열일 경우 그대로 사용
				this._state.adminList = adminData;
			} else {
				this._state.adminList = [];
			}

			console.log('[AuthStore] 관리자 목록 로드 완료:', this._state.adminList);
		} catch (error) {
			console.error('[AuthStore] 관리자 목록 로드 실패:', error);
			this._state.adminList = [];
		}
	}
}

export const authStore = new AuthStore();
```

## 5. 사용 예제

### 5.1 기본 사용법

#### 5.1.1 컴포넌트에서 사용

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
</script>

{#if authStore.loading}
	<p>로딩 중...</p>
{:else if authStore.isAuthenticated}
	<div>
		<p>환영합니다, {authStore.user?.displayName || '사용자'}님!</p>
		<p>이메일: {authStore.user?.email}</p>

		{#if authStore.isAdmin}
			<a href="/admin">관리자 페이지</a>
		{/if}
	</div>
{:else}
	<a href="/user/login">로그인</a>
{/if}
```

#### 5.1.2 $effect에서 사용

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';

	$effect(() => {
		if (authStore.initialized && !authStore.isAuthenticated) {
			goto('/user/login');
		}
	});
</script>
```

### 5.2 조건부 렌더링

#### 5.2.1 로그인 상태에 따른 UI

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
</script>

<nav>
	<a href="/">홈</a>

	{#if authStore.isAuthenticated}
		<a href="/my/profile">내 프로필</a>
		<a href="/my/reports">내 신고 목록</a>

		{#if authStore.isAdmin}
			<a href="/admin/dashboard">관리자 대시보드</a>
		{/if}

		<button onclick={logout}>로그아웃</button>
	{:else}
		<a href="/user/login">로그인</a>
	{/if}
</nav>
```

#### 5.2.2 관리자 전용 기능

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
</script>

{#if authStore.isAdmin}
	<section>
		<h2>관리자 도구</h2>
		<button onclick={deleteUser}>사용자 삭제</button>
		<button onclick={banUser}>사용자 차단</button>
	</section>
{/if}
```

### 5.3 페이지 접근 제어

#### 5.3.1 비로그인 사용자 리다이렉트

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';

	$effect(() => {
		if (authStore.initialized && !authStore.isAuthenticated) {
			goto('/user/login');
		}
	});
</script>

<!-- 로그인한 사용자만 볼 수 있는 페이지 -->
<h1>마이 페이지</h1>
```

#### 5.3.2 관리자만 접근 가능한 페이지

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';

	$effect(() => {
		if (authStore.initialized && !authStore.isAdmin) {
			goto('/');
		}
	});
</script>

<!-- 관리자만 볼 수 있는 페이지 -->
<h1>관리자 대시보드</h1>
```

### 5.4 사용자 정보 표시

#### 5.4.1 프로필 카드

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import Avatar from '$lib/components/Avatar.svelte';
</script>

{#if authStore.isAuthenticated && authStore.user}
	<div class="profile-card">
		<Avatar uid={authStore.user.uid} size={64} />
		<h3>{authStore.user.displayName || '사용자'}</h3>
		<p>{authStore.user.email}</p>

		{#if authStore.isAdmin}
			<span class="badge">관리자</span>
		{/if}
	</div>
{/if}
```

#### 5.4.2 Top Bar 사용자 정보

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
</script>

<header>
	<nav>
		<a href="/">로고</a>

		<div>
			{#if authStore.loading}
				<span>로딩 중...</span>
			{:else if authStore.isAuthenticated}
				<span>{authStore.user?.displayName || authStore.user?.email}</span>
				{#if authStore.user?.photoURL}
					<img src={authStore.user.photoURL} alt="프로필" />
				{/if}
			{:else}
				<a href="/user/login">로그인</a>
			{/if}
		</div>
	</nav>
</header>
```

## 6. 보안 고려사항

### 6.1 클라이언트 측 권한 확인의 한계

**중요:** `authStore.isAdmin`은 **UI 제어 용도**입니다.

**절대 금지:**
```typescript
// ❌ 잘못된 예: 클라이언트에서만 권한 확인
if (authStore.isAdmin) {
	await deleteUser(uid); // 보안 위험!
}
```

**올바른 방법:**
```typescript
// ✅ 올바른 예: 서버(Cloud Functions)에서 권한 확인
// Cloud Function에서 실행
export const deleteUser = functions.https.onCall(async (data, context) => {
	const uid = context.auth?.uid;

	// 서버에서 관리자 확인
	const adminsSnapshot = await admin.database().ref('/system/settings/admins').once('value');
	const admins = admins Snapshot.val() || {};

	if (!uid || !admins[uid]) {
		throw new functions.https.HttpsError('permission-denied', '관리자 권한이 필요합니다.');
	}

	// 권한이 확인되면 작업 수행
	await admin.auth().deleteUser(data.targetUid);
	return { success: true };
});
```

### 6.2 Firebase Security Rules

RTDB에서도 관리자 권한을 확인해야 합니다.

**database.rules.json:**
```json
{
  "rules": {
    "system": {
      "settings": {
        "admins": {
          ".read": "auth != null",
          ".write": "root.child('system/settings/admins/' + auth.uid).exists()"
        }
      }
    },
    "users": {
      "$uid": {
        ".read": true,
        ".write": "auth != null && (auth.uid === $uid || root.child('system/settings/admins/' + auth.uid).exists())"
      }
    }
  }
}
```

**규칙 설명:**
- `/system/settings/admins`: 모든 로그인 사용자 읽기 가능, 관리자만 쓰기 가능
- `/users/{uid}`: 모든 사용자 읽기 가능, 본인 또는 관리자만 쓰기 가능

### 6.3 프로필 동기화 보안

**syncUserProfile()의 보안 특징:**

1. **조건부 업데이트만 수행**
   - 기존 데이터가 있으면 덮어쓰지 않음
   - 사용자가 수정한 프로필 보호

2. **update() 사용**
   - `set()` 대신 `update()`로 병합 업데이트
   - 다른 필드 보존

3. **에러 처리**
   - 동기화 실패 시에도 앱 중단 없음
   - 콘솔에 에러 로그만 출력

## 7. 디버깅 및 로깅

### 7.1 콘솔 로그 활용

AuthStore는 다음 시점에 로그를 출력합니다:

```typescript
// 1. Auth 상태 변경 시
console.log('[AuthStore] onAuthStateChanged 트리거됨:', user ? user.uid : 'null');

// 2. 프로필 동기화 완료 시
console.log('[AuthStore] 프로필 동기화 완료:', updates);

// 3. 관리자 목록 로드 완료 시
console.log('[AuthStore] 관리자 목록 로드 완료:', this._state.adminList);

// 4. 에러 발생 시
console.error('[AuthStore] 프로필 동기화 실패:', error);
console.error('[AuthStore] 관리자 목록 로드 실패:', error);
console.warn('[AuthStore] SSR 환경이거나 Firebase Auth가 초기화되지 않았습니다.');
console.warn('[AuthStore] RTDB가 초기화되지 않았습니다.');
```

### 7.2 디버깅 체크리스트

**문제: authStore.user가 항상 null**
- [ ] Firebase Auth가 올바르게 초기화되었는지 확인 (`auth` 객체)
- [ ] 브라우저 환경인지 확인 (`typeof window !== 'undefined'`)
- [ ] Firebase Console에서 Authentication이 활성화되었는지 확인
- [ ] 로그인 함수가 올바르게 실행되었는지 확인

**문제: authStore.isAdmin이 false**
- [ ] RTDB `/system/settings/admins`에 사용자 UID가 있는지 확인
- [ ] `loadAdminList()`가 정상 실행되었는지 콘솔 로그 확인
- [ ] RTDB Security Rules에서 읽기 권한이 있는지 확인

**문제: 프로필 동기화가 안 됨**
- [ ] `syncUserProfile()`이 실행되었는지 콘솔 로그 확인
- [ ] RTDB `/users/{uid}`에 쓰기 권한이 있는지 확인
- [ ] Firebase Auth에 photoURL, displayName이 있는지 확인

**문제: 깜빡임 (flicker) 발생**
- [ ] `authStore.initialized`를 확인하여 로딩 완료 후 UI 렌더링
- [ ] `authStore.loading` 상태에서 로딩 스피너 표시

### 7.3 프로덕션 환경 로그 제거

프로덕션 배포 전 로그를 제거하거나 환경 변수로 제어할 수 있습니다.

```typescript
const DEBUG = import.meta.env.DEV;

if (DEBUG) {
	console.log('[AuthStore] onAuthStateChanged 트리거됨:', user ? user.uid : 'null');
}
```

## 8. 테스트

### 8.1 수동 테스트 시나리오

#### 시나리오 1: 신규 사용자 로그인

1. Firebase Console에서 `/users/{uid}` 데이터 삭제
2. 애플리케이션에서 Google 로그인 실행
3. 확인 사항:
   - [ ] `authStore.user`에 사용자 정보 표시
   - [ ] RTDB `/users/{uid}`에 `photoUrl`, `displayName` 자동 생성
   - [ ] 콘솔에 "프로필 동기화 완료" 로그 출력

#### 시나리오 2: 기존 사용자 로그인

1. RTDB `/users/{uid}`에 커스텀 `displayName` 저장
2. 애플리케이션에서 로그인
3. 확인 사항:
   - [ ] RTDB의 `displayName`이 변경되지 않음
   - [ ] 콘솔에 동기화 로그가 없거나 빈 객체 표시

#### 시나리오 3: 관리자 권한 확인

1. Firebase Console RTDB에서 `/system/settings/admins/{uid}` = true 설정
2. 해당 사용자로 로그인
3. 확인 사항:
   - [ ] `authStore.isAdmin` = true
   - [ ] 관리자 전용 메뉴 표시
   - [ ] 콘솔에 "관리자 목록 로드 완료" 로그 출력

#### 시나리오 4: 로그아웃

1. 로그인 상태에서 로그아웃 실행
2. 확인 사항:
   - [ ] `authStore.user` = null
   - [ ] `authStore.isAuthenticated` = false
   - [ ] `authStore.isAdmin` = false
   - [ ] 로그인 페이지로 리다이렉트

### 8.2 단위 테스트 (향후 작업)

```typescript
import { describe, it, expect, vi } from 'vitest';
import { authStore } from '$lib/stores/auth.svelte';

describe('AuthStore', () => {
	it('초기 상태는 loading=true, user=null', () => {
		expect(authStore.loading).toBe(true);
		expect(authStore.user).toBeNull();
		expect(authStore.isAuthenticated).toBe(false);
	});

	it('로그인 시 user가 설정됨', async () => {
		// Mock Firebase Auth
		const mockUser = { uid: 'test123', displayName: 'Test User' };
		vi.mock('firebase/auth', () => ({
			onAuthStateChanged: vi.fn((auth, callback) => callback(mockUser))
		}));

		// 로그인 시뮬레이션
		await new Promise(resolve => setTimeout(resolve, 100));

		expect(authStore.user).toEqual(mockUser);
		expect(authStore.isAuthenticated).toBe(true);
	});
});
```

## 9. 관련 문서

- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 초기화 및 설정
- [sonub-firebase-auth.md](./sonub-firebase-auth.md): Firebase Authentication 샘플 코드
- [sonub-user-login.md](./sonub-user-login.md): 로그인 UI/UX 구현
- [sonub-firebase-realtime-database.md](./sonub-firebase-realtime-database.md): RTDB 유틸리티
- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md): RTDB 스키마 구조
- [sonub-firebase-security.md](./sonub-firebase-security.md): Security Rules 설정

## 10. 버전 히스토리

| 버전 | 날짜 | 변경 사항 |
|------|------|-----------|
| 1.0.0 | 2025-11-10 | 초기 작성: AuthStore 전체 구현 명세 작성 |

## 11. 승인

- [ ] 개발자 승인 필요
- [ ] AuthStore 구현 완료
- [ ] syncUserProfile() 테스트 완료
- [ ] loadAdminList() 테스트 완료
- [ ] Security Rules 설정 완료
- [ ] 문서 검토 완료
