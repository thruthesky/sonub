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
  onValueDeleted,
  onValueWritten,
} from "firebase-functions/v2/database";
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
  handleNewMessageCountWritten,
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
 * 사용자 displayName 필드 생성/수정/삭제 시 트리거
 *
 * 트리거 경로: /users/{uid}/displayName
 * 트리거 이벤트: onValueWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. createdAt 필드가 없으면 자동 생성
 *   2. displayNameLowerCase 자동 생성 (대소문자 구분 없는 검색용)
 *   3. updatedAt 업데이트
 * - 삭제 시:
 *   1. displayNameLowerCase 삭제 (동기화)
 *   2. updatedAt 업데이트
 *
 * 무한 루프 방지:
 * - displayName 필드만 감지하므로 displayNameLowerCase 업데이트 시 재트리거 안 됨
 */
export const onUserDisplayNameWrite = onValueWritten(
  {
    ref: "/users/{uid}/displayName",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const beforeValue = event.data.before.val() as string | null;
    const afterValue = event.data.after.val() as string | null;

    logger.info("displayName 필드 변경 감지 (생성/수정/삭제)", {
      uid,
      beforeValue,
      afterValue,
      action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
    });

    // 비즈니스 로직 핸들러 호출
    return await handleUserDisplayNameUpdate(uid, beforeValue, afterValue);
  }
);

/**
 * 사용자 photoUrl 필드 생성/수정/삭제 시 트리거
 *
 * 트리거 경로: /users/{uid}/photoUrl
 * 트리거 이벤트: onValueWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. createdAt 필드가 없으면 자동 생성
 *   2. updatedAt 업데이트
 * - 삭제 시:
 *   1. updatedAt 업데이트
 *
 * 무한 루프 방지:
 * - photoUrl 필드만 감지하므로 다른 필드 업데이트 시 재트리거 안 됨
 */
export const onUserPhotoUrlWrite = onValueWritten(
  {
    ref: "/users/{uid}/photoUrl",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const beforeValue = event.data.before.val() as string | null;
    const afterValue = event.data.after.val() as string | null;

    logger.info("photoUrl 필드 변경 감지 (생성/수정/삭제)", {
      uid,
      beforeValue,
      afterValue,
      action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
    });

    // 비즈니스 로직 핸들러 호출
    return await handleUserPhotoUrlUpdate(uid, beforeValue, afterValue);
  }
);

/**
 * 사용자 birthYearMonthDay 필드 생성/수정/삭제 시 트리거
 *
 * 트리거 경로: /users/{uid}/birthYearMonthDay
 * 트리거 이벤트: onValueWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. createdAt 필드가 없으면 자동 생성
 *   2. YYYY-MM-DD 형식 파싱 및 유효성 검증
 *   3. 파생 필드 자동 생성:
 *      - birthYear (number): 생년
 *      - birthMonth (number): 생월
 *      - birthDay (number): 생일
 *      - birthMonthDay (string): 생월일 (MM-DD 형식)
 * - 삭제 시:
 *   1. 모든 파생 필드 삭제 (동기화)
 *
 * 무한 루프 방지:
 * - birthYearMonthDay 필드만 감지하므로 파생 필드 업데이트 시 재트리거 안 됨
 *
 * 참고:
 * - updatedAt은 업데이트하지 않음 (내부 속성 변경으로 간주)
 */
export const onUserBirthYearMonthDayWrite = onValueWritten(
  {
    ref: "/users/{uid}/birthYearMonthDay",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const beforeValue = event.data.before.val() as string | null;
    const afterValue = event.data.after.val() as string | null;

    logger.info("birthYearMonthDay 필드 변경 감지 (생성/수정/삭제)", {
      uid,
      beforeValue,
      afterValue,
      action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
    });

    // 비즈니스 로직 핸들러 호출
    return await handleUserBirthYearMonthDayUpdate(uid, beforeValue, afterValue);
  }
);

/**
 * 사용자 gender 필드 생성/수정/삭제 시 트리거
 *
 * 트리거 경로: /users/{uid}/gender
 * 트리거 이벤트: onValueWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. photoUrl 존재 여부 확인
 *   2. photoUrl이 있는 경우:
 *      - gender=F: sort_recentFemaleWithPhoto에 createdAt 설정, sort_recentMaleWithPhoto는 null
 *      - gender=M: sort_recentMaleWithPhoto에 createdAt 설정, sort_recentFemaleWithPhoto는 null
 *   3. photoUrl이 없는 경우: 두 정렬 필드 모두 null
 *   4. updatedAt 업데이트
 * - 삭제 시:
 *   1. sort_recentFemaleWithPhoto와 sort_recentMaleWithPhoto 삭제
 *   2. updatedAt 업데이트
 *
 * 무한 루프 방지:
 * - gender 필드만 감지하므로 다른 필드 업데이트 시 재트리거 안 됨
 */
export const onUserGenderWrite = onValueWritten(
  {
    ref: "/users/{uid}/gender",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const beforeValue = event.data.before.val() as string | null;
    const afterValue = event.data.after.val() as string | null;

    logger.info("gender 필드 변경 감지 (생성/수정/삭제)", {
      uid,
      beforeValue,
      afterValue,
      action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
    });

    // 비즈니스 로직 핸들러 호출
    return await handleUserGenderUpdate(uid, beforeValue, afterValue);
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



/**
 * 채팅방 핀 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-joins/{uid}/{roomId}/pin
 * 트리거 조건: pin 필드가 생성될 때 (set(true))
 *
 * 수행 작업:
 * 1. chat-joins/{uid}/{roomId}의 모든 데이터 읽기
 * 2. xxxListOrder 또는 xxxChatListOrder로 끝나는 모든 필드 찾기
 * 3. 각 order 필드에 "500" prefix 추가
 *
 * 참고:
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 * - 클라이언트는 pin: true로 설정
 * - Cloud Functions가 모든 order 필드를 자동으로 업데이트
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomPinCreate() 참조
 */
export const onChatRoomPinCreate = onValueCreated(
  {
    ref: "/chat-joins/{uid}/{roomId}/pin",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const roomId = event.params.roomId as string;
    const pinValue = event.data.val() as boolean | null | undefined;

    logger.info("채팅방 핀 생성 감지", {
      uid,
      roomId,
      pinValue,
    });

    // pin 값이 true인 경우에만 처리
    if (pinValue !== true) {
      logger.warn("핀 값이 true가 아님, 처리 건너뜀", {
        uid,
        roomId,
        pinValue,
      });
      return;
    }

    // 비즈니스 로직 핸들러 호출
    return await handleChatRoomPinCreate(uid, roomId);
  }
);

/**
 * 채팅방 핀 삭제 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-joins/{uid}/{roomId}/pin
 * 트리거 조건: pin 필드가 삭제될 때 (set(null))
 *
 * 수행 작업:
 * 1. chat-joins/{uid}/{roomId}의 모든 데이터 읽기
 * 2. xxxListOrder 또는 xxxChatListOrder로 끝나는 모든 필드 찾기
 * 3. newMessageCount > 0이면 "200" prefix 추가
 * 4. newMessageCount === 0이면 prefix 제거 (순수 timestamp)
 *
 * 참고:
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 * - 클라이언트는 pin 필드를 삭제 (set(null))
 * - Cloud Functions가 모든 order 필드를 자동으로 업데이트
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleChatRoomPinDelete() 참조
 */
export const onChatRoomPinDelete = onValueDeleted(
  {
    ref: "/chat-joins/{uid}/{roomId}/pin",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const roomId = event.params.roomId as string;
    const oldPinValue = event.data.val() as boolean | null | undefined;

    logger.info("채팅방 핀 삭제 감지", {
      uid,
      roomId,
      oldPinValue,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleChatRoomPinDelete(uid, roomId);
  }
);

/**
 * 채팅 초대장 생성 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-invitations/{uid}/{roomId}
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
export const onChatInvitationCreate = onValueCreated(
  {
    ref: "/chat-invitations/{uid}/{roomId}",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const inviteeUid = event.params.uid as string;
    const roomId = event.params.roomId as string;
    const invitationData = (event.data.val() || {}) as {
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
 * newMessageCount 필드 변경 시 트리거되는 Cloud Function
 *
 * 트리거 경로: /chat-joins/{uid}/{roomId}/newMessageCount
 * 트리거 이벤트: onValueWritten (생성, 수정, 삭제 모두 감지)
 *
 * 수행 작업:
 * 1. newMessageCount가 0으로 변경되었는지 확인
 * 2. 0이면 모든 xxxListOrder 필드에서 "200" prefix 제거
 * 3. "500" prefix는 유지 (핀 설정된 채팅방)
 * 4. 존재하지 않는 필드는 무시
 *
 * 참고:
 * - 사용자가 채팅방에 입장하여 메시지를 읽으면 newMessageCount가 0이 됨
 * - 이때 모든 order 필드를 "읽음" 상태로 전환
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 *
 * 비즈니스 로직은 handlers/chat.handler.ts의 handleNewMessageCountWritten() 참조
 */
export const onNewMessageCountWrite = onValueWritten(
  {
    ref: "/chat-joins/{uid}/{roomId}/newMessageCount",
    region: FIREBASE_REGION,
  },
  async (event) => {
    const uid = event.params.uid as string;
    const roomId = event.params.roomId as string;
    const beforeValue = event.data.before.val() as number | null;
    const afterValue = event.data.after.val() as number | null;

    logger.info("newMessageCount 필드 변경 감지", {
      uid,
      roomId,
      beforeValue,
      afterValue,
    });

    // 비즈니스 로직 핸들러 호출
    return await handleNewMessageCountWritten(uid, roomId, beforeValue, afterValue);
  }
);
