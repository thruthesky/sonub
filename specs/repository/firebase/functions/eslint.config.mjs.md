---
name: eslint.config.mjs
description: eslint.config Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/eslint.config.mjs
---

# eslint.config.mjs

## 개요

**파일 경로**: `firebase/functions/eslint.config.mjs`
**파일 타입**: firebase-function
**카테고리**: cloud-function

eslint.config Cloud Function

## 소스 코드

```javascript
import js from '@eslint/js';
import globals from 'globals';
import tsPlugin from '@typescript-eslint/eslint-plugin';
import tsParser from '@typescript-eslint/parser';
import { fileURLToPath } from 'node:url';

const tsconfigRootDir = fileURLToPath(new URL('.', import.meta.url));

export default [
	{
		ignores: ['lib/**', 'node_modules/**', 'test/**']
	},
	js.configs.recommended,
	{
		files: ['**/*.ts'],
		languageOptions: {
			parser: tsParser,
			parserOptions: {
				project: './tsconfig.json',
				tsconfigRootDir,
				sourceType: 'module'
			},
			globals: {
				...globals.node
			}
		},
		plugins: {
			'@typescript-eslint': tsPlugin
		},
		rules: {
			...tsPlugin.configs.recommended.rules
		}
	}
];

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
