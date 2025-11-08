<script lang="ts">
	/**
	 * Card Header 컴포넌트
	 *
	 * 카드의 헤더 영역입니다.
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

<div class={cn('flex flex-col space-y-1.5 p-6', className)} {...restProps}>
	{@render children?.()}
</div>
