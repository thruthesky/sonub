---
name: card-title.svelte
description: card-title UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/card/card-title.svelte
---

# card-title.svelte

## 개요

**파일 경로**: `src/lib/components/ui/card/card-title.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

card-title UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Card Title 컴포넌트
	 *
	 * 카드의 제목입니다.
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

<h3 class={cn('text-2xl font-semibold leading-none tracking-tight', className)} {...restProps}>
	{@render children?.()}
</h3>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
