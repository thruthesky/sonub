---
name: commonerror1.js
description: commonerror1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/commonerror1.js
---

# commonerror1.js

## 개요

**파일 경로**: `src/paraglide/messages/commonerror1.js`
**파일 타입**: javascript
**카테고리**: other

commonerror1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `Error`
};

const ko_commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `오류`
};

const ja_commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `エラー`
};

const zh_commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `错误`
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
const commonerror1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonerror1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonerror1", locale)
	if (locale === "en") return en_commonerror1(inputs)
	if (locale === "ko") return ko_commonerror1(inputs)
	if (locale === "ja") return ja_commonerror1(inputs)
	return zh_commonerror1(inputs)
};
export { commonerror1 as "commonError" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
