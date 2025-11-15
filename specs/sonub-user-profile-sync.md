---
name: sonub-user-profile-sync
version: 2.0.0
description: Firestore 사용자 프로필 자동 동기화 사양
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
step: 15
priority: "***"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-store-auth.md
tags:
  - firestore
  - authentication
  - cloud-functions
---

# 사용자 프로필 자동 동기화

Firebase Authentication 로그인 직후 **Firestore `users/{uid}` 문서**를 업데이트하여 모든 화면에서 동일한 데이터를 읽을 수 있도록 한다. 동기화는 클라이언트(`src/lib/stores/auth.svelte.ts`)와 Cloud Functions(`firebase/functions/src/handlers/user.handler.ts`)가 협업하여 수행한다.

## 1. 클라이언트 동작 (auth.svelte.ts)

1. `onAuthStateChanged`에서 로그인이 감지되면 `syncUserProfile(user)` 실행
2. Firestore 문서(`users/{uid}`)를 읽어 부족한 필드만 채움
   - `displayName`: 문서에 없고 Auth에 값이 있으면 저장
   - `photoUrl`: 문서에 없거나 공백이며 Auth의 `photoURL`이 있으면 저장
   - `languageCode`: 문서에 없으면 브라우저 언어감지(`detectBrowserLanguage()`) 결과 저장
3. 문서가 없을 경우 `setDoc`으로 생성하고 `uid` 필드를 함께 기록
4. 에러 발생 시 console에만 기록 (UI 차단 없음)

> ⚠️ createdAt/updatedAt, 파생 필드는 클라이언트에서 직접 쓰지 않는다.

## 2. Cloud Functions 동작 (user.handler.ts)

### 2.1 handleUserCreate

트리거: `functions.firestore.document('users/{uid}').onCreate`

- `createdAt`가 없으면 현재 timestamp로 설정
- `displayNameLowerCase` 자동 생성
- `system/stats` 문서의 `userCount` 필드 `increment(1)`

### 2.2 handleUserUpdate 계열

`firebase/functions/src/index.ts`에서 변경된 필드별로 전용 핸들러 호출:

| 핸들러 | 조건 | 역할 |
|--------|------|------|
| `handleUserDisplayNameUpdate` | displayName 변경 | `displayNameLowerCase`, `updatedAt` 갱신 |
| `handleUserPhotoUpdate` | photoUrl 변경 | `updatedAt` 갱신 |
| `handleUserBirthYearMonthDayUpdate` | `birthYearMonthDay` 변경 | `birthYear`, `birthMonth`, `birthDay`, `birthMonthDay` 파생 필드 생성 |

모든 핸들러는 Firestore `batch`를 사용해 한번에 커밋한다.

## 3. 데이터 모델

- **Collection**: `users`
- **필수 필드**: `uid`, `displayName`, `photoUrl`, `createdAt`
- **파생 필드**: `displayNameLowerCase`, `sort-recent-with-photo`, `sort-recent-female-with-photo`, `sort-recent-male-with-photo`, `birthYear`, `birthMonth`, `birthDay`, `birthMonthDay`
- **타임스탬프 타입**: 클라이언트는 숫자(ms)를 쓰지 않고 Cloud Functions가 Firestore Timestamp 혹은 number를 일관되게 작성 (현재 구현은 `Date.now()` 기반 숫자)

## 4. 구현 규칙

1. `auth.svelte.ts`는 Firestore 인스턴스(`db`)가 없으면 경고만 출력 후 종료
2. 업데이트할 필드가 없으면 Firestore 호출을 생략
3. Cloud Functions는 절대로 사용자 입력 필드를 덮어쓰지 않고, 파생/시스템 필드만 관리
4. `system/stats` 문서는 단일 문서이며, 존재하지 않으면 `set({ userCount: increment(1) }, { merge: true })`

## 5. 테스트 시나리오

| 항목 | 절차 | 기대 결과 |
|------|------|-----------|
| 신규 로그인 | Google 로그인 → Firestore `users/{uid}` 확인 | 문서 생성, `displayName`/`photoUrl`/`languageCode` 세팅, Cloud Functions로 `createdAt` 추가 |
| displayName 변경 | Auth 프로필 변경 후 로그인 | `displayName`/`displayNameLowerCase` 업데이트, `updatedAt` 갱신 |
| 생년월일 입력 | 사용자 설정 화면에서 `birthYearMonthDay` 저장 | Cloud Functions가 `birthYear`, `birthMonth`, `birthDay`, `birthMonthDay` 작성 |
| 로그아웃 후 재로그인 | 이미 필드가 있는 사용자 | Firestore 호출 없이 무시, 콘솔에 “동기화할 정보 없음” 로그 |

## 6. 참고 파일

- `src/lib/stores/auth.svelte.ts`
- `firebase/functions/src/handlers/user.handler.ts`
- `firebase/functions/src/index.ts`
- `src/lib/types/firestore.types.ts`
