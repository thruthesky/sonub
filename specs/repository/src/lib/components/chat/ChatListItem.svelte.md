---
title: ChatListItem.svelte
type: component
path: src/lib/components/chat/ChatListItem.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/chat/ChatListItem.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 채팅 목록 아이템 컴포넌트
	 *
	 * /chat/list 페이지에서 사용되는 채팅방 목록 아이템입니다.
	 * 1:1 채팅의 경우 상대방 프로필을 실시간으로 구독하여 displayName과 photoUrl을 표시합니다.
	 */

	import Avatar from '$lib/components/user/avatar.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { userProfileStore } from '$lib/stores/user-profile.svelte';
	import { m } from '$lib/paraglide/messages';
	import { formatLongDate } from '$lib/functions/date.functions';
	import { resolveRoomTypeLabel } from '$lib/functions/chat.functions';

	type Props = {
		/** 채팅방 참여 데이터 */
		join: Record<string, unknown>;
		/** 채팅방 ID */
		roomId: string;
		/** 채팅방 클릭 핸들러 */
		onclick: () => void;
		/** 핀 토글 핸들러 */
		onTogglePin: (event: MouseEvent) => void;
	};

	let { join, roomId, onclick, onTogglePin }: Props = $props();

	// 채팅방 타입 및 기본 정보
	const roomType = $derived((join.roomType ?? join.type ?? 'single').toString());
	const isSingle = $derived(roomType.toLowerCase().includes('single'));

	// 상대방 UID (1:1 채팅의 경우)
	const partnerUid = $derived.by(() => {
		if (!isSingle) return null;
		return typeof join.partnerUid === 'string' ? join.partnerUid
			: typeof join.targetUid === 'string' ? join.targetUid
			: null;
	});

	// $effect를 사용하여 profile 실시간 구독 (1:1 채팅의 경우만)
	$effect(() => {
		if (isSingle && partnerUid) {
			userProfileStore.ensureSubscribed(partnerUid);
		}
	});

	// profile 데이터 가져오기 (캐시된 데이터)
	const profile = $derived(
		isSingle && partnerUid ? userProfileStore.getCachedProfile(partnerUid) : null
	);

	// 채팅방 제목 계산
	const roomTitle = $derived.by(() => {
		// 1:1 채팅: 상대방 displayName 우선 표시
		if (isSingle && profile?.displayName) {
			return profile.displayName;
		}

		// chat-joins에 저장된 이름들 확인
		if (typeof join.roomTitle === 'string' && join.roomTitle.trim()) return join.roomTitle;
		if (typeof join.roomName === 'string' && join.roomName.trim()) return join.roomName;
		if (typeof join.title === 'string' && join.title.trim()) return join.title;
		if (typeof join.displayName === 'string' && join.displayName.trim()) return join.displayName;
		if (typeof join.partnerDisplayName === 'string' && join.partnerDisplayName.trim())
			return join.partnerDisplayName;

		// 1:1 채팅: partnerUid fallback
		if (partnerUid) {
			return `@${partnerUid.slice(0, 8)}`;
		}

		// 최종 fallback
		return roomId || m.chatChatRoom();
	});

	// 마지막 메시지
	const lastMessage = $derived.by(() => {
		if (typeof join.lastMessageText === 'string' && join.lastMessageText.trim())
			return join.lastMessageText;
		if (typeof join.lastMessage === 'string' && join.lastMessage.trim()) return join.lastMessage;
		if (typeof join.preview === 'string') return join.preview;
		return '';
	});

	// 타임스탬프
	const timestamp = $derived(
		Number(join.lastMessageAt ?? join.updatedAt ?? join.joinedAt ?? 0) || null
	);

	// 읽지 않은 메시지 수
	const unreadCount = $derived(
		Number(join.newMessageCount ?? join.unreadCount ?? join.unread ?? 0) || 0
	);

	// 핀 상태
	const isPinned = $derived(join.pin === true);
</script>

<div class="chat-list-item">
	<button type="button" class="chat-list-item__button" {onclick}>
		{#if partnerUid}
			<Avatar uid={partnerUid} size={48} class="shadow-sm" />
		{:else}
			<div class="chat-list-item__fallback-avatar">
				{roomTitle.slice(0, 2)}
			</div>
		{/if}

		<div class="chat-list-item__content">
			<div class="chat-list-item__header">
				<span class="chat-list-item__type-badge">
					{resolveRoomTypeLabel(roomType)}
				</span>
				<span class="chat-list-item__room-id">#{roomId}</span>
				{#if unreadCount > 0}
					<span class="chat-list-item__unread-badge">
						{unreadCount}
					</span>
				{/if}
			</div>

			<h2 class="chat-list-item__title">{roomTitle}</h2>

			<p class="chat-list-item__message">
				<span class="chat-list-item__message-label">{m.chatLastMessageLabel()}:</span>
				<span class="chat-list-item__message-text">{lastMessage || m.chatNoMessages()}</span>
			</p>

			{#if timestamp}
				<p class="chat-list-item__timestamp">{formatLongDate(timestamp)}</p>
			{/if}
		</div>
	</button>

	<div class="chat-list-item__actions">
		<!-- 핀 버튼 -->
		<button
			type="button"
			onclick={onTogglePin}
			class="chat-list-item__pin-button"
			title={isPinned ? '핀 해제' : '핀 설정'}
		>
			<span class="text-xl">{isPinned ? '📌' : '📍'}</span>
		</button>
		<span class="chat-list-item__action-label">{m.chatOpenRoom()}</span>
	</div>
</div>

<style>
	@import 'tailwindcss' reference;

	.chat-list-item {
		@apply flex w-full items-start border-b border-gray-100;
	}

	.chat-list-item__button {
		@apply flex flex-1 items-start gap-4 p-4 text-left;
		@apply transition hover:bg-gray-50;
		@apply focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500;
	}

	.chat-list-item__fallback-avatar {
		@apply flex h-12 w-12 items-center justify-center;
		@apply rounded-full bg-gray-100;
		@apply text-sm font-semibold text-gray-600;
	}

	.chat-list-item__content {
		@apply flex-1 space-y-1;
	}

	.chat-list-item__header {
		@apply flex flex-wrap items-center gap-x-2 text-sm text-gray-500;
	}

	.chat-list-item__type-badge {
		@apply rounded-full bg-gray-100 px-2 py-0.5;
		@apply text-xs font-semibold uppercase tracking-wide text-gray-600;
	}

	.chat-list-item__room-id {
		@apply text-xs text-gray-400;
	}

	.chat-list-item__unread-badge {
		@apply rounded-full bg-blue-600 px-2 py-0.5;
		@apply text-xs font-semibold text-white;
	}

	.chat-list-item__title {
		@apply text-lg font-semibold text-gray-900;
	}

	.chat-list-item__message {
		@apply text-sm text-gray-500;
	}

	.chat-list-item__message-label {
		@apply font-medium text-gray-600;
	}

	.chat-list-item__message-text {
		@apply ml-1 line-clamp-1;
	}

	.chat-list-item__timestamp {
		@apply text-xs text-gray-400;
	}

	.chat-list-item__actions {
		@apply flex flex-col items-center gap-2 p-4;
	}

	.chat-list-item__pin-button {
		@apply rounded-full p-1.5;
		@apply transition hover:bg-gray-100;
		@apply focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500;
	}

	.chat-list-item__action-label {
		@apply text-sm font-medium text-blue-600;
	}
</style>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
