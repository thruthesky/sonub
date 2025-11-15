---
title: featuresveltekit5desc2.js
type: javascript
path: src/paraglide/messages/featuresveltekit5desc2.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/paraglide/messages/featuresveltekit5desc2.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `Modern framework using latest Svelte 5 runes`
};

const ko_featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `최신 Svelte 5 runes를 사용한 모던 프레임워크`
};

const ja_featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `最新のSvelte 5 runesを使用したモダンフレームワーク`
};

const zh_featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `使用最新 Svelte 5 runes 的现代框架`
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
const featuresveltekit5desc2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featuresveltekit5desc2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featuresveltekit5desc2", locale)
	if (locale === "en") return en_featuresveltekit5desc2(inputs)
	if (locale === "ko") return ko_featuresveltekit5desc2(inputs)
	if (locale === "ja") return ja_featuresveltekit5desc2(inputs)
	return zh_featuresveltekit5desc2(inputs)
};
export { featuresveltekit5desc2 as "featureSveltekit5Desc" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
