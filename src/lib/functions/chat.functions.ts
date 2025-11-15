/**
 * Svelte 클라이언트용 채팅 함수들
 *
 * 이 파일은 Firebase에 의존하는 함수들과
 * shared 폴더의 pure functions에 대한 re-export를 포함합니다.
 */

import {
	doc,
	setDoc,
	updateDoc,
	getDoc,
	deleteDoc,
	deleteField,
	type Firestore
} from 'firebase/firestore';

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
 * users/{uid}/chat-joins/{roomId}에 최소한의 정보를 저장하여 채팅방 참여를 기록합니다.
 * - 문서가 없으면 새로 생성 (처음 입장 시)
 * - 문서가 있으면 업데이트 (재입장 시)
 *
 * 저장되는 필드:
 * - roomId: 채팅방 ID
 * - roomType: 'single'
 * - partnerUid: 상대방 UID (roomId에서 추출)
 * - newMessageCount: 0 (읽음 처리)
 *
 * Cloud Functions(onChatMessageCreate)는 메시지가 생성될 때 다음 필드들을 자동으로 추가/업데이트합니다:
 * - singleChatListOrder
 * - allChatListOrder
 * - lastMessageText
 * - lastMessageAt
 * - updatedAt
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID (형식: "single-{uid1}-{uid2}")
 * @param uid - 사용자 UID
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { enterSingleChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 1:1 채팅방에 입장할 때
 * enterSingleChatRoom(db, roomId, currentUser.uid);
 * ```
 */
export function enterSingleChatRoom(
	db: Firestore,
	roomId: string,
	uid: string
): void {
	// roomId에서 상대방 UID 추출
	// roomId 형식: "single-{uid1}-{uid2}"
	const [, uid1, uid2] = roomId.split('-');
	const partnerUid = uid1 === uid ? uid2 : uid1;

	const chatJoinRef = doc(db, `users/${uid}/chat-joins/${roomId}`);

	// setDoc with merge: true를 사용하여 문서가 없으면 생성, 있으면 업데이트
	setDoc(
		chatJoinRef,
		{
			roomId: roomId,
			roomType: 'single',
			partnerUid: partnerUid,
			newMessageCount: 0
		},
		{ merge: true }
	).catch((error) => {
		console.error('1:1 채팅방 입장 실패:', error);
	});
}

/**
 * 사용자를 그룹/오픈 채팅방에 입장시킵니다.
 *
 * 이 함수는 사용자가 그룹 채팅방 또는 오픈 채팅방에 입장할 때 호출됩니다.
 * chats/{roomId}/members/{uid}를 true로 설정하여 다음을 수행합니다:
 * 1. 사용자가 채팅방에 참여 중임을 표시
 * 2. 메시지 알림을 받도록 설정
 * 3. Cloud Functions가 자동으로 memberCount를 증가시키고 chat-joins에 상세 정보를 추가합니다.
 *
 * 또한 사용자가 채팅방에 입장할 때마다 newMessageCount를 0으로 초기화하여
 * 모든 메시지를 읽은 것으로 표시합니다.
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { joinChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 그룹/오픈 채팅방에 입장할 때
 * joinChatRoom(db, roomId, currentUser.uid);
 * ```
 */
export function joinChatRoom(
	db: Firestore,
	roomId: string,
	uid: string
): void {
	// 1. 채팅방 멤버로 등록
	// merge: true를 사용하여 기존 알림 설정 보존
	// - 문서가 없으면: value: true로 생성 (최초 입장, 알림 구독)
	// - 문서가 있으면: 기존 value 유지 (사용자의 알림 설정 보존)
	const memberRef = doc(db, `chats/${roomId}/members/${uid}`);
	setDoc(memberRef, { value: true }, { merge: true })
		.catch((error) => {
			console.error('채팅방 멤버 등록 실패:', error);
		});

	// 2. newMessageCount를 0으로 초기화 (메시지를 모두 읽은 것으로 표시)
	// setDoc with merge: true를 사용하여 문서가 없으면 생성, 있으면 업데이트
	const chatJoinRef = doc(db, `users/${uid}/chat-joins/${roomId}`);
	setDoc(
		chatJoinRef,
		{
			newMessageCount: 0
		},
		{ merge: true }
	).catch((error) => {
		console.error('newMessageCount 초기화 실패:', error);
	});
}

/**
 * 사용자를 채팅방에서 퇴장시킵니다.
 *
 * 이 함수는 사용자가 그룹 채팅방 또는 오픈 채팅방에서 나갈 때 호출됩니다.
 * chats/{roomId}/members/{uid} 문서를 삭제하여 다음을 수행합니다:
 * 1. 사용자가 채팅방에서 완전히 나갔음을 표시
 * 2. Cloud Functions가 자동으로 memberCount를 감소시킴
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { leaveChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 채팅방에서 나갈 때
 * await leaveChatRoom(db, roomId, currentUser.uid);
 * ```
 */
export async function leaveChatRoom(
	db: Firestore,
	roomId: string,
	uid: string
): Promise<void> {
	const memberRef = doc(db, `chats/${roomId}/members/${uid}`);
	await deleteDoc(memberRef); // 문서 삭제
}

/**
 * 채팅방 핀 상태를 토글합니다 (고정/해제)
 *
 * 이 함수는 사용자가 채팅방을 핀하거나 핀 해제할 때 호출됩니다.
 * 클라이언트는 단순히 pin 필드를 true/false로 설정하며,
 * Cloud Functions가 자동으로 모든 xxxListOrder 필드의 prefix를 업데이트합니다.
 *
 * Prefix 규칙 (Cloud Functions에서 자동 처리):
 * - 500 prefix: 핀된 채팅방 (최상위)
 * - 200 prefix: 읽지 않은 메시지가 있는 채팅방 (상위)
 * - prefix 없음: 읽은 메시지만 있는 채팅방 (일반)
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @param roomType - 채팅방 타입 (더 이상 사용하지 않지만 호환성 유지)
 * @returns Promise<boolean> - 변경 후 핀 상태 (true: 핀됨, false: 핀 해제됨)
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { togglePinChatRoom } from '$lib/functions/chat.functions';
 *
 * // 채팅방 핀 토글
 * const isPinned = await togglePinChatRoom(db, roomId, currentUser.uid, 'single');
 * console.log(isPinned ? '핀 설정됨' : '핀 해제됨');
 * ```
 */
export async function togglePinChatRoom(
	db: Firestore,
	roomId: string,
	uid: string,
	roomType: string // eslint-disable-line @typescript-eslint/no-unused-vars
): Promise<boolean> {
	const chatJoinRef = doc(db, `users/${uid}/chat-joins/${roomId}`);

	// 현재 핀 상태 읽기
	const snapshot = await getDoc(chatJoinRef);

	// 문서가 없으면 생성 후 핀 설정
	if (!snapshot.exists()) {
		await setDoc(
			chatJoinRef,
			{
				roomId,
				roomType: 'single', // 기본값
				pin: true
			},
			{ merge: true }
		);
		// console.log('✅ 채팅방 문서 생성 및 핀 설정 완료:', roomId);
		return true; // 핀 설정됨
	}

	const data = snapshot.data();
	const currentPinValue = data?.pin ?? false;
	const isPinned = currentPinValue === true;

	// 핀 상태 토글
	if (isPinned) {
		// 핀 해제: pin 필드 삭제 (Firestore에서는 deleteField 사용)
		await updateDoc(chatJoinRef, {
			pin: deleteField()
		});
		// console.log('✅ 채팅방 핀 해제 완료:', roomId);
	} else {
		// 핀 설정: pin: true 설정
		await updateDoc(chatJoinRef, {
			pin: true
		});
		// console.log('✅ 채팅방 핀 설정 완료:', roomId);
	}

	// 새로운 핀 상태 반환
	return !isPinned;
}

/**
 * 사용자를 그룹/오픈 채팅방에 초대합니다.
 *
 * 이 함수는 채팅방 멤버가 다른 사용자를 초대할 때 호출됩니다.
 * 클라이언트는 최소한의 정보만 저장하여 Cloud Functions를 트리거합니다.
 * Cloud Functions(onChatInvitationCreate)가 자동으로 다음 필드들을 추가합니다:
 * - createdAt: 초대 생성 시간 (타임스탬프)
 * - invitationOrder: 정렬용 필드 (-createdAt 값)
 * - roomName: 채팅방 이름
 * - roomType: 채팅방 타입 (group | open)
 * - inviterName: 초대한 사람 이름
 * - message: 다국어 초대 메시지
 *
 * 또한 Cloud Functions는 다음을 자동으로 처리합니다:
 * - 초대받은 사용자의 언어 코드에 맞는 FCM 푸시 알림 전송
 * - 이미 채팅방에 참여 중인 사용자는 초대하지 않음
 * - 1:1 채팅방은 초대 불가
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID
 * @param inviteeUid - 초대받는 사용자 UID
 * @param inviterUid - 초대하는 사용자 UID
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { inviteUserToChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자를 채팅방에 초대
 * await inviteUserToChatRoom(db, roomId, inviteeUid, currentUser.uid);
 * ```
 */
export async function inviteUserToChatRoom(
	db: Firestore,
	roomId: string,
	inviteeUid: string,
	inviterUid: string
): Promise<void> {
	const invitationRef = doc(db, `users/${inviteeUid}/chat-invitations/${roomId}`);

	// 클라이언트는 최소한의 데이터만 저장 (roomId, inviterUid)
	// Cloud Functions가 나머지 필드들을 자동으로 추가합니다
	await setDoc(invitationRef, {
		roomId,
		inviterUid
		// Cloud Functions가 자동으로 추가하는 필드들:
		// - createdAt: 초대 생성 시간
		// - invitationOrder: 정렬용 필드
		// - roomName: 채팅방 이름
		// - roomType: 채팅방 타입
		// - inviterName: 초대한 사람 이름
		// - message: 다국어 초대 메시지
	});
}

/**
 * 채팅 초대를 수락합니다.
 *
 * 이 함수는 사용자가 초대를 수락할 때 호출됩니다.
 * 다음 작업을 순차적으로 수행합니다:
 * 1. joinChatRoom() 함수를 호출하여 채팅방에 입장
 * 2. users/{uid}/chat-invitations/{roomId}에서 초대 정보 삭제
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { acceptInvitation } from '$lib/functions/chat.functions';
 *
 * // 초대 수락
 * await acceptInvitation(db, roomId, currentUser.uid);
 * ```
 */
export async function acceptInvitation(
	db: Firestore,
	roomId: string,
	uid: string
): Promise<void> {
	// 1. 채팅방에 입장 (기존 함수 재사용)
	joinChatRoom(db, roomId, uid);

	// 2. 초대 정보 삭제
	const invitationRef = doc(db, `users/${uid}/chat-invitations/${roomId}`);
	await deleteDoc(invitationRef);
}

/**
 * 채팅 초대를 거절합니다.
 *
 * 이 함수는 사용자가 초대를 거절할 때 호출됩니다.
 * users/{uid}/chat-invitations/{roomId}에서 초대 정보만 삭제하며,
 * 채팅방에는 입장하지 않습니다.
 *
 * @param db - Firebase Firestore 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * import { db } from '$lib/firebase';
 * import { rejectInvitation } from '$lib/functions/chat.functions';
 *
 * // 초대 거절
 * await rejectInvitation(db, roomId, currentUser.uid);
 * ```
 */
export async function rejectInvitation(
	db: Firestore,
	roomId: string,
	uid: string
): Promise<void> {
	const invitationRef = doc(db, `users/${uid}/chat-invitations/${roomId}`);
	await deleteDoc(invitationRef);
}
