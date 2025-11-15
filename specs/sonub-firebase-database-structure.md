---
name: sonub-firestore-database-structure
version: 2.0.0
description: Cloud Firestore 데이터 구조 및 책임 분리 가이드
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
dependencies:
  - sonub-firebase-functions-index.md
  - sonub-firebase-security-rules.md
  - sonub-chat-room.md
tags:
  - firestore
  - database
  - schema
  - chat
---

# Sonub Firestore Database 구조 가이드

## 1. 개요

2025년 11월 마이그레이션 이후 Sonub 프로젝트는 **Firebase Realtime Database를 더 이상 사용하지 않고 Cloud Firestore만 사용**합니다. 이 문서는 현재 코드베이스(예: `firebase/firestore.rules`, `src/lib/types/firestore.types.ts`, `firebase/functions/src/handlers/user.handler.ts`, `src/lib/functions/chat.functions.ts`)를 기준으로 실제로 존재하는 컬렉션/문서 구조와 책임 분리를 정리합니다.

## 2. 전체 구조

```
Cloud Firestore
├── users/{uid}
│   ├── chat-joins/{roomId}
│   ├── chat-invitations/{roomId}
│   └── chat-favorites/{favoriteId}
├── chats/{roomId}
│   ├── messages/{messageId}
│   └── members/{uid}
├── chat-room-passwords/{roomId}
│   └── tries/{uid}
├── fcm-tokens/{token}
├── system/stats
└── system/settings
```

각 컬렉션은 Firestore 규칙(`firebase/firestore.rules`)과 Cloud Functions(`firebase/functions/src/index.ts`)에서 동일한 경로로 참조됩니다.

## 3. users/{uid}

- **정의**: 사용자 기본 프로필 문서. `src/lib/types/firestore.types.ts`의 `UserData` 인터페이스와 Cloud Functions `handleUserCreate`/`handleUserBirthYearMonthDayUpdate`가 사용하는 필드 목록을 그대로 사용합니다.
- **주요 필드**
  | 필드 | 설명 | 생성 책임 |
  |------|------|-----------|
  | `uid` | 문서 ID와 동일 | 클라이언트 (`src/lib/stores/auth.svelte.ts`) |
  | `displayName`, `photoUrl`, `bio` 등 | 사용자가 입력하거나 OAuth에서 받아오는 정보 | 클라이언트 |
  | `displayNameLowerCase`, `sort_recent*` | 검색/정렬용 파생 필드 | Cloud Functions `firebase/functions/src/handlers/user.handler.ts` |
  | `birthYear`, `birthMonth`, `birthDay`, `birthMonthDay` | `birthYearMonthDay` 값에서 파생 | Cloud Functions |
  | `createdAt`, `updatedAt` | 최초 가입 및 주요 필드 변경 시 자동 설정 | Cloud Functions |

- **설명**: `src/lib/stores/auth.svelte.ts`는 로그인 직후 Firebase Auth 프로필을 Firestore로 동기화합니다. 파생 필드와 통계 업데이트는 `firebase/functions/src/handlers/user.handler.ts`가 처리합니다.

### 3.1 users/{uid}/chat-joins/{roomId}

- **용도**: 채팅 목록(좌측 사이드바) 렌더링. 실제 컴포넌트는 `src/lib/components/FirestoreListView.svelte`를 통해 `/users/{uid}/chat-joins` 경로를 구독합니다(`src/routes/chat/room/+layout.svelte`).
- **주요 필드** (`ChatJoinData` 타입 참조):
  - `roomId`, `roomType`, `partnerUid`
  - `newMessageCount`, `pin`, `lastMessageText`, `lastMessageAt`
  - 정렬 필드: `singleChatListOrder`, `groupChatListOrder`, `allChatListOrder`
- **책임 분리**:
  - 클라이언트(`src/lib/functions/chat.functions.ts`)는 입장 시 `roomId`, `roomType`, `newMessageCount`만 직접 설정합니다.
  - Cloud Functions(예: `onChatMessageCreate`, `onChatJoinCreate`)가 정렬 필드와 캐시 필드를 자동 유지합니다.

### 3.2 users/{uid}/chat-invitations/{roomId}

- **용도**: 채팅 초대장 관리. `ChatInvitationData` 타입 참조.
- **필드**: `roomId`, `inviterUid`, `status`, `message`, `createdAt`, `roomName`, `roomType`.
- **책임**: 초대 생성은 클라이언트에서 수행하되, 상태 전이는 Cloud Functions가 검증합니다(향후 확장 예정).

### 3.3 users/{uid}/chat-favorites/{favoriteId}

- **용도**: 즐겨찾기 폴더. `ChatFavoriteData` 타입 참조.
- **필드**: `name`, `folderOrder`, `roomList`.
- **비고**: `folderOrder`는 Cloud Functions가 prefix를 반영해 정렬합니다.

## 4. chats/{roomId}

- **정의**: 채팅방 메타데이터. `ChatRoomData` 타입 참조.
- **주요 필드**: `type` (`single|group|open`), `owner`, `name`, `description`, `password` 플래그, `memberCount`, `members` 맵, `lastMessageText/At`.
- **생성 책임**:
  - 1:1 채팅방은 `buildSingleRoomId`(공유 함수)로 ID를 계산한 뒤, Cloud Functions가 자동으로 메타데이터를 동기화합니다.
  - 그룹/오픈 채팅 생성은 클라이언트에서 수행하되(`src/lib/functions/chat.functions.ts`), Firestore 규칙이 `type` 검증을 수행합니다.

### 4.1 chats/{roomId}/messages/{messageId}

- **용도**: 실제 메시지 저장. `ChatMessageData` 타입 참조.
- **필드**: `senderUid`, `text`, `type`, `urls[]`, `createdAt`, `updatedAt`, `deletedAt`.
- **책임**:
  - 메시지 생성/수정은 `src/routes/chat/room/+page.svelte`의 `FirestoreListView`를 통해 이루어집니다.
  - Cloud Functions는 새 메시지를 감지해 `chat-joins` 정렬 필드와 lastMessage 캐시, `newMessageCount`를 업데이트합니다 (사양: `specs/sonub-chat-room.md`, `firebase/functions/src/index.ts`).

### 4.2 chats/{roomId}/members/{uid}

- **용도**: 그룹/오픈 채팅 멤버십. `joinChatRoom`/`leaveChatRoom` 함수가 Firestore 문서를 생성하거나 삭제합니다.
- **필드**: `{ value: true | false }` — 알림 구독 여부를 나타내며 Firestore 규칙에서 존재 여부를 통해 권한을 확인합니다.

## 5. chat-room-passwords/{roomId}

- **필드**: `password` (Plain text 저장 후 Cloud Functions에서 해시 예정). 타입 정의는 `src/lib/types/firestore.types.ts` 참고.
- **관련 서브컬렉션**: `chat-room-passwords/{roomId}/tries/{uid}` — 사용자가 입력한 비밀번호 및 `triedAt`을 기록. Firestore 규칙(`firebase/firestore.rules:321`)에서 시도 횟수를 제어합니다.
- **사용 위치**: `src/lib/components/chat/room-password-setting.svelte`, `RoomPasswordPrompt.svelte`.

## 6. fcm-tokens/{token}

- **생성**: `src/lib/fcm.ts`에서 `saveFcmTokenToDatabase()`를 통해 토큰 문자열을 문서 ID로 사용.
- **필드**: `device: 'web'`, `uid?: string` (로그인 사용자일 경우만 포함).
- **쓰임**: Cloud Functions 또는 Cloud Messaging에서 푸시 발송 대상을 결정할 때 사용.

## 7. system 컬렉션

### 7.1 system/stats

- **필드**: 현재 `userCount`만 사용 (`src/routes/stats/+page.svelte`, `src/lib/components/right-sidebar.svelte`).
- **업데이트**: Cloud Functions `handleUserCreate`가 사용자 가입 시 `admin.firestore.FieldValue.increment(1)`로 증가시킵니다.

### 7.2 system/settings

- **필드**: `admins` 객체 (`{ [uid: string]: true }`).
- **사용처**: `src/lib/stores/auth.svelte.ts`의 `loadAdminList()`가 이 문서를 읽어 관리자 UID 배열을 생성합니다. 보안 규칙은 `sonub-firebase-security-rules.md`를 참고합니다.

## 8. 책임 분리 정리

| 데이터 | 클라이언트 책임 | Cloud Functions 책임 | 관련 파일 |
|--------|----------------|----------------------|-----------|
| 사용자 기본 필드 | OAuth에서 받은 `displayName`, `photoUrl`, `bio`, `languageCode` 저장 | `createdAt`, `displayNameLowerCase`, `birthYear*`, `sort_recent*`, `userCount` 업데이트 | `src/lib/stores/auth.svelte.ts`, `firebase/functions/src/handlers/user.handler.ts` |
| 채팅 참여 (`chat-joins`) | 입장/퇴장 시 `roomId`, `roomType`, `newMessageCount` 갱신 | 정렬 필드, lastMessage 캐시, 읽지 않은 메시지 카운트 유지 | `src/lib/functions/chat.functions.ts`, `firebase/functions/src/index.ts` |
| 채팅 메시지 | 메시지 CRUD, 파일 업로드, 첨부 URL 기록 | 새 메시지 감지 후 `chat-joins` 업데이트, memberCount 관리 | `src/routes/chat/room/+page.svelte`, `firebase/functions/src/index.ts` |
| 시스템 통계 | 읽기만 수행 (`right-sidebar`, `/stats` 페이지) | `system/stats.userCount` 증가 | `src/routes/stats/+page.svelte`, `firebase/functions/src/handlers/user.handler.ts` |
| 관리자 목록 | 없음 | Firestore 문서 수동 관리 (관리자만 쓰기) | `src/lib/stores/auth.svelte.ts` |

## 9. 참고 문서

- Firestore 규칙: `firebase/firestore.rules`
- 타입 정의: `src/lib/types/firestore.types.ts`
- 채팅 클라이언트 로직: `src/routes/chat/room/+page.svelte`, `src/lib/functions/chat.functions.ts`
- Cloud Functions: `firebase/functions/src/index.ts`, `firebase/functions/src/handlers/user.handler.ts`
- 통계 UI: `src/lib/components/right-sidebar.svelte`, `src/routes/stats/+page.svelte`

이 문서는 Firestore 소스 코드와의 싱크를 유지해야 합니다. 새로운 컬렉션이나 서브컬렉션을 추가할 때는 Firestore 규칙과 Cloud Functions 구현을 먼저 확정한 후 본 문서를 업데이트해 주세요.
