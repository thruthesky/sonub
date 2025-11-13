---
name: demo.spec.ts
description: Vitest 기본 테스트 파일
version: 1.0.0
type: test
category: unit-test
tags: [vitest, test, demo]
---

# demo.spec.ts

## 개요
Vitest 설정을 확인하기 위한 기본 테스트 파일입니다.

## 테스트 내용
```typescript
describe('sum test', () => {
  it('adds 1 + 2 to equal 3', () => {
    expect(1 + 2).toBe(3);
  });
});
```

## 실행 방법
```bash
npm run test
```
