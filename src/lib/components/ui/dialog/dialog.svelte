<script lang="ts">
  import { setContext } from 'svelte';
  import { writable } from 'svelte/store';
  import type { Snippet } from 'svelte';
  import type { DialogContext } from './context';
  import { dialogContextKey } from './context';

  let { open = $bindable(false), children }: { open?: boolean; children?: Snippet } = $props();

  const openStore = writable(open);

  $effect(() => {
    openStore.set(open);
  });

  function setOpen(value: boolean) {
    open = value;
    openStore.set(value);
  }

  const context: DialogContext = {
    openStore,
    setOpen
  };

  setContext(dialogContextKey, context);
</script>

{@render children?.()}
