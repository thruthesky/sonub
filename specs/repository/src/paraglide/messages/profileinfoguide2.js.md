---
name: profileinfoguide2.js
description: profileinfoguide2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profileinfoguide2.js
---

# profileinfoguide2.js

## 개요

**파일 경로**: `src/paraglide/messages/profileinfoguide2.js`
**파일 타입**: javascript
**카테고리**: other

profileinfoguide2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileinfoguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Set your nickname, gender, and date of birth`
};

const ko_profileinfoguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임, 성별, 생년월일을 설정하세요`
};

const ja_profileinfoguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `ニックネーム、性別、生年月日を設定`
};

/** @type {(inputs: {}) => string} */
const zh_profileinfoguide2 = en_profileinfoguide2;

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
const profileinfoguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileinfoguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileinfoguide2", locale)
	if (locale === "en") return en_profileinfoguide2(inputs)
	if (locale === "ko") return ko_profileinfoguide2(inputs)
	if (locale === "ja") return ja_profileinfoguide2(inputs)
	return zh_profileinfoguide2(inputs)
};
export { profileinfoguide2 as "profileInfoGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
