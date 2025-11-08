<script lang="ts">
	/**
	 * Card Title 컴포넌트
	 *
	 * 카드의 제목입니다.
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

<h3 class={cn('text-2xl font-semibold leading-none tracking-tight', className)} {...restProps}>
	{@render children?.()}
</h3>
