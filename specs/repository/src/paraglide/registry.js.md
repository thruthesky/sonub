---
title: registry.js
type: javascript
path: src/paraglide/registry.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/registry.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */

/**
 * @param {import("./runtime.js").Locale} locale
 * @param {unknown} input
 * @param {Intl.PluralRulesOptions} [options]
 * @returns {string}
 */
export function plural(locale, input, options) { 
	return new Intl.PluralRules(locale, options).select(Number(input))
};

/**
 * @param {import("./runtime.js").Locale} locale
 * @param {unknown} input
 * @param {Intl.NumberFormatOptions} [options]
 * @returns {string}
 */
export function number(locale, input, options) {
	return new Intl.NumberFormat(locale, options).format(Number(input))
};

/**
 * @param {import("./runtime.js").Locale} locale
 * @param {unknown} input
 * @param {Intl.DateTimeFormatOptions} [options]
 * @returns {string}
 */
export function datetime(locale, input, options) {
	return new Intl.DateTimeFormat(locale, options).format(new Date(/** @type {string} */ (input)))
};
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
