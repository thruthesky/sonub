<script lang="ts">
	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
	import { Alert } from '$lib/components/ui/alert';
	import { rtdb } from '$lib/firebase';
	import { ref, push, set } from 'firebase/database';

	const categories = ['qna', 'news', 'reminder'] as const;

	let isGenerating = $state(false);
	let targetCount = $state(30);
	let progress = $state(0);
	let errorMessage = $state<string | null>(null);
	let recentKeys = $state<string[]>([]);
	let lastCategory = $state<string | null>(null);
	let lastTimestamp = $state<number | null>(null);

	function clampCount(value: number): number {
		if (Number.isNaN(value) || value < 1) {
			return 1;
		}

		if (value > 200) {
			return 200;
		}

		return Math.floor(value);
	}

	function formatDate(value: number | null): string {
		if (!value) {
			return '생성 기록 없음';
		}

		return new Date(value).toLocaleString('ko-KR');
	}

	async function handleGenerate(): Promise<void> {
		if (!rtdb) {
			errorMessage = 'Firebase Realtime Database 초기화가 필요합니다.';
			return;
		}

		const sanitizedCount = clampCount(targetCount);

		if (sanitizedCount !== targetCount) {
			targetCount = sanitizedCount;
		}

		isGenerating = true;
		progress = 0;
		errorMessage = null;
		recentKeys = [];
		lastCategory = null;
		lastTimestamp = null;

		try {
			const baseRef = ref(rtdb, 'test/data');

			for (let i = 0; i < sanitizedCount; i += 1) {
				const timestamp = Date.now() + i;
				const category = categories[Math.floor(Math.random() * categories.length)];
				const payload = {
					title: `테스트 데이터 #${timestamp}`,
					createdAt: timestamp,
					[`${category}CreatedAt`]: timestamp
				};

				const newRef = push(baseRef);
				await set(newRef, payload);

				progress = i + 1;
				lastCategory = category;
				lastTimestamp = timestamp;

				if (newRef.key) {
					recentKeys = [newRef.key, ...recentKeys].slice(0, 5);
				}
			}
		} catch (error) {
			console.error('테스트 데이터 생성 실패:', error);
			errorMessage = error instanceof Error ? error.message : '알 수 없는 오류가 발생했습니다.';
		} finally {
			isGenerating = false;
		}
	}
</script>

<svelte:head>
	<title>테스트 데이터 생성 - Sonub Admin</title>
</svelte:head>

<div class="space-y-6">
	<div>
		<h1 class="text-3xl font-bold text-gray-900">테스트 데이터 생성</h1>
		<p class="mt-2 text-gray-600">
			`/test/data` 노드에 무한 스크롤 확인용 가짜 데이터를 대량으로 생성합니다.
		</p>
	</div>

	<Alert>
		<div class="space-y-2 text-sm text-gray-700">
			<p>
				<strong>데이터 구조:</strong> {`{`} title, createdAt, [category]CreatedAt {`}`}
			</p>
			<p>
				<strong>카테고리:</strong> qna, news, reminder 중 랜덤으로 선택되며
				각각 `qnaCreatedAt`, `newsCreatedAt`, `reminderCreatedAt` 필드로 저장됩니다.
			</p>
			<p>• 생성된 데이터는 `/dev/test/database-list-view` 페이지에서 바로 확인할 수 있습니다.</p>
			<p>• 한번에 최대 200개까지 생성할 수 있습니다.</p>
		</div>
	</Alert>

	<Card>
		<div class="space-y-6 p-6">
			<div class="grid gap-4 md:grid-cols-2">
				<div>
					<label for="targetCount" class="block text-sm font-medium text-gray-700">생성할 데이터 개수</label>
					<input
						id="targetCount"
						type="number"
						min="1"
						max="200"
						class="mt-2 w-full rounded-md border border-gray-300 px-4 py-3 text-lg focus:border-blue-500 focus:outline-none"
						bind:value={targetCount}
					/>
					<p class="mt-2 text-sm text-gray-500">1~200 사이의 값을 입력하세요.</p>
				</div>

				<div class="rounded-lg bg-gray-50 p-4">
					<p class="text-sm text-gray-600">최근 생성 정보</p>
					<p class="mt-1 font-semibold text-gray-900">
						{lastCategory ? `${lastCategory} ( ${formatDate(lastTimestamp)} )` : '아직 생성되지 않았습니다.'}
					</p>
				</div>
			</div>

			<div class="flex flex-col gap-4 md:flex-row">
				<Button
					onclick={handleGenerate}
					disabled={isGenerating}
					class="cursor-pointer bg-blue-600 px-6 py-3 text-base font-semibold text-white hover:bg-blue-700"
				>
					{#if isGenerating}
						생성 중...
					{:else}
						테스트 데이터 생성
					{/if}
				</Button>

				<div class="flex flex-1 flex-col justify-center rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-600">
					<p>
						진행 상황: <span class="font-semibold text-gray-900">{progress}</span> / {targetCount}
					</p>
					<p>상태: {isGenerating ? '실행 중' : progress > 0 ? '완료 또는 대기 중' : '대기 중'}</p>
				</div>
			</div>

			{#if errorMessage}
				<div class="rounded-lg bg-red-50 p-4 text-sm text-red-800">
					<strong>오류:</strong> {errorMessage}
				</div>
			{/if}

			{#if recentKeys.length > 0}
				<div>
					<h2 class="text-lg font-semibold text-gray-900">최근 생성된 키 (최대 5개)</h2>
					<ul class="mt-3 space-y-2 text-sm text-gray-700">
						{#each recentKeys as key}
							<li class="rounded border border-gray-200 bg-white px-3 py-2 font-mono text-xs text-gray-900">
								{key}
							</li>
						{/each}
					</ul>
				</div>
			{/if}
		</div>
	</Card>

	<Card>
		<div class="space-y-4 p-6 text-sm text-gray-600">
			<h2 class="text-xl font-semibold text-gray-900">데이터 검증 체크리스트</h2>
			<ul class="list-inside list-disc space-y-2">
				<li>`Database > test > data` 경로에 새로운 노드가 생성되는지 확인합니다.</li>
				<li>각 노드에는 `title`, `createdAt`, 카테고리 전용 타임스탬프 중 하나가 포함되어야 합니다.</li>
				<li>카테고리별 필드 이름은 `qnaCreatedAt`, `newsCreatedAt`, `reminderCreatedAt` 형식이어야 합니다.</li>
				<li>`/dev/test/database-list-view`에서 새 데이터가 역순으로 노출되는지 확인합니다.</li>
			</ul>
		</div>
	</Card>
</div>

<style>
	/* 필요 시 전역 스타일을 최소화하여 추가 */
</style>
