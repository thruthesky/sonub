---
name: dialog.svelte
description: dialog.svelte 컴포넌트
version: 1.0.0
type: svelte-component
category: ui-component
tags: [svelte5, sveltekit, shadcn-ui, tailwindcss]
---

# dialog.svelte

## 개요
dialog.svelte 컴포넌트

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
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<dialog />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
