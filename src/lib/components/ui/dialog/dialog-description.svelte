<script lang="ts">
  import { cn } from '$lib/utils.ts';
  import type { Snippet } from 'svelte';
  let { class: className, children }: { class?: string; children?: Snippet } = $props();
</script>

<p class={cn('text-sm text-gray-500', className)}>
  {@render children?.()}
</p>
