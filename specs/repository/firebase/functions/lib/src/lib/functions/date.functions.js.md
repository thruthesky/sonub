---
name: date.functions.js
description: date.functions Cloud Function
version: 1.0.0
type: firebase-function
category: cloud-function
original_path: firebase/functions/lib/src/lib/functions/date.functions.js
---

# date.functions.js

## 개요

**파일 경로**: `firebase/functions/lib/src/lib/functions/date.functions.js`
**파일 타입**: firebase-function
**카테고리**: cloud-function

date.functions Cloud Function

## 소스 코드

```javascript
"use strict";
/**
 * 날짜/시간 관련 순수 함수 모음
 */
Object.defineProperty(exports, "__esModule", { value: true });
exports.formatLongDate = formatLongDate;
exports.formatShortDate = formatShortDate;
const runtime_js_1 = require("$lib/paraglide/runtime.js");
/**
 * Paraglide에서 선택된 언어 코드를 Intl DateTimeFormat에 맞는 형태로 반환한다.
 */
function resolveLocale() {
    const currentLocale = (0, runtime_js_1.getLocale)();
    return currentLocale === 'ko'
        ? 'ko-KR'
        : currentLocale === 'ja'
            ? 'ja-JP'
            : currentLocale === 'zh'
                ? 'zh-CN'
                : 'en-US';
}
/**
 * 긴 형식의 날짜/시간 문자열.
 *
 * @param timestamp - Unix 타임스탬프 (밀리초)
 * @returns 연-월-일과 시:분:초를 모두 포함한 문자열
 */
function formatLongDate(timestamp) {
    if (!timestamp)
        return '';
    const date = new Date(timestamp);
    return date.toLocaleString(resolveLocale(), {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}
/**
 * 짧은 형식의 날짜/시간 문자열.
 *
 * 오늘 날짜라면 HH:MM AM/PM, 아니라면 YYYY/MM/DD 형태로 반환한다.
 *
 * @param value - Unix 타임스탬프 (밀리초)
 */
function formatShortDate(value) {
    if (!value)
        return '';
    const locale = resolveLocale();
    const target = new Date(value);
    const now = new Date();
    const sameDay = target.getFullYear() === now.getFullYear() &&
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
//# sourceMappingURL=date.functions.js.map
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
