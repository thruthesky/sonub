---
name: sidebarselectlanguage2.js
description: sidebarselectlanguage2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/sidebarselectlanguage2.js
---

# sidebarselectlanguage2.js

## 개요

**파일 경로**: `src/paraglide/messages/sidebarselectlanguage2.js`
**파일 타입**: javascript
**카테고리**: other

sidebarselectlanguage2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Language`
};

const ko_sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `언어 선택`
};

const ja_sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `言語選択`
};

const zh_sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `语言`
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
const sidebarselectlanguage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarselectlanguage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarselectlanguage2", locale)
	if (locale === "en") return en_sidebarselectlanguage2(inputs)
	if (locale === "ko") return ko_sidebarselectlanguage2(inputs)
	if (locale === "ja") return ja_sidebarselectlanguage2(inputs)
	return zh_sidebarselectlanguage2(inputs)
};
export { sidebarselectlanguage2 as "sidebarSelectLanguage" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
