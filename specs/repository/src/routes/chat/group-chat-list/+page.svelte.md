---
name: +page.svelte
description: +page 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/chat/group-chat-list/+page.svelte
---

# +page.svelte

## 개요

**파일 경로**: `src/routes/chat/group-chat-list/+page.svelte`
**파일 타입**: svelte-component
**카테고리**: route-page

+page 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 그룹 채팅방 목록 페이지
	 *
	 * DatabaseListView를 사용하여 내가 참여한 그룹 채팅방 목록을 무한 스크롤로 표시합니다.
	 */

	import DatabaseListView from '$lib/components/DatabaseListView.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import ChatCreateDialog from '$lib/components/chat/ChatCreateDialog.svelte';
	import ChatInvitationList from '$lib/components/chat/ChatInvitationList.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { m } from '$lib/paraglide/messages';
	import { formatLongDate } from '$lib/functions/date.functions';
	import { resolveRoomTypeLabel, togglePinChatRoom } from '$lib/functions/chat.functions';
	import ChatListMenu from '$lib/components/chat/ChatListMenu.svelte';
	import ChatFavoritesDialog from '$lib/components/chat/ChatFavoritesDialog.svelte';
	import { rtdb } from '$lib/firebase';

	type ChatJoinData = Record<string, unknown>;

	const PAGE_SIZE = 20;
	const JOIN_ORDER_FIELD = 'openAndGroupChatListOrder';

	// ChatCreateDialog 상태
	let createDialogOpen = $state(false); // 그룹 채팅방 생성
	let openChatDialogOpen = $state(false); // 오픈 채팅방 생성

	// ChatFavoritesDialog 상태
	let favoritesDialogOpen = $state(false);

	/**
	 * 방생성 버튼 클릭 핸들러
	 * ChatCreateDialog를 열어서 그룹 채팅방 생성
	 */
	function handleCreateRoom() {
		createDialogOpen = true;
	}

	/**
	 * 채팅방 생성 완료 핸들러
	 * 생성된 채팅방으로 자동 이동
	 */
	function handleRoomCreated(event: CustomEvent<{ roomId: string }>) {
		const { roomId } = event.detail;
		console.log('✅ 채팅방 생성 완료, 이동:', roomId);
		void goto(`/chat/room?roomId=${roomId}`);
	}

	/**
	 * 친구 찾기 메뉴 클릭 핸들러
	 */
	function handleFindFriends() {
		console.log('친구 찾기 메뉴 클릭됨');
		// TODO: 친구 찾기 기능 구현
	}

	/**
	 * 그룹챗 생성 메뉴 클릭 핸들러
	 * 그룹 채팅방 생성 다이얼로그를 엽니다.
	 */
	function handleCreateGroupChat() {
		createDialogOpen = true;
	}

	/**
	 * 오픈챗 생성 메뉴 클릭 핸들러
	 * 오픈 채팅방 생성 다이얼로그를 엽니다.
	 */
	function handleCreateOpenChat() {
		openChatDialogOpen = true;
	}

	/**
	 * 즐겨찾기 메뉴 클릭 핸들러
	 * 즐겨찾기 다이얼로그를 엽니다.
	 */
	function handleBookmark() {
		favoritesDialogOpen = true;
	}

	/**
	 * 검색 메뉴 클릭 핸들러
	 */
	function handleSearch() {
		console.log('검색 메뉴 클릭됨');
		// TODO: 검색 기능 구현
	}

	/**
	 * 즐겨찾기에서 채팅방 선택 핸들러
	 * 선택된 채팅방으로 이동합니다.
	 */
	function handleRoomSelected(event: CustomEvent<{ roomId: string }>) {
		const { roomId } = event.detail;
		void goto(`/chat/room?roomId=${roomId}`);
	}

	/**
	 * 채팅방 핀 토글 핸들러
	 * 클릭 시 채팅방을 핀하거나 핀 해제합니다
	 */
	async function handleTogglePin(
		event: MouseEvent,
		roomId: string,
		roomType: string
	): Promise<void> {
		event.stopPropagation(); // 버튼 클릭 이벤트 전파 방지

		const uid = authStore.user?.uid;
		if (!uid) {
			console.error('사용자 인증 정보가 없습니다');
			return;
		}

		if (!rtdb) {
			console.error('Database가 초기화되지 않았습니다');
			return;
		}

		try {
			const isPinned = await togglePinChatRoom(rtdb, roomId, uid, roomType);
			console.log(`✅ 채팅방 핀 ${isPinned ? '설정' : '해제'} 완료:`, roomId);
		} catch (error) {
			console.error('채팅방 핀 토글 실패:', error);
		}
	}

	// 현재 로그인 사용자의 chat-joins 경로
	const chatJoinPath = $derived.by(() => {
		const uid = authStore.user?.uid;
		const path = uid ? `chat-joins/${uid}` : '';
		console.log('🔍 [Group Chat List Debug] User UID:', uid);
		console.log('🔍 [Group Chat List Debug] Chat join path:', path);
		return path;
	});

	/**
	 * 채팅방 제목을 계산
	 */
	function resolveRoomTitle(join: ChatJoinData, fallback: string) {
		if (typeof join.roomTitle === 'string' && join.roomTitle.trim()) return join.roomTitle;
		if (typeof join.roomName === 'string' && join.roomName.trim()) return join.roomName;
		if (typeof join.title === 'string' && join.title.trim()) return join.title;
		if (typeof join.displayName === 'string' && join.displayName.trim()) return join.displayName;

		return fallback;
	}

	/**
	 * 채팅방 열기
	 */
	function openConversation(join: ChatJoinData, roomId: string) {
		if (roomId) {
			void goto(`/chat/room?roomId=${roomId}`);
		}
	}
</script>

<svelte:head>
	<title>{m.chatTabGroupChats()}</title>
</svelte:head>

<div class="space-y-6">
	<section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
		<div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
			<div>
				<h1 class="text-2xl font-semibold text-gray-900">{m.chatTabGroupChats()}</h1>
				<p class="text-sm text-gray-500">내가 참여한 그룹 채팅방 목록입니다</p>
			</div>
			{#if authStore.isAuthenticated && authStore.user?.uid}
				<p class="text-xs uppercase tracking-wide text-gray-400">
					UID: <span class="font-mono text-gray-600">{authStore.user.uid}</span>
				</p>
			{/if}
		</div>

		<!-- 채팅 목록 메뉴 컴포넌트 -->
		<ChatListMenu
			selectedTab="groupChats"
			onCreateRoom={handleCreateRoom}
			onFindFriends={handleFindFriends}
			onCreateGroupChat={handleCreateGroupChat}
			onCreateOpenChat={handleCreateOpenChat}
			onBookmark={handleBookmark}
			onSearch={handleSearch}
		/>
	</section>

	{#if authStore.loading}
		<section class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-gray-500 shadow-sm">
			<div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-2 border-gray-200 border-t-blue-500"></div>
			<p>{m.chatLoadingRooms()}</p>
		</section>
	{:else if !authStore.isAuthenticated}
		<section class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm">
			<p class="text-lg font-semibold text-gray-800">{m.chatSignInRequired()}</p>
			<p class="mt-2 text-sm text-gray-500">{m.chatSignInToSend()}</p>
		</section>
	{:else if !chatJoinPath}
		<section class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm">
			<p class="text-sm text-gray-500">{m.chatSelectConversation()}</p>
		</section>
	{:else}
		<!-- 채팅 초대 목록 -->
		<section class="rounded-2xl border border-blue-200 bg-white shadow-sm">
			<ChatInvitationList />
		</section>

		<section class="rounded-2xl border border-gray-200 bg-white p-0 shadow-sm">
			{#key chatJoinPath}
				{@const dbListViewProps = {
					path: chatJoinPath,
					pageSize: PAGE_SIZE,
					orderBy: JOIN_ORDER_FIELD,
					threshold: 320,
					reverse: true
				}}
				{#if chatJoinPath}
					{console.log('🔍 [Group Chat List Debug] DatabaseListView props:', dbListViewProps)}
				{/if}
				<DatabaseListView
					path={chatJoinPath}
					pageSize={PAGE_SIZE}
					orderBy={JOIN_ORDER_FIELD}
					threshold={320}
					reverse={true}
				>
					{#snippet item(itemData, index)}
						{console.log('🔍 [Group Chat List Debug] Item received:', {
							index,
							key: itemData.key,
							hasData: !!itemData.data,
							data: itemData.data
						})}
						{@const join = (itemData.data ?? {}) as ChatJoinData}
						{@const roomId = (join.roomId ?? itemData.key ?? '') as string}
						{@const roomType = (join.roomType ?? join.type ?? 'group').toString()}
						{@const openAndGroupChatListOrder = join.openAndGroupChatListOrder ?? null}
						{console.log('🔍 [Group Chat List Debug] Join data:', {
							roomId,
							roomType,
							openAndGroupChatListOrder,
							lastMessageText: join.lastMessageText,
							lastMessageAt: join.lastMessageAt,
							newMessageCount: join.newMessageCount,
							allFields: Object.keys(join)
						})}
						{@const lastMessage =
							typeof join.lastMessageText === 'string' && join.lastMessageText.trim()
								? join.lastMessageText
								: typeof join.lastMessage === 'string' && join.lastMessage.trim()
									? join.lastMessage
									: typeof join.preview === 'string'
										? join.preview
										: ''}
						{@const timestamp = Number(join.lastMessageAt ?? join.updatedAt ?? join.joinedAt ?? 0) || null}
						{@const unreadCount = Number(join.newMessageCount ?? join.unreadCount ?? join.unread ?? 0) || 0}
						{@const roomTitle = resolveRoomTitle(join, roomId || m.chatChatRoom())}
						{@const isPinned = join.pin === true}
						<div class="flex w-full items-start border-b border-gray-100">
							<button
								type="button"
								class="flex flex-1 items-start gap-4 p-4 text-left transition hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
								onclick={() => openConversation(join, roomId)}
							>
								<div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-blue-500 text-sm font-semibold text-white shadow-sm">
									{roomTitle.slice(0, 2)}
								</div>

								<div class="flex-1 space-y-1">
									<div class="flex flex-wrap items-center gap-x-2 text-sm text-gray-500">
										<span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-purple-600">
											{resolveRoomTypeLabel(roomType)}
										</span>
										<span class="text-xs text-gray-400">#{roomId}</span>
										{#if unreadCount > 0}
											<span class="rounded-full bg-blue-600 px-2 py-0.5 text-xs font-semibold text-white">
												{unreadCount}
											</span>
										{/if}
									</div>

									<h2 class="text-lg font-semibold text-gray-900">{roomTitle}</h2>

									<p class="text-sm text-gray-500">
										<span class="font-medium text-gray-600">{m.chatLastMessageLabel()}:</span>
										<span class="ml-1 line-clamp-1">{lastMessage || m.chatNoMessages()}</span>
									</p>

									{#if timestamp}
										<p class="text-xs text-gray-400">{formatLongDate(timestamp)}</p>
									{/if}
								</div>
							</button>

							<div class="flex flex-col items-center gap-2 p-4">
								<!-- 핀 버튼 -->
								<button
									type="button"
									onclick={(e) => handleTogglePin(e, roomId, roomType)}
									class="rounded-full p-1.5 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
									title={isPinned ? '핀 해제' : '핀 설정'}
								>
									<span class="text-xl">{isPinned ? '📌' : '📍'}</span>
								</button>
								<span class="text-sm font-medium text-blue-600">{m.chatOpenRoom()}</span>
							</div>
						</div>
					{/snippet}

					{#snippet loading()}
						<p class="py-6 text-center text-sm text-gray-500">{m.chatLoadingRooms()}</p>
					{/snippet}

					{#snippet empty()}
						<div class="py-12 text-center text-gray-500">
							<p class="text-sm">참여한 그룹 채팅방이 없습니다</p>
						</div>
					{/snippet}

					{#snippet loadingMore()}
						<p class="py-4 text-center text-xs uppercase tracking-wide text-gray-400">{m.chatLoadingMore()}</p>
					{/snippet}

					{#snippet noMore()}
						<p class="py-6 text-center text-xs uppercase tracking-wide text-gray-400">{m.chatUpToDate()}</p>
					{/snippet}
				</DatabaseListView>
			{/key}
		</section>
	{/if}
</div>

<!-- 그룹 채팅방 생성 다이얼로그 -->
<ChatCreateDialog type="group" bind:open={createDialogOpen} on:created={handleRoomCreated} />

<!-- 오픈 채팅방 생성 다이얼로그 -->
<ChatCreateDialog type="open" bind:open={openChatDialogOpen} on:created={handleRoomCreated} />

<!-- 즐겨찾기 다이얼로그 -->
<ChatFavoritesDialog bind:open={favoritesDialogOpen} on:roomSelected={handleRoomSelected} />

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
