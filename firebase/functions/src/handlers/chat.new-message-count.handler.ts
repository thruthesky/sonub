
import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";

/**
 * newMessageCount 필드 변경 시 비즈니스 로직 처리
 *
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @param beforeValue - 변경 전 값
 * @param afterValue - 변경 후 값
 * @returns Promise<void>
 *
 * 주요 처리 로직:
 * 1. **증가 감지**: newMessageCount가 증가하면 users/{uid} 문서의 newMessageCount를 increment
 * 2. **0/삭제 감지**: newMessageCount가 0이 되거나 삭제되면 전체 재계산
 *    - 모든 users/{uid}/chat-joins 서브컬렉션에서 newMessageCount > 0인 채팅방만 합산
 *    - 합산 결과를 users/{uid} 문서의 newMessageCount에 저장
 *    - ⚠️ 중요: increment가 아닌 전체 재계산으로 데이터 불일치 방지
 * 3. **Order 필드 업데이트**: newMessageCount가 0이 되면 "200" prefix 제거
 *    - xxxListOrder로 끝나는 모든 필드 찾기
 *    - "200"으로 시작하는 필드에서 "200" prefix 제거
 *    - "500"으로 시작하는 필드는 유지 (핀 설정된 채팅방)
 *
 * 참고:
 * - Prefix 규칙: "500" (핀됨) > "200" (읽지 않음) > "" (읽음)
 * - 사용자가 채팅방에 입장하여 메시지를 읽으면 newMessageCount가 0이 됨
 * - users/{uid} 문서의 newMessageCount는 모든 채팅방의 newMessageCount 합계
 */
export async function handleNewMessageCountWritten(
  uid: string,
  roomId: string,
  beforeValue: number | null,
  afterValue: number | null
): Promise<void> {
  logger.info("newMessageCount 필드 변경 감지", {
    uid,
    roomId,
    beforeValue,
    afterValue,
  });

  const before = Number(beforeValue ?? 0);
  const after = Number(afterValue ?? 0);
  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);

  // ========================================
  // 1단계: newMessageCount 증가 감지 → increment
  // ========================================
  if (after > before) {
    const increment = after - before;
    logger.info("newMessageCount 증가 감지, users/{uid} 문서의 newMessageCount increment 시작", {
      uid,
      roomId,
      before,
      after,
      increment,
    });

    try {
      await userRef.update({
        newMessageCount: admin.firestore.FieldValue.increment(increment),
      });

      logger.info("✅ users/{uid} 문서의 newMessageCount increment 완료", {
        uid,
        roomId,
        increment,
      });
    } catch (error) {
      logger.error("❌ users/{uid} 문서의 newMessageCount increment 실패", {
        uid,
        roomId,
        error,
      });
    }
  }

  // ========================================
  // 2단계: newMessageCount가 0이 되거나 삭제됨 → 전체 재계산
  // ========================================
  if (after === 0 || afterValue === null) {
    logger.info("newMessageCount가 0이 되거나 삭제됨, 전체 재계산 시작", {
      uid,
      roomId,
      before,
      after,
    });

    try {
      // 모든 users/{uid}/chat-joins 서브컬렉션에서 newMessageCount > 0인 채팅방만 가져오기
      const chatJoinsSnapshot = await db.collection(`users/${uid}/chat-joins`)
        .where("newMessageCount", ">=", 1)
        .get();

      let totalNewMessageCount = 0;

      // 각 채팅방의 newMessageCount 합산
      chatJoinsSnapshot.forEach((doc) => {
        const data = doc.data();
        const count = Number(data.newMessageCount ?? 0);
        if (count > 0) {
          totalNewMessageCount += count;
          logger.info("채팅방 newMessageCount 합산", {
            uid,
            roomKey: doc.id,
            count,
            totalNewMessageCount,
          });
        }
      });

      // users/{uid} 문서의 newMessageCount에 합산 결과 저장
      await userRef.update({newMessageCount: totalNewMessageCount});

      logger.info("✅ users/{uid} 문서의 newMessageCount 전체 재계산 완료", {
        uid,
        roomId,
        totalNewMessageCount,
      });
    } catch (error) {
      logger.error("❌ users/{uid} 문서의 newMessageCount 전체 재계산 실패", {
        uid,
        roomId,
        error,
      });
    }
  }

  // ========================================
  // 3단계: newMessageCount가 0이 되면 order 필드 업데이트 (기존 로직)
  // ========================================
  const newCount = Number(afterValue ?? 0);
  if (newCount !== 0) {
    logger.info("newMessageCount가 0이 아님, order 필드 업데이트 건너뜀", {
      uid,
      roomId,
      newMessageCount: newCount,
    });
    return;
  }

  // users/{uid}/chat-joins/{roomId} 문서 읽기
  const chatJoinRef = db.doc(`users/${uid}/chat-joins/${roomId}`);
  const snapshot = await chatJoinRef.get();

  if (!snapshot.exists) {
    logger.error("chat-joins 문서가 존재하지 않음", {uid, roomId});
    return;
  }

  const data = snapshot.data();
  if (!data) {
    logger.error("chat-joins 문서 데이터가 없음", {uid, roomId});
    return;
  }

  // xxxListOrder 필드 찾기
  const updates: Record<string, string> = {};

  for (const key of Object.keys(data)) {
    // order 필드만 처리 (ListOrder로 끝나는 필드)
    if (!key.endsWith("ListOrder")) {
      continue;
    }

    const currentValue = String(data[key]);

    // "500"으로 시작하면 건드리지 않음 (핀 설정된 채팅방)
    if (currentValue.startsWith("500")) {
      logger.info("핀 설정된 채팅방, 건너뜀", {
        uid,
        roomId,
        field: key,
        currentValue,
      });
      continue;
    }

    // "200"으로 시작하는 경우에만 처리
    if (currentValue.startsWith("200")) {
      const baseTimestamp = currentValue.slice(3); // "200" 제거
      const newValue = baseTimestamp;

      updates[key] = newValue;
      logger.info("Order 필드 업데이트 예정 (200 prefix 제거)", {
        uid,
        roomId,
        field: key,
        from: currentValue,
        to: newValue,
      });
    } else {
      // "200"으로 시작하지 않으면 이미 읽음 상태, 건너뜀
      logger.info("이미 읽음 상태, 건너뜀", {
        uid,
        roomId,
        field: key,
        currentValue,
      });
    }
  }

  // 업데이트할 필드가 있는 경우에만 실행
  if (Object.keys(updates).length > 0) {
    await chatJoinRef.update(updates);

    logger.info("newMessageCount 0으로 변경에 따른 order 필드 업데이트 완료", {
      uid,
      roomId,
      updatedFields: Object.keys(updates),
      updatesCount: Object.keys(updates).length,
    });
  } else {
    logger.info("업데이트할 order 필드가 없음 (newMessageCount 0으로 변경)", {
      uid,
      roomId,
    });
  }
}
