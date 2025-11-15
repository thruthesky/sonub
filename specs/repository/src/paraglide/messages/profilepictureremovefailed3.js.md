---
title: profilepictureremovefailed3.js
type: javascript
path: src/paraglide/messages/profilepictureremovefailed3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilepictureremovefailed3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilepictureremovefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to remove photo. Please try again.`
};

const ko_profilepictureremovefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `사진 제거에 실패했습니다. 다시 시도해주세요.`
};

const ja_profilepictureremovefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `写真の削除に失敗しました。もう一度お試しください。`
};

/** @type {(inputs: {}) => string} */
const zh_profilepictureremovefailed3 = en_profilepictureremovefailed3;

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
const profilepictureremovefailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureremovefailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureremovefailed3", locale)
	if (locale === "en") return en_profilepictureremovefailed3(inputs)
	if (locale === "ko") return ko_profilepictureremovefailed3(inputs)
	if (locale === "ja") return ja_profilepictureremovefailed3(inputs)
	return zh_profilepictureremovefailed3(inputs)
};
export { profilepictureremovefailed3 as "profilePictureRemoveFailed" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
