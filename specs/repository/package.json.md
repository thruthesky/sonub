---
name: package.json
description: SvelteKit 프로젝트의 npm 패키지 설정 파일. 프로젝트 의존성, 스크립트, 메타데이터를 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, npm, dependencies, scripts]
---

# package.json

## 개요
Sonub 프로젝트의 루트 package.json 파일입니다. 이 파일은:
- 프로젝트의 npm 스크립트 정의
- 개발 및 프로덕션 의존성 관리
- Node.js 버전 요구사항 명시
- SvelteKit 5, Firebase, Tailwind CSS 등 핵심 라이브러리 포함

## 소스 코드

```json
{
	"name": "sonub",
	"private": true,
	"version": "0.0.1",
	"type": "module",
	"node": ">=20.0.0",
	"scripts": {
		"dev": "vite dev",
		"build": "vite build",
		"preview": "vite preview",
		"start": "node build",
		"prepare": "svelte-kit sync",
		"check": "svelte-kit sync && svelte-check --tsconfig ./tsconfig.json",
		"check:watch": "svelte-kit sync && svelte-check --tsconfig ./tsconfig.json --watch",
		"format": "prettier --write .",
		"lint": "prettier --check  --log-level error . && eslint .",
		"test:unit": "vitest",
		"test": "npm run test:unit -- --run && npm run test:e2e",
		"test:e2e": "playwright test",
		"storybook": "storybook dev -p 6006",
		"build-storybook": "storybook build"
	},
	"devDependencies": {
		"@chromatic-com/storybook": "^4.1.2",
		"@eslint/compat": "^1.4.0",
		"@eslint/js": "^9.38.0",
		"@inlang/paraglide-js": "^2.4.0",
		"@inlang/paraglide-sveltekit": "^0.16.1",
		"@internationalized/date": "^3.10.0",
		"@lucide/svelte": "^0.544.0",
		"@playwright/test": "^1.56.1",
		"@storybook/addon-a11y": "^10.0.6",
		"@storybook/addon-docs": "^10.0.6",
		"@storybook/addon-svelte-csf": "^5.0.10",
		"@storybook/addon-vitest": "^10.0.6",
		"@storybook/sveltekit": "^10.0.6",
		"@sveltejs/adapter-node": "^5.4.0",
		"@sveltejs/kit": "^2.47.1",
		"@sveltejs/vite-plugin-svelte": "^6.2.1",
		"@tailwindcss/forms": "^0.5.10",
		"@tailwindcss/typography": "^0.5.19",
		"@tailwindcss/vite": "^4.1.14",
		"@types/node": "^22",
		"@vitest/browser-playwright": "^4.0.5",
		"bits-ui": "^2.14.3",
		"eslint": "^9.38.0",
		"eslint-config-prettier": "^10.1.8",
		"eslint-plugin-storybook": "^10.0.6",
		"eslint-plugin-svelte": "^3.12.4",
		"globals": "^16.4.0",
		"mdsvex": "^0.12.6",
		"playwright": "^1.56.1",
		"prettier": "^3.6.2",
		"prettier-plugin-svelte": "^3.4.0",
		"prettier-plugin-tailwindcss": "^0.7.1",
		"shadcn-svelte": "^1.0.10",
		"storybook": "^10.0.6",
		"svelte": "^5.41.0",
		"svelte-check": "^4.3.3",
		"tailwindcss": "^4.1.14",
		"typescript": "^5.9.3",
		"typescript-eslint": "^8.46.1",
		"vite": "^7.1.10",
		"vite-plugin-devtools-json": "^1.0.0",
		"vitest": "^4.0.5",
		"vitest-browser-svelte": "^2.0.1"
	},
	"dependencies": {
		"clsx": "^2.1.1",
		"firebase": "^12.5.0",
		"lucide-svelte": "^0.553.0",
		"tailwind-merge": "^3.3.1"
	}
}
```

## 주요 설정

### 프로젝트 메타데이터
- **name**: sonub
- **version**: 0.0.1
- **type**: module (ES 모듈 사용)
- **node**: >=20.0.0 (Node.js 20 이상 필수)

### 주요 스크립트
- **dev**: 개발 서버 시작 (Vite)
- **build**: 프로덕션 빌드
- **check**: TypeScript 및 Svelte 타입 체크
- **lint**: ESLint 및 Prettier 린트 검사
- **test**: 유닛 및 E2E 테스트 실행
- **storybook**: Storybook 개발 서버 (포트 6006)

### 핵심 의존성
- **@sveltejs/kit**: ^2.47.1 (SvelteKit 프레임워크)
- **svelte**: ^5.41.0 (Svelte 5 runes 사용)
- **firebase**: ^12.5.0 (Firebase SDK)
- **tailwindcss**: ^4.1.14 (Tailwind CSS v4)
- **shadcn-svelte**: ^1.0.10 (UI 컴포넌트 라이브러리)
- **@inlang/paraglide-sveltekit**: ^0.16.1 (다국어 지원)
- **vitest**: ^4.0.5 (테스트 프레임워크)
- **playwright**: ^1.56.1 (E2E 테스트)
- **storybook**: ^10.0.6 (컴포넌트 문서화)

## 관련 파일
- [tsconfig.json](./tsconfig.json.md) - TypeScript 설정
- [vite.config.ts](./vite.config.ts.md) - Vite 빌드 설정
- [svelte.config.js](./svelte.config.js.md) - Svelte 설정
- [eslint.config.js](./eslint.config.js.md) - ESLint 설정
