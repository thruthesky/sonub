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
</script>

<aside class="hidden lg:block lg:w-64 xl:w-72">
	<div class="sticky top-20 space-y-4">
		{#if authStore.isAuthenticated}
			<Card.Root>
				<Card.Header>
					<Card.Title class="text-base">내 프로필</Card.Title>
				</Card.Header>
				<Card.Content class="space-y-3">
					<div class="flex justify-center">
						<Avatar uid={authStore.user?.uid} size={64} />
					</div>
					<div class="text-center">
						<p class="font-medium text-gray-900">
							{authStore.user?.displayName || '사용자'}
						</p>
						<p class="text-sm text-gray-600">
							{authStore.user?.email || ''}
						</p>
					</div>
				</Card.Content>
			</Card.Root>
		{/if}

		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">알림</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="text-sm text-gray-600">새로운 알림이 없습니다.</p>
			</Card.Content>
		</Card.Root>

		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">추천</Card.Title>
			</Card.Header>
			<Card.Content class="space-y-2">
				<button
					type="button"
					class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
				>
					인기 게시물
				</button>
				<button
					type="button"
					class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
				>
					새로운 기능
				</button>
			</Card.Content>
		</Card.Root>
	</div>
</aside>
