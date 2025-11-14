---
name: authgetstarted2.js
description: authgetstarted2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authgetstarted2.js
---

# authgetstarted2.js

## 개요

**파일 경로**: `src/paraglide/messages/authgetstarted2.js`
**파일 타입**: javascript
**카테고리**: other

authgetstarted2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `Get Started`
};

const ko_authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `시작하기`
};

const ja_authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `始める`
};

const zh_authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `开始使用`
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
const authgetstarted2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authgetstarted2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authgetstarted2", locale)
	if (locale === "en") return en_authgetstarted2(inputs)
	if (locale === "ko") return ko_authgetstarted2(inputs)
	if (locale === "ja") return ja_authgetstarted2(inputs)
	return zh_authgetstarted2(inputs)
};
export { authgetstarted2 as "authGetStarted" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
