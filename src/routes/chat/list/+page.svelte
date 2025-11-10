<script lang="ts">
	/**
	 * 채팅 목록 페이지
	 *
	 * 아직 개발 중인 채팅 목록 페이지입니다.
	 */

	import UnderConstruction from '$lib/components/under-construction.svelte';
	import { m } from '$lib/paraglide/messages-proxy';
</script>

<svelte:head>
	<title>{m.page_title_chat()}</title>
</svelte:head>

<UnderConstruction title={m.chat()} message={m.chat_under_construction()} />
