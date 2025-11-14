---
name: info.js
description: info 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/info.js
---

# info.js

## 개요

**파일 경로**: `src/paraglide/messages/info.js`
**파일 타입**: javascript
**카테고리**: other

info 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_info = /** @type {(inputs: {}) => string} */ () => {
	return `信息`
};

/** @type {(inputs: {}) => string} */
const en_info = () => 'info'

/** @type {(inputs: {}) => string} */
const ko_info = en_info;

/** @type {(inputs: {}) => string} */
const ja_info = en_info;

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
export const info = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.info(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("info", locale)
	if (locale === "en") return en_info(inputs)
	if (locale === "ko") return ko_info(inputs)
	if (locale === "ja") return ja_info(inputs)
	return zh_info(inputs)
};
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
