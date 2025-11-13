---
title: "src/app.html"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "src/app.html"
spec_type: "repository-source"
---

## 개요

이 파일은 app.html의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```html
<!doctype html>
<html lang="%paraglide.lang%">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="color-scheme" content="light" />
		%sveltekit.head%
	</head>
	<body data-sveltekit-preload-data="hover">
		<div style="display: contents">%sveltekit.body%</div>
	</body>
</html>

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
