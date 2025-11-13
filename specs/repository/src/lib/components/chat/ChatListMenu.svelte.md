---
title: ChatListMenu.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 ChatListMenu.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 채팅 목록 상단 메뉴 컴포넌트
	 *
	 * 친구, 그룹챗, 오픈챗 탭과 방생성, 북마크, 검색 기능을 제공하는 재사용 가능한 메뉴 컴포넌트입니다.
	 */

	import { m } from '$lib/paraglide/messages';
	import { Button } from '$lib/components/ui/button/index.js';
	import { goto } from '$app/navigation';

	type TabType = 'friends' | 'groupChats' | 'openChats';

	interface Props {
		/** 현재 선택된 탭 */
		selectedTab: TabType;
		/** 방생성 버튼 클릭 콜백 (optional) */
		onCreateRoom?: () => void;
		/** 친구 찾기 메뉴 클릭 콜백 (optional) */
		onFindFriends?: () => void;
		/** 그룹챗 생성 메뉴 클릭 콜백 (optional) */
		onCreateGroupChat?: () => void;
		/** 오픈챗 생성 메뉴 클릭 콜백 (optional) */
		onCreateOpenChat?: () => void;
		/** 북마크 메뉴 클릭 콜백 (optional) */
		onBookmark?: () => void;
		/** 검색 메뉴 클릭 콜백 (optional) */
		onSearch?: () => void;
	}

	const { selectedTab, onCreateRoom, onFindFriends, onCreateGroupChat, onCreateOpenChat, onBookmark, onSearch }: Props = $props();

	// 드롭다운 메뉴 상태
	let isDropdownOpen = $state(false);
	let dropdownButton: HTMLButtonElement | null = null;

	/**
	 * 탭 클릭 핸들러 - 페이지 이동
	 */
	function handleTabClick(tab: TabType) {
		const routes: Record<TabType, string> = {
			friends: '/chat/list',
			groupChats: '/chat/group-chat-list',
			openChats: '/chat/open-chat-list'
		};
		void goto(routes[tab]);
	}

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
	function handleMenuItemClick(action: 'findFriends' | 'createGroupChat' | 'createOpenChat' | 'bookmarks' | 'search') {
		if (action === 'findFriends' && onFindFriends) {
			onFindFriends();
		} else if (action === 'createGroupChat' && onCreateGroupChat) {
			onCreateGroupChat();
		} else if (action === 'createOpenChat' && onCreateOpenChat) {
			onCreateOpenChat();
		} else if (action === 'bookmarks' && onBookmark) {
			onBookmark();
		} else if (action === 'search' && onSearch) {
			onSearch();
		}
		isDropdownOpen = false;
	}

	/**
	 * 방생성 버튼 클릭 핸들러
	 */
	function handleCreateRoom() {
		if (onCreateRoom) {
			onCreateRoom();
		}
	}
</script>

<svelte:window onclick={handleClickOutside} />

<!-- 탭바 -->
<div class="mt-6 flex items-center justify-between border-b border-gray-200">
	<!-- 왼쪽 탭들 -->
	<div class="flex gap-1">
		<button
			type="button"
			class="tab-button"
			class:tab-active={selectedTab === 'friends'}
			onclick={() => handleTabClick('friends')}
		>
			{m.chatTabFriends()}
		</button>
		<button
			type="button"
			class="tab-button"
			class:tab-active={selectedTab === 'groupChats'}
			onclick={() => handleTabClick('groupChats')}
		>
			{m.chatTabGroupChats()}
		</button>
		<button
			type="button"
			class="tab-button"
			class:tab-active={selectedTab === 'openChats'}
			onclick={() => handleTabClick('openChats')}
		>
			{m.chatTabOpenChats()}
		</button>
	</div>

	<!-- 오른쪽 버튼들 -->
	<div class="flex items-center gap-2">
		<!-- 친구 탭인 경우: 친구찾기 버튼 표시 -->
		{#if selectedTab === 'friends'}
			<Button variant="outline" size="sm" onclick={() => onFindFriends?.()}>{m.chatFindFriends()}</Button>
		{:else}
			<!-- 그룹챗/오픈챗 탭인 경우: 방생성 버튼 표시 -->
			<Button variant="outline" size="sm" onclick={handleCreateRoom}>{m.chatCreateRoom()}</Button>
		{/if}

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
					<!-- 친구 찾기 -->
					<button
						type="button"
						class="dropdown-item"
						role="menuitem"
						onclick={() => handleMenuItemClick('findFriends')}
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
								d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
							/>
						</svg>
						{m.chatFindFriends()}
					</button>

					<!-- 그룹챗 생성 -->
					<button
						type="button"
						class="dropdown-item"
						role="menuitem"
						onclick={() => handleMenuItemClick('createGroupChat')}
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
								d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"
							/>
						</svg>
						{m.chatCreateGroupChat()}
					</button>

					<!-- 오픈챗 생성 -->
					<button
						type="button"
						class="dropdown-item"
						role="menuitem"
						onclick={() => handleMenuItemClick('createOpenChat')}
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
								d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z"
							/>
						</svg>
						{m.chatCreateOpenChat()}
					</button>

					<!-- 북마크 -->
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

					<!-- 검색 -->
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
		@apply absolute right-0 top-full z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none;
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

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
