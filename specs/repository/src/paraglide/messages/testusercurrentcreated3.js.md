---
name: testusercurrentcreated3.js
description: testusercurrentcreated3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusercurrentcreated3.js
---

# testusercurrentcreated3.js

## 개요

**파일 경로**: `src/paraglide/messages/testusercurrentcreated3.js`
**파일 타입**: javascript
**카테고리**: other

testusercurrentcreated3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `Currently created`
};

const ko_testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `현재 생성된 수`
};

const ja_testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `現在作成された数`
};

const zh_testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `当前已创建`
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
const testusercurrentcreated3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercurrentcreated3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercurrentcreated3", locale)
	if (locale === "en") return en_testusercurrentcreated3(inputs)
	if (locale === "ko") return ko_testusercurrentcreated3(inputs)
	if (locale === "ja") return ja_testusercurrentcreated3(inputs)
	return zh_testusercurrentcreated3(inputs)
};
export { testusercurrentcreated3 as "testUserCurrentCreated" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
