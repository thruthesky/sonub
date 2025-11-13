---
name: date.pure-functions.ts
description: 날짜/시간 관련 순수 함수 모음 (외부 의존성 없음)
version: 1.0.0
type: pure-function
category: shared
tags: [pure-function, date, time, format, utility]
---

# date.pure-functions.ts

## 개요
이 파일은 날짜/시간 처리와 관련된 완전한 순수 함수(Pure Functions)만 포함합니다. Firebase, Svelte, Paraglide 등 어떠한 외부 의존성도 없으며, JavaScript의 표준 `Date` 객체와 `Intl` API만 사용합니다. 클라이언트와 서버(Cloud Functions)에서 공유하여 사용합니다.

## 소스 코드

```typescript
/**
 * 날짜/시간 관련 순수 함수 모음
 *
 * 이 파일은 완전한 pure functions만 포함합니다.
 * Firebase, Svelte, Paraglide 등 외부 의존성이 없습니다.
 */

/**
 * 긴 형식의 날짜/시간 문자열.
 *
 * @param timestamp - Unix 타임스탬프 (밀리초)
 * @param locale - 언어 코드 (예: 'ko-KR', 'en-US', 'ja-JP', 'zh-CN')
 * @returns 연-월-일과 시:분:초를 모두 포함한 문자열
 */
export function formatLongDate(timestamp?: number | null, locale: string = 'en-US'): string {
	if (!timestamp) return '';
	const date = new Date(timestamp);

	return date.toLocaleString(locale, {
		year: 'numeric',
		month: 'long',
		day: 'numeric',
		hour: '2-digit',
		minute: '2-digit',
		second: '2-digit'
	});
}

/**
 * 짧은 형식의 날짜/시간 문자열.
 *
 * 오늘 날짜라면 HH:MM AM/PM, 아니라면 YYYY/MM/DD 형태로 반환한다.
 *
 * @param value - Unix 타임스탬프 (밀리초)
 * @param locale - 언어 코드 (예: 'ko-KR', 'en-US', 'ja-JP', 'zh-CN')
 */
export function formatShortDate(value?: number | null, locale: string = 'en-US'): string {
	if (!value) return '';

	const target = new Date(value);
	const now = new Date();

	const sameDay =
		target.getFullYear() === now.getFullYear() &&
		target.getMonth() === now.getMonth() &&
		target.getDate() === now.getDate();

	if (sameDay) {
		return target.toLocaleTimeString(locale, {
			hour: '2-digit',
			minute: '2-digit',
			hour12: true
		});
	}

	const year = target.getFullYear();
	const month = String(target.getMonth() + 1).padStart(2, '0');
	const day = String(target.getDate()).padStart(2, '0');
	return `${year}/${month}/${day}`;
}
```

## 주요 기능

### 1. formatLongDate(timestamp, locale)
- **목적**: 긴 형식의 날짜/시간 문자열 생성
- **입력**:
  - `timestamp`: Unix 타임스탬프 (밀리초)
  - `locale`: 언어 코드 (기본값: 'en-US')
- **출력**: 연-월-일 + 시:분:초 전체 표시
- **예시**:
  - 한국어: `"2025년 11월 13일 오후 12:30:45"`
  - 영어: `"November 13, 2025 at 12:30:45 PM"`
  - 일본어: `"2025年11月13日 12:30:45"`

### 2. formatShortDate(value, locale)
- **목적**: 짧은 형식의 날짜/시간 문자열 생성
- **입력**:
  - `value`: Unix 타임스탬프 (밀리초)
  - `locale`: 언어 코드 (기본값: 'en-US')
- **로직**:
  - **오늘 날짜**: 시간만 표시 (HH:MM AM/PM)
  - **이전 날짜**: 날짜만 표시 (YYYY/MM/DD)
- **예시**:
  - 오늘: `"12:30 PM"` (한국어: `"오후 12:30"`)
  - 어제: `"2025/11/12"`

### Intl API 활용
- **다국어 지원**: `toLocaleString()`, `toLocaleTimeString()`
- **자동 현지화**: 언어별 날짜 형식 자동 적용
- **표준 API**: Node.js와 브라우저 모두 지원

## Pure Function 특징

### 순수성 보장
- **부수 효과 없음**: 외부 상태를 변경하지 않음
- **참조 투명성**: 동일한 입력 → 동일한 출력
- **테스트 용이성**: 단위 테스트 작성이 간단함

### 공유 가능
- **클라이언트**: Svelte 컴포넌트에서 import
- **서버**: Firebase Cloud Functions에서 import
- **일관성**: 클라이언트/서버 로직 동기화

## 사용 예시

```typescript
import { formatLongDate, formatShortDate } from '$shared/date.pure-functions';

// 긴 형식 (한국어)
const long = formatLongDate(Date.now(), 'ko-KR');
// "2025년 11월 13일 오후 12:30:45"

// 짧은 형식 (영어)
const short = formatShortDate(Date.now(), 'en-US');
// "12:30 PM" (오늘) 또는 "2025/11/13" (다른 날)

// 빈 값 처리
formatLongDate(null); // ""
formatShortDate(undefined); // ""
```

## Paraglide 통합
클라이언트에서는 `src/lib/functions/date.functions.ts`가 Paraglide와 통합하여 자동으로 현재 로케일을 적용합니다:

```typescript
// src/lib/functions/date.functions.ts
import { getLocale } from '$lib/paraglide/runtime.js';
import { formatLongDate as formatLongDatePure } from '$shared/date.pure-functions';

export function formatLongDate(timestamp?: number | null): string {
  const locale = getLocale() === 'ko' ? 'ko-KR' : 'en-US';
  return formatLongDatePure(timestamp, locale);
}
```
