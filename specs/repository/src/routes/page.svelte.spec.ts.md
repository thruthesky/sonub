---
name: page.svelte.spec.ts
description: page.svelte.spec 페이지
version: 1.0.0
type: test
category: route-page
original_path: src/routes/page.svelte.spec.ts
---

# page.svelte.spec.ts

## 개요

**파일 경로**: `src/routes/page.svelte.spec.ts`
**파일 타입**: test
**카테고리**: route-page

page.svelte.spec 페이지

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
