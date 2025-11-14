---
name: Page.stories.svelte
description: Page.stories 파일
version: 1.0.0
type: svelte-component
category: other
original_path: src/stories/Page.stories.svelte
---

# Page.stories.svelte

## 개요

**파일 경로**: `src/stories/Page.stories.svelte`
**파일 타입**: svelte-component
**카테고리**: other

Page.stories 파일

## 소스 코드

```svelte
<script module>
  import { defineMeta } from '@storybook/addon-svelte-csf';
  import { expect, userEvent, waitFor, within } from 'storybook/test';
  import Page from './Page.svelte';
  import { fn } from 'storybook/test';

  // More on how to set up stories at: https://storybook.js.org/docs/writing-stories
  const { Story } = defineMeta({
    title: 'Example/Page',
    component: Page,
    parameters: {
      // More on how to position stories at: https://storybook.js.org/docs/configure/story-layout
      layout: 'fullscreen',
    },
  });
</script>

<Story name="Logged In" play={async ({ canvasElement }) => {
    const canvas = within(canvasElement);
    const loginButton = canvas.getByRole('button', { name: /Log in/i });
    await expect(loginButton).toBeInTheDocument();
    await userEvent.click(loginButton);
    await waitFor(() => expect(loginButton).not.toBeInTheDocument());

    const logoutButton = canvas.getByRole('button', { name: /Log out/i });
    await expect(logoutButton).toBeInTheDocument();
  }}
/>

<Story name="Logged Out" />

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
