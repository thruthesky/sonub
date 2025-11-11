/**
 * 날짜/시간 관련 순수 함수 모음
 */

import { getLocale } from '$lib/paraglide/runtime.js';

/**
 * Paraglide에서 선택된 언어 코드를 Intl DateTimeFormat에 맞는 형태로 반환한다.
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
 * 긴 형식의 날짜/시간 문자열.
 *
 * @param timestamp - Unix 타임스탬프 (밀리초)
 * @returns 연-월-일과 시:분:초를 모두 포함한 문자열
 */
export function formatLongDate(timestamp?: number | null): string {
	if (!timestamp) return '';
	const date = new Date(timestamp);

	return date.toLocaleString(resolveLocale(), {
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
 */
export function formatShortDate(value?: number | null): string {
	if (!value) return '';

	const locale = resolveLocale();
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
