---
title: board.js
type: javascript
path: src/paraglide/messages/board.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/board.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_board = /** @type {(inputs: {}) => string} */ () => {
	return `论坛`
};

/** @type {(inputs: {}) => string} */
const en_board = () => 'board'

/** @type {(inputs: {}) => string} */
const ko_board = en_board;

/** @type {(inputs: {}) => string} */
const ja_board = en_board;

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
export const board = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.board(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("board", locale)
	if (locale === "en") return en_board(inputs)
	if (locale === "ko") return ko_board(inputs)
	if (locale === "ja") return ja_board(inputs)
	return zh_board(inputs)
};
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
