---
name: +page.svelte
description: 게시판 목록 페이지
version: 1.0.0
type: svelte-component
category: route-page
tags: [svelte5, sveltekit]
---

# +page.svelte

## 개요
게시판 목록 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 게시판 목록 페이지
	 *
	 * 아직 개발 중인 게시판 목록 페이지입니다.
	 */

	import UnderConstruction from '$lib/components/under-construction.svelte';
	import { m } from '$lib/paraglide/messages';
</script>

<svelte:head>
	<title>{m.pageTitleBoard()}</title>
</svelte:head>

<UnderConstruction title={m.navBoard()} message={m.boardConstruction()} />

```

## 주요 기능
- 코드 분석 필요

## Props/Parameters
없음

## 사용 예시
```svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<+page />
```

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
