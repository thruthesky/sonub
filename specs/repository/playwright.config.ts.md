---
title: "playwright.config.ts"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "playwright.config.ts"
spec_type: "repository-source"
---

## 개요

이 파일은 playwright.config.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
import { defineConfig } from '@playwright/test';

export default defineConfig({
	webServer: {
		command: 'npm run build && npm run preview',
		port: 4173
	},
	testDir: 'e2e'
});

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
