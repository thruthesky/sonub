---
name: profiledayformat2.js
description: profiledayformat2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profiledayformat2.js
---

# profiledayformat2.js

## 개요

**파일 경로**: `src/paraglide/messages/profiledayformat2.js`
**파일 타입**: javascript
**카테고리**: other

profiledayformat2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profiledayformat2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}日`
};

/** @type {(inputs: { day: NonNullable<unknown> }) => string} */
const en_profiledayformat2 = () => 'profileDayFormat'

/** @type {(inputs: { day: NonNullable<unknown> }) => string} */
const ko_profiledayformat2 = en_profiledayformat2;

/** @type {(inputs: { day: NonNullable<unknown> }) => string} */
const ja_profiledayformat2 = en_profiledayformat2;

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
const profiledayformat2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledayformat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledayformat2", locale)
	if (locale === "en") return en_profiledayformat2(inputs)
	if (locale === "ko") return ko_profiledayformat2(inputs)
	if (locale === "ja") return ja_profiledayformat2(inputs)
	return zh_profiledayformat2(inputs)
};
export { profiledayformat2 as "profileDayFormat" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
