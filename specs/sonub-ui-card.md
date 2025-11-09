---
name: sonub-ui-card
version: 1.0.0
description: Sonub 프로젝트 Card UI 컴포넌트 명세서 (Light 모드 전용)
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 31
priority: "**"
dependencies: ["sonub-setup-shadcn.md", "sonub-setup-tailwind.md", "sonub-ui-button.md"]
tags: ["ui", "card", "component", "light-mode", "svelte-5"]
---

# Sonub Card UI 컴포넌트 명세서

## 1. 개요

### 1.1. 목적
본 명세서는 Sonub 프로젝트에서 사용하는 Card UI 컴포넌트의 구현 및 사용법을 정의합니다. Card는 콘텐츠를 시각적으로 그룹화하고 구조화하는 컨테이너 컴포넌트입니다.

### 1.2. 범위
- Card 컴포넌트 및 서브 컴포넌트 구조
- 각 서브 컴포넌트 역할 및 스타일
- Light 모드 전용 구현
- 사용 예제 및 패턴

### 1.3. Card 컴포넌트 구성

Card는 6개의 서브 컴포넌트로 구성됩니다:

1. **Card (Root)**: 카드의 루트 컨테이너
2. **CardHeader**: 카드 상단 헤더 영역
3. **CardTitle**: 카드 제목
4. **CardDescription**: 카드 설명 텍스트
5. **CardContent**: 카드 메인 콘텐츠 영역
6. **CardFooter**: 카드 하단 푸터 영역

**구조화된 레이아웃:**
```
<Card>                          <!-- 루트 컨테이너 -->
  <CardHeader>                  <!-- 헤더 -->
    <CardTitle>제목</CardTitle>
    <CardDescription>설명</CardDescription>
  </CardHeader>
  <CardContent>                 <!-- 본문 -->
    콘텐츠
  </CardContent>
  <CardFooter>                  <!-- 푸터 -->
    액션 버튼 등
  </CardFooter>
</Card>
```

---

## 2. 컴포넌트 구조

### 2.1. 파일 위치

```
src/lib/components/ui/card/
├── card.svelte                # Card 루트 컴포넌트
├── card-header.svelte         # CardHeader 컴포넌트
├── card-title.svelte          # CardTitle 컴포넌트
├── card-description.svelte    # CardDescription 컴포넌트
├── card-content.svelte        # CardContent 컴포넌트
├── card-footer.svelte         # CardFooter 컴포넌트
└── index.ts                   # Export 파일
```

### 2.2. 컴포넌트별 역할

| 컴포넌트 | 역할 | HTML 태그 |
|----------|------|-----------|
| Card | 루트 컨테이너, 테두리 및 그림자 | `<div>` |
| CardHeader | 헤더 영역 (Title + Description 포함) | `<div>` |
| CardTitle | 카드 제목 | `<h3>` |
| CardDescription | 카드 설명 | `<p>` |
| CardContent | 메인 콘텐츠 영역 | `<div>` |
| CardFooter | 푸터 영역 (버튼 등) | `<div>` |

---

## 3. 구현 코드

### 3.1. Card (Root) 컴포넌트

**파일 경로:** `src/lib/components/ui/card/card.svelte`

```svelte
<script lang="ts">
	/**
	 * Card Root 컴포넌트
	 *
	 * 카드의 루트 컨테이너입니다.
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

<div
	class={cn('rounded-lg border bg-card text-card-foreground shadow-sm', className)}
	{...restProps}
>
	{@render children?.()}
</div>
```

**스타일 설명:**
- `rounded-lg`: 둥근 모서리 (8px)
- `border`: 테두리 (기본 색상)
- `bg-card`: 카드 배경색
- `text-card-foreground`: 카드 텍스트 색상
- `shadow-sm`: 작은 그림자 효과

### 3.2. CardHeader 컴포넌트

**파일 경로:** `src/lib/components/ui/card/card-header.svelte`

```svelte
<script lang="ts">
	/**
	 * Card Header 컴포넌트
	 *
	 * 카드의 헤더 영역입니다.
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

<div class={cn('flex flex-col space-y-1.5 p-6', className)} {...restProps}>
	{@render children?.()}
</div>
```

**스타일 설명:**
- `flex flex-col`: 세로 방향 Flexbox
- `space-y-1.5`: 자식 요소 간 수직 간격 (6px)
- `p-6`: 패딩 24px

### 3.3. CardTitle 컴포넌트

**파일 경로:** `src/lib/components/ui/card/card-title.svelte`

```svelte
<script lang="ts">
	/**
	 * Card Title 컴포넌트
	 *
	 * 카드의 제목입니다.
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

<h3 class={cn('text-2xl font-semibold leading-none tracking-tight', className)} {...restProps}>
	{@render children?.()}
</h3>
```

**스타일 설명:**
- `text-2xl`: 폰트 크기 24px
- `font-semibold`: 폰트 굵기 600
- `leading-none`: 줄 높이 1
- `tracking-tight`: 글자 간격 -0.025em

### 3.4. CardDescription 컴포넌트

**파일 경로:** `src/lib/components/ui/card/card-description.svelte`

```svelte
<script lang="ts">
	/**
	 * Card Description 컴포넌트
	 *
	 * 카드의 설명 텍스트입니다.
	 */

	import { cn } from '$lib/utils.js';
	import type { HTMLAttributes } from 'svelte/elements';
	import type { Snippet } from 'svelte';

	interface Props extends HTMLAttributes<HTMLParagraphElement> {
		class?: string;
		children?: Snippet;
	}

	let { class: className, children, ...restProps }: Props = $props();
</script>

<p class={cn('text-sm text-muted-foreground', className)} {...restProps}>
	{@render children?.()}
</p>
```

**스타일 설명:**
- `text-sm`: 폰트 크기 14px
- `text-muted-foreground`: 흐린 텍스트 색상 (보조 정보용)

### 3.5. CardContent 컴포넌트

**파일 경로:** `src/lib/components/ui/card/card-content.svelte`

```svelte
<script lang="ts">
	/**
	 * Card Content 컴포넌트
	 *
	 * 카드의 메인 콘텐츠 영역입니다.
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

<div class={cn('p-6 pt-0', className)} {...restProps}>
	{@render children?.()}
</div>
```

**스타일 설명:**
- `p-6`: 패딩 24px (좌우/하단)
- `pt-0`: 상단 패딩 0 (Header와 간격 제거)

### 3.6. CardFooter 컴포넌트

**파일 경로:** `src/lib/components/ui/card/card-footer.svelte`

```svelte
<script lang="ts">
	/**
	 * Card Footer 컴포넌트
	 *
	 * 카드의 푸터 영역입니다.
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

<div class={cn('flex items-center p-6 pt-0', className)} {...restProps}>
	{@render children?.()}
</div>
```

**스타일 설명:**
- `flex items-center`: 가로 Flexbox, 세로 중앙 정렬
- `p-6 pt-0`: 패딩 24px (좌우/하단), 상단 0

### 3.7. Export 파일 (index.ts)

**파일 경로:** `src/lib/components/ui/card/index.ts`

```typescript
/**
 * Card 컴포넌트 export
 */

import Root from './card.svelte';
import Header from './card-header.svelte';
import Title from './card-title.svelte';
import Description from './card-description.svelte';
import Content from './card-content.svelte';
import Footer from './card-footer.svelte';

export {
	Root,
	Header,
	Title,
	Description,
	Content,
	Footer,
	//
	Root as Card,
	Header as CardHeader,
	Title as CardTitle,
	Description as CardDescription,
	Content as CardContent,
	Footer as CardFooter
};
```

**사용 가능한 import 방식:**

```svelte
<!-- 방식 1: Named export -->
import * as Card from '$lib/components/ui/card';

<Card.Root>
  <Card.Header>
    <Card.Title>제목</Card.Title>
  </Card.Header>
</Card.Root>

<!-- 방식 2: 개별 import -->
import { Card, CardHeader, CardTitle } from '$lib/components/ui/card';

<Card>
  <CardHeader>
    <CardTitle>제목</CardTitle>
  </CardHeader>
</Card>
```

---

## 4. 사용 예제

### 4.1. 기본 Card

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
</script>

<Card.Root>
	<Card.Header>
		<Card.Title>카드 제목</Card.Title>
		<Card.Description>카드 설명이 여기에 표시됩니다.</Card.Description>
	</Card.Header>
	<Card.Content>
		<p class="text-sm text-gray-700">
			카드 본문 내용입니다. 여기에 다양한 콘텐츠를 추가할 수 있습니다.
		</p>
	</Card.Content>
	<Card.Footer>
		<p class="text-xs text-gray-500">푸터 정보</p>
	</Card.Footer>
</Card.Root>
```

### 4.2. 버튼이 있는 Card

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
</script>

<Card.Root class="w-[350px]">
	<Card.Header>
		<Card.Title>프로필 수정</Card.Title>
		<Card.Description>프로필 정보를 변경할 수 있습니다.</Card.Description>
	</Card.Header>
	<Card.Content>
		<form class="space-y-4">
			<div>
				<label for="name" class="text-sm font-medium">이름</label>
				<input
					id="name"
					type="text"
					class="w-full rounded-md border p-2"
					placeholder="홍길동"
				/>
			</div>
			<div>
				<label for="email" class="text-sm font-medium">이메일</label>
				<input
					id="email"
					type="email"
					class="w-full rounded-md border p-2"
					placeholder="email@example.com"
				/>
			</div>
		</form>
	</Card.Content>
	<Card.Footer class="flex justify-between">
		<Button variant="outline">취소</Button>
		<Button>저장</Button>
	</Card.Footer>
</Card.Root>
```

### 4.3. 이미지가 있는 Card

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
</script>

<Card.Root class="w-[350px] overflow-hidden">
	<!-- 이미지 -->
	<img
		src="/images/product.jpg"
		alt="제품 이미지"
		class="h-48 w-full object-cover"
	/>

	<Card.Header>
		<Card.Title>제품 이름</Card.Title>
		<Card.Description>제품 설명이 여기에 표시됩니다.</Card.Description>
	</Card.Header>
	<Card.Content>
		<p class="text-2xl font-bold text-gray-900">₩29,900</p>
	</Card.Content>
	<Card.Footer>
		<Button class="w-full">구매하기</Button>
	</Card.Footer>
</Card.Root>
```

### 4.4. Grid 레이아웃에서 Card 사용

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';

	const items = [
		{ title: '카드 1', description: '설명 1', content: '내용 1' },
		{ title: '카드 2', description: '설명 2', content: '내용 2' },
		{ title: '카드 3', description: '설명 3', content: '내용 3' },
		{ title: '카드 4', description: '설명 4', content: '내용 4' }
	];
</script>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
	{#each items as item}
		<Card.Root>
			<Card.Header>
				<Card.Title>{item.title}</Card.Title>
				<Card.Description>{item.description}</Card.Description>
			</Card.Header>
			<Card.Content>
				<p>{item.content}</p>
			</Card.Content>
		</Card.Root>
	{/each}
</div>
```

### 4.5. Hover 효과 추가

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
</script>

<Card.Root class="transition-shadow hover:shadow-lg cursor-pointer">
	<Card.Header>
		<Card.Title>Hover me!</Card.Title>
		<Card.Description>마우스를 올리면 그림자가 커집니다.</Card.Description>
	</Card.Header>
	<Card.Content>
		<p>카드에 인터랙션을 추가할 수 있습니다.</p>
	</Card.Content>
</Card.Root>
```

### 4.6. 중첩된 Card (Card 안의 Card)

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
</script>

<Card.Root>
	<Card.Header>
		<Card.Title>메인 카드</Card.Title>
		<Card.Description>카드 안에 다른 카드를 중첩할 수 있습니다.</Card.Description>
	</Card.Header>
	<Card.Content class="space-y-4">
		<!-- 중첩된 Card 1 -->
		<Card.Root class="border-gray-200">
			<Card.Header>
				<Card.Title class="text-lg">서브 카드 1</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="text-sm">중첩된 카드 내용</p>
			</Card.Content>
		</Card.Root>

		<!-- 중첩된 Card 2 -->
		<Card.Root class="border-gray-200">
			<Card.Header>
				<Card.Title class="text-lg">서브 카드 2</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="text-sm">중첩된 카드 내용</p>
			</Card.Content>
		</Card.Root>
	</Card.Content>
</Card.Root>
```

### 4.7. Header 또는 Footer 생략

모든 서브 컴포넌트는 선택적으로 사용할 수 있습니다.

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
</script>

<!-- Header 없는 Card -->
<Card.Root>
	<Card.Content>
		<p>헤더가 없는 카드입니다.</p>
	</Card.Content>
	<Card.Footer>
		<Button>액션</Button>
	</Card.Footer>
</Card.Root>

<!-- Content만 있는 Card -->
<Card.Root>
	<Card.Content>
		<p>간단한 카드입니다.</p>
	</Card.Content>
</Card.Root>
```

---

## 5. Tailwind CSS 색상 토큰

Card 컴포넌트는 다음과 같은 Tailwind CSS 디자인 토큰을 사용합니다.

### 5.1. 색상 변수

```css
@layer base {
	:root {
		--card: 0 0% 100%;                   /* 카드 배경색 (흰색) */
		--card-foreground: 222.2 84% 4.9%;  /* 카드 텍스트 색상 (진한 회색) */
		--muted-foreground: 215.4 16.3% 46.9%; /* 흐린 텍스트 색상 (CardDescription) */
		--border: 214.3 31.8% 91.4%;        /* 테두리 색상 */
	}
}
```

**중요:** Dark 모드 변수(`.dark { ... }`)는 정의하지 않습니다.

---

## 6. 레이아웃 패턴

### 6.1. 대시보드 통계 카드

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';

	const stats = [
		{ title: '총 사용자', value: '12,345', change: '+12.5%' },
		{ title: '총 매출', value: '₩1,234,567', change: '+8.2%' },
		{ title: '방문자', value: '98,765', change: '+24.1%' }
	];
</script>

<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
	{#each stats as stat}
		<Card.Root>
			<Card.Header class="flex flex-row items-center justify-between space-y-0 pb-2">
				<Card.Title class="text-sm font-medium">{stat.title}</Card.Title>
				<svg class="h-4 w-4 text-muted-foreground" ...>...</svg>
			</Card.Header>
			<Card.Content>
				<div class="text-2xl font-bold">{stat.value}</div>
				<p class="text-xs text-muted-foreground">
					<span class="text-green-600">{stat.change}</span> 지난 달 대비
				</p>
			</Card.Content>
		</Card.Root>
	{/each}
</div>
```

### 6.2. 프로필 카드

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
</script>

<Card.Root class="w-[350px]">
	<Card.Header class="flex flex-row items-center gap-4">
		<img
			src="/avatar.jpg"
			alt="프로필"
			class="h-16 w-16 rounded-full"
		/>
		<div>
			<Card.Title>홍길동</Card.Title>
			<Card.Description>hong@example.com</Card.Description>
		</div>
	</Card.Header>
	<Card.Content>
		<div class="space-y-2">
			<div class="flex justify-between text-sm">
				<span class="text-muted-foreground">가입일</span>
				<span>2024-01-01</span>
			</div>
			<div class="flex justify-between text-sm">
				<span class="text-muted-foreground">등급</span>
				<span class="font-semibold">Gold</span>
			</div>
		</div>
	</Card.Content>
	<Card.Footer class="flex gap-2">
		<Button variant="outline" class="flex-1">메시지</Button>
		<Button class="flex-1">프로필 보기</Button>
	</Card.Footer>
</Card.Root>
```

---

## 7. 스타일 커스터마이징

### 7.1. 배경색 변경

```svelte
<Card.Root class="bg-blue-50">
	<Card.Header>
		<Card.Title>파란색 배경</Card.Title>
	</Card.Header>
	<Card.Content>
		<p>배경색이 파란색으로 변경되었습니다.</p>
	</Card.Content>
</Card.Root>
```

### 7.2. 테두리 색상 변경

```svelte
<Card.Root class="border-blue-500 border-2">
	<Card.Header>
		<Card.Title>파란색 테두리</Card.Title>
	</Card.Header>
	<Card.Content>
		<p>테두리 색상과 굵기가 변경되었습니다.</p>
	</Card.Content>
</Card.Root>
```

### 7.3. 그림자 효과 변경

```svelte
<Card.Root class="shadow-lg">
	<Card.Header>
		<Card.Title>큰 그림자</Card.Title>
	</Card.Header>
	<Card.Content>
		<p>그림자 효과가 커졌습니다.</p>
	</Card.Content>
</Card.Root>

<Card.Root class="shadow-none border-2">
	<Card.Header>
		<Card.Title>그림자 없음</Card.Title>
	</Card.Header>
	<Card.Content>
		<p>그림자 대신 테두리를 강조했습니다.</p>
	</Card.Content>
</Card.Root>
```

---

## 8. 접근성 (Accessibility)

### 8.1. 시맨틱 HTML

Card 컴포넌트는 적절한 HTML 태그를 사용합니다:
- **CardTitle**: `<h3>` (제목 태그)
- **CardDescription**: `<p>` (문단 태그)
- **나머지**: `<div>` (컨테이너 태그)

### 8.2. 키보드 접근성

Card 자체는 키보드 포커스를 받지 않지만, 내부 요소(버튼, 링크 등)는 키보드로 접근 가능합니다.

```svelte
<Card.Root>
	<Card.Header>
		<Card.Title>접근 가능한 카드</Card.Title>
	</Card.Header>
	<Card.Content>
		<a href="/link" class="text-primary hover:underline">
			키보드로 접근 가능한 링크
		</a>
	</Card.Content>
	<Card.Footer>
		<Button>키보드로 접근 가능한 버튼</Button>
	</Card.Footer>
</Card.Root>
```

### 8.3. 클릭 가능한 Card

Card 전체를 클릭 가능하게 만들 때는 적절한 role과 tabindex를 추가합니다.

```svelte
<Card.Root
	role="button"
	tabindex="0"
	class="cursor-pointer transition-shadow hover:shadow-lg"
	onclick={() => console.log('Card clicked')}
	onkeydown={(e) => {
		if (e.key === 'Enter' || e.key === ' ') {
			console.log('Card clicked via keyboard');
		}
	}}
>
	<Card.Header>
		<Card.Title>클릭 가능한 카드</Card.Title>
		<Card.Description>클릭하거나 Enter 키를 누르세요.</Card.Description>
	</Card.Header>
</Card.Root>
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

**파일 위치:** `src/routes/test/card/+page.svelte`

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
</script>

<section class="p-8 space-y-8 bg-gray-50 min-h-screen">
	<h1 class="text-3xl font-bold">Card 컴포넌트 테스트</h1>

	<!-- 기본 Card -->
	<div>
		<h2 class="text-xl font-semibold mb-4">기본 Card</h2>
		<Card.Root class="w-[350px]">
			<Card.Header>
				<Card.Title>카드 제목</Card.Title>
				<Card.Description>카드 설명이 여기에 표시됩니다.</Card.Description>
			</Card.Header>
			<Card.Content>
				<p>카드 본문 내용입니다.</p>
			</Card.Content>
			<Card.Footer class="flex justify-between">
				<Button variant="outline">취소</Button>
				<Button>확인</Button>
			</Card.Footer>
		</Card.Root>
	</div>

	<!-- Grid 레이아웃 -->
	<div>
		<h2 class="text-xl font-semibold mb-4">Grid 레이아웃</h2>
		<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
			{#each [1, 2, 3] as i}
				<Card.Root>
					<Card.Header>
						<Card.Title>카드 {i}</Card.Title>
						<Card.Description>설명 {i}</Card.Description>
					</Card.Header>
					<Card.Content>
						<p>내용 {i}</p>
					</Card.Content>
				</Card.Root>
			{/each}
		</div>
	</div>
</section>
```

---

## 10. 트러블슈팅

### 10.1. Card가 전체 너비를 차지함

**원인:** `<div>` 태그는 기본적으로 블록 요소이므로 전체 너비를 차지합니다.

**해결:**
```svelte
<!-- ❌ 전체 너비 -->
<Card.Root>...</Card.Root>

<!-- ✅ 고정 너비 -->
<Card.Root class="w-[350px]">...</Card.Root>

<!-- ✅ 최대 너비 -->
<Card.Root class="max-w-md">...</Card.Root>
```

### 10.2. Header와 Content 간격 문제

**원인:** `CardContent`의 `pt-0`이 제거되어 상단 패딩이 생김

**해결:**
```svelte
<!-- ✅ 올바른 사용 -->
<Card.Content>...</Card.Content>

<!-- ❌ pt-0 덮어쓰지 말 것 -->
<Card.Content class="pt-6">...</Card.Content>
```

### 10.3. Footer의 버튼 정렬

**원인:** Footer는 기본적으로 Flexbox이지만 정렬 방식을 명시해야 함

**해결:**
```svelte
<!-- 좌우 정렬 -->
<Card.Footer class="flex justify-between">
	<Button variant="outline">취소</Button>
	<Button>확인</Button>
</Card.Footer>

<!-- 우측 정렬 -->
<Card.Footer class="flex justify-end gap-2">
	<Button variant="outline">취소</Button>
	<Button>확인</Button>
</Card.Footer>

<!-- 전체 너비 버튼 -->
<Card.Footer class="flex flex-col gap-2">
	<Button class="w-full">확인</Button>
	<Button variant="outline" class="w-full">취소</Button>
</Card.Footer>
```

---

## 11. 참고 자료

### 11.1. 관련 문서
- `sonub-ui-button.md`: Button 컴포넌트 명세서
- `sonub-setup-shadcn.md`: shadcn-svelte 배경 및 수동 구현 이유
- `sonub-setup-tailwind.md`: Tailwind CSS Light 모드 설정
- `sonub-design-components.md`: UI 컴포넌트 전체 개요

### 11.2. 외부 참조
- **shadcn-svelte Card**: https://www.shadcn-svelte.com/docs/components/card
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Svelte 5 Snippet**: https://svelte.dev/docs/svelte/snippet

---

## 12. 변경 이력

| 버전 | 날짜 | 작성자 | 변경 내용 |
|------|------|---------|-----------|
| 1.0.0 | 2025-01-09 | JaeHo Song | 초기 Card 컴포넌트 명세서 작성 |

**v1.0.0 (2025-01-09):**
- Card 컴포넌트 및 6개 서브 컴포넌트 구현 문서화
- Light 모드 전용 구현 (dark: 변형 없음)
- 다양한 사용 예제 및 레이아웃 패턴 추가
- 접근성 가이드 추가
- 스타일 커스터마이징 방법 설명
- 트러블슈팅 가이드 추가
