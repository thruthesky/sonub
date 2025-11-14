---
name: pagetitlemyreports3.js
description: pagetitlemyreports3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/pagetitlemyreports3.js
---

# pagetitlemyreports3.js

## 개요

**파일 경로**: `src/paraglide/messages/pagetitlemyreports3.js`
**파일 타입**: javascript
**카테고리**: other

pagetitlemyreports3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `My Reports - Sonub`
};

const ko_pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `내 신고 목록 - Sonub`
};

const ja_pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `マイ通報リスト - Sonub`
};

const zh_pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `我的举报 - Sonub`
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
const pagetitlemyreports3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlemyreports3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlemyreports3", locale)
	if (locale === "en") return en_pagetitlemyreports3(inputs)
	if (locale === "ko") return ko_pagetitlemyreports3(inputs)
	if (locale === "ja") return ja_pagetitlemyreports3(inputs)
	return zh_pagetitlemyreports3(inputs)
};
export { pagetitlemyreports3 as "pageTitleMyReports" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
