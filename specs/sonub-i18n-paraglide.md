---
name: sonub-i18n-paraglide
title: Paraglide-JS 기반 i18n 다국어 지원 시스템
version: 1.0.0
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

```
@inlang/paraglide-js: ^2.4.0
```

### 프로젝트 구조

```
sonub/
├── messages/                          # 다국어 메시지 파일 디렉토리
│   ├── ko.json                       # 한국어 메시지
│   ├── en.json                       # 영어 메시지 (기본)
│   ├── ja.json                       # 일본어 메시지
│   └── zh.json                       # 중국어 메시지
├── src/
│   ├── lib/
│   │   ├── paraglide/                # 자동 생성된 paraglide 런타임 (git ignore)
│   │   │   ├── messages.js           # 메시지 export 인터페이스
│   │   │   ├── messages/
│   │   │   │   ├── _index.js         # 모든 메시지 함수 (자동 생성)
│   │   │   │   ├── ko.js
│   │   │   │   ├── en.js
│   │   │   │   ├── ja.js
│   │   │   │   └── zh.js
│   │   │   ├── runtime.js            # 로케일 감지, 설정, URL 처리 (자동 생성)
│   │   │   ├── server.js             # 서버 사이드 헬퍼 (자동 생성)
│   │   │   └── registry.js           # 런타임 설정 저장소 (자동 생성)
│   │   └── stores/
│   │       └── i18n.svelte.ts        # i18n 스토어 (선택사항, 편의용)
│   ├── routes/
│   │   └── +layout.svelte            # 루트 레이아웃 (로케일 초기화)
│   └── app.html
├── package.json
└── inlang.json                       # Inlang 설정 파일 (자동 생성/관리)
```

### 파일 인코딩

- **UTF-8 (BOM 없음)**: 모든 메시지 파일과 소스 코드

---

## 워크플로우 (Workflow)

### 1. 초기 설정 및 설치

#### 1.1 패키지 설치 상태 확인

프로젝트에 `@inlang/paraglide-js`가 이미 설치되어 있습니다. 확인:

```bash
npm ls @inlang/paraglide-js
# 출력: @inlang/paraglide-js@^2.4.0
```

#### 1.2 메시지 디렉토리 구조

다국어 메시지는 `messages/` 디렉토리에 저장됩니다:

```
messages/
├── ko.json    # 한국어 (Korean)
├── en.json    # 영어 (English - 기본 언어)
├── ja.json    # 일본어 (Japanese)
└── zh.json    # 중국어 (Chinese)
```

#### 1.3 메시지 파일 형식

각 메시지 파일은 Inlang 메시지 형식을 따릅니다:

**messages/ko.json (한국어 예시)**
```json
{
  "$schema": "https://inlang.com/schema/inlang-message-format",
  "게시글": "게시글",
  "댓글": "댓글",
  "신고사유_abuse": "학대 및 괴롭힘",
  "신고사유_fake-news": "거짓 정보",
  "신고사유_spam": "스팸",
  "신고사유_inappropriate": "부적절한 콘텐츠",
  "신고사유_other": "기타",
  "로그인필요": "로그인이 필요합니다",
  "로그인": "로그인",
  "내_신고_목록": "내 신고 목록",
  "내가_작성한_신고를_확인할_수_있습니다": "내가 작성한 신고를 확인할 수 있습니다",
  "신고를취소하시겠습니까": "신고를 취소하시겠습니까"
}
```

**messages/en.json (영어 예시)**
```json
{
  "$schema": "https://inlang.com/schema/inlang-message-format",
  "게시글": "Post",
  "댓글": "Comment",
  "신고사유_abuse": "Abuse & Harassment",
  "신고사유_fake-news": "Misinformation",
  "신고사유_spam": "Spam",
  "신고사유_inappropriate": "Inappropriate Content",
  "신고사유_other": "Other",
  "로그인필요": "Login required",
  "로그인": "Login",
  "내_신고_목록": "My Reports",
  "내가_작성한_신고를_확인할_수_있습니다": "You can check the reports you submitted",
  "신고를취소하시겠습니까": "Are you sure you want to cancel this report?"
}
```

### 2. 런타임 로케일 관리

#### 2.1 로케일 감지 전략

Paraglide-JS 런타임은 다음 순서로 로케일을 감지합니다:

| 순서 | 전략 (Strategy) | 설명 |
|-----|-----------------|------|
| 1 | `cookie` | 브라우저 쿠키 `PARAGLIDE_LOCALE`에서 로케일 읽음 |
| 2 | `globalVariable` | 전역 변수 `_locale`에서 로케일 읽음 |
| 3 | `baseLocale` | 기본 로케일 `en`으로 폴백 |

#### 2.2 로케일 가져오기

```typescript
// src/lib/paraglide/runtime.js에서 제공되는 함수
import { getLocale, setLocale, locales, baseLocale } from "$lib/paraglide/runtime";

// 현재 로케일 가져오기
const currentLocale = getLocale(); // "en", "ko", "ja", "zh" 중 하나

// 사용 가능한 로케일 목록
console.log(locales); // ["en", "ko", "ja", "zh"]

// 기본 로케일
console.log(baseLocale); // "en"
```

#### 2.3 로케일 설정

```typescript
import { setLocale } from "$lib/paraglide/runtime";

// 로케일 변경 (기본: 페이지 새로고침)
setLocale("ko");

// 페이지 새로고침 없이 로케일 변경
await setLocale("ja", { reload: false });
```

### 3. Svelte 5 컴포넌트에서 메시지 사용

#### 3.1 기본 메시지 사용

생성된 메시지 함수를 Svelte 컴포넌트에서 직접 사용합니다:

**src/lib/components/user-report-list.svelte**
```svelte
<script lang="ts">
  // Paraglide-JS 자동 생성 메시지 함수
  import * as m from "$lib/paraglide/messages";
  import { getLocale } from "$lib/paraglide/runtime";

  // 현재 로케일 (반응형)
  let locale = $state(getLocale());

  // 메시지 함수 호출 (로케일 자동 감지)
  let reportTitle = $derived(m.내_신고_목록());
  let reportDesc = $derived(m.내가_작성한_신고를_확인할_수_있습니다());
  let abuseLabelText = $derived(m.신고사유_abuse());
</script>

<div class="report-page">
  <h1>{reportTitle}</h1>
  <p>{reportDesc}</p>

  <div class="report-reason">
    <label>{abuseLabelText}</label>
  </div>
</div>
```

#### 3.2 조건부 메시지 사용

```svelte
<script lang="ts">
  import * as m from "$lib/paraglide/messages";

  let reportType = $state("post"); // "post" 또는 "comment"

  let typeText = $derived(
    reportType === "post" ? m.게시글() : m.댓글()
  );
</script>

<p>신고 대상: {typeText}</p>
```

---

## 메시지 키 마이그레이션 전략

### 3.4 ID 기반 메시지 키 전환 (2025-11-10)

#### 문제 배경
Paraglide-JS는 메시지 키를 JavaScript 식별자로 변환할 때, 한글 키에 대해 언더스코어 기반 변수명을 생성합니다.
이로 인해 "공통_로딩중", "공통_닫기", "공통_저장" 같은 여러 한글 키가 동일한 변수명(\_\_\_\_\_, \_\_\_\_\_\_, 등)으로 변환되어 **"Identifier has already been declared"** 오류가 발생합니다.

#### 해결 방안
메시지 키를 한글에서 ID 기반(msg_0001, msg_0002, ...)으로 변환합니다.

**장점:**
- Paraglide 변수명 충돌 해결 (ID 기반 키는 고유한 변수명 생성)
- 메시지 관리 자동화 용이
- 언어 독립적인 구조

**변환 규칙:**
```
한글 키 → ID 기반 키
"공통_로딩중" → "msg_0001"
"공통_닫기" → "msg_0002"
"네비_소개" → "msg_0019"
...
"사이드바_새기능" → "msg_0154"
```

#### 변환 도구

**1. convert-message-keys.cjs** - 메시지 파일 변환
- 모든 언어 파일(en.json, ko.json, ja.json, zh.json) 변환
- MESSAGE_KEY_MAPPING.json 자동 생성
- 실행: `node convert-message-keys.cjs`

**2. update-source-messages.cjs** - 소스 코드 참조 업데이트
- 소스 코드의 모든 메시지 키 참조 자동 업데이트
- 지원 패턴:
  - `m.한글키()` → `m.msg_XXXX()`
  - `{t.한글키}` → `{t.msg_XXXX}`
  - `m["한글키"]()` → `m.msg_XXXX()`
  - `export const 한글키:` → `export const msg_XXXX:`
- 실행: `node update-source-messages.cjs`

**3. MESSAGE_KEY_MAPPING.json** - 매핑 참조
```json
{
  "msg_0019": {
    "oldKey": "네비_소개",
    "description": "이전: 네비_소개"
  }
}
```

#### 버전 히스토리
- **v1.0.0** (2025-11-09): 초기 한글 키 기반 설계
- **v1.1.0** (2025-11-10): ID 기반 메시지 키 마이그레이션
  - 185개 메시지 키를 msg_0001~msg_0185로 변환
  - 4개 언어 파일(en.json, ko.json, ja.json, zh.json) 동시 변환
  - 15개 소스 파일에서 341개 메시지 참조 자동 업데이트
  - Paraglide "Identifier has already been declared" 오류 완전 해결

---

## 베스트 프랙티스 (Best Practices)

### 4. 권장 사항

#### 4.1 메시지 구조화

메시지 키는 ID 기반 형식을 사용합니다:

```
msg_0001, msg_0002, ..., msg_XXXX

MESSAGE_KEY_MAPPING.json을 참고하여
각 ID가 어떤 한글 키에 대응되는지 확인합니다:
- msg_0001 → 공통_로딩중
- msg_0002 → 공통_닫기
- msg_0019 → 네비_소개
```

**참고:** 개발 과정에서 새로운 메시지가 추가되면:
1. messages/ko.json에 추가 (한글 원본)
2. 다른 언어 파일에도 추가
3. convert-message-keys.cjs 재실행 (새로운 ID 자동 할당)
4. update-source-messages.cjs 재실행 (소스 코드 업데이트)

#### 4.2 컴포넌트에서의 메시지 사용

```svelte
<script lang="ts">
  import * as m from "$lib/paraglide/messages";

  // 좋은 예: 컴포넌트 상단에서 메시지 가져오기
  const messages = {
    title: () => m.내_신고_목록(),
    description: () => m.내가_작성한_신고를_확인할_수_있습니다(),
  };
</script>

<h1>{messages.title()}</h1>
<p>{messages.description()}</p>
```

#### 4.3 로케일 전환 성능 최적화

```typescript
// 불필요한 페이지 새로고침 방지
await setLocale("ko", { reload: false });

// UI 수동 업데이트 (필요시)
locale = getLocale();
```

---

## 참고 자료 (References)

### 공식 문서
- [Svelte paraglide CLI](https://svelte.dev/docs/cli/paraglide)
- [Inlang Paraglide-SvelteKit 시작하기](https://inlang.com/m/dxnzrydw/paraglide-sveltekit-i18n/getting-started)
- [Paraglide-JS GitHub](https://github.com/opral/inlang-paraglide-js)

### 관련 사양서
- [sonub-setup-svelte.md](./sonub-setup-svelte.md) - SvelteKit 초기 설정
- [sonub-design-workflow.md](./sonub-design-workflow.md) - 디자인 워크플로우

---

**마지막 업데이트**: 2025-11-10
**SED 준수**: 엄격 모드, UTF-8 인코딩, 명시적 정의

### 변경 이력

**v1.1.0 (2025-11-10)**
- ID 기반 메시지 키 마이그레이션 완료
- 185개 메시지 키를 msg_0001~msg_0185로 변환
- Paraglide 변수명 충돌 오류 완전 해결
- 메시지 키 마이그레이션 전략 및 도구 문서화
