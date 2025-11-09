<script lang="ts">
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';

  /**
   * DatabaseListView 컴포넌트 테스트 페이지
   *
   * 다양한 props 조합으로 DatabaseListView 컴포넌트를 테스트합니다:
   * 1. 기본 사용 (정순 정렬)
   * 2. 역순 정렬 (reverse=true)
   * 3. orderPrefix 필터링
   * 4. 컨테이너 스크롤
   */

  /**
   * 현재 활성화된 테스트 케이스
   */
  let activeTest = $state<string>('basic');

  /**
   * 테스트 케이스 목록
   */
  const testCases = [
    { id: 'basic', label: '기본 사용 (정순)' },
    { id: 'reverse', label: '역순 정렬' },
    { id: 'prefix', label: 'orderPrefix 필터링' },
    { id: 'container', label: '컨테이너 스크롤' }
  ];
</script>

<svelte:head>
  <title>DatabaseListView 테스트 - Sonub Admin</title>
</svelte:head>

<div class="test-page">
  <div class="page-header">
    <h1>DatabaseListView 테스트</h1>
    <p class="subtitle">다양한 props 조합으로 컴포넌트 동작을 확인하세요</p>
  </div>

  <!-- 테스트 케이스 선택 -->
  <div class="test-selector">
    {#each testCases as testCase}
      <button
        class="test-button"
        class:active={activeTest === testCase.id}
        onclick={() => (activeTest = testCase.id)}
      >
        {testCase.label}
      </button>
    {/each}
  </div>

  <!-- 테스트 케이스 1: 기본 사용 (정순) -->
  {#if activeTest === 'basic'}
    <div class="test-case">
      <div class="test-info">
        <h2>테스트 1: 기본 사용 (정순 정렬)</h2>
        <div class="code-block">
          <pre><code>&lt;DatabaseListView
  path="users"
  pageSize={'{'}10{'}'}
  orderBy="createdAt"
  threshold={'{'}300{'}'}
  reverse={'{'}false{'}'}
/&gt;</code></pre>
        </div>
        <ul class="test-description">
          <li>Firebase RTDB의 "users" 경로에서 데이터 로드</li>
          <li>createdAt 필드로 정렬 (오래된 사용자부터)</li>
          <li>한 번에 10개씩 로드</li>
          <li>스크롤이 바닥에서 300px 이내로 오면 다음 페이지 로드</li>
        </ul>
      </div>

      <DatabaseListView path="users" pageSize={10} orderBy="createdAt" threshold={300} reverse={false}>
        {#snippet item(itemData)}
          <div class="test-item">
            <div class="item-header">
              <strong>{itemData.data?.displayName || '이름 없음'}</strong>
              <span class="item-key">Key: {itemData.key}</span>
            </div>
            <div class="item-content">
              <p>이메일: {itemData.data?.email || 'N/A'}</p>
              <p>
                가입일: {itemData.data?.createdAt
                  ? new Date(itemData.data.createdAt).toLocaleString('ko-KR')
                  : 'N/A'}
              </p>
            </div>
          </div>
        {/snippet}
      </DatabaseListView>
    </div>
  {/if}

  <!-- 테스트 케이스 2: 역순 정렬 -->
  {#if activeTest === 'reverse'}
    <div class="test-case">
      <div class="test-info">
        <h2>테스트 2: 역순 정렬</h2>
        <div class="code-block">
          <pre><code>&lt;DatabaseListView
  path="users"
  pageSize={'{'}10{'}'}
  orderBy="createdAt"
  threshold={'{'}300{'}'}
  reverse={'{'}true{'}'}
/&gt;</code></pre>
        </div>
        <ul class="test-description">
          <li>reverse=true로 최신 사용자부터 표시</li>
          <li>createdAt 값이 큰 것부터 작은 순서로 정렬</li>
          <li>새로 가입한 사용자가 먼저 표시됨</li>
        </ul>
      </div>

      <DatabaseListView path="users" pageSize={10} orderBy="createdAt" threshold={300} reverse={true}>
        {#snippet item(itemData)}
          <div class="test-item reverse">
            <div class="item-header">
              <strong>{itemData.data?.displayName || '이름 없음'}</strong>
              <span class="item-badge">최신순</span>
            </div>
            <div class="item-content">
              <p>이메일: {itemData.data?.email || 'N/A'}</p>
              <p>
                가입일: {itemData.data?.createdAt
                  ? new Date(itemData.data.createdAt).toLocaleString('ko-KR')
                  : 'N/A'}
              </p>
            </div>
          </div>
        {/snippet}
      </DatabaseListView>
    </div>
  {/if}

  <!-- 테스트 케이스 3: orderPrefix 필터링 -->
  {#if activeTest === 'prefix'}
    <div class="test-case">
      <div class="test-info">
        <h2>테스트 3: orderPrefix 필터링</h2>
        <div class="code-block">
          <pre><code>&lt;DatabaseListView
  path="posts"
  pageSize={'{'}15{'}'}
  orderBy="categoryKey"
  orderPrefix="community-"
  threshold={'{'}300{'}'}
/&gt;</code></pre>
        </div>
        <ul class="test-description">
          <li>orderPrefix를 사용하여 특정 prefix로 시작하는 데이터만 필터링</li>
          <li>categoryKey가 "community-"로 시작하는 게시글만 표시</li>
          <li>Firebase 쿼리 레벨에서 필터링되어 효율적</li>
        </ul>
        <div class="alert">
          <strong>참고:</strong> 이 테스트는 "posts" 경로에 categoryKey 필드가 있어야 동작합니다.
          데이터가 없으면 빈 상태가 표시됩니다.
        </div>
      </div>

      <DatabaseListView
        path="posts"
        pageSize={15}
        orderBy="categoryKey"
        orderPrefix="community-"
        threshold={300}
      >
        {#snippet item(itemData)}
          <div class="test-item prefix">
            <div class="item-header">
              <strong>{itemData.data?.title || '제목 없음'}</strong>
              <span class="item-badge">Community</span>
            </div>
            <div class="item-content">
              <p>카테고리 키: {itemData.data?.categoryKey || 'N/A'}</p>
              <p>내용: {itemData.data?.content?.substring(0, 100) || 'N/A'}...</p>
            </div>
          </div>
        {/snippet}

        {#snippet empty()}
          <div class="empty-placeholder">
            <p>💡 "posts" 경로에 categoryKey가 "community-"로 시작하는 데이터가 없습니다.</p>
            <p class="small">테스트 데이터를 추가하려면 Firebase Console에서 다음 구조로 데이터를 생성하세요:</p>
            <div class="code-block small">
              <pre><code>posts/
  postId1/
    title: "테스트 게시글"
    categoryKey: "community-12345"
    content: "내용..."</code></pre>
            </div>
          </div>
        {/snippet}
      </DatabaseListView>
    </div>
  {/if}

  <!-- 테스트 케이스 4: 컨테이너 스크롤 -->
  {#if activeTest === 'container'}
    <div class="test-case">
      <div class="test-info">
        <h2>테스트 4: 컨테이너 스크롤</h2>
        <div class="code-block">
          <pre><code>&lt;div class="scroll-container"&gt;
  &lt;DatabaseListView
    path="users"
    pageSize={'{'}10{'}'}
    orderBy="createdAt"
    threshold={'{'}200{'}'}
  /&gt;
&lt;/div&gt;

&lt;style&gt;
  .scroll-container {'{'}
    height: 600px;
    overflow-y: auto;
  {'}'}
&lt;/style&gt;</code></pre>
        </div>
        <ul class="test-description">
          <li>고정 높이의 컨테이너 내에서 스크롤</li>
          <li>window 스크롤이 아닌 컨테이너 스크롤 감지</li>
          <li>페이지 레이아웃과 독립적으로 동작</li>
        </ul>
      </div>

      <div class="scroll-container">
        <DatabaseListView path="users" pageSize={10} orderBy="createdAt" threshold={200}>
          {#snippet item(itemData)}
            <div class="test-item container">
              <div class="item-header">
                <strong>{itemData.data?.displayName || '이름 없음'}</strong>
                <span class="item-badge">컨테이너 스크롤</span>
              </div>
              <div class="item-content">
                <p>이메일: {itemData.data?.email || 'N/A'}</p>
                <p>
                  가입일: {itemData.data?.createdAt
                    ? new Date(itemData.data.createdAt).toLocaleString('ko-KR')
                    : 'N/A'}
                </p>
              </div>
            </div>
          {/snippet}
        </DatabaseListView>
      </div>
    </div>
  {/if}
</div>

<style>
  .test-page {
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

  /* 테스트 선택 버튼 */
  .test-selector {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    justify-content: center;
  }

  .test-button {
    padding: 0.75rem 1.5rem;
    background-color: #ffffff;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
  }

  .test-button:hover {
    border-color: #d1d5db;
    background-color: #f9fafb;
  }

  .test-button.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
  }

  /* 테스트 케이스 */
  .test-case {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
  }

  .test-info {
    margin-bottom: 2rem;
  }

  .test-info h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1rem 0;
  }

  .code-block {
    background-color: #1f2937;
    color: #e5e7eb;
    padding: 1rem;
    border-radius: 0.5rem;
    margin: 1rem 0;
    overflow-x: auto;
  }

  .code-block.small {
    font-size: 0.75rem;
  }

  .code-block pre {
    margin: 0;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
  }

  .code-block code {
    color: #e5e7eb;
  }

  .test-description {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
  }

  .test-description li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
    color: #4b5563;
  }

  .test-description li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #10b981;
    font-weight: bold;
  }

  .alert {
    background-color: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 1rem 0;
  }

  .alert strong {
    color: #92400e;
  }

  /* 테스트 아이템 */
  .test-item {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
  }

  .test-item:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .test-item.reverse {
    border-left: 4px solid #3b82f6;
  }

  .test-item.prefix {
    border-left: 4px solid #10b981;
  }

  .test-item.container {
    border-left: 4px solid #f59e0b;
  }

  .item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .item-header strong {
    font-size: 1rem;
    color: #1f2937;
  }

  .item-key {
    font-size: 0.75rem;
    color: #9ca3af;
    font-family: 'Courier New', monospace;
  }

  .item-badge {
    padding: 0.25rem 0.5rem;
    background-color: #3b82f6;
    color: white;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
  }

  .test-item.prefix .item-badge {
    background-color: #10b981;
  }

  .test-item.container .item-badge {
    background-color: #f59e0b;
  }

  .item-content {
    font-size: 0.875rem;
    color: #6b7280;
  }

  .item-content p {
    margin: 0.25rem 0;
  }

  /* 스크롤 컨테이너 */
  .scroll-container {
    height: 600px;
    overflow-y: auto;
    overflow-x: hidden;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    background-color: #f9fafb;
  }

  /* 빈 상태 플레이스홀더 */
  .empty-placeholder {
    padding: 3rem 1rem;
    text-align: center;
    color: #6b7280;
  }

  .empty-placeholder p {
    margin: 0.5rem 0;
  }

  .empty-placeholder .small {
    font-size: 0.875rem;
    color: #9ca3af;
  }

  /* 반응형 디자인 */
  @media (max-width: 640px) {
    .test-page {
      padding: 1rem 0.5rem;
    }

    .page-header h1 {
      font-size: 1.5rem;
    }

    .test-selector {
      flex-direction: column;
    }

    .test-button {
      width: 100%;
    }

    .scroll-container {
      height: 400px;
    }
  }
</style>
