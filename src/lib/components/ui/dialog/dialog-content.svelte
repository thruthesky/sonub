<script lang="ts">
  import { cn } from '$lib/utils.ts';
  import { getContext, onDestroy } from 'svelte';
  import { browser } from '$app/environment';
  import type { Snippet } from 'svelte';
  import type { DialogContext } from './context';
  import { dialogContextKey } from './context';

  let { class: className, children }: { class?: string; children?: Snippet } = $props();

  const dialog = getContext<DialogContext | undefined>(dialogContextKey);
  let isOpen = $state<boolean>(false);
  let unsubscribe: (() => void) | undefined;

  if (dialog) {
    unsubscribe = dialog.openStore.subscribe((value) => {
      isOpen = value;
    });
  }

  function closeDialog() {
    dialog?.setOpen(false);
  }

  function handleOverlayClick(event: MouseEvent) {
    if (event.target === event.currentTarget) {
      closeDialog();
    }
  }

  function handleOverlayKeydown(event: KeyboardEvent) {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      closeDialog();
    }
  }

  function handleKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      event.preventDefault();
      closeDialog();
    }
  }

  // SSR 환경에서는 document 객체에 접근할 수 없으므로 브라우저 환경에서만 실행
  $effect(() => {
    if (!browser) return;

    if (isOpen) {
      document.addEventListener('keydown', handleKeydown);
    } else {
      document.removeEventListener('keydown', handleKeydown);
    }
  });

  onDestroy(() => {
    unsubscribe?.();
    // SSR 환경에서는 document 객체에 접근할 수 없으므로 브라우저 환경에서만 실행
    if (browser) {
      document.removeEventListener('keydown', handleKeydown);
    }
  });
</script>

{#if isOpen}
  <div
    class="dialog-overlay"
    role="button"
    tabindex="0"
    aria-label="배경 선택 시 다이얼로그 닫기"
    onclick={handleOverlayClick}
    onkeydown={handleOverlayKeydown}
  >
    <div class={cn('dialog-content', className)} role="dialog" aria-modal="true">
      {@render children?.()}
    </div>
  </div>
{/if}

<style>
  .dialog-overlay {
    position: fixed;
    inset: 0;
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(2px);
    padding: 1.5rem;
  }

  .dialog-content {
    width: 100%;
    max-width: 28rem;
    border-radius: 0.75rem;
    background-color: #ffffff;
    padding: 1.75rem;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
  }
</style>
