---
title: "firebase/functions/package.json"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/functions/package.json"
spec_type: "repository-source"
---

## 개요

이 파일은 package.json의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
