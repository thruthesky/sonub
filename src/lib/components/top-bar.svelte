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

</script>

<nav class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-sm">
	<div class="container mx-auto px-4">
		<div class="flex h-16 items-center justify-between">
			<!-- 좌측: 로고 및 네비게이션 링크 -->
			<div class="flex items-center gap-8">
				<a
					href="/"
					class="text-xl font-bold text-gray-900 hover:text-gray-700"
				>
					Sonub
				</a>
				<div class="hidden gap-4 md:flex">
					<a
						href="/about"
						class="text-gray-600 hover:text-gray-900"
					>
						소개
					</a>
					<a
						href="/products"
						class="text-gray-600 hover:text-gray-900"
					>
						제품
					</a>
					<a
						href="/contact"
						class="text-gray-600 hover:text-gray-900"
					>
						연락
					</a>
				</div>
			</div>

			<!-- 우측: 사용자 메뉴 -->
			<div class="flex items-center gap-4">
				{#if authStore.loading}
					<!-- 로딩 중 -->
					<div class="text-sm text-gray-500">로딩 중...</div>
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
							<span class="text-sm text-gray-700">
								{authStore.user?.displayName || authStore.user?.email || '사용자'}
							</span>
						</div>
						<Button variant="ghost" size="sm" onclick={handleSignOut} disabled={isSigningOut}>
							{isSigningOut ? '로그아웃 중...' : '로그아웃'}
						</Button>
					</div>
				{:else}
					<!-- 비로그인 상태 -->
					<Button variant="ghost" size="sm" href="/user/login">로그인</Button>
				{/if}

				<!-- 메뉴 아이콘 (모든 상태에서 표시) -->
				<Button
					href="/menu"
					variant="ghost"
					size="icon"
					aria-label="메뉴"
					title="메뉴"
					class="text-gray-600 hover:text-gray-900"
				>
					<svg
						class="h-6 w-6"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M4 6h16M4 12h16M4 18h16"
						/>
					</svg>
				</Button>
			</div>
		</div>
	</div>
</nav>
