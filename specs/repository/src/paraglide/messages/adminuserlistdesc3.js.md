---
name: adminuserlistdesc3.js
description: adminuserlistdesc3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/adminuserlistdesc3.js
---

# adminuserlistdesc3.js

## 개요

**파일 경로**: `src/paraglide/messages/adminuserlistdesc3.js`
**파일 타입**: javascript
**카테고리**: other

adminuserlistdesc3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `View created test user list`
};

const ko_adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `생성된 테스트 사용자 목록을 확인합니다`
};

const ja_adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `作成されたテストユーザーリストを確認`
};

const zh_adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `查看已创建的测试用户列表`
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
const adminuserlistdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminuserlistdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminuserlistdesc3", locale)
	if (locale === "en") return en_adminuserlistdesc3(inputs)
	if (locale === "ko") return ko_adminuserlistdesc3(inputs)
	if (locale === "ja") return ja_adminuserlistdesc3(inputs)
	return zh_adminuserlistdesc3(inputs)
};
export { adminuserlistdesc3 as "adminUserListDesc" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
