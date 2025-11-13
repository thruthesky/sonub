---
title: +page.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 +page.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	import DatabaseListView from '$lib/components/DatabaseListView.svelte';
	import { Button } from '$lib/components/ui/button/index.js';
	import { rtdb } from '$lib/firebase';
	import { ref, push } from 'firebase/database';
	import { formatLongDate } from '$lib/functions/date.functions';

	type CategoryField = 'qnaCreatedAt' | 'newsCreatedAt' | 'reminderCreatedAt';

	type CategoryMeta = {
		key: string;
		label: string;
		field: CategoryField;
		badgeClass: string;
	};

	type ItemPayload = Record<string, unknown> | null | undefined;

	const categories: CategoryMeta[] = [
		{ key: 'qna', label: 'Q&A', field: 'qnaCreatedAt', badgeClass: 'bg-blue-50 text-blue-700' },
		{ key: 'news', label: 'News', field: 'newsCreatedAt', badgeClass: 'bg-green-50 text-green-700' },
		{
			key: 'reminder',
			label: 'Reminder',
			field: 'reminderCreatedAt',
			badgeClass: 'bg-amber-50 text-amber-700'
		}
	];

	// order prefix 옵션 (orderPrefix 필터링용)
	const orderPrefixes = [
		{ key: 'apple', label: 'Apple', prefix: 'apple-', badgeClass: 'bg-red-50 text-red-700' },
		{ key: 'banana', label: 'Banana', prefix: 'banana-', badgeClass: 'bg-yellow-50 text-yellow-700' },
		{ key: 'cherry', label: 'Cherry', prefix: 'cherry-', badgeClass: 'bg-pink-50 text-pink-700' }
	];

	// 테스트 데이터 생성 상태
	let isCreating = $state(false);
	let creationProgress = $state(0);
	let totalToCreate = $state(0);

	// 페이지 옵션 상태
	let pageSize = $state<number>(20);
	let combinedOrderBy = $state<string>('createdAt'); // orderBy와 orderPrefix를 통합한 값
	let reverse = $state<boolean>(true);

	// combinedOrderBy 값을 기반으로 orderBy와 orderPrefix 추출
	const orderBy = $derived(
		combinedOrderBy.startsWith('order-') ? 'order' : combinedOrderBy
	);
	const orderPrefix = $derived(
		combinedOrderBy === 'order-apple' ? 'apple-' :
		combinedOrderBy === 'order-banana' ? 'banana-' :
		combinedOrderBy === 'order-cherry' ? 'cherry-' : ''
	);

	// pageSize 옵션
	const pageSizeOptions = [10, 20, 30, 40, 50];

	// 통합된 orderBy 옵션 (orderBy + orderPrefix)
	type OrderByOption = {
		value: string;
		label: string;
	};

	const orderByOptions: OrderByOption[] = [
		{ value: 'createdAt', label: 'createdAt' },
		{ value: 'qnaCreatedAt', label: 'qnaCreatedAt' },
		{ value: 'newsCreatedAt', label: 'newsCreatedAt' },
		{ value: 'reminderCreatedAt', label: 'reminderCreatedAt' },
		{ value: 'order', label: 'order (전체)' },
		{ value: 'order-apple', label: 'order - Apple (apple-)' },
		{ value: 'order-banana', label: 'order - Banana (banana-)' },
		{ value: 'order-cherry', label: 'order - Cherry (cherry-)' }
	];

	function getCategory(data: ItemPayload): CategoryMeta | null {
		if (!data) {
			return null;
		}

		// category 필드가 있으면 해당 카테고리 반환
		if (data.category && typeof data.category === 'string') {
			const category = categories.find((c) => c.key === data.category);
			if (category) {
				return category;
			}
		}

		// 호환성을 위해 기존 로직도 유지: 특정 필드의 존재 여부로 판단
		for (const meta of categories) {
			if (typeof data[meta.field] === 'number' && !data.category) {
				return meta;
			}
		}

		return null;
	}

	function formatCategoryTimestamp(data: ItemPayload): string {
		const category = getCategory(data);

		if (!category || !data) {
			return '카테고리 타임스탬프 없음';
		}

		const formatted = formatLongDate(
			typeof data[category.field] === 'number' ? (data[category.field] as number) : null
		);

		return `${category.label} · ${formatted || 'N/A'}`;
	}

	/**
	 * 날짜를 YYYY-MM-DD HH:II:SS 형식으로 포맷
	 */
	function formatDateForTitle(date: Date): string {
		const year = date.getFullYear();
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const day = String(date.getDate()).padStart(2, '0');
		const hours = String(date.getHours()).padStart(2, '0');
		const minutes = String(date.getMinutes()).padStart(2, '0');
		const seconds = String(date.getSeconds()).padStart(2, '0');

		return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
	}

	/**
	 * 100개의 테스트 데이터를 생성합니다.
	 * 각 데이터는 1초씩 시간 차이가 있으며, 랜덤 카테고리를 가집니다.
	 */
	async function createTestData() {
		if (!rtdb) {
			alert('Firebase Realtime Database가 초기화되지 않았습니다.');
			return;
		}

		if (isCreating) {
			return;
		}

		isCreating = true;
		totalToCreate = 100;
		creationProgress = 0;

		try {
			const testDataRef = ref(rtdb, 'test/data');
			const now = Date.now();

			const categoryFieldMap = {
		qna: 'qnaCreatedAt',
		news: 'newsCreatedAt',
		reminder: 'reminderCreatedAt'
	} as const;

	for (let i = 0; i < 100; i++) {
				// 1초씩 차이나는 타임스탬프 (역순으로 최신이 먼저 오도록)
				const createdAt = now - i * 1000;
				const date = new Date(createdAt);

				// 랜덤 카테고리 선택
				const randomCategory = categories[Math.floor(Math.random() * categories.length)];

				// 랜덤 order prefix 선택
				const randomOrderPrefix = orderPrefixes[Math.floor(Math.random() * orderPrefixes.length)];

				// 페이지 번호 계산 (pageSize=20 기준)
				const pageNumber = Math.floor(i / 20) + 1;
				// 순서 번호 (1부터 시작)
				const orderNumber = i + 1;

				const title = `[${pageNumber}] ${orderNumber}. [${randomOrderPrefix.label}] [${randomCategory.label}] [${formatDateForTitle(date)}]`;

				// order 필드 값 생성: "prefix-timestamp"
				const orderValue = `${randomOrderPrefix.prefix}${createdAt}`;

				// 데이터 객체 생성
				// 모든 orderBy 옵션을 테스트할 수 있도록 모든 카테고리 타임스탬프 필드를 포함합니다
				// category 필드로 실제 카테고리를 구분합니다
				// order 필드로 orderPrefix 필터링을 테스트합니다
				const data: Record<string, unknown> = {
					title,
					category: randomCategory.key,
					order: orderValue,
					createdAt
				};

				const field = categoryFieldMap[randomCategory.key as keyof typeof categoryFieldMap];
				data[field] = createdAt;

				// Firebase에 저장
				await push(testDataRef, data);

				// 진행 상황 업데이트
				creationProgress = i + 1;

				// UI 업데이트를 위한 짧은 지연
				if (i % 10 === 0) {
					await new Promise((resolve) => setTimeout(resolve, 0));
				}
			}

			alert('100개의 테스트 데이터가 성공적으로 생성되었습니다!');
		} catch (error) {
			console.error('테스트 데이터 생성 실패:', error);
			alert('테스트 데이터 생성 중 오류가 발생했습니다.');
		} finally {
			isCreating = false;
			creationProgress = 0;
			totalToCreate = 0;
		}
	}
</script>

<svelte:head>
	<title>DatabaseListView 테스트 데이터 - Dev Tools</title>
</svelte:head>

<div class="space-y-8">
	<header class="space-y-3">
		<p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Realtime Database</p>
		<h1 class="text-3xl font-bold text-gray-900">/dev/test/database-list-view</h1>
		<p class="text-gray-600">
			`/test/data` 노드에 존재하는 테스트 레코드를 무한 스크롤로 읽어 들여 UI와 DatabaseListView
			동작을 검증합니다.
		</p>
	</header>

	<!-- 옵션 및 필터 섹션 -->
	<section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
		<h2 class="text-lg font-semibold text-gray-900 mb-4">옵션 및 정렬</h2>
		<div class="grid gap-4 md:grid-cols-2">
			<!-- pageSize 선택 -->
			<div>
				<label for="pageSize" class="block text-sm font-medium text-gray-700 mb-2">
					Page Size (한 번에 로드할 데이터 개수)
				</label>
				<select
					id="pageSize"
					bind:value={pageSize}
					class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
				>
					{#each pageSizeOptions as size}
						<option value={size}>{size}개</option>
					{/each}
				</select>
			</div>

			<!-- orderBy 선택 (orderPrefix 통합) -->
			<div>
				<label for="combinedOrderBy" class="block text-sm font-medium text-gray-700 mb-2">
					Order By (정렬 기준 필드)
				</label>
				<select
					id="combinedOrderBy"
					bind:value={combinedOrderBy}
					class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
				>
					{#each orderByOptions as option}
						<option value={option.value}>{option.label}</option>
					{/each}
				</select>
			</div>
		</div>

		<!-- reverse 체크박스 -->
		<div class="mt-4">
			<label class="flex items-center gap-2 cursor-pointer">
				<input
					type="checkbox"
					bind:checked={reverse}
					class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
				/>
				<span class="text-sm font-medium text-gray-700">
					Reverse (역순 정렬 - 최신 데이터 먼저)
				</span>
			</label>
		</div>
	</section>

	<section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:grid-cols-2">
		<div>
			<h2 class="text-lg font-semibold text-gray-900">연결 정보</h2>
			<dl class="mt-4 space-y-3 text-sm text-gray-600">
				<div class="flex justify-between gap-4">
					<dt class="font-medium text-gray-700">RTDB Path</dt>
					<dd class="font-mono text-xs text-gray-900">test/data</dd>
				</div>
				<div class="flex justify-between gap-4">
					<dt class="font-medium text-gray-700">orderBy</dt>
					<dd class="text-gray-900">{orderBy}</dd>
				</div>
				{#if orderPrefix}
					<div class="flex justify-between gap-4">
						<dt class="font-medium text-gray-700">orderPrefix</dt>
						<dd class="text-gray-900">{orderPrefix}</dd>
					</div>
				{/if}
				<div class="flex justify-between gap-4">
					<dt class="font-medium text-gray-700">reverse</dt>
					<dd class="text-gray-900">{reverse ? 'true (역순)' : 'false (정순)'}</dd>
				</div>
				<div class="flex justify-between gap-4">
					<dt class="font-medium text-gray-700">pageSize</dt>
					<dd class="text-gray-900">{pageSize}</dd>
				</div>
				<div class="flex justify-between gap-4">
					<dt class="font-medium text-gray-700">생성 도구</dt>
					<dd class="text-gray-900">/admin/test/create-test-data</dd>
				</div>
			</dl>
		</div>

		<div>
			<h2 class="text-lg font-semibold text-gray-900">카테고리 규칙</h2>
			<ul class="mt-4 space-y-2">
				{#each categories as category}
					<li class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-2 text-sm">
						<span class="font-medium text-gray-800">{category.label}</span>
						<span class="font-mono text-xs text-gray-500">{category.field}</span>
					</li>
				{/each}
			</ul>
		</div>
	</section>

	<section class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
		<div class="flex flex-wrap items-center justify-between gap-3">
			<div>
				<h2 class="text-xl font-semibold text-gray-900">테스트 데이터 스트림</h2>
				<p class="text-sm text-gray-500">
					최신 생성 순으로 표시되며 스크롤이 끝에 닿으면 자동으로 다음 페이지를 요청합니다.
				</p>
			</div>
			<div class="flex items-center gap-3">
				<Button
					onclick={createTestData}
					disabled={isCreating}
					class="bg-blue-600 hover:bg-blue-700 text-white"
				>
					{#if isCreating}
						<svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
							<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
						</svg>
						생성 중... ({creationProgress}/{totalToCreate})
					{:else}
						<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						데이터 생성하기
					{/if}
				</Button>
				<div class="rounded-full bg-gray-100 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
					Infinite Scroll
				</div>
			</div>
		</div>

		{#key `${pageSize}-${combinedOrderBy}-${reverse}`}
			<DatabaseListView path="test/data" pageSize={pageSize} orderBy={orderBy} orderPrefix={orderPrefix} threshold={320} reverse={reverse}>
				{#snippet item(itemData: { key: string; data: any }, index: number)}
					{@const category = getCategory(itemData.data)}
					{@const actualPageNumber = Math.floor(index / pageSize) + 1}
					{@const actualOrderNumber = index + 1}
					{@const displayTitle = itemData.data?.title
						? itemData.data.title.replace(/^\[\d+\] \d+\./, `[${actualPageNumber}] ${actualOrderNumber}.`)
						: `[${actualPageNumber}] ${actualOrderNumber}. 제목 없음`}
					<article class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-gray-200">
						<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
							<div>
								<p class="text-xs uppercase tracking-wide text-gray-400">Key (Index: {index})</p>
								<p class="font-mono text-sm text-gray-900">{itemData.key}</p>
							</div>
							{#if category}
								<span class={`rounded-full px-3 py-1 text-xs font-semibold ${category.badgeClass}`}>
									{category.label}
								</span>
							{:else}
								<span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
									Unlabeled
								</span>
							{/if}
						</div>

						<div class="mt-4 space-y-1">
							<p class="text-lg font-semibold text-gray-900">{displayTitle}</p>
							<p class="text-sm text-gray-500">
								생성 시각: {formatLongDate(itemData.data?.createdAt)}
							</p>
							<p class="text-sm text-gray-500">{formatCategoryTimestamp(itemData.data)}</p>
						</div>

						<pre class="mt-4 overflow-auto rounded-lg bg-gray-900 p-4 text-xs text-gray-100">{JSON.stringify(
							itemData.data ?? {},
							null,
							2
						)}</pre>
					</article>
				{/snippet}

				{#snippet loading()}
					<p class="py-6 text-center text-sm text-gray-500">Realtime Database에서 데이터를 불러오는 중...</p>
				{/snippet}

				{#snippet loadingMore()}
					<p class="py-4 text-center text-xs uppercase tracking-wide text-gray-400">
						다음 페이지를 읽어오는 중입니다...
					</p>
				{/snippet}

				{#snippet noMore()}
					<p class="py-6 text-center text-sm text-gray-500">
						더 이상 불러올 데이터가 없습니다. 상단 버튼으로 새 데이터를 생성해 보세요.
					</p>
				{/snippet}

				{#snippet empty()}
					<div class="py-10 text-center text-gray-500">
						<p class="text-lg font-semibold">데이터가 없습니다.</p>
						<p class="mt-2 text-sm">/admin/test/create-test-data 페이지에서 테스트 데이터를 생성해 주세요.</p>
					</div>
				{/snippet}
			</DatabaseListView>
		{/key}
	</section>
</div>

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
