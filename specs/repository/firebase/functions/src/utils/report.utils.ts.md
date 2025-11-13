---
name: report.utils.ts
description: 신고 ID 파싱 유틸리티 함수
version: 1.0.0
type: firebase-function
category: util
tags: [firebase, cloud-functions, typescript, report, util, parser]
---

# report.utils.ts

## 개요
이 파일은 신고 관련 유틸리티 함수를 제공합니다. reportId를 파싱하여 type, nodeId, uid를 추출하는 기능을 포함합니다.

## 소스 코드

```typescript
/**
 * 신고 관련 유틸리티 함수
 */

import * as logger from "firebase-functions/logger";
import {ParsedReportId} from "../types";

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
export function parseReportId(reportId: string): ParsedReportId | null {
  logger.debug("🔍 parseReportId 시작", {reportId, reportIdLength: reportId.length});

  // 1단계: type 추출 (첫 번째 하이픈 이전)
  const firstDashIndex = reportId.indexOf("-");
  if (firstDashIndex === -1) {
    logger.error("❌ reportId에 하이픈이 없음", {reportId});
    return null;
  }

  const type = reportId.substring(0, firstDashIndex);
  logger.debug("1단계: type 추출 완료", {type, firstDashIndex});

  if (type !== "post" && type !== "comment") {
    logger.error("❌ 잘못된 type", {type, reportId});
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
    logger.error("❌ remainder에 하이픈이 없음", {remainder, reportId});
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
    logger.error("❌ nodeId 또는 uid가 비어있음", {nodeId, uid, reportId});
    return null;
  }

  logger.info("✅ parseReportId 성공", {type, nodeId, uid, reportId});

  return {
    type: type as "post" | "comment",
    nodeId,
    uid,
  };
}
```

## 주요 기능
- **parseReportId**: reportId 파싱 함수
  - reportId 형식: "{type}-{nodeId}-{uid}"
  - type 추출 (post 또는 comment)
  - nodeId와 uid 분리 (하이픈이 포함될 수 있는 경우 처리)
  - 상세한 로깅 제공
  - 파싱 실패 시 null 반환

## 사용되는 Firebase 트리거
- 이 파일은 직접 트리거되지 않음
- 신고 관련 핸들러 함수에서 유틸리티로 호출됨

## 관련 함수
- `types/index.ts`: ParsedReportId 타입 정의
