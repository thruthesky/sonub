---
name: profileinfoeditguide3.js
description: profileinfoeditguide3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profileinfoeditguide3.js
---

# profileinfoeditguide3.js

## 개요

**파일 경로**: `src/paraglide/messages/profileinfoeditguide3.js`
**파일 타입**: javascript
**카테고리**: other

profileinfoeditguide3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileinfoeditguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `You can modify your profile information`
};

const ko_profileinfoeditguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `회원 정보를 수정할 수 있습니다`
};

const ja_profileinfoeditguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `会員情報を修正できます`
};

/** @type {(inputs: {}) => string} */
const zh_profileinfoeditguide3 = en_profileinfoeditguide3;

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
const profileinfoeditguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileinfoeditguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileinfoeditguide3", locale)
	if (locale === "en") return en_profileinfoeditguide3(inputs)
	if (locale === "ko") return ko_profileinfoeditguide3(inputs)
	if (locale === "ja") return ja_profileinfoeditguide3(inputs)
	return zh_profileinfoeditguide3(inputs)
};
export { profileinfoeditguide3 as "profileInfoEditGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
