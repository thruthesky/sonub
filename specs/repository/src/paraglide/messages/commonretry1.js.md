---
name: commonretry1.js
description: commonretry1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/commonretry1.js
---

# commonretry1.js

## 개요

**파일 경로**: `src/paraglide/messages/commonretry1.js`
**파일 타입**: javascript
**카테고리**: other

commonretry1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `Try Again`
};

const ko_commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `다시 시도`
};

const ja_commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `再試行`
};

const zh_commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `重试`
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
const commonretry1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonretry1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonretry1", locale)
	if (locale === "en") return en_commonretry1(inputs)
	if (locale === "ko") return ko_commonretry1(inputs)
	if (locale === "ja") return ja_commonretry1(inputs)
	return zh_commonretry1(inputs)
};
export { commonretry1 as "commonRetry" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
