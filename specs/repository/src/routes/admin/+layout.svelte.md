---
name: +layout.svelte
description: +layout 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/admin/+layout.svelte
---

# +layout.svelte

## 개요

**파일 경로**: `src/routes/admin/+layout.svelte`
**파일 타입**: svelte-component
**카테고리**: route-page

+layout 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 관리자 페이지 공통 레이아웃
	 *
	 * 관리자 페이지의 공통 구조를 정의합니다.
	 * 관리자 권한 확인 및 공통 네비게이션을 포함합니다.
	 */
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';

	let { children } = $props();

	// 관리자 권한 확인 (향후 구현)
	// if (!authStore.isAdmin) {
	//   goto('/');
	// }
</script>

<div class="admin-layout">
	<div class="admin-container">
		<header class="admin-header">
			<h2 class="admin-title">관리 메뉴</h2>
			<nav class="admin-tabs" aria-label="관리자 상단 탭">
				<a href="/admin/dashboard" class="tab-link" class:active={$page.url.pathname === '/admin/dashboard'}>
					대시보드
				</a>
				<a href="/admin/test" class="tab-link" class:active={$page.url.pathname === '/admin/test'}>
					테스트
				</a>
				<a href="/admin/users" class="tab-link" class:active={$page.url.pathname === '/admin/users'}>
					사용자목록
				</a>
				<a href="/admin/reports" class="tab-link" class:active={$page.url.pathname === '/admin/reports'}>
					신고 목록
				</a>
			</nav>
		</header>

		<!-- 관리자 메인 컨텐츠 -->
		<main class="admin-main">
			{@render children()}
		</main>
	</div>
</div>

<style>
	/* 🔴 Light Mode Only: 모든 색상은 Light Mode 전용입니다 */
	.admin-layout {
		min-height: 100vh;
		background-color: #f9fafb; /* Light gray background */
	}

	.admin-container {
		max-width: 1400px;
		margin: 0 auto;
		padding: 2rem 1rem;
	}

	.admin-header {
		background-color: #ffffff;
		border: 1px solid #e5e7eb;
		border-radius: 0.75rem;
		padding: 1.5rem;
		margin-bottom: 2rem;
	}

	.admin-title {
		margin: 0 0 1rem 0;
		font-size: 1.1rem;
		font-weight: 700;
		color: #111827;
	}

	.admin-tabs {
		display: flex;
		flex-wrap: wrap;
		gap: 0.75rem;
	}

	.tab-link {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 0.65rem 1.5rem;
		border-radius: 999px;
		border: 1px solid transparent;
		background-color: #f3f4f6;
		color: #4b5563;
		font-weight: 600;
		text-decoration: none;
		transition: all 0.2s ease;
	}

	.tab-link:hover {
		border-color: #d1d5db;
		color: #111827;
		background-color: #ffffff;
	}

	.tab-link.active {
		background-color: #3b82f6;
		border-color: #2563eb;
		color: #ffffff;
		box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
	}

	.admin-main {
		background-color: transparent;
	}

	@media (max-width: 640px) {
		.admin-container {
			padding: 1rem 0.5rem;
		}

		.admin-tabs {
			flex-direction: column;
		}

		.tab-link {
			width: 100%;
		}
	}
</style>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
