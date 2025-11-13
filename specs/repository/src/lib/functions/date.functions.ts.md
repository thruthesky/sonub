---
name: date.functions.ts
description: Svelte 클라이언트용 날짜 함수 (Paraglide i18n 통합)
version: 1.0.0
type: typescript
category: library
tags: [date, time, i18n, paraglide, format, client]
---

# date.functions.ts

## 개요
이 파일은 Svelte 클라이언트에서 사용하는 날짜/시간 포맷팅 함수들을 제공합니다. Paraglide i18n과 통합되어 사용자의 현재 언어 설정에 따라 자동으로 날짜 형식을 변환합니다. 실제 비즈니스 로직은 `shared/date.pure-functions.ts`에 있으며, 이 파일은 Paraglide wrapper 역할을 합니다.

## 소스 코드

```typescript
/**
 * Svelte 클라이언트용 날짜 함수들
 *
 * Paraglide i18n과 통합된 wrapper 함수들입니다.
 * 실제 비즈니스 로직은 shared/date.pure-functions.ts에 있습니다.
 */

import { getLocale } from '$lib/paraglide/runtime.js';
import {
	formatLongDate as formatLongDatePure,
	formatShortDate as formatShortDatePure
} from '$shared/date.pure-functions';

/**
 * Paraglide에서 선택된 언어 코드를 Intl DateTimeFormat에 맞는 형태로 반환합니다.
 *
 * @returns BCP 47 locale 코드 (예: 'ko-KR', 'en-US', 'ja-JP', 'zh-CN')
 */
function resolveLocale(): string {
	const currentLocale = getLocale();
	return currentLocale === 'ko'
		? 'ko-KR'
		: currentLocale === 'ja'
			? 'ja-JP'
			: currentLocale === 'zh'
				? 'zh-CN'
				: 'en-US';
}

/**
 * 긴 형식의 날짜/시간 문자열을 반환합니다.
 *
 * Paraglide의 현재 locale을 자동으로 사용합니다.
 *
 * @param timestamp - Unix 타임스탬프 (밀리초)
 * @returns 연-월-일과 시:분:초를 모두 포함한 문자열
 */
export function formatLongDate(timestamp?: number | null): string {
	return formatLongDatePure(timestamp, resolveLocale());
}

/**
 * 짧은 형식의 날짜/시간 문자열을 반환합니다.
 *
 * Paraglide의 현재 locale을 자동으로 사용합니다.
 * 오늘 날짜라면 HH:MM AM/PM, 아니라면 YYYY/MM/DD 형태로 반환합니다.
 *
 * @param value - Unix 타임스탬프 (밀리초)
 * @returns 짧은 형식의 날짜 문자열
 */
export function formatShortDate(value?: number | null): string {
	return formatShortDatePure(value, resolveLocale());
}
```

## 주요 기능

### 1. resolveLocale()
- **목적**: Paraglide 언어 코드를 Intl API 로케일로 변환
- **매핑**:
  - `ko` → `ko-KR` (한국어)
  - `ja` → `ja-JP` (일본어)
  - `zh` → `zh-CN` (중국어 간체)
  - 기타 → `en-US` (영어)
- **사용**: 내부 헬퍼 함수

### 2. formatLongDate(timestamp)
- **목적**: 긴 형식의 날짜/시간 문자열 생성
- **자동 다국어**: Paraglide 현재 언어 자동 적용
- **예시**:
  - 한국어: `"2025년 11월 13일 오후 12:30:45"`
  - 영어: `"November 13, 2025 at 12:30:45 PM"`
  - 일본어: `"2025年11月13日 12:30:45"`

### 3. formatShortDate(value)
- **목적**: 짧은 형식의 날짜/시간 문자열 생성
- **자동 다국어**: Paraglide 현재 언어 자동 적용
- **로직**:
  - **오늘**: 시간만 표시 (예: `"오후 12:30"`, `"12:30 PM"`)
  - **이전 날짜**: 날짜만 표시 (예: `"2025/11/12"`)

## Paraglide 통합

### 언어 감지 흐름
1. **사용자 요청** → 브라우저 쿠키/헤더
2. **서버 훅** → `hooks.server.ts`에서 로케일 감지
3. **Paraglide 설정** → `getLocale()` 반환값 업데이트
4. **자동 적용** → `formatLongDate()`, `formatShortDate()` 호출 시 자동 반영

### Pure Function 분리 이유
- **서버 공유**: Cloud Functions에서도 동일한 로직 사용
- **테스트 용이**: 순수 함수는 단위 테스트 작성이 간단
- **의존성 분리**: Paraglide 의존성을 클라이언트 레이어에만 한정

## 사용 예시

```svelte
<script lang="ts">
import { formatLongDate, formatShortDate } from '$lib/functions/date.functions';

const timestamp = Date.now();

// 자동으로 현재 언어에 맞춰 포맷팅됨
const long = formatLongDate(timestamp);
// 한국어: "2025년 11월 13일 오후 12:30:45"
// 영어: "November 13, 2025 at 12:30:45 PM"

const short = formatShortDate(timestamp);
// 오늘: "오후 12:30" (한국어) / "12:30 PM" (영어)
// 어제: "2025/11/12"
</script>

<p>{long}</p>
<p>{short}</p>
```

## 비교: Pure Function vs Wrapper

| 구분 | Pure Function | Wrapper (이 파일) |
|------|---------------|------------------|
| 위치 | `shared/date.pure-functions.ts` | `src/lib/functions/date.functions.ts` |
| 의존성 | 없음 (JavaScript 표준만) | Paraglide i18n |
| 사용처 | 클라이언트 + 서버 | 클라이언트만 |
| 로케일 | 명시적 전달 | 자동 감지 |
| 테스트 | 매우 쉬움 | Paraglide mock 필요 |
