---
name: admininfodatadelete3.js
description: admininfodatadelete3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/admininfodatadelete3.js
---

# admininfodatadelete3.js

## 개요

**파일 경로**: `src/paraglide/messages/admininfodatadelete3.js`
**파일 타입**: javascript
**카테고리**: other

admininfodatadelete3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Test data can be deleted at any time.`
};

const ko_admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 테스트 데이터는 언제든지 삭제할 수 있습니다.`
};

const ja_admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• テストデータはいつでも削除できます。`
};

const zh_admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 测试数据可以随时删除。`
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
const admininfodatadelete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admininfodatadelete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admininfodatadelete3", locale)
	if (locale === "en") return en_admininfodatadelete3(inputs)
	if (locale === "ko") return ko_admininfodatadelete3(inputs)
	if (locale === "ja") return ja_admininfodatadelete3(inputs)
	return zh_admininfodatadelete3(inputs)
};
export { admininfodatadelete3 as "adminInfoDataDelete" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
