---
name: components.json
description: shadcn-svelte UI 컴포넌트 라이브러리 설정 파일. 스타일, Tailwind 설정, 경로 별칭을 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, shadcn-svelte, ui, tailwind]
---

# components.json

## 개요
shadcn-svelte 컴포넌트 라이브러리의 설정 파일입니다. 이 파일은:
- UI 컴포넌트 스타일 테마 정의
- Tailwind CSS 설정 경로 지정
- 컴포넌트 및 유틸리티 경로 별칭 설정

## 소스 코드

```json
{
	"$schema": "https://shadcn-svelte.com/schema.json",
	"style": "default",
	"tailwind": {
		"config": "tailwind.config.js",
		"css": "src/app.css",
		"baseColor": "slate"
	},
	"aliases": {
		"components": "$lib/components",
		"utils": "$lib/utils"
	}
}
```

## 주요 설정

### 스키마
- **$schema**: `https://shadcn-svelte.com/schema.json` - shadcn-svelte 공식 스키마

### 스타일 설정
- **style**: `default` - 기본 스타일 테마 사용

### Tailwind 설정
- **config**: `tailwind.config.js` - Tailwind 설정 파일 경로
- **css**: `src/app.css` - 전역 CSS 파일 경로
- **baseColor**: `slate` - 기본 색상 팔레트 (slate gray)

### 경로 별칭
- **components**: `$lib/components` - UI 컴포넌트 저장 경로
- **utils**: `$lib/utils` - 유틸리티 함수 저장 경로

## 관련 파일
- [package.json](./package.json.md) - shadcn-svelte 의존성 포함
- [src/app.css](../src/app.css.md) - 전역 Tailwind CSS 파일
- [src/lib/utils.ts](../src/lib/utils.ts.md) - 유틸리티 함수
