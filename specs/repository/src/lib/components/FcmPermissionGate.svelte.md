---
name: FcmPermissionGate.svelte
description: FcmPermissionGate 컴포넌트
version: 1.0.0
type: svelte-component
category: component
original_path: src/lib/components/FcmPermissionGate.svelte
---

# FcmPermissionGate.svelte

## 개요

**파일 경로**: `src/lib/components/FcmPermissionGate.svelte`
**파일 타입**: svelte-component
**카테고리**: component

FcmPermissionGate 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * FCM 푸시 알림 권한 요청 가드 컴포넌트
	 *
	 * 사용자가 사이트에 익숙해진 후에만 권한 요청을 하여 브라우저의 자동 거절을 방지합니다.
	 *
	 * 동작 조건:
	 * - 페이지 이동 10회 이상 OR 채팅 목록 페이지 진입
	 * - Notification.permission === 'default' → 권한 요청 모달 표시
	 * - Notification.permission === 'denied' → 브라우저 설정 안내 모달 표시
	 *
	 * SessionStorage를 사용하여 브라우저 세션 동안만 카운트 유지
	 */

	import { browser } from '$app/environment';
	import { afterNavigate, goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { get } from 'svelte/store';
	import { requestFcmToken } from '$lib/fcm';
	import { toast } from 'svelte-sonner';

	// 페이지 이동 최소 횟수 (이 횟수 이상이면 권한 요청 가능)
	const CLICK_THRESHOLD = 10;
	const SESSION_KEY = 'pageMoveCount';
	const DISMISSED_KEY = 'fcmPermissionDismissed'; // 권한 요청 거절 플래그

	// 모달 상태 관리 (Svelte 5 runes)
	let showRequestDialog = $state(false); // 아직 permission 요청 전(default)일 때
	let showDeniedDialog = $state(false); // 이미 denied 일 때
	let isProcessing = $state(false); // 권한 요청 처리 중

	/**
	 * SessionStorage에서 페이지 이동 카운트 가져오기
	 */
	function getPageMoveCount(): number {
		if (!browser) return 0;
		const raw = sessionStorage.getItem(SESSION_KEY);
		return raw ? Number(raw) || 0 : 0;
	}

	/**
	 * SessionStorage에 페이지 이동 카운트 저장
	 */
	function setPageMoveCount(count: number) {
		if (!browser) return;
		sessionStorage.setItem(SESSION_KEY, String(count));
	}

	/**
	 * SessionStorage에서 권한 요청 거절 플래그 가져오기
	 */
	function getFcmPermissionDismissed(): boolean {
		if (!browser) return false;
		const raw = sessionStorage.getItem(DISMISSED_KEY);
		return raw === 'true';
	}

	/**
	 * SessionStorage에 권한 요청 거절 플래그 저장
	 */
	function setFcmPermissionDismissed(dismissed: boolean) {
		if (!browser) return;
		sessionStorage.setItem(DISMISSED_KEY, String(dismissed));
	}

	/**
	 * 페이지 이동 카운트 증가 (+1)
	 */
	function incrementPageMoveCount() {
		const current = getPageMoveCount();
		const next = current + 1;
		setPageMoveCount(next);
		checkPermissionLogic(next);
	}

	/**
	 * 권한 상태 + 조건을 확인하여 모달 표시 여부 결정
	 *
	 * @param currentCount 현재 페이지 이동 카운트 (선택적)
	 */
	function checkPermissionLogic(currentCount?: number) {
		if (!browser) return;
		if (typeof Notification === 'undefined') return; // 브라우저가 알림 미지원

		// 🔥 사용자가 이미 "나중에" 또는 설정 페이지를 방문한 경우 모달 표시 안 함
		if (getFcmPermissionDismissed()) return;

		const permission = Notification.permission;
		const count = currentCount ?? getPageMoveCount();
		const pathname = get(page).url.pathname;

		// 조건: 페이지 이동 10회 이상 OR 채팅 목록 페이지 진입
		const shouldTrigger = count >= CLICK_THRESHOLD;

		if (!shouldTrigger) return;

		// 이미 모달이 표시 중이면 중복 표시 방지
		if (showRequestDialog || showDeniedDialog) return;

		if (permission === 'default') {
			// 아직 권한 요청 전 → 권한 요청 모달
			showRequestDialog = true;
		} else if (permission === 'denied') {
			// 이미 거절 상태 → 안내 모달 + 설정 페이지 이동
			showDeniedDialog = true;
		}
		// 'granted' 인 경우는 아무 것도 안 함
	}

	/**
	 * "퍼미션 허용하기" 버튼 클릭 핸들러
	 * User gesture 안에서 Notification.requestPermission() 호출
	 */
	async function handleAllowClick() {
		console.log('[FCM Permission] 🔵🔵🔵 퍼미션 허용 버튼 클릭됨');
		console.log('[FCM Permission] 현재 권한 상태:', Notification.permission);

		if (typeof Notification === 'undefined') {
			console.error('[FCM Permission] ❌ Notification API 미지원');
			toast.error('이 브라우저는 알림을 지원하지 않습니다.');
			showRequestDialog = false;
			return;
		}

		// 이미 권한이 거부된 상태인지 확인
		if (Notification.permission === 'denied') {
			console.error('[FCM Permission] ❌ 권한이 이미 차단되어 있습니다');
			showRequestDialog = false;
			toast.error('알림 권한이 이미 차단되어 있습니다.');
			goto('/settings/fcm/permission');
			return;
		}

		isProcessing = true;
		console.log('[FCM Permission] 🔵 권한 요청 시작...');

		try {
			// 🔥 User gesture 안에서 권한 요청 (자동 거절 방지)
			console.log('[FCM Permission] 🔵 Notification.requestPermission() 호출 중...');
			const result = await Notification.requestPermission();
			console.log('[FCM Permission] ✅ requestPermission 결과:', result);

			if (result === 'granted') {
				console.log('[FCM Permission] ✅✅✅ 권한 허용됨!');
				showRequestDialog = false;
				toast.success('알림 권한이 허용되었습니다.');

				// FCM 토큰 발급 및 저장
				console.log('[FCM Permission] 🔵 FCM 토큰 발급 시작...');
				const token = await requestFcmToken();

				if (token) {
					console.log('[FCM Permission] ✅✅✅ FCM 토큰 발급 완료!');
					toast.success('푸시 알림이 활성화되었습니다!');
				} else {
					console.error('[FCM Permission] ❌ FCM 토큰 발급 실패');
					toast.error('FCM 토큰 발급에 실패했습니다. 콘솔을 확인해주세요.');
				}
			} else if (result === 'denied') {
				console.error('[FCM Permission] ❌ 권한 거부됨');
				showRequestDialog = false;
				toast.error('알림 권한이 거부되었습니다.');
				// 거절되면 안내 페이지로 이동
				goto('/settings/fcm/permission');
			} else {
				// 'default' 그대로인 경우 (사용자가 브라우저 팝업을 닫은 경우 등)
				console.log('[FCM Permission] ℹ️  권한 요청 취소됨 (default 상태 유지)');
				showRequestDialog = false;
			}
		} catch (error) {
			console.error('[FCM Permission] ❌❌❌ 권한 요청 중 에러 발생:', error);
			console.error('[FCM Permission] 에러 상세:', {
				name: (error as Error).name,
				message: (error as Error).message,
				stack: (error as Error).stack
			});
			toast.error('권한 요청 중 오류가 발생했습니다.');
			showRequestDialog = false;
		} finally {
			isProcessing = false;
			console.log('[FCM Permission] 🔵 권한 요청 처리 완료');
		}
	}

	/**
	 * "나중에" 버튼 클릭 핸들러
	 * SessionStorage에 플래그 저장 후 설정 페이지로 이동
	 */
	function handleLaterClick() {
		showRequestDialog = false;
		setFcmPermissionDismissed(true);
		toast.info('푸시 알림은 나중에 설정할 수 있습니다.');
		goto('/settings/fcm/permission');
	}

	/**
	 * "설정 페이지로 이동" 버튼 클릭 핸들러 (denied 상태)
	 */
	function goToSettingsFromDenied() {
		showDeniedDialog = false;
		goto('/settings/fcm/permission');
	}

	// 페이지 네비게이션 시점에 카운트 증가
	if (browser) {
		afterNavigate(() => {
			incrementPageMoveCount();
		});
	}
</script>

<!-- 퍼미션 요청 모달 (아직 권한 요청 전: default) -->
{#if showRequestDialog}
	<div class="permission-modal-overlay">
		<div class="permission-modal-content">
			<h2 class="modal-title">푸시 알림 권한이 필요합니다</h2>
			<p class="modal-description">
				원활한 서비스 이용을 위해 브라우저 푸시 알림 권한을 허용해 주세요. 채팅 알림, 새로운 메시지
				안내 등 주요 기능에 사용됩니다.
			</p>

			<div class="modal-buttons">
				<button
					type="button"
					class="btn-secondary"
					onclick={handleLaterClick}
					disabled={isProcessing}
				>
					나중에
				</button>
				<button
					type="button"
					class="btn-primary"
					onclick={handleAllowClick}
					disabled={isProcessing}
				>
					{isProcessing ? '처리 중...' : '퍼미션 허용하기'}
				</button>
			</div>
		</div>
	</div>
{/if}

<!-- 이미 denied 인 상태 안내 모달 -->
{#if showDeniedDialog}
	<div class="permission-modal-overlay">
		<div class="permission-modal-content">
			<h2 class="modal-title">알림 권한이 차단되어 있습니다</h2>
			<p class="modal-description">
				브라우저에서 이 사이트의 알림 권한을 이미 <strong>차단</strong>한 상태입니다. 푸시 알림을
				사용하려면 브라우저 설정에서 직접 권한을 다시 허용해야 합니다.
			</p>
			<p class="modal-description">
				다음 페이지에서 브라우저별로 푸시 권한을 다시 허용하는 방법을 안내해 드릴게요.
			</p>

			<div class="modal-buttons">
				<button type="button" class="btn-primary" onclick={goToSettingsFromDenied}>
					설정 페이지로 이동
				</button>
			</div>
		</div>
	</div>
{/if}

<style>
	@import 'tailwindcss' reference;

	/* 모달 오버레이 */
	.permission-modal-overlay {
		@apply fixed inset-0 z-50 flex items-center justify-center;
		background-color: rgba(0, 0, 0, 0.4);
	}

	/* 모달 콘텐츠 */
	.permission-modal-content {
		@apply w-full max-w-md rounded-2xl bg-white p-6 shadow-lg;
	}

	/* 모달 제목 */
	.modal-title {
		@apply mb-3 text-lg font-semibold text-gray-900;
	}

	/* 모달 설명 */
	.modal-description {
		@apply mb-4 text-sm text-gray-700;
	}

	/* 버튼 컨테이너 */
	.modal-buttons {
		@apply mt-4 flex justify-end gap-2;
	}

	/* Primary 버튼 */
	.btn-primary {
		@apply rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white;
		@apply transition-colors hover:bg-blue-700;
		@apply disabled:cursor-not-allowed disabled:opacity-50;
	}

	/* Secondary 버튼 */
	.btn-secondary {
		@apply rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700;
		@apply transition-colors hover:bg-gray-50;
		@apply disabled:cursor-not-allowed disabled:opacity-50;
	}
</style>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
