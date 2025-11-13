---
name: functions/eslint.config.mjs
description: Firebase Cloud Functions의 ESLint 설정 파일. TypeScript 코드 품질 및 스타일 규칙을 정의합니다.
version: 1.0.0
type: configuration
category: functions-config
tags: [configuration, eslint, functions, typescript, lint]
---

# functions/eslint.config.mjs

## 개요
Firebase Cloud Functions의 ESLint 설정 파일입니다. 이 파일은:
- TypeScript 전용 린트 규칙 적용
- 빌드 출력 및 테스트 파일 제외
- Node.js 환경 전역 변수 설정
- Flat Config 형식 사용 (최신 ESLint)

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

## 주요 설정

### 무시 패턴 (ignores)
- **lib/**: TypeScript 컴파일 출력 디렉토리
- **node_modules/**: npm 의존성 폴더
- **test/**: 테스트 파일 (별도 규칙 적용 가능)

### 기본 규칙
- **js.configs.recommended**: JavaScript 기본 권장 규칙

### TypeScript 파일 설정

#### 대상 파일
- **files**: `**/*.ts` - 모든 TypeScript 파일

#### 언어 옵션
- **parser**: TypeScript 파서
- **parserOptions**:
  - project: `./tsconfig.json` - TypeScript 설정 파일
  - tsconfigRootDir: 현재 디렉토리 (동적 계산)
  - sourceType: `module` - ES 모듈
- **globals**: Node.js 환경 전역 변수

#### 플러그인 및 규칙
- **plugins**: TypeScript ESLint 플러그인
- **rules**: TypeScript 권장 규칙 적용

## Flat Config 형식
이 파일은 ESLint의 최신 Flat Config 형식을 사용합니다:
- 배열 기반 설정
- 레거시 `.eslintrc` 형식 대체
- 더 명확한 설정 구조
- 플러그인 직접 import

## 관련 파일
- [package.json](./package.json.md) - ESLint 의존성 및 lint 스크립트
- [tsconfig.json](./tsconfig.json.md) - TypeScript 설정 (ESLint에서 참조)
- [tsconfig.dev.json](./tsconfig.dev.json.md) - 개발용 TypeScript 설정
