---
title: testuserdeleteallerror4.js
type: javascript
path: src/paraglide/messages/testuserdeleteallerror4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testuserdeleteallerror4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
