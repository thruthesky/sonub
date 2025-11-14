---
name: profileday1.js
description: profileday1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profileday1.js
---

# profileday1.js

## 개요

**파일 경로**: `src/paraglide/messages/profileday1.js`
**파일 타입**: javascript
**카테고리**: other

profileday1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileday1 = /** @type {(inputs: {}) => string} */ () => {
	return `Day`
};

const ko_profileday1 = /** @type {(inputs: {}) => string} */ () => {
	return `일`
};

const ja_profileday1 = /** @type {(inputs: {}) => string} */ () => {
	return `日`
};

/** @type {(inputs: {}) => string} */
const zh_profileday1 = en_profileday1;

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
const profileday1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileday1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileday1", locale)
	if (locale === "en") return en_profileday1(inputs)
	if (locale === "ko") return ko_profileday1(inputs)
	if (locale === "ja") return ja_profileday1(inputs)
	return zh_profileday1(inputs)
};
export { profileday1 as "profileDay" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
