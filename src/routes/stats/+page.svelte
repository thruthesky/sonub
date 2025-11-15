<script lang="ts">
	import { browser } from '$app/environment';
	import { db } from '$lib/firebase';
	import { onMount } from 'svelte';
	import { doc, onSnapshot } from 'firebase/firestore';

	let userCount: number | null = null;
	let loading = true;

	onMount(() => {
		if (!browser || !db) {
			loading = false;
			return;
		}

		// Firestore: system/stats 문서의 userCount 필드
		const counterRef = doc(db, 'system/stats');
		const unsubscribe = onSnapshot(
			counterRef,
			(snapshot) => {
				const data = snapshot.data();
				const value = data?.userCount;
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
		<p class="stats-description">가입 시 Cloud Functions가 `/system/stats` 문서의 `userCount`를 자동으로 증가시킵니다.</p>
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
