---
name: sidebarnotifications1.js
description: sidebarnotifications1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/sidebarnotifications1.js
---

# sidebarnotifications1.js

## 개요

**파일 경로**: `src/paraglide/messages/sidebarnotifications1.js`
**파일 타입**: javascript
**카테고리**: other

sidebarnotifications1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `Notifications`
};

const ko_sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `알림`
};

const ja_sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `通知`
};

const zh_sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `通知`
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
const sidebarnotifications1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarnotifications1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarnotifications1", locale)
	if (locale === "en") return en_sidebarnotifications1(inputs)
	if (locale === "ko") return ko_sidebarnotifications1(inputs)
	if (locale === "ja") return ja_sidebarnotifications1(inputs)
	return zh_sidebarnotifications1(inputs)
};
export { sidebarnotifications1 as "sidebarNotifications" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
