---
name: alert-title.svelte
description: alert-title UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/alert/alert-title.svelte
---

# alert-title.svelte

## 개요

**파일 경로**: `src/lib/components/ui/alert/alert-title.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

alert-title UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Alert Title 컴포넌트
	 *
	 * 알림의 제목입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLHeadingElement> {
		class?: string;
		children?: Snippet;
	}

	let { class: className, children, ...restProps }: Props = $props();
</script>

<h5 class={cn('mb-1 font-medium leading-none tracking-tight', className)} {...restProps}>
	{@render children?.()}
</h5>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
