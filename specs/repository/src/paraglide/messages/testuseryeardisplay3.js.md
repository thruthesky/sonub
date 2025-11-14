---
name: testuseryeardisplay3.js
description: testuseryeardisplay3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuseryeardisplay3.js
---

# testuseryeardisplay3.js

## 개요

**파일 경로**: `src/paraglide/messages/testuseryeardisplay3.js`
**파일 타입**: javascript
**카테고리**: other

testuseryeardisplay3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}`
};

const ko_testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}년`
};

const ja_testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}年`
};

const zh_testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}年`
};

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ year: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuseryeardisplay3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuseryeardisplay3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuseryeardisplay3", locale)
	if (locale === "en") return en_testuseryeardisplay3(inputs)
	if (locale === "ko") return ko_testuseryeardisplay3(inputs)
	if (locale === "ja") return ja_testuseryeardisplay3(inputs)
	return zh_testuseryeardisplay3(inputs)
};
export { testuseryeardisplay3 as "testUserYearDisplay" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
