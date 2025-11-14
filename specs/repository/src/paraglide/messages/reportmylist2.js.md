---
name: reportmylist2.js
description: reportmylist2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/reportmylist2.js
---

# reportmylist2.js

## 개요

**파일 경로**: `src/paraglide/messages/reportmylist2.js`
**파일 타입**: javascript
**카테고리**: other

reportmylist2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Reports`
};

const ko_reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 신고 목록`
};

const ja_reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `マイ通報リスト`
};

const zh_reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的举报`
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
const reportmylist2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportmylist2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportmylist2", locale)
	if (locale === "en") return en_reportmylist2(inputs)
	if (locale === "ko") return ko_reportmylist2(inputs)
	if (locale === "ja") return ja_reportmylist2(inputs)
	return zh_reportmylist2(inputs)
};
export { reportmylist2 as "reportMyList" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
