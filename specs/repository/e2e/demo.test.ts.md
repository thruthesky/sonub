---
title: demo.test.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 demo.test.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
import { expect, test } from '@playwright/test';

test('home page has expected h1', async ({ page }) => {
	await page.goto('/');
	await expect(page.locator('h1')).toBeVisible();
});

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
