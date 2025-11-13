---
title: "firebase/functions/src/types/index.ts"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/functions/src/types/index.ts"
spec_type: "repository-source"
---

## 개요

이 파일은 index.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * Firebase Cloud Functions TypeScript 타입 정의
 * SNS 프로젝트의 모든 타입을 통합 관리합니다.
 */

/**
 * 게시판 카테고리 상수
 * - community: 커뮤니티
 * - qna: 질문과 답변
 * - news: 뉴스
 * - market: 회원장터
 */
export const FORUM_CATEGORIES = ["community", "qna", "news", "market"] as const;

/**
 * 게시판 카테고리 타입
 */
export type ForumCategory = (typeof FORUM_CATEGORIES)[number];

/**
 * 게시글 데이터 인터페이스
 */
export interface PostData {
  /** 작성자 UID */
  uid?: string;
  /** 작성자 displayName */
  author?: string;
  /** 게시글 제목 */
  title?: string;
  /** 게시판 카테고리 */
  category?: ForumCategory | string;
  /** 정렬용 문자열 (Flat style: "<category>-<timestamp>") */
  order?: string;
  /** 생성 시간 (Unix timestamp, 밀리초) */
  createdAt?: number;
  /** 수정 시간 (Unix timestamp, 밀리초) */
  updatedAt?: number;
  /** 좋아요 개수 (Cloud Functions에서 자동 관리) */
  likeCount?: number;
  /** 댓글 개수 (Cloud Functions에서 자동 관리) */
  commentCount?: number;
  /** 신고 개수 (Cloud Functions에서 자동 관리) */
  reportCount?: number;
}

/**
 * 댓글 데이터 인터페이스
 */
export interface CommentData {
  /** 소속 게시글 ID (Flat style) */
  postId?: string;
  /** 작성자 UID */
  uid?: string;
  /** 부모 댓글 ID (첫 레벨은 null) */
  parentId?: string | null;
  /** 댓글 깊이 (1~12) */
  depth?: number;
  /** 정렬용 문자열 (postId 접두사 포함) */
  order?: string;
  /** 생성 시간 (Unix timestamp, 밀리초) */
  createdAt?: number;
  /** 수정 시간 (Unix timestamp, 밀리초) */
  updatedAt?: number;
  /** 좋아요 개수 (Cloud Functions에서 자동 관리) */
  likeCount?: number;
  /** 자식 댓글(대댓글) 개수 (Cloud Functions에서 자동 관리) */
  commentCount?: number;
  /** 신고 개수 (Cloud Functions에서 자동 관리) */
  reportCount?: number;
}

/**
 * 사용자 데이터 인터페이스
 */
export interface UserData {
  /** 사용자 닉네임 */
  displayName?: string;
  /** 사용자 닉네임 소문자 (대소문자 구분 없는 검색용, Cloud Functions에서 자동 생성) */
  displayNameLowerCase?: string;
  /** 프로필 사진 URL (사용자 업로드) */
  photoUrl?: string;
  /** Firebase Auth photoURL (대문자, deprecated) */
  photoURL?: string;
  /** 계정 생성 시간 (Unix timestamp, 밀리초) */
  createdAt?: number;
  /** 프로필 수정 시간 (Unix timestamp, 밀리초) */
  updatedAt?: number;
  /** 성별 (M|F) */
  gender?: string;
  /** 생년월일 (YYYY-MM-DD 형식, 클라이언트에서 저장) */
  birthYearMonthDay?: string;
  /** 생년 (Cloud Functions에서 자동 생성) */
  birthYear?: number;
  /** 생월 (Cloud Functions에서 자동 생성) */
  birthMonth?: number;
  /** 생일 (Cloud Functions에서 자동 생성) */
  birthDay?: number;
  /** 생월일 (MM-DD 형식, Cloud Functions에서 자동 생성) */
  birthMonthDay?: string;
}

/**
 * likeId 파싱 결과 인터페이스
 * 형식: "post-<post-id>-<uid>" 또는 "comment-<comment-id>-<uid>"
 */
export interface ParsedLikeId {
  /** 좋아요 타입 (post | comment) */
  type: "post" | "comment";
  /** 노드 ID (postId 또는 commentId) */
  nodeId: string;
  /** 사용자 UID */
  uid: string;
}

/**
 * 신고 사유 타입
 * - abuse: 욕설, 시비, 모욕, 명예훼손
 * - fake-news: 가짜 뉴스, 잘못된 정보
 * - spam: 스팸, 악용
 * - inappropriate: 카테고리에 맞지 않는 글 등록
 * - other: 기타
 */
export type ReportReason = "abuse" | "fake-news" | "spam" | "inappropriate" | "other";

/**
 * 신고 데이터 인터페이스
 * Firebase Realtime Database의 /reports/ 노드에 저장되는 데이터 구조
 */
export interface ReportData {
  /** 신고 대상 타입 (post | comment) */
  type?: "post" | "comment";
  /** 게시글 ID 또는 댓글 ID */
  nodeId?: string;
  /** 신고자 사용자 UID */
  uid?: string;
  /** 신고 사유 */
  reason?: ReportReason;
  /** 상세 설명 (선택 사항) */
  message?: string;
  /** 신고 생성 시간 (Unix timestamp, 밀리초) */
  createdAt?: number;
}

/**
 * reportId 파싱 결과 인터페이스
 * 형식: "post-<post-id>-<uid>" 또는 "comment-<comment-id>-<uid>"
 */
export interface ParsedReportId {
  /** 신고 타입 (post | comment) */
  type: "post" | "comment";
  /** 노드 ID (postId 또는 commentId) */
  nodeId: string;
  /** 사용자 UID */
  uid: string;
}

/**
 * 채팅 메시지 타입
 * - message: 일반 채팅 메시지
 * - post: 게시글 형식 메시지
 */
export type ChatMessageType = "message" | "post";

/**
 * 채팅 메시지 데이터 인터페이스
 * Firebase Realtime Database의 /chat-messages/ 노드에 저장되는 데이터 구조
 */
export interface ChatMessage {
  /** 채팅방 ID (예: "single-uid1-uid2" 또는 그룹/오픈 채팅방 ID) */
  roomId?: string;
  /** 메시지 타입 (기본값: "message") */
  type?: ChatMessageType;
  /** 메시지 내용 */
  text?: string;
  /** 첨부 URL 배열 */
  urls?: string[];
  /** 발신자 UID */
  senderUid?: string;
  /** 생성 시간 (Unix timestamp, 밀리초) */
  createdAt?: number;
  /** 수정 시간 (Unix timestamp, 밀리초) */
  editedAt?: number | null;
  /** 삭제 시간 (Unix timestamp, 밀리초) */
  deletedAt?: number | null;
  /** 프로토콜 메시지 (join/leave 등 시스템 메시지) */
  protocol?: string;

  // Order 필드들 (Cloud Functions에서 자동 생성)
  /** 채팅방별 메시지 정렬 키: "-{roomId}-{timestamp}" */
  roomOrder?: string;
  /** 루트 정렬 키: "-{parentRoomId}-{timestamp}" (서브 채팅방용) */
  rootOrder?: string;
  /** 오픈 채팅 전역 정렬 키: timestamp (오픈 채팅방만) */
  openOrder?: number;

  // type: "post"인 경우 추가 필드
  /** 게시판 카테고리 (type: "post"인 경우) */
  category?: string;
  /** 게시글 제목 (type: "post"인 경우) */
  title?: string;
  /** 카테고리별 정렬 키: "{categoryId}-{timestamp}" (type: "post"인 경우) */
  categoryOrder?: string;
}

/**
 * 채팅방 타입
 * - single: 1:1 채팅
 * - group: 그룹 채팅 (초대된 사용자만)
 * - open: 오픈 채팅 (누구나 입장 가능)
 */
export type ChatRoomType = "single" | "group" | "open";

/**
 * 채팅방 참여 정보 인터페이스
 * Firebase Realtime Database의 /chat-joins/{uid}/{roomId} 노드에 저장되는 데이터 구조
 */
export interface ChatJoin {
  /** 채팅방 ID */
  roomId?: string;
  /** 채팅방 타입 */
  roomType?: ChatRoomType;
  /** 채팅방 이름 */
  roomName?: string;
  /** 1:1 채팅의 상대방 UID */
  partnerUid?: string;
  /** 마지막 메시지 내용 */
  lastMessageText?: string;
  /** 마지막 메시지 시간 (Unix timestamp, 밀리초) */
  lastMessageAt?: number;
  /** 업데이트 시간 (Unix timestamp, 밀리초) */
  updatedAt?: number;
  /** 채팅방 참여 시간 (Unix timestamp, 밀리초) */
  joinedAt?: number;
  /** 읽지 않은 메시지 수 */
  unreadCount?: number;
  /** 읽지 않은 메시지 수 (newMessageCount 권장) */
  newMessageCount?: number;
  /** 1:1 채팅 정렬용 필드: prefix + timestamp */
  singleChatListOrder?: string;
  /** 그룹 채팅 정렬용 필드: prefix + timestamp */
  groupChatListOrder?: string | number;
  /** 오픈 채팅 정렬용 필드: prefix + timestamp */
  openChatListOrder?: string | number;
  /** 그룹+오픈 채팅 통합 정렬용 필드: timestamp */
  openAndGroupChatListOrder?: number;
  /** 모든 채팅방(1:1, 그룹, 오픈) 통합 정렬용 필드: timestamp */
  allChatListOrder?: number;
}

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
