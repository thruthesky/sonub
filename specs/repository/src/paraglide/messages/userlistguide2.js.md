---
title: userlistguide2.js
type: javascript
path: src/paraglide/messages/userlistguide2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/userlistguide2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `View all registered users in Firebase Realtime Database`
};

const ko_userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Realtime Database에 등록된 모든 사용자를 확인하세요`
};

const ja_userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Realtime Databaseに登録されたすべてのユーザーを確認`
};

const zh_userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `查看 Firebase Realtime Database 中注册的所有用户`
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
const userlistguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userlistguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userlistguide2", locale)
	if (locale === "en") return en_userlistguide2(inputs)
	if (locale === "ko") return ko_userlistguide2(inputs)
	if (locale === "ja") return ja_userlistguide2(inputs)
	return zh_userlistguide2(inputs)
};
export { userlistguide2 as "userListGuide" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
