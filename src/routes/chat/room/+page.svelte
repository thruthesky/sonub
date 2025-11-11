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
	import { m } from '$lib/paraglide/messages';
	import { buildSingleRoomId } from '$lib/functions/chat.functions';
	import { formatLongDate } from '$lib/functions/date.functions';
	import { tick } from 'svelte';

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
</script>

<svelte:head>
	<title>{m.pageTitleChat()}</title>
</svelte:head>

<div class="mx-auto flex max-w-[960px] flex-col gap-6 px-4 py-8 pb-16">
	<header
		class="chat-room-header flex items-center justify-between gap-4 p-6 sm:flex-col sm:items-start"
	>
		<div>
			<p class="chat-room-label">{isSingleChat ? m.chatSingleChat() : m.chatChatRoom()}</p>
			<h1 class="chat-room-title">
				{#if isSingleChat && uidParam}
					{targetDisplayName}
				{:else if roomIdParam}
					{m.chatRoom()} {roomIdParam}
				{:else}
					{m.chatOverview()}
				{/if}
			</h1>
			<p class="chat-room-subtitle mt-1.5">
				{#if !authStore.isAuthenticated}
					{m.chatSignInRequired()}
				{:else if isSingleChat && !uidParam}
					{m.chatProvideUid()}
				{:else if targetProfileLoading}
					{m.chatLoadingProfile()}
				{:else if targetProfileError}
					{m.chatLoadProfileFailed()}
				{:else if isSingleChat}
					{m.chatChattingWith({ name: targetDisplayName })}
				{:else if roomIdParam}
					{m.chatRoomReady({ roomId: roomIdParam })}
				{:else}
					{m.chatSelectConversation()}
				{/if}
			</p>
		</div>
		{#if uidParam}
			<div class="chat-room-partner flex items-center gap-3 px-4 py-3 sm:w-full sm:justify-center">
				<Avatar uid={uidParam} size={64} class="shadow-sm" />
				<div>
					<p class="partner-name">{targetDisplayName}</p>
					<p class="partner-uid">{uidParam}</p>
				</div>
			</div>
		{/if}
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

<style>
	@import 'tailwindcss' reference;

	/* 채팅방 헤더 스타일 */
	.chat-room-header {
		@apply rounded-2xl border border-gray-200 bg-white shadow-[0_10px_25px_rgba(15,23,42,0.06)];
	}

	.chat-room-label {
		@apply mb-0.5 text-sm font-semibold tracking-wider text-indigo-500 uppercase;
	}

	.chat-room-title {
		@apply m-0 text-[1.8rem] font-bold text-gray-900;
	}

	.chat-room-subtitle {
		@apply text-[0.95rem] text-gray-500;
	}

	.chat-room-partner {
		@apply rounded-full bg-gray-50;
	}

	.partner-name {
		@apply m-0 font-semibold text-gray-900;
	}

	.partner-uid {
		@apply m-0 text-sm break-all text-gray-500;
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
