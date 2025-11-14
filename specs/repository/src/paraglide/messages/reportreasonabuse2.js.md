---
name: reportreasonabuse2.js
description: reportreasonabuse2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/reportreasonabuse2.js
---

# reportreasonabuse2.js

## 개요

**파일 경로**: `src/paraglide/messages/reportreasonabuse2.js`
**파일 타입**: javascript
**카테고리**: other

reportreasonabuse2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `Abuse and Harassment`
};

const ko_reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `욕설 및 비방`
};

const ja_reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `誹謗中傷`
};

const zh_reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `辱骂和诽谤`
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
const reportreasonabuse2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportreasonabuse2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportreasonabuse2", locale)
	if (locale === "en") return en_reportreasonabuse2(inputs)
	if (locale === "ko") return ko_reportreasonabuse2(inputs)
	if (locale === "ja") return ja_reportreasonabuse2(inputs)
	return zh_reportreasonabuse2(inputs)
};
export { reportreasonabuse2 as "reportReasonAbuse" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
