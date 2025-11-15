/**
 * 사용자 프로필 동기화 비즈니스 로직 처리
 */

import * as admin from "firebase-admin";
import * as logger from "firebase-functions/logger";
import {UserData} from "../types";

/**
 * 사용자 등록 시 Firestore 문서에 주요 필드를 설정하고 createdAt을 설정합니다.
 *
 * 수행 작업:
 * 1. createdAt 필드 자동 생성 및 users/{uid} 문서에 저장
 * 2. displayNameLowerCase 필드 자동 생성 (displayName이 있는 경우)
 * 3. 사용자 데이터 정규화 및 동기화 수행
 *    - photoUrl 처리
 *    - users/{uid} 문서 업데이트
 *    - system/stats 문서의 userCount 필드 +1 (전체 사용자 통계 업데이트)
 *
 * @param {string} uid - 사용자 UID
 * @param {UserData} userData - 사용자 데이터
 * @returns {Promise<{success: boolean; uid: string}>} 처리 결과
 */
export async function handleUserCreate(
  uid: string,
  userData: UserData
): Promise<{success: boolean; uid: string}> {
  logger.info("새 사용자 등록 처리 시작", {
    uid,
    displayName: userData.displayName ?? null,
  });

  const now = Date.now();
  const db = admin.firestore();
  const batch = db.batch();
  const userRef = db.doc(`users/${uid}`);
  const updates: Record<string, unknown> = {};

  // 1. createdAt 필드 자동 생성 (없는 경우만)
  const createdAt =
    typeof userData.createdAt === "number" ? userData.createdAt : now;

  if (userData.createdAt === undefined || userData.createdAt === null) {
    updates.createdAt = createdAt;
    logger.info("createdAt 저장 예정", {uid, createdAt});
  }

  // 2. displayNameLowerCase 필드 자동 생성 (displayName이 있는 경우)
  if (userData.displayName) {
    const displayNameLowerCase = userData.displayName.toLowerCase();
    updates.displayNameLowerCase = displayNameLowerCase;
    logger.info("displayNameLowerCase 자동 생성", {
      uid,
      displayName: userData.displayName,
      displayNameLowerCase,
    });
  }

  // 3. users/{uid} 문서 업데이트 (createdAt, displayNameLowerCase)
  if (Object.keys(updates).length > 0) {
    batch.update(userRef, updates);
  }

  // 4. 📊 전체 사용자 통계 업데이트: system/stats 문서의 userCount 필드 +1
  const statsRef = db.doc("system/stats");
  batch.set(statsRef, {userCount: admin.firestore.FieldValue.increment(1)}, {merge: true});

  // 배치 커밋
  await batch.commit();
  logger.info("사용자 생성 관련 업데이트 완료", {uid, updatesCount: Object.keys(updates).length});

  return {
    success: true,
    uid,
  };
}

/**
 * 사용자 정보 업데이트 시 처리
 *
 * ⚠️ DEPRECATED: 이 함수는 무한 루프 위험이 있어 더 이상 사용하지 않습니다.
 * 대신 개별 필드별 핸들러를 사용하세요:
 * - handleUserDisplayNameUpdate
 * - handleUserPhotoUrlUpdate
 * - handleUserBirthYearMonthDayUpdate
 *
 * 수행 작업:
 * 1. createdAt 필드가 없으면 자동 생성
 * 2. displayName 또는 photoUrl이 변경된 경우에만 updatedAt을 새로운 timestamp로 업데이트
 * 3. displayNameLowerCase 자동 생성 및 저장 (대소문자 구분 없는 검색용)
 *
 * @param {string} uid - 사용자 UID
 * @param {UserData} beforeData - 변경 전 사용자 데이터
 * @param {UserData} afterData - 변경 후 사용자 데이터
 * @returns {Promise<{success: boolean; uid: string; updated: boolean}>} 처리 결과
 * @deprecated 무한 루프 위험으로 개별 필드 핸들러 사용 권장
 */
export async function handleUserUpdate(
  uid: string,
  beforeData: UserData,
  afterData: UserData
): Promise<{success: boolean; uid: string; updated: boolean}> {
  logger.info("사용자 정보 업데이트 처리 시작", {
    uid,
    beforeDisplayName: beforeData?.displayName ?? null,
    afterDisplayName: afterData?.displayName ?? null,
  });

  const now = Date.now();
  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);
  const updates: Record<string, unknown> = {};

  // 1. createdAt 필드가 없으면 자동 생성
  if (afterData.createdAt === undefined || afterData.createdAt === null) {
    const createdAt =
      typeof beforeData?.createdAt === "number" ? beforeData.createdAt : now;
    updates.createdAt = createdAt;
    logger.info("createdAt 필드 자동 생성", {uid, createdAt});
  }

  // 2. displayName 또는 photoUrl이 변경되었는지 확인
  const displayNameChanged =
    beforeData?.displayName !== afterData?.displayName;
  const photoUrlChanged =
    (beforeData?.photoUrl ?? beforeData?.photoURL) !==
    (afterData?.photoUrl ?? afterData?.photoURL);

  // 3. displayName 또는 photoUrl이 변경된 경우에만 updatedAt 업데이트
  if (displayNameChanged || photoUrlChanged) {
    updates.updatedAt = now;
    logger.info("displayName 또는 photoUrl 변경 감지, updatedAt 업데이트", {
      uid,
      displayNameChanged,
      photoUrlChanged,
      updatedAt: now,
    });
  }

  // 4. displayNameLowerCase 자동 생성 (대소문자 구분 없는 검색용)
  // displayNameLowerCase가 없거나 displayName이 변경된 경우 생성/업데이트
  const needsDisplayNameLowerCase =
    afterData.displayName &&
    (!afterData.displayNameLowerCase || displayNameChanged);

  if (needsDisplayNameLowerCase) {
    const displayNameLowerCase = afterData.displayName!.toLowerCase();
    updates.displayNameLowerCase = displayNameLowerCase;
    logger.info("displayNameLowerCase 생성/업데이트", {
      uid,
      displayNameLowerCase,
      reason: !afterData.displayNameLowerCase ? "필드 없음" : "displayName 변경",
    });
  }

  // 5. birthYearMonthDay 필드 변경 시 파생 필드 자동 생성
  const birthYearMonthDayChanged =
    beforeData?.birthYearMonthDay !== afterData?.birthYearMonthDay;

  if (afterData.birthYearMonthDay && birthYearMonthDayChanged) {
    // YYYYMMDD 숫자 또는 YYYY-MM-DD 문자열 형식 파싱
    let year = "";
    let month = "";
    let day = "";
    let parseSuccess = false;

    const birthValue = afterData.birthYearMonthDay as number | string;

    // 숫자 형식 (YYYYMMDD) 파싱 시도
    if (typeof birthValue === "number") {
      const dateStr = birthValue.toString();
      if (dateStr.length === 8) {
        year = dateStr.substring(0, 4);
        month = dateStr.substring(4, 6);
        day = dateStr.substring(6, 8);
        parseSuccess = true;
      }
    } else if (typeof birthValue === "string") {
      // 문자열 형식 (YYYY-MM-DD) 파싱 시도 (하위 호환성)
      const birthDateMatch = birthValue.match(
        /^(\d{4})-(\d{2})-(\d{2})$/
      );
      if (birthDateMatch) {
        [, year, month, day] = birthDateMatch;
        parseSuccess = true;
      }
    }

    if (parseSuccess) {
      // 파생 필드 생성
      updates.birthYear = parseInt(year, 10);
      updates.birthMonth = parseInt(month, 10);
      updates.birthDay = parseInt(day, 10);
      updates.birthMonthDay = parseInt(`${month}${day}`, 10); // MMDD 형식 숫자 (예: 1016)

      logger.info("birthYearMonthDay 파싱 및 파생 필드 생성", {
        uid,
        birthYearMonthDay: afterData.birthYearMonthDay,
        birthYear: parseInt(year, 10),
        birthMonth: parseInt(month, 10),
        birthDay: parseInt(day, 10),
        birthMonthDay: parseInt(`${month}${day}`, 10),
      });
    } else {
      logger.warn("birthYearMonthDay 형식이 올바르지 않습니다 (YYYYMMDD 숫자 또는 YYYY-MM-DD 문자열 필요)", {
        uid,
        birthYearMonthDay: afterData.birthYearMonthDay,
      });
    }
  }

  // 6. DB에 업데이트 반영
  if (Object.keys(updates).length > 0) {
    await userRef.update(updates);
    logger.info("사용자 정보 업데이트 완료", {
      uid,
      updatesCount: Object.keys(updates).length,
    });
    return {success: true, uid, updated: true};
  } else {
    logger.info("업데이트할 항목 없음", {uid});
    return {success: true, uid, updated: false};
  }
}

/**
 * 헬퍼 함수: 사용자의 createdAt 필드 확인 및 생성
 *
 * @param {string} uid - 사용자 UID
 * @returns {Promise<number | null>} createdAt 값 (생성 필요 시 새 값, 아니면 null)
 */
async function ensureCreatedAt(uid: string): Promise<number | null> {
  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);
  const snapshot = await userRef.get();

  if (!snapshot.exists || !snapshot.data()?.createdAt) {
    const now = Date.now();
    logger.info("createdAt 필드가 없어 자동 생성", {uid, createdAt: now});
    return now;
  }

  return null; // 이미 존재하므로 생성 불필요
}

/**
 * displayName 필드 생성/수정/삭제 시 처리
 *
 * 트리거: /users/{uid}/displayName 생성/수정/삭제
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. createdAt 필드가 없으면 자동 생성
 *   2. displayNameLowerCase 자동 생성 (대소문자 구분 없는 검색용)
 *   3. updatedAt 업데이트
 * - 삭제 시:
 *   1. displayNameLowerCase 삭제
 *   2. updatedAt 업데이트
 *
 * 무한 루프 방지:
 * - displayName 필드만 트리거하므로 displayNameLowerCase 업데이트 시 재트리거 안 됨
 * - displayName 값 자체는 이 함수에서 수정하지 않음
 *
 * @param {string} uid - 사용자 UID
 * @param {string | null} beforeValue - 변경 전 displayName 값
 * @param {string | null} afterValue - 변경 후 displayName 값 (삭제 시 null)
 * @returns {Promise<{success: boolean; uid: string}>} 처리 결과
 */
export async function handleUserDisplayNameUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}> {
  logger.info("displayName 변경 감지", {
    uid,
    beforeValue,
    afterValue,
    action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
  });

  const now = Date.now();
  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);
  const updates: Record<string, unknown> = {};

  // 삭제 케이스
  if (afterValue === null) {
    // displayNameLowerCase 삭제
    updates.displayNameLowerCase = admin.firestore.FieldValue.delete();
    logger.info("displayName 삭제, displayNameLowerCase도 삭제", {uid});
  } else {
    // 생성/수정 케이스
    // 1. createdAt 필드가 없으면 자동 생성
    const createdAt = await ensureCreatedAt(uid);
    if (createdAt !== null) {
      updates.createdAt = createdAt;
    }

    // 2. displayNameLowerCase 자동 생성/수정
    const displayNameLowerCase = afterValue.toLowerCase();
    updates.displayNameLowerCase = displayNameLowerCase;
    logger.info("displayNameLowerCase 자동 생성/수정", {
      uid,
      displayNameLowerCase,
    });
  }

  // 3. updatedAt 업데이트 (생성/수정/삭제 모두)
  updates.updatedAt = now;
  logger.info("updatedAt 업데이트", {uid, updatedAt: now});

  // DB 업데이트 반영
  if (Object.keys(updates).length > 0) {
    await userRef.update(updates);
    logger.info("displayName 변경 처리 완료", {
      uid,
      updatesCount: Object.keys(updates).length,
    });
  }

  return {success: true, uid};
}

/**
 * photoUrl 필드 생성/수정/삭제 시 처리
 *
 * 트리거: /users/{uid}/photoUrl 생성/수정/삭제
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. createdAt 필드가 없으면 자동 생성
 *   2. updatedAt 업데이트
 *   3. 정렬 필드 생성 (photoUrl이 있는 경우):
 *      - sort_recentWithPhoto: createdAt 값 복사 (사진 있는 회원 정렬용)
 *      - sort_recentFemaleWithPhoto: gender=F인 경우 createdAt 값 복사
 *      - sort_recentMaleWithPhoto: gender=M인 경우 createdAt 값 복사
 * - 삭제 시:
 *   1. updatedAt 업데이트
 *   2. 모든 정렬 필드 삭제 (sort_recentWithPhoto, sort_recentFemaleWithPhoto, sort_recentMaleWithPhoto)
 *
 * 무한 루프 방지:
 * - photoUrl 필드만 트리거하므로 다른 필드 업데이트 시 재트리거 안 됨
 * - photoUrl 값 자체는 이 함수에서 수정하지 않음
 *
 * @param {string} uid - 사용자 UID
 * @param {string | null} beforeValue - 변경 전 photoUrl 값
 * @param {string | null} afterValue - 변경 후 photoUrl 값 (삭제 시 null)
 * @returns {Promise<{success: boolean; uid: string}>} 처리 결과
 */
export async function handleUserPhotoUrlUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}> {
  logger.info("photoUrl 변경 감지", {
    uid,
    beforeValue,
    afterValue,
    action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
  });

  const now = Date.now();
  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);
  const updates: Record<string, unknown> = {};

  // 삭제 케이스
  if (afterValue === null) {
    // 모든 정렬 필드 삭제
    updates.sort_recentWithPhoto = admin.firestore.FieldValue.delete();
    updates.sort_recentFemaleWithPhoto = admin.firestore.FieldValue.delete();
    updates.sort_recentMaleWithPhoto = admin.firestore.FieldValue.delete();
    logger.info("photoUrl 삭제, 모든 정렬 필드도 삭제", {uid});
  } else {
    // 생성/수정 케이스
    // 1. createdAt 필드가 없으면 자동 생성
    const createdAt = await ensureCreatedAt(uid);
    if (createdAt !== null) {
      updates.createdAt = createdAt;
    }

    // 2. 사용자 데이터 읽기 (gender와 createdAt 필요)
    const userSnapshot = await userRef.get();
    const userData = userSnapshot.data();

    if (userData) {
      const userCreatedAt = userData.createdAt || Date.now();
      const gender = userData.gender;

      // 3. 정렬 필드 생성
      // sort_recentWithPhoto: photoUrl이 있으면 항상 생성
      updates.sort_recentWithPhoto = userCreatedAt;
      logger.info("sort_recentWithPhoto 생성", {uid, value: userCreatedAt});

      // sort_recentFemaleWithPhoto: 여자인 경우만
      if (gender === "F") {
        updates.sort_recentFemaleWithPhoto = userCreatedAt;
        logger.info("sort_recentFemaleWithPhoto 생성", {uid, value: userCreatedAt});
      } else {
        // 남자로 변경되었거나 gender가 없는 경우 삭제
        updates.sort_recentFemaleWithPhoto = admin.firestore.FieldValue.delete();
      }

      // sort_recentMaleWithPhoto: 남자인 경우만
      if (gender === "M") {
        updates.sort_recentMaleWithPhoto = userCreatedAt;
        logger.info("sort_recentMaleWithPhoto 생성", {uid, value: userCreatedAt});
      } else {
        // 여자로 변경되었거나 gender가 없는 경우 삭제
        updates.sort_recentMaleWithPhoto = admin.firestore.FieldValue.delete();
      }
    } else {
      logger.warn("사용자 데이터를 찾을 수 없음", {uid});
    }
  }

  // 4. updatedAt 업데이트 (생성/수정/삭제 모두)
  updates.updatedAt = now;
  logger.info("updatedAt 업데이트", {uid, updatedAt: now});

  // DB 업데이트 반영
  if (Object.keys(updates).length > 0) {
    await userRef.update(updates);
    logger.info("photoUrl 변경 처리 완료", {
      uid,
      updatesCount: Object.keys(updates).length,
    });
  }

  return {success: true, uid};
}

/**
 * birthYearMonthDay 필드 생성/수정/삭제 시 처리
 *
 * 트리거: /users/{uid}/birthYearMonthDay 생성/수정/삭제
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. createdAt 필드가 없으면 자동 생성
 *   2. YYYYMMDD 숫자 또는 YYYY-MM-DD 문자열 형식 파싱 및 유효성 검증
 *   3. 파생 필드 자동 생성:
 *      - birthYear (number): 생년
 *      - birthMonth (number): 생월
 *      - birthDay (number): 생일
 *      - birthMonthDay (number): 생월일 (MMDD 형식 숫자, 예: 1016)
 * - 삭제 시:
 *   1. 모든 파생 필드 삭제 (birthYear, birthMonth, birthDay, birthMonthDay)
 *
 * 무한 루프 방지:
 * - birthYearMonthDay 필드만 트리거하므로 파생 필드 업데이트 시 재트리거 안 됨
 * - birthYearMonthDay 값 자체는 이 함수에서 수정하지 않음
 *
 * 참고:
 * - updatedAt은 업데이트하지 않음 (내부 속성 변경으로 간주)
 *
 * @param {string} uid - 사용자 UID
 * @param {string | number | null} beforeValue - 변경 전 birthYearMonthDay 값
 * @param {string | number | null} afterValue - 변경 후 birthYearMonthDay 값 (YYYYMMDD 숫자 또는 YYYY-MM-DD 문자열, 삭제 시 null)
 * @returns {Promise<{success: boolean; uid: string}>} 처리 결과
 */
export async function handleUserBirthYearMonthDayUpdate(
  uid: string,
  beforeValue: string | number | null,
  afterValue: string | number | null
): Promise<{success: boolean; uid: string}> {
  logger.info("birthYearMonthDay 변경 감지", {
    uid,
    beforeValue,
    afterValue,
    action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
  });

  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);
  const updates: Record<string, unknown> = {};

  // 삭제 케이스
  if (afterValue === null) {
    // 모든 파생 필드 삭제
    updates.birthYear = admin.firestore.FieldValue.delete();
    updates.birthMonth = admin.firestore.FieldValue.delete();
    updates.birthDay = admin.firestore.FieldValue.delete();
    updates.birthMonthDay = admin.firestore.FieldValue.delete();
    logger.info("birthYearMonthDay 삭제, 모든 파생 필드도 삭제", {uid});
  } else {
    // 생성/수정 케이스
    // 1. createdAt 필드가 없으면 자동 생성
    const createdAt = await ensureCreatedAt(uid);
    if (createdAt !== null) {
      updates.createdAt = createdAt;
    }

    // 2. YYYYMMDD 숫자 또는 YYYY-MM-DD 문자열 형식 파싱 및 파생 필드 생성
    let year = "";
    let month = "";
    let day = "";
    let parseSuccess = false;

    // 숫자 형식 (YYYYMMDD) 파싱 시도
    if (typeof afterValue === "number") {
      const dateStr = afterValue.toString();
      if (dateStr.length === 8) {
        year = dateStr.substring(0, 4);
        month = dateStr.substring(4, 6);
        day = dateStr.substring(6, 8);
        parseSuccess = true;
      }
    } else if (typeof afterValue === "string") {
      // 문자열 형식 (YYYY-MM-DD) 파싱 시도 (하위 호환성)
      const birthDateMatch = afterValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);
      if (birthDateMatch) {
        [, year, month, day] = birthDateMatch;
        parseSuccess = true;
      }
    }

    if (parseSuccess) {
      // 파생 필드 생성/수정
      updates.birthYear = parseInt(year, 10);
      updates.birthMonth = parseInt(month, 10);
      updates.birthDay = parseInt(day, 10);
      updates.birthMonthDay = parseInt(`${month}${day}`, 10); // MMDD 형식 숫자 (예: 1016)

      logger.info("birthYearMonthDay 파싱 및 파생 필드 생성/수정", {
        uid,
        birthYearMonthDay: afterValue,
        birthYear: parseInt(year, 10),
        birthMonth: parseInt(month, 10),
        birthDay: parseInt(day, 10),
        birthMonthDay: parseInt(`${month}${day}`, 10),
      });
    } else {
      logger.warn("birthYearMonthDay 형식이 올바르지 않습니다 (YYYYMMDD 숫자 또는 YYYY-MM-DD 문자열 필요)", {
        uid,
        birthYearMonthDay: afterValue,
      });
      // 형식 오류 시에도 에러를 발생시키지 않고 계속 진행
    }
  }

  // DB 업데이트 반영
  if (Object.keys(updates).length > 0) {
    await userRef.update(updates);
    logger.info("birthYearMonthDay 변경 처리 완료", {
      uid,
      updatesCount: Object.keys(updates).length,
    });
  }

  return {success: true, uid};
}

/**
 * gender 필드 생성/수정/삭제 시 처리
 *
 * 트리거: /users/{uid}/gender 생성/수정/삭제
 *
 * 수행 작업:
 * - 생성/수정 시:
 *   1. photoUrl 존재 여부 확인
 *   2. photoUrl이 있는 경우:
 *      - gender=F: sort_recentFemaleWithPhoto에 createdAt 설정, sort_recentMaleWithPhoto는 null
 *      - gender=M: sort_recentMaleWithPhoto에 createdAt 설정, sort_recentFemaleWithPhoto는 null
 *   3. photoUrl이 없는 경우:
 *      - 두 정렬 필드 모두 null
 *   4. updatedAt 업데이트
 * - 삭제 시:
 *   1. sort_recentFemaleWithPhoto와 sort_recentMaleWithPhoto 삭제
 *   2. updatedAt 업데이트
 *
 * 무한 루프 방지:
 * - gender 필드만 트리거하므로 다른 필드 업데이트 시 재트리거 안 됨
 * - gender 값 자체는 이 함수에서 수정하지 않음
 *
 * @param {string} uid - 사용자 UID
 * @param {string | null} beforeValue - 변경 전 gender 값
 * @param {string | null} afterValue - 변경 후 gender 값 ("F" | "M" | null)
 * @returns {Promise<{success: boolean; uid: string}>} 처리 결과
 */
export async function handleUserGenderUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}> {
  logger.info("gender 변경 감지", {
    uid,
    beforeValue,
    afterValue,
    action: afterValue === null ? "삭제" : beforeValue === null ? "생성" : "수정",
  });

  const now = Date.now();
  const db = admin.firestore();
  const userRef = db.doc(`users/${uid}`);
  const updates: Record<string, unknown> = {};

  // 삭제 케이스
  if (afterValue === null) {
    // 성별 관련 정렬 필드 삭제
    updates.sort_recentFemaleWithPhoto = admin.firestore.FieldValue.delete();
    updates.sort_recentMaleWithPhoto = admin.firestore.FieldValue.delete();
    logger.info("gender 삭제, 성별 관련 정렬 필드도 삭제", {uid});
  } else {
    // 생성/수정 케이스
    // 1. 사용자 데이터 읽기 (photoUrl과 createdAt 필요)
    const userSnapshot = await userRef.get();
    const userData = userSnapshot.data();

    if (userData) {
      const photoUrl = userData.photoUrl;
      const userCreatedAt = userData.createdAt || Date.now();

      // 2. photoUrl이 있는 경우에만 정렬 필드 생성
      if (photoUrl) {
        logger.info("photoUrl 존재, gender에 따라 정렬 필드 업데이트", {
          uid,
          gender: afterValue,
          photoUrl,
        });

        // gender=F: 여성 정렬 필드 생성, 남성 정렬 필드 삭제
        if (afterValue === "F") {
          updates.sort_recentFemaleWithPhoto = userCreatedAt;
          updates.sort_recentMaleWithPhoto = admin.firestore.FieldValue.delete();
          logger.info("sort_recentFemaleWithPhoto 생성, sort_recentMaleWithPhoto 삭제", {
            uid,
            value: userCreatedAt,
          });
        }
        // gender=M: 남성 정렬 필드 생성, 여성 정렬 필드 삭제
        else if (afterValue === "M") {
          updates.sort_recentMaleWithPhoto = userCreatedAt;
          updates.sort_recentFemaleWithPhoto = admin.firestore.FieldValue.delete();
          logger.info("sort_recentMaleWithPhoto 생성, sort_recentFemaleWithPhoto 삭제", {
            uid,
            value: userCreatedAt,
          });
        }
        // gender가 F, M이 아닌 경우: 두 필드 모두 삭제
        else {
          updates.sort_recentFemaleWithPhoto = admin.firestore.FieldValue.delete();
          updates.sort_recentMaleWithPhoto = admin.firestore.FieldValue.delete();
          logger.info("gender가 F/M이 아님, 두 정렬 필드 모두 삭제", {uid, gender: afterValue});
        }
      } else {
        // photoUrl이 없는 경우: 두 정렬 필드 모두 삭제
        updates.sort_recentFemaleWithPhoto = admin.firestore.FieldValue.delete();
        updates.sort_recentMaleWithPhoto = admin.firestore.FieldValue.delete();
        logger.info("photoUrl 없음, 두 정렬 필드 모두 삭제", {uid});
      }
    } else {
      logger.warn("사용자 데이터를 찾을 수 없음", {uid});
    }
  }

  // 3. updatedAt 업데이트 (생성/수정/삭제 모두)
  updates.updatedAt = now;
  logger.info("updatedAt 업데이트", {uid, updatedAt: now});

  // DB 업데이트 반영
  if (Object.keys(updates).length > 0) {
    await userRef.update(updates);
    logger.info("gender 변경 처리 완료", {
      uid,
      updatesCount: Object.keys(updates).length,
    });
  }

  return {success: true, uid};
}
