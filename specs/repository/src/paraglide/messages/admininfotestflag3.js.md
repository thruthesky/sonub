---
name: admininfotestflag3.js
description: admininfotestflag3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/admininfotestflag3.js
---

# admininfotestflag3.js

## 개요

**파일 경로**: `src/paraglide/messages/admininfotestflag3.js`
**파일 타입**: javascript
**카테고리**: other

admininfotestflag3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Test users are marked with \`isTemporary: true\` flag.`
};

const ko_admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 테스트 사용자는 \`isTemporary: true\` 플래그로 표시됩니다.`
};

const ja_admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• テストユーザーは\`isTemporary: true\`フラグで表示されます。`
};

const zh_admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 测试用户标记为 \`isTemporary: true\` 标志。`
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
const admininfotestflag3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admininfotestflag3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admininfotestflag3", locale)
	if (locale === "en") return en_admininfotestflag3(inputs)
	if (locale === "ko") return ko_admininfotestflag3(inputs)
	if (locale === "ja") return ja_admininfotestflag3(inputs)
	return zh_admininfotestflag3(inputs)
};
export { admininfotestflag3 as "adminInfoTestFlag" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
