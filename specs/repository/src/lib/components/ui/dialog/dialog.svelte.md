---
name: dialog.svelte
description: dialog UI 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
original_path: src/lib/components/ui/dialog/dialog.svelte
---

# dialog.svelte

## 개요

**파일 경로**: `src/lib/components/ui/dialog/dialog.svelte`
**파일 타입**: svelte-component
**카테고리**: ui-component

dialog UI 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
  import { setContext } from 'svelte';
  import { writable } from 'svelte/store';
  import type { Snippet } from 'svelte';
  import type { DialogContext } from './context';
  import { dialogContextKey } from './context';

  let { open = $bindable(false), children }: { open?: boolean; children?: Snippet } = $props();

  const openStore = writable(open);

  $effect(() => {
    openStore.set(open);
  });

  function setOpen(value: boolean) {
    open = value;
    openStore.set(value);
  }

  const context: DialogContext = {
    openStore,
    setOpen
  };

  setContext(dialogContextKey, context);
</script>

{@render children?.()}

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
