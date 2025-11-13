---
title: "firebase/functions/tsconfig.json"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/functions/tsconfig.json"
spec_type: "repository-source"
---

## 개요

이 파일은 tsconfig.json의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
