---
title: chat.types.ts
type: typescript
path: src/lib/types/chat.types.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/types/chat.types.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * 채팅 관련 타입 정의
 *
 * 이 파일은 채팅 메시지 및 파일 업로드와 관련된 TypeScript 타입을 정의합니다.
 */

/**
 * 채팅 메시지 데이터 (urls 필드 포함)
 *
 * @property roomId - 채팅방 ID
 * @property type - 메시지 타입
 * @property text - 메시지 텍스트 내용
 * @property senderUid - 발신자 UID
 * @property createdAt - 메시지 생성 시각 (밀리초)
 * @property roomOrder - 채팅방 내 정렬 순서
 * @property rootOrder - 전체 채팅 정렬 순서
 * @property editedAt - 메시지 수정 시각 (밀리초, null이면 수정되지 않음)
 * @property deletedAt - 메시지 삭제 시각 (밀리초, null이면 삭제되지 않음)
 * @property urls - 첨부파일 URL 목록 (숫자 인덱스를 키로 사용, 값은 URL 문자열)
 */
export interface ChatMessage {
	roomId: string;
	type: 'text' | 'image' | 'file' | 'message';
	text?: string;
	senderUid: string;
	createdAt: number;
	roomOrder: string;
	rootOrder: string;
	editedAt?: number | null;
	deletedAt?: number | null;
	/** 첨부파일 URL 목록 (숫자 인덱스를 키로 사용, 값은 URL 문자열) */
	urls?: Record<number, string>;
}

/**
 * 파일 업로드 상태
 *
 * 각 파일의 업로드 진행 상태를 추적하기 위한 인터페이스입니다.
 *
 * @property file - 로컬 파일 객체
 * @property previewUrl - 미리보기 URL (이미지/동영상)
 * @property progress - 업로드 진행률 (0-100)
 * @property completed - 업로드 완료 여부
 * @property error - 에러 메시지
 * @property downloadUrl - Firebase Storage 업로드 URL (완료 시)
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

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
