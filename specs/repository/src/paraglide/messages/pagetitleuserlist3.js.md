---
title: pagetitleuserlist3.js
type: javascript
path: src/paraglide/messages/pagetitleuserlist3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/pagetitleuserlist3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `User List - Sonub`
};

const ko_pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록 - Sonub`
};

const ja_pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `ユーザーリスト - Sonub`
};

const zh_pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `用户列表 - Sonub`
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
const pagetitleuserlist3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitleuserlist3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitleuserlist3", locale)
	if (locale === "en") return en_pagetitleuserlist3(inputs)
	if (locale === "ko") return ko_pagetitleuserlist3(inputs)
	if (locale === "ja") return ja_pagetitleuserlist3(inputs)
	return zh_pagetitleuserlist3(inputs)
};
export { pagetitleuserlist3 as "pageTitleUserList" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
