---
name: profiledateofbirth3.js
description: profiledateofbirth3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profiledateofbirth3.js
---

# profiledateofbirth3.js

## 개요

**파일 경로**: `src/paraglide/messages/profiledateofbirth3.js`
**파일 타입**: javascript
**카테고리**: other

profiledateofbirth3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `Date of Birth`
};

const ko_profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `생년월일`
};

const ja_profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `生年月日`
};

const zh_profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `出生日期`
};

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
const profiledateofbirth3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledateofbirth3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledateofbirth3", locale)
	if (locale === "en") return en_profiledateofbirth3(inputs)
	if (locale === "ko") return ko_profiledateofbirth3(inputs)
	if (locale === "ja") return ja_profiledateofbirth3(inputs)
	return zh_profiledateofbirth3(inputs)
};
export { profiledateofbirth3 as "profileDateOfBirth" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
