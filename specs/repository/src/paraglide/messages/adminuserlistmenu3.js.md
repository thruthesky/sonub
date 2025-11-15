---
title: adminuserlistmenu3.js
type: javascript
path: src/paraglide/messages/adminuserlistmenu3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/adminuserlistmenu3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `Users`
};

const ko_adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자목록`
};

const ja_adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `ユーザーリスト`
};

const zh_adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `用户`
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
const adminuserlistmenu3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminuserlistmenu3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminuserlistmenu3", locale)
	if (locale === "en") return en_adminuserlistmenu3(inputs)
	if (locale === "ko") return ko_adminuserlistmenu3(inputs)
	if (locale === "ja") return ja_adminuserlistmenu3(inputs)
	return zh_adminuserlistmenu3(inputs)
};
export { adminuserlistmenu3 as "adminUserListMenu" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
