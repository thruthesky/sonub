---
name: commoninfo1.js
description: commoninfo1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/commoninfo1.js
---

# commoninfo1.js

## 개요

**파일 경로**: `src/paraglide/messages/commoninfo1.js`
**파일 타입**: javascript
**카테고리**: other

commoninfo1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `Information`
};

const ko_commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `정보`
};

const ja_commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `情報`
};

const zh_commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `信息`
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
const commoninfo1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commoninfo1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commoninfo1", locale)
	if (locale === "en") return en_commoninfo1(inputs)
	if (locale === "ko") return ko_commoninfo1(inputs)
	if (locale === "ja") return ja_commoninfo1(inputs)
	return zh_commoninfo1(inputs)
};
export { commoninfo1 as "commonInfo" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
