---
title: +page.svelte
type: component
path: src/routes/my/reports/+page.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/routes/my/reports/+page.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 내 신고 목록 페이지
	 *
	 * 현재 로그인한 사용자가 작성한 신고만 createdAt 순서로 표시합니다.
	 */
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import { m } from '$lib/paraglide/messages.js';
	// import DatabaseListView from "$lib/components/DatabaseListView.svelte";
	// import type { ReportWithId } from "$lib/types/report";
	// import { removeReport } from "$lib/services/report";

	/**
	 * 신고 사유를 현재 언어로 변환하는 함수
	 *
	 * @param reason - 신고 사유 (abuse, fake-news, spam, inappropriate, other)
	 * @returns 번역된 신고 사유
	 */
	function getReasonText(reason: string): string {
		switch (reason) {
			case 'abuse':
				return m.reportReasonAbuse();
			case 'fake-news':
				return m.reportReasonFakeNews();
			case 'spam':
				return m.reportReasonSpam();
			case 'inappropriate':
				return m.reportReasonInappropriate();
			case 'other':
				return m.reportReasonOther();
			default:
				return reason;
		}
	}

	/**
	 * 신고 타입을 현재 언어로 변환하는 함수
	 *
	 * @param type - 신고 타입 (post, comment)
	 * @returns 번역된 신고 타입
	 */
	function getTypeText(type: string): string {
		return type === 'post' ? m.commonPost() : m.commonComment();
	}

	/**
	 * 게시글/댓글로 이동하는 함수
	 *
	 * @param type - 신고 대상 타입
	 * @param nodeId - 신고 대상 ID
	 */
	function handleGoToNode(type: string, nodeId: string) {
		if (type === 'post') {
			// 게시글 상세 페이지로 이동
			goto(`/post/detail/${nodeId}`);
		} else {
			// 댓글은 게시글 상세 페이지로 이동 (댓글이 속한 게시글로 이동)
			// 댓글 ID로는 직접 이동할 수 없으므로, 게시글 목록으로 이동
			goto('/post/list');
		}
	}

	/**
	 * 신고 취소 핸들러
	 *
	 * @param reportId - 신고 ID
	 */
	async function handleCancelReport(reportId: string) {
		// 확인 다이얼로그
		if (!confirm(m.reportCancelConfirm())) {
			return;
		}

		// 향후 구현:
		// removeReport() API 호출
		// Toast 메시지 표시
		// DatabaseListView 실시간 업데이트
	}
</script>

<svelte:head>
	<title>{m.pageTitleMyReports()}</title>
</svelte:head>

{#if !authStore.isAuthenticated}
	<!-- 로그인하지 않은 경우 -->
	<div class="my-report-list-page">
		<div class="empty-state">
			<p>{m.authLoginRequired()}</p>
			<a class="login-btn" href="/user/login">
				{m.navLogin()}
			</a>
		</div>
	</div>
{:else}
	<!-- 로그인한 경우 -->
	<div class="my-report-list-page">
		<!-- 페이지 헤더 -->
		<div class="page-header">
			<h1 class="page-title">{m.reportMyList()}</h1>
			<p class="page-description">{m.reportMyListGuide()}</p>
		</div>

		<!-- 신고 목록 -->
		<!--
			향후 구현:
			DatabaseListView 컴포넌트를 사용하여 실시간 신고 목록 표시
			- path="reports"
			- orderBy="createdAt"
			- limitToFirst={20}
			- filter={(item) => item.uid === authStore.user?.uid}
			- 페이지네이션 및 무한 스크롤 지원
		-->
		<div class="report-list-container">
			<p class="empty-message">신고 목록이 비어있습니다.</p>
		</div>
	</div>
{/if}

<style>
	/* 페이지 컨테이너 */
	.my-report-list-page {
		max-width: 900px;
		margin: 0 auto;
		padding: 2rem 1rem;
	}

	/* 빈 상태 (로그인 안 됨) */
	.empty-state {
		text-align: center;
		padding: 3rem 1rem;
	}

	.empty-state p {
		margin-bottom: 1.5rem;
		font-size: 1.1rem;
		color: #6b7280;
	}

	.login-btn {
		display: inline-block;
		padding: 0.75rem 2rem;
		background-color: #3b82f6;
		color: #ffffff;
		border-radius: 0.5rem;
		font-size: 1rem;
		font-weight: 600;
		cursor: pointer;
		transition: background-color 0.2s ease;
		text-decoration: none;
	}

	.login-btn:hover {
		background-color: #2563eb;
	}

	/* 페이지 헤더 */
	.page-header {
		margin-bottom: 2rem;
		padding-bottom: 1rem;
		border-bottom: 2px solid #e5e7eb;
	}

	.page-title {
		margin: 0 0 0.5rem 0;
		font-size: 2rem;
		font-weight: 700;
		color: #111827;
	}

	.page-description {
		margin: 0;
		font-size: 0.95rem;
		color: #6b7280;
	}

	/* 신고 목록 컨테이너 */
	.report-list-container {
		min-height: 400px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.empty-message {
		font-size: 1rem;
		color: #9ca3af;
	}

	/* 반응형 스타일 */
	@media (max-width: 768px) {
		.my-report-list-page {
			padding: 1rem 0.5rem;
		}

		.page-title {
			font-size: 1.5rem;
		}
	}
</style>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
