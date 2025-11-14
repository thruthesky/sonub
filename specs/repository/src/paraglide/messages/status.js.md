---
name: status.js
description: status 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/status.js
---

# status.js

## 개요

**파일 경로**: `src/paraglide/messages/status.js`
**파일 타입**: javascript
**카테고리**: other

status 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_status = /** @type {(inputs: {}) => string} */ () => {
	return `状态`
};

/** @type {(inputs: {}) => string} */
const en_status = () => 'status'

/** @type {(inputs: {}) => string} */
const ko_status = en_status;

/** @type {(inputs: {}) => string} */
const ja_status = en_status;

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
export const status = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.status(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("status", locale)
	if (locale === "en") return en_status(inputs)
	if (locale === "ko") return ko_status(inputs)
	if (locale === "ja") return ja_status(inputs)
	return zh_status(inputs)
};
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
