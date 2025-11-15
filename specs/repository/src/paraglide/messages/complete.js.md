---
title: complete.js
type: javascript
path: src/paraglide/messages/complete.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/complete.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_complete = /** @type {(inputs: {}) => string} */ () => {
	return `完成`
};

/** @type {(inputs: {}) => string} */
const en_complete = () => 'complete'

/** @type {(inputs: {}) => string} */
const ko_complete = en_complete;

/** @type {(inputs: {}) => string} */
const ja_complete = en_complete;

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
export const complete = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.complete(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("complete", locale)
	if (locale === "en") return en_complete(inputs)
	if (locale === "ko") return ko_complete(inputs)
	if (locale === "ja") return ja_complete(inputs)
	return zh_complete(inputs)
};
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
