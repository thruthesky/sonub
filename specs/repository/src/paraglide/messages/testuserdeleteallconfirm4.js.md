---
name: testuserdeleteallconfirm4.js
description: testuserdeleteallconfirm4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserdeleteallconfirm4.js
---

# testuserdeleteallconfirm4.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserdeleteallconfirm4.js`
**파일 타입**: javascript
**카테고리**: other

testuserdeleteallconfirm4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `Do you want to delete all ${i.count} test users?`
};

const ko_testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count}명의 모든 테스트 사용자를 삭제하시겠습니까?`
};

const ja_testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count}人のすべてのテストユーザーを削除しますか？`
};

const zh_testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `您要删除所有 ${i.count} 个测试用户吗？`
};

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuserdeleteallconfirm4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteallconfirm4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteallconfirm4", locale)
	if (locale === "en") return en_testuserdeleteallconfirm4(inputs)
	if (locale === "ko") return ko_testuserdeleteallconfirm4(inputs)
	if (locale === "ja") return ja_testuserdeleteallconfirm4(inputs)
	return zh_testuserdeleteallconfirm4(inputs)
};
export { testuserdeleteallconfirm4 as "testUserDeleteAllConfirm" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
