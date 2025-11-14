---
name: admintestdesc2.js
description: admintestdesc2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/admintestdesc2.js
---

# admintestdesc2.js

## 개요

**파일 경로**: `src/paraglide/messages/admintestdesc2.js`
**파일 타입**: javascript
**카테고리**: other

admintestdesc2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `Use additional test features`
};

const ko_admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `기타 테스트 기능들을 사용합니다`
};

const ja_admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `その他のテスト機能を使用`
};

const zh_admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `使用其他测试功能`
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
const admintestdesc2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintestdesc2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintestdesc2", locale)
	if (locale === "en") return en_admintestdesc2(inputs)
	if (locale === "ko") return ko_admintestdesc2(inputs)
	if (locale === "ja") return ja_admintestdesc2(inputs)
	return zh_admintestdesc2(inputs)
};
export { admintestdesc2 as "adminTestDesc" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
