---
title: admininfopermissionnotimplemented4.js
type: javascript
path: src/paraglide/messages/admininfopermissionnotimplemented4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/admininfopermissionnotimplemented4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
