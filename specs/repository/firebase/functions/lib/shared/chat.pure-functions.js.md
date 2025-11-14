---
name: chat.pure-functions.js
description: chat.pure-functions Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/shared/chat.pure-functions.js
---

# chat.pure-functions.js

## 개요

**파일 경로**: `firebase/functions/lib/shared/chat.pure-functions.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

chat.pure-functions Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * 채팅 관련 순수 함수 모음
 *
 * 이 파일은 완전한 pure functions만 포함합니다.
 * Firebase, Svelte, Paraglide 등 외부 의존성이 없습니다.
 */
Object.defineProperty(exports, "__esModule", { value: true });
exports.buildSingleRoomId = buildSingleRoomId;
exports.isSingleChat = isSingleChat;
exports.extractUidsFromSingleRoomId = extractUidsFromSingleRoomId;
exports.resolveRoomTypeLabel = resolveRoomTypeLabel;
/**
 * 1:1 채팅방의 roomId를 UID 두 개로부터 고정적으로 생성한다.
 *
 * @param a - 첫 번째 사용자 UID
 * @param b - 두 번째 사용자 UID
 * @returns 1:1 채팅방 ID (형식: "single-uid1-uid2", 알파벳 순 정렬)
 */
function buildSingleRoomId(a, b) {
    return `single-${[a, b].sort().join('-')}`;
}
/**
 * roomId가 1:1 채팅방인지 확인한다.
 *
 * @param roomId - 확인할 채팅방 ID
 * @returns 1:1 채팅방이면 true, 아니면 false
 */
function isSingleChat(roomId) {
    return roomId.startsWith('single-');
}
/**
 * 1:1 채팅방 roomId에서 두 사용자의 UID를 추출한다.
 *
 * @param roomId - 1:1 채팅방 ID (형식: "single-uid1-uid2")
 * @returns 두 UID를 포함하는 배열 [uid1, uid2], 형식이 올바르지 않으면 null
 */
function extractUidsFromSingleRoomId(roomId) {
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
function resolveRoomTypeLabel(roomType) {
    var _a;
    const normalized = (_a = roomType === null || roomType === void 0 ? void 0 : roomType.toLowerCase()) !== null && _a !== void 0 ? _a : '';
    if (normalized.includes('open'))
        return 'Open';
    if (normalized.includes('group'))
        return 'Group';
    if (normalized.includes('single'))
        return 'Single';
    return 'Room';
}
//# sourceMappingURL=chat.pure-functions.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
