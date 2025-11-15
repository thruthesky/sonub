---
title: authsigninrequired3.js
type: javascript
path: src/paraglide/messages/authsigninrequired3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/authsigninrequired3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign In Required`
};

const ko_authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인 필요`
};

const ja_authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `ログインが必要です`
};

const zh_authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `需要登录`
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
const authsigninrequired3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninrequired3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninrequired3", locale)
	if (locale === "en") return en_authsigninrequired3(inputs)
	if (locale === "ko") return ko_authsigninrequired3(inputs)
	if (locale === "ja") return ja_authsigninrequired3(inputs)
	return zh_authsigninrequired3(inputs)
};
export { authsigninrequired3 as "authSignInRequired" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
