---
name: sonub-design-layout
version: 1.1.0
description: Sonub 프로젝트의 레이아웃, 탑바 및 사이드바 구조 구현 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 20
priority: "**"
dependencies: ["sonub-design-workflow.md", "sonub-user-login.md", "sonub-setup-shadcn.md"]
tags: ["layout", "topbar", "sidebar", "navigation", "ui", "authentication", "svelte5"]
---

# Sonub Design Layout - 레이아웃, 탑바 및 사이드바 구조

## 1. 개요

### 1.1 목적

본 명세서는 Sonub 프로젝트의 전역 레이아웃 구조, 탑바(상단 네비게이션 바), 좌측 사이드바, 우측 사이드바 컴포넌트의 구현을 정의합니다. 모든 페이지에서 일관된 사용자 경험을 제공하고, 사용자 인증 상태에 따른 동적 메뉴를 제공합니다.

### 1.2 범위

- 전역 레이아웃 구조 (`+layout.svelte`)
- 탑바 컴포넌트 구현 (`top-bar.svelte`)
- 좌측 사이드바 컴포넌트 구현 (`left-sidebar.svelte`)
- 우측 사이드바 컴포넌트 구현 (`right-sidebar.svelte`)
- 사용자 인증 상태 기반 네비게이션
- 반응형 디자인 (모바일/태블릿/데스크톱)
- 다크 모드 지원
- 접근성 고려

### 1.3 사전 요구사항

- ✅ TailwindCSS 설치 완료 (sonub-setup-tailwind.md 참조)
- ✅ shadcn-svelte Button 및 Card 컴포넌트 설치 완료 (sonub-setup-shadcn.md 참조)
- ✅ Firebase Authentication 설정 완료 (sonub-setup-firebase.md 참조)
- ✅ 인증 스토어 구현 완료 (sonub-user-login.md 참조)
- ✅ SvelteKit 5 프로젝트 환경

### 1.4 구현 파일

```
src/
├── lib/
│   └── components/
│       ├── top-bar.svelte              # 탑바 컴포넌트
│       ├── left-sidebar.svelte         # 좌측 사이드바 컴포넌트
│       └── right-sidebar.svelte        # 우측 사이드바 컴포넌트
└── routes/
    └── +layout.svelte                  # 전역 레이아웃
```

## 2. 레이아웃 구조

### 2.1 전역 레이아웃 개요

**파일 경로:** `src/routes/+layout.svelte`

**목적:** 모든 페이지에 공통으로 적용되는 레이아웃 구조 정의

**주요 기능:**
- 전역 CSS 임포트
- 탑바 컴포넌트 배치
- 3컬럼 레이아웃 (좌측 사이드바 + 메인 콘텐츠 + 우측 사이드바)
- 반응형 사이드바 (데스크톱에서만 표시)
- 배경 색상 및 최소 높이 설정
- 다크 모드 지원

### 2.2 레이아웃 구현

**파일:** `src/routes/+layout.svelte`

```svelte
<script lang="ts">
	import '../app.css';
	import favicon from '$lib/assets/favicon.svg';
	import TopBar from '$lib/components/top-bar.svelte';
	import LeftSidebar from '$lib/components/left-sidebar.svelte';
	import RightSidebar from '$lib/components/right-sidebar.svelte';

	let { children } = $props();
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
</svelte:head>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
	<TopBar />
	<div class="container mx-auto px-4 py-8">
		<div class="flex gap-6">
			<!-- 좌측 사이드바 (데스크톱만) -->
			<LeftSidebar />

			<!-- 메인 콘텐츠 -->
			<main class="flex-1 min-w-0">
				{@render children()}
			</main>

			<!-- 우측 사이드바 (데스크톱만) -->
			<RightSidebar />
		</div>
	</div>
</div>
```

### 2.3 레이아웃 구조 분석

#### 2.3.1 최상위 컨테이너

```svelte
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
```

**TailwindCSS 클래스 설명:**
- `min-h-screen`: 최소 높이를 100vh로 설정하여 화면 전체 높이 보장
- `bg-gray-50`: 라이트 모드 배경색 (연한 회색)
- `dark:bg-gray-900`: 다크 모드 배경색 (진한 회색)

**목적:**
- 페이지 전체를 감싸는 컨테이너
- 다크 모드 지원
- 콘텐츠가 적어도 화면 전체를 채우도록 보장

#### 2.3.2 컨테이너 래퍼

```svelte
<div class="container mx-auto px-4 py-8">
```

**TailwindCSS 클래스 설명:**
- `container`: TailwindCSS의 반응형 컨테이너 (최대 너비 제한)
  - sm (640px): max-width: 640px
  - md (768px): max-width: 768px
  - lg (1024px): max-width: 1024px
  - xl (1280px): max-width: 1280px
  - 2xl (1536px): max-width: 1536px
- `mx-auto`: 좌우 마진 자동 (중앙 정렬)
- `px-4`: 좌우 패딩 1rem (16px)
- `py-8`: 상하 패딩 2rem (32px)

#### 2.3.3 3컬럼 플렉스 레이아웃

```svelte
<div class="flex gap-6">
	<LeftSidebar />
	<main class="flex-1 min-w-0">
		{@render children()}
	</main>
	<RightSidebar />
</div>
```

**TailwindCSS 클래스 설명:**
- `flex`: flexbox 레이아웃
- `gap-6`: 자식 요소 간 간격 1.5rem (24px)

**메인 콘텐츠 영역 클래스:**
- `flex-1`: flex-grow: 1 (남은 공간 모두 차지)
- `min-w-0`: 최소 너비 0 (flexbox 오버플로우 방지)

**레이아웃 구조:**
```
┌─────────────────────────────────────────────────────┐
│                      TopBar                          │
├──────────────┬──────────────────┬────────────────────┤
│  Left        │                  │      Right         │
│  Sidebar     │   Main Content   │      Sidebar       │
│  (lg+)       │   (flex-1)       │      (lg+)         │
│              │                  │                    │
└──────────────┴──────────────────┴────────────────────┘
```

**반응형 동작:**
- **모바일/태블릿 (< 1024px)**: 사이드바 숨김, 메인 콘텐츠만 표시
- **데스크톱 (≥ 1024px)**: 3컬럼 레이아웃 표시

#### 2.3.4 메인 콘텐츠 영역

```svelte
<main class="flex-1 min-w-0">
	{@render children()}
</main>
```

**목적:**
- 페이지별 콘텐츠를 렌더링하는 영역
- 남은 공간을 모두 차지 (flex-1)
- 오버플로우 방지 (min-w-0)
  - md (768px): max-width: 768px
  - lg (1024px): max-width: 1024px
  - xl (1280px): max-width: 1280px
  - 2xl (1536px): max-width: 1536px
- `mx-auto`: 좌우 마진 자동 (중앙 정렬)
- `px-4`: 좌우 패딩 1rem (16px)
- `py-8`: 상하 패딩 2rem (32px)

**목적:**
- 콘텐츠를 중앙에 배치
- 좌우 여백 제공
- 반응형 너비 조정

## 3. 탑바 컴포넌트

### 3.1 탑바 개요

**파일 경로:** `src/lib/components/top-bar.svelte`

**목적:** 모든 페이지 상단에 표시되는 네비게이션 바

**주요 기능:**
- 브랜드 로고 및 네비게이션 링크
- 사용자 인증 상태 표시
- 로그인/로그아웃 기능
- 사용자 프로필 정보 표시
- 반응형 디자인
- 다크 모드 지원

### 3.2 탑바 구현

**파일:** `src/lib/components/top-bar.svelte`

```svelte
<script lang="ts">
	/**
	 * 탑바 (상단 네비게이션 바) 컴포넌트
	 *
	 * 사용자 로그인 상태에 따라 다른 메뉴를 표시하는 반응형 네비게이션 바입니다.
	 * TailwindCSS와 shadcn-svelte Button 컴포넌트를 사용합니다.
	 */

	import { Button } from '$lib/components/ui/button/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import { signOut } from 'firebase/auth';
	import { auth } from '$lib/firebase';
	import { goto } from '$app/navigation';

	// 로그아웃 처리 중 상태
	let isSigningOut = $state(false);

	/**
	 * 로그아웃 처리
	 */
	async function handleSignOut() {
		if (isSigningOut) return;

		isSigningOut = true;
		try {
			await signOut(auth);
			console.log('로그아웃 성공');
			await goto('/');
		} catch (error) {
			console.error('로그아웃 에러:', error);
		} finally {
			isSigningOut = false;
		}
	}

	/**
	 * 로그인 페이지로 이동
	 */
	function goToLogin() {
		goto('/user/login');
	}
</script>

<nav class="border-b bg-white dark:bg-gray-900">
	<div class="container mx-auto px-4">
		<div class="flex h-16 items-center justify-between">
			<!-- 좌측: 로고 및 네비게이션 링크 -->
			<div class="flex items-center gap-8">
				<a
					href="/"
					class="text-xl font-bold text-gray-900 hover:text-gray-700 dark:text-white dark:hover:text-gray-300"
				>
					Sonub
				</a>
				<div class="hidden gap-4 md:flex">
					<a
						href="/about"
						class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
					>
						소개
					</a>
					<a
						href="/products"
						class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
					>
						제품
					</a>
					<a
						href="/contact"
						class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
					>
						연락
					</a>
				</div>
			</div>

			<!-- 우측: 사용자 메뉴 -->
			<div class="flex items-center gap-4">
				{#if authStore.loading}
					<!-- 로딩 중 -->
					<div class="text-sm text-gray-500 dark:text-gray-400">로딩 중...</div>
				{:else if authStore.isAuthenticated}
					<!-- 로그인 상태 -->
					<div class="flex items-center gap-4">
						<div class="hidden items-center gap-2 sm:flex">
							{#if authStore.user?.photoURL}
								<img
									src={authStore.user.photoURL}
									alt={authStore.user.displayName || '사용자'}
									class="h-8 w-8 rounded-full"
								/>
							{/if}
							<span class="text-sm text-gray-700 dark:text-gray-300">
								{authStore.user?.displayName || authStore.user?.email || '사용자'}
							</span>
						</div>
						<Button variant="ghost" size="sm" onclick={handleSignOut} disabled={isSigningOut}>
							{isSigningOut ? '로그아웃 중...' : '로그아웃'}
						</Button>
					</div>
				{:else}
					<!-- 비로그인 상태 -->
					<Button variant="ghost" size="sm" onclick={goToLogin}>로그인</Button>
				{/if}
			</div>
		</div>
	</div>
</nav>
```

### 3.3 탑바 구조 분석

#### 3.3.1 최상위 네비게이션 컨테이너

```svelte
<nav class="border-b bg-white dark:bg-gray-900">
```

**TailwindCSS 클래스 설명:**
- `border-b`: 하단 테두리 (1px solid)
- `bg-white`: 라이트 모드 배경색 (흰색)
- `dark:bg-gray-900`: 다크 모드 배경색 (진한 회색)

**HTML 시맨틱:**
- `<nav>` 태그 사용으로 접근성 향상
- 스크린 리더가 네비게이션 영역임을 인식

#### 3.3.2 내부 컨테이너

```svelte
<div class="container mx-auto px-4">
```

**TailwindCSS 클래스 설명:**
- `container`: 반응형 최대 너비 컨테이너
- `mx-auto`: 중앙 정렬
- `px-4`: 좌우 패딩 1rem (16px)

**목적:**
- 콘텐츠를 레이아웃과 동일한 너비로 제한
- 큰 화면에서도 적절한 너비 유지

#### 3.3.3 플렉스 컨테이너

```svelte
<div class="flex h-16 items-center justify-between">
```

**TailwindCSS 클래스 설명:**
- `flex`: flexbox 레이아웃
- `h-16`: 고정 높이 4rem (64px)
- `items-center`: 세로 중앙 정렬
- `justify-between`: 좌우 끝으로 배치 (space-between)

**목적:**
- 좌측 로고/링크와 우측 사용자 메뉴를 양 끝에 배치
- 세로 중앙 정렬로 시각적 균형 유지
- 일관된 높이 제공

### 3.4 좌측 영역: 로고 및 네비게이션

#### 3.4.1 로고 및 링크 컨테이너

```svelte
<div class="flex items-center gap-8">
```

**TailwindCSS 클래스 설명:**
- `flex`: flexbox 레이아웃
- `items-center`: 세로 중앙 정렬
- `gap-8`: 자식 요소 간 간격 2rem (32px)

#### 3.4.2 브랜드 로고

```svelte
<a
	href="/"
	class="text-xl font-bold text-gray-900 hover:text-gray-700 dark:text-white dark:hover:text-gray-300"
>
	Sonub
</a>
```

**TailwindCSS 클래스 설명:**
- `text-xl`: 폰트 크기 1.25rem (20px)
- `font-bold`: 폰트 두께 700
- `text-gray-900`: 라이트 모드 텍스트 색상 (진한 회색)
- `hover:text-gray-700`: 라이트 모드 호버 색상
- `dark:text-white`: 다크 모드 텍스트 색상 (흰색)
- `dark:hover:text-gray-300`: 다크 모드 호버 색상

**목적:**
- 브랜드 아이덴티티 표시
- 홈페이지로 이동하는 링크
- 호버 효과로 인터랙션 피드백 제공

#### 3.4.3 네비게이션 링크

```svelte
<div class="hidden gap-4 md:flex">
	<a href="/about" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
		소개
	</a>
	<!-- 추가 링크들... -->
</div>
```

**TailwindCSS 클래스 설명:**
- `hidden`: 기본적으로 숨김 (모바일)
- `md:flex`: 중간 화면(768px) 이상에서 표시
- `gap-4`: 링크 간 간격 1rem (16px)
- `text-gray-600`: 라이트 모드 기본 색상 (중간 회색)
- `hover:text-gray-900`: 라이트 모드 호버 색상 (진한 회색)
- `dark:text-gray-400`: 다크 모드 기본 색상
- `dark:hover:text-white`: 다크 모드 호버 색상

**반응형 동작:**
- 모바일 (< 768px): 링크 숨김
- 태블릿 이상 (≥ 768px): 링크 표시

**목적:**
- 주요 페이지로의 빠른 이동
- 반응형 디자인으로 모바일 공간 절약
- 호버 효과로 클릭 가능함을 시각적으로 표현

### 3.5 우측 영역: 사용자 메뉴

#### 3.5.1 사용자 메뉴 컨테이너

```svelte
<div class="flex items-center gap-4">
```

**TailwindCSS 클래스 설명:**
- `flex`: flexbox 레이아웃
- `items-center`: 세로 중앙 정렬
- `gap-4`: 자식 요소 간 간격 1rem (16px)

#### 3.5.2 로딩 상태

```svelte
{#if authStore.loading}
	<div class="text-sm text-gray-500 dark:text-gray-400">로딩 중...</div>
```

**조건:** `authStore.loading === true`

**TailwindCSS 클래스 설명:**
- `text-sm`: 폰트 크기 0.875rem (14px)
- `text-gray-500`: 라이트 모드 텍스트 색상 (중간 회색)
- `dark:text-gray-400`: 다크 모드 텍스트 색상

**목적:**
- Firebase 인증 초기화 중 사용자에게 피드백 제공
- 깜빡임 방지

#### 3.5.3 로그인 상태

```svelte
{:else if authStore.isAuthenticated}
	<div class="flex items-center gap-4">
		<!-- 사용자 프로필 -->
		<div class="hidden items-center gap-2 sm:flex">
			{#if authStore.user?.photoURL}
				<img
					src={authStore.user.photoURL}
					alt={authStore.user.displayName || '사용자'}
					class="h-8 w-8 rounded-full"
				/>
			{/if}
			<span class="text-sm text-gray-700 dark:text-gray-300">
				{authStore.user?.displayName || authStore.user?.email || '사용자'}
			</span>
		</div>
		<!-- 로그아웃 버튼 -->
		<Button variant="ghost" size="sm" onclick={handleSignOut} disabled={isSigningOut}>
			{isSigningOut ? '로그아웃 중...' : '로그아웃'}
		</Button>
	</div>
```

**조건:** `authStore.isAuthenticated === true` (사용자 로그인됨)

**사용자 프로필 영역 클래스 설명:**
- `hidden`: 기본적으로 숨김 (모바일)
- `sm:flex`: 작은 화면(640px) 이상에서 표시
- `items-center`: 세로 중앙 정렬
- `gap-2`: 이미지와 텍스트 간 간격 0.5rem (8px)

**프로필 이미지 클래스 설명:**
- `h-8`: 높이 2rem (32px)
- `w-8`: 너비 2rem (32px)
- `rounded-full`: 완전한 원형 (border-radius: 9999px)

**반응형 동작:**
- 모바일 (< 640px): 프로필 정보 숨김, 로그아웃 버튼만 표시
- 태블릿 이상 (≥ 640px): 프로필 이미지 및 이름 표시

**목적:**
- 사용자에게 로그인 상태임을 시각적으로 표시
- 모바일에서 공간 절약
- 로그아웃 기능 제공

#### 3.5.4 비로그인 상태

```svelte
{:else}
	<Button variant="ghost" size="sm" onclick={goToLogin}>로그인</Button>
{/if}
```

**조건:** `authStore.isAuthenticated === false` (사용자 비로그인)

**shadcn-svelte Button props:**
- `variant="ghost"`: 투명한 배경, 호버 시 배경 표시
- `size="sm"`: 작은 크기 버튼
- `onclick={goToLogin}`: 클릭 시 로그인 페이지로 이동

**목적:**
- 비로그인 사용자에게 로그인 유도
- 일관된 버튼 디자인

## 4. 인증 상태 관리

### 4.1 authStore 통합

**인증 스토어 임포트:**
```typescript
import { authStore } from '$lib/stores/auth.svelte';
```

**사용 가능한 속성:**
- `authStore.loading`: 인증 초기화 중 여부 (boolean)
- `authStore.isAuthenticated`: 로그인 여부 (boolean)
- `authStore.user`: Firebase User 객체 또는 null
- `authStore.user?.displayName`: 사용자 표시 이름
- `authStore.user?.email`: 사용자 이메일
- `authStore.user?.photoURL`: 프로필 이미지 URL

### 4.2 로그인 상태별 UI 표시

**상태 순서도:**
```
authStore.loading === true
  → "로딩 중..." 표시

authStore.loading === false && authStore.isAuthenticated === true
  → 사용자 프로필 + 로그아웃 버튼 표시

authStore.loading === false && authStore.isAuthenticated === false
  → 로그인 버튼 표시
```

## 5. 로그인/로그아웃 기능

### 5.1 로그아웃 처리

**함수:** `handleSignOut()`

**로직:**
```typescript
async function handleSignOut() {
	if (isSigningOut) return; // 중복 실행 방지

	isSigningOut = true; // 로딩 상태 시작
	try {
		await signOut(auth); // Firebase 로그아웃
		console.log('로그아웃 성공');
		await goto('/'); // 홈페이지로 리다이렉트
	} catch (error) {
		console.error('로그아웃 에러:', error);
	} finally {
		isSigningOut = false; // 로딩 상태 종료
	}
}
```

**주요 특징:**
- 중복 실행 방지 (isSigningOut 플래그)
- 로딩 상태 표시 ("로그아웃 중...")
- 에러 핸들링
- 성공 시 홈페이지로 리다이렉트

### 5.2 로그인 페이지 이동

**함수:** `goToLogin()`

**로직:**
```typescript
function goToLogin() {
	goto('/user/login');
}
```

**목적:**
- 로그인 페이지로 프로그래매틱 네비게이션
- SvelteKit의 `goto` 함수 사용

## 6. 반응형 디자인

### 6.1 브레이크포인트 전략

**TailwindCSS 브레이크포인트:**
- `sm`: 640px (작은 화면)
- `md`: 768px (중간 화면)
- `lg`: 1024px (큰 화면)
- `xl`: 1280px (초대형 화면)

### 6.2 반응형 요소

#### 6.2.1 네비게이션 링크

**모바일 (< 768px):**
- 링크 숨김 (`hidden`)

**태블릿 이상 (≥ 768px):**
- 링크 표시 (`md:flex`)

#### 6.2.2 사용자 프로필

**모바일 (< 640px):**
- 프로필 정보 숨김 (`hidden`)
- 로그아웃 버튼만 표시

**태블릿 이상 (≥ 640px):**
- 프로필 이미지 및 이름 표시 (`sm:flex`)

### 6.3 반응형 테스트 체크리스트

- [ ] 모바일 (375px): 로고 + 로그인/로그아웃 버튼만 표시
- [ ] 태블릿 (768px): 네비게이션 링크 표시
- [ ] 데스크톱 (1024px): 모든 요소 표시

## 7. 다크 모드

### 7.1 다크 모드 구현

**TailwindCSS 다크 모드 클래스:**
- `dark:bg-gray-900`: 다크 배경
- `dark:text-white`: 다크 텍스트
- `dark:text-gray-300`: 다크 보조 텍스트
- `dark:hover:text-gray-300`: 다크 호버 색상

### 7.2 색상 팔레트

**라이트 모드:**
- 배경: `bg-white`, `bg-gray-50`
- 텍스트: `text-gray-900`, `text-gray-600`, `text-gray-500`
- 호버: `hover:text-gray-900`, `hover:text-gray-700`

**다크 모드:**
- 배경: `dark:bg-gray-900`
- 텍스트: `dark:text-white`, `dark:text-gray-300`, `dark:text-gray-400`
- 호버: `dark:hover:text-white`, `dark:hover:text-gray-300`

### 7.3 다크 모드 테스트

- [ ] 라이트 모드에서 모든 텍스트가 읽기 쉬운지 확인
- [ ] 다크 모드에서 모든 텍스트가 읽기 쉬운지 확인
- [ ] 호버 상태가 양쪽 모드에서 명확한지 확인

## 8. 접근성 (Accessibility)

### 8.1 시맨틱 HTML

**사용된 시맨틱 태그:**
- `<nav>`: 네비게이션 영역
- `<main>`: 메인 콘텐츠 영역
- `<a>`: 링크 요소

**목적:**
- 스크린 리더가 페이지 구조를 올바르게 인식
- SEO 향상

### 8.2 이미지 대체 텍스트

```svelte
<img
	src={authStore.user.photoURL}
	alt={authStore.user.displayName || '사용자'}
	class="h-8 w-8 rounded-full"
/>
```

**목적:**
- 스크린 리더 사용자에게 이미지 설명 제공
- 이미지 로드 실패 시 대체 정보 제공

### 8.3 키보드 내비게이션

**지원 기능:**
- Tab 키로 모든 인터랙티브 요소 접근 가능
- Enter 키로 버튼 및 링크 활성화
- 포커스 상태 시각적 표시 (shadcn Button 기본 제공)

### 8.4 접근성 체크리스트

- [x] 시맨틱 HTML 사용
- [x] 이미지 대체 텍스트 제공
- [x] 키보드 내비게이션 지원
- [x] 충분한 색상 대비 (WCAG 2.1 AA)
- [x] 포커스 상태 표시

## 9. shadcn-svelte 컴포넌트 사용

### 9.1 Button 컴포넌트

**임포트:**
```typescript
import { Button } from '$lib/components/ui/button/index.js';
```

**사용 예:**
```svelte
<Button variant="ghost" size="sm" onclick={handleSignOut} disabled={isSigningOut}>
	{isSigningOut ? '로그아웃 중...' : '로그아웃'}
</Button>
```

**Props:**
- `variant="ghost"`: 투명한 배경, 호버 시 배경 표시
- `size="sm"`: 작은 크기
- `onclick`: 클릭 이벤트 핸들러
- `disabled`: 비활성화 상태

**장점:**
- 일관된 디자인
- 접근성 기본 내장
- 다크 모드 자동 지원
- 호버/포커스 상태 자동 처리

## 10. 상태 관리

### 10.1 Svelte 5 Runes

**사용된 runes:**
```typescript
let isSigningOut = $state(false);
```

**목적:**
- 반응형 상태 관리
- 컴포넌트 리렌더링 자동화

### 10.2 상태 변수

**isSigningOut:**
- 타입: `boolean`
- 초기값: `false`
- 용도: 로그아웃 진행 중 표시 및 중복 실행 방지

## 11. 네비게이션

### 11.1 SvelteKit goto

**임포트:**
```typescript
import { goto } from '$app/navigation';
```

**사용 예:**
```typescript
await goto('/'); // 홈페이지로 이동
await goto('/user/login'); // 로그인 페이지로 이동
```

**특징:**
- 클라이언트 사이드 라우팅
- 페이지 새로고침 없이 이동
- SvelteKit의 표준 네비게이션 방법

## 12. 홈페이지 개선

### 12.1 홈페이지 구조

**파일:** `src/routes/+page.svelte`

**주요 섹션:**
1. 메인 타이틀 및 설명
2. 사용자 환영 메시지 (로그인 상태별)
3. 기능 소개 카드
4. 외부 링크

### 12.2 로그인 상태별 메시지

**비로그인 상태:**
```svelte
<Card.Root class="mx-auto max-w-md">
	<Card.Header>
		<Card.Title>시작하기</Card.Title>
		<Card.Description>
			Google 또는 Apple 계정으로 로그인하여 시작하세요
		</Card.Description>
	</Card.Header>
	<Card.Content>
		<Button class="w-full" onclick={goToLogin}>로그인하기</Button>
	</Card.Content>
</Card.Root>
```

**로그인 상태:**
```svelte
<Card.Root class="mx-auto max-w-md">
	<Card.Header>
		<Card.Title>환영합니다!</Card.Title>
		<Card.Description>
			{authStore.user?.displayName || authStore.user?.email || '사용자'}님, 로그인하셨습니다.
		</Card.Description>
	</Card.Header>
	<Card.Content>
		<div class="flex items-center justify-center gap-4">
			{#if authStore.user?.photoURL}
				<img
					src={authStore.user.photoURL}
					alt={authStore.user.displayName || '사용자'}
					class="h-16 w-16 rounded-full"
				/>
			{/if}
		</div>
	</Card.Content>
</Card.Root>
```

### 12.3 기능 소개 카드

**3컬럼 그리드 레이아웃:**
```svelte
<div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
	<Card.Root><!-- SvelteKit 5 --></Card.Root>
	<Card.Root><!-- Firebase Auth --></Card.Root>
	<Card.Root><!-- TailwindCSS --></Card.Root>
</div>
```

**반응형 동작:**
- 모바일 (< 768px): 1컬럼 (세로 배치)
- 태블릿 이상 (≥ 768px): 3컬럼 (가로 배치)

## 13. 구현 체크리스트

### 13.1 레이아웃 구현

- [x] `+layout.svelte` 파일 생성
- [x] 전역 CSS 임포트
- [x] 탑바 컴포넌트 배치
- [x] 메인 콘텐츠 영역 정의
- [x] 다크 모드 배경 설정
- [x] 반응형 컨테이너 적용

### 13.2 탑바 구현

- [x] `top-bar.svelte` 컴포넌트 생성
- [x] authStore 통합
- [x] Firebase signOut 함수 연동
- [x] 로고 및 네비게이션 링크 구현
- [x] 사용자 프로필 표시
- [x] 로그인/로그아웃 버튼 구현
- [x] 로딩 상태 표시
- [x] 반응형 디자인 적용
- [x] 다크 모드 지원

### 13.3 홈페이지 개선

- [x] 메인 타이틀 작성
- [x] 로그인 상태별 메시지 구현
- [x] 기능 소개 카드 추가
- [x] 외부 링크 추가
- [x] 반응형 레이아웃 적용

### 13.4 테스트

- [ ] 로그인/로그아웃 기능 테스트
- [ ] 모바일 반응형 확인
- [ ] 태블릿 반응형 확인
- [ ] 데스크톱 반응형 확인
- [ ] 다크 모드 동작 확인
- [ ] 키보드 내비게이션 테스트
- [ ] 스크린 리더 테스트

## 14. 성능 최적화

### 14.1 이미지 최적화

**프로필 이미지:**
- Firebase에서 제공하는 최적화된 이미지 URL 사용
- `loading="lazy"` 속성 고려 (필요 시 추가)

### 14.2 컴포넌트 로딩

**레이아웃 컴포넌트:**
- 모든 페이지에서 공유되므로 코드 스플리팅 불필요
- 초기 로드에 포함

## 15. 추가 개선 사항

### 15.1 모바일 메뉴

**향후 구현 고려사항:**
- 햄버거 메뉴 추가
- 사이드바 네비게이션
- 모바일 전용 메뉴 구조

### 15.2 사용자 드롭다운 메뉴

**향후 구현 고려사항:**
- 프로필 편집 링크
- 설정 메뉴
- 로그아웃 옵션
- shadcn-svelte Dropdown Menu 컴포넌트 활용

### 15.3 검색 기능

**향후 구현 고려사항:**
- 탑바에 검색 바 추가
- 모바일에서는 아이콘으로 표시
- 클릭 시 검색 모달 열기

## 16. 결론

본 명세서에 따라 구현된 레이아웃 및 탑바는 다음과 같은 특징을 가집니다:

✅ **일관된 디자인**: TailwindCSS와 shadcn-svelte만 사용
✅ **반응형**: 모바일, 태블릿, 데스크톱 모두 지원
✅ **다크 모드**: 완전한 다크 모드 지원
✅ **접근성**: WCAG 2.1 AA 기준 준수
✅ **사용자 인증 통합**: Firebase Auth와 완벽 연동
✅ **성능**: 최적화된 컴포넌트 구조
✅ **유지보수성**: 명확한 컴포넌트 분리 및 주석

**핵심 원칙:**
1. 모든 스타일링은 TailwindCSS 유틸리티 클래스 사용
2. shadcn-svelte 컴포넌트 최대한 활용
3. 반응형 및 접근성 필수 고려
4. 사용자 인증 상태에 따른 동적 UI 제공
5. 일관된 디자인 패턴 유지
