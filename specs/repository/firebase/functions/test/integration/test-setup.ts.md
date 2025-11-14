---
name: test-setup.ts
description: test-setup Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/test/integration/test-setup.ts
---

# test-setup.ts

## 개요

**파일 경로**: `firebase/functions/test/integration/test-setup.ts`
**파일 타입**: firebase-function
**카테고리**: cloud-function

test-setup Cloud Function

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
