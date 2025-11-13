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
 * Card 컴포넌트 export
 */

import Root from './card.svelte';
import Header from './card-header.svelte';
import Title from './card-title.svelte';
import Description from './card-description.svelte';
import Content from './card-content.svelte';
import Footer from './card-footer.svelte';

export {
	Root,
	Header,
	Title,
	Description,
	Content,
	Footer,
	//
	Root as Card,
	Header as CardHeader,
	Title as CardTitle,
	Description as CardDescription,
	Content as CardContent,
	Footer as CardFooter
};

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
