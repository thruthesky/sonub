/**
 * Svelte 클라이언트용 채팅 함수들
 *
 * 이 파일은 Firebase에 의존하는 함수들과
 * shared 폴더의 pure functions에 대한 re-export를 포함합니다.
 */

import { ref, set, update, type Database } from 'firebase/database';

// Pure functions를 shared 폴더에서 import하고 re-export
export {
	buildSingleRoomId,
	isSingleChat,
	extractUidsFromSingleRoomId,
	resolveRoomTypeLabel
} from '$shared/chat.pure-functions';

/**
 * 1:1 채팅방에 입장합니다.
 *
 * 이 함수는 사용자가 1:1 채팅방에 입장할 때 호출됩니다.
 * chat-joins/{uid}/{roomId}에 최소한의 정보만 저장하여 Cloud Functions를 트리거합니다.
 * Cloud Functions(onChatJoinCreate)가 자동으로 필요한 필드들을 추가합니다:
 * - singleChatListOrder
 * - allChatListOrder
 * - partnerUid
 * - roomType
 * - joinedAt
 *
 * 또한 사용자가 채팅방에 입장할 때마다 newMessageCount를 0으로 초기화하여
 * 모든 메시지를 읽은 것으로 표시합니다.
 *
 * @param db - Firebase Realtime Database 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 *
 * @example
 * ```typescript
 * import { rtdb } from '$lib/firebase';
 * import { enterSingleChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 1:1 채팅방에 입장할 때
 * enterSingleChatRoom(rtdb, roomId, currentUser.uid);
 * ```
 */
export function enterSingleChatRoom(
	db: Database,
	roomId: string,
	uid: string
): void {
	const chatJoinRef = ref(db, `chat-joins/${uid}/${roomId}`);
	update(chatJoinRef, {
		roomId: roomId,
		newMessageCount: 0
	}).catch((error) => {
		console.error('1:1 채팅방 입장 실패:', error);
	});
}

/**
 * 사용자를 그룹/오픈 채팅방에 입장시킵니다.
 *
 * 이 함수는 사용자가 그룹 채팅방 또는 오픈 채팅방에 입장할 때 호출됩니다.
 * chat-rooms/{roomId}/members/{uid}를 true로 설정하여 다음을 수행합니다:
 * 1. 사용자가 채팅방에 참여 중임을 표시
 * 2. 메시지 알림을 받도록 설정
 * 3. Cloud Functions가 자동으로 memberCount를 증가시키고 chat-joins에 상세 정보를 추가합니다.
 *
 * 또한 사용자가 채팅방에 입장할 때마다 newMessageCount를 0으로 초기화하여
 * 모든 메시지를 읽은 것으로 표시합니다.
 *
 * @param db - Firebase Realtime Database 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 *
 * @example
 * ```typescript
 * import { rtdb } from '$lib/firebase';
 * import { joinChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 그룹/오픈 채팅방에 입장할 때
 * joinChatRoom(rtdb, roomId, currentUser.uid);
 * ```
 */
export function joinChatRoom(
	db: Database,
	roomId: string,
	uid: string
): void {
	// 1. 채팅방 멤버로 등록
	const memberRef = ref(db, `chat-rooms/${roomId}/members/${uid}`);
	set(memberRef, true).catch((error) => {
		console.error('채팅방 멤버 등록 실패:', error);
	});

	// 2. newMessageCount를 0으로 초기화 (메시지를 모두 읽은 것으로 표시)
	const chatJoinRef = ref(db, `chat-joins/${uid}/${roomId}`);
	update(chatJoinRef, {
		newMessageCount: 0
	}).catch((error) => {
		console.error('newMessageCount 초기화 실패:', error);
	});
}

/**
 * 사용자를 채팅방에서 퇴장시킵니다.
 *
 * 이 함수는 사용자가 그룹 채팅방 또는 오픈 채팅방에서 나갈 때 호출됩니다.
 * chat-rooms/{roomId}/members/{uid} 속성을 삭제하여 다음을 수행합니다:
 * 1. 사용자가 채팅방에서 완전히 나갔음을 표시
 * 2. Cloud Functions가 자동으로 memberCount를 감소시킴
 *
 * @param db - Firebase Realtime Database 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * import { rtdb } from '$lib/firebase';
 * import { leaveChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 채팅방에서 나갈 때
 * await leaveChatRoom(rtdb, roomId, currentUser.uid);
 * ```
 */
export async function leaveChatRoom(
	db: Database,
	roomId: string,
	uid: string
): Promise<void> {
	const memberRef = ref(db, `chat-rooms/${roomId}/members/${uid}`);
	await set(memberRef, null); // null로 설정하여 속성 삭제
}
