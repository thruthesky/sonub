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

## 3. 테스트 및 참고 문서

- [sonub-user-login.md](./sonub-user-login.md): 실제 로그인 UI/UX 흐름 정의.
- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 앱 초기화 및 환경 변수 설정.
- 수동 검증 체크리스트:
  1. 로그인 성공 후 사용자 정보가 즉시 UI에 표시되는지 확인
  2. 새로 고침 시 로그인 상태가 유지되는지 확인
  3. 로그아웃 후 `currentUser`가 null이 되는지 확인
