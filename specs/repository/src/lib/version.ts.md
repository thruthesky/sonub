---
name: version.ts
description: 빌드 버전 정보
version: 1.0.0
type: typescript
category: version
tags: [version, build]
---

# version.ts

## 개요
애플리케이션의 빌드 버전을 저장합니다.

## BUILD_VERSION
형식: `YY. MM. DD. h:mmAM/PM` (예: "25. 11. 09. 8:49PM")

## 버전 업데이트 방법
개발자가 "버전 업데이트" 요청 시, 이 값을 현재 날짜와 시간으로 업데이트합니다.

## 사용 예시
```typescript
import { BUILD_VERSION } from '$lib/version';
console.log('빌드 버전:', BUILD_VERSION);
```
