/**
 * 채팅 관련 순수 함수 모음
 */

/**
 * 1:1 채팅방의 roomId를 UID 두 개로부터 고정적으로 생성한다.
 */
export function buildSingleRoomId(a: string, b: string) {
	return `single-${[a, b].sort().join('-')}`;
}

/**
 * roomId가 1:1 채팅방인지 확인한다.
 *
 * @param roomId - 확인할 채팅방 ID
 * @returns 1:1 채팅방이면 true, 아니면 false
 */
export function isSingleChat(roomId: string): boolean {
	return roomId.startsWith('single-');
}

/**
 * 1:1 채팅방 roomId에서 두 사용자의 UID를 추출한다.
 *
 * @param roomId - 1:1 채팅방 ID (형식: "single-uid1-uid2")
 * @returns 두 UID를 포함하는 배열 [uid1, uid2], 형식이 올바르지 않으면 null
 */
export function extractUidsFromSingleRoomId(roomId: string): [string, string] | null {
	const parts = roomId.split('-');
	if (parts.length !== 3 || parts[0] !== 'single') {
		return null;
	}
	return [parts[1], parts[2]];
}

/**
 * 채팅방 유형 문자열을 배지 텍스트로 변환한다.
 *
 * @param roomType - DB에 저장된 채팅방 유형 문자열
 * @returns UI에 표시할 짧은 배지 텍스트
 */
export function resolveRoomTypeLabel(roomType: string): string {
	const normalized = roomType?.toLowerCase() ?? '';
	if (normalized.includes('open')) return 'Open';
	if (normalized.includes('group')) return 'Group';
	if (normalized.includes('single')) return 'Single';
	return 'Room';
}
