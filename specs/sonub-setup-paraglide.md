---
name: sonub-setup-paraglide
title: Paraglide 최소 설정 가이드
version: 1.0.0
description: SvelteKit 5 프로젝트에서 Paraglide i18n을 최소 설정으로 적용하는 절차
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-11
updated: 2025-11-11
step: 15
priority: "**"
dependencies:
  - sonub-setup-svelte.md
related:
  - sonub-i18n-paraglide.md
tags:
  - i18n
  - paraglide
  - setup
  - localization
  - configuration
---

# Paraglide 최소 설정 가이드

본 문서는 SvelteKit 기반 sonub 프로젝트에서 Paraglide i18n을 **필수 구성 요소만**으로 적용하는 방법을 정의합니다. 실제 번역 작성·사용 규칙은 `sonub-i18n-paraglide.md`에서 설명하며, 이 문서는 빌드/런타임 설정만을 다룹니다.

---

## 1. 개요

- 목적: Paraglide를 가장 단순한 설정으로 연결해도 전체 앱이 정상 동작하도록 구성한다.
- 범위: 프로젝트 설정 파일, Vite 플러그인, 서버 훅, HTML 템플릿, 자동 생성 산출물의 관리 방법.
- 사용 가이드 및 번역 키 정책: [`sonub-i18n-paraglide.md`](./sonub-i18n-paraglide.md) 참고.

---

## 2. 요구 사항

### 2.1 필수 패키지

`package.json`의 devDependencies에 아래 패키지가 존재해야 한다.

```json
{
  "@inlang/paraglide-js": "^2.4.0",
  "@inlang/paraglide-sveltekit": "^0.16.1"
}
```

### 2.2 선행 조건

1. `sonub-setup-svelte.md`에 정의된 SvelteKit 기본 설정 완료.
2. Node.js 20 및 npm 10 이상 사용.
3. `messages/{locale}.json` 파일이 4개 언어(en, ko, ja, zh) 모두 존재.

---

## 3. 최소 파일 구성

```
project.inlang/settings.json   # Paraglide 프로젝트 설정
vite.config.ts                 # paraglideVitePlugin 단일 호출
src/hooks.server.ts            # paraglideMiddleware 적용
src/app.html                   # %paraglide.lang% 치환
src/lib/paraglide/*            # 자동 생성 산출물 (수정 금지)
messages/*.json                # 번역 원본
```

- `src/hooks.ts` 등 추가 클라이언트 훅은 사용하지 않는다.
- 타입 정의 파일(`src/types/paraglide.d.ts`)을 따로 두지 않고, Paraglide가 생성한 모듈 타입만 활용한다.

---

## 4. 설정 단계

### 4.1 `project.inlang/settings.json`

최소 구성만 포함한다.

```json
{
  "$schema": "https://inlang.com/schema/project-settings",
  "modules": [
    "https://cdn.jsdelivr.net/npm/@inlang/plugin-message-format@4/dist/index.js"
  ],
  "plugin.inlang.messageFormat": {
    "pathPattern": "./messages/{locale}.json"
  },
  "baseLocale": "en",
  "locales": ["en", "ko", "ja", "zh"]
}
```

- 추가 플러그인(예: `@inlang/plugin-m-function-matcher`)을 제거해 의존성을 최소화한다.

### 4.2 `vite.config.ts`

`paraglideVitePlugin`을 단 한 번 호출하며 `outputStructure`만 지정한다.

```ts
import { paraglideVitePlugin } from '@inlang/paraglide-js';

export default defineConfig({
  plugins: [
    tailwindcss(),
    sveltekit(),
    devtoolsJson(),
    paraglideVitePlugin({
      project: './project.inlang',
      outdir: './src/lib/paraglide',
      outputStructure: 'locale-modules'
    })
  ]
});
```

- 별도 커스텀 전략이나 옵션을 추가하지 않는다.
- 개발·빌드 모두 동일 설정을 사용한다.

### 4.3 `src/hooks.server.ts`

서버 훅에서 Paraglide 미들웨어만 연결한다.

```ts
import type { Handle } from '@sveltejs/kit';
import { paraglideMiddleware } from '$lib/paraglide/server';

const handleParaglide: Handle = ({ event, resolve }) =>
  paraglideMiddleware(event.request, ({ request, locale }) => {
    event.request = request;
    return resolve(event, {
      transformPageChunk: ({ html }) => html.replace('%paraglide.lang%', locale)
    });
  });

export const handle: Handle = handleParaglide;
```

- 추가 핸들 체인 없이 이 미들웨어만 등록한다.

### 4.4 `src/app.html`

HTML 루트에 `%paraglide.lang%` 플레이스홀더만 배치한다.

```html
<!doctype html>
<html lang="%paraglide.lang%">
  <head>...</head>
  <body>...</body>
</html>
```

### 4.5 자동 생성 산출물

- `src/lib/paraglide/` 디렉터리는 Paraglide 플러그인이 자동으로 갱신한다.
- 추가 수동 타입 선언 파일을 두지 않으며, `import { m } from '$lib/paraglide/messages'` 형태로 바로 사용한다.

---

## 5. 작업 절차

1. `messages/*.json`에서 키를 추가·수정한다.
2. 개발 서버 또는 `paraglideVitePlugin`이 자동으로 재컴파일한다.
3. 필요 시 수동으로 재컴파일:
   ```bash
   npx @inlang/paraglide-js compile --project ./project.inlang --outdir ./src/lib/paraglide
   ```
4. `npm run check`로 타입 및 Svelte 검사를 수행한다.

---

## 6. 검증 체크리스트

- `project.inlang/settings.json`에 message-format 모듈만 존재하는가?
- `vite.config.ts`에 `paraglideVitePlugin`이 한 번만 등장하는가?
- `src/hooks.server.ts`에서 `paraglideMiddleware`만 래핑했는가?
- `src/app.html`의 `lang` 속성이 `%paraglide.lang%`로 설정되어 있는가?
- `src/lib/paraglide/` 외에 수동 타입/유틸 파일이 없는가?

위 항목이 모두 충족되면 최소 설정이 유지된 것이다.

---

## 7. 관련 문서

- **번역 키 작성, 다국어 사용 흐름, 로케일 전략 설명**: [`sonub-i18n-paraglide.md`](./sonub-i18n-paraglide.md)

---

## 8. 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-11 | Codex Agent | Paraglide 최소 설정 가이드를 신규 작성하고 message-format 단일 모듈, 단일 Vite 플러그인, 서버 훅/HTML 치환 흐름을 문서화함. |
