<script lang="ts">
	/**
	 * 루트 레이아웃 컴포넌트
	 *
	 * 전체 애플리케이션의 레이아웃을 정의합니다.
	 * 3컬럼 구조: 좌측 사이드바, 메인 콘텐츠, 우측 사이드바
	 *
	 * Paraglide i18n:
	 * - hooks.server.ts의 paraglideMiddleware가 서버에서 로케일 자동 감지/설정
	 * - 클라이언트는 쿠키 기반으로 자동 감지되어 설정됨
	 *
	 * v1.0.0 - 새 메시지 알림음 시스템:
	 * - /users/{uid}/newMessageCount 실시간 구독
	 * - 값이 증가할 때만 알림음 재생
	 * - 채팅방 페이지에서는 알림음 재생 안 함
	 * - BroadcastChannel로 다중 탭 중복 재생 방지
	 */

	import '../app.css';
	import TopBar from '$lib/components/top-bar.svelte';
	import LeftSidebar from '$lib/components/left-sidebar.svelte';
	import RightSidebar from '$lib/components/right-sidebar.svelte';
	import DevIcon from '$lib/components/dev/dev-icon.svelte';
	import FcmPermissionGate from '$lib/components/FcmPermissionGate.svelte';
	import { dev, browser } from '$app/environment';
	import { Toaster, toast } from 'svelte-sonner';
	import { onMount } from 'svelte';
	import { registerServiceWorker, subscribeOnMessage } from '$lib/fcm';
	import { page } from '$app/stores';
	import { authStore } from '$lib/stores/auth.svelte';
	import { firestoreStore } from '$lib/stores/firestore.svelte';

	let { children } = $props();

	/**
	 * 현재 페이지가 채팅방인지 확인
	 * 채팅방 페이지에서는 모바일에서 TopBar를 숨김
	 */
	const currentRouteId = $derived($page.route.id ?? '');
	const currentPathname = $derived($page.url.pathname);
	const isChatRoom = $derived(
		(currentRouteId ? currentRouteId.startsWith('/chat/room') : false) ||
			currentPathname.startsWith('/chat/room')
	);

	// ========================================
	// v1.0.0: 새 메시지 알림음 시스템
	// ========================================

	/**
	 * v1.0.0: 사용자 문서 타입 (newMessageCount 필드 포함)
	 */
	interface UserData {
		newMessageCount?: number;
	}

	/**
	 * v1.0.0: 새 메시지 카운트 실시간 구독
	 */
	let newMessageCountStore = $state<ReturnType<typeof firestoreStore<UserData>> | null>(null);

	/**
	 * v1.0.0: 알림음 객체 (클라이언트 사이드에서만 초기화)
	 */
	let notificationAudio: HTMLAudioElement | null = null;

	/**
	 * v1.0.0: BroadcastChannel (다중 탭 중복 방지)
	 */
	let broadcastChannel: BroadcastChannel | null = null;

	/**
	 * v1.0.0: 마지막 알림음 재생 시각 (디바운스)
	 */
	let lastSoundPlayedAt = 0;

	/**
	 * v1.0.0: 이전 newMessageCount 값 (증가 감지용)
	 */
	let previousCount = $state(0);

	/**
	 * v1.0.0: 로그인 상태에 따라 사용자 문서 구독 (newMessageCount 필드 포함)
	 */
	$effect(() => {
		if (browser && authStore.isAuthenticated && authStore.user?.uid) {
			const path = `users/${authStore.user.uid}`;
			newMessageCountStore = firestoreStore<UserData>(path);
			// console.log(`🔔 [알림음] 사용자 문서 구독 시작 (newMessageCount 필드 포함): ${path}`);
		} else {
			newMessageCountStore = null;
			previousCount = 0;
			// console.log('🔔 [알림음] 사용자 문서 구독 해제');
		}
	});

	/**
	 * v1.0.0: newMessageCount 증가 감지 및 알림음 재생
	 * Svelte store를 구독하여 reactive 처리
	 */
	$effect(() => {
		if (!browser || !newMessageCountStore) {
			previousCount = 0;
			return;
		}

		// Svelte store를 구독
		const unsubscribe = newMessageCountStore.subscribe((state) => {
			const count = state.data?.newMessageCount ?? 0;

			// 증가 감지
			if (count > previousCount && previousCount >= 0) {
				// console.log(`🔔 [알림음] newMessageCount 증가 감지: ${previousCount} → ${count}`);

				// 채팅방에 있으면 알림음 재생 안 함
				if (isChatRoom) {
					// console.log('🔇 [알림음] 채팅방 페이지에 있으므로 재생 안 함');
					previousCount = count;
					return;
				}

				// 디바운스 체크 (최소 500ms 간격)
				const now = Date.now();
				if (now - lastSoundPlayedAt < 500) {
					// console.log('🔇 [알림음] 디바운스 - 너무 빠른 재생 요청');
					previousCount = count;
					return;
				}

				// 다른 탭에서 재생 중인지 확인
				if (broadcastChannel) {
					// 다른 탭에 알림음 재생 시작을 알림
					broadcastChannel.postMessage({ type: 'notification-sound-playing', timestamp: now });
				}

				// 알림음 재생
				playNotificationSound();
				lastSoundPlayedAt = now;
			}

			// 이전 값 업데이트
			previousCount = count;
		});

		return () => unsubscribe();
	});

	/**
	 * v1.0.0: 알림음 재생 함수
	 */
	function playNotificationSound() {
		try {
			if (!notificationAudio) {
				console.warn('🔇 [알림음] Audio 객체가 초기화되지 않음');
				return;
			}

			// 이미 재생 중이면 처음부터 다시 재생
			notificationAudio.currentTime = 0;
			notificationAudio
				.play()
				.then(() => {
					// console.log('🔊 [알림음] 재생 성공');
				})
				.catch((error) => {
					console.warn('🔇 [알림음] 재생 실패 (사용자 인터랙션 필요):', error);
				});
		} catch (error) {
			console.error('🔇 [알림음] 재생 에러:', error);
		}
	}

	/**
	 * 앱 시작 시 초기화
	 * 1. 서비스 워커 미리 등록 (FCM 토큰 발급을 위해 필요)
	 * 2. 포그라운드 메시지 리스너 등록 (Toast 알림 표시)
	 * 3. v1.0.0: 알림음 Audio 객체 초기화
	 * 4. v1.0.0: BroadcastChannel 초기화 (다중 탭 중복 방지)
	 */
	onMount(() => {
		// 비동기 초기화 작업 (즉시 실행)
		(async () => {
			// 서비스 워커 등록
			await registerServiceWorker();

			// 포그라운드 메시지 수신 리스너 등록
			subscribeOnMessage((payload) => {
				// console.log('[Layout] 포그라운드 메시지 수신:', payload);

				// Toast 알림 표시
				const title = payload.notification?.title ?? payload.data?.title ?? '새 알림';
				const body = payload.notification?.body ?? payload.data?.body ?? '';

				toast.success(title, {
					description: body,
					duration: 5000 // 5초 동안 표시
				});
			});
		})();

		// v1.0.0: 알림음 Audio 객체 초기화
		try {
			notificationAudio = new Audio('/sound/new-message.mp3');
			notificationAudio.volume = 0.7; // 볼륨 70%
			// console.log('🔊 [알림음] Audio 객체 초기화 완료');
		} catch (error) {
			console.error('🔇 [알림음] Audio 객체 초기화 실패:', error);
		}

		// v1.0.0: BroadcastChannel 초기화 (다중 탭 중복 방지)
		try {
			if ('BroadcastChannel' in window) {
				broadcastChannel = new BroadcastChannel('sonub-notifications');

				broadcastChannel.onmessage = (event) => {
					if (event.data?.type === 'notification-sound-playing') {
						// 다른 탭에서 알림음을 재생 중이면 이 탭에서는 재생 안 함
						const timestamp = event.data.timestamp;
						if (Date.now() - timestamp < 500) {
							// console.log('🔇 [알림음] 다른 탭에서 재생 중 - 이 탭에서는 스킵');
							lastSoundPlayedAt = timestamp;
						}
					}
				};

				// console.log('📡 [알림음] BroadcastChannel 초기화 완료');
			}
		} catch (error) {
			console.warn('📡 [알림음] BroadcastChannel 초기화 실패 (브라우저 미지원):', error);
		}

		// Cleanup on unmount (동기 함수로 반환)
		return () => {
			if (broadcastChannel) {
				broadcastChannel.close();
				// console.log('📡 [알림음] BroadcastChannel 종료');
			}
		};
	});
</script>

<svelte:head>
	<!-- Favicon 설정 -->
	<link rel="icon" type="image/png" sizes="64x64" href="/favicon-64.png" />
	<link rel="icon" type="image/png" sizes="512x512" href="/favicon-512.png" />
	<link rel="apple-touch-icon" sizes="512x512" href="/favicon-512.png" />
	<!-- 기본 favicon (브라우저 호환성) -->
	<link rel="icon" type="image/png" href="/favicon-64.png" />
</svelte:head>

<div class="min-h-screen bg-gray-50">
	<!-- TopBar: 채팅방 페이지에서는 모바일에서 숨김 -->
	<div class:hidden={isChatRoom} class="md:block">
		<TopBar />
	</div>

	<div class="pt-20 md:pt-16" class:pt-0={isChatRoom} class:md:pt-0={isChatRoom}>
		<div class="container mx-auto px-4 py-8" class:p-0={isChatRoom}>
			<div class="flex gap-6" class:gap-0={isChatRoom}>
				<!-- 좌측 사이드바 (데스크톱만, 채팅방에서는 숨김) -->
				{#if !isChatRoom}
					<LeftSidebar />
				{/if}

				<!-- 메인 콘텐츠 -->
				<main class="min-w-0 flex-1">
					{@render children()}
				</main>

				<!-- 우측 사이드바 (데스크톱만, 채팅방에서는 숨김) -->
				{#if !isChatRoom}
					<RightSidebar />
				{/if}
			</div>
		</div>
	</div>
</div>

<!-- 개발 모드에서만 표시되는 개발자 도구 -->
{#if dev}
	<DevIcon />
{/if}

<!-- 전역 Toast 알림 컴포넌트 -->
<Toaster position="top-center" richColors />

<!-- FCM 푸시 알림 권한 요청 가드 -->
<FcmPermissionGate />
