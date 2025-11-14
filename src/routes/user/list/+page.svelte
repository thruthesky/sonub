<script lang="ts">
  /**
   * 사용자 목록 페이지
   *
   * Firebase Realtime Database의 /users 경로에서 사용자 목록을 불러와 표시합니다.
   * DatabaseListView 컴포넌트를 사용하여 페이지네이션과 무한 스크롤을 지원합니다.
   */

  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
  import Avatar from '$lib/components/user/avatar.svelte';
  import UserSearchDialog from '$lib/components/user/UserSearchDialog.svelte';
  import { Button } from '$lib/components/ui/button/index.js';
  import { formatLongDate } from '$lib/functions/date.functions';
  import { m } from '$lib/paraglide/messages.js';

  const DEFAULT_PAGE_SIZE = 15;

  // 정렬 필드 옵션
  type SortField = 'createdAt' | 'sort_recentWithPhoto' | 'sort_recentFemaleWithPhoto' | 'sort_recentMaleWithPhoto';

  const sortOptions = [
    { value: 'createdAt' as SortField, label: '전체 회원' },
    { value: 'sort_recentWithPhoto' as SortField, label: '사진있는 회원' },
    { value: 'sort_recentFemaleWithPhoto' as SortField, label: '사진있는 여성' },
    { value: 'sort_recentMaleWithPhoto' as SortField, label: '사진있는 남성' }
  ];

  let searchDialogOpen = $state(false);
  let dialogKeyword = $state('');
  let activeSearch = $state('');
  let selectedSortField = $state<SortField>('createdAt');

  const normalizedSearch = $derived.by(() => activeSearch.trim());
  const isSearching = $derived.by(() => normalizedSearch.length > 0);
  const listKey = $derived.by(() =>
    isSearching ? `users-search-${normalizedSearch}` : `users-${selectedSortField}`
  );
  const listOrderBy = $derived.by(() => (isSearching ? 'displayNameLowerCase' : selectedSortField));
  const listPageSize = $derived.by(() => (isSearching ? 50 : DEFAULT_PAGE_SIZE));

  function openSearchDialog() {
    dialogKeyword = activeSearch;
    searchDialogOpen = true;
  }

  function handleDialogSearch(event: CustomEvent<{ keyword: string }>) {
    const { keyword } = event.detail;
    activeSearch = keyword;
    dialogKeyword = keyword;
  }

  function handleDialogClear() {
    clearSearch();
  }

  function clearSearch() {
    dialogKeyword = '';
    activeSearch = '';
    searchDialogOpen = false;
  }
</script>

<svelte:head>
  <title>{m.pageTitleUserList()}</title>
</svelte:head>

<div class="user-list-page">
  <div class="page-header">
    <h1>{m.userList()}</h1>
    <p class="subtitle">{m.userListGuide()}</p>
  </div>

  <div class="search-toolbar">
    <div class="toolbar-left">
      <select
        class="sort-select"
        bind:value={selectedSortField}
      >
        {#each sortOptions as option}
          <option value={option.value}>{option.label}</option>
        {/each}
      </select>

      {#if isSearching}
        <div class="search-chip">
          <span>"{normalizedSearch}" 검색 결과</span>
          <button type="button" onclick={clearSearch}>초기화</button>
        </div>
      {/if}
    </div>

    <Button
      type="button"
      variant="default"
      class="search-button bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg"
      onclick={openSearchDialog}
    >
      사용자 검색
    </Button>
  </div>

  <UserSearchDialog
    bind:open={searchDialogOpen}
    bind:keyword={dialogKeyword}
    on:search={handleDialogSearch}
    on:clear={handleDialogClear}
  />

  {#key listKey}
    <DatabaseListView
      path="users"
      pageSize={listPageSize}
      orderBy={listOrderBy}
      threshold={300}
      reverse={false}
      equalToValue={isSearching ? normalizedSearch : undefined}
    >
    {#snippet item(itemData: { key: string; data: any })}
      <article class="user-card">
        <a
          class="user-card-main"
          href={`/user/profile?uid=${itemData.key}`}
          aria-label={m.userProfileDetail()}
        >
          <div class="user-avatar">
            <Avatar uid={itemData.key} size={60} class="shadow-sm" />
          </div>

          <div class="user-info">
            <h3 class="user-name">{itemData.data?.displayName || m.userNoName()}</h3>
            <p class="user-email">{itemData.data?.email || 'email@example.com'}</p>
            <div class="user-meta">
              <span class="meta-item">
                <span class="meta-label">{m.userJoinDate()}</span>
                <span class="meta-value">{formatLongDate(itemData.data?.createdAt)}</span>
              </span>
              {#if itemData.data?.lastLoginAt}
                <span class="meta-item">
                  <span class="meta-label">{m.userLastLogin()}</span>
                  <span class="meta-value">{formatLongDate(itemData.data.lastLoginAt)}</span>
                </span>
              {/if}
            </div>
          </div>

          <div class="user-actions" aria-hidden="true">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </a>

        <div class="user-card-chips">
          <a class="chip chip-primary cursor-pointer" href={`/chat/room?uid=${itemData.key}`}>
            {m.navChat()}
          </a>
        </div>
      </article>
    {/snippet}

    {#snippet loading()}
      <div class="loading-state">
        <div class="spinner"></div>
        <p>{m.userLoading()}</p>
      </div>
    {/snippet}

    {#snippet empty()}
      <div class="empty-state">
        <div class="empty-icon">👥</div>
        <h3>{m.userNotRegistered()}</h3>
        <p>{m.userNotJoined()}</p>
      </div>
    {/snippet}

    {#snippet error(errorMessage: string | null)}
      <div class="error-state">
        <div class="error-icon">⚠️</div>
        <h3>{m.userLoadFailed()}</h3>
        <p class="error-message">{errorMessage ?? m.userUnknownError()}</p>
        <button class="retry-button" onclick={() => window.location.reload()}>
          {m.commonRetry()}
        </button>
      </div>
    {/snippet}

    {#snippet loadingMore()}
      <div class="loading-more-state">
        <div class="spinner small"></div>
        <p>{m.userLoadingMore()}</p>
      </div>
    {/snippet}

    {#snippet noMore()}
      <div class="no-more-state">
        <p>{m.userAllLoaded()}</p>
      </div>
    {/snippet}
    </DatabaseListView>
  {/key}
</div>

<style>
  .user-list-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }

  .search-toolbar {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: stretch;
    margin-bottom: 1.5rem;
  }

  @media (min-width: 640px) {
    .search-toolbar {
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
    }
  }

  .toolbar-left {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }

  @media (min-width: 640px) {
    .toolbar-left {
      flex-direction: row;
      align-items: center;
    }
  }

  .sort-select {
    min-width: 180px;
    padding: 0.5rem 2.5rem 0.5rem 0.75rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
    color: #1f2937;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    transition: background-color 0.2s, border-color 0.2s;
  }

  .sort-select:hover {
    background-color: #f9fafb;
    border-color: #d1d5db;
  }

  .sort-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  :global(.search-button) {
    white-space: nowrap;
  }

  @media (max-width: 640px) {
    :global(.search-button) {
      width: 100%;
    }
  }

  .search-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background-color: #111827;
    color: #f9fafb;
    font-size: 0.875rem;
  }

  .search-chip button {
    background: transparent;
    border: none;
    color: #fbbf24;
    font-size: 0.8rem;
    cursor: pointer;
  }

  .page-header {
    margin-bottom: 2rem;
    text-align: center;
  }

  .page-header h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
  }

  .subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
  }

  .user-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    margin-bottom: 0.75rem;
    background-color: #ffffff;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .user-card-main {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
  }

  .user-avatar {
    flex-shrink: 0;
  }

  .user-info {
    flex: 1;
    min-width: 0;
  }

  .user-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.25rem 0;
  }

  .user-email {
    margin: 0;
    color: #6b7280;
    font-size: 0.95rem;
  }

  .user-meta {
    margin-top: 0.75rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    color: #6b7280;
    font-size: 0.875rem;
  }

  .meta-item {
    display: flex;
    flex-direction: column;
  }

  .meta-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
    margin-bottom: 0.15rem;
  }

  .meta-value {
    font-weight: 500;
    color: #1f2937;
  }

  .user-actions {
    flex-shrink: 0;
    color: #9ca3af;
  }

  .user-card-chips {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 0.35rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
  }

  .chip-primary {
    background: #111827;
    color: #ffffff;
  }

  .chip-primary:hover {
    background: #1f2937;
  }

  .loading-state,
  .loading-more-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    gap: 1rem;
  }

  .loading-more-state {
    padding: 2rem 1rem;
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  .spinner.small {
    width: 24px;
    height: 24px;
    border-width: 3px;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .loading-state p,
  .loading-more-state p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
  }

  .empty-state,
  .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 1rem;
    text-align: center;
  }

  .empty-icon,
  .error-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
  }

  .empty-state h3,
  .error-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
  }

  .error-state h3 {
    color: #dc2626;
  }

  .error-message {
    color: #6b7280;
    margin: 0 0 1rem 0;
  }

  .retry-button {
    padding: 0.5rem 1rem;
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .retry-button:hover {
    background-color: #2563eb;
  }

  .no-more-state {
    padding: 2rem 1rem;
    text-align: center;
  }

  .no-more-state p {
    margin: 0;
    color: #9ca3af;
    font-size: 0.875rem;
  }

  @media (max-width: 640px) {
    .user-list-page {
      padding: 1rem 0.5rem;
    }

    .page-header h1 {
      font-size: 1.5rem;
    }

    .user-card {
      padding: 0.75rem;
    }

    .user-meta {
      flex-direction: column;
      gap: 0.5rem;
    }
  }
</style>
