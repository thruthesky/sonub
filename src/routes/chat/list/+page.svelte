<script lang="ts">
	/**
	 * 채팅방 목록 페이지
	 *
	 * DatabaseListView를 사용하여 내가 참여한 채팅방 목록을 무한 스크롤로 표시합니다.
	 */

	import DatabaseListView from '$lib/components/DatabaseListView.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import UserSearchDialog from '$lib/components/user/UserSearchDialog.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { m } from '$lib/paraglide/messages';
	import { formatLongDate } from '$lib/functions/date.functions';
	import { resolveRoomTypeLabel } from '$lib/functions/chat.functions';
	import ChatListMenu from '$lib/components/chat/ChatListMenu.svelte';

	type ChatJoinData = Record<string, unknown>;
	type UserData = Record<string, unknown>;

	const PAGE_SIZE = 20;
	const JOIN_ORDER_FIELD = 'singleChatListOrder';

	// UserSearchDialog 상태
	let userSearchOpen = $state(false);
	let searchKeyword = $state('');

	/**
	 * 방생성 버튼 클릭 핸들러
	 */
	function handleCreateRoom() {
		console.log('방생성 버튼 클릭됨');
		// TODO: 방생성 기능 구현
	}

	/**
	 * 친구 찾기 메뉴 클릭 핸들러
	 * UserSearchDialog를 열어서 사용자 검색
	 */
	function handleFindFriends() {
		userSearchOpen = true;
	}

	/**
	 * 사용자 선택 핸들러
	 * 선택된 사용자와 1:1 채팅방으로 이동
	 */
	function handleUserSelect(event: CustomEvent<{ user: UserData; uid: string }>) {
		const { uid } = event.detail;
		console.log('선택된 사용자:', event.detail);
		// 1:1 채팅방으로 이동
		void goto(`/chat/room?uid=${uid}`);
	}

	/**
	 * 그룹챗 생성 메뉴 클릭 핸들러
	 */
	function handleCreateGroupChat() {
		console.log('그룹챗 생성 메뉴 클릭됨');
		// TODO: 그룹챗 생성 기능 구현
	}

	/**
	 * 오픈챗 생성 메뉴 클릭 핸들러
	 */
	function handleCreateOpenChat() {
		console.log('오픈챗 생성 메뉴 클릭됨');
		// TODO: 오픈챗 생성 기능 구현
	}

	/**
	 * 북마크 메뉴 클릭 핸들러
	 */
	function handleBookmark() {
		console.log('북마크 메뉴 클릭됨');
		// TODO: 북마크 기능 구현
	}

	/**
	 * 검색 메뉴 클릭 핸들러
	 */
	function handleSearch() {
		console.log('검색 메뉴 클릭됨');
		// TODO: 검색 기능 구현
	}

	// 현재 로그인 사용자의 chat-joins 경로
	const chatJoinPath = $derived.by(() => {
		const uid = authStore.user?.uid;
		const path = uid ? `chat-joins/${uid}` : '';
		console.log('🔍 [Chat List Debug] User UID:', uid);
		console.log('🔍 [Chat List Debug] Chat join path:', path);
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
		if (typeof join.partnerDisplayName === 'string' && join.partnerDisplayName.trim())
			return join.partnerDisplayName;

		const partnerUid: string | undefined =
			typeof join.partnerUid === 'string' ? join.partnerUid
			: typeof join.targetUid === 'string' ? join.targetUid
			: undefined;

		if (partnerUid) {
			return `@${partnerUid.slice(0, 8)}`;
		}

		return fallback;
	}

	/**
	 * 채팅방 열기
	 */
	function openConversation(join: ChatJoinData, roomId: string) {
		const normalizedType = (join.roomType ?? join.type ?? 'single')
			.toString()
			.toLowerCase();

		const partnerUid: string | undefined =
			typeof join.partnerUid === 'string' ? join.partnerUid
			: typeof join.targetUid === 'string' ? join.targetUid
			: undefined;

		if (normalizedType.includes('single') && partnerUid) {
			void goto(`/chat/room?uid=${partnerUid}`);
			return;
		}

		if (roomId) {
			void goto(`/chat/room?roomId=${roomId}`);
		}
	}
</script>

<svelte:head>
	<title>{m.pageTitleChat()}</title>
</svelte:head>

<div class="space-y-6">
	<section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
		<div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
			<div>
				<h1 class="text-2xl font-semibold text-gray-900">{m.chatMyRoomsTitle()}</h1>
				<p class="text-sm text-gray-500">{m.chatMyRoomsDesc()}</p>
			</div>
			{#if authStore.isAuthenticated && authStore.user?.uid}
				<p class="text-xs uppercase tracking-wide text-gray-400">
					UID: <span class="font-mono text-gray-600">{authStore.user.uid}</span>
				</p>
			{/if}
		</div>

		<!-- 채팅 목록 메뉴 컴포넌트 -->
		<ChatListMenu
			selectedTab="friends"
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
					{console.log('🔍 [Chat List Debug] DatabaseListView props:', dbListViewProps)}
				{/if}
				<DatabaseListView
					path={chatJoinPath}
					pageSize={PAGE_SIZE}
					orderBy={JOIN_ORDER_FIELD}
					threshold={320}
					reverse={true}
				>
					{#snippet item(itemData, index)}
						{console.log('🔍 [Chat List Debug] Item received:', {
							index,
							key: itemData.key,
							hasData: !!itemData.data,
							data: itemData.data
						})}
						{@const join = (itemData.data ?? {}) as ChatJoinData}
						{@const roomId = (join.roomId ?? itemData.key ?? '') as string}
						{@const roomType = (join.roomType ?? join.type ?? 'single').toString()}
						{@const singleChatListOrder = join.singleChatListOrder ?? null}
						{console.log('🔍 [Chat List Debug] Join data:', {
							roomId,
							roomType,
							singleChatListOrder,
							partnerUid: join.partnerUid,
							lastMessageText: join.lastMessageText,
							lastMessageAt: join.lastMessageAt,
							newMessageCount: join.newMessageCount,
							allFields: Object.keys(join)
						})}
						{@const partnerUid: string | null =
							typeof join.partnerUid === 'string' ? join.partnerUid
							: typeof join.targetUid === 'string' ? join.targetUid
							: null}
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
						<button
							type="button"
							class="flex w-full items-start gap-4 border-b border-gray-100 p-4 text-left transition hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
							onclick={() => openConversation(join, roomId)}
						>
							{#if partnerUid}
								<Avatar uid={partnerUid} size={48} class="shadow-sm" />
							{:else}
								<div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600">
									{roomTitle.slice(0, 2)}
								</div>
							{/if}

							<div class="flex-1 space-y-1">
								<div class="flex flex-wrap items-center gap-x-2 text-sm text-gray-500">
									<span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-gray-600">
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

							<div class="flex items-center">
								<span class="text-sm font-medium text-blue-600">{m.chatOpenRoom()}</span>
							</div>
						</button>
					{/snippet}

					{#snippet loading()}
						<p class="py-6 text-center text-sm text-gray-500">{m.chatLoadingRooms()}</p>
					{/snippet}

					{#snippet empty()}
						<div class="py-12 text-center text-gray-500">
							<p class="text-sm">{m.chatEmptyRooms()}</p>
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

<!-- 사용자 검색 다이얼로그 -->
<UserSearchDialog
	bind:open={userSearchOpen}
	bind:keyword={searchKeyword}
	showResults={true}
	title="친구 찾기"
	description="사용자를 검색하여 1:1 채팅을 시작하세요."
	label="사용자 이름"
	placeholder="검색할 사용자 이름을 입력하세요"
	on:userSelect={handleUserSelect}
/>
