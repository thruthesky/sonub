---
title: demo.spec.ts
type: typescript
path: src/demo.spec.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/demo.spec.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
import { describe, it, expect } from 'vitest';

describe('sum test', () => {
	it('adds 1 + 2 to equal 3', () => {
		expect(1 + 2).toBe(3);
	});
});

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
