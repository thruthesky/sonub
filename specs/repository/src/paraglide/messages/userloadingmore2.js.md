---
name: userloadingmore2.js
description: userloadingmore2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/userloadingmore2.js
---

# userloadingmore2.js

## 개요

**파일 경로**: `src/paraglide/messages/userloadingmore2.js`
**파일 타입**: javascript
**카테고리**: other

userloadingmore2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading more users...`
};

const ko_userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `더 많은 사용자를 불러오는 중...`
};

const ja_userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `さらにユーザーを読み込み中...`
};

const zh_userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `加载更多用户...`
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
const userloadingmore2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userloadingmore2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userloadingmore2", locale)
	if (locale === "en") return en_userloadingmore2(inputs)
	if (locale === "ko") return ko_userloadingmore2(inputs)
	if (locale === "ja") return ja_userloadingmore2(inputs)
	return zh_userloadingmore2(inputs)
};
export { userloadingmore2 as "userLoadingMore" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
