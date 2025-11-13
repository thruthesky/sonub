---
title: dropdown-menu-shortcut.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 dropdown-menu-shortcut.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
