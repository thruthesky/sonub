---
name: profilepictureuploadsuccess3.js
description: profilepictureuploadsuccess3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilepictureuploadsuccess3.js
---

# profilepictureuploadsuccess3.js

## 개요

**파일 경로**: `src/paraglide/messages/profilepictureuploadsuccess3.js`
**파일 타입**: javascript
**카테고리**: other

profilepictureuploadsuccess3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilepictureuploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile picture uploaded successfully.`
};

const ko_profilepictureuploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 사진이 업로드되었습니다.`
};

const ja_profilepictureuploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `プロフィール写真がアップロードされました。`
};

/** @type {(inputs: {}) => string} */
const zh_profilepictureuploadsuccess3 = en_profilepictureuploadsuccess3;

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
const profilepictureuploadsuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureuploadsuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureuploadsuccess3", locale)
	if (locale === "en") return en_profilepictureuploadsuccess3(inputs)
	if (locale === "ko") return ko_profilepictureuploadsuccess3(inputs)
	if (locale === "ja") return ja_profilepictureuploadsuccess3(inputs)
	return zh_profilepictureuploadsuccess3(inputs)
};
export { profilepictureuploadsuccess3 as "profilePictureUploadSuccess" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
