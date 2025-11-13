---
name: firebase.json
description: Firebase 프로젝트 설정 파일. Cloud Functions 및 Realtime Database 규칙을 정의합니다.
version: 1.0.0
type: configuration
category: firebase-config
tags: [configuration, firebase, functions, database]
---

# firebase.json

## 개요
Firebase 프로젝트의 핵심 설정 파일입니다. 이 파일은:
- Cloud Functions 배포 설정
- Realtime Database 보안 규칙 경로 지정
- 빌드 및 배포 전 사전 작업 정의

## 소스 코드

```json
{
  "functions": [
    {
      "source": "functions",
      "codebase": "default",
      "disallowLegacyRuntimeConfig": true,
      "ignore": [
        "node_modules",
        ".git",
        "firebase-debug.log",
        "firebase-debug.*.log",
        "*.local"
      ],
      "predeploy": [
        "npm --prefix \"$RESOURCE_DIR\" run lint",
        "npm --prefix \"$RESOURCE_DIR\" run build"
      ]
    }
  ],
  "database": {
    "rules": "database.rules.json"
  }
}
```

## 주요 설정

### Cloud Functions 설정
- **source**: `functions` - Cloud Functions 소스 코드 디렉토리
- **codebase**: `default` - 기본 코드베이스
- **disallowLegacyRuntimeConfig**: true - 레거시 런타임 설정 비활성화

### 무시 파일 (ignore)
배포 시 제외되는 파일 및 폴더:
- `node_modules` - npm 의존성 폴더
- `.git` - Git 저장소
- `firebase-debug.log` - Firebase 디버그 로그
- `firebase-debug.*.log` - Firebase 디버그 로그 (패턴)
- `*.local` - 로컬 설정 파일

### 배포 전 작업 (predeploy)
Functions 배포 전 자동 실행되는 명령어:
1. `npm --prefix "$RESOURCE_DIR" run lint` - ESLint 검사
2. `npm --prefix "$RESOURCE_DIR" run build` - TypeScript 빌드

### Realtime Database 설정
- **rules**: `database.rules.json` - 보안 규칙 파일 경로

## 배포 워크플로우
1. ESLint로 코드 품질 검사
2. TypeScript → JavaScript 컴파일
3. Cloud Functions 배포
4. Database 규칙 업데이트

## 관련 파일
- [database.rules.json](./database.rules.json.md) - Realtime Database 보안 규칙
- [cors.json](./cors.json.md) - CORS 설정
- [functions/package.json](./functions/package.json.md) - Cloud Functions 의존성
