---
title: profiledayformat2.js
type: javascript
path: src/paraglide/messages/profiledayformat2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profiledayformat2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
