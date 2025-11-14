---
name: authsigninaction3.js
description: authsigninaction3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authsigninaction3.js
---

# authsigninaction3.js

## 개요

**파일 경로**: `src/paraglide/messages/authsigninaction3.js`
**파일 타입**: javascript
**카테고리**: other

authsigninaction3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign In`
};

const ko_authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인하기`
};

const ja_authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `ログイン`
};

const zh_authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `登录`
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
const authsigninaction3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninaction3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninaction3", locale)
	if (locale === "en") return en_authsigninaction3(inputs)
	if (locale === "ko") return ko_authsigninaction3(inputs)
	if (locale === "ja") return ja_authsigninaction3(inputs)
	return zh_authsigninaction3(inputs)
};
export { authsigninaction3 as "authSignInAction" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
