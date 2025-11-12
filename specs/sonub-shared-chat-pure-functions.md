---
name: sonub-shared-chat-pure-functions
title: Sonub Shared Chat Pure Functions
version: 1.0.0
description: 서버와 클라이언트가 공유하는 채팅 관련 순수 함수 모음
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-12
updated: 2025-11-12
step: 31
priority: "**"
dependencies:
  - sonub-functions-overview.md
  - sonub-functions-chat-functions.md
  - sonub-firebase-database-structure.md
tags:
  - shared
  - pure-functions
  - chat
  - utilities
---

# Sonub Shared Chat Pure Functions

## 1. 목적

본 문서는 Sonub 프로젝트의 `shared/chat.pure-functions.ts` 파일에 정의된 **완전한 순수 함수(Pure Functions)**에 대해 설명합니다. 이 함수들은 채팅 기능과 관련된 비즈니스 로직 중 Firebase나 Svelte에 의존하지 않는 순수한 계산 및 변환 로직을 포함합니다.

### 1.1. 특징

- ✅ **100% Pure Functions**: 외부 의존성이 전혀 없음
- ✅ **서버/클라이언트 공유**: Firebase Functions와 Svelte 클라이언트 모두에서 사용
- ✅ **Framework 독립적**: Firebase, Svelte 등 어떤 프레임워크에도 의존하지 않음
- ✅ **문자열 처리 전용**: roomId 생성, 파싱, 유효성 검증 등

### 1.2. 포함되지 않는 함수들

다음 함수들은 Firebase에 의존하므로 `src/lib/functions/chat.functions.ts`에 유지됩니다:

- ❌ `joinChatRoom()` - Firebase Database 쓰기 작업
- ❌ `leaveChatRoom()` - Firebase Database 쓰기 작업

## 2. 아키텍처 개요

### 2.1. 파일 위치 및 역할

```
/Users/thruthesky/apps/sonub/
├── shared/
│   └── chat.pure-functions.ts           # 순수 함수 (이 문서)
│       ├── buildSingleRoomId()          # roomId 생성
│       ├── isSingleChat()               # 1:1 채팅 여부 확인
│       ├── extractUidsFromSingleRoomId()# UID 추출
│       └── resolveRoomTypeLabel()       # 타입 레이블 변환
│
├── src/lib/functions/
│   └── chat.functions.ts                # Svelte 클라이언트 함수
│       ├── [Re-exports from shared]     # 순수 함수 재내보내기
│       ├── joinChatRoom()               # Firebase 의존
│       └── leaveChatRoom()              # Firebase 의존
│
└── firebase/functions/src/handlers/
    └── chat.handler.ts                  # Cloud Functions 핸들러
        └── [Imports from shared]        # 순수 함수 직접 사용
```

### 2.2. 의존성 그래프

```
┌──────────────────────────────────────────────────────┐
│  shared/chat.pure-functions.ts                       │
│  - 100% Pure String Manipulation                     │
│  - Zero External Dependencies                        │
│  - Framework Independent                             │
└─────────────────┬────────────────────────────────────┘
                  │
        ┌─────────┴──────────┐
        ▼                    ▼
┌────────────────────┐  ┌───────────────────────────────┐
│ Svelte Client      │  │ Firebase Cloud Functions      │
│ (Re-export)        │  │ (Direct Import)               │
├────────────────────┤  ├───────────────────────────────┤
│ chat.functions.ts  │  │ handlers/chat.handler.ts      │
│ - Pure re-export   │  │ - roomId 파싱                 │
│ - Firebase 함수    │  │ - 채팅방 타입 확인            │
└────────────────────┘  └───────────────────────────────┘
```

## 3. 함수 명세

### 3.1. `buildSingleRoomId()`

두 사용자의 UID로부터 고정된 1:1 채팅방 ID를 생성합니다.

**시그니처**:
```typescript
function buildSingleRoomId(a: string, b: string): string
```

**파라미터**:
- `a`: 첫 번째 사용자 UID
- `b`: 두 번째 사용자 UID

**반환값**:
- `string`: 1:1 채팅방 ID (형식: `"single-uid1-uid2"`)
  - UID들은 항상 알파벳 순으로 정렬됨
  - 같은 두 사용자는 항상 동일한 roomId를 얻음

**비즈니스 로직**:
1. 두 UID를 배열에 담아 알파벳 순으로 정렬
2. `"single-"` 접두사와 정렬된 UID들을 `-`로 연결
3. 결과: `"single-{smaller_uid}-{larger_uid}"`

**Pure Function 보장**:
- ✅ 동일 입력 → 동일 출력
- ✅ 외부 상태 읽기/쓰기 없음
- ✅ 부수 효과(side effect) 없음

**예시**:
```typescript
// 정순으로 호출
buildSingleRoomId('alice', 'bob')
// → "single-alice-bob"

// 역순으로 호출해도 동일한 결과
buildSingleRoomId('bob', 'alice')
// → "single-alice-bob"

// UID에 숫자 포함
buildSingleRoomId('user123', 'user456')
// → "single-user123-user456"
```

**사용 사례**:
```typescript
// 클라이언트: 1:1 채팅방 생성
const roomId = buildSingleRoomId(currentUserUid, otherUserUid);
await createChatRoom(roomId, { type: 'single' });

// Cloud Functions: 1:1 채팅 여부 확인
export async function handleChatMessage(roomId: string) {
  if (isSingleChat(roomId)) {
    const [uid1, uid2] = extractUidsFromSingleRoomId(roomId);
    // 두 사용자에게만 알림 발송
  }
}
```

### 3.2. `isSingleChat()`

roomId가 1:1 채팅방인지 확인합니다.

**시그니처**:
```typescript
function isSingleChat(roomId: string): boolean
```

**파라미터**:
- `roomId`: 확인할 채팅방 ID

**반환값**:
- `boolean`:
  - `true`: 1:1 채팅방 (`"single-"` 접두사로 시작)
  - `false`: 그룹/오픈 채팅방 또는 기타

**비즈니스 로직**:
- `roomId`가 `"single-"` 문자열로 시작하는지 확인
- 단순 문자열 접두사 검사

**Pure Function 보장**:
- ✅ 동일 입력 → 동일 출력
- ✅ 외부 상태 읽기/쓰기 없음
- ✅ 부수 효과(side effect) 없음

**예시**:
```typescript
// 1:1 채팅방
isSingleChat('single-alice-bob')
// → true

// 그룹 채팅방
isSingleChat('group-abc123')
// → false

// 오픈 채팅방
isSingleChat('open-xyz789')
// → false

// 빈 문자열
isSingleChat('')
// → false
```

**사용 사례**:
```typescript
// UI에서 채팅방 타입에 따라 다른 렌더링
if (isSingleChat(roomId)) {
  return <SingleChatRoomHeader />;
} else {
  return <GroupChatRoomHeader />;
}

// Cloud Functions: 타입별 로직 분기
if (isSingleChat(roomId)) {
  // 1:1 채팅 전용 로직
  await sendDirectMessageNotification();
} else {
  // 그룹 채팅 전용 로직
  await sendGroupNotification();
}
```

### 3.3. `extractUidsFromSingleRoomId()`

1:1 채팅방 roomId에서 두 사용자의 UID를 추출합니다.

**시그니처**:
```typescript
function extractUidsFromSingleRoomId(roomId: string): [string, string] | null
```

**파라미터**:
- `roomId`: 1:1 채팅방 ID (형식: `"single-uid1-uid2"`)

**반환값**:
- `[string, string]`: 두 UID를 포함하는 튜플 `[uid1, uid2]`
- `null`: 형식이 올바르지 않은 경우

**비즈니스 로직**:
1. `roomId`를 `-`로 분할
2. 분할 결과가 정확히 3개이고, 첫 번째 요소가 `"single"`인지 확인
3. 유효하면 `[parts[1], parts[2]]` 반환
4. 유효하지 않으면 `null` 반환

**Pure Function 보장**:
- ✅ 동일 입력 → 동일 출력
- ✅ 외부 상태 읽기/쓰기 없음
- ✅ 부수 효과(side effect) 없음

**예시**:
```typescript
// 올바른 형식
extractUidsFromSingleRoomId('single-alice-bob')
// → ['alice', 'bob']

// 올바른 형식 (숫자 포함)
extractUidsFromSingleRoomId('single-user123-user456')
// → ['user123', 'user456']

// 잘못된 형식: 접두사 없음
extractUidsFromSingleRoomId('alice-bob')
// → null

// 잘못된 형식: parts 개수 부족
extractUidsFromSingleRoomId('single-alice')
// → null

// 잘못된 형식: 그룹 채팅방
extractUidsFromSingleRoomId('group-abc123')
// → null
```

**사용 사례**:
```typescript
// Cloud Functions: 1:1 채팅 참여자에게 알림 발송
export async function onChatMessageCreate(roomId: string, message: Message) {
  if (isSingleChat(roomId)) {
    const uids = extractUidsFromSingleRoomId(roomId);

    if (uids) {
      const [uid1, uid2] = uids;
      const recipientUid = message.senderUid === uid1 ? uid2 : uid1;

      await sendNotification(recipientUid, {
        title: '새 메시지',
        body: message.text
      });
    }
  }
}

// 클라이언트: 상대방 UID 확인
const uids = extractUidsFromSingleRoomId(roomId);
if (uids) {
  const [uid1, uid2] = uids;
  const otherUserUid = uid1 === currentUserUid ? uid2 : uid1;
  const otherUser = await fetchUser(otherUserUid);
}
```

### 3.4. `resolveRoomTypeLabel()`

채팅방 유형 문자열을 UI 표시용 짧은 배지 텍스트로 변환합니다.

**시그니처**:
```typescript
function resolveRoomTypeLabel(roomType: string): string
```

**파라미터**:
- `roomType`: DB에 저장된 채팅방 유형 문자열
  - 예: `"single"`, `"group"`, `"open"`, `"OpenChat"`, `"groupChat"` 등

**반환값**:
- `string`: UI에 표시할 짧은 배지 텍스트
  - `"Open"`: 오픈 채팅방
  - `"Group"`: 그룹 채팅방
  - `"Single"`: 1:1 채팅방
  - `"Room"`: 알 수 없는 타입 (기본값)

**비즈니스 로직**:
1. `roomType`을 소문자로 변환
2. `"open"` 포함 → `"Open"` 반환
3. `"group"` 포함 → `"Group"` 반환
4. `"single"` 포함 → `"Single"` 반환
5. 그 외 → `"Room"` 반환 (기본값)

**Pure Function 보장**:
- ✅ 동일 입력 → 동일 출력
- ✅ 외부 상태 읽기/쓰기 없음
- ✅ 부수 효과(side effect) 없음

**예시**:
```typescript
// 오픈 채팅
resolveRoomTypeLabel('open')        // → "Open"
resolveRoomTypeLabel('OpenChat')    // → "Open"
resolveRoomTypeLabel('OPEN_CHAT')   // → "Open"

// 그룹 채팅
resolveRoomTypeLabel('group')       // → "Group"
resolveRoomTypeLabel('GroupChat')   // → "Group"
resolveRoomTypeLabel('GROUP')       // → "Group"

// 1:1 채팅
resolveRoomTypeLabel('single')      // → "Single"
resolveRoomTypeLabel('SingleChat')  // → "Single"
resolveRoomTypeLabel('SINGLE')      // → "Single"

// 알 수 없는 타입
resolveRoomTypeLabel('unknown')     // → "Room"
resolveRoomTypeLabel('')            // → "Room"
resolveRoomTypeLabel(null)          // → "Room"
```

**사용 사례**:
```svelte
<!-- Svelte 컴포넌트: 채팅방 타입 배지 표시 -->
<script lang="ts">
  import { resolveRoomTypeLabel } from '$lib/functions/chat.functions';

  export let room: ChatRoom;
</script>

<div class="room-card">
  <span class="badge">{resolveRoomTypeLabel(room.type)}</span>
  <h3>{room.name}</h3>
</div>
```

## 4. 사용 방법

### 4.1. Svelte 클라이언트에서 사용

Svelte 클라이언트는 `src/lib/functions/chat.functions.ts`를 통해 사용합니다. 이 파일은 shared 함수들을 re-export합니다.

```typescript
// src/lib/functions/chat.functions.ts
import { ref, set, type Database } from 'firebase/database';

// Pure functions를 shared 폴더에서 import하고 re-export
export {
  buildSingleRoomId,
  isSingleChat,
  extractUidsFromSingleRoomId,
  resolveRoomTypeLabel
} from '$shared/chat.pure-functions';

// Firebase 의존 함수들은 여기에 유지
export async function joinChatRoom(db: Database, roomId: string, uid: string) {
  const memberRef = ref(db, `chat-rooms/${roomId}/members/${uid}`);
  await set(memberRef, true);
}
```

**컴포넌트에서 사용**:
```svelte
<script lang="ts">
  import {
    buildSingleRoomId,
    isSingleChat,
    extractUidsFromSingleRoomId,
    resolveRoomTypeLabel
  } from '$lib/functions/chat.functions';

  // 1:1 채팅방 생성
  async function startDirectChat(otherUserUid: string) {
    const roomId = buildSingleRoomId(currentUser.uid, otherUserUid);
    await createRoom(roomId);
    goto(`/chat/room?id=${roomId}`);
  }

  // 채팅방 타입 확인
  $: isDirectChat = isSingleChat(roomId);

  // UID 추출
  $: otherUserUid = (() => {
    if (!isDirectChat) return null;
    const uids = extractUidsFromSingleRoomId(roomId);
    if (!uids) return null;
    const [uid1, uid2] = uids;
    return uid1 === currentUser.uid ? uid2 : uid1;
  })();
</script>
```

### 4.2. Firebase Cloud Functions에서 사용

Firebase Functions는 shared 폴더에서 직접 import합니다.

```typescript
// firebase/functions/src/handlers/chat.handler.ts
import * as admin from 'firebase-admin';
import {
  isSingleChat,
  extractUidsFromSingleRoomId
} from '../../../../shared/chat.pure-functions';

export async function handleChatMessageCreate(
  roomId: string,
  messageId: string,
  data: any
): Promise<void> {
  // 1:1 채팅방인지 확인
  if (isSingleChat(roomId)) {
    // UID 추출
    const uids = extractUidsFromSingleRoomId(roomId);

    if (uids) {
      const [uid1, uid2] = uids;
      const recipientUid = data.senderUid === uid1 ? uid2 : uid1;

      // 상대방에게만 알림 발송
      await sendPushNotification(recipientUid, {
        title: '새 메시지',
        body: data.text
      });
    }
  } else {
    // 그룹 채팅 로직
    await sendGroupNotification(roomId, data);
  }
}
```

### 4.3. TypeScript 설정

**Svelte (svelte.config.js)**:
```javascript
export default {
  kit: {
    alias: {
      $shared: './shared'
    }
  }
};
```

**Firebase Functions (tsconfig.json)**:
```json
{
  "compilerOptions": {
    "paths": {
      "@shared/*": ["../../shared/*"]
    },
    "rootDirs": ["./src", "../../shared"]
  },
  "include": ["src", "../../shared/**/*.ts"]
}
```

**Vite (vite.config.ts)**:
```typescript
export default defineConfig({
  server: {
    fs: {
      allow: ['..']  // shared 폴더 접근 허용
    }
  }
});
```

## 5. 설계 원칙

### 5.1. Pure Functions Only

```typescript
// ✅ 올바른 예시 - 순수 문자열 처리
export function buildSingleRoomId(a: string, b: string): string {
  return `single-${[a, b].sort().join('-')}`;
}

// ❌ 잘못된 예시 - Firebase 의존
export async function createSingleRoom(a: string, b: string) {
  const roomId = buildSingleRoomId(a, b);
  await set(ref(db, `chat-rooms/${roomId}`), { /* ... */ });  // ❌
}
```

### 5.2. 입력 검증

```typescript
// ✅ 올바른 예시 - null 안전성
export function extractUidsFromSingleRoomId(roomId: string): [string, string] | null {
  const parts = roomId.split('-');
  if (parts.length !== 3 || parts[0] !== 'single') {
    return null;  // 유효하지 않으면 null 반환
  }
  return [parts[1], parts[2]];
}

// ❌ 잘못된 예시 - 검증 없이 접근
export function extractUidsFromSingleRoomId(roomId: string): [string, string] {
  const parts = roomId.split('-');
  return [parts[1], parts[2]];  // ❌ parts가 3개 미만이면 undefined 반환
}
```

### 5.3. Zero External Dependencies

```typescript
// ✅ 허용: 표준 JavaScript API만 사용
const sorted = [a, b].sort();
const parts = roomId.split('-');
const lower = roomType.toLowerCase();

// ❌ 금지: 외부 라이브러리
import { ref } from 'firebase/database';  // ❌
import { getLocale } from '$lib/paraglide/runtime.js';  // ❌
```

## 6. 테스트 전략

### 6.1. 단위 테스트

```typescript
// shared/chat.pure-functions.test.ts
import { describe, it, expect } from 'vitest';
import {
  buildSingleRoomId,
  isSingleChat,
  extractUidsFromSingleRoomId,
  resolveRoomTypeLabel
} from './chat.pure-functions';

describe('buildSingleRoomId', () => {
  it('should create consistent roomId regardless of order', () => {
    const id1 = buildSingleRoomId('alice', 'bob');
    const id2 = buildSingleRoomId('bob', 'alice');

    expect(id1).toBe(id2);
    expect(id1).toBe('single-alice-bob');
  });

  it('should sort UIDs alphabetically', () => {
    const result = buildSingleRoomId('zebra', 'apple');
    expect(result).toBe('single-apple-zebra');
  });
});

describe('isSingleChat', () => {
  it('should return true for single chat', () => {
    expect(isSingleChat('single-alice-bob')).toBe(true);
  });

  it('should return false for group chat', () => {
    expect(isSingleChat('group-abc123')).toBe(false);
  });
});

describe('extractUidsFromSingleRoomId', () => {
  it('should extract UIDs from valid roomId', () => {
    const result = extractUidsFromSingleRoomId('single-alice-bob');
    expect(result).toEqual(['alice', 'bob']);
  });

  it('should return null for invalid format', () => {
    expect(extractUidsFromSingleRoomId('invalid')).toBeNull();
    expect(extractUidsFromSingleRoomId('single-alice')).toBeNull();
    expect(extractUidsFromSingleRoomId('group-alice-bob')).toBeNull();
  });
});

describe('resolveRoomTypeLabel', () => {
  it('should resolve open chat types', () => {
    expect(resolveRoomTypeLabel('open')).toBe('Open');
    expect(resolveRoomTypeLabel('OpenChat')).toBe('Open');
  });

  it('should return "Room" for unknown types', () => {
    expect(resolveRoomTypeLabel('unknown')).toBe('Room');
    expect(resolveRoomTypeLabel('')).toBe('Room');
  });
});
```

## 7. 버전 관리 및 변경 이력

| 버전  | 날짜       | 변경 내용                                          |
| ----- | ---------- | -------------------------------------------------- |
| 1.0.0 | 2025-11-12 | 최초 작성: 채팅 순수 함수 분리 및 shared 모듈 생성 |

## 8. 관련 문서

- [Sonub Functions Overview](./sonub-functions-overview.md) - Functions 아키텍처 개요
- [Sonub Functions Chat Functions](./sonub-functions-chat-functions.md) - Svelte wrapper 및 Firebase 함수
- [Sonub Shared Date Pure Functions](./sonub-shared-date-pure-functions.md) - 날짜 순수 함수
- [Sonub Firebase Database Structure](./sonub-firebase-database-structure.md) - 채팅방 데이터 구조
- [Sonub Project Overview](./sonub-project-overview.md) - 프로젝트 전체 개요
