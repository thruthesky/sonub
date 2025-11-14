---
name: testuserdeletinginprogress4.js
description: testuserdeletinginprogress4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserdeletinginprogress4.js
---

# testuserdeletinginprogress4.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserdeletinginprogress4.js`
**파일 타입**: javascript
**카테고리**: other

testuserdeletinginprogress4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `Deleting`
};

const ko_testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `삭제 진행 중`
};

const ja_testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `削除進行中`
};

const zh_testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `删除中`
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
const testuserdeletinginprogress4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeletinginprogress4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeletinginprogress4", locale)
	if (locale === "en") return en_testuserdeletinginprogress4(inputs)
	if (locale === "ko") return ko_testuserdeletinginprogress4(inputs)
	if (locale === "ja") return ja_testuserdeletinginprogress4(inputs)
	return zh_testuserdeletinginprogress4(inputs)
};
export { testuserdeletinginprogress4 as "testUserDeletingInProgress" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
