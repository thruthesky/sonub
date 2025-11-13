---
name: hooks.server.ts
description: SvelteKit 서버 훅
version: 1.0.0
type: typescript
category: server
tags: [sveltekit, hooks, i18n, paraglide, middleware]
---

# hooks.server.ts

## 개요
Paraglide i18n 미들웨어를 설정하여 요청의 쿠키/헤더에서 사용자 로케일을 자동 감지하고 HTML lang 속성을 설정합니다.

## 주요 기능
- 요청의 쿠키/헤더에서 로케일 자동 감지
- 감지된 로케일을 요청 컨텍스트에 설정
- HTML lang 속성 자동 설정 (%paraglide.lang% 치환)

## 사용 예시
```typescript
// src/app.html
<html lang="%paraglide.lang%">

// 서버에서 자동으로 치환
<html lang="ko"> // 또는 "en", "ja", "zh"
```
