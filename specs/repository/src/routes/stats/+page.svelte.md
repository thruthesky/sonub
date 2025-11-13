---
name: +page.svelte
description: +page.svelte 컴포넌트
version: 1.0.0
type: svelte-component
category: route-page
tags: [svelte5, sveltekit]
---

# +page.svelte

## 개요
+page.svelte 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	import { browser } from '$app/environment';
	import { rtdb } from '$lib/firebase';
	import { onMount } from 'svelte';
	import { onValue, ref } from 'firebase/database';

	let userCount: number | null = null;
	let loading = true;

	onMount(() => {
		if (!browser || !rtdb) {
			loading = false;
			return;
		}

		const userCounterRef = ref(rtdb, 'stats/counters/user');
		const unsubscribe = onValue(
			userCounterRef,
			(snapshot) => {
				const value = snapshot.val();
				userCount = typeof value === 'number' ? value : 0;
				loading = false;
			},
			() => {
				loading = false;
			}
		);

		return () => unsubscribe();
	});
</script>

<svelte:head>
	<title>Sonub 통계</title>
</svelte:head>

<section class="stats-page">
	<div class="stats-card">
		<h1>Sonub 통계</h1>
		<p class="stats-section-title">총 사용자 수</p>
		<p class="stats-value">
			{#if loading}
				로딩 중...
			{:else}
				{userCount ?? 0}명
			{/if}
		</p>
		<p class="stats-description">가입 시 Cloud Functions가 `/stats/counters/user`를 자동으로 증가시킵니다.</p>
	</div>
</section>

<style>
	@import 'tailwindcss' reference;

	.stats-page {
		@apply mx-auto flex min-h-[60vh] w-full max-w-3xl items-center justify-center px-4 py-16;
	}

	.stats-card {
		@apply w-full rounded-3xl border border-emerald-100 bg-white p-10 text-center shadow-xl;
	}

	.stats-card h1 {
		@apply mb-6 text-3xl font-bold text-gray-900;
	}

	.stats-section-title {
		@apply text-sm uppercase tracking-wide text-gray-500;
	}

	.stats-value {
		@apply text-5xl font-extrabold text-emerald-600;
	}

	.stats-description {
		@apply mt-4 text-sm text-gray-500;
	}
</style>

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
