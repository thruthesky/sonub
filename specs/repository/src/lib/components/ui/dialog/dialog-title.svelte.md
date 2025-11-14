---
name: dialog-title.svelte
description: dialog-title UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dialog/dialog-title.svelte
---

# dialog-title.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dialog/dialog-title.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dialog-title UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
  import { cn } from '$lib/utils.ts';
  import type { Snippet } from 'svelte';
  let { class: className, children }: { class?: string; children?: Snippet } = $props();
</script>

<h2 class={cn('text-lg font-semibold leading-none tracking-tight', className)}>
  {@render children?.()}
</h2>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
