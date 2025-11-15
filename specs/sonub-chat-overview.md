---
name: sonub-chat-overview
version: 2.0.0
description: Firestore 기반 채팅 기능 개요
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-firebase-database-structure.md
  - sonub-chat-room.md
tags:
  - chat
  - firestore
  - messaging
---

# 채팅 시스템 개요

## 1. 주요 컬렉션

| 경로 | 용도 |
|------|------|
| `chats/{roomId}` | 채팅방 메타데이터 (`type`, `owner`, `name`, `memberCount`, `lastMessageText/At`, `members` 맵 등) |
| `chats/{roomId}/messages/{messageId}` | 메시지 본문 (`senderUid`, `text`, `urls`, `createdAt`, `deletedAt`) |
| `users/{uid}/chat-joins/{roomId}` | 참여 중인 채팅방 목록 (`newMessageCount`, 정렬 필드, 캐시) |
| `users/{uid}/chat-invitations/{roomId}` | 초대장 |
| `users/{uid}/chat-favorites/{favoriteId}` | 즐겨찾기 폴더 |
| `chat-room-passwords/{roomId}` | 비밀번호 보호 채팅방 설정 (owner 전용) |

모든 컬렉션 이름은 kebab-case로 표기하며, Firestore rules에서 동일한 경로를 사용합니다.

## 2. 클라이언트 컴포넌트

- `/chat/room/+layout.svelte`: 사이드바(`FirestoreListView`) + 메인 영역
- `/chat/room/+page.svelte`: 메시지 스트림, 입력창, 첨부 파일, 비밀번호 설정/검증
- `src/lib/functions/chat.functions.ts`: `enterSingleChatRoom`, `joinChatRoom`, `leaveChatRoom`, `togglePinChatRoom` 등 Firestore 조작 함수
- `src/lib/components/FirestoreListView.svelte`: 채팅 목록/메시지 무한 스크롤 공용 컴포넌트

## 3. Cloud Functions 역할

- `firebase/functions/src/index.ts`
  - 메시지 생성 시 `chat-joins` 정렬 필드/lastMessage 캐시 갱신
  - `newMessageCount` 증가 및 읽음 처리
  - 채팅방 참여/퇴장 시 `memberCount`와 멤버 상태 유지
- `firebase/functions/src/handlers/chat.password-verification.handler.ts`: `chat-room-passwords/{roomId}/tries/{uid}`를 감시해 비밀번호 검증 후 결과를 업데이트

## 4. 비밀번호 보호 흐름

1. 방 소유자가 `chat-room-passwords/{roomId}` 문서에 `password` 필드를 저장
2. 방에 입장하려는 사용자는 `/components/chat/room-password-prompt.svelte`에서 비밀번호 입력
3. 입력 값은 `chat-room-passwords/{roomId}/tries/{uid}`에 기록 → Cloud Functions가 비교 후 성공 여부를 저장
4. 성공 시 클라이언트가 `joinChatRoom` 호출, 실패 시 에러 메시지 표시

## 5. FirestoreRule 개요

`firebase/firestore.rules` 요약:
- `chats/{roomId}`: 읽기 → 로그인 사용자, 쓰기 → owner 또는 멤버 조건
- `chats/{roomId}/messages/{messageId}`: 방 참가자만 읽기/쓰기
- `users/{uid}/chat-joins/{roomId}`: 본인만
- `chat-room-passwords/{roomId}`: owner 또는 관리자만 읽기/쓰기, `/tries/{uid}`는 해당 사용자만 쓰기

## 6. 참고 문서

- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md)
- [sonub-chat-room.md](./sonub-chat-room.md)
- [sonub-firebase-security-rules.md](./sonub-firebase-security-rules.md)
