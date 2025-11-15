---
name: sonub-chat-message
title: 채팅 메시지 수정 및 삭제 기능 명세
version: 1.0.0
description: 채팅방에서 사용자가 자신이 전송한 메시지를 수정하고 삭제할 수 있는 기능
author: Claude Code
email: noreply@anthropic.com
license: GPL-3.0
created: 2025-11-15
updated: 2025-11-15
step: 40
priority: "*"
dependencies:
  - sonub-chat-room.md
  - sonub-functions-storage.md
tags:
  - chat
  - message
  - edit
  - delete
---

# 채팅 메시지 수정 및 삭제 기능 명세

채팅방에서 사용자가 자신이 전송한 메시지를 수정하고 삭제할 수 있는 기능을 제공합니다.

---

## 1. 기능 개요

### 1.1 목적

- 사용자가 잘못 보낸 메시지를 수정하거나 삭제할 수 있도록 함
- 90분(5,400초) 이내 전송한 메시지만 수정/삭제 가능
- 삭제된 메시지는 Soft Delete 방식으로 처리하여 메시지 이력 유지

### 1.2 주요 기능

1. **메시지 수정**: 텍스트 내용과 첨부파일을 수정할 수 있음
2. **메시지 삭제**: 메시지를 삭제하면 "삭제된 메시지입니다" 표시
3. **시간 제한**: 90분 경과 메시지는 수정/삭제 불가
4. **보안 규칙**: Firebase Security Rules로 서버 측에서 검증

---

## 2. UI/UX

### 2.1 메시지 행 (message-row)

채팅방 페이지 (`/src/routes/chat/room/+page.svelte`)의 `DatabaseListView` 컴포넌트에서 각 메시지를 `message-row` snippet으로 렌더링합니다.

#### 2.1.1 설정 아이콘 표시 조건

- 본인이 보낸 메시지 (`mine === true`)
- 90분 이내 메시지 (`canEditMessage(createdAt) === true`)
- 삭제되지 않은 메시지 (`!message.deleted`)

#### 2.1.2 설정 드롭다운 메뉴

타임스탬프 옆에 설정 아이콘 (⚙) 표시:

```svelte
<DropdownMenu.Root>
  <DropdownMenu.Trigger>
    <button class="message-settings-button">⚙</button>
  </DropdownMenu.Trigger>
  <DropdownMenu.Content align="end">
    <DropdownMenu.Item onclick={() => handleEditMessage(...)}>
      ✏️ 수정
    </DropdownMenu.Item>
    <DropdownMenu.Item onclick={() => handleDeleteMessage(...)}>
      🗑️ 삭제
    </DropdownMenu.Item>
  </DropdownMenu.Content>
</DropdownMenu.Root>
```

#### 2.1.3 삭제된 메시지 표시

삭제된 메시지는 회색 이탤릭체로 "삭제된 메시지입니다" 표시:

```svelte
{#if message.deleted}
  <div class="message-bubble deleted-message">
    <p class="text-gray-400 italic">삭제된 메시지입니다</p>
  </div>
{/if}
```

### 2.2 메시지 수정 모달 (MessageEditModal)

**파일**: `/src/lib/components/chat/MessageEditModal.svelte`

#### 2.2.1 기능

1. **텍스트 편집**: textarea로 메시지 텍스트 수정
   - 스페이스바 및 Enter 키 입력 지원
   - Dialog 컴포넌트의 키보드 이벤트와 충돌 방지를 위해 `onkeydown` 이벤트에서 전파(propagation) 차단
2. **기존 첨부파일 관리**:
   - 이미지/동영상 미리보기
   - 일반 파일은 아이콘과 파일명 표시
   - 각 파일에 삭제 버튼 (✕)
3. **새 파일 업로드**:
   - 파일 선택 버튼
   - Firebase Storage에 즉시 업로드
   - 업로드 진행률 표시 (원형 프로그레스바)
4. **버튼**:
   - 취소: 모달 닫기 (변경사항 미저장)
   - 저장: Firebase RTDB 업데이트

#### 2.2.2 Props

```typescript
interface Props {
  open: boolean;                      // 모달 열림 상태 (bindable)
  messageId: string;                  // 메시지 ID
  initialText: string;                // 초기 텍스트
  initialUrls: Record<number, string>; // 초기 첨부파일 URL 목록
  roomId: string;                     // 채팅방 ID
  onClose: () => void;                // 모달 닫기 콜백
  onSaved?: () => void;               // 저장 완료 콜백
}
```

#### 2.2.3 저장 로직

```typescript
const updates = {
  text: text.trim(),
  urls,
  editedAt: Date.now()
};
await update(ref(rtdb, `chat-messages/${messageId}`), updates);
```

---

## 3. 비즈니스 로직

### 3.1 메시지 수정 가능 여부 확인

**함수**: `canEditMessage(createdAt: number): boolean`

**파일**: `/src/routes/chat/room/+page.svelte`

```typescript
function canEditMessage(createdAt: number): boolean {
  if (!createdAt) return false;

  const now = Date.now();
  const elapsed = now - createdAt;
  const ninetyMinutesInMs = 90 * 60 * 1000; // 5,400,000ms

  return elapsed < ninetyMinutesInMs;
}
```

- **90분 = 5,400,000ms**
- 현재 시각 - 메시지 생성 시각 < 90분

### 3.2 메시지 수정 핸들러

**함수**: `handleEditMessage(messageId, text, urls, createdAt)`

**파일**: `/src/routes/chat/room/+page.svelte`

```typescript
function handleEditMessage(
  messageId: string,
  text: string,
  urls: Record<number, string>,
  createdAt: number
) {
  selectedMessageId = messageId;
  selectedMessageText = text ?? '';
  selectedMessageUrls = urls ?? {};
  selectedMessageCreatedAt = createdAt;
  editModalOpen = true;
}
```

### 3.3 메시지 삭제 핸들러

**함수**: `handleDeleteMessage(messageId, urls)`

**파일**: `/src/routes/chat/room/+page.svelte`

#### 3.3.1 삭제 프로세스

1. **사용자 확인**: `confirm()` 다이얼로그
2. **첨부파일 삭제**: Storage에서 모든 첨부파일 삭제
3. **Soft Delete**: RTDB에서 메시지 필드 업데이트

```typescript
async function handleDeleteMessage(messageId: string, urls: Record<number, string>) {
  const confirmed = confirm('메시지를 삭제하시겠습니까?');
  if (!confirmed) return;

  // 1. 첨부파일 삭제 (Storage)
  if (urls && Object.keys(urls).length > 0) {
    for (const url of Object.values(urls)) {
      await deleteChatFile(url);
    }
  }

  // 2. Soft Delete (RTDB)
  const messageRef = ref(rtdb, `chat-messages/${messageId}`);
  await update(messageRef, {
    deleted: true,
    deletedAt: Date.now(),
    text: '',
    urls: null
  });
}
```

#### 3.3.2 Soft Delete 방식

삭제 시 메시지를 물리적으로 삭제하지 않고 다음 필드만 업데이트:

- `deleted`: `true` 설정
- `deletedAt`: 삭제 시각 (밀리초)
- `text`: 빈 문자열로 초기화
- `urls`: `null`로 설정

**장점**:
- 메시지 이력 유지 (채팅방 순서, 개수 등)
- 복구 가능성
- 감사 로그 (audit log) 유지

---

## 4. Firebase Realtime Database

### 4.1 데이터 구조

**경로**: `/chat-messages/{messageId}`

```json
{
  "roomId": "string",
  "type": "message",
  "text": "string",
  "senderUid": "string",
  "createdAt": 1234567890000,
  "editedAt": 1234567890000,      // 수정 시각 (null이면 미수정)
  "deleted": false,                // 삭제 여부
  "deletedAt": null,               // 삭제 시각 (null이면 미삭제)
  "urls": {
    "0": "https://...",
    "1": "https://..."
  },
  "roomOrder": "-roomId-timestamp",
  "rootOrder": "-roomId-timestamp"
}
```

### 4.2 Security Rules

**파일**: `/firebase/database.rules.json`

#### 4.2.1 쓰기 권한

```jsonc
".write": "auth != null &&
  (
    // 조건 1: 채팅방 접근 권한 확인
    (
      newData.child('roomId').val().contains(auth.uid) ||
      root.child('chat-rooms').child(newData.child('roomId').val()).child('members').child(auth.uid).exists()
    )
  ) &&
  (
    // 조건 2: 새 메시지 생성 또는 기존 메시지 수정/삭제 검증
    (
      // 2-1. 새 메시지 생성
      !data.exists()
    ) ||
    (
      // 2-2. 기존 메시지 수정/삭제: 다음 조건을 모두 만족
      data.exists() &&
      (
        // a) 본인이 작성한 메시지
        data.child('senderUid').val() === auth.uid
      ) &&
      (
        // b) 삭제되지 않은 메시지
        !data.child('deleted').val()
      ) &&
      (
        // c) 90분(5,400,000ms) 이내 메시지
        (now - data.child('createdAt').val()) < 5400000
      )
    )
  )"
```

#### 4.2.2 검증 규칙

1. **본인 메시지만 수정/삭제**: `data.child('senderUid').val() === auth.uid`
2. **삭제된 메시지는 수정 불가**: `!data.child('deleted').val()`
3. **90분 경과 메시지는 수정/삭제 불가**: `(now - createdAt) < 5400000`

---

## 5. 파일 관리 (Firebase Storage)

### 5.1 파일 업로드

**함수**: `uploadChatFile(file, uid, roomId, onProgress)`

**파일**: `/src/lib/functions/storage.functions.ts`

- Firebase Storage 경로: `users/{uid}/chat-files/{roomId}/{timestamp}-{filename}`
- 실시간 업로드 진행률 콜백
- 반환값: 다운로드 URL

### 5.2 파일 삭제

**함수**: `deleteChatFile(url)`

**파일**: `/src/lib/functions/storage.functions.ts`

- URL에서 파일 경로 추출
- Firebase Storage에서 파일 삭제

---

## 6. 타입 정의

**파일**: `/src/lib/types/chat.types.ts`

```typescript
export interface ChatMessage {
  roomId: string;
  type: 'text' | 'image' | 'file' | 'message';
  text?: string;
  senderUid: string;
  createdAt: number;
  roomOrder: string;
  rootOrder: string;
  editedAt?: number | null;       // 수정 시각
  deletedAt?: number | null;       // 삭제 시각
  deleted?: boolean;               // 삭제 여부
  urls?: Record<number, string>;   // 첨부파일 URL 목록
}
```

---

## 7. 사용 예시

### 7.1 메시지 수정

1. 사용자가 자신의 메시지 타임스탬프 옆 ⚙ 아이콘 클릭
2. 드롭다운에서 "수정" 선택
3. `MessageEditModal` 열림
   - 기존 텍스트와 첨부파일 표시
   - 텍스트 편집, 파일 삭제/추가 가능
4. "저장" 클릭
   - Firebase RTDB 업데이트: `text`, `urls`, `editedAt`
5. 모달 닫힘, 메시지 목록 자동 업데이트

### 7.2 메시지 삭제

1. 사용자가 자신의 메시지 타임스탬프 옆 ⚙ 아이콘 클릭
2. 드롭다운에서 "삭제" 선택
3. 확인 다이얼로그: "메시지를 삭제하시겠습니까?"
4. 확인 시:
   - 첨부파일 Storage에서 삭제
   - RTDB 업데이트: `deleted: true`, `deletedAt: now`, `text: ''`, `urls: null`
5. 메시지 목록에서 "삭제된 메시지입니다" 표시

---

## 8. 주의사항

### 8.1 시간 제한

- **클라이언트 체크**: `canEditMessage()` 함수로 UI 숨김 처리
- **서버 체크**: Firebase Security Rules로 강제 검증
- **시간 동기화**: 클라이언트와 서버 시간이 다를 수 있으므로 서버 체크가 최종 권한

### 8.2 삭제된 메시지

- Soft Delete 방식으로 데이터 유지
- 복구 기능은 현재 미구현 (향후 확장 가능)
- 삭제된 메시지는 수정 불가

### 8.3 첨부파일

- 메시지 삭제 시 Storage에서도 파일 삭제
- 삭제 실패 시에도 메시지는 삭제 처리 (로그 기록)

---

## 9. 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-15 | Claude Code | 채팅 메시지 수정/삭제 기능 구현 |
| 2025-11-15 | Claude Code | MessageEditModal 컴포넌트 생성 |
| 2025-11-15 | Claude Code | 채팅방 페이지에 설정 아이콘 및 드롭다운 메뉴 추가 |
| 2025-11-15 | Claude Code | 90분 시간 제한 로직 구현 (클라이언트 + 서버) |
| 2025-11-15 | Claude Code | Firebase Security Rules 업데이트 |
| 2025-11-15 | Claude Code | Soft Delete 방식으로 메시지 삭제 구현 |
| 2025-11-15 | Claude Code | MessageEditModal textarea에서 스페이스바/Enter 키 입력 문제 수정: Dialog 컴포넌트 키보드 이벤트 전파 차단 |
| 2025-11-15 | Claude Code | 첨부파일 중복 표시 문제 수정: 업로드 완료 후 1초 뒤 uploadingFiles에서 자동 제거 |
