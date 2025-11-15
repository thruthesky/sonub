/**
 * Firestore 타입 정의
 *
 * Firebase Realtime Database에서 Firestore로 마이그레이션하면서
 * 데이터 구조를 Firestore에 맞게 재정의합니다.
 */

import type { Timestamp, FieldValue } from 'firebase/firestore';

// ==================== 사용자 (Users) ====================

/**
 * 사용자 데이터 (Firestore 컬렉션: users)
 */
export interface UserData {
	/** 사용자 UID (문서 ID와 동일) */
	uid: string;
	/** 표시 이름 */
	displayName: string;
	/** 대소문자 구분 없는 검색용 이름 (Cloud Functions 자동 생성) */
	displayNameLowerCase?: string;
	/** 프로필 사진 URL */
	photoUrl?: string;
	/** 성별 */
	gender?: 'M' | 'F';
	/** 생년 */
	birthYear?: number;
	/** 생월 */
	birthMonth?: number;
	/** 생일 */
	birthDay?: number;
	/** 생년월일 (YYYY-MM-DD) */
	birthYearMonthDay?: string;
	/** 생월일 (MM-DD) */
	birthMonthDay?: string;
	/** 자기소개 */
	bio?: string;
	/** 계정 생성 시각 */
	createdAt: Timestamp;
	/** 마지막 업데이트 시각 */
	updatedAt?: Timestamp;
	/** 정렬 필드들 (Cloud Functions 자동 생성) */
	sort_recentWithPhoto?: Timestamp;
	sort_recentFemaleWithPhoto?: Timestamp;
	sort_recentMaleWithPhoto?: Timestamp;
}

// ==================== 채팅방 (Chat Rooms) ====================

/**
 * 채팅방 타입
 */
export type ChatRoomType = 'single' | 'group' | 'open';

/**
 * 채팅방 데이터 (Firestore 컬렉션: chats)
 */
export interface ChatRoomData {
	/** 채팅방 ID (문서 ID와 동일) */
	roomId: string;
	/** 채팅방 타입 */
	type: ChatRoomType;
	/** 채팅방 소유자 UID */
	owner: string;
	/** 채팅방 이름 (그룹/오픈 채팅만) */
	name?: string;
	/** 채팅방 설명 (그룹/오픈 채팅만) */
	description?: string;
	/** 비밀번호 설정 여부 */
	password?: boolean;
	/** 생성 시각 */
	createdAt: Timestamp;
	/** 멤버 수 (Cloud Functions 자동 관리) */
	memberCount: number;
	/** 멤버 맵 { uid: boolean } */
	members: Record<string, boolean>;
	/** 마지막 메시지 시각 (새로 추가) */
	lastMessageAt?: Timestamp;
	/** 마지막 메시지 텍스트 (새로 추가) */
	lastMessageText?: string;
	/** 마지막 메시지 발신자 UID (새로 추가) */
	lastMessageSenderUid?: string;
}

// ==================== 채팅 메시지 (Chat Messages) ====================

/**
 * 채팅 메시지 타입
 */
export type ChatMessageType = 'text' | 'image' | 'file' | 'message';

/**
 * 채팅 메시지 데이터 (Firestore 서브컬렉션: chats/{roomId}/messages)
 */
export interface ChatMessageData {
	/** 메시지 ID (문서 ID와 동일) */
	messageId: string;
	/** 발신자 UID */
	senderUid: string;
	/** 메시지 텍스트 */
	text: string;
	/** 메시지 타입 */
	type?: ChatMessageType;
	/** 생성 시각 */
	createdAt: Timestamp;
	/** 첨부 파일 URL 배열 (RTDB의 Record<number, string>에서 변경) */
	urls?: string[];
	/** 수정 시각 */
	updatedAt?: Timestamp;
	/** 삭제 시각 */
	deletedAt?: Timestamp;
}

// ==================== 채팅 참여 정보 (Chat Joins) ====================

/**
 * 채팅 참여 정보 (Firestore 서브컬렉션: users/{uid}/chat-joins/{roomId})
 */
export interface ChatJoinData {
	/** 채팅방 ID */
	roomId: string;
	/** 채팅방 타입 */
	roomType: ChatRoomType;
	/** 상대방 UID (1:1 채팅만) */
	partnerUid?: string;
	/** 참여 시각 */
	joinedAt: Timestamp;
	/** 마지막 읽은 시각 (새로 추가) */
	lastReadAt: Timestamp;
	/** 읽지 않은 메시지 수 */
	newMessageCount: number;
	/** 핀 설정 여부 */
	pin: boolean;
	/** 채팅방 이름 캐시 */
	roomName?: string;
	/** 마지막 메시지 텍스트 캐시 */
	lastMessageText?: string;
	/** 마지막 메시지 시각 캐시 */
	lastMessageAt?: Timestamp;
	/** 정렬 필드 (xxxListOrder를 대체, 새로 추가) */
	/** 읽지 않은 메시지가 있으면 200 + timestamp, 핀이면 500 + timestamp */
	singleChatListOrder?: string;
	groupChatListOrder?: string;
	openChatListOrder?: string;
	allChatListOrder?: string;
}

// ==================== 채팅 초대장 (Chat Invitations) ====================

/**
 * 초대장 상태
 */
export type InvitationStatus = 'pending' | 'accepted' | 'rejected';

/**
 * 채팅 초대장 데이터 (Firestore 서브컬렉션: users/{uid}/chat-invitations/{roomId})
 */
export interface ChatInvitationData {
	/** 채팅방 ID */
	roomId: string;
	/** 초대한 사용자 UID */
	inviterUid: string;
	/** 생성 시각 */
	createdAt: Timestamp;
	/** 초대 메시지 (선택) */
	message?: string;
	/** 초대 상태 (새로 추가) */
	status: InvitationStatus;
	/** 채팅방 이름 캐시 */
	roomName?: string;
	/** 채팅방 타입 캐시 */
	roomType?: ChatRoomType;
	/** 초대자 이름 캐시 */
	inviterName?: string;
}

// ==================== 채팅방 비밀번호 (Chat Room Passwords) ====================

/**
 * 채팅방 비밀번호 데이터 (Firestore 컬렉션: chatRoomPasswords)
 */
export interface ChatRoomPasswordData {
	/** 채팅방 ID (문서 ID와 동일) */
	roomId: string;
	/** 비밀번호 (Plain text) */
	password: string;
}

/**
 * 비밀번호 시도 데이터 (Firestore 서브컬렉션: chatRoomPasswords/{roomId}/tries/{uid})
 */
export interface PasswordTryData {
	/** 사용자 UID (문서 ID와 동일) */
	uid: string;
	/** 입력한 비밀번호 */
	inputPassword: string;
	/** 시도 시각 */
	triedAt: Timestamp;
}

// ==================== 채팅 즐겨찾기 (Chat Favorites) ====================

/**
 * 채팅 즐겨찾기 폴더 데이터 (Firestore 서브컬렉션: users/{uid}/chat-favorites/{favoriteId})
 */
export interface ChatFavoriteData {
	/** 즐겨찾기 ID (문서 ID와 동일) */
	favoriteId: string;
	/** 폴더 이름 */
	name: string;
	/** 폴더 설명 */
	description?: string;
	/** 생성 시각 */
	createdAt: Timestamp;
	/** 정렬 순서 (500 + timestamp = 상단 고정) */
	folderOrder: string;
	/** 폴더에 포함된 채팅방 목록 { roomId: boolean } */
	roomList: Record<string, boolean>;
}

// ==================== 파일 업로드 ====================

/**
 * 파일 업로드 상태
 *
 * 각 파일의 업로드 진행 상태를 추적하기 위한 인터페이스입니다.
 * (기존 chat.types.ts에서 가져옴, 변경 없음)
 */
export interface FileUploadStatus {
	/** 로컬 파일 객체 */
	file: File;
	/** 미리보기 URL (이미지/동영상) */
	previewUrl?: string;
	/** 업로드 진행률 (0-100) */
	progress: number;
	/** 업로드 완료 여부 */
	completed: boolean;
	/** 에러 메시지 */
	error?: string;
	/** Firebase Storage 업로드 URL (완료 시) */
	downloadUrl?: string;
}

// ==================== Firestore 스토어 상태 ====================

/**
 * Firestore 스토어 상태 타입
 * @template T - 데이터 타입
 */
export interface FirestoreStoreState<T> {
	/** Firestore에서 가져온 데이터 */
	data: T | null;
	/** 데이터 로딩 중 여부 */
	loading: boolean;
	/** 에러 발생 시 에러 객체 */
	error: Error | null;
}

/**
 * 데이터 작업 결과 타입
 */
export interface DataOperationResult {
	/** 작업 성공 여부 */
	success: boolean;
	/** 에러 메시지 (실패 시) */
	error?: string;
}

/**
 * 문서 추가 결과 타입 (생성된 ID 포함)
 */
export interface AddDocumentResult extends DataOperationResult {
	/** 생성된 문서 ID */
	id?: string;
}

/**
 * 데이터 읽기 결과 타입
 * @template T - 데이터 타입
 */
export interface ReadDocumentResult<T> extends DataOperationResult {
	/** 읽어온 데이터 */
	data?: T | null;
}
