---
name: tsconfig.json
description: tsconfig Cloud Function
version: 1.0.0
type: configuration
category: cloud-function
original_path: firebase/functions/tsconfig.json
---

# tsconfig.json

## 개요

**파일 경로**: `firebase/functions/tsconfig.json`
**파일 타입**: configuration
**카테고리**: cloud-function

tsconfig Cloud Function

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
