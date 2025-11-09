---
name: sonub-firebase-setup
version: 1.1.0
description: SvelteKit 프로젝트에 Firebase JS SDK 설치 및 설정 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-08
updated: 2025-01-09
step: 20
priority: "**"
dependencies: ["sonub-setup-svelte.md"]
tags: ["firebase", "backend", "database", "authentication", "storage", "설정", "SSR"]
---

# Firebase JS SDK 설치 및 설정 명세서

## 1. 개요

본 명세서는 Sonub 프로젝트의 SvelteKit 5 환경에 Firebase JavaScript SDK를 설치하고 설정하기 위한 완전한 절차를 정의합니다. 이 명세서에 따라 AI는 정확히 명시된 방법으로 Firebase를 통합해야 합니다.

### 1.1 목적

- SvelteKit 5 프로젝트에 Firebase JS SDK 통합
- Authentication, Firestore, Realtime Database, Storage 설정
- 환경 변수 기반 설정 관리
- 클라이언트/서버 환경 대응
- 보안 규칙 설정

### 1.2 범위

- Firebase 프로젝트 생성 (Firebase Console)
- Firebase JS SDK 설치
- 환경 변수 설정
- Firebase 초기화 코드 작성
- 주요 서비스(Auth, Firestore, RTDB, Storage) 설정
- 사용 예제 코드
- 보안 규칙 설정

### 1.3 사전 요구사항

- ✅ SvelteKit 프로젝트 설치 완료 (sonub-setup-svelte.md 참조)
- ✅ Node.js v18 이상
- ✅ Google 계정 (Firebase Console 접근용)

## 2. 요구사항

### 2.1 시스템 요구사항

**필수 소프트웨어:**
- SvelteKit: 2.47.1 이상 (이미 설치됨)
- Node.js: v18.0.0 이상
- npm: v9.0.0 이상
- 웹 브라우저: Chrome, Firefox, Safari 최신 버전

**Firebase 계정:**
- Google 계정 필수
- Firebase Console 접근 권한

### 2.2 기능 요구사항

**Firebase 서비스:**
- Authentication: 사용자 인증 (구체적인 로그인 방법은 [sonub-user-login.md](./sonub-user-login.md) 참조)
- Firestore: NoSQL 문서 데이터베이스
- Realtime Database: 실시간 동기화 데이터베이스
- Storage: 파일 저장소
- Analytics: 사용자 분석 (선택)

**Firebase SDK 버전:**
- firebase: ^11.0.0 (최신 안정 버전)

## 3. Firebase 프로젝트 설정

### 3.1 Firebase Console에서 프로젝트 생성

**단계별 절차:**

1. **Firebase Console 접속**
   - URL: https://console.firebase.google.com
   - Google 계정으로 로그인

2. **프로젝트 생성**
   ```
   단계 1: "프로젝트 추가" 버튼 클릭
   단계 2: 프로젝트 이름 입력
     - 입력: "sonub" (또는 원하는 이름)
     - 클릭: "계속"

   단계 3: Google Analytics 설정
     - 선택: "이 프로젝트에 Google Analytics 사용 설정" (권장)
     - 클릭: "계속"

   단계 4: Analytics 계정 선택
     - 선택: 기본 계정 또는 새 계정 생성
     - 클릭: "프로젝트 만들기"

   단계 5: 프로젝트 준비 완료 대기
     - 소요 시간: 약 30초~1분
     - 완료 후: "계속" 클릭
   ```

3. **웹 앱 추가**
   ```
   단계 1: 프로젝트 개요 페이지에서 "</>" (웹) 아이콘 클릭

   단계 2: 앱 등록
     - 앱 닉네임: "sonub-web"
     - Firebase Hosting 설정: ❌ 체크하지 않음 (SvelteKit 어댑터 사용)
     - 클릭: "앱 등록"

   단계 3: Firebase SDK 추가
     - 표시된 설정 정보 복사 (다음 단계에서 사용)
     - firebaseConfig 객체 정보 저장
     - 클릭: "콘솔로 이동"
   ```

### 3.2 Firebase 서비스 활성화

#### 3.2.1 Authentication 설정

**참고:** 구체적인 로그인 방법 설정은 [sonub-user-login.md](./sonub-user-login.md) 문서를 참조하세요.

```
1. 좌측 메뉴: "빌드" > "Authentication" 클릭
2. "시작하기" 버튼 클릭
3. "Sign-in method" 탭에서 필요한 로그인 제공업체 설정
   - 구글, 애플 등 프로젝트에 필요한 제공업체 활성화
```

#### 3.2.2 Firestore Database 설정

```
1. 좌측 메뉴: "빌드" > "Firestore Database" 클릭
2. "데이터베이스 만들기" 버튼 클릭
3. 보안 규칙 모드 선택:
   - 선택: "테스트 모드에서 시작" (개발용)
   - 주의: 프로덕션에서는 "프로덕션 모드"로 변경 필요
   - 클릭: "다음"
4. Cloud Firestore 위치 선택:
   - 권장: "asia-northeast3 (Seoul)"
   - 또는: "asia-northeast1 (Tokyo)"
   - 주의: 위치는 설정 후 변경 불가
   - 클릭: "사용 설정"
5. 데이터베이스 프로비저닝 대기 (약 1-2분)
```

#### 3.2.3 Realtime Database 설정

```
1. 좌측 메뉴: "빌드" > "Realtime Database" 클릭
2. "데이터베이스 만들기" 버튼 클릭
3. 데이터베이스 위치 선택:
   - 권장: "asia-southeast1 (Singapore)"
   - 참고: 한국 리전 없음, 싱가포르가 가장 가까움
   - 클릭: "다음"
4. 보안 규칙 모드 선택:
   - 선택: "테스트 모드에서 시작" (개발용)
   - 클릭: "사용 설정"
5. Database URL 확인 및 저장:
   - 형식: https://<project-id>-default-rtdb.firebaseio.com
   - 또는: https://<project-id>-default-rtdb.asia-southeast1.firebasedatabase.app
```

#### 3.2.4 Storage 설정

```
1. 좌측 메뉴: "빌드" > "Storage" 클릭
2. "시작하기" 버튼 클릭
3. 보안 규칙 확인:
   - 기본 규칙 표시됨
   - 클릭: "다음"
4. Storage 위치 선택:
   - 자동: Firestore와 동일한 위치 (asia-northeast3)
   - 클릭: "완료"
5. 버킷 생성 대기 (약 30초)
```

### 3.3 Firebase 설정 정보 수집

**수집할 정보 목록:**

프로젝트 설정 > 일반 > SDK 설정 및 구성 > 구성 섹션에서 확인:

```javascript
const firebaseConfig = {
  apiKey: "AIza...",                           // API 키
  authDomain: "sonub.firebaseapp.com",        // Auth 도메인
  projectId: "sonub",                          // 프로젝트 ID
  storageBucket: "sonub.appspot.com",         // Storage 버킷
  messagingSenderId: "123456789",              // 메시징 발신자 ID
  appId: "1:123456789:web:abc123",            // 앱 ID
  measurementId: "G-XXXXXXXXXX",               // 측정 ID (Analytics)
  databaseURL: "https://sonub-default-rtdb.asia-southeast1.firebasedatabase.app" // RTDB URL
};
```

**중요:** 이 정보는 다음 단계에서 환경 변수로 사용됩니다.

## 4. Firebase SDK 설치

### 4.1 패키지 설치

**명령어:**
```bash
npm install firebase
```

**설치되는 패키지:**
- `firebase`: ^11.0.0 이상

**설치 확인:**
```bash
npm list firebase
```

**예상 출력:**
```
sonub@0.0.1
└── firebase@11.0.0
```

## 5. 환경 변수 설정

### 5.1 .env 파일 생성

**위치:** 프로젝트 루트 디렉토리 (`/Users/thruthesky/apps/sonub/.env`)

**내용:**
```bash
# Firebase 설정
# Firebase Console에서 가져온 Web Config 정보
PUBLIC_FIREBASE_API_KEY=AIza...
PUBLIC_FIREBASE_AUTH_DOMAIN=sonub.firebaseapp.com
PUBLIC_FIREBASE_PROJECT_ID=sonub
PUBLIC_FIREBASE_STORAGE_BUCKET=sonub.appspot.com
PUBLIC_FIREBASE_MESSAGING_SENDER_ID=123456789
PUBLIC_FIREBASE_APP_ID=1:123456789:web:abc123
PUBLIC_FIREBASE_MEASUREMENT_ID=G-XXXXXXXXXX
PUBLIC_FIREBASE_DATABASE_URL=https://sonub-default-rtdb.asia-southeast1.firebasedatabase.app
```

**중요 사항:**

1. **PUBLIC_ 접두사:**
   - SvelteKit에서 클라이언트에 노출되는 변수는 `PUBLIC_` 접두사 필수
   - `PRIVATE_` 접두사는 서버 전용

2. **실제 값 입력:**
   - 위의 예시 값을 Firebase Console에서 복사한 실제 값으로 교체
   - **⚠️ 중요**: 값에 **큰따옴표(`"`)를 사용하지 마세요**
   - Vite는 큰따옴표를 값의 일부로 읽어서 Firebase 초기화 오류가 발생할 수 있습니다

3. **올바른 형식:**
   ```bash
   # ✅ 올바른 형식 (큰따옴표 없음)
   PUBLIC_FIREBASE_API_KEY=AIzaSyCXAFYnNf7QYcZNpIngCA...

   # ❌ 잘못된 형식 (큰따옴표 사용)
   PUBLIC_FIREBASE_API_KEY="AIzaSyCXAFYnNf7QYcZNpIngCA..."
   ```

3. **.gitignore 확인:**
   ```bash
   # .env 파일이 .gitignore에 포함되어 있는지 확인
   cat .gitignore | grep ".env"
   ```

   **예상 출력:**
   ```
   .env
   .env.*
   !.env.example
   ```

### 5.2 .env.example 파일 생성 (선택)

**목적:** 팀원들에게 필요한 환경 변수 형식 공유

**내용:**
```bash
# Firebase 설정 (실제 값은 Firebase Console에서 확인)
PUBLIC_FIREBASE_API_KEY="YOUR_API_KEY"
PUBLIC_FIREBASE_AUTH_DOMAIN="YOUR_PROJECT_ID.firebaseapp.com"
PUBLIC_FIREBASE_PROJECT_ID="YOUR_PROJECT_ID"
PUBLIC_FIREBASE_STORAGE_BUCKET="YOUR_PROJECT_ID.appspot.com"
PUBLIC_FIREBASE_MESSAGING_SENDER_ID="YOUR_SENDER_ID"
PUBLIC_FIREBASE_APP_ID="YOUR_APP_ID"
PUBLIC_FIREBASE_MEASUREMENT_ID="YOUR_MEASUREMENT_ID"
PUBLIC_FIREBASE_DATABASE_URL="YOUR_DATABASE_URL"
```

## 6. Firebase 초기화 코드

### 6.1 Firebase 초기화 파일 생성

**파일 경로:** `src/lib/firebase.ts`

**내용:**
```typescript
/**
 * Firebase 초기화 및 서비스 인스턴스
 *
 * 이 파일은 Firebase 앱을 초기화하고 필요한 서비스들을 export합니다.
 * - 중복 초기화 방지
 * - 브라우저 환경 체크 (SSR 대응)
 */

import { browser } from '$app/environment';
import { initializeApp, getApps, type FirebaseApp } from 'firebase/app';

// SvelteKit 환경 변수 (PUBLIC_ 접두사가 있는 환경 변수)
import {
	PUBLIC_FIREBASE_API_KEY,
	PUBLIC_FIREBASE_AUTH_DOMAIN,
	PUBLIC_FIREBASE_PROJECT_ID,
	PUBLIC_FIREBASE_STORAGE_BUCKET,
	PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
	PUBLIC_FIREBASE_APP_ID,
	PUBLIC_FIREBASE_MEASUREMENT_ID,
	PUBLIC_FIREBASE_DATABASE_URL
} from '$env/static/public';

// Authentication
import { getAuth, type Auth } from 'firebase/auth';

// Firestore
import { getFirestore, type Firestore } from 'firebase/firestore';

// Realtime Database
import { getDatabase, type Database } from 'firebase/database';

// Storage
import { getStorage, type FirebaseStorage } from 'firebase/storage';

// Analytics (브라우저에서만 사용)
import { getAnalytics, type Analytics } from 'firebase/analytics';

/**
 * Firebase 설정
 * SvelteKit 환경 변수에서 값을 가져옵니다.
 */
const firebaseConfig = {
	apiKey: PUBLIC_FIREBASE_API_KEY,
	authDomain: PUBLIC_FIREBASE_AUTH_DOMAIN,
	projectId: PUBLIC_FIREBASE_PROJECT_ID,
	storageBucket: PUBLIC_FIREBASE_STORAGE_BUCKET,
	messagingSenderId: PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
	appId: PUBLIC_FIREBASE_APP_ID,
	measurementId: PUBLIC_FIREBASE_MEASUREMENT_ID,
	databaseURL: PUBLIC_FIREBASE_DATABASE_URL
};

// 디버깅: Firebase 설정 확인
if (browser) {
	console.log('✅ Firebase 환경 변수 로드 성공');
	console.log('Firebase Config:', {
		apiKey: firebaseConfig.apiKey ? '✓ Loaded' : '✗ Missing',
		authDomain: firebaseConfig.authDomain ? '✓ Loaded' : '✗ Missing',
		projectId: firebaseConfig.projectId ? '✓ Loaded' : '✗ Missing',
		appId: firebaseConfig.appId ? '✓ Loaded' : '✗ Missing'
	});
}

/**
 * Firebase 앱 초기화
 * 중복 초기화 방지: getApps()로 기존 앱 확인
 */
let app: FirebaseApp;

if (browser) {
	// 브라우저 환경에서만 Firebase 초기화
	app = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
} else {
	// 서버 환경에서는 더미 앱 생성 (사용되지 않음)
	app = {} as FirebaseApp;
}

/**
 * Authentication 서비스
 * 브라우저 환경에서만 초기화
 */
export const auth: Auth | null = browser ? getAuth(app) : null;

/**
 * Firestore 서비스
 * 브라우저 환경에서만 초기화
 */
export const db: Firestore | null = browser ? getFirestore(app) : null;

/**
 * Realtime Database 서비스
 * 브라우저 환경에서만 초기화
 */
export const rtdb: Database | null = browser ? getDatabase(app) : null;

/**
 * Storage 서비스
 * 브라우저 환경에서만 초기화
 */
export const storage: FirebaseStorage | null = browser ? getStorage(app) : null;

/**
 * Analytics 서비스
 * 브라우저 환경에서만 초기화
 */
export const analytics: Analytics | null = browser ? getAnalytics(app) : null;

/**
 * Firebase 앱 인스턴스 export (고급 사용)
 */
export default app;
```

**주요 변경 사항:**

1. **환경 변수 로딩 방법 변경**
   - ❌ 이전: `import.meta.env.PUBLIC_FIREBASE_API_KEY`
   - ✅ 현재: `import { PUBLIC_FIREBASE_API_KEY } from '$env/static/public'`
   - **이유**: SvelteKit은 Vite의 `import.meta.env` 대신 자체 환경 변수 모듈을 사용합니다.

2. **SSR 대응 강화**
   - 모든 Firebase 서비스를 nullable 타입으로 변경 (`Auth | null` 등)
   - 브라우저 환경에서만 초기화하도록 조건 추가
   - 서버 환경에서는 더미 앱 객체 생성

3. **디버깅 로그 추가**
   - 환경 변수가 올바르게 로드되었는지 확인하는 콘솔 로그
   - 개발 중 문제 해결에 유용

### 6.2 TypeScript 타입 정의 파일 생성 (선택)

**파일 경로:** `src/lib/types/firebase.ts`

**내용:**
```typescript
/**
 * Firebase 관련 타입 정의
 */

import type { User } from 'firebase/auth';

/**
 * 사용자 프로필
 */
export interface UserProfile {
	uid: string;
	email: string | null;
	displayName: string | null;
	photoURL: string | null;
	createdAt: number;
	updatedAt: number;
}

/**
 * Realtime Database Todo 아이템
 */
export interface TodoItem {
	id: string;
	text: string;
	done: boolean;
	createdAt: number;
	userId: string;
}

/**
 * Storage 파일 정보
 */
export interface StoredFile {
	name: string;
	fullPath: string;
	url: string;
	size: number;
	contentType: string;
	uploadedAt: number;
}

/**
 * 인증 상태
 */
export interface AuthState {
	user: User | null;
	loading: boolean;
	error: Error | null;
}
```

## 7. 사용 예제

### 7.1 Authentication 예제

이전 버전의 Authentication 예제는 문서 분리를 위해 [sonub-firebase-auth.md](./sonub-firebase-auth.md)에 통합되었습니다.
Firebase 로그인 흐름과 샘플 코드는 해당 문서의 "Authentication 예제" 절을 참고하세요.

### 7.2 Realtime Database 예제

**파일 경로:** `src/routes/todos/+page.svelte`

**내용:**
```svelte
<script lang="ts">
	import { onMount, onDestroy } from 'svelte';
	import { browser } from '$app/environment';
	import { rtdb, auth } from '$lib/firebase';
	import { ref, set, update, push, onValue, off, get, child, remove } from 'firebase/database';
	import type { Unsubscribe } from 'firebase/database';
	import type { TodoItem } from '$lib/types/firebase';

	let items: TodoItem[] = [];
	let newText = '';
	let status = '';
	let unsubscribe: Unsubscribe | null = null;

	/**
	 * 컴포넌트 마운트 시 실시간 구독 시작
	 */
	onMount(() => {
		if (!browser || !auth.currentUser) {
			status = '로그인이 필요합니다.';
			return;
		}

		const userId = auth.currentUser.uid;
		const todosRef = ref(rtdb, `todos/${userId}`);

		// 실시간 구독
		unsubscribe = onValue(
			todosRef,
			(snapshot) => {
				const data = snapshot.val() ?? {};

				// 객체를 배열로 변환
				items = Object.entries(data)
					.map(([id, value]: [string, any]) => ({
						id,
						text: value.text,
						done: value.done,
						createdAt: value.createdAt,
						userId: value.userId
					}))
					.sort((a, b) => a.createdAt - b.createdAt);

				status = `총 ${items.length}개의 할 일`;
			},
			(error) => {
				status = `구독 오류: ${error.message}`;
				console.error('RTDB 구독 오류:', error);
			}
		);
	});

	/**
	 * 컴포넌트 언마운트 시 구독 해제
	 */
	onDestroy(() => {
		if (unsubscribe) {
			unsubscribe();
		}
	});

	/**
	 * 새 할 일 추가
	 */
	async function addItem(): Promise<void> {
		if (!newText.trim() || !auth.currentUser) return;

		const userId = auth.currentUser.uid;
		const todosRef = ref(rtdb, `todos/${userId}`);
		const newItemRef = push(todosRef);

		try {
			await set(newItemRef, {
				text: newText.trim(),
				done: false,
				createdAt: Date.now(),
				userId: userId
			});

			newText = '';
			status = '할 일이 추가되었습니다.';
		} catch (error: any) {
			status = `추가 실패: ${error.message}`;
			console.error('Todo 추가 오류:', error);
		}
	}

	/**
	 * 완료 상태 토글
	 */
	async function toggleDone(id: string, currentDone: boolean): Promise<void> {
		if (!auth.currentUser) return;

		const userId = auth.currentUser.uid;
		const itemRef = ref(rtdb, `todos/${userId}/${id}`);

		try {
			await update(itemRef, { done: !currentDone });
		} catch (error: any) {
			status = `업데이트 실패: ${error.message}`;
			console.error('Todo 업데이트 오류:', error);
		}
	}

	/**
	 * 할 일 삭제
	 */
	async function deleteItem(id: string): Promise<void> {
		if (!auth.currentUser) return;

		const userId = auth.currentUser.uid;
		const itemRef = ref(rtdb, `todos/${userId}/${id}`);

		try {
			await remove(itemRef);
			status = '할 일이 삭제되었습니다.';
		} catch (error: any) {
			status = `삭제 실패: ${error.message}`;
			console.error('Todo 삭제 오류:', error);
		}
	}

	/**
	 * 단건 읽기 (get 사용)
	 */
	async function readAllOnce(): Promise<void> {
		if (!auth.currentUser) return;

		const userId = auth.currentUser.uid;
		const todosRef = ref(rtdb, `todos/${userId}`);

		try {
			const snapshot = await get(todosRef);

			if (snapshot.exists()) {
				const data = snapshot.val();
				const count = Object.keys(data).length;
				status = `현재 ${count}개의 할 일이 있습니다.`;
			} else {
				status = '할 일이 없습니다.';
			}
		} catch (error: any) {
			status = `읽기 실패: ${error.message}`;
			console.error('Todo 읽기 오류:', error);
		}
	}
</script>

<div class="todos-container">
	<h1>할 일 목록 (Realtime Database)</h1>

	<p class="status">{status}</p>

	<form on:submit|preventDefault={addItem}>
		<input bind:value={newText} placeholder="할 일을 입력하세요" required />
		<button type="submit">추가</button>
	</form>

	<button on:click={readAllOnce} class="secondary">현재 상태 확인 (get)</button>

	<ul class="todo-list">
		{#each items as item (item.id)}
			<li class:completed={item.done}>
				<label>
					<input type="checkbox" checked={item.done} on:change={() => toggleDone(item.id, item.done)} />
					<span>{item.text}</span>
				</label>
				<button on:click={() => deleteItem(item.id)} class="delete">삭제</button>
			</li>
		{/each}
	</ul>

	{#if items.length === 0}
		<p class="empty">할 일이 없습니다. 새로운 할 일을 추가해보세요!</p>
	{/if}
</div>

<style>
	.todos-container {
		max-width: 600px;
		margin: 2rem auto;
		padding: 2rem;
	}

	.status {
		color: #666;
		margin-bottom: 1rem;
	}

	form {
		display: flex;
		gap: 0.5rem;
		margin-bottom: 1rem;
	}

	input[type='text'] {
		flex: 1;
		padding: 0.75rem;
		border: 1px solid #ddd;
		border-radius: 4px;
	}

	button {
		padding: 0.75rem 1.5rem;
		background-color: #4285f4;
		color: white;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	}

	button:hover {
		background-color: #3367d6;
	}

	button.secondary {
		background-color: #666;
		width: 100%;
		margin-bottom: 1rem;
	}

	button.secondary:hover {
		background-color: #555;
	}

	.todo-list {
		list-style: none;
		padding: 0;
	}

	.todo-list li {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 1rem;
		border: 1px solid #ddd;
		border-radius: 4px;
		margin-bottom: 0.5rem;
	}

	.todo-list li.completed {
		background-color: #f0f0f0;
	}

	.todo-list li.completed span {
		text-decoration: line-through;
		color: #999;
	}

	.todo-list label {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		flex: 1;
		cursor: pointer;
	}

	button.delete {
		background-color: #d32f2f;
		padding: 0.5rem 1rem;
	}

	button.delete:hover {
		background-color: #b71c1c;
	}

	.empty {
		text-align: center;
		color: #999;
		padding: 2rem;
	}
</style>
```

### 7.3 Storage 예제

파일 업로드/목록/삭제 예제는 [sonub-firebase-storage.md](./sonub-firebase-storage.md)에 정리되어 있습니다.
`src/routes/upload/+page.svelte` 구현 지침과 UI 요구사항은 해당 문서를 참고하세요.

## 8. 보안 규칙

### 8.1 Firestore Security Rules

**설정 위치:** Firebase Console > Firestore Database > 규칙

**개발 환경 (테스트 모드):**
```javascript
rules_version = '2';

service cloud.firestore {
  match /databases/{database}/documents {
    // 모든 문서 읽기/쓰기 허용 (개발 전용)
    match /{document=**} {
      allow read, write: if request.time < timestamp.date(2025, 2, 1);
    }
  }
}
```

**프로덕션 환경 (권장):**
```javascript
rules_version = '2';

service cloud.firestore {
  match /databases/{database}/documents {
    // 사용자 프로필: 본인만 읽기/쓰기
    match /users/{userId} {
      allow read, write: if request.auth != null && request.auth.uid == userId;
    }

    // 공개 게시물: 모두 읽기, 인증된 사용자만 쓰기
    match /posts/{postId} {
      allow read: if true;
      allow create: if request.auth != null;
      allow update, delete: if request.auth != null
        && request.auth.uid == resource.data.authorId;
    }

    // 댓글: 모두 읽기, 인증된 사용자만 쓰기
    match /posts/{postId}/comments/{commentId} {
      allow read: if true;
      allow create: if request.auth != null;
      allow update, delete: if request.auth != null
        && request.auth.uid == resource.data.authorId;
    }
  }
}
```

### 8.2 Realtime Database Security Rules

**설정 위치:** Firebase Console > Realtime Database > 규칙

**개발 환경 (테스트 모드):**
```json
{
  "rules": {
    ".read": "now < 1738368000000",
    ".write": "now < 1738368000000"
  }
}
```

**프로덕션 환경 (권장):**
```json
{
  "rules": {
    "users": {
      "$uid": {
        ".read": "auth != null && auth.uid === $uid",
        ".write": "auth != null && auth.uid === $uid"
      }
    },
    "todos": {
      "$uid": {
        ".read": "auth != null && auth.uid === $uid",
        ".write": "auth != null && auth.uid === $uid",
        "$todoId": {
          ".validate": "newData.hasChildren(['text', 'done', 'createdAt', 'userId'])",
          "text": {
            ".validate": "newData.isString() && newData.val().length > 0 && newData.val().length <= 200"
          },
          "done": {
            ".validate": "newData.isBoolean()"
          },
          "createdAt": {
            ".validate": "newData.isNumber()"
          },
          "userId": {
            ".validate": "newData.val() === auth.uid"
          }
        }
      }
    },
    "public": {
      ".read": true,
      ".write": "auth != null"
    }
  }
}
```

### 8.3 Storage Security Rules

**설정 위치:** Firebase Console > Storage > 규칙

**개발 환경 (테스트 모드):**
```javascript
rules_version = '2';

service firebase.storage {
  match /b/{bucket}/o {
    match /{allPaths=**} {
      allow read, write: if request.time < timestamp.date(2025, 2, 1);
    }
  }
}
```

**프로덕션 환경 (권장):**
```javascript
rules_version = '2';

service firebase.storage {
  match /b/{bucket}/o {
    // 공개 파일: 모두 읽기, 인증된 사용자만 쓰기
    match /public/{allPaths=**} {
      allow read: if true;
      allow write: if request.auth != null;
    }

    // 사용자 업로드: 본인만 읽기/쓰기
    match /uploads/{userId}/{allPaths=**} {
      allow read, write: if request.auth != null && request.auth.uid == userId;
    }

    // 프로필 이미지: 모두 읽기, 본인만 쓰기
    match /profiles/{userId}/{allPaths=**} {
      allow read: if true;
      allow write: if request.auth != null
        && request.auth.uid == userId
        && request.resource.size < 5 * 1024 * 1024  // 5MB 제한
        && request.resource.contentType.matches('image/.*');
    }
  }
}
```

## 9. 검증 방법

### 9.1 설치 검증 체크리스트

**필수 검증 단계:**

1. **Firebase 패키지 설치 확인**
```bash
npm list firebase
```

**예상 출력:**
```
sonub@0.0.1
└── firebase@11.0.0
```

2. **환경 변수 확인**
```bash
cat .env | grep FIREBASE
```

**예상 출력:**
```
PUBLIC_FIREBASE_API_KEY="AIza..."
PUBLIC_FIREBASE_AUTH_DOMAIN="sonub.firebaseapp.com"
PUBLIC_FIREBASE_PROJECT_ID="sonub"
...
```

3. **Firebase 초기화 파일 확인**
```bash
ls -la src/lib/firebase.ts
```

**예상 출력:**
```
-rw-r--r--  1 user  staff  5432 Jan  8 12:00 src/lib/firebase.ts
```

4. **TypeScript 컴파일 확인**
```bash
npm run check
```

**예상 출력:**
```
No errors found
```

5. **개발 서버 실행 테스트**
```bash
npm run dev
```

**예상:**
- 컴파일 에러 없음
- 브라우저 콘솔에 Firebase 관련 에러 없음

### 9.2 기능 검증

#### 9.2.1 Authentication 테스트

**참고:** Authentication 테스트에 대한 상세한 내용은 [sonub-user-login.md](./sonub-user-login.md) 문서를 참조하세요.

```
1. Firebase Console > Authentication > Users에서 사용자 관리 확인
2. 로그인/로그아웃 기능 테스트
```

#### 9.2.2 Realtime Database 테스트

```
1. 로그인 후 http://localhost:5173/todos 접속
2. 할 일 추가: "Firebase 테스트"
3. 체크박스 토글로 완료/미완료 전환
4. Firebase Console > Realtime Database에서 데이터 확인
5. 브라우저 여러 탭에서 동시 접속 → 실시간 동기화 확인
```

#### 9.2.3 Storage 테스트

```
1. 로그인 후 http://localhost:5173/upload 접속
2. 이미지 파일 선택 (예: test.jpg)
3. 업로드 버튼 클릭
4. 진행률 표시 확인
5. Firebase Console > Storage에서 파일 확인
6. 다운로드 URL 클릭 → 파일 열림 확인
7. 삭제 버튼 클릭 → 파일 삭제 확인
```

### 9.3 에뮬레이터 테스트 (선택)

**Firebase 에뮬레이터 설치:**
```bash
npm install -g firebase-tools
firebase login
firebase init emulators
```

**에뮬레이터 선택:**
- ✅ Authentication Emulator (포트: 9099)
- ✅ Firestore Emulator (포트: 8080)
- ✅ Realtime Database Emulator (포트: 9000)
- ✅ Storage Emulator (포트: 9199)

**에뮬레이터 실행:**
```bash
firebase emulators:start
```

**개발 서버 실행 (별도 터미널):**
```bash
npm run dev
```

**검증:**
- src/lib/firebase.ts의 에뮬레이터 연결 코드가 자동 실행됨
- Firebase Console 대신 로컬 에뮬레이터 사용
- 에뮬레이터 UI: http://localhost:4000

## 10. 문제 해결

### 10.1 일반적인 문제

**문제 1: Firebase 초기화 오류**
```
증상: "Firebase: No Firebase App '[DEFAULT]' has been created"
원인: Firebase 앱이 초기화되기 전에 서비스를 사용하려고 시도
해결:
1. src/lib/firebase.ts 파일 존재 확인
2. 환경 변수 확인: cat .env
3. 브라우저 콘솔에서 import.meta.env.PUBLIC_FIREBASE_API_KEY 확인
4. 개발 서버 재시작: npm run dev
```

**문제 2: 환경 변수 인식 안 됨**
```
증상: Firebase config 값이 undefined 또는 auth/invalid-api-key 에러
원인: 환경 변수가 로드되지 않음
해결:
1. .env 파일이 프로젝트 루트에 있는지 확인
2. 변수명이 PUBLIC_ 접두사로 시작하는지 확인
3. ⚠️ 중요: .env 파일에서 큰따옴표(") 제거
   - ❌ PUBLIC_FIREBASE_API_KEY="AIza..."
   - ✅ PUBLIC_FIREBASE_API_KEY=AIza...
4. src/lib/firebase.ts에서 $env/static/public 사용 확인
   - ❌ import.meta.env.PUBLIC_FIREBASE_API_KEY
   - ✅ import { PUBLIC_FIREBASE_API_KEY } from '$env/static/public'
5. 빌드 캐시 삭제 및 개발 서버 재시작
   - rm -rf .svelte-kit node_modules/.vite
   - npm run dev
```

**문제 3: CORS 오류**
```
증상: "Access to fetch at 'https://firebaseapp.com' from origin 'http://localhost:5173' has been blocked by CORS"
원인: Firebase 프로젝트에 도메인이 등록되지 않음
해결:
1. Firebase Console > Authentication > Settings > Authorized domains
2. "localhost" 추가 (기본적으로 포함되어 있어야 함)
3. 프로덕션 배포 시 실제 도메인 추가
```

**문제 4: Permission denied**
```
증상: "FirebaseError: Missing or insufficient permissions"
원인: 보안 규칙이 접근을 차단함
해결:
1. Firebase Console에서 보안 규칙 확인
2. 개발 중이면 테스트 모드로 임시 변경
3. 인증이 필요한 경우 로그인 상태 확인
4. 콘솔에서 auth.currentUser 확인
```

### 10.2 Authentication 관련 문제

**문제: 이메일 중복 오류**
```
증상: "auth/email-already-in-use"
해결:
1. Firebase Console > Authentication > Users에서 이메일 확인
2. 다른 이메일로 시도
3. 기존 계정 삭제 후 재시도 (개발 환경)
```

**문제: 약한 비밀번호**
```
증상: "auth/weak-password"
해결:
1. 비밀번호를 최소 6자 이상으로 설정
2. 영문, 숫자, 특수문자 조합 권장
3. 클라이언트 측에서 유효성 검사 추가
```

### 10.3 Database 관련 문제

**문제: Realtime Database URL 오류**
```
증상: "Can't determine Firebase Database URL"
원인: databaseURL 환경 변수 누락
해결:
1. .env에 PUBLIC_FIREBASE_DATABASE_URL 추가
2. Firebase Console > Realtime Database에서 URL 복사
3. 형식: https://<project-id>-default-rtdb.<region>.firebasedatabase.app
```

**문제: 데이터가 실시간으로 동기화되지 않음**
```
증상: onValue 콜백이 호출되지 않음
원인: 구독이 제대로 설정되지 않음
해결:
1. browser 체크 확인: if (browser) { ... }
2. onMount 내에서 구독 설정
3. onDestroy에서 구독 해제 확인
4. 네트워크 연결 확인
```

### 10.4 Storage 관련 문제

**문제: 업로드 실패**
```
증상: "storage/unauthorized"
원인: 보안 규칙이 업로드를 차단함
해결:
1. Firebase Console > Storage > Rules 확인
2. 로그인 상태 확인
3. 경로가 보안 규칙과 일치하는지 확인
4. 개발 중이면 테스트 모드로 변경
```

**문제: 파일 크기 제한**
```
증상: 큰 파일 업로드 시 실패
해결:
1. 보안 규칙에 size 제한 추가:
   request.resource.size < 10 * 1024 * 1024  // 10MB
2. 클라이언트에서 파일 크기 검증
3. Firebase 프로젝트 할당량 확인
```

## 11. 프로젝트 구조

### 11.1 생성된 파일 구조

```
sonub/
├── .env                           # Firebase 환경 변수
├── .env.example                   # 환경 변수 템플릿
├── src/
│   ├── lib/
│   │   ├── firebase.ts           # Firebase 초기화
│   │   └── types/
│   │       └── firebase.ts       # Firebase 타입 정의
│   └── routes/
│       ├── user/
│       │   └── login/
│       │       └── +page.svelte  # 로그인 페이지 (sonub-user-login.md 참조)
│       ├── todos/
│       │   └── +page.svelte      # Realtime DB 예제
│       └── upload/
│           └── +page.svelte      # Storage 예제
└── firebase.json                  # Firebase 설정 (에뮬레이터용)
```

### 11.2 Firebase Console 구조

```
Firebase Project (sonub)
├── Authentication
│   └── Users                      # 사용자 목록
├── Firestore Database
│   ├── Data                       # 문서 데이터
│   └── Rules                      # 보안 규칙
├── Realtime Database
│   ├── Data                       # JSON 트리 데이터
│   └── Rules                      # 보안 규칙
├── Storage
│   ├── Files                      # 업로드된 파일
│   └── Rules                      # 보안 규칙
└── Project Settings
    └── General
        └── Your apps              # 웹 앱 설정
```

## 12. 다음 단계

### 12.1 설치 후 권장 작업

1. **보안 규칙 강화**
   - 테스트 모드에서 프로덕션 모드로 전환
   - 사용자별 데이터 접근 제어 설정
   - 데이터 유효성 검사 규칙 추가

2. **에러 처리 개선**
   - 전역 에러 핸들러 구현
   - 사용자 친화적 에러 메시지
   - 에러 로깅 시스템 구축

3. **성능 최적화**
   - 데이터 페이지네이션 구현
   - 이미지 리사이징 (Cloud Functions)
   - 캐싱 전략 적용

4. **추가 기능 구현**
   - 사용자 인증 기능 확장 ([sonub-user-login.md](./sonub-user-login.md) 참조)
   - 프로필 관리
   - 알림 시스템

5. **테스트 작성**
   - 단위 테스트 (Vitest)
   - E2E 테스트 (Playwright)
   - Firebase 에뮬레이터 활용

### 12.2 배포 준비

1. **환경 변수 설정**
   - 프로덕션 환경 변수 설정
   - 민감 정보 보호

2. **보안 규칙 검토**
   - 모든 서비스의 보안 규칙 재검토
   - 테스트 모드 비활성화

3. **성능 모니터링**
   - Firebase Performance Monitoring 설정
   - Google Analytics 설정

## 13. 참고 자료

### 13.1 공식 문서

- **Firebase 공식 문서**: https://firebase.google.com/docs
- **Firebase Web SDK**: https://firebase.google.com/docs/web/setup
- **Authentication**: https://firebase.google.com/docs/auth/web/start
- **Firestore**: https://firebase.google.com/docs/firestore/quickstart
- **Realtime Database**: https://firebase.google.com/docs/database/web/start
- **Storage**: https://firebase.google.com/docs/storage/web/start
- **보안 규칙**: https://firebase.google.com/docs/rules

### 13.2 SvelteKit 통합

- **SvelteKit with Firebase**: https://svelte.dev/docs/kit
- **Environment Variables**: https://svelte.dev/docs/kit/configuration#env
- **SSR Considerations**: https://svelte.dev/docs/kit/ssr

### 13.3 추가 리소스

- **Firebase YouTube**: https://www.youtube.com/firebase
- **Firebase Community**: https://firebase.google.com/community
- **Stack Overflow**: https://stackoverflow.com/questions/tagged/firebase

## 14. 승인 기준

### 14.1 설치 완료 조건

다음 모든 조건을 만족해야 설치가 완료된 것으로 간주합니다:

- ✅ Firebase 프로젝트 생성 완료
- ✅ 웹 앱 등록 완료
- ✅ Authentication, Firestore, RTDB, Storage 활성화
- ✅ `npm install firebase` 완료 (오류 없음)
- ✅ `.env` 파일 생성 및 모든 환경 변수 설정
- ✅ `src/lib/firebase.ts` 파일 생성
- ✅ `npm run check` 통과 (TypeScript 오류 없음)
- ✅ `npm run dev` 실행 가능
- ✅ Authentication 기능 동작 확인 (sonub-user-login.md 참조)
- ✅ Realtime Database 읽기/쓰기 테스트 성공
- ✅ Storage 업로드/다운로드 테스트 성공
- ✅ Firebase Console에서 데이터 확인 가능

### 14.2 품질 기준

- **보안**: 보안 규칙이 적절히 설정됨
- **타입 안전성**: TypeScript 타입 정의 완료
- **에러 처리**: 모든 Firebase 작업에 에러 처리 구현
- **사용자 경험**: 로딩 상태, 에러 메시지 표시
- **코드 품질**: ESLint, Prettier 규칙 준수

## 15. 변경 이력

| 버전 | 날짜 | 작성자 | 변경 내용 |
|------|------|--------|-----------|
| 1.0.0 | 2025-01-08 | JaeHo Song | 초기 명세서 작성 |
| 1.1.0 | 2025-01-09 | JaeHo Song | 실제 구현 내용 반영<br>- 환경 변수 로딩 방법 변경 ($env/static/public 사용)<br>- SSR 대응 강화 (nullable 타입)<br>- .env 파일 형식 수정 (큰따옴표 제거)<br>- 문제 해결 섹션 업데이트 |

---

**주의사항:**
- 이 명세서는 SED(Spec-Exact Development) 원칙에 따라 작성되었습니다.
- AI는 이 명세서에 명시된 내용만 정확히 실행해야 합니다.
- 명세서에 없는 추가 설정이나 변경은 개발자의 명시적 승인이 필요합니다.
- 모든 파일은 UTF-8 인코딩(BOM 없음)으로 저장되어야 합니다.
- Firebase 보안 규칙은 프로덕션 배포 전에 반드시 검토해야 합니다.

**승인:**
- [ ] 개발자 승인 필요
- [ ] Firebase 프로젝트 생성 완료
- [ ] 설치 테스트 완료
- [ ] 문서 검토 완료
