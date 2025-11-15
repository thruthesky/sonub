---
title: tsconfig.json
type: config
path: firebase/functions/tsconfig.json
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `firebase/functions/tsconfig.json`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

- 2025-11-15: 스펙 문서 생성
