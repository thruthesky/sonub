---
title: +page.svelte
type: component
path: src/routes/demo/paraglide/+page.svelte
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/routes/demo/paraglide/+page.svelte`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	import { setLocale } from '$lib/paraglide/runtime';
	import { page } from '$app/state';
	import { goto } from '$app/navigation';
	import { m } from '$lib/paraglide/messages';
</script>

<h1>{m.helloWorld({ name: 'SvelteKit User' })}</h1>
<div>
	<button onclick={() => setLocale('en')}>en</button>
	<button onclick={() => setLocale('ko')}>ko</button>
	<button onclick={() => setLocale('ja')}>ja</button>
	<button onclick={() => setLocale('zh')}>zh</button>
</div>
<p>
	If you use VSCode, install the <a
		href="https://marketplace.visualstudio.com/items?itemName=inlang.vs-code-extension"
		target="_blank">Sherlock i18n extension</a
	> for a better i18n experience.
</p>

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
