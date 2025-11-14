---
name: index.ts
description: index UI 컴포넌트
version: 1.0.0
type: typescript
category: ui-component
original_path: src/lib/components/ui/card/index.ts
---

# index.ts

## 개요

**파일 경로**: `src/lib/components/ui/card/index.ts`
**파일 타입**: typescript
**카테고리**: ui-component

index UI 컴포넌트

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
