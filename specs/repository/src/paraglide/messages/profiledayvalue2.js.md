---
name: profiledayvalue2.js
description: profiledayvalue2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profiledayvalue2.js
---

# profiledayvalue2.js

## 개요

**파일 경로**: `src/paraglide/messages/profiledayvalue2.js`
**파일 타입**: javascript
**카테고리**: other

profiledayvalue2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profiledayvalue2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}`
};

const ko_profiledayvalue2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}일`
};

const ja_profiledayvalue2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}日`
};

/** @type {(inputs: { day: NonNullable<unknown> }) => string} */
const zh_profiledayvalue2 = en_profiledayvalue2;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ day: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profiledayvalue2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledayvalue2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledayvalue2", locale)
	if (locale === "en") return en_profiledayvalue2(inputs)
	if (locale === "ko") return ko_profiledayvalue2(inputs)
	if (locale === "ja") return ja_profiledayvalue2(inputs)
	return zh_profiledayvalue2(inputs)
};
export { profiledayvalue2 as "profileDayValue" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
