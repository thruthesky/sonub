<script lang="ts">
	/**
	 * 채팅방 비밀번호 설정 컴포넌트 (Owner 전용) - Firestore
	 *
	 * 채팅방 Owner가 비밀번호를 설정하거나 삭제할 수 있는 UI 컴포넌트입니다.
	 *
	 * 주요 기능:
	 * - 비밀번호 입력 (type="text"로 화면에 표시)
	 * - 최소 4자 유효성 검사
	 * - 비밀번호 저장/삭제
	 * - Firestore:
	 *   - chats/{roomId}/password-data: { password: string } - 실제 비밀번호 (plain text)
	 *   - chats/{roomId}: { password: true } - 활성화 플래그 (또는 필드 삭제)
	 *
	 * @prop roomId - 채팅방 ID
	 * @prop currentPassword - 현재 설정된 비밀번호 (optional)
	 * @prop onCancel - 취소 버튼 클릭 시 호출될 콜백 함수 (optional)
	 */

	import { Button } from '$lib/components/ui/button';
	import { Input } from '$lib/components/ui/input';
	import { toast } from 'svelte-sonner';
	import { db } from '$lib/firebase';
	import { doc, setDoc, updateDoc, deleteField } from 'firebase/firestore';
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
	 * 비밀번호 저장 핸들러 (Firestore)
	 *
	 * 로직:
	 * 1. 비밀번호 유효성 검사 (최소 4자)
	 * 2. chats/{roomId}/password-data 에 비밀번호 저장
	 * 3. chats/{roomId} 에 password: true 저장
	 */
	async function handleSave() {
		// 1. 유효성 검사
		if (password.length < 4) {
			toast.error(m.chatPasswordMinLengthError());
			return;
		}

		isSaving = true;

		try {
			// 2. 비밀번호 저장 (password-data subcollection)
			const passwordDocRef = doc(db!, `chats/${roomId}/password-data/password`);
			await setDoc(passwordDocRef, {
				password: password
			});

			// 3. 활성화 플래그 저장 (chat room document)
			const roomDocRef = doc(db!, `chats/${roomId}`);
			await updateDoc(roomDocRef, {
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
	 * 비밀번호 삭제 핸들러 (Firestore)
	 *
	 * 로직:
	 * 1. chats/{roomId}/password-data/password 문서 삭제는 하지 않음 (보안을 위해 유지)
	 * 2. chats/{roomId} 의 password 필드 삭제
	 */
	async function handleDelete() {
		isSaving = true;

		try {
			// 활성화 플래그 삭제 (password 필드만 제거)
			const roomDocRef = doc(db!, `chats/${roomId}`);
			await updateDoc(roomDocRef, {
				password: deleteField()
			});

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
