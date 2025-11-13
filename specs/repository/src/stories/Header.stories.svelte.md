---
title: Header.stories.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 Header.stories.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script module>
  import { defineMeta } from '@storybook/addon-svelte-csf';
  import Header from './Header.svelte';
  import { fn } from 'storybook/test';

  // More on how to set up stories at: https://storybook.js.org/docs/writing-stories
  const { Story } = defineMeta({
    title: 'Example/Header',
    component: Header,
    // This component will have an automatically generated Autodocs entry: https://storybook.js.org/docs/writing-docs/autodocs
    tags: ['autodocs'],
    parameters: {
      // More on how to position stories at: https://storybook.js.org/docs/configure/story-layout
      layout: 'fullscreen',
    },
    args: {
      onLogin: fn(),
      onLogout: fn(),
      onCreateAccount: fn(),
    }
  });
</script>

<Story name="Logged In" args={{ user: { name: 'Jane Doe' } }} />

<Story name="Logged Out" />

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
