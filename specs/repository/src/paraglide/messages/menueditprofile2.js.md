---
title: menueditprofile2.js
type: javascript
path: src/paraglide/messages/menueditprofile2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/menueditprofile2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `Edit Profile`
};

const ko_menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `회원 정보 수정`
};

const ja_menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `会員情報修正`
};

const zh_menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `编辑资料`
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
const menueditprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menueditprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menueditprofile2", locale)
	if (locale === "en") return en_menueditprofile2(inputs)
	if (locale === "ko") return ko_menueditprofile2(inputs)
	if (locale === "ja") return ja_menueditprofile2(inputs)
	return zh_menueditprofile2(inputs)
};
export { menueditprofile2 as "menuEditProfile" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
