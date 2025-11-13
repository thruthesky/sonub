---
title: "src/demo.spec.ts"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "src/demo.spec.ts"
spec_type: "repository-source"
---

## 개요

이 파일은 demo.spec.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

- 2025-11-13: 스펙 문서 생성/업데이트
