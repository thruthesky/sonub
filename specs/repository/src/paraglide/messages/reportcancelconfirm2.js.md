---
title: reportcancelconfirm2.js
type: javascript
path: src/paraglide/messages/reportcancelconfirm2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/reportcancelconfirm2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `Do you want to cancel this report?`
};

const ko_reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `신고를 취소하시겠습니까?`
};

const ja_reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `この通報をキャンセルしますか？`
};

const zh_reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `您要取消此举报吗？`
};

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
const reportcancelconfirm2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportcancelconfirm2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportcancelconfirm2", locale)
	if (locale === "en") return en_reportcancelconfirm2(inputs)
	if (locale === "ko") return ko_reportcancelconfirm2(inputs)
	if (locale === "ja") return ja_reportcancelconfirm2(inputs)
	return zh_reportcancelconfirm2(inputs)
};
export { reportcancelconfirm2 as "reportCancelConfirm" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
