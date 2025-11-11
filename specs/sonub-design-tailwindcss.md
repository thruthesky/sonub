---
name: sonub-tailwind-design
version: 1.0.0
description: SvelteKit 프로젝트 Tailwind CSS 사용법 및 디자인 가이드 (Light 모드 전용)
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-11
updated: 2025-01-11
step: 16
priority: "**"
dependencies: ["sonub-setup-tailwind.md"]
tags: ["tailwindcss", "디자인", "사용법", "best-practices", "cva", "clsx", "light-mode"]
---

# Tailwind CSS 디자인 가이드

## 1. 개요

### 1.1. 목적
본 명세서는 SvelteKit 프로젝트에서 Tailwind CSS를 활용한 UI 디자인 방법과 실무 베스트 프랙티스를 정의합니다. 설치 및 설정은 `sonub-setup-tailwind.md`를 참조하세요.

### 1.2. 범위
- Tailwind CSS 기본 사용법
- 유틸리티 클래스 활용
- 반응형 디자인
- 커스텀 스타일 확장
- 실무 베스트 프랙티스 (class: 디렉티브, cn 함수, cva/tv, @layer 등)
- 본 프로젝트의 CSS 스타일링 규칙

### 1.3. 전제 조건
본 명세서는 다음 문서가 완료된 후 참조되어야 합니다:
- `sonub-setup-tailwind.md`: Tailwind CSS 설치 및 설정 완료

---

## 2. 기본 사용 방법

### 2.1. 기본 유틸리티 클래스 사용

Svelte 컴포넌트에서 Tailwind 유틸리티 클래스를 사용합니다.

**예시:** `src/routes/+page.svelte`

```svelte
<section class="p-8 space-y-4">
	<h1 class="text-3xl font-bold underline">
		Hello from Tailwind CSS!
	</h1>

	<p class="text-slate-700">
		시스템이 다크 모드여도 Tailwind는 class 전략이고 HTML에 dark가 없어 다크가 적용되지 않습니다.
	</p>

	<div class="rounded-lg border border-gray-300 p-4 bg-white shadow-sm">
		<p class="text-gray-800">이 박스는 라이트 톤에서만 스타일링됩니다.</p>
	</div>
</section>
```

### 2.2. PostCSS와 함께 사용

Svelte 컴포넌트의 `<style>` 태그에서 Tailwind를 참조할 수 있습니다.

**예시:**

```svelte
<h1 class="text-3xl font-bold underline">
	Hello world!
</h1>

<style lang="postcss">
	@reference "tailwindcss";

	:global(html) {
		background-color: theme(--color-gray-100);
	}
</style>
```

**설명:**
- `@reference "tailwindcss"`: Tailwind CSS 참조
- `theme()`: Tailwind 테마 값 사용

### 2.3. 반응형 디자인

Tailwind의 반응형 유틸리티를 사용합니다.

**예시:**

```svelte
<div class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4">
	<p class="text-sm md:text-base lg:text-lg">
		화면 크기에 따라 너비와 폰트 크기가 변경됩니다.
	</p>
</div>
```

**브레이크포인트:**
- `sm`: 640px 이상
- `md`: 768px 이상
- `lg`: 1024px 이상
- `xl`: 1280px 이상
- `2xl`: 1536px 이상

### 2.4. 커스텀 스타일 확장

`tailwind.config.ts`에서 커스텀 스타일을 정의합니다.

**예시:**

```typescript
import type { Config } from 'tailwindcss';

export default {
	darkMode: ['class'],
	content: ['./src/**/*.{html,js,svelte,ts}'],
	theme: {
		extend: {
			colors: {
				primary: {
					50: '#f0f9ff',
					100: '#e0f2fe',
					500: '#0ea5e9',
					900: '#0c4a6e'
				}
			},
			fontFamily: {
				sans: ['Inter', 'sans-serif']
			},
			spacing: {
				'128': '32rem',
				'144': '36rem'
			}
		}
	},
	plugins: []
} satisfies Config;
```

**사용 예시:**

```svelte
<button class="bg-primary-500 text-white font-sans px-4 py-2 rounded">
	커스텀 버튼
</button>
```

### 2.5. 플러그인 사용

#### 2.5.1. Typography 플러그인

마크다운 콘텐츠 스타일링에 유용합니다.

**사용 예시:**

```svelte
<article class="prose lg:prose-xl">
	<h1>Tailwind Typography</h1>
	<p>아름답게 스타일링된 마크다운 콘텐츠입니다.</p>
	<ul>
		<li>항목 1</li>
		<li>항목 2</li>
	</ul>
</article>
```

**Tailwind 설정에 플러그인 추가:**

```typescript
import typography from '@tailwindcss/typography';

export default {
	// ...
	plugins: [typography]
} satisfies Config;
```

#### 2.5.2. Forms 플러그인

폼 요소의 기본 스타일을 개선합니다.

**사용 예시:**

```svelte
<form class="space-y-4">
	<input
		type="email"
		class="form-input rounded-md border-gray-300"
		placeholder="이메일을 입력하세요"
	/>

	<select class="form-select rounded-md border-gray-300">
		<option>옵션 1</option>
		<option>옵션 2</option>
	</select>

	<textarea
		class="form-textarea rounded-md border-gray-300"
		rows="4"
		placeholder="메시지를 입력하세요"
	></textarea>
</form>
```

**Tailwind 설정에 플러그인 추가:**

```typescript
import forms from '@tailwindcss/forms';

export default {
	// ...
	plugins: [forms]
} satisfies Config;
```

---

## 3. 실무 베스트 프랙티스

본 섹션은 Tailwind CSS를 SvelteKit에서 사용할 때 "클래스 지옥"을 피하고 가독성과 유지보수성을 높이는 실전 팁을 제공합니다.

### 3.1. 코드 가독성 향상

#### 3.1.1. Prettier 자동 정렬 및 줄바꿈

**Prettier + prettier-plugin-tailwindcss**를 사용하여 클래스를 자동 정렬하고 긴 줄을 자동 줄바꿈합니다.

**설정:** (sonub-setup-tailwind.md 섹션 6.2 참조)

```json
{
	"plugins": ["prettier-plugin-tailwindcss"],
	"printWidth": 100
}
```

**사용 예시:**

```svelte
<button
	class="
		inline-flex items-center
		rounded-lg
		px-3 py-2
		text-sm font-medium
		shadow
		hover:opacity-90
	"
>
	저장
</button>
```

**참고:** Svelte에서 줄바꿈된 `class="..."`는 정상 동작합니다.

#### 3.1.2. 의미 단위로 클래스 그룹화

가독성을 위해 클래스를 의미 단위로 그룹화하여 작성합니다:

```svelte
<div
	class="
		/* Layout */
		flex items-center gap-4
		/* Sizing */
		w-full max-w-4xl
		/* Spacing */
		px-6 py-4
		/* Visual */
		rounded-lg border border-gray-200
		bg-white shadow-sm
		/* States */
		hover:shadow-md
		transition-shadow duration-200
	"
>
	콘텐츠
</div>
```

### 3.2. Svelte 전용 문법

#### 3.2.1. class: 디렉티브로 조건부 스타일

Svelte의 `class:` 디렉티브를 사용하면 삼항연산자 없이 조건부 클래스를 깔끔하게 토글할 수 있습니다.

**예시:**

```svelte
<script>
	export let error = false;
	export let disabled = false;
</script>

<input
	type="text"
	class="w-full rounded-md border px-3 py-2"
	class:border-red-500={error}
	class:ring-1={error}
	class:ring-red-500={error}
	class:opacity-50={disabled}
	class:cursor-not-allowed={disabled}
/>
```

**장점:**
- 조건부 로직이 명확하게 분리됨
- 삼항연산자 중첩 방지
- 코드 가독성 향상

### 3.3. 유틸리티 재사용

#### 3.3.1. cn() 헬퍼 함수

`clsx`와 `tailwind-merge`를 결합한 `cn()` 함수로 클래스 충돌을 방지하고 조건부 클래스를 안전하게 병합합니다.

**설치:**

```bash
npm install -D clsx tailwind-merge
```

**파일 위치:** `src/lib/utils/cn.ts`

**내용:**

```typescript
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * Tailwind CSS 클래스를 안전하게 병합하는 유틸리티 함수
 * clsx로 조건부 클래스를 처리하고, twMerge로 충돌 제거
 */
export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}
```

**사용 예시:**

```svelte
<script lang="ts">
	import { cn } from '$lib/utils/cn';

	export let variant: 'default' | 'destructive' = 'default';
	export let size: 'sm' | 'md' | 'lg' = 'md';
	export let className: string = '';
</script>

<button
	class={cn(
		'inline-flex items-center rounded-lg font-medium shadow transition',
		// 크기
		size === 'sm' && 'px-2 py-1 text-xs',
		size === 'md' && 'px-3 py-2 text-sm',
		size === 'lg' && 'px-4 py-3 text-base',
		// 변형
		variant === 'default' && 'bg-blue-600 text-white hover:bg-blue-700',
		variant === 'destructive' && 'bg-red-600 text-white hover:bg-red-700',
		// 외부에서 전달된 클래스
		className
	)}
>
	<slot />
</button>
```

#### 3.3.2. 변수로 클래스 묶음 추출

자주 사용하는 클래스 패턴은 변수로 추출하여 재사용합니다.

```svelte
<script lang="ts">
	const cardBase = 'rounded-xl border bg-white p-6 shadow-sm';
	const headingBase = 'text-2xl font-bold text-gray-900';
</script>

<div class={cardBase}>
	<h2 class={headingBase}>제목</h2>
	<p class="text-gray-600">내용</p>
</div>
```

### 3.4. Variant 패턴

크기, 색상, 상태 조합이 많은 컴포넌트는 variant 라이브러리를 사용하여 선언적으로 구성합니다.

#### 3.4.1. class-variance-authority (cva)

**설치:**

```bash
npm install -D class-variance-authority
```

**파일 위치:** `src/lib/styles/button.ts`

**내용:**

```typescript
import { cva, type VariantProps } from 'class-variance-authority';

export const buttonVariants = cva(
	// 기본 클래스
	'inline-flex items-center justify-center rounded-lg font-medium shadow transition-colors',
	{
		variants: {
			variant: {
				default: 'bg-blue-600 text-white hover:bg-blue-700',
				destructive: 'bg-red-600 text-white hover:bg-red-700',
				outline: 'border border-gray-300 bg-transparent hover:bg-gray-100',
				ghost: 'bg-transparent hover:bg-gray-100'
			},
			size: {
				sm: 'px-2 py-1 text-xs',
				md: 'px-3 py-2 text-sm',
				lg: 'px-4 py-3 text-base'
			}
		},
		defaultVariants: {
			variant: 'default',
			size: 'md'
		}
	}
);

export type ButtonVariants = VariantProps<typeof buttonVariants>;
```

**컴포넌트 사용:**

```svelte
<script lang="ts">
	import { cn } from '$lib/utils/cn';
	import { buttonVariants, type ButtonVariants } from '$lib/styles/button';

	type Props = ButtonVariants & {
		class?: string;
	};

	let { variant = 'default', size = 'md', class: className, ...rest }: Props = $props();
</script>

<button class={cn(buttonVariants({ variant, size }), className)} {...rest}>
	<slot />
</button>
```

**사용 예시:**

```svelte
<script>
	import Button from '$lib/components/ui/Button.svelte';
</script>

<Button variant="default" size="md">기본 버튼</Button>
<Button variant="destructive" size="sm">삭제</Button>
<Button variant="ghost" size="lg">투명 버튼</Button>
```

#### 3.4.2. tailwind-variants (tv)

**대안:** `tailwind-variants`도 유사한 기능을 제공하며, 더 간결한 문법을 선호하는 경우 사용할 수 있습니다.

```bash
npm install -D tailwind-variants
```

```typescript
import { tv } from 'tailwind-variants';

export const button = tv({
	base: 'inline-flex items-center rounded-lg font-medium shadow transition-colors',
	variants: {
		intent: {
			primary: 'bg-blue-600 text-white hover:bg-blue-700',
			ghost: 'bg-transparent text-gray-700 hover:bg-gray-800/70'
		},
		size: {
			sm: 'px-2 py-1 text-xs',
			md: 'px-3 py-2 text-sm'
		}
	},
	defaultVariants: {
		intent: 'primary',
		size: 'md'
	}
});
```

### 3.5. 컴포넌트 레이어 활용

#### 3.5.1. @layer components + @apply

반복되는 고정 패턴은 전역 컴포넌트 레이어로 선언하여 클래스명을 짧게 유지합니다.

**파일 위치:** `src/app.css`

**내용:**

```css
@import 'tailwindcss';
@plugin '@tailwindcss/forms';
@plugin '@tailwindcss/typography';

@layer components {
	/* 카드 컴포넌트 */
	.card {
		@apply rounded-xl border border-gray-200 bg-white p-4 shadow-sm;
	}

	.card-header {
		@apply mb-4 border-b border-gray-200 pb-3;
	}

	.card-title {
		@apply text-xl font-bold text-gray-900;
	}

	/* 버튼 베이스 */
	.btn {
		@apply inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium shadow transition-colors;
	}

	.btn-primary {
		@apply btn bg-blue-600 text-white hover:bg-blue-700;
	}

	.btn-secondary {
		@apply btn border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
	}

	/* 입력 필드 */
	.input {
		@apply w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500;
	}

	.input-error {
		@apply input border-red-500 focus:border-red-500 focus:ring-red-500;
	}
}
```

**사용 예시:**

```svelte
<div class="card">
	<div class="card-header">
		<h2 class="card-title">제목</h2>
	</div>
	<p class="text-gray-600">카드 내용</p>
	<button class="btn-primary">저장</button>
</div>
```

**주의사항:**
- 변형(variants)이 많은 요소는 `@apply`보다 cva/tv가 유지보수에 유리
- `@apply`는 반복되는 "불변"에 가까운 패턴에만 사용

### 3.6. 데이터 속성 활용

#### 3.6.1. data-* 속성으로 상태 표현

Radix UI 패턴처럼 `data-*` 속성을 활용하면 클래스를 컴포넌트 상태에 "반응"하게 만들 수 있습니다.

**전역 유틸리티 정의:**

```css
@layer utilities {
	.caret {
		@apply transition-transform duration-200;
	}

	.caret[data-open='true'] {
		@apply rotate-90;
	}

	.accordion-content {
		@apply grid transition-all duration-200;
		grid-template-rows: 0fr;
	}

	.accordion-content[data-open='true'] {
		grid-template-rows: 1fr;
	}
}
```

**컴포넌트 사용:**

```svelte
<script>
	let open = $state(false);
</script>

<button
	on:click={() => (open = !open)}
	aria-expanded={open}
	class="flex items-center gap-2 rounded-lg px-4 py-2 hover:bg-gray-100"
>
	<span>섹션 제목</span>
	<svg class="caret h-4 w-4" data-open={open}>
		<path d="M6 9l6 6 6-6" />
	</svg>
</button>

<div class="accordion-content overflow-hidden" data-open={open}>
	<div class="p-4">
		<p>아코디언 내용</p>
	</div>
</div>
```

**장점:**
- 상태와 스타일의 명확한 분리
- 조건부 클래스 문자열 불필요
- 재사용 가능한 유틸리티

### 3.7. 프로젝트 구조

#### 3.7.1. UI 컴포넌트 분리

UI 프리미티브 컴포넌트를 별도 폴더에 두고, 페이지에서는 역할과 배치만 표현합니다.

**권장 구조:**

```
src/
├── lib/
│   ├── components/
│   │   └── ui/           # 재사용 가능한 UI 컴포넌트
│   │       ├── Button.svelte
│   │       ├── Card.svelte
│   │       ├── Input.svelte
│   │       └── Badge.svelte
│   ├── styles/           # CVA/TV variant 정의
│   │   ├── button.ts
│   │   ├── card.ts
│   │   └── input.ts
│   └── utils/
│       └── cn.ts         # 클래스 병합 유틸리티
├── routes/
│   └── +page.svelte      # 페이지: 레이아웃과 조합만
└── app.css               # @layer components 정의
```

#### 3.7.2. 스타일 정의 위치

**규칙:**
1. **UI 프리미티브**: `lib/components/ui/` + `lib/styles/`에 variant 정의
2. **반복 고정 패턴**: `app.css`의 `@layer components`
3. **페이지별 고유 스타일**: 해당 페이지 컴포넌트의 `<style>` 블록
4. **유틸리티 확장**: `app.css`의 `@layer utilities`

### 3.8. 코드 품질 유지

#### 3.8.1. eslint-plugin-tailwindcss

**설치:**

```bash
npm install -D eslint-plugin-tailwindcss
```

**설정:** `.eslintrc.cjs` 또는 `eslint.config.js`

```javascript
module.exports = {
	extends: ['plugin:tailwindcss/recommended'],
	plugins: ['tailwindcss'],
	rules: {
		'tailwindcss/classnames-order': 'warn', // Prettier와 중복이면 off
		'tailwindcss/no-custom-classname': 'warn', // 존재하지 않는 클래스 경고
		'tailwindcss/no-contradicting-classname': 'error' // 충돌하는 클래스 에러
	}
};
```

**기능:**
- 미사용 또는 오타 클래스 검사
- 충돌하는 클래스 감지
- 커스텀 클래스 경고

#### 3.8.2. 디자인 토큰 문서화

팀 협업을 위해 디자인 토큰(색상, 간격, 라운드 등)을 문서화합니다.

**예시:** `docs/design-tokens.md`

```markdown
# 디자인 토큰

## 색상 팔레트
- Primary: blue-600 (#2563eb)
- Destructive: red-600 (#dc2626)
- Muted: gray-100 (#f3f4f6)

## 간격
- 컴포넌트 내부: p-4 (1rem)
- 섹션 간격: space-y-8 (2rem)

## Border Radius
- 카드: rounded-xl (0.75rem)
- 버튼: rounded-lg (0.5rem)
- 입력: rounded-md (0.375rem)
```

### 3.9. 본 프로젝트의 CSS 스타일링 규칙

본 프로젝트(Sonub)는 다음 Tailwind CSS 스타일링 규칙을 따릅니다.

#### 3.9.1. Layout vs Style 분리

**규칙:**
1. **Layout 관련 클래스**: `<element class="...">` 인라인에 직접 작성
   - `flex`, `grid`, `margin`, `padding`, `width`, `height`, `position` 등
2. **Style 관련 클래스**: `<style> ... { @apply ...; } </style>` 블록에서 `@apply` 사용
   - 색상, 폰트, 테두리, 배경, 그림자 등

**예시:**

```svelte
<script>
	// ...
</script>

<!-- Layout: 인라인 class -->
<div class="flex items-center gap-4 p-6">
	<button class="px-4 py-2 btn-primary">저장</button>
</div>

<style>
	@import 'tailwindcss' reference;

	/* Style: @apply */
	.btn-primary {
		@apply rounded-lg bg-blue-600 font-semibold text-white shadow-md hover:bg-blue-700;
	}
</style>
```

**⚠️ 중요:** Tailwind CSS v4에서 `@apply`를 사용하려면 `<style>` 블록 상단에 `@import 'tailwindcss' reference;`를 반드시 추가해야 합니다.

#### 3.9.2. Light 모드 전용

본 프로젝트는 **Light 모드만 지원**합니다. Dark 모드 관련 설정이나 클래스를 사용하지 않습니다.

**금지 사항:**
- ❌ `dark:` 변형 사용 금지
- ❌ HTML `<html class="dark">` 속성 추가 금지
- ❌ `app.css`에 `.dark { ... }` 스타일 정의 금지

**이유:**
- 클래스 길이 감소
- 코드 복잡도 감소
- 디자인 일관성 유지

### 3.10. 빠른 체크리스트

실무에서 Tailwind CSS를 효율적으로 사용하기 위한 체크리스트:

- ✅ **Prettier + Tailwind 플러그인**으로 자동 정렬/줄바꿈
- ✅ **Svelte의 `class:` 디렉티브**로 조건부 토글
- ✅ **`cn(clsx + twMerge)`**로 중복 제거 & 안전 병합
- ✅ **cva/tv**로 variants 구조화
- ✅ **반복 고정 패턴**은 `@apply` 컴포넌트 레이어로
- ✅ **`data-*` 속성**으로 상태 표현 간단화
- ✅ **UI 프리미티브 분리**로 페이지 코드 슬림화
- ✅ **Layout은 인라인, Style은 @apply** (본 프로젝트 규칙)
- ✅ **Light 모드 전용**, `dark:` 사용 금지

---

## 4. 참고 자료

### 4.1. 공식 문서

- **Tailwind CSS 공식 문서**: https://tailwindcss.com/docs
- **SvelteKit 공식 문서**: https://svelte.dev/docs/kit

### 4.2. 플러그인 문서

- **@tailwindcss/typography**: https://github.com/tailwindlabs/tailwindcss-typography
- **@tailwindcss/forms**: https://github.com/tailwindlabs/tailwindcss-forms
- **prettier-plugin-tailwindcss**: https://github.com/tailwindlabs/prettier-plugin-tailwindcss

### 4.3. 베스트 프랙티스 관련 라이브러리

- **clsx**: https://github.com/lukeed/clsx
- **tailwind-merge**: https://github.com/dcastil/tailwind-merge
- **class-variance-authority (cva)**: https://cva.style/docs
- **tailwind-variants (tv)**: https://www.tailwind-variants.org/

### 4.4. 관련 명세서

- **sonub-setup-tailwind.md**: Tailwind CSS 설치 및 설정 가이드

### 4.5. 추가 리소스

- **Tailwind UI**: https://tailwindui.com/ (유료 컴포넌트)
- **Headless UI**: https://headlessui.com/ (무료 컴포넌트)
- **shadcn-svelte**: https://www.shadcn-svelte.com/ (오픈소스 컴포넌트)

---

## 5. 변경 이력

### v1.0.0 (2025-01-11)
- **초기 문서 생성**: sonub-setup-tailwind.md에서 사용법 및 베스트 프랙티스 분리
- **섹션 2 추가**: 기본 사용 방법 (기존 setup의 Section 7)
- **섹션 3 추가**: 실무 베스트 프랙티스 (기존 setup의 Section 8)
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
- **참고 자료 추가**: 공식 문서, 플러그인, 베스트 프랙티스 라이브러리 링크
- **교차 참조 추가**: sonub-setup-tailwind.md 참조


