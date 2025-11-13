---
title: utils.ts
type: typescript
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 utils.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
