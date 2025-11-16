---
name: sonub-firestore-security
version: 2.2.0
description: Firestore/Storage 보안 규칙 요약 및 작성 지침
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
tags:
  - firestore
  - security
  - rules
last_updated: 2025-01-16
---

# Firestore 보안 규칙

규칙 파일: `firebase/firestore.rules`

## 1. 기본 원칙

1. **kebab-case 컬렉션 명명**: `users`, `chats`, `chat-room-passwords`, `system/stats` 등
2. **최소 권한**: 본인 문서는 본인만 쓰기, 읽기는 공개 여부에 따라 결정
3. **Cloud Functions 연계**: 파생 필드/통계는 Functions가 처리하므로 클라이언트가 시스템 필드를 쓰지 않는다
4. **관리자 판별**: `system/settings` 문서의 `admins` 객체를 기준으로 한다 (클라이언트도 Firestore에서 동일 문서를 읽음)

## 2. 컬렉션별 규칙 요약

| 경로 | 읽기 | 쓰기 | 비고 |
|------|------|------|------|
| `/users/{uid}` | 누구나 허용 (프로필 노출) | `request.auth.uid == uid` | `.create`/`.update`에서 필수 필드 검증 |
| `/users/{uid}/chat-joins/{roomId}` | `request.auth != null && request.auth.uid == uid` | 동일 | Cloud Functions가 정렬 필드 업데이트 |
| `/chats/{roomId}` | 로그인 사용자 | 생성은 로그인 사용자, update는 owner 또는 멤버 조건 | `members` 필드는 멤버 본인만 수정 |
| `/chats/{roomId}/messages/{messageId}` | 방 멤버 또는 1:1 참가자 | 채팅 참여자만 쓰기/삭제 | `isChatRoomMember` 헬퍼 사용 |
| `/chat-room-passwords/{roomId}` | owner와 관리자만 | owner 전용 | `/tries/{uid}`는 해당 사용자만 읽기/쓰기 |
| `/fcm-tokens/{token}` | 본인만 (uid가 있을 때) | `request.auth.uid == resource.data.uid` (uid 없는 경우는 읽기 불가, 쓰기만) |
| `/system/stats` | 로그인 사용자 | Cloud Functions만 쓰기 (Security Rules에서는 `false`) |
| `/system/settings` | 로그인 사용자 | 관리자만 (`admins[request.auth.uid] == true`) |

### rules 예시

```groovy
match /databases/{database}/documents {
  function isSignedIn() {
    return request.auth != null;
  }

  function isOwner(uid) {
    return isSignedIn() && request.auth.uid == uid;
  }

  match /users/{uid} {
    allow read: if true;
    allow write: if isOwner(uid);
  }

  match /users/{uid}/chat-joins/{roomId} {
    allow read, write: if isOwner(uid);
  }

  match /chats/{roomId} {
    allow read: if isSignedIn();
    allow create: if isSignedIn();
    allow update, delete: if isChatRoomOwner(roomId);

    match /messages/{messageId} {
      allow read, write: if isChatRoomMember(roomId);
    }
  }

  match /chat-room-passwords/{roomId} {
    allow read, write: if isChatRoomOwner(roomId) || isAdmin();

    match /tries/{uid} {
      allow read, write: if isOwner(uid);
    }
  }

  match /system/{docId} {
    allow read: if isSignedIn();
    allow write: if docId == "settings" && isAdmin();
  }
}
```

> `isChatRoomOwner`/`isChatRoomMember`/`isAdmin` 함수는 규칙 파일 상단에서 정의한다. `isAdmin`은 `get(/databases/$(database)/documents/system/settings).data.admins[request.auth.uid] == true` 로 판별한다.

## 3. Storage 규칙 (요약)

파일: `firebase/storage.rules`

| 경로 | 설명 |
|------|------|
| `/users/{uid}/profile/*` | 본인만 쓰기/삭제 가능, 읽기는 공개 |
| 기타 | 기본 deny |

Storage 규칙도 kebab-case 폴더명(`/users/{uid}/profile/`)을 사용한다.

## 4. 작성 지침

- 조건식은 반드시 여러 줄로 작성하여 가독성을 높인다.
- `.validate`를 이용해 타입/최대 길이를 검증한다 (예: `newData.isString() && newData.val().size() <= 50`).
- `allow write: if false`를 사용해 Cloud Functions만 수정해야 하는 문서(`system/stats`)를 보호한다.
- 컬렉션 이름/문서 경로는 스펙과 코드 전반에서 동일한 kebab-case를 사용한다.

## 5. 참고 코드

- `firebase/firestore.rules`
- `firebase/storage.rules`
- `src/lib/functions/chat.functions.ts` (멤버십 로직)
- `firebase/functions/src/handlers/user.handler.ts`

---

## 6. 변경 이력

### v2.2.0 (2025-01-16)

**작업 내용: 오픈 채팅방 메시지 읽기 권한 오류 수정**

#### 수정 사항

**1. isChatRoomMember() 함수 수정**
- **문제**: 오픈 채팅방 입장 후 메시지 로딩 시 권한 오류 발생
  ```
  Failed to load messages.
  Missing or insufficient permissions.
  ```
- **원인**:
  - `isChatRoomMember()` 함수가 채팅방 메인 문서의 `members` 필드만 확인 ([firebase/firestore.rules:70-75](firebase/firestore.rules#L70-L75))
  - 오픈 채팅방은 멤버 정보를 `/chats/{roomId}/members/{uid}` subcollection에 저장
  - 메인 문서의 `members` 필드는 그룹 채팅방에서만 사용
  - 따라서 오픈 채팅방 멤버가 `isChatRoomMember()` 검증을 통과하지 못함
  - 메시지 읽기 권한이 `isChatRoomMember()` 함수에 의존하므로 메시지 로딩 실패

- **해결**: `firebase/firestore.rules:75-86`에서 `isChatRoomMember()` 함수 수정
  ```groovy
  function isChatRoomMember(roomId) {
    return isSignedIn() &&
           chatRoomExists(roomId) &&
           (
             // 1:1 채팅: roomId에 UID 포함
             roomId.matches('.*' + currentUserId() + '.*') ||
             // 그룹 채팅: members 필드에 포함
             (currentUserId() in getChatRoom(roomId).data.members) ||
             // 오픈 채팅: members subcollection에 문서 존재
             exists(/databases/$(database)/documents/chats/$(roomId)/members/$(currentUserId()))
           );
  }
  ```
  - `exists()` 함수로 members subcollection에서 사용자 문서 존재 확인
  - 세 가지 채팅 유형 모두 지원: 1:1, 그룹, 오픈 채팅

#### 배포 결과

```bash
cd firebase && firebase deploy --only firestore:rules
# ✔  Deploy complete!
```

#### 검증

```bash
npm run check
# ✅ Type check 통과 (CSS 경고만 존재 - 정상)
```

#### 영향받은 파일

- `firebase/firestore.rules` (Lines 75-86)

#### 기술적 배경

**Firestore Security Rules에서 Subcollection 확인:**
- Firestore는 문서와 subcollection을 별도로 관리
- Security Rules에서 subcollection의 존재를 확인하려면 `exists()` 함수 사용 필요
- `exists()` 함수는 해당 문서에 대한 읽기 권한이 있어야 작동
- Members subcollection은 본인 문서만 읽기 가능하도록 설정되어 있음 ([firebase/firestore.rules:260-263](firebase/firestore.rules#L260-L263))
- 따라서 `exists()` 함수로 자신의 멤버 문서 존재를 확인할 수 있음

**채팅 유형별 멤버십 확인 방식:**
1. **1:1 채팅**: `roomId.matches('.*' + currentUserId() + '.*')` - roomId에 두 사용자의 UID가 포함됨
2. **그룹 채팅**: `currentUserId() in getChatRoom(roomId).data.members` - 메인 문서의 members 필드 확인
3. **오픈 채팅**: `exists(/databases/$(database)/documents/chats/$(roomId)/members/$(currentUserId()))` - members subcollection 확인

#### 문제 해결 패턴

1. **Permission Error 디버깅**:
   - 에러 메시지에서 어떤 리소스에 대한 권한이 없는지 확인
   - Security Rules에서 해당 리소스의 읽기/쓰기 조건 확인
   - 헬퍼 함수가 모든 케이스를 처리하는지 검증

2. **Subcollection vs Field**:
   - Firestore에서 데이터를 subcollection에 저장하는지 필드에 저장하는지 명확히 구분
   - Security Rules의 헬퍼 함수가 실제 데이터 구조와 일치하는지 확인
   - 여러 데이터 저장 방식을 지원해야 하는 경우 OR 조건으로 모두 확인

3. **exists() 함수 활용**:
   - Subcollection 문서의 존재 여부를 확인할 때 사용
   - 읽기 권한이 있는 문서에 대해서만 작동
   - 경로는 절대 경로로 작성: `/databases/$(database)/documents/...`

---

### v2.1.0 (2025-01-16)

**작업 내용: Firestore Security Rules 권한 오류 수정 및 경로 불일치 해결**

#### 수정 사항

**1. system 컬렉션 규칙 추가**
- **문제**: `system/stats` 문서 접근 시 권한 오류 발생
  ```
  firestore.svelte.ts:197 ❌ 실시간 문서 로드 실패: system/stats
  FirebaseError: Missing or insufficient permissions.
  ```
- **원인**: `firestore.rules`에 `stats` 컬렉션만 정의되어 있고 `system` 컬렉션 규칙이 없었음
- **해결**: `firebase/firestore.rules` Lines 402-418에 `system` 컬렉션 규칙 추가
  - 읽기: `allow read: if true` (모든 사용자 - 로그인 불필요)
  - 쓰기: `allow write: if false` (Cloud Functions만)
- **관련 파일**: `src/lib/components/right-sidebar.svelte:25` (system/stats 구독)

**2. FCM 토큰 경로 불일치 수정**
- **문제**: FCM 토큰 저장 시 권한 오류 발생
  ```
  fcm.ts:238 [FCM 저장] ❌❌❌ 토큰 저장 실패:
  FirebaseError: Missing or insufficient permissions.
  ```
- **원인**:
  - 코드(`src/lib/fcm.ts:229`)는 `fcm-tokens/{token}` 경로 사용 (하이픈)
  - Rules(`firebase/firestore.rules:446`)는 `fcmTokens/{tokenId}` 경로 사용 (카멜케이스)
  - 경로 불일치로 인한 권한 거부
- **해결**: `firebase/firestore.rules:449`를 `fcm-tokens` 경로로 수정
  - `match /fcmTokens/{tokenId}` → `match /fcm-tokens/{token}`
  - 비로그인 사용자도 자신의 토큰 등록 가능하도록 추가 규칙 설정
- **참고**: `specs/sonub-firebase-database-structure.md:109-113`에서 공식 경로 확인

**3. Firestore 스토어 경로 오류 수정 (클라이언트)**
- **문제**: `newMessageCount` 구독 시 Firestore 스토어 생성 오류
  ```
  firestore.svelte.ts:206 ❌ Firestore 스토어 생성 오류:
  users/GljDA3yso2b3wIHh1M45vHGUcK72/newMessageCount
  Error: createFirestoreStore는 Document 경로만 지원합니다.
  Collection 경로가 전달되었습니다
  ```
- **원인**:
  - `src/routes/+layout.svelte:80`에서 `users/{uid}/newMessageCount`를 문서 경로로 사용
  - `newMessageCount`는 필드명이지 문서 ID가 아님
  - `firestoreStore()`는 문서 경로만 지원하며 필드 경로는 지원하지 않음
- **해결**: `src/routes/+layout.svelte` 수정
  - `UserData` 인터페이스 추가 (Lines 50-55)
  - 스토어 타입 변경: `firestoreStore<number>` → `firestoreStore<UserData>`
  - 구독 경로 변경: `users/{uid}/newMessageCount` → `users/{uid}`
  - 필드 접근 방식 변경: `state.data ?? 0` → `state.data?.newMessageCount ?? 0` (Line 109)
- **참고**: `src/lib/components/top-bar.svelte:43-44`에 이미 올바른 패턴 구현되어 있었음

#### 배포 결과

```bash
cd firebase && firebase deploy --only firestore:rules
# ✔  Deploy complete!
```

#### 검증

```bash
npm run check
# ✅ Type check 통과 (CSS 경고만 존재 - 정상)
```

#### 영향받은 파일

- `firebase/firestore.rules` (Lines 402-418, 449)
- `src/routes/+layout.svelte` (Lines 50-55, 87-95, 109)

#### 문제 해결 패턴

1. **경로 불일치 디버깅**: 코드에서 사용하는 경로와 Security Rules의 경로를 비교
2. **스펙 문서 참조**: `specs/sonub-firebase-database-structure.md`에서 공식 경로 확인
3. **작동하는 코드 참조**: `top-bar.svelte`의 올바른 패턴을 `+layout.svelte`에 적용
4. **타입 안전성**: TypeScript 인터페이스로 문서 구조 명시화
