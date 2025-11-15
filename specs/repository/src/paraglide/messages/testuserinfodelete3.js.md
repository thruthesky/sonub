---
title: testuserinfodelete3.js
type: javascript
path: src/paraglide/messages/testuserinfodelete3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testuserinfodelete3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Each user can be deleted individually or in bulk.`
};

const ko_testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 각 사용자는 개별적으로 또는 일괄적으로 삭제할 수 있습니다.`
};

const ja_testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 各ユーザーは個別または一括で削除できます。`
};

const zh_testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 每个用户可以单独或批量删除。`
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
const testuserinfodelete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfodelete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfodelete3", locale)
	if (locale === "en") return en_testuserinfodelete3(inputs)
	if (locale === "ko") return ko_testuserinfodelete3(inputs)
	if (locale === "ja") return ja_testuserinfodelete3(inputs)
	return zh_testuserinfodelete3(inputs)
};
export { testuserinfodelete3 as "testUserInfoDelete" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
