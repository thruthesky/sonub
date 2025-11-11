---
name: sonub-functions-date-functions
title: 날짜·시간 순수 함수 명세
version: 1.2.0
description: 날짜/시간 i18n 처리를 위한 순수 함수와 Intl 활용 가이드
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-11
updated: 2025-11-11
step: 30
priority: "*"
dependencies:
  - sonub-functions-overview.md
tags:
  - date
  - time
  - functions
---

# 날짜와 시간 관련 순수 함수 명세

[`sonub-functions-overview.md`](./sonub-functions-overview.md)에 정의된 규칙을 따르며, 날짜/시간 처리는 브라우저 내장 Intl API를 기본으로 합니다. 모든 함수는 `src/lib/functions/date.functions.ts`에서 관리합니다.

---

## 1. 제공 함수

### 1.1 `formatLongDate(timestamp?: number | null): string`

**파일**: `src/lib/functions/date.functions.ts`

```ts
import { getLocale } from '$lib/paraglide/runtime.js';

function resolveLocale(): string {
  const currentLocale = getLocale();
  return currentLocale === 'ko'
    ? 'ko-KR'
    : currentLocale === 'ja'
      ? 'ja-JP'
      : currentLocale === 'zh'
        ? 'zh-CN'
        : 'en-US';
}

export function formatLongDate(timestamp?: number | null): string {
  if (!timestamp) return '';
  return new Date(timestamp).toLocaleString(resolveLocale(), {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
}
```

- **용도**: 가입일, 마지막 로그인, 채팅 타임스탬프 등 정밀도가 필요한 영역에서 사용합니다.
- **출력 형식**: 로케일에 맞춘 “연-월-일 시:분:초” 문자열. 입력이 falsy면 빈 문자열을 반환합니다.
- **사용처**: `src/routes/user/list/+page.svelte`, `src/routes/chat/list/+page.svelte`, `src/routes/chat/room/+page.svelte` 등.

### 1.2 `formatShortDate(value?: number | null): string`

```ts
export function formatShortDate(value?: number | null): string {
  if (!value) return '';

  const locale = resolveLocale();
  const target = new Date(value);
  const now = new Date();

  const sameDay =
    target.getFullYear() === now.getFullYear() &&
    target.getMonth() === now.getMonth() &&
    target.getDate() === now.getDate();

  if (sameDay) {
    return target.toLocaleTimeString(locale, {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
  }

  const year = target.getFullYear();
  const month = String(target.getMonth() + 1).padStart(2, '0');
  const day = String(target.getDate()).padStart(2, '0');
  return `${year}/${month}/${day}`;
}
```

- **용도**: 채팅 목록, 알림 목록 등에서 “오늘” 기준으로 시간을 간결하게 보여줄 때 사용합니다.
- **출력 규칙**:
  - 오늘 날짜 → `HH:MM AM/PM` (로케일 기반 12시간제)
  - 오늘 이전 → `YYYY/MM/DD`
- **주의**: 반환값이 빈 문자열이면 화면에서 “데이터 없음” UI를 별도로 처리합니다.

---

## 2. Intl 활용 요약

가벼운 포맷팅·다국어 대응만 필요할 때는 브라우저 내장 Intl을 활용하는 것이 가장 깔끔합니다.

| 필요 작업 | 권장 API |
| --- | --- |
| 지역화된 날짜/시간 문자열 | `Intl.DateTimeFormat` |
| 상대 시간(“3분 전”) | `Intl.RelativeTimeFormat` |
| 타임존 지정 포맷 | `Intl.DateTimeFormat` + `timeZone` 옵션 |
| 숫자·통화 포맷 | `Intl.NumberFormat` |

```svelte
<script lang="ts">
  export let date: Date | string = new Date();
  export let locale = 'ko-KR';
  export let timeZone = 'Asia/Seoul';

  const fmtDate = new Intl.DateTimeFormat(locale, {
    dateStyle: 'medium',
    timeStyle: 'short',
    timeZone
  });

  const d = typeof date === 'string' ? new Date(date) : date;
</script>

<p>{fmtDate.format(d)}</p>
```

```ts
// 상대 시간 (초/분/시간/일 단위 자동 선택)
function formatRelative(from: Date, to = new Date(), locale = 'ko-KR') {
  const diffSec = Math.round((+from - +to) / 1000);
  const abs = Math.abs(diffSec);

  const rtf = new Intl.RelativeTimeFormat(locale, { numeric: 'auto' });

  if (abs < 60) return rtf.format(diffSec, 'second');
  if (abs < 3600) return rtf.format(Math.round(diffSec / 60), 'minute');
  if (abs < 86400) return rtf.format(Math.round(diffSec / 3600), 'hour');
  return rtf.format(Math.round(diffSec / 86400), 'day');
}
```

**장점**
- 번들 크기 증가가 없고 속도가 빠르며, OS 수준 번역 품질을 바로 활용할 수 있습니다.
- `timeZone` 옵션을 주면 서버/클라이언트 어디서든 동일 포맷을 유지할 수 있습니다.

**주의**
- 파싱은 `Date.parse`에 의존하지 말고 ISO 8601 입력만 신뢰합니다.
- 날짜 연산(더하기/빼기)을 직접 구현할 때는 윤년·말일·DST 등을 고려해야 합니다.

---

## 3. Svelte/SvelteKit & SSR 팁

1. **포맷터 재사용**  
   매 렌더마다 `new Intl.DateTimeFormat()`을 생성하지 말고 모듈 스코프나 캐시(Map)로 재사용합니다.

2. **SSR 시간 불일치**  
   서버에서 렌더한 “현재 시간”과 클라이언트 시간이 다를 수 있으므로 상대 시간은 onMount 이후 계산하거나 서버에서는 고정 값을 사용합니다.

   ```svelte
   <script>
     import { onMount } from 'svelte';
     let now = new Date(0);
     onMount(() => { now = new Date(); });
   </script>
   ```

3. **타임존 정책**  
   - 서버/DB: UTC로 저장  
   - 화면: Intl로 사용자 타임존(또는 설정값)으로 포맷  
   이렇게 분리하면 협업·테스트·로깅이 안정적입니다.

4. **Svelte 5 rune 파생값**  
   잦은 포맷 재계산을 피하려면 `$derived`로 포맷터와 결과를 묶습니다.

   ```ts
   const locale = $state('ko-KR');
   const timeZone = $state('Asia/Seoul');
   const fmt = $derived.by(() => new Intl.DateTimeFormat(locale, { dateStyle: 'medium', timeStyle: 'short', timeZone }));
   const formatted = $derived(() => fmt.format(date));
   ```

---

## 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-11 | Codex Agent | 최초 작성 |
| 2025-11-11 | Codex Agent | `formatDate` 함수와 Intl 가이드를 문문화, 구조 정리 |
| 2025-11-11 | Codex Agent | `formatLongDate`, `formatShortDate`로 개편하고 사용처를 최신화 |
