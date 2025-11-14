---
name: comment.utils.js
description: comment.utils Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/firebase/functions/src/utils/comment.utils.js
---

# comment.utils.js

## 개요

**파일 경로**: `firebase/functions/lib/firebase/functions/src/utils/comment.utils.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

comment.utils Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * 댓글 관련 유틸리티 함수
 */
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
exports.getCommentReference = getCommentReference;
const logger = __importStar(require("firebase-functions/logger"));
const admin = __importStar(require("firebase-admin"));
/**
 * 댓글 참조를 가져옵니다 (Flat Style).
 * - 직접 /comments/{commentId} 경로에 접근합니다.
 * - commentId가 '-'로 시작하지 않으면 자동으로 '-'를 붙입니다.
 * - '-'를 붙인 경로가 없으면 원본 commentId로도 시도합니다.
 *
 * @param {string} commentId - 댓글 ID
 * @returns {Promise} 댓글 참조 또는 null
 */
async function getCommentReference(commentId) {
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
        return { ref: commentRef, snapshot };
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
        return { ref: commentRef, snapshot };
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
//# sourceMappingURL=comment.utils.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
