---
name: profilenicknamelength2.js
description: profilenicknamelength2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilenicknamelength2.js
---

# profilenicknamelength2.js

## 개요

**파일 경로**: `src/paraglide/messages/profilenicknamelength2.js`
**파일 타입**: javascript
**카테고리**: other

profilenicknamelength2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilenicknamelength2 = /** @type {(inputs: {}) => string} */ () => {
	return `Nickname must be 50 characters or less.`
};

const ko_profilenicknamelength2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임은 50자 이하여야 합니다.`
};

const ja_profilenicknamelength2 = /** @type {(inputs: {}) => string} */ () => {
	return `ニックネームは50文字以内にしてください。`
};

/** @type {(inputs: {}) => string} */
const zh_profilenicknamelength2 = en_profilenicknamelength2;

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
const profilenicknamelength2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknamelength2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknamelength2", locale)
	if (locale === "en") return en_profilenicknamelength2(inputs)
	if (locale === "ko") return ko_profilenicknamelength2(inputs)
	if (locale === "ja") return ja_profilenicknamelength2(inputs)
	return zh_profilenicknamelength2(inputs)
};
export { profilenicknamelength2 as "profileNicknameLength" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
