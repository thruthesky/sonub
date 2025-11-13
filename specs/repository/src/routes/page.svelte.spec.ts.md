---
name: page.svelte.spec.ts
description: 홈 페이지 컴포넌트 테스트
version: 1.0.0
type: test
category: component-test
tags: [vitest, svelte, browser, test]
---

# page.svelte.spec.ts

## 개요
Vitest Browser Mode를 사용하여 홈 페이지 (+page.svelte) 컴포넌트를 테스트합니다.

## 테스트 내용
```typescript
describe('/+page.svelte', () => {
  it('should render h1', async () => {
    render(Page);
    const heading = page.getByRole('heading', { level: 1 });
    await expect.element(heading).toBeInTheDocument();
  });
});
```

## 실행 방법
```bash
npm run test:browser
```
