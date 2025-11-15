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
  onDocumentCreated,
  onDocumentDeleted,
  onDocumentWritten,
} from "firebase-functions/v2/firestore";
import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";

// 타입 임포트
import {UserData, ChatMessage} from "./types";

// 비즈니스 로직 핸들러 임포트
import {
  handleUserCreate,
  handleUserDisplayNameUpdate,
  handleUserPhotoUrlUpdate,
  handleUserBirthYearMonthDayUpdate,
  handleUserGenderUpdate,
} from "./handlers/user.handler";
import {
  handleChatMessageCreate,
  handleChatJoinCreate,
  handleChatRoomCreate,
  handleChatRoomMemberJoin,
  handleChatRoomMemberLeave,
  handleChatRoomPinCreate,
  handleChatRoomPinDelete,
  handleChatInvitationCreate,
} from "./handlers/chat.handler";
import { handleNewMessageCountWritten } from "./handlers/chat.new-message-count.handler";

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
 * 트리거 경로: users/{uid}
 *
 * 수행 작업:
 * 1. createdAt 필드 자동 생성 및 users/{uid}/createdAt 직접 저장
 * 2. handleUserCreate() 함수를 통해 모든 사용자 데이터 정규화 및 동기화 수행
 *    - updatedAt 필드 자동 생성
 *    - displayNameLowerCase 자동 생성
 *    - photoUrl 처리
 *    - users/{uid} 문서 업데이트
 *    - stats 문서 동기화
 *    - stats/counters/user +1 (전체 사용자 통계 업데이트)
 */
export const onUserCreate = onDocumentCreated(
  {
    document: "users/{uid}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const userData = (event.data?.data() || {}) as UserData;

    logger.info("새 사용자 등록 감지", {
      uid,
      displayName: userData.displayName ?? null,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleUserCreate(uid, userData);
  }
);



/**
 * 사용자 문서 필드 변경 시 트리거 (통합)
 *
 * 트리거 경로: users/{uid}
 * 트리거 이벤트: onDocumentWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * - displayName 필드 변경 시: handleUserDisplayNameUpdate 호출
 * - photoUrl 필드 변경 시: handleUserPhotoUrlUpdate 호출
 * - birthYearMonthDay 필드 변경 시: handleUserBirthYearMonthDayUpdate 호출
 * - gender 필드 변경 시: handleUserGenderUpdate 호출
 *
 * 참고:
 * - Firestore는 문서당 하나의 onDocumentWritten 트리거만 허용
 * - 따라서 모든 필드 변경을 하나의 트리거에서 감지하고 각 핸들러 호출
 */
export const onUserFieldsWrite = onDocumentWritten(
  {
    document: "users/{uid}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const beforeData = event.data?.before.data() as UserData | undefined;
    const afterData = event.data?.after.data() as UserData | undefined;

    // displayName 필드 변경 감지
    const beforeDisplayName = beforeData?.displayName ?? null;
    const afterDisplayName = afterData?.displayName ?? null;
    if (beforeDisplayName !== afterDisplayName) {
      logger.info("displayName 필드 변경 감지", {uid, beforeDisplayName, afterDisplayName});
      await handleUserDisplayNameUpdate(uid, beforeDisplayName, afterDisplayName);
    }

    // photoUrl 필드 변경 감지
    const beforePhotoUrl = beforeData?.photoUrl ?? null;
    const afterPhotoUrl = afterData?.photoUrl ?? null;
    if (beforePhotoUrl !== afterPhotoUrl) {
      logger.info("photoUrl 필드 변경 감지", {uid, beforePhotoUrl, afterPhotoUrl});
      await handleUserPhotoUrlUpdate(uid, beforePhotoUrl, afterPhotoUrl);
    }

    // birthYearMonthDay 필드 변경 감지
    const beforeBirthYearMonthDay = (beforeData?.birthYearMonthDay as string | undefined) ?? null;
    const afterBirthYearMonthDay = (afterData?.birthYearMonthDay as string | undefined) ?? null;
    if (beforeBirthYearMonthDay !== afterBirthYearMonthDay) {
      logger.info("birthYearMonthDay 필드 변경 감지", {uid, beforeBirthYearMonthDay, afterBirthYearMonthDay});
      await handleUserBirthYearMonthDayUpdate(uid, beforeBirthYearMonthDay, afterBirthYearMonthDay);
    }

    // gender 필드 변경 감지
    const beforeGender = (beforeData?.gender as string | undefined) ?? null;
    const afterGender = (afterData?.gender as string | undefined) ?? null;
    if (beforeGender !== afterGender) {
      logger.info("gender 필드 변경 감지", {uid, beforeGender, afterGender});
      await handleUserGenderUpdate(uid, beforeGender, afterGender);
    }
  }
);



/**
 * 채팅 메시지 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: messages/{messageId}
 *
 * 수행 작업:
 * 1. 프로토콜 메시지 건너뛰기 (시스템 메시지)
 * 2. 필수 필드 유효성 검사 (senderUid, roomId)
 * 3. 1:1 채팅인 경우:
 *    - 양쪽 사용자의 chat-joins 자동 생성/업데이트 (users/{uid1}/chat-joins/{roomId}, users/{uid2}/chat-joins/{roomId})
 *    - roomId, roomType, partnerUid 설정
 *    - lastMessageText, lastMessageAt 업데이트
 *    - 정렬 필드: singleChatListOrder (발신자: timestamp, 수신자: 200+timestamp)
 *    - allChatListOrder (통합 정렬용)
 *    - newMessageCount (수신자만 increment(1))
 * 4. 그룹/오픈 채팅인 경우:
 *    - chats에서 members 목록 조회
 *    - 모든 멤버의 chat-joins 자동 업데이트
 *    - roomName, lastMessageText, lastMessageAt 설정
 *    - 정렬 필드: groupChatListOrder 또는 openChatListOrder (발신자: timestamp, 타 멤버: 200+timestamp)
 *    - openAndGroupChatListOrder, allChatListOrder (통합 정렬용)
 *    - newMessageCount (발신자 제외한 모든 멤버 increment(1))
 *
 * 참고:
 * - 1:1 채팅 roomId 형식: "single-{uid1}-{uid2}" (알파벳 순 정렬)
 * - 그룹/오픈 채팅 roomId: 자동 생성된 Firestore 문서 ID
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatMessageCreate() 참조
 */
export const onChatMessageCreate = onDocumentCreated(
  {
    document: "messages/{messageId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const messageId = event.params.messageId as string;
    const messageData = (event.data?.data() || {}) as ChatMessage;

    // 비즈니스 로직 핸들러 호출
    return await handleChatMessageCreate(messageId, messageData);
  }
);



/**
 * 채팅방 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: chats/{roomId}
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
export const onChatRoomCreate = onDocumentCreated(
  {
    document: "chats/{roomId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const roomId = event.params.roomId as string;
    const roomData = (event.data?.data() || {}) as Record<string, unknown>;
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
 * 트리거 경로: users/{uid}/chat-joins/{roomId}
 *
 * 수행 작업:
 * 1. joinedAt 필드가 없으면 현재 타임스탬프로 자동 생성
 * 2. 1:1 채팅인 경우 (roomId 형식: "single-{uid1}-{uid2}"):
 *    - partnerUid 추출 및 설정
 *    - roomType = "single" 설정
 *    - singleChatListOrder = timestamp (문자열) 설정
 *    - allChatListOrder = timestamp (숫자) 설정
 * 3. 그룹/오픈 채팅인 경우:
 *    - chats에서 채팅방 정보 조회
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
 * - chat-joins 문서는 클라이언트가 직접 생성하거나 onChatMessageCreate에서 자동 생성됨
 * - 이 함수는 채팅방 타입별로 적절한 정렬 필드를 자동 설정
 * - 이미 완전히 설정된 경우 (joinedAt + roomType 존재) 건너뛰기
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatJoinCreate() 참조
 */
export const onChatJoinCreate = onDocumentCreated(
  {
    document: "users/{uid}/chat-joins/{roomId}",
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
 * 트리거 경로: chats/{roomId}/members/{uid}
 *
 * 수행 작업:
 * 1. chats/{roomId}/members 컬렉션의 모든 문서 읽기
 * 2. 모든 uid의 개수 세기 (member=true/false 구분 없이)
 * 3. memberCount 필드를 자동으로 증가
 *
 * 참고:
 * - members 컬렉션 구조: chats/{roomId}/members/{uid} 문서에 member: boolean
 * - true: 사용자가 채팅방에 참여 중, 메시지 알림을 받음
 * - onDocumentCreated 트리거로 멤버 입장 감지
 * - memberCount는 members 컬렉션의 모든 문서 개수 (member=true/false 모두 포함)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomMemberJoin() 참조
 */
export const onChatRoomMemberJoin = onDocumentCreated(
  {
    document: "chats/{roomId}/members/{uid}",
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
 * 트리거 경로: chats/{roomId}/members/{uid}
 *
 * 수행 작업:
 * 1. chats/{roomId}/members 컬렉션의 모든 문서 읽기
 * 2. 모든 uid의 개수 세기 (member=true/false 구분 없이)
 * 3. memberCount 필드를 자동으로 감소
 *
 * 참고:
 * - members 컬렉션에서 문서가 삭제되면 퇴장으로 간주
 * - onDocumentDeleted 트리거로 멤버 퇴장 감지
 * - memberCount는 members 컬렉션의 모든 문서 개수 (member=true/false 모두 포함)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomMemberLeave() 참조
 */
export const onChatRoomMemberLeave = onDocumentDeleted(
  {
    document: "chats/{roomId}/members/{uid}",
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



/**
 * 채팅방 참여 정보 필드 변경 시 트리거 (통합)
 *
 * 트리거 경로: users/{uid}/chat-joins/{roomId}
 * 트리거 이벤트: onDocumentWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * - pin 필드 변경 시: handleChatRoomPinCreate 또는 handleChatRoomPinDelete 호출
 * - newMessageCount 필드 변경 시: handleNewMessageCountWritten 호출
 *
 * 참고:
 * - Firestore는 문서당 하나의 onDocumentWritten 트리거만 허용
 * - 따라서 모든 필드 변경을 하나의 트리거에서 감지하고 각 핸들러 호출
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 *
 * 비즈니스 로직:
 * - pin: handlers/chat.handler.ts의 handleChatRoomPinCreate() 및 handleChatRoomPinDelete()
 * - newMessageCount: handlers/chat.new-message-count.handler.ts의 handleNewMessageCountWritten()
 */
export const onChatJoinFieldsWrite = onDocumentWritten(
  {
    document: "users/{uid}/chat-joins/{roomId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const roomId = event.params.roomId as string;
    const beforeData = event.data?.before.data() as Record<string, unknown> | undefined;
    const afterData = event.data?.after.data() as Record<string, unknown> | undefined;

    // pin 필드 변경 감지
    const beforePin = beforeData?.pin as boolean | undefined;
    const afterPin = afterData?.pin as boolean | undefined;
    if (beforePin !== afterPin) {
      logger.info("채팅방 핀 필드 변경 감지", {
        uid,
        roomId,
        beforePin,
        afterPin,
      });

      // pin이 true로 설정된 경우 (핀 생성)
      if (afterPin === true && beforePin !== true) {
        await handleChatRoomPinCreate(uid, roomId);
      }
      // pin이 false로 변경되거나 삭제된 경우 (핀 해제)
      else if (beforePin === true && afterPin !== true) {
        await handleChatRoomPinDelete(uid, roomId);
      }
    }

    // newMessageCount 필드 변경 감지
    const beforeValue = (beforeData?.newMessageCount as number | undefined) ?? null;
    const afterValue = (afterData?.newMessageCount as number | undefined) ?? null;
    if (beforeValue !== afterValue) {
      logger.info("newMessageCount 필드 변경 감지", {
        uid,
        roomId,
        beforeValue,
        afterValue,
      });

      await handleNewMessageCountWritten(uid, roomId, beforeValue, afterValue);
    }
  }
);


/**
 * 채팅 초대장 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: users/{uid}/chat-invitations/{roomId}
 * 트리거 조건: 초대장이 생성될 때
 *
 * 수행 작업:
 * 1. 채팅방 정보 조회 (roomName, roomType)
 * 2. 초대한 사람 정보 조회 (displayName)
 * 3. 초대받은 사람의 언어 코드 조회
 * 4. 초대 메시지 생성 (i18n 사용, 언어별)
 * 5. 초대장 정보 업데이트 (roomName, inviterName, message)
 * 6. FCM 푸시 알림 전송 (초대받은 사람의 언어로)
 *
 * 참고:
 * - 클라이언트는 최소한의 정보만 저장 (roomId, inviterUid, createdAt, invitationOrder)
 * - Cloud Functions가 나머지 정보를 자동으로 채움 (많은 작업을 백엔드에서 수행)
 * - 1:1 채팅방에 대한 초대는 자동으로 무시됨
 * - 이미 참여 중인 멤버에 대한 초대도 자동으로 무시됨
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatInvitationCreate() 참조
 */
export const onChatInvitationCreate = onDocumentCreated(
  {
    document: "users/{uid}/chat-invitations/{roomId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const inviteeUid = event.params.uid as string;
    const roomId = event.params.roomId as string;
    const invitationData = (event.data?.data() || {}) as {
      inviterUid?: string;
      createdAt?: number;
    };

    logger.info("채팅 초대장 생성 감지", {
      inviteeUid,
      roomId,
      inviterUid: invitationData.inviterUid,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatInvitationCreate(inviteeUid, roomId, invitationData);
  }
);


/**
 * 채팅방 비밀번호 검증 트리거
 *
 * 트리거 경로: /chat-room-passwords/{roomId}/try/{uid}
 * 트리거 이벤트: onValueWritten
 *
 * 수행 작업:
 * 1. try 경로에 기록된 비밀번호 읽기
 * 2. 실제 비밀번호와 비교 (Plain Text)
 * 3. 일치 시 members에 추가, 불일치 시 에러 로그
 * 4. try 경로 즉시 삭제 (보안)
 */
export { onPasswordTry } from "./handlers/chat.password-verification.handler";
