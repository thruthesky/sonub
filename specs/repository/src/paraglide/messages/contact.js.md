---
title: contact.js
type: javascript
path: src/paraglide/messages/contact.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/contact.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const zh_contact = /** @type {(inputs: {}) => string} */ () => {
	return `联系`
};

/** @type {(inputs: {}) => string} */
const en_contact = () => 'contact'

/** @type {(inputs: {}) => string} */
const ko_contact = en_contact;

/** @type {(inputs: {}) => string} */
const ja_contact = en_contact;

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
export const contact = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.contact(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("contact", locale)
	if (locale === "en") return en_contact(inputs)
	if (locale === "ko") return ko_contact(inputs)
	if (locale === "ja") return ja_contact(inputs)
	return zh_contact(inputs)
};
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
