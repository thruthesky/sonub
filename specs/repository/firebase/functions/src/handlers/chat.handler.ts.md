---
title: "firebase/functions/src/handlers/chat.handler.ts"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/functions/src/handlers/chat.handler.ts"
spec_type: "repository-source"
---

## 개요

이 파일은 chat.handler.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
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
} from "../../../../shared/chat.pure-functions";

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
 * 3. 1:1 채팅인 경우:
 *    - 발신자와 수신자의 chat-join 노드 업데이트
 *    - singleChatListOrder, allChatListOrder 설정
 *    - 수신자의 newMessageCount 증가
 * 4. 그룹/오픈 채팅인 경우:
 *    - chat-rooms에서 members 정보 읽기
 *    - 각 member의 chat-join 노드 업데이트
 *    - groupChatListOrder 또는 openChatListOrder 설정
 *    - openAndGroupChatListOrder, allChatListOrder 설정
 *    - 발신자를 제외한 모든 member의 newMessageCount 증가
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
  const timestamp = messageData.createdAt || Date.now();
  const messageText = messageData.text || "";

  // 단계 3: 1:1 채팅인지 확인 (공유 함수 사용)
  if (isSingleChat(roomId)) {
    // === 1:1 채팅 처리 ===
    logger.info("1:1 채팅 메시지 처리", {messageId, roomId});

    // 1:1 채팅 roomId에서 두 사용자의 UID 추출
    const uids = extractUidsFromSingleRoomId(roomId);
    if (!uids) {
      logger.error("잘못된 1:1 채팅 roomId 형식", {messageId, roomId});
      return;
    }

    const [uid1, uid2] = uids;
    const partnerUid = senderUid === uid1 ? uid2 : uid1;

    // singleChatListOrder 계산
    // - 발신자: 읽음 상태이므로 그냥 timestamp
    // - 수신자(상대방): 읽지 않은 상태이므로 200 prefix 추가
    const senderSingleListOrder = `${timestamp}`;
    const partnerSingleListOrder = `200${timestamp}`;

    const updates: {[key: string]: unknown} = {};

    // 발신자의 chat-join 업데이트 (읽음 상태)
    updates[`chat-joins/${senderUid}/${roomId}/roomId`] = roomId;
    updates[`chat-joins/${senderUid}/${roomId}/roomType`] = "single";
    updates[`chat-joins/${senderUid}/${roomId}/partnerUid`] = partnerUid;
    updates[`chat-joins/${senderUid}/${roomId}/lastMessageText`] = messageText;
    updates[`chat-joins/${senderUid}/${roomId}/lastMessageAt`] = timestamp;
    updates[`chat-joins/${senderUid}/${roomId}/updatedAt`] = timestamp;
    updates[`chat-joins/${senderUid}/${roomId}/singleChatListOrder`] =
      senderSingleListOrder;
    updates[`chat-joins/${senderUid}/${roomId}/allChatListOrder`] = timestamp;

    // 수신자의 chat-join 업데이트 (읽지 않은 상태, 200 prefix 추가)
    updates[`chat-joins/${partnerUid}/${roomId}/roomId`] = roomId;
    updates[`chat-joins/${partnerUid}/${roomId}/roomType`] = "single";
    updates[`chat-joins/${partnerUid}/${roomId}/partnerUid`] = senderUid;
    updates[`chat-joins/${partnerUid}/${roomId}/lastMessageText`] = messageText;
    updates[`chat-joins/${partnerUid}/${roomId}/lastMessageAt`] = timestamp;
    updates[`chat-joins/${partnerUid}/${roomId}/updatedAt`] = timestamp;
    updates[`chat-joins/${partnerUid}/${roomId}/singleChatListOrder`] =
      partnerSingleListOrder;
    updates[`chat-joins/${partnerUid}/${roomId}/allChatListOrder`] = timestamp;

    // 수신자의 읽지 않은 메시지 카운터 증가
    updates[`chat-joins/${partnerUid}/${roomId}/newMessageCount`] =
      admin.database.ServerValue.increment(1);

    // 모든 업데이트를 한 번에 실행
    await admin.database().ref().update(updates);

    logger.info("1:1 채팅 chat-joins 업데이트 완료", {
      messageId,
      roomId,
      senderUid,
      partnerUid,
      timestamp,
      updatesCount: Object.keys(updates).length,
    });
  } else {
    // === 그룹/오픈 채팅 처리 ===
    logger.info("그룹/오픈 채팅 메시지 처리", {messageId, roomId});

    // 채팅방 정보 조회
    const roomRef = admin.database().ref(`chat-rooms/${roomId}`);
    const roomSnapshot = await roomRef.once("value");

    if (!roomSnapshot.exists()) {
      logger.error("채팅방 정보를 찾을 수 없음", {messageId, roomId});
      return;
    }

    const roomData = roomSnapshot.val();
    const roomType = roomData.type || "group"; // 기본값: group
    const roomName = roomData.name || roomId;
    const members = roomData.members || {};

    logger.info("채팅방 정보 조회 완료", {
      messageId,
      roomId,
      roomType,
      roomName,
      memberCount: Object.keys(members).length,
    });

    // members가 없으면 처리 종료
    if (Object.keys(members).length === 0) {
      logger.warn("채팅방에 members가 없음", {messageId, roomId});
      return;
    }

    const updates: {[key: string]: unknown} = {};

    // 각 member의 chat-joins 업데이트
    for (const memberUid of Object.keys(members)) {
      const basePath = `chat-joins/${memberUid}/${roomId}`;

      // 공통 필드
      updates[`${basePath}/roomId`] = roomId;
      updates[`${basePath}/roomType`] = roomType;
      updates[`${basePath}/roomName`] = roomName;
      updates[`${basePath}/lastMessageText`] = messageText;
      updates[`${basePath}/lastMessageAt`] = timestamp;
      updates[`${basePath}/updatedAt`] = timestamp;
      updates[`${basePath}/allChatListOrder`] = timestamp;

      // 채팅방 타입에 따른 정렬 필드
      if (roomType === "group") {
        // 그룹 채팅인 경우
        // 발신자: 읽음 상태, 수신자: 읽지 않은 상태 (200 prefix)
        const groupListOrder =
          memberUid === senderUid ? `${timestamp}` : `200${timestamp}`;
        updates[`${basePath}/groupChatListOrder`] = groupListOrder;
      } else if (roomType === "open") {
        // 오픈 채팅인 경우
        const openListOrder =
          memberUid === senderUid ? `${timestamp}` : `200${timestamp}`;
        updates[`${basePath}/openChatListOrder`] = openListOrder;
      }

      // 그룹/오픈 통합 정렬 필드
      updates[`${basePath}/openAndGroupChatListOrder`] = timestamp;

      // newMessageCount 증가 (발신자 제외)
      if (memberUid !== senderUid) {
        updates[`${basePath}/newMessageCount`] =
          admin.database.ServerValue.increment(1);
      }
    }

    // 모든 업데이트를 한 번에 실행
    await admin.database().ref().update(updates);

    logger.info("그룹/오픈 채팅 chat-joins 업데이트 완료", {
      messageId,
      roomId,
      roomType,
      senderUid,
      memberCount: Object.keys(members).length,
      timestamp,
      updatesCount: Object.keys(updates).length,
    });
  }
}

/**
 * 채팅방 생성 시 비즈니스 로직 처리
 *
 * @param roomId - 생성된 채팅방 ID
 * @param roomData - 채팅방 데이터
 * @param ownerUid - 채팅방 소유자 UID (보안 규칙으로 검증됨)
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. owner UID 유효성 검사
 * 2. createdAt 필드 자동 생성 (타임스탬프)
 * 3. memberCount 필드를 1로 초기화 (그룹/오픈 채팅만)
 *
 * 보안:
 * - owner는 보안 규칙에 의해 auth.uid와 동일하게 검증됨
 * - createdAt과 memberCount는 Cloud Functions에서만 설정 가능
 * - RTDB 보안 규칙과 함께 작동하여 데이터 무결성 보장
 */
export async function handleChatRoomCreate(
  roomId: string,
  roomData: Record<string, unknown>,
  ownerUid: string | undefined
): Promise<void> {
  logger.info("채팅방 생성 처리 시작", {
    roomId,
    owner: ownerUid,
    roomType: roomData.type,
  });

  // 단계 1: owner UID 유효성 검사
  if (!ownerUid || ownerUid.trim().length === 0) {
    logger.error("owner가 설정되지 않은 채팅방 생성 시도", {
      roomId,
    });
    throw new Error("채팅방 owner가 필요합니다");
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

  // 단계 3: members 객체 확인 및 설정 (그룹/오픈 채팅만)
  const roomType = roomData.type as string;
  if (roomType === "group" || roomType === "open") {
    const membersRef = admin.database().ref(
      `chat-rooms/${roomId}/members`
    );
    const membersSnapshot = await membersRef.once("value");

    if (!membersSnapshot.exists()) {
      // members 객체에 owner를 true로 추가
      updates[`chat-rooms/${roomId}/members`] = {[ownerUid]: true};
      logger.info("members 필드 생성", {roomId, members: {[ownerUid]: true}});
    }

    // memberCount는 members 객체의 모든 uid 개수 (true/false 구분 없이)
    const memberCountRef = admin.database().ref(
      `chat-rooms/${roomId}/memberCount`
    );
    const memberCountSnapshot = await memberCountRef.once("value");

    if (!memberCountSnapshot.exists()) {
      // members가 새로 생성되면 1, 기존에 있으면 모든 uid 개수
      let totalCount = 1;
      if (membersSnapshot.exists()) {
        const membersData = membersSnapshot.val() as Record<string, boolean>;
        totalCount = Object.keys(membersData).length;
      }
      updates[`chat-rooms/${roomId}/memberCount`] = totalCount;
      logger.info("memberCount 필드 생성", {
        roomId,
        memberCount: totalCount,
      });
    }
  }

  // 단계 4: 모든 업데이트를 한 번에 실행
  if (Object.keys(updates).length > 0) {
    await admin.database().ref().update(updates);
    logger.info("채팅방 필드 설정 완료", {
      roomId,
      owner: ownerUid,
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
 * 1. 1:1 채팅인 경우:
 *    - singleChatListOrder 설정 (timestamp)
 *    - allChatListOrder 설정 (timestamp)
 *    - partnerUid 설정
 *    - roomType 설정 ("single")
 * 2. 그룹/오픈 채팅인 경우:
 *    - chat-rooms에서 정보 읽기
 *    - roomType에 따라 정렬 필드 설정
 *    - roomName 설정
 * 3. joinedAt 필드가 없으면 현재 타임스탬프로 생성
 *
 * 참고:
 * - 클라이언트가 직접 chat-joins 노드를 생성할 수 있음
 * - 이 함수는 필요한 메타데이터를 자동으로 추가
 */
export async function handleChatJoinCreate(
  uid: string,
  roomId: string
): Promise<void> {
  logger.info("채팅방 참여 정보 생성 처리 시작", {
    uid,
    roomId,
  });

  const timestamp = Date.now();
  const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
  const chatJoinSnapshot = await chatJoinRef.once("value");

  // 이미 완전히 설정된 경우 건너뛰기
  const existingData = chatJoinSnapshot.val();
  if (existingData?.joinedAt && existingData?.roomType) {
    logger.info("chat-join 정보가 이미 완전히 설정됨, 건너뜀", {
      uid,
      roomId,
      existingFields: Object.keys(existingData),
    });
    return;
  }

  const updates: Record<string, unknown> = {};

  // joinedAt이 없으면 설정
  if (!existingData?.joinedAt) {
    updates.joinedAt = timestamp;
  }

  // 1:1 채팅인지 확인
  if (isSingleChat(roomId)) {
    logger.info("1:1 채팅 참여 정보 처리", {uid, roomId});

    // 1:1 채팅 roomId에서 상대방 UID 추출
    const uids = extractUidsFromSingleRoomId(roomId);
    if (!uids) {
      logger.error("잘못된 1:1 채팅 roomId 형식", {uid, roomId});
      return;
    }

    const [uid1, uid2] = uids;
    const partnerUid = uid === uid1 ? uid2 : uid1;

    // 필수 필드 설정
    updates.roomId = roomId;
    updates.roomType = "single";
    updates.partnerUid = partnerUid;
    updates.singleChatListOrder = `${timestamp}`;
    updates.allChatListOrder = timestamp;

    logger.info("1:1 채팅 필드 설정 완료", {
      uid,
      roomId,
      partnerUid,
      fieldsToUpdate: Object.keys(updates),
    });
  } else {
    // 그룹/오픈 채팅 처리
    logger.info("그룹/오픈 채팅 참여 정보 처리", {uid, roomId});

    // 채팅방 정보 조회
    const roomRef = admin.database().ref(`chat-rooms/${roomId}`);
    const roomSnapshot = await roomRef.once("value");

    if (!roomSnapshot.exists()) {
      logger.warn("채팅방 정보를 찾을 수 없음, 기본값으로 설정", {
        uid,
        roomId,
      });
      // 기본값으로 설정
      updates.roomId = roomId;
      updates.roomType = "group";
      updates.allChatListOrder = timestamp;
    } else {
      const roomData = roomSnapshot.val();
      const roomType = roomData.type || "group";
      const roomName = roomData.name || roomId;

      // 필수 필드 설정
      updates.roomId = roomId;
      updates.roomType = roomType;
      updates.roomName = roomName;
      updates.allChatListOrder = timestamp;

      // roomType에 따라 정렬 필드 설정
      if (roomType === "group") {
        updates.groupChatListOrder = `${timestamp}`;
        updates.openAndGroupChatListOrder = timestamp;
      } else if (roomType === "open") {
        updates.openChatListOrder = `${timestamp}`;
        updates.openAndGroupChatListOrder = timestamp;
      }

      logger.info("그룹/오픈 채팅 필드 설정 완료", {
        uid,
        roomId,
        roomType,
        roomName,
        fieldsToUpdate: Object.keys(updates),
      });
    }
  }

  // 업데이트 실행
  await chatJoinRef.update(updates);

  logger.info("chat-join 정보 업데이트 완료", {
    uid,
    roomId,
    updatedFields: Object.keys(updates),
  });
}

/**
 * 채팅방 멤버 입장 시 비즈니스 로직 처리
 *
 * @param roomId - 채팅방 ID
 * @param uid - 입장한 사용자 UID
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. chat-rooms/{roomId}/members 아래의 모든 uid 읽기
 * 2. 모든 uid의 개수 세기 (true/false 구분 없이)
 * 3. memberCount 필드를 증가시킨다
 * 4. 채팅방 정보 조회 (roomType, roomName)
 * 5. 해당 채팅방의 마지막 메시지 조회 (chat-messages에서 roomOrder로 정렬)
 * 6. chat-joins/{uid}/{roomId}에 다음 정보 저장:
 *    - roomType, roomName
 *    - lastMessageText, lastMessageAt (메시지가 있는 경우)
 *    - newMessageCount (0으로 초기화)
 *    - 정렬 필드들 (groupChatListOrder, openChatListOrder 등)
 *
 * 참고:
 * - members 필드 구조: chat-rooms/{roomId}/members/{uid}: boolean
 * - true: 채팅방 참여 중, 메시지 알림을 받음
 * - onValueCreated 트리거로 멤버 입장 감지
 * - 마지막 메시지는 roomOrder 필드로 효율적으로 조회
 */
export async function handleChatRoomMemberJoin(
  roomId: string,
  uid: string
): Promise<void> {
  logger.info("채팅방 멤버 입장 처리 시작", {
    roomId,
    uid,
  });

  // 단계 1: members 필드 읽기
  const membersRef = admin.database().ref(`chat-rooms/${roomId}/members`);
  const membersSnapshot = await membersRef.once("value");

  if (!membersSnapshot.exists()) {
    logger.warn("members 필드가 없음, memberCount를 0으로 설정", {
      roomId,
    });
    await admin.database().ref(`chat-rooms/${roomId}/memberCount`).set(0);
    return;
  }

  // 단계 2: 모든 uid 개수 세기 (true/false 구분 없이)
  const membersData = membersSnapshot.val() as Record<string, boolean>;
  const totalMemberCount = Object.keys(membersData).length;

  logger.info("멤버 입장 후 참여자 수 계산 완료", {
    roomId,
    uid,
    memberCount: totalMemberCount,
  });

  // 단계 3: memberCount 업데이트
  await admin
    .database()
    .ref(`chat-rooms/${roomId}/memberCount`)
    .set(totalMemberCount);

  logger.info("memberCount 업데이트 완료 (멤버 입장)", {
    roomId,
    uid,
    memberCount: totalMemberCount,
  });

  // 단계 4: 채팅방 정보 조회
  const roomRef = admin.database().ref(`chat-rooms/${roomId}`);
  const roomSnapshot = await roomRef.once("value");

  if (!roomSnapshot.exists()) {
    logger.warn("채팅방 정보가 없음, chat-joins 업데이트를 건너뜀", {
      roomId,
      uid,
    });
    return;
  }

  const roomData = roomSnapshot.val();
  const roomType = roomData.type || "unknown";
  const roomName = roomData.name || roomId;

  logger.info("채팅방 정보 조회 완료", {
    roomId,
    uid,
    roomType,
    roomName,
  });

  // 단계 5: 마지막 채팅 메시지 조회
  const messagesRef = admin.database().ref("chat-messages");
  const lastMessageSnapshot = await messagesRef
    .orderByChild("roomOrder")
    .startAt(`-${roomId}-`)
    .endAt(`-${roomId}-\uf8ff`)
    .limitToLast(1)
    .once("value");

  let lastMessageText = "";
  let lastMessageAt = 0;

  if (lastMessageSnapshot.exists()) {
    const messages = lastMessageSnapshot.val();
    const messageId = Object.keys(messages)[0];
    const lastMessage = messages[messageId];

    lastMessageText = lastMessage.text || "";
    lastMessageAt = lastMessage.createdAt || 0;

    logger.info("마지막 메시지 조회 완료", {
      roomId,
      uid,
      messageId,
      lastMessageText,
      lastMessageAt,
    });
  } else {
    logger.info("채팅방에 메시지가 없음", {
      roomId,
      uid,
    });
  }

  // 단계 6: chat-joins 정보 업데이트
  const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
  const chatJoinSnapshot = await chatJoinRef.once("value");

  const timestamp = Date.now();
  const updates: Record<string, string | number> = {
    roomType,
    roomName,
    newMessageCount: 0,
  };

  // 마지막 메시지 정보 추가 (메시지가 있는 경우에만)
  if (lastMessageAt > 0) {
    updates.lastMessageText = lastMessageText;
    updates.lastMessageAt = lastMessageAt;
  }

  // roomType에 따라 적절한 정렬 필드 설정
  if (roomType === "group") {
    updates.groupChatListOrder = `${timestamp}`;
  } else if (roomType === "open") {
    updates.openChatListOrder = `${timestamp}`;
  }
  // 그룹/오픈 통합 정렬 필드도 설정
  if (roomType === "group" || roomType === "open") {
    updates.openAndGroupChatListOrder = timestamp;
  }
  // 모든 채팅방 통합 정렬 필드 설정
  updates.allChatListOrder = timestamp;

  // joinedAt은 없는 경우에만 설정
  if (!chatJoinSnapshot.exists() || !chatJoinSnapshot.val()?.joinedAt) {
    updates.joinedAt = timestamp;
  }

  await chatJoinRef.update(updates);

  logger.info("chat-joins 상세 정보 업데이트 완료", {
    roomId,
    uid,
    updates,
  });
}

/**
 * 채팅방 멤버 퇴장 시 비즈니스 로직 처리
 *
 * @param roomId - 채팅방 ID
 * @param uid - 퇴장한 사용자 UID
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. chat-rooms/{roomId}/members 아래의 모든 uid 읽기
 * 2. 모든 uid의 개수 세기 (true/false 구분 없이)
 * 3. memberCount 필드를 감소시킴
 *
 * 참고:
 * - members 필드에서 uid가 삭제되면 퇴장으로 간주
 * - onValueDeleted 트리거로 멤버 퇴장 감지
 */
export async function handleChatRoomMemberLeave(
  roomId: string,
  uid: string
): Promise<void> {
  logger.info("채팅방 멤버 퇴장 처리 시작", {
    roomId,
    uid,
  });

  // 단계 1: members 필드 읽기
  const membersRef = admin.database().ref(`chat-rooms/${roomId}/members`);
  const membersSnapshot = await membersRef.once("value");

  if (!membersSnapshot.exists()) {
    logger.info("members 필드가 없음, memberCount를 0으로 설정", {
      roomId,
    });
    await admin.database().ref(`chat-rooms/${roomId}/memberCount`).set(0);
    return;
  }

  // 단계 2: 모든 uid 개수 세기 (true/false 구분 없이)
  const membersData = membersSnapshot.val() as Record<string, boolean>;
  const totalMemberCount = Object.keys(membersData).length;

  logger.info("멤버 퇴장 후 참여자 수 계산 완료", {
    roomId,
    uid,
    memberCount: totalMemberCount,
  });

  // 단계 3: memberCount 업데이트
  await admin
    .database()
    .ref(`chat-rooms/${roomId}/memberCount`)
    .set(totalMemberCount);

  logger.info("memberCount 업데이트 완료 (멤버 퇴장)", {
    roomId,
    uid,
    memberCount: totalMemberCount,
  });

  // 단계 4: chat-joins 노드 삭제
  const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
  await chatJoinRef.remove();

  logger.info("chat-joins 노드 삭제 완료", {
    roomId,
    uid,
  });
}

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
