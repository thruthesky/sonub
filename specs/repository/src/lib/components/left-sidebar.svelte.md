---
name: left-sidebar.svelte
description: 좌측 사이드바 컴포넌트
version: 1.0.0
type: svelte-component
category: layout-component
tags: [svelte5, sveltekit]
---

# left-sidebar.svelte

## 개요
좌측 사이드바 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 좌측 사이드바 컴포넌트
	 *
	 * 데스크톱에서만 표시되는 좌측 네비게이션/메뉴 영역입니다.
	 * TailwindCSS를 사용하여 스타일링합니다.
	 * Paraglide-JS를 사용하여 다국어 메시지를 자동으로 처리합니다.
	 */

	import * as Card from '$lib/components/ui/card/index.js';
	import { BUILD_VERSION } from '$lib/version';
	import { getLocale, setLocale, locales } from '$lib/paraglide/runtime';
	import { m } from '$lib/paraglide/messages';
	import { page } from '$app/stores';

	// 언어 이름 매핑
	const localeNames: Record<string, string> = {
		en: 'English',
		ko: '한국어',
		ja: '日本語',
		zh: '中文'
	};

	/**
	 * 현재 경로가 주어진 경로와 일치하는지 확인
	 */
	function isActivePath(href: string): boolean {
		return $page.url.pathname === href;
	}

	/**
	 * 언어 변경 핸들러
	 *
	 * setLocale()이 자동으로 쿠키 저장 및 페이지 새로고침을 처리합니다.
	 */
	function handleLocaleChange(event: Event) {
		const target = event.target as HTMLSelectElement;
		const newLocale = target.value as (typeof locales)[number];

		// Paraglide 런타임 로케일 업데이트
		// - 자동으로 쿠키에 저장
		// - 기본값으로 페이지 새로고침하여 모든 메시지 업데이트
		setLocale(newLocale);
	}
</script>

<aside class="hidden lg:block lg:w-64 xl:w-72">
	<div class="sticky top-20 space-y-4">
		<!-- 네비게이션 메뉴 -->
		<Card.Root class="sidebar-card">
			<Card.Header>
				<Card.Title class="flex items-center gap-2 text-base">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M4 6h16M4 12h16M4 18h16"
						/>
					</svg>
					{m.sidebarMenu()}
				</Card.Title>
			</Card.Header>
			<Card.Content class="space-y-1">
				<a href="/" class="nav-link" class:active={isActivePath('/')}>
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
						/>
					</svg>
					{m.navHome()}
				</a>
				<a href="/about" class="nav-link" class:active={isActivePath('/about')}>
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
						/>
					</svg>
					{m.navAbout()}
				</a>
				<a href="/products" class="nav-link" class:active={isActivePath('/products')}>
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
						/>
					</svg>
					{m.navProducts()}
				</a>
				<a href="/contact" class="nav-link" class:active={isActivePath('/contact')}>
					<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
						/>
					</svg>
					{m.navContact()}
				</a>
			</Card.Content>
		</Card.Root>

		<!-- 정보 카드 -->
		<Card.Root class="sidebar-card">
			<Card.Header>
				<Card.Title class="flex items-center gap-2 text-base">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
						/>
					</svg>
					{m.sidebarRecentActivity()}
				</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="activity-text">{m.sidebarNoRecentActivity()}</p>
			</Card.Content>
		</Card.Root>

		<!-- 언어 선택 드롭다운 -->
		<Card.Root class="sidebar-card">
			<Card.Header>
				<Card.Title class="flex items-center gap-2 text-base">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
						/>
					</svg>
					{m.sidebarSelectLanguage()}
				</Card.Title>
			</Card.Header>
			<Card.Content>
				<select
					value={getLocale()}
					onchange={handleLocaleChange}
					class="language-select"
					aria-label={m.sidebarSelectLanguage()}
				>
					{#each locales as locale}
						<option value={locale}>
							{localeNames[locale] || locale}
						</option>
					{/each}
				</select>
			</Card.Content>
		</Card.Root>

		<!-- 빌드 버전 정보 -->
		<Card.Root class="sidebar-card version-card">
			<Card.Header>
				<Card.Title class="flex items-center gap-2 text-base">
					<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
						/>
					</svg>
					{m.sidebarBuildVersion()}
				</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="version-text">{BUILD_VERSION}</p>
			</Card.Content>
		</Card.Root>
	</div>
</aside>

<style>
	@import 'tailwindcss' reference;

	/* 사이드바 카드 스타일 */
	.sidebar-card {
		@apply shadow-md transition-shadow duration-300 hover:shadow-lg;
	}

	/* 네비게이션 링크 스타일 */
	.nav-link {
		@apply flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-blue-50 hover:text-blue-600;
	}

	.nav-link.active {
		@apply bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 shadow-sm;
		border-left: 3px solid theme('colors.blue.600');
		padding-left: calc(0.75rem - 3px);
	}

	/* 활동 텍스트 스타일 */
	.activity-text {
		@apply text-sm text-gray-500 italic;
	}

	/* 언어 선택 드롭다운 스타일 */
	.language-select {
		@apply w-full cursor-pointer rounded-lg border-2 border-gray-300 bg-white px-3 py-2.5 text-sm font-medium text-gray-900 shadow-sm transition-all duration-200 hover:border-blue-400 hover:shadow focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50;
	}

	/* 버전 카드 스타일 */
	.version-card {
		@apply bg-gradient-to-br from-gray-50 to-blue-50;
	}

	/* 버전 텍스트 스타일 */
	.version-text {
		@apply rounded-md bg-white px-3 py-2 text-center text-sm font-mono font-semibold text-blue-600 shadow-sm;
	}
</style>

```

## 주요 기능
- 좌측 사이드 레이아웃을 고정하여 메뉴, 최근 활동, 개발일지, 언어 선택 등을 제공
- 계획 중인 TODO 요약 카드를 추가해 `/dev/plan` 상의 핵심 작업(채팅 초대 처리, Post 타입 메시지, 게시판 통계)을 빠르게 확인 가능
- 빌드 버전과 다국어 스위처를 한 영역에서 관리하여 운영 편의성 제공

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<left-sidebar />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
