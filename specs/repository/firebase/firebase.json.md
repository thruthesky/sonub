---
title: "firebase/firebase.json"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/firebase.json"
spec_type: "repository-source"
---

## 개요

이 파일은 firebase.json의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
