---
name: userjoindate2.js
description: userjoindate2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/userjoindate2.js
---

# userjoindate2.js

## 개요

**파일 경로**: `src/paraglide/messages/userjoindate2.js`
**파일 타입**: javascript
**카테고리**: other

userjoindate2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_userjoindate2 = /** @type {(inputs: {}) => string} */ () => {
	return `Joined:`
};

const ko_userjoindate2 = /** @type {(inputs: {}) => string} */ () => {
	return `가입일:`
};

const ja_userjoindate2 = /** @type {(inputs: {}) => string} */ () => {
	return `登録日:`
};

/** @type {(inputs: {}) => string} */
const zh_userjoindate2 = en_userjoindate2;

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
const userjoindate2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userjoindate2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userjoindate2", locale)
	if (locale === "en") return en_userjoindate2(inputs)
	if (locale === "ko") return ko_userjoindate2(inputs)
	if (locale === "ja") return ja_userjoindate2(inputs)
	return zh_userjoindate2(inputs)
};
export { userjoindate2 as "userJoinDate" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
