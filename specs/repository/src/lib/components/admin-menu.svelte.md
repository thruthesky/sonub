---
title: "src/lib/components/admin-menu.svelte"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "src/lib/components/admin-menu.svelte"
spec_type: "repository-source"
---

## 개요

이 파일은 admin-menu.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 관리자 메뉴 컴포넌트
	 *
	 * /admin 경로의 상단에 표시될 메뉴 네비게이션입니다.
	 * 활성 메뉴를 강조 표시합니다.
	 */

	import { page } from '$app/stores';
	import { Button } from '$lib/components/ui/button';
	import { m } from '$lib/paraglide/messages';

	interface MenuItem {
		label: string;
		href: string;
	}

	// 관리자 메뉴 항목들
	const menuItems: MenuItem[] = [
		{ label: m.adminDashboardMenu(), href: '/admin/dashboard' },
		{ label: m.adminTestMenu(), href: '/admin/test' },
		{ label: m.adminUserListMenu(), href: '/admin/users' }
	];

	/**
	 * 현재 경로가 메뉴 항목과 일치하는지 확인합니다.
	 *
	 * @param href 메뉴 링크
	 * @returns 일치 여부
	 */
	function isActive(href: string): boolean {
		const currentPath = $page.url.pathname;
		return currentPath === href || currentPath.startsWith(href + '/');
	}
</script>

<!-- 관리자 메뉴 네비게이션 -->
<nav class="border-b bg-white px-4 py-3 shadow-sm">
	<div class="flex gap-2">
		{#each menuItems as item (item.href)}
			<Button
				href={item.href}
				variant={isActive(item.href) ? 'default' : 'ghost'}
				size="sm"
				class="cursor-pointer font-medium"
			>
				{item.label}
			</Button>
		{/each}
	</div>
</nav>

<style>
	/* 추가 스타일이 필요한 경우 작성 */
</style>

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
