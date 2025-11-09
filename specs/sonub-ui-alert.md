---
name: sonub-ui-alert
version: 1.0.0
description: Sonub 프로젝트 Alert UI 컴포넌트 명세서 (Light 모드 전용)
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 32
priority: "**"
dependencies: ["sonub-setup-shadcn.md", "sonub-setup-tailwind.md", "sonub-ui-button.md"]
tags: ["ui", "alert", "component", "notification", "light-mode", "svelte-5"]
---

# Sonub Alert UI 컴포넌트 명세서

## 1. 개요

### 1.1. 목적
본 명세서는 Sonub 프로젝트에서 사용하는 Alert UI 컴포넌트의 구현 및 사용법을 정의합니다. Alert는 사용자에게 중요한 메시지나 정보를 표시하는 알림 컴포넌트입니다.

### 1.2. 범위
- Alert 컴포넌트 및 서브 컴포넌트 구조
- variant 옵션 (default, destructive)
- Light 모드 전용 구현
- 사용 예제 및 패턴

### 1.3. Alert 컴포넌트 구성

Alert는 3개의 서브 컴포넌트로 구성됩니다:

1. **Alert (Root)**: 알림의 루트 컨테이너
2. **AlertTitle**: 알림 제목
3. **AlertDescription**: 알림 상세 설명

**구조화된 레이아웃:**
```
<Alert>                                <!-- 루트 컨테이너 -->
  <AlertTitle>제목</AlertTitle>
  <AlertDescription>설명</AlertDescription>
</Alert>
```

---

## 2. 컴포넌트 구조

### 2.1. 파일 위치

```
src/lib/components/ui/alert/
├── alert.svelte               # Alert 루트 컴포넌트
├── alert-title.svelte         # AlertTitle 컴포넌트
├── alert-description.svelte   # AlertDescription 컴포넌트
└── index.ts                   # Export 파일
```

### 2.2. 컴포넌트별 역할

| 컴포넌트 | 역할 | HTML 태그 | ARIA 속성 |
|----------|------|-----------|-----------|
| Alert | 루트 컨테이너, 테두리 및 배경 | `<div>` | `role="alert"` |
| AlertTitle | 알림 제목 | `<h5>` | - |
| AlertDescription | 알림 상세 설명 | `<div>` | - |

---

## 3. 구현 코드

### 3.1. Alert (Root) 컴포넌트

**파일 경로:** `src/lib/components/ui/alert/alert.svelte`

```svelte
<script lang="ts">
	/**
	 * Alert Root 컴포넌트
	 *
	 * 사용자에게 중요한 메시지를 표시하는 알림 컴포넌트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLDivElement> {
		variant?: 'default' | 'destructive';
		class?: string;
		children?: Snippet;
	}

	let { variant = 'default', class: className, children, ...restProps }: Props = $props();

	/**
	 * Alert variant별 스타일
	 */
	const variantStyles = {
		default: 'bg-background text-foreground',
		destructive: 'border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive'
	};
</script>

<div
	class={cn(
		'relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground',
		variantStyles[variant],
		className
	)}
	role="alert"
	{...restProps}
>
	{@render children?.()}
</div>
```

**스타일 설명:**

**공통 스타일:**
- `relative`: 상대 위치 (아이콘 절대 위치 기준)
- `w-full`: 전체 너비
- `rounded-lg`: 둥근 모서리 (8px)
- `border`: 테두리
- `p-4`: 패딩 16px

**아이콘 관련 스타일:**
- `[&>svg~*]:pl-7`: SVG 다음 형제 요소에 좌측 패딩 28px
- `[&>svg+div]:translate-y-[-3px]`: SVG 다음 div를 위로 3px 이동
- `[&>svg]:absolute`: SVG를 절대 위치로 배치
- `[&>svg]:left-4`: SVG 좌측 16px
- `[&>svg]:top-4`: SVG 상단 16px
- `[&>svg]:text-foreground`: SVG 색상

**⚠️ Dark Mode 클래스 발견:**

현재 코드에 `dark:border-destructive` 클래스가 포함되어 있습니다. Light 모드 전용 프로젝트에서는 이 클래스가 필요하지 않으며, 향후 제거할 수 있습니다.

**권장 수정:**
```typescript
const variantStyles = {
	default: 'bg-background text-foreground',
	// ✅ dark: 클래스 제거
	destructive: 'border-destructive/50 text-destructive [&>svg]:text-destructive'
};
```

### 3.2. AlertTitle 컴포넌트

**파일 경로:** `src/lib/components/ui/alert/alert-title.svelte`

```svelte
<script lang="ts">
	/**
	 * Alert Title 컴포넌트
	 *
	 * 알림의 제목입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLHeadingElement> {
		class?: string;
		children?: Snippet;
	}

	let { class: className, children, ...restProps }: Props = $props();
</script>

<h5 class={cn('mb-1 font-medium leading-none tracking-tight', className)} {...restProps}>
	{@render children?.()}
</h5>
```

**스타일 설명:**
- `mb-1`: 하단 마진 4px
- `font-medium`: 폰트 굵기 500
- `leading-none`: 줄 높이 1
- `tracking-tight`: 글자 간격 -0.025em

### 3.3. AlertDescription 컴포넌트

**파일 경로:** `src/lib/components/ui/alert/alert-description.svelte`

```svelte
<script lang="ts">
	/**
	 * Alert Description 컴포넌트
	 *
	 * 알림의 설명 텍스트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLDivElement> {
		class?: string;
		children?: Snippet;
	}

	let { class: className, children, ...restProps }: Props = $props();
</script>

<div class={cn('text-sm [&_p]:leading-relaxed', className)} {...restProps}>
	{@render children?.()}
</div>
```

**스타일 설명:**
- `text-sm`: 폰트 크기 14px
- `[&_p]:leading-relaxed`: 자손 `<p>` 태그의 줄 높이 1.625

### 3.4. Export 파일 (index.ts)

**파일 경로:** `src/lib/components/ui/alert/index.ts`

```typescript
/**
 * Alert 컴포넌트 export
 */

import Root from './alert.svelte';
import Title from './alert-title.svelte';
import Description from './alert-description.svelte';

export {
	Root,
	Title,
	Description,
	//
	Root as Alert,
	Title as AlertTitle,
	Description as AlertDescription
};
```

---

## 4. Variant 스타일

Alert 컴포넌트는 2가지 variant를 지원합니다:

| Variant | 설명 | 스타일 | 용도 |
|---------|------|--------|------|
| `default` | 기본 알림 | `bg-background text-foreground` | 일반 정보 메시지 |
| `destructive` | 에러/경고 알림 | `border-destructive/50 text-destructive` | 에러, 경고, 삭제 확인 등 |

### 4.1. Default Variant

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root>
	<Alert.Title>알림</Alert.Title>
	<Alert.Description>
		일반 정보 메시지입니다.
	</Alert.Description>
</Alert.Root>
```

### 4.2. Destructive Variant

```svelte
<Alert.Root variant="destructive">
	<Alert.Title>오류 발생</Alert.Title>
	<Alert.Description>
		요청을 처리할 수 없습니다. 다시 시도해 주세요.
	</Alert.Description>
</Alert.Root>
```

---

## 5. 사용 예제

### 5.1. 기본 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root>
	<Alert.Title>정보</Alert.Title>
	<Alert.Description>
		이것은 기본 알림 메시지입니다.
	</Alert.Description>
</Alert.Root>
```

### 5.2. 아이콘이 있는 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root>
	<!-- 아이콘 -->
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width="16"
		height="16"
		viewBox="0 0 24 24"
		fill="none"
		stroke="currentColor"
		stroke-width="2"
		stroke-linecap="round"
		stroke-linejoin="round"
	>
		<circle cx="12" cy="12" r="10" />
		<path d="M12 16v-4" />
		<path d="M12 8h.01" />
	</svg>

	<Alert.Title>주의</Alert.Title>
	<Alert.Description>
		이 작업은 되돌릴 수 없습니다.
	</Alert.Description>
</Alert.Root>
```

**아이콘 위치:**
- SVG는 Alert의 첫 번째 자식으로 배치
- 자동으로 좌측 상단에 절대 위치로 배치됨
- 나머지 콘텐츠는 좌측 패딩으로 아이콘 공간 확보

### 5.3. 에러 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root variant="destructive">
	<!-- 에러 아이콘 -->
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width="16"
		height="16"
		viewBox="0 0 24 24"
		fill="none"
		stroke="currentColor"
		stroke-width="2"
	>
		<circle cx="12" cy="12" r="10" />
		<path d="m15 9-6 6" />
		<path d="m9 9 6 6" />
	</svg>

	<Alert.Title>오류</Alert.Title>
	<Alert.Description>
		세션이 만료되었습니다. 다시 로그인해 주세요.
	</Alert.Description>
</Alert.Root>
```

### 5.4. 성공 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root class="border-green-500 bg-green-50 text-green-900">
	<!-- 체크 아이콘 -->
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width="16"
		height="16"
		viewBox="0 0 24 24"
		fill="none"
		stroke="currentColor"
		stroke-width="2"
		class="text-green-600"
	>
		<circle cx="12" cy="12" r="10" />
		<path d="m9 12 2 2 4-4" />
	</svg>

	<Alert.Title>성공</Alert.Title>
	<Alert.Description>
		프로필이 성공적으로 업데이트되었습니다.
	</Alert.Description>
</Alert.Root>
```

### 5.5. 경고 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root class="border-yellow-500 bg-yellow-50 text-yellow-900">
	<!-- 경고 아이콘 -->
	<svg
		xmlns="http://www.w3.org/2000/svg"
		width="16"
		height="16"
		viewBox="0 0 24 24"
		fill="none"
		stroke="currentColor"
		stroke-width="2"
		class="text-yellow-600"
	>
		<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
		<path d="M12 9v4" />
		<path d="M12 17h.01" />
	</svg>

	<Alert.Title>경고</Alert.Title>
	<Alert.Description>
		이 기능은 베타 버전입니다. 예상치 못한 동작이 발생할 수 있습니다.
	</Alert.Description>
</Alert.Root>
```

### 5.6. 링크가 있는 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<Alert.Root>
	<Alert.Title>업데이트 가능</Alert.Title>
	<Alert.Description>
		새로운 버전이 출시되었습니다.
		<a href="/updates" class="font-medium underline underline-offset-4 hover:text-primary">
			업데이트 페이지로 이동
		</a>
	</Alert.Description>
</Alert.Root>
```

### 5.7. 버튼이 있는 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
	import { Button } from '$lib/components/ui/button';
</script>

<Alert.Root variant="destructive">
	<Alert.Title>계정 삭제</Alert.Title>
	<Alert.Description class="space-y-3">
		<p>계정을 삭제하면 모든 데이터가 영구적으로 삭제됩니다.</p>
		<div class="flex gap-2">
			<Button variant="destructive" size="sm">삭제</Button>
			<Button variant="outline" size="sm">취소</Button>
		</div>
	</Alert.Description>
</Alert.Root>
```

### 5.8. Title 없이 사용

```svelte
<Alert.Root>
	<Alert.Description>
		간단한 메시지는 제목 없이 표시할 수 있습니다.
	</Alert.Description>
</Alert.Root>
```

---

## 6. 동적 알림 표시

### 6.1. 조건부 표시

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
	import { Button } from '$lib/components/ui/button';

	let showAlert = $state(false);
	let message = $state('');

	function showError(msg: string) {
		message = msg;
		showAlert = true;
	}

	function hideAlert() {
		showAlert = false;
	}
</script>

{#if showAlert}
	<Alert.Root variant="destructive">
		<Alert.Title>오류</Alert.Title>
		<Alert.Description>{message}</Alert.Description>
		<Button variant="ghost" size="sm" onclick={hideAlert}>닫기</Button>
	</Alert.Root>
{/if}

<Button onclick={() => showError('파일 업로드에 실패했습니다.')}>
	에러 표시
</Button>
```

### 6.2. 자동 사라짐

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
	import { onMount } from 'svelte';

	let visible = $state(true);

	onMount(() => {
		// 5초 후 자동으로 사라짐
		setTimeout(() => {
			visible = false;
		}, 5000);
	});
</script>

{#if visible}
	<Alert.Root>
		<Alert.Title>알림</Alert.Title>
		<Alert.Description>
			이 메시지는 5초 후 자동으로 사라집니다.
		</Alert.Description>
	</Alert.Root>
{/if}
```

### 6.3. 애니메이션 효과

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
	import { fade, slide } from 'svelte/transition';

	let visible = $state(true);
</script>

{#if visible}
	<div transition:slide>
		<Alert.Root>
			<Alert.Title>알림</Alert.Title>
			<Alert.Description>
				슬라이드 애니메이션과 함께 표시됩니다.
			</Alert.Description>
		</Alert.Root>
	</div>
{/if}
```

---

## 7. 스타일 커스터마이징

### 7.1. 색상 변경

```svelte
<!-- 파란색 알림 -->
<Alert.Root class="border-blue-500 bg-blue-50 text-blue-900">
	<Alert.Title>정보</Alert.Title>
	<Alert.Description>파란색 알림입니다.</Alert.Description>
</Alert.Root>

<!-- 보라색 알림 -->
<Alert.Root class="border-purple-500 bg-purple-50 text-purple-900">
	<Alert.Title>새 기능</Alert.Title>
	<Alert.Description>보라색 알림입니다.</Alert.Description>
</Alert.Root>
```

### 7.2. 너비 제한

```svelte
<Alert.Root class="max-w-md">
	<Alert.Title>제한된 너비</Alert.Title>
	<Alert.Description>
		이 알림은 최대 너비가 제한되어 있습니다.
	</Alert.Description>
</Alert.Root>
```

### 7.3. 그림자 효과

```svelte
<Alert.Root class="shadow-lg">
	<Alert.Title>강조된 알림</Alert.Title>
	<Alert.Description>
		그림자 효과로 강조되었습니다.
	</Alert.Description>
</Alert.Root>
```

---

## 8. 접근성 (Accessibility)

### 8.1. ARIA 속성

Alert 컴포넌트는 `role="alert"` 속성을 자동으로 포함합니다.

```html
<div role="alert" ...>
  ...
</div>
```

**효과:**
- 스크린 리더가 알림을 즉시 사용자에게 공지
- 키보드 탐색 시 알림 영역 인식

### 8.2. 시맨틱 HTML

- **AlertTitle**: `<h5>` 태그 사용 (제목 구조)
- **AlertDescription**: `<div>` 태그 사용

### 8.3. 키보드 접근성

Alert 자체는 키보드 포커스를 받지 않지만, 내부 버튼이나 링크는 키보드로 접근 가능합니다.

```svelte
<Alert.Root variant="destructive">
	<Alert.Title>확인 필요</Alert.Title>
	<Alert.Description>
		<Button tabindex="0">확인</Button>
		<Button tabindex="0" variant="outline">취소</Button>
	</Alert.Description>
</Alert.Root>
```

---

## 9. 레이아웃 패턴

### 9.1. 상단 고정 알림

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md">
	<Alert.Root>
		<Alert.Title>공지사항</Alert.Title>
		<Alert.Description>
			시스템 점검이 예정되어 있습니다.
		</Alert.Description>
	</Alert.Root>
</div>
```

### 9.2. 폼 검증 에러 표시

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
	import { Button } from '$lib/components/ui/button';

	let errors = $state<string[]>([]);

	function validateForm() {
		errors = [];
		// 검증 로직
		if (!email) errors.push('이메일을 입력하세요.');
		if (!password) errors.push('비밀번호를 입력하세요.');
	}
</script>

{#if errors.length > 0}
	<Alert.Root variant="destructive">
		<Alert.Title>입력 오류</Alert.Title>
		<Alert.Description>
			<ul class="list-disc list-inside space-y-1">
				{#each errors as error}
					<li>{error}</li>
				{/each}
			</ul>
		</Alert.Description>
	</Alert.Root>
{/if}
```

### 9.3. 여러 알림 스택

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';

	const alerts = [
		{ variant: 'default', title: '정보', desc: '새 메시지가 있습니다.' },
		{ variant: 'destructive', title: '경고', desc: '디스크 공간이 부족합니다.' }
	];
</script>

<div class="space-y-4">
	{#each alerts as alert}
		<Alert.Root variant={alert.variant}>
			<Alert.Title>{alert.title}</Alert.Title>
			<Alert.Description>{alert.desc}</Alert.Description>
		</Alert.Root>
	{/each}
</div>
```

---

## 10. Tailwind CSS 색상 토큰

Alert 컴포넌트는 다음과 같은 Tailwind CSS 디자인 토큰을 사용합니다.

### 10.1. 색상 변수

```css
@layer base {
	:root {
		--background: 0 0% 100%;
		--foreground: 222.2 84% 4.9%;
		--destructive: 0 84.2% 60.2%;
		--destructive-foreground: 210 40% 98%;
		--border: 214.3 31.8% 91.4%;
	}
}
```

**사용 예:**
- `bg-background`: 기본 배경색
- `text-foreground`: 기본 텍스트 색상
- `text-destructive`: 에러 텍스트 색상
- `border-destructive/50`: 에러 테두리 (50% 투명도)

---

## 11. 검증 방법

### 11.1. TypeScript 타입 체크

```bash
npm run check
```

**예상 출력:**
```
✓ No errors found
```

### 11.2. 시각적 테스트

**파일 위치:** `src/routes/test/alert/+page.svelte`

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert';
</script>

<section class="p-8 space-y-8 bg-gray-50 min-h-screen">
	<h1 class="text-3xl font-bold">Alert 컴포넌트 테스트</h1>

	<!-- Default Variant -->
	<div>
		<h2 class="text-xl font-semibold mb-4">Default Variant</h2>
		<Alert.Root>
			<Alert.Title>기본 알림</Alert.Title>
			<Alert.Description>
				이것은 기본 알림 메시지입니다.
			</Alert.Description>
		</Alert.Root>
	</div>

	<!-- Destructive Variant -->
	<div>
		<h2 class="text-xl font-semibold mb-4">Destructive Variant</h2>
		<Alert.Root variant="destructive">
			<Alert.Title>오류 발생</Alert.Title>
			<Alert.Description>
				요청을 처리할 수 없습니다.
			</Alert.Description>
		</Alert.Root>
	</div>

	<!-- 아이콘 포함 -->
	<div>
		<h2 class="text-xl font-semibold mb-4">아이콘 포함</h2>
		<Alert.Root>
			<svg
				xmlns="http://www.w3.org/2000/svg"
				width="16"
				height="16"
				viewBox="0 0 24 24"
				fill="none"
				stroke="currentColor"
				stroke-width="2"
			>
				<circle cx="12" cy="12" r="10" />
				<path d="M12 16v-4" />
				<path d="M12 8h.01" />
			</svg>
			<Alert.Title>주의</Alert.Title>
			<Alert.Description>
				이 작업은 되돌릴 수 없습니다.
			</Alert.Description>
		</Alert.Root>
	</div>

	<!-- 커스텀 색상 -->
	<div>
		<h2 class="text-xl font-semibold mb-4">커스텀 색상 (성공)</h2>
		<Alert.Root class="border-green-500 bg-green-50 text-green-900">
			<Alert.Title>성공</Alert.Title>
			<Alert.Description>
				작업이 성공적으로 완료되었습니다.
			</Alert.Description>
		</Alert.Root>
	</div>
</section>
```

---

## 12. 트러블슈팅

### 12.1. 아이콘이 올바르게 배치되지 않음

**원인:** SVG가 Alert의 첫 번째 자식이 아님

**해결:**
```svelte
<!-- ✅ 올바른 사용 -->
<Alert.Root>
	<svg>...</svg>  <!-- 첫 번째 자식 -->
	<Alert.Title>제목</Alert.Title>
	<Alert.Description>설명</Alert.Description>
</Alert.Root>

<!-- ❌ 잘못된 사용 -->
<Alert.Root>
	<Alert.Title>제목</Alert.Title>
	<svg>...</svg>  <!-- 첫 번째 자식이 아님 -->
	<Alert.Description>설명</Alert.Description>
</Alert.Root>
```

### 12.2. Dark Mode 클래스 제거

**현재 문제:** `destructive` variant에 `dark:border-destructive` 클래스가 포함됨

**해결:**

**파일 경로:** `src/lib/components/ui/alert/alert.svelte`

**수정 전:**
```typescript
const variantStyles = {
	default: 'bg-background text-foreground',
	destructive: 'border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive'
};
```

**수정 후:**
```typescript
const variantStyles = {
	default: 'bg-background text-foreground',
	destructive: 'border-destructive/50 text-destructive [&>svg]:text-destructive'
};
```

### 12.3. 전체 너비 문제

**원인:** Alert는 기본적으로 `w-full`이므로 부모 컨테이너의 전체 너비를 차지함

**해결:**
```svelte
<!-- 너비 제한 -->
<Alert.Root class="max-w-md">
	...
</Alert.Root>

<!-- 고정 너비 -->
<Alert.Root class="w-[400px]">
	...
</Alert.Root>
```

---

## 13. 참고 자료

### 13.1. 관련 문서
- `sonub-ui-button.md`: Button 컴포넌트 명세서
- `sonub-ui-card.md`: Card 컴포넌트 명세서
- `sonub-setup-shadcn.md`: shadcn-svelte 배경 및 수동 구현 이유
- `sonub-setup-tailwind.md`: Tailwind CSS Light 모드 설정
- `sonub-design-components.md`: UI 컴포넌트 전체 개요

### 13.2. 외부 참조
- **shadcn-svelte Alert**: https://www.shadcn-svelte.com/docs/components/alert
- **WAI-ARIA Alert Role**: https://www.w3.org/WAI/ARIA/apg/patterns/alert/
- **Tailwind CSS**: https://tailwindcss.com/docs

---

## 14. 변경 이력

| 버전 | 날짜 | 작성자 | 변경 내용 |
|------|------|---------|-----------|
| 1.0.0 | 2025-01-09 | JaeHo Song | 초기 Alert 컴포넌트 명세서 작성 |

**v1.0.0 (2025-01-09):**
- Alert 컴포넌트 및 3개 서브 컴포넌트 구현 문서화
- 2가지 variant (default, destructive) 지원
- Light 모드 전용 구현 (dark: 클래스 제거 필요 명시)
- 다양한 사용 예제 및 레이아웃 패턴 추가
- 접근성 (ARIA role="alert") 지원 명시
- 아이콘 위치 자동 조정 기능 설명
- 동적 알림 표시 및 애니메이션 예제 추가
- 트러블슈팅 가이드 추가
