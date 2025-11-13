---
title: "src/hooks.server.ts"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "src/hooks.server.ts"
spec_type: "repository-source"
---

## 개요

이 파일은 hooks.server.ts의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * SvelteKit 서버 훅
 *
 * Paraglide i18n 미들웨어:
 * - 요청의 쿠키/헤더에서 사용자 로케일 자동 감지
 * - 감지된 로케일을 요청 컨텍스트에 설정
 * - HTML lang 속성 자동 설정
 */

import type { Handle } from '@sveltejs/kit';
import { paraglideMiddleware } from '$lib/paraglide/server';

const handleParaglide: Handle = ({ event, resolve }) =>
	paraglideMiddleware(event.request, ({ request, locale }) => {
		event.request = request;

		return resolve(event, {
			transformPageChunk: ({ html }) => html.replace('%paraglide.lang%', locale)
		});
	});

export const handle: Handle = handleParaglide;

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
