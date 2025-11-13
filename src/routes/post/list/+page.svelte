<script lang="ts">
	/**
	 * 게시판 목록 페이지
	 *
	 * 아직 개발 중인 게시판 목록 페이지입니다.
	 */

	import UnderConstruction from '$lib/components/under-construction.svelte';
	import { Button } from '$lib/components/ui/button/index.js';
	import { m } from '$lib/paraglide/messages';

	type BoardTab = {
		id: string;
		label: () => string;
	};

	const boardTabs: BoardTab[] = [
		{ id: 'free', label: () => m.boardTabFree() },
		{ id: 'qna', label: () => m.boardTabQna() },
		{ id: 'market', label: () => m.boardTabMarket() }
	];

	let activeTab = $state(boardTabs[0]?.id ?? 'free');

	function handleTabSelect(tabId: string) {
		activeTab = tabId;
	}
</script>

<svelte:head>
	<title>{m.pageTitleBoard()}</title>
</svelte:head>

<section class="container mx-auto flex min-h-[calc(100vh-6rem)] flex-col gap-6 px-4 pb-12 pt-24">
	<div class="flex w-full flex-wrap items-center gap-3 border-b border-gray-200 pb-4">
		<div class="flex flex-wrap items-center gap-2" role="tablist" aria-label={m.navBoard()}>
			{#each boardTabs as tab}
				<button
					type="button"
					class="tab-button px-4 py-2"
					class:tab-button-active={activeTab === tab.id}
					on:click={() => handleTabSelect(tab.id)}
					aria-pressed={activeTab === tab.id}
					aria-label={tab.label()}
				>
					{tab.label()}
				</button>
			{/each}
		</div>

		<div class="ml-auto">
			<Button type="button" disabled title={m.boardConstruction()} class="write-button">
				{m.boardWritePost()}
			</Button>
		</div>
	</div>

	<UnderConstruction title={m.navBoard()} message={m.boardConstruction()} />
</section>

<style>
	@import 'tailwindcss' reference;

	.tab-button {
		@apply cursor-pointer rounded-full text-sm font-medium text-gray-500 transition-colors duration-150;
	}

	.tab-button-active {
		@apply bg-gray-900 text-white shadow-sm;
	}

	.write-button {
		@apply cursor-not-allowed px-6 text-sm font-semibold;
	}
</style>
