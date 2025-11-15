---
title: dropdown-menu-label.svelte
type: component
path: src/lib/components/ui/dropdown-menu/dropdown-menu-label.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/ui/dropdown-menu/dropdown-menu-label.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	import { cn, type WithElementRef } from "$lib/utils.js";
	import type { HTMLAttributes } from "svelte/elements";

	let {
		ref = $bindable(null),
		class: className,
		inset,
		children,
		...restProps
	}: WithElementRef<HTMLAttributes<HTMLDivElement>> & {
		inset?: boolean;
	} = $props();
</script>

<div
	bind:this={ref}
	data-slot="dropdown-menu-label"
	data-inset={inset}
	class={cn("px-2 py-1.5 text-sm font-semibold data-[inset]:pl-8", className)}
	{...restProps}
>
	{@render children?.()}
</div>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
