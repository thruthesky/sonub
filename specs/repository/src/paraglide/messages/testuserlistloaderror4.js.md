---
name: testuserlistloaderror4.js
description: testuserlistloaderror4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserlistloaderror4.js
---

# testuserlistloaderror4.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserlistloaderror4.js`
**파일 타입**: javascript
**카테고리**: other

testuserlistloaderror4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `Error loading test user list:`
};

const ko_testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 목록 로드 중 오류:`
};

const ja_testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `テストユーザーリスト読み込み中のエラー：`
};

const zh_testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `加载测试用户列表时出错：`
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
const testuserlistloaderror4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserlistloaderror4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserlistloaderror4", locale)
	if (locale === "en") return en_testuserlistloaderror4(inputs)
	if (locale === "ko") return ko_testuserlistloaderror4(inputs)
	if (locale === "ja") return ja_testuserlistloaderror4(inputs)
	return zh_testuserlistloaderror4(inputs)
};
export { testuserlistloaderror4 as "testUserListLoadError" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
