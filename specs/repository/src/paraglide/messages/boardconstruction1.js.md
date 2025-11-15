---
title: boardconstruction1.js
type: javascript
path: src/paraglide/messages/boardconstruction1.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/boardconstruction1.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_boardconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `Board feature is currently under development.`
};

const ko_boardconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판 기능은 현재 개발 중입니다.`
};

const ja_boardconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `掲示板機能は現在開発中です。`
};

/** @type {(inputs: {}) => string} */
const zh_boardconstruction1 = en_boardconstruction1;

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
const boardconstruction1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardconstruction1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardconstruction1", locale)
	if (locale === "en") return en_boardconstruction1(inputs)
	if (locale === "ko") return ko_boardconstruction1(inputs)
	if (locale === "ja") return ja_boardconstruction1(inputs)
	return zh_boardconstruction1(inputs)
};
export { boardconstruction1 as "boardConstruction" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
