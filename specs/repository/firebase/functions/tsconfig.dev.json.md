---
name: functions/tsconfig.dev.json
description: Firebase Cloud Functions 개발 환경용 TypeScript 설정 파일. ESLint 설정 파일 컴파일을 위한 최소 설정입니다.
version: 1.0.0
type: configuration
category: functions-config
tags: [configuration, typescript, functions, development]
---

# functions/tsconfig.dev.json

## 개요
Firebase Cloud Functions 개발 환경의 TypeScript 설정 파일입니다. 이 파일은:
- ESLint 설정 파일(.eslintrc.js) 컴파일용
- 최소한의 설정만 포함
- 메인 tsconfig.json과 분리된 개발 전용 설정

## 소스 코드

```json
{
  "include": [
    ".eslintrc.js"
  ]
}
```

## 주요 설정

### 포함 파일 (include)
- **.eslintrc.js**: ESLint 설정 파일만 포함

## 사용 목적
이 파일은 TypeScript로 작성된 ESLint 설정 파일을 컴파일하기 위한 최소한의 설정입니다. 메인 `tsconfig.json`과 분리하여:
- 개발 도구 설정 파일 별도 관리
- 빌드 프로세스와 개발 도구 분리
- IDE/에디터에서 ESLint 설정 인식 개선

## 관련 파일
- [tsconfig.json](./tsconfig.json.md) - 메인 TypeScript 설정
- [eslint.config.mjs](./eslint.config.mjs.md) - ESLint 설정 파일
- [package.json](./package.json.md) - npm 패키지 설정
