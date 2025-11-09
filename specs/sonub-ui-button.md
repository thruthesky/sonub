---
name: sonub-ui-button
version: 1.0.0
description: Sonub 프로젝트 Button UI 컴포넌트 명세서 (Light 모드 전용)
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 30
priority: "**"
dependencies: ["sonub-setup-shadcn.md", "sonub-setup-tailwind.md", "sonub-design-components.md"]
tags: ["ui", "button", "component", "light-mode", "svelte-5"]
---

# Sonub Button UI 컴포넌트 명세서

## 1. 개요

### 1.1. 목적
본 명세서는 Sonub 프로젝트에서 사용하는 Button UI 컴포넌트의 구현, 사용법, 그리고 왜 shadcn-svelte 패키지를 사용하지 않고 직접 구현했는지에 대한 이유를 정의합니다.

### 1.2. 범위
- Button 컴포넌트 구조 및 코드 설명
- variant 및 size 옵션 정의
- shadcn-svelte 대신 수동 구현한 이유
- Light 모드 전용 설정 배경
- 사용 예제 및 최적화 방법

### 1.3. shadcn-svelte를 사용하지 않고 직접 구현한 이유

#### 1.3.1. Dark 모드 문제

**shadcn-svelte의 기본 동작:**
- shadcn-svelte는 **기본적으로 dark mode를 지원**하는 컴포넌트를 생성합니다
- 생성된 컴포넌트 코드에는 `dark:` 변형 클래스가 포함되어 있습니다
- 예: `dark:border-destructive`, `dark:bg-gray-800` 등

**Sonub 프로젝트의 요구사항:**
- Sonub은 **Light 모드만 지원**합니다
- Dark 모드 스타일은 필요하지 않으며, 코드가 불필요하게 복잡해집니다
- 사용자 경험의 일관성을 위해 항상 Light 테마만 사용합니다

**Light 모드로 고정하기 위한 추가 작업:**

shadcn-svelte 컴포넌트를 Light 모드로 고정하려면 다음과 같은 번거로운 작업이 필요합니다:

1. **Tailwind 설정 변경:**
   ```typescript
   // tailwind.config.ts
   export default {
     darkMode: ['class'], // 'media'에서 'class'로 변경
     // ...
   }
   ```

2. **HTML에서 dark 클래스 제거:**
   ```html
   <!-- src/app.html -->
   <html lang="ko"> <!-- dark 클래스를 추가하지 않음 -->
   ```

3. **모든 컴포넌트에서 dark: 클래스 수동 제거:**
   ```svelte
   <!-- ❌ shadcn-svelte 생성 코드 -->
   <div class="bg-white dark:bg-gray-800">

   <!-- ✅ 수동으로 제거한 코드 -->
   <div class="bg-white">
   ```

4. **CSS 변수에서 dark 모드 변수 제거:**
   ```css
   /* ❌ shadcn-svelte가 추가하는 코드 */
   @layer base {
     :root { ... }
     .dark { ... }  <!-- 제거 필요 -->
   }
   ```

5. **shadcn-svelte CLI로 컴포넌트 추가 시마다 반복:**
   - 새 컴포넌트를 추가할 때마다 위 작업을 반복해야 합니다
   - 실수로 dark 클래스가 포함될 가능성이 있습니다

#### 1.3.2. 수동 구현의 장점

**Sonub이 선택한 방법: UI 컴포넌트를 직접 작성**

shadcn-svelte의 기본 코드를 참고하여 `src/lib/components/ui/**/*.svelte`로 직접 구현했습니다.

**장점:**

1. **Light 모드 전용 코드:**
   - `dark:` 변형 클래스가 처음부터 포함되지 않음
   - 코드가 간결하고 읽기 쉬움
   - 프로덕션 빌드 시 번들 크기 감소

2. **완전한 제어:**
   - 프로젝트 요구사항에 맞게 정확히 커스터마이징 가능
   - 불필요한 의존성 제거 (shadcn-svelte CLI 불필요)
   - 코드 소유권 및 유지보수 용이

3. **Svelte 5 Runes 완전 호환:**
   - `$props()`, `$state()`, `$derived()`, `Snippet` 등 최신 문법 사용
   - shadcn-svelte가 아직 완벽히 지원하지 않을 수 있는 Svelte 5 기능 활용

4. **설정 번거로움 제거:**
   - Tailwind dark mode 설정 고민 불필요
   - HTML에 dark 클래스 관리 불필요
   - CSS 변수 중복 관리 불필요

5. **팀 협업 효율성:**
   - 새로운 개발자가 shadcn-svelte 학습 불필요
   - Sonub 프로젝트만의 명확한 컴포넌트 규칙 적용
   - 코드 리뷰 및 유지보수 간소화

**결론:**

shadcn-svelte는 훌륭한 라이브러리지만, **Sonub의 Light 모드 전용 요구사항**과 **Svelte 5 최적화**를 위해 직접 구현하는 것이 더 효율적입니다.

---

## 2. 컴포넌트 구조

### 2.1. 파일 위치

```
src/lib/components/ui/button/
├── button.svelte    # Button 컴포넌트 구현
└── index.ts         # Export 파일
```

### 2.2. 디렉토리 설명

- **button.svelte**: 실제 버튼 컴포넌트 구현 파일
- **index.ts**: 외부에서 임포트할 수 있도록 재내보내기 파일

---

## 3. 구현 코드

### 3.1. Button 컴포넌트 (button.svelte)

**파일 경로:** `src/lib/components/ui/button/button.svelte`

**전체 코드:**

```svelte
<script lang="ts">
	/**
	 * Button 컴포넌트
	 *
	 * 다양한 스타일과 크기를 지원하는 버튼 컴포넌트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLButtonAttributes, HTMLAnchorAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface ButtonProps extends HTMLButtonAttributes {
		variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
		size?: 'default' | 'sm' | 'lg' | 'icon' | 'icon-sm' | 'icon-lg';
		class?: string;
		children?: Snippet;
		href?: never; // 버튼일 때는 href가 없음
	}

	interface LinkProps extends Omit<HTMLAnchorAttributes, 'disabled'> {
		variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
		size?: 'default' | 'sm' | 'lg' | 'icon' | 'icon-sm' | 'icon-lg';
		class?: string;
		children?: Snippet;
		href: string; // 링크일 때는 href가 필수
		disabled?: boolean;
	}

	type Props = ButtonProps | LinkProps;

	let {
		variant = 'default',
		size = 'default',
		class: className,
		children,
		href,
		disabled = false,
		...restProps
	}: Props & { disabled?: boolean } = $props();

	/**
	 * 버튼 variant별 스타일
	 */
	const variantStyles = {
		default: 'bg-primary text-primary-foreground hover:bg-primary/90',
		destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
		outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
		secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
		ghost: 'hover:bg-accent hover:text-accent-foreground',
		link: 'text-primary underline-offset-4 hover:underline'
	};

	/**
	 * 버튼 size별 스타일
	 */
	const sizeStyles = {
		default: 'h-10 px-4 py-2',
		sm: 'h-9 rounded-md px-3',
		lg: 'h-11 rounded-md px-8',
		icon: 'h-10 w-10',
		'icon-sm': 'h-8 w-8',
		'icon-lg': 'h-12 w-12'
	};

	const iconStyles = {
		default: '[&>svg]:h-4 [&>svg]:w-4 [&>svg]:shrink-0',
		sm: '[&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:shrink-0',
		lg: '[&>svg]:h-5 [&>svg]:w-5 [&>svg]:shrink-0',
		icon: '[&>svg]:h-5 [&>svg]:w-5 [&>svg]:shrink-0',
		'icon-sm': '[&>svg]:h-4 [&>svg]:w-4 [&>svg]:shrink-0',
		'icon-lg': '[&>svg]:h-6 [&>svg]:w-6 [&>svg]:shrink-0'
	} as const;
</script>

{#if href}
	<!-- href가 있으면 <a> 태그로 렌더링 -->
	<a
		href={disabled ? undefined : href}
		aria-disabled={disabled ? 'true' : undefined}
		tabindex={disabled ? -1 : undefined}
		data-disabled={disabled ? 'true' : undefined}
		class={cn(
			'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer disabled:pointer-events-none disabled:opacity-50',
			variantStyles[variant],
			sizeStyles[size],
			iconStyles[size as keyof typeof iconStyles],
			disabled ? 'pointer-events-none opacity-50' : '',
			className
		)}
		{...(restProps as any)}
	>
		{@render children?.()}
	</a>
{:else}
	<!-- href가 없으면 <button> 태그로 렌더링 -->
	<button
		disabled={disabled}
		class={cn(
			'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer disabled:pointer-events-none disabled:opacity-50',
			variantStyles[variant],
			sizeStyles[size],
			iconStyles[size as keyof typeof iconStyles],
			className
		)}
		{...(restProps as any)}
	>
		{@render children?.()}
	</button>
{/if}
```

### 3.2. Export 파일 (index.ts)

**파일 경로:** `src/lib/components/ui/button/index.ts`

```typescript
import Root from './button.svelte';

export {
	Root,
	//
	Root as Button
};
```

---

## 4. 주요 기능 설명

### 4.1. Props 타입 정의

#### ButtonProps
버튼 요소(`<button>`)로 렌더링될 때 사용하는 Props입니다.

```typescript
interface ButtonProps extends HTMLButtonAttributes {
	variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
	size?: 'default' | 'sm' | 'lg' | 'icon' | 'icon-sm' | 'icon-lg';
	class?: string;
	children?: Snippet;
	href?: never; // href가 없어야 함
}
```

#### LinkProps
링크 요소(`<a>`)로 렌더링될 때 사용하는 Props입니다.

```typescript
interface LinkProps extends Omit<HTMLAnchorAttributes, 'disabled'> {
	variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
	size?: 'default' | 'sm' | 'lg' | 'icon' | 'icon-sm' | 'icon-lg';
	class?: string;
	children?: Snippet;
	href: string; // href가 필수
	disabled?: boolean;
}
```

**핵심 차이점:**
- `href`가 있으면 `<a>` 태그로 렌더링
- `href`가 없으면 `<button>` 태그로 렌더링
- TypeScript 타입 시스템으로 안전하게 구분

### 4.2. Variant 스타일

Button 컴포넌트는 6가지 variant를 지원합니다:

| Variant | 설명 | 스타일 |
|---------|------|--------|
| `default` | 기본 버튼 (Primary) | `bg-primary text-primary-foreground hover:bg-primary/90` |
| `destructive` | 삭제/위험 작업 버튼 | `bg-destructive text-destructive-foreground hover:bg-destructive/90` |
| `outline` | 아웃라인 버튼 | `border border-input bg-background hover:bg-accent hover:text-accent-foreground` |
| `secondary` | 보조 버튼 | `bg-secondary text-secondary-foreground hover:bg-secondary/80` |
| `ghost` | 투명 배경 버튼 | `hover:bg-accent hover:text-accent-foreground` |
| `link` | 링크 스타일 버튼 | `text-primary underline-offset-4 hover:underline` |

**중요:** 모든 스타일에 `dark:` 변형이 포함되어 있지 않습니다 (Light 모드 전용).

### 4.3. Size 스타일

Button 컴포넌트는 6가지 size를 지원합니다:

| Size | 설명 | 스타일 |
|------|------|--------|
| `default` | 기본 크기 | `h-10 px-4 py-2` |
| `sm` | 작은 크기 | `h-9 rounded-md px-3` |
| `lg` | 큰 크기 | `h-11 rounded-md px-8` |
| `icon` | 아이콘 전용 (기본) | `h-10 w-10` |
| `icon-sm` | 작은 아이콘 버튼 | `h-8 w-8` |
| `icon-lg` | 큰 아이콘 버튼 | `h-12 w-12` |

### 4.4. Icon 스타일

SVG 아이콘 크기를 자동으로 조정합니다:

```typescript
const iconStyles = {
	default: '[&>svg]:h-4 [&>svg]:w-4 [&>svg]:shrink-0',
	sm: '[&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:shrink-0',
	lg: '[&>svg]:h-5 [&>svg]:w-5 [&>svg]:shrink-0',
	icon: '[&>svg]:h-5 [&>svg]:w-5 [&>svg]:shrink-0',
	'icon-sm': '[&>svg]:h-4 [&>svg]:w-4 [&>svg]:shrink-0',
	'icon-lg': '[&>svg]:h-6 [&>svg]:w-6 [&>svg]:shrink-0'
} as const;
```

**설명:**
- `[&>svg]`: 버튼 직계 자식 SVG 선택
- `shrink-0`: SVG가 축소되지 않도록 설정

### 4.5. 조건부 렌더링 (Button vs Link)

```svelte
{#if href}
	<!-- <a> 태그로 렌더링 -->
	<a href={disabled ? undefined : href} ...>
		{@render children?.()}
	</a>
{:else}
	<!-- <button> 태그로 렌더링 -->
	<button disabled={disabled} ...>
		{@render children?.()}
	</button>
{/if}
```

**로직:**
- `href` prop이 있으면 → `<a>` 태그
- `href` prop이 없으면 → `<button>` 태그
- `disabled`일 때 링크는 `href={undefined}`, 버튼은 `disabled` 속성 사용

---

## 5. 사용 예제

### 5.1. 기본 버튼

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';
</script>

<Button>기본 버튼</Button>
<Button variant="destructive">삭제</Button>
<Button variant="outline">아웃라인</Button>
<Button variant="secondary">보조 버튼</Button>
<Button variant="ghost">고스트</Button>
<Button variant="link">링크 스타일</Button>
```

### 5.2. 크기 조절

```svelte
<Button size="sm">작은 버튼</Button>
<Button size="default">기본 크기</Button>
<Button size="lg">큰 버튼</Button>
```

### 5.3. 아이콘 버튼

```svelte
<Button size="icon">
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
		<path d="M5 12h14" />
		<path d="m12 5 7 7-7 7" />
	</svg>
</Button>

<Button size="icon-sm">
	<svg ...>...</svg>
</Button>

<Button size="icon-lg">
	<svg ...>...</svg>
</Button>
```

### 5.4. 링크로 사용

```svelte
<Button href="/about">소개 페이지로 이동</Button>
<Button href="/contact" variant="outline">연락처</Button>
```

### 5.5. 비활성화 상태

```svelte
<Button disabled>비활성화된 버튼</Button>
<Button href="/disabled" disabled>비활성화된 링크</Button>
```

### 5.6. 커스텀 클래스 추가

```svelte
<Button class="w-full">전체 너비 버튼</Button>
<Button class="shadow-lg">그림자 추가</Button>
```

### 5.7. 이벤트 핸들러

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';

	function handleClick() {
		console.log('버튼 클릭됨');
	}
</script>

<Button onclick={handleClick}>클릭하세요</Button>
```

---

## 6. Tailwind CSS 색상 토큰

Button 컴포넌트는 Tailwind CSS의 디자인 토큰을 사용합니다. `src/app.css`에 정의된 변수를 참조합니다.

### 6.1. 색상 변수

```css
@layer base {
	:root {
		--background: 0 0% 100%;
		--foreground: 222.2 84% 4.9%;

		--primary: 222.2 47.4% 11.2%;
		--primary-foreground: 210 40% 98%;

		--secondary: 210 40% 96.1%;
		--secondary-foreground: 222.2 47.4% 11.2%;

		--destructive: 0 84.2% 60.2%;
		--destructive-foreground: 210 40% 98%;

		--accent: 210 40% 96.1%;
		--accent-foreground: 222.2 47.4% 11.2%;

		--border: 214.3 31.8% 91.4%;
		--input: 214.3 31.8% 91.4%;
		--ring: 222.2 84% 4.9%;
	}
}
```

**중요:** Dark 모드 변수(`.dark { ... }`)는 정의하지 않습니다.

---

## 7. 접근성 (Accessibility)

### 7.1. 기본 접근성 지원

Button 컴포넌트는 다음과 같은 접근성 기능을 기본 제공합니다:

1. **키보드 포커스:**
   ```css
   focus-visible:outline-none
   focus-visible:ring-2
   focus-visible:ring-ring
   focus-visible:ring-offset-2
   ```

2. **Disabled 상태:**
   - `disabled={true}`: 버튼 비활성화
   - `aria-disabled="true"`: 스크린 리더에 비활성 상태 전달
   - `tabindex={-1}`: 키보드 탐색에서 제외

3. **적절한 시맨틱 태그:**
   - 클릭 동작: `<button>` 사용
   - 페이지 이동: `<a>` 사용

### 7.2. ARIA 속성 추가 예제

```svelte
<Button aria-label="메뉴 열기">
	<svg>...</svg>
</Button>

<Button aria-describedby="button-description">
	더 알아보기
</Button>
<span id="button-description" class="sr-only">
	이 버튼을 클릭하면 상세 정보 페이지로 이동합니다.
</span>
```

---

## 8. 최적화 및 Best Practices

### 8.1. cn 유틸리티 함수 사용

`cn()` 함수는 `clsx` + `tailwind-merge`를 결합하여 클래스 충돌을 방지합니다.

**예시:**
```typescript
cn('px-4 py-2', 'px-8') // 결과: "py-2 px-8" (px-4가 px-8로 덮어씌워짐)
```

### 8.2. 동적 클래스 사용 피하기

**❌ 잘못된 사용:**
```svelte
<script>
	let color = 'blue';
</script>
<Button class="bg-{color}-500">버튼</Button>
```

**✅ 올바른 사용:**
```svelte
<script>
	let color = 'blue';
	let colorClass = color === 'blue' ? 'bg-blue-500' : 'bg-red-500';
</script>
<Button class={colorClass}>버튼</Button>
```

### 8.3. Icon 컴포넌트와 함께 사용

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';
	import ChevronRight from '~icons/lucide/chevron-right'; // unplugin-icons 사용 시
</script>

<Button>
	다음
	<ChevronRight />
</Button>
```

---

## 9. 검증 방법

### 9.1. TypeScript 타입 체크

```bash
npm run check
```

**예상 출력:**
```
✓ No errors found
```

### 9.2. 시각적 테스트

테스트 페이지를 생성하여 모든 variant와 size를 확인합니다.

**파일 위치:** `src/routes/test/button/+page.svelte`

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';
</script>

<section class="p-8 space-y-8">
	<h1 class="text-2xl font-bold">Button 컴포넌트 테스트</h1>

	<!-- Variants -->
	<div class="space-y-4">
		<h2 class="text-xl font-semibold">Variants</h2>
		<div class="flex flex-wrap gap-2">
			<Button variant="default">Default</Button>
			<Button variant="destructive">Destructive</Button>
			<Button variant="outline">Outline</Button>
			<Button variant="secondary">Secondary</Button>
			<Button variant="ghost">Ghost</Button>
			<Button variant="link">Link</Button>
		</div>
	</div>

	<!-- Sizes -->
	<div class="space-y-4">
		<h2 class="text-xl font-semibold">Sizes</h2>
		<div class="flex flex-wrap items-center gap-2">
			<Button size="sm">Small</Button>
			<Button size="default">Default</Button>
			<Button size="lg">Large</Button>
		</div>
	</div>

	<!-- Icon Buttons -->
	<div class="space-y-4">
		<h2 class="text-xl font-semibold">Icon Buttons</h2>
		<div class="flex flex-wrap gap-2">
			<Button size="icon-sm">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M5 12h14" />
					<path d="m12 5 7 7-7 7" />
				</svg>
			</Button>
			<Button size="icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M5 12h14" />
					<path d="m12 5 7 7-7 7" />
				</svg>
			</Button>
			<Button size="icon-lg">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M5 12h14" />
					<path d="m12 5 7 7-7 7" />
				</svg>
			</Button>
		</div>
	</div>

	<!-- Disabled State -->
	<div class="space-y-4">
		<h2 class="text-xl font-semibold">Disabled State</h2>
		<div class="flex flex-wrap gap-2">
			<Button disabled>Disabled Button</Button>
			<Button href="/test" disabled>Disabled Link</Button>
		</div>
	</div>
</section>
```

---

## 10. 트러블슈팅

### 10.1. 스타일이 적용되지 않음

**원인:** Tailwind CSS가 올바르게 설정되지 않음

**해결:**
1. `src/app.css`에 `@import "tailwindcss";` 포함 확인
2. `src/routes/+layout.svelte`에서 `import '../app.css';` 확인
3. `npm run dev` 재시작

### 10.2. cn 함수 오류

**원인:** `clsx` 또는 `tailwind-merge` 패키지 미설치

**해결:**
```bash
npm install -D clsx tailwind-merge
```

### 10.3. TypeScript 타입 에러

**원인:** Svelte 5 타입 정의 충돌

**해결:**
```bash
npm run check
```

에러 메시지를 확인하고 타입 정의를 수정합니다.

---

## 11. 참고 자료

### 11.1. 관련 문서
- `sonub-setup-shadcn.md`: shadcn-svelte 설치 배경 및 수동 구현 이유
- `sonub-setup-tailwind.md`: Tailwind CSS Light 모드 설정
- `sonub-design-components.md`: UI 컴포넌트 전체 개요

### 11.2. 외부 참조
- **shadcn-svelte Button**: https://www.shadcn-svelte.com/docs/components/button
- **Tailwind CSS 유틸리티**: https://tailwindcss.com/docs
- **Svelte 5 Snippet**: https://svelte.dev/docs/svelte/snippet

---

## 12. 변경 이력

| 버전 | 날짜 | 작성자 | 변경 내용 |
|------|------|---------|-----------|
| 1.0.0 | 2025-01-09 | JaeHo Song | 초기 Button 컴포넌트 명세서 작성 |

**v1.0.0 (2025-01-09):**
- Button 컴포넌트 구현 및 문서화
- shadcn-svelte 대신 직접 구현한 이유 설명
- Light 모드 전용 설정 배경 추가
- 6가지 variant 및 6가지 size 지원
- TypeScript 타입 안전성 확보
- Svelte 5 runes 완전 호환
- 사용 예제 및 접근성 가이드 추가
