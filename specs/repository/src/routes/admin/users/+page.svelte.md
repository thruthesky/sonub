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
		getTemporaryUserCount
	} from '$lib/utils/admin-service';
	import type { TestUser } from '$lib/utils/test-user-generator';
	import { m } from '$lib/paraglide/messages';

	// 상태 관리
	let users: Record<string, TestUser> = $state({});
	let isLoading = $state(true);
	let error: string | null = $state(null);
	let isDeleting = $state(false);
	let deleteProgress = $state(0);
	let deleteTotal = $state(0);

	/**
	 * 사용자 목록을 로드합니다.
	 */
	async function loadUsers() {
		isLoading = true;
		error = null;
		try {
			users = await getTemporaryUsers();
		} catch (err) {
			console.error('테스트 사용자 목록 로드 중 오류:', err);
			error = err instanceof Error ? err.message : m.userUnknownError();
		} finally {
			isLoading = false;
		}
	}

	/**
	 * 특정 사용자를 삭제합니다.
	 */
	async function handleDeleteUser(uid: string) {
		if (!confirm('이 테스트 사용자를 삭제하시겠습니까?')) {
			return;
		}

		try {
			await deleteUserByUid(uid);
			// 목록 새로고침
			await loadUsers();
		} catch (err) {
			const errorMsg = '테스트 사용자 삭제 중 오류가 발생했습니다.';
			console.error(errorMsg, err);
			error = err instanceof Error ? err.message : errorMsg;
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
			const errorMsg = '모든 테스트 사용자 삭제 중 오류:';
			console.error(errorMsg, err);
			error = err instanceof Error ? err.message : errorMsg;
		} finally {
			isDeleting = false;
		}
	}

	/**
	 * 생년월일을 포맷팅합니다.
	 */
	function formatBirthYear(year: number): string {
		return m.profileYearValue({ year });
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
	const deletePercentage = $derived(
		deleteTotal > 0 ? Math.round((deleteProgress / deleteTotal) * 100) : 0
	);
</script>

<div class="space-y-6">
	<!-- 페이지 제목 -->
	<div>
		<h1 class="text-3xl font-bold text-gray-900">{m.testUserList()}</h1>
		<p class="mt-2 text-gray-600">{m.testUserGuide()}</p>
	</div>

	<!-- 통계 정보 -->
	<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
		<Card>
			<div class="p-6">
				<p class="text-sm text-gray-600">{m.testUserCount()}</p>
				<p class="mt-2 text-3xl font-bold text-gray-900">{userCount}</p>
			</div>
		</Card>
		<Card>
			<div class="p-6">
				<p class="text-sm text-gray-600">{m.commonStatus()}</p>
				<p class="mt-2 text-lg font-semibold text-gray-900">
					{#if isLoading}
						{m.commonLoading()}
					{:else if userCount > 0}
						<span class="text-green-600">✓ {m.testUserCreated({ count: userCount })}</span>
					{:else}
						<span class="text-gray-600">{m.testUserNotCreated()}</span>
					{/if}
				</p>
			</div>
		</Card>
	</div>

	<!-- 삭제 진행 상황 -->
	{#if isDeleting}
		<Card>
			<div class="p-6">
				<div class="space-y-4">
					<div class="flex justify-between text-sm">
						<span class="text-gray-700">{m.testUserDeletingInProgress()}</span>
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
			<p class="text-sm text-red-800">
				<strong>✗ {m.commonError()}:</strong>
				{error}
			</p>
		</Alert>
	{/if}

	<!-- 액션 버튼 -->
	{#if !isLoading && userCount > 0}
		<div class="flex gap-2">
			<Button onclick={() => loadUsers()} variant="outline" disabled={isDeleting}>
				{m.commonRefresh()}
			</Button>
			<Button onclick={handleDeleteAllUsers} variant="destructive" disabled={isDeleting}>
				{#if isDeleting}
					{m.testUserDeleting()}
				{:else}
					{m.testUserDeleteAll()}
				{/if}
			</Button>
		</div>
	{/if}

	<!-- 사용자 목록 -->
	{#if isLoading}
		<Card>
			<div class="p-6">
				<p class="text-center text-gray-600">{m.commonLoading()}</p>
			</div>
		</Card>
	{:else if userCount === 0}
		<Card>
			<div class="p-6">
				<p class="text-center text-gray-600">
					{@html m.testUserNotCreatedGuide()}
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
										<p class="text-xs text-gray-500">{m.testUserGender()}</p>
										<p class="mt-1 text-sm font-medium text-gray-900">
											{formatGender(user.gender)}
										</p>
									</div>
									<div>
										<p class="text-xs text-gray-500">{m.testUserBirthYear()}</p>
										<p class="mt-1 text-sm font-medium text-gray-900">
											{formatBirthYear(user.birthYear)}
										</p>
									</div>
									<div>
										<p class="text-xs text-gray-500">{m.testUserCreatedDate()}</p>
										<p class="mt-1 text-sm font-medium text-gray-900">
											{formatDate(user.createdAt)}
										</p>
									</div>
									<div>
										<p class="text-xs text-gray-500">{m.commonStatus()}</p>
										<p class="mt-1 text-sm font-medium text-orange-600">{m.testUserStatus()}</p>
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
								{m.commonDelete()}
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
			<h2 class="mb-4 text-xl font-semibold text-gray-900">{m.commonInfo()}</h2>
			<div class="space-y-2 text-sm text-gray-600">
				<p>{m.testUserInfoDisplay()}</p>
				<p>{m.testUserInfoDelete()}</p>
				<p>{m.testUserInfoNoRecover()}</p>
			</div>
		</div>
	</Card>
</div>

<style>
	/* 추가 스타일이 필요한 경우 작성 */
</style>

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
