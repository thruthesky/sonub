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
