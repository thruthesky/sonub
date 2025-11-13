---
title: page.svelte.spec.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 page.svelte.spec.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
import { page } from 'vitest/browser';
import { describe, expect, it } from 'vitest';
import { render } from 'vitest-browser-svelte';
import Page from './+page.svelte';

describe('/+page.svelte', () => {
	it('should render h1', async () => {
		render(Page);

		const heading = page.getByRole('heading', { level: 1 });
		await expect.element(heading).toBeInTheDocument();
	});
});

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
