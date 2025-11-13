---
title: avatar.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 avatar.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 사용자 아바타 컴포넌트
	 *
	 * 사용자 프로필 사진을 표시하거나, 사진이 없을 경우 displayName의 첫 글자를 표시합니다.
	 * UserProfileStore를 사용하여 RTDB의 /users/{uid} 노드를 실시간으로 구독합니다.
	 *
	 * 주요 개선 사항:
	 * - onMount 제거, $effect로 uid 변경 감지
	 * - RTDB 직접 호출 제거, userProfileStore 사용
	 * - 중복 리스너 방지 (캐시 활용)
	 */

	import { userProfileStore } from '$lib/stores/user-profile.svelte';

	// Props
	interface Props {
		/**
		 * 사용자 UID (필수)
		 * UserProfileStore를 통해 photoUrl과 displayName을 자동으로 가져옵니다.
		 */
		uid?: string;

		/**
		 * 아바타 크기 (픽셀 단위)
		 * @default 40
		 */
		size?: number;

		/**
		 * 추가 CSS 클래스
		 */
		class?: string;
	}

	let {
		uid = undefined,
		size = 40,
		class: className = ''
	}: Props = $props();

	// 이미지 로드 실패 추적
	let imageLoadFailed = $state(false);

	// uid 변경 시 구독 시작 ($effect에서 상태 변경 가능)
	$effect(() => {
		userProfileStore.ensureSubscribed(uid);
	});

	// UserProfileStore에서 프로필 데이터 가져오기 (순수 읽기)
	// uid가 변경될 때마다 자동으로 업데이트됨 ($derived 사용)
	const profile = $derived(userProfileStore.getCachedProfile(uid));

	// 프로필에서 photoUrl과 displayName 추출
	const photoUrl = $derived(profile?.photoUrl ?? null);
	const displayName = $derived(profile?.displayName ?? null);

	// displayName의 첫 글자 계산
	const initial = $derived.by(() => {
		const name = displayName;
		if (!name || name.trim() === '') return 'U';
		return name.charAt(0).toUpperCase();
	});

	// 이미지를 표시할지 여부 결정
	const shouldShowImage = $derived(
		photoUrl &&
		photoUrl.trim() !== '' &&
		!imageLoadFailed
	);

	// uid 변경 시 이미지 로드 실패 상태 초기화
	$effect(() => {
		if (uid) {
			imageLoadFailed = false;
		}
	});

	// 프로필 데이터 변경 추적 (디버깅용)
	$effect(() => {
		console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
		console.log('[Avatar 컴포넌트] 프로필 상태 변경');
		console.log('  uid:', uid);
		console.log('  profile:', profile);
		console.log('  photoUrl:', photoUrl);
		console.log('  displayName:', displayName);
		console.log('  shouldShowImage:', shouldShowImage);
		console.log('  initial:', initial);
		console.log('  imageLoadFailed:', imageLoadFailed);
		console.log('  size:', size);
		console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
	});

	/**
	 * 이미지 로드 에러 핸들러
	 */
	function handleImageError(e: Event) {
		console.error('[Avatar] ❌ 이미지 로드 실패:', photoUrl);
		imageLoadFailed = true;
	}
</script>

<!-- 아바타 컨테이너 -->
<div
	class="inline-flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white font-semibold shadow-sm overflow-hidden {className}"
	style="width: {size}px; height: {size}px;"
	role="img"
	aria-label={displayName || '사용자 아바타'}
>
	{#if shouldShowImage}
		<!-- 프로필 사진 표시 -->
		<img
			src={photoUrl || ''}
			alt={displayName || '사용자'}
			class="h-full w-full object-cover"
			referrerpolicy="no-referrer"
			crossorigin="anonymous"
			onerror={handleImageError}
		/>
	{:else}
		<!-- 프로필 사진이 없거나 로드 실패: 첫 글자 표시 -->
		<span class="text-lg" style="font-size: {size * 0.45}px;">
			{initial}
		</span>
	{/if}
</div>

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
