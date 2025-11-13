---
name: chat.pure-functions.ts
description: 채팅 관련 순수 함수 모음 (외부 의존성 없음)
version: 1.0.0
type: pure-function
category: shared
tags: [pure-function, chat, roomId, utility]
---

# chat.pure-functions.ts

## 개요
이 파일은 채팅 기능과 관련된 완전한 순수 함수(Pure Functions)만 포함합니다. Firebase, Svelte, Paraglide 등 어떠한 외부 의존성도 없으며, 입력값에 대해 항상 동일한 출력을 보장합니다. 클라이언트와 서버(Cloud Functions)에서 공유하여 사용합니다.

## 소스 코드

```typescript
/**
 * 채팅 관련 순수 함수 모음
 *
 * 이 파일은 완전한 pure functions만 포함합니다.
 * Firebase, Svelte, Paraglide 등 외부 의존성이 없습니다.
 */

/**
 * 1:1 채팅방의 roomId를 UID 두 개로부터 고정적으로 생성한다.
 *
 * @param a - 첫 번째 사용자 UID
 * @param b - 두 번째 사용자 UID
 * @returns 1:1 채팅방 ID (형식: "single-uid1-uid2", 알파벳 순 정렬)
 */
export function buildSingleRoomId(a: string, b: string): string {
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
```

## 주요 기능

### 1. buildSingleRoomId(a, b)
- **목적**: 두 사용자 UID로부터 1:1 채팅방 ID 생성
- **로직**:
  - UID를 알파벳 순으로 정렬
  - `single-` 접두사 추가
  - 형식: `single-uid1-uid2`
- **예시**:
  - `buildSingleRoomId('user-B', 'user-A')` → `'single-user-A-user-B'`
  - 순서가 바뀌어도 동일한 roomId 생성 보장

### 2. isSingleChat(roomId)
- **목적**: roomId가 1:1 채팅방인지 확인
- **로직**: `single-` 접두사 검사
- **예시**:
  - `isSingleChat('single-user-A-user-B')` → `true`
  - `isSingleChat('group-room-123')` → `false`

### 3. extractUidsFromSingleRoomId(roomId)
- **목적**: 1:1 채팅방 ID에서 두 사용자 UID 추출
- **로직**:
  - roomId를 `-`로 분할
  - 형식 검증 (3개 부분, 첫 번째는 'single')
  - 두 UID 반환
- **예시**:
  - `extractUidsFromSingleRoomId('single-user-A-user-B')` → `['user-A', 'user-B']`
  - `extractUidsFromSingleRoomId('invalid')` → `null`

### 4. resolveRoomTypeLabel(roomType)
- **목적**: DB의 채팅방 유형을 UI 배지 텍스트로 변환
- **로직**: 대소문자 무시하고 키워드 매칭
- **예시**:
  - `resolveRoomTypeLabel('openChat')` → `'Open'`
  - `resolveRoomTypeLabel('GROUP_CHAT')` → `'Group'`
  - `resolveRoomTypeLabel('single')` → `'Single'`
  - `resolveRoomTypeLabel('unknown')` → `'Room'`

## Pure Function 특징

### 순수성 보장
- **부수 효과 없음**: 외부 상태를 변경하지 않음
- **참조 투명성**: 동일한 입력 → 동일한 출력
- **테스트 용이성**: 단위 테스트 작성이 간단함

### 공유 가능
- **클라이언트**: Svelte 컴포넌트에서 import
- **서버**: Firebase Cloud Functions에서 import
- **일관성**: 클라이언트/서버 로직 동기화

## 사용 예시

```typescript
// 클라이언트 (Svelte)
import { buildSingleRoomId } from '$shared/chat.pure-functions';
const roomId = buildSingleRoomId(currentUid, partnerUid);

// 서버 (Cloud Functions)
import { isSingleChat } from '../../../shared/chat.pure-functions';
if (isSingleChat(roomId)) {
  // 1:1 채팅 처리
}
```
