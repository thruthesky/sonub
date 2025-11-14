---
name: post.utils.js
description: post.utils Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/firebase/functions/src/utils/post.utils.js
---

# post.utils.js

## 개요

**파일 경로**: `firebase/functions/lib/firebase/functions/src/utils/post.utils.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

post.utils Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * 게시글 관련 유틸리티 함수
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
exports.getPostReference = getPostReference;
const logger = __importStar(require("firebase-functions/logger"));
const admin = __importStar(require("firebase-admin"));
/**
 * 게시글 참조를 가져옵니다 (Flat Style).
 * - 직접 /posts/{postId} 경로에 접근합니다.
 * - postId가 '-'로 시작하지 않으면 자동으로 '-'를 붙입니다.
 * - '-'를 붙인 경로가 없으면 원본 postId로도 시도합니다.
 *
 * @param {string} postId - 게시글 ID
 * @returns {Promise} 게시글 참조 또는 null
 */
async function getPostReference(postId) {
    const db = admin.database();
    logger.info("🔍 게시글 참조 조회 시작", {
        originalPostId: postId,
        startsWithDash: postId.startsWith("-"),
        postIdLength: postId.length,
    });
    // 시도 1: postId가 '-'로 시작하지 않으면 앞에 '-'를 붙임
    // Firebase의 push() 키는 '-'로 시작하는 형식입니다
    // 예: 'OdEWc-SaDELU2Y51FDy' → '-OdEWc-SaDELU2Y51FDy'
    const normalizedPostId = postId.startsWith("-") ? postId : `-${postId}`;
    logger.debug("시도 1: 정규화된 postId로 조회", {
        normalizedPostId,
        path: `/posts/${normalizedPostId}`,
    });
    let postRef = db.ref(`/posts/${normalizedPostId}`);
    let snapshot = await postRef.once("value");
    if (snapshot.exists()) {
        logger.info("✅ 게시글 찾음 (정규화된 경로)", {
            normalizedPostId,
            path: `/posts/${normalizedPostId}`,
            postData: snapshot.val(),
        });
        return { ref: postRef, snapshot };
    }
    logger.warn("⚠️ 정규화된 경로에서 게시글을 찾을 수 없음", {
        normalizedPostId,
        pathChecked: `/posts/${normalizedPostId}`,
    });
    // 시도 2: 원본 postId 그대로 조회 (정규화하지 않음)
    logger.debug("시도 2: 원본 postId로 조회", {
        originalPostId: postId,
        path: `/posts/${postId}`,
    });
    postRef = db.ref(`/posts/${postId}`);
    snapshot = await postRef.once("value");
    if (snapshot.exists()) {
        logger.info("✅ 게시글 찾음 (원본 경로)", {
            originalPostId: postId,
            path: `/posts/${postId}`,
            postData: snapshot.val(),
        });
        return { ref: postRef, snapshot };
    }
    logger.error("❌ 게시글을 찾을 수 없음 (모든 시도 실패)", {
        originalPostId: postId,
        normalizedPostId,
        pathsChecked: [
            `/posts/${normalizedPostId}`,
            `/posts/${postId}`,
        ],
    });
    return null;
}
//# sourceMappingURL=post.utils.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
