---
name: dropdown-menu-shortcut.svelte
description: dropdown-menu-shortcut.svelte 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
tags: [svelte5, sveltekit, shadcn-ui, tailwindcss]
---

# dropdown-menu-shortcut.svelte

## 개요
dropdown-menu-shortcut.svelte 컴포넌트

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
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<dropdown-menu-shortcut />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
