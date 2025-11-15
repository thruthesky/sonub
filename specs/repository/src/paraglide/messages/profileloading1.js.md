---
title: profileloading1.js
type: javascript
path: src/paraglide/messages/profileloading1.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profileloading1.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading profile...`
};

const ko_profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 정보를 불러오는 중...`
};

const ja_profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `プロフィール情報を読み込み中...`
};

const zh_profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `加载个人资料...`
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
const profileloading1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileloading1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileloading1", locale)
	if (locale === "en") return en_profileloading1(inputs)
	if (locale === "ko") return ko_profileloading1(inputs)
	if (locale === "ja") return ja_profileloading1(inputs)
	return zh_profileloading1(inputs)
};
export { profileloading1 as "profileLoading" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
