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
	<title>메뉴 - Sonub</title>
</svelte:head>

<div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center">
	<div class="mx-auto w-full max-w-md space-y-6">
		<!-- 메뉴 헤더 -->
		<div class="text-center">
			<h1 class="text-2xl font-bold text-gray-900">메뉴</h1>
			<p class="mt-2 text-sm text-gray-600">계정 및 설정 관리</p>
		</div>

		<!-- 로딩 상태 -->
		{#if authStore.loading}
			<Card.Root>
				<Card.Content class="pt-6">
					<p class="text-center text-gray-600">로딩 중...</p>
				</Card.Content>
			</Card.Root>

		<!-- 로그인 상태 -->
		{:else if authStore.isAuthenticated}
			<!-- 사용자 정보 카드 -->
			<Card.Root>
				<Card.Header>
					<Card.Title class="text-base">내 계정</Card.Title>
				</Card.Header>
				<Card.Content class="space-y-3">
					<!-- 프로필 정보 -->
					{#if authStore.user?.photoURL}
						<div class="flex justify-center">
							<img
								src={authStore.user.photoURL}
								alt={authStore.user.displayName || '사용자'}
								class="h-16 w-16 rounded-full"
							/>
						</div>
					{/if}
					<div class="text-center">
						<p class="font-medium text-gray-900">
							{authStore.user?.displayName || '사용자'}
						</p>
						<p class="text-sm text-gray-600">
							{authStore.user?.email || ''}
						</p>
					</div>
				</Card.Content>
			</Card.Root>

			<!-- 메뉴 항목 카드 -->
			<Card.Root>
				<Card.Content class="space-y-2 pt-6">
					<!-- 회원 정보 수정 -->
					<Button
						variant="ghost"
						class="w-full justify-start text-left text-gray-700 hover:bg-gray-100 hover:text-gray-900"
						href="/my/profile"
					>
						<svg class="mr-3 h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
						</svg>
						<span>회원 정보 수정</span>
					</Button>

					<!-- 관리자 페이지 (관리자만) -->
					{#if authStore.isAdmin}
						<Button
							variant="ghost"
							class="w-full justify-start text-left text-gray-700 hover:bg-gray-100 hover:text-gray-900"
							href="/admin/dashboard"
						>
							<svg class="mr-3 h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
							</svg>
							<span>관리자 페이지</span>
						</Button>

						<!-- 개발 테스트 페이지 (관리자만) -->
						<Button
							variant="ghost"
							class="w-full justify-start text-left text-gray-700 hover:bg-gray-100 hover:text-gray-900"
							href="/dev/test/database-list-view"
						>
							<svg class="mr-3 h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
							</svg>
							<span>개발 테스트 (DatabaseListView)</span>
						</Button>
					{/if}

					<!-- 로그아웃 -->
					<Button
						variant="ghost"
						class="w-full justify-start text-left text-red-600 hover:bg-red-50"
						onclick={handleSignOut}
						disabled={isSigningOut}
					>
						<svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
						</svg>
						<span>{isSigningOut ? '로그아웃 중...' : '로그아웃'}</span>
					</Button>
				</Card.Content>
			</Card.Root>

		<!-- 비로그인 상태 -->
		{:else}
			<Card.Root>
				<Card.Header>
					<Card.Title>로그인 필요</Card.Title>
					<Card.Description>계정에 로그인하여 더 많은 기능을 사용하세요</Card.Description>
				</Card.Header>
				<Card.Content>
					<Button class="w-full" href="/user/login">로그인하기</Button>
				</Card.Content>
			</Card.Root>
		{/if}
	</div>
</div>
