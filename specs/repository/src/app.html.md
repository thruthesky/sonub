---
name: app.html
description: SvelteKit 애플리케이션의 HTML 템플릿 파일
version: 1.0.0
type: html
category: template
tags: [sveltekit, html, template, i18n, paraglide]
---

# app.html

## 개요
이 파일은 SvelteKit 애플리케이션의 기본 HTML 템플릿입니다. 모든 페이지에 공통으로 적용되는 HTML 구조를 정의하며, Paraglide i18n과 통합하여 다국어 지원을 제공합니다.

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

## 주요 기능

### Paraglide i18n 통합
- **동적 언어 속성**: `lang="%paraglide.lang%"`
  - 서버 훅 (`hooks.server.ts`)에서 실제 언어 코드로 치환됨
  - 예: `<html lang="ko">`, `<html lang="en">`

### 메타 태그
- **UTF-8 인코딩**: `charset="utf-8"`
- **반응형 뷰포트**: `width=device-width, initial-scale=1`
- **라이트 모드 전용**: `color-scheme="light"` (다크 모드 없음)

### SvelteKit 플레이스홀더
- **%sveltekit.head%**: 페이지별 head 태그 내용 삽입
  - 메타 태그, 타이틀, 링크 등
- **%sveltekit.body%**: 페이지별 body 태그 내용 삽입
  - Svelte 컴포넌트가 렌더링되는 영역

### 데이터 프리로딩
- **data-sveltekit-preload-data="hover"**
  - 링크에 마우스를 올리면 데이터를 미리 로드
  - 페이지 전환 속도 향상

### display: contents
- **투명 래퍼**: `<div style="display: contents">`
  - DOM 트리에는 존재하지만 레이아웃에는 영향을 주지 않음
  - Svelte 컴포넌트가 body의 직접 자식처럼 동작

## 다국어 처리 흐름

1. **사용자 요청** → 서버 훅 (`hooks.server.ts`)
2. **언어 감지** → 쿠키/헤더에서 로케일 추출
3. **HTML 치환** → `%paraglide.lang%` → `ko`, `en`, `ja`, `zh`
4. **응답 전송** → 브라우저에 다국어 HTML 전달
