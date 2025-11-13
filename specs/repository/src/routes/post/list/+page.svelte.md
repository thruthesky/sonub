---
title: +page.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 +page.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
