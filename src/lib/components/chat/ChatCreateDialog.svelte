<script lang="ts">
	/**
	 * 통합 채팅방 생성 다이얼로그
	 *
	 * type prop에 따라 그룹 채팅방 또는 오픈 채팅방을 생성합니다.
	 * - type='group': 비공개 그룹 채팅방
	 * - type='open': 공개 오픈 채팅방
	 */
	import { createEventDispatcher } from 'svelte';
	import { Button } from '$lib/components/ui/button/index.js';
	import {
		Dialog,
		DialogContent,
		DialogDescription,
		DialogFooter,
		DialogHeader,
		DialogTitle
	} from '$lib/components/ui/dialog';
	import { authStore } from '$lib/stores/auth.svelte';
	import { ref, push, set } from 'firebase/database';
	import { rtdb } from '$lib/firebase';

	type ChatRoomType = 'group' | 'open';

	interface Props {
		open?: boolean;
		type: ChatRoomType;
		title?: string;
		description?: string;
	}

	let {
		open = $bindable(false),
		type,
		title = type === 'group' ? '그룹 채팅방 생성' : '오픈 채팅방 생성',
		description = type === 'group'
			? '그룹 채팅방 이름과 설명을 입력하세요.'
			: '누구나 참여할 수 있는 공개 채팅방을 만드세요.'
	}: Props = $props();

	const dispatch = createEventDispatcher<{
		created: { roomId: string };
		cancel: void;
	}>();

	let roomName = $state('');
	let roomDescription = $state('');
	let isCreating = $state(false);
	let errorMessage = $state('');
	let inputRef: HTMLInputElement | null = $state(null);

	// type에 따른 동적 설정
	const isGroupChat = $derived(type === 'group');
	const isOpenChat = $derived(type === 'open');
	const placeholderText = $derived(isGroupChat ? '예: 친구들 모임' : '예: 개발자 모임');
	const dialogClass = $derived(
		isGroupChat ? 'chat-create-dialog group' : 'chat-create-dialog open'
	);

	/**
	 * 폼 제출 핸들러 - 채팅방 생성
	 */
	async function handleSubmit(event: SubmitEvent) {
		event.preventDefault();

		const trimmedName = roomName.trim();
		if (!trimmedName) {
			errorMessage = '채팅방 이름을 입력해주세요.';
			return;
		}

		if (!authStore.user?.uid) {
			errorMessage = '로그인이 필요합니다.';
			return;
		}

		if (!rtdb) {
			errorMessage = '데이터베이스 연결 오류가 발생했습니다.';
			return;
		}

		isCreating = true;
		errorMessage = '';

		try {
			const currentUid = authStore.user.uid;
			const now = Date.now();

			// 1. chat-rooms에 새 채팅방 생성
			const chatRoomsRef = ref(rtdb, 'chat-rooms');
			const newRoomRef = push(chatRoomsRef);
			const roomId = newRoomRef.key;

			if (!roomId) {
				throw new Error('Failed to generate room ID');
			}

		// 채팅방 데이터 (type에 따라 동적 생성)
			// createdAt과 memberCount는 Cloud Functions에서 자동으로 설정됨
			const roomData: Record<string, unknown> = {
				name: trimmedName,
				description: roomDescription.trim() || '',
				type: type,
				open: isOpenChat, // 그룹챗은 비공개, 오픈챗은 공개
				owner: currentUid // 채팅방 소유자 UID
			};

			// type에 따른 추가 필드
			if (isGroupChat) {
				roomData.groupListOrder = -now; // 최신순 정렬을 위한 음수 타임스탬프
			} else {
				roomData.openListOrder = -now;
				roomData.memberCount = 1; // 생성자 포함
			}

			await set(newRoomRef, roomData);

			// 2. 생성자를 chat-joins에 추가
			const joinRef = ref(rtdb, `chat-joins/${currentUid}/${roomId}`);
			const joinData: Record<string, unknown> = {
				roomId,
				roomType: type,
				roomName: trimmedName,
				joinedAt: now,
				lastMessageAt: now
			};

			// type에 따른 추가 필드
			if (isGroupChat) {
				joinData.groupListOrder = -now;
			} else {
				joinData.openListOrder = -now;
			}

			await set(joinRef, joinData);

			// console.log(`✅ ${isGroupChat ? '그룹' : '오픈'} 채팅방 생성 완료:`, {
			// 	roomId,
			// 	roomData
			// });

			// 폼 초기화
			roomName = '';
			roomDescription = '';

			// 이벤트 발생 및 다이얼로그 닫기
			dispatch('created', { roomId });
			open = false;
		} catch (error) {
			console.error(`❌ ${isGroupChat ? '그룹' : '오픈'} 채팅방 생성 실패:`, error);
			errorMessage = '채팅방 생성에 실패했습니다. 다시 시도해주세요.';
		} finally {
			isCreating = false;
		}
	}

	/**
	 * 취소 버튼 핸들러
	 */
	function handleCancel() {
		roomName = '';
		roomDescription = '';
		errorMessage = '';
		dispatch('cancel');
		open = false;
	}

	// 다이얼로그 열릴 때 입력 필드에 포커스
	$effect(() => {
		if (open && inputRef) {
			requestAnimationFrame(() => {
				inputRef?.focus();
			});
		}
	});

	// 다이얼로그가 닫힐 때 폼 초기화
	$effect(() => {
		if (!open) {
			roomName = '';
			roomDescription = '';
			errorMessage = '';
		}
	});
</script>

<Dialog bind:open>
	<DialogContent class={dialogClass}>
		<DialogHeader>
			<DialogTitle>{title}</DialogTitle>
			<DialogDescription>{description}</DialogDescription>
		</DialogHeader>

		<form class="flex flex-col gap-4" onsubmit={handleSubmit}>
			<!-- 채팅방 이름 -->
			<label class="form-label flex flex-col gap-2">
				<span class="label-text">채팅방 이름 <span class="text-red-500">*</span></span>
				<input
					bind:this={inputRef}
					bind:value={roomName}
					type="text"
					class="form-input"
					placeholder={placeholderText}
					maxlength="50"
					required
					disabled={isCreating}
					onkeydown={(e) => e.stopPropagation()}
				/>
				<span class="hint-text">최대 50자</span>
			</label>

			<!-- 채팅방 설명 -->
			<label class="form-label flex flex-col gap-2">
				<span class="label-text">채팅방 설명 (선택)</span>
				<textarea
					bind:value={roomDescription}
					class="form-textarea"
					placeholder="채팅방에 대한 간단한 설명을 입력하세요"
					maxlength="200"
					rows="3"
					disabled={isCreating}
					onkeydown={(e) => e.stopPropagation()}
				></textarea>
				<span class="hint-text">최대 200자</span>
			</label>

			<!-- 오픈 채팅방 안내 (오픈 타입일 때만 표시) -->
			{#if isOpenChat}
				<div class="info-box">
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
							d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"
						/>
					</svg>
					<span>오픈 채팅방은 누구나 참여할 수 있는 공개 채팅방입니다.</span>
				</div>
			{/if}

			<!-- 에러 메시지 -->
			{#if errorMessage}
				<div class="error-message">
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
							d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
						/>
					</svg>
					<span>{errorMessage}</span>
				</div>
			{/if}

			<DialogFooter class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
				<Button type="button" variant="ghost" class="w-full sm:w-auto" onclick={handleCancel} disabled={isCreating}>
					취소
				</Button>
				<Button type="submit" class="w-full sm:w-auto" disabled={isCreating}>
					{isCreating ? '생성 중...' : '생성하기'}
				</Button>
			</DialogFooter>
		</form>
	</DialogContent>
</Dialog>

<style>
	@import 'tailwindcss' reference;

	.chat-create-dialog :global(.form-label) {
		@apply text-sm font-semibold text-gray-700;
	}

	.chat-create-dialog :global(.label-text) {
		@apply text-sm font-semibold text-gray-700;
	}

	.chat-create-dialog :global(.form-input) {
		@apply w-full rounded-xl border border-gray-300 px-4 py-3 text-base text-gray-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100;
	}

	.chat-create-dialog :global(.form-textarea) {
		@apply w-full rounded-xl border border-gray-300 px-4 py-3 text-base text-gray-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100;
	}

	.chat-create-dialog :global(.hint-text) {
		@apply text-xs text-gray-500;
	}

	.info-box {
		@apply flex items-start gap-2 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700;
	}

	.error-message {
		@apply flex items-center gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600;
	}
</style>
