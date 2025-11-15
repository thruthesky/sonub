---
title: testusernotcreatedguide4.js
type: javascript
path: src/paraglide/messages/testusernotcreatedguide4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testusernotcreatedguide4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusernotcreatedguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `No test users have been created. You can use the <strong>Create Test Users</strong> feature above to create 100.`
};

const ko_testusernotcreatedguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `생성된 테스트 사용자가 없습니다. 위의 <strong>테스트 사용자 생성</strong> 기능을 사용해 100명을 생성할 수 있습니다.`
};

const ja_testusernotcreatedguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `作成されたテストユーザーがいません。上記の<strong>テストユーザー作成</strong>機能を使用して100人を作成できます。`
};

/** @type {(inputs: {}) => string} */
const zh_testusernotcreatedguide4 = en_testusernotcreatedguide4;

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
const testusernotcreatedguide4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernotcreatedguide4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernotcreatedguide4", locale)
	if (locale === "en") return en_testusernotcreatedguide4(inputs)
	if (locale === "ko") return ko_testusernotcreatedguide4(inputs)
	if (locale === "ja") return ja_testusernotcreatedguide4(inputs)
	return zh_testusernotcreatedguide4(inputs)
};
export { testusernotcreatedguide4 as "testUserNotCreatedGuide" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
