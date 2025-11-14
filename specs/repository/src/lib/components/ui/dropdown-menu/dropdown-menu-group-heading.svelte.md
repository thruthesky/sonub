---
name: dropdown-menu-group-heading.svelte
description: dropdown-menu-group-heading UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dropdown-menu/dropdown-menu-group-heading.svelte
---

# dropdown-menu-group-heading.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dropdown-menu/dropdown-menu-group-heading.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dropdown-menu-group-heading UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";
	import { cn } from "$lib/utils.js";
	import type { ComponentProps } from "svelte";

	let {
		ref = $bindable(null),
		class: className,
		inset,
		...restProps
	}: ComponentProps<typeof DropdownMenuPrimitive.GroupHeading> & {
		inset?: boolean;
	} = $props();
</script>

<DropdownMenuPrimitive.GroupHeading
	bind:ref
	data-slot="dropdown-menu-group-heading"
	data-inset={inset}
	class={cn("px-2 py-1.5 text-sm font-semibold data-[inset]:pl-8", className)}
	{...restProps}
/>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
