---
title: dialog-description.svelte
type: component
path: src/lib/components/ui/dialog/dialog-description.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/ui/dialog/dialog-description.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
