---
name: utils.ts
description: utils 유틸리티
version: 1.0.0
type: typescript
category: utility
original_path: src/lib/utils.ts
---

# utils.ts

## 개요

**파일 경로**: `src/lib/utils.ts`
**파일 타입**: typescript
**카테고리**: utility

utils 유틸리티

## 소스 코드

```typescript
/**
 * 유틸리티 함수 모음
 *
 * shadcn-svelte와 호환되는 클래스 이름 병합 함수를 제공합니다.
 */

import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';
import type { HTMLAttributes } from 'svelte/elements';

/**
 * 클래스 이름을 병합하는 함수
 *
 * Tailwind CSS 클래스를 효율적으로 병합하고, 충돌하는 클래스를 제거합니다.
 *
 * @param inputs - 병합할 클래스 이름들
 * @returns 병합된 클래스 이름 문자열
 */
export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}

/**
 * shadcn-svelte 컴포넌트용 타입 유틸리티
 */
export type WithElementRef<T = HTMLAttributes<HTMLElement>> = T & {
	ref?: HTMLElement | null;
};

export type WithoutChild<T> = Omit<T, 'child'>;
export type WithoutChildrenOrChild<T> = Omit<T, 'children' | 'child'>;

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
