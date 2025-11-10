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
import {onValueCreated} from "firebase-functions/v2/database";
import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";

// 타입 임포트
import {UserData} from "./types";

// 비즈니스 로직 핸들러 임포트
import {handleUserCreate} from "./handlers/user.handler";

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
 * 채팅 메시지 생성 (게시글 + 댓글 역할 통합)
 *
 * 트리거 경로: /chat-messages/{messageId}
 *
 * 현재는 후속 구현을 위한 빈 함수로 남겨둔다.
 */
export const onChatMessageCreate = onValueCreated(
  "/chat-messages/{messageId}",
  async () => {
    // TODO: 채팅 메시지 생성 시 처리 로직을 추가하세요.
    return;
  }
);

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
