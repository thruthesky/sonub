---
name: card-content.svelte
description: card-content UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/card/card-content.svelte
---

# card-content.svelte

## 개요

**파일 경로**: `src/lib/components/ui/card/card-content.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

card-content UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Card Content 컴포넌트
	 *
	 * 카드의 메인 콘텐츠 영역입니다.
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

<div class={cn('p-6 pt-0', className)} {...restProps}>
	{@render children?.()}
</div>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
