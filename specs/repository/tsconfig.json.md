---
name: tsconfig.json
description: tsconfig 설정 파일
version: 1.0.0
type: configuration
category: root-configuration
original_path: tsconfig.json
---

# tsconfig.json

## 개요

**파일 경로**: `tsconfig.json`
**파일 타입**: configuration
**카테고리**: root-configuration

tsconfig 설정 파일

## 소스 코드

```json
{
	"extends": "./.svelte-kit/tsconfig.json",
	"compilerOptions": {
		"allowImportingTsExtensions": true,
		"allowJs": true,
		"checkJs": true,
		"esModuleInterop": true,
		"forceConsistentCasingInFileNames": true,
		"resolveJsonModule": true,
		"skipLibCheck": true,
		"sourceMap": true,
		"strict": true,
		"moduleResolution": "bundler"
	},
	"exclude": ["src/paraglide/**/*"]
	// Path aliases are handled by https://svelte.dev/docs/kit/configuration#alias
	// except $lib which is handled by https://svelte.dev/docs/kit/configuration#files
	//
	// To make changes to top-level options such as include and exclude, we recommend extending
	// the generated config; see https://svelte.dev/docs/kit/configuration#typescript
}

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
