---
name: +page.svelte
description: 공개 사용자 프로필 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/user/profile/+page.svelte
---

# +page.svelte

## 개요

**파일 경로**: `src/routes/user/profile/+page.svelte`  
**파일 타입**: svelte-component  
**카테고리**: route-page

다른 사용자의 공개 프로필을 표시하고 1:1 채팅으로 진입할 수 있는 페이지입니다. `uid` 쿼리 파라미터를 필수로 요구하며, 실시간 사용자 스토어를 통해 프로필을 구독합니다.

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

## 주요 기능

- `uid` 쿼리 파라미터를 통해 다른 사용자의 `/users/{uid}` 데이터를 실시간 구독합니다.
- 프로필 사진, 닉네임, 자기소개를 카드로 보여줍니다.
- 로그인 상태라면 `/chat/room?uid={target}` 링크로 바로 이동하는 1:1 채팅 버튼을 제공합니다.
- `uid` 누락, 로딩, 에러, 미등록 사용자 상태에 맞춰 안내 메시지를 출력합니다.

## 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-14 | Codex Agent | `/user/profile` 페이지 생성: 프로필 실시간 구독, 아바타 및 채팅 버튼 추가 |
