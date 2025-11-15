---
title: featurefirebaseauthdesc3.js
type: javascript
path: src/paraglide/messages/featurefirebaseauthdesc3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/featurefirebaseauthdesc3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `Google and Apple social login support`
};

const ko_featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `Google 및 Apple 소셜 로그인 지원`
};

const ja_featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `GoogleとAppleソーシャルログインサポート`
};

const zh_featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `支持 Google 和 Apple 社交登录`
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
const featurefirebaseauthdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featurefirebaseauthdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featurefirebaseauthdesc3", locale)
	if (locale === "en") return en_featurefirebaseauthdesc3(inputs)
	if (locale === "ko") return ko_featurefirebaseauthdesc3(inputs)
	if (locale === "ja") return ja_featurefirebaseauthdesc3(inputs)
	return zh_featurefirebaseauthdesc3(inputs)
};
export { featurefirebaseauthdesc3 as "featureFirebaseAuthDesc" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
