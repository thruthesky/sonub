---
title: profilenicknamemaxlength3.js
type: javascript
path: src/paraglide/messages/profilenicknamemaxlength3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilenicknamemaxlength3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `Max 50 characters`
};

const ko_profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `최대 50자`
};

const ja_profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `最大50文字`
};

const zh_profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `最多50个字符`
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
const profilenicknamemaxlength3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknamemaxlength3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknamemaxlength3", locale)
	if (locale === "en") return en_profilenicknamemaxlength3(inputs)
	if (locale === "ko") return ko_profilenicknamemaxlength3(inputs)
	if (locale === "ja") return ja_profilenicknamemaxlength3(inputs)
	return zh_profilenicknamemaxlength3(inputs)
};
export { profilenicknamemaxlength3 as "profileNicknameMaxLength" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
