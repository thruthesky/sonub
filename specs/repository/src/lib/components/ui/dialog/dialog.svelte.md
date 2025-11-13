---
title: dialog.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 dialog.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
