---
title: testuserinfonorecover4.js
type: javascript
path: src/paraglide/messages/testuserinfonorecover4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testuserinfonorecover4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserinfonorecover4 = /** @type {(inputs: {}) => string} */ () => {
	return `• Deleted users cannot be recovered.`
};

const ko_testuserinfonorecover4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 삭제된 사용자는 복구할 수 없습니다.`
};

const ja_testuserinfonorecover4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 削除されたユーザーは復元できません。`
};

/** @type {(inputs: {}) => string} */
const zh_testuserinfonorecover4 = en_testuserinfonorecover4;

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
const testuserinfonorecover4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfonorecover4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfonorecover4", locale)
	if (locale === "en") return en_testuserinfonorecover4(inputs)
	if (locale === "ko") return ko_testuserinfonorecover4(inputs)
	if (locale === "ja") return ja_testuserinfonorecover4(inputs)
	return zh_testuserinfonorecover4(inputs)
};
export { testuserinfonorecover4 as "testUserInfoNoRecover" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
