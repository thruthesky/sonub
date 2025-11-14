---
name: constructionbacktohome3.js
description: constructionbacktohome3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/constructionbacktohome3.js
---

# constructionbacktohome3.js

## 개요

**파일 경로**: `src/paraglide/messages/constructionbacktohome3.js`
**파일 타입**: javascript
**카테고리**: other

constructionbacktohome3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_constructionbacktohome3 = /** @type {(inputs: {}) => string} */ () => {
	return `Back to Home`
};

const ko_constructionbacktohome3 = /** @type {(inputs: {}) => string} */ () => {
	return `홈으로 돌아가기`
};

const ja_constructionbacktohome3 = /** @type {(inputs: {}) => string} */ () => {
	return `ホームに戻る`
};

/** @type {(inputs: {}) => string} */
const zh_constructionbacktohome3 = en_constructionbacktohome3;

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
const constructionbacktohome3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.constructionbacktohome3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("constructionbacktohome3", locale)
	if (locale === "en") return en_constructionbacktohome3(inputs)
	if (locale === "ko") return ko_constructionbacktohome3(inputs)
	if (locale === "ja") return ja_constructionbacktohome3(inputs)
	return zh_constructionbacktohome3(inputs)
};
export { constructionbacktohome3 as "constructionBackToHome" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
