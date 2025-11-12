<script lang="ts">
  /**
   * 사용자 검색 다이얼로그
   *
   * displayNameLowerCase 값을 기준으로 정확히 일치하는 사용자를 검색하는 모달입니다.
   * - 사용자 검색이 필요한 모든 페이지에서 재사용합니다.
   * - 검색어는 제출 시 자동으로 소문자로 정규화됩니다.
   */
  import { createEventDispatcher } from 'svelte';
  import { Button } from '$lib/components/ui/button/index.js';
  import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
  } from '$lib/components/ui/dialog';

  interface Props {
    open?: boolean;
    keyword?: string;
    title?: string;
    description?: string;
    label?: string;
    helperText?: string;
    placeholder?: string;
    minLength?: number;
    autoLowercase?: boolean;
    submitLabel?: string;
    clearLabel?: string;
  }

  let {
    open = $bindable(false),
    keyword = $bindable(''),
    title = '사용자 검색',
    description = 'displayNameLowerCase 필드가 정확히 일치하는 사용자를 찾습니다. 입력값은 자동으로 소문자로 변환됩니다.',
    label = '검색할 사용자 이름 (소문자 기준)',
    helperText = 'Firebase RTDB 의 `displayNameLowerCase` 필드와 일치해야 하므로 공백/대소문자를 제거한 형태로 입력해주세요.',
    placeholder = '예: sonub',
    minLength = 2,
    autoLowercase = true,
    submitLabel = '검색하기',
    clearLabel = '검색 초기화'
  }: Props = $props();

  const dispatch = createEventDispatcher<{
    search: { keyword: string };
    clear: void;
  }>();

  let inputRef: HTMLInputElement | null = $state(null);

  function handleSubmit(event: SubmitEvent) {
    event.preventDefault();
    const trimmed = keyword.trim();
    if (trimmed.length < minLength) {
      return;
    }
    const normalized = autoLowercase ? trimmed.toLowerCase() : trimmed;
    keyword = normalized;
    dispatch('search', { keyword: normalized });
    open = false;
  }

  function handleClear() {
    keyword = '';
    dispatch('clear');
    open = false;
  }

  $effect(() => {
    if (open && inputRef) {
      requestAnimationFrame(() => {
        inputRef?.focus();
      });
    }
  });
</script>

<Dialog bind:open>
  <DialogContent class="user-search-dialog">
    <DialogHeader>
      <DialogTitle>{title}</DialogTitle>
      <DialogDescription>{description}</DialogDescription>
    </DialogHeader>

    <form class="flex flex-col gap-3" onsubmit={handleSubmit}>
      <label class="search-label flex flex-col gap-2">
        <span>{label}</span>
        <input
          bind:this={inputRef}
          type="text"
          bind:value={keyword}
          placeholder={placeholder}
          minlength={minLength}
          required
          class="search-input"
        />
      </label>
      <p class="search-hint">{helperText}</p>

      <DialogFooter class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <Button type="button" variant="ghost" class="w-full sm:w-auto" onclick={handleClear}>
          {clearLabel}
        </Button>
        <Button type="submit" class="w-full sm:w-auto">
          {submitLabel}
        </Button>
      </DialogFooter>
    </form>
  </DialogContent>
</Dialog>

<style>
  .user-search-dialog :global(.search-input) {
    @apply w-full rounded-xl border border-gray-300 px-4 py-3 text-base text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900;
  }

  .user-search-dialog :global(.search-label) {
    @apply text-sm font-semibold text-gray-700;
  }

  .user-search-dialog :global(.search-hint) {
    @apply text-sm leading-relaxed text-gray-500;
  }
</style>
