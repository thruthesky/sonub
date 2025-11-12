---
name: sonub-functions-overview
title: Sonub Functions 운영 규칙
version: 1.1.0
description: Sonub 프로젝트의 재사용 가능한 함수 관리 원칙과 파일 구조
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-11
updated: 2025-11-12
step: 25
priority: "**"
dependencies:
  - sonub-setup-svelte.md
tags:
  - functions
  - architecture
  - pure-functions
  - reusable-code
---

# Sonub 함수 개요

## 1. 목적

본 문서는 Sonub 코드베이스에서 **재사용 가능하고 테스트 가능한 모든 함수**를 어떻게 정의하고 보관하는지에 대한 공통 규칙을 설명합니다. 이러한 함수들은 순수 함수(pure function)뿐만 아니라, 테스트와 재사용을 위해 분리할 수 있는 모든 코드를 포함합니다. 모든 세부 도메인 명세(예: `sonub-functions-chat-functions.md`)는 이 문서를 기준으로 작성/유지됩니다.

## 2. 함수 정의 및 범위

### 2.1. 순수 함수 (Pure Functions)

순수 함수는 다음 조건을 모두 만족해야 합니다:

1. 동일한 입력에 대해 항상 동일한 출력을 반환한다.
2. 외부 상태(스토어, DOM, 네트워크, 로컬 스토리지 등)를 읽거나 변경하지 않는다.
3. 내부적으로 시간, 난수, Date.now, Math.random 등을 직접 호출하지 않는다.
4. 예외 발생 여부를 제외하고 부수 효과(side effect)가 없어야 한다.

### 2.2. 재사용 가능한 함수 (Reusable Functions)

순수 함수 외에도 다음과 같은 함수들을 포함할 수 있습니다:

1. **데이터베이스 작업 함수**: Firebase Realtime Database와 상호작용하는 함수 (예: `joinChatRoom`, `leaveChatRoom`)
2. **비동기 작업 함수**: API 호출, 파일 업로드 등 외부 리소스와 상호작용하는 함수
3. **유틸리티 함수**: 재사용 가능하고 테스트 가능한 모든 헬퍼 함수

### 2.3. Firebase Cloud Functions 호환성

**🔥 매우 중요**: `src/lib/functions/*.functions.ts` 파일의 모든 코드는 **Firebase Cloud Functions의 TypeScript 환경과 호환**되어야 합니다.

- Firebase Cloud Functions에서도 동일한 함수를 import하여 사용할 수 있어야 합니다
- 브라우저 전용 API (예: `window`, `document`, `localStorage`)를 사용해서는 안 됩니다
- Svelte 컴포넌트나 Svelte 런타임 전용 기능을 사용해서는 안 됩니다
- Firebase Admin SDK와 Firebase Client SDK 모두에서 사용 가능한 공통 API만 사용해야 합니다

**예시**:
```typescript
// ✅ 올바른 예시 - Cloud Functions와 클라이언트 모두에서 사용 가능
export async function joinChatRoom(db: Database, roomId: string, uid: string): Promise<void> {
  const userRef = ref(db, `chat-rooms/${roomId}/users/${uid}`);
  await set(userRef, true);
}

// ❌ 잘못된 예시 - 브라우저 API 사용
export function saveToLocalStorage(key: string, value: string): void {
  localStorage.setItem(key, value); // Cloud Functions에서 사용 불가
}

// ❌ 잘못된 예시 - Svelte 런타임 사용
import { writable } from 'svelte/store';
export const myStore = writable(0); // Cloud Functions에서 사용 불가
```

## 3. 저장 위치 및 네이밍

### 3.1. Shared Pure Functions (서버/클라이언트 공유)

**🆕 신규 아키텍처**: 완전한 순수 함수는 `shared/` 폴더에 저장합니다.

- 모든 **100% 순수 함수**는 프로젝트 루트의 `shared/` 폴더에 위치한다.
- 파일명은 `<도메인>.pure-functions.ts` 패턴을 따른다.
  - 예) 채팅 관련 → `chat.pure-functions.ts`, 날짜 유틸 → `date.pure-functions.ts`
- **특징**:
  - ✅ Zero External Dependencies (외부 의존성 전혀 없음)
  - ✅ Framework Independent (Firebase, Svelte, Paraglide 등 어떤 프레임워크에도 독립적)
  - ✅ 서버와 클라이언트가 동일한 코드를 사용 (코드 중복 제거)
  - ✅ 완벽한 단위 테스트 가능

**예시**:
```
/Users/thruthesky/apps/sonub/
├── shared/                          # 순수 함수 전용 폴더 (NEW)
│   ├── date.pure-functions.ts      # 날짜 관련 순수 함수
│   └── chat.pure-functions.ts      # 채팅 관련 순수 함수
```

### 3.2. Svelte Client Functions (클라이언트 전용)

- Svelte 클라이언트 전용 함수는 `src/lib/functions` 폴더에 위치한다.
- 파일명은 `<도메인>.functions.ts` 패턴을 따른다.
  - 예) 채팅 관련 → `chat.functions.ts`, 게시글 → `post.functions.ts`, 날짜 유틸 → `date.functions.ts`
- **역할**:
  - ✅ Shared pure functions를 re-export (재사용)
  - ✅ Firebase Client SDK를 사용하는 함수 (예: `joinChatRoom`, `leaveChatRoom`)
  - ✅ Paraglide i18n 같은 Svelte 전용 기능을 wrapper로 제공
- 하나의 파일에는 동일 도메인에 속한 함수만 배치한다.
- `default export` 는 사용하지 않으며, 반드시 **Named Export** 로 내보낸다.

**예시**:
```typescript
// src/lib/functions/date.functions.ts
import { getLocale } from '$lib/paraglide/runtime.js';
import {
  formatLongDate as formatLongDatePure,
  formatShortDate as formatShortDatePure
} from '$shared/date.pure-functions';

// Paraglide locale을 자동 주입하는 wrapper
export function formatLongDate(timestamp?: number | null): string {
  return formatLongDatePure(timestamp, getLocale());
}
```

```typescript
// src/lib/functions/chat.functions.ts
import { ref, set, type Database } from 'firebase/database';

// Pure functions 재사용
export {
  buildSingleRoomId,
  isSingleChat,
  extractUidsFromSingleRoomId,
  resolveRoomTypeLabel
} from '$shared/chat.pure-functions';

// Firebase 의존 함수는 여기에 유지
export async function joinChatRoom(db: Database, roomId: string, uid: string) {
  const memberRef = ref(db, `chat-rooms/${roomId}/members/${uid}`);
  await set(memberRef, true);
}
```

### 3.3. Firebase Cloud Functions (서버 전용)

- Firebase Functions는 `firebase/functions/src/handlers/` 폴더에 위치한다.
- **Shared pure functions 직접 사용**: Cloud Functions는 `shared/` 폴더에서 직접 import한다.

**예시**:
```typescript
// firebase/functions/src/handlers/chat.handler.ts
import * as admin from 'firebase-admin';
import {
  isSingleChat,
  extractUidsFromSingleRoomId
} from '../../../../shared/chat.pure-functions';

export async function handleChatMessageCreate(roomId: string) {
  if (isSingleChat(roomId)) {
    const uids = extractUidsFromSingleRoomId(roomId);
    // ...
  }
}
```

## 4. 코드 작성 규칙

1. **Firebase Cloud Functions 호환성**: 브라우저 전용 API, Svelte 런타임 등 Cloud Functions에서 사용할 수 없는 의존성을 import 하지 않는다.
2. **의존성 제한**:
   - ✅ 허용: `firebase/database`, `firebase-admin` (Cloud Functions 환경에서는 admin SDK 사용)
   - ❌ 금지: `svelte/store`, `browser API` (window, document, localStorage 등), Svelte 컴포넌트
3. **테스트 용이성**: 각 함수는 독립적으로 단위 테스트가 가능하도록 설계한다.
4. **주석**: 함수 상단에 "무엇을 하는지" 한 줄 이상의 설명을 남긴다.
5. **타입 명시**: 모든 매개변수와 반환 타입을 명시하여 타입 추론에 의존하지 않는다.
6. **파일 내 구조**:
   - 최상단: 파일 개요 주석 (Firebase Cloud Functions 호환성 명시)
   - 이후: 함수 정의
   - 유틸 상수/헬퍼가 필요하다면 동일 파일 최하단에 배치하고 export 하지 않는다.

## 5. 도큐멘트 연계

### 5.1. Shared Pure Functions 문서
- [`sonub-shared-date-pure-functions.md`](./sonub-shared-date-pure-functions.md) - 날짜 관련 순수 함수
- [`sonub-shared-chat-pure-functions.md`](./sonub-shared-chat-pure-functions.md) - 채팅 관련 순수 함수

### 5.2. Svelte Client Functions 문서
- [`sonub-functions-chat-functions.md`](./sonub-functions-chat-functions.md) - 채팅 관련 클라이언트 함수
- [`sonub-functions-date-functions.md`](./sonub-functions-date-functions.md) - 날짜 관련 클라이언트 함수

### 5.3. 일반 원칙
- 새로운 도메인 문서를 추가할 때는 반드시 이 문서를 선행 참고하고 교차 링크를 명시한다.
- 순수 함수를 추가할 때는 먼저 `shared/` 폴더에 생성하고, 필요시 클라이언트/서버에서 wrapper를 만든다.

## 6. 검증 체크리스트

- [ ] Firebase Cloud Functions의 TypeScript 환경과 호환되는가?
- [ ] 브라우저 전용 API나 Svelte 런타임을 사용하지 않는가?
- [ ] `src/lib/functions/<도메인>.functions.ts` 에 위치하는가?
- [ ] Named export만 사용했는가?
- [ ] 타입 및 주석이 명확하게 작성되었는가?
- [ ] 테스트 가능하도록 설계되었는가?

모든 항목이 충족되지 않으면 해당 함수는 functions 모듈에 포함할 수 없습니다.
