---
name: dropdown-menu-radio-item.svelte
description: dropdown-menu-radio-item UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dropdown-menu/dropdown-menu-radio-item.svelte
---

# dropdown-menu-radio-item.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dropdown-menu/dropdown-menu-radio-item.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dropdown-menu-radio-item UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";
	import CircleIcon from "@lucide/svelte/icons/circle";
	import { cn, type WithoutChild } from "$lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		children: childrenProp,
		...restProps
	}: WithoutChild<DropdownMenuPrimitive.RadioItemProps> = $props();
</script>

<DropdownMenuPrimitive.RadioItem
	bind:ref
	data-slot="dropdown-menu-radio-item"
	class={cn(
		"focus:bg-accent focus:text-accent-foreground outline-hidden relative flex cursor-default select-none items-center gap-2 rounded-sm py-1.5 pl-8 pr-2 text-sm data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg:not([class*='size-'])]:size-4 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		className
	)}
	{...restProps}
>
	{#snippet children({ checked })}
		<span class="pointer-events-none absolute left-2 flex size-3.5 items-center justify-center">
			{#if checked}
				<CircleIcon class="size-2 fill-current" />
			{/if}
		</span>
		{@render childrenProp?.({ checked })}
	{/snippet}
</DropdownMenuPrimitive.RadioItem>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
