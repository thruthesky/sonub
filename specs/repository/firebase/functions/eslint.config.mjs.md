---
title: "firebase/functions/eslint.config.mjs"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/functions/eslint.config.mjs"
spec_type: "repository-source"
---

# 목적
이 문서는 `firebase/functions/eslint.config.mjs` 파일의 전체 내용을 기록하여 SED 스펙만으로도 Sonub 프로젝트를 재구성할 수 있도록 합니다.

## 파일 정보
- 상대 경로: `firebase/functions/eslint.config.mjs`
- MIME: `text/x-java`
- 유형: 텍스트

# 원본 소스 코드
```````javascript
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

```````
