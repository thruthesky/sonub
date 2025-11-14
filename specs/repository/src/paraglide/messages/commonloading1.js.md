---
name: commonloading1.js
description: commonloading1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/commonloading1.js
---

# commonloading1.js

## 개요

**파일 경로**: `src/paraglide/messages/commonloading1.js`
**파일 타입**: javascript
**카테고리**: other

commonloading1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading...`
};

const ko_commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `로딩 중...`
};

const ja_commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `読み込み中...`
};

const zh_commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `加载中...`
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
const commonloading1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonloading1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonloading1", locale)
	if (locale === "en") return en_commonloading1(inputs)
	if (locale === "ko") return ko_commonloading1(inputs)
	if (locale === "ja") return ja_commonloading1(inputs)
	return zh_commonloading1(inputs)
};
export { commonloading1 as "commonLoading" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
