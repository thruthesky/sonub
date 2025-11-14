---
name: authsigninwithgoogle4.js
description: authsigninwithgoogle4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authsigninwithgoogle4.js
---

# authsigninwithgoogle4.js

## 개요

**파일 경로**: `src/paraglide/messages/authsigninwithgoogle4.js`
**파일 타입**: javascript
**카테고리**: other

authsigninwithgoogle4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in with Google`
};

const ko_authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `Google로 로그인`
};

const ja_authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `Googleでログイン`
};

const zh_authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Google 登录`
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
const authsigninwithgoogle4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninwithgoogle4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninwithgoogle4", locale)
	if (locale === "en") return en_authsigninwithgoogle4(inputs)
	if (locale === "ko") return ko_authsigninwithgoogle4(inputs)
	if (locale === "ja") return ja_authsigninwithgoogle4(inputs)
	return zh_authsigninwithgoogle4(inputs)
};
export { authsigninwithgoogle4 as "authSignInWithGoogle" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
