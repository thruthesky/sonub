<script lang="ts">
	/**
	 * 채팅방 비밀번호 설정 컴포넌트 (Owner 전용)
	 *
	 * 채팅방 Owner가 비밀번호를 설정하거나 삭제할 수 있는 UI 컴포넌트입니다.
	 *
	 * 주요 기능:
	 * - 비밀번호 입력 (type="text"로 화면에 표시)
	 * - 최소 4자 유효성 검사
	 * - 비밀번호 저장/삭제
	 * - Firebase RTDB:
	 *   - /chat-room-passwords/{roomId}/password: 실제 비밀번호 (plain text)
	 *   - /chat-rooms/{roomId}/password: true (활성화 시) 또는 필드 삭제 (비활성화 시)
	 *
	 * @prop roomId - 채팅방 ID
	 * @prop currentPassword - 현재 설정된 비밀번호 (optional)
	 * @prop onCancel - 취소 버튼 클릭 시 호출될 콜백 함수 (optional)
	 */

	import { Button } from '$lib/components/ui/button';
	import { Input } from '$lib/components/ui/input';
	import { toast } from 'svelte-sonner';
	import { rtdb } from '$lib/firebase';
	import { ref, update, remove } from 'firebase/database';
	import { m } from '$lib/paraglide/messages';

	interface Props {
		roomId: string;
		currentPassword?: string;
		onCancel?: () => void;
	}

	let { roomId, currentPassword = '', onCancel }: Props = $props();

	// 상태 변수
	let password = $state(currentPassword);
	let isSaving = $state(false);

	/**
	 * 비밀번호 저장 핸들러
	 *
	 * 로직:
	 * 1. 비밀번호 유효성 검사 (최소 4자)
	 * 2. /chat-room-passwords/{roomId}/password 에 비밀번호 저장
	 * 3. /chat-rooms/{roomId}/password 에 true 저장
	 */
	async function handleSave() {
		// 0. rtdb null 체크
		if (!rtdb) {
			toast.error(m.chatPasswordSaveFailure());
			return;
		}

		// 1. 유효성 검사
		if (password.length < 4) {
			toast.error(m.chatPasswordMinLengthError());
			return;
		}

		isSaving = true;

		try {
			// 2. 비밀번호 저장
			await update(ref(rtdb, `chat-room-passwords/${roomId}`), {
				password: password
			});

			// 3. 활성화 플래그 저장
			await update(ref(rtdb, `chat-rooms/${roomId}`), {
				password: true
			});

			toast.success(m.chatPasswordSetSuccess());

			// 저장 성공 시 모달창 닫기
			if (onCancel) {
				onCancel();
			}
		} catch (error) {
			console.error('❌ 비밀번호 저장 실패:', error);
			toast.error(m.chatPasswordSaveFailure());
		} finally {
			isSaving = false;
		}
	}

	/**
	 * 비밀번호 삭제 핸들러
	 *
	 * 로직:
	 * 1. /chat-room-passwords/{roomId}/password 삭제
	 * 2. /chat-rooms/{roomId}/password 필드 삭제
	 */
	async function handleDelete() {
		// 0. rtdb null 체크
		if (!rtdb) {
			toast.error(m.chatPasswordSaveFailure());
			return;
		}

		isSaving = true;

		try {
			// 1. 비밀번호 삭제
			await remove(ref(rtdb, `chat-room-passwords/${roomId}/password`));

			// 2. 활성화 플래그 삭제
			await remove(ref(rtdb, `chat-rooms/${roomId}/password`));

			toast.success(m.chatPasswordDeleteSuccess());

			// 비밀번호 입력창 초기화
			password = '';

			// 삭제 성공 시 모달창 닫기
			if (onCancel) {
				onCancel();
			}
		} catch (error) {
			console.error('❌ 비밀번호 삭제 실패:', error);
			toast.error(m.chatPasswordSaveFailure());
		} finally {
			isSaving = false;
		}
	}
</script>

<div class="password-setting-container">
	<!-- 비밀번호 입력 필드 -->
	<div class="input-section">
		<Input
			type="text"
			placeholder={m.chatPasswordInputPlaceholder()}
			bind:value={password}
			disabled={isSaving}
		/>
	</div>

	<!-- 버튼 그룹 -->
	<div class="button-group">
		<!-- 취소 버튼 (좌측) -->
		{#if onCancel}
			<Button variant="outline" onclick={onCancel} disabled={isSaving}>
				{m.commonCancel()}
			</Button>
		{/if}

		<!-- 우측 버튼 그룹 -->
		<div class="right-buttons">
			<!-- 비밀번호 삭제 버튼 (기존 비밀번호가 있을 때만 표시) -->
			{#if currentPassword}
				<Button variant="destructive" onclick={handleDelete} disabled={isSaving}>
					{m.chatPasswordDelete()}
				</Button>
			{/if}

			<!-- 저장 버튼 (파란색) -->
			<Button onclick={handleSave} disabled={isSaving} class="bg-blue-600 hover:bg-blue-700 text-white">
				{isSaving ? m.chatPasswordSaving() : m.commonSave()}
			</Button>
		</div>
	</div>
</div>

<style lang="postcss">
	@import 'tailwindcss' reference;

	/**
	 * 비밀번호 설정 UI 스타일링
	 *
	 * Layout: Tailwind CSS inline classes 사용
	 * Styling: @apply를 통한 Tailwind CSS utility classes 적용
	 */

	.password-setting-container {
		@apply space-y-4;
	}

	.input-section {
		@apply space-y-3;
	}

	.button-group {
		@apply flex justify-between items-center pt-2;
	}

	.right-buttons {
		@apply flex gap-2;
	}
</style>
