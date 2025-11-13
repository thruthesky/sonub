---
name: +page.svelte
description: 관리자 신고 목록 페이지
version: 1.0.0
type: svelte-component
category: route-page
tags: [svelte5, sveltekit]
---

# +page.svelte

## 개요
관리자 신고 목록 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 관리자 신고 목록 페이지
	 *
	 * 모든 사용자의 신고를 createdAt 순서로 표시합니다.
	 * 관리자만 접근 가능합니다.
	 */
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import { m } from '$lib/paraglide/messages.js';
	// import DatabaseListView from "$lib/components/DatabaseListView.svelte";
	// import type { ReportWithId } from "$lib/types/report";

	// 라우트 보호: 관리자만 접근 가능
	// 향후 구현: admin role 확인 후 접근 제어

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
	 * @param report - 신고 데이터
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
</script>

<svelte:head>
	<title>{m.pageTitleAdminReports()}</title>
</svelte:head>

<div class="admin-report-list-page">
	<!-- 페이지 헤더 -->
	<div class="page-header">
		<h1 class="page-title">{m.adminReportList()}</h1>
		<p class="page-description">{m.adminReportListGuide()}</p>
	</div>

	<!-- 신고 목록 -->
	<!--
		향후 구현:
		DatabaseListView 컴포넌트를 사용하여 실시간 신고 목록 표시
		- path="reports"
		- orderBy="createdAt"
		- limitToFirst={20}
		- 페이지네이션 및 무한 스크롤 지원
	-->
	<div class="report-list-container">
		<p class="empty-message">신고 목록이 비어있습니다.</p>
	</div>
</div>

<style>
	/* 페이지 컨테이너 */
	.admin-report-list-page {
		max-width: 900px;
		margin: 0 auto;
		padding: 2rem 1rem;
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
		.admin-report-list-page {
			padding: 1rem 0.5rem;
		}

		.page-title {
			font-size: 1.5rem;
		}
	}
</style>

```

## 주요 기능
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<+page />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
