---
title: profiledateofbirthpasterror5.js
type: javascript
path: src/paraglide/messages/profiledateofbirthpasterror5.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profiledateofbirthpasterror5.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profiledateofbirthpasterror5 = /** @type {(inputs: {}) => string} */ () => {
	return `Date of birth must be in the past.`
};

const ko_profiledateofbirthpasterror5 = /** @type {(inputs: {}) => string} */ () => {
	return `생년월일은 과거여야 합니다.`
};

const ja_profiledateofbirthpasterror5 = /** @type {(inputs: {}) => string} */ () => {
	return `生年月日は過去の日付である必要があります。`
};

/** @type {(inputs: {}) => string} */
const zh_profiledateofbirthpasterror5 = en_profiledateofbirthpasterror5;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{}} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profiledateofbirthpasterror5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledateofbirthpasterror5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledateofbirthpasterror5", locale)
	if (locale === "en") return en_profiledateofbirthpasterror5(inputs)
	if (locale === "ko") return ko_profiledateofbirthpasterror5(inputs)
	if (locale === "ja") return ja_profiledateofbirthpasterror5(inputs)
	return zh_profiledateofbirthpasterror5(inputs)
};
export { profiledateofbirthpasterror5 as "profileDateOfBirthPastError" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
