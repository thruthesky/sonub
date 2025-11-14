---
name: testuserinfodisplay3.js
description: testuserinfodisplay3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/testuserinfodisplay3.js
---

# testuserinfodisplay3.js

## 개요

**파일 경로**: `src/paraglide/messages/testuserinfodisplay3.js`
**파일 타입**: javascript
**카테고리**: other

testuserinfodisplay3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Only users marked with \`isTemporary: true\` are displayed on this page.`
};

const ko_testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 이 페이지에는 \`isTemporary: true\`로 표시된 사용자만 표시됩니다.`
};

const ja_testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• このページには\`isTemporary: true\`で表示されたユーザーのみ表示されます。`
};

const zh_testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 此页面仅显示标记为 \`isTemporary: true\` 的用户。`
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
const testuserinfodisplay3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfodisplay3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfodisplay3", locale)
	if (locale === "en") return en_testuserinfodisplay3(inputs)
	if (locale === "ko") return ko_testuserinfodisplay3(inputs)
	if (locale === "ja") return ja_testuserinfodisplay3(inputs)
	return zh_testuserinfodisplay3(inputs)
};
export { testuserinfodisplay3 as "testUserInfoDisplay" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
