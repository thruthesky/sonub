---
title: testuserguide2.js
type: javascript
path: src/paraglide/messages/testuserguide2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testuserguide2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `View and manage test temporary users.`
};

const ko_testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트용 임시 사용자 목록을 조회하고 관리합니다.`
};

const ja_testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `テスト用一時ユーザーリストを照会して管理します。`
};

const zh_testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `查看和管理测试临时用户。`
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
const testuserguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserguide2", locale)
	if (locale === "en") return en_testuserguide2(inputs)
	if (locale === "ko") return ko_testuserguide2(inputs)
	if (locale === "ja") return ja_testuserguide2(inputs)
	return zh_testuserguide2(inputs)
};
export { testuserguide2 as "testUserGuide" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
