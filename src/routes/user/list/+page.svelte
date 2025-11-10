<script lang="ts">
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
  import Avatar from '$lib/components/user/avatar.svelte';
  import { getLocale } from '$lib/paraglide/runtime.js';

  // 다국어 메시지를 전체 import 후 개별 변수로 할당
  import * as messages from '$lib/paraglide/messages.js';

  const 페이지_타이틀_사용자목록 = (messages as any)['페이지_타이틀_사용자목록'];
  const 사용자_목록 = (messages as any)['사용자_목록'];
  const 사용자_목록_안내 = (messages as any)['사용자_목록_안내'];
  const 사용자_프로필_상세 = (messages as any)['사용자_프로필_상세'];
  const 사용자_이름없음 = (messages as any)['사용자_이름없음'];
  const 사용자_가입일 = (messages as any)['사용자_가입일'];
  const 사용자_마지막로그인 = (messages as any)['사용자_마지막로그인'];
  const 사용자_로딩중 = (messages as any)['사용자_로딩중'];
  const 사용자_등록없음 = (messages as any)['사용자_등록없음'];
  const 사용자_가입없음 = (messages as any)['사용자_가입없음'];
  const 사용자_로드실패 = (messages as any)['사용자_로드실패'];
  const 사용자_알수없는오류 = (messages as any)['사용자_알수없는오류'];
  const 공통_다시시도 = (messages as any)['공통_다시시도'];
  const 사용자_더로딩중 = (messages as any)['사용자_더로딩중'];
  const 사용자_모두로드 = (messages as any)['사용자_모두로드'];
  const 네비_채팅 = (messages as any)['네비_채팅'];

  function formatDate(timestamp: number) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const currentLocale = getLocale();
    const locale =
      currentLocale === 'ko'
        ? 'ko-KR'
        : currentLocale === 'ja'
        ? 'ja-JP'
        : currentLocale === 'zh'
        ? 'zh-CN'
        : 'en-US';

    return date.toLocaleDateString(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
</script>

<svelte:head>
  <title>{페이지_타이틀_사용자목록()}</title>
</svelte:head>

<div class="user-list-page">
  <div class="page-header">
    <h1>{사용자_목록()}</h1>
    <p class="subtitle">{사용자_목록_안내()}</p>
  </div>

  <DatabaseListView path="users" pageSize={15} orderBy="createdAt" threshold={300} reverse={false}>
    {#snippet item(itemData: { key: string; data: any })}
      <article class="user-card">
        <a
          class="user-card-main"
          href={`/user/profile/${itemData.key}`}
          aria-label={사용자_프로필_상세()}
        >
          <div class="user-avatar">
            <Avatar uid={itemData.key} size={60} class="shadow-sm" />
          </div>

          <div class="user-info">
            <h3 class="user-name">{itemData.data?.displayName || 사용자_이름없음()}</h3>
            <p class="user-email">{itemData.data?.email || 'email@example.com'}</p>
            <div class="user-meta">
              <span class="meta-item">
                <span class="meta-label">{사용자_가입일()}</span>
                <span class="meta-value">{formatDate(itemData.data?.createdAt)}</span>
              </span>
              {#if itemData.data?.lastLoginAt}
                <span class="meta-item">
                  <span class="meta-label">{사용자_마지막로그인()}</span>
                  <span class="meta-value">{formatDate(itemData.data.lastLoginAt)}</span>
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
            {네비_채팅()}
          </a>
        </div>
      </article>
    {/snippet}

    {#snippet loading()}
      <div class="loading-state">
        <div class="spinner"></div>
        <p>{사용자_로딩중()}</p>
      </div>
    {/snippet}

    {#snippet empty()}
      <div class="empty-state">
        <div class="empty-icon">👥</div>
        <h3>{사용자_등록없음()}</h3>
        <p>{사용자_가입없음()}</p>
      </div>
    {/snippet}

    {#snippet error(errorMessage: string | null)}
      <div class="error-state">
        <div class="error-icon">⚠️</div>
        <h3>{사용자_로드실패()}</h3>
        <p class="error-message">{errorMessage ?? 사용자_알수없는오류()}</p>
        <button class="retry-button" onclick={() => window.location.reload()}>
          {공통_다시시도()}
        </button>
      </div>
    {/snippet}

    {#snippet loadingMore()}
      <div class="loading-more-state">
        <div class="spinner small"></div>
        <p>{사용자_더로딩중()}</p>
      </div>
    {/snippet}

    {#snippet noMore()}
      <div class="no-more-state">
        <p>{사용자_모두로드()}</p>
      </div>
    {/snippet}
  </DatabaseListView>
</div>

<style>
  .user-list-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
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
