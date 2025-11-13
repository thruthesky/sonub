---
name: utils.ts
description: 유틸리티 함수 모음
version: 1.0.0
type: util
category: utility
tags: [tailwind, clsx, shadcn, typescript]
---

# utils.ts

## 개요
shadcn-svelte와 호환되는 클래스 이름 병합 함수를 제공합니다.

## 주요 함수
- **cn(...inputs)**: Tailwind CSS 클래스를 효율적으로 병합하고 충돌하는 클래스를 제거

## 사용 예시
```typescript
import { cn } from '$lib/utils';

const className = cn('px-4 py-2', 'bg-blue-500', 'hover:bg-blue-600');
```
