---
name: testuserdeleteallerror4.js
description: testuserdeleteallerror4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserdeleteallerror4.js
---

# testuserdeleteallerror4.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserdeleteallerror4.js`
**파일 타입**: javascript
**카테고리**: other

testuserdeleteallerror4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `Error deleting all test users:`
};

const ko_testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `모든 테스트 사용자 삭제 중 오류:`
};

const ja_testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `すべてのテストユーザー削除中のエラー：`
};

const zh_testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `删除所有测试用户时出错：`
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
const testuserdeleteallerror4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteallerror4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteallerror4", locale)
	if (locale === "en") return en_testuserdeleteallerror4(inputs)
	if (locale === "ko") return ko_testuserdeleteallerror4(inputs)
	if (locale === "ja") return ja_testuserdeleteallerror4(inputs)
	return zh_testuserdeleteallerror4(inputs)
};
export { testuserdeleteallerror4 as "testUserDeleteAllError" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
