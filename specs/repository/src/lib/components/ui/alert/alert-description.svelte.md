---
name: alert-description.svelte
description: alert-description UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/alert/alert-description.svelte
---

# alert-description.svelte

## 개요

**파일 경로**: `src/lib/components/ui/alert/alert-description.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

alert-description UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Alert Description 컴포넌트
	 *
	 * 알림의 설명 텍스트입니다.
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

<div class={cn('text-sm [&_p]:leading-relaxed', className)} {...restProps}>
	{@render children?.()}
</div>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
