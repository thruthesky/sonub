---
name: navchat1.js
description: navchat1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/navchat1.js
---

# navchat1.js

## 개요

**파일 경로**: `src/paraglide/messages/navchat1.js`
**파일 타입**: javascript
**카테고리**: other

navchat1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat`
};

const ko_navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅`
};

const ja_navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `チャット`
};

const zh_navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天`
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
const navchat1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navchat1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navchat1", locale)
	if (locale === "en") return en_navchat1(inputs)
	if (locale === "ko") return ko_navchat1(inputs)
	if (locale === "ja") return ja_navchat1(inputs)
	return zh_navchat1(inputs)
};
export { navchat1 as "navChat" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
