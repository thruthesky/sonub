---
name: profilesave1.js
description: profilesave1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilesave1.js
---

# profilesave1.js

## 개요

**파일 경로**: `src/paraglide/messages/profilesave1.js`
**파일 타입**: javascript
**카테고리**: other

profilesave1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `Save Profile`
};

const ko_profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 저장`
};

const ja_profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `プロフィール保存`
};

const zh_profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `保存资料`
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
const profilesave1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilesave1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilesave1", locale)
	if (locale === "en") return en_profilesave1(inputs)
	if (locale === "ko") return ko_profilesave1(inputs)
	if (locale === "ja") return ja_profilesave1(inputs)
	return zh_profilesave1(inputs)
};
export { profilesave1 as "profileSave" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
