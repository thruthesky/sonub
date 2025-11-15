<script lang="ts">
	/**
	 * 채팅방 전용 레이아웃
	 *
	 * 채팅방 페이지에서만 사용되는 레이아웃입니다.
	 * - 모바일: TopBar를 숨기고 전체 화면 사용
	 * - 데스크톱: TopBar 표시, 왼쪽 사이드바(채팅방 목록) + 오른쪽(채팅 메시지) 2-column 레이아웃
	 */

	import FirestoreListView from '$lib/components/FirestoreListView.svelte';
	import ChatRoomListItem from './ChatRoomListItem.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { m } from '$lib/paraglide/messages';
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { getPartnerUidFromSingleRoomId } from '$shared/chat.pure-functions';

	let { children } = $props();

	// 현재 활성화된 roomId (URL 파라미터에서 가져옴)
	const activeRoomId = $derived.by(() => {
		const urlRoomId = $page.url.searchParams.get('roomId');
		const urlUid = $page.url.searchParams.get('uid');
		if (urlRoomId) return urlRoomId;
		if (urlUid && authStore.user?.uid) {
			// 1:1 채팅방 ID 생성 (uid 정렬)
			const uids = [authStore.user.uid, urlUid].sort();
			return `single-${uids[0]}-${uids[1]}`;
		}
		return '';
	});

	// 채팅방 클릭 핸들러
	function handleRoomClick(roomId: string, type: string) {
		if (type === 'single') {
			// 1:1 채팅방: roomId에서 상대 uid 추출
			const partnerUid = getPartnerUidFromSingleRoomId(roomId, authStore.user?.uid || '');
			if (partnerUid) {
				void goto(`/chat/room?uid=${partnerUid}`);
			}
		} else {
			// 그룹/오픈 채팅방
			void goto(`/chat/room?roomId=${roomId}`);
		}
	}
</script>

<!-- 채팅방 컨테이너: 전체 화면 활용 -->
<div class="chat-room-layout">
	<!-- 데스크톱: 왼쪽 사이드바 (채팅방 목록) -->
	<aside class="chat-room-sidebar">
		<div class="sidebar-header">
			<h2 class="sidebar-title">{m.chatRoomList()}</h2>
		</div>

		<div class="sidebar-content">
			{#if authStore.user?.uid}
				<FirestoreListView
					path="users/{authStore.user.uid}/chat-joins"
					orderByField="allChatListOrder"
					orderDirection="desc"
					pageSize={20}
					scrollTrigger="bottom"
				>
					{#snippet item(itemData)}
						<ChatRoomListItem
							roomId={itemData.id}
							roomData={itemData.data ?? {}}
							{activeRoomId}
							onclick={() => handleRoomClick(itemData.id, itemData.data?.type || 'group')}
						/>
					{/snippet}

					{#snippet loading()}
						<div class="sidebar-placeholder">{m.chatRoomListLoading()}</div>
					{/snippet}

					{#snippet empty()}
						<div class="sidebar-placeholder">{m.chatRoomListEmpty()}</div>
					{/snippet}

					{#snippet error(errorMessage: string | null)}
						<div class="sidebar-error">{errorMessage}</div>
					{/snippet}
				</FirestoreListView>
			{:else}
				<div class="sidebar-placeholder">{m.authSignInRequired()}</div>
			{/if}
		</div>
	</aside>

	<!-- 메인 콘텐츠 (채팅방 페이지) -->
	<main class="chat-room-main">
		{@render children()}
	</main>
</div>

<style>
	@import 'tailwindcss' reference;

	/**
	 * 채팅방 레이아웃 컨테이너
	 * - 모바일: 단일 컬럼 (전체 화면)
	 * - 데스크톱: 2-column (사이드바 + 메인)
	 */
	.chat-room-layout {
		@apply fixed;
		@apply top-0 left-0;
		/* 모바일: 전체 화면 높이 사용 (TopBar 숨김) */
		@apply h-[100dvh];

		/* 전체 너비 사용 */
		@apply w-full;

		/* 배경색 */
		@apply bg-gray-50;

		/* 모바일 safe-area 고려 (상태바, 노치 등) */
		padding-top: env(safe-area-inset-top);
		padding-bottom: env(safe-area-inset-bottom);

		/* 모바일: 단일 컬럼 */
		@apply flex flex-col;
	}

	/* 데스크톱: TopBar 높이(4rem) 제외, 2-column 레이아웃 */
	@media (min-width: 768px) {
		.chat-room-layout {
			/* TopBar 높이(4rem) 제외 + 채팅방 상하 여백 (4rem) */
			height: calc(100vh - 8rem);
			@apply relative;
			/* 2-column: 사이드바 + 메인 */
			@apply flex-row;
		}
	}

	/**
	 * 왼쪽 사이드바 (채팅방 목록)
	 * - 모바일: 숨김
	 * - 데스크톱: 표시 (고정 너비)
	 */
	.chat-room-sidebar {
		/* 모바일: 숨김 */
		@apply hidden;
	}

	/* 데스크톱: 사이드바 표시 */
	@media (min-width: 768px) {
		.chat-room-sidebar {
			@apply flex flex-col;
			@apply w-80;
			@apply border-r border-gray-200;
			@apply bg-white;
			@apply overflow-hidden;
		}
	}

	.sidebar-header {
		@apply flex items-center justify-between;
		@apply px-4 py-3;
		@apply border-b border-gray-200;
		@apply shrink-0;
	}

	.sidebar-title {
		@apply text-lg font-semibold text-gray-900;
	}

	.sidebar-content {
		@apply flex-1 overflow-auto;
	}

	.sidebar-placeholder {
		@apply flex items-center justify-center;
		@apply p-8 text-center text-gray-500;
	}

	.sidebar-error {
		@apply p-4 text-center text-red-600;
	}

	/**
	 * 메인 콘텐츠 (채팅방 페이지)
	 * - 모바일: 전체 화면
	 * - 데스크톱: 나머지 공간 사용
	 */
	.chat-room-main {
		@apply flex-1;
		@apply overflow-hidden;
	}
</style>
