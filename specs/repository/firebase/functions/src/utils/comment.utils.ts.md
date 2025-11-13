---
name: comment.utils.ts
description: 댓글 참조 조회 유틸리티 함수
version: 1.0.0
type: firebase-function
category: util
tags: [firebase, cloud-functions, typescript, comment, util, rtdb]
---

# comment.utils.ts

## 개요
이 파일은 댓글 관련 유틸리티 함수를 제공합니다. Firebase Realtime Database에서 댓글 참조를 조회하는 기능을 포함하며, Firebase push key 형식을 자동으로 처리합니다.

## 소스 코드

```typescript
/**
 * 댓글 관련 유틸리티 함수
 */

import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";

/**
 * 댓글 참조를 가져옵니다 (Flat Style).
 * - 직접 /comments/{commentId} 경로에 접근합니다.
 * - commentId가 '-'로 시작하지 않으면 자동으로 '-'를 붙입니다.
 * - '-'를 붙인 경로가 없으면 원본 commentId로도 시도합니다.
 *
 * @param {string} commentId - 댓글 ID
 * @returns {Promise} 댓글 참조 또는 null
 */
export async function getCommentReference(commentId: string): Promise<{
  ref: admin.database.Reference;
  snapshot: admin.database.DataSnapshot;
} | null> {
  const db = admin.database();

  logger.info("🔍 댓글 참조 조회 시작", {
    originalCommentId: commentId,
    startsWithDash: commentId.startsWith("-"),
    commentIdLength: commentId.length,
  });

  // 시도 1: commentId가 '-'로 시작하지 않으면 앞에 '-'를 붙임
  // Firebase의 push() 키는 '-'로 시작하는 형식입니다
  // 예: 'OdHmkcgoutoA84V5ldF' → '-OdHmkcgoutoA84V5ldF'
  const normalizedCommentId = commentId.startsWith("-") ? commentId : `-${commentId}`;

  logger.debug("시도 1: 정규화된 commentId로 조회", {
    normalizedCommentId,
    path: `/comments/${normalizedCommentId}`,
  });

  let commentRef = db.ref(`/comments/${normalizedCommentId}`);
  let snapshot = await commentRef.once("value");

  if (snapshot.exists()) {
    logger.info("✅ 댓글 찾음 (정규화된 경로)", {
      normalizedCommentId,
      path: `/comments/${normalizedCommentId}`,
      commentData: snapshot.val(),
    });
    return {ref: commentRef, snapshot};
  }

  logger.warn("⚠️ 정규화된 경로에서 댓글을 찾을 수 없음", {
    normalizedCommentId,
    pathChecked: `/comments/${normalizedCommentId}`,
  });

  // 시도 2: 원본 commentId 그대로 조회 (정규화하지 않음)
  logger.debug("시도 2: 원본 commentId로 조회", {
    originalCommentId: commentId,
    path: `/comments/${commentId}`,
  });

  commentRef = db.ref(`/comments/${commentId}`);
  snapshot = await commentRef.once("value");

  if (snapshot.exists()) {
    logger.info("✅ 댓글 찾음 (원본 경로)", {
      originalCommentId: commentId,
      path: `/comments/${commentId}`,
      commentData: snapshot.val(),
    });
    return {ref: commentRef, snapshot};
  }

  logger.error("❌ 댓글을 찾을 수 없음 (모든 시도 실패)", {
    originalCommentId: commentId,
    normalizedCommentId,
    pathsChecked: [
      `/comments/${normalizedCommentId}`,
      `/comments/${commentId}`,
    ],
  });

  return null;
}
```

## 주요 기능
- **getCommentReference**: 댓글 참조 조회
  - Firebase push key 형식 자동 처리 (하이픈 접두사)
  - 두 가지 방식으로 조회 시도:
    1. 정규화된 commentId (하이픈 접두사 추가)
    2. 원본 commentId
  - 상세한 로깅 제공
  - 댓글을 찾지 못하면 null 반환

## 사용되는 Firebase 트리거
- 이 파일은 직접 트리거되지 않음
- 다른 핸들러 함수에서 유틸리티로 호출됨

## 관련 함수
- Firebase Realtime Database의 `/comments` 노드와 상호작용
