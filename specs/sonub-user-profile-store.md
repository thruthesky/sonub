---
name: sonub-user-profile-store
version: 1.1.0
description: 사용자 프로필 중앙 캐시 스토어 구현 - 중복 RTDB 리스너 제거 및 Svelte 5 반응성 이슈 해결 (state_unsafe_mutation 에러 수정)
author: Claude Code
email: noreply@anthropic.com
homepage: https://github.com/thruthesky/
license: GPL-3.0
created: 2025-11-10
updated: 2025-11-10
step: 44
priority: "**"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-user-avatar.md
tags:
  - firebase
  - rtdb
  - realtime-database
  - svelte5
  - store
  - cache
  - reactivity
  - state-unsafe-mutation
  - effect
  - derived
---

# 사용자 프로필 스토어 (UserProfileStore)

## 1. 개요

### 1.1 배경 및 문제점

#### 1.1.1 초기 문제 상황

사용자가 프로필 이미지를 업데이트했을 때, 세 곳의 아바타가 서로 다른 이미지를 표시하는 문제가 발생했습니다:

1. **Top-bar**: 상단 네비게이션의 사용자 아바타
2. **Menu 페이지**: 메뉴에서의 사용자 프로필
3. **프로필 페이지**: 프로필 수정 페이지의 아바타

**근본 원인:**
- 각 Avatar 컴포넌트가 개별적으로 RTDB `/users/{uid}` 노드를 구독
- 동일한 uid에 대해 중복된 `onValue` 리스너 생성
- 컴포넌트마다 로컬 상태 관리 (데이터 불일치 가능성)

#### 1.1.2 추가 발견된 문제 (Issue #1: Map 반응성)

실시간 업데이트 테스트 중 발견된 문제:
- RTDB에서 데이터를 정상적으로 수신 (콘솔 로그 확인됨)
- 하지만 Avatar 컴포넌트가 re-render되지 않음
- **Svelte 5 Map 반응성 이슈** 발견

#### 1.1.3 추가 발견된 문제 (Issue #2: state_unsafe_mutation)

Avatar 컴포넌트 실행 시 발견된 치명적 에러:

```
Uncaught Svelte error: state_unsafe_mutation
Updating state inside `$derived(...)`, `$inspect(...)` or a template expression is forbidden.
https://svelte.dev/e/state_unsafe_mutation

at avatar.svelte:47 → $derived(userProfileStore.getProfile(uid))
```

**근본 원인:**
- `getProfile()` 메서드가 `$derived` 내부에서 호출됨
- 첫 호출 시 `subscribeToProfile()`이 실행되면서 `this.cache.set()` 호출 (상태 변경)
- **Svelte 5는 `$derived` 내부에서 상태 변경을 엄격히 금지**
- 프로필 데이터는 정상 수신되지만, 에러로 인해 컴포넌트 렌더링 실패

### 1.2 해결책 개요

**중앙 집중식 프로필 캐시 스토어 구현:**

- **파일**: `src/lib/stores/user-profile.svelte.ts`
- **목적**:
  - 동일한 uid에 대해 하나의 RTDB 리스너만 생성
  - 모든 Avatar 컴포넌트가 동일한 캐시 데이터 공유
  - 프로필 데이터 업데이트 시 모든 컴포넌트에 자동 반영

**주요 기능:**
1. Map 기반 프로필 캐시 (uid → ProfileData)
2. RTDB 리스너 중복 제거
3. Svelte 5 runes 기반 반응성
4. 에러 및 로딩 상태 관리

## 2. 기술 구조

### 2.1 타입 정의

```typescript
/**
 * 사용자 프로필 데이터 타입
 * RTDB의 /users/{uid} 노드 구조
 */
export interface UserProfile {
	/** 사용자 닉네임 (필수) */
	displayName: string | null;
	/** 프로필 사진 URL (선택) */
	photoUrl: string | null;
	/** 성별 (M: 남성, F: 여성, 선택) */
	gender?: 'M' | 'F' | null;
	/** 생년 (선택) */
	birthYear?: number | null;
	/** 생월 (선택) */
	birthMonth?: number | null;
	/** 생일 (선택) */
	birthDay?: number | null;
	/** 자기소개 (선택) */
	bio?: string | null;
	/** 계정 생성 시간 (Unix timestamp, 밀리초) */
	createdAt?: number | null;
	/** 프로필 수정 시간 (Unix timestamp, 밀리초) */
	updatedAt?: number | null;
}

/**
 * 프로필 캐시 항목
 */
interface ProfileCacheItem {
	/** 프로필 데이터 */
	data: UserProfile | null;
	/** 로딩 중 여부 */
	loading: boolean;
	/** 에러 발생 시 에러 객체 */
	error: Error | null;
	/** RTDB 리스너 구독 해제 함수 */
	unsubscribe: Unsubscribe | null;
}
```

### 2.2 클래스 구조

```typescript
class UserProfileStore {
	/**
	 * uid별 프로필 캐시
	 * Map<uid, ProfileCacheItem>
	 */
	private cache = $state<Map<string, ProfileCacheItem>>(new Map());

	/**
	 * 프로필 구독 시작 (상태 변경 가능)
	 * ⚠️ 주의: $effect에서만 호출하세요. $derived에서 호출하면 안 됩니다.
	 */
	ensureSubscribed(uid: string | undefined): void {
		// 구현...
	}

	/**
	 * 캐시된 프로필 데이터 조회 (순수 읽기)
	 * ✅ 안전: $derived에서 호출 가능 (상태 변경 없음)
	 */
	getCachedProfile(uid: string | undefined): UserProfile | null {
		// 구현...
	}

	/**
	 * 프로필 로딩 상태 확인
	 */
	isLoading(uid: string | undefined): boolean {
		// 구현...
	}

	/**
	 * 프로필 에러 확인
	 */
	getError(uid: string | undefined): Error | null {
		// 구현...
	}

	/**
	 * 프로필 실시간 구독 시작 (private)
	 */
	private subscribeToProfile(uid: string): void {
		// 구현...
	}

	/**
	 * 프로필 구독 해제
	 */
	unsubscribeProfile(uid: string): void {
		// 구현...
	}

	/**
	 * 전체 캐시 초기화
	 */
	clearAll(): void {
		// 구현...
	}
}

/**
 * 전역 인스턴스
 */
export const userProfileStore = new UserProfileStore();
```

### 2.3 동작 흐름 (개선된 패턴)

```
┌─────────────────────────────────────────────┐
│ Avatar 컴포넌트 A (uid: "user123")          │
│                                             │
│ $effect(() => {                             │
│   ensureSubscribed("user123")  ← 구독 시작  │
│ })                                          │
│                                             │
│ profile = $derived(                         │
│   getCachedProfile("user123")  ← 데이터 읽기│
│ )                                           │
└─────────────────────────────────────────────┘
         ↓ (구독 시작)
┌─────────────────────────────────────────────┐
│ UserProfileStore.ensureSubscribed()         │
│ - cache.has("user123")? NO                  │
│ - subscribeToProfile("user123") 호출        │
│ ⚠️ 상태 변경 발생 (OK in $effect)           │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ RTDB onValue 리스너 등록                    │
│ 경로: /users/user123                        │
└─────────────────────────────────────────────┘
         ↓ (데이터 읽기)
┌─────────────────────────────────────────────┐
│ UserProfileStore.getCachedProfile()         │
│ - cache.get("user123") 조회                 │
│ - 초기값 null 반환 (로딩 중)                │
│ ✅ 순수 읽기 (OK in $derived)               │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ Avatar 컴포넌트 B (uid: "user123")          │
│                                             │
│ $effect(() => {                             │
│   ensureSubscribed("user123")  ← 이미 구독됨│
│ })                                          │
│                                             │
│ profile = $derived(                         │
│   getCachedProfile("user123")  ← 캐시 재사용│
│ )                                           │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ UserProfileStore.ensureSubscribed()         │
│ - cache.has("user123")? YES                 │
│ - 아무것도 하지 않음 (중복 방지!)          │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ RTDB 데이터 수신                            │
│ {photoUrl: "...", displayName: "..."}       │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ UserProfileStore 캐시 업데이트              │
│ this.cache = new Map(this.cache)            │
│   .set("user123", newCacheItem)             │
└─────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────┐
│ 모든 Avatar 컴포넌트 자동 re-render         │
│ (A, B 모두 getCachedProfile()이 새 데이터   │
│  반환하여 $derived 트리거)                  │
└─────────────────────────────────────────────┘
```

## 3. 구현 상세

### 3.1 ensureSubscribed() 메서드 (상태 변경 가능)

**목적**: uid에 대한 구독을 시작합니다. 이미 구독 중이면 아무것도 하지 않습니다.

**⚠️ 중요**: 이 메서드는 상태를 변경할 수 있으므로 `$effect`에서만 호출해야 합니다.

```typescript
ensureSubscribed(uid: string | undefined): void {
	// uid가 없으면 무시
	if (!uid) {
		return;
	}

	// Firebase RTDB가 초기화되지 않은 경우
	if (!rtdb) {
		console.error('[UserProfileStore] ❌ Firebase RTDB가 초기화되지 않았습니다.');
		return;
	}

	// 이미 캐시에 있으면 구독 중이므로 무시
	if (this.cache.has(uid)) {
		return;
	}

	// 새로운 프로필 구독 시작
	console.log(`[UserProfileStore] 🆕 새 프로필 구독 시작: ${uid}`);
	this.subscribeToProfile(uid);
}
```

**특징:**
- 캐시 히트: 아무것도 하지 않음 (중복 구독 방지)
- 캐시 미스: 새 구독 시작
- 반환값 없음 (void)
- `$effect`에서 호출하여 구독 라이프사이클 관리

**사용 예제:**
```typescript
// Avatar 컴포넌트에서
$effect(() => {
	userProfileStore.ensureSubscribed(uid);
});
```

### 3.2 getCachedProfile() 메서드 (순수 읽기)

**목적**: 캐시된 프로필 데이터를 조회합니다. 구독이 없으면 null을 반환합니다.

**✅ 안전**: 이 메서드는 순수 읽기 전용이므로 `$derived`에서 안전하게 호출할 수 있습니다.

```typescript
getCachedProfile(uid: string | undefined): UserProfile | null {
	// uid가 없으면 null 반환
	if (!uid) {
		return null;
	}

	// 캐시에 있으면 데이터 반환
	if (this.cache.has(uid)) {
		const cached = this.cache.get(uid)!;
		return cached.data;
	}

	// 캐시에 없으면 null 반환 (구독 필요)
	return null;
}
```

**특징:**
- 상태 변경 없음 (순수 함수)
- 캐시에 있으면 데이터 반환
- 캐시에 없으면 null 반환 (자동으로 구독하지 않음!)
- `$derived`에서 호출하여 반응성 활용

**사용 예제:**
```typescript
// Avatar 컴포넌트에서
const profile = $derived(userProfileStore.getCachedProfile(uid));
const photoUrl = $derived(profile?.photoUrl ?? null);
const displayName = $derived(profile?.displayName ?? null);
```

### 3.3 왜 두 개의 메서드로 분리했는가?

**문제 상황:**
```typescript
// ❌ 잘못된 패턴 (Svelte 5 에러 발생)
const profile = $derived(userProfileStore.getProfile(uid));
```

이 코드는 다음과 같은 에러를 발생시킵니다:
```
Uncaught Svelte error: state_unsafe_mutation
Updating state inside `$derived(...)` is forbidden.
```

**원인:**
- `getProfile()`은 내부적으로 `subscribeToProfile()`을 호출
- `subscribeToProfile()`은 `this.cache.set()` 호출 (상태 변경)
- Svelte 5는 `$derived` 내부에서 상태 변경을 엄격히 금지

**해결책:**
```typescript
// ✅ 올바른 패턴 (Svelte 5 규칙 준수)

// 구독 관리: $effect에서 (상태 변경 허용)
$effect(() => {
	userProfileStore.ensureSubscribed(uid);
});

// 데이터 읽기: $derived에서 (순수 읽기)
const profile = $derived(userProfileStore.getCachedProfile(uid));
```

**핵심 원칙:**
- **Side Effects (부작용)**: `$effect`에서만
- **Pure Computations (순수 계산)**: `$derived`에서만
- **관심사의 분리**: 구독 관리 vs 데이터 읽기

### 3.4 subscribeToProfile() 메서드

**목적**: RTDB 리스너 등록 및 데이터 수신

```typescript
private subscribeToProfile(uid: string): void {
	console.log(`[UserProfileStore] ✅ 프로필 구독 시작: ${uid}`);
	console.log(`[UserProfileStore] 🔗 RTDB 경로: /users/${uid}`);

	// 초기 캐시 항목 생성 (로딩 상태)
	const cacheItem: ProfileCacheItem = {
		data: null,
		loading: true,
		error: null,
		unsubscribe: null
	};

	this.cache.set(uid, cacheItem);

	// RTDB 리스너 등록
	const userRef = ref(rtdb!, `users/${uid}`);

	const unsubscribe = onValue(
		userRef,
		(snapshot) => {
			// 데이터 로드 성공
			const data = snapshot.val() as UserProfile | null;

			console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
			console.log(`[UserProfileStore] 📥 프로필 데이터 수신: ${uid}`);
			console.log('  수신 시간:', new Date().toISOString());
			console.log('  데이터:', data);
			console.log('  photoUrl:', data?.photoUrl);
			console.log('  displayName:', data?.displayName);
			console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

			// 🔥 중요: 반응성 트리거를 위해 새로운 객체 생성
			const newCacheItem: ProfileCacheItem = {
				data: data,
				loading: false,
				error: null,
				unsubscribe: unsubscribe
			};

			// Map 자체를 재할당하여 반응성 트리거
			this.cache = new Map(this.cache).set(uid, newCacheItem);

			console.log(`[UserProfileStore] ✨ 캐시 업데이트 완료: ${uid}`);
			console.log(`[UserProfileStore] 📊 현재 캐시 크기: ${this.cache.size}`);
		},
		(error) => {
			// 데이터 로드 실패
			console.error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
			console.error(`[UserProfileStore] ❌ 프로필 로드 실패: ${uid}`);
			console.error('  에러:', error);
			console.error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

			// 🔥 중요: 반응성 트리거를 위해 새로운 객체 생성
			const newCacheItem: ProfileCacheItem = {
				data: null,
				loading: false,
				error: error as Error,
				unsubscribe: unsubscribe
			};

			// Map 자체를 재할당하여 반응성 트리거
			this.cache = new Map(this.cache).set(uid, newCacheItem);
		}
	);

	// 구독 해제 함수 저장 (초기 설정)
	const item = this.cache.get(uid);
	if (item) {
		const updatedItem: ProfileCacheItem = {
			...item,
			unsubscribe: unsubscribe
		};
		this.cache = new Map(this.cache).set(uid, updatedItem);
	}

	console.log(`[UserProfileStore] 🎧 RTDB 리스너 등록 완료: ${uid}`);
}
```

## 4. 🔥 Svelte 5 Map 반응성 이슈 해결

### 4.1 문제 상황

**증상:**
- RTDB에서 데이터를 정상적으로 수신 (콘솔 로그 확인됨)
- `this.cache`에 데이터가 업데이트됨
- 하지만 Avatar 컴포넌트가 re-render되지 않음

**원인:**
Svelte 5에서 `$state` Map의 객체를 **변경(mutate)**하고 `.set()`으로 저장해도 반응성이 트리거되지 않습니다.

### 4.2 잘못된 코드

```typescript
// ❌ WRONG - 동일한 객체 참조를 수정
const item = this.cache.get(uid);
if (item) {
    item.data = data;           // 기존 객체 수정
    item.loading = false;
    item.error = null;
    this.cache.set(uid, item);  // 동일한 참조 → 반응성 X
}
```

**문제점:**
1. `item`은 기존 객체의 참조
2. `item.data = data`는 객체 내부를 수정 (mutation)
3. `this.cache.set(uid, item)`은 동일한 참조를 다시 저장
4. Svelte 5는 Map 자체가 재할당되지 않으면 반응성을 트리거하지 않음

### 4.3 올바른 코드

```typescript
// ✅ CORRECT - 새 객체 생성 및 Map 재할당
const newCacheItem: ProfileCacheItem = {
    data: data,                  // 완전히 새로운 객체 생성
    loading: false,
    error: null,
    unsubscribe: unsubscribe
};

// Map 자체를 재할당하여 반응성 트리거
this.cache = new Map(this.cache).set(uid, newCacheItem);
```

**해결 포인트:**
1. **새 객체 생성**: 기존 객체를 수정하지 않고 완전히 새로운 객체 생성
2. **Map 재할당**: `this.cache = new Map(this.cache).set(...)` 패턴 사용
3. **모든 업데이트에 적용**: 성공/실패 콜백 모두 동일한 패턴 사용

### 4.4 핵심 원칙

**Svelte 5 반응성 규칙:**

1. **객체 불변성 (Immutability)**
   ```typescript
   // ❌ 잘못됨
   obj.prop = value;

   // ✅ 올바름
   obj = { ...obj, prop: value };
   ```

2. **배열 불변성**
   ```typescript
   // ❌ 잘못됨
   arr.push(item);

   // ✅ 올바름
   arr = [...arr, item];
   ```

3. **Map 불변성**
   ```typescript
   // ❌ 잘못됨
   map.set(key, value);

   // ✅ 올바름
   map = new Map(map).set(key, value);
   ```

4. **Set 불변성**
   ```typescript
   // ❌ 잘못됨
   set.add(value);

   // ✅ 올바름
   set = new Set(set).add(value);
   ```

### 4.5 검증 방법

**디버깅 로그 추가:**

```typescript
// UserProfileStore에서
console.log(`[UserProfileStore] ✨ 캐시 업데이트 완료: ${uid}`);
console.log(`[UserProfileStore] 📊 현재 캐시 크기: ${this.cache.size}`);

// Avatar 컴포넌트에서
$effect(() => {
	console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
	console.log('[Avatar 컴포넌트] 프로필 상태 변경');
	console.log('  uid:', uid);
	console.log('  profile:', profile);
	console.log('  photoUrl:', photoUrl);
	console.log('  displayName:', displayName);
	console.log('  shouldShowImage:', shouldShowImage);
	console.log('  initial:', initial);
	console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
});
```

**콘솔 출력 확인:**
1. RTDB 데이터 수신 로그
2. 캐시 업데이트 로그
3. Avatar 컴포넌트 상태 변경 로그

모두 정상적으로 출력되면 반응성이 올바르게 작동하는 것입니다.

## 5. 🔥 Svelte 5 $derived 반응성 규칙 위반 해결

### 5.1 문제 상황

Avatar 컴포넌트에서 다음과 같은 치명적 에러가 발생했습니다:

```
Uncaught Svelte error: state_unsafe_mutation
Updating state inside `$derived(...)`, `$inspect(...)` or a template expression is forbidden.
If the value should not be reactive, declare it without `$state`
https://svelte.dev/e/state_unsafe_mutation

at avatar.svelte:47 → $derived(userProfileStore.getProfile(uid))
at UserProfileStore.subscribeToProfile (user-profile.svelte.ts:233)
at UserProfileStore.getProfile (user-profile.svelte.ts:126)
```

**에러 발생 코드:**
```typescript
// ❌ WRONG - Svelte 5 에러 발생
const profile = $derived(userProfileStore.getProfile(uid));
```

### 5.2 근본 원인 분석

**문제의 흐름:**

1. `$derived`가 `getProfile(uid)` 호출
2. `getProfile()`이 캐시 확인
3. 캐시 미스 시 `subscribeToProfile()` 호출
4. `subscribeToProfile()`이 `this.cache.set()` 실행 → **상태 변경 발생!**
5. **Svelte 5 에러**: `$derived` 내부에서 상태 변경 금지

**핵심 문제:**
- `getProfile()`은 "읽기"와 "쓰기"를 동시에 수행하는 혼합 메서드
- Svelte 5는 `$derived` 내부에서 순수성(purity)을 요구
- 부작용(side effects)과 순수 계산(pure computation)의 분리가 필요

### 5.3 해결책: API 분리

**해결 방법:**

`getProfile()`을 두 개의 메서드로 분리:

1. **ensureSubscribed()**: 구독 관리 (부작용 허용, `$effect`에서 호출)
2. **getCachedProfile()**: 데이터 읽기 (순수 함수, `$derived`에서 호출)

**수정된 코드:**
```typescript
// ✅ CORRECT - Svelte 5 규칙 준수

// 구독 관리: $effect에서 (상태 변경 허용)
$effect(() => {
	userProfileStore.ensureSubscribed(uid);
});

// 데이터 읽기: $derived에서 (순수 읽기)
const profile = $derived(userProfileStore.getCachedProfile(uid));
```

### 5.4 Svelte 5 반응성 규칙 정리

**$effect (부작용 허용):**
- ✅ 상태 변경 가능
- ✅ RTDB 리스너 등록/해제
- ✅ DOM 조작
- ✅ 네트워크 요청
- ✅ 타이머 설정

**$derived (순수 계산만):**
- ✅ 데이터 읽기
- ✅ 계산 및 변환
- ✅ 필터링 및 매핑
- ❌ 상태 변경 금지
- ❌ RTDB 리스너 등록 금지
- ❌ DOM 조작 금지

**권장 패턴:**
```typescript
// 부작용과 순수 계산의 명확한 분리

// 1. $effect: 구독 라이프사이클 관리
$effect(() => {
	store.ensureSubscribed(key);
	return () => store.unsubscribe(key); // cleanup
});

// 2. $derived: 데이터 읽기 및 계산
const data = $derived(store.getCachedData(key));
const computed = $derived(transform(data));
```

## 6. Avatar 컴포넌트와의 통합

### 6.1 Avatar 컴포넌트 수정 사항

**변경 전 (onMount 패턴, 문제 있음):**
```typescript
onMount(() => {
	if (!uid) return;

	const userRef = ref(rtdb!, `users/${uid}`);
	const unsubscribe = onValue(userRef, (snapshot) => {
		const data = snapshot.val();
		photoUrl = data?.photoUrl ?? null;
		displayName = data?.displayName ?? null;
	});

	return () => unsubscribe();
});
```

**중간 단계 ($derived 패턴, 에러 발생):**
```typescript
// ❌ WRONG - state_unsafe_mutation 에러
const profile = $derived(userProfileStore.getProfile(uid));
```

**최종 수정 ($effect + $derived 패턴):**
```typescript
import { userProfileStore } from '$lib/stores/user-profile.svelte';

// uid 변경 시 구독 시작 ($effect에서 상태 변경 가능)
$effect(() => {
	userProfileStore.ensureSubscribed(uid);
});

// UserProfileStore에서 프로필 데이터 가져오기 (순수 읽기)
// uid가 변경될 때마다 자동으로 업데이트됨 ($derived 사용)
const profile = $derived(userProfileStore.getCachedProfile(uid));

// 프로필에서 photoUrl과 displayName 추출
const photoUrl = $derived(profile?.photoUrl ?? null);
const displayName = $derived(profile?.displayName ?? null);

// displayName의 첫 글자 계산
const initial = $derived.by(() => {
	const name = displayName;
	if (!name || name.trim() === '') return 'U';
	return name.charAt(0).toUpperCase();
});

// 이미지를 표시할지 여부 결정
const shouldShowImage = $derived(
	photoUrl &&
	photoUrl.trim() !== '' &&
	!imageLoadFailed
);
```

**주요 개선점:**
1. **onMount 제거**: `$effect`가 uid 변경을 자동 감지
2. **RTDB 직접 호출 제거**: userProfileStore 사용
3. **중복 리스너 방지**: 스토어가 리스너 재사용
4. **자동 반응성**: uid 변경 시 자동으로 새 데이터 로드
5. **Svelte 5 규칙 준수**: 부작용과 순수 계산의 명확한 분리

### 6.2 top-bar.svelte 수정

**변경 전:**
```svelte
<Avatar uid={authStore.user.uid} size={40} />
```

**변경 후:**
```svelte
<Avatar uid={authStore.user?.uid} size={40} />
```

**이유:**
- `authStore.user`가 null일 수 있음
- Optional chaining으로 안전하게 처리

## 7. 사용 예제

### 7.1 기본 사용법

```svelte
<script lang="ts">
	import { userProfileStore } from '$lib/stores/user-profile.svelte';

	let uid = 'user123';

	// 구독 시작 ($effect에서)
	$effect(() => {
		userProfileStore.ensureSubscribed(uid);
	});

	// 데이터 읽기 ($derived에서)
	const profile = $derived(userProfileStore.getCachedProfile(uid));
	const isLoading = $derived(userProfileStore.isLoading(uid));
	const error = $derived(userProfileStore.getError(uid));
</script>

{#if isLoading}
	<p>로딩 중...</p>
{:else if error}
	<p>에러: {error.message}</p>
{:else if profile}
	<div>
		<img src={profile.photoUrl} alt={profile.displayName} />
		<h2>{profile.displayName}</h2>
		<p>{profile.bio}</p>
	</div>
{:else}
	<p>프로필 없음</p>
{/if}
```

### 7.2 여러 곳에서 동일한 프로필 사용

```svelte
<!-- TopBar.svelte -->
<script>
	$effect(() => {
		userProfileStore.ensureSubscribed(currentUserId);
	});
	const profile = $derived(userProfileStore.getCachedProfile(currentUserId));
</script>
<Avatar photoUrl={profile?.photoUrl} displayName={profile?.displayName} />

<!-- Menu.svelte -->
<script>
	$effect(() => {
		userProfileStore.ensureSubscribed(currentUserId);
	});
	const profile = $derived(userProfileStore.getCachedProfile(currentUserId));
</script>
<Avatar photoUrl={profile?.photoUrl} displayName={profile?.displayName} />

<!-- Profile.svelte -->
<script>
	$effect(() => {
		userProfileStore.ensureSubscribed(currentUserId);
	});
	const profile = $derived(userProfileStore.getCachedProfile(currentUserId));
</script>
<Avatar photoUrl={profile?.photoUrl} displayName={profile?.displayName} />
```

**결과:**
- 세 컴포넌트가 동일한 RTDB 리스너 공유
- 프로필 업데이트 시 세 곳 모두 자동으로 업데이트
- Svelte 5 반응성 규칙 준수

## 8. 성능 최적화

### 8.1 리스너 재사용

**이전 방식 (비효율적):**
```
TopBar → RTDB 리스너 1 (users/user123)
Menu   → RTDB 리스너 2 (users/user123)
Profile → RTDB 리스너 3 (users/user123)
─────────────────────────────────────────
총 3개 리스너 (중복!)
```

**현재 방식 (효율적):**
```
TopBar   ─┐
Menu     ─┤→ userProfileStore → RTDB 리스너 1 (users/user123)
Profile  ─┘
─────────────────────────────────────────
총 1개 리스너 (공유!)
```

### 8.2 캐시 히트율

```typescript
// 첫 번째 컴포넌트 (캐시 미스)
userProfileStore.ensureSubscribed('user123'); // → RTDB 리스너 생성

// 두 번째 컴포넌트 (캐시 히트)
userProfileStore.ensureSubscribed('user123'); // → 이미 구독 중이므로 무시

// 세 번째 컴포넌트 (캐시 히트)
userProfileStore.ensureSubscribed('user123'); // → 이미 구독 중이므로 무시

// 모든 컴포넌트에서 데이터 읽기
userProfileStore.getCachedProfile('user123'); // → 캐시에서 즉시 반환
```

## 9. 에러 처리

### 9.1 uid 누락

```typescript
ensureSubscribed(uid: string | undefined): void {
	if (!uid) {
		return; // 조용히 무시
	}
	// ...
}

getCachedProfile(uid: string | undefined): UserProfile | null {
	if (!uid) {
		return null; // null 반환
	}
	// ...
}
```

### 9.2 RTDB 초기화 실패

```typescript
ensureSubscribed(uid: string | undefined): void {
	// ...
	if (!rtdb) {
		console.error('[UserProfileStore] ❌ Firebase RTDB가 초기화되지 않았습니다.');
		return;
	}
	// ...
}
```

### 9.3 데이터 로드 실패

```typescript
onValue(
	userRef,
	(snapshot) => { /* 성공 */ },
	(error) => {
		console.error(`[UserProfileStore] ❌ 프로필 로드 실패: ${uid}`);
		console.error('  에러:', error);

		// 에러 상태 저장
		const newCacheItem: ProfileCacheItem = {
			data: null,
			loading: false,
			error: error as Error,
			unsubscribe: unsubscribe
		};
		this.cache = new Map(this.cache).set(uid, newCacheItem);
	}
);
```

## 10. 디버깅

### 10.1 로그 구조

**UserProfileStore 로그:**
```
[UserProfileStore] 🆕 새 프로필 구독 시작: user123
[UserProfileStore] ✅ 프로필 구독 시작: user123
[UserProfileStore] 🔗 RTDB 경로: /users/user123
[UserProfileStore] 🎧 RTDB 리스너 등록 완료: user123
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[UserProfileStore] 📥 프로필 데이터 수신: user123
  수신 시간: 2025-11-10T12:34:56.789Z
  데이터: {photoUrl: "...", displayName: "..."}
  photoUrl: https://...
  displayName: JaeHo Song
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[UserProfileStore] ✨ 캐시 업데이트 완료: user123
[UserProfileStore] 📊 현재 캐시 크기: 1
[UserProfileStore] 📦 캐시에서 반환: user123
```

**Avatar 컴포넌트 로그:**
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[Avatar 컴포넌트] 프로필 상태 변경
  uid: user123
  profile: {photoUrl: "...", displayName: "..."}
  photoUrl: https://...
  displayName: JaeHo Song
  shouldShowImage: true
  initial: J
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 10.2 캐시 상태 확인

```typescript
// 브라우저 콘솔에서
userProfileStore.debug();

// 출력:
[UserProfileStore] 📊 캐시 상태:
  - 총 구독 수: 3
  - user123: {loading: false, hasData: true, hasError: false}
  - user456: {loading: false, hasData: true, hasError: false}
  - user789: {loading: true, hasData: false, hasError: false}
```

## 11. 테스트

### 11.1 실시간 업데이트 테스트

**시나리오:**
1. 브라우저에서 프로필 페이지 열기
2. Firebase Console에서 `/users/{uid}/photoUrl` 직접 수정
3. 페이지 새로고침 없이 이미지가 자동으로 변경되는지 확인

**예상 로그:**
```
[UserProfileStore] 📥 프로필 데이터 수신: user123
  photoUrl: https://new-image-url.jpg
[UserProfileStore] ✨ 캐시 업데이트 완료: user123
[Avatar 컴포넌트] 프로필 상태 변경
  photoUrl: https://new-image-url.jpg
```

### 11.2 중복 리스너 방지 테스트

**시나리오:**
1. 동일한 uid를 가진 Avatar 컴포넌트 3개 렌더링
2. 콘솔 로그 확인

**예상 로그:**
```
[UserProfileStore] 🆕 새 프로필 구독 시작: user123
[UserProfileStore] ensureSubscribed 호출 (이미 구독 중, 무시)  ← 두 번째
[UserProfileStore] ensureSubscribed 호출 (이미 구독 중, 무시)  ← 세 번째
```

**확인 사항:**
- "새 프로필 구독 시작" 로그가 1번만 출력되어야 함
- 두 번째, 세 번째 호출은 조용히 무시되어야 함

## 12. 보안 고려사항

### 12.1 RTDB 보안 규칙

```json
{
  "rules": {
    "users": {
      "$uid": {
        ".read": true,  // 누구나 읽기 가능 (프로필 공개)
        ".write": "$uid === auth.uid"  // 본인만 수정 가능
      }
    }
  }
}
```

### 12.2 민감 정보 보호

**공개 필드:**
- displayName
- photoUrl
- gender
- birthYear (연도만)
- bio

**비공개 필드 (별도 노드 관리):**
- email (Firebase Auth에만 저장)
- phoneNumber
- 정확한 생년월일 (birthMonth, birthDay는 선택적 공개)

## 13. 프로덕션 정리

### 13.1 디버깅 로그 제거

프로덕션 배포 전 다음 로그 제거:
- `console.log` 로그 (성능 이슈)
- `━━━━━━` 시각적 구분선
- `$effect` 디버깅 블록

### 13.2 환경 변수 기반 로그

```typescript
const DEBUG = import.meta.env.DEV;

if (DEBUG) {
	console.log('[UserProfileStore] 📥 프로필 데이터 수신');
}
```

## 14. 확장 아이디어

### 14.1 타임아웃 처리

```typescript
private subscribeToProfile(uid: string, timeout = 10000): void {
	const timer = setTimeout(() => {
		console.warn('[UserProfileStore] ⏰ 타임아웃: 데이터 로드 실패');
		// 타임아웃 에러 처리
	}, timeout);

	onValue(userRef, (snapshot) => {
		clearTimeout(timer);
		// 데이터 처리...
	});
}
```

### 14.2 재시도 로직

```typescript
private async retrySubscribe(uid: string, maxRetries = 3): Promise<void> {
	for (let i = 0; i < maxRetries; i++) {
		try {
			await this.subscribeToProfile(uid);
			return;
		} catch (error) {
			console.warn(`[UserProfileStore] 재시도 ${i + 1}/${maxRetries}`);
			await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
		}
	}
	console.error('[UserProfileStore] ❌ 최대 재시도 횟수 초과');
}
```

### 14.3 캐시 만료

```typescript
interface ProfileCacheItem {
	data: UserProfile | null;
	loading: boolean;
	error: Error | null;
	unsubscribe: Unsubscribe | null;
	timestamp: number;  // 캐시 생성 시간
}

// 1시간 후 캐시 만료
const CACHE_TTL = 60 * 60 * 1000;

ensureSubscribed(uid: string): void {
	const cached = this.cache.get(uid);
	if (cached) {
		const age = Date.now() - cached.timestamp;
		if (age > CACHE_TTL) {
			console.log('[UserProfileStore] ⏰ 캐시 만료, 재구독');
			this.unsubscribeProfile(uid);
			this.subscribeToProfile(uid);
		}
	}
	// ...
}
```

## 15. 관련 문서

- [sonub-user-avatar.md](./sonub-user-avatar.md): Avatar 컴포넌트 구현
- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 설정
- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md): RTDB 구조
- [Svelte 5 Runes](https://svelte.dev/docs/svelte/$state): $state, $derived 문서
- [Svelte 5 Reactivity](https://svelte.dev/e/state_unsafe_mutation): state_unsafe_mutation 에러 해설

## 16. 변경 이력

| 버전 | 날짜 | 변경 내용 |
|------|------|-----------|
| 1.0.0 | 2025-11-10 | 초기 구현: UserProfileStore 생성, Svelte 5 Map 반응성 이슈 수정, Avatar 컴포넌트 통합, 디버깅 로그 추가 |
| 1.1.0 | 2025-11-10 | **중요 수정**: Svelte 5 `state_unsafe_mutation` 에러 해결 - `getProfile()` 메서드를 `ensureSubscribed()`와 `getCachedProfile()`로 분리, `$effect`와 `$derived`의 명확한 분리 |

## 17. 결론

UserProfileStore는 다음과 같은 문제를 해결했습니다:

1. ✅ **중복 리스너 제거**: 동일한 uid에 대해 하나의 RTDB 리스너만 생성
2. ✅ **데이터 일관성**: 모든 컴포넌트가 동일한 캐시 데이터 공유
3. ✅ **실시간 동기화**: 프로필 업데이트 시 모든 Avatar 자동 반영
4. ✅ **Svelte 5 Map 반응성**: Map 불변성 패턴으로 반응성 보장
5. ✅ **Svelte 5 $derived 규칙 준수**: 부작용과 순수 계산의 명확한 분리
6. ✅ **성능 최적화**: 캐시 히트율 향상, 네트워크 요청 감소

**핵심 기술:**
- Svelte 5 runes ($state, $derived, $effect)
- Firebase Realtime Database (onValue)
- Map 불변성 패턴
- 중앙 집중식 캐시 관리
- 관심사의 분리 (Separation of Concerns)

**주요 교훈:**
- Svelte 5는 `$derived` 내부에서 상태 변경을 엄격히 금지
- 부작용(side effects)은 `$effect`에서, 순수 계산(pure computations)은 `$derived`에서
- API 설계 시 읽기와 쓰기를 명확히 분리하면 반응성 문제를 예방할 수 있음

**다음 단계:**
- 프로덕션 배포 전 디버깅 로그 정리
- 타임아웃 및 재시도 로직 추가
- 캐시 만료 정책 구현
- 단위 테스트 작성
