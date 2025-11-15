---
title: profileloadfailed2.js
type: javascript
path: src/paraglide/messages/profileloadfailed2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profileloadfailed2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to load profile information.`
};

const ko_profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 정보를 불러오는데 실패했습니다.`
};

const ja_profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `プロフィール情報の読み込みに失敗しました。`
};

const zh_profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `加载个人资料信息失败。`
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
const profileloadfailed2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileloadfailed2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileloadfailed2", locale)
	if (locale === "en") return en_profileloadfailed2(inputs)
	if (locale === "ko") return ko_profileloadfailed2(inputs)
	if (locale === "ja") return ja_profileloadfailed2(inputs)
	return zh_profileloadfailed2(inputs)
};
export { profileloadfailed2 as "profileLoadFailed" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
