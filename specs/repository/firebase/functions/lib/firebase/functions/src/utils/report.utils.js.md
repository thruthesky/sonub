---
name: report.utils.js
description: report.utils Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/firebase/functions/src/utils/report.utils.js
---

# report.utils.js

## 개요

**파일 경로**: `firebase/functions/lib/firebase/functions/src/utils/report.utils.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

report.utils Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * 신고 관련 유틸리티 함수
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
exports.parseReportId = parseReportId;
const logger = __importStar(require("firebase-functions/logger"));
/**
 * reportId를 파싱하여 type, nodeId, uid를 추출합니다.
 *
 * reportId 형식: "{type}-{nodeId}-{uid}"
 * - 문제: nodeId와 uid에 하이픈(-)이 포함될 수 있음
 * - 해결: 마지막 하이픈을 기준으로 uid를 분리하고, 나머지를 nodeId로 간주
 *
 * 예시:
 * - "post-OdEWc-SaDELU2Y51FDy-zodDYjqcmfb5WHi1rVYrUJi0d2j2-user123"
 * - type: "post"
 * - nodeId: "OdEWc-SaDELU2Y51FDy-zodDYjqcmfb5WHi1rVYrUJi0d2j2"
 * - uid: "user123"
 *
 * @param {string} reportId - 파싱할 reportId
 * @returns {ParsedReportId | null} 파싱 결과 또는 null (파싱 실패 시)
 */
function parseReportId(reportId) {
    logger.debug("🔍 parseReportId 시작", { reportId, reportIdLength: reportId.length });
    // 1단계: type 추출 (첫 번째 하이픈 이전)
    const firstDashIndex = reportId.indexOf("-");
    if (firstDashIndex === -1) {
        logger.error("❌ reportId에 하이픈이 없음", { reportId });
        return null;
    }
    const type = reportId.substring(0, firstDashIndex);
    logger.debug("1단계: type 추출 완료", { type, firstDashIndex });
    if (type !== "post" && type !== "comment") {
        logger.error("❌ 잘못된 type", { type, reportId });
        return null;
    }
    // 2단계: nodeId와 uid 분리
    // type 이후의 문자열을 추출: "post-ABC-DEF-user123" -> "ABC-DEF-user123"
    const remainder = reportId.substring(firstDashIndex + 1);
    logger.debug("2단계: remainder 추출 완료", {
        remainder,
        remainderLength: remainder.length,
    });
    // 마지막 하이픈을 기준으로 uid 분리
    // "ABC-DEF-user123" -> nodeId: "ABC-DEF", uid: "user123"
    const lastDashIndex = remainder.lastIndexOf("-");
    if (lastDashIndex === -1) {
        logger.error("❌ remainder에 하이픈이 없음", { remainder, reportId });
        return null;
    }
    const nodeId = remainder.substring(0, lastDashIndex);
    const uid = remainder.substring(lastDashIndex + 1);
    logger.debug("3단계: nodeId와 uid 분리 완료", {
        nodeId,
        uid,
        lastDashIndex,
    });
    if (!nodeId || !uid) {
        logger.error("❌ nodeId 또는 uid가 비어있음", { nodeId, uid, reportId });
        return null;
    }
    logger.info("✅ parseReportId 성공", { type, nodeId, uid, reportId });
    return {
        type: type,
        nodeId,
        uid,
    };
}
//# sourceMappingURL=report.utils.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
