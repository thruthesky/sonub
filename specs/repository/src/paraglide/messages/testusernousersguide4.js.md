---
title: testusernousersguide4.js
type: javascript
path: src/paraglide/messages/testusernousersguide4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testusernousersguide4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_testusernousersguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未创建测试用户。您可以使用上面的<strong>创建测试用户</strong>功能来创建100个。`
};

/** @type {(inputs: {}) => string} */
const en_testusernousersguide4 = () => 'testUserNoUsersGuide'

/** @type {(inputs: {}) => string} */
const ko_testusernousersguide4 = en_testusernousersguide4;

/** @type {(inputs: {}) => string} */
const ja_testusernousersguide4 = en_testusernousersguide4;

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
const testusernousersguide4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernousersguide4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernousersguide4", locale)
	if (locale === "en") return en_testusernousersguide4(inputs)
	if (locale === "ko") return ko_testusernousersguide4(inputs)
	if (locale === "ja") return ja_testusernousersguide4(inputs)
	return zh_testusernousersguide4(inputs)
};
export { testusernousersguide4 as "testUserNoUsersGuide" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
