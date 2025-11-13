---
title: dialog-title.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 dialog-title.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
