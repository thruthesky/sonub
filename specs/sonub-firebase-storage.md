---
name: sonub-firebase-storage
version: 1.0.0
description: Firebase Storage 업로드/목록/삭제 예제 명세
author: Codex Agent
email: noreply@openai.com
step: 45
priority: '*'
dependencies:
  - sonub-setup-firebase.md
tags:
  - firebase
  - storage
  - upload
  - example
---

## 1. 개요

- Firebase Storage 예제는 `sonub-setup-firebase.md`의 사용 예제 섹션에서 분리되었습니다.
- 본 문서는 `src/routes/upload/+page.svelte`에 구현해야 할 파일 업로드 도구의 전체 코드를 정의합니다.

## 2. Storage 업로드 예제

**파일 경로:** `src/routes/upload/+page.svelte`

**설명:**
- 로그인한 사용자의 UID별 디렉터리에 파일을 업로드합니다.
- 업로드 진행률, 취소, 최근 업로드 URL 표시, 목록 새로고침, 삭제 기능을 포함합니다.

```svelte
<script lang="ts">
	import { browser } from '$app/environment';
	import { storage, auth } from '$lib/firebase';
	import {
		ref as sRef,
		uploadBytesResumable,
		getDownloadURL,
		deleteObject,
		listAll,
		type UploadTask
	} from 'firebase/storage';
	import type { StoredFile } from '$lib/types/firebase';

	let file: File | null = null;
	let progress = 0;
	let files: StoredFile[] = [];
	let lastURL = '';
	let uploadTask: UploadTask | null = null;

	/**
	 * 파일 업로드
	 */
	function upload(): void {
		if (!file || !auth.currentUser) {
			alert('파일을 선택하고 로그인하세요.');
			return;
		}

		const userId = auth.currentUser.uid;
		const timestamp = Date.now();
		const path = `uploads/${userId}/${timestamp}_${file.name}`;
		const storageRef = sRef(storage, path);

		uploadTask = uploadBytesResumable(storageRef, file, {
			contentType: file.type
		});

		uploadTask.on(
			'state_changed',
			// 진행률 업데이트
			(snapshot) => {
				progress = Math.round((snapshot.bytesTransferred / snapshot.totalBytes) * 100);
				console.log(`업로드 진행률: ${progress}%`);
			},
			// 에러 처리
			(error) => {
				console.error('업로드 실패:', error);
				alert(`업로드 실패: ${error.message}`);
				progress = 0;
			},
			// 완료 처리
			async () => {
				try {
					lastURL = await getDownloadURL(uploadTask!.snapshot.ref);
					console.log('업로드 완료:', lastURL);
					await refreshList();
					progress = 0;
					file = null;
				} catch (error: any) {
					console.error('다운로드 URL 가져오기 실패:', error);
					alert(`다운로드 URL 가져오기 실패: ${error.message}`);
				}
			}
		);
	}

	/**
	 * 업로드 취소
	 */
	function cancelUpload(): void {
		if (uploadTask) {
			uploadTask.cancel();
			progress = 0;
			alert('업로드가 취소되었습니다.');
		}
	}

	/**
	 * 파일 목록 새로고침
	 */
	async function refreshList(): Promise<void> {
		if (!auth.currentUser) return;

		const userId = auth.currentUser.uid;
		const dirRef = sRef(storage, `uploads/${userId}`);

		try {
			const result = await listAll(dirRef);

			files = await Promise.all(
				result.items.map(async (itemRef) => {
					const url = await getDownloadURL(itemRef);
					const metadata = await itemRef.getMetadata();

					return {
						name: itemRef.name,
						fullPath: itemRef.fullPath,
						url: url,
						size: metadata.size,
						contentType: metadata.contentType || 'unknown',
						uploadedAt: new Date(metadata.timeCreated).getTime()
					};
				})
			);

			// 최신 업로드 순 정렬
			files.sort((a, b) => b.uploadedAt - a.uploadedAt);
		} catch (error: any) {
			console.error('목록 가져오기 실패:', error);
			alert(`목록 가져오기 실패: ${error.message}`);
		}
	}

	/**
	 * 파일 삭제
	 */
	async function remove(fullPath: string): Promise<void> {
		if (!confirm('정말로 삭제하시겠습니까?')) return;

		try {
			await deleteObject(sRef(storage, fullPath));
			console.log('삭제 완료:', fullPath);
			await refreshList();
		} catch (error: any) {
			console.error('삭제 실패:', error);
			alert(`삭제 실패: ${error.message}`);
		}
	}

	/**
	 * 파일 크기 포맷팅
	 */
	function formatBytes(bytes: number): string {
		if (bytes === 0) return '0 Bytes';
		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
	}

	// 컴포넌트 마운트 시 목록 로드
	if (browser && auth.currentUser) {
		refreshList();
	}
</script>

<div class="upload-container">
	<h1>파일 업로드 (Firebase Storage)</h1>

	<div class="upload-section">
		<input
			type="file"
			on:change={(e) => {
				const target = e.target as HTMLInputElement;
				file = target.files?.[0] ?? null;
			}}
		/>
		<button on:click={upload} disabled={!file || progress > 0}>업로드</button>

		{#if progress > 0 && progress < 100}
			<div class="progress-container">
				<div class="progress-bar" style="width: {progress}%"></div>
				<span class="progress-text">{progress}%</span>
			</div>
			<button on:click={cancelUpload} class="cancel">취소</button>
		{/if}
	</div>

	{#if lastURL}
		<div class="last-upload">
			<h3>최근 업로드:</h3>
			<a href={lastURL} target="_blank" rel="noreferrer">{lastURL}</a>
		</div>
	{/if}

	<div class="list-section">
		<div class="list-header">
			<h2>업로드된 파일 목록</h2>
			<button on:click={refreshList} class="refresh">새로고침</button>
		</div>

		{#if files.length === 0}
			<p class="empty">업로드된 파일이 없습니다.</p>
		{:else}
			<ul class="file-list">
				{#each files as f (f.fullPath)}
					<li>
						<div class="file-info">
							<a href={f.url} target="_blank" rel="noreferrer">{f.name}</a>
							<span class="file-meta">
								{formatBytes(f.size)} • {new Date(f.uploadedAt).toLocaleString('ko-KR')}
							</span>
						</div>
						<button on:click={() => remove(f.fullPath)} class="delete">삭제</button>
					</li>
				{/each}
			</ul>
		{/if}
	</div>
</div>

<style>
	.upload-container {
		max-width: 800px;
		margin: 2rem auto;
		padding: 2rem;
	}

	.upload-section {
		margin-bottom: 2rem;
		padding: 1.5rem;
		border: 2px dashed #ddd;
		border-radius: 8px;
	}

	input[type='file'] {
		margin-bottom: 1rem;
		display: block;
	}

	button {
		padding: 0.75rem 1.5rem;
		background-color: #4285f4;
		color: white;
		border: none;
		border-radius: 4px;
		cursor: pointer;
		margin-right: 0.5rem;
	}

	button:hover:not(:disabled) {
		background-color: #3367d6;
	}

	button:disabled {
		background-color: #ccc;
		cursor: not-allowed;
	}

	button.cancel {
		background-color: #f44336;
	}

	button.cancel:hover {
		background-color: #d32f2f;
	}

	.progress-container {
		position: relative;
		height: 12px;
		background-color: #eee;
		border-radius: 999px;
		margin: 1rem 0;
		overflow: hidden;
	}

	.progress-bar {
		height: 100%;
		background-color: #4285f4;
		transition: width 0.3s ease;
	}

	.progress-text {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		font-weight: 600;
		color: #333;
	}

	.last-upload {
		padding: 1rem;
		background-color: #e8f5e9;
		border-radius: 4px;
		margin-bottom: 2rem;
	}

	.last-upload h3 {
		margin: 0 0 0.5rem 0;
	}

	.last-upload a {
		word-break: break-all;
		color: #1976d2;
	}

	.list-section {
		margin-top: 2rem;
	}

	.list-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 1rem;
	}

	button.refresh {
		background-color: #666;
		padding: 0.5rem 1rem;
	}

	button.refresh:hover {
		background-color: #555;
	}

	.file-list {
		list-style: none;
		padding: 0;
	}

	.file-list li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 1rem;
		border: 1px solid #ddd;
		border-radius: 4px;
		margin-bottom: 0.5rem;
	}

	.file-info {
		flex: 1;
		display: flex;
		flex-direction: column;
		gap: 0.25rem;
	}

	.file-info a {
		color: #1976d2;
		text-decoration: none;
	}

	.file-info a:hover {
		text-decoration: underline;
	}

	.file-meta {
		font-size: 0.875rem;
		color: #666;
	}

	button.delete {
		background-color: #d32f2f;
		padding: 0.5rem 1rem;
	}

	button.delete:hover {
		background-color: #b71c1c;
	}

	.empty {
		text-align: center;
		color: #999;
		padding: 2rem;
	}
</style>
```

## 3. 동작 요약

- 업로드 중에는 진행률 바와 취소 버튼을 함께 표시합니다.
- 업로드 완료 후 `getDownloadURL`을 호출하여 최근 업로드 URL을 화면에 출력합니다.
- `listAll`과 `getMetadata`를 조합해 파일 목록을 최신 업로드 순으로 정렬합니다.
- 삭제 버튼은 `confirm()`으로 재확인을 거친 뒤 `deleteObject`를 호출합니다.

## 4. 검증 절차

1. 로그인 후 `/upload` 페이지에 접속합니다.
2. 10MB 이하의 파일을 업로드하고 진행률 표시 및 취소 버튼을 확인합니다.
3. 업로드 완료 후 다운로드 URL이 표시되고, 목록에 새 항목이 추가되는지 확인합니다.
4. `새로고침` 버튼으로 목록이 다시 로드되는지 테스트합니다.
5. 삭제 버튼을 클릭해 Storage 객체가 즉시 제거되는지 확인합니다.
