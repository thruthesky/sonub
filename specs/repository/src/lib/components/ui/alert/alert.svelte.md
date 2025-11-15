---
title: alert.svelte
type: component
path: src/lib/components/ui/alert/alert.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/ui/alert/alert.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Alert Root 컴포넌트
	 *
	 * 사용자에게 중요한 메시지를 표시하는 알림 컴포넌트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLDivElement> {
		variant?: 'default' | 'destructive';
		class?: string;
		children?: Snippet;
	}

	let { variant = 'default', class: className, children, ...restProps }: Props = $props();

	/**
	 * Alert variant별 스타일
	 */
	const variantStyles = {
		default: 'bg-background text-foreground',
		destructive: 'border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive'
	};
</script>

<div
	class={cn(
		'relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground',
		variantStyles[variant],
		className
	)}
	role="alert"
	{...restProps}
>
	{@render children?.()}
</div>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
