<script lang="ts">
	/**
	 * 사용자 아바타 컴포넌트
	 *
	 * 사용자 프로필 사진을 표시하거나, 사진이 없을 경우 displayName의 첫 글자를 표시합니다.
	 * RTDB의 /users/{uid}/photoUrl 값을 사용합니다.
	 */

	import { onMount } from 'svelte';
	import { rtdb } from '$lib/firebase';
	import { ref, onValue, type Unsubscribe } from 'firebase/database';

	// Props
	interface Props {
		/**
		 * 사용자 UID (필수)
		 * RTDB에서 photoUrl과 displayName을 자동으로 가져옵니다.
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

	// RTDB에서 가져온 데이터
	let photoUrl = $state<string | null>(null);
	let displayName = $state<string | null>(null);

	// 이미지 로드 실패 추적
	let imageLoadFailed = $state(false);

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

	// 디버깅 로그
	$effect(() => {
		console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
		console.log('[Avatar 상태]');
		console.log('  uid:', uid);
		console.log('  photoUrl:', photoUrl);
		console.log('  displayName:', displayName);
		console.log('  imageLoadFailed:', imageLoadFailed);
		console.log('  shouldShowImage:', shouldShowImage);
		console.log('  initial:', initial);
		console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
	});

	// RTDB에서 데이터 가져오기
	let unsubscribe: Unsubscribe | null = null;

	onMount(() => {
		console.log('[Avatar onMount] 시작, uid:', uid);

		if (!uid) {
			console.warn('[Avatar] uid가 제공되지 않았습니다.');
			return;
		}

		if (!rtdb) {
			console.error('[Avatar] Firebase RTDB가 초기화되지 않았습니다.');
			return;
		}

		const userRef = ref(rtdb, `users/${uid}`);
		console.log('[Avatar] RTDB 리스너 등록 시작:', `users/${uid}`);

		unsubscribe = onValue(
			userRef,
			(snapshot) => {
				const data = snapshot.val();
				console.log('[Avatar] ✅ RTDB 데이터 수신:', data);

				if (data) {
					// 이미지 로드 실패 상태 초기화
					imageLoadFailed = false;

					// 데이터 설정
					photoUrl = data.photoUrl || null;
					displayName = data.displayName || null;

					console.log('[Avatar] 데이터 설정 완료');
					console.log('  - photoUrl:', photoUrl);
					console.log('  - displayName:', displayName);
				} else {
					console.warn('[Avatar] ⚠️ RTDB에 사용자 데이터가 없습니다.');
					photoUrl = null;
					displayName = null;
				}
			},
			(error) => {
				console.error('[Avatar] ❌ RTDB 데이터 로드 실패:', error);
			}
		);

		// 정리 함수
		return () => {
			console.log('[Avatar] 리스너 해제, uid:', uid);
			if (unsubscribe) {
				unsubscribe();
			}
		};
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
