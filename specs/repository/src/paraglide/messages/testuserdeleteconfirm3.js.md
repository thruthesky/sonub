---
name: testuserdeleteconfirm3.js
description: testuserdeleteconfirm3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserdeleteconfirm3.js
---

# testuserdeleteconfirm3.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserdeleteconfirm3.js`
**파일 타입**: javascript
**카테고리**: other

testuserdeleteconfirm3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `Do you want to delete this test user?`
};

const ko_testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `이 테스트 사용자를 삭제하시겠습니까?`
};

const ja_testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `このテストユーザーを削除しますか？`
};

const zh_testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `您要删除此测试用户吗？`
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
const testuserdeleteconfirm3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteconfirm3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteconfirm3", locale)
	if (locale === "en") return en_testuserdeleteconfirm3(inputs)
	if (locale === "ko") return ko_testuserdeleteconfirm3(inputs)
	if (locale === "ja") return ja_testuserdeleteconfirm3(inputs)
	return zh_testuserdeleteconfirm3(inputs)
};
export { testuserdeleteconfirm3 as "testUserDeleteConfirm" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
