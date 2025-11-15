---
title: navmyprofile2.js
type: javascript
path: src/paraglide/messages/navmyprofile2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/navmyprofile2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Profile`
};

const ko_navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 프로필`
};

const ja_navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `マイプロフィール`
};

const zh_navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
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
const navmyprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navmyprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navmyprofile2", locale)
	if (locale === "en") return en_navmyprofile2(inputs)
	if (locale === "ko") return ko_navmyprofile2(inputs)
	if (locale === "ja") return ja_navmyprofile2(inputs)
	return zh_navmyprofile2(inputs)
};
export { navmyprofile2 as "navMyProfile" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
