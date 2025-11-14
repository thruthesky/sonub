---
name: dropdown-menu-sub-content.svelte
description: dropdown-menu-sub-content UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dropdown-menu/dropdown-menu-sub-content.svelte
---

# dropdown-menu-sub-content.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dropdown-menu/dropdown-menu-sub-content.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dropdown-menu-sub-content UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";
	import { cn } from "$lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		...restProps
	}: DropdownMenuPrimitive.SubContentProps = $props();
</script>

<DropdownMenuPrimitive.SubContent
	bind:ref
	data-slot="dropdown-menu-sub-content"
	class={cn(
		"bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 origin-(--bits-dropdown-menu-content-transform-origin) z-50 min-w-[8rem] overflow-hidden rounded-md border p-1 shadow-lg",
		className
	)}
	{...restProps}
/>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
