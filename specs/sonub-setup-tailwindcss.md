---
name: sonub-tailwind-setup
version: 1.2.0
description: SvelteKit 프로젝트에 Tailwind CSS 설치 및 설정 명세서 (Light 모드 전용)
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-08
updated: 2025-01-11
step: 15
priority: "**"
dependencies: ["sonub-setup-svelte.md"]
related: ["sonub-design-tailwindcss.md"]
tags: ["tailwindcss", "styling", "css", "설정", "light-mode", "installation"]
---

# SvelteKit 프로젝트 Tailwind CSS 설치 및 설정 명세서

## 1. 개요

### 1.1. 목적
본 명세서는 SvelteKit 5 프로젝트에서 Tailwind CSS 4.x를 설치하고 설정하는 방법을 정의합니다. Light 모드 전용 설정을 포함하며, 설치 및 검증 절차를 상세히 안내합니다.

### 1.2. 범위
- Tailwind CSS 설치 및 설정
- Vite 플러그인 구성
- Light 모드 전용 설정
- Prettier 플러그인 설정
- 검증 및 문제 해결

**사용법 및 디자인 가이드는 `sonub-design-tailwindcss.md`를 참조하세요.**

### 1.3. 전제 조건
본 명세서는 다음 문서가 완료된 후 실행되어야 합니다:
- `sonub-setup-svelte.md`: SvelteKit 프로젝트 초기 설정 완료

### 1.4. 관련 문서
- **sonub-design-tailwindcss.md**: Tailwind CSS 사용법 및 디자인 가이드

---

## 2. 중요 안내

### ⚠️ SvelteKit 설치 시 Tailwind CSS 함께 설치 필수

**SvelteKit 5 프로젝트를 생성할 때 반드시 Tailwind CSS를 함께 설치해야 합니다.**

`npx sv create` 명령 실행 시 다음 애드온을 선택해야 합니다:
- ✅ **tailwindcss** (필수 선택)
- ✅ **prettier-plugin-tailwindcss** (권장)

**이미 Tailwind CSS가 설치된 경우:**
- 본 명세서의 "3. 요구사항" 섹션에서 설치 확인
- "6. 설정 방법"부터 진행

**Tailwind CSS가 설치되지 않은 경우:**
- 본 명세서의 "5. 별도 설치 절차"를 참고하여 설치
- 이후 "6. 설정 방법"부터 진행

---

## 3. 요구사항

### 3.1. 시스템 요구사항
- Node.js 18.x 이상
- npm 9.x 이상 (또는 pnpm, yarn)
- SvelteKit 2.x
- Vite 7.x

### 3.2. 필수 패키지 버전

본 프로젝트는 다음 Tailwind CSS 관련 패키지를 사용합니다:

```json
{
  "devDependencies": {
    "tailwindcss": "^4.1.14",
    "@tailwindcss/vite": "^4.1.14",
    "@tailwindcss/forms": "^0.5.10",
    "@tailwindcss/typography": "^0.5.19",
    "prettier-plugin-tailwindcss": "^0.7.1"
  }
}
```

### 3.3. 설치 확인 방법

다음 명령어로 Tailwind CSS 설치 여부를 확인합니다:

```bash
npm list tailwindcss
```

**예상 출력 (설치된 경우):**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
└── tailwindcss@4.1.14
```

**예상 출력 (설치되지 않은 경우):**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
└── (empty)
```

---

## 4. 설치 환경

### 4.1. 운영 체제
- macOS (권장: Ventura 13.x 이상)
- Linux (Ubuntu 22.04 LTS 이상)
- Windows 10/11 (WSL2 사용 권장)

### 4.2. 개발 도구
- Visual Studio Code (권장)
- Tailwind CSS IntelliSense 확장 프로그램 (권장)

---

## 5. 별도 설치 절차

**이 섹션은 SvelteKit 설치 시 Tailwind CSS를 선택하지 않은 경우에만 수행합니다.**

### 5.1. 방법 1: Svelte CLI 사용 (권장)

Svelte CLI를 사용하여 자동으로 설치 및 설정합니다.

#### 5.1.1. 기본 설치

```bash
npx sv add tailwindcss
```

**질문 및 응답:**

1. **Which plugins would you like to add?**
   - `typography` (선택 사항): 타이포그래피 스타일링
   - `forms` (선택 사항): 폼 요소 스타일

   **선택 예시:**
   ```
   ✔ Which plugins would you like to add? › typography, forms
   ```

2. **설치 진행**
   CLI가 자동으로 다음 작업을 수행합니다:
   - 필수 패키지 설치
   - `vite.config.ts` 업데이트
   - `src/app.css` 생성 또는 업데이트
   - `src/routes/+layout.svelte` 업데이트
   - (Prettier 사용 시) `prettier-plugin-tailwindcss` 설치

**예상 출력:**
```
✔ Which plugins would you like to add? › typography, forms

◆  Setting up...
│
◇  Installing dependencies...
│  npm install tailwindcss @tailwindcss/vite @tailwindcss/typography @tailwindcss/forms
│
◇  Updating vite.config.ts
│
◇  Creating src/app.css
│
◇  Updating src/routes/+layout.svelte
│
└  Done!

Next steps:
  - Start using Tailwind CSS utility classes in your components
  - Customize your theme in vite.config.ts (Tailwind v4 uses Vite config)
```

#### 5.1.2. 플러그인 옵션 지정

특정 플러그인을 명령어에 직접 지정할 수 있습니다:

```bash
npx sv add tailwindcss="plugins:typography,forms"
```

### 5.2. 방법 2: 수동 설치

수동으로 설치하여 세밀한 제어가 필요한 경우 사용합니다.

#### 5.2.1. 패키지 설치

```bash
npm install -D tailwindcss @tailwindcss/vite
```

**선택적 플러그인 설치:**

```bash
npm install -D @tailwindcss/typography @tailwindcss/forms
```

#### 5.2.2. Vite 설정 파일 수정

`vite.config.ts` 파일을 열어 Tailwind Vite 플러그인을 추가합니다:

**파일 위치:** `vite.config.ts`

**기존 내용:**
```typescript
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [sveltekit()]
});
```

**수정 내용:**
```typescript
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
	plugins: [tailwindcss(), sveltekit()]
});
```

**중요:** `tailwindcss()` 플러그인은 `sveltekit()` 앞에 위치해야 합니다.

#### 5.2.3. CSS 파일 생성

`src/app.css` 파일을 생성하고 Tailwind 지시문을 추가합니다:

**파일 위치:** `src/app.css`

**내용:**
```css
@import "tailwindcss";
```

**Tailwind v4 변경 사항:**
- v3: `@tailwind base; @tailwind components; @tailwind utilities;`
- v4: `@import "tailwindcss";` (단일 import 문 사용)

#### 5.2.4. 레이아웃 파일 수정

`src/routes/+layout.svelte` 파일을 생성 또는 수정하여 CSS를 임포트합니다:

**파일 위치:** `src/routes/+layout.svelte`

**내용:**
```svelte
<script>
	import '../app.css';
</script>

<slot />
```

**Svelte 5 Runes 문법 사용 시:**
```svelte
<script>
	let { children } = $props();
	import '../app.css';
</script>

{@render children()}
```

#### 5.2.5. 설치 검증

개발 서버를 실행하여 설치를 검증합니다:

```bash
npm run dev
```

브라우저에서 `http://localhost:5173`을 열고 Tailwind 클래스가 적용되는지 확인합니다.

---

## 6. 설정 방법

### 6.1. Light 모드 전용 설정

본 프로젝트는 다크 모드를 지원하지 않고 Light 모드만 사용합니다.

#### 6.1.1. Tailwind 설정 (tailwind.config.ts)

**Tailwind v4부터는 별도의 `tailwind.config.js` 파일이 선택 사항입니다.**

다크 모드를 비활성화하려면 다음과 같이 설정합니다:

**파일 위치:** `tailwind.config.ts` (또는 `tailwind.config.js`)

**내용:**
```typescript
import type { Config } from 'tailwindcss';

export default {
	darkMode: ['class'], // 'class' 전략 사용 (HTML에 dark 클래스가 없으면 다크 모드 비활성화)
	content: ['./src/**/*.{html,js,svelte,ts}'],
	theme: {
		extend: {}
	},
	plugins: []
} satisfies Config;
```

**darkMode 옵션 설명:**
- `'media'`: 사용자의 시스템 설정에 따라 다크 모드 자동 적용 (기본값)
- `'class'`: HTML에 `dark` 클래스가 있을 때만 다크 모드 적용
- **Light 전용 설정:** `'class'`로 설정하고 `dark` 클래스를 사용하지 않으면 항상 Light 모드

#### 6.1.2. 전역 CSS 설정 (app.css)

Light 모드 전용 CSS 변수 및 스타일을 정의합니다.

**파일 위치:** `src/app.css`

**내용:**
```css
@import "tailwindcss";

/* Light 모드 전용 CSS 변수 */
@layer base {
	:root {
		--radius: 0.5rem;

		/* 디자인 토큰 (필요 시 추가) */
		/* --bg: #ffffff; */
		/* --fg: #0f172a; */
	}

	/* ❌ 다크 모드 스타일은 정의하지 않습니다 */
	/* .dark { ... } 생략 */
}
```

**설명:**
- `@layer base`: Tailwind의 기본 스타일 레이어에 추가
- `:root`: 전역 CSS 변수 정의
- 다크 모드 관련 `.dark` 선택자는 생략

#### 6.1.3. HTML 템플릿 설정 (app.html)

HTML에 `dark` 클래스를 추가하지 않습니다.

**파일 위치:** `src/app.html`

**내용:**
```html
<!doctype html>
<html lang="ko"> <!-- ✅ dark 클래스 없음 -->
	<head>
		<meta charset="utf-8" />
		<link rel="icon" href="%sveltekit.assets%/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		%sveltekit.head%
	</head>
	<body>
		<div style="display: contents">%sveltekit.body%</div>
	</body>
</html>
```

**중요:**
- `<html>` 태그에 `class="dark"` 속성을 추가하지 않습니다
- 과거 코드나 라이브러리가 `dark` 클래스를 추가하는 경우 제거합니다

#### 6.1.4. 레이아웃 파일 (필수)

전역 CSS를 임포트합니다.

**파일 위치:** `src/routes/+layout.svelte`

**내용:**
```svelte
<script>
	import '../app.css';
</script>

<slot />
```

### 6.2. Prettier 플러그인 설정 (선택 사항)

Tailwind CSS 클래스의 자동 정렬을 위해 Prettier 플러그인을 설정합니다.

#### 6.2.1. 플러그인 설치 확인

```bash
npm list prettier-plugin-tailwindcss
```

**설치되지 않은 경우:**
```bash
npm install -D prettier-plugin-tailwindcss
```

#### 6.2.2. Prettier 설정 파일 수정

**파일 위치:** `.prettierrc`

**내용:**
```json
{
	"useTabs": true,
	"singleQuote": true,
	"trailingComma": "none",
	"printWidth": 100,
	"plugins": ["prettier-plugin-svelte", "prettier-plugin-tailwindcss"],
	"overrides": [
		{
			"files": "*.svelte",
			"options": {
				"parser": "svelte"
			}
		}
	]
}
```

**중요:** `prettier-plugin-tailwindcss`는 플러그인 배열의 마지막에 위치해야 합니다.

---

## 7. shadcn-svelte와 함께 사용 (선택 사항)

shadcn-svelte 컴포넌트 라이브러리를 사용하는 경우에도 Light 모드만 지원 가능합니다.

### 7.1. 설정 방법

shadcn-svelte 컴포넌트가 `dark:` 변형을 포함하더라도, HTML에 `dark` 클래스가 없으면 다크 스타일은 적용되지 않습니다.

### 7.2. 컴포넌트 색상 토큰

`components.json`의 색상 토큰을 Light 기준으로만 유지합니다.

**예시:** `components.json`

```json
{
	"style": "default",
	"tailwind": {
		"config": "tailwind.config.ts",
		"css": "src/app.css",
		"baseColor": "slate"
	},
	"aliases": {
		"components": "$lib/components",
		"utils": "$lib/utils"
	}
}
```

---

## 8. 검증 방법

### 8.1. 설치 검증 체크리스트

다음 항목을 확인하여 Tailwind CSS가 올바르게 설정되었는지 검증합니다:

#### ✅ 1. 패키지 설치 확인

```bash
npm list tailwindcss @tailwindcss/vite
```

**예상 출력:**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
├── tailwindcss@4.1.14
└── @tailwindcss/vite@4.1.14
```

#### ✅ 2. Vite 설정 확인

`vite.config.ts` 파일에 Tailwind 플러그인이 포함되어 있는지 확인:

```bash
grep -n "tailwindcss" vite.config.ts
```

**예상 출력:**
```
3:import tailwindcss from '@tailwindcss/vite';
6:	plugins: [tailwindcss(), sveltekit()]
```

#### ✅ 3. CSS 파일 확인

`src/app.css` 파일이 존재하고 Tailwind import 문을 포함하는지 확인:

```bash
cat src/app.css | grep "@import"
```

**예상 출력:**
```
@import "tailwindcss";
```

#### ✅ 4. 레이아웃 파일 확인

`src/routes/+layout.svelte`에서 `app.css`를 임포트하는지 확인:

```bash
grep -n "app.css" src/routes/+layout.svelte
```

**예상 출력:**
```
2:	import '../app.css';
```

#### ✅ 5. 다크 모드 비활성화 확인

`src/app.html`에 `dark` 클래스가 없는지 확인:

```bash
grep -n "dark" src/app.html
```

**예상 출력:**
```
(출력 없음 - dark 클래스가 없어야 정상)
```

#### ✅ 6. 개발 서버 실행

```bash
npm run dev
```

**예상 출력:**
```
  VITE v7.1.10  ready in 1234 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h + enter to show help
```

#### ✅ 7. 브라우저 테스트

브라우저에서 `http://localhost:5173`을 열고:
1. 개발자 도구 (F12) 열기
2. Elements 탭에서 Tailwind 클래스 적용 확인
3. Computed 스타일에서 Tailwind 유틸리티 값 확인

**예시:**
```html
<h1 class="text-3xl font-bold">
  <!-- Computed: font-size: 1.875rem; font-weight: 700; -->
</h1>
```

#### ✅ 8. 빌드 테스트

프로덕션 빌드가 성공하는지 확인:

```bash
npm run build
```

**예상 출력:**
```
vite v7.1.10 building for production...
✓ 123 modules transformed.
✓ built in 2.34s
```

### 8.2. 동작 확인 예시

간단한 테스트 페이지를 생성하여 Tailwind 동작을 확인합니다.

**파일 위치:** `src/routes/tailwind-test/+page.svelte`

**내용:**
```svelte
<section class="min-h-screen bg-gray-50 p-8">
	<div class="max-w-4xl mx-auto space-y-8">
		<h1 class="text-4xl font-bold text-gray-900">
			Tailwind CSS 테스트 페이지
		</h1>

		<div class="bg-white rounded-lg shadow-md p-6 space-y-4">
			<h2 class="text-2xl font-semibold text-gray-800">색상 테스트</h2>
			<div class="grid grid-cols-5 gap-4">
				<div class="h-20 bg-red-500 rounded"></div>
				<div class="h-20 bg-blue-500 rounded"></div>
				<div class="h-20 bg-green-500 rounded"></div>
				<div class="h-20 bg-yellow-500 rounded"></div>
				<div class="h-20 bg-purple-500 rounded"></div>
			</div>
		</div>

		<div class="bg-white rounded-lg shadow-md p-6 space-y-4">
			<h2 class="text-2xl font-semibold text-gray-800">타이포그래피 테스트</h2>
			<p class="text-sm text-gray-600">작은 텍스트 (text-sm)</p>
			<p class="text-base text-gray-700">기본 텍스트 (text-base)</p>
			<p class="text-lg text-gray-800">큰 텍스트 (text-lg)</p>
			<p class="text-xl text-gray-900">더 큰 텍스트 (text-xl)</p>
		</div>

		<div class="bg-white rounded-lg shadow-md p-6 space-y-4">
			<h2 class="text-2xl font-semibold text-gray-800">반응형 테스트</h2>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				<div class="bg-indigo-100 p-4 rounded">박스 1</div>
				<div class="bg-indigo-200 p-4 rounded">박스 2</div>
				<div class="bg-indigo-300 p-4 rounded">박스 3</div>
			</div>
		</div>
	</div>
</section>
```

**테스트 방법:**
1. 개발 서버 실행: `npm run dev`
2. 브라우저에서 `http://localhost:5173/tailwind-test` 접속
3. 색상, 타이포그래피, 반응형 레이아웃 확인
4. 브라우저 창 크기 조절하여 반응형 동작 확인

---

## 9. 문제 해결

### 9.1. 스타일이 적용되지 않음

**증상:**
- Tailwind 클래스를 사용했지만 스타일이 적용되지 않습니다.

**해결 방법:**

#### 방법 1: content 경로 확인

`tailwind.config.ts`의 `content` 배열에 모든 템플릿 파일이 포함되어 있는지 확인:

```typescript
export default {
	content: [
		'./src/**/*.{html,js,svelte,ts}',
		// 다른 경로가 필요한 경우 추가
	],
	// ...
} satisfies Config;
```

#### 방법 2: 캐시 삭제 및 재시작

```bash
# 개발 서버 중지 (Ctrl+C)

# .svelte-kit 캐시 삭제
rm -rf .svelte-kit

# node_modules/.vite 캐시 삭제
rm -rf node_modules/.vite

# 개발 서버 재시작
npm run dev
```

#### 방법 3: CSS import 확인

`src/routes/+layout.svelte`에서 `app.css`를 정확히 임포트하는지 확인:

```svelte
<script>
	import '../app.css'; // ✅ 정확한 경로
	// import '$lib/app.css'; // ❌ 잘못된 경로
</script>
```

### 9.2. 다크 모드가 자동으로 적용됨

**증상:**
- Light 모드만 사용하려고 했지만 시스템 다크 모드에 따라 스타일이 변경됩니다.

**해결 방법:**

#### 방법 1: darkMode 설정 변경

`tailwind.config.ts`에서 `darkMode`를 `'class'`로 변경:

```typescript
export default {
	darkMode: ['class'], // 'media'가 아닌 'class' 사용
	// ...
} satisfies Config;
```

#### 방법 2: HTML에서 dark 클래스 제거

`src/app.html`에서 `class="dark"` 속성 제거:

```html
<html lang="ko"> <!-- ✅ dark 클래스 없음 -->
```

#### 방법 3: 스크립트에서 dark 클래스 추가 방지

전역 스크립트나 라이브러리가 `dark` 클래스를 추가하는지 확인하고 제거:

```javascript
// ❌ 다음과 같은 코드 제거
document.documentElement.classList.add('dark');
```

### 9.3. Prettier가 Tailwind 클래스를 정렬하지 않음

**증상:**
- Prettier를 실행해도 Tailwind 클래스가 정렬되지 않습니다.

**해결 방법:**

#### 방법 1: 플러그인 설치 확인

```bash
npm list prettier-plugin-tailwindcss
```

설치되지 않은 경우:
```bash
npm install -D prettier-plugin-tailwindcss
```

#### 방법 2: Prettier 설정 확인

`.prettierrc` 파일에 플러그인 추가:

```json
{
	"plugins": ["prettier-plugin-svelte", "prettier-plugin-tailwindcss"]
}
```

**중요:** `prettier-plugin-tailwindcss`는 배열의 마지막에 위치해야 합니다.

#### 방법 3: 에디터 재시작

VSCode를 재시작하여 Prettier 설정을 다시 로드합니다.

### 9.4. 빌드 시 Tailwind 클래스가 제거됨

**증상:**
- 개발 환경에서는 정상 작동하지만 프로덕션 빌드 후 일부 클래스가 적용되지 않습니다.

**해결 방법:**

#### 방법 1: 동적 클래스명 사용 금지

Tailwind는 정적 분석을 사용하므로 동적으로 생성된 클래스명은 인식하지 못합니다.

**❌ 잘못된 사용:**
```svelte
<script>
	let color = 'blue';
</script>

<div class="text-{color}-500"> <!-- 동적 클래스명 - 작동하지 않음 -->
	텍스트
</div>
```

**✅ 올바른 사용:**
```svelte
<script>
	let color = 'blue';
	let colorClass = color === 'blue' ? 'text-blue-500' : 'text-red-500';
</script>

<div class={colorClass}> <!-- 완전한 클래스명 사용 -->
	텍스트
</div>
```

#### 방법 2: safelist 사용

동적 클래스가 필요한 경우 `tailwind.config.ts`에 추가:

```typescript
export default {
	safelist: [
		'text-blue-500',
		'text-red-500',
		'bg-green-500',
		// 또는 패턴 사용
		{
			pattern: /bg-(red|green|blue)-(100|500|900)/
		}
	],
	// ...
} satisfies Config;
```

### 9.5. VSCode IntelliSense가 작동하지 않음

**증상:**
- VSCode에서 Tailwind 클래스 자동완성이 표시되지 않습니다.

**해결 방법:**

#### 방법 1: Tailwind CSS IntelliSense 확장 프로그램 설치

VSCode Extensions에서 "Tailwind CSS IntelliSense" 설치:

```
확장 프로그램 ID: bradlc.vscode-tailwindcss
```

#### 방법 2: VSCode 설정 추가

`.vscode/settings.json` 파일 생성 또는 수정:

```json
{
	"tailwindCSS.experimental.classRegex": [
		["class:\\s*?[\"'`]([^\"'`]*).*?[\"'`]", "[\"'`]([^\"'`]*).*?[\"'`]"]
	],
	"tailwindCSS.includeLanguages": {
		"svelte": "html"
	},
	"editor.quickSuggestions": {
		"strings": true
	}
}
```

#### 방법 3: VSCode 재시작

설정 변경 후 VSCode를 완전히 종료하고 재시작합니다.

---

## 10. 다음 단계

Tailwind CSS 설정 완료 후 다음 작업을 진행합니다:

1. **컴포넌트 라이브러리 설치 (선택 사항)**
   - shadcn-svelte
   - DaisyUI
   - Flowbite Svelte

2. **디자인 시스템 구축**
   - 커스텀 테마 색상 정의
   - 타이포그래피 스케일 설정
   - 스페이싱 시스템 확립

3. **UI 컴포넌트 개발**
   - 버튼, 카드, 모달 등 기본 컴포넌트
   - 폼 요소 스타일링
   - 레이아웃 컴포넌트

4. **반응형 디자인 구현**
   - 모바일, 태블릿, 데스크톱 레이아웃
   - 브레이크포인트별 최적화

---

## 11. 참고 자료

### 11.1. 공식 문서

- **Svelte CLI - Tailwind CSS**: https://svelte.dev/docs/cli/tailwind
- **Tailwind CSS - SvelteKit 설치 가이드**: https://tailwindcss.com/docs/installation/framework-guides/sveltekit
- **Tailwind CSS 공식 문서**: https://tailwindcss.com/docs
- **SvelteKit 공식 문서**: https://svelte.dev/docs/kit

### 11.2. 플러그인 문서

- **@tailwindcss/typography**: https://github.com/tailwindlabs/tailwindcss-typography
- **@tailwindcss/forms**: https://github.com/tailwindlabs/tailwindcss-forms
- **prettier-plugin-tailwindcss**: https://github.com/tailwindlabs/prettier-plugin-tailwindcss

### 11.3. 관련 명세서

- **sonub-setup-svelte.md**: SvelteKit 프로젝트 초기 설정
- **sonub-design-tailwindcss.md**: Tailwind CSS 사용법 및 디자인 가이드

### 11.4. 추가 리소스

- **Tailwind UI**: https://tailwindui.com/ (유료 컴포넌트)
- **Headless UI**: https://headlessui.com/ (무료 컴포넌트)
- **shadcn-svelte**: https://www.shadcn-svelte.com/ (오픈소스 컴포넌트)

---

## 12. 승인 기준

본 명세서에 따른 Tailwind CSS 설정이 완료되었다고 판단하는 기준:

### 12.1. 필수 요구사항

- ✅ `tailwindcss` 및 `@tailwindcss/vite` 패키지 설치 완료
- ✅ `vite.config.ts`에 Tailwind Vite 플러그인 추가
- ✅ `src/app.css`에 `@import "tailwindcss"` 선언
- ✅ `src/routes/+layout.svelte`에서 `app.css` 임포트
- ✅ `src/app.html`에 `dark` 클래스 미포함 (Light 모드 전용)
- ✅ `npm run dev` 실행 시 Tailwind 클래스 정상 적용
- ✅ `npm run build` 실행 시 프로덕션 빌드 성공

### 12.2. 선택 요구사항

- ✅ `@tailwindcss/typography` 플러그인 설치 (선택)
- ✅ `@tailwindcss/forms` 플러그인 설치 (선택)
- ✅ `prettier-plugin-tailwindcss` 설치 및 설정 (권장)
- ✅ VSCode Tailwind CSS IntelliSense 확장 프로그램 설치 (권장)

### 12.3. 동작 검증

- ✅ 테스트 페이지에서 색상, 타이포그래피, 레이아웃 클래스 정상 작동
- ✅ 반응형 유틸리티 클래스가 브라우저 크기에 따라 정상 동작
- ✅ 시스템이 다크 모드여도 Light 모드로 표시 (darkMode: 'class' 설정)
- ✅ 프로덕션 빌드 후 모든 Tailwind 클래스 정상 작동

---

## 13. 변경 이력

### v1.2.0 (2025-01-11)
- **문서 재구성**: 설치/설정 문서와 사용법/디자인 가이드 분리
  - Section 7, 8 제거: 사용법 및 베스트 프랙티스를 sonub-design-tailwindcss.md로 이동
  - Section 9-15를 Section 7-13으로 재번호
  - YAML 헤더에 `related` 필드 추가: sonub-design-tailwindcss.md 참조
  - 개요 섹션에 관련 문서 링크 추가
  - 참고 자료에 sonub-design-tailwindcss.md 교차 참조 추가
- **문서 범위 명확화**: 설치 및 설정에만 집중
- **베스트 프랙티스 관련 내용 제거**: 사용법, cn 함수, cva/tv, @layer 등은 디자인 가이드로 이전
- **승인 기준 간소화**: 설치/설정 관련 항목만 유지

### v1.1.0 (2025-01-11)
- **섹션 8 추가**: 실무 베스트 프랙티스
  - 코드 가독성 향상 (Prettier, 클래스 그룹화)
  - Svelte `class:` 디렉티브 사용법
  - `cn()` 헬퍼 함수 (clsx + tailwind-merge)
  - Variant 패턴 (cva, tailwind-variants)
  - `@layer components` + `@apply` 사용법
  - `data-*` 속성 활용
  - 프로젝트 구조 권장사항
  - eslint-plugin-tailwindcss 설정
  - 본 프로젝트의 CSS 스타일링 규칙 (Layout vs Style 분리, Light 모드 전용)
  - 빠른 체크리스트
- **참고 자료 추가**: clsx, tailwind-merge, cva, tailwind-variants 링크
- **승인 기준 추가**: 유틸리티 라이브러리 설치 항목
- **섹션 번호 재정렬**: 기존 8-14 → 9-15
- **svelte.config.js 업데이트**: css-unused-selector 경고 무시 설정 추가
- **`@import 'tailwindcss' reference` 규칙 명시**: Tailwind v4에서 `@apply` 사용 시 필수

### v1.0.0 (2025-01-08)
- 초기 명세서 작성
- SED 형식 YAML 헤더 추가
- Tailwind CSS 4.x 설치 및 설정 방법 정의
- Light 모드 전용 설정 추가
- Svelte CLI 및 수동 설치 방법 문서화
- 공식 문서 참조 추가 (svelte.dev, tailwindcss.com)
- 검증 방법 및 문제 해결 가이드 추가
- 승인 기준 정의



