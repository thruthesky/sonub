---
name: profilemonthformat2.js
description: profilemonthformat2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilemonthformat2.js
---

# profilemonthformat2.js

## 개요

**파일 경로**: `src/paraglide/messages/profilemonthformat2.js`
**파일 타입**: javascript
**카테고리**: other

profilemonthformat2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profilemonthformat2 = /** @type {(inputs: { month: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.month}月`
};

/** @type {(inputs: { month: NonNullable<unknown> }) => string} */
const en_profilemonthformat2 = () => 'profileMonthFormat'

/** @type {(inputs: { month: NonNullable<unknown> }) => string} */
const ko_profilemonthformat2 = en_profilemonthformat2;

/** @type {(inputs: { month: NonNullable<unknown> }) => string} */
const ja_profilemonthformat2 = en_profilemonthformat2;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ month: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profilemonthformat2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilemonthformat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilemonthformat2", locale)
	if (locale === "en") return en_profilemonthformat2(inputs)
	if (locale === "ko") return ko_profilemonthformat2(inputs)
	if (locale === "ja") return ja_profilemonthformat2(inputs)
	return zh_profilemonthformat2(inputs)
};
export { profilemonthformat2 as "profileMonthFormat" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
