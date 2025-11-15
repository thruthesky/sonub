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

/**
 * 채팅 메시지용 날짜/시간 문자열.
 *
 * - 오늘 날짜이면: "시:분 ap" (예: "3:45 PM", "오후 3:45")
 * - 오늘이 아니지만 올해이면: "월/일 시:분 ap" (예: "11/14 3:45 PM", "11월 14일 오후 3:45")
 * - 올해가 아니면: "x년 x월 x일 시:분 ap" (예: "2024년 11월 14일 오후 3:45")
 *
 * @param value - Unix 타임스탬프 (밀리초)
 * @param locale - 언어 코드 (예: 'ko-KR', 'en-US', 'ja-JP', 'zh-CN')
 * @returns 채팅 메시지용 날짜/시간 문자열
 */
export function formatChatMessageDate(value?: number | null, locale: string = 'en-US'): string {
	if (!value) return '';

	const target = new Date(value);
	const now = new Date();

	// 오늘 날짜인지 확인
	const isToday =
		target.getFullYear() === now.getFullYear() &&
		target.getMonth() === now.getMonth() &&
		target.getDate() === now.getDate();

	// 같은 해인지 확인
	const isSameYear = target.getFullYear() === now.getFullYear();

	if (isToday) {
		// 오늘 날짜: "시:분 ap"
		return target.toLocaleTimeString(locale, {
			hour: 'numeric',
			minute: '2-digit',
			hour12: true
		});
	} else if (isSameYear) {
		// 올해: "월/일 시:분 ap"
		return target.toLocaleString(locale, {
			month: 'numeric',
			day: 'numeric',
			hour: 'numeric',
			minute: '2-digit',
			hour12: true
		});
	} else {
		// 올해가 아님: "x년 x월 x일 시:분 ap"
		return target.toLocaleString(locale, {
			year: 'numeric',
			month: 'numeric',
			day: 'numeric',
			hour: 'numeric',
			minute: '2-digit',
			hour12: true
		});
	}
}
