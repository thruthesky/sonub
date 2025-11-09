
## DatabaseListView 컴포넌트

- **무한 스크롤 리스트 구현 시 `DatabaseListView` 컴포넌트를 활용합니다**
  - Firebase Realtime Database의 데이터를 무한 스크롤로 표시하는 재사용 가능한 Svelte 5 컴포넌트입니다
  - 두 가지 스크롤 방식 지원:
    - **Body 스크롤**: DatabaseListView를 body에 직접 마운트하여 전체 페이지 스크롤 사용
    - **Container 스크롤**: 래퍼 컨테이너로 감싸고 높이를 지정하여 특정 영역만 스크롤
  - **마이그레이션**: 기존 Custom Elements 방식에서 Svelte 5 컴포넌트 방식으로 전환
  - **핵심 변경사항**: `orderPrefix` prop이 `orderPrefix`로 변경됨



# DatabaseListView 컴포넌트 코딩 가이드라인

`DatabaseListView`는 Firebase Realtime Database의 데이터를 무한 스크롤 방식으로 표시하는 재사용 가능한 Svelte 5 컴포넌트입니다.

## 0. Custom Elements에서 Svelte 5로 마이그레이션

이전 버전의 Sonub에서는 Custom Elements 방식의 DatabaseListView를 사용했습니다. 현재 버전에서는 Svelte 5 컴포넌트 방식으로 전환되었습니다.

### 주요 변경사항

#### 1. Prop 이름 변경

**이전 (Custom Elements)**:
```javascript
// orderPrefix prop 사용
<database-list-view orderPrefix="community-"></database-list-view>
```

**현재 (Svelte 5)**:
```svelte
<!-- orderPrefix prop 사용 -->
<DatabaseListView orderPrefix="community-" />
```

#### 2. 컴포넌트 구조 변경

| 항목 | Custom Elements | Svelte 5 |
|------|----------------|----------|
| 파일 확장자 | `.js` | `.svelte` |
| Props 정의 | `this.getAttribute()` | `$props()` |
| 상태 관리 | `this.state` | `$state()` |
| 라이프사이클 | `connectedCallback()`, `disconnectedCallback()` | `$effect()` cleanup |
| 반응성 | 수동 DOM 업데이트 | Svelte의 자동 반응형 시스템 |
| Slot/Snippet | `<slot>` 또는 템플릿 함수 | `{#snippet}` |

#### 3. 사용 예시 비교

**이전 (Custom Elements)**:
```html
<database-list-view
  path="posts"
  page-size="20"
  order-by="createdAt"
  sort-prefix="community-"
>
  <template data-slot="item">
    <div class="post-card">${data.title}</div>
  </template>
</database-list-view>
```

**현재 (Svelte 5)**:
```svelte
<script>
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
</script>

<DatabaseListView
  path="posts"
  pageSize={20}
  orderBy="createdAt"
  orderPrefix="community-"
>
  {#snippet item(itemData)}
    <div class="post-card">
      <h3>{itemData.data.title}</h3>
    </div>
  {/snippet}
</DatabaseListView>
```

#### 4. 마이그레이션 체크리스트

- [ ] `sortPrefix` prop을 `orderPrefix`로 변경
- [ ] 케밥 케이스 props (`page-size`)를 카멜 케이스 (`pageSize`)로 변경
- [ ] `<template>` 슬롯을 `{#snippet}`으로 변경
- [ ] 문자열 prop 값에 `{}`를 사용하여 JavaScript 표현식으로 전달 (예: `pageSize={20}`)
- [ ] Custom Elements 이벤트 리스너를 Svelte 이벤트 핸들러로 변경
- [ ] DOM API (`querySelector` 등) 사용을 Svelte 바인딩 (`bind:this`)으로 변경

## 1. 개요

- **목적**: Firebase RTDB 데이터를 페이지네이션과 무한 스크롤로 표시
- **특징**:
  - 자동 스크롤 감지 (컨테이너 스크롤 + window 스크롤)
  - 실시간 데이터 동기화 (`onValue` 기반)
  - 실시간 노드 삭제 감지 (`onChildRemoved` 기반)
  - Svelte 5 Runes 기반 반응형 상태 관리
  - 커스터마이징 가능한 snippet 지원

## 2. 기본 사용법

```svelte
<script>
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
</script>

<DatabaseListView
  path="users"
  pageSize={10}
  orderBy="createdAt"
  threshold={300}
  reverse={false}
>
  {#snippet item(itemData)}
    <div class="item-card">
      <h3>{itemData.data.displayName}</h3>
      <p>{itemData.data.email}</p>
    </div>
  {/snippet}
</DatabaseListView>
```

## 3. Props 설명

| Prop | 타입 | 기본값 | 설명 |
|------|------|--------|------|
| `path` | `string` | (필수) | Firebase RTDB 경로 (예: `"users"`, `"posts/community"`) |
| `pageSize` | `number` | `10` | 한 번에 가져올 아이템 개수 |
| `orderBy` | `string` | `"createdAt"` | 정렬 기준 필드 |
| `orderPrefix` | `string` | `""` | 정렬 필드의 prefix 값으로 필터링 (예: `"community-"`) - 선택 사항 |
| `threshold` | `number` | `300` | 스크롤 threshold (px) - 바닥에서 이 거리만큼 떨어지면 다음 페이지 로드 |
| `reverse` | `boolean` | `false` | 역순 정렬 여부 |

## 4. Snippets

DatabaseListView는 다양한 상태에 대한 커스터마이징 가능한 snippet을 제공합니다:

- `item(itemData, index)` - 각 아이템 렌더링
- `loading()` - 초기 로딩 상태
- `empty()` - 데이터 없음 상태
- `error(errorMessage)` - 에러 상태
- `loadingMore()` - 더 로드 중 상태
- `noMore()` - 더 이상 데이터 없음 상태

## 5. orderPrefix와 startAt(false) 필터링

DatabaseListView는 Firebase 쿼리에서 **자동으로 null/undefined 값을 필터링**합니다.

### 📌 기본 동작 (orderPrefix가 없는 경우)

`orderPrefix`를 제공하지 않으면 `startAt(false)`가 자동으로 추가되어 **orderBy 필드가 null 또는 undefined인 항목을 제외**합니다.

```svelte
<DatabaseListView
  path="users"
  orderBy="createdAt"
  pageSize={10}
/>
```

**Firebase 쿼리 결과**:
- ✅ `createdAt` 필드가 있는 항목만 조회됨
- ❌ `createdAt` 필드가 `null` 또는 `undefined`인 항목은 제외됨
- ✅ 숫자 타입인 경우 **가장 작은 값부터 정렬**됨

**내부 쿼리**:
```javascript
query(
  baseRef,
  orderByChild('createdAt'),
  startAt(false),  // ← null/undefined 필터링
  limitToFirst(10)
)
```

### 📌 orderPrefix를 사용하는 경우

`orderPrefix`를 제공하면 해당 prefix로 시작하는 값만 필터링합니다:

```svelte
<DatabaseListView
  path="posts"
  orderBy="categoryKey"
  orderPrefix="community-"
  pageSize={20}
/>
```

**Firebase 쿼리 결과**:
- ✅ `categoryKey`가 `"community-"`로 시작하는 항목만 조회됨
- ❌ `categoryKey`가 `"qna-"`, `"news-"` 등인 항목은 제외됨
- ❌ `categoryKey`가 `null` 또는 `undefined`인 항목도 제외됨

**내부 쿼리**:
```javascript
query(
  baseRef,
  orderByChild('categoryKey'),
  startAt('community-'),
  endAt('community-\uf8ff'),  // ← prefix 범위 필터링
  limitToFirst(20)
)
```

### 📌 startAt(false)가 필요한 이유

Firebase Realtime Database의 `orderByChild()` 쿼리는 기본적으로 **null 값을 포함**합니다. 이로 인해:

1. **페이지네이션 오류 발생**
   - `orderBy` 필드가 없는 항목이 커서 값으로 사용됨
   - 다음 페이지 로드 시 타입 불일치 에러 발생

2. **불완전한 데이터 표시**
   - 정렬 필드가 없는 항목이 리스트에 포함됨
   - UI에서 의미 없는 데이터가 표시됨

3. **성능 저하**
   - 불필요한 데이터를 네트워크로 전송
   - 클라이언트에서 추가 필터링 필요

**`startAt(false)` 사용 시**:
- ✅ Firebase 쿼리 단계에서 null/undefined 항목 제외
- ✅ 네트워크 비용 절감 (불필요한 데이터 전송 방지)
- ✅ 정확한 페이지네이션 동작 보장
- ✅ 타입 안전성 확보 (커서 값이 항상 유효함)

### 📌 중요한 제약사항: startAt()과 커서의 충돌

⚠️ **Firebase 쿼리에서는 `startAt()`, `startAfter()`, `endBefore()`, `equalTo()` 중 하나만 사용할 수 있습니다.**

DatabaseListView는 이 제약을 자동으로 처리합니다:

1. **초기 로드 (`loadInitialData`)**:
   - `startAt(false)` 사용 ✅
   - null/undefined 값을 필터링합니다

2. **페이지네이션 (`loadMore`)**:
   - `startAfter(lastLoadedValue)` 또는 `endBefore(lastLoadedValue)` 사용 ✅
   - ❌ `startAt(false)`는 **사용하지 않음** (충돌 방지)
   - 초기 로드에서 이미 null/undefined 값을 제외했으므로, 커서 이후/이전의 값들도 유효함

**잘못된 쿼리 예시 (에러 발생)**:
```javascript
// ❌ 이렇게 하면 에러 발생!
query(
  baseRef,
  orderByChild('createdAt'),
  startAt(false),        // ← 시작점 설정
  startAfter(1234567890) // ← 또 다른 시작점 설정! (충돌)
)
// Error: startAfter: Starting point was already set
// (by another call to startAt, startAfter, or equalTo).
```

**올바른 쿼리 예시**:
```javascript
// ✅ 초기 로드: startAt(false)만 사용
query(
  baseRef,
  orderByChild('createdAt'),
  startAt(false),
  limitToFirst(10)
)

// ✅ 페이지네이션: startAfter()만 사용
query(
  baseRef,
  orderByChild('createdAt'),
  startAfter(1234567890),
  limitToFirst(10)
)
```

### 📌 사용 예시

#### 예시 1: 사용자 목록 (createdAt 기준 정렬)

```svelte
<DatabaseListView
  path="users"
  orderBy="createdAt"
  pageSize={15}
>
  {#snippet item(itemData)}
    <div class="user-card">
      <h3>{itemData.data.displayName}</h3>
      <p>가입일: {new Date(itemData.data.createdAt).toLocaleDateString()}</p>
    </div>
  {/snippet}
</DatabaseListView>
```

**결과**:
- ✅ `createdAt` 필드가 있는 사용자만 표시
- ✅ 가장 오래된 사용자부터 정렬 (작은 timestamp → 큰 timestamp)
- ❌ `createdAt`가 없는 사용자는 리스트에서 제외

#### 예시 2: 게시글 목록 (카테고리별 필터링)

```svelte
<DatabaseListView
  path="posts"
  orderBy="categoryKey"
  orderPrefix="community-"
  reverse={true}
  pageSize={20}
>
  {#snippet item(itemData)}
    <div class="post-card">
      <h3>{itemData.data.title}</h3>
      <p>{itemData.data.content}</p>
    </div>
  {/snippet}
</DatabaseListView>
```

**결과**:
- ✅ `categoryKey`가 `"community-"`로 시작하는 게시글만 표시
- ✅ 카테고리 prefix가 다른 게시글은 제외
- ✅ `reverse={true}`로 최신 글부터 표시

### 📌 주의사항

#### ⚠️ orderBy 필드는 반드시 존재해야 함

DatabaseListView를 사용하려면 **모든 아이템이 orderBy 필드를 가지고 있어야** 합니다:

```javascript
// ❌ 잘못된 데이터 구조 - createdAt 필드 누락
{
  "users": {
    "user1": {
      "displayName": "홍길동",
      // createdAt 없음!
    }
  }
}

// ✅ 올바른 데이터 구조 - createdAt 필드 포함
{
  "users": {
    "user1": {
      "displayName": "홍길동",
      "createdAt": 1234567890123
    }
  }
}
```

#### ⚠️ 자동 필드 생성 권장

사용자 생성 시 `createdAt`, `updatedAt` 같은 필드를 자동으로 생성하는 것이 좋습니다:

- **클라이언트 측**: `firebase-login-user.svelte.js`에서 자동 생성
- **서버 측**: Firebase Cloud Functions의 `onUserRegister`에서 자동 생성

참고: [firebase-login-user.svelte.js](../src/lib/utils/firebase-login-user.svelte.js) (lines 168-188)

## 6. 스크롤 방식 선택

DatabaseListView는 두 가지 스크롤 방식을 지원합니다:

### 방식 1: Body 스크롤 (전체 페이지 무한 스크롤)

**사용 시기**:
- 전체 페이지를 스크롤하며 무한 스크롤을 구현하고 싶을 때
- 페이지 전체가 리스트로 구성될 때
- 자연스러운 네이티브 스크롤 경험을 제공하고 싶을 때

**구현 방법**:

```svelte
<script>
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
</script>

<!-- 래퍼 없이 직접 마운트 -->
<DatabaseListView
  path="users"
  pageSize={15}
  orderBy="createdAt"
>
  {#snippet item(itemData)}
    <div class="user-card">
      <!-- 아이템 내용 -->
    </div>
  {/snippet}
</DatabaseListView>

<style>
  /* 아이템 스타일만 정의 */
  .user-card {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
</style>
```

**장점**:
- ✅ 자연스러운 스크롤 경험
- ✅ 높이 제한 없음
- ✅ 코드가 간단함

**단점**:
- ❌ 페이지 레이아웃 제어가 어려움
- ❌ 다른 컨텐츠와 함께 배치하기 어려움

### 방식 2: 컨테이너 스크롤 (제한된 영역에서 무한 스크롤)

**사용 시기**:
- 특정 영역에만 리스트를 표시하고 싶을 때
- 페이지 내 다른 컨텐츠와 함께 배치할 때
- 고정된 높이의 리스트 영역이 필요할 때

**구현 방법**:

```svelte
<script>
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
</script>

<!-- 래퍼 컨테이너로 감싸기 -->
<div class="user-list-container">
  <DatabaseListView
    path="users"
    pageSize={10}
    orderBy="createdAt"
  >
    {#snippet item(itemData)}
      <div class="user-card">
        <!-- 아이템 내용 -->
      </div>
    {/snippet}
  </DatabaseListView>
</div>

<style>
  /* 래퍼 컨테이너에 높이와 스크롤 설정 */
  .user-list-container {
    /* 고정 높이 설정 */
    height: 600px;

    /* 또는 뷰포트 기준 높이 (topbar 높이 4rem 제외) */
    /* height: calc(100vh - 4rem); */

    /* 스크롤 활성화 */
    overflow-y: auto;
    overflow-x: hidden;

    /* 스타일링 (선택사항) */
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background-color: #ffffff;
  }

  /* 아이템 스타일 */
  .user-card {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
</style>
```

**장점**:
- ✅ 페이지 레이아웃 제어 가능
- ✅ 다른 컨텐츠와 함께 배치 가능
- ✅ 고정된 영역에 리스트 표시

**단점**:
- ❌ 컨테이너 높이를 명시적으로 설정해야 함
- ❌ 스크롤이 두 개 생길 수 있음 (페이지 스크롤 + 컨테이너 스크롤)

## 7. 컨테이너 높이 설정 방법

### 고정 높이

```css
.list-container {
  height: 500px;  /* 픽셀 단위 */
  overflow-y: auto;
}
```

### 뷰포트 기준 높이

```css
.list-container {
  /* 전체 뷰포트 높이 */
  height: 100vh;

  /* topbar(4rem) 제외 */
  height: calc(100vh - 4rem);

  /* topbar(4rem) + 여백 제외 */
  height: calc(100vh - 6rem);

  overflow-y: auto;
}
```

### Flexbox를 사용한 자동 높이

```css
.page-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.header {
  flex-shrink: 0;  /* 헤더는 고정 */
}

.list-container {
  flex: 1;  /* 남은 공간을 모두 차지 */
  overflow-y: auto;
}
```

## 8. 실전 예제

### 예제 1: Body 스크롤 (사용자 목록 페이지)

```svelte
<script>
  import DatabaseListView from '../lib/components/DatabaseListView.svelte';
  import { login } from '../lib/utils/firebase-login-user.svelte.js';

  function goToProfile(uid) {
    window.history.pushState({}, '', `/user/profile/${uid}`);
    window.dispatchEvent(new PopStateEvent('popstate'));
  }
</script>

<!-- 래퍼 없이 직접 마운트 -->
<DatabaseListView
  path="users"
  pageSize={15}
  orderBy="createdAt"
  threshold={300}
>
  {#snippet item(itemData)}
    <div
      class="user-card"
      onclick={() => goToProfile(itemData.key)}
    >
      <img src={itemData.data?.photoUrl} alt="프로필" />
      <div>
        <h3>{itemData.data?.displayName}</h3>
        <p>{itemData.data?.email}</p>
      </div>
    </div>
  {/snippet}

  {#snippet loading()}
    <div class="loading">로딩 중...</div>
  {/snippet}

  {#snippet empty()}
    <div class="empty">사용자가 없습니다.</div>
  {/snippet}
</DatabaseListView>

<style>
  .user-card {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    cursor: pointer;
  }

  .user-card:hover {
    background-color: #f9fafb;
  }
</style>
```

### 예제 2: 컨테이너 스크롤 (채팅 목록)

```svelte
<script>
  import DatabaseListView from '../lib/components/DatabaseListView.svelte';
</script>

<div class="page-layout">
  <!-- 헤더 -->
  <div class="header">
    <h1>채팅 목록</h1>
    <button>새 채팅</button>
  </div>

  <!-- 채팅 리스트 (스크롤 영역) -->
  <div class="chat-list-container">
    <DatabaseListView
      path="chats"
      pageSize={20}
      orderBy="lastMessageAt"
      reverse={true}
    >
      {#snippet item(itemData)}
        <div class="chat-item">
          <img src={itemData.data?.avatar} alt="프로필" />
          <div>
            <h3>{itemData.data?.title}</h3>
            <p>{itemData.data?.lastMessage}</p>
          </div>
        </div>
      {/snippet}
    </DatabaseListView>
  </div>
</div>

<style>
  .page-layout {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 4rem);  /* topbar 제외 */
  }

  .header {
    flex-shrink: 0;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }

  .chat-list-container {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .chat-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
</style>
```

## 9. 주의사항

### ⚠️ 컨테이너 높이 설정 필수

컨테이너 스크롤 방식을 사용할 때는 **반드시** 래퍼 컨테이너에 명시적인 높이를 설정해야 합니다:

```css
/* ❌ 잘못된 예 - 높이 없음 */
.list-container {
  overflow-y: auto;  /* 높이가 없으면 스크롤 안 됨! */
}

/* ✅ 올바른 예 */
.list-container {
  height: 600px;  /* 또는 calc(100vh - 4rem) */
  overflow-y: auto;
}
```

### ⚠️ box-sizing 고려

패딩이나 보더를 포함한 높이 계산이 필요하면 `box-sizing`을 설정하세요:

```css
.list-container {
  height: calc(100vh - 4rem);
  padding: 1rem;
  box-sizing: border-box;  /* 패딩을 높이에 포함 */
  overflow-y: auto;
}
```

### ⚠️ 스크롤 감지 방식

DatabaseListView는 두 가지 스크롤을 **모두** 감지합니다:
- **컨테이너 스크롤**: 래퍼 div의 내부 스크롤
- **Window 스크롤**: body의 네이티브 스크롤

따라서 두 방식 중 어떤 것을 사용해도 무한 스크롤이 정상 작동합니다.

## 10. 선택 가이드

| 요구사항 | 추천 방식 |
|---------|----------|
| 전체 페이지가 리스트인 경우 | Body 스크롤 |
| 다른 컨텐츠와 함께 배치 | 컨테이너 스크롤 |
| 고정 헤더/푸터 필요 | 컨테이너 스크롤 |
| 심플한 구현 원함 | Body 스크롤 |
| 복잡한 레이아웃 | 컨테이너 스크롤 |
| 모바일 네이티브 느낌 | Body 스크롤 |

## 11. 실시간 노드 삭제 감지

DatabaseListView는 Firebase Realtime Database에서 **노드가 삭제될 때 자동으로 화면에서 제거**하는 기능을 제공합니다.

### 11.1. 개요

- **이벤트**: `onChildRemoved` 리스너 사용
- **자동 처리**: 삭제된 노드가 감지되면 items 배열에서 자동 제거
- **메모리 관리**: 삭제된 노드의 `onValue` 리스너도 자동 해제
- **실시간 동기화**: 다른 사용자가 노드를 삭제해도 즉시 반영

### 11.2. 작동 방식

1. **리스너 설정**:
   - 초기 데이터 로드 완료 후 `onChildRemoved` 리스너 자동 등록
   - 초기 `onChildAdded` 리스너와 동일한 쿼리 범위 사용
   - orderPrefix가 있으면 해당 범위에서만 삭제 감지

2. **삭제 감지**:
   - Firebase에서 노드가 삭제되면 `onChildRemoved` 이벤트 발생
   - 삭제된 노드의 key를 기준으로 items 배열에서 해당 아이템 찾기
   - 해당 아이템을 배열에서 필터링하여 제거

3. **메모리 정리**:
   - 삭제된 노드의 `onValue` 리스너 해제 (메모리 누수 방지)
   - 리스너 맵(unsubscribers)에서 해당 항목 제거

4. **UI 업데이트**:
   - items 배열이 Svelte 5 `$state`로 관리되므로 자동 반응형 업데이트
   - 화면에서 삭제된 아이템이 즉시 사라짐

### 11.3. 사용 예시

#### 예시 1: 게시글 삭제 시 자동 제거

```svelte
<script>
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
  import { deletePost } from '$lib/services/forum.js';

  async function handleDelete(postId) {
    // 게시글 삭제 API 호출
    const result = await deletePost(postId);

    if (result.success) {
      // ✅ DatabaseListView가 자동으로 삭제를 감지하여 화면에서 제거
      // 따로 items 배열을 수동으로 업데이트할 필요 없음!
      console.log('게시글이 삭제되었습니다. 자동으로 화면에서 제거됩니다.');
    }
  }
</script>

<DatabaseListView
  path="posts"
  orderBy="order"
  orderPrefix="community-"
  pageSize={20}
>
  {#snippet item(itemData)}
    <div class="post-card">
      <h3>{itemData.data.title}</h3>
      <p>{itemData.data.content}</p>
      <button onclick={() => handleDelete(itemData.key)}>
        삭제
      </button>
    </div>
  {/snippet}
</DatabaseListView>
```

**결과**:
- ✅ 사용자가 "삭제" 버튼 클릭 → Firebase에서 노드 삭제
- ✅ `onChildRemoved` 리스너가 삭제 감지
- ✅ items 배열에서 해당 게시글 자동 제거
- ✅ 화면에서 즉시 사라짐 (새로고침 불필요)

#### 예시 2: 다른 사용자의 삭제도 실시간 반영

```svelte
<script>
  import DatabaseListView from '$lib/components/DatabaseListView.svelte';
</script>

<!-- 채팅 메시지 목록 -->
<DatabaseListView
  path="chat-messages"
  orderBy="createdAt"
  pageSize={50}
>
  {#snippet item(itemData)}
    <div class="message">
      <p>{itemData.data.text}</p>
      <span>{itemData.data.author}</span>
    </div>
  {/snippet}
</DatabaseListView>
```

**결과**:
- ✅ 사용자 A가 메시지 삭제 → 사용자 B의 화면에서도 즉시 사라짐
- ✅ 실시간 협업 환경에서 자동 동기화
- ✅ 수동으로 폴링하거나 새로고침할 필요 없음

### 11.4. 내부 구현 상세

DatabaseListView는 다음과 같이 삭제를 처리합니다:

```typescript
// 1. onChildRemoved 리스너 등록 (초기 로드 완료 후)
function setupChildRemovedListener() {
  const baseRef = dbRef(database, path);

  // orderPrefix가 있으면 범위 쿼리 사용
  let dataQuery;
  if (orderPrefix) {
    dataQuery = query(
      baseRef,
      orderByChild(orderBy),
      startAt(orderPrefix),
      endAt(orderPrefix + '\uf8ff')
    );
  } else {
    // orderPrefix가 없으면 startAt(false) 사용
    dataQuery = query(
      baseRef,
      orderByChild(orderBy),
      startAt(false)
    );
  }

  // 2. 삭제 이벤트 감지
  childRemovedUnsubscribe = onChildRemoved(dataQuery, (snapshot) => {
    const removedKey = snapshot.key;

    // 3. items 배열에서 제거
    items = items.filter(item => item.key !== removedKey);

    // 4. 해당 아이템의 onValue 리스너 해제 (메모리 관리)
    const unsubscribe = unsubscribers.get(removedKey);
    if (unsubscribe) {
      unsubscribe();
      unsubscribers.delete(removedKey);
    }
  });
}

// 5. 컴포넌트 언마운트 시 리스너 정리
$effect(() => {
  return () => {
    if (childRemovedUnsubscribe) {
      childRemovedUnsubscribe();
      childRemovedUnsubscribe = null;
    }
  };
});
```

### 11.5. 메모리 관리의 중요성

**왜 onValue 리스너도 해제해야 하나요?**

1. **메모리 누수 방지**:
   - 삭제된 노드의 `onValue` 리스너가 계속 실행되면 메모리 낭비
   - 장시간 사용 시 메모리 사용량이 계속 증가

2. **네트워크 비용 절감**:
   - Firebase는 각 리스너마다 실시간 연결 유지
   - 불필요한 리스너는 즉시 해제하여 비용 절감

3. **성능 최적화**:
   - 리스너가 많을수록 이벤트 처리 오버헤드 증가
   - 사용하지 않는 리스너는 제거하여 성능 향상

**DatabaseListView는 자동으로 처리합니다:**
- ✅ 삭제된 노드의 리스너 자동 해제
- ✅ unsubscribers 맵에서 자동 제거
- ✅ 메모리 누수 걱정 없음

### 11.6. 주의사항

#### ⚠️ onChildRemoved는 쿼리 범위 내에서만 작동

orderPrefix를 사용하는 경우, **해당 범위 내에서 삭제된 노드만 감지**합니다:

```svelte
<!-- community 카테고리의 게시글만 표시 -->
<DatabaseListView
  path="posts"
  orderBy="order"
  orderPrefix="community-"
  pageSize={20}
/>
```

**결과**:
- ✅ `order`가 `"community-"`로 시작하는 게시글 삭제 → 감지됨
- ❌ `order`가 `"qna-"`로 시작하는 게시글 삭제 → 감지 안 됨 (범위 밖)

#### ⚠️ 초기 로드 완료 후에만 작동

`onChildRemoved` 리스너는 **초기 데이터 로드가 완료된 후에만 등록**됩니다:

1. **초기 로드 단계**: `onChildAdded` 리스너로 초기 데이터 수집
2. **로드 완료 후**: `setupChildRemovedListener()` 호출
3. **실시간 감지**: 이후 삭제된 노드 자동 감지

**이유**: 초기 로드 중에는 아직 화면에 없는 데이터이므로 삭제 감지가 불필요

### 11.7. 장점

- ✅ **자동 동기화**: 수동으로 배열을 관리할 필요 없음
- ✅ **실시간 반영**: 다른 사용자의 삭제도 즉시 반영
- ✅ **메모리 안전**: 자동 리스너 해제로 메모리 누수 방지
- ✅ **코드 간결화**: 삭제 후 UI 업데이트 로직 불필요
- ✅ **일관성 보장**: Firebase 데이터와 화면이 항상 동기화

### 11.8. 실제 사용 사례

- **게시판**: 게시글 삭제 시 목록에서 자동 제거
- **채팅**: 메시지 삭제 시 채팅창에서 자동 제거
- **댓글**: 댓글 삭제 시 댓글 목록에서 자동 제거
- **사용자 목록**: 사용자 탈퇴 시 목록에서 자동 제거
- **알림**: 알림 삭제 시 알림 목록에서 자동 제거

---

## 12. 요약

- ✅ **자동 null/undefined 필터링**: orderPrefix가 없으면 startAt(false) 자동 적용
- ✅ **orderPrefix 지원**: prefix 기반 범위 쿼리 지원
- ✅ **두 가지 스크롤 방식**: Body 스크롤 (전체 페이지) vs 컨테이너 스크롤 (제한된 영역)
- ✅ **자동 감지**: 두 방식 모두 자동으로 감지하여 무한 스크롤 작동
- ✅ **실시간 노드 삭제**: onChildRemoved로 삭제된 노드 자동 제거 및 리스너 정리
- ✅ **높이 설정 필수**: 컨테이너 스크롤 사용 시 명시적인 높이 설정 필요
- ✅ **Flexbox 활용**: flex를 사용하면 동적 높이 계산 가능
- ✅ **용도별 선택**: 페이지 구조와 요구사항에 맞는 방식 선택

---

## 13. 핵심 구현 원리

### 13.1. Firebase 정렬 순서 보존의 중요성

#### 문제: Object.entries()는 정렬 순서를 보장하지 않음

Firebase Realtime Database는 쿼리 결과를 **정렬된 순서로 반환**하지만, JavaScript에서 이를 잘못 처리하면 순서가 깨집니다.

**❌ 잘못된 방식 (순서가 깨짐)**:
```typescript
const snapshot = await get(dataQuery);
if (snapshot.exists()) {
  const data = snapshot.val(); // 객체로 변환
  const items: ItemData[] = [];

  // ❌ Object.entries()는 프로퍼티 순서를 보장하지 않음!
  Object.entries(data).forEach(([key, value]) => {
    items.push({ key, data: value });
  });

  // 결과: Firebase의 정렬 순서와 다르게 배열이 구성됨
}
```

**문제점**:
- JavaScript 객체의 프로퍼티 순서는 삽입 순서 또는 키 타입에 따라 결정됨
- 특히 문자열 키의 경우 예측 불가능한 순서로 정렬될 수 있음
- `order` 필드처럼 문자열 정렬이 중요한 경우 심각한 문제 발생

**✅ 올바른 방식 (순서 유지)**:
```typescript
const snapshot = await get(dataQuery);
if (snapshot.exists()) {
  const items: ItemData[] = [];

  // ✅ snapshot.forEach()를 사용하여 Firebase의 정렬 순서 유지
  snapshot.forEach((childSnapshot) => {
    const key = childSnapshot.key;
    const data = childSnapshot.val();
    if (key) {
      items.push({ key, data });
    }
  });

  // 결과: Firebase가 반환한 정렬 순서 그대로 배열 구성
}
```

#### 실제 영향

**테스트 데이터**:
```javascript
{
  "test/data": {
    "-ABC123": { "order": "cherry-1699520445266", "title": "[3] 57. [Cherry] [News]" },
    "-ABC124": { "order": "cherry-1699520446266", "title": "[3] 58. [Cherry] [News]" },
    "-ABC125": { "order": "cherry-1699520447266", "title": "[2] 22. [Cherry] [News]" }
  }
}
```

**Firebase 쿼리**:
```typescript
query(
  ref(db, 'test/data'),
  orderByChild('order'),
  startAt('cherry-'),
  endAt('cherry-\uf8ff'),
  limitToFirst(20)
)
```

**Object.entries() 사용 시**:
```
[2] 22. [Cherry] [News]  (order: cherry-1699520447266)
[3] 57. [Cherry] [News]  (order: cherry-1699520445266)  ← 순서가 뒤바뀜!
[3] 58. [Cherry] [News]  (order: cherry-1699520446266)
```

**snapshot.forEach() 사용 시**:
```
[3] 57. [Cherry] [News]  (order: cherry-1699520445266)  ← 올바른 순서
[3] 58. [Cherry] [News]  (order: cherry-1699520446266)
[2] 22. [Cherry] [News]  (order: cherry-1699520447266)
```

#### DatabaseListView 구현

```typescript
// loadInitialData() 함수 내부
async function loadInitialData() {
  // ... Firebase 쿼리 생성 ...

  const snapshot = await get(dataQuery);

  if (snapshot.exists()) {
    let loadedItems: ItemData[] = [];

    // 🔥 중요: snapshot.forEach()를 사용하여 Firebase의 정렬 순서를 유지
    snapshot.forEach((childSnapshot) => {
      const key = childSnapshot.key;
      const data = childSnapshot.val();
      if (key) {
        loadedItems.push({ key, data });
      }
    });

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

    // ... 필터링 및 정렬 처리 ...
  }
}

// loadMore() 함수도 동일한 방식 적용
async function loadMore() {
  // ... Firebase 쿼리 생성 ...

  const snapshot = await get(dataQuery);

  if (snapshot.exists()) {
    const newItems: ItemData[] = [];

    // 🔥 snapshot.forEach() 사용
    snapshot.forEach((childSnapshot) => {
      const key = childSnapshot.key;
      const data = childSnapshot.val();
      if (key) {
        newItems.push({ key, data });
      }
    });

    // ... 이후 처리 ...
  }
}
```

#### 교훈

1. **항상 snapshot.forEach() 사용**
   - Firebase의 정렬 순서를 보존하는 유일한 방법
   - Object.entries()는 절대 사용하지 말 것

2. **디버깅 로그 필수**
   - Firebase 반환 순서를 콘솔에 출력하여 확인
   - 순서 문제를 조기에 발견할 수 있음

3. **문자열 정렬에 특히 주의**
   - `order`, `categoryKey` 같은 문자열 필드로 정렬할 때 더욱 중요
   - 숫자 타입은 상대적으로 덜 민감하지만 여전히 snapshot.forEach() 사용 권장

### 13.2. 디버깅 로그 시스템

DatabaseListView는 모든 주요 작업에 대해 상세한 디버깅 로그를 제공합니다.

#### 로그 색상 체계

```typescript
// 초록색: 성공 및 완료
console.log('%c[DatabaseListView] ✅ Initial Load Complete',
  'color: #10b981; font-weight: bold; font-size: 14px;', data);

// 파란색: 일반 정보
console.log('%c[DatabaseListView] Load More - Page 1',
  'color: #3b82f6; font-weight: bold;', data);

// 보라색: 필터링 결과
console.log('%c[DatabaseListView] After duplicate filtering: 21 → 20 items',
  'color: #8b5cf6;');

// 핑크색: reverse 전 상태
console.log('%c[DatabaseListView] Before reverse:',
  'color: #ec4899;', items);

// 주황색: 경고
console.warn('%c[DatabaseListView] Filtering out item without orderBy field:',
  'color: #f59e0b;', item);
```

#### 초기 로드 로그

```typescript
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
```

#### 필터링 로그

```typescript
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
```

#### reverse 로그

```typescript
if (reverse) {
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
}
```

#### 완료 로그

```typescript
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
```

#### 로그 출력 예시

콘솔에서 다음과 같이 표시됩니다:

```
[DatabaseListView] Initial Load - Query Settings
  { path: "test/data", orderBy: "order", orderPrefix: "cherry-", reverse: true, pageSize: 20 }

[DatabaseListView] Initial Load - Firebase returned 21 items

[DatabaseListView] Initial Load - Items in Firebase order:
  [
    { index: 0, key: "-ABC123", order: "cherry-1699520445266", title: "[3] 57..." },
    { index: 1, key: "-ABC124", order: "cherry-1699520446266", title: "[3] 58..." },
    ...
  ]

[DatabaseListView] Before reverse:
  [
    { index: 0, order: "cherry-1699520445266", title: "[3] 57..." },
    { index: 1, order: "cherry-1699520446266", title: "[3] 58..." },
    ...
  ]

[DatabaseListView] After reverse (newest first):
  [
    { index: 0, order: "cherry-1699520467266", title: "[2] 22..." },
    { index: 1, order: "cherry-1699520466266", title: "[3] 58..." },
    ...
  ]

[DatabaseListView] ✅ Initial Load Complete
  {
    page: 0,
    loaded: 20,
    hasMore: true,
    finalOrder: [...]
  }
```

### 13.3. orderBy 필드 필터링

#### 문제: 페이지네이션 시 orderBy 필드가 없는 항목도 반환됨

Firebase는 `startAt()`과 `endBefore()` 또는 `startAfter()`와 `endBefore()`를 **동시에 사용할 수 없습니다**.

**초기 로드**:
```typescript
// ✅ startAt(false)로 null/undefined 필터링 가능
query(
  baseRef,
  orderByChild('qnaCreatedAt'),
  startAt(false),  // null/undefined 제외
  limitToLast(20)
)
```

**페이지네이션**:
```typescript
// ❌ startAt(false)와 endBefore()를 동시에 사용할 수 없음!
query(
  baseRef,
  orderByChild('qnaCreatedAt'),
  startAt(false),           // ← 불가능!
  endBefore(lastLoadedValue),  // ← 충돌
  limitToLast(20)
)

// ✅ endBefore()만 사용
query(
  baseRef,
  orderByChild('qnaCreatedAt'),
  endBefore(lastLoadedValue),
  limitToLast(20)
)
// 문제: qnaCreatedAt이 없는 항목도 반환될 수 있음
```

#### 해결: 클라이언트 측 필터링

```typescript
async function loadMore() {
  // ... Firebase 쿼리 실행 ...

  snapshot.forEach((childSnapshot) => {
    newItems.push({ key: childSnapshot.key, data: childSnapshot.val() });
  });

  // 중복 제거
  let uniqueItems = newItems.filter((item) => !existingKeys.has(item.key));

  // 🔥 orderBy 필드가 있는 항목만 필터링
  const validItems = uniqueItems.filter((item) => {
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

  uniqueItems = validItems;

  // ... 이후 처리 ...
}
```

#### 왜 초기 로드에서도 필터링하나?

초기 로드에서는 `startAt(false)`를 사용하여 서버 측에서 필터링하지만, **추가 안전성**을 위해 클라이언트에서도 필터링합니다:

```typescript
async function loadInitialData() {
  // ... Firebase 쿼리 (startAt(false) 포함) ...

  snapshot.forEach((childSnapshot) => {
    loadedItems.push({ key: childSnapshot.key, data: childSnapshot.val() });
  });

  // 🔥 추가 안전성을 위한 클라이언트 필터링
  const beforeFilterCount = loadedItems.length;
  loadedItems = loadedItems.filter((item) => {
    return item.data[orderBy] != null && item.data[orderBy] !== '';
  });

  if (beforeFilterCount !== loadedItems.length) {
    console.log(`Filtered out ${beforeFilterCount - loadedItems.length} items`);
  }
}
```

**이유**:
- Firebase 쿼리 동작이 버전에 따라 달라질 수 있음
- 데이터 무결성 보장
- 예외 상황 대비

### 13.4. 실제 인덱스 전달

#### snippet에 index 전달

DatabaseListView는 각 아이템의 **실제 배열 인덱스**를 snippet으로 전달합니다:

```typescript
// Props 타입 정의
type ItemSnippet = Snippet<[itemData: ItemData, index: number]>;

interface Props {
  item: ItemSnippet;
  // ... 다른 props ...
}

// 템플릿에서 index 전달
{#each items as itemData, index (itemData.key)}
  <div class="item-wrapper" data-key={itemData.key}>
    {#if item}
      {@render item(itemData, index)}  {/* ← index 전달 */}
    {/if}
  </div>
{/each}
```

#### 상위 컴포넌트에서 활용

```svelte
<DatabaseListView path="test/data" pageSize={20} orderBy="order" orderPrefix="cherry-">
  {#snippet item(itemData: { key: string; data: any }, index: number)}
    {@const actualPageNumber = Math.floor(index / 20) + 1}
    {@const actualOrderNumber = index + 1}

    <div class="item-card">
      <p>페이지: {actualPageNumber}</p>
      <p>순서: {actualOrderNumber}</p>
      <p>인덱스: {index}</p>
      <h3>{itemData.data.title}</h3>
    </div>
  {/snippet}
</DatabaseListView>
```

**결과**:
```
페이지: 1, 순서: 1, 인덱스: 0
페이지: 1, 순서: 2, 인덱스: 1
페이지: 1, 순서: 3, 인덱스: 2
...
페이지: 1, 순서: 20, 인덱스: 19
페이지: 2, 순서: 21, 인덱스: 20
페이지: 2, 순서: 22, 인덱스: 21
```

#### 필터링된 데이터에서도 정확한 순서

orderBy 필드로 필터링하면 실제 표시되는 항목만 카운트됩니다:

```svelte
<!-- qnaCreatedAt으로 필터링 -->
<DatabaseListView orderBy="qnaCreatedAt">
  {#snippet item(itemData, index)}
    <div>
      {index + 1}. {itemData.data.title}
      <!-- Q&A 항목만 1, 2, 3... 순서로 표시 -->
    </div>
  {/snippet}
</DatabaseListView>
```

## 14. 구현 및 테스트 사례

### 14.1. 컴포넌트 파일 위치

- **컴포넌트**: [src/lib/components/DatabaseListView.svelte](../src/lib/components/DatabaseListView.svelte)
- **타입**: Svelte 5 컴포넌트 (`.svelte`)
- **크기**: ~1350 라인 (주석 포함)

### 14.2. 구현된 페이지

#### 1. 사용자 목록 페이지

**경로**: `/user/list`
**파일**: [src/routes/user/list/+page.svelte](../src/routes/user/list/+page.svelte)

**구현 내용**:
```svelte
<DatabaseListView
  path="users"
  pageSize={15}
  orderBy="createdAt"
  threshold={300}
  reverse={false}
>
  {#snippet item(itemData)}
    <!-- 사용자 카드 UI -->
  {/snippet}
</DatabaseListView>
```

**주요 기능**:
- Firebase RTDB의 `users` 경로에서 사용자 데이터 로드
- createdAt 필드로 정렬 (오래된 사용자부터)
- 무한 스크롤로 15개씩 로드
- 사용자 프로필 카드 UI 제공
- 클릭 시 사용자 프로필 페이지로 이동

**UI 특징**:
- 사용자 아바타 이미지 (또는 placeholder)
- 이름, 이메일, 가입일, 마지막 로그인 시간 표시
- 호버 효과 및 클릭 가능한 카드
- 반응형 디자인 지원

#### 2. DatabaseListView 테스트 페이지

**경로**: `/dev/test/database-list-view`
**파일**: [src/routes/dev/test/database-list-view/+page.svelte](../src/routes/dev/test/database-list-view/+page.svelte)

**테스트 케이스**:

1. **기본 사용 (정순 정렬)**
   - path: "users"
   - orderBy: "createdAt"
   - reverse: false
   - 오래된 사용자부터 표시

2. **역순 정렬**
   - reverse: true
   - 최신 사용자부터 표시
   - limitToLast 사용

3. **orderPrefix 필터링**
   - path: "posts"
   - orderBy: "categoryKey"
   - orderPrefix: "community-"
   - 특정 카테고리만 필터링

4. **컨테이너 스크롤**
   - 고정 높이 컨테이너 (600px)
   - 컨테이너 내부 스크롤 감지
   - overflow-y: auto

**테스트 UI 특징**:
- 탭 방식으로 테스트 케이스 선택
- 각 케이스별 코드 예시 제공
- 테스트 설명 및 주의사항 표시
- 실시간 동작 확인 가능

> ℹ️ 2025-11-09 기준으로 `/admin/test/database-list-view` 페이지는 `/dev/test/database-list-view`와 기능이 완전히 중복되어 제거되었습니다. 이제 모든 DatabaseListView QA는 개발용 경로(`/dev/test/...`)에서만 수행합니다.

### 14.3. 구현 과정

#### Phase 1: Custom Elements → Svelte 5 변환 (2025-01-09)

**작업 내용**:
1. Custom Elements 방식의 DatabaseListView 분석
2. Svelte 5 문법으로 변환:
   - `this.getAttribute()` → `$props()`
   - `this.state` → `$state()`
   - `connectedCallback()` → `$effect()`
   - `<template>` → `{#snippet}`
3. prop 이름 변경: `sortPrefix` → `orderPrefix`
4. 타입 안전성 향상 (TypeScript 적용)

**주요 개선사항**:
- Svelte의 자동 반응형 시스템 활용
- 코드 가독성 향상
- 타입 안전성 확보
- 더 간결한 템플릿 문법

#### Phase 2: 페이지 구현 (2025-01-09)

**작업 내용**:
1. `/user/list` 페이지 생성
   - DatabaseListView 활용
   - 사용자 목록 UI 디자인
   - 프로필 페이지 연동

2. `/dev/test/database-list-view` 테스트 페이지 생성
   - 4가지 테스트 케이스 구현
   - 코드 예시 제공
   - 인터랙티브 테스트 환경

#### Phase 3: 문서화 (2025-01-09)

**작업 내용**:
1. 스펙 문서 업데이트
   - Custom Elements → Svelte 5 마이그레이션 가이드 추가
   - `sortPrefix` → `orderPrefix` 전체 변경
   - 마이그레이션 체크리스트 제공
2. 구현 사례 문서화
3. 테스트 방법 문서화

### 14.4. 검증 방법

#### 1. 수동 테스트

**테스트 페이지 접속**:
```
http://localhost:5173/dev/test/database-list-view
```

**확인 항목**:
- [ ] 초기 데이터 로드 정상 작동
- [ ] 스크롤 시 다음 페이지 자동 로드
- [ ] reverse 모드 정상 작동
- [ ] orderPrefix 필터링 정상 작동
- [ ] 컨테이너 스크롤 감지 정상 작동
- [ ] 실시간 데이터 업데이트 반영
- [ ] 노드 삭제 시 자동 제거

#### 2. 콘솔 로그 확인

DatabaseListView는 상세한 디버깅 로그를 제공합니다:

```javascript
// 초기 로드
DatabaseListView: Loading initial data from users (reverse: false)
DatabaseListView: Using limitToFirst with startAt(false) to filter null/undefined
DatabaseListView: Initial query returned 11 items from Firebase
DatabaseListView: Page 0 - Loaded 10 items, hasMore: true

// 페이지네이션
DatabaseListView: Near bottom (window scroll), loading more...
DatabaseListView: Loading more data (server-side pagination) - Page 1
DatabaseListView: Using startAfter + limitToFirst for normal pagination

// 실시간 업데이트
DatabaseListView: Setting up child_added listener for users
DatabaseListView: New child added: userId123
DatabaseListView: Setting up child_removed listener for users
DatabaseListView: Child removed: userId456
```

#### 3. Firebase Console 확인

**데이터 구조 확인**:
```
users/
  userId1/
    displayName: "홍길동"
    email: "hong@example.com"
    photoUrl: "https://..."
    createdAt: 1704844800000
    lastLoginAt: 1704931200000
```

**필수 필드**:
- `createdAt`: 정렬 기준 필드 (timestamp)
- `displayName`: 사용자 이름
- `email`: 이메일 주소

### 14.5. 성능 최적화

#### 1. 메모리 관리

- onValue 리스너 자동 해제
- 컴포넌트 언마운트 시 모든 리스너 정리
- unsubscribers Map으로 리스너 추적

#### 2. 네트워크 최적화

- pageSize + 1 쿼리로 hasMore 판단
- startAt(false)로 null/undefined 필터링
- Firebase 쿼리 레벨에서 필터링

#### 3. 렌더링 최적화

- Svelte의 자동 최적화 활용
- key 기반 리스트 렌더링
- 중복 제거 로직

### 14.6. 알려진 제약사항

1. **Firebase 쿼리 제약**:
   - startAt()과 startAfter()를 동시에 사용할 수 없음
   - orderPrefix 필터링 시 클라이언트 측 필터링 필요 (페이지네이션 시)

2. **orderBy 필드 필수**:
   - 모든 아이템이 orderBy 필드를 가지고 있어야 함
   - 필드가 없으면 페이지네이션 오류 발생

3. **컨테이너 스크롤**:
   - 명시적인 높이 설정 필요
   - overflow-y: auto 필수

### 14.7. 향후 개선 계획

1. **검색 기능 추가**
   - 텍스트 검색
   - 필터링 옵션

2. **정렬 옵션**
   - 다중 필드 정렬
   - 정렬 방향 변경

3. **UI 개선**
   - 로딩 스켈레톤
   - 페이지네이션 상태 표시
   - 스크롤 위치 복원

4. **성능 개선**
   - 가상 스크롤링
   - 지연 로딩
   - 캐싱 전략
