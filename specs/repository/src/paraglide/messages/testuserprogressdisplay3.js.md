---
name: testuserprogressdisplay3.js
description: testuserprogressdisplay3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserprogressdisplay3.js
---

# testuserprogressdisplay3.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserprogressdisplay3.js`
**파일 타입**: javascript
**카테고리**: other

testuserprogressdisplay3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

const ko_testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

const ja_testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

const zh_testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuserprogressdisplay3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserprogressdisplay3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserprogressdisplay3", locale)
	if (locale === "en") return en_testuserprogressdisplay3(inputs)
	if (locale === "ko") return ko_testuserprogressdisplay3(inputs)
	if (locale === "ja") return ja_testuserprogressdisplay3(inputs)
	return zh_testuserprogressdisplay3(inputs)
};
export { testuserprogressdisplay3 as "testUserProgressDisplay" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
