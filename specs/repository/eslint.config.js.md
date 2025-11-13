---
name: eslint.config.js
description: ESLint 린트 도구 설정 파일. JavaScript, TypeScript, Svelte 코드 스타일 및 품질 규칙을 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, eslint, lint, code-quality]
---

# eslint.config.js

## 개요
ESLint 린트 도구의 설정 파일입니다. 이 파일은:
- JavaScript, TypeScript, Svelte 파일 린트 규칙 적용
- Prettier와 통합하여 코드 포맷 일관성 유지
- Storybook 플러그인 통합
- Firebase 및 specs 폴더 검사 제외

## 소스 코드

```javascript
// For more info, see https://github.com/storybookjs/eslint-plugin-storybook#configuration-flat-config-format
import storybook from 'eslint-plugin-storybook';

import prettier from 'eslint-config-prettier';
import { fileURLToPath } from 'node:url';
import { includeIgnoreFile } from '@eslint/compat';
import js from '@eslint/js';
import svelte from 'eslint-plugin-svelte';
import { defineConfig } from 'eslint/config';
import globals from 'globals';
import ts from 'typescript-eslint';
import svelteConfig from './svelte.config.js';

const gitignorePath = fileURLToPath(new URL('./.gitignore', import.meta.url));

export default defineConfig(
	includeIgnoreFile(gitignorePath),
	// firebase 및 specs 폴더 eslint 검사 제외
	{
		ignores: ['firebase/**', 'specs/**']
	},
	js.configs.recommended,
	...ts.configs.recommended,
	...svelte.configs.recommended,
	prettier,
	...svelte.configs.prettier,
	{
		languageOptions: {
			globals: { ...globals.browser, ...globals.node }
		},
		rules: {
			// typescript-eslint strongly recommend that you do not use the no-undef lint rule on TypeScript projects.
			// see: https://typescript-eslint.io/troubleshooting/faqs/eslint/#i-get-errors-from-the-no-undef-rule-about-global-variables-not-being-defined-even-though-there-are-no-typescript-errors
			'no-undef': 'off'
		}
	},
	{
		files: ['**/*.svelte', '**/*.svelte.ts', '**/*.svelte.js'],
		languageOptions: {
			parserOptions: {
				projectService: true,
				extraFileExtensions: ['.svelte'],
				parser: ts.parser,
				svelteConfig
			}
		}
	}
);
```

## 주요 설정

### 무시 패턴 (Ignores)
- **.gitignore 파일**: Git에서 무시하는 모든 파일
- **firebase/**: Firebase 관련 파일 (별도 ESLint 설정 사용)
- **specs/**: SED 문서 폴더 (Markdown 파일)

### 적용 규칙 (Configs)
1. **js.configs.recommended**: JavaScript 기본 권장 규칙
2. **ts.configs.recommended**: TypeScript 권장 규칙
3. **svelte.configs.recommended**: Svelte 권장 규칙
4. **prettier**: Prettier 코드 포맷팅 규칙
5. **svelte.configs.prettier**: Svelte용 Prettier 규칙

### 언어 옵션
- **globals**: browser + node 환경 전역 변수

### 규칙 (Rules)
- **no-undef**: `off` - TypeScript에서는 불필요 (타입 체크로 대체)

### Svelte 파일 전용 설정
- **files**: `**/*.svelte`, `**/*.svelte.ts`, `**/*.svelte.js`
- **parserOptions**:
  - projectService: true - TypeScript 프로젝트 서비스 활성화
  - extraFileExtensions: ['.svelte'] - .svelte 확장자 인식
  - parser: TypeScript 파서
  - svelteConfig: svelte.config.js 참조

## 관련 파일
- [package.json](./package.json.md) - ESLint 플러그인 의존성
- [svelte.config.js](./svelte.config.js.md) - Svelte 설정 참조
- [tsconfig.json](./tsconfig.json.md) - TypeScript 설정
- [.gitignore](../.gitignore) - Git 무시 파일 목록
