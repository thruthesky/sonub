---
name: sonub-shared-date-pure-functions
title: Sonub Shared Date Pure Functions
version: 1.0.0
description: 서버와 클라이언트가 공유하는 날짜 관련 순수 함수 모음
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-12
updated: 2025-11-12
step: 30
priority: "**"
dependencies:
  - sonub-functions-overview.md
  - sonub-functions-date-functions.md
tags:
  - shared
  - pure-functions
  - date
  - utilities
---

# Sonub Shared Date Pure Functions

## 1. 목적

본 문서는 Sonub 프로젝트의 `shared/date.pure-functions.ts` 파일에 정의된 **완전한 순수 함수(Pure Functions)**에 대해 설명합니다. 이 함수들은 다음과 같은 특징을 가집니다:

- ✅ **100% Pure Functions**: 외부 의존성이 전혀 없음
- ✅ **서버/클라이언트 공유**: Firebase Functions와 Svelte 클라이언트 모두에서 사용
- ✅ **Framework 독립적**: Firebase, Svelte, Paraglide 등 어떤 프레임워크에도 의존하지 않음
- ✅ **테스트 용이성**: 독립적으로 단위 테스트 가능

## 2. 아키텍처 개요

### 2.1. 파일 위치

```
/Users/thruthesky/apps/sonub/
├── shared/                          # 공유 모듈 (NEW)
│   ├── date.pure-functions.ts      # 날짜 순수 함수
│   └── chat.pure-functions.ts      # 채팅 순수 함수
├── src/lib/functions/               # Svelte 클라이언트 함수
│   ├── date.functions.ts           # Paraglide wrapper
│   └── chat.functions.ts           # Firebase wrapper + re-export
└── firebase/functions/src/handlers/ # Cloud Functions 핸들러
    └── chat.handler.ts             # shared 함수 사용
```

### 2.2. 의존성 그래프

```
┌─────────────────────────────────────────────────────┐
│  shared/date.pure-functions.ts                      │
│  - 100% Pure Functions                              │
│  - Zero External Dependencies                       │
│  - Framework Independent                            │
└─────────────────┬───────────────────────────────────┘
                  │
        ┌─────────┴─────────┐
        ▼                   ▼
┌───────────────────┐  ┌──────────────────────────────┐
│ Svelte Client     │  │ Firebase Cloud Functions     │
│ (Wrapper Layer)   │  │ (Direct Import)              │
├───────────────────┤  ├──────────────────────────────┤
│ date.functions.ts │  │ handlers/chat.handler.ts     │
│ - Paraglide 주입  │  │ - Admin SDK와 함께 사용      │
│ - locale 자동화   │  │ - Server-side 로직           │
└───────────────────┘  └──────────────────────────────┘
```

## 3. 함수 명세

### 3.1. `formatLongDate()`

긴 형식의 날짜/시간 문자열을 반환합니다.

**시그니처**:
```typescript
function formatLongDate(
  timestamp?: number | null,
  locale: string = 'en-US'
): string
```

**파라미터**:
- `timestamp` (optional): Unix 타임스탬프 (밀리초). `undefined` 또는 `null`인 경우 빈 문자열 반환
- `locale` (optional): BCP 47 locale 코드 (기본값: `'en-US'`)
  - 지원 언어: `'ko-KR'`, `'en-US'`, `'ja-JP'`, `'zh-CN'`

**반환값**:
- `string`: 연-월-일과 시:분:초를 포함한 로컬라이즈된 문자열
- 예시 (ko-KR): `"2025년 11월 12일 오후 10:30:45"`
- 예시 (en-US): `"November 12, 2025 at 10:30:45 PM"`

**Pure Function 보장**:
- ✅ 동일 입력 → 동일 출력
- ✅ 외부 상태 읽기/쓰기 없음
- ✅ 부수 효과(side effect) 없음

**예시**:
```typescript
// 한국어
formatLongDate(1699876845000, 'ko-KR')
// → "2023년 11월 13일 오후 10:47:25"

// 영어
formatLongDate(1699876845000, 'en-US')
// → "November 13, 2023 at 10:47:25 PM"

// 빈 값 처리
formatLongDate(null, 'ko-KR')
// → ""
```

### 3.2. `formatShortDate()`

짧은 형식의 날짜/시간 문자열을 반환합니다. 오늘 날짜면 시간만, 과거 날짜면 날짜만 표시합니다.

**시그니처**:
```typescript
function formatShortDate(
  value?: number | null,
  locale: string = 'en-US'
): string
```

**파라미터**:
- `value` (optional): Unix 타임스탬프 (밀리초). `undefined` 또는 `null`인 경우 빈 문자열 반환
- `locale` (optional): BCP 47 locale 코드 (기본값: `'en-US'`)

**반환값**:
- `string`:
  - **오늘 날짜인 경우**: `"HH:MM AM/PM"` 형식 (예: `"10:30 PM"`)
  - **과거 날짜인 경우**: `"YYYY/MM/DD"` 형식 (예: `"2023/11/12"`)

**비즈니스 로직**:
1. 현재 날짜(`new Date()`)와 입력 타임스탬프의 년/월/일을 비교
2. 동일하면 → 시간만 표시 (12시간 형식)
3. 다르면 → 날짜만 표시 (YYYY/MM/DD)

**Pure Function 보장**:
- ⚠️ **주의**: `new Date()`를 사용하여 현재 시간을 얻으므로, 엄격한 의미의 순수 함수는 아닙니다
- 하지만 동일 시점에 실행하면 동일한 결과를 보장합니다
- 테스트 시 시간을 모킹하여 테스트 가능합니다

**예시**:
```typescript
const now = Date.now(); // 2025-11-12 22:30:00

// 오늘 날짜 (같은 날)
formatShortDate(now, 'ko-KR')
// → "오후 10:30"

formatShortDate(now, 'en-US')
// → "10:30 PM"

// 과거 날짜 (다른 날)
formatShortDate(1699876845000, 'ko-KR')
// → "2023/11/13"

// 빈 값 처리
formatShortDate(undefined, 'en-US')
// → ""
```

## 4. 사용 방법

### 4.1. Svelte 클라이언트에서 사용

Svelte 클라이언트는 `src/lib/functions/date.functions.ts`를 통해 간접적으로 사용합니다. 이 wrapper는 Paraglide의 locale을 자동으로 주입합니다.

```typescript
// src/lib/functions/date.functions.ts
import { getLocale } from '$lib/paraglide/runtime.js';
import {
  formatLongDate as formatLongDatePure,
  formatShortDate as formatShortDatePure
} from '$shared/date.pure-functions';

function resolveLocale(): string {
  const currentLocale = getLocale();
  return currentLocale === 'ko' ? 'ko-KR'
    : currentLocale === 'ja' ? 'ja-JP'
    : currentLocale === 'zh' ? 'zh-CN'
    : 'en-US';
}

// Paraglide locale을 자동으로 주입하는 wrapper
export function formatLongDate(timestamp?: number | null): string {
  return formatLongDatePure(timestamp, resolveLocale());
}

export function formatShortDate(value?: number | null): string {
  return formatShortDatePure(value, resolveLocale());
}
```

**컴포넌트에서 사용**:
```svelte
<script lang="ts">
  import { formatLongDate, formatShortDate } from '$lib/functions/date.functions';

  const timestamp = 1699876845000;
</script>

<div>
  <p>긴 형식: {formatLongDate(timestamp)}</p>
  <p>짧은 형식: {formatShortDate(timestamp)}</p>
</div>
```

### 4.2. Firebase Cloud Functions에서 사용

Firebase Functions는 shared 폴더에서 직접 import하여 사용합니다.

```typescript
// firebase/functions/src/handlers/notification.handler.ts
import {
  formatLongDate,
  formatShortDate
} from '../../../../shared/date.pure-functions';

export async function sendNotification(timestamp: number, userLocale: string) {
  const formattedDate = formatLongDate(timestamp, userLocale);

  // 알림 발송 로직...
  await admin.messaging().send({
    notification: {
      title: '새 메시지',
      body: `${formattedDate}에 메시지가 도착했습니다`
    }
  });
}
```

### 4.3. TypeScript 설정

**Svelte tsconfig.json**:
```json
{
  "extends": "./.svelte-kit/tsconfig.json",
  "compilerOptions": {
    // SvelteKit이 $shared alias를 자동으로 처리
  }
}
```

**svelte.config.js**:
```javascript
export default {
  kit: {
    alias: {
      $shared: './shared'
    }
  }
};
```

**Firebase Functions tsconfig.json**:
```json
{
  "compilerOptions": {
    "module": "CommonJS",
    "paths": {
      "@shared/*": ["../../shared/*"]
    },
    "rootDirs": [
      "./src",
      "../../shared"
    ]
  },
  "include": [
    "src",
    "../../shared/**/*.ts"
  ]
}
```

**Vite 설정 (vite.config.ts)**:
```typescript
export default defineConfig({
  server: {
    fs: {
      // shared 폴더를 Vite가 서빙할 수 있도록 허용
      allow: ['..']
    }
  }
});
```

## 5. 설계 원칙

### 5.1. 완전한 순수 함수 유지

```typescript
// ✅ 올바른 예시 - locale을 파라미터로 받음
export function formatLongDate(
  timestamp?: number | null,
  locale: string = 'en-US'
): string {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  return date.toLocaleString(locale, { /* options */ });
}

// ❌ 잘못된 예시 - 외부 의존성 사용
import { getLocale } from '$lib/paraglide/runtime.js';  // ❌
export function formatLongDate(timestamp?: number | null): string {
  const locale = getLocale();  // ❌ 외부 상태 읽기
  // ...
}
```

### 5.2. 의존성 주입 패턴

외부 의존성이 필요한 경우 **파라미터로 주입**합니다:

```typescript
// Pure function: locale을 파라미터로 받음
export function formatLongDate(timestamp: number, locale: string): string {
  // ...
}

// Wrapper function: 의존성을 주입
export function formatLongDateWithCurrentLocale(timestamp: number): string {
  const locale = getLocale();  // 여기서 외부 상태 읽기
  return formatLongDate(timestamp, locale);  // Pure function 호출
}
```

### 5.3. Zero External Dependencies

```typescript
// ✅ 허용: 표준 JavaScript API만 사용
const date = new Date(timestamp);
const formatted = date.toLocaleString(locale, options);

// ❌ 금지: 외부 라이브러리
import moment from 'moment';  // ❌
import { getLocale } from '$lib/paraglide/runtime.js';  // ❌
import { ref } from 'firebase/database';  // ❌
```

## 6. 테스트 전략

### 6.1. 단위 테스트

```typescript
// shared/date.pure-functions.test.ts
import { describe, it, expect } from 'vitest';
import { formatLongDate, formatShortDate } from './date.pure-functions';

describe('formatLongDate', () => {
  it('should format timestamp in Korean', () => {
    const result = formatLongDate(1699876845000, 'ko-KR');
    expect(result).toContain('2023년 11월');
  });

  it('should return empty string for null', () => {
    const result = formatLongDate(null, 'ko-KR');
    expect(result).toBe('');
  });
});

describe('formatShortDate', () => {
  it('should show time only for today', () => {
    const now = Date.now();
    const result = formatShortDate(now, 'en-US');
    expect(result).toMatch(/\d{1,2}:\d{2}\s[AP]M/);
  });

  it('should show date only for past dates', () => {
    const past = 1699876845000;
    const result = formatShortDate(past, 'ko-KR');
    expect(result).toMatch(/\d{4}\/\d{2}\/\d{2}/);
  });
});
```

### 6.2. 통합 테스트

Svelte 컴포넌트와 Firebase Functions에서의 사용을 검증합니다.

## 7. 버전 관리 및 변경 이력

| 버전  | 날짜       | 변경 내용                                      |
| ----- | ---------- | ---------------------------------------------- |
| 1.0.0 | 2025-11-12 | 최초 작성: 순수 함수 분리 및 shared 모듈 생성 |

## 8. 관련 문서

- [Sonub Functions Overview](./sonub-functions-overview.md) - Functions 아키텍처 개요
- [Sonub Functions Date Functions](./sonub-functions-date-functions.md) - Svelte wrapper 함수
- [Sonub Shared Chat Pure Functions](./sonub-shared-chat-pure-functions.md) - 채팅 순수 함수
- [Sonub Project Overview](./sonub-project-overview.md) - 프로젝트 전체 개요
