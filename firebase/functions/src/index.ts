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
import {onValueCreated, onValueUpdated} from "firebase-functions/v2/database";
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
} from "./handlers/chat.handler";

// Firebase Admin 초기화
if (!admin.apps.length) {
  admin.initializeApp();
  logger.info("Firebase Admin initialized");
}

// 비용 관리를 위한 전역 옵션 설정
// 최대 10개의 컨테이너만 동시 실행하여 예상치 못한 비용 급증 방지
setGlobalOptions({
  maxInstances: 10,
  region: "asia-southeast1", // RTDB region과 일치 필수
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
export const onUserCreate = onValueCreated("/users/{uid}", async (event) => {
  const uid = event.params.uid as string;
  const userData = (event.data.val() || {}) as UserData;

  logger.info("새 사용자 등록 감지", {
    uid,
    displayName: userData.displayName ?? null,
  });

  // 비즈니스 로직 핸들러 호출
  return await handleUserCreate(uid, userData);
});



/**
 * 사용자 정보 수정
 *
 * 트리거 경로: /users/{uid}
 *
 * 수행 작업:
 * 1. createdAt 필드가 없으면 자동 생성
 * 2. 주의: updatedAt 은 모든 업데이트에서 수정하는 것이 아니라, `displayName`, `photoUrl` 이 업데이트 된 경우에만, updatedAt 을 새로운 timestamp 로 업데이트 합니다.
 */
export const onUserUpdate = onValueUpdated("/users/{uid}", async (event) => {
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
});



/**
 * 채팅 메시지 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-messages/{messageId}
 *
 * 수행 작업:
 * 1. 프로토콜 메시지 건너뛰기 (시스템 메시지)
 * 2. 필수 필드 유효성 검사 (senderUid, roomId)
 * 3. 1:1 채팅인 경우 양쪽 사용자의 chat-joins 자동 생성/업데이트
 *    - /chat-joins/{uid1}/{roomId}
 *    - /chat-joins/{uid2}/{roomId}
 * 4. 각 chat-join 노드에 다음 정보 업데이트:
 *    - roomId, roomType, partnerUid (1:1 채팅의 경우)
 *    - lastMessageText, lastMessageAt
 *    - updatedAt, joinedAt (신규 생성 시)
 *    - listOrder (정렬용, 발신자: timestamp, 수신자: 200+timestamp)
 *    - newMessageCount (수신자만 increment(1))
 *
 * 참고:
 * - 1:1 채팅 roomId 형식: "single-{uid1}-{uid2}" (알파벳 순 정렬)
 * - 그룹/오픈 채팅의 경우 chat-joins 생성 로직은 아직 미구현
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatMessageCreate() 참조
 */
export const onChatMessageCreate = onValueCreated(
  "/chat-messages/{messageId}",
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
 * 1. 임시 필드(_requestingUid)를 통해 채팅방 생성자 확인
 * 2. createdAt 필드 자동 생성
 * 3. owner 필드를 _requestingUid 값으로 설정
 * 4. 임시 필드(_requestingUid) 삭제
 *
 * 보안:
 * - _requestingUid 필드는 RTDB 보안 규칙에 의해 auth.uid와 동일하게 검증됨
 * - createdAt과 owner 필드는 Cloud Functions에서만 설정 가능
 * - 클라이언트는 이 필드들을 직접 설정할 수 없음
 *
 * 참고:
 * - 그룹 채팅방: type='group'
 * - 오픈 채팅방: type='open'
 * - 1:1 채팅방: type='single' (owner 필드 불필요)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomCreate() 참조
 */
export const onChatRoomCreate = onValueCreated(
  "/chat-rooms/{roomId}",
  async (event) => {
    const roomId = event.params.roomId as string;
    const roomData = (event.data.val() || {}) as Record<string, unknown>;
    // _requestingUid는 보안 규칙에 의해 auth.uid와 동일하게 검증되므로 신뢰 가능
    const authUid = typeof roomData._requestingUid === "string"
      ? roomData._requestingUid
      : undefined;

    logger.info("채팅방 생성 감지", {
      roomId,
      authUid,
      roomType: roomData.type,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatRoomCreate(roomId, roomData, authUid);
  }
);



/**
 * 채팅 참여 정보 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-joins/{uid}/{roomId}
 *
 * 수행 작업:
 * 1. joinedAt 필드가 없으면 현재 타임스탬프로 자동 생성
 *
 * 참고:
 * - chat-joins 노드는 onChatMessageCreate에서 자동 생성됨
 * - 이 함수는 joinedAt 필드만 추가하는 역할
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatJoinCreate() 참조
 */
export const onChatJoinCreate = onValueCreated(
  "/chat-joins/{uid}/{roomId}",
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