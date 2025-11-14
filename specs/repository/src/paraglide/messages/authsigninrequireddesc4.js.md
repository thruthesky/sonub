---
name: authsigninrequireddesc4.js
description: authsigninrequireddesc4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authsigninrequireddesc4.js
---

# authsigninrequireddesc4.js

## 개요

**파일 경로**: `src/paraglide/messages/authsigninrequireddesc4.js`
**파일 타입**: javascript
**카테고리**: other

authsigninrequireddesc4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in to your account to access more features`
};

const ko_authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `계정에 로그인하여 더 많은 기능을 사용하세요`
};

const ja_authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `アカウントにログインしてより多くの機能を利用`
};

const zh_authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `登录您的账号以访问更多功能`
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
const authsigninrequireddesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninrequireddesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninrequireddesc4", locale)
	if (locale === "en") return en_authsigninrequireddesc4(inputs)
	if (locale === "ko") return ko_authsigninrequireddesc4(inputs)
	if (locale === "ja") return ja_authsigninrequireddesc4(inputs)
	return zh_authsigninrequireddesc4(inputs)
};
export { authsigninrequireddesc4 as "authSignInRequiredDesc" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
