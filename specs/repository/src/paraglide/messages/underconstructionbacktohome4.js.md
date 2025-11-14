---
name: underconstructionbacktohome4.js
description: underconstructionbacktohome4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/underconstructionbacktohome4.js
---

# underconstructionbacktohome4.js

## 개요

**파일 경로**: `src/paraglide/messages/underconstructionbacktohome4.js`
**파일 타입**: javascript
**카테고리**: other

underconstructionbacktohome4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_underconstructionbacktohome4 = /** @type {(inputs: {}) => string} */ () => {
	return `返回首页`
};

/** @type {(inputs: {}) => string} */
const en_underconstructionbacktohome4 = () => 'underConstructionBackToHome'

/** @type {(inputs: {}) => string} */
const ko_underconstructionbacktohome4 = en_underconstructionbacktohome4;

/** @type {(inputs: {}) => string} */
const ja_underconstructionbacktohome4 = en_underconstructionbacktohome4;

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
const underconstructionbacktohome4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.underconstructionbacktohome4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("underconstructionbacktohome4", locale)
	if (locale === "en") return en_underconstructionbacktohome4(inputs)
	if (locale === "ko") return ko_underconstructionbacktohome4(inputs)
	if (locale === "ja") return ja_underconstructionbacktohome4(inputs)
	return zh_underconstructionbacktohome4(inputs)
};
export { underconstructionbacktohome4 as "underConstructionBackToHome" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
