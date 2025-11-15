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

### 3. handleUserDisplayNameUpdate

**목적:** displayName 필드 생성/수정/삭제 시 관련 필드 자동 업데이트 및 동기화

**시그니처:**

```typescript
export async function handleUserDisplayNameUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}>
```

**파라미터:**
- `uid` (string): 사용자 UID
- `beforeValue` (string | null): 변경 전 displayName 값
- `afterValue` (string | null): 변경 후 displayName 값 (null이면 삭제됨)

**반환값:**
```typescript
{
  success: boolean;  // 성공 여부
  uid: string;       // 사용자 UID
}
```

**수행 작업:**

1. **삭제 케이스 처리** (`afterValue === null`)
   - displayNameLowerCase도 null로 설정하여 삭제
   - updatedAt 업데이트
   - DB 반영 후 종료

2. **생성/수정 케이스 처리**
   - createdAt 확인 및 자동 생성 (`ensureCreatedAt()` 호출)
   - displayNameLowerCase 자동 생성 (`afterValue.toLowerCase()`)
   - updatedAt 업데이트
   - DB 반영

**로깅:**
- "displayName 필드 변경 감지": uid, beforeValue, afterValue, action (생성/수정/삭제)
- "displayName 삭제, displayNameLowerCase도 삭제": uid (삭제 시)

---

### 4. handleUserPhotoUrlUpdate

**목적:** photoUrl 필드 생성/수정/삭제 시 관련 필드 자동 업데이트 및 정렬 필드 생성

**시그니처:**

```typescript
export async function handleUserPhotoUrlUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}>
```

**파라미터:**
- `uid` (string): 사용자 UID
- `beforeValue` (string | null): 변경 전 photoUrl 값
- `afterValue` (string | null): 변경 후 photoUrl 값 (null이면 삭제됨)

**반환값:**
```typescript
{
  success: boolean;  // 성공 여부
  uid: string;       // 사용자 UID
}
```

**수행 작업:**

1. **삭제 케이스 처리** (`afterValue === null`)
   - 모든 정렬 필드 삭제:
     - `sort_recentWithPhoto` = null
     - `sort_recentFemaleWithPhoto` = null
     - `sort_recentMaleWithPhoto` = null
   - updatedAt 업데이트
   - DB 반영 후 종료

2. **생성/수정 케이스 처리**
   - createdAt 확인 및 자동 생성 (`ensureCreatedAt()` 호출)
   - 사용자 데이터 읽기 (gender, createdAt 필요)
   - **정렬 필드 생성** ⭐ 핵심 기능
     - `sort_recentWithPhoto`: photoUrl이 있으면 항상 createdAt 값 복사
     - `sort_recentFemaleWithPhoto`: gender=F인 경우만 createdAt 값 복사, 아니면 null
     - `sort_recentMaleWithPhoto`: gender=M인 경우만 createdAt 값 복사, 아니면 null
   - updatedAt 업데이트
   - DB 반영

**로깅:**
- "photoUrl 필드 변경 감지": uid, beforeValue, afterValue, action (생성/수정/삭제)
- "photoUrl 삭제, 모든 정렬 필드도 삭제": uid (삭제 시)
- "sort_recentWithPhoto 생성": uid, value (생성/수정 시)
- "sort_recentFemaleWithPhoto 생성": uid, value (gender=F일 때)
- "sort_recentMaleWithPhoto 생성": uid, value (gender=M일 때)

**특징:**

1. **정렬 필드 자동 생성** ⭐ 핵심
   - 회원 목록 시 사진이 있는 회원을 효율적으로 필터링 및 정렬
   - 성별(gender)에 따라 분리된 정렬 필드 생성
   - createdAt 순서로 정렬 가능

2. **데이터 동기화**
   - photoUrl 삭제 시 모든 정렬 필드도 자동 삭제
   - gender 변경 시 해당 성별 정렬 필드만 유지 (다른 필드는 null)

3. **쿼리 최적화**
   - Firebase Database 쿼리 시 `sort_recentWithPhoto`로 orderBy하여 사진 있는 회원만 효율적으로 로드
   - 남자만: `sort_recentMaleWithPhoto`로 orderBy
   - 여자만: `sort_recentFemaleWithPhoto`로 orderBy

---

### 5. handleUserBirthYearMonthDayUpdate

**목적:** birthYearMonthDay 필드 생성/수정/삭제 시 파생 필드 자동 생성/삭제

**시그니처:**

```typescript
export async function handleUserBirthYearMonthDayUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}>
```

**파라미터:**
- `uid` (string): 사용자 UID
- `beforeValue` (string | null): 변경 전 birthYearMonthDay 값
- `afterValue` (string | null): 변경 후 birthYearMonthDay 값 (null이면 삭제됨)

**반환값:**
```typescript
{
  success: boolean;  // 성공 여부
  uid: string;       // 사용자 UID
}
```

**수행 작업:**

1. **삭제 케이스 처리** (`afterValue === null`)
   - 모든 파생 필드를 null로 설정하여 삭제:
     - birthYear
     - birthMonth
     - birthDay
     - birthMonthDay
   - DB 반영 후 종료

2. **생성/수정 케이스 처리**
   - YYYY-MM-DD 형식 검증 (정규식)
   - 파생 필드 자동 생성:
     - `birthYear` (number): 생년
     - `birthMonth` (number): 생월
     - `birthDay` (number): 생일
     - `birthMonthDay` (string): 생월일 (MM-DD)
   - DB 반영

**로깅:**
- "birthYearMonthDay 필드 변경 감지": uid, beforeValue, afterValue, action (생성/수정/삭제)
- "birthYearMonthDay 삭제, 모든 파생 필드도 삭제": uid (삭제 시)
- "birthYearMonthDay 파싱 및 파생 필드 생성": uid, birthYearMonthDay, birthYear, birthMonth, birthDay, birthMonthDay (생성/수정 시)

---

### 6. handleUserGenderUpdate

**목적:** gender 필드 생성/수정/삭제 시 정렬 필드 자동 업데이트

**시그니처:**

```typescript
export async function handleUserGenderUpdate(
  uid: string,
  beforeValue: string | null,
  afterValue: string | null
): Promise<{success: boolean; uid: string}>
```

**파라미터:**
- `uid` (string): 사용자 UID
- `beforeValue` (string | null): 변경 전 gender 값
- `afterValue` (string | null): 변경 후 gender 값 ("F" | "M" | null)

**반환값:**
```typescript
{
  success: boolean;  // 성공 여부
  uid: string;       // 사용자 UID
}
```

**수행 작업:**

1. **삭제 케이스 처리** (`afterValue === null`)
   - 성별 관련 정렬 필드 삭제:
     - `sort_recentFemaleWithPhoto` = null
     - `sort_recentMaleWithPhoto` = null
   - updatedAt 업데이트
   - DB 반영 후 종료

2. **생성/수정 케이스 처리**
   - 사용자 데이터 읽기 (photoUrl, createdAt 필요)
   - **photoUrl 존재 여부 확인** ⭐ 핵심 조건
     - photoUrl이 **있는 경우**:
       - gender=F: `sort_recentFemaleWithPhoto`에 createdAt 설정, `sort_recentMaleWithPhoto` = null
       - gender=M: `sort_recentMaleWithPhoto`에 createdAt 설정, `sort_recentFemaleWithPhoto` = null
       - gender가 F/M이 아닌 경우: 두 필드 모두 null
     - photoUrl이 **없는 경우**:
       - 두 정렬 필드 모두 null
   - updatedAt 업데이트
   - DB 반영

**로깅:**
- "gender 필드 변경 감지": uid, beforeValue, afterValue, action (생성/수정/삭제)
- "gender 삭제, 성별 관련 정렬 필드도 삭제": uid (삭제 시)
- "photoUrl 존재, gender에 따라 정렬 필드 업데이트": uid, gender, photoUrl (생성/수정 시)
- "sort_recentFemaleWithPhoto 생성, sort_recentMaleWithPhoto 삭제": uid, value (gender=F일 때)
- "sort_recentMaleWithPhoto 생성, sort_recentFemaleWithPhoto 삭제": uid, value (gender=M일 때)
- "photoUrl 없음, 두 정렬 필드 모두 삭제": uid (photoUrl이 없을 때)

**특징:**

1. **조건부 정렬 필드 생성** ⭐ 핵심
   - photoUrl이 있는 경우에만 성별 정렬 필드 생성
   - photoUrl이 없으면 정렬 필드 생성하지 않음

2. **상호 배타적 필드 관리**
   - 여성(F): sort_recentFemaleWithPhoto만 생성, sort_recentMaleWithPhoto는 null
   - 남성(M): sort_recentMaleWithPhoto만 생성, sort_recentFemaleWithPhoto는 null
   - 동시에 두 필드가 값을 가질 수 없음

3. **데이터 동기화**
   - gender 변경 시 성별 정렬 필드 자동 재생성
   - photoUrl이 있으면 즉시 반영, 없으면 필드 삭제

4. **photoUrl 트리거와의 협업**
   - `handleUserPhotoUrlUpdate`가 photoUrl 변경 시 정렬 필드 생성
   - `handleUserGenderUpdate`가 gender 변경 시 정렬 필드 업데이트
   - 두 함수가 함께 작동하여 완전한 데이터 동기화 보장

**무한 루프 방지:**
- gender 필드만 트리거하므로 다른 필드 업데이트 시 재트리거 안 됨
- gender 값 자체는 이 함수에서 수정하지 않음

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
  gender?: string;  // "M" (남자) | "F" (여자)

  // 생년월일 관련 (클라이언트/서버 역할 분리)
  birthYearMonthDay?: string;  // 클라이언트가 저장 (YYYY-MM-DD)
  birthYear?: number;          // Cloud Functions가 자동 생성
  birthMonth?: number;         // Cloud Functions가 자동 생성
  birthDay?: number;           // Cloud Functions가 자동 생성
  birthMonthDay?: string;      // Cloud Functions가 자동 생성 (MM-DD)

  // 정렬 필드 (Cloud Functions가 자동 생성)
  sort_recentWithPhoto?: number;        // photoUrl이 있으면 createdAt 복사
  sort_recentFemaleWithPhoto?: number;  // photoUrl이 있고 gender=F이면 createdAt 복사
  sort_recentMaleWithPhoto?: number;    // photoUrl이 있고 gender=M이면 createdAt 복사

  // 기타 필드...
}
```

## 무한 루프 방지 전략

### 문제점

Firebase Cloud Functions의 트리거를 사용할 때, 전체 노드를 감지하면 무한 루프에 빠질 위험이 있습니다.

**예시: 위험한 패턴**
```typescript
// ❌ 위험: /users/{uid} 전체 노드 감지
export const onUserUpdate = onValueWritten(
  { ref: "/users/{uid}", region: FIREBASE_REGION },
  async (event) => {
    // displayName 변경 감지
    // → displayNameLowerCase 업데이트
    // → /users/{uid} 노드가 다시 변경됨
    // → onUserUpdate 다시 트리거 (무한 루프!)
  }
);
```

**무한 루프 발생 시나리오:**

1. 사용자가 `/users/{uid}/displayName` 업데이트
2. Cloud Function이 트리거되어 `/users/{uid}/displayNameLowerCase` 업데이트
3. `/users/{uid}` 노드가 다시 변경되어 Cloud Function 다시 트리거
4. 2-3 단계 무한 반복 → **비용 폭증 및 성능 저하**

### 해결 방법

**개별 필드별로 이벤트 트리거를 분리하고, `onValueWritten`을 사용하여 생성/수정/삭제를 모두 처리합니다.**

```typescript
// ✅ 안전: 개별 필드만 감지하고 생성/수정/삭제 모두 처리
export const onUserDisplayNameWrite = onValueWritten(
  { ref: "/users/{uid}/displayName", region: FIREBASE_REGION },
  async (event) => {
    // displayName이 생성/수정/삭제될 때만 트리거
    // displayNameLowerCase를 업데이트해도
    // displayName 값은 변경되지 않으므로 재트리거 안 됨

    // 삭제 케이스: afterValue === null
    // → displayNameLowerCase도 함께 삭제하여 데이터 동기화
  }
);

export const onUserPhotoUrlWrite = onValueWritten(
  { ref: "/users/{uid}/photoUrl", region: FIREBASE_REGION },
  async (event) => {
    // photoUrl이 생성/수정/삭제될 때만 트리거
  }
);

export const onUserBirthYearMonthDayWrite = onValueWritten(
  { ref: "/users/{uid}/birthYearMonthDay", region: FIREBASE_REGION },
  async (event) => {
    // birthYearMonthDay가 생성/수정/삭제될 때만 트리거
    // 파생 필드(birthYear, birthMonth 등)를 업데이트해도
    // birthYearMonthDay 값은 변경되지 않으므로 재트리거 안 됨

    // 삭제 케이스: afterValue === null
    // → 모든 파생 필드(birthYear, birthMonth, birthDay, birthMonthDay)도 함께 삭제
  }
);

export const onUserGenderWrite = onValueWritten(
  { ref: "/users/{uid}/gender", region: FIREBASE_REGION },
  async (event) => {
    // gender가 생성/수정/삭제될 때만 트리거
    // 정렬 필드(sort_recentFemaleWithPhoto, sort_recentMaleWithPhoto)를 업데이트해도
    // gender 값은 변경되지 않으므로 재트리거 안 됨

    // 삭제 케이스: afterValue === null
    // → 성별 관련 정렬 필드(sort_recentFemaleWithPhoto, sort_recentMaleWithPhoto) 삭제
  }
);
```

### 장점

1. **무한 루프 방지** ⭐ 핵심
   - 트리거 필드와 업데이트 필드가 분리되어 재트리거 방지
   - 예: displayName 트리거 → displayNameLowerCase 업데이트 (displayName은 변경 안 됨)

2. **완전한 데이터 동기화** ⭐ 중요
   - `onValueWritten` 사용으로 생성/수정/삭제 모두 감지
   - 필드 삭제 시 파생 필드도 함께 삭제하여 데이터 일관성 보장
   - 예: displayName 삭제 → displayNameLowerCase도 자동 삭제
   - 예: birthYearMonthDay 삭제 → 모든 파생 필드(birthYear, birthMonth, birthDay, birthMonthDay) 자동 삭제

3. **성능 최적화**
   - 필요한 필드만 변경될 때만 트리거
   - 불필요한 함수 실행 방지

4. **비용 절감**
   - Cloud Functions 실행 횟수 감소
   - 데이터베이스 읽기/쓰기 작업 최소화

5. **명확한 책임 분리**
   - 각 트리거가 담당하는 필드가 명확
   - 디버깅 및 유지보수 용이

### 구현 예시

#### 1. displayName 생성/수정/삭제 시
```typescript
export const onUserDisplayNameWrite = onValueWritten(
  { ref: "/users/{uid}/displayName", region: FIREBASE_REGION },
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

    // 삭제 케이스: displayNameLowerCase도 함께 삭제
    // 생성/수정 케이스: displayNameLowerCase 자동 생성, createdAt 없으면 추가, updatedAt 업데이트
    return await handleUserDisplayNameUpdate(uid, beforeValue, afterValue);
  }
);
```

#### 2. photoUrl 생성/수정/삭제 시
```typescript
export const onUserPhotoUrlWrite = onValueWritten(
  { ref: "/users/{uid}/photoUrl", region: FIREBASE_REGION },
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

    // createdAt 없으면 추가
    // updatedAt 업데이트
    return await handleUserPhotoUrlUpdate(uid, beforeValue, afterValue);
  }
);
```

#### 3. birthYearMonthDay 생성/수정/삭제 시
```typescript
export const onUserBirthYearMonthDayWrite = onValueWritten(
  { ref: "/users/{uid}/birthYearMonthDay", region: FIREBASE_REGION },
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

    // 삭제 케이스: 모든 파생 필드(birthYear, birthMonth, birthDay, birthMonthDay) 삭제
    // 생성/수정 케이스: YYYY-MM-DD 파싱 및 파생 필드 생성, createdAt 없으면 추가
    return await handleUserBirthYearMonthDayUpdate(uid, beforeValue, afterValue);
  }
);
```

### 데이터 흐름

#### 생성/수정 케이스
```
사용자 액션: displayName 생성 또는 업데이트
   ↓
/users/{uid}/displayName 변경
   ↓
onUserDisplayNameWrite 트리거
   ↓
handleUserDisplayNameUpdate 실행
   ↓
/users/{uid}/displayNameLowerCase 업데이트
/users/{uid}/updatedAt 업데이트
   ↓
displayName 값은 변경 안 됨 → 재트리거 안 됨 ✅
```

#### 삭제 케이스
```
사용자 액션: displayName 삭제
   ↓
/users/{uid}/displayName = null
   ↓
onUserDisplayNameWrite 트리거
   ↓
handleUserDisplayNameUpdate 실행 (afterValue === null)
   ↓
/users/{uid}/displayNameLowerCase = null (삭제)
/users/{uid}/updatedAt 업데이트
   ↓
데이터 동기화 완료, displayName 값은 변경 안 됨 → 재트리거 안 됨 ✅
```

#### birthYearMonthDay 삭제 케이스 (다중 파생 필드)
```
사용자 액션: birthYearMonthDay 삭제
   ↓
/users/{uid}/birthYearMonthDay = null
   ↓
onUserBirthYearMonthDayWrite 트리거
   ↓
handleUserBirthYearMonthDayUpdate 실행 (afterValue === null)
   ↓
모든 파생 필드 삭제:
  - /users/{uid}/birthYear = null
  - /users/{uid}/birthMonth = null
  - /users/{uid}/birthDay = null
  - /users/{uid}/birthMonthDay = null
   ↓
데이터 동기화 완료, birthYearMonthDay 값은 변경 안 됨 → 재트리거 안 됨 ✅
```

### 주의사항

1. **트리거 필드와 업데이트 필드 분리 필수**
   - 트리거되는 필드는 핸들러 내에서 수정하지 않음
   - 예: displayName 트리거 → displayName 수정 금지

2. **onValueWritten 사용 필수** ⭐ 중요
   - `onValueUpdated` 대신 `onValueWritten` 사용
   - 생성/수정/삭제 모두 감지하여 완전한 데이터 동기화 보장
   - 삭제 케이스를 놓치면 파생 필드가 남아 데이터 불일치 발생

3. **삭제 케이스 처리 필수** ⭐ 중요
   - `afterValue === null` 체크로 삭제 감지
   - 파생 필드도 함께 삭제 (`null` 설정)하여 데이터 동기화
   - 예: displayName 삭제 시 displayNameLowerCase도 삭제
   - 예: birthYearMonthDay 삭제 시 모든 파생 필드 삭제

4. **여러 필드 동시 변경 시**
   - 각 필드별 트리거가 독립적으로 실행됨
   - 예: displayName + photoUrl 동시 변경 → 2개 트리거 모두 실행

5. **createdAt 처리**
   - 모든 핸들러에서 createdAt 체크 필요
   - 한 번만 생성되므로 중복 실행 시에도 안전
   - 삭제 케이스에서는 createdAt 처리하지 않음 (불필요)

---

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

## 정렬 필드 사용 예시

### 목적

photoUrl이 있는 회원을 효율적으로 필터링 및 정렬하기 위한 정렬 필드 사용 방법

### 사용 시나리오

#### 1. 사진이 있는 모든 회원 목록 (가입일 역순)

```typescript
const usersRef = admin.database().ref('users');
const snapshot = await usersRef
  .orderByChild('sort_recentWithPhoto')
  .limitToLast(20)  // 최근 20명
  .once('value');

const users = [];
snapshot.forEach((child) => {
  users.push({
    uid: child.key,
    ...child.val()
  });
});

// 역순 정렬 (최신 → 과거)
users.reverse();
```

#### 2. 사진이 있는 여성 회원만 목록

```typescript
const usersRef = admin.database().ref('users');
const snapshot = await usersRef
  .orderByChild('sort_recentFemaleWithPhoto')
  .limitToLast(20)
  .once('value');

const femaleUsers = [];
snapshot.forEach((child) => {
  const user = child.val();
  // null이 아닌 값만 필터링 (gender=F인 경우만)
  if (user.sort_recentFemaleWithPhoto) {
    femaleUsers.push({
      uid: child.key,
      ...user
    });
  }
});

femaleUsers.reverse();
```

#### 3. 사진이 있는 남성 회원만 목록

```typescript
const usersRef = admin.database().ref('users');
const snapshot = await usersRef
  .orderByChild('sort_recentMaleWithPhoto')
  .limitToLast(20)
  .once('value');

const maleUsers = [];
snapshot.forEach((child) => {
  const user = child.val();
  // null이 아닌 값만 필터링 (gender=M인 경우만)
  if (user.sort_recentMaleWithPhoto) {
    maleUsers.push({
      uid: child.key,
      ...user
    });
  }
});

maleUsers.reverse();
```

#### 4. 특정 기간의 사진 있는 회원 목록

```typescript
// 2024년 1월 1일 이후 가입한 사진 있는 회원
const startDate = new Date('2024-01-01').getTime();

const usersRef = admin.database().ref('users');
const snapshot = await usersRef
  .orderByChild('sort_recentWithPhoto')
  .startAt(startDate)
  .limitToLast(20)
  .once('value');

const recentUsers = [];
snapshot.forEach((child) => {
  recentUsers.push({
    uid: child.key,
    ...child.val()
  });
});

recentUsers.reverse();
```

### 장점

1. **성능 최적화**
   - Firebase Database의 인덱싱을 활용한 빠른 쿼리
   - photoUrl 필드를 직접 체크하지 않고 정렬 필드만 확인

2. **비용 절감**
   - 필요한 데이터만 효율적으로 로드
   - 불필요한 읽기 작업 최소화

3. **유연한 필터링**
   - 성별별로 분리된 필드로 다양한 쿼리 가능
   - 조합 쿼리로 복잡한 조건 처리 가능

### 주의사항

1. **Firebase Database 인덱싱 필요**
   ```json
   {
     "rules": {
       "users": {
         ".indexOn": ["sort_recentWithPhoto", "sort_recentFemaleWithPhoto", "sort_recentMaleWithPhoto"]
       }
     }
   }
   ```

2. **null 값 필터링**
   - `sort_recentFemaleWithPhoto`와 `sort_recentMaleWithPhoto`는 조건에 맞지 않으면 null
   - 쿼리 결과에서 null 값을 필터링해야 함

3. **데이터 동기화** ⭐ 자동 업데이트
   - photoUrl 업데이트 시 자동으로 정렬 필드 업데이트됨
   - **gender 변경 시에도 자동으로 정렬 필드 갱신됨** (`onUserGenderWrite` 트리거)
   - photoUrl과 gender 중 하나라도 변경되면 즉시 정렬 필드가 동기화됨

---

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

- [Cloud Firestore](https://firebase.google.com/docs/firestore)
- [Firebase Admin SDK - Database](https://firebase.google.com/docs/admin/setup#initialize-sdk)
- [TypeScript 타입 안전성](https://www.typescriptlang.org/docs/handbook/2/everyday-types.html)
