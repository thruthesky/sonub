---
name: profilegendernoanswer3.js
description: profilegendernoanswer3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilegendernoanswer3.js
---

# profilegendernoanswer3.js

## 개요

**파일 경로**: `src/paraglide/messages/profilegendernoanswer3.js`
**파일 타입**: javascript
**카테고리**: other

profilegendernoanswer3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilegendernoanswer3 = /** @type {(inputs: {}) => string} */ () => {
	return `Prefer not to say`
};

const ko_profilegendernoanswer3 = /** @type {(inputs: {}) => string} */ () => {
	return `선택 안 함`
};

const ja_profilegendernoanswer3 = /** @type {(inputs: {}) => string} */ () => {
	return `選択しない`
};

/** @type {(inputs: {}) => string} */
const zh_profilegendernoanswer3 = en_profilegendernoanswer3;

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
const profilegendernoanswer3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilegendernoanswer3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilegendernoanswer3", locale)
	if (locale === "en") return en_profilegendernoanswer3(inputs)
	if (locale === "ko") return ko_profilegendernoanswer3(inputs)
	if (locale === "ja") return ja_profilegendernoanswer3(inputs)
	return zh_profilegendernoanswer3(inputs)
};
export { profilegendernoanswer3 as "profileGenderNoAnswer" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
