---
name: helloworld1.js
description: helloworld1 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/helloworld1.js
---

# helloworld1.js

## 개요

**파일 경로**: `src/paraglide/messages/helloworld1.js`
**파일 타입**: javascript
**카테고리**: other

helloworld1 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from en!`
};

const ko_helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from ko!`
};

const ja_helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from ja!`
};

const zh_helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from zh!`
};

/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ name: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const helloworld1 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.helloworld1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("helloworld1", locale)
	if (locale === "en") return en_helloworld1(inputs)
	if (locale === "ko") return ko_helloworld1(inputs)
	if (locale === "ja") return ja_helloworld1(inputs)
	return zh_helloworld1(inputs)
};
export { helloworld1 as "helloWorld" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
