---
name: fcm.utils.js
description: fcm.utils Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/firebase/functions/src/utils/fcm.utils.js
---

# fcm.utils.js

## 개요

**파일 경로**: `firebase/functions/lib/firebase/functions/src/utils/fcm.utils.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

fcm.utils Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * Firebase Cloud Messaging (FCM) 유틸리티 함수
 *
 * FCM 푸시 알림 전송과 관련된 헬퍼 함수들을 제공합니다.
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
exports.getFcmTokensByUid = getFcmTokensByUid;
exports.getFcmTokensByUids = getFcmTokensByUids;
exports.sendFcmNotificationBatch = sendFcmNotificationBatch;
exports.sendChatMessageNotification = sendChatMessageNotification;
exports.isChatSubscribed = isChatSubscribed;
const logger = __importStar(require("firebase-functions/logger"));
const admin = __importStar(require("firebase-admin"));
/**
 * FCM 토큰 배치 전송 최대 개수
 * FCM API 제한: 최대 500개이지만 안전하게 255개로 제한
 */
const MAX_FCM_BATCH_SIZE = 255;
/**
 * 사용자 UID로 FCM 토큰 조회
 *
 * @param uid - 사용자 UID
 * @returns FCM 토큰 배열
 *
 * 동작 방식:
 * 1. /fcm-tokens 노드에서 orderByChild("uid").equalTo(uid) 쿼리 실행
 * 2. Firebase 서버 측에서 필터링하여 해당 사용자의 토큰만 가져옴
 * 3. 토큰 키(token ID)들을 배열로 반환
 *
 * 중요: 모든 토큰을 가져온 후 클라이언트에서 필터링하지 않음
 * - orderByChild를 사용하여 서버 측 필터링 수행
 * - 수백만 개의 토큰이 있어도 필요한 토큰만 조회
 */
async function getFcmTokensByUid(uid) {
    logger.info("FCM 토큰 조회 시작", { uid });
    const tokensRef = admin.database().ref("fcm-tokens");
    // orderByChild를 사용하여 uid 필드로 필터링
    // Firebase 서버 측에서 필터링하므로 필요한 토큰만 가져옴
    const snapshot = await tokensRef
        .orderByChild("uid")
        .equalTo(uid)
        .once("value");
    if (!snapshot.exists()) {
        logger.info("해당 사용자의 FCM 토큰이 없음", { uid });
        return [];
    }
    const tokensData = snapshot.val();
    // 토큰 키(token ID)들을 배열로 추출
    const tokens = Object.keys(tokensData);
    logger.info("FCM 토큰 조회 완료", {
        uid,
        tokenCount: tokens.length,
    });
    return tokens;
}
/**
 * 여러 사용자의 FCM 토큰을 한 번에 조회
 *
 * @param uids - 사용자 UID 배열
 * @returns FCM 토큰 배열
 *
 * 동작 방식:
 * 1. 각 uid에 대해 getFcmTokensByUid 호출
 * 2. 모든 토큰을 하나의 배열로 합침
 * 3. 중복 제거 후 반환
 */
async function getFcmTokensByUids(uids) {
    logger.info("여러 사용자의 FCM 토큰 조회 시작", {
        uidCount: uids.length,
    });
    const allTokens = [];
    for (const uid of uids) {
        const tokens = await getFcmTokensByUid(uid);
        allTokens.push(...tokens);
    }
    // 중복 제거
    const uniqueTokens = [...new Set(allTokens)];
    logger.info("여러 사용자의 FCM 토큰 조회 완료", {
        uidCount: uids.length,
        totalTokenCount: uniqueTokens.length,
    });
    return uniqueTokens;
}
/**
 * FCM 푸시 알림을 배치로 전송
 *
 * @param tokens - FCM 토큰 배열
 * @param title - 알림 제목
 * @param body - 알림 내용
 * @param data - 추가 데이터 (선택)
 * @returns 전송 결과 요약
 *
 * 동작 방식:
 * 1. 토큰을 255개씩 배치로 분할
 * 2. 각 배치에 대해 sendEachForMulticast 호출 (최신 API)
 * 3. 실패한 토큰 중 유효하지 않은 토큰만 삭제
 * 4. 전송 결과 요약 반환
 *
 * 토큰 삭제 규칙:
 * - messaging/invalid-argument: 삭제
 * - messaging/registration-token-not-registered: 삭제
 * - 네트워크 에러 또는 일시적 에러: 삭제하지 않음
 */
async function sendFcmNotificationBatch(tokens, title, body, data) {
    logger.info("FCM 배치 알림 전송 시작", {
        totalTokens: tokens.length,
        batchSize: MAX_FCM_BATCH_SIZE,
    });
    let successCount = 0;
    let failureCount = 0;
    let deletedTokenCount = 0;
    // 토큰을 배치로 분할
    const batches = [];
    for (let i = 0; i < tokens.length; i += MAX_FCM_BATCH_SIZE) {
        batches.push(tokens.slice(i, i + MAX_FCM_BATCH_SIZE));
    }
    logger.info("배치 분할 완료", {
        totalTokens: tokens.length,
        batchCount: batches.length,
    });
    // 각 배치에 대해 메시지 전송
    for (let batchIndex = 0; batchIndex < batches.length; batchIndex++) {
        const batch = batches[batchIndex];
        logger.info("배치 전송 시작", {
            batchIndex: batchIndex + 1,
            totalBatches: batches.length,
            batchSize: batch.length,
        });
        try {
            // MulticastMessage 생성 (최신 API)
            const message = {
                notification: {
                    title,
                    body,
                },
                tokens: batch,
            };
            // data가 있으면 추가
            if (data) {
                message.data = data;
            }
            // sendEachForMulticast 사용 (최신 API)
            const response = await admin.messaging().sendEachForMulticast(message);
            logger.info("배치 전송 완료", {
                batchIndex: batchIndex + 1,
                successCount: response.successCount,
                failureCount: response.failureCount,
            });
            successCount += response.successCount;
            failureCount += response.failureCount;
            // 실패한 토큰 처리
            if (response.failureCount > 0 && response.responses) {
                for (let i = 0; i < response.responses.length; i++) {
                    const result = response.responses[i];
                    const token = batch[i];
                    // 실패한 경우
                    if (!result.success && result.error) {
                        const errorCode = result.error.code;
                        logger.warn("FCM 전송 실패", {
                            token,
                            errorCode,
                            errorMessage: result.error.message,
                        });
                        // 유효하지 않은 토큰만 삭제
                        if (errorCode === "messaging/invalid-argument" ||
                            errorCode === "messaging/registration-token-not-registered") {
                            logger.info("유효하지 않은 토큰 삭제", {
                                token,
                                errorCode,
                            });
                            try {
                                await admin.database().ref(`fcm-tokens/${token}`).remove();
                                deletedTokenCount++;
                            }
                            catch (deleteError) {
                                logger.error("토큰 삭제 실패", {
                                    token,
                                    error: deleteError,
                                });
                            }
                        }
                        else {
                            logger.info("일시적 에러, 토큰 유지", {
                                token,
                                errorCode,
                            });
                        }
                    }
                }
            }
        }
        catch (error) {
            logger.error("배치 전송 중 에러 발생", {
                batchIndex: batchIndex + 1,
                error,
            });
            failureCount += batch.length;
        }
    }
    logger.info("FCM 배치 알림 전송 완료", {
        totalTokens: tokens.length,
        successCount,
        failureCount,
        deletedTokenCount,
    });
    return {
        successCount,
        failureCount,
        deletedTokenCount,
    };
}
/**
 * 채팅 메시지 푸시 알림 전송
 *
 * @param senderName - 발신자 이름
 * @param messageText - 메시지 내용
 * @param roomId - 채팅방 ID
 * @param recipientUids - 수신자 UID 배열
 * @returns 전송 결과 요약
 *
 * 동작 방식:
 * 1. 수신자들의 FCM 토큰 조회
 * 2. 푸시 알림 페이로드 생성
 * 3. 배치로 FCM 전송
 */
async function sendChatMessageNotification(senderName, messageText, roomId, recipientUids) {
    logger.info("채팅 메시지 푸시 알림 전송 시작", {
        senderName,
        roomId,
        recipientCount: recipientUids.length,
    });
    // 수신자가 없으면 종료
    if (recipientUids.length === 0) {
        logger.info("수신자가 없어 알림 전송을 건너뜀", { roomId });
        return {
            successCount: 0,
            failureCount: 0,
            deletedTokenCount: 0,
        };
    }
    // 수신자들의 FCM 토큰 조회
    const tokens = await getFcmTokensByUids(recipientUids);
    if (tokens.length === 0) {
        logger.info("FCM 토큰이 없어 알림 전송을 건너뜀", {
            roomId,
            recipientCount: recipientUids.length,
        });
        return {
            successCount: 0,
            failureCount: 0,
            deletedTokenCount: 0,
        };
    }
    // 배치로 FCM 전송
    return await sendFcmNotificationBatch(tokens, senderName, // title
    messageText || "(메시지 없음)", // body
    {
        // data
        roomId,
        type: "chat-message",
        senderName,
    });
}
/**
 * 사용자의 채팅방 알림 구독 상태 확인
 *
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @param isSingleChat - 1:1 채팅방 여부
 * @returns true: 구독 중, false: 구독 해제
 *
 * 동작 방식:
 * - 1:1 채팅방: /chat-joins/{uid}/{roomId}/fcm-subscription 필드 확인
 *   - 필드가 없으면 구독 중 (기본값)
 *   - false면 구독 해제
 * - 그룹/오픈 채팅방: /chat-rooms/{roomId}/members/{uid} 필드 확인
 *   - true면 구독 중
 *   - false면 구독 해제
 *   - 필드가 없으면 구독 중 (기본값)
 */
async function isChatSubscribed(roomId, uid, isSingleChat) {
    if (isSingleChat) {
        // 1:1 채팅방: fcm-subscription 필드 확인
        const subscriptionRef = admin
            .database()
            .ref(`chat-joins/${uid}/${roomId}/fcm-subscription`);
        const snapshot = await subscriptionRef.once("value");
        // 필드가 없거나 false가 아니면 구독 중
        if (!snapshot.exists()) {
            return true; // 기본값: 구독 중
        }
        const value = snapshot.val();
        return value !== false;
    }
    else {
        // 그룹/오픈 채팅방: members 필드 확인
        const memberRef = admin
            .database()
            .ref(`chat-rooms/${roomId}/members/${uid}`);
        const snapshot = await memberRef.once("value");
        // 필드가 없으면 구독 중으로 간주
        if (!snapshot.exists()) {
            return true; // 기본값: 구독 중
        }
        const value = snapshot.val();
        return value === true;
    }
}
//# sourceMappingURL=fcm.utils.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
