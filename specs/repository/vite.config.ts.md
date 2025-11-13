---
name: vite.config.ts
description: Vite 빌드 도구 설정 파일. 플러그인, 서버 설정, 테스트 환경을 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, vite, build, plugins, test]
---

# vite.config.ts

## 개요
Vite 빌드 도구의 설정 파일입니다. 이 파일은:
- SvelteKit, Tailwind CSS, Paraglide 플러그인 통합
- 개발 서버 파일 서빙 권한 설정
- Vitest 테스트 환경 구성 (클라이언트/서버 분리)

## 소스 코드

```typescript
import { paraglideVitePlugin } from '@inlang/paraglide-js';
import devtoolsJson from 'vite-plugin-devtools-json';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vitest/config';
import { playwright } from '@vitest/browser-playwright';
import { sveltekit } from '@sveltejs/kit/vite';

export default defineConfig({
	plugins: [
		tailwindcss(),
		sveltekit(),
		devtoolsJson(),
		paraglideVitePlugin({
			project: './project.inlang',
			outdir: './src/lib/paraglide',
			outputStructure: 'locale-modules'
		})
	],
	server: {
		fs: {
			// shared 폴더를 Vite가 서빙할 수 있도록 허용
			allow: ['..']
		}
	},
	test: {
		expect: { requireAssertions: true },
		projects: [
			{
				extends: './vite.config.ts',
				test: {
					name: 'client',
					browser: {
						enabled: true,
						provider: playwright(),
						instances: [{ browser: 'chromium', headless: true }]
					},
					include: ['src/**/*.svelte.{test,spec}.{js,ts}'],
					exclude: ['src/lib/server/**']
				}
			},
			{
				extends: './vite.config.ts',
				test: {
					name: 'server',
					environment: 'node',
					include: ['src/**/*.{test,spec}.{js,ts}'],
					exclude: ['src/**/*.svelte.{test,spec}.{js,ts}']
				}
			}
		]
	}
});
```

## 주요 설정

### 플러그인 (Plugins)
1. **tailwindcss()**: Tailwind CSS v4 Vite 플러그인
2. **sveltekit()**: SvelteKit Vite 플러그인
3. **devtoolsJson()**: JSON 개발 도구 플러그인
4. **paraglideVitePlugin**: 다국어(i18n) 지원 플러그인
   - project: `./project.inlang` - Inlang 프로젝트 경로
   - outdir: `./src/lib/paraglide` - 생성 파일 출력 디렉토리
   - outputStructure: `locale-modules` - 언어별 모듈 구조

### 서버 설정 (Server)
- **fs.allow**: `['..']` - 상위 디렉토리(`shared` 폴더) 접근 허용

### 테스트 설정 (Test)
- **expect.requireAssertions**: true - 모든 테스트에 assertion 필수

#### 클라이언트 테스트 프로젝트
- **name**: `client`
- **browser**: Playwright (Chromium, headless)
- **include**: `src/**/*.svelte.{test,spec}.{js,ts}` - Svelte 컴포넌트 테스트
- **exclude**: `src/lib/server/**` - 서버 코드 제외

#### 서버 테스트 프로젝트
- **name**: `server`
- **environment**: `node` - Node.js 환경
- **include**: `src/**/*.{test,spec}.{js,ts}` - 일반 TypeScript 테스트
- **exclude**: `src/**/*.svelte.{test,spec}.{js,ts}` - Svelte 테스트 제외

## 관련 파일
- [package.json](./package.json.md) - Vite 및 플러그인 의존성
- [svelte.config.js](./svelte.config.js.md) - SvelteKit 설정
- [tsconfig.json](./tsconfig.json.md) - TypeScript 설정
- [playwright.config.ts](./playwright.config.ts.md) - E2E 테스트 설정
