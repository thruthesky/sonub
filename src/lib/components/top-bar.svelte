<script lang="ts">
	/**
	 * 탑바 (상단 네비게이션 바) 컴포넌트
	 *
	 * 사용자 로그인 상태에 따라 다른 메뉴를 표시하는 반응형 네비게이션 바입니다.
	 * TailwindCSS와 shadcn-svelte Button 컴포넌트를 사용합니다.
	 */

	import { Button } from '$lib/components/ui/button/index.js';
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

	/**
	 * 로그인 페이지로 이동
	 */
	function goToLogin() {
		goto('/user/login');
	}
</script>

<nav class="border-b bg-white dark:bg-gray-900">
	<div class="container mx-auto px-4">
		<div class="flex h-16 items-center justify-between">
			<!-- 좌측: 로고 및 네비게이션 링크 -->
			<div class="flex items-center gap-8">
				<a
					href="/"
					class="text-xl font-bold text-gray-900 hover:text-gray-700 dark:text-white dark:hover:text-gray-300"
				>
					Sonub
				</a>
				<div class="hidden gap-4 md:flex">
					<a
						href="/about"
						class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
					>
						소개
					</a>
					<a
						href="/products"
						class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
					>
						제품
					</a>
					<a
						href="/contact"
						class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
					>
						연락
					</a>
				</div>
			</div>

			<!-- 우측: 사용자 메뉴 -->
			<div class="flex items-center gap-4">
				{#if authStore.loading}
					<!-- 로딩 중 -->
					<div class="text-sm text-gray-500 dark:text-gray-400">로딩 중...</div>
				{:else if authStore.isAuthenticated}
					<!-- 로그인 상태 -->
					<div class="flex items-center gap-4">
						<div class="hidden items-center gap-2 sm:flex">
							{#if authStore.user?.photoURL}
								<img
									src={authStore.user.photoURL}
									alt={authStore.user.displayName || '사용자'}
									class="h-8 w-8 rounded-full"
								/>
							{/if}
							<span class="text-sm text-gray-700 dark:text-gray-300">
								{authStore.user?.displayName || authStore.user?.email || '사용자'}
							</span>
						</div>
						<Button variant="ghost" size="sm" onclick={handleSignOut} disabled={isSigningOut}>
							{isSigningOut ? '로그아웃 중...' : '로그아웃'}
						</Button>
					</div>
				{:else}
					<!-- 비로그인 상태 -->
					<Button variant="ghost" size="sm" onclick={goToLogin}>로그인</Button>
				{/if}
			</div>
		</div>
	</div>
</nav>
