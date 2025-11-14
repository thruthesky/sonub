---
name: card.svelte
description: card UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/card/card.svelte
---

# card.svelte

## 개요

**파일 경로**: `src/lib/components/ui/card/card.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

card UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Card Root 컴포넌트
	 *
	 * 카드의 루트 컨테이너입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLDivElement> {
		class?: string;
		children?: Snippet;
	}

	let { class: className, children, ...restProps }: Props = $props();
</script>

<div
	class={cn('rounded-lg border bg-card text-card-foreground shadow-sm', className)}
	{...restProps}
>
	{@render children?.()}
</div>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
