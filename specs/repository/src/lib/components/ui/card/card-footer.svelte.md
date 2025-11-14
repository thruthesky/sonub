---
name: card-footer.svelte
description: card-footer UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/card/card-footer.svelte
---

# card-footer.svelte

## 개요

**파일 경로**: `src/lib/components/ui/card/card-footer.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

card-footer UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Card Footer 컴포넌트
	 *
	 * 카드의 푸터 영역입니다.
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

<div class={cn('flex items-center p-6 pt-0', className)} {...restProps}>
	{@render children?.()}
</div>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
