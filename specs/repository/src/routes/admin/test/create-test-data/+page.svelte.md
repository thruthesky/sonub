---
title: +page.svelte
type: component
path: src/routes/admin/test/create-test-data/+page.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/routes/admin/test/create-test-data/+page.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
	import { Alert } from '$lib/components/ui/alert';
	import { rtdb } from '$lib/firebase';
	import { ref, push, set } from 'firebase/database';
	import { saveTestUsersToFirebase } from '$lib/utils/admin-service';
	import { generateTestUsers } from '$lib/utils/test-user-generator';
	import { m } from '$lib/paraglide/messages';

	const categories = ['qna', 'news', 'reminder'] as const;

	let isGenerating = $state(false);
	let targetCount = $state(30);
	let progress = $state(0);
	let errorMessage = $state<string | null>(null);
	let recentKeys = $state<string[]>([]);
	let lastCategory = $state<string | null>(null);
	let lastTimestamp = $state<number | null>(null);
	let isCreatingTestUsers = $state(false);
	let userCreationProgress = $state(0);
	let userCreationTotal = $state(0);
	let userCreationCompleted = $state(false);
	let userCreationError = $state<string | null>(null);

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

	const userCreationPercentage = $derived(
		userCreationTotal > 0 ? Math.round((userCreationProgress / userCreationTotal) * 100) : 0
	);

	async function handleCreateTestUsers() {
		if (isCreatingTestUsers) return;

		isCreatingTestUsers = true;
		userCreationCompleted = false;
		userCreationError = null;
		userCreationProgress = 0;

		try {
			const testUsers = generateTestUsers();
			userCreationTotal = testUsers.length;

			await saveTestUsersToFirebase(testUsers, (index, total) => {
				userCreationProgress = index;
				userCreationTotal = total;
			});

			userCreationCompleted = true;
		} catch (error) {
			console.error('테스트 사용자 생성 실패:', error);
			userCreationError = error instanceof Error ? error.message : '알 수 없는 오류가 발생했습니다.';
		} finally {
			isCreatingTestUsers = false;
		}
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

	<Card>
		<div class="space-y-6 p-6">
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<div>
					<h2 class="text-xl font-semibold text-gray-900">{m.testUserCreate()}</h2>
					<p class="text-sm text-gray-600">{m.testUserCreateGuide()}</p>
				</div>
				<Button
					onclick={handleCreateTestUsers}
					disabled={isCreatingTestUsers}
					size="lg"
					class="min-w-48 bg-blue-600 text-white hover:bg-blue-700"
				>
					{#if isCreatingTestUsers}
						{m.testUserCreating()}
					{:else if userCreationCompleted}
						{m.testUserCreateComplete()}
					{:else}
						{m.testUserCreateIcon()}
					{/if}
				</Button>
			</div>

			{#if isCreatingTestUsers || userCreationProgress > 0}
				<div class="space-y-2">
					<div class="flex justify-between text-sm">
						<span class="text-gray-700">{m.commonProgress()}</span>
						<span class="font-semibold text-gray-900">
							{userCreationProgress} / {userCreationTotal} ({userCreationPercentage}%)
						</span>
					</div>
					<div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
						<div
							class="h-full bg-blue-500 transition-all duration-300"
							style="width: {userCreationPercentage}%"
						></div>
					</div>
				</div>
			{/if}

			{#if userCreationCompleted}
				<div class="rounded-lg bg-green-50 p-4 text-sm text-green-800">
					{m.testUserCreateCompleteMessage({ count: userCreationProgress })}
				</div>
			{/if}

			{#if userCreationError}
				<div class="rounded-lg bg-red-50 p-4 text-sm text-red-800">
					<strong>✗ {m.commonError()}:</strong>
					{userCreationError}
				</div>
			{/if}

			<div class="grid gap-4 md:grid-cols-2">
				<div class="rounded-lg bg-gray-50 p-4">
					<p class="text-sm text-gray-600">{m.testUserCreateAtOnce()}</p>
					<p class="mt-1 text-2xl font-bold text-gray-900">100</p>
				</div>
				<div class="rounded-lg bg-gray-50 p-4">
					<p class="text-sm text-gray-600">{m.testUserCurrentCreated()}</p>
					<p class="mt-1 text-2xl font-bold text-gray-900">{userCreationProgress}</p>
				</div>
			</div>
		</div>
	</Card>

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

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
