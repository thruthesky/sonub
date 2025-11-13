---
title: "firebase/functions/test/unit/user.handler.test.ts"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/functions/test/unit/user.handler.test.ts"
spec_type: "repository-source"
---

## 개요

이 파일은 user.handler.test.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * Unit Test: user.handler.ts
 * handleUserUpdate 함수 테스트
 *
 * Mocking: Firebase Admin SDK (database, logger)
 */

import {expect} from "chai";
import * as sinon from "sinon";
import * as admin from "firebase-admin";
import * as logger from "firebase-functions/logger";
import {handleUserUpdate} from "../../src/handlers/user.handler";
import {UserData} from "../../src/types";

// Firebase Admin 초기화 (테스트 파일 로드 시 한 번만 실행)
if (!admin.apps.length) {
  // 환경 변수 설정으로 실제 credential 조회 방지
  process.env.FIREBASE_AUTH_EMULATOR_HOST = "localhost:9099";
  process.env.FIRESTORE_EMULATOR_HOST = "localhost:8080";
  process.env.FIREBASE_DATABASE_EMULATOR_HOST = "localhost:9000";

  admin.initializeApp({
    projectId: "test-project",
    databaseURL: "https://test-project.firebaseio.com",
  });
}

describe("user.handler - 사용자 정보 업데이트 처리", () => {
  let sandbox: sinon.SinonSandbox;
  let refStub: sinon.SinonStub;
  let updateStub: sinon.SinonStub;
  let loggerInfoStub: sinon.SinonStub;

  beforeEach(() => {
    sandbox = sinon.createSandbox();

    // Logger 스텁
    loggerInfoStub = sandbox.stub(logger, "info");

    // Database 스텁 설정
    updateStub = sandbox.stub().resolves();
    refStub = sandbox.stub().returns({
      update: updateStub,
    });

    // admin.database()를 모킹
    const databaseStub = sandbox.stub(admin, "database") as any;
    databaseStub.returns({
      ref: refStub,
      ServerValue: admin.database.ServerValue,
    });
  });

  afterEach(() => {
    sandbox.restore();
  });

  describe("handleUserUpdate - 사용자 정보 업데이트 처리", () => {
    describe("✅ 정상 케이스", () => {
      it("displayName이 변경되면 updatedAt과 displayNameLowerCase를 업데이트한다", async () => {
        // Given: displayName이 변경된 경우
        const uid = "user-123";
        const beforeData: UserData = {
          displayName: "OldName",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "NewName",
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답
        expect(result.success).to.be.true;
        expect(result.uid).to.equal(uid);
        expect(result.updated).to.be.true;

        // Then: update() 호출 확인
        expect(updateStub.calledOnce).to.be.true;
        const updates = updateStub.firstCall.args[0];

        // Then: updatedAt 업데이트 확인
        expect(updates[`users/${uid}/updatedAt`]).to.be.a("number");
        expect(updates[`users/${uid}/updatedAt`]).to.be.greaterThan(1000000);

        // Then: displayNameLowerCase 업데이트 확인
        expect(updates[`users/${uid}/displayNameLowerCase`]).to.equal("newname");

        // Then: 로그 출력 확인
        expect(loggerInfoStub.called).to.be.true;
      });

      it("photoUrl이 변경되면 updatedAt만 업데이트한다", async () => {
        // Given: photoUrl이 변경된 경우
        const uid = "user-456";
        const beforeData: UserData = {
          displayName: "SameName",
          photoUrl: "https://old-photo.jpg",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "SameName",
          photoUrl: "https://new-photo.jpg",
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        // Then: update() 호출 확인
        expect(updateStub.calledOnce).to.be.true;
        const updates = updateStub.firstCall.args[0];

        // Then: updatedAt만 업데이트되고 displayNameLowerCase는 업데이트되지 않음
        expect(updates[`users/${uid}/updatedAt`]).to.be.a("number");
        expect(updates[`users/${uid}/displayNameLowerCase`]).to.be.undefined;
      });

      it("displayName과 photoUrl이 모두 변경되면 모든 필드를 업데이트한다", async () => {
        // Given: displayName과 photoUrl이 모두 변경된 경우
        const uid = "user-789";
        const beforeData: UserData = {
          displayName: "OldName",
          photoUrl: "https://old-photo.jpg",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "NewName",
          photoUrl: "https://new-photo.jpg",
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        // Then: update() 호출 확인
        expect(updateStub.calledOnce).to.be.true;
        const updates = updateStub.firstCall.args[0];

        // Then: updatedAt과 displayNameLowerCase 모두 업데이트 확인
        expect(updates[`users/${uid}/updatedAt`]).to.be.a("number");
        expect(updates[`users/${uid}/displayNameLowerCase`]).to.equal("newname");
      });

      it("createdAt이 없으면 자동으로 생성한다", async () => {
        // Given: afterData에 createdAt이 없고, beforeData에는 있는 경우
        const uid = "user-new";
        const beforeData: UserData = {
          displayName: "Name",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "Name",
          // createdAt 없음
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        // Then: update() 호출 확인
        expect(updateStub.calledOnce).to.be.true;
        const updates = updateStub.firstCall.args[0];

        // Then: createdAt이 beforeData의 값으로 설정됨
        expect(updates[`users/${uid}/createdAt`]).to.equal(1000000);
      });

      it("createdAt이 beforeData와 afterData 모두 없으면 현재 시간으로 생성한다", async () => {
        // Given: beforeData와 afterData 모두 createdAt이 없는 경우
        const uid = "user-new-2";
        const beforeData: UserData = {
          displayName: "Name",
        };
        const afterData: UserData = {
          displayName: "Name",
        };

        const beforeTime = Date.now();

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        const afterTime = Date.now();

        // Then: 성공 응답
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        // Then: update() 호출 확인
        expect(updateStub.calledOnce).to.be.true;
        const updates = updateStub.firstCall.args[0];

        // Then: createdAt이 현재 시간으로 설정됨
        expect(updates[`users/${uid}/createdAt`]).to.be.a("number");
        expect(updates[`users/${uid}/createdAt`]).to.be.at.least(beforeTime);
        expect(updates[`users/${uid}/createdAt`]).to.be.at.most(afterTime);
      });
    });

    describe("❌ 변경 없음 케이스", () => {
      it("displayName과 photoUrl이 변경되지 않으면 updatedAt을 업데이트하지 않는다", async () => {
        // Given: displayName과 photoUrl이 변경되지 않은 경우
        const uid = "user-unchanged";
        const beforeData: UserData = {
          displayName: "SameName",
          photoUrl: "https://same-photo.jpg",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "SameName",
          photoUrl: "https://same-photo.jpg",
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답이지만 업데이트되지 않음
        expect(result.success).to.be.true;
        expect(result.updated).to.be.false;

        // Then: update() 호출되지 않음
        expect(updateStub.called).to.be.false;
      });

      it("다른 필드만 변경되고 displayName과 photoUrl은 변경되지 않으면 업데이트하지 않는다", async () => {
        // Given: gender 등 다른 필드만 변경된 경우
        const uid = "user-other-field";
        const beforeData: UserData = {
          displayName: "SameName",
          photoUrl: "https://same-photo.jpg",
          gender: "M",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "SameName",
          photoUrl: "https://same-photo.jpg",
          gender: "F",
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답이지만 업데이트되지 않음
        expect(result.success).to.be.true;
        expect(result.updated).to.be.false;

        // Then: update() 호출되지 않음
        expect(updateStub.called).to.be.false;
      });
    });

    describe("🔍 경계값 테스트", () => {
      it("photoURL(대문자)과 photoUrl(소문자)을 모두 처리한다", async () => {
        // Given: beforeData는 photoURL(대문자), afterData는 photoUrl(소문자)
        const uid = "user-photo-case";
        const beforeData: UserData = {
          displayName: "Name",
          photoURL: "https://old-photo.jpg", // 대문자
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: "Name",
          photoUrl: "https://new-photo.jpg", // 소문자
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: photoUrl 변경으로 감지되어 updatedAt 업데이트됨
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        const updates = updateStub.firstCall.args[0];
        expect(updates[`users/${uid}/updatedAt`]).to.be.a("number");
      });

      it("빈 문자열과 null/undefined를 구분한다", async () => {
        // Given: displayName이 빈 문자열에서 null로 변경
        const uid = "user-empty-string";
        const beforeData: UserData = {
          displayName: "",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: undefined,
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 변경으로 감지되어 updatedAt 업데이트됨
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        const updates = updateStub.firstCall.args[0];
        expect(updates[`users/${uid}/updatedAt`]).to.be.a("number");
      });

      it("매우 긴 displayName을 처리한다", async () => {
        // Given: 매우 긴 displayName
        const uid = "user-long-name";
        const longName = "A".repeat(1000);
        const beforeData: UserData = {
          displayName: "Short",
          createdAt: 1000000,
        };
        const afterData: UserData = {
          displayName: longName,
          createdAt: 1000000,
        };

        // When: 사용자 정보 업데이트 처리
        const result = await handleUserUpdate(uid, beforeData, afterData);

        // Then: 성공 응답
        expect(result.success).to.be.true;
        expect(result.updated).to.be.true;

        const updates = updateStub.firstCall.args[0];
        expect(updates[`users/${uid}/displayNameLowerCase`]).to.equal(longName.toLowerCase());
      });
    });
  });
});

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
