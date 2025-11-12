<script lang="ts">
	/**
	 * 그룹 채팅방 생성 다이얼로그
	 *
	 * 그룹 채팅방 이름과 설명을 입력받아 Firebase RTDB에 채팅방을 생성합니다.
	 * 생성 후 자동으로 채팅방으로 이동합니다.
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
	import { ref, push, set, serverTimestamp } from 'firebase/database';
	import { rtdb } from '$lib/firebase';

	interface Props {
		open?: boolean;
		title?: string;
		description?: string;
	}

	let {
		open = $bindable(false),
		title = '그룹 채팅방 생성',
		description = '그룹 채팅방 이름과 설명을 입력하세요.'
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

	/**
	 * 폼 제출 핸들러 - 그룹 채팅방 생성
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

			// 채팅방 데이터
			const roomData = {
				name: trimmedName,
				description: roomDescription.trim() || '',
				type: 'group',
				createdAt: now,
				createdBy: currentUid,
				open: false, // 그룹챗은 비공개
				groupListOrder: -now // 최신순 정렬을 위한 음수 타임스탬프
			};

			await set(newRoomRef, roomData);

			// 2. 생성자를 chat-joins에 추가
			const joinRef = ref(rtdb, `chat-joins/${currentUid}/${roomId}`);
			const joinData = {
				roomId,
				roomType: 'group',
				roomTitle: trimmedName,
				joinedAt: now,
				groupListOrder: -now,
				lastMessageAt: now
			};

			await set(joinRef, joinData);

			console.log('✅ 그룹 채팅방 생성 완료:', { roomId, roomData });

			// 폼 초기화
			roomName = '';
			roomDescription = '';

			// 이벤트 발생 및 다이얼로그 닫기
			dispatch('created', { roomId });
			open = false;
		} catch (error) {
			console.error('❌ 그룹 채팅방 생성 실패:', error);
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
	<DialogContent class="group-chat-create-dialog">
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
					placeholder="예: 친구들 모임"
					maxlength="50"
					required
					disabled={isCreating}
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
				></textarea>
				<span class="hint-text">최대 200자</span>
			</label>

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

	.group-chat-create-dialog :global(.form-label) {
		@apply text-sm font-semibold text-gray-700;
	}

	.group-chat-create-dialog :global(.label-text) {
		@apply text-sm font-semibold text-gray-700;
	}

	.group-chat-create-dialog :global(.form-input) {
		@apply w-full rounded-xl border border-gray-300 px-4 py-3 text-base text-gray-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100;
	}

	.group-chat-create-dialog :global(.form-textarea) {
		@apply w-full rounded-xl border border-gray-300 px-4 py-3 text-base text-gray-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100;
	}

	.group-chat-create-dialog :global(.hint-text) {
		@apply text-xs text-gray-500;
	}

	.error-message {
		@apply flex items-center gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600;
	}
</style>
