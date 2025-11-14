---
name: dropdown-menu-shortcut.svelte
description: dropdown-menu-shortcut UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dropdown-menu/dropdown-menu-shortcut.svelte
---

# dropdown-menu-shortcut.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dropdown-menu/dropdown-menu-shortcut.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dropdown-menu-shortcut UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import type { HTMLAttributes } from "svelte/elements";
	import { cn, type WithElementRef } from "$lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: WithElementRef<HTMLAttributes<HTMLSpanElement>> = $props();
</script>

<span
	bind:this={ref}
	data-slot="dropdown-menu-shortcut"
	class={cn("text-muted-foreground ml-auto text-xs tracking-widest", className)}
	{...restProps}
>
	{@render children?.()}
</span>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
