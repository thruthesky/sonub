---
name: sonub-firebase-realtime-database
version: 1.0.0
description: Firebase Realtime Database 유틸리티 라이브러리 구현
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
license: GPL-3.0
created: 2025-11-09
updated: 2025-11-10
step: 30
priority: "***"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
tags:
  - firebase
  - rtdb
  - realtime-database
  - svelte
  - store
  - 유틸리티
  - 라이브러리
---

# Firebase Realtime Database 유틸리티 라이브러리

## 1. 개요

### 1.1 목적

이 문서는 Sonub 프로젝트에서 Firebase Realtime Database(RTDB)를 쉽게 사용할 수 있도록 제공하는 유틸리티 라이브러리를 설명합니다. 이 라이브러리는 Svelte 5 runes를 사용하여 RTDB와의 상호작용을 간소화하고, 실시간 데이터 구독을 편리하게 만듭니다.

### 1.2 주요 기능

- RTDB 읽기/쓰기/업데이트/삭제 간편 API 제공
- 실시간 데이터 구독을 위한 Svelte 스토어 생성
- 실시간 데이터 구독 시 로딩/에러 상태 자동 관리
- 중복 RTDB 리스너 방지를 위한 구조 제공
- 온라인 상태 관리 기능

### 1.3 파일 위치

- **파일**: `src/lib/stores/database.svelte.ts`
- **타입 정의**: 모든 타입은 동일 파일 내에 정의
- **환경**: Svelte 5, Firebase JS SDK 11.0.0+

### 1.4 사전 요구사항

- ✅ Firebase JS SDK 설치 완료 (sonub-setup-firebase.md 참조)
- ✅ SvelteKit 5 프로젝트 설정 완료
- ✅ Svelte 5 runes 이해 ($state, $derived, $effect)
- ✅ Firebase Realtime Database 기본 구조 이해 (sonub-firebase-database-structure.md 참조)

## 2. 요구사항

### 2.1 기술 요구사항

**개발 환경:**
- SvelteKit: 2.47.1 이상
- Svelte: 5.0.0 이상
- Node.js: v18.0.0 이상
- Firebase JS SDK: 11.0.0 이상

### 2.2 기능 요구사항

**RTDB 유틸리티 라이브러리 (database.svelte.ts):**
- ✅ 실시간 데이터 구독 스토어 생성 (`createRealtimeStore`, `rtdbStore`)
- ✅ 데이터 쓰기 (`writeData`)
- ✅ 데이터 부분 업데이트 (`updateData`)
- ✅ 데이터 삭제 (`deleteData`)
- ✅ 자동 키 생성 데이터 추가 (`pushData`)
- ✅ 일회성 데이터 읽기 (`readData`)
- ✅ 온라인 상태 관리 (`setupPresence`)

**실시간 데이터 구독 스토어:**
- ✅ 로딩/에러 상태 자동 추적
- ✅ 기본값 제공 옵션
- ✅ TypeScript 제네릭 지원

## 3. 워크플로우

### 3.1 개발 작업 순서

이 라이브러리는 **Firebase Realtime Database 유틸리티 라이브러리를 생성, 구현, 테스트**하는 과정입니다.

- ✅ **작업 전 확인 사항**:
  - Firebase 설정 완료 (sonub-setup-firebase.md 참조)
  - RTDB 데이터 구조 이해 (sonub-firebase-database-structure.md 참조)
  - 개발 환경 준비 (Node.js, Firebase 프로젝트 설정)

- ✅ **작업 후 확인 사항**:
  - Firebase 설정 (sonub-setup-firebase.md 참조)
  - RTDB 데이터 구조 (sonub-firebase-database-structure.md 참조)
  - 보안 규칙 설정 (읽기/쓰기 권한)

### 3.2 개발 유틸리티 구현 프로세스

**기능 우선순위:**

1. **타입 정의** (RealtimeStoreState, DataOperationResult 등)
2. **createRealtimeStore** (실시간 구독 핵심 기능)
3. **writeData** (데이터 쓰기)
4. **updateData** (데이터 업데이트)
5. **deleteData** (데이터 삭제)
6. **pushData** (데이터 추가)
7. **readData** (일회성 읽기)
8. **setupPresence** (온라인 상태 관리)

## 4. 아키텍처

### 4.1 전체 구조

```
src/lib/stores/database.svelte.ts
├── 타입 정의
│   ├── RealtimeStoreState<T>      (데이터, 로딩, 에러 상태)
│   ├── RealtimeStore<T>            (Svelte 스토어 + unsubscribe)
│   ├── DataOperationResult         (성공/실패 결과)
│   ├── PushDataResult              (생성된 키 포함)
│   ├── ReadDataResult<T>           (읽기 결과 + 데이터)
│   └── UserStatus                  (온라인 상태)
│
├── 실시간 구독 함수
│   ├── createRealtimeStore<T>()    (실시간 데이터 구독)
│   └── rtdbStore (alias)           (짧은 이름 별칭)
│
├── CRUD 함수
│   ├── writeData()                 (데이터 쓰기)
│   ├── updateData()                (데이터 업데이트)
│   ├── deleteData()                (데이터 삭제)
│   ├── pushData()                  (데이터 추가)
│   └── readData<T>()               (데이터 읽기)
│
└── 온라인 상태 관리
    └── setupPresence()             (온라인/오프라인 상태)
```

### 4.2 실시간 구독 동작 흐름

```
사용자 컴포넌트
    ↓
┌─────────────────────────────────────────────┐
│ createRealtimeStore('posts')                │
│ - path 지정                                 │
│ - defaultValue 지정 (선택)                  │
└─────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────┐
│ Svelte 스토어 생성                          │
│ - 초기 상태: {data: null, loading: true}   │
└─────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────┐
│ RTDB onValue 리스너 등록                    │
│ - 경로: /posts                              │
│ - 실시간 데이터 수신 대기                   │
└─────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────┐
│ 데이터 수신                                 │
│ - snapshot.val()로 데이터 추출              │
│ - null이면 defaultValue 사용                │
└─────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────┐
│ 스토어 업데이트                             │
│ - data: 받은 데이터                         │
│ - loading: false                            │
│ - error: null                               │
└─────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────┐
│ 컴포넌트 자동 re-render                     │
│ - $posts.data 사용 가능                     │
└─────────────────────────────────────────────┘
```

## 5. 구현 상세

### 5.1 타입 정의

#### 5.1.1 RealtimeStoreState<T>

실시간 스토어의 상태를 나타내는 타입입니다.

```typescript
export interface RealtimeStoreState<T> {
	/** 데이터베이스에서 가져온 데이터 */
	data: T | null;
	/** 데이터 로딩 중 여부 */
	loading: boolean;
	/** 에러 발생 시 에러 객체 */
	error: Error | null;
}
```

**사용 예:**
```typescript
const posts = createRealtimeStore<Post[]>('posts');
// $posts는 RealtimeStoreState<Post[]> 타입
// $posts.data: Post[] | null
// $posts.loading: boolean
// $posts.error: Error | null
```

#### 5.1.2 RealtimeStore<T>

Svelte 스토어에 `unsubscribe` 메서드를 추가한 타입입니다.

```typescript
export interface RealtimeStore<T> extends Writable<RealtimeStoreState<T>> {
	/** 데이터베이스 리스너 구독 해제 함수 */
	unsubscribe: () => void;
}
```

**특징:**
- Svelte의 `Writable` 인터페이스 확장
- `unsubscribe()` 메서드로 RTDB 리스너 해제

#### 5.1.3 DataOperationResult

데이터 작업 결과를 나타내는 타입입니다.

```typescript
export interface DataOperationResult {
	/** 작업 성공 여부 */
	success: boolean;
	/** 에러 메시지 (실패 시) */
	error?: string;
}
```

**사용 예:**
```typescript
const result = await writeData('users/user123', userData);
if (result.success) {
	console.log('저장 성공');
} else {
	console.error('저장 실패:', result.error);
}
```

#### 5.1.4 PushDataResult

데이터 추가 작업 결과 (생성된 키 포함)

```typescript
export interface PushDataResult extends DataOperationResult {
	/** 생성된 키 (push 작업 시) */
	key?: string | null;
}
```

**사용 예:**
```typescript
const result = await pushData('posts', newPost);
if (result.success) {
	console.log('생성된 게시글 ID:', result.key);
}
```

#### 5.1.5 ReadDataResult<T>

데이터 읽기 작업 결과

```typescript
export interface ReadDataResult<T> extends DataOperationResult {
	/** 읽어온 데이터 */
	data?: T | null;
}
```

### 5.2 createRealtimeStore()

**목적**: 실시간 데이터 구독을 위한 Svelte 스토어 생성

**함수 시그니처:**
```typescript
export function createRealtimeStore<T = any>(
	path: string,
	defaultValue?: T
): RealtimeStore<T>
```

**파라미터:**
- `path`: RTDB 경로 (예: `'posts'`, `'users/user123'`)
- `defaultValue`: 노드가 존재하지 않을 때 사용할 기본값 (선택)

**반환값:**
- `RealtimeStore<T>`: Svelte 스토어 (`{ data, loading, error }`)

**구현 상세:**

```typescript
export function createRealtimeStore<T = any>(
	path: string,
	defaultValue?: T
): RealtimeStore<T> {
	// Firebase RTDB가 초기화되지 않은 경우 에러 처리
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		const { subscribe, set: setStore } = writable<RealtimeStoreState<T>>({
			data: defaultValue ?? null,
			loading: false,
			error: new Error('Firebase Realtime Database가 초기화되지 않았습니다.')
		});

		return {
			subscribe,
			set: setStore,
			update: (_updater) => {
				throw new Error('직접 업데이트는 지원하지 않습니다. Firebase를 통해 데이터를 변경하세요.');
			},
			unsubscribe: () => {}
		};
	}

	// 초기 상태: data는 defaultValue 또는 null, loading은 true, error는 null
	const { subscribe, set: setStore } = writable<RealtimeStoreState<T>>({
		data: defaultValue ?? null,
		loading: true,
		error: null
	});

	const dbRef: DatabaseReference = ref(rtdb, path);

	// 데이터 변경 실시간 감지 (성공 및 실패 콜백 포함)
	onValue(
		dbRef,
		(snapshot) => {
			// 데이터 로드 성공
			const data = snapshot.val() as T | null;
			// 노드가 존재하지 않으면 (data === null) defaultValue 사용
			setStore({
				data: data !== null ? data : (defaultValue ?? null),
				loading: false,
				error: null
			});
			console.log(`✅ 실시간 데이터 로드 성공: ${path}`, data !== null ? data : (defaultValue ?? null));
		},
		(error) => {
			// 데이터 로드 실패 (권한 오류, 네트워크 오류 등)
			console.error(`❌ 실시간 데이터 로드 실패: ${path}`, error);
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
			throw new Error('직접 업데이트는 지원하지 않습니다. Firebase를 통해 데이터를 변경하세요.');
		},
		unsubscribe: () => off(dbRef)
	};
}
```

**주요 특징:**
1. **RTDB 초기화 확인**: `rtdb`가 null이면 에러 스토어 반환
2. **초기 로딩 상태**: `loading: true`로 시작
3. **onValue 리스너**: 실시간 데이터 수신
4. **defaultValue 처리**: 노드가 없으면 기본값 사용
5. **에러 처리**: 권한 오류, 네트워크 오류 자동 처리

**사용 예제:**

```svelte
<script lang="ts">
	import { createRealtimeStore } from '$lib/stores/database.svelte';

	interface Post {
		id: string;
		title: string;
		content: string;
	}

	// 실시간 구독
	const posts = createRealtimeStore<Post[]>('posts');
</script>

{#if $posts.loading}
	<p>로딩 중...</p>
{:else if $posts.error}
	<p>에러: {$posts.error.message}</p>
{:else if $posts.data}
	{#each $posts.data as post}
		<div>
			<h2>{post.title}</h2>
			<p>{post.content}</p>
		</div>
	{/each}
{:else}
	<p>게시글이 없습니다.</p>
{/if}
```

### 5.3 rtdbStore (별칭)

**목적**: `createRealtimeStore()`의 짧은 별칭

```typescript
export const rtdbStore = createRealtimeStore;
```

**사용 예:**
```typescript
// 긴 이름
const posts = createRealtimeStore<Post[]>('posts');

// 짧은 이름 (권장)
const posts = rtdbStore<Post[]>('posts');

// 두 방식 모두 동일합니다
```

### 5.4 writeData()

**목적**: 데이터 쓰기 (기존 데이터 덮어쓰기)

**함수 시그니처:**
```typescript
export async function writeData(path: string, data: any): Promise<DataOperationResult>
```

**파라미터:**
- `path`: RTDB 경로
- `data`: 저장할 데이터 (객체, 배열, 문자열, 숫자 등)

**반환값:**
- `DataOperationResult`: `{ success: boolean, error?: string }`

**구현:**
```typescript
export async function writeData(path: string, data: any): Promise<DataOperationResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		await set(dbRef, data);
		console.log(`✅ 데이터 쓰기 성공: ${path}`);
		return { success: true };
	} catch (error) {
		console.error('❌ 데이터 쓰기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}
```

**사용 예제:**
```typescript
// 사용자 프로필 저장
const result = await writeData('users/user123', {
	displayName: 'JaeHo Song',
	photoUrl: 'https://example.com/photo.jpg',
	gender: 'M',
	birthYear: 1990
});

if (result.success) {
	console.log('프로필 저장 완료');
} else {
	console.error('프로필 저장 실패:', result.error);
}
```

### 5.5 updateData()

**목적**: 데이터 부분 업데이트 (기존 데이터 유지)

**함수 시그니처:**
```typescript
export async function updateData(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult>
```

**파라미터:**
- `path`: RTDB 경로
- `updates`: 업데이트할 필드들 `{ field1: value1, field2: value2 }`

**구현:**
```typescript
export async function updateData(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		await update(dbRef, updates);
		console.log(`✅ 데이터 업데이트 성공: ${path}`, updates);
		return { success: true };
	} catch (error) {
		console.error('❌ 데이터 업데이트 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}
```

**사용 예제:**
```typescript
// 사용자 나이만 업데이트 (다른 필드는 유지)
const result = await updateData('users/user123', {
	birthYear: 1991,
	updatedAt: Date.now()
});
```

**writeData vs updateData:**
```typescript
// writeData: 전체 덮어쓰기
await writeData('users/user123', { displayName: 'John' });
// 결과: { displayName: 'John' } (기존 photoUrl, gender 등 모두 삭제됨)

// updateData: 부분 업데이트
await updateData('users/user123', { displayName: 'John' });
// 결과: { displayName: 'John', photoUrl: '...', gender: 'M', ... } (기존 필드 유지)
```

### 5.6 deleteData()

**목적**: 데이터 삭제

**함수 시그니처:**
```typescript
export async function deleteData(path: string): Promise<DataOperationResult>
```

**구현:**
```typescript
export async function deleteData(path: string): Promise<DataOperationResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		await remove(dbRef);
		console.log(`✅ 데이터 삭제 성공: ${path}`);
		return { success: true };
	} catch (error) {
		console.error('❌ 데이터 삭제 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}
```

**사용 예제:**
```typescript
// 게시글 삭제
const result = await deleteData('posts/post123');

if (result.success) {
	console.log('게시글 삭제 완료');
}
```

### 5.7 pushData()

**목적**: 새 항목 추가 (자동 생성된 키 사용)

**함수 시그니처:**
```typescript
export async function pushData(path: string, data: any): Promise<PushDataResult>
```

**반환값:**
- `PushDataResult`: `{ success: boolean, key?: string, error?: string }`

**구현:**
```typescript
export async function pushData(path: string, data: any): Promise<PushDataResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		const newRef = push(dbRef);
		await set(newRef, data);
		console.log(`✅ 데이터 추가 성공: ${path}, 생성된 키: ${newRef.key}`);
		return { success: true, key: newRef.key };
	} catch (error) {
		console.error('❌ 데이터 추가 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}
```

**사용 예제:**
```typescript
// 새 게시글 추가
const result = await pushData('posts', {
	title: '제목',
	content: '내용',
	authorId: 'user123',
	createdAt: Date.now()
});

if (result.success) {
	console.log('생성된 게시글 ID:', result.key);
	// 결과: 생성된 게시글 ID: -NqxK9Z1mX8hY2wVtUvZ
}
```

**자동 생성된 키:**
- Firebase가 타임스탬프 기반으로 고유 키 생성
- 예: `-NqxK9Z1mX8hY2wVtUvZ`
- 시간 순서대로 정렬 가능

### 5.8 readData()

**목적**: 데이터 한 번만 읽기 (실시간 구독 없음)

**함수 시그니처:**
```typescript
export async function readData<T = any>(path: string): Promise<ReadDataResult<T>>
```

**반환값:**
- `ReadDataResult<T>`: `{ success: boolean, data?: T | null, error?: string }`

**구현:**
```typescript
export async function readData<T = any>(path: string): Promise<ReadDataResult<T>> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		const snapshot = await get(dbRef);

		if (snapshot.exists()) {
			console.log(`✅ 데이터 읽기 성공: ${path}`);
			return { success: true, data: snapshot.val() as T };
		} else {
			console.log(`ℹ️ 데이터가 존재하지 않음: ${path}`);
			return { success: true, data: null };
		}
	} catch (error) {
		console.error('❌ 데이터 읽기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}
```

**사용 예제:**
```typescript
// 사용자 프로필 한 번만 읽기
const result = await readData<UserProfile>('users/user123');

if (result.success && result.data) {
	console.log('사용자 이름:', result.data.displayName);
} else if (result.success && !result.data) {
	console.log('사용자가 존재하지 않습니다.');
} else {
	console.error('읽기 실패:', result.error);
}
```

**createRealtimeStore vs readData:**
```typescript
// createRealtimeStore: 실시간 구독 (데이터 변경 시 자동 업데이트)
const posts = createRealtimeStore<Post[]>('posts');
// $posts.data는 자동으로 업데이트됨

// readData: 한 번만 읽기 (데이터 변경 시 업데이트 안 됨)
const result = await readData<Post[]>('posts');
// result.data는 고정된 값 (업데이트 없음)
```

### 5.9 setupPresence()

**목적**: 온라인/오프라인 상태 관리

**함수 시그니처:**
```typescript
export function setupPresence(userId: string): () => void
```

**파라미터:**
- `userId`: 사용자 ID

**반환값:**
- 상태 관리 해제 함수

**구현:**
```typescript
export function setupPresence(userId: string): () => void {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return () => {};
	}

	const userStatusRef = ref(rtdb, `status/${userId}`);
	const connectedRef = ref(rtdb, '.info/connected');

	// Firebase 연결 상태 모니터링
	onValue(connectedRef, (snapshot) => {
		if (snapshot.val() === true) {
			// 온라인 상태로 설정
			set(userStatusRef, {
				online: true,
				lastSeen: Date.now()
			});

			// 연결이 끊기면 오프라인으로 변경
			onValue(ref(rtdb!, '.info/connected'), (snap) => {
				if (!snap.val()) {
					update(userStatusRef, {
						online: false,
						lastSeen: Date.now()
					});
				}
			});
		}
	});

	return () => {
		// 수동으로 오프라인 상태로 설정
		update(userStatusRef, {
			online: false,
			lastSeen: Date.now()
		});
		off(connectedRef);
	};
}
```

**동작 원리:**
1. `.info/connected` 노드 구독 (Firebase 연결 상태)
2. 연결 시: `status/{userId}` 노드를 `{ online: true, lastSeen: now }` 로 설정
3. 연결 끊김 시: `{ online: false, lastSeen: now }` 로 자동 업데이트
4. 수동 해제: `cleanup()` 함수 호출 시 오프라인 설정

**사용 예제:**
```typescript
import { onAuthStateChanged } from 'firebase/auth';
import { auth } from '$lib/firebase';
import { setupPresence } from '$lib/stores/database.svelte';

let cleanupPresence: (() => void) | null = null;

onAuthStateChanged(auth, (user) => {
	if (user) {
		// 로그인 시 온라인 상태 관리 시작
		cleanupPresence = setupPresence(user.uid);
	} else {
		// 로그아웃 시 정리
		if (cleanupPresence) {
			cleanupPresence();
			cleanupPresence = null;
		}
	}
});
```

**데이터 구조:**
```json
{
  "status": {
    "user123": {
      "online": true,
      "lastSeen": 1699876543210
    },
    "user456": {
      "online": false,
      "lastSeen": 1699876123456
    }
  }
}
```

## 6. 사용 예제

### 6.1 기본 사용법

```svelte
<script lang="ts">
	import { createRealtimeStore, writeData, updateData, deleteData, pushData, readData } from '$lib/stores/database.svelte';

	interface Post {
		id: string;
		title: string;
		content: string;
		authorId: string;
		createdAt: number;
	}

	// 실시간 구독
	const posts = createRealtimeStore<Post[]>('posts');

	// 새 게시글 추가
	async function addPost(title: string, content: string) {
		const result = await pushData('posts', {
			title,
			content,
			authorId: 'user123',
			createdAt: Date.now()
		});

		if (result.success) {
			console.log('게시글 추가 성공:', result.key);
		}
	}

	// 게시글 수정
	async function updatePost(postId: string, updates: Partial<Post>) {
		const result = await updateData(`posts/${postId}`, updates);
		if (result.success) {
			console.log('게시글 수정 완료');
		}
	}

	// 게시글 삭제
	async function removePost(postId: string) {
		const result = await deleteData(`posts/${postId}`);
		if (result.success) {
			console.log('게시글 삭제 완료');
		}
	}
</script>

{#if $posts.loading}
	<p>로딩 중...</p>
{:else if $posts.error}
	<p>에러: {$posts.error.message}</p>
{:else if $posts.data && $posts.data.length > 0}
	<ul>
		{#each $posts.data as post}
			<li>
				<h3>{post.title}</h3>
				<p>{post.content}</p>
				<button onclick={() => updatePost(post.id, { title: '수정된 제목' })}>수정</button>
				<button onclick={() => removePost(post.id)}>삭제</button>
			</li>
		{/each}
	</ul>
{:else}
	<p>게시글이 없습니다.</p>
{/if}

<button onclick={() => addPost('새 제목', '새 내용')}>게시글 추가</button>
```

### 6.2 기본값 사용

```svelte
<script lang="ts">
	import { createRealtimeStore } from '$lib/stores/database.svelte';

	// 좋아요 수 구독 (기본값: 0)
	const likeCount = createRealtimeStore<number>('likes/post-123', 0);
	// 노드가 없으면 0, 있으면 해당 값

	// 설정 구독 (기본값: { theme: 'light', language: 'ko' })
	const settings = createRealtimeStore({
		theme: 'light',
		language: 'ko'
	});
</script>

<p>좋아요: {$likeCount.data ?? 0}</p>
```

### 6.3 일회성 읽기

```typescript
import { readData } from '$lib/stores/database.svelte';

// 사용자 프로필 한 번만 읽기
async function loadUserProfile(userId: string) {
	const result = await readData<UserProfile>(`users/${userId}`);

	if (result.success && result.data) {
		return result.data;
	} else if (result.success && !result.data) {
		console.log('사용자가 존재하지 않습니다.');
		return null;
	} else {
		console.error('읽기 실패:', result.error);
		throw new Error(result.error);
	}
}
```

## 7. 테스트 및 검증

### 7.1 실시간 구독 테스트

**시나리오:**
1. 브라우저에서 페이지 열기
2. Firebase Console에서 데이터 직접 수정
3. 페이지 새로고침 없이 자동으로 업데이트되는지 확인

**예상 로그:**
```
✅ 실시간 데이터 로드 성공: posts
(데이터 변경)
✅ 실시간 데이터 로드 성공: posts
```

### 7.2 CRUD 테스트

```typescript
// 1. 쓰기 테스트
await writeData('test/data', { value: 123 });
// Firebase Console 확인: test/data = { value: 123 }

// 2. 업데이트 테스트
await updateData('test/data', { value: 456 });
// Firebase Console 확인: test/data = { value: 456 }

// 3. 읽기 테스트
const result = await readData('test/data');
console.log(result.data); // { value: 456 }

// 4. 삭제 테스트
await deleteData('test/data');
// Firebase Console 확인: test/data 노드 삭제됨
```

### 7.3 온라인 상태 테스트

**시나리오:**
1. 로그인 후 `setupPresence(user.uid)` 호출
2. Firebase Console에서 `status/{uid}` 노드 확인
3. 네트워크 연결 끊기 (브라우저 개발자 도구에서 Offline 설정)
4. `status/{uid}/online`이 `false`로 변경되는지 확인

## 8. 프로덕션 정리

### 8.1 디버깅 로그 제거

프로덕션 배포 전 다음 로그 제거:
- `console.log` (성능 이슈)
- `console.error` (민감 정보 노출 가능)

### 8.2 환경 변수 기반 로그

```typescript
const DEBUG = import.meta.env.DEV;

if (DEBUG) {
	console.log(`✅ 실시간 데이터 로드 성공: ${path}`);
}
```

## 9. 보안 고려사항

### 9.1 RTDB 보안 규칙

```json
{
  "rules": {
    "users": {
      "$uid": {
        ".read": true,
        ".write": "$uid === auth.uid"
      }
    },
    "posts": {
      "$postId": {
        ".read": true,
        ".write": "auth != null"
      }
    },
    "status": {
      "$uid": {
        ".read": true,
        ".write": "$uid === auth.uid"
      }
    }
  }
}
```

## 10. 확장 아이디어

### 10.1 페이지네이션

```typescript
export function createPaginatedStore<T>(
	path: string,
	pageSize: number
): RealtimeStore<T> {
	// limitToFirst, startAt, endAt 사용
	// ...
}
```

### 10.2 필터링

```typescript
export function createFilteredStore<T>(
	path: string,
	filter: (item: T) => boolean
): RealtimeStore<T[]> {
	// orderByChild, equalTo 사용
	// ...
}
```

## 11. 관련 문서

- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 설정
- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md): RTDB 구조
- [sonub-user-profile-store.md](./sonub-user-profile-store.md): UserProfileStore 구현
- [Firebase RTDB 문서](https://firebase.google.com/docs/database): 공식 문서

## 12. 변경 이력

| 버전 | 날짜 | 변경 내용 |
|------|------|-----------|
| 1.0.0 | 2025-11-09 | 초기 구현: createRealtimeStore, CRUD 함수, setupPresence, 전체 타입 정의 |

## 13. 결론

RTDB 유틸리티 라이브러리는 다음과 같은 문제를 해결했습니다:

1. ✅ **간편한 API**: 복잡한 Firebase 코드를 간단한 함수 호출로 단순화
2. ✅ **실시간 구독**: 로딩/에러 상태를 자동으로 추적하는 Svelte 스토어
3. ✅ **타입 안전성**: TypeScript 제네릭으로 타입 안전성 보장
4. ✅ **에러 처리**: 모든 함수에서 일관된 에러 처리
5. ✅ **온라인 상태 관리**: `.info/connected`를 사용한 자동 상태 관리

**핵심 기술:**
- Svelte 5 stores (writable)
- Firebase Realtime Database (onValue, set, update, remove, push, get)
- TypeScript 제네릭
- 에러 처리 패턴

**다음 단계:**
- 페이지네이션 기능 추가
- 필터링 기능 추가
- 단위 테스트 작성
- 프로덕션 배포 전 로그 정리
