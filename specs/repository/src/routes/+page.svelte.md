---
name: +page.svelte
description: +page 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/+page.svelte
---

# +page.svelte

## 개요

**파일 경로**: `src/routes/+page.svelte`
**파일 타입**: svelte-component
**카테고리**: route-page

+page 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 홈페이지
	 *
	 * Sonub 프로젝트의 메인 랜딩 페이지입니다.
	 */

	import { onMount } from 'svelte';
	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { m } from '$lib/paraglide/messages';
	import { rtdb } from '$lib/firebase';
	import { get, limitToLast, orderByChild, query, ref, type DatabaseReference } from 'firebase/database';

	type UserPreview = {
		uid: string;
		displayName: string;
		photoUrl: string | null;
		sortRecentWithPhoto: number;
	};

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

	let recentUsers = $state<UserPreview[]>([]);
	let isLoadingRecentUsers = $state(true);

	onMount(() => {
		fetchRecentUsers();
	});

	async function fetchRecentUsers() {
		if (!rtdb) {
			isLoadingRecentUsers = false;
			return;
		}

		try {
			const usersRef: DatabaseReference = ref(rtdb, 'users');
			const recentQuery = query(usersRef, orderByChild('sort_recentWithPhoto'), limitToLast(5));
			const snapshot = await get(recentQuery);

			const users: UserPreview[] = [];

			snapshot.forEach((child) => {
				const value = child.val();
				users.push({
					uid: child.key ?? '',
					displayName: value?.displayName ?? '',
					photoUrl: value?.photoUrl ?? null,
					sortRecentWithPhoto: value?.sort_recentWithPhoto ?? 0
				});
			});

			users.sort((a, b) => (b.sortRecentWithPhoto ?? 0) - (a.sortRecentWithPhoto ?? 0));
			recentUsers = users.filter((user) => Boolean(user.photoUrl));
		} catch (error) {
			console.error('[Home] 최근 사용자 로드 실패:', error);
			recentUsers = [];
		} finally {
			isLoadingRecentUsers = false;
		}
	}
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
					{#if card.id === 'recent-users'}
						{#if isLoadingRecentUsers}
							<div class="placeholder-panel">
								<p class="placeholder-text">{m.commonLoading()}</p>
							</div>
						{:else if recentUsers.length === 0}
							<div class="placeholder-panel">
								<p class="placeholder-text">{m.homeSectionRecentUsersDesc()}</p>
							</div>
						{:else}
							<div class="stacked-wrapper">
								<div class="stacked-avatars" aria-label={m.homeSectionRecentUsers()}>
									{#each recentUsers as user, index (user.uid)}
										<img
											src={user.photoUrl ?? ''}
											alt={user.displayName || 'recent user'}
											class="stacked-avatar"
											style={`z-index: ${recentUsers.length - index};`}
											loading="lazy"
										/>
									{/each}
								</div>
								<p class="stacked-caption">
									{m.homeSectionRecentUsersCount({ count: recentUsers.length })}
								</p>
							</div>
						{/if}
					{:else}
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
					{/if}
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

	.stacked-wrapper {
		@apply flex flex-col items-start gap-3;
	}

	.stacked-avatars {
		@apply flex items-center;
	}

	.stacked-avatar {
		@apply h-12 w-12 rounded-full border-2 border-white object-cover shadow ring-1 ring-gray-200;
		margin-left: -0.75rem;
	}

	.stacked-avatar:first-child {
		margin-left: 0;
	}

	.stacked-caption {
		@apply text-sm font-medium text-gray-800;
	}
</style>

```

## 주요 기능

- 홈페이지 상단에서 로그인 상태에 따라 환영 카드 또는 로그인 유도 버튼을 표시합니다.
- `최근 사용자` 카드: `/users` 경로에서 `sort_recentWithPhoto` 기준으로 최신 5명의 프로필 이미지를 스택드 아바타로 보여줍니다.
- `최근 오픈 채팅 메시지` 카드: 로그인한 사용자의 `/chat-joins/{uid}` 데이터를 `openChatListOrder` 기준 내림차순으로 조회해 최신 5개의 오픈 채팅 미리보기를 출력합니다.

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)

## 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-14 | Codex Agent | `최근 오픈 채팅 메시지` 카드가 `/chat-joins/{uid}`의 `openChatListOrder` TOP 5를 실시간 구독하도록 명시 |
