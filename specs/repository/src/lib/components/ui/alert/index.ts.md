---
name: index.ts
description: index UI 컴포넌트
version: 1.0.0
type: typescript
category: ui-component
original_path: src/lib/components/ui/alert/index.ts
---

# index.ts

## 개요

**파일 경로**: `src/lib/components/ui/alert/index.ts`
**파일 타입**: typescript
**카테고리**: ui-component

index UI 컴포넌트

## 소스 코드

```typescript
/**
 * Alert 컴포넌트 export
 */

import Root from './alert.svelte';
import Title from './alert-title.svelte';
import Description from './alert-description.svelte';

export {
	Root,
	Title,
	Description,
	//
	Root as Alert,
	Title as AlertTitle,
	Description as AlertDescription
};

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
