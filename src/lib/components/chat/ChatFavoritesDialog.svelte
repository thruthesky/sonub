<script lang="ts">
	/**
	 * 채팅 즐겨찾기 관리 다이얼로그
	 *
	 * 기능:
	 * - 즐겨찾기 폴더 목록/생성/수정/삭제
	 * - 폴더별 채팅방 추가/제거
	 * - 폴더 클릭 시 채팅방 목록 표시
	 * - 채팅방에서 호출 시 현재 채팅방이 속한 폴더 강조 표시
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
	import { ref, push, set, remove, get, update } from 'firebase/database';
	import { rtdb } from '$lib/firebase';
	import * as m from '$lib/paraglide/messages.js';

	/**
	 * 즐겨찾기 폴더 인터페이스
	 */
	interface Favorite {
		id: string;
		name: string;
		description?: string;
		createdAt: number;
		folderOrder: string;
		roomList?: Record<string, boolean>;
	}

	/**
	 * Props
	 */
	interface Props {
		/** 다이얼로그 열림 상태 */
		open?: boolean;
		/** 현재 채팅방 ID (채팅방에서 호출 시) */
		currentRoomId?: string | null;
	}

	let {
		open = $bindable(false),
		currentRoomId = null
	}: Props = $props();

	const dispatch = createEventDispatcher<{
		roomSelected: { roomId: string };
		close: void;
	}>();

	// 상태 변수
	let favorites = $state<Favorite[]>([]);
	let selectedFavorite = $state<Favorite | null>(null);
	let isLoading = $state(false);
	let errorMessage = $state('');
	let viewMode = $state<'list' | 'create' | 'edit'>('list');

	/**
	 * 표시 모드 (채팅방에서 호출 시에만 사용)
	 * - 'add': 폴더에 현재 채팅방 추가/제거 모드
	 * - 'browse': 폴더 및 채팅방 목록 탐색 모드
	 */
	let displayMode = $state<'add' | 'browse'>('add');

	// 폼 입력 상태
	let folderName = $state('');
	let folderDescription = $state('');
	let pinToTop = $state(false);
	let isSaving = $state(false);

	/**
	 * 즐겨찾기 폴더 목록 불러오기
	 */
	async function loadFavorites() {
		if (!authStore.user?.uid || !rtdb) return;

		isLoading = true;
		errorMessage = '';

		try {
			const favoritesRef = ref(rtdb, `chat-favorites/${authStore.user.uid}`);
			const snapshot = await get(favoritesRef);

			if (snapshot.exists()) {
				const data = snapshot.val();
				favorites = Object.entries(data).map(([id, value]) => ({
					id,
					...(value as Omit<Favorite, 'id'>)
				})).sort((a, b) => {
					// pinned 폴더 (folderOrder가 "500"으로 시작)를 먼저 표시
					const aIsPinned = a.folderOrder.startsWith('500');
					const bIsPinned = b.folderOrder.startsWith('500');

					// 둘 다 pinned이거나 둘 다 아닌 경우: folderOrder로 정렬
					if (aIsPinned === bIsPinned) {
						return a.folderOrder.localeCompare(b.folderOrder);
					}

					// pinned 폴더를 먼저 표시
					return aIsPinned ? -1 : 1;
				});
			} else {
				favorites = [];
			}
		} catch (error) {
			console.error('즐겨찾기 로드 실패:', error);
			errorMessage = '즐겨찾기를 불러오는데 실패했습니다.';
		} finally {
			isLoading = false;
		}
	}

	/**
	 * 폴더 생성 모드로 전환
	 */
	function startCreateFolder() {
		viewMode = 'create';
		folderName = '';
		folderDescription = '';
		pinToTop = false;
	}

	/**
	 * 폴더 수정 모드로 전환
	 */
	function startEditFolder(favorite: Favorite) {
		selectedFavorite = favorite;
		viewMode = 'edit';
		folderName = favorite.name;
		folderDescription = favorite.description || '';
		pinToTop = favorite.folderOrder.startsWith('500');
	}

	/**
	 * 폴더 저장 (생성 또는 수정)
	 */
	async function saveFolder() {
		const trimmedName = folderName.trim();
		if (!trimmedName) {
			errorMessage = '폴더 이름을 입력해주세요.';
			return;
		}

		if (!authStore.user?.uid || !rtdb) {
			errorMessage = '로그인이 필요합니다.';
			return;
		}

		isSaving = true;
		errorMessage = '';

		try {
			const now = Date.now();
			const prefix = pinToTop ? '500' : '';
			const folderOrder = `${prefix}${now}`;

			if (viewMode === 'create') {
				// 새 폴더 생성
				const favoritesRef = ref(rtdb, `chat-favorites/${authStore.user.uid}`);
				const newFavoriteRef = push(favoritesRef);

				await set(newFavoriteRef, {
					name: trimmedName,
					description: folderDescription.trim() || null,
					createdAt: now,
					folderOrder,
					roomList: {}
				});
			} else if (viewMode === 'edit' && selectedFavorite) {
				// 기존 폴더 수정
				const favoriteRef = ref(rtdb, `chat-favorites/${authStore.user.uid}/${selectedFavorite.id}`);
				await update(favoriteRef, {
					name: trimmedName,
					description: folderDescription.trim() || null,
					folderOrder
				});
			}

			// 목록 새로고침 및 목록 모드로 전환
			await loadFavorites();
			viewMode = 'list';
			selectedFavorite = null;
		} catch (error) {
			console.error('폴더 저장 실패:', error);
			errorMessage = '폴더 저장에 실패했습니다.';
		} finally {
			isSaving = false;
		}
	}

	/**
	 * 폴더 삭제
	 */
	async function deleteFolder(favorite: Favorite) {
		if (!confirm(`"${favorite.name}" 폴더를 삭제하시겠습니까?`)) {
			return;
		}

		if (!authStore.user?.uid || !rtdb) return;

		try {
			const favoriteRef = ref(rtdb, `chat-favorites/${authStore.user.uid}/${favorite.id}`);
			await remove(favoriteRef);
			await loadFavorites();
		} catch (error) {
			console.error('폴더 삭제 실패:', error);
			errorMessage = '폴더 삭제에 실패했습니다.';
		}
	}

	/**
	 * 폴더 선택 (채팅방 추가/제거 토글)
	 */
	async function toggleRoomInFolder(favorite: Favorite) {
		if (!currentRoomId || !authStore.user?.uid || !rtdb) return;

		try {
			const roomList = favorite.roomList || {};
			const isInFolder = roomList[currentRoomId] === true;

			const favoriteRef = ref(rtdb, `chat-favorites/${authStore.user.uid}/${favorite.id}/roomList/${currentRoomId}`);

			if (isInFolder) {
				// 이미 추가되어 있으면 제거
				await remove(favoriteRef);
			} else {
				// 추가되어 있지 않으면 추가
				await set(favoriteRef, true);
			}

			// 목록 새로고침
			await loadFavorites();
		} catch (error) {
			console.error('즐겨찾기 토글 실패:', error);
			errorMessage = '즐겨찾기 변경에 실패했습니다.';
		}
	}

	/**
	 * 폴더에서 채팅방 제거
	 */
	async function removeRoomFromFolder(favorite: Favorite, roomId: string) {
		if (!authStore.user?.uid || !rtdb) return;

		try {
			const favoriteRef = ref(rtdb, `chat-favorites/${authStore.user.uid}/${favorite.id}/roomList/${roomId}`);
			await remove(favoriteRef);
			await loadFavorites();
		} catch (error) {
			console.error('채팅방 제거 실패:', error);
			errorMessage = '채팅방 제거에 실패했습니다.';
		}
	}

	/**
	 * 폴더 클릭 핸들러
	 */
	function handleFolderClick(favorite: Favorite) {
		if (currentRoomId && displayMode === 'add') {
			// 추가 모드: 채팅방을 폴더에 추가/제거 토글
			toggleRoomInFolder(favorite);
		} else {
			// 목록 모드 또는 채팅 목록에서 호출: 폴더 확장/축소
			selectedFavorite = selectedFavorite?.id === favorite.id ? null : favorite;
		}
	}

	/**
	 * 채팅방 클릭 핸들러
	 */
	function handleRoomClick(roomId: string) {
		dispatch('roomSelected', { roomId });
		open = false;
	}

	/**
	 * 목록 모드로 돌아가기
	 */
	function backToList() {
		viewMode = 'list';
		selectedFavorite = null;
		errorMessage = '';
	}

	/**
	 * 표시 모드 토글 (추가 ↔ 목록)
	 */
	function toggleDisplayMode() {
		displayMode = displayMode === 'add' ? 'browse' : 'add';
		selectedFavorite = null; // 모드 전환 시 선택 초기화
	}

	/**
	 * 다이얼로그 열릴 때 즐겨찾기 목록 로드 및 초기화
	 */
	$effect(() => {
		if (open) {
			loadFavorites();
			displayMode = 'add'; // 다이얼로그를 열 때마다 추가 모드로 초기화
			selectedFavorite = null;
		}
	});
</script>

<Dialog bind:open>
	<DialogContent class="max-w-3xl max-h-[80vh] overflow-y-auto">
		<DialogHeader>
			<div class="flex items-center justify-between">
				<div class="flex-1">
					<DialogTitle>
						{#if currentRoomId && displayMode === 'add'}
							즐겨찾기 추가
						{:else}
							{m.chatTabBookmarks()}
						{/if}
					</DialogTitle>
					<DialogDescription>
						{#if currentRoomId && displayMode === 'add'}
							즐겨찾기 폴더를 선택하여 채팅방을 추가하거나 제거하세요.
						{:else if currentRoomId && displayMode === 'browse'}
							폴더를 클릭하여 저장된 채팅방 목록을 확인하세요.
						{:else}
							채팅방을 폴더에 정리하세요.
						{/if}
					</DialogDescription>
				</div>
				{#if currentRoomId}
					<Button
						type="button"
						variant="outline"
						size="sm"
						onclick={toggleDisplayMode}
						class="ml-4"
					>
						{displayMode === 'add' ? '목록' : '추가'}
					</Button>
				{/if}
			</div>
		</DialogHeader>

		{#if errorMessage}
			<div class="error-message">
				{errorMessage}
			</div>
		{/if}

		{#if viewMode === 'list'}
			<!-- 폴더 목록 모드 -->
			<div class="favorites-container">
				<div class="favorites-header">
					<Button onclick={startCreateFolder} variant="outline" size="sm">
						<span class="mr-1">+</span>
						폴더 생성
					</Button>
				</div>

				{#if isLoading}
					<div class="loading-container">
						{m.commonLoading()}
					</div>
				{:else if favorites.length === 0}
					<div class="empty-state">
						<p>아직 폴더가 없습니다</p>
						<p class="text-sm text-gray-500">폴더를 만들어 채팅방을 정리하세요.</p>
					</div>
				{:else}
					<div class="folders-list">
						{#each favorites as favorite (favorite.id)}
							<div
								class="folder-item"
								class:highlighted={currentRoomId && favorite.roomList?.[currentRoomId]}
							>
								<button
									type="button"
									class="folder-header"
									onclick={() => handleFolderClick(favorite)}
								>
									<div class="folder-info">
										<h3 class="folder-name">
											{favorite.folderOrder.startsWith('500') ? '📌 ' : ''}
											{favorite.name}
										</h3>
										{#if favorite.description}
											<p class="folder-description">{favorite.description}</p>
										{/if}
										<p class="room-count">
											{Object.keys(favorite.roomList || {}).length}개 방
										</p>
									</div>
									{#if currentRoomId && displayMode === 'add'}
										<div class="toggle-indicator">
											{favorite.roomList?.[currentRoomId] ? '✓' : '+'}
										</div>
									{/if}
								</button>

								{#if !currentRoomId}
									<div class="folder-actions">
										<button
											type="button"
											class="action-btn"
											onclick={(e) => {
												e.stopPropagation();
												startEditFolder(favorite);
											}}
										>
											✏️
										</button>
										<button
											type="button"
											class="action-btn"
											onclick={(e) => {
												e.stopPropagation();
												deleteFolder(favorite);
											}}
										>
											🗑️
										</button>
									</div>
								{/if}

								<!-- 폴더가 선택되었고, (목록 모드이거나 현재 방 ID가 없는 경우) 채팅방 목록 표시 -->
								{#if selectedFavorite?.id === favorite.id && (!currentRoomId || displayMode === 'browse')}
									<div class="rooms-list">
										{#if favorite.roomList && Object.keys(favorite.roomList).length > 0}
											{#each Object.keys(favorite.roomList) as roomId (roomId)}
												<div class="room-item">
													<button
														type="button"
														class="room-button"
														onclick={() => handleRoomClick(roomId)}
													>
														<span class="room-id">방: {roomId}</span>
													</button>
													{#if !currentRoomId}
														<button
															type="button"
															class="remove-room-btn"
															onclick={(e) => {
																e.stopPropagation();
																removeRoomFromFolder(favorite, roomId);
															}}
														>
															✕
														</button>
													{/if}
												</div>
											{/each}
										{:else}
											<p class="empty-rooms">방이 없습니다</p>
										{/if}
									</div>
								{/if}
							</div>
						{/each}
					</div>
				{/if}
			</div>
		{:else if viewMode === 'create' || viewMode === 'edit'}
			<!-- 폴더 생성/수정 모드 -->
			<form class="folder-form" onsubmit={(e) => { e.preventDefault(); saveFolder(); }}>
				<div class="form-group">
					<label for="folderName" class="form-label">폴더 이름 *</label>
					<input
						id="folderName"
						type="text"
						bind:value={folderName}
						class="form-input"
						placeholder="예: 업무 관련"
						maxlength="30"
						required
						onkeydown={(e) => e.stopPropagation()}
					/>
				</div>

				<div class="form-group">
					<label for="folderDescription" class="form-label">폴더 설명 (선택사항)</label>
					<textarea
						id="folderDescription"
						bind:value={folderDescription}
						class="form-textarea"
						placeholder="예: 팀 채팅을 모아둔 폴더"
						maxlength="100"
						rows="3"
						onkeydown={(e) => e.stopPropagation()}
					></textarea>
				</div>

				<div class="form-group checkbox-group">
					<label class="checkbox-label">
						<input
							type="checkbox"
							bind:checked={pinToTop}
							class="form-checkbox"
						/>
						<span>상단 고정</span>
					</label>
				</div>

				<DialogFooter>
					<Button type="button" variant="outline" onclick={backToList} disabled={isSaving}>
						{m.commonCancel()}
					</Button>
					<Button type="submit" disabled={isSaving}>
						{isSaving ? '저장 중...' : m.commonSave()}
					</Button>
				</DialogFooter>
			</form>
		{/if}
	</DialogContent>
</Dialog>

<style>
	@import 'tailwindcss' reference;

	.error-message {
		@apply rounded-md bg-red-50 p-3 text-sm text-red-800;
	}

	.favorites-container {
		@apply space-y-4;
	}

	.favorites-header {
		@apply flex items-center justify-between border-b pb-3;
	}

	.loading-container {
		@apply flex items-center justify-center py-8 text-gray-500;
	}

	.empty-state {
		@apply flex flex-col items-center justify-center py-12 text-center text-gray-500;
	}

	.folders-list {
		@apply space-y-3;
	}

	.folder-item {
		@apply rounded-lg border bg-white transition-colors;
	}

	.folder-item.highlighted {
		@apply border-pink-300 bg-pink-50;
	}

	.folder-header {
		@apply flex w-full cursor-pointer items-center justify-between p-4 text-left transition-colors hover:bg-gray-50;
	}

	.folder-info {
		@apply flex-1;
	}

	.folder-name {
		@apply text-base font-semibold text-gray-900;
	}

	.folder-description {
		@apply mt-1 text-sm text-gray-600;
	}

	.room-count {
		@apply mt-2 text-xs text-gray-500;
	}

	.toggle-indicator {
		@apply flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-lg font-bold;
	}

	.folder-actions {
		@apply flex items-center gap-2 border-t px-4 py-2;
	}

	.action-btn {
		@apply rounded px-3 py-1 text-sm transition-colors hover:bg-gray-100;
	}

	.rooms-list {
		@apply space-y-2 border-t bg-gray-50 p-4;
	}

	.room-item {
		@apply flex items-center justify-between rounded-md bg-white p-3 shadow-sm;
	}

	.room-button {
		@apply flex-1 cursor-pointer text-left transition-colors hover:text-blue-600;
	}

	.room-id {
		@apply text-sm font-medium text-gray-700;
	}

	.remove-room-btn {
		@apply flex h-6 w-6 items-center justify-center rounded-full text-sm text-gray-400 transition-colors hover:bg-red-100 hover:text-red-600;
	}

	.empty-rooms {
		@apply py-4 text-center text-sm text-gray-500;
	}

	.folder-form {
		@apply space-y-4;
	}

	.form-group {
		@apply space-y-2;
	}

	.form-label {
		@apply block text-sm font-medium text-gray-700;
	}

	.form-input,
	.form-textarea {
		@apply w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500;
	}

	.form-textarea {
		@apply resize-none;
	}

	.checkbox-group {
		@apply flex items-center;
	}

	.checkbox-label {
		@apply flex cursor-pointer items-center gap-2 text-sm text-gray-700;
	}

	.form-checkbox {
		@apply h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600 transition-colors focus:ring-2 focus:ring-blue-500;
	}
</style>
