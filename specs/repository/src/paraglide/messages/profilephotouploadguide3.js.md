---
name: profilephotouploadguide3.js
description: profilephotouploadguide3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilephotouploadguide3.js
---

# profilephotouploadguide3.js

## 개요

**파일 경로**: `src/paraglide/messages/profilephotouploadguide3.js`
**파일 타입**: javascript
**카테고리**: other

profilephotouploadguide3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profilephotouploadguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `点击上传个人资料照片（最大5MB）`
};

/** @type {(inputs: {}) => string} */
const en_profilephotouploadguide3 = () => 'profilePhotoUploadGuide'

/** @type {(inputs: {}) => string} */
const ko_profilephotouploadguide3 = en_profilephotouploadguide3;

/** @type {(inputs: {}) => string} */
const ja_profilephotouploadguide3 = en_profilephotouploadguide3;

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
const profilephotouploadguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotouploadguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotouploadguide3", locale)
	if (locale === "en") return en_profilephotouploadguide3(inputs)
	if (locale === "ko") return ko_profilephotouploadguide3(inputs)
	if (locale === "ja") return ja_profilephotouploadguide3(inputs)
	return zh_profilephotouploadguide3(inputs)
};
export { profilephotouploadguide3 as "profilePhotoUploadGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
