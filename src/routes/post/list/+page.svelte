<script lang="ts">
	/**
	 * 게시판 목록 페이지
	 *
	 * 아직 개발 중인 게시판 목록 페이지입니다.
	 */

	import UnderConstruction from '$lib/components/under-construction.svelte';
	import { m } from '$lib/paraglide/messages-proxy';
</script>

<svelte:head>
	<title>{m.page_title_board()}</title>
</svelte:head>

<UnderConstruction title={m.nav_board()} message={m.board_under_construction()} />
