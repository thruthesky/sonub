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
