<script lang="ts">
	/**
	 * 채팅 메시지 수정 모달
	 *
	 * 사용자가 자신의 채팅 메시지를 수정할 수 있는 모달 컴포넌트입니다.
	 * - 텍스트 편집
	 * - 기존 첨부파일 삭제
	 * - 새 파일 업로드
	 */

	import { Dialog, DialogContent, DialogHeader, DialogTitle } from '$lib/components/ui/dialog';
	import { Button } from '$lib/components/ui/button';
	import {
		uploadChatFile,
		deleteChatFile,
		formatFileSize,
		getFilenameFromUrl,
		isImageUrl,
		isVideoUrl,
		getExtensionFromFilename
	} from '$lib/functions/storage.functions';
	import type { FileUploadStatus } from '$lib/types/chat.types';
	import { authStore } from '$lib/stores/auth.svelte';
	import { rtdb } from '$lib/firebase';
	import { ref, update } from 'firebase/database';
	import { m } from '$lib/paraglide/messages';

	// Props
	interface Props {
		/** 모달 열림 상태 */
		open: boolean;
		/** 메시지 ID */
		messageId: string;
		/** 초기 텍스트 */
		initialText: string;
		/** 초기 첨부파일 URL 목록 */
		initialUrls: Record<number, string>;
		/** 채팅방 ID */
		roomId: string;
		/** 모달 닫기 콜백 */
		onClose: () => void;
		/** 저장 완료 콜백 */
		onSaved?: () => void;
	}

	let {
		open = $bindable(),
		messageId,
		initialText,
		initialUrls,
		roomId,
		onClose,
		onSaved
	}: Props = $props();

	// State
	let text = $state(initialText);
	let urls = $state<Record<number, string>>({ ...initialUrls });
	let uploadingFiles: FileUploadStatus[] = $state([]);
	let saving = $state(false);
	let error = $state<string | null>(null);

	// 파일 입력 참조
	let fileInputRef: HTMLInputElement | null = $state(null);

	// 최대 파일 크기
	const MAX_FILE_SIZE = 10 * 1024 * 1024; // 일반 파일: 10MB
	const MAX_VIDEO_SIZE = 24 * 1024 * 1024; // 동영상 파일 (.mp4): 24MB

	/**
	 * 초기화: 모달이 열릴 때 초기값 복원
	 */
	$effect(() => {
		if (open) {
			text = initialText;
			urls = { ...initialUrls };
			uploadingFiles = [];
			error = null;
		}
	});

	/**
	 * 파일 선택 버튼 클릭
	 */
	function handleFileButtonClick() {
		fileInputRef?.click();
	}

	/**
	 * 파일 선택 핸들러
	 */
	async function handleFileSelect(event: Event) {
		const input = event.target as HTMLInputElement;
		const files = Array.from(input.files || []);

		if (files.length === 0) return;

		if (!authStore.user?.uid) {
			alert('로그인이 필요합니다.');
			return;
		}

		await processFiles(files);

		// input 초기화
		input.value = '';
	}

	/**
	 * 파일 처리 (업로드)
	 */
	async function processFiles(files: File[]) {
		for (const file of files) {
			// 파일 크기 체크
			const isVideo = file.type === 'video/mp4' || file.name.toLowerCase().endsWith('.mp4');
			const maxSize = isVideo ? MAX_VIDEO_SIZE : MAX_FILE_SIZE;
			const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(0);

			if (file.size > maxSize) {
				alert(`파일 크기가 ${maxSizeMB}MB를 초과합니다: ${file.name}`);
				continue;
			}

			// 파일 업로드 상태 추가
			const fileStatus: FileUploadStatus = {
				file,
				progress: 0,
				completed: false
			};

			uploadingFiles = [...uploadingFiles, fileStatus];
			const currentIndex = uploadingFiles.length - 1;

			// Firebase Storage에 업로드
			try {
				const downloadUrl = await uploadChatFile(
					file,
					authStore.user!.uid,
					roomId,
					(progress) => {
						uploadingFiles[currentIndex].progress = progress;
					}
				);

				// 업로드 완료
				uploadingFiles[currentIndex].completed = true;
				uploadingFiles[currentIndex].downloadUrl = downloadUrl;

				// urls에 추가 (다음 인덱스 사용)
				const nextIndex = Math.max(...Object.keys(urls).map(Number), -1) + 1;
				urls = { ...urls, [nextIndex]: downloadUrl };

			// 업로드 완료 후 1초 뒤에 uploadingFiles에서 제거
			// 사용자가 업로드 완료를 인지할 수 있도록 짧은 시간 표시 후 제거하여 중복 표시 방지
			setTimeout(() => {
				uploadingFiles = uploadingFiles.filter((_, i) => i !== currentIndex);
			}, 1000);
			} catch (err) {
				console.error('파일 업로드 실패:', err);
				uploadingFiles[currentIndex].error = '업로드 실패';
			}
		}
	}

	/**
	 * 기존 파일 삭제
	 */
	async function handleRemoveExistingFile(index: number) {
		const url = urls[index];
		if (!url) return;

		try {
			// Storage에서 삭제
			await deleteChatFile(url);

			// 로컬 상태에서 제거
			const newUrls = { ...urls };
			delete newUrls[index];
			urls = newUrls;
		} catch (err) {
			console.error('파일 삭제 실패:', err);
			// 삭제 실패해도 로컬에서는 제거
			const newUrls = { ...urls };
			delete newUrls[index];
			urls = newUrls;
		}
	}

	/**
	 * 업로드 중인 파일 삭제
	 */
	async function handleRemoveUploadingFile(index: number) {
		const fileStatus = uploadingFiles[index];

		// Storage에서 삭제 (업로드 완료된 경우만)
		if (fileStatus.downloadUrl) {
			try {
				await deleteChatFile(fileStatus.downloadUrl);

				// urls에서도 제거 (방금 추가된 URL 찾기)
				const urlIndex = Object.entries(urls).find(([_, url]) => url === fileStatus.downloadUrl)?.[0];
				if (urlIndex) {
					const newUrls = { ...urls };
					delete newUrls[Number(urlIndex)];
					urls = newUrls;
				}
			} catch (err) {
				console.error('파일 삭제 실패:', err);
			}
		}

		// 로컬 목록에서 제거
		uploadingFiles = uploadingFiles.filter((_, i) => i !== index);
	}

	/**
	 * 취소 버튼
	 */
	function handleCancel() {
		onClose();
	}

	/**
	 * 저장 버튼
	 */
	async function handleSave() {
		if (saving) return;

		// 업로드 중인 파일 확인
		const incompleteFiles = uploadingFiles.filter((fs) => !fs.completed && !fs.error);
		if (incompleteFiles.length > 0) {
			error = `업로드 중인 파일이 ${incompleteFiles.length}개 있습니다. 업로드 완료 후 다시 시도해주세요.`;
			return;
		}

		// 업로드 실패한 파일 확인
		const failedFiles = uploadingFiles.filter((fs) => fs.error);
		if (failedFiles.length > 0) {
			error = `업로드 실패한 파일이 ${failedFiles.length}개 있습니다. 삭제 후 다시 시도해주세요.`;
			return;
		}

		if (!rtdb) {
			error = 'Firebase 연결이 없습니다.';
			return;
		}

		saving = true;
		error = null;

		try {
			const messageRef = ref(rtdb, `chat-messages/${messageId}`);
			const updates = {
				text: text.trim(),
				urls,
				editedAt: Date.now()
			};

			await update(messageRef, updates);

			// 저장 완료
			saving = false;
			onSaved?.();
			onClose();
		} catch (err) {
			console.error('메시지 저장 실패:', err);
			error = '메시지 저장에 실패했습니다. 다시 시도해주세요.';
			saving = false;
		}
	}
</script>

<Dialog bind:open>
	<DialogContent class="sm:max-w-2xl">
		<DialogHeader>
			<DialogTitle>메시지 수정</DialogTitle>
		</DialogHeader>

		<div class="edit-modal-content">
			<!-- 텍스트 편집 -->
			<div class="form-group">
				<label for="message-text" class="form-label">메시지 텍스트</label>
				<textarea
					id="message-text"
					class="form-textarea"
					rows="4"
					bind:value={text}
					placeholder="메시지를 입력하세요..."
					onkeydown={(e) => {
						// 스페이스바 및 Enter 키 이벤트가 Dialog 컴포넌트로 전파되지 않도록 막음
						// 이렇게 하면 textarea 내에서 자유롭게 스페이스와 줄바꿈을 입력할 수 있음
						if (e.key === ' ' || e.key === 'Enter') {
							e.stopPropagation();
						}
					}}
				/>
			</div>

			<!-- 기존 첨부파일 목록 -->
			{#if Object.keys(urls).length > 0}
				<div class="form-group">
					<label class="form-label">첨부파일</label>
					<div class="file-grid">
						{#each Object.entries(urls) as [index, url]}
							<div class="file-item">
								<!-- 파일 미리보기 -->
								{#if isImageUrl(url)}
									<img src={url} alt="첨부 이미지" class="file-preview-image" />
								{:else if isVideoUrl(url)}
									<video src={url} class="file-preview-video" controls />
								{:else}
									<div class="file-icon-box">
										<span class="file-extension-text"
											>{getExtensionFromFilename(getFilenameFromUrl(url))
												.replace('.', '')
												.toUpperCase()}</span
										>
									</div>
								{/if}

								<!-- 삭제 버튼 -->
								<button
									type="button"
									class="file-remove-button"
									onclick={() => handleRemoveExistingFile(Number(index))}
								>
									✕
								</button>

								<!-- 파일명 -->
								<p class="file-name">{getFilenameFromUrl(url)}</p>
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- 업로드 중인 파일 목록 -->
			{#if uploadingFiles.length > 0}
				<div class="form-group">
					<label class="form-label">업로드 중</label>
					<div class="file-grid">
						{#each uploadingFiles as fileStatus, index}
							<div class="file-item">
								<!-- 파일 미리보기 -->
								{#if fileStatus.file.type.startsWith('image/') || fileStatus.file.type.startsWith('video/')}
									<div class="preview-thumbnail">
										{#if fileStatus.downloadUrl}
											{#if fileStatus.file.type.startsWith('image/')}
												<img src={fileStatus.downloadUrl} alt={fileStatus.file.name} />
											{:else if fileStatus.file.type.startsWith('video/')}
												<video src={fileStatus.downloadUrl} controls />
											{/if}
										{:else}
											<div class="preview-placeholder"></div>
										{/if}

										<!-- 프로그레스바 -->
										{#if !fileStatus.completed && !fileStatus.error}
											<div class="upload-progress-overlay">
												<svg class="progress-ring" width="80" height="80">
													<circle class="progress-ring-bg" cx="40" cy="40" r="32" stroke-width="6" />
													<circle
														class="progress-ring-circle"
														cx="40"
														cy="40"
														r="32"
														stroke-width="6"
														stroke-dasharray="201.06"
														stroke-dashoffset={201.06 - (201.06 * fileStatus.progress) / 100}
													/>
												</svg>
												<span class="upload-percentage">{fileStatus.progress}%</span>
											</div>
										{/if}
									</div>
								{:else}
									<div class="file-icon-box">
										<span class="file-extension-text"
											>{getExtensionFromFilename(fileStatus.file.name)
												.replace('.', '')
												.toUpperCase()}</span
										>

										{#if !fileStatus.completed && !fileStatus.error}
											<div class="upload-progress-overlay">
												<svg class="progress-ring" width="80" height="80">
													<circle class="progress-ring-bg" cx="40" cy="40" r="32" stroke-width="6" />
													<circle
														class="progress-ring-circle"
														cx="40"
														cy="40"
														r="32"
														stroke-width="6"
														stroke-dasharray="201.06"
														stroke-dashoffset={201.06 - (201.06 * fileStatus.progress) / 100}
													/>
												</svg>
												<span class="upload-percentage">{fileStatus.progress}%</span>
											</div>
										{/if}
									</div>
								{/if}

								<!-- 에러 표시 -->
								{#if fileStatus.error}
									<div class="upload-error-overlay">
										<p class="upload-error">{fileStatus.error}</p>
									</div>
								{/if}

								<!-- 삭제 버튼 -->
								<button
									type="button"
									class="file-remove-button"
									onclick={() => handleRemoveUploadingFile(index)}
								>
									✕
								</button>

								<!-- 파일명 -->
								<p class="file-name">{fileStatus.file.name}</p>
							</div>
						{/each}
					</div>
				</div>
			{/if}

			<!-- 파일 업로드 버튼 -->
			<div class="form-group">
				<button type="button" class="upload-button" onclick={handleFileButtonClick}>
					<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
						/>
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
					</svg>
					파일 추가
				</button>

				<input
					bind:this={fileInputRef}
					type="file"
					onchange={handleFileSelect}
					multiple
					accept="image/*,video/*,.pdf,.txt,.doc,.docx,.zip,.rar"
					style="display: none;"
				/>
			</div>

			<!-- 에러 메시지 -->
			{#if error}
				<p class="error-message">{error}</p>
			{/if}

			<!-- 버튼 그룹 -->
			<div class="button-group">
				<Button variant="outline" onclick={handleCancel} disabled={saving}>취소</Button>
				<Button onclick={handleSave} disabled={saving}>{saving ? '저장 중...' : '저장'}</Button>
			</div>
		</div>
	</DialogContent>
</Dialog>

<style>
	@import 'tailwindcss' reference;

	.edit-modal-content {
		@apply space-y-4;
	}

	.form-group {
		@apply space-y-2;
	}

	.form-label {
		@apply text-sm font-semibold text-gray-700;
	}

	.form-textarea {
		@apply w-full rounded-lg border border-gray-300 px-3 py-2 text-sm;
		@apply focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20;
	}

	.file-grid {
		@apply grid grid-cols-2 gap-3 md:grid-cols-3;
	}

	.file-item {
		@apply relative rounded-lg border-2 border-gray-200 overflow-hidden;
		@apply transition-all hover:border-gray-300;
	}

	.file-preview-image,
	.file-preview-video {
		@apply aspect-square w-full object-cover;
	}

	.file-icon-box {
		@apply relative flex aspect-square w-full items-center justify-center bg-gray-100;
	}

	.file-extension-text {
		@apply text-3xl font-bold uppercase text-gray-600;
	}

	.preview-thumbnail {
		@apply relative aspect-square w-full overflow-hidden bg-gray-100;
	}

	.preview-thumbnail img,
	.preview-thumbnail video {
		@apply h-full w-full object-cover;
	}

	.preview-placeholder {
		@apply h-full w-full bg-gray-200;
	}

	.upload-progress-overlay {
		@apply absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm;
	}

	.progress-ring {
		@apply absolute;
		transform: rotate(-90deg);
	}

	.progress-ring-bg {
		@apply fill-none stroke-white/30;
	}

	.progress-ring-circle {
		@apply fill-none stroke-blue-400;
		transition: stroke-dashoffset 0.3s ease-in-out;
		stroke-linecap: round;
	}

	.upload-percentage {
		@apply absolute text-2xl font-bold text-white drop-shadow-lg;
		z-index: 10;
	}

	.upload-error-overlay {
		@apply absolute inset-0 flex items-center justify-center bg-red-500/80 backdrop-blur-sm p-2;
	}

	.upload-error {
		@apply text-xs text-center text-white font-semibold;
	}

	.file-remove-button {
		@apply absolute right-1 top-1 z-10;
		@apply flex h-6 w-6 items-center justify-center;
		@apply rounded-full bg-red-500 text-xs font-bold text-white shadow-lg;
		@apply transition-all hover:bg-red-600 hover:scale-110 active:scale-95;
	}

	.file-name {
		@apply truncate px-2 py-1 text-xs text-gray-600;
	}

	.upload-button {
		@apply flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm;
		@apply transition-colors hover:bg-gray-50 active:bg-gray-100;
	}

	.error-message {
		@apply text-sm text-red-600;
	}

	.button-group {
		@apply flex justify-end gap-2 pt-2;
	}
</style>
