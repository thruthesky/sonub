---
title: navmenu1.js
type: javascript
path: src/paraglide/messages/navmenu1.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/navmenu1.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `Menu`
};

const ko_navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `메뉴`
};

const ja_navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `メニュー`
};

const zh_navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `菜单`
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
const navmenu1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navmenu1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navmenu1", locale)
	if (locale === "en") return en_navmenu1(inputs)
	if (locale === "ko") return ko_navmenu1(inputs)
	if (locale === "ja") return ja_navmenu1(inputs)
	return zh_navmenu1(inputs)
};
export { navmenu1 as "navMenu" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
