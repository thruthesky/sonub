<script lang="ts">
	/**
	 * 좌측 사이드바 컴포넌트
	 *
	 * 데스크톱에서만 표시되는 좌측 네비게이션/메뉴 영역입니다.
	 * TailwindCSS를 사용하여 스타일링합니다.
	 */

	import * as Card from '$lib/components/ui/card/index.js';
</script>

<aside class="hidden lg:block lg:w-64 xl:w-72">
	<div class="sticky top-20 space-y-4">
		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">메뉴</Card.Title>
			</Card.Header>
			<Card.Content class="space-y-2">
				<a
					href="/"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
				>
					홈
				</a>
				<a
					href="/about"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
				>
					소개
				</a>
				<a
					href="/products"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
				>
					제품
				</a>
				<a
					href="/contact"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
				>
					연락
				</a>
			</Card.Content>
		</Card.Root>

		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">최근 활동</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="text-sm text-gray-600 dark:text-gray-400">최근 활동이 없습니다.</p>
			</Card.Content>
		</Card.Root>
	</div>
</aside>
