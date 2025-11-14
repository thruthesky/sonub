---
name: firebase.json
description: firebase 파일
version: 1.0.0
type: configuration
category: other
original_path: firebase/firebase.json
---

# firebase.json

## 개요

**파일 경로**: `firebase/firebase.json`
**파일 타입**: configuration
**카테고리**: other

firebase 파일

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
