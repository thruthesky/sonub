---
name: sonub-i18n-paraglide
title: Paraglide-JS 기반 i18n 다국어 지원 시스템
version: 1.2.0
description: SvelteKit 5에서 Paraglide-JS를 사용한 다국어(i18n) 지원 시스템 구현 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
step: 15
priority: **
dependencies:
  - sonub-setup-svelte.md
tags:
  - i18n
  - internationalization
  - paraglide-js
  - localization
  - multi-language
  - inlang
  - sveltekit5
  - svelte5
  - typescript
---

# Paraglide-JS 기반 i18n 다국어 지원 시스템

## 개요 (Overview)

Paraglide-JS는 SvelteKit 프로젝트를 위한 현대적인 다국어 지원 라이브러리입니다. 이 명세서는 SvelteKit 5 환경에서 Paraglide-JS를 활용하여 다국어 지원 시스템을 구현하는 방법을 정의합니다.

### 핵심 기능

- **자동 메시지 생성**: Inlang 메시지 형식의 JSON 파일에서 TypeScript/JavaScript 함수 자동 생성
- **타입 안전성**: 생성된 메시지 함수로 컴파일 타임 타입 검증
- **유연한 로케일 전략**: 쿠키, localStorage, 전역 변수, URL, 선호 언어 등 다양한 전략 지원
- **SSR 지원**: 서버 사이드 렌더링 환경에서의 완전한 지원
- **성능 최적화**: 트리 쉐이킹으로 불필요한 코드 제거

### 지원 언어

- **기본 언어 (Base Locale)**: `en` (영어)
- **지원 언어**: `ko` (한국어), `ja` (일본어), `zh` (중국어)

---

## 요구사항 (Requirements)

### 설치된 패키지

```bash
@inlang/paraglide-sveltekit: ^0.16.1
```

### 설정 파일

**`project.inlang/settings.json`**
```json
{
  "baseLocale": "en",
  "locales": ["en", "ko", "ja", "zh"],
  "plugin.inlang.messageFormat": {
    "pathPattern": "./messages/{locale}.json"
  }
}
```

### 번역 파일

**`messages/{locale}.json`**
- `messages/en.json` - 영어 (기본)
- `messages/ko.json` - 한국어
- `messages/ja.json` - 일본어
- `messages/zh.json` - 중국어

---

## 빠른 시작 (Quick Start)

### 1. 컴포넌트에서 번역 사용

```svelte
<script lang="ts">
  import { m } from '$lib/paraglide/messages';
</script>

<h1>{m.navHome()}</h1>
<p>{m.authWelcomeUser({ name: '사용자' })}</p>
```

### 2. 언어 변경

```svelte
<script lang="ts">
  import { getLocale, setLocale, locales } from '$lib/paraglide/runtime';

  function changeLanguage(newLocale) {
    setLocale(newLocale); // 자동으로 쿠키 저장 및 페이지 새로고침
  }
</script>

<select value={getLocale()} onchange={(e) => changeLanguage(e.target.value)}>
  {#each locales as locale}
    <option value={locale}>{locale}</option>
  {/each}
</select>
```

### 3. 현재 로케일 가져오기

```typescript
import { getLocale } from '$lib/paraglide/runtime';

const currentLocale = getLocale(); // 'en', 'ko', 'ja', 'zh' 중 하나
```

---

## 파일 구조 (File Structure)

```
프로젝트/
├── messages/                      # 번역 파일 (수정 가능)
│   ├── en.json                   #   영어 (기본)
│   ├── ko.json                   #   한국어
│   ├── ja.json                   #   일본어
│   └── zh.json                   #   중국어
│
├── project.inlang/               # Paraglide 설정
│   └── settings.json             #   로케일 및 경로 설정
│
└── src/
    ├── hooks.server.ts           # 서버 훅 (로케일 자동 감지)
    └── lib/paraglide/            # 자동 생성 파일 (수정 금지!)
        ├── messages.js           #   타입 안전 메시지 함수
        ├── runtime.js            #   런타임 유틸리티
        └── server.js             #   서버 미들웨어
```

**⚠️ 중요**: `src/lib/paraglide/` 폴더의 파일들은 Paraglide가 자동으로 생성하므로 절대 수정하지 마세요.

---

## 핵심 원칙 (Core Principles)

### 1. 키 이름은 항상 camelCase

Paraglide는 JSON 키를 camelCase 함수로 변환합니다.

```json
✅ { "navHome": "홈" }
✅ { "authLogin": "로그인" }
❌ { "nav_home": "홈" }
❌ { "auth-login": "로그인" }
```

### 2. 모든 언어 파일에 동일한 키 유지

누락된 키가 있으면 빌드 시 에러가 발생합니다.

```json
// messages/ko.json
{ "greeting": "안녕하세요" }

// messages/en.json
{ "greeting": "Hello" }

// messages/ja.json
{ "greeting": "こんにちは" }

// messages/zh.json
{ "greeting": "你好" }
```

### 3. 자동 생성 파일은 수정 금지

`src/lib/paraglide/` 폴더는 Paraglide가 자동으로 생성하고 관리합니다.

**✅ 수정 가능**: `messages/*.json`
**❌ 수정 금지**: `src/lib/paraglide/*`

---

## 키 명명 규칙 (Naming Conventions)

카테고리별 접두사를 사용하여 체계적으로 관리합니다.

```json
{
  "navHome": "홈",                    // 네비게이션
  "navAbout": "소개",
  "navProducts": "제품",
  "navContact": "연락처",

  "authLogin": "로그인",              // 인증
  "authSignup": "회원가입",
  "authLogout": "로그아웃",
  "authWelcomeUser": "환영합니다, {name}님!",

  "profileNickname": "닉네임",        // 프로필
  "profileEmail": "이메일",
  "profileGender": "성별",
  "profilePicture": "프로필 사진",

  "commonSave": "저장",               // 공통
  "commonCancel": "취소",
  "commonLoading": "로딩 중...",
  "commonError": "오류가 발생했습니다",

  "testUserList": "테스트 사용자 목록",  // 테스트
  "testUserCreate": "테스트 사용자 생성"
}
```

---

## 작동 원리 (How It Works)

### 서버 사이드 (Server-Side)

**`src/hooks.server.ts`**

```typescript
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

**자동 처리 작업:**
1. 쿠키에서 `PARAGLIDE_LOCALE` 읽기
2. Accept-Language 헤더 확인
3. 기본 로케일(en)로 폴백
4. HTML lang 속성 설정

### 클라이언트 사이드 (Client-Side)

**자동 감지:**
- 페이지 로드 시 쿠키의 `PARAGLIDE_LOCALE` 값 사용
- 쿠키가 없으면 기본 로케일(en) 사용

**언어 변경:**
```typescript
setLocale('ko'); // 자동으로 쿠키 저장 (1년 유효) + 페이지 새로고침
```

**로케일 전략 순서:**
```
쿠키 (PARAGLIDE_LOCALE)
  ↓ (없으면)
Accept-Language 헤더
  ↓ (없으면)
기본 로케일 (en)
```

---

## 새 번역 추가하기 (Adding New Translations)

### 1단계: 메시지 파일에 키 추가

**`messages/ko.json`**
```json
{
  "myNewKey": "새로운 메시지",
  "greetingWithParam": "안녕하세요, {name}님!"
}
```

**`messages/en.json`**
```json
{
  "myNewKey": "New message",
  "greetingWithParam": "Hello, {name}!"
}
```

**`messages/ja.json`**
```json
{
  "myNewKey": "新しいメッセージ",
  "greetingWithParam": "こんにちは、{name}さん!"
}
```

**`messages/zh.json`**
```json
{
  "myNewKey": "新消息",
  "greetingWithParam": "你好，{name}！"
}
```

### 2단계: Paraglide 재컴파일

```bash
npm run dev  # 개발 서버가 자동으로 재컴파일
```

### 3단계: 컴포넌트에서 사용

```svelte
<script lang="ts">
  import { m } from '$lib/paraglide/messages';
</script>

<p>{m.myNewKey()}</p>
<p>{m.greetingWithParam({ name: '홍길동' })}</p>
```

---

## 파라미터 사용 (Using Parameters)

### 단일 파라미터

```json
{
  "welcome": "환영합니다, {name}님!"
}
```

```svelte
{m.welcome({ name: '홍길동' })}
```

### 복수 파라미터

```json
{
  "notification": "{count}개의 새 메시지가 있습니다. {sender}님이 보냈습니다."
}
```

```svelte
{m.notification({ count: 5, sender: '김철수' })}
```

---

## 주의사항 (Important Notes)

### ✅ 해야 할 것

1. **키 이름은 camelCase로 작성**: `myKey` (O), `my_key` (X)
2. **모든 로케일에 동일한 키 유지**: 누락된 키가 있으면 빌드 에러
3. **타입 안전성 활용**: TypeScript가 자동으로 타입 체크
4. **번역 파일만 수정**: `messages/*.json` 파일만 수정

### ❌ 하지 말아야 할 것

1. **자동 생성 파일 수정 금지**: `src/lib/paraglide/` 폴더
2. **snake_case 키 사용 금지**: camelCase만 사용
3. **일부 언어에만 키 추가 금지**: 모든 언어 파일에 동일한 키 필요
4. **수동 쿠키 관리 금지**: `setLocale()`이 자동으로 처리

---

## 트러블슈팅 (Troubleshooting)

### 문제 1: `m.xxx is not a function` 오류

**원인**: 메시지 파일의 키가 camelCase가 아니거나 존재하지 않음

**해결 방법**:
```json
❌ "my_key": "값"           // snake_case
❌ "my-key": "값"           // kebab-case
✅ "myKey": "값"            // camelCase
```

**코드 수정**:
```svelte
❌ {m.my_key()}
✅ {m.myKey()}
```

### 문제 2: 번역이 업데이트되지 않음

**원인**: Paraglide가 재컴파일되지 않음

**해결 방법**:
```bash
# 개발 서버 재시작
npm run dev
```

### 문제 3: 쿠키가 저장되지 않음

**확인 사항**:
- `setLocale()`을 호출했는지 확인
- 브라우저 쿠키 설정 확인
- HTTPS 환경에서는 Secure 플래그 필요할 수 있음

**올바른 사용법**:
```typescript
// ✅ 올바른 방법 (자동으로 쿠키 저장)
setLocale('ko');

// ❌ 잘못된 방법 (수동 쿠키 설정 불필요)
document.cookie = `PARAGLIDE_LOCALE=ko; ...`;
setLocale('ko');
```

### 문제 4: 일부 언어에서만 번역 누락

**원인**: 모든 언어 파일에 키가 없음

**해결 방법**:
```bash
# 모든 언어 파일 확인
cat messages/ko.json | jq 'keys'
cat messages/en.json | jq 'keys'
cat messages/ja.json | jq 'keys'
cat messages/zh.json | jq 'keys'
```

모든 파일에 동일한 키가 있어야 합니다.

### 문제 5: TypeScript 타입 오류

**원인**: Paraglide가 생성한 타입 파일이 최신이 아님

**해결 방법**:
```bash
# 개발 서버 재시작
npm run dev

# 또는 명시적으로 재컴파일
npx @inlang/paraglide-js compile
```

---

## 자주 사용하는 함수 (Common Functions)

```typescript
import { getLocale, setLocale, locales } from '$lib/paraglide/runtime';
import { m } from '$lib/paraglide/messages';

// 현재 로케일 가져오기
const currentLocale = getLocale(); // 'en' | 'ko' | 'ja' | 'zh'

// 로케일 변경 (자동으로 쿠키 저장 + 페이지 새로고침)
setLocale('ko');

// 지원하는 모든 로케일 목록
console.log(locales); // ['en', 'ko', 'ja', 'zh']

// 메시지 함수 사용
m.navHome();                           // "홈"
m.authWelcomeUser({ name: '홍길동' }); // "환영합니다, 홍길동님!"
```

---

## 요약 (Summary)

### 핵심 포인트

1. **번역 추가**: `messages/*.json` 파일에 camelCase 키로 작성
2. **사용**: `m.keyName()` 함수 호출
3. **언어 변경**: `setLocale('ko')` 호출 (쿠키 저장 자동)
4. **자동 생성 파일**: `src/lib/paraglide/` 폴더는 절대 수정 금지
5. **명명 규칙**: camelCase + 카테고리 접두사 사용

### 작업 흐름

```
1. messages/*.json 수정
   ↓
2. npm run dev (자동 재컴파일)
   ↓
3. m.keyName() 사용
   ↓
4. setLocale() 로 언어 변경
```

### 간소화된 접근 방식

- ✅ **쿠키 관리**: `setLocale()`이 자동 처리
- ✅ **로케일 감지**: `hooks.server.ts` 미들웨어가 자동 처리
- ✅ **타입 안전성**: Paraglide가 자동 생성
- ✅ **페이지 새로고침**: `setLocale()`이 자동 처리

**더 자세한 내용은 다음 문서를 참고하세요:**
- [Paraglide 설정 가이드](../docs/paraglide-setup.md)
- [Paraglide 간단 가이드](../docs/paraglide-simple-guide.md)

