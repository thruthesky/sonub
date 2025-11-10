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
	import { m } from '$lib/paraglide/messages-proxy';
</script>

<svelte:head>
	<title>{m.page_title_home()}</title>
</svelte:head>

<div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center">
	<div class="mx-auto max-w-4xl space-y-8 text-center">
		<!-- 메인 타이틀 -->
		<div class="space-y-4">
			<h1 class="text-4xl font-bold text-gray-900 md:text-6xl">{m.auth_welcome_message()}</h1>
			<p class="text-lg text-gray-600 md:text-xl">
				{m.auth_intro()}
			</p>
		</div>

		<!-- 사용자 환영 메시지 또는 로그인 유도 -->
		{#if authStore.loading}
			<Card.Root class="mx-auto max-w-md">
				<Card.Content class="pt-6">
					<p class="text-center text-gray-600">{m.loading()}</p>
				</Card.Content>
			</Card.Root>
		{:else if authStore.isAuthenticated}
			<Card.Root class="mx-auto max-w-md">
				<Card.Header>
					<Card.Title>{m.auth_welcome()}</Card.Title>
					<Card.Description>
						{m.auth_welcome_user({ name: authStore.user?.displayName || authStore.user?.email || m.user() })}
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
					<Card.Title>{m.auth_get_started()}</Card.Title>
					<Card.Description>{m.auth_sign_in_guide_start()}</Card.Description>
				</Card.Header>
				<Card.Content>
					<Button class="w-full" href="/user/login">{m.auth_sign_in_action()}</Button>
				</Card.Content>
			</Card.Root>
		{/if}

		<!-- 기능 소개 -->
		<div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">{m.feature_sveltekit5()}</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">
						{m.feature_sveltekit5_desc()}
					</p>
				</Card.Content>
			</Card.Root>

			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">{m.feature_firebase_auth()}</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">{m.feature_firebase_auth_desc()}</p>
				</Card.Content>
			</Card.Root>

			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">{m.feature_tailwind_css()}</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">
						{m.feature_tailwind_css_desc()}
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
				{m.link_svelte_kit_docs()}
			</a>
			<span class="hidden sm:inline">•</span>
			<a
				href="https://firebase.google.com/docs"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				{m.link_firebase_docs()}
			</a>
			<span class="hidden sm:inline">•</span>
			<a
				href="https://www.shadcn-svelte.com"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				{m.link_shadcn_svelte()}
			</a>
		</div>
	</div>
</div>
