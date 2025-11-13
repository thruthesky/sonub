---
name: +page.svelte
description: 게시판 목록 페이지
version: 1.0.0
type: svelte-component
category: route-page
tags: [svelte5, sveltekit]
---

# +page.svelte

## 개요
게시판 목록 페이지

## 소스 코드

```svelte
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

```

## 주요 기능
- 자유토론/질문답변/회원장터 탭을 토글하여 게시판 카테고리를 선택할 수 있는 상단 탭바 표시
- 글쓰기 버튼을 우측에 고정 배치하여 향후 게시글 작성 기능을 위한 진입점을 제공 (현재는 공사중 안내와 함께 비활성화)
- 전체 콘텐츠 영역은 공사중 컴포넌트로 대체되어 향후 개발 예정임을 명확히 전달

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<+page />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
