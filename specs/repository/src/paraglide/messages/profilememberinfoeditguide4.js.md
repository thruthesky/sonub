---
title: profilememberinfoeditguide4.js
type: javascript
path: src/paraglide/messages/profilememberinfoeditguide4.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/profilememberinfoeditguide4.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_profilememberinfoeditguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `您可以修改您的个人资料信息`
};

/** @type {(inputs: {}) => string} */
const en_profilememberinfoeditguide4 = () => 'profileMemberInfoEditGuide'

/** @type {(inputs: {}) => string} */
const ko_profilememberinfoeditguide4 = en_profilememberinfoeditguide4;

/** @type {(inputs: {}) => string} */
const ja_profilememberinfoeditguide4 = en_profilememberinfoeditguide4;

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
const profilememberinfoeditguide4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilememberinfoeditguide4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilememberinfoeditguide4", locale)
	if (locale === "en") return en_profilememberinfoeditguide4(inputs)
	if (locale === "ko") return ko_profilememberinfoeditguide4(inputs)
	if (locale === "ja") return ja_profilememberinfoeditguide4(inputs)
	return zh_profilememberinfoeditguide4(inputs)
};
export { profilememberinfoeditguide4 as "profileMemberInfoEditGuide" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
