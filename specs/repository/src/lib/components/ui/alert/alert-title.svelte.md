---
name: alert-title.svelte
description: Alert Title 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
tags: [svelte5, sveltekit, shadcn-ui, tailwindcss]
---

# alert-title.svelte

## 개요
Alert Title 컴포넌트

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
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<alert-title />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
