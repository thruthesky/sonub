---
name: admin-menu.svelte
description: 관리자 메뉴 컴포넌트
version: 1.0.0
type: svelte-component
category: feature-component
tags: [svelte5, sveltekit]
---

# admin-menu.svelte

## 개요
관리자 메뉴 컴포넌트

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

## 주요 기능
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<admin-menu />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
