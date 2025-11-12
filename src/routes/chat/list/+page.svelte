<script lang="ts">
	/**
	 * 채팅방 목록 페이지
	 *
	 * DatabaseListView를 사용하여 내가 참여한 채팅방 목록을 무한 스크롤로 표시합니다.
	 */

	import DatabaseListView from '$lib/components/DatabaseListView.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { m } from '$lib/paraglide/messages';
	import { formatLongDate } from '$lib/functions/date.functions';
	import { resolveRoomTypeLabel } from '$lib/functions/chat.functions';
	import { Button } from '$lib/components/ui/button/index.js';

	type ChatJoinData = Record<string, unknown>;
	type TabType = 'friends' | 'groupChats' | 'openChats';

	const PAGE_SIZE = 20;
	const JOIN_ORDER_FIELD = 'listOrder';

	// 현재 선택된 탭 상태
	let selectedTab = $state<TabType>('friends');

	// 드롭다운 메뉴 상태
	let isDropdownOpen = $state(false);
	let dropdownButton: HTMLButtonElement | null = null;

	/**
	 * 드롭다운 토글
	 */
	function toggleDropdown() {
		isDropdownOpen = !isDropdownOpen;
	}

	/**
	 * 드롭다운 외부 클릭 감지
	 */
	function handleClickOutside(event: MouseEvent) {
		if (dropdownButton && !dropdownButton.contains(event.target as Node)) {
			isDropdownOpen = false;
		}
	}

	/**
	 * 메뉴 아이템 클릭 핸들러
	 */
	function handleMenuItemClick(action: 'bookmarks' | 'search') {
		console.log(`Selected: ${action}`);
		// TODO: 북마크/검색 기능 구현
		isDropdownOpen = false;
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

<svelte:window onclick={handleClickOutside} />

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

		<!-- 탭바 -->
		<div class="mt-6 flex items-center justify-between border-b border-gray-200">
			<!-- 왼쪽 탭들 -->
			<div class="flex gap-1">
				<button
					type="button"
					class="tab-button"
					class:tab-active={selectedTab === 'friends'}
					onclick={() => (selectedTab = 'friends')}
				>
					{m.chatTabFriends()}
				</button>
				<button
					type="button"
					class="tab-button"
					class:tab-active={selectedTab === 'groupChats'}
					onclick={() => (selectedTab = 'groupChats')}
				>
					{m.chatTabGroupChats()}
				</button>
				<button
					type="button"
					class="tab-button"
					class:tab-active={selectedTab === 'openChats'}
					onclick={() => (selectedTab = 'openChats')}
				>
					{m.chatTabOpenChats()}
				</button>
			</div>

		<!-- 오른쪽 버튼들 -->
		<div class="flex items-center gap-2">
			<!-- 방생성 버튼 -->
			<Button variant="outline" size="sm">{m.chatCreateRoom()}</Button>

			<!-- 설정 드롭다운 -->
			<div class="relative">
				<button
					bind:this={dropdownButton}
					type="button"
					class="rounded-md p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900"
					onclick={toggleDropdown}
					aria-label="설정 메뉴"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						fill="none"
						viewBox="0 0 24 24"
						stroke-width="1.5"
						stroke="currentColor"
						class="h-5 w-5"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z"
						/>
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
					</svg>
				</button>

				{#if isDropdownOpen}
					<div
						class="dropdown-menu"
						role="menu"
						aria-orientation="vertical"
						aria-labelledby="menu-button"
					>
						<button
							type="button"
							class="dropdown-item"
							role="menuitem"
							onclick={() => handleMenuItemClick('bookmarks')}
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="h-4 w-4"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"
								/>
							</svg>
							{m.chatTabBookmarks()}
						</button>
						<button
							type="button"
							class="dropdown-item"
							role="menuitem"
							onclick={() => handleMenuItemClick('search')}
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
								stroke-width="1.5"
								stroke="currentColor"
								class="h-4 w-4"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
								/>
							</svg>
							{m.chatTabSearch()}
						</button>
					</div>
				{/if}
			</div>
		</div>
		</div>
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
						{@const listOrder = join.listOrder ?? null}
						{console.log('🔍 [Chat List Debug] Join data:', {
							roomId,
							roomType,
							listOrder,
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

<style>
	@import 'tailwindcss' reference;

	/* 탭 버튼 스타일 */
	.tab-button {
		@apply relative border-b-2 border-transparent px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:text-gray-900;
	}

	.tab-active {
		@apply border-blue-600 text-blue-600;
	}

	/* 드롭다운 메뉴 스타일 */
	.dropdown-menu {
		@apply absolute right-0 top-full z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none;
	}

	.dropdown-item {
		@apply flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900;
	}

	.dropdown-item:first-child {
		@apply rounded-t-md;
	}

	.dropdown-item:last-child {
		@apply rounded-b-md;
	}
</style>
