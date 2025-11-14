---
name: chat.functions.js
description: chat.functions Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/src/lib/functions/chat.functions.js
---

# chat.functions.js

## 개요

**파일 경로**: `firebase/functions/lib/src/lib/functions/chat.functions.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

chat.functions Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * 채팅 관련 함수 모음
 *
 * 이 파일은 순수 함수뿐만 아니라, 재사용 가능하고 테스트 가능한 모든 함수를 포함합니다.
 * 중요: 이 코드는 Firebase Cloud Functions에서도 사용될 수 있으므로,
 * Firebase Cloud Functions의 TypeScript 환경과 호환되어야 합니다.
 */
Object.defineProperty(exports, "__esModule", { value: true });
exports.buildSingleRoomId = buildSingleRoomId;
exports.isSingleChat = isSingleChat;
exports.extractUidsFromSingleRoomId = extractUidsFromSingleRoomId;
exports.resolveRoomTypeLabel = resolveRoomTypeLabel;
exports.joinChatRoom = joinChatRoom;
exports.leaveChatRoom = leaveChatRoom;
const database_1 = require("firebase/database");
/**
 * 1:1 채팅방의 roomId를 UID 두 개로부터 고정적으로 생성한다.
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
/**
 * 사용자를 채팅방에 입장시킨다.
 *
 * 이 함수는 사용자가 그룹 채팅방 또는 오픈 채팅방에 입장할 때 호출됩니다.
 * chat-rooms/{roomId}/members/{uid}를 true로 설정하여 다음을 수행합니다:
 * 1. 사용자가 채팅방에 참여 중임을 표시
 * 2. 메시지 알림을 받도록 설정
 * 3. Cloud Functions가 자동으로 memberCount를 증가시킴
 *
 * @param db - Firebase Realtime Database 인스턴스
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * import { rtdb } from '$lib/firebase';
 * import { joinChatRoom } from '$lib/functions/chat.functions';
 *
 * // 사용자가 채팅방에 입장할 때
 * await joinChatRoom(rtdb, roomId, currentUser.uid);
 * ```
 */
async function joinChatRoom(db, roomId, uid) {
    const memberRef = (0, database_1.ref)(db, `chat-rooms/${roomId}/members/${uid}`);
    await (0, database_1.set)(memberRef, true);
}
/**
 * 사용자를 채팅방에서 퇴장시킨다.
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
async function leaveChatRoom(db, roomId, uid) {
    const memberRef = (0, database_1.ref)(db, `chat-rooms/${roomId}/members/${uid}`);
    await (0, database_1.set)(memberRef, null); // null로 설정하여 속성 삭제
}
//# sourceMappingURL=chat.functions.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
