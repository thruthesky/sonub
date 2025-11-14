---
name: profileagerestriction2.js
description: profileagerestriction2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profileagerestriction2.js
---

# profileagerestriction2.js

## 개요

**파일 경로**: `src/paraglide/messages/profileagerestriction2.js`
**파일 타입**: javascript
**카테고리**: other

profileagerestriction2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileagerestriction2 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `Must be 18 years or older (${i.minYear} - ${i.maxYear})`
};

const ko_profileagerestriction2 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `만 18세 이상만 가입할 수 있습니다 (${i.minYear}년 ~ ${i.maxYear}년생)`
};

const ja_profileagerestriction2 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `18歳以上である必要があります (${i.minYear}年 - ${i.maxYear}年)`
};

/** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */
const zh_profileagerestriction2 = en_profileagerestriction2;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profileagerestriction2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileagerestriction2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileagerestriction2", locale)
	if (locale === "en") return en_profileagerestriction2(inputs)
	if (locale === "ko") return ko_profileagerestriction2(inputs)
	if (locale === "ja") return ja_profileagerestriction2(inputs)
	return zh_profileagerestriction2(inputs)
};
export { profileagerestriction2 as "profileAgeRestriction" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
