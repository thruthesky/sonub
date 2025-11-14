---
name: playwright.config.ts
description: playwright.config 설정 파일
version: 1.0.0
type: typescript
category: root-configuration
original_path: playwright.config.ts
---

# playwright.config.ts

## 개요

**파일 경로**: `playwright.config.ts`
**파일 타입**: typescript
**카테고리**: root-configuration

playwright.config 설정 파일

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
