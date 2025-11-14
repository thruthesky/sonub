<script lang="ts">
	/**
	 * 홈페이지
	 *
	 * Sonub 프로젝트의 메인 랜딩 페이지입니다.
	 */

	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { m } from '$lib/paraglide/messages';

	const dashboardCards = [
		{
			id: 'recent-users',
			title: () => m.homeSectionRecentUsers(),
			description: () => m.homeSectionRecentUsersDesc()
		},
		{
			id: 'recent-open-chat',
			title: () => m.homeSectionRecentOpenChat(),
			description: () => m.homeSectionRecentOpenChatDesc()
		},
		{
			id: 'popular-open-room',
			title: () => m.homeSectionPopularOpenRoom(),
			description: () => m.homeSectionPopularOpenRoomDesc()
		},
		{
			id: 'recent-posts',
			title: () => m.homeSectionRecentPosts(),
			description: () => m.homeSectionRecentPostsDesc()
		}
	];
</script>

<svelte:head>
	<title>{m.pageTitleHome()}</title>
</svelte:head>

<div class="mx-auto max-w-7xl space-y-8">
	<!-- 메인 타이틀 -->
	<div class="space-y-4 text-center">
		<h1 class="text-4xl font-bold text-gray-900 md:text-6xl">{m.authWelcomeMessage()}</h1>
		<p class="text-lg text-gray-600 md:text-xl">
			{m.authIntro()}
		</p>
	</div>

	<!-- 사용자 환영 메시지 또는 로그인 유도 -->
	{#if authStore.loading}
		<Card.Root class="mx-auto max-w-md">
			<Card.Content class="pt-6">
				<p class="text-center text-gray-600">{m.commonLoading()}</p>
			</Card.Content>
		</Card.Root>
	{:else if authStore.isAuthenticated}
		<Card.Root class="mx-auto max-w-md">
			<Card.Header>
				<Card.Title>{m.authWelcome()}</Card.Title>
				<Card.Description>
					{m.authWelcomeUser({ name: authStore.user?.displayName || authStore.user?.email || m.commonUser() })}
				</Card.Description>
			</Card.Header>
			<Card.Content>
				<div class="flex items-center justify-center gap-4">
					{#if authStore.user?.uid}
						<Avatar uid={authStore.user.uid} size={64} class="shadow-sm" />
					{:else}
						<div class="h-16 w-16 rounded-full bg-gray-200" aria-hidden="true"></div>
					{/if}
				</div>
			</Card.Content>
		</Card.Root>
	{:else}
		<Card.Root class="mx-auto max-w-md">
			<Card.Header>
				<Card.Title>{m.authGetStarted()}</Card.Title>
				<Card.Description>{m.authSignInGuideStart()}</Card.Description>
			</Card.Header>
			<Card.Content>
				<Button class="w-full" href="/user/login">{m.authSignInAction()}</Button>
			</Card.Content>
		</Card.Root>
	{/if}
	<!-- 대시보드 카드 플레이스홀더 -->
	<section class="grid gap-4 md:grid-cols-2">
		{#each dashboardCards as card}
			<Card.Root class="home-card">
				<Card.Header>
					<Card.Title class="text-lg font-semibold text-gray-900">{card.title()}</Card.Title>
					<Card.Description class="text-sm text-gray-600">
						{card.description()}
					</Card.Description>
				</Card.Header>
				<Card.Content>
					<div class="placeholder-panel">
						<div class="placeholder-indicators">
							<span class="indicator-dot" aria-hidden="true"></span>
							<span class="indicator-dot" aria-hidden="true"></span>
							<span class="indicator-dot" aria-hidden="true"></span>
						</div>
						<p class="placeholder-text">
							Coming soon
						</p>
					</div>
				</Card.Content>
			</Card.Root>
		{/each}
	</section>
</div>

<style>
	@import 'tailwindcss' reference;

	.home-card {
		@apply h-full border border-gray-100 shadow-sm;
	}

	.placeholder-panel {
		@apply flex min-h-[140px] flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50/60 text-center;
	}

	.placeholder-indicators {
		@apply mb-3 flex items-center gap-2;
	}

	.indicator-dot {
		@apply h-2 w-2 animate-pulse rounded-full bg-gray-400;
	}

	.placeholder-text {
		@apply text-sm font-medium text-gray-500;
	}
</style>
