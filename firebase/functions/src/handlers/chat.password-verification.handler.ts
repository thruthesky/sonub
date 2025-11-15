import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";
import { onDocumentWritten } from "firebase-functions/v2/firestore";

/**
 * 채팅방 비밀번호 검증 핸들러 (Firestore)
 *
 * chats/{roomId}/password-tries/{uid} 경로에 값이 기록되면 자동 실행됩니다.
 *
 * 주요 로직:
 * 1. try 경로에 기록된 입력 비밀번호 읽기
 * 2. chats/{roomId}/password-data/password 실제 비밀번호 읽기
 * 3. 문자열 비교 (Plain Text 비교)
 * 4. 일치하면:
 *    - chats/{roomId}/members/{uid}: { member: true } 저장
 *    - password-tries/{uid} 삭제
 * 5. 불일치하면:
 *    - password-tries/{uid} 삭제
 *    - 에러 로그 기록
 *
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @param tryPassword - 입력된 비밀번호
 * @returns Promise<void>
 */
export async function handlePasswordVerification(
  roomId: string,
  uid: string,
  tryPassword: string
): Promise<void> {
  logger.info("채팅방 비밀번호 검증 시작", { roomId, uid });

  const db = admin.firestore();

  try {
    // 1. 실제 비밀번호 읽기 (Firestore)
    const passwordDocRef = db.doc(`chats/${roomId}/password-data/password`);
    const passwordDoc = await passwordDocRef.get();

    if (!passwordDoc.exists) {
      logger.error("❌ 비밀번호가 설정되지 않음", { roomId, uid });
      // try 경로 삭제
      await db.doc(`chats/${roomId}/password-tries/${uid}`).delete();
      return;
    }

    const actualPassword = passwordDoc.data()?.password as string;

    // 2. 비밀번호 비교 (Plain Text)
    if (tryPassword === actualPassword) {
      logger.info("✅ 비밀번호 일치 - members에 추가 시작", { roomId, uid });

      // 3. members에 추가 (admin 권한으로 Security Rules 우회)
      await db.doc(`chats/${roomId}/members/${uid}`).set({
        member: true,
        joinedAt: admin.firestore.FieldValue.serverTimestamp()
      });

      logger.info("✅ members 추가 완료", { roomId, uid });
    } else {
      logger.warn("❌ 비밀번호 불일치", { roomId, uid });
    }

    // 4. try 경로 삭제 (보안상 즉시 삭제)
    await db.doc(`chats/${roomId}/password-tries/${uid}`).delete();

    logger.info("✅ try 경로 삭제 완료", { roomId, uid });

  } catch (error) {
    logger.error("❌ 비밀번호 검증 에러", { roomId, uid, error });

    // 에러 발생 시에도 try 경로 삭제
    try {
      await db.doc(`chats/${roomId}/password-tries/${uid}`).delete();
    } catch (deleteError) {
      logger.error("❌ try 경로 삭제 실패", { roomId, uid, error: deleteError });
    }
  }
}

/**
 * Cloud Functions 트리거 등록 (Firestore)
 *
 * chats/{roomId}/password-tries/{uid} 경로에 값이 기록되거나 삭제되면 자동 실행됩니다.
 */
export const onPasswordTry = onDocumentWritten(
  {
    document: "chats/{roomId}/password-tries/{uid}",
    region:  "asia-northeast3"
  },
  async (event) => {
    const roomId = event.params.roomId as string;
    const uid = event.params.uid as string;
    const afterData = event.data?.after.data();
    const tryPassword = afterData?.password as string | null;

    // 삭제된 경우 무시
    if (!tryPassword || !event.data?.after.exists) {
      logger.info("try 경로 삭제됨 - 무시", { roomId, uid });
      return;
    }

    logger.info("onPasswordTry 트리거 실행", { roomId, uid });

    await handlePasswordVerification(roomId, uid, tryPassword);
  }
);
