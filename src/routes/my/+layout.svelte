<script lang="ts">
	/**
	 * 사용자 페이지 공통 레이아웃
	 *
	 * 사용자 개인 페이지의 공통 구조를 정의합니다.
	 * 로그인 확인 및 사용자 메뉴를 포함합니다.
	 */
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import { authStore } from '$lib/stores/auth.svelte';

	let { children } = $props();

	// 로그인 확인 (향후 구현)
	// if (!authStore.isAuthenticated) {
	//   goto('/user/login');
	// }
</script>

<div class="user-layout">
	<div class="user-container">
		<!-- 사용자 사이드바 -->
		<aside class="user-sidebar">
			<nav class="user-nav">
				<h2 class="user-title">내 메뉴</h2>
				<ul class="nav-list">
					<li>
						<a href="/my/reports" class="nav-link" class:active={$page.url.pathname === '/my/reports'}>
							내 신고 목록
						</a>
					</li>
					<!-- 향후 추가 사용자 메뉴 -->
				</ul>
			</nav>
		</aside>

		<!-- 사용자 메인 컨텐츠 -->
		<main class="user-main">
			{@render children()}
		</main>
	</div>
</div>

<style>
	.user-layout {
		min-height: 100vh;
		background-color: #f9fafb;
	}

	.user-container {
		display: flex;
		gap: 2rem;
		max-width: 1400px;
		margin: 0 auto;
		padding: 2rem 1rem;
	}

	/* 사용자 사이드바 */
	.user-sidebar {
		width: 250px;
		flex-shrink: 0;
	}

	.user-nav {
		background-color: #ffffff;
		border: 1px solid #e5e7eb;
		border-radius: 0.5rem;
		padding: 1.5rem;
		position: sticky;
		top: 100px;
	}

	.user-title {
		margin: 0 0 1rem 0;
		font-size: 1.1rem;
		font-weight: 700;
		color: #111827;
	}

	.nav-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.nav-list li {
		margin-bottom: 0.5rem;
	}

	.nav-link {
		display: block;
		padding: 0.75rem 1rem;
		color: #4b5563;
		text-decoration: none;
		border-radius: 0.375rem;
		transition: all 0.2s ease;
		font-weight: 500;
	}

	.nav-link:hover {
		background-color: #f3f4f6;
		color: #111827;
	}

	.nav-link.active {
		background-color: #3b82f6;
		color: #ffffff;
	}

	/* 사용자 메인 컨텐츠 */
	.user-main {
		flex: 1;
		min-width: 0;
	}

	/* 반응형 */
	@media (max-width: 1024px) {
		.user-container {
			flex-direction: column;
			gap: 1rem;
		}

		.user-sidebar {
			width: 100%;
		}

		.user-nav {
			position: static;
			display: grid;
			grid-template-columns: auto 1fr;
			align-items: center;
			gap: 1rem;
		}

		.user-title {
			margin: 0;
		}

		.nav-list {
			display: flex;
			gap: 0.5rem;
		}

		.nav-list li {
			margin-bottom: 0;
		}
	}

	/* 모바일 */
	@media (max-width: 640px) {
		.user-container {
			padding: 1rem 0.5rem;
		}

		.user-nav {
			flex-direction: column;
			gap: 0.5rem;
		}

		.nav-list {
			flex-direction: column;
		}
	}
</style>
