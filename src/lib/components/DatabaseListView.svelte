<!--
  DatabaseListView - Firebase RTDB 무한 스크롤 리스트 뷰 컴포넌트

  재사용 가능한 무한 스크롤 컴포넌트로, Firebase Realtime Database의 데이터를
  페이지네이션과 함께 표시합니다.

  사용 예시:
  ```svelte
  <DatabaseListView
    path="users"
    pageSize={20}
    orderBy="createdAt"
    threshold={300}
    reverse={true}
  >
    {#snippet item(itemData, index)}
      <div class="user-card">
        <h3>{itemData.data.name}</h3>
        <p>{itemData.data.email}</p>
      </div>
    {/snippet}

    {#snippet loading()}
      <p>로딩 중...</p>
    {/snippet}

    {#snippet empty()}
      <p>데이터가 없습니다.</p>
    {/snippet}
  </DatabaseListView>
  ```
-->

<script lang="ts">
  import {
    ref as dbRef,
    get,
    onValue,
    onChildAdded,
    onChildRemoved,
    query,
    orderByChild,
    limitToFirst,
    limitToLast,
    startAt,
    startAfter,
    endAt,
    endBefore,
    equalTo
  } from 'firebase/database';
  import type { Snippet } from 'svelte';
  import { tick } from 'svelte';
  import { rtdb as database } from '$lib/firebase';

  /**
   * 아이템 데이터 타입
   */
  type ItemData = {
    key: string;
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
   * - path: RTDB 경로 (예: "posts" 또는 "users/uid/posts")
   * - pageSize: 한 번에 가져올 아이템 개수 (기본값: 10)
   * - orderBy: 정렬 기준 필드 (기본값: "createdAt")
   * - orderPrefix: 정렬 필드의 prefix 값 (예: "community-")으로 필터링 (선택 사항)
   * - equalToValue: orderBy 필드가 특정 값과 정확히 일치하는 데이터만 조회 (선택 사항)
   * - threshold: 스크롤 threshold (px) - 바닥에서 이 값만큼 떨어지면 다음 페이지 로드 (기본값: 300)
   * - reverse: 역순 정렬 여부 (기본값: false)
   * - scrollTrigger: 스크롤 트리거 위치 (기본값: "bottom")
   *   - "bottom": 아래로 스크롤하면 다음 페이지 로드 (일반 목록)
   *   - "top": 위로 스크롤하면 이전 페이지 로드 (채팅방 스타일)
   * - autoScrollToEnd: 초기 로드 후 자동으로 스크롤을 맨 아래로 이동 (기본값: false)
   *   - scrollTrigger="top"과 함께 사용하면 채팅방 스타일 동작
   * - autoScrollOnNewData: 새 데이터 추가 시 자동 스크롤 여부 (기본값: false)
   *   - true이면 새 노드가 추가될 때 스크롤 위치가 threshold 이내면 자동으로 맨 아래로 스크롤
   *   - 채팅 메시지 목록에 유용 (사용자가 조금만 스크롤업 한 경우 새 메시지를 보여주기 위해 자동 스크롤)
   * - onItemAdded: 새 데이터 추가 시 호출되는 콜백 함수 (선택 사항)
   *   - 부모 컴포넌트에서 새 노드가 추가될 때 특정 동작을 수행할 수 있음
   *   - 예: 새 메시지 알림, 사운드 재생, 배지 업데이트 등
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
    orderBy?: string;
    orderPrefix?: string;
    equalToValue?: string | number | boolean | null;
    threshold?: number;
    reverse?: boolean;
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
    orderBy = 'createdAt',
    orderPrefix = '',
    equalToValue = undefined,
    threshold = 300,
    reverse = false,
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
  // Types (타입 정의)
  // ============================================================================

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
   * 마지막으로 로드한 아이템의 orderBy 필드 값
   * Firebase 쿼리의 startAfter에 사용됨
   */
  let lastLoadedValue = $state<any>(null);

  /**
   * 마지막으로 로드한 아이템의 키
   * 같은 orderBy 값을 가진 여러 아이템을 구분하기 위해 사용
   */
  let lastLoadedKey = $state<string | null>(null);

  /**
   * 현재 로드된 페이지 번호 (0부터 시작)
   * 페이지별 로드 추적용
   */
  let currentPage = $state<number>(0);

  /**
   * 에러 메시지
   */
  let error = $state<string | null>(null);

  /**
   * onValue 구독 해제 함수들을 관리하는 맵
   * 각 페이지의 데이터 변경을 실시간으로 리스닝
   */
  let unsubscribers = new Map<string, () => void>();

  /**
   * 각 페이지에서 로드한 아이템들을 관리하는 맵
   * 페이지별로 실시간 업데이트를 추적하기 위해 사용
   */
  let pageItems = new Map<number, ItemData[]>();

  /**
   * onChildAdded 리스너 해제 함수
   * 새로운 노드 생성을 감지하는 리스너
   */
  let childAddedUnsubscribe: (() => void) | null = null;

  /**
   * onChildAdded 리스너가 초기화되었는지 여부
   * 초기 로드 시 기존 아이템들에 대한 child_added 이벤트를 무시하기 위한 플래그
   */
  let childAddedListenerReady = $state<boolean>(false);

  /**
   * onChildRemoved 리스너 해제 함수
   * 노드 삭제를 감지하는 리스너
   */
  let childRemovedUnsubscribe: (() => void) | null = null;

  /**
   * equalToValue 가 문자열일 경우 트림된 값을 사용한다.
   * undefined/null 또는 빈 문자열이면 null로 간주한다.
   */
  const resolvedEqualValue = $derived.by(() => {
    if (equalToValue === undefined || equalToValue === null) return null;
    if (typeof equalToValue === 'string') {
      const trimmed = equalToValue.trim();
      return trimmed.length > 0 ? trimmed : null;
    }
    return equalToValue;
  });

  const hasEqualFilter = $derived.by(() => resolvedEqualValue !== null);

  /**
   * 스크롤 컨테이너 DOM 요소 참조
   * autoScrollToEnd 기능을 위해 사용
   * HTMLElement로 타입 지정 (부모 요소가 div가 아닐 수도 있음)
   */
  let scrollContainerRef: HTMLElement | null = null;

  /**
   * loadMore 작업 중 플래그
   * 스크롤 위치 보존을 위해 사용
   */
  let isLoadingMore = $state<boolean>(false);

  // ============================================================================
  // Lifecycle (생명주기)
  // ============================================================================

  /**
   * 컴포넌트 마운트 시 초기 데이터 로드
   * 컴포넌트 언마운트 시 모든 리스너 해제
   */
  $effect(() => {
    // props 변경 시에도 항상 최신 조건으로 다시 로드
    const deps = {
      path,
      orderBy,
      orderPrefix,
      pageSize,
      reverse,
      scrollTrigger,
      resolvedEqualValue
    };

    if (deps.path && database) {
      loadInitialData();
    }

    // cleanup: 컴포넌트 언마운트 시 모든 리스너 해제
    return () => {
      console.log('DatabaseListView: Cleaning up listeners');

      // child_added 리스너 해제
      if (childAddedUnsubscribe) {
        childAddedUnsubscribe();
        childAddedUnsubscribe = null;
      }

      // child_removed 리스너 해제
      if (childRemovedUnsubscribe) {
        childRemovedUnsubscribe();
        childRemovedUnsubscribe = null;
      }

      // 모든 onValue 리스너 해제
      unsubscribers.forEach((unsubscribe) => {
        unsubscribe();
      });
      unsubscribers.clear();

      console.log('DatabaseListView: All listeners cleaned up');
    };
  });

  /**
   * 스크롤 리스너 설정 action
   * DOM 요소가 마운트될 때 스크롤 이벤트 리스너를 등록합니다.
   * use:action 디렉티브를 사용하여 타이밍 문제를 해결합니다.
   */
  function setupScrollListener(node: HTMLDivElement) {
    console.log('DatabaseListView: Setting up scroll listeners on mounted node');

    // 스크롤 컨테이너 찾기
    // 부모 요소 중 overflow-auto 또는 overflow-scroll을 가진 요소를 찾습니다
    let actualScrollContainer: HTMLElement = node;
    let parent = node.parentElement;

    while (parent) {
      const overflowY = window.getComputedStyle(parent).overflowY;
      if (overflowY === 'auto' || overflowY === 'scroll') {
        actualScrollContainer = parent;
        console.log('DatabaseListView: Found scroll container (parent with overflow)', {
          tagName: parent.tagName,
          className: parent.className,
          overflowY
        });
        break;
      }
      parent = parent.parentElement;
    }

    // 스크롤 컨테이너 참조 저장 (autoScrollToEnd 기능을 위해)
    scrollContainerRef = actualScrollContainer;

    console.log('DatabaseListView: Registering scroll listener on', {
      isParent: actualScrollContainer !== node,
      tagName: actualScrollContainer.tagName,
      className: actualScrollContainer.className
    });

    // 실제 스크롤 컨테이너에 리스너 등록
    actualScrollContainer.addEventListener('scroll', handleScroll);
    // window 스크롤 감지 (body 스크롤)
    window.addEventListener('scroll', handleWindowScroll);

    console.log('DatabaseListView: Scroll listeners registered successfully');

    return {
      destroy() {
        console.log('DatabaseListView: Removing scroll listeners');
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
   * 아이템 목록의 마지막 항목에서 orderBy 필드 값 추출
   *
   * 페이지 커서를 위해 마지막 항목의 orderBy 필드 값이 필요합니다.
   * 필드가 없으면 createdAt을 폴백으로 사용합니다.
   */
  function getLastItemCursor(
    itemList: ItemData[],
    primaryField: string
  ): {value: any, key: string} | null {
    if (itemList.length === 0) return null;

    const lastItem = itemList[itemList.length - 1];
    if (!lastItem) return null; // 추가 안전성 체크

    const value = lastItem.data[primaryField];

    // 주 필드 값이 있으면 사용
    if (value != null && value !== '') {
      console.log(`DatabaseListView: Using cursor from '${primaryField}':`, {
        value: value,
        key: lastItem.key
      });
      return {
        value: value,
        key: lastItem.key
      };
    }

    // 주 필드가 없으면 null 반환 (무한 스크롤 중단)
    // 에러가 아닌 경고로 처리하여 앱이 중단되지 않도록 함
    console.warn(
      `DatabaseListView: Field '${primaryField}' not found in last item (key: ${lastItem.key}).`,
      `Pagination will stop here. This is normal if not all items have the '${primaryField}' field.`,
      `Item data:`, lastItem.data
    );
    return null;
  }

  /**
   * 각 아이템에 onValue 리스너 설정 (실시간 업데이트)
   *
   * Firebase의 onValue()를 사용하여 각 아이템의 변경사항을 실시간으로 감지합니다.
   */
  function setupItemListener(itemKey: string, index: number): void {
    // database null 체크
    if (!database) {
      console.error('DatabaseListView: Database is not initialized');
      return;
    }

    // 이미 리스닝 중이면 스킵
    const listenerKey = `${itemKey}`;
    if (unsubscribers.has(listenerKey)) {
      return;
    }

    const itemRef = dbRef(database, `${path}/${itemKey}`);
    const unsubscribe = onValue(
      itemRef,
      (snapshot) => {
        if (snapshot.exists()) {
          const updatedData = snapshot.val();
          // items 배열 업데이트
          items[index] = {
            key: itemKey,
            data: updatedData
          };
          items = [...items]; // 반응성을 위해 배열 재할당
          console.log(`DatabaseListView: Item updated ${itemKey}`, updatedData);
        }
      },
      (error) => {
        console.error(`DatabaseListView: Error listening to item ${itemKey}`, error);
      }
    );

    // 리스너 해제 함수 저장
    unsubscribers.set(listenerKey, unsubscribe);
  }

  /**
   * 새로운 노드 생성 감지 리스너 설정 (onChildAdded)
   *
   * Firebase의 onChildAdded()를 사용하여 새로운 노드가 생성되면 실시간으로 화면에 추가합니다.
   * - reverse가 true이면 배열 맨 앞에 추가 (최신 글이 위에)
   * - reverse가 false이면 배열 맨 뒤에 추가 (최신 글이 아래에)
   *
   * 주의: onChildAdded는 초기에 기존 아이템들에 대해서도 발생하므로,
   * childAddedListenerReady 플래그를 사용하여 초기 로드 완료 후에만 새 아이템으로 처리합니다.
   */
  function setupChildAddedListener() {
    // database null 체크
    if (!database) {
      console.error('DatabaseListView: Database is not initialized');
      return;
    }

    if (childAddedUnsubscribe) {
      // 기존 리스너가 있으면 먼저 해제
      childAddedUnsubscribe();
      childAddedUnsubscribe = null;
    }

    console.log('DatabaseListView: Setting up child_added listener for', path);
    childAddedListenerReady = false;

    const baseRef = dbRef(database, path);

    // equalToValue가 있으면 정확히 일치하는 값만 필터링
    // orderPrefix가 있으면 범위 쿼리 추가
    // 아무 것도 없으면 startAt(false)로 null/undefined 값 제외
    let dataQuery;
    if (hasEqualFilter) {
      dataQuery = query(baseRef, orderByChild(orderBy), equalTo(resolvedEqualValue));
      console.log('DatabaseListView: child_added listener with equalTo filter:', resolvedEqualValue);
    } else if (orderPrefix) {
      dataQuery = query(
        baseRef,
        orderByChild(orderBy),
        startAt(orderPrefix),
        endAt(orderPrefix + '\uf8ff')
      );
      console.log('DatabaseListView: child_added listener with orderPrefix:', orderPrefix);
    } else {
      // orderPrefix가 없으면 startAt(false) 사용
      // 이렇게 하면 orderBy 필드가 null 또는 undefined인 항목은 제외됩니다
      dataQuery = query(
        baseRef,
        orderByChild(orderBy),
        startAt(false)
      );
      console.log('DatabaseListView: child_added listener with startAt(false) to filter null/undefined');
    }

    childAddedUnsubscribe = onChildAdded(dataQuery, (snapshot) => {
      // 초기 로드 완료 전에는 무시 (기존 아이템들은 loadInitialData에서 처리)
      if (!childAddedListenerReady) {
        return;
      }

      const newItemKey = snapshot.key;
      const newItemData = snapshot.val();

      // key가 null인 경우 무시 (Firebase에서는 일반적으로 null이 아니지만 타입상 체크 필요)
      if (!newItemKey) {
        console.warn('DatabaseListView: Snapshot key is null, skipping');
        return;
      }

      // 중복 체크: 이미 items에 있는 key는 추가하지 않음
      const exists = items.some(item => item.key === newItemKey);
      if (exists) {
        console.log('DatabaseListView: Child already exists, skipping:', newItemKey);
        return;
      }

      console.log('DatabaseListView: New child added:', newItemKey, newItemData);

      const newItem: ItemData = {
        key: newItemKey,
        data: newItemData
      };

      // reverse 여부에 따라 배열의 앞 또는 뒤에 추가
      if (reverse) {
        // reverse가 true: 최신 글이 위에 → 배열 맨 앞에 추가
        items = [newItem, ...items];
        console.log('DatabaseListView: Added new item to the beginning (reverse mode)');

        // 새 아이템에 onValue 리스너 설정 (인덱스 0)
        setupItemListener(newItemKey, 0);

        // 기존 아이템들의 인덱스가 밀렸으므로, unsubscribers의 인덱스를 업데이트할 필요는 없음
        // (setupItemListener는 itemKey를 키로 사용하므로 인덱스 변경에 영향 없음)
        // 하지만 items[index] 업데이트를 위해 모든 리스너를 다시 설정하는 것이 안전할 수 있음
        // 성능을 위해 여기서는 새 아이템에만 리스너 설정
      } else {
        // reverse가 false: 오래된 글이 위에 → 배열 맨 뒤에 추가
        const newIndex = items.length;
        items = [...items, newItem];
        console.log('DatabaseListView: Added new item to the end (normal mode)');

        // 새 아이템에 onValue 리스너 설정
        setupItemListener(newItemKey, newIndex);
      }

      // 부모 컴포넌트에 새 아이템 추가 알림
      if (onItemAdded) {
        console.log('DatabaseListView: Calling onItemAdded callback with new item:', newItem);
        onItemAdded(newItem);
      }

      // autoScrollOnNewData가 활성화된 경우, 스크롤 위치 체크 후 자동 스크롤
      // 사용자가 맨 아래 근처(threshold 이내)에 있으면 새 메시지를 보여주기 위해 자동으로 맨 아래로 스크롤
      if (autoScrollOnNewData && scrollContainerRef) {
        // 현재 스크롤 위치가 맨 아래에서 threshold 이내인지 확인
        const { scrollTop, scrollHeight, clientHeight } = scrollContainerRef;
        const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);

        console.log('DatabaseListView: Auto-scroll check', {
          distanceFromBottom,
          threshold,
          willAutoScroll: distanceFromBottom <= threshold
        });

        if (distanceFromBottom <= threshold) {
          // DOM 업데이트 완료 대기 후 스크롤
          tick().then(() => {
            if (scrollContainerRef) {
              scrollContainerRef.scrollTop = scrollContainerRef.scrollHeight;
              console.log('DatabaseListView: Auto-scrolled to bottom on new data', {
                scrollHeight: scrollContainerRef.scrollHeight,
                scrollTop: scrollContainerRef.scrollTop
              });
            }
          });
        } else {
          console.log('DatabaseListView: User scrolled up beyond threshold, skipping auto-scroll');
        }
      }
    }, (error) => {
      console.error('DatabaseListView: Error in child_added listener', error);
    });

    // 약간의 지연 후 리스너를 활성화 (기존 아이템들의 child_added 이벤트를 건너뛰기 위해)
    // Firebase는 리스너 설정 직후 기존 아이템들에 대해 child_added를 발생시킴
    setTimeout(() => {
      childAddedListenerReady = true;
      console.log('DatabaseListView: child_added listener is now ready to accept new children');
    }, 1000);
  }

  /**
   * 노드 삭제 감지 리스너 설정 (onChildRemoved)
   *
   * Firebase의 onChildRemoved()를 사용하여 노드가 삭제되면 실시간으로 화면에서 제거합니다.
   * - items 배열에서 해당 노드를 필터링하여 제거
   * - 해당 노드의 onValue 리스너도 해제
   */
  function setupChildRemovedListener() {
    // database null 체크
    if (!database) {
      console.error('DatabaseListView: Database is not initialized');
      return;
    }

    if (childRemovedUnsubscribe) {
      // 기존 리스너가 있으면 먼저 해제
      childRemovedUnsubscribe();
      childRemovedUnsubscribe = null;
    }

    console.log('DatabaseListView: Setting up child_removed listener for', path);

    const baseRef = dbRef(database, path);

    // equalToValue가 있으면 정확히 일치하는 값만 필터링
    // orderPrefix가 있으면 범위 쿼리 추가
    // child_added 리스너와 동일한 쿼리 사용
    let dataQuery;
    if (hasEqualFilter) {
      dataQuery = query(baseRef, orderByChild(orderBy), equalTo(resolvedEqualValue));
      console.log('DatabaseListView: child_removed listener with equalTo filter:', resolvedEqualValue);
    } else if (orderPrefix) {
      dataQuery = query(
        baseRef,
        orderByChild(orderBy),
        startAt(orderPrefix),
        endAt(orderPrefix + '\uf8ff')
      );
      console.log('DatabaseListView: child_removed listener with orderPrefix:', orderPrefix);
    } else {
      // orderPrefix가 없으면 startAt(false) 사용
      dataQuery = query(
        baseRef,
        orderByChild(orderBy),
        startAt(false)
      );
      console.log('DatabaseListView: child_removed listener with startAt(false)');
    }

    childRemovedUnsubscribe = onChildRemoved(dataQuery, (snapshot) => {
      const removedKey = snapshot.key;

      // key가 null인 경우 무시
      if (!removedKey) {
        console.warn('DatabaseListView: Removed snapshot key is null, skipping');
        return;
      }

      console.log('DatabaseListView: Child removed:', removedKey);

      // items 배열에서 해당 key를 가진 아이템 찾기
      const removedIndex = items.findIndex(item => item.key === removedKey);

      if (removedIndex !== -1) {
        // items 배열에서 제거
        items = items.filter(item => item.key !== removedKey);
        console.log('DatabaseListView: Removed item from list:', removedKey, '(was at index', removedIndex, ')');

        // 해당 아이템의 onValue 리스너 해제
        const listenerKey = `${removedKey}`;
        const unsubscribe = unsubscribers.get(listenerKey);
        if (unsubscribe) {
          unsubscribe();
          unsubscribers.delete(listenerKey);
          console.log('DatabaseListView: Unsubscribed from removed item:', removedKey);
        }
      } else {
        console.log('DatabaseListView: Removed item not found in current list:', removedKey);
      }
    }, (error) => {
      console.error('DatabaseListView: Error in child_removed listener', error);
    });
  }

  /**
   * 초기 데이터 로드 (페이지별 Firebase 쿼리)
   *
   * Firebase 쿼리를 사용하여 첫 번째 페이지 + 1개를 로드합니다.
   * pageSize + 1개를 로드하여 다음 페이지 존재 여부를 판단합니다.
   * 각 아이템에 onValue 리스너를 설정하여 실시간 업데이트를 감지합니다.
   *
   * reverse가 true일 때는 limitToLast를 사용하여 최신 아이템부터 가져옵니다.
   */
  async function loadInitialData() {
    // database null 체크
    if (!database) {
      console.error('DatabaseListView: Database is not initialized');
      error = 'Database가 초기화되지 않았습니다.';
      initialLoading = false;
      return;
    }

    console.log('DatabaseListView: Loading initial data from', path, '(reverse:', reverse, ')');
    initialLoading = true;
    error = null;
    items = [];
    pageItems.clear();

    // 기존 리스너들 정리
    unsubscribers.forEach((unsubscribe) => unsubscribe());
    unsubscribers.clear();

    // child_added 리스너 해제
    if (childAddedUnsubscribe) {
      childAddedUnsubscribe();
      childAddedUnsubscribe = null;
    }
    childAddedListenerReady = false;

    // child_removed 리스너 해제
    if (childRemovedUnsubscribe) {
      childRemovedUnsubscribe();
      childRemovedUnsubscribe = null;
    }

    lastLoadedValue = null;
    lastLoadedKey = null;
    hasMore = true;
    currentPage = 0;

    try {
      const baseRef = dbRef(database, path);

      // Firebase 쿼리 생성
      // scrollTrigger='top'이면 채팅방 스타일이므로 limitToLast 사용 (최신 메시지 가져오기)
      // reverse가 true면 limitToLast를 사용하여 가장 최근 데이터부터 가져옵니다
      // pageSize + 1개를 가져와서 hasMore를 판단합니다
      // equalToValue가 있으면 정확히 일치하는 값만 조회합니다.
      // orderPrefix가 있으면 startAt과 endAt으로 범위 필터링
      // orderPrefix가 없으면 startAt(false)로 null/undefined 값 제외
      let dataQuery;
      if (hasEqualFilter) {
        dataQuery = query(baseRef, orderByChild(orderBy), equalTo(resolvedEqualValue));
        console.log('DatabaseListView: Using equalTo filter for initial load:', resolvedEqualValue);
      } else if (scrollTrigger === 'top' || reverse) {
        // 채팅방 스타일 또는 역순 정렬: limitToLast 사용
        if (orderPrefix) {
          // orderPrefix가 있으면 범위 쿼리 추가
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAt(orderPrefix),
            endAt(orderPrefix + '\uf8ff'),
            limitToLast(pageSize + 1)
          );
          console.log('DatabaseListView: Using limitToLast with orderPrefix:', orderPrefix, '(scrollTrigger:', scrollTrigger, ')');
        } else {
          // orderPrefix가 없으면 startAt(false) 사용
          // 이렇게 하면 orderBy 필드가 null 또는 undefined인 항목은 제외됩니다
          // orderBy 필드가 숫자 타입인 경우 가장 작은 값부터 정렬됩니다
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAt(false),
            limitToLast(pageSize + 1)
          );
          console.log('DatabaseListView: Using limitToLast with startAt(false) to filter null/undefined (scrollTrigger:', scrollTrigger, ')');
        }
      } else {
        // 정순 정렬: limitToFirst 사용
        if (orderPrefix) {
          // orderPrefix가 있으면 범위 쿼리 추가
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAt(orderPrefix),
            endAt(orderPrefix + '\uf8ff'),
            limitToFirst(pageSize + 1)
          );
          console.log('DatabaseListView: Using limitToFirst with orderPrefix:', orderPrefix);
        } else {
          // orderPrefix가 없으면 startAt(false) 사용
          // 이렇게 하면 orderBy 필드가 null 또는 undefined인 항목은 제외됩니다
          // orderBy 필드가 숫자 타입인 경우 가장 작은 값부터 정렬됩니다
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAt(false),
            limitToFirst(pageSize + 1)
          );
          console.log('DatabaseListView: Using limitToFirst with startAt(false) to filter null/undefined');
        }
      }

      const snapshot = await get(dataQuery);

      if (snapshot.exists()) {
        let loadedItems: ItemData[] = [];

        // 🔥 중요: snapshot.forEach()를 사용하여 Firebase의 정렬 순서를 유지합니다
        // Object.entries()를 사용하면 JavaScript 객체 프로퍼티 순서 때문에 정렬이 깨질 수 있습니다
        snapshot.forEach((childSnapshot) => {
          const key = childSnapshot.key;
          const data = childSnapshot.val();
          if (key) {
            loadedItems.push({
              key,
              data
            });
          }
        });

        // 🔍 디버깅: 초기 로드 결과
        console.log(
          `%c[DatabaseListView] Initial Load - Query Settings`,
          'color: #10b981; font-weight: bold;',
          { path, orderBy, orderPrefix, reverse, pageSize }
        );
        console.log(
          `%c[DatabaseListView] Initial Load - Firebase returned ${loadedItems.length} items`,
          'color: #3b82f6; font-weight: bold;'
        );
        console.log(
          `%c[DatabaseListView] Initial Load - Items in Firebase order:`,
          'color: #6366f1;',
          loadedItems.map((item, idx) => ({
            index: idx,
            key: item.key,
            [orderBy]: item.data[orderBy],
            title: item.data.title
          }))
        );

        // orderBy 필드가 있는 항목만 필터링
        // startAt(false)를 사용했지만, 추가 안전성을 위해 클라이언트에서도 필터링합니다
        const beforeFilterCount = loadedItems.length;
        loadedItems = loadedItems.filter((item) => {
          const hasOrderByField = item.data[orderBy] != null && item.data[orderBy] !== '';
          if (!hasOrderByField) {
            console.warn(
              `%c[DatabaseListView] Filtering out item without '${orderBy}' field:`,
              'color: #f59e0b;',
              { key: item.key, data: item.data }
            );
          }
          return hasOrderByField;
        });

        if (beforeFilterCount !== loadedItems.length) {
          console.log(
            `%c[DatabaseListView] After filtering: ${beforeFilterCount} → ${loadedItems.length} items`,
            'color: #8b5cf6;'
          );
        }

        // limitToLast를 사용하면 Firebase가 오름차순으로 반환하므로
        // reverse가 true일 때는 배열을 뒤집어야 합니다 (최신 글이 먼저 오도록)
        // 단, scrollTrigger='top'일 때는 채팅방 스타일이므로 reverse하지 않습니다
        // (오래된 메시지가 위에, 최신 메시지가 아래에 있어야 함)
        if (reverse && scrollTrigger !== 'top') {
          console.log(
            `%c[DatabaseListView] Before reverse:`,
            'color: #ec4899;',
            loadedItems.map((item, idx) => ({
              index: idx,
              [orderBy]: item.data[orderBy],
              title: item.data.title
            }))
          );
          loadedItems.reverse();
          console.log(
            `%c[DatabaseListView] After reverse (newest first):`,
            'color: #10b981;',
            loadedItems.map((item, idx) => ({
              index: idx,
              [orderBy]: item.data[orderBy],
              title: item.data.title
            }))
          );
        } else if (scrollTrigger === 'top') {
          console.log(
            `%c[DatabaseListView] Chat style - NOT reversing (oldest first, newest last):`,
            'color: #10b981;',
            loadedItems.map((item, idx) => ({
              index: idx,
              [orderBy]: item.data[orderBy],
              title: item.data.title
            }))
          );
        }

        // equalToValue가 있으면 한 번의 로드로 모든 결과를 표시하고 페이징을 중단한다.
        if (hasEqualFilter) {
          hasMore = false;
          items = loadedItems;
          lastLoadedValue = null;
          lastLoadedKey = null;
          console.log('DatabaseListView: equalTo filter active - pagination disabled after initial load.');
        }
        // pageSize보다 많으면 hasMore = true
        // 채팅방 스타일(scrollTrigger='top')에서는 첫 번째 아이템을 버리고 나머지 사용 (가장 최신 메시지 보존)
        // 일반 목록에서는 마지막 아이템을 버림
        else if (loadedItems.length > pageSize) {
          hasMore = true;

          if (scrollTrigger === 'top') {
            // 채팅방 스타일: 첫 번째 아이템을 버리고 나머지 pageSize개 사용
            // Firebase limitToLast는 [오래된, ..., 최신] 순서로 반환하므로
            // 첫 번째를 버리면 [더 최신, ..., 가장 최신]이 됩니다
            items = loadedItems.slice(1);
            console.log(
              `%c[DatabaseListView] Chat style - keeping newest ${items.length} items (removed oldest)`,
              'color: #f59e0b;',
              {
                total: loadedItems.length,
                kept: items.length,
                firstKept: items[0]?.data[orderBy],
                lastKept: items[items.length - 1]?.data[orderBy]
              }
            );

            // 커서는 첫 번째 아이템에서 추출 (스크롤 업 시 더 오래된 메시지 로드)
            const cursor = items.length > 0 ? {
              value: items[0].data[orderBy],
              key: items[0].key
            } : null;

            if (cursor && cursor.value != null) {
              lastLoadedValue = cursor.value;
              lastLoadedKey = cursor.key;
              console.log('DatabaseListView: Chat style cursor (first item):', { lastLoadedValue, lastLoadedKey });
            } else {
              hasMore = false;
            }
          } else {
            // 일반 목록: 마지막 아이템을 버리고 앞부분 pageSize개 사용
            items = loadedItems.slice(0, pageSize);

            // 커서는 마지막 아이템에서 추출
            const cursor = getLastItemCursor(items, orderBy);
            if (cursor) {
              lastLoadedValue = cursor.value;
              lastLoadedKey = cursor.key;
              console.log('DatabaseListView: Next page cursor set:', { lastLoadedValue, lastLoadedKey });
            } else {
              hasMore = false;
            }
          }
        } else {
          hasMore = false;
          items = loadedItems;

          if (items.length > 0) {
            if (scrollTrigger === 'top') {
              // 채팅방 스타일: 첫 번째 아이템을 커서로 사용
              const cursor = {
                value: items[0].data[orderBy],
                key: items[0].key
              };
              if (cursor.value != null) {
                lastLoadedValue = cursor.value;
                lastLoadedKey = cursor.key;
                console.log('DatabaseListView: Chat style cursor (first item, last page):', { lastLoadedValue, lastLoadedKey });
              }
            } else {
              // 일반 목록: 마지막 항목에서 페이지 커서 값 추출
              const cursor = getLastItemCursor(items, orderBy);
              if (cursor) {
                lastLoadedValue = cursor.value;
                lastLoadedKey = cursor.key;
                console.log('DatabaseListView: Last cursor set:', { lastLoadedValue, lastLoadedKey });
              }
            }
          }
        }

        // 첫 페이지 아이템들을 pageItems에 저장
        pageItems.set(0, items);

        // 각 아이템에 onValue 리스너 설정
        items.forEach((item, index) => {
          setupItemListener(item.key, index);
        });

        console.log(
          `%c[DatabaseListView] ✅ Initial Load Complete`,
          'color: #10b981; font-weight: bold; font-size: 14px;',
          {
            page: currentPage,
            loaded: items.length,
            hasMore,
            finalOrder: items.map((item, idx) => ({
              index: idx,
              [orderBy]: item.data[orderBy],
              title: item.data.title
            }))
          }
        );
      } else {
        console.log('DatabaseListView: No data found');
        items = [];
        hasMore = false;
      }
    } catch (err) {
      console.error('DatabaseListView: Load error', err);
      error = err instanceof Error ? err.message : String(err);
    } finally {
      initialLoading = false;

      // 초기 로드 완료 후 child_added 리스너 설정
      // 이후 새로 생성되는 노드를 실시간으로 감지하여 화면에 추가
      setupChildAddedListener();

      // 초기 로드 완료 후 child_removed 리스너 설정
      // 노드가 삭제되면 실시간으로 화면에서 제거
      setupChildRemovedListener();

      // autoScrollToEnd가 true이면 스크롤을 맨 아래로 이동 (채팅방 스타일)
      // 약간의 지연을 두어 DOM 렌더링이 완료된 후 스크롤 이동
      // scrollContainerRef는 이미 실제 스크롤 컨테이너를 가리키고 있습니다
      if (autoScrollToEnd && scrollContainerRef) {
        setTimeout(() => {
          if (scrollContainerRef) {
            scrollContainerRef.scrollTop = scrollContainerRef.scrollHeight;
            console.log('DatabaseListView: Auto-scrolled to bottom', {
              scrollHeight: scrollContainerRef.scrollHeight,
              scrollTop: scrollContainerRef.scrollTop
            });
          }
        }, 100);
      }
    }
  }

  /**
   * 다음 페이지 데이터 로드 (Firebase 쿼리)
   *
   * Firebase 쿼리를 사용하여 다음 페이지를 로드합니다.
   * - reverse가 false일 때: startAfter + limitToFirst 사용 (오래된 글 → 최신 글 순서)
   * - reverse가 true일 때: endBefore + limitToLast 사용 (최신 글 → 오래된 글 순서)
   * pageSize + 1개를 로드하여 hasMore를 판단합니다.
   */
  async function loadMore() {
    // database null 체크
    if (!database) {
      console.error('DatabaseListView: Database is not initialized');
      error = 'Database가 초기화되지 않았습니다.';
      return;
    }

    if (loading || !hasMore) {
      console.log('DatabaseListView: Cannot load more - loading:', loading, 'hasMore:', hasMore);
      return;
    }

    if (hasEqualFilter) {
      console.log('DatabaseListView: equalTo filter active - pagination skipped.');
      hasMore = false;
      return;
    }

    currentPage++;
    loading = true;
    isLoadingMore = true;
    error = null;

    console.log(`[loadMore] Page ${currentPage} 시작, cursor:`, lastLoadedValue);

    try {
      // lastLoadedValue가 null 또는 undefined이면 더 이상 로드할 수 없음
      // (undefined 체크도 필수 - orderBy 필드가 없는 항목이 있을 수 있음)
      if (lastLoadedValue == null) {
        console.log('DatabaseListView: No lastLoadedValue (null or undefined), cannot load more');
        hasMore = false;
        loading = false;
        return;
      }

      const baseRef = dbRef(database, path);

      // Firebase 쿼리 생성
      // scrollTrigger='top' 또는 reverse=true일 때 endBefore + limitToLast 사용
      // orderPrefix가 있으면 범위 쿼리도 함께 적용 (서버 측 필터링)
      // orderPrefix가 없으면 startAt(false)로 null/undefined 값 제외
      let dataQuery;
      if (scrollTrigger === 'top' || reverse) {
        // 채팅방 스타일 또는 역순 정렬: endBefore + limitToLast 사용
        // limitToLast를 사용하면 마지막 N개를 가져오는데,
        // endBefore로 현재 커서 이전 데이터를 가져옵니다
        //
        // orderPrefix가 있으면 startAt(orderPrefix)를 추가하여 범위 필터링
        // Firebase는 startAt()과 endBefore()를 동시에 사용할 수 있습니다
        if (orderPrefix) {
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAt(orderPrefix),
            endBefore(lastLoadedValue),
            limitToLast(pageSize + 1)
          );
          console.log('DatabaseListView: Using startAt + endBefore + limitToLast for chat/reverse pagination with orderPrefix:', orderPrefix, '(scrollTrigger:', scrollTrigger, ')');
        } else {
          // orderPrefix가 없으면 endBefore()만 사용
          // 초기 로드에서 이미 null/undefined 값을 제외했으므로,
          // 커서 이전의 값들도 유효한 값만 있을 것입니다.
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            endBefore(lastLoadedValue),
            limitToLast(pageSize + 1)
          );
          console.log('DatabaseListView: Using endBefore + limitToLast for chat/reverse pagination (scrollTrigger:', scrollTrigger, ')');
        }
      } else {
        // 정순 정렬: startAfter + limitToFirst 사용
        //
        // orderPrefix가 있으면 endAt(orderPrefix + '\uf8ff')를 추가하여 범위 필터링
        // Firebase는 startAfter()와 endAt()을 동시에 사용할 수 있습니다
        if (orderPrefix) {
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAfter(lastLoadedValue),
            endAt(orderPrefix + '\uf8ff'),
            limitToFirst(pageSize + 1)
          );
          console.log('DatabaseListView: Using startAfter + endAt + limitToFirst for normal pagination with orderPrefix:', orderPrefix);
        } else {
          // orderPrefix가 없으면 startAfter()만 사용
          // 초기 로드에서 이미 null/undefined 값을 제외했으므로,
          // 커서 이후의 값들도 유효한 값만 있을 것입니다.
          dataQuery = query(
            baseRef,
            orderByChild(orderBy),
            startAfter(lastLoadedValue),
            limitToFirst(pageSize + 1)
          );
          console.log('DatabaseListView: Using startAfter + limitToFirst for normal pagination');
        }
      }

      const snapshot = await get(dataQuery);

      if (snapshot.exists()) {
        const newItems: ItemData[] = [];

        snapshot.forEach((childSnapshot) => {
          const key = childSnapshot.key;
          const data = childSnapshot.val();
          if (key) {
            newItems.push({ key, data });
          }
        });

        console.log(`[loadMore] Firebase 반환: ${newItems.length}개`);

        // reverse 처리
        if (reverse && scrollTrigger !== 'top') {
          newItems.reverse();
        }

        // 중복 제거
        const existingKeys = new Set(items.map(item => item.key));
        let uniqueItems = newItems.filter((item) => !existingKeys.has(item.key));

        // orderBy 필드 검증
        uniqueItems = uniqueItems.filter((item) => {
          return item.data[orderBy] != null && item.data[orderBy] !== '';
        });

        console.log(`[loadMore] 중복 제거 후: ${uniqueItems.length}개`);

        if (uniqueItems.length === 0) {
          hasMore = false;
          loading = false;
          isLoadingMore = false;
          return;
        }

        // scrollTrigger='top'일 때 스크롤 위치 보존을 위한 준비
        let scrollRestoreInfo: { scrollTop: number; scrollHeight: number } | null = null;
        if (scrollTrigger === 'top' && scrollContainerRef) {
          scrollRestoreInfo = {
            scrollTop: scrollContainerRef.scrollTop,
            scrollHeight: scrollContainerRef.scrollHeight
          };
          console.log('[loadMore] 스크롤 위치 저장:', scrollRestoreInfo);
        }

        // hasMore 판단 및 items 배열 업데이트
        if (newItems.length > pageSize) {
          hasMore = true;

          let itemsToAdd: Array<{ key: string; data: any }>;

          if (scrollTrigger === 'top') {
            itemsToAdd = uniqueItems.slice(1);
          } else {
            itemsToAdd = uniqueItems.slice(0, pageSize);
          }

          // items 배열 업데이트
          if (scrollTrigger === 'top') {
            items = [...itemsToAdd, ...items];
          } else {
            items = [...items, ...itemsToAdd];
          }

          // 커서 업데이트
          let cursor: { value: any; key: string } | null;

          if (scrollTrigger === 'top') {
            if (itemsToAdd.length > 0 && itemsToAdd[0].data[orderBy] != null) {
              cursor = {
                value: itemsToAdd[0].data[orderBy],
                key: itemsToAdd[0].key
              };
            } else {
              cursor = null;
            }
          } else {
            cursor = getLastItemCursor(itemsToAdd, orderBy);
          }

          if (cursor) {
            lastLoadedValue = cursor.value;
            lastLoadedKey = cursor.key;
          } else {
            hasMore = false;
          }
        } else {
          hasMore = false;

          // items 배열 업데이트
          if (scrollTrigger === 'top') {
            items = [...uniqueItems, ...items];
          } else {
            items = [...items, ...uniqueItems];
          }

          // 커서 업데이트
          if (uniqueItems.length > 0) {
            let cursor: { value: any; key: string } | null;

            if (scrollTrigger === 'top') {
              if (uniqueItems[0].data[orderBy] != null) {
                cursor = {
                  value: uniqueItems[0].data[orderBy],
                  key: uniqueItems[0].key
                };
              } else {
                cursor = null;
              }
            } else {
              cursor = getLastItemCursor(uniqueItems, orderBy);
            }

            if (cursor) {
              lastLoadedValue = cursor.value;
              lastLoadedKey = cursor.key;
            }
          }
        }

        // scrollTrigger='top'일 때 스크롤 위치 복원
        if (scrollTrigger === 'top' && scrollRestoreInfo && scrollContainerRef) {
          // 1. Svelte의 모든 반응형 업데이트 완료 대기
          await tick();

          // 2. 브라우저의 다음 repaint 대기 (DOM 렌더링 완료)
          await new Promise(resolve => requestAnimationFrame(resolve));

          // 3. 스크롤 위치 복원
          if (scrollContainerRef) {
            const newScrollHeight = scrollContainerRef.scrollHeight;
            const heightDifference = newScrollHeight - scrollRestoreInfo.scrollHeight;
            const newScrollTop = scrollRestoreInfo.scrollTop + heightDifference;

            scrollContainerRef.scrollTop = newScrollTop;

            console.log('[loadMore] 스크롤 복원 완료:', {
              이전높이: scrollRestoreInfo.scrollHeight,
              새높이: newScrollHeight,
              높이차이: heightDifference,
              이전스크롤: scrollRestoreInfo.scrollTop,
              새스크롤: newScrollTop
            });
          }
        }

        // 4. 새로 추가된 아이템들에 onValue 리스너 설정 (스크롤 복원 후)
        if (scrollTrigger === 'top') {
          const addedCount = uniqueItems.length > pageSize ? pageSize : uniqueItems.length;
          items.slice(0, addedCount).forEach((item, index) => {
            setupItemListener(item.key, index);
          });
        } else {
          const startIndex = items.length - (uniqueItems.length > pageSize ? pageSize : uniqueItems.length);
          items.slice(startIndex).forEach((item, relativeIndex) => {
            setupItemListener(item.key, startIndex + relativeIndex);
          });
        }

        console.log(`[loadMore] 완료 - 추가: ${uniqueItems.length}, 전체: ${items.length}, hasMore: ${hasMore}`);
      } else {
        console.log('DatabaseListView: Query returned no data, hasMore set to false');
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
   * scrollTrigger 설정에 따라 top 또는 bottom 스크롤을 감지하여 다음 페이지 로드
   * - scrollTrigger='bottom': 아래로 스크롤하면 다음 페이지 로드 (일반 목록)
   * - scrollTrigger='top': 위로 스크롤하면 이전 페이지 로드 (채팅방 스타일)
   */
  function handleScroll(event: Event) {
    if (loading || !hasMore) return;

    const target = event.currentTarget as HTMLDivElement;
    if (!target) return;

    const { scrollTop, scrollHeight, clientHeight } = target;

    if (scrollTrigger === 'top') {
      // 채팅방 스타일: 위로 스크롤하여 천장에 가까워지면 이전 페이지 로드
      if (scrollTop < threshold) {
        console.log('DatabaseListView: Near top (container scroll), loading more...', {
          scrollTop,
          scrollHeight,
          clientHeight,
          threshold
        });
        loadMore();
      }
    } else {
      // 일반 목록: 아래로 스크롤하여 바닥에 가까워지면 다음 페이지 로드
      const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
      if (distanceFromBottom < threshold) {
        console.log('DatabaseListView: Near bottom (container scroll), loading more...', {
          scrollTop,
          scrollHeight,
          clientHeight,
          distanceFromBottom,
          threshold
        });
        loadMore();
      }
    }
  }

  /**
   * Window 스크롤 이벤트 핸들러
   * scrollTrigger 설정에 따라 top 또는 bottom 스크롤을 감지하여 다음 페이지 로드
   * - scrollTrigger='bottom': 아래로 스크롤하면 다음 페이지 로드 (일반 목록)
   * - scrollTrigger='top': 위로 스크롤하면 이전 페이지 로드 (채팅방 스타일)
   */
  function handleWindowScroll() {
    if (loading || !hasMore) {
      // console.log('DatabaseListView: Window scroll - skip (loading:', loading, 'hasMore:', hasMore, ')');
      return;
    }

    // document의 전체 높이와 현재 스크롤 위치를 확인
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight;
    const clientHeight = window.innerHeight;

    if (scrollTrigger === 'top') {
      // 채팅방 스타일: 위로 스크롤하여 천장에 가까워지면 이전 페이지 로드
      if (scrollTop < threshold) {
        console.log('DatabaseListView: Near top (window scroll), loading more...', {
          scrollTop,
          scrollHeight,
          clientHeight,
          threshold
        });
        loadMore();
      }
    } else {
      // 일반 목록: 아래로 스크롤하여 바닥에 가까워지면 다음 페이지 로드
      const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
      if (distanceFromBottom < threshold) {
        console.log('DatabaseListView: Near bottom (window scroll), loading more...', {
          scrollTop,
          scrollHeight,
          clientHeight,
          distanceFromBottom,
          threshold
        });
        loadMore();
      }
    }
  }

  /**
   * 새로고침 (처음부터 다시 로드)
   */
  export function refresh() {
    console.log('DatabaseListView: Refreshing...');
    loadInitialData();
  }

  /**
   * 스크롤을 맨 위로 이동
   * 부모 컴포넌트에서 호출 가능
   */
  export function scrollToTop() {
    if (scrollContainerRef) {
      scrollContainerRef.scrollTop = 0;
      console.log('DatabaseListView: Scrolled to top');
    } else {
      console.warn('DatabaseListView: Cannot scroll to top - scrollContainerRef is null');
    }
  }

  /**
   * 스크롤을 맨 아래로 이동
   * 부모 컴포넌트에서 호출 가능
   */
  export function scrollToBottom() {
    if (scrollContainerRef) {
      scrollContainerRef.scrollTop = scrollContainerRef.scrollHeight;
      console.log('DatabaseListView: Scrolled to bottom', {
        scrollHeight: scrollContainerRef.scrollHeight,
        scrollTop: scrollContainerRef.scrollTop
      });
    } else {
      console.warn('DatabaseListView: Cannot scroll to bottom - scrollContainerRef is null');
    }
  }
</script>

<!-- ============================================================================
     Template (템플릿)
     ============================================================================ -->

<div class="database-list-view" use:setupScrollListener>
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
      {#each items as itemData, index (itemData.key)}
        <div class="item-wrapper" data-key={itemData.key}>
          {#if item}
            {@render item(itemData, index)}
          {:else}
            <!-- 기본 아이템 렌더링 (snippet이 제공되지 않은 경우) -->
            <div class="default-item">
              <pre>{JSON.stringify(itemData.data, null, 2)}</pre>
            </div>
          {/if}
        </div>
      {/each}

      <!-- 더 로드 중 표시 -->
      {#if loading}
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

      <!-- 더 이상 데이터 없음 표시 -->
      {#if !hasMore && !loading}
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
  /* 컨테이너 */
  .database-list-view {
    width: 100%;
    /* height와 overflow는 부모에서 제어하도록 제거 */
    /* 이렇게 하면 body 스크롤(window scroll)이 제대로 동작합니다 */
    /* 만약 컨테이너 스크롤이 필요하면 부모에서 height와 overflow-y: auto를 설정하세요 */
    display: flex;
    flex-direction: column;
  }

  /* 아이템 컨테이너 */
  .items-container {
    width: 100%;
    display: flex;
    flex-direction: column;
  }

  /* 아이템 래퍼 */
  .item-wrapper {
    width: 100%;
  }

  /* 기본 아이템 스타일 */
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

  /* 로딩 컨테이너 */
  .loading-container,
  .error-container,
  .empty-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    padding: 2rem;
  }

  /* 로딩 스피너 */
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

  /* 더 로드 중 표시 */
  .loading-more {
    padding: 1rem;
    text-align: center;
  }

  /* 더 이상 데이터 없음 */
  .no-more {
    padding: 1.5rem;
    text-align: center;
  }

  .no-more-text {
    margin: 0;
    color: #9ca3af;
    font-size: 0.875rem;
  }

  /* 빈 상태 메시지 */
  .empty-message {
    text-align: center;
    color: #6b7280;
  }

  .empty-message p {
    margin: 0;
    font-size: 1rem;
  }

  /* 에러 메시지 */
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
