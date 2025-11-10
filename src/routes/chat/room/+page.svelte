<script lang="ts">
	/**
	 * 채팅방 페이지
	 *
	 * GET 파라미터로 전달된 uid 값이 있으면 1:1 채팅방으로 동작합니다.
	 * 채팅 상대의 프로필을 실시간으로 구독하고 메시지 목록 및 입력창을 제공합니다.
	 */

	import { page } from '$app/stores';
	import DatabaseListView from '$lib/components/DatabaseListView.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { userProfileStore } from '$lib/stores/user-profile.svelte';
	import { pushData } from '$lib/stores/database.svelte';
	import { getLocale } from '$lib/paraglide/runtime.js';
	import { m } from '$lib/paraglide/messages-proxy';

	// GET 파라미터 추출
	const uidParam = $derived.by(() => $page.url.searchParams.get('uid') ?? '');
	const roomIdParam = $derived.by(() => $page.url.searchParams.get('roomId') ?? '');

	// 1:1 채팅 여부
	const isDirectChat = $derived.by(() => Boolean(uidParam));

	// 채팅에 사용할 roomId 계산 (1:1이면 두 UID를 정렬해서 고정 키 생성)
	function buildDirectRoomId(a: string, b: string) {
		return `direct-${[a, b].sort().join('-')}`;
	}

	const activeRoomId = $derived.by(() => {
		if (roomIdParam) return roomIdParam;
		if (isDirectChat && authStore.user?.uid && uidParam) {
			return buildDirectRoomId(authStore.user.uid, uidParam);
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

	const targetProfile = $derived(userProfileStore.getCachedProfile(uidParam));
	const targetProfileLoading = $derived(userProfileStore.isLoading(uidParam));
	const targetProfileError = $derived(userProfileStore.getError(uidParam));

	// 채팅 상대 표시 이름
	const targetDisplayName = $derived.by(() => {
		if (targetProfile?.displayName) return targetProfile.displayName;
		if (uidParam) return `@${uidParam.slice(0, 6)}`;
		return 'Chat Partner';
	});

	// 작성 중인 메시지
	let composerText = $state('');
	let isSending = $state(false);
	let sendError = $state<string | null>(null);

	// 타임스탬프 포맷터
	function formatTimestamp(value?: number | null) {
		if (!value) return '';
		const currentLocale = getLocale();
		const locale = currentLocale === 'ko' ? 'ko-KR'
			: currentLocale === 'ja' ? 'ja-JP'
			: currentLocale === 'zh' ? 'zh-CN'
			: 'en-US';
		return new Date(value).toLocaleString(locale, {
			year: 'numeric',
			month: 'short',
			day: '2-digit',
			hour: '2-digit',
			minute: '2-digit'
		});
	}

	// 메시지 전송 처리
	async function handleSendMessage(event: SubmitEvent) {
		event.preventDefault();
		if (isSending) return;
		if (!composerText.trim()) return;
		if (!authStore.user?.uid) {
			sendError = 'Please sign in to send messages.';
			return;
		}
		if (!activeRoomId) {
			sendError = 'Chat room is not ready.';
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
			sendError = result.error ?? 'Failed to send message.';
		} else {
			composerText = '';
		}

		isSending = false;
	}

	// 메시지 작성 가능 여부
	const composerDisabled = $derived.by(() => !authStore.isAuthenticated || !activeRoomId);

	// 발신자 라벨
	function resolveSenderLabel(senderUid?: string | null) {
		if (!senderUid) return 'Unknown user';
		if (senderUid === authStore.user?.uid) return 'You';
		if (senderUid === uidParam && targetDisplayName) return targetDisplayName;
		return senderUid.slice(0, 10);
	}
</script>

<svelte:head>
	<title>{m.page_title_chat()}</title>
</svelte:head>

<div class="chat-room-page">
	<header class="chat-room-header">
		<div>
			<p class="chat-room-label">{isDirectChat ? 'Direct Chat' : 'Chat Room'}</p>
			<h1 class="chat-room-title">
				{#if isDirectChat && uidParam}
					{targetDisplayName}
				{:else if roomIdParam}
					Room: {roomIdParam}
				{:else}
					Chat Overview
				{/if}
			</h1>
			<p class="chat-room-subtitle">
				{#if !authStore.isAuthenticated}
					Please sign in to start chatting.
				{:else if isDirectChat && !uidParam}
					Provide a uid query parameter to open a direct chat.
				{:else if targetProfileLoading}
					Loading the participant profile...
				{:else if targetProfileError}
					Failed to load participant profile.
				{:else if isDirectChat}
					You are chatting with {targetDisplayName}.
				{:else if roomIdParam}
					Room ID {roomIdParam} is ready.
				{:else}
					Select a conversation to begin.
				{/if}
			</p>
		</div>
		{#if uidParam}
			<div class="chat-room-partner">
				<Avatar uid={uidParam} size={64} class="shadow-sm" />
				<div>
					<p class="partner-name">{targetDisplayName}</p>
					<p class="partner-uid">{uidParam}</p>
				</div>
			</div>
		{/if}
	</header>

	{#if !activeRoomId}
		<section class="chat-room-empty">
			<p class="empty-title">Chat room is not ready.</p>
			<p class="empty-subtitle">
				Add ?uid=TARGET_UID or ?roomId=ROOM_KEY to the URL to open a conversation.
			</p>
		</section>
	{:else}
		<section class="chat-room-content">
			<div class="message-list-section">
				{#if canRenderMessages}
					{#key roomOrderPrefix}
						<DatabaseListView
							path={messagePath}
							pageSize={25}
							orderBy={roomOrderField}
							orderPrefix={roomOrderPrefix}
							threshold={280}
							reverse={true}
						>
							{#snippet item(itemData: { key: string; data: any })}
								{@const message = itemData.data ?? {}}
								{@const mine = message.senderUid === authStore.user?.uid}
								<article class={`message-item ${mine ? 'mine' : ''}`}>
									<div class="message-avatar">
										<Avatar uid={message.senderUid} size={40} class="shadow-sm" />
									</div>
									<div class="message-body">
										<div class="message-meta">
											<span class="message-sender">{resolveSenderLabel(message.senderUid)}</span>
											<span class="message-time">{formatTimestamp(message.createdAt)}</span>
										</div>
										<p class="message-text">
											{message.text || ''}
										</p>
									</div>
								</article>
							{/snippet}

							{#snippet loading()}
								<div class="message-placeholder">Loading messages...</div>
							{/snippet}

							{#snippet empty()}
								<div class="message-placeholder">No messages yet. Say hello!</div>
							{/snippet}

							{#snippet error(errorMessage: string | null)}
								<div class="message-error">
									<p>Failed to load messages.</p>
									<p>{errorMessage ?? 'Unknown error.'}</p>
								</div>
							{/snippet}

							{#snippet loadingMore()}
								<div class="message-placeholder subtle">Loading more...</div>
							{/snippet}

							{#snippet noMore()}
								<div class="message-placeholder subtle">You are up to date.</div>
							{/snippet}
						</DatabaseListView>
					{/key}
				{:else}
					<div class="message-placeholder">Preparing the message stream...</div>
				{/if}
			</div>

			<form class="message-composer" onsubmit={handleSendMessage}>
				<input
					type="text"
					class="composer-input"
					placeholder="Write a message..."
					bind:value={composerText}
					disabled={composerDisabled || isSending}
				/>
				<button
					type="submit"
					class="composer-button cursor-pointer"
					disabled={composerDisabled || isSending || !composerText.trim()}
				>
					{isSending ? 'Sending...' : 'Send'}
				</button>
			</form>

			{#if sendError}
				<p class="composer-error">{sendError}</p>
			{/if}
		</section>
	{/if}
</div>

<style>
	.chat-room-page {
		max-width: 960px;
		margin: 0 auto;
		padding: 2rem 1rem 4rem;
		display: flex;
		flex-direction: column;
		gap: 1.5rem;
	}

	.chat-room-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 1rem;
		border: 1px solid #e5e7eb;
		border-radius: 1rem;
		padding: 1.5rem;
		background: #ffffff;
		box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
	}

	.chat-room-label {
		font-size: 0.9rem;
		font-weight: 600;
		color: #6366f1;
		text-transform: uppercase;
		letter-spacing: 0.08em;
		margin-bottom: 0.2rem;
	}

	.chat-room-title {
		font-size: 1.8rem;
		font-weight: 700;
		color: #111827;
		margin: 0;
	}

	.chat-room-subtitle {
		margin-top: 0.35rem;
		color: #6b7280;
		font-size: 0.95rem;
	}

	.chat-room-partner {
		display: flex;
		align-items: center;
		gap: 0.75rem;
		padding: 0.75rem 1rem;
		border-radius: 999px;
		background: #f9fafb;
	}

	.partner-name {
		font-weight: 600;
		color: #111827;
		margin: 0;
	}

	.partner-uid {
		font-size: 0.85rem;
		color: #6b7280;
		margin: 0;
		word-break: break-all;
	}

	.chat-room-empty {
		border: 1px dashed #d1d5db;
		border-radius: 1rem;
		padding: 2rem;
		text-align: center;
		background: #fdfdfd;
	}

	.empty-title {
		font-size: 1.25rem;
		font-weight: 600;
		color: #111827;
		margin-bottom: 0.5rem;
	}

	.empty-subtitle {
		color: #6b7280;
	}

	.chat-room-content {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.message-list-section {
		border: 1px solid #e5e7eb;
		border-radius: 1rem;
		background: #ffffff;
		padding: 1rem;
		min-height: 320px;
		max-height: 60vh;
		overflow: auto;
	}

	.message-item {
		display: flex;
		gap: 0.75rem;
		padding: 0.75rem 0.5rem;
		border-bottom: 1px solid rgba(229, 231, 235, 0.6);
	}

	.message-item:last-child {
		border-bottom: none;
	}

	.message-item.mine .message-body {
		background: #eef2ff;
	}

	.message-avatar {
		flex-shrink: 0;
	}

	.message-body {
		flex: 1;
		background: #f9fafb;
		padding: 0.75rem;
		border-radius: 0.75rem;
		box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04);
	}

	.message-meta {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 0.25rem;
	}

	.message-sender {
		font-weight: 600;
		color: #111827;
	}

	.message-time {
		font-size: 0.8rem;
		color: #9ca3af;
	}

	.message-text {
		margin: 0;
		font-size: 0.95rem;
		color: #1f2937;
		white-space: pre-wrap;
		word-break: break-word;
	}

	.message-placeholder {
		text-align: center;
		color: #6b7280;
		padding: 1.5rem 0;
	}

	.message-placeholder.subtle {
		color: #9ca3af;
		font-size: 0.85rem;
	}

	.message-error {
		text-align: center;
		color: #dc2626;
		padding: 1rem 0;
	}

	.message-composer {
		display: flex;
		gap: 0.75rem;
	}

	.composer-input {
		flex: 1;
		padding: 0.85rem 1rem;
		border-radius: 999px;
		border: 1px solid #d1d5db;
		font-size: 1rem;
		background: #ffffff;
	}

	.composer-input:disabled {
		background: #f3f4f6;
	}

	.composer-button {
		border: none;
		padding: 0 1.5rem;
		border-radius: 999px;
		background: #111827;
		color: #ffffff;
		font-weight: 600;
		transition: background 0.2s;
	}

	.composer-button:disabled {
		background: #9ca3af;
		cursor: not-allowed;
	}

	.composer-error {
		color: #dc2626;
		font-size: 0.9rem;
		margin: 0;
	}

	@media (max-width: 640px) {
		.chat-room-header {
			flex-direction: column;
			align-items: flex-start;
		}

		.chat-room-partner {
			width: 100%;
			justify-content: center;
		}

		.message-item {
			flex-direction: column;
		}

		.message-meta {
			flex-direction: column;
			align-items: flex-start;
			gap: 0.2rem;
		}

		.message-composer {
			flex-direction: column;
		}

		.composer-button {
			width: 100%;
			padding: 0.9rem;
		}
	}
</style>
