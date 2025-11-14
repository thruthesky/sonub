---
name: pagetitleboard2.js
description: pagetitleboard2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/pagetitleboard2.js
---

# pagetitleboard2.js

## 개요

**파일 경로**: `src/paraglide/messages/pagetitleboard2.js`
**파일 타입**: javascript
**카테고리**: other

pagetitleboard2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `Board - Sonub`
};

const ko_pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판 - Sonub`
};

const ja_pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `掲示板 - Sonub`
};

const zh_pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `论坛 - Sonub`
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
const pagetitleboard2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitleboard2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitleboard2", locale)
	if (locale === "en") return en_pagetitleboard2(inputs)
	if (locale === "ko") return ko_pagetitleboard2(inputs)
	if (locale === "ja") return ja_pagetitleboard2(inputs)
	return zh_pagetitleboard2(inputs)
};
export { pagetitleboard2 as "pageTitleBoard" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
