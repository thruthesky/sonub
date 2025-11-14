---
name: pagetitlechat2.js
description: pagetitlechat2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/pagetitlechat2.js
---

# pagetitlechat2.js

## 개요

**파일 경로**: `src/paraglide/messages/pagetitlechat2.js`
**파일 타입**: javascript
**카테고리**: other

pagetitlechat2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat - Sonub`
};

const ko_pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅 - Sonub`
};

const ja_pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `チャット - Sonub`
};

const zh_pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天 - Sonub`
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
const pagetitlechat2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlechat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlechat2", locale)
	if (locale === "en") return en_pagetitlechat2(inputs)
	if (locale === "ko") return ko_pagetitlechat2(inputs)
	if (locale === "ja") return ja_pagetitlechat2(inputs)
	return zh_pagetitlechat2(inputs)
};
export { pagetitlechat2 as "pageTitleChat" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
