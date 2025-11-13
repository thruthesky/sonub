---
name: +page.svelte
description: 메뉴 페이지
version: 1.0.0
type: svelte-component
category: route-page
tags: [svelte5, sveltekit]
---

# +page.svelte

## 개요
메뉴 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 메뉴 페이지
	 *
	 * 사용자 메뉴 항목을 표시하는 페이지입니다.
	 * 인증 상태에 따라 다른 메뉴를 표시합니다.
	 */

	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import { signOut } from 'firebase/auth';
	import { auth } from '$lib/firebase';
	import { goto } from '$app/navigation';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { m } from '$lib/paraglide/messages';

	// 로그아웃 처리 중 상태
	let isSigningOut = $state(false);

	/**
	 * 로그아웃 처리
	 */
	async function handleSignOut() {
		if (isSigningOut || !auth) return;

		isSigningOut = true;
		try {
			await signOut(auth);
			console.log('로그아웃 성공');
			await goto('/');
		} catch (error) {
			console.error('로그아웃 에러:', error);
		} finally {
			isSigningOut = false;
		}
	}
</script>

<svelte:head>
	<title>{m.pageTitleMenu()}</title>
</svelte:head>

<div class="min-h-[calc(100vh-8rem)] py-8 px-4">
	<div class="mx-auto w-full max-w-md space-y-6">
		<!-- 메뉴 헤더 -->
		<div class="text-center space-y-3">
			<div class="flex justify-center">
				<div class="p-3 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-sm">
					<svg
						class="h-8 w-8 text-blue-600"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M4 6h16M4 12h16M4 18h16"
						/>
					</svg>
				</div>
			</div>
			<div>
				<h1 class="text-3xl font-bold text-gray-900">{m.menuTitle()}</h1>
				<p class="mt-2 text-sm text-gray-500">{m.menuGuide()}</p>
			</div>
		</div>

		<!-- 로딩 상태 -->
		{#if authStore.loading}
			<Card.Root class="shadow-md border border-gray-200">
				<Card.Content class="pt-6">
					<div class="flex justify-center items-center space-x-2">
						<div
							class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"
						></div>
						<p class="text-center text-gray-600">{m.commonLoading()}</p>
					</div>
				</Card.Content>
			</Card.Root>

			<!-- 로그인 상태 -->
		{:else if authStore.isAuthenticated}
			<!-- 사용자 정보 카드 -->
			<Card.Root class="shadow-lg border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-xl">
				<div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 px-6 pt-8 pb-6">
					<!-- 프로필 정보 -->
					<div class="flex flex-col items-center space-y-4">
						<div class="relative">
							<div
								class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full blur-lg opacity-30"
							></div>
							<div class="relative">
								<Avatar uid={authStore.user?.uid} size={80} />
							</div>
						</div>
						<div class="text-center">
							<p class="text-xl font-bold text-gray-900">
								{authStore.user?.displayName || m.commonUser()}
							</p>
							<p class="text-sm text-gray-600 mt-1">
								{authStore.user?.email || ''}
							</p>
						</div>
					</div>
				</div>
			</Card.Root>

			<!-- 메뉴 항목 카드 -->
			<Card.Root class="shadow-lg border border-gray-200">
				<Card.Content class="space-y-1 p-4">
					<!-- 회원 정보 수정 -->
					<Button
						variant="ghost"
						class="w-full justify-start text-left text-gray-700 hover:bg-blue-50 hover:text-blue-700 border border-transparent hover:border-blue-200 rounded-lg py-3 transition-all duration-200"
						href="/my/profile"
					>
						<div
							class="mr-3 p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors"
						>
							<svg
								class="h-5 w-5 text-blue-600"
								fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
								/>
							</svg>
						</div>
						<span class="font-medium">{m.menuEditProfile()}</span>
					</Button>

					<!-- 관리자 페이지 (관리자만) -->
					{#if authStore.isAdmin}
						<Button
							variant="ghost"
							class="w-full justify-start text-left text-gray-700 hover:bg-purple-50 hover:text-purple-700 border border-transparent hover:border-purple-200 rounded-lg py-3 transition-all duration-200"
							href="/admin/dashboard"
						>
							<div
								class="mr-3 p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors"
							>
								<svg
									class="h-5 w-5 text-purple-600"
									fill="none"
									stroke="currentColor"
									viewBox="0 0 24 24"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
									/>
								</svg>
							</div>
							<span class="font-medium">{m.menuAdminPage()}</span>
						</Button>

						<!-- 개발 테스트 페이지 (관리자만) -->
						<Button
							variant="ghost"
							class="w-full justify-start text-left text-gray-700 hover:bg-green-50 hover:text-green-700 border border-transparent hover:border-green-200 rounded-lg py-3 transition-all duration-200"
							href="/dev/test/database-list-view"
						>
							<div
								class="mr-3 p-2 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors"
							>
								<svg
									class="h-5 w-5 text-green-600"
									fill="none"
									stroke="currentColor"
									viewBox="0 0 24 24"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
									/>
								</svg>
							</div>
							<span class="font-medium">{m.menuDevTest()}</span>
						</Button>
					{/if}

					<!-- 구분선 -->
					<div class="border-t border-gray-200 my-3"></div>

					<!-- 로그아웃 -->
					<Button
						variant="ghost"
						class="w-full justify-start text-left text-red-600 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-200 rounded-lg py-3 transition-all duration-200"
						onclick={handleSignOut}
						disabled={isSigningOut}
					>
						<div
							class="mr-3 p-2 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors"
						>
							<svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
								/>
							</svg>
						</div>
						<span class="font-medium">{isSigningOut ? m.authSigningOut() : m.navLogout()}</span>
					</Button>
				</Card.Content>
			</Card.Root>

			<!-- 비로그인 상태 -->
		{:else}
			<Card.Root class="shadow-lg border border-gray-200 overflow-hidden">
				<div class="bg-gradient-to-br from-gray-50 to-blue-50 p-6">
					<div class="text-center space-y-2">
						<div class="flex justify-center">
							<div
								class="p-4 bg-blue-100 rounded-full"
							>
								<svg
									class="h-8 w-8 text-blue-600"
									fill="none"
									stroke="currentColor"
									viewBox="0 0 24 24"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
									/>
								</svg>
							</div>
						</div>
						<div>
							<h3 class="text-xl font-bold text-gray-900">{m.authSignInRequired()}</h3>
							<p class="text-sm text-gray-600 mt-2">{m.authSignInRequiredDesc()}</p>
						</div>
					</div>
				</div>
				<Card.Content class="p-6">
					<Button
						class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
						href="/user/login"
					>
						{m.authSignInAction()}
					</Button>
				</Card.Content>
			</Card.Root>
		{/if}
	</div>
</div>

```

## 주요 기능
- 코드 분석 필요

## Props/Parameters
State variables: isSigningOut

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<+page />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
