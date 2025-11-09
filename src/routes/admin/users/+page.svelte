<script lang="ts">
	/**
	 * 사용자 목록 페이지
	 *
	 * 생성된 테스트 사용자 목록을 표시하고, 삭제할 수 있는 기능을 제공합니다.
	 */

	import { onMount } from 'svelte';
	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
	import { Alert } from '$lib/components/ui/alert';
import {
	getTemporaryUsers,
	deleteUserByUid,
	deleteAllTemporaryUsers,
	getTemporaryUserCount,
	saveTestUsersToFirebase
} from '$lib/utils/admin-service';
import { generateTestUsers, type TestUser } from '$lib/utils/test-user-generator';

	// 상태 관리
	let users: Record<string, TestUser> = $state({});
let isLoading = $state(true);
let error: string | null = $state(null);
let isDeleting = $state(false);
let deleteProgress = $state(0);
let deleteTotal = $state(0);
let isCreating = $state(false);
let isCreationCompleted = $state(false);
let creationError: string | null = $state(null);
let creationProgress = $state(0);
let creationTotal = $state(0);

	/**
	 * 사용자 목록을 로드합니다.
	 */
	async function loadUsers() {
		isLoading = true;
		error = null;
		try {
			users = await getTemporaryUsers();
		} catch (err) {
			console.error('사용자 목록 로드 중 오류:', err);
			error = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
		} finally {
			isLoading = false;
		}
	}

	/**
	 * 특정 사용자를 삭제합니다.
	 */
	async function handleDeleteUser(uid: string) {
		if (!confirm('이 사용자를 삭제하시겠습니까?')) {
			return;
		}

		try {
			await deleteUserByUid(uid);
			// 목록 새로고침
			await loadUsers();
		} catch (err) {
			console.error('사용자 삭제 중 오류:', err);
			error = err instanceof Error ? err.message : '사용자 삭제 중 오류가 발생했습니다.';
		}
	}

	/**
	 * 모든 테스트 사용자를 삭제합니다.
	 */
async function handleDeleteAllUsers() {
		const count = await getTemporaryUserCount();

		if (count === 0) {
			alert('삭제할 테스트 사용자가 없습니다.');
			return;
		}

		if (!confirm(`${count}명의 모든 테스트 사용자를 삭제하시겠습니까?`)) {
			return;
		}

		isDeleting = true;
		deleteProgress = 0;
		deleteTotal = count;
		error = null;

		try {
			await deleteAllTemporaryUsers((deleted, total) => {
				deleteProgress = deleted;
				deleteTotal = total;
			});

			// 목록 새로고침
			await loadUsers();
		} catch (err) {
			console.error('모든 사용자 삭제 중 오류:', err);
			error = err instanceof Error ? err.message : '사용자 삭제 중 오류가 발생했습니다.';
		} finally {
			isDeleting = false;
		}
}

/**
 * 테스트 사용자 100명을 생성하고 저장합니다.
 */
async function handleCreateUsers() {
	if (isCreating) return;

	isCreating = true;
	isCreationCompleted = false;
	creationError = null;
	creationProgress = 0;

	try {
		const testUsers = generateTestUsers();
		creationTotal = testUsers.length;

		await saveTestUsersToFirebase(testUsers, (index, total) => {
			creationProgress = index;
			creationTotal = total;
		});

		isCreationCompleted = true;
		await loadUsers();
	} catch (err) {
		console.error('테스트 사용자 생성 중 오류:', err);
		creationError = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
	} finally {
		isCreating = false;
	}
}

/**
 * 생년월일을 포맷팅합니다.
 */
	function formatBirthYear(year: number): string {
		return `${year}년`;
	}

	/**
	 * 성별을 한글로 변환합니다.
	 */
	function formatGender(gender: string): string {
		const genderMap: Record<string, string> = {
			male: '남성',
			female: '여성',
			other: '기타'
		};
		return genderMap[gender] || gender;
	}

	/**
	 * 타임스탬프를 날짜로 변환합니다.
	 */
	function formatDate(timestamp: number): string {
		return new Date(timestamp).toLocaleString('ko-KR');
	}

	onMount(() => {
		loadUsers();
	});

const userList = $derived(Object.entries(users));
const userCount = $derived(userList.length);
const deletePercentage = $derived(deleteTotal > 0 ? Math.round((deleteProgress / deleteTotal) * 100) : 0);
const creationPercentage = $derived(
	creationTotal > 0 ? Math.round((creationProgress / creationTotal) * 100) : 0
);
</script>

<div class="space-y-6">
	<!-- 페이지 제목 -->
	<div>
		<h1 class="text-3xl font-bold text-gray-900">사용자 목록</h1>
		<p class="mt-2 text-gray-600">테스트용 임시 사용자 목록을 조회하고 관리합니다.</p>
	</div>

	<!-- 통계 정보 -->
	<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
		<Card>
			<div class="p-6">
				<p class="text-sm text-gray-600">테스트 사용자 수</p>
				<p class="mt-2 text-3xl font-bold text-gray-900">{userCount}</p>
			</div>
		</Card>
		<Card>
			<div class="p-6">
				<p class="text-sm text-gray-600">상태</p>
				<p class="mt-2 text-lg font-semibold text-gray-900">
					{#if isLoading}
						로딩 중...
					{:else if userCount > 0}
						<span class="text-green-600">✓ {userCount}명 생성됨</span>
					{:else}
						<span class="text-gray-600">아직 생성된 사용자 없음</span>
					{/if}
				</p>
			</div>
		</Card>
	</div>

	<!-- 테스트 사용자 생성 영역 -->
	<Card>
		<div class="space-y-6 p-6">
			<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
				<div>
					<h2 class="text-xl font-semibold text-gray-900">테스트 사용자 생성</h2>
					<p class="text-sm text-gray-600">
						버튼을 클릭하면 테스트용 임시 사용자 100명이 순차적으로 생성되고 목록에 추가됩니다.
					</p>
				</div>
				<Button
					onclick={handleCreateUsers}
					disabled={isCreating}
					size="lg"
					class="min-w-48 bg-blue-600 text-white hover:bg-blue-700"
				>
					{#if isCreating}
						⏳ 생성 중...
					{:else if isCreationCompleted}
						✓ 생성 완료
					{:else}
						🚀 테스트 사용자 생성
					{/if}
				</Button>
			</div>

			{#if isCreating || creationProgress > 0}
				<div class="space-y-2">
					<div class="flex justify-between text-sm">
						<span class="text-gray-700">진행 상황</span>
						<span class="font-semibold text-gray-900">
							{creationProgress} / {creationTotal} ({creationPercentage}%)
						</span>
					</div>
					<div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
						<div class="h-full bg-blue-500 transition-all duration-300" style="width: {creationPercentage}%"></div>
					</div>
				</div>
			{/if}

			{#if isCreationCompleted}
				<div class="rounded-lg bg-green-50 p-4 text-sm text-green-800">
					<strong>✓ 완료:</strong> {creationProgress}명의 테스트 사용자가 생성되었습니다.
				</div>
			{/if}

			{#if creationError}
				<div class="rounded-lg bg-red-50 p-4 text-sm text-red-800">
					<strong>✗ 오류:</strong> {creationError}
				</div>
			{/if}

			<div class="grid gap-4 md:grid-cols-2">
				<div class="rounded-lg bg-gray-50 p-4">
					<p class="text-sm text-gray-600">한 번에 생성되는 수</p>
					<p class="mt-1 text-2xl font-bold text-gray-900">100</p>
				</div>
				<div class="rounded-lg bg-gray-50 p-4">
					<p class="text-sm text-gray-600">현재 생성된 수</p>
					<p class="mt-1 text-2xl font-bold text-gray-900">{creationProgress}</p>
				</div>
			</div>
		</div>
	</Card>

	<!-- 삭제 진행 상황 -->
	{#if isDeleting}
		<Card>
			<div class="p-6">
				<div class="space-y-4">
					<div class="flex justify-between text-sm">
						<span class="text-gray-700">삭제 진행 중</span>
						<span class="font-semibold text-gray-900">
							{deleteProgress} / {deleteTotal} ({deletePercentage}%)
						</span>
					</div>
					<div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
						<div
							class="h-full bg-red-500 transition-all duration-300"
							style="width: {deletePercentage}%"
						></div>
					</div>
				</div>
			</div>
		</Card>
	{/if}

	<!-- 에러 메시지 -->
	{#if error}
		<Alert>
			<p class="text-sm text-red-800"><strong>✗ 오류:</strong> {error}</p>
		</Alert>
	{/if}

	<!-- 액션 버튼 -->
	{#if !isLoading && userCount > 0}
		<div class="flex gap-2">
			<Button
				onclick={() => loadUsers()}
				variant="outline"
				disabled={isDeleting}
			>
				새로고침
			</Button>
			<Button
				onclick={handleDeleteAllUsers}
				variant="destructive"
				disabled={isDeleting}
			>
				{#if isDeleting}
					삭제 중...
				{:else}
					모든 테스트 사용자 삭제
				{/if}
			</Button>
		</div>
	{/if}

	<!-- 사용자 목록 -->
	{#if isLoading}
		<Card>
			<div class="p-6">
				<p class="text-center text-gray-600">로딩 중...</p>
			</div>
		</Card>
	{:else if userCount === 0}
		<Card>
			<div class="p-6">
				<p class="text-center text-gray-600">
					생성된 테스트 사용자가 없습니다. 위의 <strong>테스트 사용자 생성</strong> 기능을 사용해 100명을 생성할 수 있습니다.
				</p>
			</div>
		</Card>
	{:else}
		<div class="space-y-4">
			{#each userList as [uid, user] (uid)}
				<Card>
					<div class="p-6">
						<div class="flex items-start justify-between">
							<div class="flex-1">
								<h3 class="text-lg font-semibold text-gray-900">{user.displayName}</h3>
								<p class="mt-1 text-sm text-gray-600">{user.email}</p>

								<!-- 사용자 정보 -->
								<div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
									<div>
										<p class="text-xs text-gray-500">성별</p>
										<p class="mt-1 text-sm font-medium text-gray-900">
											{formatGender(user.gender)}
										</p>
									</div>
									<div>
										<p class="text-xs text-gray-500">생년도</p>
										<p class="mt-1 text-sm font-medium text-gray-900">
											{formatBirthYear(user.birthYear)}
										</p>
									</div>
									<div>
										<p class="text-xs text-gray-500">생성일</p>
										<p class="mt-1 text-sm font-medium text-gray-900">
											{formatDate(user.createdAt)}
										</p>
									</div>
									<div>
										<p class="text-xs text-gray-500">상태</p>
										<p class="mt-1 text-sm font-medium text-orange-600">테스트 사용자</p>
									</div>
								</div>
							</div>

							<!-- 삭제 버튼 -->
							<Button
								onclick={() => handleDeleteUser(uid)}
								variant="destructive"
								size="sm"
								disabled={isDeleting}
								class="ml-4 flex-shrink-0"
							>
								삭제
							</Button>
						</div>
					</div>
				</Card>
			{/each}
		</div>
	{/if}

	<!-- 안내 메시지 -->
	<Card>
		<div class="p-6">
			<h2 class="mb-4 text-xl font-semibold text-gray-900">정보</h2>
			<div class="space-y-2 text-sm text-gray-600">
				<p>• 이 페이지에는 `isTemporary: true`로 표시된 사용자만 표시됩니다.</p>
				<p>• 각 사용자는 개별적으로 또는 일괄적으로 삭제할 수 있습니다.</p>
				<p>• 삭제된 사용자는 복구할 수 없습니다.</p>
			</div>
		</div>
	</Card>
</div>

<style>
	/* 추가 스타일이 필요한 경우 작성 */
</style>
