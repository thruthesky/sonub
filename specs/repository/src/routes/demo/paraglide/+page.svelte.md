---
name: +page.svelte
description: +page 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/demo/paraglide/+page.svelte
---

# +page.svelte

## 개요

**파일 경로**: `src/routes/demo/paraglide/+page.svelte`
**파일 타입**: svelte-component
**카테고리**: route-page

+page 페이지

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
