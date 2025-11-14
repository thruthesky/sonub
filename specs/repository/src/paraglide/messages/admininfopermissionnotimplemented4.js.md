---
name: admininfopermissionnotimplemented4.js
description: admininfopermissionnotimplemented4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/admininfopermissionnotimplemented4.js
---

# admininfopermissionnotimplemented4.js

## 개요

**파일 경로**: `src/paraglide/messages/admininfopermissionnotimplemented4.js`
**파일 타입**: javascript
**카테고리**: other

admininfopermissionnotimplemented4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• Admin permission verification is not yet implemented.`
};

const ko_admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 현재 관리자 권한 검증은 구현되지 않았습니다.`
};

const ja_admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 現在、管理者権限検証は実装されていません。`
};

const zh_admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 管理员权限验证尚未实施。`
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
const admininfopermissionnotimplemented4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admininfopermissionnotimplemented4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admininfopermissionnotimplemented4", locale)
	if (locale === "en") return en_admininfopermissionnotimplemented4(inputs)
	if (locale === "ko") return ko_admininfopermissionnotimplemented4(inputs)
	if (locale === "ja") return ja_admininfopermissionnotimplemented4(inputs)
	return zh_admininfopermissionnotimplemented4(inputs)
};
export { admininfopermissionnotimplemented4 as "adminInfoPermissionNotImplemented" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
