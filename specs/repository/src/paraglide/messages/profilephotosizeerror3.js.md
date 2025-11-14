---
name: profilephotosizeerror3.js
description: profilephotosizeerror3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilephotosizeerror3.js
---

# profilephotosizeerror3.js

## 개요

**파일 경로**: `src/paraglide/messages/profilephotosizeerror3.js`
**파일 타입**: javascript
**카테고리**: other

profilephotosizeerror3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profilephotosizeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `文件大小必须在5MB以内。`
};

/** @type {(inputs: {}) => string} */
const en_profilephotosizeerror3 = () => 'profilePhotoSizeError'

/** @type {(inputs: {}) => string} */
const ko_profilephotosizeerror3 = en_profilephotosizeerror3;

/** @type {(inputs: {}) => string} */
const ja_profilephotosizeerror3 = en_profilephotosizeerror3;

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
const profilephotosizeerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotosizeerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotosizeerror3", locale)
	if (locale === "en") return en_profilephotosizeerror3(inputs)
	if (locale === "ko") return ko_profilephotosizeerror3(inputs)
	if (locale === "ja") return ja_profilephotosizeerror3(inputs)
	return zh_profilephotosizeerror3(inputs)
};
export { profilephotosizeerror3 as "profilePhotoSizeError" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
