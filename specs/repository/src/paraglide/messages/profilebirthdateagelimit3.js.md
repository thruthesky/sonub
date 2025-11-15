---
title: profilebirthdateagelimit3.js
type: javascript
path: src/paraglide/messages/profilebirthdateagelimit3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilebirthdateagelimit3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profilebirthdateagelimit3 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `必须年满18岁（${i.minYear}年 - ${i.maxYear}年）`
};

/** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */
const en_profilebirthdateagelimit3 = () => 'profileBirthdateAgeLimit'

/** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */
const ko_profilebirthdateagelimit3 = en_profilebirthdateagelimit3;

/** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */
const ja_profilebirthdateagelimit3 = en_profilebirthdateagelimit3;

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
const profilebirthdateagelimit3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdateagelimit3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdateagelimit3", locale)
	if (locale === "en") return en_profilebirthdateagelimit3(inputs)
	if (locale === "ko") return ko_profilebirthdateagelimit3(inputs)
	if (locale === "ja") return ja_profilebirthdateagelimit3(inputs)
	return zh_profilebirthdateagelimit3(inputs)
};
export { profilebirthdateagelimit3 as "profileBirthdateAgeLimit" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
