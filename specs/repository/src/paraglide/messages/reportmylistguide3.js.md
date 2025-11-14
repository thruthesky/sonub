---
name: reportmylistguide3.js
description: reportmylistguide3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/reportmylistguide3.js
---

# reportmylistguide3.js

## 개요

**파일 경로**: `src/paraglide/messages/reportmylistguide3.js`
**파일 타입**: javascript
**카테고리**: other

reportmylistguide3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `View your submitted reports`
};

const ko_reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `내가 작성한 신고를 확인할 수 있습니다`
};

const ja_reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `あなたが送信した通報を確認できます`
};

const zh_reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `查看您提交的举报`
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
const reportmylistguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportmylistguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportmylistguide3", locale)
	if (locale === "en") return en_reportmylistguide3(inputs)
	if (locale === "ko") return ko_reportmylistguide3(inputs)
	if (locale === "ja") return ja_reportmylistguide3(inputs)
	return zh_reportmylistguide3(inputs)
};
export { reportmylistguide3 as "reportMyListGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
