---
name: profileyearvalue2.js
description: profileyearvalue2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profileyearvalue2.js
---

# profileyearvalue2.js

## 개요

**파일 경로**: `src/paraglide/messages/profileyearvalue2.js`
**파일 타입**: javascript
**카테고리**: other

profileyearvalue2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileyearvalue2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}`
};

const ko_profileyearvalue2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}년`
};

const ja_profileyearvalue2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}年`
};

/** @type {(inputs: { year: NonNullable<unknown> }) => string} */
const zh_profileyearvalue2 = en_profileyearvalue2;

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
const profileyearvalue2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileyearvalue2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileyearvalue2", locale)
	if (locale === "en") return en_profileyearvalue2(inputs)
	if (locale === "ko") return ko_profileyearvalue2(inputs)
	if (locale === "ja") return ja_profileyearvalue2(inputs)
	return zh_profileyearvalue2(inputs)
};
export { profileyearvalue2 as "profileYearValue" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
