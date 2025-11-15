---
title: playwright.config.ts
type: typescript
path: playwright.config.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `playwright.config.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

- 2025-11-15: 스펙 문서 생성
