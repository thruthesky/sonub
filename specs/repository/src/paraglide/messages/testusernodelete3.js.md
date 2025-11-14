---
name: testusernodelete3.js
description: testusernodelete3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusernodelete3.js
---

# testusernodelete3.js

## 개요

**파일 경로**: `src/paraglide/messages/testusernodelete3.js`
**파일 타입**: javascript
**카테고리**: other

testusernodelete3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_testusernodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有要删除的测试用户。`
};

/** @type {(inputs: {}) => string} */
const en_testusernodelete3 = () => 'testUserNoDelete'

/** @type {(inputs: {}) => string} */
const ko_testusernodelete3 = en_testusernodelete3;

/** @type {(inputs: {}) => string} */
const ja_testusernodelete3 = en_testusernodelete3;

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
const testusernodelete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernodelete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernodelete3", locale)
	if (locale === "en") return en_testusernodelete3(inputs)
	if (locale === "ko") return ko_testusernodelete3(inputs)
	if (locale === "ja") return ja_testusernodelete3(inputs)
	return zh_testusernodelete3(inputs)
};
export { testusernodelete3 as "testUserNoDelete" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
