---
title: context.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 context.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
import type { Writable } from 'svelte/store';

export type DialogContext = {
  openStore: Writable<boolean>;
  setOpen(value: boolean): void;
};

export const dialogContextKey = Symbol('ui-dialog-context');

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
