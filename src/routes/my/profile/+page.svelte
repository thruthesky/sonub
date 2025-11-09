<script lang="ts">
	/**
	 * 내 프로필 수정 페이지
	 *
	 * 로그인한 사용자 자신의 프로필 정보를 수정하는 페이지입니다.
	 * - 닉네임 (displayName)
	 * - 성별 (gender)
	 * - 생년월일 (birthYear, birthMonth, birthDay)
	 */

	import { authStore } from '$lib/stores/auth.svelte';
	import { rtdb } from '$lib/firebase';
	import { ref, get, update } from 'firebase/database';
	import { goto } from '$app/navigation';
	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import * as Alert from '$lib/components/ui/alert/index.js';

	// 폼 데이터 상태
	let displayName = $state('');
	let gender = $state<'male' | 'female' | 'other' | 'none'>('none');
	let birthYear = $state<number | null>(null);
	let birthMonth = $state<number | null>(null);
	let birthDay = $state<number | null>(null);

	// UI 상태
	let loading = $state(false);
	let saving = $state(false);
	let successMessage = $state('');
	let errorMessage = $state('');

	// 년도 옵션 생성 (현재년도-70 ~ 현재년도-18)
	const currentYear = new Date().getFullYear();
	const minYear = currentYear - 70; // 70년 전
	const maxYear = currentYear - 18; // 18년 전 (미성년자 제외)
	const yearOptions = $derived(
		Array.from({ length: maxYear - minYear + 1 }, (_, i) => maxYear - i)
	);

	// 월 옵션 (1-12)
	const monthOptions = Array.from({ length: 12 }, (_, i) => i + 1);

	// 일 옵션 (1-31) - 실제로는 월에 따라 달라질 수 있지만 단순화
	const dayOptions = Array.from({ length: 31 }, (_, i) => i + 1);

	/**
	 * 사용자 프로필 데이터 로드
	 */
	async function loadProfile() {
		if (!authStore.user?.uid || !rtdb) return;

		loading = true;
		errorMessage = '';

		try {
			const userRef = ref(rtdb, `users/${authStore.user.uid}`);
			const snapshot = await get(userRef);

			if (snapshot.exists()) {
				const userData = snapshot.val();
				displayName = userData.displayName || '';
				gender = userData.gender || 'none';

				// dateOfBirth 파싱 (YYYY-MM-DD 형식)
				if (userData.dateOfBirth) {
					const parts = userData.dateOfBirth.split('-');
					if (parts.length === 3) {
						birthYear = parseInt(parts[0]);
						birthMonth = parseInt(parts[1]);
						birthDay = parseInt(parts[2]);
					}
				}
			} else {
				// 신규 사용자 - Firebase Auth에서 displayName 가져오기
				displayName = authStore.user.displayName || '';
			}
		} catch (error) {
			console.error('프로필 로드 실패:', error);
			errorMessage = '프로필 정보를 불러오는데 실패했습니다.';
		} finally {
			loading = false;
		}
	}

	/**
	 * 프로필 저장
	 */
	async function handleSave() {
		if (!authStore.user?.uid || !rtdb) {
			errorMessage = '로그인이 필요합니다.';
			return;
		}

		// 유효성 검증
		if (!displayName.trim()) {
			errorMessage = '닉네임을 입력해주세요.';
			return;
		}

		if (displayName.length > 50) {
			errorMessage = '닉네임은 50자 이하여야 합니다.';
			return;
		}

		saving = true;
		errorMessage = '';
		successMessage = '';

		try {
			const updateData: Record<string, string> = {
				displayName: displayName.trim(),
				gender
			};

			// 생년월일이 모두 선택된 경우에만 저장
			if (birthYear !== null && birthMonth !== null && birthDay !== null) {
				// YYYY-MM-DD 형식으로 변환
				const month = birthMonth.toString().padStart(2, '0');
				const day = birthDay.toString().padStart(2, '0');
				updateData.dateOfBirth = `${birthYear}-${month}-${day}`;

				// 미래 날짜 검증
				const birthDate = new Date(birthYear, birthMonth - 1, birthDay);
				if (birthDate > new Date()) {
					errorMessage = '생년월일은 과거여야 합니다.';
					saving = false;
					return;
				}
			}

			// Firebase RTDB에 저장
			const userRef = ref(rtdb, `users/${authStore.user.uid}`);
			await update(userRef, updateData);

			successMessage = '프로필이 성공적으로 업데이트되었습니다.';

			// 3초 후 성공 메시지 제거
			setTimeout(() => {
				successMessage = '';
			}, 3000);
		} catch (error) {
			console.error('프로필 저장 실패:', error);
			errorMessage = '프로필 저장에 실패했습니다. 다시 시도해주세요.';
		} finally {
			saving = false;
		}
	}

	/**
	 * 컴포넌트 마운트 시 프로필 로드
	 */
	$effect(() => {
		if (authStore.initialized) {
			if (!authStore.isAuthenticated) {
				// 비로그인 사용자는 로그인 페이지로 리다이렉트
				goto('/user/login');
			} else {
				loadProfile();
			}
		}
	});
</script>

<svelte:head>
	<title>내 프로필 - Sonub</title>
</svelte:head>

<div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center py-8">
	<div class="mx-auto w-full max-w-md space-y-6">
		<!-- 페이지 헤더 -->
		<div class="text-center">
			<h1 class="text-2xl font-bold text-gray-900">내 프로필</h1>
			<p class="mt-2 text-sm text-gray-600">회원 정보를 수정할 수 있습니다</p>
		</div>

		<!-- 로딩 상태 -->
		{#if loading}
			<Card.Root>
				<Card.Content class="pt-6">
					<p class="text-center text-gray-600">프로필 정보를 불러오는 중...</p>
				</Card.Content>
			</Card.Root>
		{:else}
			<!-- 프로필 수정 폼 -->
			<Card.Root>
				<Card.Header>
					<Card.Title>회원 정보</Card.Title>
					<Card.Description>닉네임, 성별, 생년월일을 설정하세요</Card.Description>
				</Card.Header>
				<Card.Content class="space-y-4">
					<!-- 성공 메시지 -->
					{#if successMessage}
						<Alert.Root>
							<Alert.Title>성공</Alert.Title>
							<Alert.Description>{successMessage}</Alert.Description>
						</Alert.Root>
					{/if}

					<!-- 에러 메시지 -->
					{#if errorMessage}
						<Alert.Root variant="destructive">
							<Alert.Title>오류</Alert.Title>
							<Alert.Description>{errorMessage}</Alert.Description>
						</Alert.Root>
					{/if}

					<!-- 닉네임 -->
					<div class="space-y-2">
						<label for="displayName" class="block text-sm font-medium text-gray-700">
							닉네임 <span class="text-red-500">*</span>
						</label>
						<input
							type="text"
							id="displayName"
							bind:value={displayName}
							placeholder="닉네임을 입력하세요"
							class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
							maxlength="50"
						/>
						<p class="text-xs text-gray-500">최대 50자</p>
					</div>

					<!-- 성별 -->
					<div class="space-y-2">
						<label for="gender" class="block text-sm font-medium text-gray-700">
							성별
						</label>
						<select
							id="gender"
							bind:value={gender}
							class="w-full cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
						>
							<option value="none">선택 안 함</option>
							<option value="male">남성</option>
							<option value="female">여성</option>
							<option value="other">기타</option>
						</select>
					</div>

					<!-- 생년월일 -->
					<div class="space-y-2">
						<label for="birthYear" class="block text-sm font-medium text-gray-700">
							생년월일
						</label>
						<div class="grid grid-cols-3 gap-2">
							<!-- 연도 -->
							<div>
								<select
									id="birthYear"
									bind:value={birthYear}
									class="w-full cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
								>
									<option value={null}>년도</option>
									{#each yearOptions as year}
										<option value={year}>{year}년</option>
									{/each}
								</select>
							</div>

							<!-- 월 -->
							<div>
								<select
									bind:value={birthMonth}
									class="w-full cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
								>
									<option value={null}>월</option>
									{#each monthOptions as month}
										<option value={month}>{month}월</option>
									{/each}
								</select>
							</div>

							<!-- 일 -->
							<div>
								<select
									bind:value={birthDay}
									class="w-full cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
								>
									<option value={null}>일</option>
									{#each dayOptions as day}
										<option value={day}>{day}일</option>
									{/each}
								</select>
							</div>
						</div>
						<p class="text-xs text-gray-500">
							만 18세 이상만 가입할 수 있습니다 ({minYear}년 ~ {maxYear}년생)
						</p>
					</div>

					<!-- 저장 버튼 -->
					<div class="pt-4">
						<Button
							class="w-full cursor-pointer"
							onclick={handleSave}
							disabled={saving}
						>
							{saving ? '저장 중...' : '저장'}
						</Button>
					</div>
				</Card.Content>
			</Card.Root>
		{/if}
	</div>
</div>
