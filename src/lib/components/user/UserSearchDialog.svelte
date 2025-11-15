<script lang="ts">
  /**
   * 사용자 검색 다이얼로그 (Firestore)
   *
   * displayNameLowerCase 값을 기준으로 prefix 매칭으로 사용자를 검색하는 모달입니다.
   * - 사용자 검색이 필요한 모든 페이지에서 재사용합니다.
   * - 검색어는 제출 시 자동으로 소문자로 정규화됩니다.
   * - showResults=true 시 검색 결과를 다이얼로그 내에 FirestoreListView로 표시합니다.
   * - 검색 결과에서 사용자를 클릭하면 onUserSelect 콜백이 호출됩니다.
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
  import FirestoreListView from '$lib/components/FirestoreListView.svelte';
  import Avatar from '$lib/components/user/avatar.svelte';

  type UserData = Record<string, unknown>;

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
    /** 검색 결과를 다이얼로그 내에 표시할지 여부 (기본값: false) */
    showResults?: boolean;
    /** Firestore 사용자 컬렉션 경로 (기본값: "users") */
    usersPath?: string;
    /** 검색할 필드 이름 (기본값: "displayNameLowerCase") */
    searchField?: string;
    /** 검색 결과 페이지 크기 (기본값: 20) */
    pageSize?: number;
  }

  let {
    open = $bindable(false),
    keyword = $bindable(''),
    title = '사용자 검색',
    description = 'displayNameLowerCase 필드를 prefix 매칭으로 검색합니다. 입력값은 자동으로 소문자로 변환됩니다.',
    label = '검색할 사용자 이름 (소문자 기준)',
    helperText = 'Firestore의 `displayNameLowerCase` 필드를 prefix 검색하므로 공백/대소문자를 제거한 형태로 입력해주세요.',
    placeholder = '예: sonub',
    minLength = 2,
    autoLowercase = true,
    submitLabel = '검색하기',
    clearLabel = '검색 초기화',
    showResults = false,
    usersPath = 'users',
    searchField = 'displayNameLowerCase',
    pageSize = 20
  }: Props = $props();

  const dispatch = createEventDispatcher<{
    search: { keyword: string };
    clear: void;
    userSelect: { user: UserData; uid: string };
  }>();

  let inputRef: HTMLInputElement | null = $state(null);
  /** 검색이 실행된 키워드 (FirestoreListView에 전달) */
  let searchKeyword = $state<string | null>(null);

  function handleSubmit(event: SubmitEvent) {
    event.preventDefault();
    const trimmed = keyword.trim();
    if (trimmed.length < minLength) {
      return;
    }
    const normalized = autoLowercase ? trimmed.toLowerCase() : trimmed;
    keyword = normalized;

    if (showResults) {
      // 검색 결과를 표시하는 모드
      searchKeyword = normalized;
    } else {
      // 검색어만 전달하는 모드 (기존 방식)
      dispatch('search', { keyword: normalized });
      open = false;
    }
  }

  function handleClear() {
    keyword = '';
    searchKeyword = null;
    dispatch('clear');
    if (!showResults) {
      open = false;
    }
  }

  /**
   * 검색 결과에서 사용자 클릭 시 호출
   */
  function handleUserClick(user: UserData, uid: string) {
    dispatch('userSelect', { user, uid });
    open = false;
  }

  $effect(() => {
    if (open && inputRef) {
      requestAnimationFrame(() => {
        inputRef?.focus();
      });
    }
  });

  // 다이얼로그가 닫힐 때 검색 결과 초기화
  $effect(() => {
    if (!open) {
      searchKeyword = null;
    }
  });
</script>

<Dialog bind:open>
  <DialogContent class="user-search-dialog {showResults ? 'max-w-2xl' : ''}">
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
          onkeydown={(e) => {
            e.stopPropagation();
            if (e.key === 'Enter') {
              e.preventDefault();
              const trimmed = keyword.trim();
              if (trimmed.length >= minLength) {
                const normalized = autoLowercase ? trimmed.toLowerCase() : trimmed;
                keyword = normalized;
                if (showResults) {
                  searchKeyword = normalized;
                } else {
                  dispatch('search', { keyword: normalized });
                  open = false;
                }
              }
            }
          }}
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

    {#if showResults && searchKeyword}
      <div class="search-results">
        <h3 class="results-title">검색 결과</h3>
        {#key searchKeyword}
          <FirestoreListView
            path={usersPath}
            pageSize={pageSize}
            orderByField={searchField}
            orderDirection="asc"
            whereFilters={[
              { field: searchField, operator: '>=', value: searchKeyword },
              { field: searchField, operator: '<', value: searchKeyword + '\uf8ff' }
            ]}
          >
            {#snippet item(itemData)}
              {@const user = (itemData.data ?? {}) as UserData}
              {@const uid = (itemData.id ?? '') as string}
              {@const displayName =
                typeof user.displayName === 'string' ? user.displayName : uid}
              {@const email = typeof user.email === 'string' ? user.email : ''}
              <button
                type="button"
                class="user-item"
                onclick={() => handleUserClick(user, uid)}
              >
                <Avatar uid={uid} size={48} class="shadow-sm" />
                <div class="user-info">
                  <h4 class="user-name">{displayName}</h4>
                  {#if email}
                    <p class="user-email">{email}</p>
                  {/if}
                  <p class="user-uid">UID: {uid}</p>
                </div>
              </button>
            {/snippet}

            {#snippet loading()}
              <p class="status-message">사용자 검색 중...</p>
            {/snippet}

            {#snippet empty()}
              <div class="status-message">
                <p>"{searchKeyword}" 와 일치하는 사용자를 찾을 수 없습니다.</p>
              </div>
            {/snippet}
          </FirestoreListView>
        {/key}
      </div>
    {/if}
  </DialogContent>
</Dialog>

<style>
  @import 'tailwindcss' reference;

  .user-search-dialog :global(.search-input) {
    @apply w-full rounded-xl border border-gray-300 px-4 py-3 text-base text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900;
  }

  .user-search-dialog :global(.search-label) {
    @apply text-sm font-semibold text-gray-700;
  }

  .user-search-dialog :global(.search-hint) {
    @apply text-sm leading-relaxed text-gray-500;
  }

  /* 검색 결과 컨테이너 */
  .search-results {
    @apply mt-4 max-h-96 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50;
  }

  .results-title {
    @apply sticky top-0 border-b border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700;
  }

  /* 사용자 아이템 */
  .user-item {
    @apply flex w-full items-center gap-3 border-b border-gray-100 bg-white p-4 text-left transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500;
  }

  .user-item:last-child {
    @apply border-b-0;
  }

  .user-info {
    @apply flex-1 space-y-1;
  }

  .user-name {
    @apply text-base font-semibold text-gray-900;
  }

  .user-email {
    @apply text-sm text-gray-600;
  }

  .user-uid {
    @apply text-xs text-gray-400;
  }

  /* 상태 메시지 */
  .status-message {
    @apply py-8 text-center text-sm text-gray-500;
  }
</style>
