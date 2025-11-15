---
title: button.svelte
type: component
path: src/lib/components/ui/button/button.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/ui/button/button.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * Button 컴포넌트
	 *
	 * 다양한 스타일과 크기를 지원하는 버튼 컴포넌트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLButtonAttributes, HTMLAnchorAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface ButtonProps extends HTMLButtonAttributes {
		variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
		size?: 'default' | 'sm' | 'lg' | 'icon' | 'icon-sm' | 'icon-lg';
		class?: string;
		children?: Snippet;
		href?: never; // 버튼일 때는 href가 없음
	}

	interface LinkProps extends Omit<HTMLAnchorAttributes, 'disabled'> {
		variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
		size?: 'default' | 'sm' | 'lg' | 'icon' | 'icon-sm' | 'icon-lg';
		class?: string;
		children?: Snippet;
		href: string; // 링크일 때는 href가 필수
		disabled?: boolean;
	}

	type Props = ButtonProps | LinkProps;

	let {
		variant = 'default',
		size = 'default',
		class: className,
		children,
		href,
		disabled = false,
		...restProps
	}: Props & { disabled?: boolean } = $props();

	/**
	 * 버튼 variant별 스타일
	 */
	const variantStyles = {
		default: 'bg-primary text-primary-foreground hover:bg-primary/90',
		destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
		outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
		secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
		ghost: 'hover:bg-accent hover:text-accent-foreground',
		link: 'text-primary underline-offset-4 hover:underline'
	};

	/**
	 * 버튼 size별 스타일
	 */
	const sizeStyles = {
		default: 'h-10 px-4 py-2',
		sm: 'h-9 rounded-md px-3',
		lg: 'h-11 rounded-md px-8',
		icon: 'h-10 w-10',
		'icon-sm': 'h-8 w-8',
		'icon-lg': 'h-12 w-12'
	};

	const iconStyles = {
		default: '[&>svg]:h-4 [&>svg]:w-4 [&>svg]:shrink-0',
		sm: '[&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:shrink-0',
		lg: '[&>svg]:h-5 [&>svg]:w-5 [&>svg]:shrink-0',
		icon: '[&>svg]:h-5 [&>svg]:w-5 [&>svg]:shrink-0',
		'icon-sm': '[&>svg]:h-4 [&>svg]:w-4 [&>svg]:shrink-0',
		'icon-lg': '[&>svg]:h-6 [&>svg]:w-6 [&>svg]:shrink-0'
	} as const;
</script>

{#if href}
	<!-- href가 있으면 <a> 태그로 렌더링 -->
	<a
		href={disabled ? undefined : href}
		aria-disabled={disabled ? 'true' : undefined}
		tabindex={disabled ? -1 : undefined}
		data-disabled={disabled ? 'true' : undefined}
		class={cn(
			'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer disabled:pointer-events-none disabled:opacity-50',
			variantStyles[variant],
			sizeStyles[size],
			iconStyles[size as keyof typeof iconStyles],
			disabled ? 'pointer-events-none opacity-50' : '',
			className
		)}
		{...(restProps as any)}
	>
		{@render children?.()}
	</a>
{:else}
	<!-- href가 없으면 <button> 태그로 렌더링 -->
	<button
		disabled={disabled}
		class={cn(
			'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer disabled:pointer-events-none disabled:opacity-50',
			variantStyles[variant],
			sizeStyles[size],
			iconStyles[size as keyof typeof iconStyles],
			className
		)}
		{...(restProps as any)}
	>
		{@render children?.()}
	</button>
{/if}

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
