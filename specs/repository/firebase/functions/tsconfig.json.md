---
name: functions/tsconfig.json
description: Firebase Cloud Functions의 TypeScript 컴파일러 설정 파일. 경로 별칭 및 공유 코드 통합을 정의합니다.
version: 1.0.0
type: configuration
category: functions-config
tags: [configuration, typescript, functions, path-alias]
---

# functions/tsconfig.json

## 개요
Firebase Cloud Functions의 TypeScript 설정 파일입니다. 이 파일은:
- CommonJS 모듈 시스템 사용
- SvelteKit 프로젝트의 공유 함수 및 타입 참조
- 경로 별칭으로 클린 import 지원
- 엄격한 타입 체크 활성화

## 소스 코드

```json
{
  "compilerOptions": {
    "module": "CommonJS",
    "esModuleInterop": true,
    "moduleResolution": "node",
    "noImplicitReturns": true,
    "noUnusedLocals": true,
    "outDir": "lib",
    "baseUrl": ".",
    "paths": {
      "@functions/*": ["../../src/lib/functions/*"],
      "@shared/*": ["../../shared/*"]
    },
    "rootDirs": [
      "./src",
      "../../src/lib/functions",
      "../../shared"
    ],
    "sourceMap": true,
    "strict": true,
    "target": "es2017",
    "typeRoots": [
      "./node_modules/@types"
    ]
  },
  "compileOnSave": true,
  "include": [
    "src",
    "scripts",
    "../../shared/**/*.ts"
  ]
}
```

## 주요 설정

### 컴파일러 옵션

#### 모듈 시스템
- **module**: CommonJS - Node.js 호환 모듈
- **esModuleInterop**: true - ES 모듈과 CommonJS 상호운용성
- **moduleResolution**: node - Node.js 스타일 모듈 해석

#### 타입 체크
- **strict**: true - 엄격한 타입 체크
- **noImplicitReturns**: true - 모든 경로에서 반환값 필수
- **noUnusedLocals**: true - 사용하지 않는 로컬 변수 금지

#### 출력 설정
- **outDir**: lib - 컴파일된 JavaScript 출력 디렉토리
- **target**: es2017 - ECMAScript 2017 타겟
- **sourceMap**: true - 소스맵 생성

#### 경로 별칭 (Path Aliases)
- **baseUrl**: `.` - 상대 경로 기준점
- **paths**:
  - `@functions/*`: SvelteKit 프로젝트의 `src/lib/functions/*` 참조
  - `@shared/*`: 루트의 `shared/*` 폴더 참조
- **rootDirs**: 여러 소스 디렉토리 통합
  - `./src` - Cloud Functions 소스
  - `../../src/lib/functions` - SvelteKit 공유 함수
  - `../../shared` - 공유 타입/유틸리티

#### 타입 정의
- **typeRoots**: `["./node_modules/@types"]` - 타입 정의 위치

### 컴파일 설정
- **compileOnSave**: true - 저장 시 자동 컴파일

### 포함 파일 (include)
- `src` - Cloud Functions 소스 디렉토리
- `scripts` - 스크립트 디렉토리
- `../../shared/**/*.ts` - 공유 TypeScript 파일

## 경로 별칭 사용 예시

```typescript
// @functions 별칭 사용
import { someFunction } from '@functions/utils';

// @shared 별칭 사용
import { SharedType } from '@shared/types';

// 실제 경로
// @functions/utils → ../../src/lib/functions/utils
// @shared/types → ../../shared/types
```

## 관련 파일
- [package.json](./package.json.md) - tsc-alias로 경로 별칭 해석
- [tsconfig.dev.json](./tsconfig.dev.json.md) - 개발용 TypeScript 설정
- [eslint.config.mjs](./eslint.config.mjs.md) - ESLint 설정
