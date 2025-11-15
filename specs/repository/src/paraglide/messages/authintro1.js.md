---
title: authintro1.js
type: javascript
path: src/paraglide/messages/authintro1.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/authintro1.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `Adding community features to SedAi.Dev with Sonub spec.`
};

const ko_authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `SedAi.Dev 에 Sonub spec 으로 커뮤니티 기능을 집어 넣는다.`
};

const ja_authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `SedAi.DevにSonub specでコミュニティ機能を追加します。`
};

const zh_authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Sonub spec 为 SedAi.Dev 添加社区功能。`
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
const authintro1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authintro1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authintro1", locale)
	if (locale === "en") return en_authintro1(inputs)
	if (locale === "ko") return ko_authintro1(inputs)
	if (locale === "ja") return ja_authintro1(inputs)
	return zh_authintro1(inputs)
};
export { authintro1 as "authIntro" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
