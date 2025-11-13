---
name: Header.stories.svelte
description: Header.stories.svelte 컴포넌트
version: 1.0.0
type: svelte-component
category: storybook
tags: [svelte5, sveltekit]
---

# Header.stories.svelte

## 개요
Header.stories.svelte 컴포넌트

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

## 주요 기능
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<Header.stories />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
