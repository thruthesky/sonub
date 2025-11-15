---
title: profilesavesuccess2.js
type: javascript
path: src/paraglide/messages/profilesavesuccess2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilesavesuccess2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile updated successfully.`
};

const ko_profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필이 성공적으로 업데이트되었습니다.`
};

const ja_profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `プロフィールが正常に更新されました。`
};

const zh_profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料更新成功。`
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
const profilesavesuccess2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilesavesuccess2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilesavesuccess2", locale)
	if (locale === "en") return en_profilesavesuccess2(inputs)
	if (locale === "ko") return ko_profilesavesuccess2(inputs)
	if (locale === "ja") return ja_profilesavesuccess2(inputs)
	return zh_profilesavesuccess2(inputs)
};
export { profilesavesuccess2 as "profileSaveSuccess" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
