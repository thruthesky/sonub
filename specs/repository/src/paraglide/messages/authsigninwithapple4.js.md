---
name: authsigninwithapple4.js
description: authsigninwithapple4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authsigninwithapple4.js
---

# authsigninwithapple4.js

## 개요

**파일 경로**: `src/paraglide/messages/authsigninwithapple4.js`
**파일 타입**: javascript
**카테고리**: other

authsigninwithapple4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in with Apple`
};

const ko_authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `Apple로 로그인`
};

const ja_authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `Appleでログイン`
};

const zh_authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Apple 登录`
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
const authsigninwithapple4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninwithapple4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninwithapple4", locale)
	if (locale === "en") return en_authsigninwithapple4(inputs)
	if (locale === "ko") return ko_authsigninwithapple4(inputs)
	if (locale === "ja") return ja_authsigninwithapple4(inputs)
	return zh_authsigninwithapple4(inputs)
};
export { authsigninwithapple4 as "authSignInWithApple" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
