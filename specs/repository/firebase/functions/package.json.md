---
name: functions/package.json
description: Firebase Cloud Functions의 npm 패키지 설정 파일. 서버리스 함수의 의존성 및 스크립트를 정의합니다.
version: 1.0.0
type: configuration
category: functions-config
tags: [configuration, firebase, functions, npm, serverless]
---

# functions/package.json

## 개요
Firebase Cloud Functions의 package.json 파일입니다. 이 파일은:
- Cloud Functions의 빌드, 배포, 테스트 스크립트 정의
- Firebase Admin SDK 및 Functions SDK 의존성 관리
- Node.js 22 런타임 지정
- CommonJS 모듈 시스템 사용

## 소스 코드

```json
{
  "name": "functions",
  "type": "commonjs",
  "scripts": {
    "lint": "eslint src",
    "lint:fix": "eslint src --fix",
    "build": "tsc && tsc-alias",
    "build:watch": "tsc --watch",
    "serve": "npm run build && firebase emulators:start --only functions",
    "shell": "npm run build && firebase functions:shell",
    "start": "npm run shell",
    "deploy": "firebase deploy --only functions --force --non-interactive",
    "logs": "firebase functions:log",
    "generate:posts": "npm run build && node lib/scripts/generate-sample-posts.js",
    "test": "mocha --require ts-node/register 'test/**/*.test.ts' --timeout 10000",
    "test:unit": "mocha --require ts-node/register 'test/unit/**/*.test.ts' --timeout 5000",
    "test:integration": "mocha --require ts-node/register 'test/integration/**/*.test.ts' --timeout 10000",
    "test:watch": "mocha --require ts-node/register 'test/**/*.test.ts' --watch --watch-extensions ts"
  },
  "engines": {
    "node": "22"
  },
  "main": "lib/firebase/functions/src/index.js",
  "dependencies": {
    "firebase-admin": "^12.6.0",
    "firebase-functions": "^7.0.0"
  },
  "devDependencies": {
    "@types/chai": "^5.2.3",
    "@types/mocha": "^10.0.10",
    "@types/sinon": "^17.0.4",
    "@typescript-eslint/eslint-plugin": "^8.46.4",
    "@typescript-eslint/parser": "^8.46.4",
    "chai": "^6.2.0",
    "eslint": "^8.9.0",
    "eslint-config-google": "^0.14.0",
    "eslint-plugin-import": "^2.25.4",
    "firebase-functions-test": "^3.1.0",
    "mocha": "^11.7.4",
    "sinon": "^21.0.0",
    "ts-node": "^10.9.2",
    "tsc-alias": "^1.8.16",
    "typescript": "^5.9.3"
  },
  "private": true
}
```

## 주요 설정

### 프로젝트 메타데이터
- **name**: functions
- **type**: commonjs (CommonJS 모듈 시스템)
- **engines.node**: 22 (Node.js 22 런타임)
- **main**: `lib/firebase/functions/src/index.js` (진입점)

### 주요 스크립트

#### 개발
- **lint**: ESLint로 코드 검사
- **lint:fix**: ESLint 자동 수정
- **build**: TypeScript 컴파일 + 경로 별칭 해석
- **build:watch**: TypeScript 감시 모드 빌드

#### 실행 및 테스트
- **serve**: Firebase 에뮬레이터 시작
- **shell**: Functions 대화형 셸
- **start**: shell 실행
- **test**: 전체 테스트 (10초 타임아웃)
- **test:unit**: 유닛 테스트 (5초 타임아웃)
- **test:integration**: 통합 테스트 (10초 타임아웃)
- **test:watch**: 테스트 감시 모드

#### 배포 및 유틸리티
- **deploy**: Cloud Functions 강제 배포 (비대화형)
- **logs**: Functions 로그 조회
- **generate:posts**: 샘플 게시물 생성 스크립트

### 핵심 의존성
- **firebase-admin**: ^12.6.0 - Firebase Admin SDK (서버측)
- **firebase-functions**: ^7.0.0 - Cloud Functions SDK

### 개발 의존성
- **typescript**: ^5.9.3 - TypeScript 컴파일러
- **tsc-alias**: ^1.8.16 - 경로 별칭 해석
- **mocha**: ^11.7.4 - 테스트 프레임워크
- **chai**: ^6.2.0 - Assertion 라이브러리
- **sinon**: ^21.0.0 - Mocking 라이브러리
- **ts-node**: ^10.9.2 - TypeScript 실행 환경
- **firebase-functions-test**: ^3.1.0 - Functions 테스트 유틸리티
- **eslint**: ^8.9.0 + TypeScript ESLint 플러그인

## 관련 파일
- [tsconfig.json](./tsconfig.json.md) - TypeScript 설정
- [eslint.config.mjs](./eslint.config.mjs.md) - ESLint 설정
- [../firebase.json](../firebase.json.md) - Firebase 프로젝트 설정
