---
name: index.ts
description: Firebase Cloud Functions Gen 2의 메인 진입점. 모든 트리거 함수들을 정의하고 내보냅니다.
version: 1.0.0
type: firebase-function
category: handler
tags: [firebase, cloud-functions, typescript, gen2, rtdb, triggers, entry-point]
---

# index.ts

## 개요
이 파일은 Firebase Cloud Functions Gen 2의 메인 진입점입니다. 모든 트리거 함수들을 정의하고 내보내는 역할을 합니다. SNS 프로젝트의 백그라운드 이벤트 처리를 위한 함수들이 선언되어 있습니다.

**⚠️ 중요:** 모든 함수는 반드시 Gen 2 버전으로 작성해야 합니다.
- Gen 2 API: `firebase-functions/v2`
- Gen 1 API 사용 금지

## 소스 코드

```typescript
/**
 * Firebase Cloud Functions (Gen 2)
 * SNS 프로젝트의 백그라운드 이벤트 처리 함수들
 *
 * ⚠️ 중요: 모든 함수는 반드시 Gen 2 버전으로 작성해야 합니다.
 * - Gen 2 API: firebase-functions/v2
 * - Gen 1 API 사용 금지
 *
 * 참고: https://firebase.google.com/docs/functions/2nd-gen
 */

// Gen 2 API imports
import {setGlobalOptions} from "firebase-functions/v2";
import {
  onValueCreated,
  onValueUpdated,
  onValueDeleted,
} from "firebase-functions/v2/database";
import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";

// 타입 임포트
import {UserData, ChatMessage} from "./types";

// 비즈니스 로직 핸들러 임포트
import {handleUserCreate, handleUserUpdate} from "./handlers/user.handler";
import {
  handleChatMessageCreate,
  handleChatJoinCreate,
  handleChatRoomCreate,
  handleChatRoomMemberJoin,
  handleChatRoomMemberLeave,
} from "./handlers/chat.handler";

// 상수 정의
const FIREBASE_REGION = "asia-southeast1";


// Firebase Admin 초기화
if (!admin.apps.length) {
  admin.initializeApp();
  logger.info("Firebase Admin initialized");
}

// 비용 관리를 위한 전역 옵션 설정
// 최대 10개의 컨테이너만 동시 실행하여 예상치 못한 비용 급증 방지
setGlobalOptions({
  maxInstances: 10,
  region: FIREBASE_REGION, // RTDB region과 일치 필수
});

/**
 * 사용자 등록 시 user-props 노드에 주요 필드를 분리 저장하고 createdAt을 설정합니다.
 *
 * 트리거 경로: /users/{uid}
 *
 * 수행 작업:
 * 1. createdAt 필드 자동 생성 및 /users/{uid}/createdAt 직접 저장
 * 2. handleUserCreate() 함수를 통해 모든 사용자 데이터 정규화 및 동기화 수행
 *    - updatedAt 필드 자동 생성
 *    - displayNameLowerCase 자동 생성
 *    - photoUrl 처리
 *    - /users/{uid} 노드 업데이트
 *    - /user-props/ 노드 동기화
 *    - /stats/counters/user +1 (전체 사용자 통계 업데이트)
 */
export const onUserCreate = onValueCreated(
  {
    ref: "/users/{uid}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const userData = (event.data.val() || {}) as UserData;

    logger.info("새 사용자 등록 감지", {
      uid,
      displayName: userData.displayName ?? null,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleUserCreate(uid, userData);
  }
);



/**
 * 사용자 정보 수정
 *
 * 트리거 경로: /users/{uid}
 *
 * 수행 작업:
 * 1. createdAt 필드가 없으면 자동 생성
 * 2. 주의: updatedAt 은 모든 업데이트에서 수정하는 것이 아니라, `displayName`, `photoUrl` 이 업데이트 된 경우에만, updatedAt 을 새로운 timestamp 로 업데이트 합니다.
 */
export const onUserUpdate = onValueUpdated(
  {
    ref: "/users/{uid}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;

    // onValueUpdated는 before와 after 데이터를 모두 제공
    const beforeData = (event.data.before.val() || {}) as UserData;
    const afterData = (event.data.after.val() || {}) as UserData;

    logger.info("사용자 정보 수정 감지", {
      uid,
      beforeDisplayName: beforeData.displayName ?? null,
      afterDisplayName: afterData.displayName ?? null,
    });

    // 비즈니스 로직 핸들러 호출 (before/after 데이터 전달)
    return await handleUserUpdate(uid, beforeData, afterData);
  }
);



/**
 * 채팅 메시지 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-messages/{messageId}
 *
 * 수행 작업:
 * 1. 프로토콜 메시지 건너뛰기 (시스템 메시지)
 * 2. 필수 필드 유효성 검사 (senderUid, roomId)
 * 3. 1:1 채팅인 경우:
 *    - 양쪽 사용자의 chat-joins 자동 생성/업데이트 (/chat-joins/{uid1}/{roomId}, /chat-joins/{uid2}/{roomId})
 *    - roomId, roomType, partnerUid 설정
 *    - lastMessageText, lastMessageAt 업데이트
 *    - 정렬 필드: singleChatListOrder (발신자: timestamp, 수신자: 200+timestamp)
 *    - allChatListOrder (통합 정렬용)
 *    - newMessageCount (수신자만 increment(1))
 * 4. 그룹/오픈 채팅인 경우:
 *    - chat-rooms에서 members 목록 조회
 *    - 모든 멤버의 chat-joins 자동 업데이트
 *    - roomName, lastMessageText, lastMessageAt 설정
 *    - 정렬 필드: groupChatListOrder 또는 openChatListOrder (발신자: timestamp, 타 멤버: 200+timestamp)
 *    - openAndGroupChatListOrder, allChatListOrder (통합 정렬용)
 *    - newMessageCount (발신자 제외한 모든 멤버 increment(1))
 *
 * 참고:
 * - 1:1 채팅 roomId 형식: "single-{uid1}-{uid2}" (알파벳 순 정렬)
 * - 그룹/오픈 채팅 roomId: 자동 생성된 Firebase push key
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatMessageCreate() 참조
 */
export const onChatMessageCreate = onValueCreated(
  {
    ref: "/chat-messages/{messageId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const messageId = event.params.messageId as string;
    const messageData = (event.data.val() || {}) as ChatMessage;

    // 비즈니스 로직 핸들러 호출
    return await handleChatMessageCreate(messageId, messageData);
  }
);



/**
 * 채팅방 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-rooms/{roomId}
 *
 * 수행 작업:
 * 1. owner 필드를 통해 채팅방 생성자 확인 (보안 규칙으로 검증됨)
 * 2. createdAt 필드 자동 생성
 * 3. memberCount 필드를 1로 초기화 (생성자 포함)
 *
 * 보안:
 * - owner 필드는 보안 규칙에 의해 auth.uid와 동일하게 검증됨
 * - createdAt과 memberCount는 Cloud Functions에서만 설정 가능
 *
 * 참고:
 * - 그룹 채팅방: type='group'
 * - 오픈 채팅방: type='open'
 * - 1:1 채팅방: type='single' (memberCount 관리 불필요)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomCreate() 참조
 */
export const onChatRoomCreate = onValueCreated(
  {
    ref: "/chat-rooms/{roomId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const roomId = event.params.roomId as string;
    const roomData = (event.data.val() || {}) as Record<string, unknown>;
    // owner 필드는 보안 규칙에 의해 auth.uid와 동일하게 검증되므로 신뢰 가능
    const ownerUid = typeof roomData.owner === "string"
      ? roomData.owner
      : undefined;

    logger.info("채팅방 생성 감지", {
      roomId,
      owner: ownerUid,
      roomType: roomData.type,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatRoomCreate(roomId, roomData, ownerUid);
  }
);



/**
 * 채팅 참여 정보 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-joins/{uid}/{roomId}
 *
 * 수행 작업:
 * 1. joinedAt 필드가 없으면 현재 타임스탬프로 자동 생성
 * 2. 1:1 채팅인 경우 (roomId 형식: "single-{uid1}-{uid2}"):
 *    - partnerUid 추출 및 설정
 *    - roomType = "single" 설정
 *    - singleChatListOrder = timestamp (문자열) 설정
 *    - allChatListOrder = timestamp (숫자) 설정
 * 3. 그룹/오픈 채팅인 경우:
 *    - chat-rooms에서 채팅방 정보 조회
 *    - roomType, roomName 설정
 *    - roomType이 "group"인 경우:
 *      - groupChatListOrder = timestamp (문자열) 설정
 *      - openAndGroupChatListOrder = timestamp (숫자) 설정
 *    - roomType이 "open"인 경우:
 *      - openChatListOrder = timestamp (문자열) 설정
 *      - openAndGroupChatListOrder = timestamp (숫자) 설정
 *    - allChatListOrder = timestamp (숫자) 설정
 *
 * 참고:
 * - chat-joins 노드는 클라이언트가 직접 생성하거나 onChatMessageCreate에서 자동 생성됨
 * - 이 함수는 채팅방 타입별로 적절한 정렬 필드를 자동 설정
 * - 이미 완전히 설정된 경우 (joinedAt + roomType 존재) 건너뛰기
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatJoinCreate() 참조
 */
export const onChatJoinCreate = onValueCreated(
  {
    ref: "/chat-joins/{uid}/{roomId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const roomId = event.params.roomId as string;

    logger.info("채팅방 참여 정보 생성 감지", {
      uid,
      roomId,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatJoinCreate(uid, roomId);
  }
);



/**
 * 채팅방 멤버 입장 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-rooms/{roomId}/members/{uid}
 *
 * 수행 작업:
 * 1. chat-rooms/{roomId}/members 아래의 모든 uid 읽기
 * 2. 모든 uid의 개수 세기 (true/false 구분 없이)
 * 3. memberCount 필드를 자동으로 증가
 *
 * 참고:
 * - members 필드 구조: chat-rooms/{roomId}/members/{uid}: boolean
 * - true: 사용자가 채팅방에 참여 중, 메시지 알림을 받음
 * - onValueCreated 트리거로 멤버 입장 감지
 * - memberCount는 members 객체의 모든 uid 개수 (true/false 모두 포함)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomMemberJoin() 참조
 */
export const onChatRoomMemberJoin = onValueCreated(
  {
    ref: "/chat-rooms/{roomId}/members/{uid}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const roomId = event.params.roomId as string;
    const uid = event.params.uid as string;

    logger.info("채팅방 멤버 입장 감지", {
      roomId,
      uid,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatRoomMemberJoin(roomId, uid);
  }
);



/**
 * 채팅방 멤버 퇴장 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-rooms/{roomId}/members/{uid}
 *
 * 수행 작업:
 * 1. chat-rooms/{roomId}/members 아래의 모든 uid 읽기
 * 2. 모든 uid의 개수 세기 (true/false 구분 없이)
 * 3. memberCount 필드를 자동으로 감소
 *
 * 참고:
 * - members 필드에서 uid가 삭제되면 퇴장으로 간주
 * - onValueDeleted 트리거로 멤버 퇴장 감지
 * - memberCount는 members 객체의 모든 uid 개수 (true/false 모두 포함)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomMemberLeave() 참조
 */
export const onChatRoomMemberLeave = onValueDeleted(
  {
    ref: "/chat-rooms/{roomId}/members/{uid}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const roomId = event.params.roomId as string;
    const uid = event.params.uid as string;

    logger.info("채팅방 멤버 퇴장 감지", {
      roomId,
      uid,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatRoomMemberLeave(roomId, uid);
  }
);
```

## 주요 기능
- **Firebase Admin 초기화**: Firebase Admin SDK를 초기화하여 Realtime Database 접근
- **전역 옵션 설정**: 비용 관리를 위해 최대 인스턴스 수를 10개로 제한
- **사용자 관리 함수**:
  - `onUserCreate`: 사용자 등록 시 createdAt 생성 및 통계 업데이트
  - `onUserUpdate`: 사용자 정보 수정 시 updatedAt 업데이트 및 파생 필드 생성
- **채팅 관리 함수**:
  - `onChatMessageCreate`: 채팅 메시지 생성 시 chat-joins 업데이트
  - `onChatRoomCreate`: 채팅방 생성 시 createdAt 및 memberCount 초기화
  - `onChatJoinCreate`: 채팅방 참여 시 메타데이터 자동 설정
  - `onChatRoomMemberJoin`: 채팅방 입장 시 memberCount 증가
  - `onChatRoomMemberLeave`: 채팅방 퇴장 시 memberCount 감소

## 사용되는 Firebase 트리거
- **onValueCreated**: 새로운 데이터가 생성될 때 트리거
  - `/users/{uid}`
  - `/chat-messages/{messageId}`
  - `/chat-rooms/{roomId}`
  - `/chat-joins/{uid}/{roomId}`
  - `/chat-rooms/{roomId}/members/{uid}`
- **onValueUpdated**: 기존 데이터가 수정될 때 트리거
  - `/users/{uid}`
- **onValueDeleted**: 데이터가 삭제될 때 트리거
  - `/chat-rooms/{roomId}/members/{uid}`

## 관련 함수
- `handlers/user.handler.ts`: 사용자 관련 비즈니스 로직
- `handlers/chat.handler.ts`: 채팅 관련 비즈니스 로직
- `types/index.ts`: 타입 정의
