---
name: sonub-shadcn-setup
version: 1.1.0
description: SvelteKit 프로젝트에 shadcn-svelte UI 컴포넌트 라이브러리 설치 및 설정 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-08
updated: 2025-01-09
step: 25
priority: "*"
dependencies: ["sonub-setup-svelte.md", "sonub-setup-tailwind.md"]
tags: ["shadcn-svelte", "ui", "components", "라이브러리", "설정", "수동구현"]
---

# SvelteKit 프로젝트 shadcn-svelte 설치 명세서

## 1. 개요

### 1.1. 목적
본 명세서는 SvelteKit 5 프로젝트에 shadcn-svelte UI 컴포넌트 라이브러리를 설치하고 설정하는 방법을 정의합니다. shadcn-svelte는 Radix UI를 기반으로 한 재사용 가능한 고품질 UI 컴포넌트 라이브러리입니다.

### 1.2. 범위
- shadcn-svelte 설치 및 초기 설정
- 컴포넌트 추가 방법
- 기본 사용법 및 예제
- 검증 및 문제 해결

### 1.3. 전제 조건
본 명세서는 다음 문서가 완료된 후 실행되어야 합니다:
- `sonub-setup-svelte.md`: SvelteKit 프로젝트 초기 설정 완료
- `sonub-setup-tailwind.md`: Tailwind CSS 설치 및 설정 완료

### 1.4. shadcn-svelte 특징

**주요 특징:**
- ✅ **완전한 소스 코드 제공**: npm 패키지가 아닌 소스 코드를 프로젝트에 직접 복사
- ✅ **커스터마이징 용이**: 컴포넌트를 자유롭게 수정 가능
- ✅ **접근성**: Radix UI 기반으로 웹 접근성 표준 준수
- ✅ **타입 안전**: TypeScript 완전 지원
- ✅ **Tailwind CSS 기반**: 일관된 스타일링 시스템

**React shadcn/ui와의 차이점:**
- React 버전은 컴포넌트가 단일 파일에 존재
- Svelte 버전은 컴포넌트가 여러 파일로 분리 (예: Accordion → 4개 파일)
- `index.ts`를 통해 단일 파일에서 임포트 가능

---

## 2. 중요 안내

### ⚠️ 전제 조건 확인

**shadcn-svelte 설치 전에 다음이 반드시 완료되어 있어야 합니다:**

1. ✅ **SvelteKit 5 설치 완료**
   - `sonub-setup-svelte.md` 명세서에 따라 설치

2. ✅ **Tailwind CSS 설치 완료**
   - `sonub-setup-tailwind.md` 명세서에 따라 설치
   - `src/app.css`에 Tailwind 지시문 포함
   - `vite.config.ts`에 Tailwind 플러그인 추가

**설치 확인 방법:**

```bash
# SvelteKit 확인
npm list @sveltejs/kit

# Tailwind CSS 확인
npm list tailwindcss
```

**예상 출력:**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
├── @sveltejs/kit@2.47.1
└── tailwindcss@4.1.14
```

---

## 3. 요구사항

### 3.1. 시스템 요구사항
- Node.js 18.x 이상
- npm 9.x 이상 (또는 pnpm, yarn)
- SvelteKit 2.x
- Tailwind CSS 4.x

### 3.2. 필수 패키지 확인

본 프로젝트에 shadcn-svelte를 설치하기 전 다음 패키지가 설치되어 있어야 합니다:

```json
{
  "devDependencies": {
    "@sveltejs/kit": "^2.47.1",
    "svelte": "^5.41.0",
    "tailwindcss": "^4.1.14",
    "@tailwindcss/vite": "^4.1.14"
  }
}
```

### 3.3. 경로 별칭 설정 확인

`svelte.config.js`에서 경로 별칭이 올바르게 설정되어 있는지 확인합니다.

**파일 위치:** `svelte.config.js`

**기본 설정:**
```javascript
import adapter from '@sveltejs/adapter-auto';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	preprocess: vitePreprocess(),
	kit: {
		adapter: adapter(),
		alias: {
			$lib: './src/lib'
			// 다른 별칭을 사용하는 경우 여기에 추가
		}
	}
};

export default config;
```

**참고:** 기본 `$lib` 별칭을 사용하므로 추가 설정이 필요 없습니다.

---

## 4. 설치 환경

### 4.1. 운영 체제
- macOS (권장: Ventura 13.x 이상)
- Linux (Ubuntu 22.04 LTS 이상)
- Windows 10/11 (WSL2 사용 권장)

### 4.2. 개발 도구
- Visual Studio Code (권장)
- shadcn-svelte VSCode 확장 프로그램 (선택 사항)
- JetBrains IDE 플러그인 (IntelliJ, WebStorm 등, 선택 사항)

### 4.3. 추천 VSCode 확장 프로그램

**shadcn-svelte 확장:**
- 확장 프로그램 ID: `huntabyte.shadcn-svelte`
- 기능: IDE에서 직접 컴포넌트 추가 가능
- 설치 방법: VSCode Extensions에서 "shadcn-svelte" 검색

---

## 5. 설치 절차

### 5.1. shadcn-svelte 개발 패키지 설치 (선택 사항)

**참고:** shadcn-svelte는 컴포넌트 소스 코드를 프로젝트에 복사하는 방식이므로, CLI 도구만 있으면 됩니다. 개발 의존성으로 설치하는 것은 선택 사항입니다.

```bash
npm install -D shadcn-svelte
```

**또는** CLI 도구를 직접 실행하는 방식 사용:
```bash
npx shadcn-svelte@latest init
```

### 5.2. shadcn-svelte 초기화

프로젝트에 shadcn-svelte를 설정합니다.

```bash
npx shadcn-svelte@latest init
```

### 5.3. 초기화 설정 질문 및 응답

초기화 명령을 실행하면 다음 질문들이 나타납니다. 각 질문에 대한 권장 응답은 다음과 같습니다:

#### 질문 1: Which style would you like to use?
**질문:** 어떤 스타일을 사용하시겠습니까?

**선택지:**
- `Default` (기본)
- `New York` (고급 스타일)

**권장 응답:**
```
? Which style would you like to use? › Default
```

**설명:**
- `Default`: 일반적인 그림자와 둥근 모서리를 가진 기본 스타일
- `New York`: 더 미니멀한 디자인 (New York Times 스타일)

#### 질문 2: Which base color would you like to use?
**질문:** 어떤 기본 색상을 사용하시겠습니까?

**선택지:**
- `Slate`
- `Gray`
- `Zinc`
- `Neutral`
- `Stone`

**권장 응답:**
```
? Which base color would you like to use? › Slate
```

**설명:**
- Tailwind CSS의 색상 팔레트 중 하나 선택
- Light 모드 전용이므로 라이트 톤에서 잘 보이는 Slate 권장

#### 질문 3: Where is your global CSS file?
**질문:** 전역 CSS 파일의 위치는 어디입니까?

**권장 응답:**
```
? Where is your global CSS file? › src/app.css
```

**설명:**
- Tailwind CSS가 설정된 전역 CSS 파일 경로
- SvelteKit 기본 경로는 `src/app.css`

#### 질문 4: Configure the import alias for components
**질문:** 컴포넌트 임포트 별칭을 설정하세요

**권장 응답:**
```
? Configure the import alias for components: › $lib/components
```

**설명:**
- 일반 컴포넌트가 저장될 디렉토리
- 기본값 사용 권장

#### 질문 5: Configure the import alias for utils
**질문:** 유틸리티 임포트 별칭을 설정하세요

**권장 응답:**
```
? Configure the import alias for utils: › $lib/utils
```

**설명:**
- shadcn-svelte 유틸리티 함수가 저장될 디렉토리
- 기본값 사용 권장

#### 질문 6: Configure the import alias for ui
**질문:** UI 컴포넌트 임포트 별칭을 설정하세요

**권장 응답:**
```
? Configure the import alias for ui: › $lib/components/ui
```

**설명:**
- shadcn-svelte UI 컴포넌트가 저장될 디렉토리
- 기본값 사용 권장

### 5.4. 초기화 완료 확인

초기화가 완료되면 다음 파일들이 생성됩니다:

#### 생성된 파일 및 디렉토리:

**1. components.json**
- 위치: 프로젝트 루트
- 내용: shadcn-svelte 설정 정보

**예시:**
```json
{
	"$schema": "https://shadcn-svelte.com/schema.json",
	"style": "default",
	"tailwind": {
		"config": "tailwind.config.ts",
		"css": "src/app.css",
		"baseColor": "slate"
	},
	"aliases": {
		"components": "$lib/components",
		"utils": "$lib/utils",
		"ui": "$lib/components/ui"
	}
}
```

**2. src/lib/utils.ts**
- 위치: `src/lib/utils.ts`
- 내용: shadcn-svelte에서 사용하는 유틸리티 함수

**예시:**
```typescript
import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}
```

**3. src/lib/components/ui/**
- 위치: `src/lib/components/ui/`
- 내용: UI 컴포넌트가 저장될 디렉토리 (초기에는 비어 있음)

**4. src/app.css 업데이트**
- shadcn-svelte CSS 변수가 추가됩니다

**업데이트된 내용:**
```css
@import "tailwindcss";

@layer base {
	:root {
		--radius: 0.5rem;

		/* shadcn-svelte 색상 변수 */
		--background: 0 0% 100%;
		--foreground: 222.2 84% 4.9%;
		--card: 0 0% 100%;
		--card-foreground: 222.2 84% 4.9%;
		--popover: 0 0% 100%;
		--popover-foreground: 222.2 84% 4.9%;
		--primary: 222.2 47.4% 11.2%;
		--primary-foreground: 210 40% 98%;
		--secondary: 210 40% 96.1%;
		--secondary-foreground: 222.2 47.4% 11.2%;
		--muted: 210 40% 96.1%;
		--muted-foreground: 215.4 16.3% 46.9%;
		--accent: 210 40% 96.1%;
		--accent-foreground: 222.2 47.4% 11.2%;
		--destructive: 0 84.2% 60.2%;
		--destructive-foreground: 210 40% 98%;
		--border: 214.3 31.8% 91.4%;
		--input: 214.3 31.8% 91.4%;
		--ring: 222.2 84% 4.9%;
	}
}
```

### 5.5. 필수 의존성 패키지 설치 확인

shadcn-svelte가 자동으로 설치하는 패키지들을 확인합니다:

```bash
npm list clsx tailwind-merge
```

**예상 출력:**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
├── clsx@2.1.0
└── tailwind-merge@2.2.1
```

**설치되지 않은 경우:**
```bash
npm install -D clsx tailwind-merge
```

---

## 6. 컴포넌트 추가 방법

### 6.1. 사용 가능한 컴포넌트 목록 확인

shadcn-svelte에서 제공하는 모든 컴포넌트 목록을 확인합니다.

```bash
npx shadcn-svelte@latest list
```

**예상 출력:**
```
Available components:
  - accordion
  - alert
  - alert-dialog
  - aspect-ratio
  - avatar
  - badge
  - breadcrumb
  - button
  - calendar
  - card
  - carousel
  - checkbox
  - collapsible
  - combobox
  - command
  - context-menu
  - data-table
  - date-picker
  - dialog
  - dropdown-menu
  - form
  - hover-card
  - input
  - label
  - menubar
  - navigation-menu
  - pagination
  - popover
  - progress
  - radio-group
  - scroll-area
  - select
  - separator
  - sheet
  - skeleton
  - slider
  - switch
  - table
  - tabs
  - textarea
  - toast
  - toggle
  - toggle-group
  - tooltip
```

### 6.2. 단일 컴포넌트 추가

Button 컴포넌트를 추가하는 예시입니다.

```bash
npx shadcn-svelte@latest add button
```

**예상 출력:**
```
✔ Downloading component...
✔ Creating files...

Created files:
  - src/lib/components/ui/button/button.svelte
  - src/lib/components/ui/button/index.ts

Done!
```

**생성된 파일 구조:**
```
src/lib/components/ui/button/
├── button.svelte          # 버튼 컴포넌트
└── index.ts              # 재내보내기 파일
```

### 6.3. 여러 컴포넌트 한 번에 추가

여러 컴포넌트를 한 번에 추가할 수 있습니다.

```bash
npx shadcn-svelte@latest add card input label button
```

**예상 출력:**
```
✔ Downloading components...
✔ Creating files...

Created files:
  - src/lib/components/ui/card/card.svelte
  - src/lib/components/ui/card/card-content.svelte
  - src/lib/components/ui/card/card-description.svelte
  - src/lib/components/ui/card/card-footer.svelte
  - src/lib/components/ui/card/card-header.svelte
  - src/lib/components/ui/card/card-title.svelte
  - src/lib/components/ui/card/index.ts
  - src/lib/components/ui/input/input.svelte
  - src/lib/components/ui/input/index.ts
  - src/lib/components/ui/label/label.svelte
  - src/lib/components/ui/label/index.ts
  - src/lib/components/ui/button/button.svelte
  - src/lib/components/ui/button/index.ts

Done!
```

### 6.4. VSCode 확장 프로그램으로 컴포넌트 추가

VSCode에 shadcn-svelte 확장 프로그램을 설치한 경우:

1. **Command Palette 열기:** `Cmd+Shift+P` (macOS) 또는 `Ctrl+Shift+P` (Windows/Linux)
2. **명령 입력:** `shadcn-svelte: Add Component`
3. **컴포넌트 선택:** 목록에서 원하는 컴포넌트 선택
4. **자동 추가:** 선택한 컴포넌트가 프로젝트에 추가됨

---

## 7. 사용 방법

### 7.1. 기본 컴포넌트 사용 예제

#### 7.1.1. Button 컴포넌트

**파일 위치:** `src/routes/+page.svelte`

**기본 버튼:**
```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';
</script>

<section class="p-8 space-y-4">
	<h1 class="text-2xl font-bold mb-4">shadcn-svelte 버튼 예제</h1>

	<div class="flex gap-2">
		<Button>기본 버튼</Button>
		<Button variant="secondary">보조 버튼</Button>
		<Button variant="destructive">삭제 버튼</Button>
		<Button variant="outline">아웃라인 버튼</Button>
		<Button variant="ghost">고스트 버튼</Button>
		<Button variant="link">링크 버튼</Button>
	</div>

	<div class="flex gap-2">
		<Button size="sm">작은 버튼</Button>
		<Button size="default">기본 크기</Button>
		<Button size="lg">큰 버튼</Button>
		<Button size="icon">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M5 12h14" />
				<path d="m12 5 7 7-7 7" />
			</svg>
		</Button>
	</div>
</section>
```

**버튼 Props:**
- `variant`: "default" | "destructive" | "outline" | "secondary" | "ghost" | "link"
- `size`: "default" | "sm" | "lg" | "icon"
- `class`: 추가 Tailwind 클래스

#### 7.1.2. Card 컴포넌트

**컴포넌트 추가:**
```bash
npx shadcn-svelte@latest add card
```

**사용 예제:**
```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
</script>

<section class="p-8">
	<Card.Root class="w-[350px]">
		<Card.Header>
			<Card.Title>카드 제목</Card.Title>
			<Card.Description>카드 설명이 여기에 표시됩니다.</Card.Description>
		</Card.Header>
		<Card.Content>
			<p class="text-sm text-muted-foreground">
				카드 본문 내용입니다. 여기에 다양한 콘텐츠를 추가할 수 있습니다.
			</p>
		</Card.Content>
		<Card.Footer class="flex justify-between">
			<Button variant="outline">취소</Button>
			<Button>확인</Button>
		</Card.Footer>
	</Card.Root>
</section>
```

#### 7.1.3. Input 및 Label 컴포넌트

**컴포넌트 추가:**
```bash
npx shadcn-svelte@latest add input label
```

**사용 예제:**
```svelte
<script lang="ts">
	import { Input } from '$lib/components/ui/input';
	import { Label } from '$lib/components/ui/label';
	import { Button } from '$lib/components/ui/button';

	let email = '';
	let password = '';

	function handleSubmit() {
		console.log({ email, password });
	}
</script>

<section class="p-8">
	<form on:submit|preventDefault={handleSubmit} class="space-y-4 max-w-sm">
		<div class="space-y-2">
			<Label for="email">이메일</Label>
			<Input
				id="email"
				type="email"
				placeholder="email@example.com"
				bind:value={email}
			/>
		</div>

		<div class="space-y-2">
			<Label for="password">비밀번호</Label>
			<Input
				id="password"
				type="password"
				placeholder="비밀번호를 입력하세요"
				bind:value={password}
			/>
		</div>

		<Button type="submit" class="w-full">로그인</Button>
	</form>
</section>
```

### 7.2. 고급 컴포넌트 사용 예제

#### 7.2.1. Dialog (모달) 컴포넌트

**컴포넌트 추가:**
```bash
npx shadcn-svelte@latest add dialog
```

**사용 예제:**
```svelte
<script lang="ts">
	import * as Dialog from '$lib/components/ui/dialog';
	import { Button } from '$lib/components/ui/button';
	import { Input } from '$lib/components/ui/input';
	import { Label } from '$lib/components/ui/label';

	let open = false;
	let name = '';

	function handleSave() {
		console.log('저장:', name);
		open = false;
	}
</script>

<section class="p-8">
	<Dialog.Root bind:open>
		<Dialog.Trigger asChild let:builder>
			<Button builders={[builder]}>프로필 수정</Button>
		</Dialog.Trigger>
		<Dialog.Content class="sm:max-w-[425px]">
			<Dialog.Header>
				<Dialog.Title>프로필 수정</Dialog.Title>
				<Dialog.Description>
					여기에서 프로필 정보를 변경할 수 있습니다.
				</Dialog.Description>
			</Dialog.Header>
			<div class="grid gap-4 py-4">
				<div class="grid grid-cols-4 items-center gap-4">
					<Label for="name" class="text-right">이름</Label>
					<Input id="name" bind:value={name} class="col-span-3" />
				</div>
			</div>
			<Dialog.Footer>
				<Button on:click={handleSave}>저장</Button>
			</Dialog.Footer>
		</Dialog.Content>
	</Dialog.Root>
</section>
```

#### 7.2.2. Select 컴포넌트

**컴포넌트 추가:**
```bash
npx shadcn-svelte@latest add select
```

**사용 예제:**
```svelte
<script lang="ts">
	import * as Select from '$lib/components/ui/select';
	import { Label } from '$lib/components/ui/label';

	let selectedFruit = { value: 'apple', label: '사과' };
</script>

<section class="p-8 max-w-sm">
	<div class="space-y-2">
		<Label>과일 선택</Label>
		<Select.Root bind:selected={selectedFruit}>
			<Select.Trigger>
				<Select.Value placeholder="과일을 선택하세요" />
			</Select.Trigger>
			<Select.Content>
				<Select.Item value="apple">사과</Select.Item>
				<Select.Item value="banana">바나나</Select.Item>
				<Select.Item value="orange">오렌지</Select.Item>
				<Select.Item value="grape">포도</Select.Item>
			</Select.Content>
		</Select.Root>
	</div>

	<p class="mt-4 text-sm text-muted-foreground">
		선택된 과일: {selectedFruit.label}
	</p>
</section>
```

#### 7.2.3. Toast (알림) 컴포넌트

**컴포넌트 추가:**
```bash
npx shadcn-svelte@latest add toast
```

**사용 예제:**

**1. 레이아웃에 Toaster 추가:**

**파일 위치:** `src/routes/+layout.svelte`

```svelte
<script>
	import '../app.css';
	import { Toaster } from '$lib/components/ui/toast';
</script>

<slot />

<Toaster />
```

**2. Toast 사용:**

**파일 위치:** `src/routes/+page.svelte`

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';
	import { toast } from '$lib/components/ui/toast';

	function showSuccess() {
		toast.success('성공!', {
			description: '작업이 성공적으로 완료되었습니다.'
		});
	}

	function showError() {
		toast.error('오류 발생', {
			description: '작업을 완료할 수 없습니다.'
		});
	}

	function showInfo() {
		toast.info('알림', {
			description: '새로운 메시지가 있습니다.'
		});
	}
</script>

<section class="p-8">
	<div class="flex gap-2">
		<Button on:click={showSuccess}>성공 토스트</Button>
		<Button on:click={showError} variant="destructive">에러 토스트</Button>
		<Button on:click={showInfo} variant="secondary">정보 토스트</Button>
	</div>
</section>
```

### 7.3. 컴포넌트 커스터마이징

shadcn-svelte 컴포넌트는 소스 코드가 프로젝트에 직접 복사되므로 자유롭게 수정할 수 있습니다.

**예시: Button 컴포넌트 스타일 변경**

**파일 위치:** `src/lib/components/ui/button/button.svelte`

```svelte
<script lang="ts">
	import { cn } from '$lib/utils';
	import type { Snippet } from 'svelte';

	type Props = {
		variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link' | 'custom';
		size?: 'default' | 'sm' | 'lg' | 'icon';
		class?: string;
		children?: Snippet;
		// ... 기타 props
	};

	let { variant = 'default', size = 'default', class: className, children, ...restProps }: Props = $props();

	const variants = {
		default: 'bg-primary text-primary-foreground hover:bg-primary/90',
		destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
		outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
		secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
		ghost: 'hover:bg-accent hover:text-accent-foreground',
		link: 'text-primary underline-offset-4 hover:underline',
		// ✅ 커스텀 variant 추가
		custom: 'bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600'
	};

	// ... 나머지 코드
</script>

<!-- 사용: <Button variant="custom">커스텀 버튼</Button> -->
```

---

## 8. 검증 방법

### 8.1. 설치 검증 체크리스트

다음 항목을 확인하여 shadcn-svelte가 올바르게 설정되었는지 검증합니다:

#### ✅ 1. components.json 파일 존재 확인

```bash
ls -la components.json
```

**예상 출력:**
```
-rw-r--r--  1 user  staff  XXX Jan  8 12:00 components.json
```

#### ✅ 2. utils.ts 파일 존재 확인

```bash
ls -la src/lib/utils.ts
```

**예상 출력:**
```
-rw-r--r--  1 user  staff  XXX Jan  8 12:00 src/lib/utils.ts
```

#### ✅ 3. UI 컴포넌트 디렉토리 확인

```bash
ls -la src/lib/components/ui/
```

**예상 출력 (button 추가 후):**
```
drwxr-xr-x  4 user  staff  128 Jan  8 12:00 button
```

#### ✅ 4. 필수 패키지 설치 확인

```bash
npm list clsx tailwind-merge
```

**예상 출력:**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
├── clsx@2.1.0
└── tailwind-merge@2.2.1
```

#### ✅ 5. app.css에 shadcn 변수 추가 확인

```bash
grep -n "background" src/app.css
```

**예상 출력:**
```
10:		--background: 0 0% 100%;
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
1. Button 컴포넌트 사용 페이지로 이동
2. 버튼이 올바른 스타일로 렌더링되는지 확인
3. 버튼 클릭 시 정상 동작 확인

#### ✅ 8. 빌드 테스트

```bash
npm run build
```

**예상 출력:**
```
vite v7.1.10 building for production...
✓ 123 modules transformed.
✓ built in 2.34s
```

### 8.2. 컴포넌트 동작 확인

**테스트 페이지 생성:**

**파일 위치:** `src/routes/shadcn-test/+page.svelte`

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button';
	import * as Card from '$lib/components/ui/card';
	import { Input } from '$lib/components/ui/input';
	import { Label } from '$lib/components/ui/label';
</script>

<section class="min-h-screen bg-gray-50 p-8">
	<div class="max-w-4xl mx-auto space-y-8">
		<h1 class="text-4xl font-bold text-gray-900">
			shadcn-svelte 테스트 페이지
		</h1>

		<!-- Button 테스트 -->
		<Card.Root>
			<Card.Header>
				<Card.Title>Button 컴포넌트</Card.Title>
				<Card.Description>다양한 버튼 variants와 sizes</Card.Description>
			</Card.Header>
			<Card.Content class="space-y-4">
				<div class="flex flex-wrap gap-2">
					<Button>Default</Button>
					<Button variant="secondary">Secondary</Button>
					<Button variant="destructive">Destructive</Button>
					<Button variant="outline">Outline</Button>
					<Button variant="ghost">Ghost</Button>
					<Button variant="link">Link</Button>
				</div>
				<div class="flex flex-wrap gap-2">
					<Button size="sm">Small</Button>
					<Button size="default">Default</Button>
					<Button size="lg">Large</Button>
				</div>
			</Card.Content>
		</Card.Root>

		<!-- Form 테스트 -->
		<Card.Root>
			<Card.Header>
				<Card.Title>Form 컴포넌트</Card.Title>
				<Card.Description>Input과 Label 조합</Card.Description>
			</Card.Header>
			<Card.Content>
				<form class="space-y-4">
					<div class="space-y-2">
						<Label for="test-email">이메일</Label>
						<Input id="test-email" type="email" placeholder="email@example.com" />
					</div>
					<div class="space-y-2">
						<Label for="test-password">비밀번호</Label>
						<Input id="test-password" type="password" placeholder="비밀번호" />
					</div>
					<Button type="submit" class="w-full">제출</Button>
				</form>
			</Card.Content>
		</Card.Root>
	</div>
</section>
```

**테스트 방법:**
1. 개발 서버 실행: `npm run dev`
2. 브라우저에서 `http://localhost:5173/shadcn-test` 접속
3. 모든 컴포넌트가 올바르게 렌더링되는지 확인
4. 버튼 클릭, 입력 필드 상호작용 테스트

---

## 9. 문제 해결

### 9.1. 컴포넌트를 찾을 수 없음

**증상:**
```
Cannot find module '$lib/components/ui/button'
```

**해결 방법:**

#### 방법 1: 컴포넌트가 실제로 추가되었는지 확인

```bash
ls -la src/lib/components/ui/button/
```

컴포넌트가 없다면 다시 추가:
```bash
npx shadcn-svelte@latest add button
```

#### 방법 2: TypeScript 경로 설정 확인

**파일 위치:** `tsconfig.json`

```json
{
	"extends": "./.svelte-kit/tsconfig.json",
	"compilerOptions": {
		"allowJs": true,
		"checkJs": true,
		"esModuleInterop": true,
		"forceConsistentCasingInFileNames": true,
		"resolveJsonModule": true,
		"skipLibCheck": true,
		"sourceMap": true,
		"strict": true,
		"paths": {
			"$lib": ["./src/lib"],
			"$lib/*": ["./src/lib/*"]
		}
	}
}
```

#### 방법 3: IDE 재시작

VSCode를 완전히 종료하고 재시작합니다.

### 9.2. 스타일이 적용되지 않음

**증상:**
- 컴포넌트는 렌더링되지만 스타일이 없음

**해결 방법:**

#### 방법 1: app.css가 레이아웃에서 임포트되었는지 확인

**파일 위치:** `src/routes/+layout.svelte`

```svelte
<script>
	import '../app.css'; // ✅ 반드시 포함
</script>

<slot />
```

#### 방법 2: Tailwind CSS가 올바르게 설정되었는지 확인

```bash
npm list tailwindcss @tailwindcss/vite
```

설치되지 않은 경우 `sonub-setup-tailwind.md` 참조하여 설치

#### 방법 3: app.css에 shadcn 변수가 있는지 확인

```bash
cat src/app.css | grep "background"
```

없다면 shadcn-svelte를 다시 초기화:
```bash
npx shadcn-svelte@latest init
```

### 9.3. cn 함수 오류

**증상:**
```
Cannot find module '$lib/utils' or its corresponding type declarations
```

**해결 방법:**

#### 방법 1: utils.ts 파일 확인

```bash
ls -la src/lib/utils.ts
```

없다면 수동으로 생성:

**파일 위치:** `src/lib/utils.ts`

```typescript
import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}
```

#### 방법 2: 필수 패키지 설치 확인

```bash
npm install -D clsx tailwind-merge
```

### 9.4. 컴포넌트 추가 시 오류

**증상:**
```
Error: components.json not found
```

**해결 방법:**

#### 방법 1: shadcn-svelte 재초기화

```bash
npx shadcn-svelte@latest init
```

모든 질문에 응답하여 `components.json` 생성

#### 방법 2: 프로젝트 루트에서 명령 실행

올바른 디렉토리에서 명령을 실행하고 있는지 확인:
```bash
pwd  # 프로젝트 루트인지 확인
ls -la components.json  # 파일이 있는지 확인
```

### 9.5. Svelte 5 Runes 호환성 문제

**증상:**
- Svelte 5 runes 문법에서 컴포넌트가 작동하지 않음

**해결 방법:**

#### 방법 1: 최신 버전 사용

shadcn-svelte는 Svelte 5를 지원합니다. 최신 버전을 사용하세요:

```bash
npx shadcn-svelte@latest add button
```

#### 방법 2: 컴포넌트 코드 확인

Svelte 5 문법(`$props()`, `$state()` 등)을 사용하는지 확인

**예시:**
```svelte
<script lang="ts">
	let { variant = 'default', ...restProps } = $props();
	// Svelte 5 runes 문법 ✅
</script>
```

---

## 10. 다음 단계

shadcn-svelte 설치 완료 후 다음 작업을 진행합니다:

1. **주요 컴포넌트 추가**
   - Form 관련: Input, Label, Textarea, Select, Checkbox, Radio
   - 피드백: Alert, Toast, Dialog, Alert Dialog
   - 네비게이션: Tabs, Breadcrumb, Pagination
   - 데이터 표시: Table, Card, Badge, Avatar

2. **커스텀 컴포넌트 개발**
   - shadcn-svelte 컴포넌트를 기반으로 프로젝트 전용 컴포넌트 제작
   - 디자인 시스템에 맞게 스타일 커스터마이징

3. **Form 라이브러리 통합 (선택 사항)**
   - Superforms + Zod 통합
   - Form validation 설정

4. **Storybook 통합 (선택 사항)**
   - shadcn-svelte 컴포넌트 스토리 작성
   - UI 컴포넌트 문서화

---

## 11. 참고 자료

### 11.1. 공식 문서

- **shadcn-svelte 공식 사이트**: https://www.shadcn-svelte.com
- **shadcn-svelte 설치 가이드**: https://www.shadcn-svelte.com/docs/installation
- **shadcn-svelte SvelteKit 가이드**: https://www.shadcn-svelte.com/docs/installation/sveltekit
- **컴포넌트 목록**: https://www.shadcn-svelte.com/docs/components

### 11.2. 관련 라이브러리

- **Radix UI**: https://www.radix-ui.com/ (컴포넌트 기반)
- **clsx**: https://github.com/lukeed/clsx (클래스명 유틸리티)
- **tailwind-merge**: https://github.com/dcastil/tailwind-merge (Tailwind 클래스 병합)

### 11.3. 관련 명세서

- `sonub-setup-svelte.md`: SvelteKit 프로젝트 초기 설정
- `sonub-setup-tailwind.md`: Tailwind CSS 설치 및 설정

### 11.4. VSCode 확장 프로그램

- **shadcn-svelte**: `huntabyte.shadcn-svelte`
- **Svelte for VS Code**: `svelte.svelte-vscode`
- **Tailwind CSS IntelliSense**: `bradlc.vscode-tailwindcss`

---

## 12. 승인 기준

본 명세서에 따른 shadcn-svelte 설치가 완료되었다고 판단하는 기준:

### 12.1. 필수 요구사항

- ✅ `npx shadcn-svelte@latest init` 실행 완료
- ✅ `components.json` 파일 생성 확인
- ✅ `src/lib/utils.ts` 파일 생성 확인
- ✅ `src/lib/components/ui/` 디렉토리 생성 확인
- ✅ `src/app.css`에 shadcn CSS 변수 추가 확인
- ✅ `clsx` 및 `tailwind-merge` 패키지 설치 확인
- ✅ 최소 1개 이상의 컴포넌트 추가 (예: Button)
- ✅ `npm run dev` 실행 시 컴포넌트 정상 렌더링

### 12.2. 선택 요구사항

- ✅ shadcn-svelte VSCode 확장 프로그램 설치 (권장)
- ✅ 여러 기본 컴포넌트 추가 (Button, Card, Input, Label 등)
- ✅ 테스트 페이지 작성하여 동작 확인

### 12.3. 동작 검증

- ✅ Button 컴포넌트 모든 variants 정상 렌더링
- ✅ Card 컴포넌트 정상 렌더링
- ✅ Input/Label 컴포넌트 상호작용 정상 동작
- ✅ 컴포넌트 스타일이 Tailwind CSS와 올바르게 통합
- ✅ 프로덕션 빌드 성공 (`npm run build`)

---

## 13. 실제 구현 내용

### 13.1. 구현 방식

본 프로젝트에서는 **shadcn-svelte CLI를 사용하지 않고**, Svelte 5 runes 문법에 맞춰 **UI 컴포넌트를 수동으로 작성**했습니다.

**이유:**
- Svelte 5 runes ($props, $state, Snippet) 완전 호환
- 프로젝트 요구사항에 맞는 정확한 컴포넌트 구조
- 불필요한 의존성 최소화

### 13.2. 설치된 패키지

UI 컴포넌트 구현을 위해 다음 패키지들이 설치되었습니다:

```json
{
  "dependencies": {
    "firebase": "^12.5.0",
    "clsx": "^2.1.0",
    "tailwind-merge": "^2.2.1"
  }
}
```

**패키지 역할:**
- **clsx**: 조건부 클래스명 결합 유틸리티
- **tailwind-merge**: Tailwind CSS 클래스 충돌 방지 및 병합

### 13.3. 생성된 유틸리티 함수

**파일 경로:** `src/lib/utils.ts`

**내용:**
```typescript
/**
 * 유틸리티 함수 모음
 *
 * shadcn-svelte와 호환되는 클래스 이름 병합 함수를 제공합니다.
 */

import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * 클래스 이름을 병합하는 함수
 *
 * Tailwind CSS 클래스를 효율적으로 병합하고, 충돌하는 클래스를 제거합니다.
 *
 * @param inputs - 병합할 클래스 이름들
 * @returns 병합된 클래스 이름 문자열
 */
export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}
```

### 13.4. 생성된 UI 컴포넌트

#### 13.4.1. Button 컴포넌트

**디렉토리 구조:**
```
src/lib/components/ui/button/
├── button.svelte      # 버튼 컴포넌트 (Svelte 5 runes)
└── index.ts          # Export 파일
```

**주요 기능:**
- 6가지 variants: default, destructive, outline, secondary, ghost, link
- 4가지 sizes: default, sm, lg, icon
- Svelte 5 runes ($props, Snippet) 사용
- Tailwind CSS 기반 스타일링
- 완전한 TypeScript 타입 지원

#### 13.4.2. Card 컴포넌트

**디렉토리 구조:**
```
src/lib/components/ui/card/
├── card.svelte                # 카드 루트 컨테이너
├── card-header.svelte         # 카드 헤더
├── card-title.svelte          # 카드 제목
├── card-description.svelte    # 카드 설명
├── card-content.svelte        # 카드 콘텐츠
├── card-footer.svelte         # 카드 푸터
└── index.ts                   # 모든 카드 컴포넌트 export
```

**주요 기능:**
- 구조화된 카드 레이아웃 (Header, Title, Description, Content, Footer)
- 각 부분을 독립적으로 사용 가능
- Svelte 5 runes 사용
- 반응형 디자인 지원

**Export 구조 (`index.ts`):**
```typescript
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

#### 13.4.3. Alert 컴포넌트

**디렉토리 구조:**
```
src/lib/components/ui/alert/
├── alert.svelte               # 알림 루트
├── alert-title.svelte         # 알림 제목
├── alert-description.svelte   # 알림 설명
└── index.ts                   # 모든 알림 컴포넌트 export
```

**주요 기능:**
- 2가지 variants: default, destructive
- 에러 및 정보 메시지 표시에 최적화
- Svelte 5 runes 사용
- 접근성 지원 (role="alert")

**Export 구조 (`index.ts`):**
```typescript
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

### 13.5. 컴포넌트 사용 예제

#### Button 사용 예제

```svelte
<script lang="ts">
	import { Button } from '$lib/components/ui/button/index.js';
</script>

<Button variant="default">기본 버튼</Button>
<Button variant="outline">아웃라인 버튼</Button>
<Button variant="destructive">삭제 버튼</Button>
```

#### Card 사용 예제

```svelte
<script lang="ts">
	import * as Card from '$lib/components/ui/card/index.js';
</script>

<Card.Root>
	<Card.Header>
		<Card.Title>카드 제목</Card.Title>
		<Card.Description>카드 설명</Card.Description>
	</Card.Header>
	<Card.Content>
		<p>카드 내용</p>
	</Card.Content>
	<Card.Footer>
		<p>푸터 내용</p>
	</Card.Footer>
</Card.Root>
```

#### Alert 사용 예제

```svelte
<script lang="ts">
	import * as Alert from '$lib/components/ui/alert/index.js';
</script>

<Alert.Root variant="destructive">
	<Alert.Title>에러 발생</Alert.Title>
	<Alert.Description>문제가 발생했습니다.</Alert.Description>
</Alert.Root>
```

### 13.6. 파일 구조

실제 생성된 전체 파일 구조:

```
src/
├── lib/
│   ├── components/
│   │   └── ui/
│   │       ├── button/
│   │       │   ├── button.svelte
│   │       │   └── index.ts
│   │       ├── card/
│   │       │   ├── card.svelte
│   │       │   ├── card-header.svelte
│   │       │   ├── card-title.svelte
│   │       │   ├── card-description.svelte
│   │       │   ├── card-content.svelte
│   │       │   ├── card-footer.svelte
│   │       │   └── index.ts
│   │       └── alert/
│   │           ├── alert.svelte
│   │           ├── alert-title.svelte
│   │           ├── alert-description.svelte
│   │           └── index.ts
│   └── utils.ts                    # cn 유틸리티 함수
└── ...
```

### 13.7. TypeScript 컴파일 확인

모든 컴포넌트는 TypeScript 타입 검사를 통과합니다:

```bash
npm run check
```

**결과:**
```
✓ No errors found
```

---

## 14. 변경 이력

| 버전  | 날짜       | 작성자      | 변경 내용                                                           |
| ----- | ---------- | ----------- | ------------------------------------------------------------------- |
| 1.0.0 | 2025-01-08 | JaeHo Song  | 초기 명세서 작성                                                    |
| 1.1.0 | 2025-01-09 | JaeHo Song  | 실제 구현 내용 반영 (수동으로 생성한 UI 컴포넌트 및 패키지 정보 추가) |

**v1.0.0 (2025-01-08):**
- 초기 명세서 작성
- SED 형식 YAML 헤더 추가
- shadcn-svelte 설치 및 설정 방법 정의
- 공식 문서 기반 설치 절차 문서화
- 기본 컴포넌트 사용 예제 추가 (Button, Card, Input, Label)
- 고급 컴포넌트 사용 예제 추가 (Dialog, Select, Toast)
- 컴포넌트 커스터마이징 가이드 추가
- 검증 방법 및 문제 해결 가이드 추가
- 승인 기준 정의

**v1.1.0 (2025-01-09):**
- 섹션 13 "실제 구현 내용" 추가
- 수동으로 생성한 UI 컴포넌트 상세 정보 추가 (Button, Card, Alert)
- 설치된 패키지 정보 추가 (clsx, tailwind-merge)
- 유틸리티 함수 (cn) 코드 추가
- 실제 파일 구조 및 사용 예제 추가
