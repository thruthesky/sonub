---
name: menuguide1.js
description: menuguide1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/menuguide1.js
---

# menuguide1.js

## 개요

**파일 경로**: `src/paraglide/messages/menuguide1.js`
**파일 타입**: javascript
**카테고리**: other

menuguide1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `Account & Settings`
};

const ko_menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `계정 및 설정 관리`
};

const ja_menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `アカウントと設定管理`
};

const zh_menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `账号与设置`
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
const menuguide1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menuguide1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menuguide1", locale)
	if (locale === "en") return en_menuguide1(inputs)
	if (locale === "ko") return ko_menuguide1(inputs)
	if (locale === "ja") return ja_menuguide1(inputs)
	return zh_menuguide1(inputs)
};
export { menuguide1 as "menuGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
