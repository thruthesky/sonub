---
name: date.pure-functions.ts
description: date.pure-functions 순수 함수
version: 1.0.0
type: typescript
category: pure-function
original_path: shared/date.pure-functions.ts
---

# date.pure-functions.ts

## 개요

**파일 경로**: `shared/date.pure-functions.ts`
**파일 타입**: typescript
**카테고리**: pure-function

date.pure-functions 순수 함수

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

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
