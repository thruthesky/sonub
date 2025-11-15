---
name: sonub-firestore-list-view
version: 2.0.0
description: FirestoreListView 무한 스크롤 컴포넌트 가이드
dependencies:
  - sonub-firebase-database-structure.md
  - sonub-chat-room.md
tags:
  - firestore
  - infinite-scroll
  - svelte
---

# FirestoreListView 컴포넌트 가이드

## 1. 개요

`FirestoreListView`는 `src/lib/components/FirestoreListView.svelte`에 구현된 **Cloud Firestore 전용 무한 스크롤 리스트 컴포넌트**입니다. Realtime Database 기반 `DatabaseListView`는 더 이상 사용하지 않으며, 모든 목록은 Firestore 쿼리를 기반으로 합니다. 주요 사용 예시는 `/user/list` 페이지(`src/routes/user/list/+page.svelte`)와 채팅 메시지 스트림(`src/routes/chat/room/+page.svelte`)입니다.

## 2. 주요 특징

- **Firestore 쿼리 자동화**: `collection()` + `query()`를 사용해 `where`, `orderBy`, `limit`, `startAfter`/`endBefore`를 자동 구성합니다.
- **양방향 스크롤 지원**: `scrollTrigger="bottom"`(일반 목록)과 `"top"`(채팅 메시지와 같이 위로 무한 스크롤)을 모두 지원합니다.
- **실시간 listener 결합**: 최초 페이지를 `getDocs()`로 로드한 뒤, `onSnapshot()`을 두어 새 문서 추가/삭제를 반영합니다.
- **컨테이너/Window 스크롤 감지**: `use:setupScrollListener` 액션이 부모 컨테이너를 탐색하여 적절한 스크롤 소스를 선택합니다.
- **Svelte 5 runes 활용**: `$state`, `$derived`, `$effect` 기반으로 상태를 관리합니다.

## 3. Props

| Prop | 타입 | 기본값 | 설명 |
|------|------|--------|------|
| `path` | `string` | `''` | Firestore 컬렉션 경로 (`users/{uid}/chat-joins`, `chats/{roomId}/messages` 등) |
| `pageSize` | `number` | `10` | 한 번에 가져올 문서 수 |
| `orderByField` | `string` | `'createdAt'` | 정렬 기준 필드 |
| `orderDirection` | `'asc' \| 'desc'` | `'asc'` | 정렬 방향 |
| `whereFilters` | `{ field, operator, value }[]` | `[]` | Firestore `where` 필터 배열 (예: `{ field: 'displayNameLowerCase', operator: '>=', value: 'sonub' }`) |
| `threshold` | `number` | `300` | 컨테이너 바닥과의 거리(px)가 이 값보다 작으면 자동으로 `loadMore()` 호출 |
| `scrollTrigger` | `'bottom' \| 'top'` | `'bottom'` | 다음 페이지 로드 방향. 채팅 메시지는 `'top'` 사용 |
| `autoScrollToEnd` | `boolean` | `false` | 초기 로드 후 맨 아래 스크롤 여부 (채팅 UI용) |
| `autoScrollOnNewData` | `boolean` | `false` | 새 데이터가 추가됐을 때 자동 스크롤 여부 |
| `onItemAdded` | `(item) => void` | `undefined` | 새 문서가 추가될 때 콜백 |
| `item` / `loading` / `empty` / `error` / `loadingMore` / `noMore` | `Snippet` | 선택 | 각 상태별 커스텀 렌더링 |

> Prop 이름/타입은 `FirestoreListView.svelte` 내부 `$props()` 선언을 그대로 따릅니다.

## 4. 사용 예시

### 4.1 사용자 목록 (`src/routes/user/list/+page.svelte`)

```svelte
<FirestoreListView
  path="users"
  pageSize={isSearching ? 50 : 15}
  orderByField={listOrderBy}
  orderDirection="desc"
  whereFilters={listWhereFilters}
  threshold={300}
>
  {#snippet item(itemData)}
    <UserCard data={itemData.data} uid={itemData.id} />
  {/snippet}
{/FirestoreListView}
```

- 정렬 필드는 검색 여부에 따라 `displayNameLowerCase` 또는 Firestore에 파생 저장된 `sort_recent*` 필드를 사용합니다.
- 검색 필터는 Firestore prefix 쿼리(`>= keyword`, `< keyword + '\uf8ff'`)로 구성됩니다.

### 4.2 채팅 메시지 (`src/routes/chat/room/+page.svelte`)

```svelte
<FirestoreListView
  path={`chats/${activeRoomId}/messages`}
  pageSize={30}
  orderByField="createdAt"
  orderDirection="desc"
  scrollTrigger="top"
  autoScrollToEnd={false}
>
  {#snippet item(message)}
    <ChatMessage {...message.data} />
  {/snippet}
{/FirestoreListView}
```

- 위로 스크롤하는 UI이므로 `scrollTrigger="top"`을 사용하며, `loadMore()`는 `endBefore(firstDocSnapshot)`로 페이지네이션합니다.

## 5. 내부 동작 요약

1. **초기 로드 (`loadInitialData`)**
   - `limit(pageSize + 1)`을 사용해 `hasMore` 여부를 판단합니다.
   - `scrollTrigger='top'`일 때는 첫 문서를 버리고 나머지를 사용해 자연스러운 스크롤을 만듭니다.
   - 각 아이템마다 `setupItemListener()`를 호출해 개별 `onSnapshot()`을 등록합니다.

2. **추가 로드 (`loadMore`)**
   - 커서(`lastDocSnapshot` 또는 `firstDocSnapshot`)를 기준으로 `startAfter()` / `endBefore()` 쿼리를 구성합니다.
   - 새로운 문서에서 기존 ID를 제거해 중복을 방지합니다.
   - `scrollTrigger='top'`일 때는 기존 스크롤 위치를 보존하기 위해 높이 차이를 계산해 복원합니다.

3. **실시간 업데이트 (`setupCollectionListener`)**
   - 컬렉션 전체 스냅샷을 구독해 `change.type === 'added'`/`'removed'` 이벤트를 감지합니다.
   - 신규 문서는 `scrollTrigger`/`orderDirection`에 따라 앞/뒤에 삽입하고, 선택적으로 `autoScrollOnNewData` 로직을 실행합니다.

4. **스크롤 감지**
   - `handleScroll`은 컨테이너 스크롤 이벤트를 사용하고, `handleWindowScroll`은 페이지 자체 스크롤을 사용합니다.
   - `distanceFromBottom < threshold` 또는 `scrollTop < threshold` 조건을 만족하면 `loadMore()` 호출.
   - 스크롤 컨테이너는 `setupScrollListener`가 DOM을 탐색해 자동으로 찾습니다.

## 6. 상태/스니펫 사용 팁

- `initialLoading`: 최초 데이터를 불러오는 동안 `loading()` snippet을 렌더링.
- `hasMore`: false가 되면 `noMore()` snippet 또는 기본 메시지를 노출.
- `isLoadingMore`: `loadingMore()` snippet과 로딩 스피너를 제어.
- `error`: Firestore 에러 메시지를 출력하며, snippet이 없으면 기본 메시지를 렌더링.

## 7. Firestore 규칙 및 성능 고려사항

- Firestore 규칙(`firebase/firestore.rules`)에서 해당 컬렉션/서브컬렉션 경로에 대한 읽기 권한이 필요합니다.
- `whereFilters` + `orderByField` 조합에 맞는 **복합 색인**을 `firebase/firestore.indexes.json`에 등록해야 합니다. 예: 사용자 검색(`displayNameLowerCase` + `orderDirection="desc"`)은 해당 색인이 존재해야 합니다.
- `threshold`가 너무 크면 페이지 진입 즉시 여러 페이지를 로드할 수 있으므로, `/user/list`처럼 컨테이너 높이가 짧은 경우 UI 레이아웃을 조정하거나 초기 데이터 수를 늘려 빈 화면을 피하세요.

## 8. 마이그레이션 체크리스트 (DatabaseListView → FirestoreListView)

- [x] `DatabaseListView` import를 `FirestoreListView`로 변경
- [x] Realtime Database 경로(`path="posts"`)를 Firestore 컬렉션 경로로 업데이트 (예: `path="users"`, `path="users/{uid}/chat-joins"`)
- [x] `orderBy` → `orderByField`, `reverse` → `orderDirection` 조합으로 변경
- [x] `orderPrefix`, `startAt(false)` 등 RTDB 전용 옵션 제거
- [x] 기존 `firebase/database` API (`ref`, `query`, `onValue`) 사용 코드를 모두 제거하고 Firestore 기반 스토어(`firestoreStore`)로 대체

## 9. 관련 문서

- [Firestore 데이터 구조](./sonub-firebase-database-structure.md)
- [채팅방 레이아웃/동작](./sonub-chat-room.md)
- [Firestore 보안 규칙](./sonub-firebase-security-rules.md)

FirestoreListView는 Firestore 쿼리의 파라미터를 그대로 노출하므로, 새 컬렉션을 추가하거나 정렬 필드를 늘릴 때마다 이 문서와 `src/lib/components/FirestoreListView.svelte`를 함께 업데이트하세요.
