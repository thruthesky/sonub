---
title: tsconfig.json
type: config
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 tsconfig.json의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
