<script lang="ts">
	/**
	 * 루트 레이아웃 컴포넌트
	 *
	 * 전체 애플리케이션의 레이아웃을 정의합니다.
	 * 3컬럼 구조: 좌측 사이드바, 메인 콘텐츠, 우측 사이드바
	 * Paraglide 다국어 지원
	 */

	import '../app.css';
	import favicon from '$lib/assets/favicon.svg';
	import TopBar from '$lib/components/top-bar.svelte';
	import LeftSidebar from '$lib/components/left-sidebar.svelte';
	import RightSidebar from '$lib/components/right-sidebar.svelte';
	import DevIcon from '$lib/components/dev/dev-icon.svelte';
	import { dev } from '$app/environment';
	import { setLocale, locales } from '$lib/paraglide/runtime';

	// 쿠키에서 로케일을 읽어 Paraglide 런타임에 설정
	const cookies = typeof document !== 'undefined' ? document.cookie : '';
	const localeMatch = cookies.match(/PARAGLIDE_LOCALE=([^;]+)/);
	const savedLocaleString = localeMatch ? localeMatch[1] : 'en';

	// 저장된 로케일이 지원되는 로케일 목록에 포함되는지 확인
	const savedLocale = (locales.includes(savedLocaleString as any)
		? savedLocaleString
		: 'en') as typeof locales[number];

	// 렌더링 후 Paraglide 로케일 설정
	if (typeof document !== 'undefined') {
		setLocale(savedLocale, { reload: false });
	}

	let { children } = $props();
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
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
