---
name: dropdown-menu-group.svelte
description: dropdown-menu-group UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dropdown-menu/dropdown-menu-group.svelte
---

# dropdown-menu-group.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dropdown-menu/dropdown-menu-group.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dropdown-menu-group UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";

	let { ref = $bindable(null), ...restProps }: DropdownMenuPrimitive.GroupProps = $props();
</script>

<DropdownMenuPrimitive.Group bind:ref data-slot="dropdown-menu-group" {...restProps} />

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
