---
name: sonub-store-database
version: 1.0.0
description: Firebase Realtime Database 유틸리티 스토어 - createRealtimeStore, CRUD 함수, 온라인 상태 관리
author: Claude Code
email: noreply@anthropic.com
homepage: https://github.com/thruthesky/
license: GPL-3.0
created: 2025-11-10
updated: 2025-11-10
step: 46
priority: "***"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
tags:
  - firebase
  - rtdb
  - realtime-database
  - svelte5
  - store
  - crud
  - utilities
---

# 데이터베이스 스토어 (Database Store)

## 1. 개요

### 1.1 목적과 배경

#### 1.1.1 해결하려는 문제

Firebase Realtime Database(RTDB)를 사용할 때 다음과 같은 문제가 발생합니다:

1. **반복적인 API 호출**: `ref()`, `onValue()`, `set()`, `update()` 등 Firebase SDK를 매번 직접 호출
2. **로딩/에러 상태 관리**: 컴포넌트마다 loading, error 상태를 수동으로 관리
3. **실시간 구독 관리**: `onValue()` 리스너를 수동으로 등록/해제해야 함
4. **타입 안전성**: 제네릭 타입이 없어 데이터 구조 오류 발생 가능
5. **중복 코드**: CRUD 로직이 컴포넌트마다 중복 작성됨

**이 스토어가 해결하는 문제:**
- 공통 CRUD 함수 제공으로 코드 중복 제거
- 실시간 스토어로 loading/error 상태 자동 관리
- TypeScript 제네릭으로 타입 안전성 보장
- setupPresence로 온라인 상태 자동 관리

#### 1.1.2 핵심 기능

1. **실시간 스토어 (createRealtimeStore)**
   - Svelte 5 Writable 스토어 기반 실시간 데이터 구독
   - loading, error 상태 자동 관리
   - TypeScript 제네릭 지원

2. **CRUD 함수**
   - `writeData`: 데이터 완전 교체 (set)
   - `updateData`: 데이터 부분 업데이트 (merge)
   - `deleteData`: 데이터 삭제
   - `pushData`: 새 아이템 추가 (auto-generated key)
   - `readData`: 일회성 데이터 읽기

3. **온라인 상태 관리 (setupPresence)**
   - 사용자의 온라인/오프라인 상태 자동 트래킹
   - 연결 해제 시 자동으로 상태 업데이트
   - 중복 리스너 방지

### 1.2 파일 위치

- **파일**: `src/lib/stores/database.svelte.ts`
- **타입 정의**: 동일 파일 내 정의
- **의존성**: `$lib/firebase.ts`, Firebase SDK
- **환경**: Svelte 5, TypeScript

### 1.3 사전 요구사항

- ✅ Firebase JS SDK 설치 ([sonub-setup-firebase.md](./sonub-setup-firebase.md))
- ✅ Firebase Realtime Database 설정 완료
- ✅ Svelte 5 runes 이해 ($state, $derived, $effect)
- ✅ TypeScript 제네릭 기본 지식
- ✅ RTDB 데이터 구조 이해 ([sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md))

## 2. 타입 정의

### 2.1 RealtimeStoreState<T>

```typescript
export interface RealtimeStoreState<T> {
	data: T | null;
	loading: boolean;
	error: Error | null;
}
```

**용도:** 실시간 스토어의 상태를 나타내는 인터페이스

**필드:**

| 필드 | 타입 | 설명 |
|------|------|------|
| `data` | `T \| null` | RTDB에서 가져온 데이터. 데이터가 없으면 `null` |
| `loading` | `boolean` | 데이터 로딩 중이면 `true` |
| `error` | `Error \| null` | 에러 발생 시 Error 객체, 정상이면 `null` |

**제네릭 타입 T:**
- RTDB에서 반환되는 데이터의 타입
- 예: `RealtimeStoreState<User>`, `RealtimeStoreState<Post[]>`

### 2.2 RealtimeStore<T>

```typescript
export interface RealtimeStore<T> extends Writable<RealtimeStoreState<T>> {
	unsubscribe: () => void;
}
```

**용도:** 실시간 스토어 객체의 인터페이스

**상속:** Svelte의 `Writable<RealtimeStoreState<T>>`

**추가 메서드:**

| 메서드 | 타입 | 설명 |
|--------|------|------|
| `unsubscribe` | `() => void` | RTDB 리스너를 해제하는 함수 |

**Writable 메서드:**
- `subscribe(callback)`: 상태 변경 시 콜백 실행
- `set(value)`: 상태 직접 설정 (RTDB에는 반영 안 됨)
- `update(updater)`: 상태 업데이트 (사용 불가, 에러 발생)

### 2.3 DataOperationResult

```typescript
export interface DataOperationResult {
	success: boolean;
	error?: Error;
}
```

**용도:** CRUD 함수의 결과를 나타내는 인터페이스

**필드:**

| 필드 | 타입 | 설명 |
|------|------|------|
| `success` | `boolean` | 작업 성공 시 `true`, 실패 시 `false` |
| `error` | `Error` (optional) | 실패 시 Error 객체 포함 |

**사용 예:**
```typescript
const result = await writeData('users/abc123', { name: 'John' });
if (result.success) {
	console.log('저장 성공');
} else {
	console.error('저장 실패:', result.error);
}
```

### 2.4 PushDataResult

```typescript
export interface PushDataResult extends DataOperationResult {
	key?: string;
}
```

**용도:** `pushData` 함수의 결과를 나타내는 인터페이스

**상속:** `DataOperationResult`

**추가 필드:**

| 필드 | 타입 | 설명 |
|------|------|------|
| `key` | `string` (optional) | 생성된 아이템의 auto-generated key |

**사용 예:**
```typescript
const result = await pushData('posts', { title: 'Hello' });
if (result.success && result.key) {
	console.log('생성된 키:', result.key);
}
```

### 2.5 ReadDataResult<T>

```typescript
export interface ReadDataResult<T> extends DataOperationResult {
	data?: T;
}
```

**용도:** `readData` 함수의 결과를 나타내는 인터페이스

**상속:** `DataOperationResult`

**추가 필드:**

| 필드 | 타입 | 설명 |
|------|------|------|
| `data` | `T` (optional) | 읽어온 데이터 |

**사용 예:**
```typescript
const result = await readData<User>('users/abc123');
if (result.success && result.data) {
	console.log('사용자 이름:', result.data.name);
}
```

## 3. 함수 구현

### 3.1 createRealtimeStore<T>

#### 3.1.1 함수 시그니처

```typescript
export function createRealtimeStore<T = any>(
	path: string,
	defaultValue?: T
): RealtimeStore<T>
```

**별칭:** `rtdbStore` (동일한 함수)

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `path` | `string` | ✅ | RTDB 경로 (예: `users/abc123`) |
| `defaultValue` | `T` | ❌ | 데이터가 없을 때 기본값 |

**반환값:** `RealtimeStore<T>` - 실시간 스토어 객체

#### 3.1.2 전체 구현

```typescript
export function createRealtimeStore<T = any>(
	path: string,
	defaultValue?: T
): RealtimeStore<T> {
	const { subscribe, set: setStore } = writable<RealtimeStoreState<T>>({
		data: defaultValue ?? null,
		loading: true,
		error: null
	});

	const dbRef: DatabaseReference = ref(rtdb, path);

	onValue(
		dbRef,
		(snapshot) => {
			const data = snapshot.val() as T | null;
			setStore({
				data: data !== null ? data : (defaultValue ?? null),
				loading: false,
				error: null
			});
		},
		(error) => {
			setStore({
				data: defaultValue ?? null,
				loading: false,
				error: error as Error
			});
		}
	);

	return {
		subscribe,
		set: setStore,
		update: (_updater) => {
			throw new Error('직접 업데이트는 지원하지 않습니다. writeData 또는 updateData를 사용하세요.');
		},
		unsubscribe: () => off(dbRef)
	};
}

export const rtdbStore = createRealtimeStore;
```

#### 3.1.3 동작 원리

1. **Writable 스토어 생성**
   - 초기 상태: `{ data: defaultValue, loading: true, error: null }`

2. **RTDB 리스너 등록**
   - `onValue(dbRef, successCallback, errorCallback)`
   - 데이터 변경 시 자동으로 `setStore()` 호출

3. **성공 콜백**
   - `snapshot.val()`로 데이터 추출
   - `data !== null` ? 데이터 저장 : 기본값 사용
   - `loading = false`, `error = null`

4. **에러 콜백**
   - 에러 발생 시 `error` 필드에 저장
   - `data`는 기본값으로 설정
   - `loading = false`

5. **메서드 제공**
   - `subscribe`: Svelte에서 자동 구독 (`$store`)
   - `set`: 스토어 상태 직접 설정 (RTDB에는 반영 안 됨)
   - `update`: 사용 금지 (에러 발생)
   - `unsubscribe`: 리스너 해제

#### 3.1.4 사용 예제

```svelte
<script lang="ts">
	import { createRealtimeStore } from '$lib/stores/database.svelte';

	interface User {
		name: string;
		email: string;
	}

	const userStore = createRealtimeStore<User>('users/abc123', { name: '', email: '' });
</script>

{#if $userStore.loading}
	<p>로딩 중...</p>
{:else if $userStore.error}
	<p>에러: {$userStore.error.message}</p>
{:else if $userStore.data}
	<div>
		<p>이름: {$userStore.data.name}</p>
		<p>이메일: {$userStore.data.email}</p>
	</div>
{:else}
	<p>사용자 데이터가 없습니다.</p>
{/if}
```

### 3.2 writeData

#### 3.2.1 함수 시그니처

```typescript
export async function writeData(path: string, data: any): Promise<DataOperationResult>
```

**용도:** RTDB 경로에 데이터를 완전히 교체 (덮어쓰기)

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `path` | `string` | ✅ | RTDB 경로 |
| `data` | `any` | ✅ | 저장할 데이터 |

**반환값:** `Promise<DataOperationResult>`

#### 3.2.2 전체 구현

```typescript
export async function writeData(path: string, data: any): Promise<DataOperationResult> {
	try {
		const dbRef = ref(rtdb, path);
		await set(dbRef, data);
		return { success: true };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}
```

#### 3.2.3 동작 원리

1. `ref(rtdb, path)`로 RTDB 참조 생성
2. `set(dbRef, data)`로 데이터 완전 교체
3. 성공 시 `{ success: true }` 반환
4. 실패 시 `{ success: false, error }` 반환

#### 3.2.4 사용 예제

```typescript
import { writeData } from '$lib/stores/database.svelte';

// 사용자 프로필 완전 교체
const result = await writeData('users/abc123', {
	name: 'John Doe',
	email: 'john@example.com',
	age: 30
});

if (result.success) {
	console.log('저장 성공');
} else {
	console.error('저장 실패:', result.error);
}
```

**주의사항:**
- `set()`은 기존 데이터를 완전히 덮어씁니다
- 부분 업데이트가 필요하면 `updateData()` 사용

### 3.3 updateData

#### 3.3.1 함수 시그니처

```typescript
export async function updateData(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult>
```

**용도:** RTDB 데이터를 부분 업데이트 (병합)

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `path` | `string` | ✅ | RTDB 경로 |
| `updates` | `Record<string, any>` | ✅ | 업데이트할 필드들 |

**반환값:** `Promise<DataOperationResult>`

#### 3.3.2 전체 구현

```typescript
export async function updateData(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult> {
	try {
		const dbRef = ref(rtdb, path);
		await update(dbRef, updates);
		return { success: true };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}
```

#### 3.3.3 동작 원리

1. `ref(rtdb, path)`로 RTDB 참조 생성
2. `update(dbRef, updates)`로 필드 병합
3. 성공 시 `{ success: true }` 반환
4. 실패 시 `{ success: false, error }` 반환

#### 3.3.4 사용 예제

```typescript
import { updateData } from '$lib/stores/database.svelte';

// 사용자 나이만 업데이트 (다른 필드는 유지)
const result = await updateData('users/abc123', {
	age: 31
});

if (result.success) {
	console.log('업데이트 성공');
}
```

**writeData vs updateData:**

```typescript
// writeData: 전체 교체
await writeData('users/abc123', { age: 31 });
// 결과: { age: 31 } (name, email 삭제됨)

// updateData: 병합
await updateData('users/abc123', { age: 31 });
// 결과: { name: 'John', email: 'john@example.com', age: 31 }
```

### 3.4 deleteData

#### 3.4.1 함수 시그니처

```typescript
export async function deleteData(path: string): Promise<DataOperationResult>
```

**용도:** RTDB 경로의 데이터 삭제

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `path` | `string` | ✅ | 삭제할 RTDB 경로 |

**반환값:** `Promise<DataOperationResult>`

#### 3.4.2 전체 구현

```typescript
export async function deleteData(path: string): Promise<DataOperationResult> {
	try {
		const dbRef = ref(rtdb, path);
		await remove(dbRef);
		return { success: true };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}
```

#### 3.4.3 동작 원리

1. `ref(rtdb, path)`로 RTDB 참조 생성
2. `remove(dbRef)`로 데이터 삭제
3. 성공 시 `{ success: true }` 반환
4. 실패 시 `{ success: false, error }` 반환

#### 3.4.4 사용 예제

```typescript
import { deleteData } from '$lib/stores/database.svelte';

// 사용자 프로필 삭제
const result = await deleteData('users/abc123');

if (result.success) {
	console.log('삭제 성공');
} else {
	console.error('삭제 실패:', result.error);
}
```

**주의사항:**
- 삭제된 데이터는 복구할 수 없습니다
- 하위 경로도 모두 삭제됩니다 (예: `users/abc123` 삭제 시 `users/abc123/profile`도 삭제)

### 3.5 pushData

#### 3.5.1 함수 시그니처

```typescript
export async function pushData(path: string, data: any): Promise<PushDataResult>
```

**용도:** RTDB 리스트에 새 아이템 추가 (auto-generated key)

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `path` | `string` | ✅ | 리스트 경로 (예: `posts`) |
| `data` | `any` | ✅ | 추가할 데이터 |

**반환값:** `Promise<PushDataResult>` (key 포함)

#### 3.5.2 전체 구현

```typescript
export async function pushData(path: string, data: any): Promise<PushDataResult> {
	try {
		const dbRef = ref(rtdb, path);
		const newRef = push(dbRef);
		await set(newRef, data);
		return { success: true, key: newRef.key ?? undefined };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}
```

#### 3.5.3 동작 원리

1. `ref(rtdb, path)`로 리스트 참조 생성
2. `push(dbRef)`로 새 자식 참조 생성 (auto-generated key)
3. `set(newRef, data)`로 데이터 저장
4. 성공 시 `{ success: true, key }` 반환
5. 실패 시 `{ success: false, error }` 반환

#### 3.5.4 사용 예제

```typescript
import { pushData } from '$lib/stores/database.svelte';

// 새 게시글 추가
const result = await pushData('posts', {
	title: 'Hello World',
	content: 'This is my first post',
	createdAt: Date.now()
});

if (result.success && result.key) {
	console.log('생성된 게시글 ID:', result.key);
	// 예: "-NqVLxxx..."
}
```

**생성되는 데이터 구조:**
```json
{
  "posts": {
    "-NqVLxxx...": {
      "title": "Hello World",
      "content": "This is my first post",
      "createdAt": 1699999999999
    }
  }
}
```

### 3.6 readData<T>

#### 3.6.1 함수 시그니처

```typescript
export async function readData<T = any>(path: string): Promise<ReadDataResult<T>>
```

**용도:** RTDB에서 일회성 데이터 읽기 (실시간 구독 없음)

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `path` | `string` | ✅ | 읽을 RTDB 경로 |

**제네릭 타입 T:** 반환 데이터의 타입

**반환값:** `Promise<ReadDataResult<T>>` (data 포함)

#### 3.6.2 전체 구현

```typescript
export async function readData<T = any>(path: string): Promise<ReadDataResult<T>> {
	try {
		const dbRef = ref(rtdb, path);
		const snapshot = await get(dbRef);
		const data = snapshot.val() as T | null;
		return { success: true, data: data ?? undefined };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}
```

#### 3.6.3 동작 원리

1. `ref(rtdb, path)`로 RTDB 참조 생성
2. `get(dbRef)`로 일회성 읽기
3. `snapshot.val()`로 데이터 추출
4. 성공 시 `{ success: true, data }` 반환
5. 실패 시 `{ success: false, error }` 반환

#### 3.6.4 사용 예제

```typescript
import { readData } from '$lib/stores/database.svelte';

interface User {
	name: string;
	email: string;
}

// 사용자 프로필 읽기
const result = await readData<User>('users/abc123');

if (result.success && result.data) {
	console.log('사용자 이름:', result.data.name);
} else if (result.error) {
	console.error('읽기 실패:', result.error);
} else {
	console.log('데이터가 없습니다.');
}
```

**readData vs createRealtimeStore:**

| 기능 | readData | createRealtimeStore |
|------|----------|---------------------|
| 읽기 방식 | 일회성 | 실시간 구독 |
| 데이터 변경 감지 | ❌ | ✅ |
| 로딩 상태 | ❌ | ✅ (자동) |
| 에러 상태 | ✅ (반환값) | ✅ (스토어) |
| 사용 예 | 한 번만 읽기 | 실시간 동기화 |

### 3.7 setupPresence

#### 3.7.1 함수 시그니처

```typescript
export function setupPresence(uid: string): void
```

**용도:** 사용자의 온라인/오프라인 상태 자동 관리

**파라미터:**

| 파라미터 | 타입 | 필수 | 설명 |
|----------|------|------|------|
| `uid` | `string` | ✅ | 사용자 UID |

**반환값:** 없음 (`void`)

#### 3.7.2 전체 구현

```typescript
let presenceInitialized = false;

export function setupPresence(uid: string): void {
	if (presenceInitialized) {
		console.warn('[setupPresence] 이미 초기화되었습니다.');
		return;
	}

	const userStatusRef = ref(rtdb, `/status/${uid}`);
	const connectedRef = ref(rtdb, '.info/connected');

	onValue(connectedRef, (snapshot) => {
		if (snapshot.val() === true) {
			// 연결됨: 온라인 상태로 설정
			set(userStatusRef, {
				state: 'online',
				lastChanged: Date.now()
			});

			// 연결 해제 시 오프라인 상태로 자동 변경
			onDisconnect(userStatusRef).set({
				state: 'offline',
				lastChanged: Date.now()
			});
		}
	});

	presenceInitialized = true;
}
```

#### 3.7.3 동작 원리

1. **중복 초기화 방지**
   - `presenceInitialized` 플래그로 한 번만 실행

2. **연결 상태 감지**
   - `.info/connected` 경로를 구독하여 RTDB 연결 상태 확인
   - 연결되면 `true`, 끊기면 `false`

3. **온라인 상태 설정**
   - 연결 시 `/status/{uid}` 경로에 `{ state: 'online', lastChanged }` 저장

4. **오프라인 상태 예약**
   - `onDisconnect().set()`으로 연결 해제 시 자동으로 오프라인 상태로 변경
   - 앱이 강제 종료되어도 Firebase가 자동으로 실행

#### 3.7.4 사용 예제

**컴포넌트에서 사용:**
```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { setupPresence } from '$lib/stores/database.svelte';

	$effect(() => {
		if (authStore.isAuthenticated && authStore.user) {
			setupPresence(authStore.user.uid);
		}
	});
</script>
```

**RTDB 데이터 구조:**
```json
{
  "status": {
    "abc123": {
      "state": "online",
      "lastChanged": 1699999999999
    },
    "def456": {
      "state": "offline",
      "lastChanged": 1699999999999
    }
  }
}
```

**온라인 사용자 목록 표시:**
```svelte
<script lang="ts">
	import { createRealtimeStore } from '$lib/stores/database.svelte';

	interface UserStatus {
		state: 'online' | 'offline';
		lastChanged: number;
	}

	const statusStore = createRealtimeStore<Record<string, UserStatus>>('status');
</script>

{#if $statusStore.data}
	<h3>온라인 사용자</h3>
	<ul>
		{#each Object.entries($statusStore.data) as [uid, status]}
			{#if status.state === 'online'}
				<li>{uid}</li>
			{/if}
		{/each}
	</ul>
{/if}
```

## 4. 전체 소스 코드

**파일 경로:** `src/lib/stores/database.svelte.ts`

```typescript
import {
	ref,
	set,
	update,
	remove,
	push,
	get,
	onValue,
	off,
	onDisconnect,
	type DatabaseReference
} from 'firebase/database';
import { rtdb } from '$lib/firebase';
import { writable, type Writable } from 'svelte/store';

/**
 * 실시간 스토어 상태 인터페이스
 */
export interface RealtimeStoreState<T> {
	data: T | null;
	loading: boolean;
	error: Error | null;
}

/**
 * 실시간 스토어 인터페이스
 */
export interface RealtimeStore<T> extends Writable<RealtimeStoreState<T>> {
	unsubscribe: () => void;
}

/**
 * 데이터 작업 결과 인터페이스
 */
export interface DataOperationResult {
	success: boolean;
	error?: Error;
}

/**
 * Push 작업 결과 인터페이스 (key 포함)
 */
export interface PushDataResult extends DataOperationResult {
	key?: string;
}

/**
 * Read 작업 결과 인터페이스 (data 포함)
 */
export interface ReadDataResult<T> extends DataOperationResult {
	data?: T;
}

/**
 * 실시간 데이터 구독 스토어 생성
 *
 * @param path - RTDB 경로
 * @param defaultValue - 데이터가 없을 때 기본값
 * @returns RealtimeStore - 실시간 스토어 객체
 *
 * @example
 * ```typescript
 * const userStore = createRealtimeStore<User>('users/abc123');
 * // 사용: $userStore.data, $userStore.loading, $userStore.error
 * ```
 */
export function createRealtimeStore<T = any>(
	path: string,
	defaultValue?: T
): RealtimeStore<T> {
	const { subscribe, set: setStore } = writable<RealtimeStoreState<T>>({
		data: defaultValue ?? null,
		loading: true,
		error: null
	});

	const dbRef: DatabaseReference = ref(rtdb, path);

	onValue(
		dbRef,
		(snapshot) => {
			const data = snapshot.val() as T | null;
			setStore({
				data: data !== null ? data : (defaultValue ?? null),
				loading: false,
				error: null
			});
		},
		(error) => {
			setStore({
				data: defaultValue ?? null,
				loading: false,
				error: error as Error
			});
		}
	);

	return {
		subscribe,
		set: setStore,
		update: (_updater) => {
			throw new Error('직접 업데이트는 지원하지 않습니다. writeData 또는 updateData를 사용하세요.');
		},
		unsubscribe: () => off(dbRef)
	};
}

/**
 * createRealtimeStore의 별칭
 */
export const rtdbStore = createRealtimeStore;

/**
 * RTDB에 데이터 쓰기 (덮어쓰기)
 *
 * @param path - RTDB 경로
 * @param data - 저장할 데이터
 * @returns Promise<DataOperationResult>
 *
 * @example
 * ```typescript
 * const result = await writeData('users/abc123', { name: 'John' });
 * if (result.success) {
 *   console.log('저장 성공');
 * }
 * ```
 */
export async function writeData(path: string, data: any): Promise<DataOperationResult> {
	try {
		const dbRef = ref(rtdb, path);
		await set(dbRef, data);
		return { success: true };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}

/**
 * RTDB 데이터 부분 업데이트 (병합)
 *
 * @param path - RTDB 경로
 * @param updates - 업데이트할 필드들
 * @returns Promise<DataOperationResult>
 *
 * @example
 * ```typescript
 * const result = await updateData('users/abc123', { age: 31 });
 * ```
 */
export async function updateData(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult> {
	try {
		const dbRef = ref(rtdb, path);
		await update(dbRef, updates);
		return { success: true };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}

/**
 * RTDB 데이터 삭제
 *
 * @param path - 삭제할 RTDB 경로
 * @returns Promise<DataOperationResult>
 *
 * @example
 * ```typescript
 * const result = await deleteData('users/abc123');
 * ```
 */
export async function deleteData(path: string): Promise<DataOperationResult> {
	try {
		const dbRef = ref(rtdb, path);
		await remove(dbRef);
		return { success: true };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}

/**
 * RTDB 리스트에 새 아이템 추가 (auto-generated key)
 *
 * @param path - 리스트 경로
 * @param data - 추가할 데이터
 * @returns Promise<PushDataResult> (생성된 key 포함)
 *
 * @example
 * ```typescript
 * const result = await pushData('posts', { title: 'Hello' });
 * console.log('생성된 키:', result.key);
 * ```
 */
export async function pushData(path: string, data: any): Promise<PushDataResult> {
	try {
		const dbRef = ref(rtdb, path);
		const newRef = push(dbRef);
		await set(newRef, data);
		return { success: true, key: newRef.key ?? undefined };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}

/**
 * RTDB에서 일회성 데이터 읽기
 *
 * @param path - 읽을 RTDB 경로
 * @returns Promise<ReadDataResult<T>> (data 포함)
 *
 * @example
 * ```typescript
 * const result = await readData<User>('users/abc123');
 * if (result.success && result.data) {
 *   console.log(result.data.name);
 * }
 * ```
 */
export async function readData<T = any>(path: string): Promise<ReadDataResult<T>> {
	try {
		const dbRef = ref(rtdb, path);
		const snapshot = await get(dbRef);
		const data = snapshot.val() as T | null;
		return { success: true, data: data ?? undefined };
	} catch (error) {
		return { success: false, error: error as Error };
	}
}

/**
 * 온라인 상태 관리 초기화 플래그
 */
let presenceInitialized = false;

/**
 * 사용자 온라인/오프라인 상태 자동 관리
 *
 * @param uid - 사용자 UID
 *
 * @example
 * ```typescript
 * $effect(() => {
 *   if (authStore.isAuthenticated && authStore.user) {
 *     setupPresence(authStore.user.uid);
 *   }
 * });
 * ```
 */
export function setupPresence(uid: string): void {
	if (presenceInitialized) {
		console.warn('[setupPresence] 이미 초기화되었습니다.');
		return;
	}

	const userStatusRef = ref(rtdb, `/status/${uid}`);
	const connectedRef = ref(rtdb, '.info/connected');

	onValue(connectedRef, (snapshot) => {
		if (snapshot.val() === true) {
			// 연결됨: 온라인 상태로 설정
			set(userStatusRef, {
				state: 'online',
				lastChanged: Date.now()
			});

			// 연결 해제 시 오프라인 상태로 자동 변경
			onDisconnect(userStatusRef).set({
				state: 'offline',
				lastChanged: Date.now()
			});
		}
	});

	presenceInitialized = true;
}
```

## 5. 사용 예제 모음

### 5.1 실시간 사용자 프로필 표시

```svelte
<script lang="ts">
	import { createRealtimeStore } from '$lib/stores/database.svelte';

	interface User {
		name: string;
		email: string;
		photoUrl?: string;
	}

	const uid = 'abc123';
	const userStore = createRealtimeStore<User>(`users/${uid}`);
</script>

{#if $userStore.loading}
	<p>프로필 로딩 중...</p>
{:else if $userStore.error}
	<p class="error">에러: {$userStore.error.message}</p>
{:else if $userStore.data}
	<div class="profile">
		{#if $userStore.data.photoUrl}
			<img src={$userStore.data.photoUrl} alt={$userStore.data.name} />
		{/if}
		<h2>{$userStore.data.name}</h2>
		<p>{$userStore.data.email}</p>
	</div>
{:else}
	<p>사용자 정보가 없습니다.</p>
{/if}
```

### 5.2 게시글 목록 실시간 표시

```svelte
<script lang="ts">
	import { createRealtimeStore } from '$lib/stores/database.svelte';

	interface Post {
		title: string;
		content: string;
		createdAt: number;
	}

	const postsStore = createRealtimeStore<Record<string, Post>>('posts');
</script>

{#if $postsStore.data}
	<ul>
		{#each Object.entries($postsStore.data) as [id, post]}
			<li>
				<h3>{post.title}</h3>
				<p>{post.content}</p>
				<small>{new Date(post.createdAt).toLocaleString()}</small>
			</li>
		{/each}
	</ul>
{/if}
```

### 5.3 프로필 수정 폼

```svelte
<script lang="ts">
	import { updateData } from '$lib/stores/database.svelte';
	import { authStore } from '$lib/stores/auth.svelte';

	let name = $state('');
	let age = $state(0);
	let saving = $state(false);
	let message = $state('');

	async function handleSubmit() {
		if (!authStore.user) return;

		saving = true;
		message = '';

		const result = await updateData(`users/${authStore.user.uid}`, {
			name,
			age,
			updatedAt: Date.now()
		});

		saving = false;

		if (result.success) {
			message = '저장되었습니다!';
		} else {
			message = `에러: ${result.error?.message}`;
		}
	}
</script>

<form onsubmit={handleSubmit}>
	<input bind:value={name} placeholder="이름" required />
	<input type="number" bind:value={age} placeholder="나이" required />
	<button type="submit" disabled={saving}>
		{saving ? '저장 중...' : '저장'}
	</button>
	{#if message}
		<p>{message}</p>
	{/if}
</form>
```

### 5.4 새 게시글 작성

```svelte
<script lang="ts">
	import { pushData } from '$lib/stores/database.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';

	let title = $state('');
	let content = $state('');
	let submitting = $state(false);

	async function handleSubmit() {
		if (!authStore.user) return;

		submitting = true;

		const result = await pushData('posts', {
			title,
			content,
			uid: authStore.user.uid,
			createdAt: Date.now()
		});

		submitting = false;

		if (result.success && result.key) {
			goto(`/posts/${result.key}`);
		} else {
			alert(`에러: ${result.error?.message}`);
		}
	}
</script>

<form onsubmit={handleSubmit}>
	<input bind:value={title} placeholder="제목" required />
	<textarea bind:value={content} placeholder="내용" required></textarea>
	<button type="submit" disabled={submitting}>
		{submitting ? '작성 중...' : '작성'}
	</button>
</form>
```

### 5.5 데이터 삭제

```svelte
<script lang="ts">
	import { deleteData } from '$lib/stores/database.svelte';

	async function handleDelete(postId: string) {
		if (!confirm('정말 삭제하시겠습니까?')) return;

		const result = await deleteData(`posts/${postId}`);

		if (result.success) {
			alert('삭제되었습니다.');
		} else {
			alert(`에러: ${result.error?.message}`);
		}
	}
</script>

<button onclick={() => handleDelete('post123')}>삭제</button>
```

### 5.6 온라인 상태 표시

```svelte
<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { setupPresence, createRealtimeStore } from '$lib/stores/database.svelte';

	// 현재 사용자 온라인 상태 설정
	$effect(() => {
		if (authStore.isAuthenticated && authStore.user) {
			setupPresence(authStore.user.uid);
		}
	});

	// 다른 사용자의 온라인 상태 표시
	const userStatus = createRealtimeStore<{ state: string }>('status/abc123');
</script>

{#if $userStatus.data}
	<span class="status-{$userStatus.data.state}">
		{$userStatus.data.state === 'online' ? '🟢 온라인' : '⚫ 오프라인'}
	</span>
{/if}
```

## 6. 테스트

### 6.1 수동 테스트 시나리오

#### 시나리오 1: 실시간 스토어 구독

1. Firebase Console에서 `/test/data` 경로에 `{ message: "Hello" }` 저장
2. 애플리케이션에서 `createRealtimeStore('test/data')` 실행
3. 확인 사항:
   - [ ] `$store.loading`이 처음에는 `true`
   - [ ] 데이터 로드 후 `$store.loading`이 `false`
   - [ ] `$store.data.message` = "Hello"
   - [ ] Firebase Console에서 데이터 수정 시 자동으로 UI 업데이트

#### 시나리오 2: writeData 테스트

1. `writeData('test/user', { name: 'John', age: 30 })` 실행
2. 확인 사항:
   - [ ] `result.success` = true
   - [ ] Firebase Console에서 `/test/user` 경로에 데이터 확인
   - [ ] 기존 데이터가 완전히 교체되었는지 확인

#### 시나리오 3: updateData 테스트

1. Firebase Console에서 `/test/user` = `{ name: 'John', age: 30, city: 'Seoul' }` 저장
2. `updateData('test/user', { age: 31 })` 실행
3. 확인 사항:
   - [ ] `result.success` = true
   - [ ] Firebase Console에서 `age`만 31로 변경
   - [ ] `name`, `city`는 그대로 유지

#### 시나리오 4: pushData 테스트

1. `pushData('test/posts', { title: 'Hello' })` 실행
2. 확인 사항:
   - [ ] `result.success` = true
   - [ ] `result.key`가 auto-generated key (예: "-NqVLxxx")
   - [ ] Firebase Console에서 `/test/posts/{key}` 경로에 데이터 확인

#### 시나리오 5: deleteData 테스트

1. Firebase Console에서 `/test/temp` = `{ data: "test" }` 저장
2. `deleteData('test/temp')` 실행
3. 확인 사항:
   - [ ] `result.success` = true
   - [ ] Firebase Console에서 `/test/temp` 경로가 삭제됨

#### 시나리오 6: setupPresence 테스트

1. 로그인 후 `setupPresence(uid)` 실행
2. 확인 사항:
   - [ ] Firebase Console에서 `/status/{uid}/state` = "online"
   - [ ] 브라우저 탭 닫기 또는 새로고침 후 "offline"으로 자동 변경

## 7. 보안 고려사항

### 7.1 Firebase Security Rules

RTDB Security Rules를 반드시 설정해야 합니다.

**database.rules.json:**
```json
{
  "rules": {
    "users": {
      "$uid": {
        ".read": true,
        ".write": "auth != null && auth.uid === $uid"
      }
    },
    "posts": {
      ".read": true,
      "$postId": {
        ".write": "auth != null"
      }
    },
    "status": {
      "$uid": {
        ".read": true,
        ".write": "auth != null && auth.uid === $uid"
      }
    }
  }
}
```

### 7.2 에러 처리

모든 함수는 에러를 반환하므로 UI에서 처리해야 합니다.

```typescript
const result = await writeData('users/abc123', { name: 'John' });

if (!result.success) {
	// 에러 처리
	if (result.error?.message.includes('permission-denied')) {
		alert('권한이 없습니다.');
	} else {
		alert(`에러: ${result.error?.message}`);
	}
}
```

## 8. 관련 문서

- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 초기화 및 설정
- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md): RTDB 스키마 구조
- [sonub-firebase-realtime-database.md](./sonub-firebase-realtime-database.md): RTDB 사용법 개요
- [sonub-firebase-security.md](./sonub-firebase-security.md): Security Rules 설정

## 9. 버전 히스토리

| 버전 | 날짜 | 변경 사항 |
|------|------|-----------|
| 1.0.0 | 2025-11-10 | 초기 작성: Database Store 전체 구현 명세 작성 |

## 10. 승인

- [ ] 개발자 승인 필요
- [ ] Database Store 구현 완료
- [ ] 모든 CRUD 함수 테스트 완료
- [ ] setupPresence 테스트 완료
- [ ] Security Rules 설정 완료
- [ ] 문서 검토 완료
