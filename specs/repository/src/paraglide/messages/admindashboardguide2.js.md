---
name: admindashboardguide2.js
description: admindashboardguide2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/admindashboardguide2.js
---

# admindashboardguide2.js

## 개요

**파일 경로**: `src/paraglide/messages/admindashboardguide2.js`
**파일 타입**: javascript
**카테고리**: other

admindashboardguide2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Select an admin tool to get started.`
};

const ko_admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `관리 도구를 선택하여 작업을 시작하세요.`
};

const ja_admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `管理ツールを選択して作業を開始してください。`
};

const zh_admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `选择管理工具以开始。`
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
const admindashboardguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admindashboardguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admindashboardguide2", locale)
	if (locale === "en") return en_admindashboardguide2(inputs)
	if (locale === "ko") return ko_admindashboardguide2(inputs)
	if (locale === "ja") return ja_admindashboardguide2(inputs)
	return zh_admindashboardguide2(inputs)
};
export { admindashboardguide2 as "adminDashboardGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
