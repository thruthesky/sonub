---
title: components.json
type: config
path: components.json
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `components.json`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
