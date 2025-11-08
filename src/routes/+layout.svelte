<script lang="ts">
	import '../app.css';
	import favicon from '$lib/assets/favicon.svg';
	import TopBar from '$lib/components/top-bar.svelte';
	import LeftSidebar from '$lib/components/left-sidebar.svelte';
	import RightSidebar from '$lib/components/right-sidebar.svelte';

	let { children } = $props();
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
</svelte:head>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
	<TopBar />
	<div class="container mx-auto px-4 py-8">
		<div class="flex gap-6">
			<!-- 좌측 사이드바 (데스크톱만) -->
			<LeftSidebar />

			<!-- 메인 콘텐츠 -->
			<main class="min-w-0 flex-1">
				{@render children()}
			</main>

			<!-- 우측 사이드바 (데스크톱만) -->
			<RightSidebar />
		</div>
	</div>
</div>
