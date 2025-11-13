---
name: user.handler.ts
description: 사용자 프로필 동기화 및 업데이트 비즈니스 로직 처리 핸들러
version: 1.0.0
type: firebase-function
category: handler
tags: [firebase, cloud-functions, typescript, user, profile, handler]
---

# user.handler.ts

## 개요
이 파일은 사용자 프로필 동기화 및 업데이트와 관련된 비즈니스 로직을 처리하는 핸들러입니다. Firebase Cloud Functions의 트리거 함수에서 호출되어 실제 데이터 처리를 수행합니다.

## 소스 코드

```typescript
/**
 * 사용자 프로필 동기화 비즈니스 로직 처리
 */

import * as admin from "firebase-admin";
import * as logger from "firebase-functions/logger";
import {UserData} from "../types";

/**
 * 사용자 등록 시 user-props 노드에 주요 필드를 분리 저장하고 createdAt을 설정합니다.
 *
 * 수행 작업:
 * 1. createdAt 필드 자동 생성 및 /users/{uid}/createdAt 직접 저장
 * 2. updateUserProps() 함수를 통해 모든 사용자 데이터 정규화 및 동기화 수행
 *    - updatedAt 필드 자동 생성
 *    - displayNameLowerCase 자동 생성
 *    - photoUrl 처리
 *    - /users/{uid} 노드 업데이트
 *    - /user-props/ 노드 동기화
 *    - /stats/counters/user +1 (전체 사용자 통계 업데이트)
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

  const updates: Record<string, unknown> = {};

  // createdAt 필드 자동 생성 (없는 경우만)
  const createdAt =
    typeof userData.createdAt === "number" ? userData.createdAt : now;

  // /users/{uid}/createdAt 직접 저장 (없는 경우만)
  if (userData.createdAt === undefined || userData.createdAt === null) {
    updates[`users/${uid}/createdAt`] = createdAt;
    logger.info("createdAt 저장 예정", {uid, createdAt});
  }

  // 📊 전체 사용자 통계 업데이트: /stats/counters/user +1
  updates["stats/counters/user"] = admin.database.ServerValue.increment(1);

  if (Object.keys(updates).length > 0) {
    await admin.database().ref().update(updates);
    logger.info("사용자 생성 관련 업데이트 완료", {
      uid,
      updatesCount: Object.keys(updates).length,
    });
  }

  return {
    success: true,
    uid,
  };
}

/**
 * 사용자 정보 업데이트 시 처리
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
  const updates: Record<string, unknown> = {};

  // 1. createdAt 필드가 없으면 자동 생성
  if (afterData.createdAt === undefined || afterData.createdAt === null) {
    const createdAt =
      typeof beforeData?.createdAt === "number" ? beforeData.createdAt : now;
    updates[`users/${uid}/createdAt`] = createdAt;
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
    updates[`users/${uid}/updatedAt`] = now;
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
    updates[`users/${uid}/displayNameLowerCase`] = displayNameLowerCase;
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
    // YYYY-MM-DD 형식 파싱
    const birthDateMatch = afterData.birthYearMonthDay.match(
      /^(\d{4})-(\d{2})-(\d{2})$/
    );

    if (birthDateMatch) {
      const [, year, month, day] = birthDateMatch;

      // 파생 필드 생성
      updates[`users/${uid}/birthYear`] = parseInt(year, 10);
      updates[`users/${uid}/birthMonth`] = parseInt(month, 10);
      updates[`users/${uid}/birthDay`] = parseInt(day, 10);
      updates[`users/${uid}/birthMonthDay`] = `${month}-${day}`;

      logger.info("birthYearMonthDay 파싱 및 파생 필드 생성", {
        uid,
        birthYearMonthDay: afterData.birthYearMonthDay,
        birthYear: parseInt(year, 10),
        birthMonth: parseInt(month, 10),
        birthDay: parseInt(day, 10),
        birthMonthDay: `${month}-${day}`,
      });
    } else {
      logger.warn("birthYearMonthDay 형식이 올바르지 않습니다", {
        uid,
        birthYearMonthDay: afterData.birthYearMonthDay,
      });
    }
  }

  // 6. DB에 업데이트 반영
  if (Object.keys(updates).length > 0) {
    await admin.database().ref().update(updates);
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
```

## 주요 기능
- **handleUserCreate**: 사용자 등록 시 처리
  - createdAt 필드 자동 생성
  - 전체 사용자 통계 카운터 증가 (/stats/counters/user)
- **handleUserUpdate**: 사용자 정보 업데이트 시 처리
  - createdAt 필드 보완 (없는 경우 생성)
  - displayName 또는 photoUrl 변경 시에만 updatedAt 업데이트
  - displayNameLowerCase 자동 생성 (검색 최적화)
  - birthYearMonthDay 파싱 및 파생 필드 자동 생성 (birthYear, birthMonth, birthDay, birthMonthDay)

## 사용되는 Firebase 트리거
- 트리거 함수에서 호출됨 (직접 트리거하지 않음)
- `index.ts`의 `onUserCreate`, `onUserUpdate`에서 호출

## 관련 함수
- `types/index.ts`: UserData 타입 정의
- `index.ts`: onUserCreate, onUserUpdate 트리거 함수
