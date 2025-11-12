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
						{m.navAbout()}
					</a>
					<a
						href="/products"
						class="text-gray-600 hover:text-gray-900"
					>
						{m.navProducts()}
					</a>
					<a
						href="/contact"
						class="text-gray-600 hover:text-gray-900"
					>
						{m.navContact()}
					</a>
				</div>
			</div>

			<!-- 우측: 사용자 메뉴 -->
			<div class="flex items-center gap-2">
				<!-- 게시판 버튼 -->
				<Button
					href="/post/list"
					variant="ghost"
					aria-label={m.navBoard()}
					title={m.navBoard()}
					class="cursor-pointer text-gray-600 hover:text-gray-900 px-2 lg:px-3 gap-2"
				>
					<svg
						class="h-6 w-6 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M3 7h18M3 12h18M3 17h18"
						/>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M7 3v18M17 3v18"
						/>
					</svg>
					<span class="hidden lg:inline text-sm">{m.navBoard()}</span>
				</Button>

				<!-- 채팅 버튼 -->
				<Button
					href="/chat/list"
					variant="ghost"
					aria-label={m.navChat()}
					title={m.navChat()}
					class="cursor-pointer text-gray-600 hover:text-gray-900 px-2 lg:px-3 gap-2"
				>
					<svg
						class="h-6 w-6 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
						/>
					</svg>
					<span class="hidden lg:inline text-sm">{m.navChat()}</span>
				</Button>

				<!-- 친구찾기 버튼 -->
				<Button
					href="/user/list"
					variant="ghost"
					aria-label={m.navFindUsers()}
					title={m.navFindUsers()}
					class="cursor-pointer text-gray-600 hover:text-gray-900 px-2 lg:px-3 gap-2"
				>
					<svg
						class="h-6 w-6 flex-shrink-0"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
						/>
					</svg>
					<span class="hidden lg:inline text-sm">{m.navFindUsers()}</span>
				</Button>

				{#if authStore.loading}
					<!-- 로딩 중 -->
					<div class="h-10 w-10 animate-pulse rounded-full bg-gray-200"></div>
				{:else if authStore.isAuthenticated && authStore.user}
					<!-- 로그인 상태: 사용자 아바타 -->
					<a
						href="/my/profile"
						class="cursor-pointer hover:opacity-80 transition-opacity"
						aria-label={m.navMyProfile()}
						title={authStore.user.displayName || authStore.user.email || m.navMyProfile()}
					>
						<Avatar uid={authStore.user?.uid} size={40} />
					</a>
				{:else}
					<!-- 비로그인 상태: 로그인 버튼 -->
					<Button variant="ghost" size="sm" href="/user/login" class="cursor-pointer">
						{m.navLogin()}
					</Button>
				{/if}

				<!-- 햄버거 메뉴 아이콘 -->
				<Button
					href="/menu"
					variant="ghost"
					size="icon"
					aria-label={m.navMenu()}
					title={m.navMenu()}
					class="cursor-pointer text-gray-600 hover:text-gray-900"
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
