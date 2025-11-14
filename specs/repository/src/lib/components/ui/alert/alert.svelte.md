---
name: alert.svelte
description: alert UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/alert/alert.svelte
---

# alert.svelte

## 개요

**파일 경로**: `src/lib/components/ui/alert/alert.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

alert UI 컴포넌트

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
