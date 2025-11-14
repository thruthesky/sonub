---
name: context.ts
description: context UI 컴포넌트
version: 1.0.0
type: typescript
category: ui-component
original_path: src/lib/components/ui/dialog/context.ts
---

# context.ts

## 개요

**파일 경로**: `src/lib/components/ui/dialog/context.ts`
**파일 타입**: typescript
**카테고리**: ui-component

context UI 컴포넌트

## 소스 코드

```typescript
import type { Writable } from 'svelte/store';

export type DialogContext = {
  openStore: Writable<boolean>;
  setOpen(value: boolean): void;
};

export const dialogContextKey = Symbol('ui-dialog-context');

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
