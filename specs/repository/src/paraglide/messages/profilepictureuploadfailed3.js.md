---
title: profilepictureuploadfailed3.js
type: javascript
path: src/paraglide/messages/profilepictureuploadfailed3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilepictureuploadfailed3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilepictureuploadfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to upload photo. Please try again.`
};

const ko_profilepictureuploadfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `사진 업로드에 실패했습니다. 다시 시도해주세요.`
};

const ja_profilepictureuploadfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `写真のアップロードに失敗しました。もう一度お試しください。`
};

/** @type {(inputs: {}) => string} */
const zh_profilepictureuploadfailed3 = en_profilepictureuploadfailed3;

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
const profilepictureuploadfailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureuploadfailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureuploadfailed3", locale)
	if (locale === "en") return en_profilepictureuploadfailed3(inputs)
	if (locale === "ko") return ko_profilepictureuploadfailed3(inputs)
	if (locale === "ja") return ja_profilepictureuploadfailed3(inputs)
	return zh_profilepictureuploadfailed3(inputs)
};
export { profilepictureuploadfailed3 as "profilePictureUploadFailed" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
