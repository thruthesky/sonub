---
name: chat.handler.js
description: chat.handler Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/firebase/functions/src/handlers/chat.handler.js
---

# chat.handler.js

## 개요

**파일 경로**: `firebase/functions/lib/firebase/functions/src/handlers/chat.handler.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

chat.handler Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * Firebase Cloud Functions - Chat Message Handler
 *
 * 채팅 메시지 생성 시 비즈니스 로직을 처리하는 핸들러 모듈입니다.
 * Firebase Cloud Functions의 트리거 함수(onMessageCreate)에서 호출되어
 * 실제 데이터 처리를 수행합니다.
 */
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
exports.handleChatMessageCreate = handleChatMessageCreate;
exports.handleChatRoomCreate = handleChatRoomCreate;
exports.handleChatJoinCreate = handleChatJoinCreate;
exports.handleChatRoomMemberJoin = handleChatRoomMemberJoin;
exports.handleChatRoomMemberLeave = handleChatRoomMemberLeave;
exports.handleChatRoomPinCreate = handleChatRoomPinCreate;
exports.handleChatRoomPinDelete = handleChatRoomPinDelete;
exports.handleChatInvitationCreate = handleChatInvitationCreate;
const logger = __importStar(require("firebase-functions/logger"));
const admin = __importStar(require("firebase-admin"));
const chat_pure_functions_1 = require("../../../../shared/chat.pure-functions");
const fcm_utils_1 = require("../utils/fcm.utils");
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
async function handleChatMessageCreate(messageId, messageData) {
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
        logger.error("senderUid 필드가 비어있음", { messageId });
        throw new Error("senderUid는 필수이지만 정의되지 않았거나 비어있습니다");
    }
    if (!messageData.roomId || messageData.roomId.trim().length === 0) {
        logger.error("roomId 필드가 비어있음", { messageId });
        throw new Error("roomId는 필수이지만 정의되지 않았거나 비어있습니다");
    }
    const roomId = messageData.roomId;
    const senderUid = messageData.senderUid;
    const timestamp = messageData.createdAt || Date.now();
    const messageText = messageData.text || "";
    // 단계 3: 1:1 채팅인지 확인 (공유 함수 사용)
    if ((0, chat_pure_functions_1.isSingleChat)(roomId)) {
        // === 1:1 채팅 처리 ===
        logger.info("1:1 채팅 메시지 처리", { messageId, roomId });
        // 1:1 채팅 roomId에서 두 사용자의 UID 추출
        const uids = (0, chat_pure_functions_1.extractUidsFromSingleRoomId)(roomId);
        if (!uids) {
            logger.error("잘못된 1:1 채팅 roomId 형식", { messageId, roomId });
            return;
        }
        const [uid1, uid2] = uids;
        const partnerUid = senderUid === uid1 ? uid2 : uid1;
        // singleChatListOrder 계산
        // - 발신자: 읽음 상태이므로 그냥 timestamp
        // - 수신자(상대방): 읽지 않은 상태이므로 200 prefix 추가
        const senderSingleListOrder = `${timestamp}`;
        const partnerSingleListOrder = `200${timestamp}`;
        const updates = {};
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
    }
    else {
        // === 그룹/오픈 채팅 처리 ===
        logger.info("그룹/오픈 채팅 메시지 처리", { messageId, roomId });
        // 채팅방 정보 조회
        const roomRef = admin.database().ref(`chat-rooms/${roomId}`);
        const roomSnapshot = await roomRef.once("value");
        if (!roomSnapshot.exists()) {
            logger.error("채팅방 정보를 찾을 수 없음", { messageId, roomId });
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
            logger.warn("채팅방에 members가 없음", { messageId, roomId });
            return;
        }
        const updates = {};
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
                const groupListOrder = memberUid === senderUid ? `${timestamp}` : `200${timestamp}`;
                updates[`${basePath}/groupChatListOrder`] = groupListOrder;
            }
            else if (roomType === "open") {
                // 오픈 채팅인 경우
                const openListOrder = memberUid === senderUid ? `${timestamp}` : `200${timestamp}`;
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
    // ========================================
    // 푸시 알림 전송
    // ========================================
    logger.info("푸시 알림 전송 준비", { messageId, roomId, senderUid });
    try {
        // 단계 1: 발신자 이름 조회
        const senderRef = admin.database().ref(`users/${senderUid}/displayName`);
        const senderSnapshot = await senderRef.once("value");
        const senderName = senderSnapshot.exists() ?
            senderSnapshot.val() :
            "알 수 없음";
        logger.info("발신자 이름 조회 완료", {
            messageId,
            senderUid,
            senderName,
        });
        // 단계 2: 수신자 목록 생성 (구독 상태 확인 포함)
        let recipientUids = [];
        if ((0, chat_pure_functions_1.isSingleChat)(roomId)) {
            // 1:1 채팅: 상대방만 수신자
            const uids = (0, chat_pure_functions_1.extractUidsFromSingleRoomId)(roomId);
            if (uids) {
                const [uid1, uid2] = uids;
                const partnerUid = senderUid === uid1 ? uid2 : uid1;
                // 구독 상태 확인
                const isSubscribed = await (0, fcm_utils_1.isChatSubscribed)(roomId, partnerUid, true);
                if (isSubscribed) {
                    recipientUids = [partnerUid];
                }
                else {
                    logger.info("수신자가 알림 구독 해제 상태", {
                        messageId,
                        roomId,
                        partnerUid,
                    });
                }
            }
        }
        else {
            // 그룹/오픈 채팅: 발신자를 제외한 모든 멤버 (구독 중인 멤버만)
            const roomRef = admin.database().ref(`chat-rooms/${roomId}`);
            const roomSnapshot = await roomRef.once("value");
            if (roomSnapshot.exists()) {
                const roomData = roomSnapshot.val();
                const members = roomData.members || {};
                // 발신자 제외하고 구독 중인 멤버만 필터링
                for (const uid of Object.keys(members)) {
                    if (uid === senderUid)
                        continue; // 발신자 제외
                    const isSubscribed = members[uid] === true;
                    if (isSubscribed) {
                        recipientUids.push(uid);
                    }
                }
                logger.info("그룹 채팅 수신자 목록 생성 완료 (구독 중인 멤버만)", {
                    messageId,
                    roomId,
                    totalMembers: Object.keys(members).length,
                    subscribedRecipients: recipientUids.length,
                });
            }
        }
        if ((0, chat_pure_functions_1.isSingleChat)(roomId)) {
            logger.info("1:1 채팅 수신자 목록 생성 완료", {
                messageId,
                roomId,
                recipientCount: recipientUids.length,
            });
        }
        // 단계 3: FCM 푸시 알림 전송
        if (recipientUids.length > 0) {
            const result = await (0, fcm_utils_1.sendChatMessageNotification)(senderName, messageText, roomId, recipientUids);
            logger.info("푸시 알림 전송 완료", {
                messageId,
                roomId,
                recipientCount: recipientUids.length,
                successCount: result.successCount,
                failureCount: result.failureCount,
                deletedTokenCount: result.deletedTokenCount,
            });
        }
        else {
            logger.info("수신자가 없어 푸시 알림 전송을 건너뜀", {
                messageId,
                roomId,
            });
        }
    }
    catch (error) {
        // 푸시 알림 전송 실패는 치명적이지 않으므로 로그만 남기고 계속 진행
        logger.error("푸시 알림 전송 중 에러 발생", {
            messageId,
            roomId,
            senderUid,
            error,
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
async function handleChatRoomCreate(roomId, roomData, ownerUid) {
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
    const updates = {};
    // 단계 2: createdAt 필드 확인 및 설정
    const createdAtRef = admin.database().ref(`chat-rooms/${roomId}/createdAt`);
    const createdAtSnapshot = await createdAtRef.once("value");
    if (!createdAtSnapshot.exists()) {
        updates[`chat-rooms/${roomId}/createdAt`] = timestamp;
        logger.info("createdAt 필드 생성", { roomId, createdAt: timestamp });
    }
    // 단계 3: members 객체 확인 및 설정 (그룹/오픈 채팅만)
    const roomType = roomData.type;
    if (roomType === "group" || roomType === "open") {
        const membersRef = admin.database().ref(`chat-rooms/${roomId}/members`);
        const membersSnapshot = await membersRef.once("value");
        if (!membersSnapshot.exists()) {
            // members 객체에 owner를 true로 추가
            updates[`chat-rooms/${roomId}/members`] = { [ownerUid]: true };
            logger.info("members 필드 생성", { roomId, members: { [ownerUid]: true } });
        }
        // memberCount는 members 객체의 모든 uid 개수 (true/false 구분 없이)
        const memberCountRef = admin.database().ref(`chat-rooms/${roomId}/memberCount`);
        const memberCountSnapshot = await memberCountRef.once("value");
        if (!memberCountSnapshot.exists()) {
            // members가 새로 생성되면 1, 기존에 있으면 모든 uid 개수
            let totalCount = 1;
            if (membersSnapshot.exists()) {
                const membersData = membersSnapshot.val();
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
async function handleChatJoinCreate(uid, roomId) {
    logger.info("채팅방 참여 정보 생성 처리 시작", {
        uid,
        roomId,
    });
    const timestamp = Date.now();
    const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
    const chatJoinSnapshot = await chatJoinRef.once("value");
    // 이미 완전히 설정된 경우 건너뛰기
    const existingData = chatJoinSnapshot.val();
    if ((existingData === null || existingData === void 0 ? void 0 : existingData.joinedAt) && (existingData === null || existingData === void 0 ? void 0 : existingData.roomType)) {
        logger.info("chat-join 정보가 이미 완전히 설정됨, 건너뜀", {
            uid,
            roomId,
            existingFields: Object.keys(existingData),
        });
        return;
    }
    const updates = {};
    // joinedAt이 없으면 설정
    if (!(existingData === null || existingData === void 0 ? void 0 : existingData.joinedAt)) {
        updates.joinedAt = timestamp;
    }
    // 1:1 채팅인지 확인
    if ((0, chat_pure_functions_1.isSingleChat)(roomId)) {
        logger.info("1:1 채팅 참여 정보 처리", { uid, roomId });
        // 1:1 채팅 roomId에서 상대방 UID 추출
        const uids = (0, chat_pure_functions_1.extractUidsFromSingleRoomId)(roomId);
        if (!uids) {
            logger.error("잘못된 1:1 채팅 roomId 형식", { uid, roomId });
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
    }
    else {
        // 그룹/오픈 채팅 처리
        logger.info("그룹/오픈 채팅 참여 정보 처리", { uid, roomId });
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
        }
        else {
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
            }
            else if (roomType === "open") {
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
async function handleChatRoomMemberJoin(roomId, uid) {
    var _a;
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
    const membersData = membersSnapshot.val();
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
    }
    else {
        logger.info("채팅방에 메시지가 없음", {
            roomId,
            uid,
        });
    }
    // 단계 6: chat-joins 정보 업데이트
    const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
    const chatJoinSnapshot = await chatJoinRef.once("value");
    const timestamp = Date.now();
    const updates = {
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
    }
    else if (roomType === "open") {
        updates.openChatListOrder = `${timestamp}`;
    }
    // 그룹/오픈 통합 정렬 필드도 설정
    if (roomType === "group" || roomType === "open") {
        updates.openAndGroupChatListOrder = timestamp;
    }
    // 모든 채팅방 통합 정렬 필드 설정
    updates.allChatListOrder = timestamp;
    // joinedAt은 없는 경우에만 설정
    if (!chatJoinSnapshot.exists() || !((_a = chatJoinSnapshot.val()) === null || _a === void 0 ? void 0 : _a.joinedAt)) {
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
async function handleChatRoomMemberLeave(roomId, uid) {
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
    const membersData = membersSnapshot.val();
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
/**
 * 채팅방 핀 생성 시 비즈니스 로직 처리
 *
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. chat-joins/{uid}/{roomId}의 모든 데이터 읽기
 * 2. xxxListOrder 또는 xxxChatListOrder로 끝나는 모든 필드 찾기
 * 3. 각 order 필드에 "500" prefix 추가
 *
 * 참고:
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 * - 모든 order 필드에 일관되게 prefix 적용
 * - Base timestamp는 유지하되 prefix만 변경
 */
async function handleChatRoomPinCreate(uid, roomId) {
    logger.info("채팅방 핀 생성 처리 시작", {
        uid,
        roomId,
    });
    // chat-joins 데이터 읽기
    const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
    const snapshot = await chatJoinRef.once("value");
    if (!snapshot.exists()) {
        logger.error("chat-joins 노드가 존재하지 않음", { uid, roomId });
        return;
    }
    const data = snapshot.val();
    // xxxListOrder 또는 xxxChatListOrder 필드 찾기
    const updates = {};
    for (const key of Object.keys(data)) {
        // order 필드만 처리 (ListOrder 또는 ChatListOrder로 끝나는 필드)
        if (!key.endsWith("ListOrder") &&
            !key.endsWith("ChatListOrder")) {
            continue;
        }
        const currentValue = String(data[key]);
        // Base timestamp 추출 (prefix 제거)
        let baseTimestamp;
        if (currentValue.startsWith("500")) {
            // 이미 "500" prefix가 있으면 건너뜀
            continue;
        }
        else if (currentValue.startsWith("200")) {
            baseTimestamp = currentValue.slice(3); // "200" 제거
        }
        else {
            baseTimestamp = currentValue;
        }
        // 핀 설정: "500" prefix 추가
        const newValue = `500${baseTimestamp}`;
        // 값이 실제로 변경된 경우에만 업데이트
        if (newValue !== currentValue) {
            updates[key] = newValue;
            logger.info("Order 필드 업데이트 예정 (핀 설정)", {
                uid,
                roomId,
                field: key,
                from: currentValue,
                to: newValue,
            });
        }
    }
    // 업데이트할 필드가 있는 경우에만 실행
    if (Object.keys(updates).length > 0) {
        await chatJoinRef.update(updates);
        logger.info("채팅방 핀 생성 완료", {
            uid,
            roomId,
            updatedFields: Object.keys(updates),
            updatesCount: Object.keys(updates).length,
        });
    }
    else {
        logger.info("업데이트할 order 필드가 없음 (핀 설정)", {
            uid,
            roomId,
        });
    }
}
/**
 * 채팅방 핀 삭제 시 비즈니스 로직 처리
 *
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. chat-joins/{uid}/{roomId}의 모든 데이터 읽기
 * 2. xxxListOrder 또는 xxxChatListOrder로 끝나는 모든 필드 찾기
 * 3. newMessageCount > 0이면 "200" prefix 추가
 * 4. newMessageCount === 0이면 prefix 제거 (순수 timestamp)
 *
 * 참고:
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 * - 모든 order 필드에 일관되게 prefix 적용
 * - Base timestamp는 유지하되 prefix만 변경
 */
async function handleChatRoomPinDelete(uid, roomId) {
    var _a;
    logger.info("채팅방 핀 삭제 처리 시작", {
        uid,
        roomId,
    });
    // chat-joins 데이터 읽기
    const chatJoinRef = admin.database().ref(`chat-joins/${uid}/${roomId}`);
    const snapshot = await chatJoinRef.once("value");
    if (!snapshot.exists()) {
        logger.error("chat-joins 노드가 존재하지 않음", { uid, roomId });
        return;
    }
    const data = snapshot.val();
    const newMessageCount = Number((_a = data.newMessageCount) !== null && _a !== void 0 ? _a : 0);
    // xxxListOrder 또는 xxxChatListOrder 필드 찾기
    const updates = {};
    for (const key of Object.keys(data)) {
        // order 필드만 처리 (ListOrder 또는 ChatListOrder로 끝나는 필드)
        if (!key.endsWith("ListOrder") &&
            !key.endsWith("ChatListOrder")) {
            continue;
        }
        const currentValue = String(data[key]);
        // Base timestamp 추출 (prefix 제거)
        let baseTimestamp;
        if (currentValue.startsWith("500")) {
            baseTimestamp = currentValue.slice(3); // "500" 제거
        }
        else if (currentValue.startsWith("200")) {
            baseTimestamp = currentValue.slice(3); // "200" 제거
        }
        else {
            baseTimestamp = currentValue;
        }
        // 핀 해제: newMessageCount에 따라 "200" 추가 또는 prefix 제거
        let newValue;
        if (newMessageCount > 0) {
            newValue = `200${baseTimestamp}`;
        }
        else {
            newValue = baseTimestamp;
        }
        // 값이 실제로 변경된 경우에만 업데이트
        if (newValue !== currentValue) {
            updates[key] = newValue;
            logger.info("Order 필드 업데이트 예정 (핀 해제)", {
                uid,
                roomId,
                field: key,
                from: currentValue,
                to: newValue,
                newMessageCount,
            });
        }
    }
    // 업데이트할 필드가 있는 경우에만 실행
    if (Object.keys(updates).length > 0) {
        await chatJoinRef.update(updates);
        logger.info("채팅방 핀 삭제 완료", {
            uid,
            roomId,
            newMessageCount,
            updatedFields: Object.keys(updates),
            updatesCount: Object.keys(updates).length,
        });
    }
    else {
        logger.info("업데이트할 order 필드가 없음 (핀 해제)", {
            uid,
            roomId,
        });
    }
}
/**
 * 채팅 초대장 생성 시 비즈니스 로직 처리
 *
 * @param inviteeUid - 초대받은 사용자 UID
 * @param roomId - 채팅방 ID
 * @param invitationData - 초대장 데이터 (클라이언트가 생성한 최소 정보)
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. 채팅방 정보 조회 (roomName, roomType)
 * 2. 초대한 사람 정보 조회 (displayName)
 * 3. 초대받은 사람의 언어 코드 조회
 * 4. 초대 메시지 생성 (i18n 사용, 언어별)
 * 5. 초대장 정보 업데이트 (createdAt, invitationOrder, roomName, roomType, inviterName, message)
 * 6. FCM 푸시 알림 전송 (초대받은 사람의 언어로)
 *
 * 참고:
 * - 클라이언트는 최소한의 정보만 저장 (roomId, inviterUid)
 * - Cloud Functions가 나머지 정보를 자동으로 채움 (createdAt, invitationOrder, roomName, roomType, inviterName, message)
 * - 이미 참여 중인 멤버인지 검증 (그룹/오픈 채팅방만)
 */
async function handleChatInvitationCreate(inviteeUid, roomId, invitationData) {
    logger.info("채팅 초대장 생성 처리 시작", {
        inviteeUid,
        roomId,
        inviterUid: invitationData.inviterUid,
    });
    // 필수 필드 검증
    if (!invitationData.inviterUid) {
        logger.error("inviterUid 필드가 없음", { inviteeUid, roomId });
        throw new Error("inviterUid는 필수이지만 정의되지 않았습니다");
    }
    const inviterUid = invitationData.inviterUid;
    const updates = {};
    // 1. 채팅방 정보 조회
    const roomRef = admin.database().ref(`chat-rooms/${roomId}`);
    const roomSnapshot = await roomRef.once("value");
    if (!roomSnapshot.exists()) {
        logger.error("채팅방을 찾을 수 없음", { roomId });
        throw new Error(`채팅방을 찾을 수 없습니다: ${roomId}`);
    }
    const roomData = roomSnapshot.val();
    const roomName = roomData.name || "채팅방";
    const roomType = roomData.type;
    logger.info("채팅방 정보 조회 완료", {
        roomId,
        roomName,
        roomType,
    });
    // 1:1 채팅방에 대한 초대는 허용하지 않음
    if ((0, chat_pure_functions_1.isSingleChat)(roomId)) {
        logger.warn("1:1 채팅방에 대한 초대 시도, 무시함", {
            roomId,
            inviterUid,
            inviteeUid,
        });
        return { success: false, inviteeUid, roomId };
    }
    // 이미 참여 중인 멤버인지 확인
    if (roomData.members && roomData.members[inviteeUid] === true) {
        logger.warn("이미 참여 중인 멤버에게 초대 시도, 무시함", {
            roomId,
            inviteeUid,
        });
        return { success: false, inviteeUid, roomId };
    }
    // 2. 초대한 사람 정보 조회
    const inviterRef = admin.database().ref(`users/${inviterUid}`);
    const inviterSnapshot = await inviterRef.once("value");
    const inviterData = inviterSnapshot.val();
    const inviterName = (inviterData === null || inviterData === void 0 ? void 0 : inviterData.displayName) || "사용자";
    logger.info("초대한 사람 정보 조회 완료", {
        inviterUid,
        inviterName,
    });
    // 3. 초대받은 사람의 언어 코드 조회
    const inviteeRef = admin.database().ref(`users/${inviteeUid}`);
    const inviteeSnapshot = await inviteeRef.once("value");
    const inviteeData = inviteeSnapshot.val();
    const languageCode = (inviteeData === null || inviteeData === void 0 ? void 0 : inviteeData.languageCode) || "en";
    logger.info("초대받은 사람 언어 코드 조회 완료", {
        inviteeUid,
        languageCode,
    });
    // 4. 초대 메시지 생성 (i18n 사용)
    // i18n.ts의 t() 함수 임포트 필요
    const { t } = await Promise.resolve().then(() => __importStar(require("../i18n")));
    const message = t(languageCode, "chatInvitation.message", {
        inviterName,
        roomName,
    });
    logger.info("초대 메시지 생성 완료", {
        languageCode,
        message,
    });
    // 5. 초대장 정보 업데이트
    // 서버 타임스탬프 생성
    const now = Date.now();
    // createdAt과 invitationOrder 추가
    updates[`chat-invitations/${inviteeUid}/${roomId}/createdAt`] = now;
    updates[`chat-invitations/${inviteeUid}/${roomId}/invitationOrder`] = `-${now}`;
    // 나머지 필드 추가
    updates[`chat-invitations/${inviteeUid}/${roomId}/roomName`] = roomName;
    updates[`chat-invitations/${inviteeUid}/${roomId}/roomType`] = roomType;
    updates[`chat-invitations/${inviteeUid}/${roomId}/inviterName`] = inviterName;
    updates[`chat-invitations/${inviteeUid}/${roomId}/message`] = message;
    await admin.database().ref().update(updates);
    logger.info("초대장 정보 업데이트 완료", {
        inviteeUid,
        roomId,
        updatesCount: Object.keys(updates).length,
    });
    // 6. FCM 푸시 알림 전송
    try {
        const { getFcmTokensByUid, sendFcmNotificationBatch } = await Promise.resolve().then(() => __importStar(require("../utils/fcm.utils")));
        // 초대받은 사람의 FCM 토큰 조회
        const tokens = await getFcmTokensByUid(inviteeUid);
        if (tokens.length > 0) {
            // 푸시 알림 제목과 본문 (i18n 사용)
            const notificationTitle = t(languageCode, "chatInvitation.title");
            const notificationBody = t(languageCode, "chatInvitation.body", {
                inviterName,
                roomName,
            });
            logger.info("FCM 푸시 알림 전송 시작", {
                inviteeUid,
                tokenCount: tokens.length,
                title: notificationTitle,
                body: notificationBody,
            });
            const fcmResult = await sendFcmNotificationBatch(tokens, notificationTitle, notificationBody, {
                type: "chat-invitation",
                roomId,
                inviterUid,
                inviterName,
            });
            logger.info("FCM 푸시 알림 전송 완료", {
                inviteeUid,
                roomId,
                successCount: fcmResult.successCount,
                failureCount: fcmResult.failureCount,
                deletedTokenCount: fcmResult.deletedTokenCount,
            });
        }
        else {
            logger.info("FCM 토큰이 없어 푸시 알림 전송을 건너뜀", {
                inviteeUid,
            });
        }
    }
    catch (fcmError) {
        logger.error("FCM 푸시 알림 전송 실패 (계속 진행)", {
            inviteeUid,
            roomId,
            error: fcmError,
        });
        // FCM 실패는 무시하고 계속 진행
    }
    logger.info("채팅 초대장 생성 처리 완료", {
        inviteeUid,
        roomId,
    });
    return { success: true, inviteeUid, roomId };
}
//# sourceMappingURL=chat.handler.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
