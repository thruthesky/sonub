---
name: authwelcomemessage2.js
description: authwelcomemessage2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authwelcomemessage2.js
---

# authwelcomemessage2.js

## 개요

**파일 경로**: `src/paraglide/messages/authwelcomemessage2.js`
**파일 타입**: javascript
**카테고리**: other

authwelcomemessage2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Welcome to Sonub`
};

const ko_authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Welcome to Sonub`
};

const ja_authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Sonubへようこそ`
};

const zh_authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `欢迎来到 Sonub`
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
const authwelcomemessage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authwelcomemessage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authwelcomemessage2", locale)
	if (locale === "en") return en_authwelcomemessage2(inputs)
	if (locale === "ko") return ko_authwelcomemessage2(inputs)
	if (locale === "ja") return ja_authwelcomemessage2(inputs)
	return zh_authwelcomemessage2(inputs)
};
export { authwelcomemessage2 as "authWelcomeMessage" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
