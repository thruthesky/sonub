---
name: demo.test.ts
description: demo.test 파일
version: 1.0.0
type: test
category: other
original_path: e2e/demo.test.ts
---

# demo.test.ts

## 개요

**파일 경로**: `e2e/demo.test.ts`
**파일 타입**: test
**카테고리**: other

demo.test 파일

## 소스 코드

```typescript
import { expect, test } from '@playwright/test';

test('home page has expected h1', async ({ page }) => {
	await page.goto('/');
	await expect(page.locator('h1')).toBeVisible();
});

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
