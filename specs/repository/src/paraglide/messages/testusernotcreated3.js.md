---
name: testusernotcreated3.js
description: testusernotcreated3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusernotcreated3.js
---

# testusernotcreated3.js

## 개요

**파일 경로**: `src/paraglide/messages/testusernotcreated3.js`
**파일 타입**: javascript
**카테고리**: other

testusernotcreated3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `No users created yet`
};

const ko_testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `아직 생성된 사용자 없음`
};

const ja_testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `まだ作成されたユーザーなし`
};

const zh_testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未创建用户`
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
const testusernotcreated3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernotcreated3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernotcreated3", locale)
	if (locale === "en") return en_testusernotcreated3(inputs)
	if (locale === "ko") return ko_testusernotcreated3(inputs)
	if (locale === "ja") return ja_testusernotcreated3(inputs)
	return zh_testusernotcreated3(inputs)
};
export { testusernotcreated3 as "testUserNotCreated" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
