---
title: version.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 version.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * 빌드 버전 정보
 *
 * 이 파일은 애플리케이션의 빌드 버전을 저장합니다.
 * 형식: YY. MM. DD. h:mmAM/PM (예: "25. 11. 09. 8:49PM")
 *
 * 버전 업데이트 방법:
 * - 개발자가 "버전 업데이트" 요청 시, 이 값을 현재 날짜와 시간으로 업데이트합니다.
 * - 형식을 정확히 유지해야 합니다.
 */

export const BUILD_VERSION = '25. 11. 09. 8:49PM';

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
