<!--
  FirestoreListView - Firestore 무한 스크롤 리스트 뷰 컴포넌트

  재사용 가능한 무한 스크롤 컴포넌트로, Firestore의 데이터를
  페이지네이션과 함께 표시합니다.

  사용 예시:
  ```svelte
  <FirestoreListView
    path="users"
    pageSize={20}
    orderByField="createdAt"
    orderDirection="desc"
    threshold={300}
  >
    {#snippet item(itemData, index)}
      <div class="user-card">
        <h3>{itemData.data.displayName}</h3>
        <p>{itemData.data.email}</p>
      </div>
    {/snippet}

    {#snippet loading()}
      <p>로딩 중...</p>
    {/snippet}

    {#snippet empty()}
      <p>데이터가 없습니다.</p>
    {/snippet}
  </FirestoreListView>
  ```
-->

<script lang="ts">
  import {
    collection,
    doc,
    query,
    orderBy as firestoreOrderBy,
    limit,
    startAfter,
    endBefore,
    where,
    getDocs,
    onSnapshot,
    type QueryConstraint,
    type DocumentData,
    type Query,
    type Unsubscribe,
    type DocumentSnapshot,
    type OrderByDirection
  } from 'firebase/firestore';
  import type { Snippet } from 'svelte';
  import { tick } from 'svelte';
  import { db } from '$lib/firebase';

  /**
   * 아이템 데이터 타입
   */
  type ItemData = {
    id: string; // Firestore는 key 대신 id 사용
    data: any;
  };

  type ItemSnippet = Snippet<[itemData: ItemData, index: number]>;
  type StatusSnippet = Snippet<[]>;
  type ErrorSnippet = Snippet<[errorMessage: string | null]>;

  // ============================================================================
  // Props (컴포넌트 속성)
  // ============================================================================

  /**
   * 컴포넌트 Props
   * - path: Firestore Collection 경로 (예: "users" 또는 "chats/room123/messages")
   * - pageSize: 한 번에 가져올 아이템 개수 (기본값: 10)
   * - orderByField: 정렬 기준 필드 (기본값: "createdAt")
   * - orderDirection: 정렬 방향 "asc" | "desc" (기본값: "asc")
   * - whereFilters: WHERE 조건 배열 (선택 사항)
   *   - 예: [{ field: 'status', operator: '==', value: 'active' }]
   * - threshold: 스크롤 threshold (px) - 바닥에서 이 값만큼 떨어지면 다음 페이지 로드 (기본값: 300)
   * - scrollTrigger: 스크롤 트리거 위치 (기본값: "bottom")
   *   - "bottom": 아래로 스크롤하면 다음 페이지 로드 (일반 목록)
   *   - "top": 위로 스크롤하면 이전 페이지 로드 (채팅방 스타일)
   * - autoScrollToEnd: 초기 로드 후 자동으로 스크롤을 맨 아래로 이동 (기본값: false)
   * - autoScrollOnNewData: 새 데이터 추가 시 자동 스크롤 여부 (기본값: false)
   * - onItemAdded: 새 데이터 추가 시 호출되는 콜백 함수 (선택 사항)
   * - item: 아이템 렌더링 snippet
   * - loading: 로딩 상태 snippet
   * - empty: 빈 상태 snippet
   * - error: 에러 상태 snippet
   * - loadingMore: 더 로드 중 snippet
   * - noMore: 더 이상 데이터 없음 snippet
   */
  interface Props {
    path?: string;
    pageSize?: number;
    orderByField?: string;
    orderDirection?: OrderByDirection;
    whereFilters?: Array<{
      field: string;
      operator: '<' | '<=' | '==' | '!=' | '>=' | '>' | 'array-contains' | 'array-contains-any' | 'in' | 'not-in';
      value: any;
    }>;
    threshold?: number;
    scrollTrigger?: 'bottom' | 'top';
    autoScrollToEnd?: boolean;
    autoScrollOnNewData?: boolean;
    onItemAdded?: (item: ItemData) => void;
    item: ItemSnippet;
    loading?: StatusSnippet;
    empty?: StatusSnippet;
    error?: ErrorSnippet;
    loadingMore?: StatusSnippet;
    noMore?: StatusSnippet;
  }

  let {
    path = '',
    pageSize = 10,
    orderByField = 'createdAt',
    orderDirection = 'asc',
    whereFilters = [],
    threshold = 300,
    scrollTrigger = 'bottom',
    autoScrollToEnd = false,
    autoScrollOnNewData = false,
    onItemAdded = undefined,
    item,
    loading: loadingSnippet = undefined,
    empty = undefined,
    error: errorSnippet = undefined,
    loadingMore = undefined,
    noMore = undefined
  }: Props = $props();

  // ============================================================================
  // State (반응형 상태)
  // ============================================================================

  /**
   * 현재 표시 중인 아이템 목록
   */
  let items = $state<ItemData[]>([]);

  /**
   * 로딩 상태 (페이지 로드 중)
   */
  let loading = $state<boolean>(false);

  /**
   * 초기 로딩 상태 (첫 페이지 로드)
   */
  let initialLoading = $state<boolean>(true);

  /**
   * 더 가져올 데이터가 있는지 여부
   */
  let hasMore = $state<boolean>(true);

  /**
   * 마지막으로 로드한 문서 스냅샷
   * Firestore 페이지네이션의 startAfter/endBefore에 사용
   */
  let lastDocSnapshot = $state<DocumentSnapshot | null>(null);

  /**
   * 첫 번째 문서 스냅샷 (scrollTrigger='top'일 때 사용)
   */
  let firstDocSnapshot = $state<DocumentSnapshot | null>(null);

  /**
   * 현재 로드된 페이지 번호 (0부터 시작)
   */
  let currentPage = $state<number>(0);

  /**
   * 에러 메시지
   */
  let error = $state<string | null>(null);

  /**
   * onSnapshot 구독 해제 함수들을 관리하는 맵
   */
  let unsubscribers = new Map<string, Unsubscribe>();

  /**
   * Collection 전체 리스너 (새 문서 추가/삭제 감지)
   */
  let collectionUnsubscribe: Unsubscribe | null = null;

  /**
   * 스크롤 컨테이너 DOM 요소 참조
   */
  let scrollContainerRef: HTMLElement | null = null;

  /**
   * loadMore 작업 중 플래그
   */
  let isLoadingMore = $state<boolean>(false);

  /**
   * 각 아이템의 이전 orderBy 값을 추적하는 맵
   */
  let previousOrderByValues = new Map<string, any>();

  // ============================================================================
  // Lifecycle (생명주기)
  // ============================================================================

  /**
   * 컴포넌트 마운트 시 초기 데이터 로드
   */
  $effect(() => {
    const deps = {
      path,
      orderByField,
      orderDirection,
      pageSize,
      scrollTrigger,
      whereFilters: JSON.stringify(whereFilters)
    };

    if (deps.path && db) {
      loadInitialData();
    }

    return () => {
      // console.log('FirestoreListView: Cleaning up listeners');

      // Collection 리스너 해제
      if (collectionUnsubscribe) {
        collectionUnsubscribe();
        collectionUnsubscribe = null;
      }

      // 모든 문서 리스너 해제
      unsubscribers.forEach((unsubscribe) => {
        unsubscribe();
      });
      unsubscribers.clear();

      // orderBy 값 추적 맵 초기화
      previousOrderByValues.clear();
    };
  });

  /**
   * 스크롤 리스너 설정 action
   */
  function setupScrollListener(node: HTMLDivElement) {
    let actualScrollContainer: HTMLElement = node;
    let parent = node.parentElement;

    while (parent) {
      const overflowY = window.getComputedStyle(parent).overflowY;
      if (overflowY === 'auto' || overflowY === 'scroll') {
        actualScrollContainer = parent;
        break;
      }
      parent = parent.parentElement;
    }

    scrollContainerRef = actualScrollContainer;

    actualScrollContainer.addEventListener('scroll', handleScroll);
    window.addEventListener('scroll', handleWindowScroll);

    return {
      destroy() {
        actualScrollContainer.removeEventListener('scroll', handleScroll);
        window.removeEventListener('scroll', handleWindowScroll);
        scrollContainerRef = null;
      }
    };
  }

  // ============================================================================
  // Methods (메서드)
  // ============================================================================

  /**
   * 아이템 재배치 (orderBy 필드 변경 시)
   */
  function repositionItem(itemId: string, newData: any, newOrderByValue: any): void {
    const currentIndex = items.findIndex(item => item.id === itemId);
    if (currentIndex === -1) {
      console.warn(`[repositionItem] 아이템을 찾을 수 없음: ${itemId}`);
      return;
    }

    const updatedItem: ItemData = {
      id: itemId,
      data: newData
    };

    const itemsWithoutCurrent = items.filter(item => item.id !== itemId);

    // 새로운 orderBy 값에 따라 삽입 위치 찾기
    let insertIndex = 0;
    const ascending = orderDirection === 'asc';

    for (let i = 0; i < itemsWithoutCurrent.length; i++) {
      const compareValue = itemsWithoutCurrent[i].data[orderByField];

      if (ascending) {
        if (newOrderByValue < compareValue) {
          insertIndex = i;
          break;
        }
        insertIndex = i + 1;
      } else {
        if (newOrderByValue > compareValue) {
          insertIndex = i;
          break;
        }
        insertIndex = i + 1;
      }
    }

    const newItems = [
      ...itemsWithoutCurrent.slice(0, insertIndex),
      updatedItem,
      ...itemsWithoutCurrent.slice(insertIndex)
    ];

    items = newItems;

    // 리스너 재설정
    unsubscribers.forEach((unsubscribe) => unsubscribe());
    unsubscribers.clear();

    items.forEach((item) => {
      setupItemListener(item.id);
    });
  }

  /**
   * 각 문서에 onSnapshot 리스너 설정
   */
  function setupItemListener(itemId: string): void {
    if (!db) {
      console.error('FirestoreListView: Firestore is not initialized');
      return;
    }

    const listenerKey = `${itemId}`;
    if (unsubscribers.has(listenerKey)) {
      return;
    }

    const currentItem = items.find(item => item.id === itemId);
    if (currentItem) {
      const currentOrderByValue = currentItem.data[orderByField];
      previousOrderByValues.set(itemId, currentOrderByValue);
    }

    // path에서 document 참조 생성
    const docPath = path ? `${path}/${itemId}` : itemId;
    const docRef = doc(db, docPath);

    const unsubscribe = onSnapshot(
      docRef,
      (snapshot) => {
        if (snapshot.exists()) {
          const updatedData = snapshot.data();
          const newOrderByValue = updatedData[orderByField];
          const previousOrderByValue = previousOrderByValues.get(itemId);

          if (previousOrderByValue !== undefined && previousOrderByValue !== newOrderByValue) {
            previousOrderByValues.set(itemId, newOrderByValue);
            repositionItem(itemId, updatedData, newOrderByValue);
          } else {
            const itemIndex = items.findIndex(item => item.id === itemId);
            if (itemIndex !== -1) {
              items[itemIndex] = {
                id: itemId,
                data: updatedData
              };
              items = [...items];
            }
          }
        }
      },
      (err) => {
        console.error(`FirestoreListView: Error listening to item ${itemId}`, err);
      }
    );

    unsubscribers.set(listenerKey, unsubscribe);
  }

  /**
   * Collection 리스너 설정 (새 문서 추가/삭제 감지)
   */
  function setupCollectionListener() {
    if (!db) {
      console.error('FirestoreListView: Firestore is not initialized');
      return;
    }

    if (collectionUnsubscribe) {
      collectionUnsubscribe();
      collectionUnsubscribe = null;
    }

    const colRef = collection(db, path);

    // 쿼리 제약 조건 생성
    const constraints: QueryConstraint[] = [];

    // WHERE 조건 추가
    if (whereFilters && whereFilters.length > 0) {
      whereFilters.forEach(({ field, operator, value }) => {
        constraints.push(where(field, operator, value));
      });
    }

    // ORDER BY 추가
    constraints.push(firestoreOrderBy(orderByField, orderDirection));

    const q = query(colRef, ...constraints);

    // 초기화 플래그
    let initialized = false;

    collectionUnsubscribe = onSnapshot(q, (snapshot) => {
      if (!initialized) {
        // 초기 로드 시에는 기존 데이터는 무시
        initialized = true;
        return;
      }

      snapshot.docChanges().forEach((change) => {
        if (change.type === 'added') {
          const newItemId = change.doc.id;
          const newItemData = change.doc.data();

          // 중복 체크
          const exists = items.some(item => item.id === newItemId);
          if (exists) {
            return;
          }

          const newItem: ItemData = {
            id: newItemId,
            data: newItemData
          };

          // scrollTrigger와 orderDirection에 따라 배열 위치 결정
          if (scrollTrigger === 'top' && orderDirection === 'asc') {
            // 채팅방 스타일: 최신 메시지가 아래에 (배열 끝)
            items = [...items, newItem];
            setupItemListener(newItemId);
          } else if (orderDirection === 'desc') {
            // 역순 정렬: 최신 데이터가 위에 (배열 앞)
            items = [newItem, ...items];
            setupItemListener(newItemId);
          } else {
            // 정순 정렬: 오래된 데이터가 위에 (배열 끝)
            items = [...items, newItem];
            setupItemListener(newItemId);
          }

          // 부모 컴포넌트에 알림
          if (onItemAdded) {
            onItemAdded(newItem);
          }

          // 자동 스크롤
          if (autoScrollOnNewData && scrollContainerRef) {
            const { scrollTop, scrollHeight, clientHeight } = scrollContainerRef;
            const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);

            if (distanceFromBottom <= threshold) {
              tick().then(() => {
                if (scrollContainerRef) {
                  scrollContainerRef.scrollTop = scrollContainerRef.scrollHeight;
                }
              });
            }
          }
        } else if (change.type === 'removed') {
          const removedId = change.doc.id;
          const removedIndex = items.findIndex(item => item.id === removedId);

          if (removedIndex !== -1) {
            items = items.filter(item => item.id !== removedId);

            const listenerKey = `${removedId}`;
            const unsubscribe = unsubscribers.get(listenerKey);
            if (unsubscribe) {
              unsubscribe();
              unsubscribers.delete(listenerKey);
            }
          }
        }
      });
    }, (err) => {
      console.error('FirestoreListView: Error in collection listener', err);
    });
  }

  /**
   * 초기 데이터 로드
   */
  async function loadInitialData() {
    if (!db) {
      console.error('FirestoreListView: Firestore is not initialized');
      error = 'Firestore가 초기화되지 않았습니다.';
      initialLoading = false;
      return;
    }

    initialLoading = true;
    error = null;
    items = [];

    // 기존 리스너들 정리
    if (collectionUnsubscribe) {
      collectionUnsubscribe();
      collectionUnsubscribe = null;
    }
    unsubscribers.forEach((unsubscribe) => unsubscribe());
    unsubscribers.clear();
    previousOrderByValues.clear();

    lastDocSnapshot = null;
    firstDocSnapshot = null;
    hasMore = true;
    currentPage = 0;

    try {
      const colRef = collection(db, path);

      // 쿼리 제약 조건 생성
      const constraints: QueryConstraint[] = [];

      // WHERE 조건 추가
      if (whereFilters && whereFilters.length > 0) {
        whereFilters.forEach(({ field, operator, value }) => {
          constraints.push(where(field, operator, value));
        });
      }

      // ORDER BY 추가
      constraints.push(firestoreOrderBy(orderByField, orderDirection));

      // LIMIT 추가 (pageSize + 1개를 가져와서 hasMore 판단)
      constraints.push(limit(pageSize + 1));

      const q = query(colRef, ...constraints);
      const snapshot = await getDocs(q);

      if (!snapshot.empty) {
        const loadedItems: ItemData[] = [];

        snapshot.forEach((docSnap) => {
          loadedItems.push({
            id: docSnap.id,
            data: docSnap.data()
          });
        });

        // hasMore 판단
        if (loadedItems.length > pageSize) {
          hasMore = true;

          if (scrollTrigger === 'top') {
            // 채팅방 스타일: 첫 번째 문서를 버리고 나머지 사용
            items = loadedItems.slice(1);
            firstDocSnapshot = snapshot.docs[1];
            lastDocSnapshot = snapshot.docs[snapshot.docs.length - 1];
          } else {
            // 일반 목록: 마지막 문서를 버리고 앞부분 사용
            items = loadedItems.slice(0, pageSize);
            firstDocSnapshot = snapshot.docs[0];
            lastDocSnapshot = snapshot.docs[pageSize - 1];
          }
        } else {
          hasMore = false;
          items = loadedItems;
          firstDocSnapshot = snapshot.docs[0];
          lastDocSnapshot = snapshot.docs[snapshot.docs.length - 1];
        }

        // 각 문서에 리스너 설정
        items.forEach((item) => {
          setupItemListener(item.id);
        });
      } else {
        items = [];
        hasMore = false;
      }
    } catch (err) {
      console.error('FirestoreListView: Load error', err);
      error = err instanceof Error ? err.message : String(err);
    } finally {
      initialLoading = false;

      // Collection 리스너 설정
      setupCollectionListener();

      // 자동 스크롤
      if (autoScrollToEnd && scrollContainerRef) {
        setTimeout(() => {
          if (scrollContainerRef) {
            scrollContainerRef.scrollTop = scrollContainerRef.scrollHeight;
          }
        }, 100);
      }
    }
  }

  /**
   * 다음 페이지 데이터 로드
   */
  async function loadMore() {
    if (!db) {
      console.error('FirestoreListView: Firestore is not initialized');
      error = 'Firestore가 초기화되지 않았습니다.';
      return;
    }

    if (loading || !hasMore) {
      return;
    }

    const cursorSnapshot = scrollTrigger === 'top' ? firstDocSnapshot : lastDocSnapshot;
    if (!cursorSnapshot) {
      hasMore = false;
      return;
    }

    currentPage++;
    loading = true;
    isLoadingMore = true;
    error = null;

    try {
      const colRef = collection(db, path);

      // 쿼리 제약 조건 생성
      const constraints: QueryConstraint[] = [];

      // WHERE 조건 추가
      if (whereFilters && whereFilters.length > 0) {
        whereFilters.forEach(({ field, operator, value }) => {
          constraints.push(where(field, operator, value));
        });
      }

      // ORDER BY 추가
      constraints.push(firestoreOrderBy(orderByField, orderDirection));

      // Pagination 추가
      if (scrollTrigger === 'top') {
        // 채팅방 스타일: endBefore 사용
        constraints.push(endBefore(cursorSnapshot));
      } else {
        // 일반 목록: startAfter 사용
        constraints.push(startAfter(cursorSnapshot));
      }

      // LIMIT 추가
      constraints.push(limit(pageSize + 1));

      const q = query(colRef, ...constraints);
      const snapshot = await getDocs(q);

      if (!snapshot.empty) {
        const newItems: ItemData[] = [];

        snapshot.forEach((docSnap) => {
          newItems.push({
            id: docSnap.id,
            data: docSnap.data()
          });
        });

        // 중복 제거
        const existingIds = new Set(items.map(item => item.id));
        const uniqueItems = newItems.filter(item => !existingIds.has(item.id));

        if (uniqueItems.length === 0) {
          hasMore = false;
          loading = false;
          isLoadingMore = false;
          return;
        }

        // scrollTrigger='top'일 때 스크롤 위치 보존
        let scrollRestoreInfo: { scrollTop: number; scrollHeight: number } | null = null;
        if (scrollTrigger === 'top' && scrollContainerRef) {
          scrollRestoreInfo = {
            scrollTop: scrollContainerRef.scrollTop,
            scrollHeight: scrollContainerRef.scrollHeight
          };
        }

        // hasMore 판단 및 items 업데이트
        if (newItems.length > pageSize) {
          hasMore = true;

          if (scrollTrigger === 'top') {
            const itemsToAdd = uniqueItems.slice(1);
            items = [...itemsToAdd, ...items];
            firstDocSnapshot = snapshot.docs[1];
          } else {
            const itemsToAdd = uniqueItems.slice(0, pageSize);
            items = [...items, ...itemsToAdd];
            lastDocSnapshot = snapshot.docs[pageSize - 1];
          }
        } else {
          hasMore = false;

          if (scrollTrigger === 'top') {
            items = [...uniqueItems, ...items];
            firstDocSnapshot = snapshot.docs[0];
          } else {
            items = [...items, ...uniqueItems];
            lastDocSnapshot = snapshot.docs[snapshot.docs.length - 1];
          }
        }

        // 스크롤 위치 복원
        if (scrollTrigger === 'top' && scrollRestoreInfo && scrollContainerRef) {
          await tick();
          await new Promise(resolve => requestAnimationFrame(resolve));

          if (scrollContainerRef) {
            const newScrollHeight = scrollContainerRef.scrollHeight;
            const heightDifference = newScrollHeight - scrollRestoreInfo.scrollHeight;
            scrollContainerRef.scrollTop = scrollRestoreInfo.scrollTop + heightDifference;
          }
        }

        // 새 아이템에 리스너 설정
        uniqueItems.forEach((item) => {
          setupItemListener(item.id);
        });
      } else {
        hasMore = false;
      }
    } catch (err) {
      console.error('[loadMore] 에러:', err);
      error = err instanceof Error ? err.message : String(err);
    } finally {
      loading = false;
      isLoadingMore = false;
    }
  }

  /**
   * 컨테이너 스크롤 이벤트 핸들러
   */
  function handleScroll(event: Event) {
    if (loading || !hasMore) return;

    const target = event.currentTarget as HTMLDivElement;
    if (!target) return;

    const { scrollTop, scrollHeight, clientHeight } = target;

    if (scrollTrigger === 'top') {
      if (scrollTop < threshold) {
        loadMore();
      }
    } else {
      const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
      if (distanceFromBottom < threshold) {
        loadMore();
      }
    }
  }

  /**
   * Window 스크롤 이벤트 핸들러
   */
  function handleWindowScroll() {
    if (loading || !hasMore) return;

    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight;
    const clientHeight = window.innerHeight;

    if (scrollTrigger === 'top') {
      if (scrollTop < threshold) {
        loadMore();
      }
    } else {
      const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
      if (distanceFromBottom < threshold) {
        loadMore();
      }
    }
  }

  /**
   * 새로고침
   */
  export function refresh() {
    loadInitialData();
  }

  /**
   * 스크롤을 맨 위로 이동
   */
  export function scrollToTop() {
    if (scrollContainerRef) {
      scrollContainerRef.scrollTop = 0;
    }
  }

  /**
   * 스크롤을 맨 아래로 이동
   */
  export function scrollToBottom() {
    if (scrollContainerRef) {
      scrollContainerRef.scrollTop = scrollContainerRef.scrollHeight;
    }
  }
</script>

<!-- ============================================================================
     Template (템플릿)
     ============================================================================ -->

<div class="firestore-list-view" use:setupScrollListener>
  <!-- 초기 로딩 상태 -->
  {#if initialLoading}
    <div class="loading-container">
      {#if loadingSnippet}
        {@render loadingSnippet()}
      {:else}
        <div class="loading-spinner">
          <div class="spinner"></div>
          <p>로딩 중...</p>
        </div>
      {/if}
    </div>

  <!-- 에러 상태 -->
  {:else if error}
    <div class="error-container">
      {#if errorSnippet}
        {@render errorSnippet(error)}
      {:else}
        <div class="error-message">
          <p>⚠️ 에러가 발생했습니다</p>
          <p class="error-detail">{error}</p>
          <button onclick={refresh} class="retry-button">다시 시도</button>
        </div>
      {/if}
    </div>

  <!-- 데이터 없음 -->
  {:else if items.length === 0}
    <div class="empty-container">
      {#if empty}
        {@render empty()}
      {:else}
        <div class="empty-message">
          <p>📭 표시할 데이터가 없습니다</p>
        </div>
      {/if}
    </div>

  <!-- 데이터 목록 -->
  {:else}
    <div class="items-container">
      <!-- scrollTrigger="top"일 때: 더 이상 데이터 없음 표시를 맨 위에 -->
      {#if scrollTrigger === 'top' && !hasMore && !loading}
        <div class="no-more">
          {#if noMore}
            {@render noMore()}
          {:else}
            <p class="no-more-text">더 이상 데이터가 없습니다</p>
          {/if}
        </div>
      {/if}

      <!-- scrollTrigger="top"일 때: 더 로드 중 표시를 맨 위에 -->
      {#if scrollTrigger === 'top' && loading}
        <div class="loading-more">
          {#if loadingMore}
            {@render loadingMore()}
          {:else}
            <div class="loading-spinner small">
              <div class="spinner"></div>
              <p>더 불러오는 중...</p>
            </div>
          {/if}
        </div>
      {/if}

      {#each items as itemData, index (itemData.id)}
        <div class="item-wrapper" data-id={itemData.id}>
          {#if item}
            {@render item(itemData, index)}
          {:else}
            <div class="default-item">
              <pre>{JSON.stringify(itemData.data, null, 2)}</pre>
            </div>
          {/if}
        </div>
      {/each}

      <!-- scrollTrigger="bottom"일 때: 더 로드 중 표시를 맨 아래에 -->
      {#if scrollTrigger === 'bottom' && loading}
        <div class="loading-more">
          {#if loadingMore}
            {@render loadingMore()}
          {:else}
            <div class="loading-spinner small">
              <div class="spinner"></div>
              <p>더 불러오는 중...</p>
            </div>
          {/if}
        </div>
      {/if}

      <!-- scrollTrigger="bottom"일 때: 더 이상 데이터 없음 표시를 맨 아래에 -->
      {#if scrollTrigger === 'bottom' && !hasMore && !loading}
        <div class="no-more">
          {#if noMore}
            {@render noMore()}
          {:else}
            <p class="no-more-text">더 이상 데이터가 없습니다</p>
          {/if}
        </div>
      {/if}
    </div>
  {/if}
</div>

<!-- ============================================================================
     Styles (스타일)
     ============================================================================ -->

<style>
  .firestore-list-view {
    width: 100%;
    display: flex;
    flex-direction: column;
  }

  .items-container {
    width: 100%;
    display: flex;
    flex-direction: column;
  }

  .item-wrapper {
    width: 100%;
  }

  .default-item {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    background-color: #ffffff;
  }

  .default-item pre {
    margin: 0;
    font-size: 0.875rem;
    white-space: pre-wrap;
    word-break: break-all;
  }

  .loading-container,
  .error-container,
  .empty-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    padding: 2rem;
  }

  .loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }

  .loading-spinner.small {
    padding: 1rem;
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  .loading-spinner.small .spinner {
    width: 24px;
    height: 24px;
    border-width: 3px;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .loading-spinner p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
  }

  .loading-more {
    padding: 1rem;
    text-align: center;
  }

  .no-more {
    padding: 1.5rem;
    text-align: center;
  }

  .no-more-text {
    margin: 0;
    color: #9ca3af;
    font-size: 0.875rem;
  }

  .empty-message {
    text-align: center;
    color: #6b7280;
  }

  .empty-message p {
    margin: 0;
    font-size: 1rem;
  }

  .error-message {
    text-align: center;
    color: #dc2626;
  }

  .error-message p {
    margin: 0 0 0.5rem 0;
  }

  .error-detail {
    color: #6b7280;
    font-size: 0.875rem;
  }

  .retry-button {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .retry-button:hover {
    background-color: #2563eb;
  }

  .retry-button:active {
    background-color: #1d4ed8;
  }
</style>
