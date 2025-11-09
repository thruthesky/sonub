<script lang="ts">
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';

  /**
   * /user/list 페이지
   *
   * Firebase Realtime Database의 users 노드에서 사용자 목록을 가져와
   * 무한 스크롤로 표시합니다.
   */

  /**
   * 날짜 포맷팅 함수
   */
  function formatDate(timestamp: number) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }
</script>

<svelte:head>
  <title>사용자 목록 - Sonub</title>
</svelte:head>

<div class="user-list-page">
  <div class="page-header">
    <h1>사용자 목록</h1>
    <p class="subtitle">Firebase Realtime Database에 등록된 모든 사용자를 확인하세요</p>
  </div>

  <DatabaseListView
    path="users"
    pageSize={15}
    orderBy="createdAt"
    threshold={300}
    reverse={false}
  >
    {#snippet item(itemData: { key: string; data: any })}
      <a class="user-card" href={`/user/profile/${itemData.key}`} aria-label="사용자 프로필 상세">
        <div class="user-avatar">
          {#if itemData.data?.photoUrl}
            <img src={itemData.data.photoUrl} alt={itemData.data?.displayName || '사용자'} />
          {:else}
            <div class="avatar-placeholder">
              {itemData.data?.displayName?.charAt(0)?.toUpperCase() || '?'}
            </div>
          {/if}
        </div>

        <div class="user-info">
          <h3 class="user-name">
            {itemData.data?.displayName || '이름 없음'}
          </h3>
          <p class="user-email">
            {itemData.data?.email || 'email@example.com'}
          </p>
          <div class="user-meta">
            <span class="meta-item">
              <span class="meta-label">가입일:</span>
              <span class="meta-value">
                {formatDate(itemData.data?.createdAt)}
              </span>
            </span>
            {#if itemData.data?.lastLoginAt}
              <span class="meta-item">
                <span class="meta-label">마지막 로그인:</span>
                <span class="meta-value">
                  {formatDate(itemData.data.lastLoginAt)}
                </span>
              </span>
            {/if}
          </div>
        </div>

        <div class="user-actions">
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
    {/snippet}

    {#snippet loading()}
      <div class="loading-state">
        <div class="spinner"></div>
        <p>사용자 목록을 불러오는 중...</p>
      </div>
    {/snippet}

    {#snippet empty()}
      <div class="empty-state">
        <div class="empty-icon">👥</div>
        <h3>등록된 사용자가 없습니다</h3>
        <p>아직 가입한 사용자가 없습니다.</p>
      </div>
    {/snippet}

    {#snippet error(errorMessage: string | null)}
      <div class="error-state">
        <div class="error-icon">⚠️</div>
        <h3>사용자 목록을 불러올 수 없습니다</h3>
        <p class="error-message">{errorMessage ?? '알 수 없는 오류가 발생했습니다.'}</p>
        <button class="retry-button" onclick={() => window.location.reload()}>
          다시 시도
        </button>
      </div>
    {/snippet}

    {#snippet loadingMore()}
      <div class="loading-more-state">
        <div class="spinner small"></div>
        <p>더 많은 사용자를 불러오는 중...</p>
      </div>
    {/snippet}

    {#snippet noMore()}
      <div class="no-more-state">
        <p>모든 사용자를 불러왔습니다</p>
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

  /* 사용자 카드 */
  .user-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    background-color: #ffffff;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
  }

  .user-card:hover {
    background-color: #f9fafb;
    border-color: #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .user-card:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
  }

  /* 아바타 */
  .user-avatar {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
  }

  .user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
  }

  /* 사용자 정보 */
  .user-info {
    flex: 1;
    min-width: 0;
  }

  .user-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.25rem 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .user-email {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0 0 0.5rem 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .user-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.75rem;
  }

  .meta-item {
    display: flex;
    gap: 0.25rem;
  }

  .meta-label {
    color: #9ca3af;
  }

  .meta-value {
    color: #6b7280;
    font-weight: 500;
  }

  /* 액션 아이콘 */
  .user-actions {
    flex-shrink: 0;
    color: #9ca3af;
    transition: color 0.2s;
  }

  .user-card:hover .user-actions {
    color: #3b82f6;
  }

  /* 로딩 상태 */
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

  /* 빈 상태 */
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 1rem;
    text-align: center;
  }

  .empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
  }

  .empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
  }

  .empty-state p {
    color: #6b7280;
    margin: 0;
  }

  /* 에러 상태 */
  .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 1rem;
    text-align: center;
  }

  .error-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
  }

  .error-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #dc2626;
    margin: 0 0 0.5rem 0;
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

  .retry-button:active {
    background-color: #1d4ed8;
  }

  /* 더 이상 없음 상태 */
  .no-more-state {
    padding: 2rem 1rem;
    text-align: center;
  }

  .no-more-state p {
    margin: 0;
    color: #9ca3af;
    font-size: 0.875rem;
  }

  /* 반응형 디자인 */
  @media (max-width: 640px) {
    .user-list-page {
      padding: 1rem 0.5rem;
    }

    .page-header h1 {
      font-size: 1.5rem;
    }

    .user-card {
      padding: 0.75rem;
      gap: 0.75rem;
    }

    .user-avatar {
      width: 48px;
      height: 48px;
    }

    .avatar-placeholder {
      font-size: 1.25rem;
    }

    .user-name {
      font-size: 1rem;
    }

    .user-meta {
      flex-direction: column;
      gap: 0.25rem;
    }
  }
</style>
