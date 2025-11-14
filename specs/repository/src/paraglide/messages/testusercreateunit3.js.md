---
name: testusercreateunit3.js
description: testusercreateunit3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusercreateunit3.js
---

# testusercreateunit3.js

## 개요

**파일 경로**: `src/paraglide/messages/testusercreateunit3.js`
**파일 타입**: javascript
**카테고리**: other

testusercreateunit3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_testusercreateunit3 = /** @type {(inputs: {}) => string} */ () => {
	return `100`
};

/** @type {(inputs: {}) => string} */
const en_testusercreateunit3 = () => 'testUserCreateUnit'

/** @type {(inputs: {}) => string} */
const ko_testusercreateunit3 = en_testusercreateunit3;

/** @type {(inputs: {}) => string} */
const ja_testusercreateunit3 = en_testusercreateunit3;

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
const testusercreateunit3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateunit3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateunit3", locale)
	if (locale === "en") return en_testusercreateunit3(inputs)
	if (locale === "ko") return ko_testusercreateunit3(inputs)
	if (locale === "ja") return ja_testusercreateunit3(inputs)
	return zh_testusercreateunit3(inputs)
};
export { testusercreateunit3 as "testUserCreateUnit" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
