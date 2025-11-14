---
name: testusercreated2.js
description: testusercreated2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusercreated2.js
---

# testusercreated2.js

## 개요

**파일 경로**: `src/paraglide/messages/testusercreated2.js`
**파일 타입**: javascript
**카테고리**: other

testusercreated2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count} created`
};

const ko_testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count}명 생성됨`
};

const ja_testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count}人作成済み`
};

const zh_testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `已创建 ${i.count} 个`
};

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testusercreated2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreated2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreated2", locale)
	if (locale === "en") return en_testusercreated2(inputs)
	if (locale === "ko") return ko_testusercreated2(inputs)
	if (locale === "ja") return ja_testusercreated2(inputs)
	return zh_testusercreated2(inputs)
};
export { testusercreated2 as "testUserCreated" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
