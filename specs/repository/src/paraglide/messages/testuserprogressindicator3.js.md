---
title: testuserprogressindicator3.js
type: javascript
path: src/paraglide/messages/testuserprogressindicator3.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/testuserprogressindicator3.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_testuserprogressindicator3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

/** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */
const en_testuserprogressindicator3 = () => 'testUserProgressIndicator'

/** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */
const ko_testuserprogressindicator3 = en_testuserprogressindicator3;

/** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */
const ja_testuserprogressindicator3 = en_testuserprogressindicator3;

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuserprogressindicator3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserprogressindicator3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserprogressindicator3", locale)
	if (locale === "en") return en_testuserprogressindicator3(inputs)
	if (locale === "ko") return ko_testuserprogressindicator3(inputs)
	if (locale === "ja") return ja_testuserprogressindicator3(inputs)
	return zh_testuserprogressindicator3(inputs)
};
export { testuserprogressindicator3 as "testUserProgressIndicator" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
