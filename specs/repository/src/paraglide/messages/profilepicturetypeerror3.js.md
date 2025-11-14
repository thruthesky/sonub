---
name: profilepicturetypeerror3.js
description: profilepicturetypeerror3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilepicturetypeerror3.js
---

# profilepicturetypeerror3.js

## 개요

**파일 경로**: `src/paraglide/messages/profilepicturetypeerror3.js`
**파일 타입**: javascript
**카테고리**: other

profilepicturetypeerror3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilepicturetypeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `Only image files can be uploaded.`
};

const ko_profilepicturetypeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `이미지 파일만 업로드할 수 있습니다.`
};

const ja_profilepicturetypeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `画像ファイルのみアップロードできます。`
};

/** @type {(inputs: {}) => string} */
const zh_profilepicturetypeerror3 = en_profilepicturetypeerror3;

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
const profilepicturetypeerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepicturetypeerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepicturetypeerror3", locale)
	if (locale === "en") return en_profilepicturetypeerror3(inputs)
	if (locale === "ko") return ko_profilepicturetypeerror3(inputs)
	if (locale === "ja") return ja_profilepicturetypeerror3(inputs)
	return zh_profilepicturetypeerror3(inputs)
};
export { profilepicturetypeerror3 as "profilePictureTypeError" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
