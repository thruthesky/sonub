---
name: sidebarmyprofile2.js
description: sidebarmyprofile2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/sidebarmyprofile2.js
---

# sidebarmyprofile2.js

## 개요

**파일 경로**: `src/paraglide/messages/sidebarmyprofile2.js`
**파일 타입**: javascript
**카테고리**: other

sidebarmyprofile2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Profile`
};

const ko_sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 프로필`
};

const ja_sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `マイプロフィール`
};

const zh_sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的资料`
};

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
const sidebarmyprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarmyprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarmyprofile2", locale)
	if (locale === "en") return en_sidebarmyprofile2(inputs)
	if (locale === "ko") return ko_sidebarmyprofile2(inputs)
	if (locale === "ja") return ja_sidebarmyprofile2(inputs)
	return zh_sidebarmyprofile2(inputs)
};
export { sidebarmyprofile2 as "sidebarMyProfile" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
