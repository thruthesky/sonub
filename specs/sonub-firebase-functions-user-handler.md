---
title: Firebase Cloud Functions - User Handler
version: 1.0.0
status: implemented
tags: [firebase, cloud-functions, user, handler, business-logic]
author: Claude SED Agent
date: 2025-11-11
dependencies:
  - sonub-firebase-database-structure.md
---

# Firebase Cloud Functions - User Handler

## 개요

사용자 관련 비즈니스 로직을 처리하는 핸들러 모듈입니다. Firebase Cloud Functions의 트리거 함수에서 호출되어 실제 데이터 처리를 수행합니다.

## 목적

- 사용자 등록 시 필수 필드 자동 생성 및 데이터 정규화
- 사용자 정보 업데이트 시 조건부 필드 업데이트
- 트리거 함수와 비즈니스 로직 분리 (관심사 분리)

## 파일 위치

`firebase/functions/src/handlers/user.handler.ts`

## 함수 목록

### 1. handleUserCreate

**목적:** 새로운 사용자 등록 시 자동으로 필수 필드 생성 및 데이터 정규화

**시그니처:**

```typescript
export async function handleUserCreate(
  uid: string,
  userData: UserData
): Promise<{success: boolean; uid: string}>
```

**파라미터:**
- `uid` (string): 사용자 UID
- `userData` (UserData): 사용자 데이터 객체

**반환값:**
```typescript
{
  success: boolean;  // 성공 여부
  uid: string;       // 사용자 UID
}
```

**수행 작업:**

1. **createdAt 필드 자동 생성**
   - `userData.createdAt`이 있으면 그 값 사용
   - 없으면 현재 시간(`Date.now()`) 사용
   - `/users/{uid}/createdAt`에 직접 저장

2. **데이터 정규화 및 동기화** (향후 구현 예정)
   - `updatedAt` 필드 자동 생성
   - `displayNameLowerCase` 자동 생성
   - `photoUrl` 처리
   - `/users/{uid}` 노드 업데이트
   - `/user-props/` 노드 동기화
   - `/stats/counters/user +1` (전체 사용자 통계 업데이트)

**소스 코드:**

```typescript
export async function handleUserCreate(
  uid: string,
  userData: UserData
): Promise<{success: boolean; uid: string}> {
  logger.info("새 사용자 등록 처리 시작", {
    uid,
    displayName: userData.displayName ?? null,
  });

  const now = Date.now();

  // createdAt 필드 자동 생성 (없는 경우만)
  const createdAt =
    typeof userData.createdAt === "number" ? userData.createdAt : now;

  // /users/{uid}/createdAt 직접 저장 (없는 경우만)
  if (userData.createdAt === undefined || userData.createdAt === null) {
    await admin.database().ref(`users/${uid}/createdAt`).set(createdAt);
    logger.info("createdAt 저장 완료", {uid, createdAt});
  }

  return {
    success: true,
    uid,
  };
}
```

**로깅:**
- "새 사용자 등록 처리 시작": uid, displayName
- "createdAt 저장 완료": uid, createdAt

**특징:**
- `createdAt`이 이미 있으면 덮어쓰지 않음
- 현재는 createdAt만 처리하고, 향후 추가 로직 구현 예정

---

### 2. handleUserUpdate

**목적:** 사용자 정보 업데이트 시 특정 필드 변경에 따라 조건부로 관련 필드 업데이트

**시그니처:**

```typescript
export async function handleUserUpdate(
  uid: string,
  beforeData: UserData,
  afterData: UserData
): Promise<{success: boolean; uid: string; updated: boolean}>
```

**파라미터:**
- `uid` (string): 사용자 UID
- `beforeData` (UserData): 변경 전 사용자 데이터
- `afterData` (UserData): 변경 후 사용자 데이터

**반환값:**
```typescript
{
  success: boolean;  // 성공 여부
  uid: string;       // 사용자 UID
  updated: boolean;  // 실제로 업데이트가 발생했는지 여부
}
```

**수행 작업:**

1. **createdAt 필드 자동 생성**
   - `afterData.createdAt`이 없으면 자동 생성
   - `beforeData.createdAt`이 있으면 그 값 사용
   - 둘 다 없으면 현재 시간 사용

2. **displayName 또는 photoUrl 변경 감지**
   - `beforeData.displayName !== afterData.displayName` 확인
   - `beforeData.photoUrl !== afterData.photoUrl` 확인
   - `photoURL` (대문자) 필드도 함께 확인 (하위 호환성)

3. **조건부 updatedAt 업데이트** ⭐ **중요**
   - displayName 또는 photoUrl이 **변경된 경우에만** updatedAt 업데이트
   - 다른 필드만 변경된 경우 updatedAt 업데이트하지 않음
   - 현재 시간(`Date.now()`)으로 업데이트

4. **displayNameLowerCase 자동 생성**
   - displayName이 변경된 경우에만 업데이트
   - `afterData.displayName.toLowerCase()` 값 사용
   - 대소문자 구분 없는 검색용

5. **birthYearMonthDay 필드 변경 시 파생 필드 자동 생성** ⭐ **중요 - 클라이언트/서버 역할 분리**
   - 클라이언트는 최소한의 데이터만 저장: `birthYearMonthDay` (YYYY-MM-DD 형식)
   - Cloud Functions가 파생 필드 자동 생성:
     - `birthYear` (number): 생년
     - `birthMonth` (number): 생월
     - `birthDay` (number): 생일
     - `birthMonthDay` (string): 생월일 (MM-DD 형식)
   - YYYY-MM-DD 형식 정규식 검증
   - 형식이 올바르지 않으면 경고 로그만 출력 (에러 발생 안 함)

6. **DB 업데이트 반영**
   - 변경사항이 있으면 `admin.database().ref().update()` 호출
   - 변경사항이 없으면 DB 업데이트 스킵

**소스 코드:**

```typescript
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
  if (afterData.displayName && displayNameChanged) {
    const displayNameLowerCase = afterData.displayName.toLowerCase();
    updates[`users/${uid}/displayNameLowerCase`] = displayNameLowerCase;
    logger.info("displayNameLowerCase 업데이트", {
      uid,
      displayNameLowerCase,
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

**로깅:**
- "사용자 정보 업데이트 처리 시작": uid, beforeDisplayName, afterDisplayName
- "createdAt 필드 자동 생성": uid, createdAt (조건부)
- "displayName 또는 photoUrl 변경 감지, updatedAt 업데이트": uid, displayNameChanged, photoUrlChanged, updatedAt (조건부)
- "displayNameLowerCase 업데이트": uid, displayNameLowerCase (조건부)
- "birthYearMonthDay 파싱 및 파생 필드 생성": uid, birthYearMonthDay, birthYear, birthMonth, birthDay, birthMonthDay (조건부)
- "birthYearMonthDay 형식이 올바르지 않습니다" (경고): uid, birthYearMonthDay (조건부)
- "사용자 정보 업데이트 완료": uid, updatesCount (변경사항 있을 때)
- "업데이트할 항목 없음": uid (변경사항 없을 때)

**특징:**

1. **조건부 업데이트 ⭐ 핵심 로직**
   - updatedAt은 displayName 또는 photoUrl 변경 시에만 업데이트
   - 다른 필드(예: gender, birthYear 등)만 변경되면 updatedAt 업데이트 안 함
   - 불필요한 DB 쓰기 방지 → 성능 최적화 및 비용 절감

2. **클라이언트/서버 역할 분리 ⭐ 핵심 설계 패턴**
   - 클라이언트: 최소한의 데이터만 저장 (`birthYearMonthDay`)
   - Cloud Functions: 파생 필드 자동 생성 (`birthYear`, `birthMonth`, `birthDay`, `birthMonthDay`)
   - 장점: 데이터 일관성 보장, 클라이언트 로직 단순화, 서버에서 중앙화된 비즈니스 로직 관리

3. **하위 호환성**
   - `photoURL` (대문자)와 `photoUrl` (소문자) 모두 지원
   - Null-safe 연산자(`??`) 사용으로 undefined/null 처리

4. **변경 감지**
   - before/after 데이터 비교로 실제 변경 여부 판단
   - 불필요한 업데이트 방지

5. **원자적 업데이트**
   - 모든 변경사항을 하나의 `update()` 호출로 처리
   - 일관성 보장

6. **견고한 에러 처리**
   - birthYearMonthDay 형식 검증 (정규식)
   - 형식이 올바르지 않으면 경고 로그만 출력 (에러 발생 안 함)
   - 다른 업데이트는 정상 진행

---

## 전체 소스 코드

**파일 경로:** `firebase/functions/src/handlers/user.handler.ts`

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

  // createdAt 필드 자동 생성 (없는 경우만)
  const createdAt =
    typeof userData.createdAt === "number" ? userData.createdAt : now;

  // /users/{uid}/createdAt 직접 저장 (없는 경우만)
  if (userData.createdAt === undefined || userData.createdAt === null) {
    await admin.database().ref(`users/${uid}/createdAt`).set(createdAt);
    logger.info("createdAt 저장 완료", {uid, createdAt});
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
  if (afterData.displayName && displayNameChanged) {
    const displayNameLowerCase = afterData.displayName.toLowerCase();
    updates[`users/${uid}/displayNameLowerCase`] = displayNameLowerCase;
    logger.info("displayNameLowerCase 업데이트", {
      uid,
      displayNameLowerCase,
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

## 데이터 흐름

### handleUserCreate

```
1. 새 사용자 등록 (/users/{uid} 생성)
   ↓
2. onUserCreate 트리거 실행 (index.ts)
   ↓
3. handleUserCreate 호출 (user.handler.ts)
   ↓
4. createdAt 확인
   - 있으면: 기존 값 사용
   - 없으면: Date.now() 사용
   ↓
5. /users/{uid}/createdAt 저장
   ↓
6. 성공 응답 반환
```

### handleUserUpdate

```
1. 사용자 정보 업데이트 (/users/{uid} 변경)
   ↓
2. onUserUpdate 트리거 실행 (index.ts)
   ↓
3. handleUserUpdate 호출 (user.handler.ts)
   - beforeData: 변경 전 데이터
   - afterData: 변경 후 데이터
   ↓
4. 변경 감지
   - displayName 변경? → displayNameChanged = true
   - photoUrl 변경? → photoUrlChanged = true
   - birthYearMonthDay 변경? → birthYearMonthDayChanged = true
   ↓
5. 조건부 업데이트 결정
   - displayNameChanged OR photoUrlChanged?
     - Yes: updatedAt 업데이트
     - No: updatedAt 업데이트 안 함
   ↓
6. displayNameLowerCase 업데이트 (displayName 변경 시)
   ↓
7. birthYearMonthDay 파싱 및 파생 필드 생성 (birthYearMonthDay 변경 시)
   - YYYY-MM-DD 형식 검증
   - birthYear, birthMonth, birthDay, birthMonthDay 자동 생성
   ↓
8. DB 업데이트 (변경사항 있을 때만)
   ↓
9. 성공 응답 반환 (updated: true/false)
```

## 테스트

단위 테스트: `firebase/functions/test/unit/user.handler.test.ts`

### 테스트 케이스

**✅ 정상 케이스:**
1. displayName이 변경되면 updatedAt과 displayNameLowerCase를 업데이트한다
2. photoUrl이 변경되면 updatedAt만 업데이트한다
3. displayName과 photoUrl이 모두 변경되면 모든 필드를 업데이트한다
4. createdAt이 없으면 자동으로 생성한다
5. createdAt이 beforeData와 afterData 모두 없으면 현재 시간으로 생성한다

**❌ 변경 없음 케이스:**
1. displayName과 photoUrl이 변경되지 않으면 updatedAt을 업데이트하지 않는다
2. 다른 필드만 변경되고 displayName과 photoUrl은 변경되지 않으면 업데이트하지 않는다

**🔍 경계값 테스트:**
1. photoURL(대문자)과 photoUrl(소문자)을 모두 처리한다
2. 빈 문자열과 null/undefined를 구분한다
3. 매우 긴 displayName을 처리한다

### 테스트 실행

```bash
# 단위 테스트 실행
npm run test:unit

# user.handler 테스트만 실행
npm run test:unit -- test/unit/user.handler.test.ts
```

## 의존성

### 필수 패키지

- `firebase-admin`: Firebase Admin SDK
- `firebase-functions`: Firebase Functions SDK (logger)

### 내부 모듈

- `../types`: TypeScript 타입 정의 (UserData)

## UserData 타입

```typescript
interface UserData {
  // 필수 필드
  displayName?: string;

  // 프로필 이미지
  photoUrl?: string;
  photoURL?: string;  // 하위 호환성

  // 타임스탬프
  createdAt?: number;
  updatedAt?: number;

  // 검색용
  displayNameLowerCase?: string;

  // 추가 필드
  gender?: string;

  // 생년월일 관련 (클라이언트/서버 역할 분리)
  birthYearMonthDay?: string;  // 클라이언트가 저장 (YYYY-MM-DD)
  birthYear?: number;          // Cloud Functions가 자동 생성
  birthMonth?: number;         // Cloud Functions가 자동 생성
  birthDay?: number;           // Cloud Functions가 자동 생성
  birthMonthDay?: string;      // Cloud Functions가 자동 생성 (MM-DD)

  // 기타 필드...
}
```

## 주의사항

1. **조건부 업데이트 ⭐ 핵심**
   - updatedAt은 displayName 또는 photoUrl 변경 시에만 업데이트
   - 다른 필드 변경 시 updatedAt 업데이트 안 함
   - 성능 최적화 및 비용 절감

2. **클라이언트/서버 역할 분리 ⭐ 핵심 설계 원칙**
   - 클라이언트: 최소한의 데이터만 저장 (`birthYearMonthDay`)
   - Cloud Functions: 파생 필드 자동 생성 (`birthYear`, `birthMonth`, `birthDay`, `birthMonthDay`)
   - 이 패턴을 다른 필드에도 적용 가능 (예: `fullName` → `firstName`, `lastName`)
   - 장점: 데이터 일관성, 중앙화된 비즈니스 로직, 클라이언트 단순화

3. **Null 안전성**
   - `??` 연산자 사용으로 undefined/null 안전하게 처리
   - Optional chaining (`?.`) 사용 권장

4. **원자적 업데이트**
   - 여러 필드를 하나의 `update()` 호출로 처리
   - 일관성 보장

5. **로깅**
   - 모든 주요 작업은 로깅
   - Cloud Logging에서 확인 가능

6. **에러 처리**
   - birthYearMonthDay 형식 검증 (정규식)
   - 형식 오류 시 경고 로그만 출력 (함수 실패 안 함)
   - 다른 에러는 함수 실패로 처리되며 Firebase Functions가 자동 재시도

## 향후 개선 사항

1. **handleUserCreate 확장**
   - user-props 노드 동기화
   - 전체 사용자 통계 업데이트 (/stats/counters/user +1)
   - updatedAt 자동 생성

2. **에러 처리 강화**
   - try-catch 블록 추가
   - 구체적인 에러 메시지 반환

3. **검증 로직 추가**
   - displayName 길이 제한
   - photoUrl 형식 검증

4. **성능 최적화**
   - Batch 업데이트 지원
   - 캐싱 고려

## 참고 문서

- [Firebase Realtime Database](https://firebase.google.com/docs/database)
- [Firebase Admin SDK - Database](https://firebase.google.com/docs/admin/setup#initialize-sdk)
- [TypeScript 타입 안전성](https://www.typescriptlang.org/docs/handbook/2/everyday-types.html)
