---
name: admintestusermanagementdesc4.js
description: admintestusermanagementdesc4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/admintestusermanagementdesc4.js
---

# admintestusermanagementdesc4.js

## 개요

**파일 경로**: `src/paraglide/messages/admintestusermanagementdesc4.js`
**파일 타입**: javascript
**카테고리**: other

admintestusermanagementdesc4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Manage temporary user creation, listing, and deletion on one page`
};

const ko_admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `임시 사용자 생성/목록/삭제를 한 페이지에서 관리합니다`
};

const ja_admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `一時ユーザーの作成/リスト/削除を1ページで管理`
};

const zh_admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `在一个页面上管理临时用户创建、列表和删除`
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
const admintestusermanagementdesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintestusermanagementdesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintestusermanagementdesc4", locale)
	if (locale === "en") return en_admintestusermanagementdesc4(inputs)
	if (locale === "ko") return ko_admintestusermanagementdesc4(inputs)
	if (locale === "ja") return ja_admintestusermanagementdesc4(inputs)
	return zh_admintestusermanagementdesc4(inputs)
};
export { admintestusermanagementdesc4 as "adminTestUserManagementDesc" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
