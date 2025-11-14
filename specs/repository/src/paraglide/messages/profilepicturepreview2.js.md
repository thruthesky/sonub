---
name: profilepicturepreview2.js
description: profilepicturepreview2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilepicturepreview2.js
---

# profilepicturepreview2.js

## 개요

**파일 경로**: `src/paraglide/messages/profilepicturepreview2.js`
**파일 타입**: javascript
**카테고리**: other

profilepicturepreview2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilepicturepreview2 = /** @type {(inputs: {}) => string} */ () => {
	return `Upload preview`
};

const ko_profilepicturepreview2 = /** @type {(inputs: {}) => string} */ () => {
	return `업로드 미리보기`
};

const ja_profilepicturepreview2 = /** @type {(inputs: {}) => string} */ () => {
	return `アップロードプレビュー`
};

/** @type {(inputs: {}) => string} */
const zh_profilepicturepreview2 = en_profilepicturepreview2;

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
const profilepicturepreview2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepicturepreview2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepicturepreview2", locale)
	if (locale === "en") return en_profilepicturepreview2(inputs)
	if (locale === "ko") return ko_profilepicturepreview2(inputs)
	if (locale === "ja") return ja_profilepicturepreview2(inputs)
	return zh_profilepicturepreview2(inputs)
};
export { profilepicturepreview2 as "profilePicturePreview" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
