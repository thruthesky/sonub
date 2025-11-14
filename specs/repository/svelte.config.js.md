---
name: svelte.config.js
description: svelte.config 설정 파일
version: 1.0.0
type: javascript
category: root-configuration
original_path: svelte.config.js
---

# svelte.config.js

## 개요

**파일 경로**: `svelte.config.js`
**파일 타입**: javascript
**카테고리**: root-configuration

svelte.config 설정 파일

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
