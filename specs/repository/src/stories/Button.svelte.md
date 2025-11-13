---
name: Button.svelte
description: Button.svelte 컴포넌트
version: 1.0.0
type: svelte-component
category: storybook
tags: [svelte5, sveltekit]
---

# Button.svelte

## 개요
Button.svelte 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
  import './button.css';

  interface Props {
    /** Is this the principal call to action on the page? */
    primary?: boolean;
    /** What background color to use */
    backgroundColor?: string;
    /** How large should the button be? */
    size?: 'small' | 'medium' | 'large';
    /** Button contents */
    label: string;
    /** The onclick event handler */
    onclick?: () => void;
  }

  const { primary = false, backgroundColor, size = 'medium', label, ...props }: Props = $props();
  
  let mode = $derived(primary ? 'storybook-button--primary' : 'storybook-button--secondary');
  let style = $derived(backgroundColor ? `background-color: ${backgroundColor}` : '');
</script>

<button
  type="button"
  class={['storybook-button', `storybook-button--${size}`, mode].join(' ')}
  {style}
  {...props}
>
  {label}
</button>

```

## 주요 기능
- 코드 분석 필요

## Props/Parameters
State variables: mode, style

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<Button />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
