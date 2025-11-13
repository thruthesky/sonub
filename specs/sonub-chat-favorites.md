---
name: 채팅방 즐겨찾기 기능
description: 사용자가 채팅방을 폴더별로 정리하고 관리할 수 있는 즐겨찾기(북마크) 기능
version: 1.0.0
step: 60
priority: **
dependencies:
  - sonub-firebase-database-structure.md
  - sonub-chat-overview.md
tags:
  - chat
  - favorites
  - bookmark
  - folder
  - firebase
  - rtdb
---

# 채팅방 즐겨찾기 기능

## 1. 개요 (Overview)

채팅방 즐겨찾기 기능은 사용자가 여러 채팅방을 폴더별로 구조화하여 관리할 수 있도록 하는 기능입니다. 사용자는 폴더를 생성하고, 채팅방을 원하는 폴더에 추가하며, 폴더를 상단에 고정(pin)할 수 있습니다.

### 1.1 핵심 개념

- **폴더 관리**: 사용자는 즐겨찾기 폴더를 생성, 수정, 삭제할 수 있습니다
- **채팅방 관리**: 각 폴더에 채팅방을 추가하거나 제거할 수 있습니다
- **상단 고정**: 중요한 폴더를 목록 상단에 고정할 수 있습니다
- **듀얼 모드**: 채팅방에서 호출 시 "추가 모드"와 "목록 모드"를 전환할 수 있습니다
- **실시간 동기화**: Firebase RTDB를 통해 실시간으로 데이터가 동기화됩니다

### 1.2 사용 시나리오

#### 시나리오 1: 채팅 목록에서 즐겨찾기 관리
1. 사용자가 채팅 목록 페이지에서 메뉴의 "즐겨찾기" 버튼을 클릭합니다
2. 즐겨찾기 다이얼로그가 열리고 기존 폴더 목록을 표시합니다
3. 사용자는 폴더를 생성하고 편집할 수 있습니다
4. 폴더를 클릭하면 해당 폴더에 저장된 채팅방 목록을 확인할 수 있습니다
5. 채팅방을 클릭하면 해당 채팅방으로 이동합니다

#### 시나리오 2: 채팅방에서 즐겨찾기에 추가
1. 사용자가 특정 채팅방에서 메뉴의 "즐겨찾기" 버튼을 클릭합니다
2. 즐겨찾기 다이얼로그가 "추가 모드"로 열립니다
3. 현재 채팅방이 속한 폴더는 핑크색으로 하이라이트되고 "✓" 표시가 됩니다
4. 사용자가 폴더를 클릭하면 현재 채팅방을 해당 폴더에 추가하거나 제거합니다
5. "목록" 버튼을 클릭하면 "목록 모드"로 전환하여 폴더 내 채팅방 목록을 확인할 수 있습니다

## 2. 요구사항 (Requirements)

### 2.1 기능 요구사항

#### 폴더 관리
- [x] 폴더 생성: 이름과 선택적 설명을 입력하여 새 폴더 생성
- [x] 폴더 수정: 기존 폴더의 이름, 설명, 고정 상태 수정
- [x] 폴더 삭제: 확인 다이얼로그 후 폴더 삭제
- [x] 상단 고정: 폴더를 목록 상단에 고정 (📌 아이콘 표시)
- [x] 폴더 정렬: 고정된 폴더가 먼저, 그 다음 최신순으로 정렬

#### 채팅방 관리
- [x] 채팅방 추가: 폴더에 채팅방 추가
- [x] 채팅방 제거: 폴더에서 채팅방 제거
- [x] 채팅방 목록: 폴더 내 채팅방 목록 표시
- [x] 채팅방 이동: 채팅방 클릭 시 해당 채팅방으로 이동
- [x] 멀티 폴더: 하나의 채팅방이 여러 폴더에 추가 가능

#### 듀얼 모드 (채팅방에서 호출 시)
- [x] 추가 모드: 현재 채팅방을 폴더에 추가/제거하는 모드
  - 폴더 클릭 시 토글 동작
  - 현재 채팅방이 속한 폴더 하이라이트 (핑크색 배경)
  - 토글 인디케이터 표시 (✓ 또는 +)
- [x] 목록 모드: 폴더와 채팅방 목록을 탐색하는 모드
  - 폴더 클릭 시 확장/축소 동작
  - 채팅방 목록 표시
- [x] 모드 전환: "추가" ↔ "목록" 버튼으로 모드 전환

### 2.2 UI/UX 요구사항
- [x] 반응형 디자인: 모바일/데스크톱 환경에서 사용 가능
- [x] 다국어 지원: paraglide-js를 통한 i18n
- [x] 에러 핸들링: 사용자에게 명확한 에러 메시지 표시
- [x] 로딩 상태: 데이터 로딩 중 로딩 인디케이터 표시
- [x] 빈 상태: 폴더나 채팅방이 없을 때 안내 메시지 표시

### 2.3 기술 요구사항
- [x] Svelte 5 Runes: `$state`, `$bindable`, `$derived`, `$effect` 사용
- [x] Firebase RTDB: 실시간 데이터베이스 사용
- [x] shadcn-svelte: Dialog 컴포넌트 사용
- [x] TailwindCSS: Light Mode 스타일링
- [x] TypeScript: 타입 안정성 보장

## 3. 데이터베이스 구조 (Database Structure)

### 3.1 Firebase RTDB 경로

```
/chat-favorites/{uid}/{favoriteId}
  ├─ name: string                    # 폴더 이름
  ├─ description: string | null      # 폴더 설명 (선택사항)
  ├─ createdAt: number              # 생성 타임스탬프
  ├─ folderOrder: string            # 정렬 순서 ("500{timestamp}" 또는 "{timestamp}")
  └─ roomList: object               # 채팅방 목록
       └─ {roomId}: true            # 채팅방 ID를 키로 하는 boolean
```

### 3.2 데이터 구조 예시

```json
{
  "chat-favorites": {
    "user123": {
      "favorite001": {
        "name": "업무 관련",
        "description": "팀 채팅을 모아둔 폴더",
        "createdAt": 1700000000000,
        "folderOrder": "5001700000000000",
        "roomList": {
          "room001": true,
          "room002": true,
          "room003": true
        }
      },
      "favorite002": {
        "name": "친구들",
        "description": null,
        "createdAt": 1700100000000,
        "folderOrder": "1700100000000",
        "roomList": {
          "room004": true,
          "room005": true
        }
      }
    }
  }
}
```

### 3.3 정렬 로직 설명

- `folderOrder` 필드는 폴더의 정렬 순서를 결정합니다
- **상단 고정 폴더**: `folderOrder`가 "500"으로 시작 (예: "5001700000000000")
- **일반 폴더**: 타임스탬프만 사용 (예: "1700000000000")
- **정렬 순서**:
  1. 고정된 폴더가 먼저 표시됨
  2. 같은 그룹 내에서는 `folderOrder` 문자열 비교로 정렬
  3. 결과적으로 최신 생성 순서대로 정렬됨

## 4. 타입 정의 (Type Definitions)

### 4.1 Favorite 인터페이스

```typescript
/**
 * 즐겨찾기 폴더 인터페이스
 */
interface Favorite {
  /** 폴더 ID (Firebase에서 생성된 키) */
  id: string;

  /** 폴더 이름 */
  name: string;

  /** 폴더 설명 (선택사항) */
  description?: string;

  /** 생성 타임스탬프 */
  createdAt: number;

  /** 정렬 순서 (고정: "500{timestamp}", 일반: "{timestamp}") */
  folderOrder: string;

  /** 채팅방 목록 (roomId를 키로 하는 boolean 맵) */
  roomList?: Record<string, boolean>;
}
```

### 4.2 Props 인터페이스

```typescript
/**
 * ChatFavoritesDialog 컴포넌트 Props
 */
interface Props {
  /** 다이얼로그 열림 상태 (양방향 바인딩) */
  open?: boolean;

  /** 현재 채팅방 ID (채팅방에서 호출 시) */
  currentRoomId?: string | null;
}
```

### 4.3 DisplayMode 타입

```typescript
/**
 * 표시 모드 (채팅방에서 호출 시에만 사용)
 */
type DisplayMode = 'add' | 'browse';

// 'add': 폴더에 현재 채팅방 추가/제거 모드
// 'browse': 폴더 및 채팅방 목록 탐색 모드
```

## 5. 주요 함수 구현 (Implementation Details)

### 5.1 loadFavorites() - 폴더 목록 불러오기

```typescript
/**
 * 즐겨찾기 폴더 목록 불러오기
 *
 * 동작:
 * 1. 현재 사용자의 chat-favorites 경로에서 데이터 읽기
 * 2. 데이터를 Favorite[] 배열로 변환
 * 3. 정렬 로직 적용 (고정 폴더 먼저, 그 다음 최신순)
 */
async function loadFavorites() {
  if (!authStore.user?.uid || !rtdb) return;

  isLoading = true;
  errorMessage = '';

  try {
    const favoritesRef = ref(rtdb, `chat-favorites/${authStore.user.uid}`);
    const snapshot = await get(favoritesRef);

    if (snapshot.exists()) {
      const data = snapshot.val();
      favorites = Object.entries(data).map(([id, value]) => ({
        id,
        ...(value as Omit<Favorite, 'id'>)
      })).sort((a, b) => {
        // pinned 폴더 (folderOrder가 "500"으로 시작)를 먼저 표시
        const aIsPinned = a.folderOrder.startsWith('500');
        const bIsPinned = b.folderOrder.startsWith('500');

        // 둘 다 pinned이거나 둘 다 아닌 경우: folderOrder로 정렬
        if (aIsPinned === bIsPinned) {
          return a.folderOrder.localeCompare(b.folderOrder);
        }

        // pinned 폴더를 먼저 표시
        return aIsPinned ? -1 : 1;
      });
    } else {
      favorites = [];
    }
  } catch (error) {
    console.error('즐겨찾기 로드 실패:', error);
    errorMessage = '즐겨찾기를 불러오는데 실패했습니다.';
  } finally {
    isLoading = false;
  }
}
```

### 5.2 saveFolder() - 폴더 저장 (생성/수정)

```typescript
/**
 * 폴더 저장 (생성 또는 수정)
 *
 * 동작:
 * 1. 입력값 검증
 * 2. folderOrder 계산 (고정 시 "500" 접두사 추가)
 * 3. 생성 모드: push()로 새 폴더 생성
 * 4. 수정 모드: update()로 기존 폴더 수정
 * 5. 목록 새로고침 및 목록 모드로 전환
 */
async function saveFolder() {
  const trimmedName = folderName.trim();
  if (!trimmedName) {
    errorMessage = '폴더 이름을 입력해주세요.';
    return;
  }

  if (!authStore.user?.uid || !rtdb) {
    errorMessage = '로그인이 필요합니다.';
    return;
  }

  isSaving = true;
  errorMessage = '';

  try {
    const now = Date.now();
    const prefix = pinToTop ? '500' : '';
    const folderOrder = `${prefix}${now}`;

    if (viewMode === 'create') {
      // 새 폴더 생성
      const favoritesRef = ref(rtdb, `chat-favorites/${authStore.user.uid}`);
      const newFavoriteRef = push(favoritesRef);

      await set(newFavoriteRef, {
        name: trimmedName,
        description: folderDescription.trim() || null,
        createdAt: now,
        folderOrder,
        roomList: {}
      });
    } else if (viewMode === 'edit' && selectedFavorite) {
      // 기존 폴더 수정
      const favoriteRef = ref(rtdb, `chat-favorites/${authStore.user.uid}/${selectedFavorite.id}`);
      await update(favoriteRef, {
        name: trimmedName,
        description: folderDescription.trim() || null,
        folderOrder
      });
    }

    // 목록 새로고침 및 목록 모드로 전환
    await loadFavorites();
    viewMode = 'list';
    selectedFavorite = null;
  } catch (error) {
    console.error('폴더 저장 실패:', error);
    errorMessage = '폴더 저장에 실패했습니다.';
  } finally {
    isSaving = false;
  }
}
```

### 5.3 toggleRoomInFolder() - 채팅방 추가/제거 토글

```typescript
/**
 * 폴더 선택 (채팅방 추가/제거 토글)
 *
 * 동작:
 * 1. 현재 채팅방이 해당 폴더에 있는지 확인
 * 2. 있으면 remove(), 없으면 set(true)
 * 3. 목록 새로고침
 */
async function toggleRoomInFolder(favorite: Favorite) {
  if (!currentRoomId || !authStore.user?.uid || !rtdb) return;

  try {
    const roomList = favorite.roomList || {};
    const isInFolder = roomList[currentRoomId] === true;

    const favoriteRef = ref(rtdb, `chat-favorites/${authStore.user.uid}/${favorite.id}/roomList/${currentRoomId}`);

    if (isInFolder) {
      // 이미 추가되어 있으면 제거
      await remove(favoriteRef);
    } else {
      // 추가되어 있지 않으면 추가
      await set(favoriteRef, true);
    }

    // 목록 새로고침
    await loadFavorites();
  } catch (error) {
    console.error('즐겨찾기 토글 실패:', error);
    errorMessage = '즐겨찾기 변경에 실패했습니다.';
  }
}
```

### 5.4 handleFolderClick() - 폴더 클릭 핸들러 (듀얼 모드)

```typescript
/**
 * 폴더 클릭 핸들러
 *
 * 동작:
 * - currentRoomId가 있고 displayMode가 'add'인 경우:
 *   → 채팅방을 폴더에 추가/제거 토글
 * - 그 외의 경우:
 *   → 폴더 확장/축소 (채팅방 목록 표시/숨김)
 */
function handleFolderClick(favorite: Favorite) {
  if (currentRoomId && displayMode === 'add') {
    // 추가 모드: 채팅방을 폴더에 추가/제거 토글
    toggleRoomInFolder(favorite);
  } else {
    // 목록 모드 또는 채팅 목록에서 호출: 폴더 확장/축소
    selectedFavorite = selectedFavorite?.id === favorite.id ? null : favorite;
  }
}
```

### 5.5 toggleDisplayMode() - 모드 전환

```typescript
/**
 * 표시 모드 토글 (추가 ↔ 목록)
 *
 * 동작:
 * 1. displayMode를 'add' ↔ 'browse'로 전환
 * 2. 선택된 폴더 초기화
 */
function toggleDisplayMode() {
  displayMode = displayMode === 'add' ? 'browse' : 'add';
  selectedFavorite = null; // 모드 전환 시 선택 초기화
}
```

### 5.6 $effect - 다이얼로그 초기화

```typescript
/**
 * 다이얼로그 열릴 때 즐겨찾기 목록 로드 및 초기화
 *
 * 동작:
 * 1. 다이얼로그가 열릴 때마다 폴더 목록 로드
 * 2. displayMode를 'add'로 초기화
 * 3. 선택된 폴더 초기화
 */
$effect(() => {
  if (open) {
    loadFavorites();
    displayMode = 'add'; // 다이얼로그를 열 때마다 추가 모드로 초기화
    selectedFavorite = null;
  }
});
```

## 6. UI/UX 레이아웃

### 6.1 다이얼로그 헤더 (조건부 제목과 버튼)

```svelte
<DialogHeader>
  <div class="flex items-center justify-between">
    <div class="flex-1">
      <DialogTitle>
        {#if currentRoomId && displayMode === 'add'}
          즐겨찾기 추가
        {:else}
          {m.chatTabBookmarks()}
        {/if}
      </DialogTitle>
      <DialogDescription>
        {#if currentRoomId && displayMode === 'add'}
          즐겨찾기 폴더를 선택하여 채팅방을 추가하거나 제거하세요.
        {:else if currentRoomId && displayMode === 'browse'}
          폴더를 클릭하여 저장된 채팅방 목록을 확인하세요.
        {:else}
          채팅방을 폴더에 정리하세요.
        {/if}
      </DialogDescription>
    </div>
    {#if currentRoomId}
      <Button
        type="button"
        variant="outline"
        size="sm"
        onclick={toggleDisplayMode}
        class="ml-4"
      >
        {displayMode === 'add' ? '목록' : '추가'}
      </Button>
    {/if}
  </div>
</DialogHeader>
```

### 6.2 폴더 아이템 구조

```
┌─────────────────────────────────────────┐
│ [폴더 헤더 (클릭 가능)]                   │
│  ┌─────────────────────────────────┐     │
│  │ 📌 업무 관련              [✓]   │     │
│  │ 팀 채팅을 모아둔 폴더            │     │
│  │ 3개 방                          │     │
│  └─────────────────────────────────┘     │
│                                           │
│ [폴더 액션 버튼] (채팅 목록에서만)          │
│  ✏️ 수정   🗑️ 삭제                       │
│                                           │
│ [채팅방 목록] (폴더 확장 시)                │
│  ┌─────────────────────────────────┐     │
│  │ 방: room001               [✕]   │     │
│  │ 방: room002               [✕]   │     │
│  │ 방: room003               [✕]   │     │
│  └─────────────────────────────────┘     │
└─────────────────────────────────────────┘
```

### 6.3 하이라이트 효과 (채팅방에서 호출 시)

- **현재 채팅방이 속한 폴더**: 핑크색 배경 (`bg-pink-50`) 및 핑크색 테두리 (`border-pink-300`)
- **토글 인디케이터**:
  - 폴더에 속한 경우: "✓" 표시
  - 폴더에 속하지 않은 경우: "+" 표시

### 6.4 조건부 UI 표시

1. **토글 인디케이터**: `currentRoomId && displayMode === 'add'`일 때만 표시
2. **폴더 액션 버튼**: `!currentRoomId`일 때만 표시 (채팅 목록에서 호출)
3. **채팅방 목록**: `selectedFavorite?.id === favorite.id && (!currentRoomId || displayMode === 'browse')`일 때만 표시
4. **채팅방 제거 버튼**: `!currentRoomId`일 때만 표시

## 7. 사용 예시 (Usage Examples)

### 7.1 채팅 목록 페이지에서 사용

```svelte
<script lang="ts">
  import ChatFavoritesDialog from '$lib/components/chat/ChatFavoritesDialog.svelte';
  import { goto } from '$app/navigation';

  let favoritesDialogOpen = $state(false);

  function handleBookmark() {
    favoritesDialogOpen = true;
  }

  function handleRoomSelected(event: CustomEvent<{ roomId: string }>) {
    const { roomId } = event.detail;
    void goto(`/chat/room?roomId=${roomId}`);
  }
</script>

<!-- 즐겨찾기 다이얼로그 -->
<ChatFavoritesDialog
  bind:open={favoritesDialogOpen}
  on:roomSelected={handleRoomSelected}
/>

<!-- 메뉴 버튼 -->
<button onclick={handleBookmark}>즐겨찾기</button>
```

### 7.2 채팅방 페이지에서 사용

```svelte
<script lang="ts">
  import ChatFavoritesDialog from '$lib/components/chat/ChatFavoritesDialog.svelte';
  import { page } from '$app/stores';

  // 현재 채팅방 ID
  const currentRoomId = $derived(
    $page.url.searchParams.get('roomId') ?? null
  );

  let favoritesDialogOpen = $state(false);

  function handleBookmark() {
    favoritesDialogOpen = true;
  }
</script>

<!-- 즐겨찾기 다이얼로그 (currentRoomId 전달) -->
<ChatFavoritesDialog
  bind:open={favoritesDialogOpen}
  currentRoomId={currentRoomId}
/>

<!-- 메뉴 버튼 -->
<button onclick={handleBookmark}>즐겨찾기</button>
```

### 7.3 이벤트 처리

```svelte
<ChatFavoritesDialog
  bind:open={favoritesDialogOpen}
  currentRoomId={currentRoomId}
  on:roomSelected={(event) => {
    const { roomId } = event.detail;
    console.log('선택된 채팅방:', roomId);
    void goto(`/chat/room?roomId=${roomId}`);
  }}
  on:close={() => {
    console.log('다이얼로그 닫힘');
  }}
/>
```

## 8. 테스트 시나리오 (Test Scenarios)

### 8.1 폴더 생성 테스트

**테스트 케이스**: 새 폴더 생성
1. 채팅 목록에서 "즐겨찾기" 버튼 클릭
2. "폴더 생성" 버튼 클릭
3. 폴더 이름: "테스트 폴더" 입력
4. 폴더 설명: "테스트용 폴더입니다" 입력
5. "저장" 버튼 클릭
6. **예상 결과**: 폴더 목록에 새 폴더가 추가됨

**테스트 케이스**: 상단 고정 폴더 생성
1. "폴더 생성" 버튼 클릭
2. 폴더 이름: "중요 폴더" 입력
3. "상단 고정" 체크박스 선택
4. "저장" 버튼 클릭
5. **예상 결과**: 폴더가 목록 상단에 표시되고 📌 아이콘이 표시됨

### 8.2 채팅방 추가/제거 테스트

**테스트 케이스**: 채팅방을 폴더에 추가
1. 특정 채팅방에서 "즐겨찾기" 버튼 클릭
2. 다이얼로그가 "추가 모드"로 열림
3. 원하는 폴더를 클릭
4. **예상 결과**:
   - 폴더가 핑크색으로 하이라이트됨
   - "✓" 표시가 나타남
   - 실시간으로 Firebase에 저장됨

**테스트 케이스**: 채팅방을 폴더에서 제거
1. 이미 추가된 폴더(핑크색 하이라이트)를 다시 클릭
2. **예상 결과**:
   - 하이라이트가 사라짐
   - "+" 표시로 변경됨
   - Firebase에서 삭제됨

### 8.3 듀얼 모드 테스트

**테스트 케이스**: 추가 모드에서 목록 모드로 전환
1. 채팅방에서 "즐겨찾기" 버튼 클릭 (추가 모드)
2. "목록" 버튼 클릭
3. **예상 결과**:
   - 제목이 "즐겨찾기"로 변경됨
   - 설명이 "폴더를 클릭하여 저장된 채팅방 목록을 확인하세요"로 변경됨
   - 폴더 클릭 시 확장/축소 동작으로 변경됨
   - 버튼 텍스트가 "추가"로 변경됨

**테스트 케이스**: 목록 모드에서 채팅방 목록 확인
1. 목록 모드에서 폴더 클릭
2. **예상 결과**:
   - 폴더가 확장되고 채팅방 목록이 표시됨
   - 채팅방을 클릭하면 해당 채팅방으로 이동함

### 8.4 폴더 수정/삭제 테스트

**테스트 케이스**: 폴더 수정
1. 채팅 목록에서 "즐겨찾기" 버튼 클릭
2. 수정하려는 폴더의 "✏️" 버튼 클릭
3. 폴더 이름과 설명 수정
4. "저장" 버튼 클릭
5. **예상 결과**: 폴더 정보가 업데이트됨

**테스트 케이스**: 폴더 삭제
1. 삭제하려는 폴더의 "🗑️" 버튼 클릭
2. 확인 다이얼로그에서 "확인" 클릭
3. **예상 결과**: 폴더가 목록에서 제거됨

### 8.5 정렬 테스트

**테스트 케이스**: 고정 폴더 정렬
1. 일반 폴더 2개, 고정 폴더 2개 생성
2. **예상 결과**:
   - 고정 폴더 2개가 상단에 표시됨
   - 각 그룹 내에서 최신순으로 정렬됨

### 8.6 엣지 케이스 테스트

**테스트 케이스**: 빈 폴더 이름
1. "폴더 생성" 버튼 클릭
2. 폴더 이름을 입력하지 않고 "저장" 클릭
3. **예상 결과**: "폴더 이름을 입력해주세요." 에러 메시지 표시

**테스트 케이스**: 로그아웃 상태에서 호출
1. 로그아웃 후 즐겨찾기 다이얼로그 열기 시도
2. **예상 결과**: 에러가 발생하지 않고 빈 목록 표시 또는 로딩 상태 유지

**테스트 케이스**: 네트워크 에러
1. 네트워크 연결 끊기
2. 폴더 생성 또는 수정 시도
3. **예상 결과**: 명확한 에러 메시지 표시

## 9. 스타일링 (Styling)

### 9.1 TailwindCSS 클래스 적용

모든 스타일은 TailwindCSS 유틸리티 클래스를 통해 적용되며, `<style>` 블록에서 `@apply` 지시어를 사용합니다:

```css
@import 'tailwindcss' reference;

.error-message {
  @apply rounded-md bg-red-50 p-3 text-sm text-red-800;
}

.folder-item {
  @apply rounded-lg border bg-white transition-colors;
}

.folder-item.highlighted {
  @apply border-pink-300 bg-pink-50;
}
```

### 9.2 반응형 디자인

- 다이얼로그 최대 너비: `max-w-3xl`
- 최대 높이: `max-h-[80vh]`
- 오버플로우: `overflow-y-auto`

### 9.3 인터랙션 효과

- **호버 효과**: `hover:bg-gray-50`, `hover:text-blue-600`
- **트랜지션**: `transition-colors`
- **포커스**: `focus:ring-2 focus:ring-blue-500`

## 10. 향후 개선 사항 (Future Improvements)

### 10.1 추가 기능
- [ ] 폴더 드래그 앤 드롭으로 순서 변경
- [ ] 채팅방 드래그 앤 드롭으로 폴더 간 이동
- [ ] 폴더 색상 커스터마이징
- [ ] 폴더 아이콘 선택
- [ ] 폴더 검색 기능
- [ ] 채팅방 정보 미리보기 (이름, 마지막 메시지 등)

### 10.2 성능 최적화
- [ ] 가상 스크롤 구현 (폴더/채팅방 수가 많을 때)
- [ ] 로컬 캐싱으로 로딩 속도 개선
- [ ] Debounce 적용 (폴더 이름 입력 시)

### 10.3 UX 개선
- [ ] 폴더 이동 애니메이션
- [ ] 토스트 알림 추가 (성공/실패 메시지)
- [ ] 키보드 단축키 지원
- [ ] 접근성 개선 (ARIA 속성 추가)

## 11. 관련 파일 (Related Files)

### 11.1 컴포넌트
- [src/lib/components/chat/ChatFavoritesDialog.svelte](../src/lib/components/chat/ChatFavoritesDialog.svelte) - 메인 컴포넌트

### 11.2 페이지
- [src/routes/chat/list/+page.svelte](../src/routes/chat/list/+page.svelte:357) - 채팅 목록 페이지
- [src/routes/chat/group-chat-list/+page.svelte](../src/routes/chat/group-chat-list/+page.svelte:294) - 그룹 채팅 목록 페이지
- [src/routes/chat/open-chat-list/+page.svelte](../src/routes/chat/open-chat-list/+page.svelte:295) - 오픈 채팅 목록 페이지
- [src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte) - 채팅방 페이지

### 11.3 스펙 문서
- [specs/sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md) - Firebase 데이터베이스 구조
- [specs/sonub-chat-overview.md](./sonub-chat-overview.md) - 채팅 기능 개요

## 12. 변경 이력 (Change History)

### v1.0.0 (2025-11-13)
- ✅ 초기 구현 완료
- ✅ 폴더 생성/수정/삭제 기능
- ✅ 채팅방 추가/제거 기능
- ✅ 상단 고정 기능
- ✅ 듀얼 모드 (추가/목록) 구현
- ✅ 채팅 목록 페이지 통합
- ✅ 채팅방 페이지 통합
- ✅ SED 형식 문서 작성
