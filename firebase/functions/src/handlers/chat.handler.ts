/**
 * Firebase Cloud Functions - Chat Message Handler
 *
 * 채팅 메시지 생성 시 비즈니스 로직을 처리하는 핸들러 모듈입니다.
 * Firebase Cloud Functions의 트리거 함수(onMessageCreate)에서 호출되어
 * 실제 데이터 처리를 수행합니다.
 */

import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";
import {ChatMessage} from "../types";
import {
  isSingleChat,
  extractUidsFromSingleRoomId,
} from "@functions/chat.functions.js";

/**
 * 채팅 메시지 생성 시 비즈니스 로직 처리
 *
 * @param messageId - 생성된 메시지 ID
 * @param messageData - 메시지 데이터
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. 프로토콜 메시지 필터링 (시스템 메시지 건너뛰기)
 * 2. 필수 필드 유효성 검사 (senderUid, roomId)
 * 3. 1:1 채팅 감지 (isSingleChat 함수 사용)
 * 4. chat-joins 자동 생성/업데이트
 *    - 발신자와 수신자 모두의 chat-join 노드 업데이트
 *    - roomId, roomType, partnerUid, lastMessageText, timestamps 설정
 *    - listOrder 정렬 필드 설정
 */
export async function handleChatMessageCreate(
  messageId: string,
  messageData: ChatMessage
): Promise<void> {
  logger.info("채팅 메시지 생성 처리 시작", {
    messageId,
    roomId: messageData.roomId,
    senderUid: messageData.senderUid,
    type: messageData.type,
  });

  // 단계 1: 프로토콜 메시지 건너뛰기 (join/leave와 같은 시스템 메시지)
  if (messageData.protocol) {
    logger.info("프로토콜 메시지 건너뜀", {
      messageId,
      protocol: messageData.protocol,
    });
    return;
  }

  // 단계 2: 필수 필드 유효성 검사
  if (!messageData.senderUid || messageData.senderUid.trim().length === 0) {
    logger.error("senderUid 필드가 비어있음", {messageId});
    throw new Error("senderUid는 필수이지만 정의되지 않았거나 비어있습니다");
  }

  if (!messageData.roomId || messageData.roomId.trim().length === 0) {
    logger.error("roomId 필드가 비어있음", {messageId});
    throw new Error("roomId는 필수이지만 정의되지 않았거나 비어있습니다");
  }

  const roomId = messageData.roomId;
  const senderUid = messageData.senderUid;

  // 단계 3: 1:1 채팅인지 확인 (공유 함수 사용)
  if (!isSingleChat(roomId)) {
    logger.info("1:1 채팅이 아니므로 chat-joins 생성을 건너뜀", {
      messageId,
      roomId,
    });
    return;
  }

  // 단계 4: 1:1 채팅 roomId에서 두 사용자의 UID 추출
  const uids = extractUidsFromSingleRoomId(roomId);
  if (!uids) {
    logger.error("잘못된 1:1 채팅 roomId 형식", {messageId, roomId});
    return;
  }

  const [uid1, uid2] = uids;

  // 단계 5: 상대방 UID 결정
  const partnerUid = senderUid === uid1 ? uid2 : uid1;

  // 단계 6: chat-joins 업데이트할 데이터 준비
  const timestamp = messageData.createdAt || Date.now();
  const messageText = messageData.text || "";

  // listOrder 계산
  // - 발신자: 읽음 상태이므로 그냥 timestamp
  // - 수신자(상대방): 읽지 않은 상태이므로 200 prefix 추가
  const senderListOrder = `${timestamp}`;
  const partnerListOrder = `200${timestamp}`;

  const updates: {[key: string]: unknown} = {};

  // 발신자의 chat-join 업데이트 (읽음 상태)
  updates[`chat-joins/${senderUid}/${roomId}/roomId`] = roomId;
  updates[`chat-joins/${senderUid}/${roomId}/roomType`] = "single";
  updates[`chat-joins/${senderUid}/${roomId}/partnerUid`] = partnerUid;
  updates[`chat-joins/${senderUid}/${roomId}/lastMessageText`] = messageText;
  updates[`chat-joins/${senderUid}/${roomId}/lastMessageAt`] = timestamp;
  updates[`chat-joins/${senderUid}/${roomId}/updatedAt`] = timestamp;
  updates[`chat-joins/${senderUid}/${roomId}/listOrder`] = senderListOrder;

  // 수신자의 chat-join 업데이트 (읽지 않은 상태, 200 prefix 추가)
  updates[`chat-joins/${partnerUid}/${roomId}/roomId`] = roomId;
  updates[`chat-joins/${partnerUid}/${roomId}/roomType`] = "single";
  updates[`chat-joins/${partnerUid}/${roomId}/partnerUid`] = senderUid;
  updates[`chat-joins/${partnerUid}/${roomId}/lastMessageText`] = messageText;
  updates[`chat-joins/${partnerUid}/${roomId}/lastMessageAt`] = timestamp;
  updates[`chat-joins/${partnerUid}/${roomId}/updatedAt`] = timestamp;
  updates[`chat-joins/${partnerUid}/${roomId}/listOrder`] = partnerListOrder;

  // 수신자의 읽지 않은 메시지 카운터 증가
  updates[`chat-joins/${partnerUid}/${roomId}/newMessageCount`] =
    admin.database.ServerValue.increment(1);

  // 단계 7: 모든 업데이트를 한 번에 실행
  await admin.database().ref().update(updates);

  logger.info("chat-joins 업데이트 완료", {
    messageId,
    roomId,
    senderUid,
    partnerUid,
    timestamp,
    updatesCount: Object.keys(updates).length,
  });
}

/**
 * 채팅방 생성 시 비즈니스 로직 처리
 *
 * @param roomId - 생성된 채팅방 ID
 * @param roomData - 채팅방 데이터
 * @param authUid - 인증된 사용자 UID
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. 인증된 사용자 UID 유효성 검사
 * 2. createdAt 필드 자동 생성 (타임스탬프)
 * 3. owner 필드를 authUid로 설정
 *
 * 보안:
 * - createdAt과 owner 필드는 Cloud Functions에서만 설정
 * - 클라이언트에서 이 필드들을 설정할 수 없음
 * - RTDB 보안 규칙과 함께 작동하여 데이터 무결성 보장
 */
export async function handleChatRoomCreate(
  roomId: string,
  roomData: Record<string, unknown>,
  authUid: string | undefined
): Promise<void> {
  logger.info("채팅방 생성 처리 시작", {
    roomId,
    authUid,
    roomType: roomData.type,
  });

  // 단계 1: 인증된 사용자 UID 유효성 검사
  if (!authUid || authUid.trim().length === 0) {
    logger.error("인증되지 않은 사용자가 채팅방을 생성하려고 시도함", {
      roomId,
    });
    throw new Error("채팅방 생성은 인증된 사용자만 가능합니다");
  }

  const timestamp = Date.now();
  const updates: {[key: string]: unknown} = {};

  // 단계 2: createdAt 필드 확인 및 설정
  const createdAtRef = admin.database().ref(
    `chat-rooms/${roomId}/createdAt`
  );
  const createdAtSnapshot = await createdAtRef.once("value");

  if (!createdAtSnapshot.exists()) {
    updates[`chat-rooms/${roomId}/createdAt`] = timestamp;
    logger.info("createdAt 필드 생성", {roomId, createdAt: timestamp});
  }

  // 단계 3: owner 필드 확인 및 설정
  const ownerRef = admin.database().ref(`chat-rooms/${roomId}/owner`);
  const ownerSnapshot = await ownerRef.once("value");

  if (!ownerSnapshot.exists()) {
    updates[`chat-rooms/${roomId}/owner`] = authUid;
    logger.info("owner 필드 생성", {roomId, owner: authUid});
  } else {
    logger.warn("owner 필드가 이미 존재함", {
      roomId,
      existingOwner: ownerSnapshot.val(),
    });
  }

  // 단계 4: 임시 필드(_requestingUid) 삭제
  updates[`chat-rooms/${roomId}/_requestingUid`] = null;

  // 단계 5: 모든 업데이트를 한 번에 실행
  if (Object.keys(updates).length > 0) {
    await admin.database().ref().update(updates);
    logger.info("채팅방 필드 설정 완료", {
      roomId,
      owner: authUid,
      createdAt: timestamp,
      roomType: roomData.type,
      updatesCount: Object.keys(updates).length,
    });
  }
}

/**
 * 채팅방 참여 정보 생성 시 비즈니스 로직 처리
 *
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. joinedAt 필드가 없으면 현재 타임스탬프로 생성
 *
 * 참고:
 * - chat-joins 노드는 메시지 생성 시 자동으로 생성됨
 * - 이 함수는 joinedAt 필드만 추가하는 역할
 */
export async function handleChatJoinCreate(
  uid: string,
  roomId: string
): Promise<void> {
  logger.info("채팅방 참여 정보 생성 처리 시작", {
    uid,
    roomId,
  });

  // joinedAt 필드 확인
  const joinedAtRef = admin.database().ref(
    `chat-joins/${uid}/${roomId}/joinedAt`
  );
  const joinedAtSnapshot = await joinedAtRef.once("value");

  // joinedAt이 없으면 현재 시간으로 설정
  if (!joinedAtSnapshot.exists()) {
    const timestamp = Date.now();
    await joinedAtRef.set(timestamp);

    logger.info("joinedAt 필드 생성 완료", {
      uid,
      roomId,
      joinedAt: timestamp,
    });
  } else {
    logger.info("joinedAt 필드가 이미 존재함, 건너뜀", {
      uid,
      roomId,
      joinedAt: joinedAtSnapshot.val(),
    });
  }
}
