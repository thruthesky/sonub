---
name: profilesavefailed2.js
description: profilesavefailed2 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/profilesavefailed2.js
---

# profilesavefailed2.js

## 개요

**파일 경로**: `src/paraglide/messages/profilesavefailed2.js`
**파일 타입**: javascript
**카테고리**: other

profilesavefailed2 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to save profile. Please try again.`
};

const ko_profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 저장에 실패했습니다. 다시 시도해주세요.`
};

const ja_profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `プロフィールの保存に失敗しました。もう一度お試しください。`
};

const zh_profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `保存个人资料失败。请重试。`
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
const profilesavefailed2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilesavefailed2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilesavefailed2", locale)
	if (locale === "en") return en_profilesavefailed2(inputs)
	if (locale === "ko") return ko_profilesavefailed2(inputs)
	if (locale === "ja") return ja_profilesavefailed2(inputs)
	return zh_profilesavefailed2(inputs)
};
export { profilesavefailed2 as "profileSaveFailed" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
