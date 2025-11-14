---
name: testusercreateguide3.js
description: testusercreateguide3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusercreateguide3.js
---

# testusercreateguide3.js

## 개요

**파일 경로**: `src/paraglide/messages/testusercreateguide3.js`
**파일 타입**: javascript
**카테고리**: other

testusercreateguide3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `Click the button to sequentially create 100 test temporary users and add them to the list.`
};

const ko_testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `버튼을 클릭하면 테스트용 임시 사용자 100명이 순차적으로 생성되고 목록에 추가됩니다.`
};

const ja_testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `ボタンをクリックするとテスト用一時ユーザー100人が順次作成され、リストに追加されます。`
};

const zh_testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `点击按钮以顺序创建100个测试临时用户并将其添加到列表中。`
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
const testusercreateguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateguide3", locale)
	if (locale === "en") return en_testusercreateguide3(inputs)
	if (locale === "ko") return ko_testusercreateguide3(inputs)
	if (locale === "ja") return ja_testusercreateguide3(inputs)
	return zh_testusercreateguide3(inputs)
};
export { testusercreateguide3 as "testUserCreateGuide" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
