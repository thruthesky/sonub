---
name: dropdown-menu-content.svelte
description: dropdown-menu-content.svelte 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
tags: [svelte5, sveltekit, shadcn-ui, tailwindcss]
---

# dropdown-menu-content.svelte

## 개요
dropdown-menu-content.svelte 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import { cn } from "$lib/utils.js";
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";

	let {
		ref = $bindable(null),
		sideOffset = 4,
		portalProps,
		class: className,
		...restProps
	}: DropdownMenuPrimitive.ContentProps & {
		portalProps?: DropdownMenuPrimitive.PortalProps;
	} = $props();
</script>

<DropdownMenuPrimitive.Portal {...portalProps}>
	<DropdownMenuPrimitive.Content
		bind:ref
		data-slot="dropdown-menu-content"
		{sideOffset}
		class={cn(
			"bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 max-h-(--bits-dropdown-menu-content-available-height) origin-(--bits-dropdown-menu-content-transform-origin) z-50 min-w-[8rem] overflow-y-auto overflow-x-hidden rounded-md border p-1 shadow-md outline-none",
			className
		)}
		{...restProps}
	/>
</DropdownMenuPrimitive.Portal>

```

## 주요 기능
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<dropdown-menu-content />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
