<script lang="ts">
	/**
	 * 우측 사이드바 컴포넌트
	 *
	 * 데스크톱에서만 표시되는 우측 정보/위젯 영역입니다.
	 * TailwindCSS를 사용하여 스타일링합니다.
	 */

	import * as Card from '$lib/components/ui/card/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { m } from '$lib/paraglide/messages';
	import { User, Bell, TrendingUp, Sparkles, Mail } from 'lucide-svelte';
</script>

<aside class="hidden lg:block lg:w-64 xl:w-72">
	<div class="sticky top-20 space-y-5">
		{#if authStore.isAuthenticated}
			<Card.Root class="profile-card">
				<Card.Header class="pb-3">
					<div class="flex items-center gap-2">
						<div class="profile-icon-wrapper">
							<User class="profile-icon" size={16} />
						</div>
						<Card.Title class="text-base font-semibold">{m.sidebarMyProfile()}</Card.Title>
					</div>
				</Card.Header>
				<Card.Content class="space-y-4 pt-2">
					<div class="flex justify-center">
						<div class="avatar-wrapper">
							<Avatar uid={authStore.user?.uid} size={72} />
						</div>
					</div>
					<div class="text-center space-y-1">
						<p class="profile-name">
							{authStore.user?.displayName || m.commonUser()}
						</p>
						<div class="flex items-center justify-center gap-1">
							<Mail size={14} class="text-gray-500" />
							<p class="profile-email">
								{authStore.user?.email || ''}
							</p>
						</div>
					</div>
				</Card.Content>
			</Card.Root>
		{/if}

		<Card.Root class="notification-card">
			<Card.Header class="pb-3">
				<div class="flex items-center gap-2">
					<div class="notification-icon-wrapper">
						<Bell class="notification-icon" size={16} />
					</div>
					<Card.Title class="text-base font-semibold">{m.sidebarNotifications()}</Card.Title>
				</div>
			</Card.Header>
			<Card.Content class="pt-2">
				<p class="empty-state">{m.sidebarNoNotifications()}</p>
			</Card.Content>
		</Card.Root>

		<Card.Root class="suggestions-card">
			<Card.Header class="pb-3">
				<div class="flex items-center gap-2">
					<div class="suggestions-icon-wrapper">
						<Sparkles class="suggestions-icon" size={16} />
					</div>
					<Card.Title class="text-base font-semibold">{m.sidebarSuggestions()}</Card.Title>
				</div>
			</Card.Header>
			<Card.Content class="space-y-2 pt-2">
				<button type="button" class="suggestion-button">
					<TrendingUp size={16} />
					<span>{m.sidebarPopularPosts()}</span>
				</button>
				<button type="button" class="suggestion-button">
					<Sparkles size={16} />
					<span>{m.sidebarNewFeatures()}</span>
				</button>
			</Card.Content>
		</Card.Root>
	</div>
</aside>

<style>
	@import 'tailwindcss' reference;

	/* 프로필 카드 스타일 */
	.profile-card {
		@apply border-blue-100 bg-gradient-to-br from-blue-50 to-white shadow-md;
	}

	.profile-icon-wrapper {
		@apply flex items-center justify-center rounded-full bg-blue-100 p-1.5;
	}

	.profile-icon {
		@apply text-blue-600;
	}

	.avatar-wrapper {
		@apply rounded-full border-4 border-white shadow-lg ring-2 ring-blue-100;
	}

	.profile-name {
		@apply text-base font-semibold text-gray-900;
	}

	.profile-email {
		@apply text-xs text-gray-600;
	}

	/* 알림 카드 스타일 */
	.notification-card {
		@apply border-amber-100 bg-gradient-to-br from-amber-50 to-white shadow-md;
	}

	.notification-icon-wrapper {
		@apply flex items-center justify-center rounded-full bg-amber-100 p-1.5;
	}

	.notification-icon {
		@apply text-amber-600;
	}

	/* 제안 카드 스타일 */
	.suggestions-card {
		@apply border-purple-100 bg-gradient-to-br from-purple-50 to-white shadow-md;
	}

	.suggestions-icon-wrapper {
		@apply flex items-center justify-center rounded-full bg-purple-100 p-1.5;
	}

	.suggestions-icon {
		@apply text-purple-600;
	}

	/* 빈 상태 텍스트 */
	.empty-state {
		@apply text-center text-sm text-gray-500;
	}

	/* 제안 버튼 스타일 */
	.suggestion-button {
		@apply flex w-full cursor-pointer items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium text-gray-700 transition-all hover:bg-purple-100 hover:text-purple-700 hover:shadow-sm;
	}
</style>
