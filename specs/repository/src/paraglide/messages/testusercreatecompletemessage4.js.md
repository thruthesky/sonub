---
name: testusercreatecompletemessage4.js
description: testusercreatecompletemessage4 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testusercreatecompletemessage4.js
---

# testusercreatecompletemessage4.js

## 개요

**파일 경로**: `src/paraglide/messages/testusercreatecompletemessage4.js`
**파일 타입**: javascript
**카테고리**: other

testusercreatecompletemessage4 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ Complete: ${i.count} test users created.`
};

const ko_testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ 완료: ${i.count}명의 테스트 사용자가 생성되었습니다.`
};

const ja_testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ 完了: ${i.count}人のテストユーザーが作成されました。`
};

const zh_testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ 完成：已创建 ${i.count} 个测试用户。`
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
const testusercreatecompletemessage4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreatecompletemessage4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreatecompletemessage4", locale)
	if (locale === "en") return en_testusercreatecompletemessage4(inputs)
	if (locale === "ko") return ko_testusercreatecompletemessage4(inputs)
	if (locale === "ja") return ja_testusercreatecompletemessage4(inputs)
	return zh_testusercreatecompletemessage4(inputs)
};
export { testusercreatecompletemessage4 as "testUserCreateCompleteMessage" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
