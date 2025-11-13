---
name: +page.svelte
description: 로그인 페이지
version: 1.0.0
type: svelte-component
category: route-page
tags: [svelte5, sveltekit]
---

# +page.svelte

## 개요
로그인 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 로그인 페이지
	 *
	 * 사용자가 Google 또는 Apple 계정으로 로그인할 수 있는 페이지입니다.
	 * 이미 로그인된 사용자는 자동으로 홈페이지로 리다이렉트됩니다.
	 */

	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import UserLogin from '$lib/components/user-login.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { m } from '$lib/paraglide/messages';

	/**
	 * 컴포넌트 마운트 시 인증 상태 확인
	 * 이미 로그인된 사용자는 홈페이지로 리다이렉트
	 */
	onMount(() => {
		// 인증 시스템이 초기화될 때까지 대기
		const checkAuth = setInterval(() => {
			if (authStore.initialized) {
				clearInterval(checkAuth);

				// 이미 로그인된 경우 홈페이지로 이동
				if (authStore.isAuthenticated) {
					goto('/');
				}
			}
		}, 100);

		// 컴포넌트 언마운트 시 interval 정리
		return () => clearInterval(checkAuth);
	});
</script>

<svelte:head>
	<title>{m.pageTitleLogin()}</title>
	<meta name="description" content={m.pageMetaLogin()} />
</svelte:head>

<div class="container flex min-h-screen items-center justify-center px-4 py-8">
	{#if authStore.loading}
		<!-- 인증 상태 확인 중 로딩 표시 -->
		<div class="flex flex-col items-center gap-4">
			<svg
				class="h-8 w-8 animate-spin text-primary"
				xmlns="http://www.w3.org/2000/svg"
				fill="none"
				viewBox="0 0 24 24"
			>
				<circle
					class="opacity-25"
					cx="12"
					cy="12"
					r="10"
					stroke="currentColor"
					stroke-width="4"
				></circle>
				<path
					class="opacity-75"
					fill="currentColor"
					d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
				></path>
			</svg>
			<p class="text-muted-foreground">{m.commonLoading()}</p>
		</div>
	{:else}
		<!-- 로그인 컴포넌트 표시 -->
		<UserLogin class="w-full max-w-md" />
	{/if}
</div>

```

## 주요 기능
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<+page />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
