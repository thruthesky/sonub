---
title: index.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 index.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

- 2025-11-13: 스펙 문서 생성/업데이트
