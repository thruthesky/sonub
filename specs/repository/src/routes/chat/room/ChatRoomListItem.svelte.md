---
title: ChatRoomListItem.svelte
type: component
path: src/routes/chat/room/ChatRoomListItem.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/routes/chat/room/ChatRoomListItem.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 채팅방 목록 아이템 컴포넌트
	 *
	 * 각 채팅방을 렌더링하며, 1:1 채팅의 경우 상대방 프로필을 실시간으로 구독합니다.
	 * $effect를 사용하여 profile 구독 및 반응성을 보장합니다.
	 */
	import Avatar from '$lib/components/user/avatar.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { userProfileStore } from '$lib/stores/user-profile.svelte';
	import { getPartnerUidFromSingleRoomId } from '$shared/chat.pure-functions';

	type Props = {
		/** 채팅방 ID */
		roomId: string;
		/** 채팅방 데이터 */
		roomData: any;
		/** 현재 활성화된 채팅방 ID */
		activeRoomId: string;
		/** 클릭 핸들러 */
		onclick: () => void;
	};

	let { roomId, roomData, activeRoomId, onclick }: Props = $props();

	// 1:1 채팅 여부 판단 (roomId가 'single-'로 시작하는지 확인)
	// Firebase 데이터의 type 필드보다 더 확실한 방법
	const isSingle = $derived(roomId.startsWith('single-'));

	// partnerUid 추출 (1:1 채팅의 경우)
	const partnerUid = $derived.by(() => {
		if (!isSingle) return '';
		return getPartnerUidFromSingleRoomId(roomId, authStore.user?.uid || '') || '';
	});

	// $effect를 사용하여 profile 실시간 구독
	// snippet 내의 side effect와 달리, 이 방법은 Svelte의 반응성 시스템과 제대로 작동합니다
	$effect(() => {
		if (isSingle && partnerUid) {
			userProfileStore.ensureSubscribed(partnerUid);
		}
	});

	// profile 데이터 가져오기 (캐시된 데이터)
	const profile = $derived(
		isSingle && partnerUid ? userProfileStore.getCachedProfile(partnerUid) : null
	);

	// 표시할 이름 (displayName 또는 fallback)
	const displayName = $derived(
		profile?.displayName || (partnerUid ? `@${partnerUid.slice(0, 6)}` : '')
	);

	// 활성 상태 확인
	const isActive = $derived(roomId === activeRoomId);
</script>

{#if isSingle}
	<!-- 1:1 채팅 -->
	<button type="button" class={`room-item ${isActive ? 'room-item--active' : ''}`} {onclick}>
		<!-- 상대방 아바타 -->
		<div class="room-avatar">
			<Avatar uid={partnerUid} size={48} />
		</div>

		<!-- 채팅방 정보 -->
		<div class="room-info">
			<div class="room-name">{displayName}</div>
			{#if roomData.lastMessage}
				<div class="room-last-message">{roomData.lastMessage}</div>
			{/if}
		</div>

		<!-- 읽지 않은 메시지 수 -->
		{#if roomData.newMessageCount > 0}
			<div class="room-badge">{roomData.newMessageCount}</div>
		{/if}
	</button>
{:else}
	<!-- 그룹/오픈 채팅 -->
	<button type="button" class={`room-item ${isActive ? 'room-item--active' : ''}`} {onclick}>
		<!-- 채팅방 아이콘 -->
		<div class="room-icon">
			{#if roomData.type === 'group'}
				👥
			{:else}
				🌐
			{/if}
		</div>

		<!-- 채팅방 정보 -->
		<div class="room-info">
			<div class="room-name">{roomData.name || roomData.roomId || roomId}</div>
			{#if roomData.lastMessage}
				<div class="room-last-message">{roomData.lastMessage}</div>
			{/if}
		</div>

		<!-- 읽지 않은 메시지 수 -->
		{#if roomData.newMessageCount > 0}
			<div class="room-badge">{roomData.newMessageCount}</div>
		{/if}
	</button>
{/if}

<style>
	@import 'tailwindcss' reference;

	/**
	 * 채팅방 아이템 (목록 항목)
	 */
	.room-item {
		@apply flex items-center gap-3;
		@apply w-full px-4 py-3;
		@apply border-b border-gray-100;
		@apply transition-colors duration-150;
		@apply cursor-pointer;
		@apply hover:bg-gray-50;
	}

	.room-item--active {
		@apply bg-blue-50 hover:bg-blue-100;
	}

	.room-icon {
		@apply flex items-center justify-center;
		@apply h-12 w-12;
		@apply rounded-full bg-gray-200;
		@apply text-2xl;
		@apply shrink-0;
	}

	.room-avatar {
		@apply shrink-0;
	}

	.room-info {
		@apply flex-1 overflow-hidden;
	}

	.room-name {
		@apply truncate font-semibold text-gray-900;
	}

	.room-last-message {
		@apply truncate text-sm text-gray-500;
	}

	.room-badge {
		@apply flex items-center justify-center;
		@apply h-6 min-w-6;
		@apply px-2;
		@apply rounded-full bg-red-500;
		@apply text-xs font-bold text-white;
		@apply shrink-0;
	}
</style>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
