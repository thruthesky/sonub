---
name: authwelcomeuser2.js
description: authwelcomeuser2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/authwelcomeuser2.js
---

# authwelcomeuser2.js

## 개요

**파일 경로**: `src/paraglide/messages/authwelcomeuser2.js`
**파일 타입**: javascript
**카테고리**: other

authwelcomeuser2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Welcome, ${i.name}!`
};

const ko_authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.name}님, 로그인하셨습니다.`
};

const ja_authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.name}さん、ログインしました。`
};

const zh_authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `欢迎，${i.name}！`
};

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ name: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const authwelcomeuser2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authwelcomeuser2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authwelcomeuser2", locale)
	if (locale === "en") return en_authwelcomeuser2(inputs)
	if (locale === "ko") return ko_authwelcomeuser2(inputs)
	if (locale === "ja") return ja_authwelcomeuser2(inputs)
	return zh_authwelcomeuser2(inputs)
};
export { authwelcomeuser2 as "authWelcomeUser" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
