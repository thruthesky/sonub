---
title: usernousersregistered3.js
type: javascript
path: src/paraglide/messages/usernousersregistered3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/usernousersregistered3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_usernousersregistered3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有注册用户`
};

/** @type {(inputs: {}) => string} */
const en_usernousersregistered3 = () => 'userNoUsersRegistered'

/** @type {(inputs: {}) => string} */
const ko_usernousersregistered3 = en_usernousersregistered3;

/** @type {(inputs: {}) => string} */
const ja_usernousersregistered3 = en_usernousersregistered3;

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
const usernousersregistered3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernousersregistered3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernousersregistered3", locale)
	if (locale === "en") return en_usernousersregistered3(inputs)
	if (locale === "ko") return ko_usernousersregistered3(inputs)
	if (locale === "ja") return ja_usernousersregistered3(inputs)
	return zh_usernousersregistered3(inputs)
};
export { usernousersregistered3 as "userNoUsersRegistered" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
