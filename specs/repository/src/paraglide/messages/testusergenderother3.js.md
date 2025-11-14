---
name: testusergenderother3.js
description: testusergenderother3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusergenderother3.js
---

# testusergenderother3.js

## 개요

**파일 경로**: `src/paraglide/messages/testusergenderother3.js`
**파일 타입**: javascript
**카테고리**: other

testusergenderother3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `Other`
};

const ko_testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `기타`
};

const ja_testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `その他`
};

const zh_testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `其他`
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
const testusergenderother3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusergenderother3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusergenderother3", locale)
	if (locale === "en") return en_testusergenderother3(inputs)
	if (locale === "ko") return ko_testusergenderother3(inputs)
	if (locale === "ja") return ja_testusergenderother3(inputs)
	return zh_testusergenderother3(inputs)
};
export { testusergenderother3 as "testUserGenderOther" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
