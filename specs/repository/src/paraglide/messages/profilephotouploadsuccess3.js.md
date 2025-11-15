---
title: profilephotouploadsuccess3.js
type: javascript
path: src/paraglide/messages/profilephotouploadsuccess3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilephotouploadsuccess3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profilephotouploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料照片上传成功。`
};

/** @type {(inputs: {}) => string} */
const en_profilephotouploadsuccess3 = () => 'profilePhotoUploadSuccess'

/** @type {(inputs: {}) => string} */
const ko_profilephotouploadsuccess3 = en_profilephotouploadsuccess3;

/** @type {(inputs: {}) => string} */
const ja_profilephotouploadsuccess3 = en_profilephotouploadsuccess3;

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
const profilephotouploadsuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotouploadsuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotouploadsuccess3", locale)
	if (locale === "en") return en_profilephotouploadsuccess3(inputs)
	if (locale === "ko") return ko_profilephotouploadsuccess3(inputs)
	if (locale === "ja") return ja_profilephotouploadsuccess3(inputs)
	return zh_profilephotouploadsuccess3(inputs)
};
export { profilephotouploadsuccess3 as "profilePhotoUploadSuccess" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
