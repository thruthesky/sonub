---
name: linksveltekitdocs3.js
description: linksveltekitdocs3 파일
version: 1.0.0
type: javascript
category: other
original_path: src/paraglide/messages/linksveltekitdocs3.js
---

# linksveltekitdocs3.js

## 개요

**파일 경로**: `src/paraglide/messages/linksveltekitdocs3.js`
**파일 타입**: javascript
**카테고리**: other

linksveltekitdocs3 파일

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from '../runtime.js';

const en_linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit Docs`
};

const ko_linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 문서`
};

const ja_linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKitドキュメント`
};

const zh_linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 文档`
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
const linksveltekitdocs3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.linksveltekitdocs3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("linksveltekitdocs3", locale)
	if (locale === "en") return en_linksveltekitdocs3(inputs)
	if (locale === "ko") return ko_linksveltekitdocs3(inputs)
	if (locale === "ja") return ja_linksveltekitdocs3(inputs)
	return zh_linksveltekitdocs3(inputs)
};
export { linksveltekitdocs3 as "linkSvelteKitDocs" }
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
