---
name: demo.test.ts
description: Playwright E2E 기본 테스트
version: 1.0.0
type: test
category: e2e-test
tags: [playwright, e2e, test]
---

# demo.test.ts

## 개요
Playwright를 사용한 E2E 테스트 파일입니다. 홈 페이지의 h1 요소가 표시되는지 확인합니다.

## 테스트 내용
```typescript
test('home page has expected h1', async ({ page }) => {
  await page.goto('/');
  await expect(page.locator('h1')).toBeVisible();
});
```

## 실행 방법
```bash
npm run test:e2e
```
