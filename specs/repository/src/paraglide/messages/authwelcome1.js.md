---
title: authwelcome1.js
type: javascript
path: src/paraglide/messages/authwelcome1.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/authwelcome1.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `Welcome`
};

const ko_authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `환영합니다`
};

const ja_authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `ようこそ`
};

const zh_authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `欢迎`
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
const authwelcome1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authwelcome1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authwelcome1", locale)
	if (locale === "en") return en_authwelcome1(inputs)
	if (locale === "ko") return ko_authwelcome1(inputs)
	if (locale === "ja") return ja_authwelcome1(inputs)
	return zh_authwelcome1(inputs)
};
export { authwelcome1 as "authWelcome" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
