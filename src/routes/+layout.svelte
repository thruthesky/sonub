<script lang="ts">
	/**
	 * 루트 레이아웃 컴포넌트
	 *
	 * 전체 애플리케이션의 레이아웃을 정의합니다.
	 * 3컬럼 구조: 좌측 사이드바, 메인 콘텐츠, 우측 사이드바
	 *
	 * Paraglide i18n:
	 * - hooks.server.ts의 paraglideMiddleware가 서버에서 로케일 자동 감지/설정
	 * - 클라이언트는 쿠키 기반으로 자동 감지되어 설정됨
	 */

	import '../app.css';
	import TopBar from '$lib/components/top-bar.svelte';
	import LeftSidebar from '$lib/components/left-sidebar.svelte';
	import RightSidebar from '$lib/components/right-sidebar.svelte';
	import DevIcon from '$lib/components/dev/dev-icon.svelte';
	import { dev } from '$app/environment';

	let { children } = $props();
</script>

<svelte:head>
	<!-- Favicon 설정 -->
	<link rel="icon" type="image/png" sizes="64x64" href="/favicon-64.png" />
	<link rel="icon" type="image/png" sizes="512x512" href="/favicon-512.png" />
	<link rel="apple-touch-icon" sizes="512x512" href="/favicon-512.png" />
	<!-- 기본 favicon (브라우저 호환성) -->
	<link rel="icon" type="image/png" href="/favicon-64.png" />
</svelte:head>

<div class="min-h-screen bg-gray-50">
	<TopBar />
	<div class="pt-20">
		<div class="container mx-auto px-4 py-8">
			<div class="flex gap-6">
				<!-- 좌측 사이드바 (데스크톱만) -->
				<LeftSidebar />

				<!-- 메인 콘텐츠 -->
				<main class="min-w-0 flex-1">
					{@render children()}
				</main>

				<!-- 우측 사이드바 (데스크톱만) -->
				<RightSidebar />
			</div>
		</div>
	</div>
</div>

<!-- 개발 모드에서만 표시되는 개발자 도구 -->
{#if dev}
	<DevIcon />
{/if}
