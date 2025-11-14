---
name: testusernousertodelete5.js
description: testusernousertodelete5 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusernousertodelete5.js
---

# testusernousertodelete5.js

## 개요

**파일 경로**: `src/paraglide/messages/testusernousertodelete5.js`
**파일 타입**: javascript
**카테고리**: other

testusernousertodelete5 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusernousertodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `No test users to delete.`
};

const ko_testusernousertodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `삭제할 테스트 사용자가 없습니다.`
};

const ja_testusernousertodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `削除するテストユーザーがいません。`
};

/** @type {(inputs: {}) => string} */
const zh_testusernousertodelete5 = en_testusernousertodelete5;

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
const testusernousertodelete5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernousertodelete5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernousertodelete5", locale)
	if (locale === "en") return en_testusernousertodelete5(inputs)
	if (locale === "ko") return ko_testusernousertodelete5(inputs)
	if (locale === "ja") return ja_testusernousertodelete5(inputs)
	return zh_testusernousertodelete5(inputs)
};
export { testusernousertodelete5 as "testUserNoUserToDelete" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
