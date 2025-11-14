---
name: testusernouserstodelete5.js
description: testusernouserstodelete5 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusernouserstodelete5.js
---

# testusernouserstodelete5.js

## 개요

**파일 경로**: `src/paraglide/messages/testusernouserstodelete5.js`
**파일 타입**: javascript
**카테고리**: other

testusernouserstodelete5 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_testusernouserstodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `没有要删除的测试用户。`
};

/** @type {(inputs: {}) => string} */
const en_testusernouserstodelete5 = () => 'testUserNoUsersToDelete'

/** @type {(inputs: {}) => string} */
const ko_testusernouserstodelete5 = en_testusernouserstodelete5;

/** @type {(inputs: {}) => string} */
const ja_testusernouserstodelete5 = en_testusernouserstodelete5;

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
const testusernouserstodelete5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernouserstodelete5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernouserstodelete5", locale)
	if (locale === "en") return en_testusernouserstodelete5(inputs)
	if (locale === "ko") return ko_testusernouserstodelete5(inputs)
	if (locale === "ja") return ja_testusernouserstodelete5(inputs)
	return zh_testusernouserstodelete5(inputs)
};
export { testusernouserstodelete5 as "testUserNoUsersToDelete" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
