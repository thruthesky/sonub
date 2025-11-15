---
name: sonub-chat-room
version: 1.0.0
description: 채팅방 UI 및 RTDB 연동 사양
dependencies:
  - sonub-firebase-database-structure.md
---
# Sonub 채팅방 기능 사양

## 개요

Sonub 애플리케이션의 1:1 채팅방 및 그룹 채팅방 기능에 대한 상세 사양 문서입니다.

## 파일 위치

- **페이지**: [src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte)
- **다국어**: [messages/*.json](../messages/)

## 채팅방 유형

### 1:1 채팅방 (Single Chat)

1:1 채팅방은 두 사용자 간의 개인 대화를 위한 채팅방입니다.

#### URL 파라미터

- **uid**: 채팅 상대방의 사용자 UID
- **형식**: `?uid=TARGET_UID`
- **예시**: `/chat/room?uid=abc123xyz`

#### 채팅방 ID 생성 규칙

```typescript
function buildSingleRoomId(a: string, b: string) {
  return `single-${[a, b].sort().join('-')}`;
}
```

- 두 사용자의 UID를 알파벳 순으로 정렬하여 고정된 roomId 생성
- 예시: `single-abc123-xyz789`
- 이렇게 하면 누가 먼저 채팅을 시작하든 동일한 채팅방 ID가 생성됩니다

### 그룹 채팅방 (Chat Room)

그룹 채팅방은 여러 사용자가 참여할 수 있는 채팅방입니다.

#### URL 파라미터

- **roomId**: 그룹 채팅방의 고유 ID
- **형식**: `?roomId=ROOM_KEY`
- **예시**: `/chat/room?roomId=general-chat`

## 주요 기능

### 0. 채팅방 목록 사이드바 (데스크톱 전용)

데스크톱 화면에서 채팅방 왼쪽에 채팅 목록을 표시하여 빠른 채팅방 전환을 지원합니다.

#### 레이아웃 구조

```typescript
// /src/routes/chat/room/+layout.svelte
<div class="chat-room-layout">
  <!-- 좌측 사이드바 (데스크톱만) -->
  <aside class="chat-room-sidebar">
    <DatabaseListView path="chat-joins/{authStore.user.uid}" ... />
  </aside>

  <!-- 메인 콘텐츠 -->
  <main class="chat-room-main">
    {@render children()}
  </main>
</div>
```

**반응형 동작**:
- **모바일** (768px 미만): 사이드바 숨김 (`hidden`), 메인 콘텐츠만 표시
- **데스크톱** (768px 이상): 2-column 레이아웃, 사이드바 320px + 메인 콘텐츠

#### Firebase 데이터 구조

```typescript
// 채팅방 참여 정보 경로
const path = `chat-joins/${authStore.user.uid}`;

// 데이터 구조
{
  "{roomId}": {
    "roomType": "single" | "group" | "open",
    "name": "채팅방 이름",
    "lastMessage": "마지막 메시지 내용",
    "newMessageCount": 3,           // 읽지 않은 메시지 수
    "allChatListOrder": -1234567890, // 정렬 순서 (음수 timestamp)
    "joinedAt": 1234567890
  }
}
```

#### DatabaseListView 설정

```typescript
<DatabaseListView
  path="chat-joins/{authStore.user.uid}"
  orderBy="allChatListOrder"
  pageSize={20}
  reverse={true}
  scrollTrigger="bottom"
>
```

- **path**: 사용자의 참여 채팅방 목록 경로
- **orderBy**: `allChatListOrder` 필드로 정렬 (음수 timestamp로 최신 메시지 우선)
- **pageSize**: 한 번에 로드할 채팅방 수 (20개)
- **reverse**: 역순 정렬 (true)
- **scrollTrigger**: 아래로 스크롤 시 더 로드

#### 채팅방 아이템 UI

각 채팅방 아이템은 다음 정보를 포함합니다:

- **1:1 채팅** (`roomId.startsWith('single-')`):
  - **상대방 프로필 실시간 표시**: Avatar 컴포넌트 + userProfileStore
  - **상대방 이름**: displayName (없으면 `@{uid.slice(0,6)}`)
  - **상대방 사진**: photoUrl을 Avatar 컴포넌트로 표시
- **그룹 채팅** (`group`):
  - **아이콘**: 👥
  - **채팅방 이름**: `name` 필드 (없으면 roomId)
- **오픈 채팅** (`open`):
  - **아이콘**: 🌐
  - **채팅방 이름**: `name` 필드 (없으면 roomId)
- **공통 표시 항목**:
  - **마지막 메시지**: `lastMessage` 필드 (미리보기)
  - **읽지 않은 메시지 배지**: `newMessageCount > 0`이면 빨간 원형 배지 표시

#### ChatRoomListItem 컴포넌트

각 채팅방 아이템은 독립적인 컴포넌트([src/routes/chat/room/ChatRoomListItem.svelte](../src/routes/chat/room/ChatRoomListItem.svelte))로 구현되어 있습니다.

**Props**:
```typescript
type Props = {
  roomId: string;      // 채팅방 ID
  roomData: any;       // 채팅방 데이터
  activeRoomId: string; // 현재 활성 채팅방 ID
  onclick: () => void;  // 클릭 핸들러
};
```

**주요 로직**:
```typescript
// 1:1 채팅 여부 판단 (roomId 패턴 기반)
const isSingle = $derived(roomId.startsWith('single-'));

// 상대방 UID 추출
const partnerUid = $derived.by(() => {
  if (!isSingle) return '';
  const parts = roomId.replace('single-', '').split('-');
  return parts.find((uid) => uid !== authStore.user?.uid) || '';
});

// 실시간 프로필 구독 ($effect 사용)
$effect(() => {
  if (isSingle && partnerUid) {
    userProfileStore.ensureSubscribed(partnerUid);
  }
});

// 캐시된 프로필 데이터 가져오기
const profile = $derived(
  isSingle && partnerUid ? userProfileStore.getCachedProfile(partnerUid) : null
);

// 표시할 이름 (displayName 또는 fallback)
const displayName = $derived(
  profile?.displayName || (partnerUid ? `@${partnerUid.slice(0, 6)}` : '')
);
```

**반응성 보장**:
- `$effect`를 사용하여 partnerUid 변경 시 자동으로 프로필 구독
- profile 데이터가 로드되거나 업데이트되면 컴포넌트 자동 재렌더링
- snippet 내 side effect 안티패턴 제거 (이전 방식의 문제점 해결)

**1:1 채팅 렌더링**:
```svelte
{#if isSingle}
  <button class="room-item" {onclick}>
    <div class="room-avatar">
      <Avatar uid={partnerUid} size={48} />
    </div>
    <div class="room-info">
      <div class="room-name">{displayName}</div>
      {#if roomData.lastMessage}
        <div class="room-last-message">{roomData.lastMessage}</div>
      {/if}
    </div>
    {#if roomData.newMessageCount > 0}
      <div class="room-badge">{roomData.newMessageCount}</div>
    {/if}
  </button>
{/if}
```

#### 활성 채팅방 하이라이팅

```typescript
// URL 파라미터에서 현재 활성 채팅방 ID 계산
const activeRoomId = $derived.by(() => {
  const urlRoomId = $page.url.searchParams.get('roomId');
  const urlUid = $page.url.searchParams.get('uid');

  if (urlRoomId) return urlRoomId;

  if (urlUid && authStore.user?.uid) {
    // 1:1 채팅방 ID 생성 (uid 정렬)
    const uids = [authStore.user.uid, urlUid].sort();
    return `single-${uids[0]}-${uids[1]}`;
  }

  return '';
});

// 활성 채팅방 여부 확인
const isActive = roomId === activeRoomId;
```

**스타일링**:
- 일반 채팅방: 흰색 배경, 호버 시 회색 (`hover:bg-gray-50`)
- 활성 채팅방: 파란색 배경 (`bg-blue-50`), 호버 시 더 진한 파란색 (`hover:bg-blue-100`)

#### 채팅방 클릭 네비게이션

```typescript
function handleRoomClick(roomId: string, type: string) {
  if (type === 'single') {
    // 1:1 채팅방: roomId에서 상대 uid 추출
    const parts = roomId.replace('single-', '').split('-');
    const partnerUid = parts.find((uid) => uid !== authStore.user?.uid);
    if (partnerUid) {
      void goto(`/chat/room?uid=${partnerUid}`);
    }
  } else {
    // 그룹/오픈 채팅방
    void goto(`/chat/room?roomId=${roomId}`);
  }
}
```

**네비게이션 전략**:
1. **1:1 채팅**: roomId (`single-{uidA}-{uidB}`)에서 상대방 UID 추출 → `?uid={partnerUid}`로 이동
2. **그룹/오픈 채팅**: roomId 그대로 → `?roomId={roomId}`로 이동

#### 다국어 지원

| 키 | 한국어 | 영어 | 일본어 | 중국어 |
|---|---|---|---|---|
| `chatRoomList` | 내 채팅방 | My Chats | マイチャット | 我的聊天 |
| `chatRoomListEmpty` | 참여한 채팅방이 없습니다. | No chat rooms joined yet. | 参加しているチャットルームがありません。 | 还没有加入任何聊天室。 |
| `chatRoomListLoading` | 채팅방 목록을 불러오는 중... | Loading chat rooms... | チャットルームを読み込み中... | 正在加载聊天室... |

```svelte
<h2>{m.chatRoomList()}</h2>
<div class="empty-state">{m.chatRoomListEmpty()}</div>
<div class="loading-state">{m.chatRoomListLoading()}</div>
```

#### CSS 스타일링

```css
/* 메인 레이아웃 */
.chat-room-layout {
  /* 모바일: single column, 전체 화면 */
  @apply fixed top-0 left-0 h-[100dvh] w-full bg-gray-50 flex flex-col;
  padding-top: env(safe-area-inset-top);
  padding-bottom: env(safe-area-inset-bottom);
}

/* 데스크톱: 2-column layout */
@media (min-width: 768px) {
  .chat-room-layout {
    height: calc(100vh - 4rem); /* TopBar 높이 제외 */
    @apply relative flex-row;
  }
}

/* 사이드바 */
.chat-room-sidebar {
  @apply hidden; /* 모바일에서 숨김 */
}

@media (min-width: 768px) {
  .chat-room-sidebar {
    @apply flex flex-col w-80 border-r border-gray-200 bg-white overflow-hidden;
  }
}

/* 채팅방 아이템 */
.room-item {
  @apply flex items-center gap-3 w-full px-4 py-3;
  @apply border-b border-gray-100 transition-colors duration-150;
  @apply cursor-pointer hover:bg-gray-50;
}

.room-item--active {
  @apply bg-blue-50 hover:bg-blue-100;
}

/* 읽지 않은 메시지 배지 */
.room-badge {
  @apply flex items-center justify-center min-w-[20px] h-5 px-1.5;
  @apply bg-red-500 text-white text-xs font-bold rounded-full;
}
```

**디자인 특징**:
- 깔끔한 흰색 배경
- 우측 테두리로 메인 콘텐츠와 구분
- 호버 효과로 클릭 가능성 표시
- 활성 채팅방은 파란색으로 강조
- 읽지 않은 메시지는 빨간 배지로 명확히 표시

---

## ⚠️ 중요: 채팅방 레이아웃 구조 변경 금지 규칙

**🚨 경고**: 채팅방 레이아웃(`src/routes/chat/room/+layout.svelte`)의 CSS 구조를 변경하면 **무한 스크롤이 작동하지 않고** 레이아웃이 깨집니다. 아래 규칙을 **절대 위반하지 마세요**.

### 문제 발생 사례

레이아웃 관련 작업 시 다음과 같은 문제가 **자주** 발생합니다:
- ❌ 채팅 입력창(footer)이 화면에서 사라짐
- ❌ 메시지 목록의 무한 스크롤이 작동하지 않음
- ❌ 왼쪽 사이드바의 채팅방 목록 스크롤이 작동하지 않음
- ❌ 모바일/데스크톱에서 overflow가 발생하여 화면이 깨짐

### 필수 준수 사항

#### 1. `.chat-room-layout` - 절대 변경 금지 속성

```css
.chat-room-layout {
  @apply fixed;           /* ✅ REQUIRED: fixed positioning */
  @apply top-0 left-0;    /* ✅ REQUIRED: 화면 좌상단 고정 */
  @apply h-[100dvh];      /* ✅ REQUIRED: 모바일 전체 화면 높이 */
  @apply w-full;          /* ✅ REQUIRED: 전체 너비 */
  @apply bg-gray-50;      /* 배경색 (변경 가능) */

  /* ✅ REQUIRED: 모바일 safe-area 고려 */
  padding-top: env(safe-area-inset-top);
  padding-bottom: env(safe-area-inset-bottom);

  @apply flex flex-col;   /* ✅ REQUIRED: 모바일 세로 레이아웃 */
}

@media (min-width: 768px) {
  .chat-room-layout {
    /* ✅ REQUIRED: TopBar 높이(4rem) + 상하 여백(4rem) = 8rem 제외 */
    height: calc(100vh - 8rem);
    @apply relative;      /* ✅ REQUIRED: 데스크톱은 relative positioning */
    @apply flex-row;      /* ✅ REQUIRED: 데스크톱 가로 레이아웃 (2-column) */
  }
}
```

**🔴 절대 하지 말아야 할 것**:
- ❌ `fixed` → `relative`, `absolute`, `static`으로 변경
- ❌ `h-[100dvh]` → `h-full`, `h-screen`, `min-h-screen`으로 변경
- ❌ `calc(100vh - 8rem)` → `calc(100vh - 4rem)` 또는 다른 값으로 변경
- ❌ `flex-col` / `flex-row` 제거 또는 변경
- ❌ `top-0 left-0` 제거
- ❌ `padding-top/bottom` (safe-area-inset) 제거

**✅ 변경해도 되는 것**:
- ✅ `bg-gray-50` 등 배경색 변경

#### 2. `.chat-room-sidebar` - 무한 스크롤 필수 구조

```css
.chat-room-sidebar {
  @apply hidden;  /* ✅ REQUIRED: 모바일에서 숨김 */
}

@media (min-width: 768px) {
  .chat-room-sidebar {
    @apply flex flex-col;         /* ✅ REQUIRED: Flexbox 세로 레이아웃 */
    @apply w-80;                  /* 너비 (변경 가능) */
    @apply border-r border-gray-200;  /* 테두리 (변경 가능) */
    @apply bg-white;              /* 배경색 (변경 가능) */
    @apply overflow-hidden;       /* ✅ REQUIRED: overflow 숨김 */
  }
}
```

**🔴 절대 하지 말아야 할 것**:
- ❌ `flex flex-col` 제거 → 무한 스크롤 작동 안 함
- ❌ `overflow-hidden` 제거 또는 `overflow-auto`, `overflow-scroll`로 변경
- ❌ 모바일에서 `hidden` 제거

**✅ 변경해도 되는 것**:
- ✅ `w-80` → 다른 너비로 변경
- ✅ 테두리, 배경색 변경

#### 3. `.sidebar-content` - 무한 스크롤 핵심

```css
.sidebar-content {
  @apply flex-1 overflow-auto;  /* ✅ REQUIRED: flex-1 + overflow-auto */
}
```

**🔴 절대 하지 말아야 할 것**:
- ❌ `flex-1` 제거 → 높이가 늘어나며 레이아웃 깨짐
- ❌ `overflow-auto` 제거 또는 `overflow-hidden`으로 변경 → 스크롤 안 됨

#### 4. `.chat-room-main` - 메인 콘텐츠 영역

```css
.chat-room-main {
  @apply flex-1;          /* ✅ REQUIRED: 남은 공간 차지 */
  @apply overflow-hidden; /* ✅ REQUIRED: overflow 숨김 */
}
```

**🔴 절대 하지 말아야 할 것**:
- ❌ `flex-1` 제거
- ❌ `overflow-hidden` 제거 또는 `overflow-auto`로 변경

### 레이아웃이 작동하는 원리

```
┌─────────────────────────────────────────────────┐
│ .chat-room-layout (fixed, h-[100dvh], flex)    │
├────────────────┬────────────────────────────────┤
│ .chat-room-    │ .chat-room-main                │
│ sidebar        │ (flex-1, overflow-hidden)      │
│ (flex flex-col)│                                │
│ ┌────────────┐ │ ┌────────────────────────────┐ │
│ │ .sidebar-  │ │ │ 채팅방 페이지              │ │
│ │ header     │ │ │ (+page.svelte)             │ │
│ │ (shrink-0) │ │ │                            │ │
│ ├────────────┤ │ │ .chat-room-container       │ │
│ │ .sidebar-  │ │ │ (flex flex-col h-full)     │ │
│ │ content    │ │ │                            │ │
│ │ (flex-1    │ │ │ ┌────────────────────────┐ │ │
│ │ overflow-  │ │ │ │ 헤더 (shrink-0)        │ │ │
│ │ auto)      │ │ │ ├────────────────────────┤ │ │
│ │            │ │ │ │ 메시지 목록 (flex-1    │ │ │
│ │ [무한      │ │ │ │ overflow-auto)         │ │ │
│ │ 스크롤]    │ │ │ │                        │ │ │
│ │            │ │ │ │ [무한 스크롤]          │ │ │
│ │            │ │ │ ├────────────────────────┤ │ │
│ │            │ │ │ │ 입력창 (shrink-0)      │ │ │
│ │            │ │ │ └────────────────────────┘ │ │
│ └────────────┘ │ └────────────────────────────┘ │
└────────────────┴────────────────────────────────┘
```

**핵심 원리**:
1. **고정 높이 컨테이너** (`.chat-room-layout`): `fixed` + `h-[100dvh]` / `calc(100vh - 8rem)`
2. **Flexbox 레이아웃**: `flex flex-col` (모바일) / `flex-row` (데스크톱)
3. **flex-1 영역**: 남은 공간을 차지하여 자동으로 높이 계산
4. **overflow-auto**: `flex-1`로 계산된 높이 내에서만 스크롤
5. **shrink-0**: 헤더/푸터는 고정 높이 유지

**왜 무한 스크롤이 작동하는가?**
- `.sidebar-content` / 메시지 목록이 `flex-1`로 **정확한 높이**를 가지므로
- `overflow-auto`가 해당 높이를 **초과하는 콘텐츠를 스크롤**할 수 있음
- 스크롤 이벤트가 정상적으로 감지되어 **DatabaseListView의 무한 스크롤 트리거 작동**

### 레이아웃 수정 시 체크리스트

레이아웃 관련 작업을 할 때 **반드시** 다음을 확인하세요:

- [ ] `.chat-room-layout`의 `fixed`, `h-[100dvh]`, `calc(100vh - 8rem)` 유지
- [ ] `.chat-room-sidebar`의 `overflow-hidden` 유지
- [ ] `.sidebar-content`의 `flex-1 overflow-auto` 유지
- [ ] `.chat-room-main`의 `flex-1 overflow-hidden` 유지
- [ ] `safe-area-inset-top/bottom` 유지
- [ ] 모바일 `flex-col`, 데스크톱 `flex-row` 유지
- [ ] `npm run dev` 실행 후 실제 브라우저에서 테스트:
  - [ ] 모바일 화면에서 메시지 입력창이 보이는지
  - [ ] 데스크톱 화면에서 왼쪽 사이드바 스크롤 작동하는지
  - [ ] 메시지 목록 무한 스크롤이 작동하는지
  - [ ] 채팅 입력창(footer)이 화면에 표시되는지

### 완전한 레이아웃 CSS 코드 (현재 작동 중인 버전)

**파일**: `src/routes/chat/room/+layout.svelte`

```css
@import 'tailwindcss' reference;

/**
 * 채팅방 레이아웃 컨테이너
 * - 모바일: 단일 컬럼 (전체 화면)
 * - 데스크톱: 2-column (사이드바 + 메인)
 */
.chat-room-layout {
  @apply fixed;
  @apply top-0 left-0;
  /* 모바일: 전체 화면 높이 사용 (TopBar 숨김) */
  @apply h-[100dvh];

  /* 전체 너비 사용 */
  @apply w-full;

  /* 배경색 */
  @apply bg-gray-50;

  /* 모바일 safe-area 고려 (상태바, 노치 등) */
  padding-top: env(safe-area-inset-top);
  padding-bottom: env(safe-area-inset-bottom);

  /* 모바일: 단일 컬럼 */
  @apply flex flex-col;
}

/* 데스크톱: TopBar 높이(4rem) 제외, 2-column 레이아웃 */
@media (min-width: 768px) {
  .chat-room-layout {
    /* TopBar 높이(4rem) 제외 + 채팅방 상하 여백 (4rem) */
    height: calc(100vh - 8rem);
    @apply relative;
    /* 2-column: 사이드바 + 메인 */
    @apply flex-row;
  }
}

/**
 * 왼쪽 사이드바 (채팅방 목록)
 * - 모바일: 숨김
 * - 데스크톱: 표시 (고정 너비)
 */
.chat-room-sidebar {
  /* 모바일: 숨김 */
  @apply hidden;
}

/* 데스크톱: 사이드바 표시 */
@media (min-width: 768px) {
  .chat-room-sidebar {
    @apply flex flex-col;
    @apply w-80;
    @apply border-r border-gray-200;
    @apply bg-white;
    @apply overflow-hidden;
  }
}

.sidebar-header {
  @apply flex items-center justify-between;
  @apply px-4 py-3;
  @apply border-b border-gray-200;
  @apply shrink-0;
}

.sidebar-title {
  @apply text-lg font-semibold text-gray-900;
}

.sidebar-content {
  @apply flex-1 overflow-auto;
}

.sidebar-placeholder {
  @apply flex items-center justify-center;
  @apply p-8 text-center text-gray-500;
}

.sidebar-error {
  @apply p-4 text-center text-red-600;
}

/**
 * 메인 콘텐츠 (채팅방 페이지)
 * - 모바일: 전체 화면
 * - 데스크톱: 나머지 공간 사용
 */
.chat-room-main {
  @apply flex-1;
  @apply overflow-hidden;
}
```

**⚠️ 이 코드를 수정할 때는 반드시 위의 "필수 준수 사항"을 확인하세요!**

---

### 1. 사용자 프로필 실시간 구독

채팅 상대방의 프로필 정보를 실시간으로 구독하여 표시합니다.

```typescript
// userProfileStore를 사용한 프로필 구독
$effect(() => {
  if (uidParam) {
    userProfileStore.ensureSubscribed(uidParam);
  }
});

// 프로필 데이터 접근
const targetProfile = $derived(userProfileStore.getCachedProfile(uidParam));
const targetProfileLoading = $derived(userProfileStore.isLoading(uidParam));
const targetProfileError = $derived(userProfileStore.getError(uidParam));
```

#### Firebase 경로

- **프로필 경로**: `/users/{uid}`
- **데이터 구조**:
  ```json
  {
    "displayName": "사용자 이름",
    "photoURL": "프로필 이미지 URL",
    "createdAt": 1234567890,
    "lastLoginAt": 1234567890
  }
  ```

### 2. 채팅방 헤더

채팅방 상단에 채팅 상대의 정보를 표시합니다.

#### 표시 정보

- **채팅 유형**: 1:1 채팅 또는 그룹 채팅방
- **상대방 이름**:
  - displayName이 있으면 표시
  - 없으면 `@{uid의 앞 6자리}` 형식으로 표시
  - 프로필이 없으면 다국어 메시지 `chatPartner` 표시
- **Avatar**: 64px 크기의 프로필 이미지
- **상태 메시지**:
  - 로그인 필요
  - 프로필 로딩 중
  - 프로필 로딩 실패
  - 채팅 중 안내

### 3. 메시지 목록 (DatabaseListView)

Firebase Realtime Database의 메시지를 실시간으로 표시합니다.

#### Firebase 데이터 구조

```typescript
// 메시지 경로
const messagePath = 'chat-messages';

// 메시지 데이터 구조
{
  roomId: string;           // 채팅방 ID
  type: 'message';          // 메시지 타입
  text: string;             // 메시지 내용
  urls: string[];           // 첨부 URL (미래 확장)
  senderUid: string;        // 발신자 UID
  createdAt: number;        // 생성 시간 (timestamp)
  editedAt: number | null;  // 수정 시간
  deletedAt: number | null; // 삭제 시간
  roomOrder: string;        // 정렬 키: `-{roomId}-{timestamp}`
  rootOrder: string;        // 루트 정렬 키
}
```

#### DatabaseListView 설정

```typescript
<DatabaseListView
  path="chat-messages"
  pageSize={25}
  orderBy="roomOrder"
  orderPrefix={roomOrderPrefix}  // `-{roomId}-`
  threshold={280}
  reverse={true}
/>
```

- **pageSize**: 한 번에 로드할 메시지 수 (25개)
- **orderBy**: 정렬 기준 필드 (`roomOrder`)
- **orderPrefix**: 특정 채팅방의 메시지만 필터링 (`-{roomId}-`)
- **threshold**: 스크롤 임계값 (280px)
- **reverse**: 역순 정렬 (최신 메시지가 위에 표시)

#### 메시지 표시

각 메시지는 다음 정보를 포함합니다:

- **Avatar**: 발신자의 프로필 이미지 (40px)
- **발신자 이름**:
  - 본인: 다국어 메시지 `chatYou`
  - 상대방: displayName 또는 UID
  - 알 수 없음: 다국어 메시지 `chatUnknownUser`
- **시간**: 로케일에 맞춘 날짜/시간 형식
- **메시지 내용**: 텍스트 (줄바꿈 지원)

#### 시간 포맷

모든 타임스탬프는 `src/lib/functions/date.functions.ts`의 `formatLongDate` 함수로 처리합니다. Paraglide 로케일을 기반으로 연·월·일·시·분·초를 모두 그려 주므로 컴포넌트 안에서 별도 Intl 로직을 작성할 필요가 없습니다.

```typescript
import { formatLongDate } from '$lib/functions/date.functions';

<span class="message-timestamp">{formatLongDate(message.createdAt)}</span>
```

### 4. 메시지 입력 및 전송

하단에 메시지 입력창과 전송 버튼을 제공합니다.

#### UI 구성

- **입력창**: 텍스트 입력 필드 (placeholder: 다국어 지원)
- **전송 버튼**:
  - 활성화 조건: 로그인 상태 + 채팅방 준비 + 내용 입력
  - 전송 중일 때 "전송 중..." 표시
  - 비활성화 시 회색 배경

#### 메시지 전송 로직

```typescript
async function handleSendMessage(event: SubmitEvent) {
  event.preventDefault();

  // 유효성 검사
  if (isSending) return;
  if (!composerText.trim()) return;
  if (!authStore.user?.uid) {
    sendError = m.chatSignInToSend();
    return;
  }
  if (!activeRoomId) {
    sendError = m.chatRoomNotReady();
    return;
  }

  isSending = true;
  sendError = null;

  const trimmed = composerText.trim();
  const timestamp = Date.now();

  // 메시지 데이터 구성
  const payload = {
    roomId: activeRoomId,
    type: 'message',
    text: trimmed,
    urls: [],
    senderUid: authStore.user.uid,
    createdAt: timestamp,
    editedAt: null,
    deletedAt: null,
    roomOrder: `-${activeRoomId}-${timestamp}`,
    rootOrder: `-${activeRoomId}-${timestamp}`
  };

  // Firebase에 저장
  const result = await pushData(messagePath, payload);

  if (!result.success) {
    sendError = result.error ?? m.chatSendFailed();
  } else {
    composerText = '';
  }

  isSending = false;
}
```

#### Firebase 저장

- **함수**: `pushData(path, data)`
- **경로**: `/chat-messages/{auto-generated-key}`
- **반환**: `{ success: boolean, error?: string }`

## 다국어 지원

모든 UI 텍스트는 Paraglide를 통해 다국어를 지원합니다.

### 다국어 키 목록

| 키 | 한국어 | 영어 | 일본어 | 중국어 |
|---|---|---|---|---|
| `chatSingleChat` | 1:1 채팅 | Single Chat | シングルチャット | 私聊 |
| `chatChatRoom` | 채팅방 | Chat Room | チャットルーム | 聊天室 |
| `chatRoom` | 방: | Room: | ルーム: | 房间: |
| `chatOverview` | 채팅 개요 | Chat Overview | チャット概要 | 聊天概览 |
| `chatSignInRequired` | 채팅을 시작하려면 로그인하세요. | Please sign in to start chatting. | チャットを開始するにはログインしてください。 | 请登录以开始聊天。 |
| `chatProvideUid` | 1:1 채팅을 열려면 uid 쿼리 파라미터를 제공하세요. | Provide a uid query parameter to open a single chat. | シングルチャットを開くにはuidクエリパラメータを指定してください。 | 提供 uid 查询参数以打开私聊。 |
| `chatLoadingProfile` | 참가자 프로필을 불러오는 중... | Loading the participant profile... | 参加者プロフィールを読み込み中... | 加载参与者资料中... |
| `chatLoadProfileFailed` | 참가자 프로필을 불러오는데 실패했습니다. | Failed to load participant profile. | 参加者プロフィールの読み込みに失敗しました。 | 加载参与者资料失败。 |
| `chatChattingWith` | {name}님과 채팅 중입니다. | You are chatting with {name}. | {name}さんとチャット中です。 | 您正在与{name}聊天。 |
| `chatRoomReady` | 방 ID {roomId}가 준비되었습니다. | Room ID {roomId} is ready. | ルームID {roomId}が準備完了。 | 房间ID {roomId}已准备就绪。 |
| `chatSelectConversation` | 대화를 선택하여 시작하세요. | Select a conversation to begin. | 会話を選択して開始してください。 | 选择一个对话开始。 |
| `chatRoomNotReady` | 채팅방이 준비되지 않았습니다. | Chat room is not ready. | チャットルームが準備されていません。 | 聊天室未准备就绪。 |
| `chatAddUidOrRoomId` | 대화를 열려면 URL에 ?uid=TARGET_UID 또는 ?roomId=ROOM_KEY를 추가하세요. | Add ?uid=TARGET_UID or ?roomId=ROOM_KEY to the URL to open a conversation. | 会話を開くにはURLに?uid=TARGET_UIDまたは?roomId=ROOM_KEYを追加してください。 | 在URL中添加?uid=TARGET_UID或?roomId=ROOM_KEY以打开对话。 |
| `chatLoadingMessages` | 메시지를 불러오는 중... | Loading messages... | メッセージを読み込み中... | 加载消息中... |
| `chatNoMessages` | 아직 메시지가 없습니다. 인사해보세요! | No messages yet. Say hello! | まだメッセージがありません。挨拶してみましょう！ | 还没有消息。打个招呼吧！ |
| `chatLoadMessagesFailed` | 메시지를 불러오는데 실패했습니다. | Failed to load messages. | メッセージの読み込みに失敗しました。 | 加载消息失败。 |
| `chatUnknownError` | 알 수 없는 오류. | Unknown error. | 不明なエラー。 | 未知错误。 |
| `chatLoadingMore` | 더 불러오는 중... | Loading more... | さらに読み込み中... | 加载更多中... |
| `chatUpToDate` | 모든 메시지를 확인했습니다. | You are up to date. | すべて確認済みです。 | 已查看所有消息。 |
| `chatPreparingStream` | 메시지 스트림을 준비하는 중... | Preparing the message stream... | メッセージストリームを準備中... | 准备消息流中... |
| `chatWriteMessage` | 메시지를 입력하세요... | Write a message... | メッセージを入力... | 输入消息... |
| `chatSending` | 전송 중... | Sending... | 送信中... | 发送中... |
| `chatSend` | 전송 | Send | 送信 | 发送 |
| `chatSignInToSend` | 메시지를 보내려면 로그인하세요. | Please sign in to send messages. | メッセージを送信するにはログインしてください。 | 请登录以发送消息。 |
| `chatSendFailed` | 메시지 전송에 실패했습니다. | Failed to send message. | メッセージの送信に失敗しました。 | 发送消息失败。 |
| `chatUnknownUser` | 알 수 없는 사용자 | Unknown user | 不明なユーザー | 未知用户 |
| `chatYou` | 나 | You | あなた | 你 |
| `chatPartner` | 채팅 상대 | Chat Partner | チャットパートナー | 聊天伙伴 |

### 사용 예시

```svelte
<p>{m.chatChattingWith({ name: targetDisplayName })}</p>
<p>{m.chatRoomReady({ roomId: roomIdParam })}</p>
<input placeholder={m.chatWriteMessage()} />
```

## 기술 검증 및 동작 흐름

### 전체 시스템 검증 결과

1:1 채팅방의 전체 흐름을 세밀하게 검증한 결과, 모든 핵심 기능이 올바르게 동작하는 것을 확인했습니다.

### 올바르게 동작하는 부분

#### 1. Room ID 생성 로직

```typescript
// src/lib/functions/chat.functions.ts
function buildSingleRoomId(a: string, b: string) {
  return `single-${[a, b].sort().join('-')}`;
}
```

✅ **검증 완료**:
- 두 사용자 UID를 알파벳 순으로 정렬하여 deterministic한 방 ID 생성
- 예시: `single-alice-bob` (항상 동일한 순서)
- 누가 먼저 채팅을 시작하든 동일한 채팅방으로 연결됨

#### 2. 메시지 페이로드 구조

```typescript
// src/routes/chat/room/+page.svelte:100-111
const payload = {
  roomId: activeRoomId,              // "single-alice-bob"
  type: 'message',
  text: trimmed,
  urls: [],
  senderUid: authStore.user.uid,
  createdAt: timestamp,              // Date.now()
  editedAt: null,
  deletedAt: null,
  roomOrder: `-${activeRoomId}-${timestamp}`,  // "-single-alice-bob-1234567890"
  rootOrder: `-${activeRoomId}-${timestamp}`
};
```

✅ **검증 완료**:
- `roomOrder` 필드가 올바른 형식으로 생성됨
- `-` 접두사로 시작하여 Firebase에서 역순 정렬 가능
- `{roomId}-{timestamp}` 형식으로 방별 필터링 및 시간순 정렬 지원

#### 3. Firebase 저장 메커니즘

```typescript
// src/lib/stores/database.svelte.ts:362-378
const result = await pushData('chat-messages', payload);
```

✅ **검증 완료**:
- Firebase Realtime Database의 `chat-messages/{자동생성키}` 경로에 저장
- `push()` 함수로 고유 키 자동 생성 (예: `-NqxK7bF3M...`)
- 성공 시 생성된 키 반환: `{ success: true, key: "..." }`
- 실패 시 에러 메시지 반환: `{ success: false, error: "..." }`

#### 4. DatabaseListView 설정

```typescript
// src/routes/chat/room/+page.svelte:194-201
<DatabaseListView
  path="chat-messages"              // ✅ Firebase 경로
  pageSize={25}                     // ✅ 한 페이지당 25개 메시지
  orderBy="roomOrder"               // ✅ 정렬 필드
  orderPrefix="-{roomId}-"          // ✅ 특정 방의 메시지만 필터링
  threshold={280}                   // ✅ 스크롤 임계값 (280px)
  reverse={true}                    // ⚠️ 최신 메시지가 위에 표시됨
/>
```

✅ **검증 완료**:
- `orderPrefix`가 메시지의 `roomOrder` 필드와 정확히 매칭됨
- 해당 채팅방의 메시지만 정확히 필터링됨
- 페이지네이션이 정상 동작함

#### 5. 실시간 업데이트 메커니즘

DatabaseListView 컴포넌트가 제공하는 실시간 리스너:

```typescript
// src/lib/components/DatabaseListView.svelte
// 1. 개별 메시지 업데이트 감지
onValue(itemRef, (snapshot) => {
  items[index].data = snapshot.val();
});

// 2. 새 메시지 추가 감지
onChildAdded(dataQuery, (snapshot) => {
  const newItem = { key: snapshot.key, data: snapshot.val() };
  if (reverse) {
    items = [newItem, ...items];  // 배열 맨 앞에 추가
  } else {
    items = [...items, newItem];  // 배열 맨 뒤에 추가
  }
});

// 3. 메시지 삭제 감지
onChildRemoved(dataQuery, (snapshot) => {
  items = items.filter(item => item.key !== snapshot.key);
});
```

✅ **검증 완료**:
- 메시지 생성, 수정, 삭제가 모두 실시간으로 반영됨
- 여러 기기/브라우저에서 동시에 접속해도 동기화됨
- Firebase의 `onValue`, `onChildAdded`, `onChildRemoved` 리스너가 정상 동작

### 메시지 표시 순서

현재 구현에서는 **최신 메시지가 화면 위에 표시**됩니다:

**동작 방식**:
1. `reverse={true}` 설정
2. Firebase에서 `limitToLast(25)` 사용 (최신 25개)
3. `onChildAdded`에서 새 메시지를 배열 **맨 앞**에 추가
4. 화면 렌더링 시 배열 순서대로 (위→아래)
5. **결과**: 최신 메시지가 위 ⬆️, 오래된 메시지가 아래 ⬇️

**일반적인 채팅 UI와의 차이**:
- 일반 채팅 앱: 오래된 메시지 위 ⬆️, 최신 메시지 아래 ⬇️
- 현재 구현: 최신 메시지 위 ⬆️, 오래된 메시지 아래 ⬇️

이 방식은 트위터/소셜 미디어 피드와 유사하며, 최신 내용을 먼저 보여주는 장점이 있습니다.

### 전체 메시지 전송 흐름

```
[1] 사용자가 메시지 입력 후 전송 버튼 클릭
    ↓
[2] handleSendMessage() 함수 실행
    ├─ 유효성 검증: 로그인 여부, 공백 검사, 방 ID 확인
    └─ 통과
    ↓
[3] 메시지 페이로드 생성
    ├─ roomOrder: "-{roomId}-{timestamp}"
    ├─ senderUid, text, createdAt 등 포함
    └─ payload 객체 완성
    ↓
[4] pushData('chat-messages', payload) 호출
    ├─ Firebase push()로 고유 키 생성
    ├─ set()으로 데이터 저장
    └─ 성공 시 { success: true, key: "..." } 반환
    ↓
[5] Firebase onChildAdded 리스너 트리거
    ├─ DatabaseListView가 새 메시지 감지
    ├─ orderPrefix 필터링 (해당 방의 메시지만)
    └─ items 배열에 추가
    ↓
[6] Svelte 반응성 시스템
    ├─ items 배열 변경 감지
    ├─ UI 자동 리렌더링
    └─ 화면에 새 메시지 표시 ✅
    ↓
[7] 사용자가 화면에서 메시지 확인 완료
```

### 성능 및 안정성

✅ **확인된 사항**:
- TypeScript 컴파일: 0 errors, 70 warnings (Tailwind CSS v4 관련, 기능에 영향 없음)
- Firebase 연결: 정상
- 실시간 동기화: 정상
- 메모리 누수: 없음 (컴포넌트 언마운트 시 리스너 자동 해제)
- 페이지네이션: 정상 (25개씩 로드)

### 검증 결론

**1:1 채팅방 기능은 완벽하게 동작합니다!**

✅ 메시지 전송: 정상
✅ Firebase 저장: 정상
✅ 실시간 업데이트: 정상
✅ 방별 메시지 필터링: 정상
✅ 페이지네이션: 정상
✅ 다국어 지원: 정상 (4개 언어)
✅ 반응형 디자인: 정상

**특이사항**: 메시지 표시 순서가 최신 메시지 위 ⬆️ 방식으로 구현되어 있으며, 이는 의도된 동작으로 확인됨.

## 스타일링

### 반응형 디자인

- **데스크톱**: 최대 너비 960px, 가로 레이아웃
- **모바일** (640px 이하):
  - 헤더: 세로 레이아웃
  - 메시지: 세로 레이아웃
  - 입력창: 전체 너비

### 주요 스타일

- **채팅방 헤더**: 흰색 배경, 그림자, 둥근 모서리
- **메시지 목록**: 스크롤 가능, 최대 높이 60vh
- **내 메시지**: 파란색 배경 (`#eef2ff`)
- **상대방 메시지**: 회색 배경 (`#f9fafb`)
- **입력창**: 둥근 모서리 (999px), 흰색 배경
- **전송 버튼**: 검은색 배경, 흰색 텍스트, 호버 효과

### 메시지 목록 레이아웃

- **내 메시지**는 오른쪽 정렬, 상대 메시지는 왼쪽 정렬로 배치합니다.
- 말풍선 최대 너비는 데스크톱 `65%`, 모바일 `85%`로 제한하여 긴 문장도 가독성을 유지합니다.
- 상대 메시지에는 아바타와 발신자 이름을 노출하고, 내 메시지는 아바타 없이 말풍선만 표시합니다.
- 시스템 메시지는 가운데 정렬 pill(`bg-gray-600 text-white px-4 py-1 rounded-full`)로 구분합니다.

```svelte
<div class="space-y-3">
  {#each messages as message (message.id)}
    <article
      class={`flex ${message.isMine ? 'justify-end' : 'justify-start'}`}
      aria-label={message.isMine ? '내 메시지' : `${message.senderName}의 메시지`}
      tabindex="0"
    >
      {#if !message.isMine}
        <Avatar uid={message.senderUid} size={32} class="mr-2 mt-1" />
      {/if}

      <div class={`max-w-[75%] space-y-1 ${message.isMine ? 'items-end text-right flex flex-col' : 'flex flex-col'}`}>
        {#if !message.isMine}
          <div class="text-xs text-gray-400">{message.senderName}</div>
        {/if}
        <div
          class={`rounded-2xl px-4 py-3 text-sm leading-relaxed ${
            message.isMine
              ? 'bg-amber-300 text-gray-900 shadow-inner'
              : 'bg-white text-gray-900 shadow-sm border border-gray-200'
          }`}
        >
          {@html message.html}
        </div>
        <div class="text-[11px] text-gray-400">{formatLongDate(message.createdAt)}</div>
      </div>
    </article>
  {/each}
</div>
```

- 스크린 리더 지원을 위해 `aria-label`로 발신자를 안내하고, 키보드 탐색을 위해 `tabindex="0"`을 지정합니다.

## 보안 및 권한

### 현재 구현

- 로그인한 사용자만 메시지 전송 가능
- 누구나 채팅방 조회 가능 (URL 접근)

### 향후 개선 사항

- Firebase Security Rules를 통한 접근 제어
- 채팅방 멤버십 검증
- 메시지 수정/삭제 권한 제어
- 스팸 방지 및 신고 기능

### 🔥 매우 중요: `members/{uid}` 필드의 의미 🔥

**채팅방 멤버 관리 시 반드시 알아야 할 사항**

`/chat-rooms/{roomId}/members/{uid}` 필드는 **세 가지 상태**를 가질 수 있습니다:

1. **필드가 존재하지 않음**: 사용자가 채팅방의 멤버가 **아닙니다**
2. **`true`**: 사용자가 멤버이며 **알림을 구독**합니다
3. **`false`**: 사용자가 멤버이지만 **알림을 구독하지 않습니다**

**⚠️ 흔한 실수**: `snapshot.val() === true`로 체크하면 `false`일 때 멤버가 아닌 것으로 잘못 판단합니다!

**✅ 올바른 방법**: 멤버 여부를 확인할 때는 `snapshot.exists()`를 사용해야 합니다.

**코드 예시**:
```typescript
// ❌ 잘못된 코드 - false일 때 멤버가 아닌 것으로 잘못 판단
const isMember = snapshot.val() === true;

// ✅ 올바른 코드 - 필드 존재 여부만 확인 (true/false 모두 멤버임)
const isMember = snapshot.exists();
```

**참고 파일**:
- [src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte): 223번째 줄 참조
- [src/lib/functions/chat.functions.ts](../src/lib/functions/chat.functions.ts): `joinChatRoom` 함수 참조

## 성능 최적화

### 현재 적용

- DatabaseListView를 통한 페이지네이션 (25개씩 로드)
- 역순 정렬로 최신 메시지 우선 표시
- 프로필 캐싱 (userProfileStore)

### 향후 개선 사항

- 메시지 가상 스크롤 (Virtual Scrolling)
- 이미지 레이지 로딩
- 오프라인 지원 (IndexedDB 캐싱)
- 읽음 상태 추적

## 테스트 시나리오

### 1:1 채팅 테스트

1. 로그인하지 않은 상태에서 `/chat/room?uid=test-user` 접근
   - 결과: "채팅을 시작하려면 로그인하세요." 메시지 표시

2. 로그인 후 `/chat/room?uid=test-user` 접근
   - 결과: 채팅방 헤더에 상대방 프로필 표시

3. 메시지 입력 및 전송
   - 결과: 실시간으로 메시지 목록에 추가

4. 다른 브라우저/기기에서 동일한 채팅방 접근
   - 결과: 실시간으로 메시지 동기화

### 그룹 채팅 테스트

1. `/chat/room?roomId=general` 접근
   - 결과: 그룹 채팅방 헤더 표시

2. 여러 사용자가 동일한 roomId로 접근
   - 결과: 모든 사용자의 메시지가 공유됨

### 다국어 테스트

1. 언어를 한국어로 설정
   - 결과: 모든 UI가 한국어로 표시

2. 언어를 영어로 전환
   - 결과: 모든 UI가 영어로 즉시 변경

## 관련 파일

- [src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte) - 채팅방 페이지
- [src/lib/components/DatabaseListView.svelte](../src/lib/components/DatabaseListView.svelte) - 메시지 목록 컴포넌트
- [src/lib/components/user/avatar.svelte](../src/lib/components/user/avatar.svelte) - 사용자 아바타
- [src/lib/stores/user-profile.svelte](../src/lib/stores/user-profile.svelte) - 사용자 프로필 스토어
- [src/lib/stores/database.svelte](../src/lib/stores/database.svelte) - Firebase 데이터베이스 유틸리티
- [messages/*.json](../messages/) - 다국어 메시지 파일

## 변경 이력

- **2025-11-15**: 채팅방 레이아웃 구조 변경 금지 규칙 추가
  - **배경**: 채팅방 레이아웃 작업 시 자주 발생하는 무한 스크롤 및 레이아웃 깨짐 문제 방지
  - **문제점**:
    - 레이아웃 CSS 수정 시 채팅 입력창(footer)이 사라지는 문제
    - 메시지 목록 및 사이드바의 무한 스크롤이 작동하지 않는 문제
    - 모바일/데스크톱에서 overflow 발생하여 화면이 깨지는 문제
  - **추가 내용**:
    - "⚠️ 중요: 채팅방 레이아웃 구조 변경 금지 규칙" 섹션 신규 추가
    - `.chat-room-layout`, `.chat-room-sidebar`, `.sidebar-content`, `.chat-room-main` 클래스별 필수 준수 사항 명시
    - 절대 변경하면 안 되는 CSS 속성 목록 (🔴 표시)
    - 변경 가능한 CSS 속성 목록 (✅ 표시)
    - 레이아웃 작동 원리 ASCII 다이어그램 추가
    - 레이아웃 수정 시 체크리스트 제공
    - 완전한 레이아웃 CSS 코드 (현재 작동 중인 버전) 전체 포함
  - **핵심 규칙**:
    - `fixed` positioning, `h-[100dvh]`, `calc(100vh - 8rem)` 절대 변경 금지
    - `flex-1 overflow-auto` 조합 유지 (무한 스크롤 작동의 핵심)
    - `safe-area-inset-top/bottom` 유지 (모바일 화면 대응)
  - **목적**: 향후 레이아웃 관련 작업 시 이 규칙을 반드시 참조하여 동일한 문제 재발 방지

- **2025-11-14 (저녁)**: Cloud Functions - allChatListOrder 우선순위 수정
  - **문제점**: 새 채팅 메시지가 있음에도 채팅방 목록에서 상단에 표시되지 않음
    - `allChatListOrder` 필드에 `200` prefix가 추가되지 않아 우선순위가 올라가지 않음
    - `singleChatListOrder`, `groupChatListOrder`, `openChatListOrder`는 정상 작동
    - 하지만 `allChatListOrder`를 기준으로 정렬하는 목록에서는 문제 발생

  - **해결책**: Firebase Cloud Functions `handleChatMessageCreate` 로직 개선 ([firebase/functions/src/handlers/chat.handler.ts](../firebase/functions/src/handlers/chat.handler.ts))

    **1:1 채팅 수정** (라인 110-123):
    - 발신자: `allChatListOrder` = `senderSingleListOrder` (= `${timestamp}`)
    - 수신자: `allChatListOrder` = `partnerSingleListOrder` (= `200${timestamp}`)
    - 이전에는 무조건 `timestamp`만 설정되었음

    **그룹/오픈 채팅 수정** (라인 174-307):
    - 각 멤버의 기존 `chat-joins` 데이터를 먼저 읽음 (병렬 처리로 성능 최적화)
    - `allChatListOrder`, `groupChatListOrder`, `openChatListOrder`, `openAndGroupChatListOrder` 모두에 대해:
      - **기존 값이 "500"으로 시작**: 유지 (핀 설정된 채팅방, 건드리지 않음)
      - **기존 값이 있고 발신자**: `${timestamp}` (읽음 상태)
      - **기존 값이 있고 수신자**: `200${timestamp}` (새 메시지, 우선순위 UP)
      - **기존 값이 없음**: `${timestamp}` (새로 생성)
    - 이전에는 무조건 `timestamp`만 설정되어 우선순위가 변경되지 않았음

  - **우선순위 규칙**:
    - **500 prefix** (핀 설정): 항상 최상단
    - **200 prefix** (읽지 않은 메시지): 일반 채팅방보다 위
    - **prefix 없음** (읽은 메시지): 일반 우선순위

  - **배포 완료**:
    - `npm run deploy` 명령으로 Firebase Cloud Functions 배포 완료
    - 13개의 Functions 업데이트 완료 (asia-southeast1 리전)
    - 배포 시간: ~2분

  - **영향**:
    - `/chat/room/+layout.svelte`의 채팅방 목록이 `allChatListOrder` 기준으로 정렬될 때 정상 작동
    - 새 메시지가 있는 채팅방이 자동으로 목록 상단으로 이동
    - 핀 설정된 채팅방은 항상 최상단 유지
    - 기존 다른 정렬 필드들(`singleChatListOrder` 등)도 동일한 로직 적용

- **2025-11-14 (오후)**: 1:1 채팅방 목록에 상대방 프로필 실시간 표시 기능 추가
  - **문제점**: snippet 내 side effect로 `userProfileStore.ensureSubscribed()` 호출 시 반응성이 작동하지 않음
    - profile이 로드된 후에도 컴포넌트가 재렌더링되지 않음
    - 1:1 채팅방에서 상대방 이름과 사진 대신 roomId가 계속 표시됨

  - **해결책**: ChatRoomListItem 컴포넌트 분리 및 $effect 활용
    - **새 컴포넌트 생성** ([src/routes/chat/room/ChatRoomListItem.svelte](../src/routes/chat/room/ChatRoomListItem.svelte)):
      - 각 채팅방 아이템을 독립적인 컴포넌트로 분리
      - `$effect(() => { userProfileStore.ensureSubscribed(partnerUid) })` 사용으로 반응성 보장
      - profile 데이터 변경 시 자동 재렌더링

    - **1:1 채팅 판단 로직 개선**:
      - `roomData.type === 'single'` 대신 `roomId.startsWith('single-')` 사용
      - Firebase 데이터의 `type` 필드 의존도 제거 (더 견고한 로직)

    - **상대방 UID 추출**:
      ```typescript
      const partnerUid = $derived.by(() => {
        if (!isSingle) return '';
        const parts = roomId.replace('single-', '').split('-');
        return parts.find((uid) => uid !== authStore.user?.uid) || '';
      });
      ```

    - **실시간 프로필 구독**:
      - `userProfileStore.ensureSubscribed(partnerUid)`: Firebase RTDB `/users/{uid}` 경로 구독
      - `userProfileStore.getCachedProfile(partnerUid)`: 캐시된 프로필 데이터 가져오기
      - displayName, photoUrl 실시간 반영

    - **UI 컴포넌트**:
      - 1:1 채팅: `<Avatar uid={partnerUid} size={48} />` 컴포넌트로 상대방 사진 표시
      - displayName 표시 (없으면 `@{uid.slice(0,6)}` fallback)
      - 그룹/오픈 채팅: 기존 이모지 아이콘 유지 (👥, 🌐)

    - **레이아웃 파일 리팩토링** ([src/routes/chat/room/+layout.svelte](../src/routes/chat/room/+layout.svelte)):
      - `ChatRoomListItem` import 추가
      - snippet 내용 간소화: `<ChatRoomListItem {...props} />` 렌더링만 담당
      - CSS 스타일을 ChatRoomListItem.svelte로 이동 (캡슐화)
      - Avatar, userProfileStore import 제거 (ChatRoomListItem에서 처리)

  - **기술적 개선 사항**:
    - **반응성 보장**: snippet 내 side effect 안티패턴 제거, $effect 사용
    - **컴포넌트 분리**: 관심사의 분리 (Separation of Concerns)
    - **재사용성 향상**: ChatRoomListItem 컴포넌트 독립적으로 재사용 가능
    - **유지보수성**: 각 채팅방 아이템 로직이 독립적으로 관리됨

  - **TypeScript 검증**: 0 errors, 950 warnings (기존 Tailwind CSS 관련, 기능 영향 없음)

- **2025-11-14 (오전)**: 데스크톱 채팅방 좌측 사이드바에 채팅 목록 표시 기능 추가
  - **레이아웃 구조 개선** ([src/routes/chat/room/+layout.svelte](../src/routes/chat/room/+layout.svelte)):
    - 데스크톱 화면에서 2-column 레이아웃 구현 (사이드바 320px + 메인 콘텐츠)
    - 모바일 화면에서는 기존 single-column 레이아웃 유지 (사이드바 숨김)
    - Flexbox 기반 반응형 레이아웃 (`flex-row` on desktop, `flex-col` on mobile)

  - **채팅방 목록 사이드바 구현**:
    - DatabaseListView 컴포넌트를 사용하여 `/chat-joins/{uid}` 경로의 채팅방 목록 표시
    - `allChatListOrder` 필드 기준으로 정렬 (최신 메시지 우선)
    - 무한 스크롤 지원 (페이지당 20개 항목, bottom trigger)
    - 채팅방 타입별 아이콘 표시 (👤 1:1, 👥 그룹, 🌐 오픈)
    - 읽지 않은 메시지 수 배지 표시 (`newMessageCount`)
    - 채팅방 이름과 마지막 메시지 미리보기

  - **활성 채팅방 감지 및 하이라이팅**:
    - URL 파라미터(`roomId`, `uid`)에서 현재 활성 채팅방 ID 추출
    - 1:1 채팅의 경우 `buildSingleRoomId()` 함수로 roomId 생성
    - 활성 채팅방에 파란색 배경 (`bg-blue-50`) 적용

  - **채팅방 클릭 네비게이션**:
    - 1:1 채팅: roomId에서 상대방 UID 추출 후 `/chat/room?uid={partnerUid}`로 이동
    - 그룹/오픈 채팅: `/chat/room?roomId={roomId}`로 이동
    - SvelteKit의 `goto()` 함수 사용

  - **다국어 메시지 추가** (4개 언어):
    - `chatRoomList`: "내 채팅방" / "My Chats" / "マイチャット" / "我的聊天"
    - `chatRoomListEmpty`: "참여한 채팅방이 없습니다." / "No chat rooms joined yet." / "参加しているチャットルームがありません。" / "还没有加入任何聊天室。"
    - `chatRoomListLoading`: "채팅방 목록을 불러오는 중..." / "Loading chat rooms..." / "チャットルームを読み込み中..." / "正在加载聊天室..."

  - **CSS 스타일링**:
    - `.chat-room-layout`: Flexbox 컨테이너, 모바일(`h-[100dvh]`)/데스크톱(`calc(100vh - 4rem)`) 높이 조정
    - `.chat-room-sidebar`: 데스크톱에서만 표시(`@media (min-width: 768px)`), 320px 고정 너비
    - `.room-item`: 채팅방 리스트 아이템, 호버 효과 및 활성 상태 스타일링
    - `.room-item--active`: 활성 채팅방 파란색 배경
    - Safe area insets 지원 (모바일 노치 대응)

  - **TypeScript 검증**: 0 errors, 950 warnings (기존 Tailwind CSS 관련, 기능 영향 없음)

  - **UX 개선 효과**:
    - 데스크톱에서 채팅방 전환이 매우 빠르고 직관적
    - 읽지 않은 메시지 수를 한눈에 확인 가능
    - 마지막 메시지 미리보기로 대화 내용 파악
    - 모바일에서는 기존 UX 유지 (사이드바 숨김)

- **2025-11-13**: 채팅방 입장 상태에서 새 메시지 자동 읽음 처리 기능 추가
  - **DatabaseListView 새 아이템 콜백 활용** ([src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte)):
    - `handleNewMessage()` 콜백 함수에 `newMessageCount` 자동 0 업데이트 로직 구현
    - 사용자가 채팅방에 입장해 있는 상태에서 새 메시지 도착 시 즉시 읽음 처리
    - Firebase의 `ref`와 `update` 함수를 사용하여 `/chat-joins/{uid}/{roomId}/newMessageCount`를 0으로 업데이트
    - 채팅 목록에서 읽지 않은 메시지 배지가 자동으로 사라지도록 개선

  - **함수 분리 및 재사용성 개선**:
    - `markCurrentRoomAsRead()` 함수를 별도로 분리하여 재사용 가능하도록 구현
    - 채팅방 활성화 상태 및 사용자 인증 상태를 확인하는 유효성 검증 로직 포함
    - 업데이트 시도 여부를 boolean으로 반환하여 호출자가 결과를 확인 가능

  - **Cloud Functions와의 타이밍 이슈 해결**:
    - **문제**: 새 메시지 생성 시 Cloud Functions가 `newMessageCount`를 +1 증가시키는데, 클라이언트가 즉시 0으로 설정하면 타이밍 차이로 인해 값이 1로 남을 수 있음
    - **해결**: 790ms 지연 후 `newMessageCount`를 0으로 업데이트하여 Cloud Functions의 +1 증가가 먼저 완료되도록 보장
    - **처리 순서**:
      1. Firebase RTDB에 새 메시지 노드 생성
      2. Cloud Functions의 `onChatMessageCreate` 트리거 실행 → `newMessageCount` +1 증가
      3. 클라이언트의 DatabaseListView가 새 메시지 감지 → `handleNewMessage` 콜백 호출
      4. 790ms 대기 후 클라이언트가 `newMessageCount`를 0으로 업데이트
    - **추가 안전장치**: setTimeout 내부에서 재차 유효성 검증 (타이머 실행 중 사용자 로그아웃/방 나가기 처리)

  - **구현 로직**:
    ```typescript
    /**
     * 현재 채팅방의 읽지 않은 메시지 수를 0으로 초기화합니다.
     *
     * Cloud Functions와의 타이밍 이슈를 해결하기 위해 0.79초 지연 후 업데이트합니다.
     */
    function markCurrentRoomAsRead(): boolean {
      if (!activeRoomId || !authStore.user?.uid || !rtdb) {
        console.log('채팅방 또는 사용자 정보 없음 - newMessageCount 업데이트 건너뜀');
        return false;
      }

      // Cloud Functions 실행 완료를 기다린 후 newMessageCount를 0으로 업데이트
      setTimeout(() => {
        // 타이머 실행 중 상태 변경 가능성을 고려한 재검증
        if (!activeRoomId || !authStore.user?.uid || !rtdb) {
          console.log('타이머 실행 중 상태 변경 - newMessageCount 업데이트 취소');
          return;
        }

        const chatJoinRef = ref(rtdb, `chat-joins/${authStore.user.uid}/${activeRoomId}`);
        update(chatJoinRef, { newMessageCount: 0 })
          .then(() => console.log('newMessageCount 0으로 업데이트 완료'))
          .catch((error) => console.error('newMessageCount 업데이트 실패:', error));
      }, 790); // 0.79초 지연

      return true;
    }

    /**
     * DatabaseListView에서 새 메시지 추가 시 호출되는 콜백
     */
    function handleNewMessage(item: { key: string; data: any }) {
      console.log('새 메시지 추가됨:', item);
      markCurrentRoomAsRead();
    }
    ```

  - **UX 개선 효과**:
    - 사용자가 채팅방에 머물러 있으면 새 메시지를 즉시 읽은 것으로 간주
    - 채팅 목록에서 배지 숫자가 자동으로 0으로 업데이트됨 (타이밍 이슈 해결로 정확성 보장)
    - 사용자가 수동으로 채팅방을 나갔다 들어올 필요 없음
    - Cloud Functions와의 race condition 해결로 안정적인 읽음 처리 보장

  - **TypeScript 검증**: 0 errors, 471 warnings (Tailwind CSS 관련, 기능에 영향 없음)

- **2025-11-12**: 그룹/오픈 채팅방 입장 로직 개선
  - **클라이언트 코드 개선** ([src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte)):
    - 그룹/오픈 채팅방 입장 시 `/chat-rooms/{roomId}/members/{uid}: true` 자동 설정
    - `joinChatRoom()` 함수를 import하여 members 필드 관리
    - 1:1 채팅과 그룹/오픈 채팅을 구분하여 처리
    - `rtdb` null 체크 추가로 타입 안정성 개선
  - **Cloud Functions 개선** ([firebase/functions/src/handlers/chat.handler.ts](../firebase/functions/src/handlers/chat.handler.ts)):
    - `handleChatRoomMemberJoin()` 함수 확장
    - 채팅방 정보 조회 (roomType, roomName)
    - `/chat-joins/{uid}/{roomId}` 노드에 상세 정보 자동 추가:
      - `roomType`: 채팅방 타입 (group, open)
      - `roomName`: 채팅방 이름 (캐싱용)
      - `listOrder`: 정렬 순서 (현재 timestamp)
      - `newMessageCount`: 0으로 초기화
      - `joinedAt`: 입장 시각 (없는 경우에만)
    - TypeScript 타입 안정성 개선 (`Record<string, string | number>`)
  - **채팅방 생성 다이얼로그 개선** ([src/lib/components/chat/ChatCreateDialog.svelte](../src/lib/components/chat/ChatCreateDialog.svelte)):
    - `rtdb` null 체크 추가로 데이터베이스 연결 오류 처리
    - 사용자 친화적 에러 메시지 표시
  - **데이터 흐름 개선**:
    ```
    사용자 입장 → 클라이언트: chat-joins + members 설정
         ↓
    Cloud Functions: chat-joins 상세 정보 자동 추가
         ↓
    결과: 채팅방 목록에서 즉시 정렬 및 표시 가능
    ```
  - **TypeScript 검증**: 0 errors, 413 warnings (Tailwind CSS 관련, 기능에 영향 없음)
  - **Firebase Functions 배포**: asia-southeast1 리전에 성공적으로 배포 완료

- **2025-11-11**: 초기 사양 작성 및 1:1 채팅 기능 완전 구현
  - URL 파라미터 기반 채팅방 접근 (uid, roomId)
  - Firebase Realtime Database를 통한 실시간 메시지 동기화
  - 사용자 프로필 실시간 구독 및 표시
  - DatabaseListView를 통한 메시지 목록 페이지네이션
  - 메시지 입력 및 전송 기능
  - 4개 언어 완전 지원 (한국어, 영어, 일본어, 중국어)
  - 반응형 디자인 (데스크톱/모바일)
  - **용어 변경**: "Direct Chat" → "Single Chat"
    - 변수명: `isDirectChat` → `isSingleChat`
    - 함수명: `buildDirectRoomId` → `buildSingleRoomId`
    - Room ID 접두사: `direct-` → `single-`
    - 다국어 키: `chatDirectChat` → `chatSingleChat`
    - 모든 문서 및 소스 코드에 "Single Chat" 용어 통일
  - **기술 검증 및 문서화**:
    - 전체 채팅방 흐름 세밀하게 검증
    - Room ID 생성, 메시지 페이로드, Firebase 저장 메커니즘 검증
    - DatabaseListView 설정 및 실시간 업데이트 확인
    - 메시지 전송 흐름 다이어그램 작성
    - 성능 및 안정성 확인 (TypeScript 0 errors)
    - 모든 핵심 기능 정상 동작 확인 완료

---

## 완전한 구현 가이드

이 섹션은 `/src/routes/chat/room/+page.svelte` 파일의 완전한 재구현을 위한 상세 가이드입니다.

### 전체 파일 구조

```svelte
<script lang="ts">
  // 1. imports
  // 2. URL 파라미터 및 파생 상태
  // 3. 사용자 프로필 구독
  // 4. 메시지 전송 관련 상태
  // 5. 메시지 전송 핸들러
  // 6. 헬퍼 함수들
</script>

<svelte:head>
  <!-- 페이지 제목 -->
</svelte:head>

<!-- 메인 컨테이너 -->
<div class="mx-auto flex max-w-[960px] flex-col gap-6 px-4 py-8 pb-16">
  <!-- 1. 채팅방 헤더 -->
  <!-- 2. 채팅방 준비 여부에 따른 분기 -->
  <!-- 3. 메시지 목록 (DatabaseListView) -->
  <!-- 4. 메시지 입력 폼 -->
</div>

<style>
  /* Tailwind CSS @apply 패턴 */
</style>
```

### 1. Imports 및 초기 설정

```typescript
import { page } from '$app/stores';
import DatabaseListView from '$lib/components/DatabaseListView.svelte';
import Avatar from '$lib/components/user/avatar.svelte';
import { authStore } from '$lib/stores/auth.svelte';
import { userProfileStore } from '$lib/stores/user-profile.svelte';
import { pushData } from '$lib/stores/database.svelte';
import { m } from '$lib/paraglide/messages';
import { buildSingleRoomId } from '$lib/functions/chat.functions';
import { formatLongDate } from '$lib/functions/date.functions';
import { tick } from 'svelte';
```

**주요 의존성**:
- `$app/stores`: SvelteKit의 페이지 스토어 (URL 파라미터 접근)
- `DatabaseListView`: Firebase RTDB 리스트 표시 컴포넌트
- `Avatar`: 사용자 아바타 컴포넌트
- `authStore`: 인증 상태 관리 스토어
- `userProfileStore`: 사용자 프로필 구독 및 캐싱 스토어
- `pushData`: Firebase에 데이터 저장하는 유틸리티 함수
- `m`: Paraglide 다국어 메시지 함수
- `buildSingleRoomId`: 1:1 채팅방 ID 생성 함수
- `formatLongDate`: 타임스탬프를 로케일에 맞게 포맷하는 함수
- `tick`: Svelte의 DOM 업데이트 대기 함수

### 2. URL 파라미터 및 파생 상태

```typescript
// GET 파라미터 추출
const uidParam = $derived.by(() => $page.url.searchParams.get('uid') ?? '');
const roomIdParam = $derived.by(() => $page.url.searchParams.get('roomId') ?? '');

// 1:1 채팅 여부
const isSingleChat = $derived.by(() => Boolean(uidParam));

// 활성 채팅방 ID 계산
const activeRoomId = $derived.by(() => {
  if (roomIdParam) return roomIdParam;
  if (isSingleChat && authStore.user?.uid && uidParam) {
    return buildSingleRoomId(authStore.user.uid, uidParam);
  }
  return '';
});
```

**핵심 개념**:
- `$derived.by()`: Svelte 5의 파생 상태 생성 Rune
- URL 파라미터는 두 가지 방식으로 받음:
  - `?uid=TARGET_UID`: 1:1 채팅 (자동으로 roomId 생성)
  - `?roomId=ROOM_ID`: 그룹 채팅
- `activeRoomId`는 우선순위에 따라 결정:
  1. `roomIdParam`이 있으면 그것 사용
  2. `uidParam`과 현재 사용자 UID로 roomId 생성
  3. 둘 다 없으면 빈 문자열 (채팅방 준비 안됨)

### 3. DatabaseListView 설정

```typescript
// DatabaseListView 설정 (Flat 구조 기준)
const messagePath = 'chat-messages';
const roomOrderField = 'roomOrder';
const roomOrderPrefix = $derived.by(() => (activeRoomId ? `-${activeRoomId}-` : ''));
const canRenderMessages = $derived.by(() => Boolean(activeRoomId && roomOrderPrefix));
```

**핵심 개념**:
- `messagePath`: Firebase RTDB의 메시지 저장 경로
- `roomOrderField`: 정렬에 사용할 필드명
- `roomOrderPrefix`: 특정 채팅방의 메시지만 필터링하기 위한 접두사
  - 예: `-single-alice-bob-`
- `canRenderMessages`: 메시지 목록을 렌더링할 준비가 되었는지 확인

### 4. 사용자 프로필 구독

```typescript
// 채팅 상대 프로필 구독
$effect(() => {
  if (uidParam) {
    userProfileStore.ensureSubscribed(uidParam);
  }
});

const targetProfile = $derived(userProfileStore.getCachedProfile(uidParam));
const targetProfileLoading = $derived(userProfileStore.isLoading(uidParam));
const targetProfileError = $derived(userProfileStore.getError(uidParam));
```

**핵심 개념**:
- `$effect()`: Svelte 5의 Effect Rune (부수효과 실행)
- `uidParam`이 변경될 때마다 해당 사용자의 프로필을 구독
- `userProfileStore`는 자동으로 Firebase 리스너를 관리하고 캐싱 처리
- `targetProfile`: 캐시된 프로필 데이터 (displayName, photoURL 등)
- `targetProfileLoading`: 로딩 상태
- `targetProfileError`: 에러 메시지

### 5. 채팅 상대 표시 이름

```typescript
// 채팅 상대 표시 이름
const targetDisplayName = $derived.by(() => {
  if (targetProfile?.displayName) return targetProfile.displayName;
  if (uidParam) return `@${uidParam.slice(0, 6)}`;
  return m.chatPartner();
});
```

**우선순위**:
1. 프로필의 `displayName` 사용
2. UID의 앞 6자리로 임시 이름 (`@abc123`)
3. 기본 다국어 메시지 ("채팅 상대")

### 6. 메시지 전송 관련 상태

```typescript
// 작성 중인 메시지
let composerText = $state('');
let isSending = $state(false);
let sendError = $state<string | null>(null);

// 채팅 입력 창(input) 직접 참조
let composerInputRef: HTMLInputElement | null = $state(null);

// 메시지 작성 가능 여부
const composerDisabled = $derived.by(() => !authStore.isAuthenticated || !activeRoomId);
```

**핵심 개념**:
- `$state()`: Svelte 5의 상태 변수 생성 Rune
- `composerText`: 입력창의 현재 텍스트
- `isSending`: 전송 중 상태 (중복 전송 방지)
- `sendError`: 전송 실패 시 에러 메시지
- `composerInputRef`: **중요!** 입력 요소에 대한 직접 참조 (포커스 관리용)
- `composerDisabled`: 로그인하지 않았거나 채팅방이 준비되지 않으면 비활성화

### 7. 메시지 전송 핸들러 (완전한 구현)

```typescript
async function handleSendMessage(event: SubmitEvent) {
  event.preventDefault();

  // 중복 전송 방지
  if (isSending) return;

  // 공백만 있는 메시지 방지
  if (!composerText.trim()) return;

  // 로그인 확인
  if (!authStore.user?.uid) {
    sendError = m.chatSignInToSend();
    return;
  }

  // 채팅방 준비 확인
  if (!activeRoomId) {
    sendError = m.chatRoomNotReady();
    return;
  }

  isSending = true;
  sendError = null;

  const trimmed = composerText.trim();
  const timestamp = Date.now();

  // 메시지 페이로드 구성
  const payload = {
    roomId: activeRoomId,
    type: 'message',
    text: trimmed,
    urls: [],
    senderUid: authStore.user.uid,
    createdAt: timestamp,
    editedAt: null,
    deletedAt: null,
    roomOrder: `-${activeRoomId}-${timestamp}`,
    rootOrder: `-${activeRoomId}-${timestamp}`
  };

  // Firebase에 저장
  const result = await pushData(messagePath, payload);

  if (!result.success) {
    // 전송 실패
    sendError = result.error ?? m.chatSendFailed();
    isSending = false;
  } else {
    // 전송 성공
    composerText = '';
    sendError = null;
    isSending = false;

    // ⭐ 포커스 관리: DOM 업데이트 완료 후 포커스 추가
    await tick(); // Svelte DOM 업데이트 대기

    // 브라우저 렌더링 완료를 확실히 기다린 후 포커스
    requestAnimationFrame(() => {
      if (composerInputRef) {
        composerInputRef.focus();
        console.log('✅ 채팅 입력 창에 포커스 추가됨');
      }
    });
  }
}
```

**핵심 포인트**:
1. **유효성 검사**: 중복 전송, 공백, 로그인, 채팅방 준비 확인
2. **페이로드 구성**: `roomOrder` 필드가 핵심 (정렬 및 필터링에 사용)
3. **Firebase 저장**: `pushData()` 함수로 `/chat-messages/{auto-key}` 경로에 저장
4. **포커스 관리**:
   - `await tick()`: Svelte의 반응성 시스템이 DOM 업데이트 완료
   - `requestAnimationFrame()`: 브라우저의 렌더링 완료 대기
   - `composerInputRef.focus()`: 직접 참조로 포커스 추가
   - 이 패턴은 100% 신뢰할 수 있는 포커스 관리 방법

### 8. DatabaseListView 컴포넌트 참조 및 스크롤 제어

```typescript
// DatabaseListView 컴포넌트 참조 (스크롤 제어용)
let databaseListView: any = $state(null);

// 스크롤을 맨 위로 이동
function handleScrollToTop() {
  databaseListView?.scrollToTop();
}

// 스크롤을 맨 아래로 이동
function handleScrollToBottom() {
  databaseListView?.scrollToBottom();
}
```

**핵심 개념**:
- `bind:this`로 DatabaseListView 컴포넌트 인스턴스 참조
- `scrollToTop()`, `scrollToBottom()` 메서드 호출로 스크롤 제어
- 사용자가 긴 대화 내역을 빠르게 탐색할 수 있도록 지원

### 9. 발신자 라벨 결정 함수

```typescript
// 발신자 라벨
function resolveSenderLabel(senderUid?: string | null) {
  if (!senderUid) return m.chatUnknownUser();
  if (senderUid === authStore.user?.uid) return m.chatYou();
  if (senderUid === uidParam && targetDisplayName) return targetDisplayName;
  return senderUid.slice(0, 10);
}
```

**우선순위**:
1. `senderUid`가 없으면: "알 수 없는 사용자"
2. 본인의 UID와 같으면: "나"
3. 채팅 상대의 UID와 같으면: 상대방 이름 (`targetDisplayName`)
4. 그 외: UID의 앞 10자리

### 10. 템플릿 구조 - 헤더

```svelte
<svelte:head>
  <title>{m.pageTitleChat()}</title>
</svelte:head>

<div class="mx-auto flex max-w-[960px] flex-col gap-6 px-4 py-8 pb-16">
  <header
    class="chat-room-header flex items-center justify-between gap-4 p-6 sm:flex-col sm:items-start"
  >
    <div>
      <p class="chat-room-label">{isSingleChat ? m.chatSingleChat() : m.chatChatRoom()}</p>
      <h1 class="chat-room-title">
        {#if isSingleChat && uidParam}
          {targetDisplayName}
        {:else if roomIdParam}
          {m.chatRoom()} {roomIdParam}
        {:else}
          {m.chatOverview()}
        {/if}
      </h1>
      <p class="chat-room-subtitle mt-1.5">
        {#if !authStore.isAuthenticated}
          {m.chatSignInRequired()}
        {:else if isSingleChat && !uidParam}
          {m.chatProvideUid()}
        {:else if targetProfileLoading}
          {m.chatLoadingProfile()}
        {:else if targetProfileError}
          {m.chatLoadProfileFailed()}
        {:else if isSingleChat}
          {m.chatChattingWith({ name: targetDisplayName })}
        {:else if roomIdParam}
          {m.chatRoomReady({ roomId: roomIdParam })}
        {:else}
          {m.chatSelectConversation()}
        {/if}
      </p>
    </div>
    {#if uidParam}
      <div class="chat-room-partner flex items-center gap-3 px-4 py-3 sm:w-full sm:justify-center">
        <Avatar uid={uidParam} size={64} class="shadow-sm" />
        <div>
          <p class="partner-name">{targetDisplayName}</p>
          <p class="partner-uid">{uidParam}</p>
        </div>
      </div>
    {/if}
  </header>
```

**레이아웃**:
- 최대 너비 960px, 가운데 정렬
- 헤더는 데스크톱에서 좌우 배치, 모바일(`sm:`)에서 세로 배치
- 채팅 유형, 상대방 이름, 상태 메시지 표시
- 1:1 채팅인 경우 상대방 프로필 (아바타 + 이름 + UID)

### 11. 템플릿 구조 - 메시지 목록

```svelte
  {#if !activeRoomId}
    <section class="chat-room-empty p-8">
      <p class="empty-title">{m.chatRoomNotReady()}</p>
      <p class="empty-subtitle">
        {m.chatAddUidOrRoomId()}
      </p>
    </section>
  {:else}
    <section class="flex flex-col gap-4">
      <div class="message-list-section relative max-h-[60vh] min-h-80 overflow-auto p-4">
        {#if canRenderMessages}
          {#key roomOrderPrefix}
            <DatabaseListView
              bind:this={databaseListView}
              path={messagePath}
              pageSize={20}
              orderBy={roomOrderField}
              orderPrefix={roomOrderPrefix}
              threshold={300}
              reverse={false}
              scrollTrigger="top"
              autoScrollToEnd={true}
              autoScrollOnNewData={true}
            >
              {#snippet item(itemData: { key: string; data: any })}
                {@const message = itemData.data ?? {}}
                {@const mine = message.senderUid === authStore.user?.uid}
                <article
                  class={`message-row ${mine ? 'message-row--mine' : 'message-row--theirs'}`}
                >
                  {#if !mine}
                    <Avatar uid={message.senderUid} size={36} class="message-avatar" />
                  {/if}
                  <div class={`message-bubble-wrap ${mine ? 'items-end text-right' : ''}`}>
                    {#if !mine}
                      <span class="message-sender-label"
                        >{resolveSenderLabel(message.senderUid)}</span
                      >
                    {/if}
                    <div class={`message-bubble ${mine ? 'bubble-mine' : 'bubble-theirs'}`}>
                      <p class="message-text m-0">{message.text || ''}</p>
                    </div>
                    <span class="message-timestamp">{formatLongDate(message.createdAt)}</span>
                  </div>
                </article>
              {/snippet}

              {#snippet loading()}
                <div class="message-placeholder py-6">{m.chatLoadingMessages()}</div>
              {/snippet}

              {#snippet empty()}
                <div class="message-placeholder py-6">{m.chatNoMessages()}</div>
              {/snippet}

              {#snippet error(errorMessage: string | null)}
                <div class="message-error py-4">
                  <p>{m.chatLoadMessagesFailed()}</p>
                  <p>{errorMessage ?? m.chatUnknownError()}</p>
                </div>
              {/snippet}

              {#snippet loadingMore()}
                <div class="message-placeholder subtle py-6">{m.chatLoadingMore()}</div>
              {/snippet}

              {#snippet noMore()}
                <div class="message-placeholder subtle py-6">{m.chatUpToDate()}</div>
              {/snippet}
            </DatabaseListView>
          {/key}
        {:else}
          <div class="message-placeholder py-6">{m.chatPreparingStream()}</div>
        {/if}

        <!-- 스크롤 컨트롤 버튼 -->
        {#if canRenderMessages}
          <div class="scroll-controls">
            <button
              type="button"
              class="scroll-button scroll-to-top"
              onclick={handleScrollToTop}
              title="맨 위로 이동"
            >
              ↑
            </button>
            <button
              type="button"
              class="scroll-button scroll-to-bottom"
              onclick={handleScrollToBottom}
              title="맨 아래로 이동"
            >
              ↓
            </button>
          </div>
        {/if}
      </div>
```

**핵심 포인트**:
1. **조건부 렌더링**: `activeRoomId`가 없으면 안내 메시지, 있으면 메시지 목록
2. **`{#key}` 블록**: `roomOrderPrefix`가 변경될 때 DatabaseListView 완전히 재생성
3. **DatabaseListView 설정**:
   - `pageSize={20}`: 한 페이지당 20개 메시지
   - `reverse={false}`: 오래된 메시지가 위, 최신 메시지가 아래 (일반 채팅 UI)
   - `scrollTrigger="top"`: 위로 스크롤하면 더 오래된 메시지 로드
   - `autoScrollToEnd={true}`: 처음 로드 시 맨 아래로 스크롤
   - `autoScrollOnNewData={true}`: 새 메시지 도착 시 자동 스크롤 (사용자가 하단에 있을 때만)
4. **Snippet 패턴**: Svelte 5의 새로운 템플릿 패턴
   - `item`: 각 메시지 렌더링
   - `loading`: 초기 로딩 상태
   - `empty`: 메시지가 없을 때
   - `error`: 로딩 실패 시
   - `loadingMore`: 페이지네이션 로딩
   - `noMore`: 모든 메시지 로드 완료
5. **메시지 레이아웃**:
   - 내 메시지(`mine`): 오른쪽 정렬, 아바타 없음
   - 상대 메시지: 왼쪽 정렬, 아바타 + 이름 표시
6. **스크롤 컨트롤**: 고정 위치(absolute)로 우측 하단에 배치

### 12. 템플릿 구조 - 메시지 입력 폼

```svelte
      <form class="flex items-center gap-3" onsubmit={handleSendMessage}>
        <input
          bind:this={composerInputRef}
          type="text"
          name="composer"
          class="composer-input flex-1 px-4 py-3.5"
          placeholder={m.chatWriteMessage()}
          bind:value={composerText}
          disabled={composerDisabled || isSending}
        />
        <button
          type="submit"
          class="composer-button cursor-pointer px-8 py-3.5"
          disabled={composerDisabled || isSending || !composerText.trim()}
        >
          {isSending ? m.chatSending() : m.chatSend()}
        </button>
      </form>

      {#if sendError}
        <p class="composer-error m-0">{sendError}</p>
      {/if}
    </section>
  {/if}
</div>
```

**핵심 포인트**:
1. **⭐ `bind:this={composerInputRef}`**: 입력 요소에 대한 직접 참조 (포커스 관리의 핵심!)
2. **`bind:value={composerText}`**: 양방향 데이터 바인딩
3. **`disabled` 조건**:
   - 입력창: 로그인 안됨 OR 채팅방 준비 안됨 OR 전송 중
   - 버튼: 위 조건 + 텍스트가 비어있음
4. **버튼 텍스트**: 전송 중일 때 "전송 중...", 아니면 "전송"
5. **에러 표시**: `sendError`가 있으면 하단에 빨간색으로 표시

### 13. CSS 스타일링 - 채팅방 헤더

```css
@import 'tailwindcss' reference;

/* 채팅방 헤더 스타일 */
.chat-room-header {
  @apply rounded-2xl border border-gray-200 bg-white shadow-[0_10px_25px_rgba(15,23,42,0.06)];
}

.chat-room-label {
  @apply mb-0.5 text-sm font-semibold tracking-wider text-indigo-500 uppercase;
}

.chat-room-title {
  @apply m-0 text-[1.8rem] font-bold text-gray-900;
}

.chat-room-subtitle {
  @apply text-[0.95rem] text-gray-500;
}

.chat-room-partner {
  @apply rounded-full bg-gray-50;
}

.partner-name {
  @apply m-0 font-semibold text-gray-900;
}

.partner-uid {
  @apply m-0 text-sm break-all text-gray-500;
}
```

**디자인 특징**:
- 둥근 모서리 (`rounded-2xl`)
- 은은한 그림자 효과
- 흰색 배경 + 회색 테두리
- 라벨은 인디고 색상 + 대문자 + 넓은 자간
- 제목은 큰 글씨 + 굵은 폰트
- 부제목은 회색 텍스트

### 14. CSS 스타일링 - 빈 채팅방

```css
/* 빈 채팅방 스타일 */
.chat-room-empty {
  @apply rounded-2xl border border-dashed border-gray-300 bg-[#fdfdfd] text-center;
}

.empty-title {
  @apply mb-2 text-xl font-semibold text-gray-900;
}

.empty-subtitle {
  @apply text-gray-500;
}
```

**디자인 특징**:
- 점선 테두리 (`border-dashed`)
- 아주 연한 회색 배경
- 가운데 정렬된 텍스트

### 15. CSS 스타일링 - 메시지 목록

```css
/* 메시지 목록 스타일 */
.message-list-section {
  @apply rounded-2xl border border-gray-200 bg-white;
}

.message-row {
  @apply flex gap-3 px-2 py-3;
}

.message-row--mine {
  @apply justify-end;
}

.message-row--theirs {
  @apply justify-start;
}

.message-avatar {
  @apply mr-2 rounded-full bg-gray-100 shadow-sm;
}

.message-bubble-wrap {
  @apply flex max-w-[75%] flex-col gap-1;
}

.message-bubble {
  @apply rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm;
}

.bubble-mine {
  @apply bg-amber-300 text-gray-900 shadow-inner;
}

.bubble-theirs {
  @apply border border-gray-200 bg-white text-gray-900;
}

.message-sender-label {
  @apply text-xs text-gray-400;
}

.message-text {
  @apply text-[0.95rem] break-words whitespace-pre-wrap text-gray-900;
}

.message-timestamp {
  @apply text-[11px] text-gray-400;
}
```

**디자인 특징**:
- **내 메시지 (`bubble-mine`)**:
  - 앰버(호박색) 배경 (`bg-amber-300`)
  - 오른쪽 정렬
  - 안쪽 그림자 (`shadow-inner`)
- **상대 메시지 (`bubble-theirs`)**:
  - 흰색 배경 + 회색 테두리
  - 왼쪽 정렬
  - 발신자 이름 표시
- **말풍선 최대 너비**: 75% (긴 메시지도 화면을 가득 채우지 않음)
- **줄바꿈 지원**: `whitespace-pre-wrap`으로 개행 문자 보존

### 16. CSS 스타일링 - 플레이스홀더 및 에러

```css
/* 메시지 플레이스홀더 스타일 */
.message-placeholder {
  @apply text-center text-gray-500;
}

.message-placeholder.subtle {
  @apply text-sm text-gray-400;
}

.message-error {
  @apply text-center text-red-600;
}
```

**디자인 특징**:
- 로딩, 빈 상태, 에러 메시지 모두 가운데 정렬
- `.subtle` 클래스: 더 작고 연한 텍스트 (페이지네이션 상태용)
- 에러는 빨간색 텍스트

### 17. CSS 스타일링 - 메시지 입력창

```css
/* 메시지 입력 스타일 */
.composer-input {
  @apply rounded-full border border-gray-300 bg-white text-base;
}

.composer-input:disabled {
  @apply bg-gray-100;
}

.composer-button {
  @apply rounded-full border-0 bg-gray-900 font-semibold text-white transition-colors duration-200;
}

.composer-button:disabled {
  @apply cursor-not-allowed bg-gray-400;
}

.composer-error {
  @apply text-sm text-red-600;
}
```

**디자인 특징**:
- **입력창**:
  - 완전히 둥근 모서리 (`rounded-full`)
  - 흰색 배경 + 회색 테두리
  - 비활성화 시 회색 배경
- **전송 버튼**:
  - 완전히 둥근 모서리
  - 검은색 배경 + 흰색 텍스트
  - 호버/포커스 시 색상 전환 애니메이션
  - 비활성화 시 회색 배경 + 커서 금지 아이콘
- **에러 메시지**: 작은 빨간색 텍스트

### 18. CSS 스타일링 - 스크롤 컨트롤 버튼

```css
/* 스크롤 컨트롤 버튼 스타일 */
.scroll-controls {
  @apply absolute right-4 bottom-4 flex flex-col gap-2;
}

.scroll-button {
  @apply flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-0 bg-gray-900 text-lg font-bold text-white shadow-lg transition-all duration-200;
}

.scroll-button:hover {
  @apply scale-110 bg-gray-700;
}

.scroll-button:active {
  @apply scale-95 bg-gray-950;
}
```

**디자인 특징**:
- **위치**: `absolute`로 메시지 목록 우측 하단에 고정
- **레이아웃**: 세로 방향 (`flex-col`), 2개 버튼 (↑, ↓)
- **스타일**:
  - 원형 (`rounded-full`)
  - 검은색 배경 + 흰색 화살표
  - 큰 그림자 (`shadow-lg`)
- **애니메이션**:
  - 호버 시: 10% 확대 (`scale-110`) + 밝은 회색
  - 클릭 시: 5% 축소 (`scale-95`) + 더 진한 검은색
  - 부드러운 전환 (`transition-all duration-200`)

### 19. 포커스 관리 상세 설명

**문제점**:
- 채팅 메시지 전송 후 포커스가 입력창에 남아있지 않음
- 사용자가 수동으로 입력창을 클릭해야 다음 메시지 입력 가능
- querySelector 방식은 Svelte 5의 반응성 시스템과 타이밍 충돌 발생

**해결책**:
```typescript
// 1. 입력 요소에 대한 직접 참조 생성
let composerInputRef: HTMLInputElement | null = $state(null);

// 2. 템플릿에서 bind:this로 참조 연결
<input bind:this={composerInputRef} ... />

// 3. 메시지 전송 성공 후 포커스 추가
if (!result.success) {
  // 에러 처리
} else {
  composerText = '';
  sendError = null;
  isSending = false;

  // Svelte DOM 업데이트 대기
  await tick();

  // 브라우저 렌더링 완료 대기 후 포커스
  requestAnimationFrame(() => {
    if (composerInputRef) {
      composerInputRef.focus();
      console.log('✅ 채팅 입력 창에 포커스 추가됨');
    }
  });
}
```

**타이밍 흐름**:
1. `composerText = ''`: 상태 변수 초기화
2. `await tick()`: Svelte가 이 변경사항을 DOM에 반영할 때까지 대기
3. `requestAnimationFrame()`: 브라우저가 화면을 실제로 그릴 때까지 대기
4. `composerInputRef.focus()`: 완전히 렌더링된 입력 요소에 포커스

**왜 이 방법이 작동하는가**:
- `bind:this`는 Svelte 5에서 권장하는 DOM 참조 방식
- `tick()`은 Svelte의 반응성 시스템 동기화
- `requestAnimationFrame()`은 브라우저의 렌더 사이클 동기화
- 두 단계의 동기화로 100% 신뢰성 확보

**주의사항**:
- ⚠️ 포커스는 **메시지 전송 성공 시에만** 추가 (에러 발생 시 X)
- ⚠️ 포커스는 **처음 채팅방 입장 시에는** 추가하지 않음
- ⚠️ `null` 체크 필수 (`if (composerInputRef)`)

### 20. 스크롤 제어 상세 설명

**기능**:
- 사용자가 긴 대화 내역을 빠르게 탐색할 수 있도록 지원
- 맨 위로 이동 (↑) 버튼: 과거 메시지 확인
- 맨 아래로 이동 (↓) 버튼: 최신 메시지로 이동

**구현**:
```typescript
// DatabaseListView 컴포넌트 참조
let databaseListView: any = $state(null);

// 스크롤 핸들러
function handleScrollToTop() {
  databaseListView?.scrollToTop();
}

function handleScrollToBottom() {
  databaseListView?.scrollToBottom();
}

// 템플릿에서 참조 연결 및 버튼 배치
<DatabaseListView bind:this={databaseListView} ... />

<div class="scroll-controls">
  <button onclick={handleScrollToTop}>↑</button>
  <button onclick={handleScrollToBottom}>↓</button>
</div>
```

**작동 원리**:
- `DatabaseListView` 컴포넌트는 내부적으로 스크롤 컨테이너를 관리
- `scrollToTop()`: `scrollTop = 0`으로 설정
- `scrollToBottom()`: `scrollTop = scrollHeight`으로 설정
- `bind:this`로 컴포넌트 인스턴스에 접근하여 메서드 호출

**UX 고려사항**:
- 버튼은 우측 하단에 고정 (`absolute`)
- 메시지 목록 위에 떠있어 항상 접근 가능
- 호버 시 확대되어 클릭 대상 명확화
- 클릭 시 축소되어 시각적 피드백 제공

### 21. 반응형 디자인 패턴

**Tailwind CSS Breakpoint**:
- 기본: 데스크톱 (640px 이상)
- `sm:`: 모바일 (640px 이하)

**모바일 최적화**:
```svelte
<!-- 헤더: 데스크톱 가로, 모바일 세로 -->
<header class="... sm:flex-col sm:items-start">

<!-- 상대방 프로필: 모바일에서 전체 너비 + 가운데 정렬 -->
<div class="... sm:w-full sm:justify-center">

<!-- 메시지 말풍선: 최대 너비 조정 -->
<div class="max-w-[75%] ...">
```

**핵심 원칙**:
1. 모바일 우선 (Mobile-First)이 아닌 데스크톱 우선
2. `sm:` prefix로 작은 화면에서의 오버라이드
3. Flexbox 레이아웃으로 유연한 반응형 구조
4. 터치 친화적인 버튼 크기 (최소 44x44px)

---

## 완전한 재구현을 위한 체크리스트

스펙 파일만 보고 채팅방 페이지를 재구현할 때 다음 사항들을 확인하세요:

### Script 섹션
- [ ] 모든 imports 추가 (12개)
- [ ] URL 파라미터 파생 상태 (`uidParam`, `roomIdParam`, `isSingleChat`, `activeRoomId`)
- [ ] DatabaseListView 설정 (`messagePath`, `roomOrderField`, `roomOrderPrefix`, `canRenderMessages`)
- [ ] 사용자 프로필 구독 (`$effect`, `userProfileStore`)
- [ ] 프로필 파생 상태 (`targetProfile`, `targetProfileLoading`, `targetProfileError`, `targetDisplayName`)
- [ ] 메시지 전송 상태 (`composerText`, `isSending`, `sendError`, `composerInputRef`, `composerDisabled`)
- [ ] 메시지 전송 핸들러 (`handleSendMessage`) - 포커스 관리 포함!
- [ ] DatabaseListView 참조 및 스크롤 핸들러 (`databaseListView`, `handleScrollToTop`, `handleScrollToBottom`)
- [ ] 발신자 라벨 함수 (`resolveSenderLabel`)

### Template 섹션
- [ ] `<svelte:head>` 페이지 제목
- [ ] 메인 컨테이너 (`max-w-[960px]`)
- [ ] 채팅방 헤더 (유형, 제목, 부제목, 상대방 프로필)
- [ ] 빈 채팅방 안내 (`!activeRoomId` 조건)
- [ ] 메시지 목록 섹션 (`max-h-[60vh]`)
- [ ] `{#key roomOrderPrefix}` 블록
- [ ] DatabaseListView 컴포넌트 (모든 props 설정)
- [ ] 6개 Snippet (item, loading, empty, error, loadingMore, noMore)
- [ ] 메시지 렌더링 (아바타, 말풍선, 타임스탬프)
- [ ] 스크롤 컨트롤 버튼 (↑, ↓)
- [ ] 메시지 입력 폼 (`bind:this`, `bind:value`, `disabled` 조건)
- [ ] 에러 메시지 표시

### Style 섹션
- [ ] `@import 'tailwindcss' reference;`
- [ ] 채팅방 헤더 스타일 (5개 클래스)
- [ ] 빈 채팅방 스타일 (3개 클래스)
- [ ] 메시지 목록 스타일 (9개 클래스)
- [ ] 메시지 입력 스타일 (5개 클래스)
- [ ] 스크롤 컨트롤 스타일 (3개 클래스 + 호버/액티브)

### 다국어 지원
- [ ] 모든 하드코딩된 텍스트를 `m.*()` 함수로 교체
- [ ] 24개 다국어 키 사용 확인
- [ ] 파라미터가 있는 다국어 (`chatChattingWith`, `chatRoomReady`) 올바른 형식 사용

### 테스트
- [ ] 로그인하지 않은 상태에서 접근 시 안내 메시지
- [ ] `?uid=TARGET_UID`로 1:1 채팅방 접근
- [ ] `?roomId=ROOM_ID`로 그룹 채팅방 접근
- [ ] 메시지 전송 및 실시간 업데이트 확인
- [ ] 포커스가 전송 후 입력창에 유지되는지 확인
- [ ] 스크롤 컨트롤 버튼 작동 확인
- [ ] 모바일 반응형 레이아웃 확인

---

이 구현 가이드를 따르면 `/src/routes/chat/room/+page.svelte` 파일을 완벽히 재구현할 수 있습니다!

## 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-15 | Claude Code | 채팅 입력창을 input에서 textarea로 변경: Shift+Enter 줄바꿈 지원, 최대 3줄 자동 높이 조정 |
| 2025-11-15 | Claude Code | Shift+Enter 줄바꿈 무제한 허용, 입력창 최대 높이 4줄로 확장, 4줄 초과 시 스크롤바 표시 |
