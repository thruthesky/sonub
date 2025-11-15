---
title: +page.svelte
type: component
path: src/routes/user/profile/+page.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/routes/user/profile/+page.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	import { page } from '$app/stores';
	import Avatar from '$lib/components/user/avatar.svelte';
	import * as Card from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
	import { authStore } from '$lib/stores/auth.svelte';
	import { userProfileStore } from '$lib/stores/user-profile.svelte';
	import { m } from '$lib/paraglide/messages';

	const uidParam = $derived.by(() => $page.url.searchParams.get('uid') ?? '');

	$effect(() => {
		if (uidParam) {
			userProfileStore.ensureSubscribed(uidParam);
		}
	});

	const profile = $derived(userProfileStore.getCachedProfile(uidParam));
	const isLoading = $derived(userProfileStore.isLoading(uidParam));
	const loadError = $derived(userProfileStore.getError(uidParam));

	const displayName = $derived.by(() => profile?.displayName || m.userNoName());
	const profileBio = $derived.by(() => profile?.bio || '');
	const chatUrl = $derived.by(() => (uidParam ? `/chat/room?uid=${encodeURIComponent(uidParam)}` : '#'));
</script>

<svelte:head>
	<title>{m.userProfileDetail()}</title>
</svelte:head>

<section class="profile-page">
	<Card.Root class="profile-card">
		<Card.Header class="profile-header">
			<Card.Title class="profile-title">{m.userProfileDetail()}</Card.Title>
		</Card.Header>

		<Card.Content class="profile-body">
			{#if !uidParam}
				<p class="profile-alert">{m.chatProvideUid()}</p>
			{:else if isLoading}
				<p class="profile-status">{m.profileLoading()}</p>
			{:else if loadError}
				<p class="profile-alert">{m.profileLoadFailed()}</p>
			{:else if !profile}
				<p class="profile-alert">{m.userNotRegistered()}</p>
			{:else}
				<div class="profile-main">
					<Avatar uid={uidParam} size={96} class="profile-avatar" />
					<div class="profile-info">
						<h2 class="profile-name">{displayName}</h2>
						{#if profileBio}
							<p class="profile-bio">{profileBio}</p>
						{/if}
					</div>
				</div>

				<div class="profile-actions">
					{#if authStore.isAuthenticated}
						<Button href={chatUrl} class="profile-chat-button">
							{m.chatSingleChat()}
						</Button>
					{:else}
						<Button href="/user/login" variant="secondary" class="profile-login-button">
							{m.chatSignInRequired()}
						</Button>
					{/if}
				</div>
			{/if}
		</Card.Content>
	</Card.Root>
</section>

<style>
	@import 'tailwindcss' reference;

	.profile-page {
		@apply mx-auto flex min-h-[60vh] w-full max-w-3xl items-center justify-center px-4 py-12;
	}

	.profile-card {
		@apply w-full border border-gray-100 bg-white shadow-sm;
	}

	.profile-header {
		@apply text-center;
	}

	.profile-title {
		@apply text-2xl font-semibold text-gray-900;
	}

	.profile-body {
		@apply flex flex-col gap-6;
	}

	.profile-status {
		@apply text-center text-gray-600;
	}

	.profile-alert {
		@apply text-center text-sm font-medium text-red-600;
	}

	.profile-main {
		@apply flex flex-col items-center gap-4;
	}

	.profile-info {
		@apply text-center;
	}

	.profile-name {
		@apply text-xl font-semibold text-gray-900;
	}

	.profile-bio {
		@apply text-sm text-gray-600;
	}

	.profile-actions {
		@apply flex flex-col gap-3 sm:flex-row sm:justify-center;
	}

	.profile-chat-button {
		@apply w-full bg-blue-600 text-white shadow hover:bg-blue-700;
	}

	.profile-login-button {
		@apply w-full;
	}
</style>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
