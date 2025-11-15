---
title: _index.js
type: javascript
path: src/lib/paraglide/messages/_index.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/paraglide/messages/_index.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */
import { getLocale, trackMessageCall, experimentalMiddlewareLocaleSplitting, isServer } from "../runtime.js"
import * as en from "./en.js"
import * as ko from "./ko.js"
import * as ja from "./ja.js"
import * as zh from "./zh.js"
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
	if (locale === "en") return en.helloworld1(inputs)
	if (locale === "ko") return ko.helloworld1(inputs)
	if (locale === "ja") return ja.helloworld1(inputs)
	return zh.helloworld1(inputs)
};
export { helloworld1 as "helloWorld" }
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
const commonloading1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonloading1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonloading1", locale)
	if (locale === "en") return en.commonloading1(inputs)
	if (locale === "ko") return ko.commonloading1(inputs)
	if (locale === "ja") return ja.commonloading1(inputs)
	return zh.commonloading1(inputs)
};
export { commonloading1 as "commonLoading" }
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
const commonclose1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonclose1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonclose1", locale)
	if (locale === "en") return en.commonclose1(inputs)
	if (locale === "ko") return ko.commonclose1(inputs)
	if (locale === "ja") return ja.commonclose1(inputs)
	return zh.commonclose1(inputs)
};
export { commonclose1 as "commonClose" }
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
const commonsave1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonsave1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonsave1", locale)
	if (locale === "en") return en.commonsave1(inputs)
	if (locale === "ko") return ko.commonsave1(inputs)
	if (locale === "ja") return ja.commonsave1(inputs)
	return zh.commonsave1(inputs)
};
export { commonsave1 as "commonSave" }
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
const commondelete1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commondelete1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commondelete1", locale)
	if (locale === "en") return en.commondelete1(inputs)
	if (locale === "ko") return ko.commondelete1(inputs)
	if (locale === "ja") return ja.commondelete1(inputs)
	return zh.commondelete1(inputs)
};
export { commondelete1 as "commonDelete" }
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
const commoncancel1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commoncancel1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commoncancel1", locale)
	if (locale === "en") return en.commoncancel1(inputs)
	if (locale === "ko") return ko.commoncancel1(inputs)
	if (locale === "ja") return ja.commoncancel1(inputs)
	return zh.commoncancel1(inputs)
};
export { commoncancel1 as "commonCancel" }
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
const commonconfirm1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonconfirm1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonconfirm1", locale)
	if (locale === "en") return en.commonconfirm1(inputs)
	if (locale === "ko") return ko.commonconfirm1(inputs)
	if (locale === "ja") return ja.commonconfirm1(inputs)
	return zh.commonconfirm1(inputs)
};
export { commonconfirm1 as "commonConfirm" }
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
const commonrefresh1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonrefresh1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonrefresh1", locale)
	if (locale === "en") return en.commonrefresh1(inputs)
	if (locale === "ko") return ko.commonrefresh1(inputs)
	if (locale === "ja") return ja.commonrefresh1(inputs)
	return zh.commonrefresh1(inputs)
};
export { commonrefresh1 as "commonRefresh" }
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
const commonretry1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonretry1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonretry1", locale)
	if (locale === "en") return en.commonretry1(inputs)
	if (locale === "ko") return ko.commonretry1(inputs)
	if (locale === "ja") return ja.commonretry1(inputs)
	return zh.commonretry1(inputs)
};
export { commonretry1 as "commonRetry" }
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
const commoninfo1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commoninfo1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commoninfo1", locale)
	if (locale === "en") return en.commoninfo1(inputs)
	if (locale === "ko") return ko.commoninfo1(inputs)
	if (locale === "ja") return ja.commoninfo1(inputs)
	return zh.commoninfo1(inputs)
};
export { commoninfo1 as "commonInfo" }
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
const commonstatus1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonstatus1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonstatus1", locale)
	if (locale === "en") return en.commonstatus1(inputs)
	if (locale === "ko") return ko.commonstatus1(inputs)
	if (locale === "ja") return ja.commonstatus1(inputs)
	return zh.commonstatus1(inputs)
};
export { commonstatus1 as "commonStatus" }
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
const commonsuccess1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonsuccess1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonsuccess1", locale)
	if (locale === "en") return en.commonsuccess1(inputs)
	if (locale === "ko") return ko.commonsuccess1(inputs)
	if (locale === "ja") return ja.commonsuccess1(inputs)
	return zh.commonsuccess1(inputs)
};
export { commonsuccess1 as "commonSuccess" }
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
const commonerror1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonerror1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonerror1", locale)
	if (locale === "en") return en.commonerror1(inputs)
	if (locale === "ko") return ko.commonerror1(inputs)
	if (locale === "ja") return ja.commonerror1(inputs)
	return zh.commonerror1(inputs)
};
export { commonerror1 as "commonError" }
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
const commoncomplete1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commoncomplete1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commoncomplete1", locale)
	if (locale === "en") return en.commoncomplete1(inputs)
	if (locale === "ko") return ko.commoncomplete1(inputs)
	if (locale === "ja") return ja.commoncomplete1(inputs)
	return zh.commoncomplete1(inputs)
};
export { commoncomplete1 as "commonComplete" }
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
const commonprogress1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonprogress1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonprogress1", locale)
	if (locale === "en") return en.commonprogress1(inputs)
	if (locale === "ko") return ko.commonprogress1(inputs)
	if (locale === "ja") return ja.commonprogress1(inputs)
	return zh.commonprogress1(inputs)
};
export { commonprogress1 as "commonProgress" }
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
const commongo1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commongo1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commongo1", locale)
	if (locale === "en") return en.commongo1(inputs)
	if (locale === "ko") return ko.commongo1(inputs)
	if (locale === "ja") return ja.commongo1(inputs)
	return zh.commongo1(inputs)
};
export { commongo1 as "commonGo" }
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
const commonuser1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonuser1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonuser1", locale)
	if (locale === "en") return en.commonuser1(inputs)
	if (locale === "ko") return ko.commonuser1(inputs)
	if (locale === "ja") return ja.commonuser1(inputs)
	return zh.commonuser1(inputs)
};
export { commonuser1 as "commonUser" }
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
const navhome1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navhome1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navhome1", locale)
	if (locale === "en") return en.navhome1(inputs)
	if (locale === "ko") return ko.navhome1(inputs)
	if (locale === "ja") return ja.navhome1(inputs)
	return zh.navhome1(inputs)
};
export { navhome1 as "navHome" }
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
const navabout1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navabout1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navabout1", locale)
	if (locale === "en") return en.navabout1(inputs)
	if (locale === "ko") return ko.navabout1(inputs)
	if (locale === "ja") return ja.navabout1(inputs)
	return zh.navabout1(inputs)
};
export { navabout1 as "navAbout" }
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
const navproducts1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navproducts1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navproducts1", locale)
	if (locale === "en") return en.navproducts1(inputs)
	if (locale === "ko") return ko.navproducts1(inputs)
	if (locale === "ja") return ja.navproducts1(inputs)
	return zh.navproducts1(inputs)
};
export { navproducts1 as "navProducts" }
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
const navcontact1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navcontact1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navcontact1", locale)
	if (locale === "en") return en.navcontact1(inputs)
	if (locale === "ko") return ko.navcontact1(inputs)
	if (locale === "ja") return ja.navcontact1(inputs)
	return zh.navcontact1(inputs)
};
export { navcontact1 as "navContact" }
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
const navboard1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navboard1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navboard1", locale)
	if (locale === "en") return en.navboard1(inputs)
	if (locale === "ko") return ko.navboard1(inputs)
	if (locale === "ja") return ja.navboard1(inputs)
	return zh.navboard1(inputs)
};
export { navboard1 as "navBoard" }
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
const navchat1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navchat1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navchat1", locale)
	if (locale === "en") return en.navchat1(inputs)
	if (locale === "ko") return ko.navchat1(inputs)
	if (locale === "ja") return ja.navchat1(inputs)
	return zh.navchat1(inputs)
};
export { navchat1 as "navChat" }
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
const navfindusers2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navfindusers2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navfindusers2", locale)
	if (locale === "en") return en.navfindusers2(inputs)
	if (locale === "ko") return ko.navfindusers2(inputs)
	if (locale === "ja") return ja.navfindusers2(inputs)
	return zh.navfindusers2(inputs)
};
export { navfindusers2 as "navFindUsers" }
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
const navlogin1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navlogin1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navlogin1", locale)
	if (locale === "en") return en.navlogin1(inputs)
	if (locale === "ko") return ko.navlogin1(inputs)
	if (locale === "ja") return ja.navlogin1(inputs)
	return zh.navlogin1(inputs)
};
export { navlogin1 as "navLogin" }
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
const navlogout1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navlogout1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navlogout1", locale)
	if (locale === "en") return en.navlogout1(inputs)
	if (locale === "ko") return ko.navlogout1(inputs)
	if (locale === "ja") return ja.navlogout1(inputs)
	return zh.navlogout1(inputs)
};
export { navlogout1 as "navLogout" }
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
const navmenu1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navmenu1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navmenu1", locale)
	if (locale === "en") return en.navmenu1(inputs)
	if (locale === "ko") return ko.navmenu1(inputs)
	if (locale === "ja") return ja.navmenu1(inputs)
	return zh.navmenu1(inputs)
};
export { navmenu1 as "navMenu" }
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
const navmyprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.navmyprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("navmyprofile2", locale)
	if (locale === "en") return en.navmyprofile2(inputs)
	if (locale === "ko") return ko.navmyprofile2(inputs)
	if (locale === "ja") return ja.navmyprofile2(inputs)
	return zh.navmyprofile2(inputs)
};
export { navmyprofile2 as "navMyProfile" }
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
const authwelcome1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authwelcome1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authwelcome1", locale)
	if (locale === "en") return en.authwelcome1(inputs)
	if (locale === "ko") return ko.authwelcome1(inputs)
	if (locale === "ja") return ja.authwelcome1(inputs)
	return zh.authwelcome1(inputs)
};
export { authwelcome1 as "authWelcome" }
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
const authwelcomemessage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authwelcomemessage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authwelcomemessage2", locale)
	if (locale === "en") return en.authwelcomemessage2(inputs)
	if (locale === "ko") return ko.authwelcomemessage2(inputs)
	if (locale === "ja") return ja.authwelcomemessage2(inputs)
	return zh.authwelcomemessage2(inputs)
};
export { authwelcomemessage2 as "authWelcomeMessage" }
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
const authintro1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authintro1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authintro1", locale)
	if (locale === "en") return en.authintro1(inputs)
	if (locale === "ko") return ko.authintro1(inputs)
	if (locale === "ja") return ja.authintro1(inputs)
	return zh.authintro1(inputs)
};
export { authintro1 as "authIntro" }
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
const authgetstarted2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authgetstarted2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authgetstarted2", locale)
	if (locale === "en") return en.authgetstarted2(inputs)
	if (locale === "ko") return ko.authgetstarted2(inputs)
	if (locale === "ja") return ja.authgetstarted2(inputs)
	return zh.authgetstarted2(inputs)
};
export { authgetstarted2 as "authGetStarted" }
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
const authsigninguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninguide3", locale)
	if (locale === "en") return en.authsigninguide3(inputs)
	if (locale === "ko") return ko.authsigninguide3(inputs)
	if (locale === "ja") return ja.authsigninguide3(inputs)
	return zh.authsigninguide3(inputs)
};
export { authsigninguide3 as "authSignInGuide" }
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
const authsigninguidestart4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninguidestart4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninguidestart4", locale)
	if (locale === "en") return en.authsigninguidestart4(inputs)
	if (locale === "ko") return ko.authsigninguidestart4(inputs)
	if (locale === "ja") return ja.authsigninguidestart4(inputs)
	return zh.authsigninguidestart4(inputs)
};
export { authsigninguidestart4 as "authSignInGuideStart" }
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
const authsigninaction3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninaction3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninaction3", locale)
	if (locale === "en") return en.authsigninaction3(inputs)
	if (locale === "ko") return ko.authsigninaction3(inputs)
	if (locale === "ja") return ja.authsigninaction3(inputs)
	return zh.authsigninaction3(inputs)
};
export { authsigninaction3 as "authSignInAction" }
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
const authsigninwithgoogle4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninwithgoogle4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninwithgoogle4", locale)
	if (locale === "en") return en.authsigninwithgoogle4(inputs)
	if (locale === "ko") return ko.authsigninwithgoogle4(inputs)
	if (locale === "ja") return ja.authsigninwithgoogle4(inputs)
	return zh.authsigninwithgoogle4(inputs)
};
export { authsigninwithgoogle4 as "authSignInWithGoogle" }
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
const authsigninwithapple4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninwithapple4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninwithapple4", locale)
	if (locale === "en") return en.authsigninwithapple4(inputs)
	if (locale === "ko") return ko.authsigninwithapple4(inputs)
	if (locale === "ja") return ja.authsigninwithapple4(inputs)
	return zh.authsigninwithapple4(inputs)
};
export { authsigninwithapple4 as "authSignInWithApple" }
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
const authsigningin2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigningin2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigningin2", locale)
	if (locale === "en") return en.authsigningin2(inputs)
	if (locale === "ko") return ko.authsigningin2(inputs)
	if (locale === "ja") return ja.authsigningin2(inputs)
	return zh.authsigningin2(inputs)
};
export { authsigningin2 as "authSigningIn" }
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
const authsigningout2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigningout2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigningout2", locale)
	if (locale === "en") return en.authsigningout2(inputs)
	if (locale === "ko") return ko.authsigningout2(inputs)
	if (locale === "ja") return ja.authsigningout2(inputs)
	return zh.authsigningout2(inputs)
};
export { authsigningout2 as "authSigningOut" }
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
const authsigninfailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninfailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninfailed3", locale)
	if (locale === "en") return en.authsigninfailed3(inputs)
	if (locale === "ko") return ko.authsigninfailed3(inputs)
	if (locale === "ja") return ja.authsigninfailed3(inputs)
	return zh.authsigninfailed3(inputs)
};
export { authsigninfailed3 as "authSignInFailed" }
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
const authsigninrequired3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninrequired3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninrequired3", locale)
	if (locale === "en") return en.authsigninrequired3(inputs)
	if (locale === "ko") return ko.authsigninrequired3(inputs)
	if (locale === "ja") return ja.authsigninrequired3(inputs)
	return zh.authsigninrequired3(inputs)
};
export { authsigninrequired3 as "authSignInRequired" }
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
const authsigninrequireddesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsigninrequireddesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsigninrequireddesc4", locale)
	if (locale === "en") return en.authsigninrequireddesc4(inputs)
	if (locale === "ko") return ko.authsigninrequireddesc4(inputs)
	if (locale === "ja") return ja.authsigninrequireddesc4(inputs)
	return zh.authsigninrequireddesc4(inputs)
};
export { authsigninrequireddesc4 as "authSignInRequiredDesc" }
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
const authwelcomeuser2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authwelcomeuser2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authwelcomeuser2", locale)
	if (locale === "en") return en.authwelcomeuser2(inputs)
	if (locale === "ko") return ko.authwelcomeuser2(inputs)
	if (locale === "ja") return ja.authwelcomeuser2(inputs)
	return zh.authwelcomeuser2(inputs)
};
export { authwelcomeuser2 as "authWelcomeUser" }
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
const profilenickname1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenickname1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenickname1", locale)
	if (locale === "en") return en.profilenickname1(inputs)
	if (locale === "ko") return ko.profilenickname1(inputs)
	if (locale === "ja") return ja.profilenickname1(inputs)
	return zh.profilenickname1(inputs)
};
export { profilenickname1 as "profileNickname" }
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
const profilenicknameinput2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknameinput2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknameinput2", locale)
	if (locale === "en") return en.profilenicknameinput2(inputs)
	if (locale === "ko") return ko.profilenicknameinput2(inputs)
	if (locale === "ja") return ja.profilenicknameinput2(inputs)
	return zh.profilenicknameinput2(inputs)
};
export { profilenicknameinput2 as "profileNicknameInput" }
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
const profilenicknamerequired2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknamerequired2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknamerequired2", locale)
	if (locale === "en") return en.profilenicknamerequired2(inputs)
	if (locale === "ko") return ko.profilenicknamerequired2(inputs)
	if (locale === "ja") return ja.profilenicknamerequired2(inputs)
	return zh.profilenicknamerequired2(inputs)
};
export { profilenicknamerequired2 as "profileNicknameRequired" }
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
const profilenicknamelength2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknamelength2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknamelength2", locale)
	if (locale === "en") return en.profilenicknamelength2(inputs)
	if (locale === "ko") return ko.profilenicknamelength2(inputs)
	if (locale === "ja") return ja.profilenicknamelength2(inputs)
	return zh.profilenicknamelength2(inputs)
};
export { profilenicknamelength2 as "profileNicknameLength" }
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
const profilenicknamemaxlength3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknamemaxlength3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknamemaxlength3", locale)
	if (locale === "en") return en.profilenicknamemaxlength3(inputs)
	if (locale === "ko") return ko.profilenicknamemaxlength3(inputs)
	if (locale === "ja") return ja.profilenicknamemaxlength3(inputs)
	return zh.profilenicknamemaxlength3(inputs)
};
export { profilenicknamemaxlength3 as "profileNicknameMaxLength" }
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
const profilegender1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilegender1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilegender1", locale)
	if (locale === "en") return en.profilegender1(inputs)
	if (locale === "ko") return ko.profilegender1(inputs)
	if (locale === "ja") return ja.profilegender1(inputs)
	return zh.profilegender1(inputs)
};
export { profilegender1 as "profileGender" }
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
const profilegendermale2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilegendermale2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilegendermale2", locale)
	if (locale === "en") return en.profilegendermale2(inputs)
	if (locale === "ko") return ko.profilegendermale2(inputs)
	if (locale === "ja") return ja.profilegendermale2(inputs)
	return zh.profilegendermale2(inputs)
};
export { profilegendermale2 as "profileGenderMale" }
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
const profilegenderfemale2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilegenderfemale2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilegenderfemale2", locale)
	if (locale === "en") return en.profilegenderfemale2(inputs)
	if (locale === "ko") return ko.profilegenderfemale2(inputs)
	if (locale === "ja") return ja.profilegenderfemale2(inputs)
	return zh.profilegenderfemale2(inputs)
};
export { profilegenderfemale2 as "profileGenderFemale" }
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
const profilegendernoanswer3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilegendernoanswer3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilegendernoanswer3", locale)
	if (locale === "en") return en.profilegendernoanswer3(inputs)
	if (locale === "ko") return ko.profilegendernoanswer3(inputs)
	if (locale === "ja") return ja.profilegendernoanswer3(inputs)
	return zh.profilegendernoanswer3(inputs)
};
export { profilegendernoanswer3 as "profileGenderNoAnswer" }
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
const profiledateofbirth3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledateofbirth3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledateofbirth3", locale)
	if (locale === "en") return en.profiledateofbirth3(inputs)
	if (locale === "ko") return ko.profiledateofbirth3(inputs)
	if (locale === "ja") return ja.profiledateofbirth3(inputs)
	return zh.profiledateofbirth3(inputs)
};
export { profiledateofbirth3 as "profileDateOfBirth" }
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
const profileyear1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileyear1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileyear1", locale)
	if (locale === "en") return en.profileyear1(inputs)
	if (locale === "ko") return ko.profileyear1(inputs)
	if (locale === "ja") return ja.profileyear1(inputs)
	return zh.profileyear1(inputs)
};
export { profileyear1 as "profileYear" }
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
const profilemonth1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilemonth1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilemonth1", locale)
	if (locale === "en") return en.profilemonth1(inputs)
	if (locale === "ko") return ko.profilemonth1(inputs)
	if (locale === "ja") return ja.profilemonth1(inputs)
	return zh.profilemonth1(inputs)
};
export { profilemonth1 as "profileMonth" }
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
const profileday1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileday1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileday1", locale)
	if (locale === "en") return en.profileday1(inputs)
	if (locale === "ko") return ko.profileday1(inputs)
	if (locale === "ja") return ja.profileday1(inputs)
	return zh.profileday1(inputs)
};
export { profileday1 as "profileDay" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ year: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profileyearvalue2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileyearvalue2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileyearvalue2", locale)
	if (locale === "en") return en.profileyearvalue2(inputs)
	if (locale === "ko") return ko.profileyearvalue2(inputs)
	if (locale === "ja") return ja.profileyearvalue2(inputs)
	return zh.profileyearvalue2(inputs)
};
export { profileyearvalue2 as "profileYearValue" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ month: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profilemonthvalue2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilemonthvalue2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilemonthvalue2", locale)
	if (locale === "en") return en.profilemonthvalue2(inputs)
	if (locale === "ko") return ko.profilemonthvalue2(inputs)
	if (locale === "ja") return ja.profilemonthvalue2(inputs)
	return zh.profilemonthvalue2(inputs)
};
export { profilemonthvalue2 as "profileMonthValue" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ day: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profiledayvalue2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledayvalue2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledayvalue2", locale)
	if (locale === "en") return en.profiledayvalue2(inputs)
	if (locale === "ko") return ko.profiledayvalue2(inputs)
	if (locale === "ja") return ja.profiledayvalue2(inputs)
	return zh.profiledayvalue2(inputs)
};
export { profiledayvalue2 as "profileDayValue" }
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
const profiledateofbirthpasterror5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledateofbirthpasterror5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledateofbirthpasterror5", locale)
	if (locale === "en") return en.profiledateofbirthpasterror5(inputs)
	if (locale === "ko") return ko.profiledateofbirthpasterror5(inputs)
	if (locale === "ja") return ja.profiledateofbirthpasterror5(inputs)
	return zh.profiledateofbirthpasterror5(inputs)
};
export { profiledateofbirthpasterror5 as "profileDateOfBirthPastError" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profileagerestriction2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileagerestriction2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileagerestriction2", locale)
	if (locale === "en") return en.profileagerestriction2(inputs)
	if (locale === "ko") return ko.profileagerestriction2(inputs)
	if (locale === "ja") return ja.profileagerestriction2(inputs)
	return zh.profileagerestriction2(inputs)
};
export { profileagerestriction2 as "profileAgeRestriction" }
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
const profilepicture1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepicture1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepicture1", locale)
	if (locale === "en") return en.profilepicture1(inputs)
	if (locale === "ko") return ko.profilepicture1(inputs)
	if (locale === "ja") return ja.profilepicture1(inputs)
	return zh.profilepicture1(inputs)
};
export { profilepicture1 as "profilePicture" }
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
const profilepictureuploadguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureuploadguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureuploadguide3", locale)
	if (locale === "en") return en.profilepictureuploadguide3(inputs)
	if (locale === "ko") return ko.profilepictureuploadguide3(inputs)
	if (locale === "ja") return ja.profilepictureuploadguide3(inputs)
	return zh.profilepictureuploadguide3(inputs)
};
export { profilepictureuploadguide3 as "profilePictureUploadGuide" }
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
const profilepictureuploadsuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureuploadsuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureuploadsuccess3", locale)
	if (locale === "en") return en.profilepictureuploadsuccess3(inputs)
	if (locale === "ko") return ko.profilepictureuploadsuccess3(inputs)
	if (locale === "ja") return ja.profilepictureuploadsuccess3(inputs)
	return zh.profilepictureuploadsuccess3(inputs)
};
export { profilepictureuploadsuccess3 as "profilePictureUploadSuccess" }
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
const profilepictureuploadfailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureuploadfailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureuploadfailed3", locale)
	if (locale === "en") return en.profilepictureuploadfailed3(inputs)
	if (locale === "ko") return ko.profilepictureuploadfailed3(inputs)
	if (locale === "ja") return ja.profilepictureuploadfailed3(inputs)
	return zh.profilepictureuploadfailed3(inputs)
};
export { profilepictureuploadfailed3 as "profilePictureUploadFailed" }
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
const profilepictureremove2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureremove2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureremove2", locale)
	if (locale === "en") return en.profilepictureremove2(inputs)
	if (locale === "ko") return ko.profilepictureremove2(inputs)
	if (locale === "ja") return ja.profilepictureremove2(inputs)
	return zh.profilepictureremove2(inputs)
};
export { profilepictureremove2 as "profilePictureRemove" }
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
const profilepictureremovesuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureremovesuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureremovesuccess3", locale)
	if (locale === "en") return en.profilepictureremovesuccess3(inputs)
	if (locale === "ko") return ko.profilepictureremovesuccess3(inputs)
	if (locale === "ja") return ja.profilepictureremovesuccess3(inputs)
	return zh.profilepictureremovesuccess3(inputs)
};
export { profilepictureremovesuccess3 as "profilePictureRemoveSuccess" }
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
const profilepictureremovefailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepictureremovefailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepictureremovefailed3", locale)
	if (locale === "en") return en.profilepictureremovefailed3(inputs)
	if (locale === "ko") return ko.profilepictureremovefailed3(inputs)
	if (locale === "ja") return ja.profilepictureremovefailed3(inputs)
	return zh.profilepictureremovefailed3(inputs)
};
export { profilepictureremovefailed3 as "profilePictureRemoveFailed" }
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
const profilepicturetypeerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepicturetypeerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepicturetypeerror3", locale)
	if (locale === "en") return en.profilepicturetypeerror3(inputs)
	if (locale === "ko") return ko.profilepicturetypeerror3(inputs)
	if (locale === "ja") return ja.profilepicturetypeerror3(inputs)
	return zh.profilepicturetypeerror3(inputs)
};
export { profilepicturetypeerror3 as "profilePictureTypeError" }
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
const profilepicturesizeerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepicturesizeerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepicturesizeerror3", locale)
	if (locale === "en") return en.profilepicturesizeerror3(inputs)
	if (locale === "ko") return ko.profilepicturesizeerror3(inputs)
	if (locale === "ja") return ja.profilepicturesizeerror3(inputs)
	return zh.profilepicturesizeerror3(inputs)
};
export { profilepicturesizeerror3 as "profilePictureSizeError" }
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
const profilesave1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilesave1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilesave1", locale)
	if (locale === "en") return en.profilesave1(inputs)
	if (locale === "ko") return ko.profilesave1(inputs)
	if (locale === "ja") return ja.profilesave1(inputs)
	return zh.profilesave1(inputs)
};
export { profilesave1 as "profileSave" }
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
const profilesaving1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilesaving1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilesaving1", locale)
	if (locale === "en") return en.profilesaving1(inputs)
	if (locale === "ko") return ko.profilesaving1(inputs)
	if (locale === "ja") return ja.profilesaving1(inputs)
	return zh.profilesaving1(inputs)
};
export { profilesaving1 as "profileSaving" }
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
	if (locale === "en") return en.profilesavesuccess2(inputs)
	if (locale === "ko") return ko.profilesavesuccess2(inputs)
	if (locale === "ja") return ja.profilesavesuccess2(inputs)
	return zh.profilesavesuccess2(inputs)
};
export { profilesavesuccess2 as "profileSaveSuccess" }
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
	if (locale === "en") return en.profilesavefailed2(inputs)
	if (locale === "ko") return ko.profilesavefailed2(inputs)
	if (locale === "ja") return ja.profilesavefailed2(inputs)
	return zh.profilesavefailed2(inputs)
};
export { profilesavefailed2 as "profileSaveFailed" }
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
const profileloading1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileloading1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileloading1", locale)
	if (locale === "en") return en.profileloading1(inputs)
	if (locale === "ko") return ko.profileloading1(inputs)
	if (locale === "ja") return ja.profileloading1(inputs)
	return zh.profileloading1(inputs)
};
export { profileloading1 as "profileLoading" }
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
	if (locale === "en") return en.profileloadfailed2(inputs)
	if (locale === "ko") return ko.profileloadfailed2(inputs)
	if (locale === "ja") return ja.profileloadfailed2(inputs)
	return zh.profileloadfailed2(inputs)
};
export { profileloadfailed2 as "profileLoadFailed" }
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
const profileinfo1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileinfo1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileinfo1", locale)
	if (locale === "en") return en.profileinfo1(inputs)
	if (locale === "ko") return ko.profileinfo1(inputs)
	if (locale === "ja") return ja.profileinfo1(inputs)
	return zh.profileinfo1(inputs)
};
export { profileinfo1 as "profileInfo" }
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
const profileinfoguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileinfoguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileinfoguide2", locale)
	if (locale === "en") return en.profileinfoguide2(inputs)
	if (locale === "ko") return ko.profileinfoguide2(inputs)
	if (locale === "ja") return ja.profileinfoguide2(inputs)
	return zh.profileinfoguide2(inputs)
};
export { profileinfoguide2 as "profileInfoGuide" }
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
const profileinfoeditguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileinfoeditguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileinfoeditguide3", locale)
	if (locale === "en") return en.profileinfoeditguide3(inputs)
	if (locale === "ko") return ko.profileinfoeditguide3(inputs)
	if (locale === "ja") return ja.profileinfoeditguide3(inputs)
	return zh.profileinfoeditguide3(inputs)
};
export { profileinfoeditguide3 as "profileInfoEditGuide" }
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
const userlist1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userlist1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userlist1", locale)
	if (locale === "en") return en.userlist1(inputs)
	if (locale === "ko") return ko.userlist1(inputs)
	if (locale === "ja") return ja.userlist1(inputs)
	return zh.userlist1(inputs)
};
export { userlist1 as "userList" }
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
const userlistguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userlistguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userlistguide2", locale)
	if (locale === "en") return en.userlistguide2(inputs)
	if (locale === "ko") return ko.userlistguide2(inputs)
	if (locale === "ja") return ja.userlistguide2(inputs)
	return zh.userlistguide2(inputs)
};
export { userlistguide2 as "userListGuide" }
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
const userjoindate2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userjoindate2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userjoindate2", locale)
	if (locale === "en") return en.userjoindate2(inputs)
	if (locale === "ko") return ko.userjoindate2(inputs)
	if (locale === "ja") return ja.userjoindate2(inputs)
	return zh.userjoindate2(inputs)
};
export { userjoindate2 as "userJoinDate" }
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
const userlastlogin2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userlastlogin2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userlastlogin2", locale)
	if (locale === "en") return en.userlastlogin2(inputs)
	if (locale === "ko") return ko.userlastlogin2(inputs)
	if (locale === "ja") return ja.userlastlogin2(inputs)
	return zh.userlastlogin2(inputs)
};
export { userlastlogin2 as "userLastLogin" }
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
const usernoname2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernoname2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernoname2", locale)
	if (locale === "en") return en.usernoname2(inputs)
	if (locale === "ko") return ko.usernoname2(inputs)
	if (locale === "ja") return ja.usernoname2(inputs)
	return zh.usernoname2(inputs)
};
export { usernoname2 as "userNoName" }
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
const userloading1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userloading1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userloading1", locale)
	if (locale === "en") return en.userloading1(inputs)
	if (locale === "ko") return ko.userloading1(inputs)
	if (locale === "ja") return ja.userloading1(inputs)
	return zh.userloading1(inputs)
};
export { userloading1 as "userLoading" }
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
const usernotregistered2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernotregistered2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernotregistered2", locale)
	if (locale === "en") return en.usernotregistered2(inputs)
	if (locale === "ko") return ko.usernotregistered2(inputs)
	if (locale === "ja") return ja.usernotregistered2(inputs)
	return zh.usernotregistered2(inputs)
};
export { usernotregistered2 as "userNotRegistered" }
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
const usernotjoined2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernotjoined2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernotjoined2", locale)
	if (locale === "en") return en.usernotjoined2(inputs)
	if (locale === "ko") return ko.usernotjoined2(inputs)
	if (locale === "ja") return ja.usernotjoined2(inputs)
	return zh.usernotjoined2(inputs)
};
export { usernotjoined2 as "userNotJoined" }
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
const userloadfailed2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userloadfailed2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userloadfailed2", locale)
	if (locale === "en") return en.userloadfailed2(inputs)
	if (locale === "ko") return ko.userloadfailed2(inputs)
	if (locale === "ja") return ja.userloadfailed2(inputs)
	return zh.userloadfailed2(inputs)
};
export { userloadfailed2 as "userLoadFailed" }
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
const userloadingmore2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userloadingmore2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userloadingmore2", locale)
	if (locale === "en") return en.userloadingmore2(inputs)
	if (locale === "ko") return ko.userloadingmore2(inputs)
	if (locale === "ja") return ja.userloadingmore2(inputs)
	return zh.userloadingmore2(inputs)
};
export { userloadingmore2 as "userLoadingMore" }
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
const userallloaded2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userallloaded2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userallloaded2", locale)
	if (locale === "en") return en.userallloaded2(inputs)
	if (locale === "ko") return ko.userallloaded2(inputs)
	if (locale === "ja") return ja.userallloaded2(inputs)
	return zh.userallloaded2(inputs)
};
export { userallloaded2 as "userAllLoaded" }
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
const userunknownerror2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userunknownerror2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userunknownerror2", locale)
	if (locale === "en") return en.userunknownerror2(inputs)
	if (locale === "ko") return ko.userunknownerror2(inputs)
	if (locale === "ja") return ja.userunknownerror2(inputs)
	return zh.userunknownerror2(inputs)
};
export { userunknownerror2 as "userUnknownError" }
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
const userprofiledetail2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userprofiledetail2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userprofiledetail2", locale)
	if (locale === "en") return en.userprofiledetail2(inputs)
	if (locale === "ko") return ko.userprofiledetail2(inputs)
	if (locale === "ja") return ja.userprofiledetail2(inputs)
	return zh.userprofiledetail2(inputs)
};
export { userprofiledetail2 as "userProfileDetail" }
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
const menutitle1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menutitle1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menutitle1", locale)
	if (locale === "en") return en.menutitle1(inputs)
	if (locale === "ko") return ko.menutitle1(inputs)
	if (locale === "ja") return ja.menutitle1(inputs)
	return zh.menutitle1(inputs)
};
export { menutitle1 as "menuTitle" }
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
const menuguide1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menuguide1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menuguide1", locale)
	if (locale === "en") return en.menuguide1(inputs)
	if (locale === "ko") return ko.menuguide1(inputs)
	if (locale === "ja") return ja.menuguide1(inputs)
	return zh.menuguide1(inputs)
};
export { menuguide1 as "menuGuide" }
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
const menumyaccount2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menumyaccount2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menumyaccount2", locale)
	if (locale === "en") return en.menumyaccount2(inputs)
	if (locale === "ko") return ko.menumyaccount2(inputs)
	if (locale === "ja") return ja.menumyaccount2(inputs)
	return zh.menumyaccount2(inputs)
};
export { menumyaccount2 as "menuMyAccount" }
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
const menueditprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menueditprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menueditprofile2", locale)
	if (locale === "en") return en.menueditprofile2(inputs)
	if (locale === "ko") return ko.menueditprofile2(inputs)
	if (locale === "ja") return ja.menueditprofile2(inputs)
	return zh.menueditprofile2(inputs)
};
export { menueditprofile2 as "menuEditProfile" }
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
const menuadminpage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menuadminpage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menuadminpage2", locale)
	if (locale === "en") return en.menuadminpage2(inputs)
	if (locale === "ko") return ko.menuadminpage2(inputs)
	if (locale === "ja") return ja.menuadminpage2(inputs)
	return zh.menuadminpage2(inputs)
};
export { menuadminpage2 as "menuAdminPage" }
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
const menudevtest2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menudevtest2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menudevtest2", locale)
	if (locale === "en") return en.menudevtest2(inputs)
	if (locale === "ko") return ko.menudevtest2(inputs)
	if (locale === "ja") return ja.menudevtest2(inputs)
	return zh.menudevtest2(inputs)
};
export { menudevtest2 as "menuDevTest" }
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
const admindashboard1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admindashboard1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admindashboard1", locale)
	if (locale === "en") return en.admindashboard1(inputs)
	if (locale === "ko") return ko.admindashboard1(inputs)
	if (locale === "ja") return ja.admindashboard1(inputs)
	return zh.admindashboard1(inputs)
};
export { admindashboard1 as "adminDashboard" }
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
const admindashboardguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admindashboardguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admindashboardguide2", locale)
	if (locale === "en") return en.admindashboardguide2(inputs)
	if (locale === "ko") return ko.admindashboardguide2(inputs)
	if (locale === "ja") return ja.admindashboardguide2(inputs)
	return zh.admindashboardguide2(inputs)
};
export { admindashboardguide2 as "adminDashboardGuide" }
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
const admintestusermanagement3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintestusermanagement3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintestusermanagement3", locale)
	if (locale === "en") return en.admintestusermanagement3(inputs)
	if (locale === "ko") return ko.admintestusermanagement3(inputs)
	if (locale === "ja") return ja.admintestusermanagement3(inputs)
	return zh.admintestusermanagement3(inputs)
};
export { admintestusermanagement3 as "adminTestUserManagement" }
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
const admintestusermanagementdesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintestusermanagementdesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintestusermanagementdesc4", locale)
	if (locale === "en") return en.admintestusermanagementdesc4(inputs)
	if (locale === "ko") return ko.admintestusermanagementdesc4(inputs)
	if (locale === "ja") return ja.admintestusermanagementdesc4(inputs)
	return zh.admintestusermanagementdesc4(inputs)
};
export { admintestusermanagementdesc4 as "adminTestUserManagementDesc" }
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
const adminuserlist2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminuserlist2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminuserlist2", locale)
	if (locale === "en") return en.adminuserlist2(inputs)
	if (locale === "ko") return ko.adminuserlist2(inputs)
	if (locale === "ja") return ja.adminuserlist2(inputs)
	return zh.adminuserlist2(inputs)
};
export { adminuserlist2 as "adminUserList" }
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
const adminuserlistdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminuserlistdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminuserlistdesc3", locale)
	if (locale === "en") return en.adminuserlistdesc3(inputs)
	if (locale === "ko") return ko.adminuserlistdesc3(inputs)
	if (locale === "ja") return ja.adminuserlistdesc3(inputs)
	return zh.adminuserlistdesc3(inputs)
};
export { adminuserlistdesc3 as "adminUserListDesc" }
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
const adminreportlist2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminreportlist2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminreportlist2", locale)
	if (locale === "en") return en.adminreportlist2(inputs)
	if (locale === "ko") return ko.adminreportlist2(inputs)
	if (locale === "ja") return ja.adminreportlist2(inputs)
	return zh.adminreportlist2(inputs)
};
export { adminreportlist2 as "adminReportList" }
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
const adminreportlistdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminreportlistdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminreportlistdesc3", locale)
	if (locale === "en") return en.adminreportlistdesc3(inputs)
	if (locale === "ko") return ko.adminreportlistdesc3(inputs)
	if (locale === "ja") return ja.adminreportlistdesc3(inputs)
	return zh.adminreportlistdesc3(inputs)
};
export { adminreportlistdesc3 as "adminReportListDesc" }
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
const admintest1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintest1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintest1", locale)
	if (locale === "en") return en.admintest1(inputs)
	if (locale === "ko") return ko.admintest1(inputs)
	if (locale === "ja") return ja.admintest1(inputs)
	return zh.admintest1(inputs)
};
export { admintest1 as "adminTest" }
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
const admintestdesc2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintestdesc2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintestdesc2", locale)
	if (locale === "en") return en.admintestdesc2(inputs)
	if (locale === "ko") return ko.admintestdesc2(inputs)
	if (locale === "ja") return ja.admintestdesc2(inputs)
	return zh.admintestdesc2(inputs)
};
export { admintestdesc2 as "adminTestDesc" }
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
const admininfopermissionnotimplemented4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admininfopermissionnotimplemented4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admininfopermissionnotimplemented4", locale)
	if (locale === "en") return en.admininfopermissionnotimplemented4(inputs)
	if (locale === "ko") return ko.admininfopermissionnotimplemented4(inputs)
	if (locale === "ja") return ja.admininfopermissionnotimplemented4(inputs)
	return zh.admininfopermissionnotimplemented4(inputs)
};
export { admininfopermissionnotimplemented4 as "adminInfoPermissionNotImplemented" }
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
const admininfotestflag3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admininfotestflag3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admininfotestflag3", locale)
	if (locale === "en") return en.admininfotestflag3(inputs)
	if (locale === "ko") return ko.admininfotestflag3(inputs)
	if (locale === "ja") return ja.admininfotestflag3(inputs)
	return zh.admininfotestflag3(inputs)
};
export { admininfotestflag3 as "adminInfoTestFlag" }
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
const admininfodatadelete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admininfodatadelete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admininfodatadelete3", locale)
	if (locale === "en") return en.admininfodatadelete3(inputs)
	if (locale === "ko") return ko.admininfodatadelete3(inputs)
	if (locale === "ja") return ja.admininfodatadelete3(inputs)
	return zh.admininfodatadelete3(inputs)
};
export { admininfodatadelete3 as "adminInfoDataDelete" }
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
const admindashboardmenu2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admindashboardmenu2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admindashboardmenu2", locale)
	if (locale === "en") return en.admindashboardmenu2(inputs)
	if (locale === "ko") return ko.admindashboardmenu2(inputs)
	if (locale === "ja") return ja.admindashboardmenu2(inputs)
	return zh.admindashboardmenu2(inputs)
};
export { admindashboardmenu2 as "adminDashboardMenu" }
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
const adminuserlistmenu3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminuserlistmenu3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminuserlistmenu3", locale)
	if (locale === "en") return en.adminuserlistmenu3(inputs)
	if (locale === "ko") return ko.adminuserlistmenu3(inputs)
	if (locale === "ja") return ja.adminuserlistmenu3(inputs)
	return zh.adminuserlistmenu3(inputs)
};
export { adminuserlistmenu3 as "adminUserListMenu" }
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
const testuserlist2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserlist2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserlist2", locale)
	if (locale === "en") return en.testuserlist2(inputs)
	if (locale === "ko") return ko.testuserlist2(inputs)
	if (locale === "ja") return ja.testuserlist2(inputs)
	return zh.testuserlist2(inputs)
};
export { testuserlist2 as "testUserList" }
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
const testuserguide2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserguide2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserguide2", locale)
	if (locale === "en") return en.testuserguide2(inputs)
	if (locale === "ko") return ko.testuserguide2(inputs)
	if (locale === "ja") return ja.testuserguide2(inputs)
	return zh.testuserguide2(inputs)
};
export { testuserguide2 as "testUserGuide" }
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
const testusercount2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercount2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercount2", locale)
	if (locale === "en") return en.testusercount2(inputs)
	if (locale === "ko") return ko.testusercount2(inputs)
	if (locale === "ja") return ja.testusercount2(inputs)
	return zh.testusercount2(inputs)
};
export { testusercount2 as "testUserCount" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testusercreated2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreated2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreated2", locale)
	if (locale === "en") return en.testusercreated2(inputs)
	if (locale === "ko") return ko.testusercreated2(inputs)
	if (locale === "ja") return ja.testusercreated2(inputs)
	return zh.testusercreated2(inputs)
};
export { testusercreated2 as "testUserCreated" }
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
const testusernotcreated3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernotcreated3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernotcreated3", locale)
	if (locale === "en") return en.testusernotcreated3(inputs)
	if (locale === "ko") return ko.testusernotcreated3(inputs)
	if (locale === "ja") return ja.testusernotcreated3(inputs)
	return zh.testusernotcreated3(inputs)
};
export { testusernotcreated3 as "testUserNotCreated" }
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
const testusercreate2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreate2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreate2", locale)
	if (locale === "en") return en.testusercreate2(inputs)
	if (locale === "ko") return ko.testusercreate2(inputs)
	if (locale === "ja") return ja.testusercreate2(inputs)
	return zh.testusercreate2(inputs)
};
export { testusercreate2 as "testUserCreate" }
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
const testusercreateicon3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateicon3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateicon3", locale)
	if (locale === "en") return en.testusercreateicon3(inputs)
	if (locale === "ko") return ko.testusercreateicon3(inputs)
	if (locale === "ja") return ja.testusercreateicon3(inputs)
	return zh.testusercreateicon3(inputs)
};
export { testusercreateicon3 as "testUserCreateIcon" }
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
const testusercreateguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateguide3", locale)
	if (locale === "en") return en.testusercreateguide3(inputs)
	if (locale === "ko") return ko.testusercreateguide3(inputs)
	if (locale === "ja") return ja.testusercreateguide3(inputs)
	return zh.testusercreateguide3(inputs)
};
export { testusercreateguide3 as "testUserCreateGuide" }
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
const testusercreating2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreating2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreating2", locale)
	if (locale === "en") return en.testusercreating2(inputs)
	if (locale === "ko") return ko.testusercreating2(inputs)
	if (locale === "ja") return ja.testusercreating2(inputs)
	return zh.testusercreating2(inputs)
};
export { testusercreating2 as "testUserCreating" }
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
const testusercreatecomplete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreatecomplete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreatecomplete3", locale)
	if (locale === "en") return en.testusercreatecomplete3(inputs)
	if (locale === "ko") return ko.testusercreatecomplete3(inputs)
	if (locale === "ja") return ja.testusercreatecomplete3(inputs)
	return zh.testusercreatecomplete3(inputs)
};
export { testusercreatecomplete3 as "testUserCreateComplete" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testusercreatecompletemessage4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreatecompletemessage4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreatecompletemessage4", locale)
	if (locale === "en") return en.testusercreatecompletemessage4(inputs)
	if (locale === "ko") return ko.testusercreatecompletemessage4(inputs)
	if (locale === "ja") return ja.testusercreatecompletemessage4(inputs)
	return zh.testusercreatecompletemessage4(inputs)
};
export { testusercreatecompletemessage4 as "testUserCreateCompleteMessage" }
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
const testusercreateatonce4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateatonce4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateatonce4", locale)
	if (locale === "en") return en.testusercreateatonce4(inputs)
	if (locale === "ko") return ko.testusercreateatonce4(inputs)
	if (locale === "ja") return ja.testusercreateatonce4(inputs)
	return zh.testusercreateatonce4(inputs)
};
export { testusercreateatonce4 as "testUserCreateAtOnce" }
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
const testusercurrentcreated3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercurrentcreated3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercurrentcreated3", locale)
	if (locale === "en") return en.testusercurrentcreated3(inputs)
	if (locale === "ko") return ko.testusercurrentcreated3(inputs)
	if (locale === "ja") return ja.testusercurrentcreated3(inputs)
	return zh.testusercurrentcreated3(inputs)
};
export { testusercurrentcreated3 as "testUserCurrentCreated" }
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
const testuserdeletinginprogress4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeletinginprogress4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeletinginprogress4", locale)
	if (locale === "en") return en.testuserdeletinginprogress4(inputs)
	if (locale === "ko") return ko.testuserdeletinginprogress4(inputs)
	if (locale === "ja") return ja.testuserdeletinginprogress4(inputs)
	return zh.testuserdeletinginprogress4(inputs)
};
export { testuserdeletinginprogress4 as "testUserDeletingInProgress" }
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
const testuserdeleting2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleting2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleting2", locale)
	if (locale === "en") return en.testuserdeleting2(inputs)
	if (locale === "ko") return ko.testuserdeleting2(inputs)
	if (locale === "ja") return ja.testuserdeleting2(inputs)
	return zh.testuserdeleting2(inputs)
};
export { testuserdeleting2 as "testUserDeleting" }
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
const testuserdeleteall3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteall3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteall3", locale)
	if (locale === "en") return en.testuserdeleteall3(inputs)
	if (locale === "ko") return ko.testuserdeleteall3(inputs)
	if (locale === "ja") return ja.testuserdeleteall3(inputs)
	return zh.testuserdeleteall3(inputs)
};
export { testuserdeleteall3 as "testUserDeleteAll" }
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
const testusernotcreatedguide4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernotcreatedguide4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernotcreatedguide4", locale)
	if (locale === "en") return en.testusernotcreatedguide4(inputs)
	if (locale === "ko") return ko.testusernotcreatedguide4(inputs)
	if (locale === "ja") return ja.testusernotcreatedguide4(inputs)
	return zh.testusernotcreatedguide4(inputs)
};
export { testusernotcreatedguide4 as "testUserNotCreatedGuide" }
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
const testusergender2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusergender2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusergender2", locale)
	if (locale === "en") return en.testusergender2(inputs)
	if (locale === "ko") return ko.testusergender2(inputs)
	if (locale === "ja") return ja.testusergender2(inputs)
	return zh.testusergender2(inputs)
};
export { testusergender2 as "testUserGender" }
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
const testuserbirthyear3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserbirthyear3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserbirthyear3", locale)
	if (locale === "en") return en.testuserbirthyear3(inputs)
	if (locale === "ko") return ko.testuserbirthyear3(inputs)
	if (locale === "ja") return ja.testuserbirthyear3(inputs)
	return zh.testuserbirthyear3(inputs)
};
export { testuserbirthyear3 as "testUserBirthYear" }
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
const testusercreateddate3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateddate3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateddate3", locale)
	if (locale === "en") return en.testusercreateddate3(inputs)
	if (locale === "ko") return ko.testusercreateddate3(inputs)
	if (locale === "ja") return ja.testusercreateddate3(inputs)
	return zh.testusercreateddate3(inputs)
};
export { testusercreateddate3 as "testUserCreatedDate" }
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
const testuserstatus2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserstatus2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserstatus2", locale)
	if (locale === "en") return en.testuserstatus2(inputs)
	if (locale === "ko") return ko.testuserstatus2(inputs)
	if (locale === "ja") return ja.testuserstatus2(inputs)
	return zh.testuserstatus2(inputs)
};
export { testuserstatus2 as "testUserStatus" }
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
const testuserinfodisplay3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfodisplay3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfodisplay3", locale)
	if (locale === "en") return en.testuserinfodisplay3(inputs)
	if (locale === "ko") return ko.testuserinfodisplay3(inputs)
	if (locale === "ja") return ja.testuserinfodisplay3(inputs)
	return zh.testuserinfodisplay3(inputs)
};
export { testuserinfodisplay3 as "testUserInfoDisplay" }
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
const testuserinfodelete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfodelete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfodelete3", locale)
	if (locale === "en") return en.testuserinfodelete3(inputs)
	if (locale === "ko") return ko.testuserinfodelete3(inputs)
	if (locale === "ja") return ja.testuserinfodelete3(inputs)
	return zh.testuserinfodelete3(inputs)
};
export { testuserinfodelete3 as "testUserInfoDelete" }
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
const testuserinfonorecover4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfonorecover4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfonorecover4", locale)
	if (locale === "en") return en.testuserinfonorecover4(inputs)
	if (locale === "ko") return ko.testuserinfonorecover4(inputs)
	if (locale === "ja") return ja.testuserinfonorecover4(inputs)
	return zh.testuserinfonorecover4(inputs)
};
export { testuserinfonorecover4 as "testUserInfoNoRecover" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ year: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuseryeardisplay3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuseryeardisplay3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuseryeardisplay3", locale)
	if (locale === "en") return en.testuseryeardisplay3(inputs)
	if (locale === "ko") return ko.testuseryeardisplay3(inputs)
	if (locale === "ja") return ja.testuseryeardisplay3(inputs)
	return zh.testuseryeardisplay3(inputs)
};
export { testuseryeardisplay3 as "testUserYearDisplay" }
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
const constructiontitle1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.constructiontitle1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("constructiontitle1", locale)
	if (locale === "en") return en.constructiontitle1(inputs)
	if (locale === "ko") return ko.constructiontitle1(inputs)
	if (locale === "ja") return ja.constructiontitle1(inputs)
	return zh.constructiontitle1(inputs)
};
export { constructiontitle1 as "constructionTitle" }
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
const constructionmessage1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.constructionmessage1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("constructionmessage1", locale)
	if (locale === "en") return en.constructionmessage1(inputs)
	if (locale === "ko") return ko.constructionmessage1(inputs)
	if (locale === "ja") return ja.constructionmessage1(inputs)
	return zh.constructionmessage1(inputs)
};
export { constructionmessage1 as "constructionMessage" }
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
const constructionbacktohome3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.constructionbacktohome3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("constructionbacktohome3", locale)
	if (locale === "en") return en.constructionbacktohome3(inputs)
	if (locale === "ko") return ko.constructionbacktohome3(inputs)
	if (locale === "ja") return ja.constructionbacktohome3(inputs)
	return zh.constructionbacktohome3(inputs)
};
export { constructionbacktohome3 as "constructionBackToHome" }
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
const boardconstruction1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardconstruction1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardconstruction1", locale)
	if (locale === "en") return en.boardconstruction1(inputs)
	if (locale === "ko") return ko.boardconstruction1(inputs)
	if (locale === "ja") return ja.boardconstruction1(inputs)
	return zh.boardconstruction1(inputs)
};
export { boardconstruction1 as "boardConstruction" }
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
const chatconstruction1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatconstruction1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatconstruction1", locale)
	if (locale === "en") return en.chatconstruction1(inputs)
	if (locale === "ko") return ko.chatconstruction1(inputs)
	if (locale === "ja") return ja.chatconstruction1(inputs)
	return zh.chatconstruction1(inputs)
};
export { chatconstruction1 as "chatConstruction" }
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
const sidebarmenu1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarmenu1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarmenu1", locale)
	if (locale === "en") return en.sidebarmenu1(inputs)
	if (locale === "ko") return ko.sidebarmenu1(inputs)
	if (locale === "ja") return ja.sidebarmenu1(inputs)
	return zh.sidebarmenu1(inputs)
};
export { sidebarmenu1 as "sidebarMenu" }
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
const sidebarrecentactivity2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarrecentactivity2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarrecentactivity2", locale)
	if (locale === "en") return en.sidebarrecentactivity2(inputs)
	if (locale === "ko") return ko.sidebarrecentactivity2(inputs)
	if (locale === "ja") return ja.sidebarrecentactivity2(inputs)
	return zh.sidebarrecentactivity2(inputs)
};
export { sidebarrecentactivity2 as "sidebarRecentActivity" }
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
const sidebarnorecentactivity3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarnorecentactivity3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarnorecentactivity3", locale)
	if (locale === "en") return en.sidebarnorecentactivity3(inputs)
	if (locale === "ko") return ko.sidebarnorecentactivity3(inputs)
	if (locale === "ja") return ja.sidebarnorecentactivity3(inputs)
	return zh.sidebarnorecentactivity3(inputs)
};
export { sidebarnorecentactivity3 as "sidebarNoRecentActivity" }
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
const sidebarselectlanguage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarselectlanguage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarselectlanguage2", locale)
	if (locale === "en") return en.sidebarselectlanguage2(inputs)
	if (locale === "ko") return ko.sidebarselectlanguage2(inputs)
	if (locale === "ja") return ja.sidebarselectlanguage2(inputs)
	return zh.sidebarselectlanguage2(inputs)
};
export { sidebarselectlanguage2 as "sidebarSelectLanguage" }
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
const sidebarbuildversion2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarbuildversion2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarbuildversion2", locale)
	if (locale === "en") return en.sidebarbuildversion2(inputs)
	if (locale === "ko") return ko.sidebarbuildversion2(inputs)
	if (locale === "ja") return ja.sidebarbuildversion2(inputs)
	return zh.sidebarbuildversion2(inputs)
};
export { sidebarbuildversion2 as "sidebarBuildVersion" }
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
const sidebarmyprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarmyprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarmyprofile2", locale)
	if (locale === "en") return en.sidebarmyprofile2(inputs)
	if (locale === "ko") return ko.sidebarmyprofile2(inputs)
	if (locale === "ja") return ja.sidebarmyprofile2(inputs)
	return zh.sidebarmyprofile2(inputs)
};
export { sidebarmyprofile2 as "sidebarMyProfile" }
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
const sidebarnotifications1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarnotifications1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarnotifications1", locale)
	if (locale === "en") return en.sidebarnotifications1(inputs)
	if (locale === "ko") return ko.sidebarnotifications1(inputs)
	if (locale === "ja") return ja.sidebarnotifications1(inputs)
	return zh.sidebarnotifications1(inputs)
};
export { sidebarnotifications1 as "sidebarNotifications" }
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
const sidebarnonotifications2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarnonotifications2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarnonotifications2", locale)
	if (locale === "en") return en.sidebarnonotifications2(inputs)
	if (locale === "ko") return ko.sidebarnonotifications2(inputs)
	if (locale === "ja") return ja.sidebarnonotifications2(inputs)
	return zh.sidebarnonotifications2(inputs)
};
export { sidebarnonotifications2 as "sidebarNoNotifications" }
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
const sidebarsuggestions1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarsuggestions1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarsuggestions1", locale)
	if (locale === "en") return en.sidebarsuggestions1(inputs)
	if (locale === "ko") return ko.sidebarsuggestions1(inputs)
	if (locale === "ja") return ja.sidebarsuggestions1(inputs)
	return zh.sidebarsuggestions1(inputs)
};
export { sidebarsuggestions1 as "sidebarSuggestions" }
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
const sidebarpopularposts2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarpopularposts2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarpopularposts2", locale)
	if (locale === "en") return en.sidebarpopularposts2(inputs)
	if (locale === "ko") return ko.sidebarpopularposts2(inputs)
	if (locale === "ja") return ja.sidebarpopularposts2(inputs)
	return zh.sidebarpopularposts2(inputs)
};
export { sidebarpopularposts2 as "sidebarPopularPosts" }
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
const sidebarnewfeatures2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarnewfeatures2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarnewfeatures2", locale)
	if (locale === "en") return en.sidebarnewfeatures2(inputs)
	if (locale === "ko") return ko.sidebarnewfeatures2(inputs)
	if (locale === "ja") return ja.sidebarnewfeatures2(inputs)
	return zh.sidebarnewfeatures2(inputs)
};
export { sidebarnewfeatures2 as "sidebarNewFeatures" }
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
const featuresveltekit51 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featuresveltekit51(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featuresveltekit51", locale)
	if (locale === "en") return en.featuresveltekit51(inputs)
	if (locale === "ko") return ko.featuresveltekit51(inputs)
	if (locale === "ja") return ja.featuresveltekit51(inputs)
	return zh.featuresveltekit51(inputs)
};
export { featuresveltekit51 as "featureSveltekit5" }
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
	if (locale === "en") return en.featuresveltekit5desc2(inputs)
	if (locale === "ko") return ko.featuresveltekit5desc2(inputs)
	if (locale === "ja") return ja.featuresveltekit5desc2(inputs)
	return zh.featuresveltekit5desc2(inputs)
};
export { featuresveltekit5desc2 as "featureSveltekit5Desc" }
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
const featurefirebaseauth2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featurefirebaseauth2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featurefirebaseauth2", locale)
	if (locale === "en") return en.featurefirebaseauth2(inputs)
	if (locale === "ko") return ko.featurefirebaseauth2(inputs)
	if (locale === "ja") return ja.featurefirebaseauth2(inputs)
	return zh.featurefirebaseauth2(inputs)
};
export { featurefirebaseauth2 as "featureFirebaseAuth" }
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
const featurefirebaseauthdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featurefirebaseauthdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featurefirebaseauthdesc3", locale)
	if (locale === "en") return en.featurefirebaseauthdesc3(inputs)
	if (locale === "ko") return ko.featurefirebaseauthdesc3(inputs)
	if (locale === "ja") return ja.featurefirebaseauthdesc3(inputs)
	return zh.featurefirebaseauthdesc3(inputs)
};
export { featurefirebaseauthdesc3 as "featureFirebaseAuthDesc" }
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
const featuretailwindcss2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featuretailwindcss2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featuretailwindcss2", locale)
	if (locale === "en") return en.featuretailwindcss2(inputs)
	if (locale === "ko") return ko.featuretailwindcss2(inputs)
	if (locale === "ja") return ja.featuretailwindcss2(inputs)
	return zh.featuretailwindcss2(inputs)
};
export { featuretailwindcss2 as "featureTailwindCss" }
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
const featuretailwindcssdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featuretailwindcssdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featuretailwindcssdesc3", locale)
	if (locale === "en") return en.featuretailwindcssdesc3(inputs)
	if (locale === "ko") return ko.featuretailwindcssdesc3(inputs)
	if (locale === "ja") return ja.featuretailwindcssdesc3(inputs)
	return zh.featuretailwindcssdesc3(inputs)
};
export { featuretailwindcssdesc3 as "featureTailwindCssDesc" }
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
	if (locale === "en") return en.linksveltekitdocs3(inputs)
	if (locale === "ko") return ko.linksveltekitdocs3(inputs)
	if (locale === "ja") return ja.linksveltekitdocs3(inputs)
	return zh.linksveltekitdocs3(inputs)
};
export { linksveltekitdocs3 as "linkSvelteKitDocs" }
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
const linkfirebasedocs2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.linkfirebasedocs2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("linkfirebasedocs2", locale)
	if (locale === "en") return en.linkfirebasedocs2(inputs)
	if (locale === "ko") return ko.linkfirebasedocs2(inputs)
	if (locale === "ja") return ja.linkfirebasedocs2(inputs)
	return zh.linkfirebasedocs2(inputs)
};
export { linkfirebasedocs2 as "linkFirebaseDocs" }
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
const linkshadcnsvelte2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.linkshadcnsvelte2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("linkshadcnsvelte2", locale)
	if (locale === "en") return en.linkshadcnsvelte2(inputs)
	if (locale === "ko") return ko.linkshadcnsvelte2(inputs)
	if (locale === "ja") return ja.linkshadcnsvelte2(inputs)
	return zh.linkshadcnsvelte2(inputs)
};
export { linkshadcnsvelte2 as "linkShadcnSvelte" }
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
const pagetitlehome2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlehome2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlehome2", locale)
	if (locale === "en") return en.pagetitlehome2(inputs)
	if (locale === "ko") return ko.pagetitlehome2(inputs)
	if (locale === "ja") return ja.pagetitlehome2(inputs)
	return zh.pagetitlehome2(inputs)
};
export { pagetitlehome2 as "pageTitleHome" }
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
const pagetitlemenu2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlemenu2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlemenu2", locale)
	if (locale === "en") return en.pagetitlemenu2(inputs)
	if (locale === "ko") return ko.pagetitlemenu2(inputs)
	if (locale === "ja") return ja.pagetitlemenu2(inputs)
	return zh.pagetitlemenu2(inputs)
};
export { pagetitlemenu2 as "pageTitleMenu" }
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
const pagetitlelogin2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlelogin2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlelogin2", locale)
	if (locale === "en") return en.pagetitlelogin2(inputs)
	if (locale === "ko") return ko.pagetitlelogin2(inputs)
	if (locale === "ja") return ja.pagetitlelogin2(inputs)
	return zh.pagetitlelogin2(inputs)
};
export { pagetitlelogin2 as "pageTitleLogin" }
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
const pagetitleuserlist3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitleuserlist3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitleuserlist3", locale)
	if (locale === "en") return en.pagetitleuserlist3(inputs)
	if (locale === "ko") return ko.pagetitleuserlist3(inputs)
	if (locale === "ja") return ja.pagetitleuserlist3(inputs)
	return zh.pagetitleuserlist3(inputs)
};
export { pagetitleuserlist3 as "pageTitleUserList" }
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
const pagetitlemyprofile3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlemyprofile3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlemyprofile3", locale)
	if (locale === "en") return en.pagetitlemyprofile3(inputs)
	if (locale === "ko") return ko.pagetitlemyprofile3(inputs)
	if (locale === "ja") return ja.pagetitlemyprofile3(inputs)
	return zh.pagetitlemyprofile3(inputs)
};
export { pagetitlemyprofile3 as "pageTitleMyProfile" }
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
const pagetitleboard2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitleboard2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitleboard2", locale)
	if (locale === "en") return en.pagetitleboard2(inputs)
	if (locale === "ko") return ko.pagetitleboard2(inputs)
	if (locale === "ja") return ja.pagetitleboard2(inputs)
	return zh.pagetitleboard2(inputs)
};
export { pagetitleboard2 as "pageTitleBoard" }
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
const pagetitlechat2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlechat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlechat2", locale)
	if (locale === "en") return en.pagetitlechat2(inputs)
	if (locale === "ko") return ko.pagetitlechat2(inputs)
	if (locale === "ja") return ja.pagetitlechat2(inputs)
	return zh.pagetitlechat2(inputs)
};
export { pagetitlechat2 as "pageTitleChat" }
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
const pagemetalogin2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagemetalogin2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagemetalogin2", locale)
	if (locale === "en") return en.pagemetalogin2(inputs)
	if (locale === "ko") return ko.pagemetalogin2(inputs)
	if (locale === "ja") return ja.pagemetalogin2(inputs)
	return zh.pagemetalogin2(inputs)
};
export { pagemetalogin2 as "pageMetaLogin" }
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
const admintestmenu2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.admintestmenu2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("admintestmenu2", locale)
	if (locale === "en") return en.admintestmenu2(inputs)
	if (locale === "ko") return ko.admintestmenu2(inputs)
	if (locale === "ja") return ja.admintestmenu2(inputs)
	return zh.admintestmenu2(inputs)
};
export { admintestmenu2 as "adminTestMenu" }
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
const profilepicturepreview2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilepicturepreview2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilepicturepreview2", locale)
	if (locale === "en") return en.profilepicturepreview2(inputs)
	if (locale === "ko") return ko.profilepicturepreview2(inputs)
	if (locale === "ja") return ja.profilepicturepreview2(inputs)
	return zh.profilepicturepreview2(inputs)
};
export { profilepicturepreview2 as "profilePicturePreview" }
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
const testuserdeleteconfirm3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteconfirm3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteconfirm3", locale)
	if (locale === "en") return en.testuserdeleteconfirm3(inputs)
	if (locale === "ko") return ko.testuserdeleteconfirm3(inputs)
	if (locale === "ja") return ja.testuserdeleteconfirm3(inputs)
	return zh.testuserdeleteconfirm3(inputs)
};
export { testuserdeleteconfirm3 as "testUserDeleteConfirm" }
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
const testusernousertodelete5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernousertodelete5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernousertodelete5", locale)
	if (locale === "en") return en.testusernousertodelete5(inputs)
	if (locale === "ko") return ko.testusernousertodelete5(inputs)
	if (locale === "ja") return ja.testusernousertodelete5(inputs)
	return zh.testusernousertodelete5(inputs)
};
export { testusernousertodelete5 as "testUserNoUserToDelete" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuserdeleteallconfirm4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteallconfirm4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteallconfirm4", locale)
	if (locale === "en") return en.testuserdeleteallconfirm4(inputs)
	if (locale === "ko") return ko.testuserdeleteallconfirm4(inputs)
	if (locale === "ja") return ja.testuserdeleteallconfirm4(inputs)
	return zh.testuserdeleteallconfirm4(inputs)
};
export { testuserdeleteallconfirm4 as "testUserDeleteAllConfirm" }
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
const testuserdeleteerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteerror3", locale)
	if (locale === "en") return en.testuserdeleteerror3(inputs)
	if (locale === "ko") return ko.testuserdeleteerror3(inputs)
	if (locale === "ja") return ja.testuserdeleteerror3(inputs)
	return zh.testuserdeleteerror3(inputs)
};
export { testuserdeleteerror3 as "testUserDeleteError" }
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
const testuserlistloaderror4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserlistloaderror4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserlistloaderror4", locale)
	if (locale === "en") return en.testuserlistloaderror4(inputs)
	if (locale === "ko") return ko.testuserlistloaderror4(inputs)
	if (locale === "ja") return ja.testuserlistloaderror4(inputs)
	return zh.testuserlistloaderror4(inputs)
};
export { testuserlistloaderror4 as "testUserListLoadError" }
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
const testusercreateerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateerror3", locale)
	if (locale === "en") return en.testusercreateerror3(inputs)
	if (locale === "ko") return ko.testusercreateerror3(inputs)
	if (locale === "ja") return ja.testusercreateerror3(inputs)
	return zh.testusercreateerror3(inputs)
};
export { testusercreateerror3 as "testUserCreateError" }
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
const testuserdeleteallerror4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteallerror4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteallerror4", locale)
	if (locale === "en") return en.testuserdeleteallerror4(inputs)
	if (locale === "ko") return ko.testuserdeleteallerror4(inputs)
	if (locale === "ja") return ja.testuserdeleteallerror4(inputs)
	return zh.testuserdeleteallerror4(inputs)
};
export { testuserdeleteallerror4 as "testUserDeleteAllError" }
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
const testuserprogressdisplay3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserprogressdisplay3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserprogressdisplay3", locale)
	if (locale === "en") return en.testuserprogressdisplay3(inputs)
	if (locale === "ko") return ko.testuserprogressdisplay3(inputs)
	if (locale === "ja") return ja.testuserprogressdisplay3(inputs)
	return zh.testuserprogressdisplay3(inputs)
};
export { testuserprogressdisplay3 as "testUserProgressDisplay" }
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
const testusergendermale3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusergendermale3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusergendermale3", locale)
	if (locale === "en") return en.testusergendermale3(inputs)
	if (locale === "ko") return ko.testusergendermale3(inputs)
	if (locale === "ja") return ja.testusergendermale3(inputs)
	return zh.testusergendermale3(inputs)
};
export { testusergendermale3 as "testUserGenderMale" }
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
const testusergenderfemale3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusergenderfemale3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusergenderfemale3", locale)
	if (locale === "en") return en.testusergenderfemale3(inputs)
	if (locale === "ko") return ko.testusergenderfemale3(inputs)
	if (locale === "ja") return ja.testusergenderfemale3(inputs)
	return zh.testusergenderfemale3(inputs)
};
export { testusergenderfemale3 as "testUserGenderFemale" }
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
const testusergenderother3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusergenderother3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusergenderother3", locale)
	if (locale === "en") return en.testusergenderother3(inputs)
	if (locale === "ko") return ko.testusergenderother3(inputs)
	if (locale === "ja") return ja.testusergenderother3(inputs)
	return zh.testusergenderother3(inputs)
};
export { testusergenderother3 as "testUserGenderOther" }
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
const testusercreatecount3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreatecount3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreatecount3", locale)
	if (locale === "en") return en.testusercreatecount3(inputs)
	if (locale === "ko") return ko.testusercreatecount3(inputs)
	if (locale === "ja") return ja.testusercreatecount3(inputs)
	return zh.testusercreatecount3(inputs)
};
export { testusercreatecount3 as "testUserCreateCount" }
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
const reportreasonabuse2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportreasonabuse2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportreasonabuse2", locale)
	if (locale === "en") return en.reportreasonabuse2(inputs)
	if (locale === "ko") return ko.reportreasonabuse2(inputs)
	if (locale === "ja") return ja.reportreasonabuse2(inputs)
	return zh.reportreasonabuse2(inputs)
};
export { reportreasonabuse2 as "reportReasonAbuse" }
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
const reportreasonfakenews3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportreasonfakenews3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportreasonfakenews3", locale)
	if (locale === "en") return en.reportreasonfakenews3(inputs)
	if (locale === "ko") return ko.reportreasonfakenews3(inputs)
	if (locale === "ja") return ja.reportreasonfakenews3(inputs)
	return zh.reportreasonfakenews3(inputs)
};
export { reportreasonfakenews3 as "reportReasonFakeNews" }
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
const reportreasonspam2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportreasonspam2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportreasonspam2", locale)
	if (locale === "en") return en.reportreasonspam2(inputs)
	if (locale === "ko") return ko.reportreasonspam2(inputs)
	if (locale === "ja") return ja.reportreasonspam2(inputs)
	return zh.reportreasonspam2(inputs)
};
export { reportreasonspam2 as "reportReasonSpam" }
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
const reportreasoninappropriate2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportreasoninappropriate2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportreasoninappropriate2", locale)
	if (locale === "en") return en.reportreasoninappropriate2(inputs)
	if (locale === "ko") return ko.reportreasoninappropriate2(inputs)
	if (locale === "ja") return ja.reportreasoninappropriate2(inputs)
	return zh.reportreasoninappropriate2(inputs)
};
export { reportreasoninappropriate2 as "reportReasonInappropriate" }
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
const reportreasonother2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportreasonother2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportreasonother2", locale)
	if (locale === "en") return en.reportreasonother2(inputs)
	if (locale === "ko") return ko.reportreasonother2(inputs)
	if (locale === "ja") return ja.reportreasonother2(inputs)
	return zh.reportreasonother2(inputs)
};
export { reportreasonother2 as "reportReasonOther" }
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
const commonpost1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commonpost1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commonpost1", locale)
	if (locale === "en") return en.commonpost1(inputs)
	if (locale === "ko") return ko.commonpost1(inputs)
	if (locale === "ja") return ja.commonpost1(inputs)
	return zh.commonpost1(inputs)
};
export { commonpost1 as "commonPost" }
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
const commoncomment1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.commoncomment1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("commoncomment1", locale)
	if (locale === "en") return en.commoncomment1(inputs)
	if (locale === "ko") return ko.commoncomment1(inputs)
	if (locale === "ja") return ja.commoncomment1(inputs)
	return zh.commoncomment1(inputs)
};
export { commoncomment1 as "commonComment" }
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
const reportcancelconfirm2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportcancelconfirm2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportcancelconfirm2", locale)
	if (locale === "en") return en.reportcancelconfirm2(inputs)
	if (locale === "ko") return ko.reportcancelconfirm2(inputs)
	if (locale === "ja") return ja.reportcancelconfirm2(inputs)
	return zh.reportcancelconfirm2(inputs)
};
export { reportcancelconfirm2 as "reportCancelConfirm" }
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
const authloginrequired2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authloginrequired2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authloginrequired2", locale)
	if (locale === "en") return en.authloginrequired2(inputs)
	if (locale === "ko") return ko.authloginrequired2(inputs)
	if (locale === "ja") return ja.authloginrequired2(inputs)
	return zh.authloginrequired2(inputs)
};
export { authloginrequired2 as "authLoginRequired" }
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
const reportmylist2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportmylist2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportmylist2", locale)
	if (locale === "en") return en.reportmylist2(inputs)
	if (locale === "ko") return ko.reportmylist2(inputs)
	if (locale === "ja") return ja.reportmylist2(inputs)
	return zh.reportmylist2(inputs)
};
export { reportmylist2 as "reportMyList" }
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
const reportmylistguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.reportmylistguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("reportmylistguide3", locale)
	if (locale === "en") return en.reportmylistguide3(inputs)
	if (locale === "ko") return ko.reportmylistguide3(inputs)
	if (locale === "ja") return ja.reportmylistguide3(inputs)
	return zh.reportmylistguide3(inputs)
};
export { reportmylistguide3 as "reportMyListGuide" }
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
const pagetitlemyreports3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlemyreports3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlemyreports3", locale)
	if (locale === "en") return en.pagetitlemyreports3(inputs)
	if (locale === "ko") return ko.pagetitlemyreports3(inputs)
	if (locale === "ja") return ja.pagetitlemyreports3(inputs)
	return zh.pagetitlemyreports3(inputs)
};
export { pagetitlemyreports3 as "pageTitleMyReports" }
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
const adminreportlistguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.adminreportlistguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("adminreportlistguide3", locale)
	if (locale === "en") return en.adminreportlistguide3(inputs)
	if (locale === "ko") return ko.adminreportlistguide3(inputs)
	if (locale === "ja") return ja.adminreportlistguide3(inputs)
	return zh.adminreportlistguide3(inputs)
};
export { adminreportlistguide3 as "adminReportListGuide" }
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
const pagetitleadminreports3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitleadminreports3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitleadminreports3", locale)
	if (locale === "en") return en.pagetitleadminreports3(inputs)
	if (locale === "ko") return ko.pagetitleadminreports3(inputs)
	if (locale === "ja") return ja.pagetitleadminreports3(inputs)
	return zh.pagetitleadminreports3(inputs)
};
export { pagetitleadminreports3 as "pageTitleAdminReports" }
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
const chatchatroom2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatchatroom2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatchatroom2", locale)
	if (locale === "en") return en.chatchatroom2(inputs)
	if (locale === "ko") return ko.chatchatroom2(inputs)
	if (locale === "ja") return ja.chatchatroom2(inputs)
	return zh.chatchatroom2(inputs)
};
export { chatchatroom2 as "chatChatRoom" }
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
const chatroom1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatroom1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatroom1", locale)
	if (locale === "en") return en.chatroom1(inputs)
	if (locale === "ko") return ko.chatroom1(inputs)
	if (locale === "ja") return ja.chatroom1(inputs)
	return zh.chatroom1(inputs)
};
export { chatroom1 as "chatRoom" }
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
const chatoverview1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatoverview1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatoverview1", locale)
	if (locale === "en") return en.chatoverview1(inputs)
	if (locale === "ko") return ko.chatoverview1(inputs)
	if (locale === "ja") return ja.chatoverview1(inputs)
	return zh.chatoverview1(inputs)
};
export { chatoverview1 as "chatOverview" }
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
const chatsigninrequired3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsigninrequired3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsigninrequired3", locale)
	if (locale === "en") return en.chatsigninrequired3(inputs)
	if (locale === "ko") return ko.chatsigninrequired3(inputs)
	if (locale === "ja") return ja.chatsigninrequired3(inputs)
	return zh.chatsigninrequired3(inputs)
};
export { chatsigninrequired3 as "chatSignInRequired" }
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
const chatprovideuid2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatprovideuid2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatprovideuid2", locale)
	if (locale === "en") return en.chatprovideuid2(inputs)
	if (locale === "ko") return ko.chatprovideuid2(inputs)
	if (locale === "ja") return ja.chatprovideuid2(inputs)
	return zh.chatprovideuid2(inputs)
};
export { chatprovideuid2 as "chatProvideUid" }
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
const chatloadingprofile2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatloadingprofile2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatloadingprofile2", locale)
	if (locale === "en") return en.chatloadingprofile2(inputs)
	if (locale === "ko") return ko.chatloadingprofile2(inputs)
	if (locale === "ja") return ja.chatloadingprofile2(inputs)
	return zh.chatloadingprofile2(inputs)
};
export { chatloadingprofile2 as "chatLoadingProfile" }
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
const chatloadprofilefailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatloadprofilefailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatloadprofilefailed3", locale)
	if (locale === "en") return en.chatloadprofilefailed3(inputs)
	if (locale === "ko") return ko.chatloadprofilefailed3(inputs)
	if (locale === "ja") return ja.chatloadprofilefailed3(inputs)
	return zh.chatloadprofilefailed3(inputs)
};
export { chatloadprofilefailed3 as "chatLoadProfileFailed" }
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
const chatchattingwith2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatchattingwith2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatchattingwith2", locale)
	if (locale === "en") return en.chatchattingwith2(inputs)
	if (locale === "ko") return ko.chatchattingwith2(inputs)
	if (locale === "ja") return ja.chatchattingwith2(inputs)
	return zh.chatchattingwith2(inputs)
};
export { chatchattingwith2 as "chatChattingWith" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ roomId: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const chatroomready2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatroomready2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatroomready2", locale)
	if (locale === "en") return en.chatroomready2(inputs)
	if (locale === "ko") return ko.chatroomready2(inputs)
	if (locale === "ja") return ja.chatroomready2(inputs)
	return zh.chatroomready2(inputs)
};
export { chatroomready2 as "chatRoomReady" }
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
const chatselectconversation2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatselectconversation2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatselectconversation2", locale)
	if (locale === "en") return en.chatselectconversation2(inputs)
	if (locale === "ko") return ko.chatselectconversation2(inputs)
	if (locale === "ja") return ja.chatselectconversation2(inputs)
	return zh.chatselectconversation2(inputs)
};
export { chatselectconversation2 as "chatSelectConversation" }
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
const chatroomnotready3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatroomnotready3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatroomnotready3", locale)
	if (locale === "en") return en.chatroomnotready3(inputs)
	if (locale === "ko") return ko.chatroomnotready3(inputs)
	if (locale === "ja") return ja.chatroomnotready3(inputs)
	return zh.chatroomnotready3(inputs)
};
export { chatroomnotready3 as "chatRoomNotReady" }
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
const chatadduidorroomid5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatadduidorroomid5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatadduidorroomid5", locale)
	if (locale === "en") return en.chatadduidorroomid5(inputs)
	if (locale === "ko") return ko.chatadduidorroomid5(inputs)
	if (locale === "ja") return ja.chatadduidorroomid5(inputs)
	return zh.chatadduidorroomid5(inputs)
};
export { chatadduidorroomid5 as "chatAddUidOrRoomId" }
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
const chatloadingmessages2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatloadingmessages2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatloadingmessages2", locale)
	if (locale === "en") return en.chatloadingmessages2(inputs)
	if (locale === "ko") return ko.chatloadingmessages2(inputs)
	if (locale === "ja") return ja.chatloadingmessages2(inputs)
	return zh.chatloadingmessages2(inputs)
};
export { chatloadingmessages2 as "chatLoadingMessages" }
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
const chatnomessages2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatnomessages2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatnomessages2", locale)
	if (locale === "en") return en.chatnomessages2(inputs)
	if (locale === "ko") return ko.chatnomessages2(inputs)
	if (locale === "ja") return ja.chatnomessages2(inputs)
	return zh.chatnomessages2(inputs)
};
export { chatnomessages2 as "chatNoMessages" }
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
const chatloadmessagesfailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatloadmessagesfailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatloadmessagesfailed3", locale)
	if (locale === "en") return en.chatloadmessagesfailed3(inputs)
	if (locale === "ko") return ko.chatloadmessagesfailed3(inputs)
	if (locale === "ja") return ja.chatloadmessagesfailed3(inputs)
	return zh.chatloadmessagesfailed3(inputs)
};
export { chatloadmessagesfailed3 as "chatLoadMessagesFailed" }
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
const chatunknownerror2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatunknownerror2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatunknownerror2", locale)
	if (locale === "en") return en.chatunknownerror2(inputs)
	if (locale === "ko") return ko.chatunknownerror2(inputs)
	if (locale === "ja") return ja.chatunknownerror2(inputs)
	return zh.chatunknownerror2(inputs)
};
export { chatunknownerror2 as "chatUnknownError" }
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
const chatloadingmore2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatloadingmore2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatloadingmore2", locale)
	if (locale === "en") return en.chatloadingmore2(inputs)
	if (locale === "ko") return ko.chatloadingmore2(inputs)
	if (locale === "ja") return ja.chatloadingmore2(inputs)
	return zh.chatloadingmore2(inputs)
};
export { chatloadingmore2 as "chatLoadingMore" }
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
const chatuptodate3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatuptodate3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatuptodate3", locale)
	if (locale === "en") return en.chatuptodate3(inputs)
	if (locale === "ko") return ko.chatuptodate3(inputs)
	if (locale === "ja") return ja.chatuptodate3(inputs)
	return zh.chatuptodate3(inputs)
};
export { chatuptodate3 as "chatUpToDate" }
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
const chatpreparingstream2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpreparingstream2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpreparingstream2", locale)
	if (locale === "en") return en.chatpreparingstream2(inputs)
	if (locale === "ko") return ko.chatpreparingstream2(inputs)
	if (locale === "ja") return ja.chatpreparingstream2(inputs)
	return zh.chatpreparingstream2(inputs)
};
export { chatpreparingstream2 as "chatPreparingStream" }
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
const chatwritemessage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatwritemessage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatwritemessage2", locale)
	if (locale === "en") return en.chatwritemessage2(inputs)
	if (locale === "ko") return ko.chatwritemessage2(inputs)
	if (locale === "ja") return ja.chatwritemessage2(inputs)
	return zh.chatwritemessage2(inputs)
};
export { chatwritemessage2 as "chatWriteMessage" }
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
const chatsending1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsending1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsending1", locale)
	if (locale === "en") return en.chatsending1(inputs)
	if (locale === "ko") return ko.chatsending1(inputs)
	if (locale === "ja") return ja.chatsending1(inputs)
	return zh.chatsending1(inputs)
};
export { chatsending1 as "chatSending" }
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
const chatsend1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsend1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsend1", locale)
	if (locale === "en") return en.chatsend1(inputs)
	if (locale === "ko") return ko.chatsend1(inputs)
	if (locale === "ja") return ja.chatsend1(inputs)
	return zh.chatsend1(inputs)
};
export { chatsend1 as "chatSend" }
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
const chatsignintosend4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsignintosend4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsignintosend4", locale)
	if (locale === "en") return en.chatsignintosend4(inputs)
	if (locale === "ko") return ko.chatsignintosend4(inputs)
	if (locale === "ja") return ja.chatsignintosend4(inputs)
	return zh.chatsignintosend4(inputs)
};
export { chatsignintosend4 as "chatSignInToSend" }
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
const chatsendfailed2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsendfailed2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsendfailed2", locale)
	if (locale === "en") return en.chatsendfailed2(inputs)
	if (locale === "ko") return ko.chatsendfailed2(inputs)
	if (locale === "ja") return ja.chatsendfailed2(inputs)
	return zh.chatsendfailed2(inputs)
};
export { chatsendfailed2 as "chatSendFailed" }
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
const chatunknownuser2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatunknownuser2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatunknownuser2", locale)
	if (locale === "en") return en.chatunknownuser2(inputs)
	if (locale === "ko") return ko.chatunknownuser2(inputs)
	if (locale === "ja") return ja.chatunknownuser2(inputs)
	return zh.chatunknownuser2(inputs)
};
export { chatunknownuser2 as "chatUnknownUser" }
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
const chatyou1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatyou1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatyou1", locale)
	if (locale === "en") return en.chatyou1(inputs)
	if (locale === "ko") return ko.chatyou1(inputs)
	if (locale === "ja") return ja.chatyou1(inputs)
	return zh.chatyou1(inputs)
};
export { chatyou1 as "chatYou" }
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
const chatpartner1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpartner1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpartner1", locale)
	if (locale === "en") return en.chatpartner1(inputs)
	if (locale === "ko") return ko.chatpartner1(inputs)
	if (locale === "ja") return ja.chatpartner1(inputs)
	return zh.chatpartner1(inputs)
};
export { chatpartner1 as "chatPartner" }
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
const chatsinglechat2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsinglechat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsinglechat2", locale)
	if (locale === "en") return en.chatsinglechat2(inputs)
	if (locale === "ko") return ko.chatsinglechat2(inputs)
	if (locale === "ja") return ja.chatsinglechat2(inputs)
	return zh.chatsinglechat2(inputs)
};
export { chatsinglechat2 as "chatSingleChat" }
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
const chatmyroomstitle3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatmyroomstitle3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatmyroomstitle3", locale)
	if (locale === "en") return en.chatmyroomstitle3(inputs)
	if (locale === "ko") return ko.chatmyroomstitle3(inputs)
	if (locale === "ja") return ja.chatmyroomstitle3(inputs)
	return zh.chatmyroomstitle3(inputs)
};
export { chatmyroomstitle3 as "chatMyRoomsTitle" }
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
const chatmyroomsdesc3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatmyroomsdesc3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatmyroomsdesc3", locale)
	if (locale === "en") return en.chatmyroomsdesc3(inputs)
	if (locale === "ko") return ko.chatmyroomsdesc3(inputs)
	if (locale === "ja") return ja.chatmyroomsdesc3(inputs)
	return zh.chatmyroomsdesc3(inputs)
};
export { chatmyroomsdesc3 as "chatMyRoomsDesc" }
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
const chatemptyrooms2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatemptyrooms2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatemptyrooms2", locale)
	if (locale === "en") return en.chatemptyrooms2(inputs)
	if (locale === "ko") return ko.chatemptyrooms2(inputs)
	if (locale === "ja") return ja.chatemptyrooms2(inputs)
	return zh.chatemptyrooms2(inputs)
};
export { chatemptyrooms2 as "chatEmptyRooms" }
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
const chatloadingrooms2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatloadingrooms2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatloadingrooms2", locale)
	if (locale === "en") return en.chatloadingrooms2(inputs)
	if (locale === "ko") return ko.chatloadingrooms2(inputs)
	if (locale === "ja") return ja.chatloadingrooms2(inputs)
	return zh.chatloadingrooms2(inputs)
};
export { chatloadingrooms2 as "chatLoadingRooms" }
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
const chatopenroom2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatopenroom2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatopenroom2", locale)
	if (locale === "en") return en.chatopenroom2(inputs)
	if (locale === "ko") return ko.chatopenroom2(inputs)
	if (locale === "ja") return ja.chatopenroom2(inputs)
	return zh.chatopenroom2(inputs)
};
export { chatopenroom2 as "chatOpenRoom" }
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
const chatlastmessagelabel3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatlastmessagelabel3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatlastmessagelabel3", locale)
	if (locale === "en") return en.chatlastmessagelabel3(inputs)
	if (locale === "ko") return ko.chatlastmessagelabel3(inputs)
	if (locale === "ja") return ja.chatlastmessagelabel3(inputs)
	return zh.chatlastmessagelabel3(inputs)
};
export { chatlastmessagelabel3 as "chatLastMessageLabel" }
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
const chattabfriends2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chattabfriends2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chattabfriends2", locale)
	if (locale === "en") return en.chattabfriends2(inputs)
	if (locale === "ko") return ko.chattabfriends2(inputs)
	if (locale === "ja") return ja.chattabfriends2(inputs)
	return zh.chattabfriends2(inputs)
};
export { chattabfriends2 as "chatTabFriends" }
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
const chattabgroupchats3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chattabgroupchats3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chattabgroupchats3", locale)
	if (locale === "en") return en.chattabgroupchats3(inputs)
	if (locale === "ko") return ko.chattabgroupchats3(inputs)
	if (locale === "ja") return ja.chattabgroupchats3(inputs)
	return zh.chattabgroupchats3(inputs)
};
export { chattabgroupchats3 as "chatTabGroupChats" }
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
const chattabopenchats3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chattabopenchats3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chattabopenchats3", locale)
	if (locale === "en") return en.chattabopenchats3(inputs)
	if (locale === "ko") return ko.chattabopenchats3(inputs)
	if (locale === "ja") return ja.chattabopenchats3(inputs)
	return zh.chattabopenchats3(inputs)
};
export { chattabopenchats3 as "chatTabOpenChats" }
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
const chattabbookmarks2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chattabbookmarks2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chattabbookmarks2", locale)
	if (locale === "en") return en.chattabbookmarks2(inputs)
	if (locale === "ko") return ko.chattabbookmarks2(inputs)
	if (locale === "ja") return ja.chattabbookmarks2(inputs)
	return zh.chattabbookmarks2(inputs)
};
export { chattabbookmarks2 as "chatTabBookmarks" }
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
const chattabsearch2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chattabsearch2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chattabsearch2", locale)
	if (locale === "en") return en.chattabsearch2(inputs)
	if (locale === "ko") return ko.chattabsearch2(inputs)
	if (locale === "ja") return ja.chattabsearch2(inputs)
	return zh.chattabsearch2(inputs)
};
export { chattabsearch2 as "chatTabSearch" }
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
const chatcreateroom2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatcreateroom2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatcreateroom2", locale)
	if (locale === "en") return en.chatcreateroom2(inputs)
	if (locale === "ko") return ko.chatcreateroom2(inputs)
	if (locale === "ja") return ja.chatcreateroom2(inputs)
	return zh.chatcreateroom2(inputs)
};
export { chatcreateroom2 as "chatCreateRoom" }
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
const chatfindfriends2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatfindfriends2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatfindfriends2", locale)
	if (locale === "en") return en.chatfindfriends2(inputs)
	if (locale === "ko") return ko.chatfindfriends2(inputs)
	if (locale === "ja") return ja.chatfindfriends2(inputs)
	return zh.chatfindfriends2(inputs)
};
export { chatfindfriends2 as "chatFindFriends" }
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
const chatcreategroupchat3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatcreategroupchat3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatcreategroupchat3", locale)
	if (locale === "en") return en.chatcreategroupchat3(inputs)
	if (locale === "ko") return ko.chatcreategroupchat3(inputs)
	if (locale === "ja") return ja.chatcreategroupchat3(inputs)
	return zh.chatcreategroupchat3(inputs)
};
export { chatcreategroupchat3 as "chatCreateGroupChat" }
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
const chatcreateopenchat3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatcreateopenchat3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatcreateopenchat3", locale)
	if (locale === "en") return en.chatcreateopenchat3(inputs)
	if (locale === "ko") return ko.chatcreateopenchat3(inputs)
	if (locale === "ja") return ja.chatcreateopenchat3(inputs)
	return zh.chatcreateopenchat3(inputs)
};
export { chatcreateopenchat3 as "chatCreateOpenChat" }
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
const boardtabfree2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardtabfree2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardtabfree2", locale)
	if (locale === "en") return en.boardtabfree2(inputs)
	if (locale === "ko") return ko.boardtabfree2(inputs)
	if (locale === "ja") return ja.boardtabfree2(inputs)
	return zh.boardtabfree2(inputs)
};
export { boardtabfree2 as "boardTabFree" }
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
const boardtabqna2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardtabqna2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardtabqna2", locale)
	if (locale === "en") return en.boardtabqna2(inputs)
	if (locale === "ko") return ko.boardtabqna2(inputs)
	if (locale === "ja") return ja.boardtabqna2(inputs)
	return zh.boardtabqna2(inputs)
};
export { boardtabqna2 as "boardTabQna" }
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
const boardtabmarket2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardtabmarket2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardtabmarket2", locale)
	if (locale === "en") return en.boardtabmarket2(inputs)
	if (locale === "ko") return ko.boardtabmarket2(inputs)
	if (locale === "ja") return ja.boardtabmarket2(inputs)
	return zh.boardtabmarket2(inputs)
};
export { boardtabmarket2 as "boardTabMarket" }
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
const boardwritepost2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardwritepost2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardwritepost2", locale)
	if (locale === "en") return en.boardwritepost2(inputs)
	if (locale === "ko") return ko.boardwritepost2(inputs)
	if (locale === "ja") return ja.boardwritepost2(inputs)
	return zh.boardwritepost2(inputs)
};
export { boardwritepost2 as "boardWritePost" }
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
const sidebarupcomingtodos2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarupcomingtodos2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarupcomingtodos2", locale)
	if (locale === "en") return en.sidebarupcomingtodos2(inputs)
	if (locale === "ko") return ko.sidebarupcomingtodos2(inputs)
	if (locale === "ja") return ja.sidebarupcomingtodos2(inputs)
	return zh.sidebarupcomingtodos2(inputs)
};
export { sidebarupcomingtodos2 as "sidebarUpcomingTodos" }
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
const sidebartodochatinvites3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebartodochatinvites3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebartodochatinvites3", locale)
	if (locale === "en") return en.sidebartodochatinvites3(inputs)
	if (locale === "ko") return ko.sidebartodochatinvites3(inputs)
	if (locale === "ja") return ja.sidebartodochatinvites3(inputs)
	return zh.sidebartodochatinvites3(inputs)
};
export { sidebartodochatinvites3 as "sidebarTodoChatInvites" }
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
const sidebartodochatinvitesdesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebartodochatinvitesdesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebartodochatinvitesdesc4", locale)
	if (locale === "en") return en.sidebartodochatinvitesdesc4(inputs)
	if (locale === "ko") return ko.sidebartodochatinvitesdesc4(inputs)
	if (locale === "ja") return ja.sidebartodochatinvitesdesc4(inputs)
	return zh.sidebartodochatinvitesdesc4(inputs)
};
export { sidebartodochatinvitesdesc4 as "sidebarTodoChatInvitesDesc" }
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
const sidebartodoposttype3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebartodoposttype3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebartodoposttype3", locale)
	if (locale === "en") return en.sidebartodoposttype3(inputs)
	if (locale === "ko") return ko.sidebartodoposttype3(inputs)
	if (locale === "ja") return ja.sidebartodoposttype3(inputs)
	return zh.sidebartodoposttype3(inputs)
};
export { sidebartodoposttype3 as "sidebarTodoPostType" }
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
const sidebartodoposttypedesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebartodoposttypedesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebartodoposttypedesc4", locale)
	if (locale === "en") return en.sidebartodoposttypedesc4(inputs)
	if (locale === "ko") return ko.sidebartodoposttypedesc4(inputs)
	if (locale === "ja") return ja.sidebartodoposttypedesc4(inputs)
	return zh.sidebartodoposttypedesc4(inputs)
};
export { sidebartodoposttypedesc4 as "sidebarTodoPostTypeDesc" }
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
const sidebartodoboardstats3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebartodoboardstats3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebartodoboardstats3", locale)
	if (locale === "en") return en.sidebartodoboardstats3(inputs)
	if (locale === "ko") return ko.sidebartodoboardstats3(inputs)
	if (locale === "ja") return ja.sidebartodoboardstats3(inputs)
	return zh.sidebartodoboardstats3(inputs)
};
export { sidebartodoboardstats3 as "sidebarTodoBoardStats" }
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
const sidebartodoboardstatsdesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebartodoboardstatsdesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebartodoboardstatsdesc4", locale)
	if (locale === "en") return en.sidebartodoboardstatsdesc4(inputs)
	if (locale === "ko") return ko.sidebartodoboardstatsdesc4(inputs)
	if (locale === "ja") return ja.sidebartodoboardstatsdesc4(inputs)
	return zh.sidebartodoboardstatsdesc4(inputs)
};
export { sidebartodoboardstatsdesc4 as "sidebarTodoBoardStatsDesc" }
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
const homesectionrecentusers3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentusers3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentusers3", locale)
	if (locale === "en") return en.homesectionrecentusers3(inputs)
	if (locale === "ko") return ko.homesectionrecentusers3(inputs)
	if (locale === "ja") return ja.homesectionrecentusers3(inputs)
	return zh.homesectionrecentusers3(inputs)
};
export { homesectionrecentusers3 as "homeSectionRecentUsers" }
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
const homesectionrecentusersdesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentusersdesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentusersdesc4", locale)
	if (locale === "en") return en.homesectionrecentusersdesc4(inputs)
	if (locale === "ko") return ko.homesectionrecentusersdesc4(inputs)
	if (locale === "ja") return ja.homesectionrecentusersdesc4(inputs)
	return zh.homesectionrecentusersdesc4(inputs)
};
export { homesectionrecentusersdesc4 as "homeSectionRecentUsersDesc" }
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
const homesectionrecentopenchat4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentopenchat4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentopenchat4", locale)
	if (locale === "en") return en.homesectionrecentopenchat4(inputs)
	if (locale === "ko") return ko.homesectionrecentopenchat4(inputs)
	if (locale === "ja") return ja.homesectionrecentopenchat4(inputs)
	return zh.homesectionrecentopenchat4(inputs)
};
export { homesectionrecentopenchat4 as "homeSectionRecentOpenChat" }
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
const homesectionrecentopenchatdesc5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentopenchatdesc5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentopenchatdesc5", locale)
	if (locale === "en") return en.homesectionrecentopenchatdesc5(inputs)
	if (locale === "ko") return ko.homesectionrecentopenchatdesc5(inputs)
	if (locale === "ja") return ja.homesectionrecentopenchatdesc5(inputs)
	return zh.homesectionrecentopenchatdesc5(inputs)
};
export { homesectionrecentopenchatdesc5 as "homeSectionRecentOpenChatDesc" }
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
const homesectionrecentopenchatempty5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentopenchatempty5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentopenchatempty5", locale)
	if (locale === "en") return en.homesectionrecentopenchatempty5(inputs)
	if (locale === "ko") return ko.homesectionrecentopenchatempty5(inputs)
	if (locale === "ja") return ja.homesectionrecentopenchatempty5(inputs)
	return zh.homesectionrecentopenchatempty5(inputs)
};
export { homesectionrecentopenchatempty5 as "homeSectionRecentOpenChatEmpty" }
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
const homesectionrecentopenchatlogin5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentopenchatlogin5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentopenchatlogin5", locale)
	if (locale === "en") return en.homesectionrecentopenchatlogin5(inputs)
	if (locale === "ko") return ko.homesectionrecentopenchatlogin5(inputs)
	if (locale === "ja") return ja.homesectionrecentopenchatlogin5(inputs)
	return zh.homesectionrecentopenchatlogin5(inputs)
};
export { homesectionrecentopenchatlogin5 as "homeSectionRecentOpenChatLogin" }
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
const homesectionpopularopenroom4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionpopularopenroom4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionpopularopenroom4", locale)
	if (locale === "en") return en.homesectionpopularopenroom4(inputs)
	if (locale === "ko") return ko.homesectionpopularopenroom4(inputs)
	if (locale === "ja") return ja.homesectionpopularopenroom4(inputs)
	return zh.homesectionpopularopenroom4(inputs)
};
export { homesectionpopularopenroom4 as "homeSectionPopularOpenRoom" }
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
const homesectionpopularopenroomdesc5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionpopularopenroomdesc5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionpopularopenroomdesc5", locale)
	if (locale === "en") return en.homesectionpopularopenroomdesc5(inputs)
	if (locale === "ko") return ko.homesectionpopularopenroomdesc5(inputs)
	if (locale === "ja") return ja.homesectionpopularopenroomdesc5(inputs)
	return zh.homesectionpopularopenroomdesc5(inputs)
};
export { homesectionpopularopenroomdesc5 as "homeSectionPopularOpenRoomDesc" }
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
const homesectionrecentposts3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentposts3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentposts3", locale)
	if (locale === "en") return en.homesectionrecentposts3(inputs)
	if (locale === "ko") return ko.homesectionrecentposts3(inputs)
	if (locale === "ja") return ja.homesectionrecentposts3(inputs)
	return zh.homesectionrecentposts3(inputs)
};
export { homesectionrecentposts3 as "homeSectionRecentPosts" }
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
const homesectionrecentpostsdesc4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentpostsdesc4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentpostsdesc4", locale)
	if (locale === "en") return en.homesectionrecentpostsdesc4(inputs)
	if (locale === "ko") return ko.homesectionrecentpostsdesc4(inputs)
	if (locale === "ja") return ja.homesectionrecentpostsdesc4(inputs)
	return zh.homesectionrecentpostsdesc4(inputs)
};
export { homesectionrecentpostsdesc4 as "homeSectionRecentPostsDesc" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const homesectionrecentuserscount4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homesectionrecentuserscount4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homesectionrecentuserscount4", locale)
	if (locale === "en") return en.homesectionrecentuserscount4(inputs)
	if (locale === "ko") return ko.homesectionrecentuserscount4(inputs)
	if (locale === "ja") return ja.homesectionrecentuserscount4(inputs)
	return zh.homesectionrecentuserscount4(inputs)
};
export { homesectionrecentuserscount4 as "homeSectionRecentUsersCount" }
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
const homeopenchatnomessage4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.homeopenchatnomessage4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("homeopenchatnomessage4", locale)
	if (locale === "en") return en.homeopenchatnomessage4(inputs)
	if (locale === "ko") return ko.homeopenchatnomessage4(inputs)
	if (locale === "ja") return ja.homeopenchatnomessage4(inputs)
	return zh.homeopenchatnomessage4(inputs)
};
export { homeopenchatnomessage4 as "homeOpenChatNoMessage" }
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
const sidebardevhighlightattachment3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebardevhighlightattachment3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebardevhighlightattachment3", locale)
	if (locale === "en") return en.sidebardevhighlightattachment3(inputs)
	if (locale === "ko") return ko.sidebardevhighlightattachment3(inputs)
	if (locale === "ja") return ja.sidebardevhighlightattachment3(inputs)
	return zh.sidebardevhighlightattachment3(inputs)
};
export { sidebardevhighlightattachment3 as "sidebarDevHighlightAttachment" }
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
const sidebardevhighlightpassword3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebardevhighlightpassword3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebardevhighlightpassword3", locale)
	if (locale === "en") return en.sidebardevhighlightpassword3(inputs)
	if (locale === "ko") return ko.sidebardevhighlightpassword3(inputs)
	if (locale === "ja") return ja.sidebardevhighlightpassword3(inputs)
	return zh.sidebardevhighlightpassword3(inputs)
};
export { sidebardevhighlightpassword3 as "sidebarDevHighlightPassword" }
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
const sidebardevhighlightposttype4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebardevhighlightposttype4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebardevhighlightposttype4", locale)
	if (locale === "en") return en.sidebardevhighlightposttype4(inputs)
	if (locale === "ko") return ko.sidebardevhighlightposttype4(inputs)
	if (locale === "ja") return ja.sidebardevhighlightposttype4(inputs)
	return zh.sidebardevhighlightposttype4(inputs)
};
export { sidebardevhighlightposttype4 as "sidebarDevHighlightPostType" }
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
const sidebardevhighlightwrapup4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebardevhighlightwrapup4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebardevhighlightwrapup4", locale)
	if (locale === "en") return en.sidebardevhighlightwrapup4(inputs)
	if (locale === "ko") return ko.sidebardevhighlightwrapup4(inputs)
	if (locale === "ja") return ja.sidebardevhighlightwrapup4(inputs)
	return zh.sidebardevhighlightwrapup4(inputs)
};
export { sidebardevhighlightwrapup4 as "sidebarDevHighlightWrapUp" }
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
const chatnomoremessages3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatnomoremessages3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatnomoremessages3", locale)
	if (locale === "en") return en.chatnomoremessages3(inputs)
	if (locale === "ko") return ko.chatnomoremessages3(inputs)
	if (locale === "ja") return ja.chatnomoremessages3(inputs)
	return zh.chatnomoremessages3(inputs)
};
export { chatnomoremessages3 as "chatNoMoreMessages" }
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
const chatroomlist2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatroomlist2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatroomlist2", locale)
	if (locale === "en") return en.chatroomlist2(inputs)
	if (locale === "ko") return ko.chatroomlist2(inputs)
	if (locale === "ja") return ja.chatroomlist2(inputs)
	return zh.chatroomlist2(inputs)
};
export { chatroomlist2 as "chatRoomList" }
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
const chatroomlistempty3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatroomlistempty3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatroomlistempty3", locale)
	if (locale === "en") return en.chatroomlistempty3(inputs)
	if (locale === "ko") return ko.chatroomlistempty3(inputs)
	if (locale === "ja") return ja.chatroomlistempty3(inputs)
	return zh.chatroomlistempty3(inputs)
};
export { chatroomlistempty3 as "chatRoomListEmpty" }
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
const chatroomlistloading3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatroomlistloading3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatroomlistloading3", locale)
	if (locale === "en") return en.chatroomlistloading3(inputs)
	if (locale === "ko") return ko.chatroomlistloading3(inputs)
	if (locale === "ja") return ja.chatroomlistloading3(inputs)
	return zh.chatroomlistloading3(inputs)
};
export { chatroomlistloading3 as "chatRoomListLoading" }
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
const chatpasswordsaving2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordsaving2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordsaving2", locale)
	if (locale === "en") return en.chatpasswordsaving2(inputs)
	if (locale === "ko") return ko.chatpasswordsaving2(inputs)
	if (locale === "ja") return ja.chatpasswordsaving2(inputs)
	return zh.chatpasswordsaving2(inputs)
};
export { chatpasswordsaving2 as "chatPasswordSaving" }
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
const chatpasswordenabletoggle3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordenabletoggle3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordenabletoggle3", locale)
	if (locale === "en") return en.chatpasswordenabletoggle3(inputs)
	if (locale === "ko") return ko.chatpasswordenabletoggle3(inputs)
	if (locale === "ja") return ja.chatpasswordenabletoggle3(inputs)
	return zh.chatpasswordenabletoggle3(inputs)
};
export { chatpasswordenabletoggle3 as "chatPasswordEnableToggle" }
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
const chatpasswordinputplaceholder3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordinputplaceholder3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordinputplaceholder3", locale)
	if (locale === "en") return en.chatpasswordinputplaceholder3(inputs)
	if (locale === "ko") return ko.chatpasswordinputplaceholder3(inputs)
	if (locale === "ja") return ja.chatpasswordinputplaceholder3(inputs)
	return zh.chatpasswordinputplaceholder3(inputs)
};
export { chatpasswordinputplaceholder3 as "chatPasswordInputPlaceholder" }
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
const chatpasswordconfirmplaceholder3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordconfirmplaceholder3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordconfirmplaceholder3", locale)
	if (locale === "en") return en.chatpasswordconfirmplaceholder3(inputs)
	if (locale === "ko") return ko.chatpasswordconfirmplaceholder3(inputs)
	if (locale === "ja") return ja.chatpasswordconfirmplaceholder3(inputs)
	return zh.chatpasswordconfirmplaceholder3(inputs)
};
export { chatpasswordconfirmplaceholder3 as "chatPasswordConfirmPlaceholder" }
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
const chatpasswordminlengtherror4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordminlengtherror4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordminlengtherror4", locale)
	if (locale === "en") return en.chatpasswordminlengtherror4(inputs)
	if (locale === "ko") return ko.chatpasswordminlengtherror4(inputs)
	if (locale === "ja") return ja.chatpasswordminlengtherror4(inputs)
	return zh.chatpasswordminlengtherror4(inputs)
};
export { chatpasswordminlengtherror4 as "chatPasswordMinLengthError" }
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
const chatpasswordmismatcherror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordmismatcherror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordmismatcherror3", locale)
	if (locale === "en") return en.chatpasswordmismatcherror3(inputs)
	if (locale === "ko") return ko.chatpasswordmismatcherror3(inputs)
	if (locale === "ja") return ja.chatpasswordmismatcherror3(inputs)
	return zh.chatpasswordmismatcherror3(inputs)
};
export { chatpasswordmismatcherror3 as "chatPasswordMismatchError" }
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
const chatpasswordsetsuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordsetsuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordsetsuccess3", locale)
	if (locale === "en") return en.chatpasswordsetsuccess3(inputs)
	if (locale === "ko") return ko.chatpasswordsetsuccess3(inputs)
	if (locale === "ja") return ja.chatpasswordsetsuccess3(inputs)
	return zh.chatpasswordsetsuccess3(inputs)
};
export { chatpasswordsetsuccess3 as "chatPasswordSetSuccess" }
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
const chatpasswordremovesuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordremovesuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordremovesuccess3", locale)
	if (locale === "en") return en.chatpasswordremovesuccess3(inputs)
	if (locale === "ko") return ko.chatpasswordremovesuccess3(inputs)
	if (locale === "ja") return ja.chatpasswordremovesuccess3(inputs)
	return zh.chatpasswordremovesuccess3(inputs)
};
export { chatpasswordremovesuccess3 as "chatPasswordRemoveSuccess" }
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
const chatpasswordsavefailure3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordsavefailure3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordsavefailure3", locale)
	if (locale === "en") return en.chatpasswordsavefailure3(inputs)
	if (locale === "ko") return ko.chatpasswordsavefailure3(inputs)
	if (locale === "ja") return ja.chatpasswordsavefailure3(inputs)
	return zh.chatpasswordsavefailure3(inputs)
};
export { chatpasswordsavefailure3 as "chatPasswordSaveFailure" }
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
const chatpasswordsettings2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordsettings2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordsettings2", locale)
	if (locale === "en") return en.chatpasswordsettings2(inputs)
	if (locale === "ko") return ko.chatpasswordsettings2(inputs)
	if (locale === "ja") return ja.chatpasswordsettings2(inputs)
	return zh.chatpasswordsettings2(inputs)
};
export { chatpasswordsettings2 as "chatPasswordSettings" }
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
const chatpasswordrequired2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordrequired2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordrequired2", locale)
	if (locale === "en") return en.chatpasswordrequired2(inputs)
	if (locale === "ko") return ko.chatpasswordrequired2(inputs)
	if (locale === "ja") return ja.chatpasswordrequired2(inputs)
	return zh.chatpasswordrequired2(inputs)
};
export { chatpasswordrequired2 as "chatPasswordRequired" }
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
const chatpasswordenterprompt3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordenterprompt3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordenterprompt3", locale)
	if (locale === "en") return en.chatpasswordenterprompt3(inputs)
	if (locale === "ko") return ko.chatpasswordenterprompt3(inputs)
	if (locale === "ja") return ja.chatpasswordenterprompt3(inputs)
	return zh.chatpasswordenterprompt3(inputs)
};
export { chatpasswordenterprompt3 as "chatPasswordEnterPrompt" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ countdown: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const chatpasswordverifying2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordverifying2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordverifying2", locale)
	if (locale === "en") return en.chatpasswordverifying2(inputs)
	if (locale === "ko") return ko.chatpasswordverifying2(inputs)
	if (locale === "ja") return ja.chatpasswordverifying2(inputs)
	return zh.chatpasswordverifying2(inputs)
};
export { chatpasswordverifying2 as "chatPasswordVerifying" }
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
const chatpasswordverifysuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordverifysuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordverifysuccess3", locale)
	if (locale === "en") return en.chatpasswordverifysuccess3(inputs)
	if (locale === "ko") return ko.chatpasswordverifysuccess3(inputs)
	if (locale === "ja") return ja.chatpasswordverifysuccess3(inputs)
	return zh.chatpasswordverifysuccess3(inputs)
};
export { chatpasswordverifysuccess3 as "chatPasswordVerifySuccess" }
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
const chatpasswordincorrect2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordincorrect2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordincorrect2", locale)
	if (locale === "en") return en.chatpasswordincorrect2(inputs)
	if (locale === "ko") return ko.chatpasswordincorrect2(inputs)
	if (locale === "ja") return ja.chatpasswordincorrect2(inputs)
	return zh.chatpasswordincorrect2(inputs)
};
export { chatpasswordincorrect2 as "chatPasswordIncorrect" }
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
const chatpasswordverifyfailure3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpasswordverifyfailure3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpasswordverifyfailure3", locale)
	if (locale === "en") return en.chatpasswordverifyfailure3(inputs)
	if (locale === "ko") return ko.chatpasswordverifyfailure3(inputs)
	if (locale === "ja") return ja.chatpasswordverifyfailure3(inputs)
	return zh.chatpasswordverifyfailure3(inputs)
};
export { chatpasswordverifyfailure3 as "chatPasswordVerifyFailure" }
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
const chatpassworddelete2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpassworddelete2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpassworddelete2", locale)
	if (locale === "en") return en.chatpassworddelete2(inputs)
	if (locale === "ko") return ko.chatpassworddelete2(inputs)
	if (locale === "ja") return ja.chatpassworddelete2(inputs)
	return zh.chatpassworddelete2(inputs)
};
export { chatpassworddelete2 as "chatPasswordDelete" }
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
const chatpassworddeletesuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatpassworddeletesuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatpassworddeletesuccess3", locale)
	if (locale === "en") return en.chatpassworddeletesuccess3(inputs)
	if (locale === "ko") return ko.chatpassworddeletesuccess3(inputs)
	if (locale === "ja") return ja.chatpassworddeletesuccess3(inputs)
	return zh.chatpassworddeletesuccess3(inputs)
};
export { chatpassworddeletesuccess3 as "chatPasswordDeleteSuccess" }
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
const chataccept1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chataccept1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chataccept1", locale)
	if (locale === "en") return en.chataccept1(inputs)
	if (locale === "ko") return ko.chataccept1(inputs)
	if (locale === "ja") return ja.chataccept1(inputs)
	return zh.chataccept1(inputs)
};
export { chataccept1 as "chatAccept" }
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
const chatreject1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatreject1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatreject1", locale)
	if (locale === "en") return en.chatreject1(inputs)
	if (locale === "ko") return ko.chatreject1(inputs)
	if (locale === "ja") return ja.chatreject1(inputs)
	return zh.chatreject1(inputs)
};
export { chatreject1 as "chatReject" }
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
const chatinvitefriend2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatinvitefriend2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatinvitefriend2", locale)
	if (locale === "en") return en.chatinvitefriend2(inputs)
	if (locale === "ko") return ko.chatinvitefriend2(inputs)
	if (locale === "ja") return ja.chatinvitefriend2(inputs)
	return zh.chatinvitefriend2(inputs)
};
export { chatinvitefriend2 as "chatInviteFriend" }
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
const chatinvitetoroom3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatinvitetoroom3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatinvitetoroom3", locale)
	if (locale === "en") return en.chatinvitetoroom3(inputs)
	if (locale === "ko") return ko.chatinvitetoroom3(inputs)
	if (locale === "ja") return ja.chatinvitetoroom3(inputs)
	return zh.chatinvitetoroom3(inputs)
};
export { chatinvitetoroom3 as "chatInviteToRoom" }
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
const chatsearchusertoinvite4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatsearchusertoinvite4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatsearchusertoinvite4", locale)
	if (locale === "en") return en.chatsearchusertoinvite4(inputs)
	if (locale === "ko") return ko.chatsearchusertoinvite4(inputs)
	if (locale === "ja") return ja.chatsearchusertoinvite4(inputs)
	return zh.chatsearchusertoinvite4(inputs)
};
export { chatsearchusertoinvite4 as "chatSearchUserToInvite" }
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
const chatinvitationsent2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatinvitationsent2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatinvitationsent2", locale)
	if (locale === "en") return en.chatinvitationsent2(inputs)
	if (locale === "ko") return ko.chatinvitationsent2(inputs)
	if (locale === "ja") return ja.chatinvitationsent2(inputs)
	return zh.chatinvitationsent2(inputs)
};
export { chatinvitationsent2 as "chatInvitationSent" }
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
const chatdirectchat2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatdirectchat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatdirectchat2", locale)
	if (locale === "en") return en.chatdirectchat2(inputs)
	if (locale === "ko") return ko.chatdirectchat2(inputs)
	if (locale === "ja") return ja.chatdirectchat2(inputs)
	return zh.chatdirectchat2(inputs)
};
export { chatdirectchat2 as "chatDirectChat" }
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
export const loading = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.loading(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("loading", locale)
	if (locale === "en") return en.loading(inputs)
	if (locale === "ko") return ko.loading(inputs)
	if (locale === "ja") return ja.loading(inputs)
	return zh.loading(inputs)
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
export const close = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.close(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("close", locale)
	if (locale === "en") return en.close(inputs)
	if (locale === "ko") return ko.close(inputs)
	if (locale === "ja") return ja.close(inputs)
	return zh.close(inputs)
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
export const save = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.save(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("save", locale)
	if (locale === "en") return en.save(inputs)
	if (locale === "ko") return ko.save(inputs)
	if (locale === "ja") return ja.save(inputs)
	return zh.save(inputs)
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
const _delete = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr._delete(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("_delete", locale)
	if (locale === "en") return en._delete(inputs)
	if (locale === "ko") return ko._delete(inputs)
	if (locale === "ja") return ja._delete(inputs)
	return zh._delete(inputs)
};
export { _delete as "delete" }
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
export const cancel = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.cancel(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("cancel", locale)
	if (locale === "en") return en.cancel(inputs)
	if (locale === "ko") return ko.cancel(inputs)
	if (locale === "ja") return ja.cancel(inputs)
	return zh.cancel(inputs)
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
export const confirm = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.confirm(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("confirm", locale)
	if (locale === "en") return en.confirm(inputs)
	if (locale === "ko") return ko.confirm(inputs)
	if (locale === "ja") return ja.confirm(inputs)
	return zh.confirm(inputs)
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
export const refresh = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.refresh(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("refresh", locale)
	if (locale === "en") return en.refresh(inputs)
	if (locale === "ko") return ko.refresh(inputs)
	if (locale === "ja") return ja.refresh(inputs)
	return zh.refresh(inputs)
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
export const retry = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.retry(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("retry", locale)
	if (locale === "en") return en.retry(inputs)
	if (locale === "ko") return ko.retry(inputs)
	if (locale === "ja") return ja.retry(inputs)
	return zh.retry(inputs)
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
export const info = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.info(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("info", locale)
	if (locale === "en") return en.info(inputs)
	if (locale === "ko") return ko.info(inputs)
	if (locale === "ja") return ja.info(inputs)
	return zh.info(inputs)
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
export const status = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.status(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("status", locale)
	if (locale === "en") return en.status(inputs)
	if (locale === "ko") return ko.status(inputs)
	if (locale === "ja") return ja.status(inputs)
	return zh.status(inputs)
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
export const success = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.success(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("success", locale)
	if (locale === "en") return en.success(inputs)
	if (locale === "ko") return ko.success(inputs)
	if (locale === "ja") return ja.success(inputs)
	return zh.success(inputs)
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
export const error = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.error(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("error", locale)
	if (locale === "en") return en.error(inputs)
	if (locale === "ko") return ko.error(inputs)
	if (locale === "ja") return ja.error(inputs)
	return zh.error(inputs)
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
export const complete = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.complete(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("complete", locale)
	if (locale === "en") return en.complete(inputs)
	if (locale === "ko") return ko.complete(inputs)
	if (locale === "ja") return ja.complete(inputs)
	return zh.complete(inputs)
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
export const progress = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.progress(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("progress", locale)
	if (locale === "en") return en.progress(inputs)
	if (locale === "ko") return ko.progress(inputs)
	if (locale === "ja") return ja.progress(inputs)
	return zh.progress(inputs)
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
export const go = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.go(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("go", locale)
	if (locale === "en") return en.go(inputs)
	if (locale === "ko") return ko.go(inputs)
	if (locale === "ja") return ja.go(inputs)
	return zh.go(inputs)
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
export const user = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.user(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("user", locale)
	if (locale === "en") return en.user(inputs)
	if (locale === "ko") return ko.user(inputs)
	if (locale === "ja") return ja.user(inputs)
	return zh.user(inputs)
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
export const home = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.home(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("home", locale)
	if (locale === "en") return en.home(inputs)
	if (locale === "ko") return ko.home(inputs)
	if (locale === "ja") return ja.home(inputs)
	return zh.home(inputs)
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
export const about = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.about(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("about", locale)
	if (locale === "en") return en.about(inputs)
	if (locale === "ko") return ko.about(inputs)
	if (locale === "ja") return ja.about(inputs)
	return zh.about(inputs)
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
export const products = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.products(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("products", locale)
	if (locale === "en") return en.products(inputs)
	if (locale === "ko") return ko.products(inputs)
	if (locale === "ja") return ja.products(inputs)
	return zh.products(inputs)
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
export const contact = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.contact(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("contact", locale)
	if (locale === "en") return en.contact(inputs)
	if (locale === "ko") return ko.contact(inputs)
	if (locale === "ja") return ja.contact(inputs)
	return zh.contact(inputs)
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
export const board = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.board(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("board", locale)
	if (locale === "en") return en.board(inputs)
	if (locale === "ko") return ko.board(inputs)
	if (locale === "ja") return ja.board(inputs)
	return zh.board(inputs)
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
export const chat = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chat(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chat", locale)
	if (locale === "en") return en.chat(inputs)
	if (locale === "ko") return ko.chat(inputs)
	if (locale === "ja") return ja.chat(inputs)
	return zh.chat(inputs)
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
const findusers1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.findusers1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("findusers1", locale)
	if (locale === "en") return en.findusers1(inputs)
	if (locale === "ko") return ko.findusers1(inputs)
	if (locale === "ja") return ja.findusers1(inputs)
	return zh.findusers1(inputs)
};
export { findusers1 as "findUsers" }
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
const signin1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.signin1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("signin1", locale)
	if (locale === "en") return en.signin1(inputs)
	if (locale === "ko") return ko.signin1(inputs)
	if (locale === "ja") return ja.signin1(inputs)
	return zh.signin1(inputs)
};
export { signin1 as "signIn" }
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
const signout1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.signout1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("signout1", locale)
	if (locale === "en") return en.signout1(inputs)
	if (locale === "ko") return ko.signout1(inputs)
	if (locale === "ja") return ja.signout1(inputs)
	return zh.signout1(inputs)
};
export { signout1 as "signOut" }
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
export const menu = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.menu(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("menu", locale)
	if (locale === "en") return en.menu(inputs)
	if (locale === "ko") return ko.menu(inputs)
	if (locale === "ja") return ja.menu(inputs)
	return zh.menu(inputs)
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
const myprofile1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.myprofile1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("myprofile1", locale)
	if (locale === "en") return en.myprofile1(inputs)
	if (locale === "ko") return ko.myprofile1(inputs)
	if (locale === "ja") return ja.myprofile1(inputs)
	return zh.myprofile1(inputs)
};
export { myprofile1 as "myProfile" }
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
const profilenicknamelengthlimit3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilenicknamelengthlimit3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilenicknamelengthlimit3", locale)
	if (locale === "en") return en.profilenicknamelengthlimit3(inputs)
	if (locale === "ko") return ko.profilenicknamelengthlimit3(inputs)
	if (locale === "ja") return ja.profilenicknamelengthlimit3(inputs)
	return zh.profilenicknamelengthlimit3(inputs)
};
export { profilenicknamelengthlimit3 as "profileNicknameLengthLimit" }
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
const profilegendernotspecified3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilegendernotspecified3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilegendernotspecified3", locale)
	if (locale === "en") return en.profilegendernotspecified3(inputs)
	if (locale === "ko") return ko.profilegendernotspecified3(inputs)
	if (locale === "ja") return ja.profilegendernotspecified3(inputs)
	return zh.profilegendernotspecified3(inputs)
};
export { profilegendernotspecified3 as "profileGenderNotSpecified" }
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
const profilebirthdate1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdate1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdate1", locale)
	if (locale === "en") return en.profilebirthdate1(inputs)
	if (locale === "ko") return ko.profilebirthdate1(inputs)
	if (locale === "ja") return ja.profilebirthdate1(inputs)
	return zh.profilebirthdate1(inputs)
};
export { profilebirthdate1 as "profileBirthdate" }
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
const profilebirthdateyear2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdateyear2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdateyear2", locale)
	if (locale === "en") return en.profilebirthdateyear2(inputs)
	if (locale === "ko") return ko.profilebirthdateyear2(inputs)
	if (locale === "ja") return ja.profilebirthdateyear2(inputs)
	return zh.profilebirthdateyear2(inputs)
};
export { profilebirthdateyear2 as "profileBirthdateYear" }
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
const profilebirthdatemonth2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdatemonth2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdatemonth2", locale)
	if (locale === "en") return en.profilebirthdatemonth2(inputs)
	if (locale === "ko") return ko.profilebirthdatemonth2(inputs)
	if (locale === "ja") return ja.profilebirthdatemonth2(inputs)
	return zh.profilebirthdatemonth2(inputs)
};
export { profilebirthdatemonth2 as "profileBirthdateMonth" }
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
const profilebirthdateday2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdateday2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdateday2", locale)
	if (locale === "en") return en.profilebirthdateday2(inputs)
	if (locale === "ko") return ko.profilebirthdateday2(inputs)
	if (locale === "ja") return ja.profilebirthdateday2(inputs)
	return zh.profilebirthdateday2(inputs)
};
export { profilebirthdateday2 as "profileBirthdateDay" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ year: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profileyearformat2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileyearformat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileyearformat2", locale)
	if (locale === "en") return en.profileyearformat2(inputs)
	if (locale === "ko") return ko.profileyearformat2(inputs)
	if (locale === "ja") return ja.profileyearformat2(inputs)
	return zh.profileyearformat2(inputs)
};
export { profileyearformat2 as "profileYearFormat" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ month: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profilemonthformat2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilemonthformat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilemonthformat2", locale)
	if (locale === "en") return en.profilemonthformat2(inputs)
	if (locale === "ko") return ko.profilemonthformat2(inputs)
	if (locale === "ja") return ja.profilemonthformat2(inputs)
	return zh.profilemonthformat2(inputs)
};
export { profilemonthformat2 as "profileMonthFormat" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ day: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profiledayformat2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profiledayformat2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profiledayformat2", locale)
	if (locale === "en") return en.profiledayformat2(inputs)
	if (locale === "ko") return ko.profiledayformat2(inputs)
	if (locale === "ja") return ja.profiledayformat2(inputs)
	return zh.profiledayformat2(inputs)
};
export { profiledayformat2 as "profileDayFormat" }
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
const profilebirthdatefutureerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdatefutureerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdatefutureerror3", locale)
	if (locale === "en") return en.profilebirthdatefutureerror3(inputs)
	if (locale === "ko") return ko.profilebirthdatefutureerror3(inputs)
	if (locale === "ja") return ja.profilebirthdatefutureerror3(inputs)
	return zh.profilebirthdatefutureerror3(inputs)
};
export { profilebirthdatefutureerror3 as "profileBirthdateFutureError" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const profilebirthdateagelimit3 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilebirthdateagelimit3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilebirthdateagelimit3", locale)
	if (locale === "en") return en.profilebirthdateagelimit3(inputs)
	if (locale === "ko") return ko.profilebirthdateagelimit3(inputs)
	if (locale === "ja") return ja.profilebirthdateagelimit3(inputs)
	return zh.profilebirthdateagelimit3(inputs)
};
export { profilebirthdateagelimit3 as "profileBirthdateAgeLimit" }
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
const profilephoto1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephoto1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephoto1", locale)
	if (locale === "en") return en.profilephoto1(inputs)
	if (locale === "ko") return ko.profilephoto1(inputs)
	if (locale === "ja") return ja.profilephoto1(inputs)
	return zh.profilephoto1(inputs)
};
export { profilephoto1 as "profilePhoto" }
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
const profilephotouploadguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotouploadguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotouploadguide3", locale)
	if (locale === "en") return en.profilephotouploadguide3(inputs)
	if (locale === "ko") return ko.profilephotouploadguide3(inputs)
	if (locale === "ja") return ja.profilephotouploadguide3(inputs)
	return zh.profilephotouploadguide3(inputs)
};
export { profilephotouploadguide3 as "profilePhotoUploadGuide" }
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
const profilephotouploadsuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotouploadsuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotouploadsuccess3", locale)
	if (locale === "en") return en.profilephotouploadsuccess3(inputs)
	if (locale === "ko") return ko.profilephotouploadsuccess3(inputs)
	if (locale === "ja") return ja.profilephotouploadsuccess3(inputs)
	return zh.profilephotouploadsuccess3(inputs)
};
export { profilephotouploadsuccess3 as "profilePhotoUploadSuccess" }
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
const profilephotouploadfailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotouploadfailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotouploadfailed3", locale)
	if (locale === "en") return en.profilephotouploadfailed3(inputs)
	if (locale === "ko") return ko.profilephotouploadfailed3(inputs)
	if (locale === "ja") return ja.profilephotouploadfailed3(inputs)
	return zh.profilephotouploadfailed3(inputs)
};
export { profilephotouploadfailed3 as "profilePhotoUploadFailed" }
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
const profilephotoremove2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotoremove2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotoremove2", locale)
	if (locale === "en") return en.profilephotoremove2(inputs)
	if (locale === "ko") return ko.profilephotoremove2(inputs)
	if (locale === "ja") return ja.profilephotoremove2(inputs)
	return zh.profilephotoremove2(inputs)
};
export { profilephotoremove2 as "profilePhotoRemove" }
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
const profilephotoremovesuccess3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotoremovesuccess3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotoremovesuccess3", locale)
	if (locale === "en") return en.profilephotoremovesuccess3(inputs)
	if (locale === "ko") return ko.profilephotoremovesuccess3(inputs)
	if (locale === "ja") return ja.profilephotoremovesuccess3(inputs)
	return zh.profilephotoremovesuccess3(inputs)
};
export { profilephotoremovesuccess3 as "profilePhotoRemoveSuccess" }
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
const profilephotoremovefailed3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotoremovefailed3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotoremovefailed3", locale)
	if (locale === "en") return en.profilephotoremovefailed3(inputs)
	if (locale === "ko") return ko.profilephotoremovefailed3(inputs)
	if (locale === "ja") return ja.profilephotoremovefailed3(inputs)
	return zh.profilephotoremovefailed3(inputs)
};
export { profilephotoremovefailed3 as "profilePhotoRemoveFailed" }
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
const profilephototypeerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephototypeerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephototypeerror3", locale)
	if (locale === "en") return en.profilephototypeerror3(inputs)
	if (locale === "ko") return ko.profilephototypeerror3(inputs)
	if (locale === "ja") return ja.profilephototypeerror3(inputs)
	return zh.profilephototypeerror3(inputs)
};
export { profilephototypeerror3 as "profilePhotoTypeError" }
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
const profilephotosizeerror3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotosizeerror3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotosizeerror3", locale)
	if (locale === "en") return en.profilephotosizeerror3(inputs)
	if (locale === "ko") return ko.profilephotosizeerror3(inputs)
	if (locale === "ja") return ja.profilephotosizeerror3(inputs)
	return zh.profilephotosizeerror3(inputs)
};
export { profilephotosizeerror3 as "profilePhotoSizeError" }
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
const profilememberinfo2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilememberinfo2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilememberinfo2", locale)
	if (locale === "en") return en.profilememberinfo2(inputs)
	if (locale === "ko") return ko.profilememberinfo2(inputs)
	if (locale === "ja") return ja.profilememberinfo2(inputs)
	return zh.profilememberinfo2(inputs)
};
export { profilememberinfo2 as "profileMemberInfo" }
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
const profilememberinfoguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilememberinfoguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilememberinfoguide3", locale)
	if (locale === "en") return en.profilememberinfoguide3(inputs)
	if (locale === "ko") return ko.profilememberinfoguide3(inputs)
	if (locale === "ja") return ja.profilememberinfoguide3(inputs)
	return zh.profilememberinfoguide3(inputs)
};
export { profilememberinfoguide3 as "profileMemberInfoGuide" }
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
	if (locale === "en") return en.profilememberinfoeditguide4(inputs)
	if (locale === "ko") return ko.profilememberinfoeditguide4(inputs)
	if (locale === "ja") return ja.profilememberinfoeditguide4(inputs)
	return zh.profilememberinfoeditguide4(inputs)
};
export { profilememberinfoeditguide4 as "profileMemberInfoEditGuide" }
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
const userjoineddate2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.userjoineddate2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("userjoineddate2", locale)
	if (locale === "en") return en.userjoineddate2(inputs)
	if (locale === "ko") return ko.userjoineddate2(inputs)
	if (locale === "ja") return ja.userjoineddate2(inputs)
	return zh.userjoineddate2(inputs)
};
export { userjoineddate2 as "userJoinedDate" }
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
const usernoregistration2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernoregistration2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernoregistration2", locale)
	if (locale === "en") return en.usernoregistration2(inputs)
	if (locale === "ko") return ko.usernoregistration2(inputs)
	if (locale === "ja") return ja.usernoregistration2(inputs)
	return zh.usernoregistration2(inputs)
};
export { usernoregistration2 as "userNoRegistration" }
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
const usernosignup2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernosignup2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernosignup2", locale)
	if (locale === "en") return en.usernosignup2(inputs)
	if (locale === "ko") return ko.usernosignup2(inputs)
	if (locale === "ja") return ja.usernosignup2(inputs)
	return zh.usernosignup2(inputs)
};
export { usernosignup2 as "userNoSignup" }
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
const testusercreatebatchcount4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreatebatchcount4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreatebatchcount4", locale)
	if (locale === "en") return en.testusercreatebatchcount4(inputs)
	if (locale === "ko") return ko.testusercreatebatchcount4(inputs)
	if (locale === "ja") return ja.testusercreatebatchcount4(inputs)
	return zh.testusercreatebatchcount4(inputs)
};
export { testusercreatebatchcount4 as "testUserCreateBatchCount" }
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
const testusercurrentcreatecount4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercurrentcreatecount4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercurrentcreatecount4", locale)
	if (locale === "en") return en.testusercurrentcreatecount4(inputs)
	if (locale === "ko") return ko.testusercurrentcreatecount4(inputs)
	if (locale === "ja") return ja.testusercurrentcreatecount4(inputs)
	return zh.testusercurrentcreatecount4(inputs)
};
export { testusercurrentcreatecount4 as "testUserCurrentCreateCount" }
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
const testusernoneguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernoneguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernoneguide3", locale)
	if (locale === "en") return en.testusernoneguide3(inputs)
	if (locale === "ko") return ko.testusernoneguide3(inputs)
	if (locale === "ja") return ja.testusernoneguide3(inputs)
	return zh.testusernoneguide3(inputs)
};
export { testusernoneguide3 as "testUserNoneGuide" }
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
const testuserinfounrecoverable3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserinfounrecoverable3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserinfounrecoverable3", locale)
	if (locale === "en") return en.testuserinfounrecoverable3(inputs)
	if (locale === "ko") return ko.testuserinfounrecoverable3(inputs)
	if (locale === "ja") return ja.testuserinfounrecoverable3(inputs)
	return zh.testuserinfounrecoverable3(inputs)
};
export { testuserinfounrecoverable3 as "testUserInfoUnrecoverable" }
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
const underconstruction1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.underconstruction1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("underconstruction1", locale)
	if (locale === "en") return en.underconstruction1(inputs)
	if (locale === "ko") return ko.underconstruction1(inputs)
	if (locale === "ja") return ja.underconstruction1(inputs)
	return zh.underconstruction1(inputs)
};
export { underconstruction1 as "underConstruction" }
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
const underconstructionmessage2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.underconstructionmessage2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("underconstructionmessage2", locale)
	if (locale === "en") return en.underconstructionmessage2(inputs)
	if (locale === "ko") return ko.underconstructionmessage2(inputs)
	if (locale === "ja") return ja.underconstructionmessage2(inputs)
	return zh.underconstructionmessage2(inputs)
};
export { underconstructionmessage2 as "underConstructionMessage" }
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
const underconstructionbacktohome4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.underconstructionbacktohome4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("underconstructionbacktohome4", locale)
	if (locale === "en") return en.underconstructionbacktohome4(inputs)
	if (locale === "ko") return ko.underconstructionbacktohome4(inputs)
	if (locale === "ja") return ja.underconstructionbacktohome4(inputs)
	return zh.underconstructionbacktohome4(inputs)
};
export { underconstructionbacktohome4 as "underConstructionBackToHome" }
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
const boardunderconstruction2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.boardunderconstruction2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("boardunderconstruction2", locale)
	if (locale === "en") return en.boardunderconstruction2(inputs)
	if (locale === "ko") return ko.boardunderconstruction2(inputs)
	if (locale === "ja") return ja.boardunderconstruction2(inputs)
	return zh.boardunderconstruction2(inputs)
};
export { boardunderconstruction2 as "boardUnderConstruction" }
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
const chatunderconstruction2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.chatunderconstruction2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("chatunderconstruction2", locale)
	if (locale === "en") return en.chatunderconstruction2(inputs)
	if (locale === "ko") return ko.chatunderconstruction2(inputs)
	if (locale === "ja") return ja.chatunderconstruction2(inputs)
	return zh.chatunderconstruction2(inputs)
};
export { chatunderconstruction2 as "chatUnderConstruction" }
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
const sidebarlanguage1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.sidebarlanguage1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("sidebarlanguage1", locale)
	if (locale === "en") return en.sidebarlanguage1(inputs)
	if (locale === "ko") return ko.sidebarlanguage1(inputs)
	if (locale === "ja") return ja.sidebarlanguage1(inputs)
	return zh.sidebarlanguage1(inputs)
};
export { sidebarlanguage1 as "sidebarLanguage" }
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
const pagetitlesignin3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagetitlesignin3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagetitlesignin3", locale)
	if (locale === "en") return en.pagetitlesignin3(inputs)
	if (locale === "ko") return ko.pagetitlesignin3(inputs)
	if (locale === "ja") return ja.pagetitlesignin3(inputs)
	return zh.pagetitlesignin3(inputs)
};
export { pagetitlesignin3 as "pageTitleSignIn" }
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
const pagemetasignin3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.pagemetasignin3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("pagemetasignin3", locale)
	if (locale === "en") return en.pagemetasignin3(inputs)
	if (locale === "ko") return ko.pagemetasignin3(inputs)
	if (locale === "ja") return ja.pagemetasignin3(inputs)
	return zh.pagemetasignin3(inputs)
};
export { pagemetasignin3 as "pageMetaSignIn" }
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
const profilephotouploadpreview3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profilephotouploadpreview3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profilephotouploadpreview3", locale)
	if (locale === "en") return en.profilephotouploadpreview3(inputs)
	if (locale === "ko") return ko.profilephotouploadpreview3(inputs)
	if (locale === "ja") return ja.profilephotouploadpreview3(inputs)
	return zh.profilephotouploadpreview3(inputs)
};
export { profilephotouploadpreview3 as "profilePhotoUploadPreview" }
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
const testusernodelete3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernodelete3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernodelete3", locale)
	if (locale === "en") return en.testusernodelete3(inputs)
	if (locale === "ko") return ko.testusernodelete3(inputs)
	if (locale === "ja") return ja.testusernodelete3(inputs)
	return zh.testusernodelete3(inputs)
};
export { testusernodelete3 as "testUserNoDelete" }
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
const testusercreateunit3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreateunit3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreateunit3", locale)
	if (locale === "en") return en.testusercreateunit3(inputs)
	if (locale === "ko") return ko.testusercreateunit3(inputs)
	if (locale === "ja") return ja.testusercreateunit3(inputs)
	return zh.testusercreateunit3(inputs)
};
export { testusercreateunit3 as "testUserCreateUnit" }
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
const authdescription1 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authdescription1(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authdescription1", locale)
	if (locale === "en") return en.authdescription1(inputs)
	if (locale === "ko") return ko.authdescription1(inputs)
	if (locale === "ja") return ja.authdescription1(inputs)
	return zh.authdescription1(inputs)
};
export { authdescription1 as "authDescription" }
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
const authsignin2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.authsignin2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("authsignin2", locale)
	if (locale === "en") return en.authsignin2(inputs)
	if (locale === "ko") return ko.authsignin2(inputs)
	if (locale === "ja") return ja.authsignin2(inputs)
	return zh.authsignin2(inputs)
};
export { authsignin2 as "authSignIn" }
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
const profileuserinfo2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileuserinfo2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileuserinfo2", locale)
	if (locale === "en") return en.profileuserinfo2(inputs)
	if (locale === "ko") return ko.profileuserinfo2(inputs)
	if (locale === "ja") return ja.profileuserinfo2(inputs)
	return zh.profileuserinfo2(inputs)
};
export { profileuserinfo2 as "profileUserInfo" }
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
const profileuserinfoguide3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.profileuserinfoguide3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("profileuserinfoguide3", locale)
	if (locale === "en") return en.profileuserinfoguide3(inputs)
	if (locale === "ko") return ko.profileuserinfoguide3(inputs)
	if (locale === "ja") return ja.profileuserinfoguide3(inputs)
	return zh.profileuserinfoguide3(inputs)
};
export { profileuserinfoguide3 as "profileUserInfoGuide" }
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
const usernousersregistered3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernousersregistered3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernousersregistered3", locale)
	if (locale === "en") return en.usernousersregistered3(inputs)
	if (locale === "ko") return ko.usernousersregistered3(inputs)
	if (locale === "ja") return ja.usernousersregistered3(inputs)
	return zh.usernousersregistered3(inputs)
};
export { usernousersregistered3 as "userNoUsersRegistered" }
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
const usernosignedup3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.usernosignedup3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("usernosignedup3", locale)
	if (locale === "en") return en.usernosignedup3(inputs)
	if (locale === "ko") return ko.usernosignedup3(inputs)
	if (locale === "ja") return ja.usernosignedup3(inputs)
	return zh.usernosignedup3(inputs)
};
export { usernosignedup3 as "userNoSignedUp" }
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
const testusercreatedatonce4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusercreatedatonce4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusercreatedatonce4", locale)
	if (locale === "en") return en.testusercreatedatonce4(inputs)
	if (locale === "ko") return ko.testusercreatedatonce4(inputs)
	if (locale === "ja") return ja.testusercreatedatonce4(inputs)
	return zh.testusercreatedatonce4(inputs)
};
export { testusercreatedatonce4 as "testUserCreatedAtOnce" }
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
const testuserdeletingprogress3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeletingprogress3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeletingprogress3", locale)
	if (locale === "en") return en.testuserdeletingprogress3(inputs)
	if (locale === "ko") return ko.testuserdeletingprogress3(inputs)
	if (locale === "ja") return ja.testuserdeletingprogress3(inputs)
	return zh.testuserdeletingprogress3(inputs)
};
export { testuserdeletingprogress3 as "testUserDeletingProgress" }
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
const testusernousersguide4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernousersguide4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernousersguide4", locale)
	if (locale === "en") return en.testusernousersguide4(inputs)
	if (locale === "ko") return ko.testusernousersguide4(inputs)
	if (locale === "ja") return ja.testusernousersguide4(inputs)
	return zh.testusernousersguide4(inputs)
};
export { testusernousersguide4 as "testUserNoUsersGuide" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ year: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuseryear2 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuseryear2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuseryear2", locale)
	if (locale === "en") return en.testuseryear2(inputs)
	if (locale === "ko") return ko.testuseryear2(inputs)
	if (locale === "ja") return ja.testuseryear2(inputs)
	return zh.testuseryear2(inputs)
};
export { testuseryear2 as "testUserYear" }
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
const constructionunderconstruction2 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.constructionunderconstruction2(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("constructionunderconstruction2", locale)
	if (locale === "en") return en.constructionunderconstruction2(inputs)
	if (locale === "ko") return ko.constructionunderconstruction2(inputs)
	if (locale === "ja") return ja.constructionunderconstruction2(inputs)
	return zh.constructionunderconstruction2(inputs)
};
export { constructionunderconstruction2 as "constructionUnderConstruction" }
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
const featuresveltekit52 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featuresveltekit52(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featuresveltekit52", locale)
	if (locale === "en") return en.featuresveltekit52(inputs)
	if (locale === "ko") return ko.featuresveltekit52(inputs)
	if (locale === "ja") return ja.featuresveltekit52(inputs)
	return zh.featuresveltekit52(inputs)
};
export { featuresveltekit52 as "featureSvelteKit5" }
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
const featuretailwindcss4 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.featuretailwindcss4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("featuretailwindcss4", locale)
	if (locale === "en") return en.featuretailwindcss4(inputs)
	if (locale === "ko") return ko.featuretailwindcss4(inputs)
	if (locale === "ja") return ja.featuretailwindcss4(inputs)
	return zh.featuretailwindcss4(inputs)
};
export { featuretailwindcss4 as "featureTailwindCSS" }
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
const testuserdeleteconfirmation3 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteconfirmation3(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteconfirmation3", locale)
	if (locale === "en") return en.testuserdeleteconfirmation3(inputs)
	if (locale === "ko") return ko.testuserdeleteconfirmation3(inputs)
	if (locale === "ja") return ja.testuserdeleteconfirmation3(inputs)
	return zh.testuserdeleteconfirmation3(inputs)
};
export { testuserdeleteconfirmation3 as "testUserDeleteConfirmation" }
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
const testusernouserstodelete5 = (inputs = {}, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testusernouserstodelete5(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testusernouserstodelete5", locale)
	if (locale === "en") return en.testusernouserstodelete5(inputs)
	if (locale === "ko") return ko.testusernouserstodelete5(inputs)
	if (locale === "ja") return ja.testusernouserstodelete5(inputs)
	return zh.testusernouserstodelete5(inputs)
};
export { testusernouserstodelete5 as "testUserNoUsersToDelete" }
/**
* This function has been compiled by [Paraglide JS](https://inlang.com/m/gerre34r).
*
* - Changing this function will be over-written by the next build.
*
* - If you want to change the translations, you can either edit the source files e.g. `en.json`, or
* use another inlang app like [Fink](https://inlang.com/m/tdozzpar) or the [VSCode extension Sherlock](https://inlang.com/m/r7kp499g).
* 
* @param {{ count: NonNullable<unknown> }} inputs
* @param {{ locale?: "en" | "ko" | "ja" | "zh" }} options
* @returns {string}
*/
/* @__NO_SIDE_EFFECTS__ */
const testuserdeleteallconfirmation4 = (inputs, options = {}) => {
	if (experimentalMiddlewareLocaleSplitting && isServer === false) {
		return /** @type {any} */ (globalThis).__paraglide_ssr.testuserdeleteallconfirmation4(inputs) 
	}
	const locale = options.locale ?? getLocale()
	trackMessageCall("testuserdeleteallconfirmation4", locale)
	if (locale === "en") return en.testuserdeleteallconfirmation4(inputs)
	if (locale === "ko") return ko.testuserdeleteallconfirmation4(inputs)
	if (locale === "ja") return ja.testuserdeleteallconfirmation4(inputs)
	return zh.testuserdeleteallconfirmation4(inputs)
};
export { testuserdeleteallconfirmation4 as "testUserDeleteAllConfirmation" }
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
	if (locale === "en") return en.testuserprogressindicator3(inputs)
	if (locale === "ko") return ko.testuserprogressindicator3(inputs)
	if (locale === "ja") return ja.testuserprogressindicator3(inputs)
	return zh.testuserprogressindicator3(inputs)
};
export { testuserprogressindicator3 as "testUserProgressIndicator" }
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
