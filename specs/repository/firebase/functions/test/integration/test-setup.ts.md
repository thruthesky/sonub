---
title: test-setup.ts
type: typescript
path: firebase/functions/test/integration/test-setup.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `firebase/functions/test/integration/test-setup.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * Integration Test Setup
 * firebase-functions-test를 사용하여 Event Handler를 직접 테스트
 *
 * ⚠️ 주의:
 * - 이 테스트는 실제 Firebase 프로젝트에 연결합니다 (또는 Emulator)
 * - 환경 변수 FIREBASE_CONFIG를 설정해야 합니다
 * - 테스트 전용 프로젝트 사용을 권장합니다
 */

import * as functionsTest from "firebase-functions-test";
import * as admin from "firebase-admin";

// Firebase Functions Test 초기화 (offline 모드)
// offline 모드: Event 객체만 생성, 실제 Firebase에 연결하지 않음
// online 모드: 실제 Firebase 프로젝트에 연결 (환경 변수 필요)
const testEnv = functionsTest({
  projectId: "test-project-id",
}, "./service-account-key.json"); // 테스트용 service account key (선택 사항)

// Admin SDK 초기화 (이미 index.ts에서 초기화되었을 수 있음)
if (!admin.apps.length) {
  admin.initializeApp({
    projectId: "test-project-id",
    databaseURL: "https://test-project-id-default-rtdb.firebaseio.com",
  });
}

export {testEnv, admin};

/**
 * 테스트 종료 시 cleanup
 */
export function cleanup() {
  testEnv.cleanup();
}

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
