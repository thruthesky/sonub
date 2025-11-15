---
title: testuserdeleteallconfirmation4.js
type: javascript
path: src/paraglide/messages/testuserdeleteallconfirmation4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testuserdeleteallconfirmation4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_testuserdeleteallconfirmation4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `您要删除所有 ${i.count} 个测试用户吗？`
};

/** @type {(inputs: { count: NonNullable<unknown> }) => string} */
const en_testuserdeleteallconfirmation4 = () => 'testUserDeleteAllConfirmation'

/** @type {(inputs: { count: NonNullable<unknown> }) => string} */
const ko_testuserdeleteallconfirmation4 = en_testuserdeleteallconfirmation4;

/** @type {(inputs: { count: NonNullable<unknown> }) => string} */
const ja_testuserdeleteallconfirmation4 = en_testuserdeleteallconfirmation4;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuserdeleteallconfirmation4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteallconfirmation4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteallconfirmation4", locale)
	if (locale === "en") return en_testuserdeleteallconfirmation4(inputs)
	if (locale === "ko") return ko_testuserdeleteallconfirmation4(inputs)
	if (locale === "ja") return ja_testuserdeleteallconfirmation4(inputs)
	return zh_testuserdeleteallconfirmation4(inputs)
};
export { testuserdeleteallconfirmation4 as "testUserDeleteAllConfirmation" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
