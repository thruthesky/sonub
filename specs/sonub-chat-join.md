---
name: sonub-chat-join
version: 1.0.0
description: Firebase Realtime Database chat-joins 노드 구조 및 동작 원리 상세 가이드
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
dependencies: []
---

- [채팅방 참여 정보 (chat-joins) 가이드](#채팅방-참여-정보-chat-joins-가이드)
  - [워크플로우](#워크플로우)
  - [개요](#개요)
  - [데이터베이스 구조](#데이터베이스-구조)
    - [경로 구조](#경로-구조)
    - [필드 상세 설명](#필드-상세-설명)
  - [주요 필드 상세 설명](#주요-필드-상세-설명)
    - [1. partnerUid (1:1 채팅 상대방 UID)](#1-partneruid-11-채팅-상대방-uid)
    - [2. roomType (채팅방 유형)](#2-roomtype-채팅방-유형)
    - [3. listOrder (정렬 필드)](#3-listorder-정렬-필드)
    - [4. newMessageCount (읽지 않은 메시지 개수)](#4-newmessagecount-읽지-않은-메시지-개수)
    - [5. joinedAt (채팅방 참여 시간)](#5-joinedat-채팅방-참여-시간)
  - [클라이언트/서버 역할 분리](#클라이언트서버-역할-분리)
  - [Cloud Functions 동작 흐름](#cloud-functions-동작-흐름)
    - [1. 메시지 생성 시 (onChatMessageCreate)](#1-메시지-생성-시-onchatmessagecreate)
    - [2. chat-joins 노드 생성 시 (onChatJoinCreate)](#2-chat-joins-노드-생성-시-onchatjoincreate)
  - [클라이언트 구현 예시](#클라이언트-구현-예시)
    - [채팅방 목록 조회](#채팅방-목록-조회)
    - [읽음 처리 (채팅방 입장 시)](#읽음-처리-채팅방-입장-시)
    - [PIN 고정/해제](#pin-고정해제)
  - [관련 문서](#관련-문서)

## Overview
- 이 문서는 Firebase Realtime Database의 `chat-joins` 노드 구조와 동작 원리를 상세하게 설명합니다.
- `chat-joins`는 각 사용자가 참여한 채팅방 목록을 관리하며, 채팅방 목록 화면에서 사용됩니다.

## Requirements
- Firebase Realtime Database 기본 이해
- Firebase Cloud Functions 기본 이해
- TypeScript 기본 문법 이해
- [Firebase Realtime Database 구조 가이드](./sonub-firebase-database-structure.md) 문서 숙지

## Workflow
1. 사용자가 채팅 메시지를 전송하면 클라이언트는 `/chat-messages/{messageId}` 노드에 메시지를 저장합니다.
2. Firebase Cloud Functions의 `onChatMessageCreate` 트리거가 메시지 생성을 감지합니다.
3. Cloud Functions가 자동으로 `/chat-joins/{uid}/{roomId}` 노드를 생성/업데이트합니다.
4. `onChatJoinCreate` 트리거가 chat-joins 노드 생성을 감지하여 `joinedAt` 필드를 추가합니다.
5. 클라이언트는 `/chat-joins/{uid}/` 경로를 구독하여 채팅방 목록을 실시간으로 표시합니다.

## Detail Items

# 채팅방 참여 정보 (chat-joins) 가이드

## 워크플로우

1. **메시지 전송**: 사용자가 채팅 메시지를 전송
2. **Cloud Functions 트리거**: `onChatMessageCreate`가 메시지 생성 감지
3. **chat-joins 생성/업데이트**: Cloud Functions가 자동으로 양쪽 사용자의 chat-joins 노드 생성/업데이트
4. **joinedAt 추가**: `onChatJoinCreate` 트리거가 `joinedAt` 필드 추가
5. **실시간 동기화**: 클라이언트가 채팅방 목록을 실시간으로 표시

## 개요

채팅방 참여 정보(`chat-joins`)는 각 사용자가 참여한 채팅방 목록을 관리하는 데이터 구조입니다.

**주요 특징:**
- 경로: `/chat-joins/{uid}/{roomId}/`
- 각 사용자별로 참여한 모든 채팅방 정보를 저장
- 채팅방 목록 화면에서 사용
- Cloud Functions가 자동으로 생성/업데이트
- 클라이언트는 읽음 처리 및 PIN 기능만 직접 수정

## 데이터베이스 구조

### 경로 구조

```
/chat-joins/
├── {uid1}/
│   ├── {roomId1}/
│   │   ├── roomId: string
│   │   ├── roomType: "single" | "group" | "open"
│   │   ├── partnerUid: string (1:1 채팅만)
│   │   ├── lastMessageText: string
│   │   ├── lastMessageAt: number
│   │   ├── joinedAt: number
│   │   ├── updatedAt: number
│   │   ├── listOrder: string
│   │   └── newMessageCount: number
│   └── {roomId2}/
│       └── ...
└── {uid2}/
    └── ...
```

### 필드 상세 설명

| 필드 | 타입 | 필수 | 설명 | 자동 생성 |
|------|------|------|------|----------|
| `roomId` | string | ✅ | 채팅방 ID | onChatMessageCreate |
| `roomType` | string | ✅ | 채팅방 유형 (single, group, open) | onChatMessageCreate |
| `partnerUid` | string | ❌ | 1:1 채팅의 상대방 UID (1:1 채팅만) | onChatMessageCreate |
| `lastMessageText` | string | ❌ | 마지막 메시지 내용 (미리보기용) | onChatMessageCreate |
| `lastMessageAt` | number | ✅ | 마지막 메시지 시간 (Unix timestamp, 밀리초) | onChatMessageCreate |
| `joinedAt` | number | ✅ | 채팅방 참여 시간 (Unix timestamp, 밀리초) | onChatJoinCreate |
| `updatedAt` | number | ✅ | 마지막 업데이트 시간 (Unix timestamp, 밀리초) | onChatMessageCreate |
| `listOrder` | string | ✅ | 정렬 필드 (prefix + timestamp) | onChatMessageCreate |
| `newMessageCount` | number | ✅ | 읽지 않은 메시지 개수 | onChatMessageCreate |

## 주요 필드 상세 설명

### 1. partnerUid (1:1 채팅 상대방 UID)

**목적**: 1:1 채팅에서 상대방의 UID를 저장하여 빠르게 상대방 프로필을 조회할 수 있도록 합니다.

**특징:**
- **1:1 채팅만 해당**: `roomType`이 `"single"`일 때만 저장됨
- **그룹/오픈 채팅에서는 없음**: `roomType`이 `"group"` 또는 `"open"`일 때는 `null` 또는 저장되지 않음
- **자동 계산**: Cloud Functions가 자동으로 상대방 UID를 계산하여 저장

**사용 예시:**
```typescript
// 1:1 채팅방 목록에서 상대방 프로필 조회
const partnerUid = chatJoin.partnerUid;
const partnerProfile = await database.ref(`users/${partnerUid}`).once('value');
```

**데이터 예시:**
```json
{
  "chat-joins": {
    "uid1": {
      "single-uid1-uid2": {
        "roomId": "single-uid1-uid2",
        "roomType": "single",
        "partnerUid": "uid2"  // ← uid1의 상대방은 uid2
      }
    },
    "uid2": {
      "single-uid1-uid2": {
        "roomId": "single-uid1-uid2",
        "roomType": "single",
        "partnerUid": "uid1"  // ← uid2의 상대방은 uid1
      }
    }
  }
}
```

### 2. roomType (채팅방 유형)

**목적**: 채팅방 유형을 구분하여 UI 및 로직을 다르게 처리할 수 있도록 합니다.

**가능한 값:**
- `"single"`: 1:1 채팅 (두 명만 참여)
- `"group"`: 그룹 채팅 (여러 명 참여, 비공개)
- `"open"`: 오픈 채팅 (누구나 참여 가능, 공개)

**특징:**
- **자동 설정**: Cloud Functions가 `roomId` 형식을 분석하여 자동으로 설정
- **UI 분기**: 클라이언트에서 `roomType`에 따라 다른 UI를 표시

**roomId 형식과 roomType 관계:**
| roomId 형식 | roomType | 예시 |
|-------------|----------|------|
| `single-{uid1}-{uid2}` | `"single"` | `single-abc123-xyz789` |
| `group-{roomId}` | `"group"` | `group-team123` |
| `open-{roomId}` | `"open"` | `open-general` |

**사용 예시:**
```typescript
// UI 분기
if (chatJoin.roomType === 'single') {
  // 1:1 채팅: 상대방 프로필 표시
  showPartnerProfile(chatJoin.partnerUid);
} else if (chatJoin.roomType === 'group') {
  // 그룹 채팅: 그룹 이름 표시
  showGroupName(chatJoin.roomId);
} else if (chatJoin.roomType === 'open') {
  // 오픈 채팅: 공개 채팅방 아이콘 표시
  showOpenChatIcon();
}
```

### 3. listOrder (정렬 필드)

**목적**: 채팅방 목록을 최신 메시지 순으로 정렬하고, 읽지 않은 메시지/PIN 상태를 구분합니다.

**형식**: `prefix + timestamp` (문자열)

**Prefix 규칙:**
| Prefix | 상태 | 설명 | 예시 값 |
|--------|------|------|---------|
| 없음 | 읽음 상태 | 메시지를 읽은 채팅방 | `1698473000000` |
| `200` | 읽지 않음 | 읽지 않은 메시지가 있는 채팅방 | `2001698473000000` |
| `500` | PIN 고정 | 사용자가 고정한 채팅방 | `5001698473000000` |

**정렬 순서** (reverse() 사용 시):
```
"5001698474000000"  (PIN 고정)          ← 최상위
"2001698473000000"  (읽지 않은 메시지)  ← 맨 위
"1698471000000"     (읽음 상태)         ← 맨 아래
```

**상세 설명**: [Firebase Realtime Database 구조 가이드 - listOrder 필드](./sonub-firebase-database-structure.md#🔥-listorder-필드-상세-설명) 참조

### 4. newMessageCount (읽지 않은 메시지 개수)

**목적**: 사용자가 읽지 않은 메시지 개수를 표시합니다.

**특징:**
- **자동 증가**: Cloud Functions가 메시지 생성 시 수신자의 `newMessageCount`를 자동으로 증가 (`increment(1)`)
- **발신자는 증가 안 함**: 메시지를 보낸 사람의 `newMessageCount`는 증가하지 않음 (읽음 상태이므로)
- **클라이언트가 0으로 초기화**: 사용자가 채팅방에 입장하면 클라이언트가 `newMessageCount`를 0으로 초기화

**동작 흐름:**
1. 사용자 A가 사용자 B에게 메시지 전송
2. Cloud Functions가 B의 `chat-joins/{B-uid}/{roomId}/newMessageCount`를 `increment(1)`
3. B가 채팅방 목록에서 읽지 않은 메시지 배지 확인: `newMessageCount: 1`
4. B가 채팅방에 입장하면 클라이언트가 `newMessageCount`를 0으로 초기화
5. 채팅방 목록에서 배지가 사라짐

### 5. joinedAt (채팅방 참여 시간)

**목적**: 사용자가 채팅방에 처음 참여한 시간을 기록합니다.

**특징:**
- **자동 생성**: Cloud Functions의 `onChatJoinCreate` 트리거가 자동으로 생성
- **최초 생성 시에만 설정**: 이미 `joinedAt`이 있으면 업데이트하지 않음
- **변경되지 않음**: 한 번 설정된 후에는 변경되지 않음

**동작 흐름:**
1. `onChatMessageCreate`가 `chat-joins/{uid}/{roomId}` 노드 생성 (단, `joinedAt` 제외)
2. `onChatJoinCreate` 트리거가 `chat-joins/{uid}/{roomId}` 노드 생성 감지
3. `joinedAt` 필드가 없으면 현재 타임스탬프로 설정
4. 이후 메시지가 생성되어도 `joinedAt`은 변경되지 않음

## 클라이언트/서버 역할 분리

**클라이언트의 역할:**
- ❌ `chat-joins` 노드를 직접 생성하지 않음
- ✅ 채팅방 입장 시 `listOrder`의 200 prefix 제거 (읽음 처리)
- ✅ 채팅방 입장 시 `newMessageCount`를 0으로 초기화
- ✅ 채팅방 PIN 고정/해제 시 `listOrder`의 500 prefix 추가/제거

**서버(Cloud Functions)의 역할:**
- ✅ 메시지 생성 시 `chat-joins` 노드 자동 생성/업데이트
- ✅ `roomId`, `roomType`, `partnerUid` 자동 설정
- ✅ `lastMessageText`, `lastMessageAt`, `updatedAt` 자동 업데이트
- ✅ `listOrder` 자동 계산 및 업데이트 (발신자: timestamp, 수신자: 200+timestamp)
- ✅ `newMessageCount` 자동 증가 (수신자만 increment(1))
- ✅ `joinedAt` 자동 생성 (최초 생성 시에만)

## Cloud Functions 동작 흐름

### 1. 메시지 생성 시 (onChatMessageCreate)

**트리거 경로**: `/chat-messages/{messageId}`

**수행 작업:**
1. 프로토콜 메시지 건너뛰기
2. 필수 필드 유효성 검사 (`senderUid`, `roomId`)
3. 1:1 채팅 감지 (`isSingleChat` 함수 사용)
4. 양쪽 사용자의 `chat-joins` 노드 생성/업데이트:
   - **발신자**: `listOrder` = timestamp (읽음 상태)
   - **수신자**: `listOrder` = 200+timestamp (읽지 않은 상태), `newMessageCount` = increment(1)

**코드 참조**: [firebase/functions/src/handlers/chat.handler.ts](../firebase/functions/src/handlers/chat.handler.ts)

### 2. chat-joins 노드 생성 시 (onChatJoinCreate)

**트리거 경로**: `/chat-joins/{uid}/{roomId}`

**수행 작업:**
1. `joinedAt` 필드 확인
2. `joinedAt`이 없으면 현재 타임스탬프로 설정
3. 이미 있으면 건너뜀

**코드 참조**: [firebase/functions/src/handlers/chat.handler.ts](../firebase/functions/src/handlers/chat.handler.ts)

## 클라이언트 구현 예시

### 채팅방 목록 조회

```typescript
import { ref, query, orderByChild, limitToLast, onValue } from 'firebase/database';

// 채팅방 목록 조회 (내림차순 정렬: PIN → 읽지 않음 → 읽음)
const chatJoinsRef = ref(database, `chat-joins/${uid}`);
const chatJoinsQuery = query(
  chatJoinsRef,
  orderByChild('listOrder'),
  limitToLast(20)
);

onValue(chatJoinsQuery, (snapshot) => {
  const chatRooms: ChatJoin[] = [];
  snapshot.forEach((child) => {
    chatRooms.push({
      roomId: child.key,
      ...child.val()
    });
  });

  // reverse()로 내림차순 변환 (큰 값부터)
  chatRooms.reverse();

  // 정렬 순서:
  // 1. PIN 고정 (500 prefix)
  // 2. 읽지 않음 (200 prefix)
  // 3. 읽음 (prefix 없음)
  console.log(chatRooms);
});
```

### 읽음 처리 (채팅방 입장 시)

```typescript
import { ref, get, set } from 'firebase/database';

// 사용자가 채팅방 입장 시
const currentListOrderRef = ref(database, `chat-joins/${uid}/${roomId}/listOrder`);
const currentListOrderSnapshot = await get(currentListOrderRef);
const currentListOrder = currentListOrderSnapshot.val();

// 200 prefix 제거 (읽음 처리)
if (currentListOrder?.startsWith('200')) {
  const newListOrder = currentListOrder.substring(3);
  await set(ref(database, `chat-joins/${uid}/${roomId}/listOrder`), newListOrder);

  // 읽지 않은 메시지 카운터도 0으로 초기화
  await set(ref(database, `chat-joins/${uid}/${roomId}/newMessageCount`), 0);
}

// PIN 채팅방은 prefix 제거하지 않음
if (currentListOrder?.startsWith('500')) {
  // 500 prefix는 유지 (항상 맨 위에 고정)
  // newMessageCount만 0으로 초기화
  await set(ref(database, `chat-joins/${uid}/${roomId}/newMessageCount`), 0);
}
```

### PIN 고정/해제

```typescript
import { ref, get, set } from 'firebase/database';

// 채팅방 고정
async function pinChatRoom(uid: string, roomId: string) {
  const listOrderRef = ref(database, `chat-joins/${uid}/${roomId}/listOrder`);
  const currentListOrder = (await get(listOrderRef)).val();

  if (!currentListOrder.startsWith('500')) {
    const pinnedListOrder = `500${currentListOrder}`;
    await set(listOrderRef, pinnedListOrder);
  }
}

// 채팅방 고정 해제
async function unpinChatRoom(uid: string, roomId: string) {
  const listOrderRef = ref(database, `chat-joins/${uid}/${roomId}/listOrder`);
  const currentListOrder = (await get(listOrderRef)).val();

  if (currentListOrder.startsWith('500')) {
    const unpinnedListOrder = currentListOrder.substring(3);
    await set(listOrderRef, unpinnedListOrder);
  }
}
```

## 관련 문서

- **📖 [Firebase Realtime Database 구조 가이드](./sonub-firebase-database-structure.md)** - 전체 데이터베이스 구조 및 chat-joins 필드 설명
- **📖 [채팅 기능 개발 가이드](./sonub-chat-room.md)** - 채팅방 생성, 메시지 전송, 실시간 메시지 수신
- **📖 [Firebase Cloud Functions 개발 가이드](./sonub-firebase-functions.md)** - Cloud Functions 구현 상세 가이드
