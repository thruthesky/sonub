---
name: sidebarnorecentactivity3.js
description: sidebarnorecentactivity3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/sidebarnorecentactivity3.js
---

# sidebarnorecentactivity3.js

## 개요

**파일 경로**: `src/paraglide/messages/sidebarnorecentactivity3.js`
**파일 타입**: javascript
**카테고리**: other

sidebarnorecentactivity3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `No recent activity.`
};

const ko_sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `최근 활동이 없습니다.`
};

const ja_sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `最近のアクティビティはありません。`
};

const zh_sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有最近的活动。`
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
const sidebarnorecentactivity3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarnorecentactivity3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarnorecentactivity3", locale)
	if (locale === "en") return en_sidebarnorecentactivity3(inputs)
	if (locale === "ko") return ko_sidebarnorecentactivity3(inputs)
	if (locale === "ja") return ja_sidebarnorecentactivity3(inputs)
	return zh_sidebarnorecentactivity3(inputs)
};
export { sidebarnorecentactivity3 as "sidebarNoRecentActivity" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
