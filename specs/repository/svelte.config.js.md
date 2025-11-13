---
name: svelte.config.js
description: SvelteKit 프로젝트의 핵심 설정 파일. 어댑터, 전처리기, 경로 별칭, 경고 처리를 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, sveltekit, svelte, adapter, preprocessor]
---

# svelte.config.js

## 개요
SvelteKit의 핵심 설정 파일입니다. 이 파일은:
- Node.js 어댑터 설정 (프로덕션 배포용)
- Vite 및 mdsvex 전처리기 설정
- `$shared` 경로 별칭 정의
- Tailwind CSS v4 관련 경고 무시 처리

## 소스 코드

```javascript
import { mdsvex } from 'mdsvex';
import adapter from '@sveltejs/adapter-node';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	// Consult https://svelte.dev/docs/kit/integrations
	// for more information about preprocessors
	preprocess: [vitePreprocess(), mdsvex()],
	kit: {
		adapter: adapter(),
		alias: {
			$shared: './shared'
		}
	},
	extensions: ['.svelte', '.svx'],
	// Tailwind CSS v4의 @import 'tailwindcss' reference 사용 시 발생하는
	// css-unused-selector 경고를 무시합니다.
	// 이 경고는 실제 빌드나 런타임에는 영향을 주지 않습니다.
	onwarn: (warning, handler) => {
		// css-unused-selector 경고 무시
		// 둘 다 차단
		if (
			warning.code === 'css-unused-selector' ||
			warning.code === 'css_unused_selector'
		) return;
		// 다른 경고는 정상적으로 처리
		handler(warning);
	}
};

export default config;
```

## 주요 설정

### 전처리기 (Preprocessors)
- **vitePreprocess()**: TypeScript, PostCSS 등 Vite 기반 전처리
- **mdsvex()**: Markdown + Svelte 파일(.svx) 지원

### Kit 설정
- **adapter**: `adapter-node()` - Node.js 서버 환경용 어댑터
- **alias**: `$shared: './shared'` - 공유 코드 경로 별칭

### 파일 확장자
- **extensions**: `['.svelte', '.svx']` - Svelte 및 MDSveX 파일 인식

### 경고 처리 (onwarn)
- **css-unused-selector**: Tailwind CSS v4 사용 시 발생하는 무해한 경고 무시
- **css_unused_selector**: 동일 경고의 언더스코어 버전도 무시
- 나머지 경고는 정상적으로 표시

## 관련 파일
- [vite.config.ts](./vite.config.ts.md) - Vite 빌드 설정
- [package.json](./package.json.md) - mdsvex, adapter-node 의존성
- [tsconfig.json](./tsconfig.json.md) - TypeScript 설정
