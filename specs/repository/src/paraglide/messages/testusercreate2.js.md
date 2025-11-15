---
title: testusercreate2.js
type: javascript
path: src/paraglide/messages/testusercreate2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testusercreate2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `Create Test Users`
};

const ko_testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 생성`
};

const ja_testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `テストユーザー作成`
};

const zh_testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `创建测试用户`
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
const testusercreate2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreate2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreate2", locale)
	if (locale === "en") return en_testusercreate2(inputs)
	if (locale === "ko") return ko_testusercreate2(inputs)
	if (locale === "ja") return ja_testusercreate2(inputs)
	return zh_testusercreate2(inputs)
};
export { testusercreate2 as "testUserCreate" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
