---
title: alert-title.svelte
type: component
path: src/lib/components/ui/alert/alert-title.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/ui/alert/alert-title.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
