---
name: sonub-user-profile-sync
version: 1.0.0
description: Firebase Auth 로그인 시 사용자 프로필 정보를 Realtime Database에 자동 동기화하는 기능
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-09
updated: 2025-11-09
step: 15
priority: "***"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-user-login.md
  - sonub-user-props.md
tags: ["authentication", "database", "sync", "firebase", "rtdb"]
---

# Sonub User Profile Sync - 사용자 프로필 자동 동기화

## 목차

- [Sonub User Profile Sync - 사용자 프로필 자동 동기화](#sonub-user-profile-sync---사용자-프로필-자동-동기화)
  - [목차](#목차)
  - [1. 개요](#1-개요)
    - [1.1 목적](#11-목적)
    - [1.2 범위](#12-범위)
    - [1.3 사전 요구사항](#13-사전-요구사항)
    - [1.4 제외 사항](#14-제외-사항)
  - [2. 아키텍처 설계](#2-아키텍처-설계)
    - [2.1 전체 플로우](#21-전체-플로우)
    - [2.2 데이터 흐름](#22-데이터-흐름)
    - [2.3 책임 분리](#23-책임-분리)
  - [3. 데이터베이스 스키마](#3-데이터베이스-스키마)
    - [3.1 저장 위치](#31-저장-위치)
    - [3.2 동기화 필드](#32-동기화-필드)
    - [3.3 필드 변환 규칙](#33-필드-변환-규칙)
  - [4. 구현 사양](#4-구현-사양)
    - [4.1 syncAuthProfileToRtdb() 함수](#41-syncauthprofiletortdb-함수)
    - [4.2 signInWithGoogle() 함수 수정](#42-signinwithgoogle-함수-수정)
    - [4.3 signInWithApple() 함수 수정](#43-signinwithapple-함수-수정)
    - [4.4 Auth Store 연동](#44-auth-store-연동)
  - [5. 에러 처리](#5-에러-처리)
    - [5.1 에러 유형](#51-에러-유형)
    - [5.2 에러 처리 전략](#52-에러-처리-전략)
    - [5.3 로깅 규칙](#53-로깅-규칙)
  - [6. Cloud Functions 연계](#6-cloud-functions-연계)
    - [6.1 onUserCreate 트리거](#61-onusercreate-트리거)
    - [6.2 onUserUpdate 트리거](#62-onuserupdate-트리거)
    - [6.3 user-props 동기화](#63-user-props-동기화)
  - [7. 테스트 계획](#7-테스트-계획)
    - [7.1 유닛 테스트](#71-유닛-테스트)
    - [7.2 통합 테스트](#72-통합-테스트)
    - [7.3 E2E 테스트](#73-e2e-테스트)
  - [8. 검증 방법](#8-검증-방법)
    - [8.1 Firebase Emulator 검증](#81-firebase-emulator-검증)
    - [8.2 프로덕션 검증](#82-프로덕션-검증)
  - [9. 참고 문서](#9-참고-문서)

---

## 1. 개요

### 1.1 목적

Firebase Authentication 로그인 성공 시, 사용자의 `displayName`과 `photoURL`을 Realtime Database의 `/users/{uid}/` 경로에 자동으로 동기화하여 다음 목표를 달성합니다:

- ✅ **일관성 보장**: Auth와 RTDB 간 사용자 정보 일치
- ✅ **자동화**: 수동 저장 없이 로그인만으로 프로필 생성
- ✅ **Cloud Functions 트리거**: `/users/{uid}` 생성 시 user-props 및 통계 자동 갱신
- ✅ **재사용성**: 모든 로그인 방식(Google, Apple, 향후 추가 provider)에서 동일한 로직 사용

### 1.2 범위

- ✅ **클라이언트 측 동기화**: `auth-helpers.ts`에 `syncAuthProfileToRtdb()` 함수 추가
- ✅ **로그인 플로우 통합**: `signInWithGoogle()`, `signInWithApple()` 함수에 동기화 호출 추가
- ✅ **Auth Store 연동**: `onAuthStateChanged`에서도 동기화 수행
- ✅ **필드 변환**: `photoURL` → `photoUrl` 자동 변환
- ✅ **에러 처리**: 동기화 실패 시 로깅 및 사용자 알림
- ✅ **Cloud Functions 연계**: 첫 생성 시 `onUserCreate` 트리거 실행

### 1.3 사전 요구사항

- ✅ Firebase 프로젝트 설정 완료 ([sonub-setup-firebase.md](./sonub-setup-firebase.md))
- ✅ Firebase Realtime Database 활성화
- ✅ Firebase Authentication 활성화 (Google, Apple provider 설정 완료)
- ✅ Cloud Functions 배포 완료 (`onUserCreate`, `onUserUpdate`)
- ✅ `src/lib/firebase.ts`에 `rtdb` 인스턴스 생성 완료
- ✅ `src/lib/stores/auth.svelte.ts` 인증 스토어 구현 완료

### 1.4 제외 사항

- ❌ 이메일/비밀번호 로그인 (별도 명세)
- ❌ 사용자 프로필 수정 기능 (별도 명세)
- ❌ 프로필 이미지 업로드 (별도 명세)
- ❌ Cloud Functions 내부 로직 수정 (기존 구현 활용)

---

## 2. 아키텍처 설계

### 2.1 전체 플로우

```
┌─────────────────────────────────────────────────────────────────┐
│                         User Login Flow                          │
└─────────────────────────────────────────────────────────────────┘

1. 사용자가 로그인 버튼 클릭 (Google/Apple)
   ↓
2. signInWithGoogle() 또는 signInWithApple() 호출
   ↓
3. Firebase Auth 로그인 성공 → UserCredential 반환
   ↓
4. syncAuthProfileToRtdb(user) 호출
   ↓
5. /users/{uid}에 displayName, photoUrl 저장
   ↓
6. Cloud Function onUserCreate 트리거 (첫 생성 시)
   ↓
7. user-props 동기화, 통계 업데이트
   ↓
8. 사용자 홈페이지로 리다이렉트
```

### 2.2 데이터 흐름

```
Firebase Auth                Realtime Database              Cloud Functions
─────────────               ─────────────────              ───────────────

User.displayName  ──┐
User.photoURL     ──┼──→  syncAuthProfileToRtdb()
User.uid          ──┘                │
                                     │
                                     ↓
                             /users/{uid}/
                               ├── displayName
                               └── photoUrl
                                     │
                                     │ (onCreate 트리거)
                                     ↓
                             onUserCreate()
                               ├── createdAt 추가
                               ├── updatedAt 추가
                               ├── displayNameLowerCase 생성
                               ├── /user-props/ 동기화
                               └── /stats/counters/user +1
```

### 2.3 책임 분리

| 영역 | 책임 | 파일 |
|------|------|------|
| **클라이언트** | Auth 로그인, displayName/photoUrl 저장 | `src/lib/utils/auth-helpers.ts` |
| **Cloud Functions** | createdAt/updatedAt 추가, user-props 동기화, 통계 갱신 | `firebase/functions/src/handlers/user.handler.ts` |
| **RTDB 규칙** | 쓰기 권한 검증 (auth.uid == $uid) | `firebase/database.rules.json` |

**중요 원칙:**
- 클라이언트는 **사용자가 소유한 데이터만** 저장 (displayName, photoUrl)
- 서버는 **시스템 필드** 관리 (createdAt, updatedAt, 통계)
- **타임스탬프는 서버에서만** 생성 (클라이언트는 `serverTimestamp()` 사용 금지)

---

## 3. 데이터베이스 스키마

### 3.1 저장 위치

```
/users/{uid}/
```

### 3.2 동기화 필드

| 필드 | 타입 | 소스 | 설명 |
|------|------|------|------|
| `displayName` | string | Auth.displayName | 사용자 표시 이름 |
| `photoUrl` | string | Auth.photoURL | 프로필 사진 URL |

**저장하지 않는 필드:**
- ❌ `createdAt`: Cloud Functions에서 자동 생성
- ❌ `updatedAt`: Cloud Functions에서 자동 생성
- ❌ `displayNameLowerCase`: Cloud Functions에서 자동 생성
- ❌ `email`: 보안상 RTDB에 저장하지 않음 (Auth에만 존재)

### 3.3 필드 변환 규칙

| Firebase Auth | Realtime Database | 변환 규칙 |
|---------------|-------------------|-----------|
| `displayName` | `displayName` | 그대로 사용 |
| `photoURL` | `photoUrl` | 카멜케이스 변환 (URL → Url) |
| `null` 또는 `undefined` | 저장 안 함 | 빈 값은 저장하지 않음 |

---

## 4. 구현 사양

### 4.1 syncAuthProfileToRtdb() 함수

**파일 위치:**
```
src/lib/utils/auth-helpers.ts
```

**함수 시그니처:**

```typescript
/**
 * Firebase Auth 사용자 프로필을 Realtime Database에 동기화
 * 
 * displayName과 photoURL을 /users/{uid}/에 저장합니다.
 * 값이 없는 필드는 저장하지 않습니다.
 * 
 * @param {User} user - Firebase Auth 사용자 객체
 * @returns {Promise<void>}
 * @throws {Error} RTDB 쓰기 실패 시
 */
export async function syncAuthProfileToRtdb(user: User): Promise<void>
```

**구현 코드:**

```typescript
import { rtdb } from '$lib/firebase';
import { ref, update } from 'firebase/database';
import type { User } from 'firebase/auth';

export async function syncAuthProfileToRtdb(user: User): Promise<void> {
	// RTDB가 초기화되지 않은 경우 (서버 환경)
	if (!rtdb) {
		console.warn('RTDB가 초기화되지 않았습니다. 브라우저 환경에서만 사용 가능합니다.');
		return;
	}

	// 사용자 UID 확인
	if (!user.uid) {
		throw new Error('사용자 UID가 없습니다.');
	}

	try {
		// 업데이트할 데이터 객체 생성
		const updates: Record<string, string> = {};

		// displayName이 있는 경우에만 추가
		if (user.displayName) {
			updates[`users/${user.uid}/displayName`] = user.displayName;
		}

		// photoURL이 있는 경우에만 추가 (photoUrl로 변환)
		if (user.photoURL) {
			updates[`users/${user.uid}/photoUrl`] = user.photoURL;
		}

		// 업데이트할 데이터가 있는 경우에만 저장
		if (Object.keys(updates).length > 0) {
			const dbRef = ref(rtdb);
			await update(dbRef, updates);

			console.log(`[Profile Sync] 사용자 프로필 동기화 완료: ${user.uid}`, {
				displayName: user.displayName,
				photoUrl: user.photoURL
			});
		} else {
			console.log(`[Profile Sync] 동기화할 데이터가 없습니다: ${user.uid}`);
		}
	} catch (error) {
		console.error(`[Profile Sync] 프로필 동기화 실패: ${user.uid}`, error);
		throw new Error(`프로필 동기화 실패: ${error instanceof Error ? error.message : '알 수 없는 오류'}`);
	}
}
```

**핵심 설계 원칙:**

1. **조건부 저장**: `displayName`, `photoURL`이 있을 때만 저장
2. **필드 변환**: `photoURL` → `photoUrl` 자동 변환
3. **에러 전파**: 실패 시 상위로 에러 전파 (로그인 플로우에서 처리)
4. **로깅**: 성공/실패 모두 콘솔 로그 출력
5. **서버 안전성**: RTDB가 없는 환경(SSR)에서는 경고만 출력하고 종료

### 4.2 signInWithGoogle() 함수 수정

**기존 코드:**
```typescript
export async function signInWithGoogle(): Promise<UserCredential> {
	// ... 생략 ...
	const result = await signInWithPopup(auth, provider);
	console.log('Google 로그인 성공:', result.user.uid);
	return result;
}
```

**수정 코드:**
```typescript
export async function signInWithGoogle(): Promise<UserCredential> {
	if (!auth) {
		throw new Error('Firebase Auth가 초기화되지 않았습니다. 브라우저 환경에서만 사용 가능합니다.');
	}

	try {
		// Google Provider 생성
		const provider = new GoogleAuthProvider();

		// 언어 설정
		const locale = getAuthLanguage();
		provider.setCustomParameters({
			locale: locale
		});

		// 팝업 방식으로 로그인
		const result = await signInWithPopup(auth, provider);

		console.log('Google 로그인 성공:', result.user.uid);

		// ✅ 프로필 동기화 추가
		await syncAuthProfileToRtdb(result.user);

		return result;
	} catch (error: any) {
		console.error('Google 로그인 실패:', error);
		throw error;
	}
}
```

### 4.3 signInWithApple() 함수 수정

**기존 코드:**
```typescript
export async function signInWithApple(): Promise<UserCredential> {
	// ... 생략 ...
	const result = await signInWithPopup(auth, provider);
	console.log('Apple 로그인 성공:', result.user.uid);
	return result;
}
```

**수정 코드:**
```typescript
export async function signInWithApple(): Promise<UserCredential> {
	if (!auth) {
		throw new Error('Firebase Auth가 초기화되지 않았습니다. 브라우저 환경에서만 사용 가능합니다.');
	}

	try {
		// Apple Provider 생성
		const provider = new OAuthProvider('apple.com');

		// 언어 설정
		const locale = getAuthLanguage();
		provider.setCustomParameters({
			locale: locale
		});

		// 사용자 정보 요청 스코프 설정
		provider.addScope('email');
		provider.addScope('name');

		// 팝업 방식으로 로그인
		const result = await signInWithPopup(auth, provider);

		console.log('Apple 로그인 성공:', result.user.uid);

		// ✅ 프로필 동기화 추가
		await syncAuthProfileToRtdb(result.user);

		return result;
	} catch (error: any) {
		console.error('Apple 로그인 실패:', error);
		throw error;
	}
}
```

### 4.4 Auth Store 연동

**파일 위치:**
```
src/lib/stores/auth.svelte.ts
```

**수정 위치:**
`initializeAuthListener()` 메서드의 `onAuthStateChanged` 콜백

**기존 코드:**
```typescript
private initializeAuthListener() {
	if (!auth) {
		console.warn('Firebase Auth가 초기화되지 않았습니다.');
		this._state.loading = false;
		this._state.initialized = true;
		return;
	}

	onAuthStateChanged(auth, (user) => {
		this._state.user = user;
		this._state.loading = false;
		this._state.initialized = true;

		if (user) {
			console.log('[Auth Store] 사용자 로그인:', user.uid);
		} else {
			console.log('[Auth Store] 사용자 로그아웃');
		}
	});
}
```

**수정 코드:**
```typescript
private initializeAuthListener() {
	if (!auth) {
		console.warn('Firebase Auth가 초기화되지 않았습니다.');
		this._state.loading = false;
		this._state.initialized = true;
		return;
	}

	onAuthStateChanged(auth, async (user) => {
		this._state.user = user;
		this._state.loading = false;
		this._state.initialized = true;

		if (user) {
			console.log('[Auth Store] 사용자 로그인:', user.uid);

			// ✅ 프로필 동기화 추가 (에러는 무시)
			try {
				// dynamic import로 순환 참조 방지
				const { syncAuthProfileToRtdb } = await import('$lib/utils/auth-helpers');
				await syncAuthProfileToRtdb(user);
			} catch (error) {
				console.error('[Auth Store] 프로필 동기화 실패 (무시):', error);
				// 로그인 상태는 유지 (동기화 실패해도 앱 사용 가능)
			}
		} else {
			console.log('[Auth Store] 사용자 로그아웃');
		}
	});
}
```

**중요 설계 결정:**

1. **Dynamic Import**: `import()`를 사용하여 순환 참조 방지
2. **에러 무시**: 동기화 실패 시 로그만 출력하고 앱 사용 가능 (UX 우선)
3. **중복 호출 허용**: 로그인 직후 + `onAuthStateChanged` 두 번 호출되지만, RTDB의 `update()`는 멱등성 보장

---

## 5. 에러 처리

### 5.1 에러 유형

| 에러 유형 | 원인 | 처리 방법 |
|-----------|------|-----------|
| **RTDB 미초기화** | 서버 환경(SSR)에서 호출 | 경고 로그 출력 후 종료 |
| **UID 없음** | Auth 객체 손상 | Error 발생 |
| **권한 에러** | RTDB 규칙 위반 | Error 발생, 사용자에게 알림 |
| **네트워크 에러** | 인터넷 연결 끊김 | Error 발생, 재시도 안내 |

### 5.2 에러 처리 전략

**1. 로그인 플로우 (`signInWithGoogle`, `signInWithApple`)**

```typescript
try {
	const result = await signInWithPopup(auth, provider);
	await syncAuthProfileToRtdb(result.user); // ✅ 에러 전파
	return result;
} catch (error: any) {
	console.error('로그인 실패:', error);
	throw error; // ✅ 상위(user-login.svelte)에서 처리
}
```

**2. Auth Store (`onAuthStateChanged`)**

```typescript
try {
	await syncAuthProfileToRtdb(user);
} catch (error) {
	console.error('프로필 동기화 실패 (무시):', error);
	// ❌ 에러 전파 안 함 (로그인 상태는 유지)
}
```

**3. 사용자 피드백**

`user-login.svelte`에서 에러 메시지 표시:

```typescript
try {
	await signInWithGoogle();
	await goto('/');
} catch (error: any) {
	if (error.message.includes('프로필 동기화')) {
		errorMessage = '프로필 정보 저장에 실패했습니다. 잠시 후 다시 시도해주세요.';
	} else {
		errorMessage = getAuthErrorMessage(error.code, 'google');
	}
}
```

### 5.3 로깅 규칙

**성공 로그:**
```typescript
console.log(`[Profile Sync] 사용자 프로필 동기화 완료: ${user.uid}`, {
	displayName: user.displayName,
	photoUrl: user.photoURL
});
```

**에러 로그:**
```typescript
console.error(`[Profile Sync] 프로필 동기화 실패: ${user.uid}`, error);
```

**경고 로그:**
```typescript
console.warn('[Profile Sync] 동기화할 데이터가 없습니다: ${user.uid}');
```

---

## 6. Cloud Functions 연계

### 6.1 onUserCreate 트리거

**위치:**
```
firebase/functions/src/index.ts
```

**기존 코드 (수정 불필요):**
```typescript
export const onUserCreate = onValueCreated(
	{
		ref: '/users/{uid}',
		region: 'asia-northeast3'
	},
	async (event) => {
		const uid = event.params.uid;
		const userData = event.data.val() as UserData;
		const createdAt = Date.now();

		await updateUserProps(uid, userData, createdAt);
	}
);
```

**동작 방식:**
1. 클라이언트가 `/users/{uid}`에 첫 데이터 저장
2. `onUserCreate` 트리거 실행
3. `updateUserProps()` 호출
   - `createdAt`, `updatedAt` 추가
   - `displayNameLowerCase` 생성
   - `/user-props/` 동기화
   - `/stats/counters/user` +1

### 6.2 onUserUpdate 트리거

**현재 상태:**
- ❌ 구현되지 않음 (specs/sonub-user-props.md line 97 참조)

**향후 구현 필요:**
```typescript
export const onUserProfileUpdate = onValueUpdated(
	{
		ref: '/users/{uid}',
		region: 'asia-northeast3'
	},
	async (event) => {
		const uid = event.params.uid;
		const before = event.data.before.val() as UserData;
		const after = event.data.after.val() as UserData;

		// displayName이나 photoUrl이 변경된 경우 user-props 동기화
		if (before.displayName !== after.displayName || before.photoUrl !== after.photoUrl) {
			await updateUserProps(uid, after, after.createdAt || Date.now());
		}
	}
);
```

**참고:**
- 이 명세서에서는 구현하지 않음
- 별도 명세서에서 정의 필요

### 6.3 user-props 동기화

**현재 구현 (firebase/functions/src/handlers/user.handler.ts):**

```typescript
// /user-props/displayName/{uid} 저장
if (userData.displayName) {
	updates[`user-props/displayName/${uid}`] = userData.displayName;
}

// /user-props/photoUrl/{uid} 저장
if (photoUrl) {
	updates[`user-props/photoUrl/${uid}`] = photoUrl;
}

// /user-props/displayNameLowerCase/{uid} 저장
if (userData.displayName) {
	const displayNameLowerCase = userData.displayName.toLowerCase();
	updates[`user-props/displayNameLowerCase/${uid}`] = displayNameLowerCase;
	updates[`users/${uid}/displayNameLowerCase`] = displayNameLowerCase;
}
```

**동작 보장:**
- 클라이언트가 `displayName`, `photoUrl`만 저장
- Cloud Functions가 자동으로 `user-props`에 분리 저장
- 추가 클라이언트 코드 불필요

---

## 7. 테스트 계획

### 7.1 유닛 테스트

**테스트 파일:**
```
src/lib/utils/auth-helpers.spec.ts
```

**테스트 케이스:**

```typescript
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { syncAuthProfileToRtdb } from './auth-helpers';
import type { User } from 'firebase/auth';

// Mock Firebase
vi.mock('$lib/firebase', () => ({
	rtdb: {}
}));

vi.mock('firebase/database', () => ({
	ref: vi.fn(),
	update: vi.fn()
}));

describe('syncAuthProfileToRtdb', () => {
	let mockUser: Partial<User>;

	beforeEach(() => {
		mockUser = {
			uid: 'test-uid-123',
			displayName: 'Test User',
			photoURL: 'https://example.com/photo.jpg'
		};
	});

	it('displayName과 photoURL을 RTDB에 저장해야 함', async () => {
		await syncAuthProfileToRtdb(mockUser as User);

		// update()가 올바른 데이터로 호출되었는지 확인
		// (구현 세부사항 확인)
	});

	it('displayName만 있을 때 photoUrl 없이 저장해야 함', async () => {
		mockUser.photoURL = null;
		await syncAuthProfileToRtdb(mockUser as User);

		// photoUrl이 포함되지 않았는지 확인
	});

	it('displayName과 photoURL이 모두 없을 때 저장하지 않아야 함', async () => {
		mockUser.displayName = null;
		mockUser.photoURL = null;
		await syncAuthProfileToRtdb(mockUser as User);

		// update()가 호출되지 않았는지 확인
	});

	it('UID가 없을 때 에러를 발생시켜야 함', async () => {
		mockUser.uid = '';

		await expect(syncAuthProfileToRtdb(mockUser as User)).rejects.toThrow('사용자 UID가 없습니다');
	});
});
```

### 7.2 통합 테스트

**테스트 파일:**
```
src/lib/utils/auth-helpers.integration.spec.ts
```

**테스트 케이스:**

```typescript
import { describe, it, expect, beforeAll, afterAll } from 'vitest';
import { signInWithGoogle, syncAuthProfileToRtdb } from './auth-helpers';
import { ref, get, remove } from 'firebase/database';
import { rtdb } from '$lib/firebase';

describe('Profile Sync Integration', () => {
	let testUid: string;

	beforeAll(async () => {
		// Firebase Emulator에 연결
		// 테스트 사용자로 로그인
	});

	afterAll(async () => {
		// 테스트 데이터 정리
		if (testUid) {
			const userRef = ref(rtdb!, `users/${testUid}`);
			await remove(userRef);
		}
	});

	it('Google 로그인 후 RTDB에 프로필이 저장되어야 함', async () => {
		const result = await signInWithGoogle();
		testUid = result.user.uid;

		// RTDB에서 데이터 확인
		const userRef = ref(rtdb!, `users/${testUid}`);
		const snapshot = await get(userRef);

		expect(snapshot.exists()).toBe(true);
		expect(snapshot.val().displayName).toBe(result.user.displayName);
		expect(snapshot.val().photoUrl).toBe(result.user.photoURL);
	});

	it('Cloud Function이 실행되어 createdAt이 추가되어야 함', async () => {
		// 잠시 대기 (Cloud Function 실행 시간)
		await new Promise(resolve => setTimeout(resolve, 2000));

		const userRef = ref(rtdb!, `users/${testUid}`);
		const snapshot = await get(userRef);

		expect(snapshot.val().createdAt).toBeDefined();
		expect(snapshot.val().updatedAt).toBeDefined();
	});
});
```

### 7.3 E2E 테스트

**테스트 파일:**
```
e2e/user-login-profile-sync.test.ts
```

**테스트 케이스:**

```typescript
import { test, expect } from '@playwright/test';

test.describe('User Profile Sync', () => {
	test('Google 로그인 후 프로필 정보가 표시되어야 함', async ({ page }) => {
		// 로그인 페이지로 이동
		await page.goto('/user/login');

		// Google 로그인 버튼 클릭
		await page.click('button:has-text("Google")');

		// 팝업에서 로그인 (테스트 계정 사용)
		// ...

		// 홈페이지로 리다이렉트 확인
		await expect(page).toHaveURL('/');

		// 프로필 정보가 표시되는지 확인
		const displayName = await page.textContent('[data-testid="user-display-name"]');
		expect(displayName).toBeTruthy();

		// 프로필 사진이 표시되는지 확인
		const photoUrl = await page.getAttribute('[data-testid="user-photo"]', 'src');
		expect(photoUrl).toContain('http');
	});
});
```

---

## 8. 검증 방법

### 8.1 Firebase Emulator 검증

**1. Emulator 시작:**

```bash
cd firebase
firebase emulators:start
```

**2. 테스트 앱 실행:**

```bash
npm run dev
```

**3. 로그인 테스트:**

- `http://localhost:5173/user/login` 접속
- Google 로그인 클릭
- Emulator UI(`http://localhost:4000`)에서 확인:
  - Authentication > Users 탭에 사용자 추가됨
  - Realtime Database > Data 탭에 `/users/{uid}` 생성됨
  - Functions > Logs 탭에 `onUserCreate` 실행 로그 확인

**4. RTDB 데이터 확인:**

```json
{
  "users": {
    "test-uid-123": {
      "displayName": "Test User",
      "photoUrl": "https://example.com/photo.jpg",
      "createdAt": 1699545600000,
      "updatedAt": 1699545600000,
      "displayNameLowerCase": "test user"
    }
  },
  "user-props": {
    "displayName": {
      "test-uid-123": "Test User"
    },
    "photoUrl": {
      "test-uid-123": "https://example.com/photo.jpg"
    }
  }
}
```

### 8.2 프로덕션 검증

**1. 단계적 배포:**

- 먼저 Cloud Functions 배포
- 클라이언트 코드 배포
- 실제 사용자로 테스트

**2. 모니터링:**

```bash
# Firebase Console > Functions > Logs
# onUserCreate 실행 로그 확인

# Cloud Logging 쿼리
resource.type="cloud_function"
resource.labels.function_name="onUserCreate"
severity>=INFO
```

**3. 데이터 검증:**

Firebase Console > Realtime Database에서 수동 확인:
- `/users/{uid}/displayName` 존재 여부
- `/users/{uid}/photoUrl` 존재 여부
- `/user-props/` 동기화 여부

---

## 9. 참고 문서

- [Firebase Authentication](https://firebase.google.com/docs/auth)
- [Firebase Realtime Database](https://firebase.google.com/docs/database)
- [Cloud Functions for Firebase](https://firebase.google.com/docs/functions)
- [SvelteKit Documentation](https://kit.svelte.dev/)
- [Vitest Documentation](https://vitest.dev/)
- [Playwright Testing](https://playwright.dev/)
- [sonub-setup-firebase.md](./sonub-setup-firebase.md)
- [sonub-firebase-database-structure.md](specs/sonub-firebase-database-structure.md)
- [sonub-user-login.md](./sonub-user-login.md)
- [sonub-user-props.md](./sonub-user-props.md)
