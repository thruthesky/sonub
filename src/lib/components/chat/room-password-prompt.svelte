<script lang="ts">
	/**
	 * 채팅방 비밀번호 입력 모달 컴포넌트 (Firestore)
	 *
	 * 비밀번호가 설정된 채팅방에 입장하려는 사용자가 비밀번호를 입력하는 Dialog 모달입니다.
	 *
	 * 주요 기능:
	 * 1. 비밀번호 입력 필드
	 * 2. try 경로에 비밀번호 저장: chats/{roomId}/password-tries/{uid}
	 * 3. 5초 동안 매초 members 확인: chats/{roomId}/members/{uid}
	 * 4. Cloud Functions가 비밀번호 검증 후 members에 추가
	 * 5. 성공 시 invalidate() 호출하여 SvelteKit 데이터 재로드
	 * 6. 실패 시 에러 메시지 표시
	 * 7. 카운트다운 표시 (5초)
	 *
	 * @prop roomId - 채팅방 ID
	 * @prop open - Dialog 열림/닫힘 상태
	 * @prop onSuccess - 비밀번호 검증 성공 시 콜백
	 * @prop onCancel - 취소 시 콜백
	 */

	import {
		Dialog,
		DialogContent,
		DialogHeader,
		DialogTitle,
		DialogDescription
	} from '$lib/components/ui/dialog';
	import { Input } from '$lib/components/ui/input';
	import { Button } from '$lib/components/ui/button';
	import { toast } from 'svelte-sonner';
	import { db } from '$lib/firebase';
	import { doc, setDoc, onSnapshot } from 'firebase/firestore';
	import { authStore } from '$lib/stores/auth.svelte';
	import { invalidate } from '$app/navigation';
	import { m } from '$lib/paraglide/messages';

	interface Props {
		roomId: string;
		open: boolean;
		onSuccess: () => void;
		onCancel: () => void;
	}

	let { roomId, open = $bindable(), onSuccess, onCancel }: Props = $props();

	// 상태 변수
	let password = $state('');
	let isVerifying = $state(false);
	let countdown = $state(5);

	/**
	 * 입력 필드에서 공백 입력 시 Dialog가 닫히는 것을 방지합니다.
	 * Dialog 컴포넌트가 Space 키 이벤트를 전역으로 처리하지 않도록
	 * 해당 키 입력의 전파만 차단합니다.
	 */
	function handlePasswordKeyDown(event: KeyboardEvent) {
		if (
			event.code === 'Space' ||
			event.key === ' ' ||
			event.key === 'Spacebar' ||
			event.key === 'Enter'
		) {
			event.stopPropagation();
		}
	}

	/**
	 * 비밀번호 제출 핸들러 (Firestore)
	 *
	 * 로직:
	 * 1. try 경로에 비밀번호 저장: chats/{roomId}/password-tries/{uid}
	 * 2. waitForVerification() 호출하여 5초 동안 members 확인
	 * 3. 성공 시:
	 *    - 성공 토스트 표시
	 *    - invalidate('chat:room') 호출하여 SvelteKit 데이터 재로드
	 *    - onSuccess() 콜백 호출
	 * 4. 실패 시:
	 *    - 에러 토스트 표시
	 *    - 비밀번호 입력 필드 초기화
	 */
	async function handleSubmit() {
		if (!password || !authStore.user?.uid) return;

		isVerifying = true;
		countdown = 5;

		try {
			// 1. try 경로에 비밀번호 저장 (Firestore)
			const tryDocRef = doc(db!, `chats/${roomId}/password-tries/${authStore.user.uid}`);
			await setDoc(tryDocRef, {
				password: password,
				timestamp: Date.now()
			});

			// 2. 5초 동안 매초 members 확인
			const verified = await waitForVerification(roomId, authStore.user.uid);

			if (verified) {
				// 3. 검증 성공
				toast.success(m.chatPasswordVerifySuccess());
				await invalidate('chat:room'); // SvelteKit 데이터 재로드
				onSuccess();
			} else {
				// 4. 검증 실패 (5초 타임아웃)
				toast.error(m.chatPasswordIncorrect());
				password = '';
			}
		} catch (error) {
			console.error('❌ 비밀번호 검증 에러:', error);
			toast.error(m.chatPasswordVerifyFailure());
		} finally {
			isVerifying = false;
		}
	}

	/**
	 * 비밀번호 검증 대기 함수 (Firestore)
	 *
	 * 5초 동안 매초 members 경로를 확인하여 Cloud Functions가
	 * 비밀번호 검증 후 members에 추가했는지 확인합니다.
	 *
	 * @param roomId - 채팅방 ID
	 * @param uid - 사용자 UID
	 * @returns Promise<boolean> - 검증 성공 여부
	 */
	async function waitForVerification(roomId: string, uid: string): Promise<boolean> {
		return new Promise((resolve) => {
			const memberDocRef = doc(db!, `chats/${roomId}/members/${uid}`);
			let intervalId: any;
			let timeoutId: any;
			let unsubscribe: (() => void) | null = null;

			// 매초 카운트다운
			intervalId = setInterval(() => {
				countdown--;
			}, 1000);

			// 5초 타임아웃
			timeoutId = setTimeout(() => {
				clearInterval(intervalId);
				if (unsubscribe) unsubscribe();
				resolve(false);
			}, 5000);

			// members 경로 실시간 확인 (Firestore onSnapshot)
			unsubscribe = onSnapshot(memberDocRef, (snapshot) => {
				if (snapshot.exists() && snapshot.data()?.member === true) {
					// 검증 성공: members에 추가됨
					clearInterval(intervalId);
					clearTimeout(timeoutId);
					if (unsubscribe) unsubscribe();
					resolve(true);
				}
			});
		});
	}

	/**
	 * 취소 핸들러
	 */
	function handleCancel() {
		if (isVerifying) return; // 검증 중에는 취소 불가
		password = '';
		onCancel();
	}
</script>

<Dialog bind:open>
	<DialogContent class="modal-content">
		<DialogHeader>
			<DialogTitle class="modal-title">
				{m.chatPasswordSettings()}
			</DialogTitle>
			<DialogDescription class="modal-description">
				{m.chatPasswordRequired()}
			</DialogDescription>
		</DialogHeader>

		<form onsubmit={(e) => { e.preventDefault(); handleSubmit(); }} class="modal-form">
			<!-- 비밀번호 입력 필드 -->
			<Input
				type="password"
				placeholder={m.chatPasswordEnterPrompt()}
				bind:value={password}
				disabled={isVerifying}
				class="password-input"
				onkeydown={handlePasswordKeyDown}
			/>

			<!-- 검증 중 카운트다운 표시 -->
			{#if isVerifying}
				<p class="countdown-text">
					{m.chatPasswordVerifying({ countdown })}
				</p>
			{/if}

			<!-- 버튼 영역 -->
			<div class="button-group">
				<button
					type="button"
					class="cancel-text-button"
					onclick={handleCancel}
					disabled={isVerifying}
				>
					{m.commonCancel()}
				</button>
				<Button type="submit" disabled={isVerifying || !password} class="confirm-button">
					{m.commonConfirm()}
				</Button>
			</div>
		</form>
	</DialogContent>
</Dialog>

<style lang="postcss">
	@import 'tailwindcss' reference;

	/**
	 * 비밀번호 입력 모달 스타일링
	 *
	 * Layout: Tailwind CSS inline classes 사용
	 * Styling: @apply를 통한 Tailwind CSS utility classes 적용
	 */

	.modal-content {
		@apply sm:max-w-md;
	}

	.modal-title {
		@apply text-lg font-semibold;
	}

	.modal-description {
		@apply text-sm text-gray-600;
	}

	.modal-form {
		@apply space-y-4 pt-4;
	}

	.password-input {
		@apply w-full;
	}

	.countdown-text {
		@apply text-sm text-gray-600 text-center;
	}

	.button-group {
		@apply flex items-center justify-end gap-4;
	}

	.cancel-text-button {
		@apply bg-transparent border-0 p-0 text-sm font-medium text-gray-500 transition-colors cursor-pointer;
	}

	.cancel-text-button:hover {
		@apply text-gray-900;
	}

	.cancel-text-button:disabled {
		@apply cursor-not-allowed text-gray-300;
	}

	.confirm-button {
		@apply min-w-[6rem];
	}
</style>
