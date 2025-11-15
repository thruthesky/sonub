---
title: pagetitleadminreports3.js
type: javascript
path: src/paraglide/messages/pagetitleadminreports3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/pagetitleadminreports3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_pagetitleadminreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `Admin Reports - Sonub`
};

const ja_pagetitleadminreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `管理者通報リスト - Sonub`
};

const zh_pagetitleadminreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `管理员举报列表 - Sonub`
};

/** @type {(inputs: {}) => string} */
const ko_pagetitleadminreports3 = en_pagetitleadminreports3;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{}} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const pagetitleadminreports3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitleadminreports3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitleadminreports3", locale)
	if (locale === "en") return en_pagetitleadminreports3(inputs)
	if (locale === "ko") return ko_pagetitleadminreports3(inputs)
	if (locale === "ja") return ja_pagetitleadminreports3(inputs)
	return zh_pagetitleadminreports3(inputs)
};
export { pagetitleadminreports3 as "pageTitleAdminReports" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
