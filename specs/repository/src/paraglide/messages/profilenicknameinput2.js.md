---
name: profilenicknameinput2.js
description: profilenicknameinput2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilenicknameinput2.js
---

# profilenicknameinput2.js

## 개요

**파일 경로**: `src/paraglide/messages/profilenicknameinput2.js`
**파일 타입**: javascript
**카테고리**: other

profilenicknameinput2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `Enter your nickname`
};

const ko_profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임을 입력하세요`
};

const ja_profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `ニックネームを入力`
};

const zh_profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `请输入您的昵称`
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
const profilenicknameinput2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknameinput2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknameinput2", locale)
	if (locale === "en") return en_profilenicknameinput2(inputs)
	if (locale === "ko") return ko_profilenicknameinput2(inputs)
	if (locale === "ja") return ja_profilenicknameinput2(inputs)
	return zh_profilenicknameinput2(inputs)
};
export { profilenicknameinput2 as "profileNicknameInput" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
