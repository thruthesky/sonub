<script lang="ts">
	/**
	 * 테스트 사용자 생성 페이지
	 *
	 * 테스트용 임시 사용자 100명을 일괄 생성하는 페이지입니다.
	 * 버튼을 클릭하면 사용자가 순차적으로 생성되며, 진행 상황을 실시간으로 표시합니다.
	 */

	import { onMount } from 'svelte';
	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
	import { Alert } from '$lib/components/ui/alert';
	import { generateTestUsers } from '$lib/utils/test-user-generator';
	import { saveTestUsersToFirebase } from '$lib/utils/admin-service';

	// 상태 관리
	let isLoading = $state(false);
	let isCompleted = $state(false);
	let error: string | null = $state(null);
	let progress = $state(0);
	let totalUsers = $state(0);
	let successCount = $state(0);

	/**
	 * 테스트 사용자를 생성하고 Firebase에 저장합니다.
	 */
	async function handleCreateUsers() {
		// 초기화
		isLoading = true;
		isCompleted = false;
		error = null;
		progress = 0;
		successCount = 0;

		try {
			// 테스트 사용자 100명 생성
			const testUsers = generateTestUsers();
			totalUsers = testUsers.length;

			// Firebase에 저장
			await saveTestUsersToFirebase(testUsers, (index, total) => {
				progress = index;
				successCount = index;
			});

			// 완료
			isCompleted = true;
		} catch (err) {
			// 에러 처리
			console.error('테스트 사용자 생성 중 오류:', err);
			error = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
		} finally {
			isLoading = false;
		}
	}

	/**
	 * 진행률을 백분율로 계산합니다.
	 */
	function getProgressPercentage(): number {
		return totalUsers > 0 ? Math.round((progress / totalUsers) * 100) : 0;
	}

	onMount(() => {
		// 마운트 시 초기화
		isLoading = false;
		isCompleted = false;
		error = null;
		progress = 0;
		totalUsers = 0;
		successCount = 0;
	});
</script>

<div class="space-y-6">
	<!-- 페이지 제목 -->
	<div>
		<h1 class="text-3xl font-bold text-gray-900">테스트 사용자 생성</h1>
		<p class="mt-2 text-gray-600">
			버튼을 클릭하여 Firebase에 테스트용 임시 사용자 100명을 일괄 생성합니다.
		</p>
	</div>

	<!-- 안내 메시지 -->
	<Alert>
		<div class="space-y-2 text-sm">
			<p><strong>주의:</strong> 이 기능은 테스트 목적으로만 사용하세요.</p>
			<p>• 100명의 테스트 사용자가 생성됩니다</p>
			<p>• 각 사용자는 `isTemporary: true` 플래그로 표시됩니다</p>
			<p>• 테스트 데이터는 `/admin/users` 페이지에서 확인할 수 있습니다</p>
			<p>• 생성된 사용자는 `/admin/users` 페이지에서 일괄 삭제할 수 있습니다</p>
		</div>
	</Alert>

	<!-- 메인 카드 -->
	<Card>
		<div class="p-8">
			<div class="space-y-6">
				<!-- 버튼 -->
				<div class="flex justify-center">
					<Button
						onclick={handleCreateUsers}
						disabled={isLoading}
						size="lg"
						class="min-w-48 cursor-pointer text-base"
					>
						{#if isLoading}
							생성 중...
						{:else if isCompleted}
							완료됨
						{:else}
							테스트 사용자 생성
						{/if}
					</Button>
				</div>

				<!-- 진행 상황 -->
				{#if isLoading || (isCompleted && progress > 0)}
					<div class="space-y-2">
						<div class="flex justify-between text-sm">
							<span class="text-gray-700">진행 상황</span>
							<span class="font-semibold text-gray-900">
								{progress} / {totalUsers} ({getProgressPercentage()}%)
							</span>
						</div>
						<!-- 진행률 바 -->
						<div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
							<div
								class="h-full bg-blue-500 transition-all duration-300"
								style="width: {getProgressPercentage()}%"
							/>
						</div>
					</div>
				{/if}

				<!-- 완료 메시지 -->
				{#if isCompleted && progress > 0}
					<div class="rounded-lg bg-green-50 p-4">
						<p class="text-center text-green-800">
							<strong>✓ 완료!</strong> {successCount}명의 테스트 사용자가 생성되었습니다.
						</p>
					</div>
				{/if}

				<!-- 에러 메시지 -->
				{#if error}
					<div class="rounded-lg bg-red-50 p-4">
						<p class="text-center text-red-800">
							<strong>✗ 오류:</strong> {error}
						</p>
					</div>
				{/if}

				<!-- 상태 정보 -->
				<div class="border-t pt-6">
					<h3 class="mb-4 font-semibold text-gray-900">상태 정보</h3>
					<div class="grid grid-cols-2 gap-4">
						<div class="rounded-lg bg-gray-50 p-4">
							<p class="text-sm text-gray-600">생성할 사용자 수</p>
							<p class="mt-1 text-2xl font-bold text-gray-900">100</p>
						</div>
						<div class="rounded-lg bg-gray-50 p-4">
							<p class="text-sm text-gray-600">현재 생성된 수</p>
							<p class="mt-1 text-2xl font-bold text-gray-900">{progress}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</Card>

	<!-- 정보 섹션 -->
	<Card>
		<div class="p-6">
			<h2 class="mb-4 text-xl font-semibold text-gray-900">생성되는 사용자 정보</h2>
			<div class="space-y-2 text-sm text-gray-600">
				<p>• <strong>displayName:</strong> "테스트 사용자 001" ~ "테스트 사용자 100"</p>
				<p>• <strong>email:</strong> "test.user.001@example.com" ~ "test.user.100@example.com"</p>
				<p>• <strong>gender:</strong> "male", "female", "other" (랜덤)</p>
				<p>• <strong>birthYear:</strong> 1950~2010년 범위 (랜덤)</p>
				<p>• <strong>isTemporary:</strong> true (테스트 사용자 표시)</p>
				<p>• <strong>createdAt/updatedAt:</strong> 생성 시간 (밀리초 타임스탬프)</p>
			</div>
		</div>
	</Card>
</div>

<style>
	/* 추가 스타일이 필요한 경우 작성 */
</style>
