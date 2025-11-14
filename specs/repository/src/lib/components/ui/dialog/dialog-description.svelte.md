---
name: dialog-description.svelte
description: dialog-description UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dialog/dialog-description.svelte
---

# dialog-description.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dialog/dialog-description.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dialog-description UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
  import { cn } from '$lib/utils.ts';
  import type { Snippet } from 'svelte';
  let { class: className, children }: { class?: string; children?: Snippet } = $props();
</script>

<p class={cn('text-sm text-gray-500', className)}>
  {@render children?.()}
</p>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
