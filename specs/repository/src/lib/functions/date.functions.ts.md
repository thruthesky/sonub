---
title: date.functions.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 date.functions.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
