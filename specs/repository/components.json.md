---
title: "components.json"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "components.json"
spec_type: "repository-source"
---

## 개요

이 파일은 components.json의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

- 2025-11-13: 스펙 문서 생성/업데이트
