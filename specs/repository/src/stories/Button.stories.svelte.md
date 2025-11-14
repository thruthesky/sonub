---
name: Button.stories.svelte
description: Button.stories 파일
version: 1.0.0
type: svelte-component
category: other
original_path: src/stories/Button.stories.svelte
---

# Button.stories.svelte

## 개요

**파일 경로**: `src/stories/Button.stories.svelte`
**파일 타입**: svelte-component
**카테고리**: other

Button.stories 파일

## 소스 코드

```svelte
<script module>
  import { defineMeta } from '@storybook/addon-svelte-csf';
  import Button from './Button.svelte';
  import { fn } from 'storybook/test';

  // More on how to set up stories at: https://storybook.js.org/docs/writing-stories
  const { Story } = defineMeta({
    title: 'Example/Button',
    component: Button,
    tags: ['autodocs'],
    argTypes: {
      backgroundColor: { control: 'color' },
      size: {
        control: { type: 'select' },
        options: ['small', 'medium', 'large'],
      },
    },
    args: {
      onclick: fn(),
    }
  });
</script>

<!-- More on writing stories with args: https://storybook.js.org/docs/writing-stories/args -->
<Story name="Primary" args={{ primary: true, label: 'Button' }} />

<Story name="Secondary" args={{ label: 'Button' }} />

<Story name="Large" args={{ size: 'large', label: 'Button' }} />

<Story name="Small" args={{ size: 'small', label: 'Button' }} />

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
