---
title: +page.svelte
type: component
path: src/routes/+page.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/routes/+page.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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
	import { formatShortDate } from '$lib/functions/date.functions';
import { get, limitToLast, onValue, orderByChild, query, ref, type DatabaseReference } from 'firebase/database';

	type UserPreview = {
		uid: string;
		displayName: string;
		photoUrl: string | null;
		sortRecentWithPhoto: number;
	};

	type OpenChatPreview = {
		roomId: string;
		roomName: string;
		lastMessageText: string;
		lastMessageAt: number;
		orderValue: number;
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
let recentOpenChats = $state<OpenChatPreview[]>([]);
let isLoadingRecentOpenChats = $state(true);

onMount(() => {
	fetchRecentUsers();
});

$effect(() => {
	const uid = authStore.user?.uid ?? null;

	if (!uid || !rtdb) {
		recentOpenChats = [];
		isLoadingRecentOpenChats = false;
		return;
	}

	isLoadingRecentOpenChats = true;

	const joinsRef = ref(rtdb, `chat-joins/${uid}`);
	const openQuery = query(joinsRef, orderByChild('openChatListOrder'), limitToLast(5));

	return onValue(
		openQuery,
		(snapshot) => {
			const items: OpenChatPreview[] = [];

			snapshot.forEach((child) => {
				const value = child.val() ?? {};

				if (value?.roomType !== 'open') {
					return;
				}

				const orderValue = resolveOrderValue(value?.openChatListOrder);
				const roomId = child.key ?? '';

				items.push({
					roomId,
					roomName: (value?.roomName as string) || roomId || 'Open Chat',
					lastMessageText: (value?.lastMessageText as string) || '',
					lastMessageAt: Number(value?.lastMessageAt) || 0,
					orderValue
				});
			});

			items.sort((a, b) => b.orderValue - a.orderValue);

			recentOpenChats = items.slice(0, 5);
			isLoadingRecentOpenChats = false;
		},
		(error) => {
			console.error('[Home] 최근 오픈 채팅 메시지 실시간 구독 실패:', error);
			recentOpenChats = [];
			isLoadingRecentOpenChats = false;
		}
	);
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

function resolveOrderValue(value: unknown): number {
		if (typeof value === 'number') {
			return value;
		}

		if (typeof value === 'string') {
			let boost = 0;
			let normalized = value;

			if (value.startsWith('500')) {
				boost = 2;
				normalized = value.slice(3);
			} else if (value.startsWith('200')) {
				boost = 1;
				normalized = value.slice(3);
			}

			const base = Number.parseInt(normalized, 10);
			return boost * 1e15 + (Number.isFinite(base) ? base : 0);
		}

		return 0;
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
					{:else if card.id === 'recent-open-chat'}
						{#if !authStore.isAuthenticated}
							<div class="placeholder-panel">
								<p class="placeholder-text">{m.homeSectionRecentOpenChatLogin()}</p>
							</div>
						{:else if isLoadingRecentOpenChats}
							<div class="placeholder-panel">
								<p class="placeholder-text">{m.commonLoading()}</p>
							</div>
						{:else if recentOpenChats.length === 0}
							<div class="placeholder-panel">
								<p class="placeholder-text">{m.homeSectionRecentOpenChatEmpty()}</p>
							</div>
						{:else}
							<ul class="open-chat-list">
								{#each recentOpenChats as chat}
									<li class="open-chat-item">
										<div class="open-chat-head">
											<span class="open-chat-room">{chat.roomName}</span>
											{#if chat.lastMessageAt}
												<span class="open-chat-time">{formatShortDate(chat.lastMessageAt)}</span>
											{/if}
										</div>
										<p class="open-chat-body">
											{chat.lastMessageText || m.homeOpenChatNoMessage()}
										</p>
									</li>
								{/each}
							</ul>
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

	.open-chat-list {
		@apply space-y-3;
	}

	.open-chat-item {
		@apply rounded-lg border border-blue-100 bg-blue-50/80 p-3 shadow-sm;
	}

	.open-chat-head {
		@apply flex items-center justify-between gap-2;
	}

	.open-chat-room {
		@apply text-sm font-semibold text-blue-900;
	}

	.open-chat-time {
		@apply text-xs text-gray-500;
	}

	.open-chat-body {
		@apply mt-1 text-sm text-gray-700;
	}
</style>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
