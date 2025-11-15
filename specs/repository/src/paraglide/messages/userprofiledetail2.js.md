---
title: userprofiledetail2.js
type: javascript
path: src/paraglide/messages/userprofiledetail2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/userprofiledetail2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `User Profile Details`
};

const ko_userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 프로필 상세`
};

const ja_userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `ユーザープロフィール詳細`
};

const zh_userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `用户资料详情`
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
const userprofiledetail2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userprofiledetail2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userprofiledetail2", locale)
	if (locale === "en") return en_userprofiledetail2(inputs)
	if (locale === "ko") return ko_userprofiledetail2(inputs)
	if (locale === "ja") return ja_userprofiledetail2(inputs)
	return zh_userprofiledetail2(inputs)
};
export { userprofiledetail2 as "userProfileDetail" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
