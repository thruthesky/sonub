<script lang="ts">
	/**
	 * 홈페이지
	 *
	 * Sonub 프로젝트의 메인 랜딩 페이지입니다.
	 */

	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';

	function goToLogin() {
		goto('/user/login');
	}
</script>

<svelte:head>
	<title>Sonub - Welcome</title>
</svelte:head>

<div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center">
	<div class="mx-auto max-w-4xl space-y-8 text-center">
		<!-- 메인 타이틀 -->
		<div class="space-y-4">
			<h1 class="text-4xl font-bold text-gray-900 md:text-6xl">Welcome to Sonub</h1>
			<p class="text-lg text-gray-600 md:text-xl">
				SedAi.Dev 에 Sonub spec 으로 커뮤니티 기능을 집어 넣는다.
			</p>
		</div>

		<!-- 사용자 환영 메시지 또는 로그인 유도 -->
		{#if authStore.loading}
			<Card.Root class="mx-auto max-w-md">
				<Card.Content class="pt-6">
					<p class="text-center text-gray-600">로딩 중...</p>
				</Card.Content>
			</Card.Root>
		{:else if authStore.isAuthenticated}
			<Card.Root class="mx-auto max-w-md">
				<Card.Header>
					<Card.Title>환영합니다!</Card.Title>
					<Card.Description>
						{authStore.user?.displayName || authStore.user?.email || '사용자'}님, 로그인하셨습니다.
					</Card.Description>
				</Card.Header>
				<Card.Content>
					<div class="flex items-center justify-center gap-4">
						{#if authStore.user?.photoURL}
							<img
								src={authStore.user.photoURL}
								alt={authStore.user.displayName || '사용자'}
								class="h-16 w-16 rounded-full"
							/>
						{/if}
					</div>
				</Card.Content>
			</Card.Root>
		{:else}
			<Card.Root class="mx-auto max-w-md">
				<Card.Header>
					<Card.Title>시작하기</Card.Title>
					<Card.Description>Google 또는 Apple 계정으로 로그인하여 시작하세요</Card.Description>
				</Card.Header>
				<Card.Content>
					<Button class="w-full" onclick={goToLogin}>로그인하기</Button>
				</Card.Content>
			</Card.Root>
		{/if}

		<!-- 기능 소개 -->
		<div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">SvelteKit 5</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">
						최신 Svelte 5 runes를 사용한 모던 프레임워크
					</p>
				</Card.Content>
			</Card.Root>

			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">Firebase Auth</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">Google 및 Apple 소셜 로그인 지원</p>
				</Card.Content>
			</Card.Root>

			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">TailwindCSS</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">
						shadcn-svelte와 함께하는 아름다운 UI
					</p>
				</Card.Content>
			</Card.Root>
		</div>

		<!-- 링크 -->
		<div
			class="flex flex-col items-center justify-center gap-4 text-sm text-gray-600 sm:flex-row"
		>
			<a
				href="https://svelte.dev/docs/kit"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				SvelteKit 문서
			</a>
			<span class="hidden sm:inline">•</span>
			<a
				href="https://firebase.google.com/docs"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				Firebase 문서
			</a>
			<span class="hidden sm:inline">•</span>
			<a
				href="https://www.shadcn-svelte.com"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				shadcn-svelte
			</a>
		</div>
	</div>
</div>
