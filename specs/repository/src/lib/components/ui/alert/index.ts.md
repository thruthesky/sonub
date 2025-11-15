---
title: index.ts
type: typescript
path: src/lib/components/ui/alert/index.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/components/ui/alert/index.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
