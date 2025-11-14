---
name: card-description.svelte
description: card-description UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/card/card-description.svelte
---

# card-description.svelte

## 개요

**파일 경로**: `src/lib/components/ui/card/card-description.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

card-description UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Card Description 컴포넌트
	 *
	 * 카드의 설명 텍스트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLParagraphElement> {
		class?: string;
		children?: Snippet;
	}

	let { class: className, children, ...restProps }: Props = $props();
</script>

<p class={cn('text-sm text-muted-foreground', className)} {...restProps}>
	{@render children?.()}
</p>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
