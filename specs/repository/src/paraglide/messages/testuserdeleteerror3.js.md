---
name: testuserdeleteerror3.js
description: testuserdeleteerror3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserdeleteerror3.js
---

# testuserdeleteerror3.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserdeleteerror3.js`
**파일 타입**: javascript
**카테고리**: other

testuserdeleteerror3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `An error occurred while deleting the test user.`
};

const ko_testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 삭제 중 오류가 발생했습니다.`
};

const ja_testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `テストユーザー削除中にエラーが発生しました。`
};

const zh_testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `删除测试用户时发生错误。`
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
const testuserdeleteerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteerror3", locale)
	if (locale === "en") return en_testuserdeleteerror3(inputs)
	if (locale === "ko") return ko_testuserdeleteerror3(inputs)
	if (locale === "ja") return ja_testuserdeleteerror3(inputs)
	return zh_testuserdeleteerror3(inputs)
};
export { testuserdeleteerror3 as "testUserDeleteError" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
