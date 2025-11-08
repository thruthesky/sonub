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
