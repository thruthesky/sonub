---
name: package.json
description: package 설정 파일
version: 1.0.0
type: configuration
category: root-configuration
original_path: package.json
---

# package.json

## 개요

**파일 경로**: `package.json`
**파일 타입**: configuration
**카테고리**: root-configuration

package 설정 파일

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
		"@types/node": "^22.19.1",
		"@vitest/browser-playwright": "^4.0.5",
		"bits-ui": "^2.14.3",
		"eslint": "^9.38.0",
		"eslint-config-prettier": "^10.1.8",
		"eslint-plugin-storybook": "^10.0.6",
		"eslint-plugin-svelte": "^3.12.4",
		"glob": "^11.0.3",
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
		"ts-node": "^10.9.2",
		"tsx": "^4.20.6",
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
		"svelte-sonner": "^1.0.6",
		"tailwind-merge": "^3.3.1"
	}
}

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
