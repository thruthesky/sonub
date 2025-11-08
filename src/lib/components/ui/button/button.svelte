<script lang="ts">
	/**
	 * Button 컴포넌트
	 *
	 * 다양한 스타일과 크기를 지원하는 버튼 컴포넌트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLButtonAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLButtonAttributes {
		variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
		size?: 'default' | 'sm' | 'lg' | 'icon';
		class?: string;
		children?: Snippet;
	}

	let {
		variant = 'default',
		size = 'default',
		class: className,
		children,
		...restProps
	}: Props = $props();

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
		icon: 'h-10 w-10'
	};
</script>

<button
	class={cn(
		'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
		variantStyles[variant],
		sizeStyles[size],
		className
	)}
	{...restProps}
>
	{@render children?.()}
</button>
