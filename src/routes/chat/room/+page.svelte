<script lang="ts">
	/**
	 * 채팅방 페이지
	 *
	 * GET 파라미터로 전달된 uid 값이 있으면 1:1 채팅방으로 동작합니다.
	 * 채팅 상대의 프로필을 실시간으로 구독하고 메시지 목록 및 입력창을 제공합니다.
	 */

	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import DatabaseListView from '$lib/components/DatabaseListView.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { userProfileStore } from '$lib/stores/user-profile.svelte';
	import { pushData } from '$lib/stores/database.svelte';
	import { m } from '$lib/paraglide/messages';
	import {
		buildSingleRoomId,
		enterSingleChatRoom,
		joinChatRoom,
		leaveChatRoom,
		togglePinChatRoom,
		inviteUserToChatRoom
	} from '$lib/functions/chat.functions';
	import { formatLongDate } from '$lib/functions/date.functions';
	import { tick } from 'svelte';
	import { rtdb } from '$lib/firebase';
	import { ref, update, onValue, set, remove, get } from 'firebase/database';
	import * as DropdownMenu from '$lib/components/ui/dropdown-menu';
	import { Button } from '$lib/components/ui/button';
	import ChatFavoritesDialog from '$lib/components/chat/ChatFavoritesDialog.svelte';
	import UserSearchDialog from '$lib/components/user/UserSearchDialog.svelte';

	// GET 파라미터 추출
	const uidParam = $derived.by(() => $page.url.searchParams.get('uid') ?? '');
	const roomIdParam = $derived.by(() => $page.url.searchParams.get('roomId') ?? '');

	// 1:1 채팅 여부
	const isSingleChat = $derived.by(() => Boolean(uidParam));

	const activeRoomId = $derived.by(() => {
		if (roomIdParam) return roomIdParam;
		if (isSingleChat && authStore.user?.uid && uidParam) {
			return buildSingleRoomId(authStore.user.uid, uidParam);
		}
		return '';
	});

	// DatabaseListView 설정 (Flat 구조 기준)
	const messagePath = 'chat-messages';
	const roomOrderField = 'roomOrder';
	const roomOrderPrefix = $derived.by(() => (activeRoomId ? `-${activeRoomId}-` : ''));
	const canRenderMessages = $derived.by(() => Boolean(activeRoomId && roomOrderPrefix));

	// 채팅 상대 프로필 구독
	$effect(() => {
		if (uidParam) {
			userProfileStore.ensureSubscribed(uidParam);
		}
	});

	// 채팅방 입장 처리
	// 1:1 채팅과 그룹/오픈 채팅은 서로 다른 방식으로 처리합니다.
	$effect(() => {
		if (activeRoomId && authStore.user?.uid && rtdb) {
			if (isSingleChat) {
				// 1:1 채팅: chat-joins 노드에 최소 정보만 업데이트
				// Cloud Functions(onChatJoinCreate)가 자동으로 필요한 필드들을 추가합니다.
				enterSingleChatRoom(rtdb, activeRoomId, authStore.user.uid);
			} else {
				// 그룹/오픈 채팅: members 필드만 설정
				// Cloud Functions가 자동으로 memberCount를 업데이트하고 chat-joins에 상세 정보를 추가합니다.
				joinChatRoom(rtdb, activeRoomId, authStore.user.uid);
			}
		}
	});

	const targetProfile = $derived(userProfileStore.getCachedProfile(uidParam));
	const targetProfileLoading = $derived(userProfileStore.isLoading(uidParam));
	const targetProfileError = $derived(userProfileStore.getError(uidParam));

	// 채팅 상대 표시 이름
	const targetDisplayName = $derived.by(() => {
		if (targetProfile?.displayName) return targetProfile.displayName;
		if (uidParam) return `@${uidParam.slice(0, 6)}`;
		return m.chatPartner();
	});

	// 작성 중인 메시지
	let composerText = $state('');
	let isSending = $state(false);
	let sendError = $state<string | null>(null);

	// ChatFavoritesDialog 상태
	let favoritesDialogOpen = $state(false);

	// UserSearchDialog 상태 (친구 초대용)
	let inviteDialogOpen = $state(false);

	// 핀 상태 관리
	let isPinned = $state(false);
	let currentRoomType = $derived.by(() => {
		if (isSingleChat) return 'single';
		// 그룹/오픈 채팅 구분은 roomId로는 불가능하므로 기본값 사용
		// TODO: 채팅방 정보에서 타입 가져오기
		return 'group';
	});

	// 채팅방 핀 상태 구독
	$effect(() => {
		if (!activeRoomId || !authStore.user?.uid || !rtdb) {
			isPinned = false;
			return;
		}

		const pinRef = ref(rtdb, `chat-joins/${authStore.user.uid}/${activeRoomId}/pin`);
		const unsubscribe = onValue(pinRef, (snapshot) => {
			if (!snapshot.exists()) {
				isPinned = false;
				return;
			}

			const pinValue = snapshot.val();
			if (pinValue === true) {
				isPinned = true;
			} else {
				isPinned = false;
			}
		});

		return () => {
			unsubscribe();
		};
	});

	// 채팅방 알림 구독 상태 관리
	let isNotificationSubscribed = $state(true); // 기본값: 구독 중
	let subscriptionLoading = $state(false);

	/**
	 * 채팅방 알림 구독 상태 로드
	 *
	 * 1:1 채팅방: /chat-joins/{uid}/{roomId}/fcm-subscription 확인
	 * - 필드 없음 → 구독 중 (true)
	 * - false → 구독 해제
	 *
	 * 그룹/오픈 채팅방: /chat-rooms/{roomId}/members/{uid} 확인
	 * - true → 구독 중
	 * - false → 구독 해제
	 * - 필드 없음 → 구독 중 (기본값)
	 */
	$effect(() => {
		if (!activeRoomId || !authStore.user?.uid || !rtdb) {
			isNotificationSubscribed = true; // 기본값
			return;
		}

		let unsubscribe: (() => void) | undefined;

		if (isSingleChat) {
			// 1:1 채팅방: fcm-subscription 필드 구독
			const subscriptionRef = ref(
				rtdb,
				`chat-joins/${authStore.user.uid}/${activeRoomId}/fcm-subscription`
			);

			unsubscribe = onValue(subscriptionRef, (snapshot) => {
				if (!snapshot.exists()) {
					isNotificationSubscribed = true; // 기본값: 구독 중
					return;
				}

				const value = snapshot.val();
				isNotificationSubscribed = value !== false;
			});
		} else {
			// 그룹/오픈 채팅방: members 필드 구독
			const memberRef = ref(rtdb, `chat-rooms/${activeRoomId}/members/${authStore.user.uid}`);

			unsubscribe = onValue(memberRef, (snapshot) => {
				if (!snapshot.exists()) {
					isNotificationSubscribed = true; // 기본값: 구독 중
					return;
				}

				const value = snapshot.val();
				isNotificationSubscribed = value === true;
			});
		}

		return () => {
			if (unsubscribe) {
				unsubscribe();
			}
		};
	});

	// 채팅 입력 창(input) 직접 참조
	let composerInputRef: HTMLInputElement | null = $state(null);

	// 메시지 전송 처리
	async function handleSendMessage(event: SubmitEvent) {
		event.preventDefault();
		if (isSending) return;
		if (!composerText.trim()) return;
		if (!authStore.user?.uid) {
			sendError = m.chatSignInToSend();
			return;
		}
		if (!activeRoomId) {
			sendError = m.chatRoomNotReady();
			return;
		}

		isSending = true;
		sendError = null;

		const trimmed = composerText.trim();
		const timestamp = Date.now();

		const payload = {
			roomId: activeRoomId,
			type: 'message',
			text: trimmed,
			urls: [],
			senderUid: authStore.user.uid,
			createdAt: timestamp,
			editedAt: null,
			deletedAt: null,
			roomOrder: `-${activeRoomId}-${timestamp}`,
			rootOrder: `-${activeRoomId}-${timestamp}`
		};

		const result = await pushData(messagePath, payload);

		if (!result.success) {
			sendError = result.error ?? m.chatSendFailed();
			isSending = false;
		} else {
			// 메시지 전송 성공 시
			composerText = '';
			sendError = null;
			isSending = false;

			// DOM 업데이트 완료 후 포커스 추가
			await tick();

			// 브라우저 렌더링 완료를 확실히 기다린 후 포커스
			requestAnimationFrame(() => {
				if (composerInputRef) {
					composerInputRef.focus();
					console.log('✅ 채팅 입력 창에 포커스 추가됨');
				}
			});
		}
	}

	// 메시지 작성 가능 여부
	const composerDisabled = $derived.by(() => !authStore.isAuthenticated || !activeRoomId);

	// DatabaseListView 컴포넌트 참조 (스크롤 제어용)
	let databaseListView: any = $state(null);

	// 발신자 라벨
	function resolveSenderLabel(senderUid?: string | null) {
		if (!senderUid) return m.chatUnknownUser();
		if (senderUid === authStore.user?.uid) return m.chatYou();
		if (senderUid === uidParam && targetDisplayName) return targetDisplayName;
		return senderUid.slice(0, 10);
	}

	// 스크롤을 맨 위로 이동
	function handleScrollToTop() {
		databaseListView?.scrollToTop();
	}

	// 스크롤을 맨 아래로 이동
	function handleScrollToBottom() {
		databaseListView?.scrollToBottom();
	}

	// 뒤로가기 (채팅 목록으로)
	function handleGoBack() {
		void goto('/chat/list');
	}

	// 즐겨찾기 추가/제거
	// 즐겨찾기 다이얼로그를 열어서 현재 채팅방이 포함된 폴더를 강조 표시합니다.
	function handleBookmark() {
		favoritesDialogOpen = true;
	}

	// URL 복사
	async function handleCopyUrl() {
		try {
			const url = window.location.href;
			await navigator.clipboard.writeText(url);
			console.log('URL 복사됨:', url);
			// TODO: 토스트 메시지로 알림
		} catch (error) {
			console.error('URL 복사 실패:', error);
		}
	}

	// 멤버 목록
	function handleMemberList() {
		console.log('멤버 목록 클릭');
		// TODO: 멤버 목록 다이얼로그 표시
	}

	// 즐겨찾기에서 채팅방 선택 핸들러
	// 선택된 채팅방으로 이동합니다.
	function handleRoomSelected(event: CustomEvent<{ roomId: string }>) {
		const { roomId } = event.detail;
		void goto(`/chat/room?roomId=${roomId}`);
	}

	// 방 탈퇴하기
	async function handleLeaveRoom() {
		if (!activeRoomId || !authStore.user?.uid || !rtdb) return;

		const confirmed = confirm('채팅방에서 나가시겠습니까?');
		if (!confirmed) return;

		try {
			await leaveChatRoom(rtdb, activeRoomId, authStore.user.uid);
			console.log('채팅방 탈퇴 완료');
			void goto('/chat/list');
		} catch (error) {
			console.error('채팅방 탈퇴 실패:', error);
		}
	}

	// 신고하고 탈퇴하기
	function handleReportAndLeave() {
		console.log('신고하고 탈퇴하기 클릭');
		// TODO: 신고 다이얼로그 표시 후 탈퇴
	}

	/**
	 * 친구 초대 메뉴 클릭 핸들러
	 * UserSearchDialog를 열어서 초대할 친구를 검색합니다.
	 */
	function handleInviteFriend() {
		inviteDialogOpen = true;
	}

	/**
	 * 사용자 선택 핸들러 (초대 실행)
	 * UserSearchDialog에서 사용자를 선택하면 채팅방에 초대합니다.
	 */
	async function handleUserSelect(event: CustomEvent<{ user: any; uid: string }>) {
		const { uid } = event.detail;

		if (!activeRoomId || !authStore.user?.uid || !rtdb) {
			console.error('채팅방 또는 사용자 정보 없음');
			return;
		}

		try {
			await inviteUserToChatRoom(rtdb, activeRoomId, uid, authStore.user.uid);
			console.log('✅ 초대 성공:', uid);
			alert(m.chatInvitationSent());
		} catch (error) {
			console.error('❌ 초대 실패:', error);
			alert('초대를 보내지 못했습니다.');
		}
	}

	/**
	 * 채팅방 핀 토글 핸들러
	 * 채팅방을 핀하거나 핀 해제합니다
	 */
	async function handleTogglePin() {
		if (!activeRoomId || !authStore.user?.uid || !rtdb) {
			console.error('채팅방 또는 사용자 정보 없음');
			return;
		}

		try {
			const newPinState = await togglePinChatRoom(
				rtdb,
				activeRoomId,
				authStore.user.uid,
				currentRoomType
			);
			console.log(`✅ 채팅방 핀 ${newPinState ? '설정' : '해제'} 완료:`, activeRoomId);
		} catch (error) {
			console.error('채팅방 핀 토글 실패:', error);
			alert('핀 기능을 사용할 수 없습니다. 채팅방에 참여한 후 시도해주세요.');
		}
	}

	/**
	 * 채팅방 알림 구독 토글 핸들러
	 *
	 * 1:1 채팅방:
	 * - 구독 → 구독 해제: fcm-subscription: false 저장
	 * - 구독 해제 → 구독: fcm-subscription 필드 삭제
	 *
	 * 그룹/오픈 채팅방:
	 * - 구독 → 구독 해제: members/{uid}: false 저장
	 * - 구독 해제 → 구독: members/{uid}: true 저장
	 */
	async function handleToggleNotificationSubscription() {
		if (!activeRoomId || !authStore.user?.uid || !rtdb || subscriptionLoading) {
			console.error('채팅방 또는 사용자 정보 없음');
			return;
		}

		subscriptionLoading = true;
		const newStatus = !isNotificationSubscribed;

		try {
			if (isSingleChat) {
				// 1:1 채팅방
				const subscriptionRef = ref(
					rtdb,
					`chat-joins/${authStore.user.uid}/${activeRoomId}/fcm-subscription`
				);

				if (newStatus) {
					// 구독: 필드 삭제
					await remove(subscriptionRef);
					console.log(`📢 1:1 채팅방 알림 구독 완료: ${activeRoomId}`);
				} else {
					// 구독 해제: false 저장
					await set(subscriptionRef, false);
					console.log(`🔕 1:1 채팅방 알림 구독 해제: ${activeRoomId}`);
				}
			} else {
				// 그룹/오픈 채팅방
				const memberRef = ref(rtdb, `chat-rooms/${activeRoomId}/members/${authStore.user.uid}`);
				await set(memberRef, newStatus);
				console.log(
					`${newStatus ? '📢' : '🔕'} 그룹 채팅방 알림 ${newStatus ? '구독' : '구독 해제'}: ${activeRoomId}`
				);
			}

			// 로컬 상태 업데이트 (onValue 리스너가 자동으로 업데이트하지만 즉각적인 UI 반영을 위해)
			isNotificationSubscribed = newStatus;
		} catch (error) {
			console.error('알림 구독 상태 변경 실패:', error);
			alert('알림 설정을 변경할 수 없습니다. 잠시 후 다시 시도해주세요.');
		} finally {
			subscriptionLoading = false;
		}
	}

	/**
	 * 현재 채팅방의 읽지 않은 메시지 수를 0으로 초기화합니다.
	 *
	 * 사용자가 채팅방에 입장해 있는 상태에서 새 메시지를 읽었음을 표시하기 위해
	 * Firebase RTDB의 `/chat-joins/{uid}/{roomId}/newMessageCount`를 0으로 업데이트합니다.
	 *
	 * **타이밍 이슈 해결:**
	 * 새 메시지가 생성되면 다음과 같은 순서로 처리됩니다:
	 * 1. Firebase RTDB에 새 메시지 노드 생성
	 * 2. Cloud Functions의 onChatMessageCreate 트리거 실행 → newMessageCount +1 증가
	 * 3. 클라이언트의 DatabaseListView가 새 메시지 감지 → handleNewMessage 콜백 호출
	 *
	 * 문제: 클라이언트가 즉시 newMessageCount를 0으로 설정하면,
	 * Cloud Functions가 아직 실행 중이거나 완료되지 않아 값이 다시 1로 증가할 수 있습니다.
	 * 결과적으로 채팅 목록에 읽지 않은 메시지 배지(1)가 남아있게 됩니다.
	 *
	 * 해결책: 0.79초(790ms) 지연 후 newMessageCount를 0으로 설정합니다.
	 * 이렇게 하면 Cloud Functions가 먼저 +1 증가를 완료한 후,
	 * 클라이언트가 0으로 초기화하여 배지가 정확히 사라집니다.
	 *
	 * @returns {boolean} 업데이트 시도 여부 (true: 업데이트 시도함, false: 조건 미충족으로 건너뜀)
	 */
	function markCurrentRoomAsRead(): boolean {
		// 채팅방 활성화 상태 및 사용자 인증 확인
		if (!activeRoomId || !authStore.user?.uid || !rtdb) {
			console.log('채팅방 또는 사용자 정보 없음 - newMessageCount 업데이트 건너뜀');
			return false;
		}

		// Cloud Functions 실행 완료를 기다린 후 newMessageCount를 0으로 업데이트
		// 790ms 지연을 두어 Cloud Functions의 +1 증가가 먼저 완료되도록 보장
		setTimeout(() => {
			// 다시 한번 유효성 검사 (타이머 실행 중 사용자가 로그아웃하거나 방을 나갈 수 있음)
			if (!activeRoomId || !authStore.user?.uid || !rtdb) {
				console.log('타이머 실행 중 상태 변경 - newMessageCount 업데이트 취소');
				return;
			}

			const chatJoinRef = ref(rtdb, `chat-joins/${authStore.user.uid}/${activeRoomId}`);
			update(chatJoinRef, {
				newMessageCount: 0
			})
				.then(() => {
					console.log('newMessageCount 0으로 업데이트 완료 (채팅방에서 새 메시지 읽음 처리)');
				})
				.catch((error) => {
					console.error('newMessageCount 업데이트 실패:', error);
				});
		}, 790); // 0.79초 지연

		return true;
	}

	/**
	 * DatabaseListView에서 새 메시지 추가 시 호출되는 콜백
	 *
	 * 사용자가 채팅방에 입장해 있는 상태에서 새로운 메시지가 도착하면
	 * 즉시 읽음 처리를 위해 newMessageCount를 0으로 업데이트합니다.
	 *
	 * @param item - 새로 추가된 메시지 아이템 ({ key: string, data: any })
	 */
	function handleNewMessage(item: { key: string; data: any }) {
		console.log('새 메시지 추가됨:', item);

		// 현재 채팅방을 읽음 상태로 표시
		markCurrentRoomAsRead();

		// TODO: 필요한 추가 작업 수행
		// 예: 사운드 재생, 알림 표시, 배지 업데이트 등
	}
</script>

<svelte:head>
	<title>{m.pageTitleChat()}</title>
</svelte:head>

<div class="mx-auto flex max-w-[960px] flex-col gap-6 px-4 py-8 pb-16">
	<!-- 채팅방 상단 헤더 -->
	<header class="chat-room-header">
		<!-- 뒤로가기 버튼 -->
		<Button variant="ghost" size="icon" onclick={handleGoBack} class="shrink-0">
			<span class="text-xl">←</span>
		</Button>

		<!-- 채팅방 제목/프로필 -->
		<div class="flex flex-1 items-center gap-3 overflow-hidden">
			{#if isSingleChat && uidParam}
				<!-- 1:1 채팅: 프로필 사진 + 이름 -->
				<Avatar uid={uidParam} size={40} class="shrink-0 shadow-sm" />
				<div class="flex-1 overflow-hidden">
					<h1 class="truncate text-lg font-semibold text-gray-900">{targetDisplayName}</h1>
					{#if targetProfileLoading}
						<p class="text-xs text-gray-500">로딩 중...</p>
					{:else if targetProfileError}
						<p class="text-xs text-red-500">프로필 로드 실패</p>
					{/if}
				</div>
			{:else if roomIdParam}
				<!-- 그룹/오픈 채팅: 방 이름 -->
				<div class="flex-1 overflow-hidden">
					<h1 class="truncate text-lg font-semibold text-gray-900">
						{m.chatRoom()} {roomIdParam}
					</h1>
					<p class="text-xs text-gray-500">{m.chatChatRoom()}</p>
				</div>
			{:else}
				<!-- 기본 상태 -->
				<div class="flex-1 overflow-hidden">
					<h1 class="text-lg font-semibold text-gray-900">{m.chatOverview()}</h1>
					<p class="text-xs text-gray-500">{m.chatSelectConversation()}</p>
				</div>
			{/if}
		</div>

		<!-- 핀 버튼 -->
		<Button
			variant="ghost"
			size="icon"
			onclick={handleTogglePin}
			class="shrink-0"
			title={isPinned ? '핀 해제' : '핀 설정'}
		>
			<span class="text-xl">{isPinned ? '📌' : '📍'}</span>
		</Button>

		<!-- 알림 구독 버튼 -->
		<Button
			variant="ghost"
			size="icon"
			onclick={handleToggleNotificationSubscription}
			disabled={subscriptionLoading}
			class="shrink-0"
			title={isNotificationSubscribed ? '알림 구독 해제' : '알림 구독'}
		>
			{#if isNotificationSubscribed}
				<!-- 구독 중: 진한 벨 아이콘 (실선) -->
				<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
					<path
						d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
					/>
				</svg>
			{:else}
				<!-- 구독 해제: 연한 벨 아이콘 + 슬래시 -->
				<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
					/>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6" />
				</svg>
			{/if}
		</Button>

		<!-- 메뉴 드롭다운 -->
		<DropdownMenu.Root>
			<DropdownMenu.Trigger>
				<Button variant="ghost" size="icon" class="shrink-0">
					<span class="text-xl">⋮</span>
				</Button>
			</DropdownMenu.Trigger>
			<DropdownMenu.Content align="end" class="w-56">
				<DropdownMenu.Item onclick={handleBookmark} class="bg-pink-50 hover:bg-pink-100">
					<span class="mr-2">🔖</span>
					{m.chatTabBookmarks()}
				</DropdownMenu.Item>
				<DropdownMenu.Item onclick={handleCopyUrl} class="bg-gray-50 hover:bg-gray-100">
					<span class="mr-2">🔗</span>
					URL 복사
				</DropdownMenu.Item>
				<DropdownMenu.Separator />
				{#if !isSingleChat}
					<!-- 그룹/오픈 채팅방에서만 친구 초대 기능 표시 -->
					<DropdownMenu.Item onclick={handleInviteFriend} class="bg-green-50 hover:bg-green-100">
						<span class="mr-2">👤</span>
						{m.chatInviteFriend()}
					</DropdownMenu.Item>
					<DropdownMenu.Separator />
				{/if}
				<DropdownMenu.Item onclick={handleMemberList} class="bg-blue-50 hover:bg-blue-100">
					<span class="mr-2">👥</span>
					멤버 목록
				</DropdownMenu.Item>
				<DropdownMenu.Separator />
				<DropdownMenu.Item
					onclick={handleLeaveRoom}
					class="bg-orange-50 text-orange-600 hover:bg-orange-100"
				>
					<span class="mr-2">🚪</span>
					방 탈퇴하기
				</DropdownMenu.Item>
				<DropdownMenu.Item
					onclick={handleReportAndLeave}
					class="bg-yellow-50 text-red-600 hover:bg-yellow-100"
				>
					<span class="mr-2">⚠️</span>
					신고하고 탈퇴하기
				</DropdownMenu.Item>
			</DropdownMenu.Content>
		</DropdownMenu.Root>
	</header>

	{#if !activeRoomId}
		<section class="chat-room-empty p-8">
			<p class="empty-title">{m.chatRoomNotReady()}</p>
			<p class="empty-subtitle">
				{m.chatAddUidOrRoomId()}
			</p>
		</section>
	{:else}
		<section class="flex flex-col gap-4">
			<div class="message-list-section relative max-h-[60vh] min-h-80 overflow-auto p-4">
				{#if canRenderMessages}
					{#key roomOrderPrefix}
						<DatabaseListView
							bind:this={databaseListView}
							path={messagePath}
							pageSize={20}
							orderBy={roomOrderField}
							orderPrefix={roomOrderPrefix}
							threshold={300}
							reverse={false}
							scrollTrigger="top"
							autoScrollToEnd={true}
							autoScrollOnNewData={true}
							onItemAdded={handleNewMessage}
						>
							{#snippet item(itemData: { key: string; data: any })}
								{@const message = itemData.data ?? {}}
								{@const mine = message.senderUid === authStore.user?.uid}
								<article
									class={`message-row ${mine ? 'message-row--mine' : 'message-row--theirs'}`}
								>
									{#if !mine}
										<Avatar uid={message.senderUid} size={36} class="message-avatar" />
									{/if}
									<div class={`message-bubble-wrap ${mine ? 'items-end text-right' : ''}`}>
										{#if !mine}
											<span class="message-sender-label"
												>{resolveSenderLabel(message.senderUid)}</span
											>
										{/if}
										<div class={`message-bubble ${mine ? 'bubble-mine' : 'bubble-theirs'}`}>
											<p class="message-text m-0">{message.text || ''}</p>
										</div>
										<span class="message-timestamp">{formatLongDate(message.createdAt)}</span>
									</div>
								</article>
							{/snippet}

							{#snippet loading()}
								<div class="message-placeholder py-6">{m.chatLoadingMessages()}</div>
							{/snippet}

							{#snippet empty()}
								<div class="message-placeholder py-6">{m.chatNoMessages()}</div>
							{/snippet}

							{#snippet error(errorMessage: string | null)}
								<div class="message-error py-4">
									<p>{m.chatLoadMessagesFailed()}</p>
									<p>{errorMessage ?? m.chatUnknownError()}</p>
								</div>
							{/snippet}

							{#snippet loadingMore()}
								<div class="message-placeholder subtle py-6">{m.chatLoadingMore()}</div>
							{/snippet}

							{#snippet noMore()}
								<div class="message-placeholder subtle py-6">{m.chatUpToDate()}</div>
							{/snippet}
						</DatabaseListView>
					{/key}
				{:else}
					<div class="message-placeholder py-6">{m.chatPreparingStream()}</div>
				{/if}

				<!-- 스크롤 컨트롤 버튼 -->
				{#if canRenderMessages}
					<div class="scroll-controls">
						<button
							type="button"
							class="scroll-button scroll-to-top"
							onclick={handleScrollToTop}
							title="맨 위로 이동"
						>
							↑
						</button>
						<button
							type="button"
							class="scroll-button scroll-to-bottom"
							onclick={handleScrollToBottom}
							title="맨 아래로 이동"
						>
							↓
						</button>
					</div>
				{/if}
			</div>

			<form class="flex items-center gap-3" onsubmit={handleSendMessage}>
				<input
					bind:this={composerInputRef}
					type="text"
					name="composer"
					class="composer-input flex-1 px-4 py-3.5"
					placeholder={m.chatWriteMessage()}
					bind:value={composerText}
					disabled={composerDisabled || isSending}
				/>
				<button
					type="submit"
					class="composer-button cursor-pointer px-8 py-3.5"
					disabled={composerDisabled || isSending || !composerText.trim()}
				>
					{isSending ? m.chatSending() : m.chatSend()}
				</button>
			</form>

			{#if sendError}
				<p class="composer-error m-0">{sendError}</p>
			{/if}
		</section>
	{/if}
</div>

<!-- 즐겨찾기 다이얼로그 -->
<ChatFavoritesDialog
	bind:open={favoritesDialogOpen}
	currentRoomId={activeRoomId}
	on:roomSelected={handleRoomSelected}
/>

<!-- 친구 초대 다이얼로그 -->
<UserSearchDialog
	bind:open={inviteDialogOpen}
	title={m.chatInviteFriend()}
	description={m.chatInviteToRoom()}
	submitLabel={m.chatInviteFriend()}
	showResults={true}
	on:userSelect={handleUserSelect}
/>

<style>
	@import 'tailwindcss' reference;

	/* 채팅방 헤더 스타일 */
	.chat-room-header {
		@apply flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-[0_10px_25px_rgba(15,23,42,0.06)];
	}

	/* 빈 채팅방 스타일 */
	.chat-room-empty {
		@apply rounded-2xl border border-dashed border-gray-300 bg-[#fdfdfd] text-center;
	}

	.empty-title {
		@apply mb-2 text-xl font-semibold text-gray-900;
	}

	.empty-subtitle {
		@apply text-gray-500;
	}

	/* 메시지 목록 스타일 */
	.message-list-section {
		@apply rounded-2xl border border-gray-200 bg-white;
	}

	.message-row {
		@apply flex gap-3 px-2 py-3;
	}

	.message-row--mine {
		@apply justify-end;
	}

	.message-row--theirs {
		@apply justify-start;
	}

	.message-avatar {
		@apply mr-2 rounded-full bg-gray-100 shadow-sm;
	}

	.message-bubble-wrap {
		@apply flex max-w-[75%] flex-col gap-1;
	}

	.message-bubble {
		@apply rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm;
	}

	.bubble-mine {
		@apply bg-amber-300 text-gray-900 shadow-inner;
	}

	.bubble-theirs {
		@apply border border-gray-200 bg-white text-gray-900;
	}

	.message-sender-label {
		@apply text-xs text-gray-400;
	}

	.message-text {
		@apply text-[0.95rem] break-words whitespace-pre-wrap text-gray-900;
	}

	.message-timestamp {
		@apply text-[11px] text-gray-400;
	}

	/* 메시지 플레이스홀더 스타일 */
	.message-placeholder {
		@apply text-center text-gray-500;
	}

	.message-placeholder.subtle {
		@apply text-sm text-gray-400;
	}

	.message-error {
		@apply text-center text-red-600;
	}

	/* 메시지 입력 스타일 */
	.composer-input {
		@apply rounded-full border border-gray-300 bg-white text-base;
	}

	.composer-input:disabled {
		@apply bg-gray-100;
	}

	.composer-button {
		@apply rounded-full border-0 bg-gray-900 font-semibold text-white transition-colors duration-200;
	}

	.composer-button:disabled {
		@apply cursor-not-allowed bg-gray-400;
	}

	.composer-error {
		@apply text-sm text-red-600;
	}

	/* 스크롤 컨트롤 버튼 스타일 */
	.scroll-controls {
		@apply absolute right-4 bottom-4 flex flex-col gap-2;
	}

	.scroll-button {
		@apply flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-0 bg-gray-900 text-lg font-bold text-white shadow-lg transition-all duration-200;
	}

	.scroll-button:hover {
		@apply scale-110 bg-gray-700;
	}

	.scroll-button:active {
		@apply scale-95 bg-gray-950;
	}
</style>
