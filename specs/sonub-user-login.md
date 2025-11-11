---
name: sonub-user-login
version: 1.1.0
description: Firebase를 사용한 Google 및 Apple 소셜 로그인 기능 구현 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 30
priority: "**"
dependencies:
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-firebase-database-structure.md
tags: ["firebase", "authentication", "google-login", "apple-login", "oauth", "svelte5"]
---

# Sonub User Login - Google 및 Apple 소셜 로그인

## 1. 개요

### 1.1 목적

본 명세서는 Sonub 프로젝트의 SvelteKit 5 환경에서 Firebase Authentication을 사용하여 Google 및 Apple 소셜 로그인 기능을 구현하기 위한 완전한 절차를 정의합니다.

### 1.2 범위

- Google OAuth 2.0 로그인 구현
- Apple Sign In 로그인 구현
- 다국어 지원 (ko, ja, zh, en)
- 사용자 세션 관리
- 에러 핸들링 및 로딩 상태 관리
- 로그인 후 리다이렉션
- shadcn-svelte UI 컴포넌트 활용

### 1.3 사전 요구사항

- ✅ Firebase 프로젝트 설정 완료 (sonub-setup-firebase.md 참조)
- ✅ Tailwind CSS 설치 완료 (sonub-setup-tailwind.md 참조)
- ✅ SvelteKit 5 프로젝트 환경
- ✅ Firebase JS SDK 11.0.0 이상 설치

### 1.4 필수 패키지

다음 패키지들이 필요합니다:

```json
{
  "dependencies": {
    "firebase": "^12.5.0",
    "clsx": "^2.1.0",
    "tailwind-merge": "^2.2.1"
  }
}
```

- **firebase**: Firebase Authentication SDK
- **clsx**: 클래스명 조건부 결합 유틸리티
- **tailwind-merge**: Tailwind CSS 클래스 충돌 방지

### 1.5 제외 사항

- ❌ 이메일/비밀번호 로그인 (구현하지 않음)
- ❌ 전화번호 인증
- ❌ 기타 소셜 로그인 (Facebook, Twitter 등)

## 2. Firebase Console 설정

### 2.1 Google 로그인 활성화

**설정 위치:** Firebase Console > Authentication > Sign-in method

```
1. Firebase Console 접속: https://console.firebase.google.com
2. 프로젝트 선택: "sonub"
3. 좌측 메뉴: "빌드" > "Authentication" 클릭
4. "Sign-in method" 탭 선택
5. "Google" 제공업체 클릭
6. "사용 설정" 토글 ON
7. 프로젝트 지원 이메일 선택
8. "저장" 클릭
```

**확인 사항:**
- ✅ Google 제공업체 상태가 "사용 설정됨"으로 표시
- ✅ 프로젝트 지원 이메일이 설정됨

### 2.2 Apple 로그인 활성화

**설정 위치:** Firebase Console > Authentication > Sign-in method

```
1. "Sign-in method" 탭에서 "Apple" 제공업체 클릭
2. "사용 설정" 토글 ON
3. (선택) Services ID 설정:
   - Apple Developer Console에서 Services ID 생성 필요
   - 웹 환경에서 Apple 로그인 사용 시 필요
   - 개발 중에는 기본 설정으로 진행 가능
4. "저장" 클릭
```

**참고:** Apple 로그인은 Firebase가 자동으로 생성한 익명 Services ID를 사용할 수 있습니다. 프로덕션 배포 시 Apple Developer 계정에서 정식 Services ID를 생성하여 등록해야 합니다.

### 2.3 승인된 도메인 확인

**설정 위치:** Firebase Console > Authentication > Settings > Authorized domains

```
개발 환경:
- localhost (기본 포함)
- 127.0.0.1 (기본 포함)

프로덕션 환경:
- 실제 배포 도메인 추가 필요 (예: sonub.com)
```

## 3. 파일 구조

본 명세서에 따라 생성되는 파일들:

```
src/
├── lib/
│   ├── components/
│   │   ├── ui/                         # UI 컴포넌트 라이브러리
│   │   │   ├── button/
│   │   │   │   ├── button.svelte      # 버튼 컴포넌트
│   │   │   │   └── index.ts           # 버튼 export
│   │   │   ├── card/
│   │   │   │   ├── card.svelte        # 카드 루트
│   │   │   │   ├── card-header.svelte # 카드 헤더
│   │   │   │   ├── card-title.svelte  # 카드 제목
│   │   │   │   ├── card-description.svelte  # 카드 설명
│   │   │   │   ├── card-content.svelte     # 카드 콘텐츠
│   │   │   │   ├── card-footer.svelte      # 카드 푸터
│   │   │   │   └── index.ts           # 카드 export
│   │   │   └── alert/
│   │   │       ├── alert.svelte        # 알림 루트
│   │   │       ├── alert-title.svelte  # 알림 제목
│   │   │       ├── alert-description.svelte  # 알림 설명
│   │   │       └── index.ts           # 알림 export
│   │   └── user-login.svelte          # 로그인 컴포넌트
│   ├── stores/
│   │   └── auth.svelte.ts              # 인증 상태 관리 스토어
│   └── utils/
│       ├── auth-helpers.ts             # 인증 헬퍼 함수
│       └── utils.ts                    # 유틸리티 함수 (cn)
└── routes/
    └── user/
        └── login/
            └── +page.svelte            # 로그인 페이지
```

## 4. 설치

### 4.1 필수 패키지 설치

Firebase와 UI 컴포넌트에 필요한 패키지를 설치합니다.

```bash
# Firebase (이미 설치되어 있을 경우 skip)
npm install firebase

# UI 유틸리티 패키지
npm install clsx tailwind-merge
```

**설치 확인:**

```bash
npm list firebase clsx tailwind-merge
```

**예상 출력:**
```
sonub@0.0.1 /Users/thruthesky/apps/sonub
├── firebase@12.5.0
├── clsx@2.1.0
└── tailwind-merge@2.2.1
```

### 4.2 유틸리티 함수 생성

UI 컴포넌트에서 사용할 `cn` 유틸리티 함수를 생성합니다.

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

### 4.3 UI 컴포넌트 생성

로그인 화면에 필요한 UI 컴포넌트들을 생성합니다.

#### 4.3.1 Button 컴포넌트

**파일 경로:** `src/lib/components/ui/button/button.svelte`

**전체 코드는 실제 구현 파일을 참조하세요.**

**주요 기능:**
- 다양한 variant 지원 (default, destructive, outline, secondary, ghost, link)
- 다양한 size 지원 (default, sm, lg, icon)
- Svelte 5 runes ($props, Snippet) 사용
- Tailwind CSS 기반 스타일링

**파일 경로:** `src/lib/components/ui/button/index.ts`

```typescript
/**
 * Button 컴포넌트 export
 */

import Button from './button.svelte';

export { Button };
```

#### 4.3.2 Card 컴포넌트

**파일들:**
- `src/lib/components/ui/card/card.svelte` - 카드 루트 컨테이너
- `src/lib/components/ui/card/card-header.svelte` - 카드 헤더
- `src/lib/components/ui/card/card-title.svelte` - 카드 제목
- `src/lib/components/ui/card/card-description.svelte` - 카드 설명
- `src/lib/components/ui/card/card-content.svelte` - 카드 콘텐츠
- `src/lib/components/ui/card/card-footer.svelte` - 카드 푸터
- `src/lib/components/ui/card/index.ts` - 모든 카드 컴포넌트 export

**주요 기능:**
- 구조화된 카드 레이아웃
- 헤더, 콘텐츠, 푸터 분리
- Svelte 5 runes 사용
- 반응형 디자인

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

#### 4.3.3 Alert 컴포넌트

**파일들:**
- `src/lib/components/ui/alert/alert.svelte` - 알림 루트
- `src/lib/components/ui/alert/alert-title.svelte` - 알림 제목
- `src/lib/components/ui/alert/alert-description.svelte` - 알림 설명
- `src/lib/components/ui/alert/index.ts` - 모든 알림 컴포넌트 export

**주요 기능:**
- 기본 및 위험(destructive) variant 지원
- 에러 메시지 표시에 최적화
- Svelte 5 runes 사용
- 접근성 지원 (role="alert")

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

**참고:** 각 UI 컴포넌트의 전체 소스 코드는 프로젝트의 `src/lib/components/ui/` 디렉토리에서 확인할 수 있습니다. 모든 컴포넌트는 Svelte 5 문법을 사용하며, `Snippet` 타입을 통해 children을 지원합니다.

## 5. 구현

### 5.1 Firebase 초기화 확인

Firebase는 이미 `sonub-setup-firebase.md`에 따라 초기화되어 있어야 합니다.

**파일 경로:** `src/lib/firebase.ts`

**확인 사항:**
- ✅ Firebase 앱 초기화
- ✅ Authentication 인스턴스 export
- ✅ 환경 변수 설정 (.env 파일)

### 5.2 인증 헬퍼 함수 작성

**파일 경로:** `src/lib/utils/auth-helpers.ts`

**목적:** 로그인 관련 유틸리티 함수 제공

**내용:**

```typescript
/**
 * Firebase Authentication 헬퍼 함수
 *
 * 이 파일은 Google 및 Apple 로그인에 필요한 유틸리티 함수를 제공합니다.
 */

import { auth } from '$lib/firebase';
import {
	GoogleAuthProvider,
	OAuthProvider,
	signInWithPopup,
	signOut as firebaseSignOut,
	type UserCredential
} from 'firebase/auth';

/**
 * 브라우저 언어 코드를 Firebase 지원 언어로 변환
 *
 * 지원 언어: ko, ja, zh
 * 기본 언어: en
 *
 * @returns {string} 언어 코드 (ko, ja, zh, en)
 */
export function getAuthLanguage(): string {
	// 브라우저 언어 가져오기
	const browserLang = navigator.language.split('-')[0].toLowerCase();

	// 지원 언어 목록
	const supportedLanguages = ['ko', 'ja', 'zh'];

	// 지원 언어인 경우 해당 언어 반환, 아니면 영어 반환
	return supportedLanguages.includes(browserLang) ? browserLang : 'en';
}

/**
 * Google 로그인 처리
 *
 * Firebase Authentication을 사용하여 Google 계정으로 로그인합니다.
 * 팝업 방식을 사용하며, 사용자 언어 설정을 적용합니다.
 *
 * @returns {Promise<UserCredential>} 사용자 인증 정보
 * @throws {Error} 로그인 실패 시 에러 발생
 */
export async function signInWithGoogle(): Promise<UserCredential> {
	try {
		// Google Provider 생성
		const provider = new GoogleAuthProvider();

		// 언어 설정
		const locale = getAuthLanguage();
		provider.setCustomParameters({
			locale: locale
		});

		// 팝업 방식으로 로그인
		const result = await signInWithPopup(auth, provider);

		console.log('Google 로그인 성공:', result.user.uid);
		return result;
	} catch (error: any) {
		console.error('Google 로그인 실패:', error);
		throw error;
	}
}

/**
 * Apple 로그인 처리
 *
 * Firebase Authentication을 사용하여 Apple 계정으로 로그인합니다.
 * 팝업 방식을 사용하며, 사용자 언어 설정을 적용합니다.
 *
 * @returns {Promise<UserCredential>} 사용자 인증 정보
 * @throws {Error} 로그인 실패 시 에러 발생
 */
export async function signInWithApple(): Promise<UserCredential> {
	try {
		// Apple Provider 생성
		const provider = new OAuthProvider('apple.com');

		// 언어 설정
		const locale = getAuthLanguage();
		provider.setCustomParameters({
			locale: locale
		});

		// 사용자 정보 요청 스코프 설정
		provider.addScope('email');
		provider.addScope('name');

		// 팝업 방식으로 로그인
		const result = await signInWithPopup(auth, provider);

		console.log('Apple 로그인 성공:', result.user.uid);
		return result;
	} catch (error: any) {
		console.error('Apple 로그인 실패:', error);
		throw error;
	}
}

/**
 * 로그아웃 처리
 *
 * Firebase Authentication에서 현재 사용자를 로그아웃합니다.
 *
 * @returns {Promise<void>}
 * @throws {Error} 로그아웃 실패 시 에러 발생
 */
export async function signOut(): Promise<void> {
	try {
		await firebaseSignOut(auth);
		console.log('로그아웃 성공');
	} catch (error: any) {
		console.error('로그아웃 실패:', error);
		throw error;
	}
}

/**
 * Firebase 에러 코드를 사용자 친화적인 메시지로 변환
 *
 * @param {string} errorCode - Firebase 에러 코드
 * @param {string} provider - 로그인 제공자 ('google' | 'apple')
 * @returns {string} 한글 에러 메시지
 */
export function getAuthErrorMessage(errorCode: string, provider: 'google' | 'apple'): string {
	const providerName = provider === 'google' ? 'Google' : 'Apple';

	switch (errorCode) {
		case 'auth/popup-closed-by-user':
			return '로그인 창이 닫혔습니다. 다시 시도해주세요.';
		case 'auth/popup-blocked':
			return '팝업이 차단되었습니다. 팝업 차단을 해제하고 다시 시도해주세요.';
		case 'auth/cancelled-popup-request':
			return '로그인 요청이 취소되었습니다.';
		case 'auth/account-exists-with-different-credential':
			return `이미 다른 방법으로 가입된 이메일입니다. 다른 로그인 방법을 사용해주세요.`;
		case 'auth/invalid-credential':
			return '인증 정보가 올바르지 않습니다.';
		case 'auth/operation-not-allowed':
			return `${providerName} 로그인이 활성화되지 않았습니다. 관리자에게 문의하세요.`;
		case 'auth/user-disabled':
			return '비활성화된 계정입니다. 관리자에게 문의하세요.';
		case 'auth/network-request-failed':
			return '네트워크 연결을 확인하고 다시 시도해주세요.';
		default:
			return `${providerName} 로그인에 실패했습니다. 다시 시도해주세요.`;
	}
}
```

### 5.3 인증 상태 관리 스토어

**파일 경로:** `src/lib/stores/auth.svelte.ts`

**목적:** 전역 인증 상태 관리

**상세 내용:**

AuthStore의 전체 구현, API, 사용 예제는 [sonub-store-auth.md](./sonub-store-auth.md)를 참조하세요.

**주요 기능:**
- `onAuthStateChanged` 리스너 자동 등록
- Firebase Auth → RTDB 프로필 동기화 (`syncUserProfile`)
- 관리자 목록 로드 (`loadAdminList`)
- `isAdmin` getter로 권한 확인

**사용 예:**
```typescript
import { authStore } from '$lib/stores/auth.svelte';

// 현재 사용자 확인
const user = authStore.user;
const isLoggedIn = authStore.isAuthenticated;
const isAdmin = authStore.isAdmin;
```

### 5.4 로그인 컴포넌트 작성

**파일 경로:** `src/lib/components/user-login.svelte`

**목적:** Google 및 Apple 로그인 UI 및 로직 제공

**내용:**

```svelte
<script lang="ts">
	/**
	 * 사용자 로그인 컴포넌트
	 *
	 * Google 및 Apple 소셜 로그인 기능을 제공하는 컴포넌트입니다.
	 * shadcn-svelte UI 컴포넌트를 사용하여 일관된 디자인을 제공합니다.
	 */

	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import * as Alert from '$lib/components/ui/alert/index.js';
	import { cn } from '$lib/utils.js';
	import { signInWithGoogle, signInWithApple, getAuthErrorMessage } from '$lib/utils/auth-helpers';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	import type { HTMLAttributes } from 'svelte/elements';

	// Props: 컴포넌트의 추가 클래스명과 기타 HTML 속성
	let { class: className, ...restProps }: HTMLAttributes<HTMLDivElement> = $props();

	// 로딩 상태 관리
	let googleLoading = $state(false);
	let appleLoading = $state(false);

	// 에러 메시지 상태
	let errorMessage = $state<string | null>(null);

	/**
	 * 로그인 진행 중 여부
	 */
	const isLoading = $derived(googleLoading || appleLoading);

	/**
	 * Google 로그인 처리
	 */
	async function handleGoogleLogin() {
		// 이미 로그인 중이면 중복 실행 방지
		if (isLoading) return;

		// 상태 초기화
		googleLoading = true;
		errorMessage = null;

		try {
			// Google 로그인 실행
			await signInWithGoogle();

			// 로그인 성공 시 홈페이지로 리다이렉트
			// authStore의 onAuthStateChanged에서 자동으로 user 상태 업데이트됨
			await goto('/');
		} catch (error: any) {
			console.error('Google 로그인 에러:', error);

			// 에러 메시지 표시
			errorMessage = getAuthErrorMessage(error.code, 'google');
		} finally {
			googleLoading = false;
		}
	}

	/**
	 * Apple 로그인 처리
	 */
	async function handleAppleLogin() {
		// 이미 로그인 중이면 중복 실행 방지
		if (isLoading) return;

		// 상태 초기화
		appleLoading = true;
		errorMessage = null;

		try {
			// Apple 로그인 실행
			await signInWithApple();

			// 로그인 성공 시 홈페이지로 리다이렉트
			await goto('/');
		} catch (error: any) {
			console.error('Apple 로그인 에러:', error);

			// 에러 메시지 표시
			errorMessage = getAuthErrorMessage(error.code, 'apple');
		} finally {
			appleLoading = false;
		}
	}

	/**
	 * 에러 메시지 닫기
	 */
	function dismissError() {
		errorMessage = null;
	}
</script>

<div class={cn('flex flex-col gap-6', className)} {...restProps}>
	<Card.Root>
		<Card.Header class="text-center">
			<Card.Title class="text-xl">환영합니다</Card.Title>
			<Card.Description>
				Google 또는 Apple 계정으로 로그인하세요
			</Card.Description>
		</Card.Header>
		<Card.Content>
			<div class="flex flex-col gap-4">
				<!-- 에러 메시지 표시 -->
				{#if errorMessage}
					<Alert.Root variant="destructive">
						<Alert.Title>로그인 실패</Alert.Title>
						<Alert.Description class="flex items-center justify-between">
							<span>{errorMessage}</span>
							<button
								onclick={dismissError}
								class="ml-2 text-sm underline hover:no-underline"
								type="button"
							>
								닫기
							</button>
						</Alert.Description>
					</Alert.Root>
				{/if}

				<!-- Google 로그인 버튼 -->
				<Button
					variant="outline"
					type="button"
					class="w-full"
					disabled={isLoading}
					onclick={handleGoogleLogin}
				>
					{#if googleLoading}
						<!-- 로딩 스피너 -->
						<svg
							class="mr-2 h-5 w-5 animate-spin"
							xmlns="http://www.w3.org/2000/svg"
							fill="none"
							viewBox="0 0 24 24"
						>
							<circle
								class="opacity-25"
								cx="12"
								cy="12"
								r="10"
								stroke="currentColor"
								stroke-width="4"
							></circle>
							<path
								class="opacity-75"
								fill="currentColor"
								d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
							></path>
						</svg>
						로그인 중...
					{:else}
						<!-- Google 아이콘 -->
						<svg
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 24 24"
							class="mr-2 h-5 w-5"
						>
							<path
								d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"
								fill="currentColor"
							/>
						</svg>
						Google로 로그인
					{/if}
				</Button>

				<!-- Apple 로그인 버튼 -->
				<Button
					variant="outline"
					type="button"
					class="w-full"
					disabled={isLoading}
					onclick={handleAppleLogin}
				>
					{#if appleLoading}
						<!-- 로딩 스피너 -->
						<svg
							class="mr-2 h-5 w-5 animate-spin"
							xmlns="http://www.w3.org/2000/svg"
							fill="none"
							viewBox="0 0 24 24"
						>
							<circle
								class="opacity-25"
								cx="12"
								cy="12"
								r="10"
								stroke="currentColor"
								stroke-width="4"
							></circle>
							<path
								class="opacity-75"
								fill="currentColor"
								d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
							></path>
						</svg>
						로그인 중...
					{:else}
						<!-- Apple 아이콘 -->
						<svg
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 24 24"
							class="mr-2 h-5 w-5"
						>
							<path
								d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"
								fill="currentColor"
							/>
						</svg>
						Apple로 로그인
					{/if}
				</Button>
			</div>
		</Card.Content>
	</Card.Root>
</div>
```

### 5.5 로그인 페이지 작성

**파일 경로:** `src/routes/user/login/+page.svelte`

**목적:** 로그인 페이지 렌더링 및 인증 상태 확인

**내용:**

```svelte
<script lang="ts">
	/**
	 * 로그인 페이지
	 *
	 * 사용자가 Google 또는 Apple 계정으로 로그인할 수 있는 페이지입니다.
	 * 이미 로그인된 사용자는 자동으로 홈페이지로 리다이렉트됩니다.
	 */

	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import UserLogin from '$lib/components/user-login.svelte';
	import { authStore } from '$lib/stores/auth.svelte';

	/**
	 * 컴포넌트 마운트 시 인증 상태 확인
	 * 이미 로그인된 사용자는 홈페이지로 리다이렉트
	 */
	onMount(() => {
		// 인증 시스템이 초기화될 때까지 대기
		const checkAuth = setInterval(() => {
			if (authStore.initialized) {
				clearInterval(checkAuth);

				// 이미 로그인된 경우 홈페이지로 이동
				if (authStore.isAuthenticated) {
					goto('/');
				}
			}
		}, 100);

		// 컴포넌트 언마운트 시 interval 정리
		return () => clearInterval(checkAuth);
	});
</script>

<svelte:head>
	<title>로그인 - Sonub</title>
	<meta name="description" content="Sonub에 로그인하세요" />
</svelte:head>

<div class="container flex min-h-screen items-center justify-center px-4 py-8">
	{#if authStore.loading}
		<!-- 인증 상태 확인 중 로딩 표시 -->
		<div class="flex flex-col items-center gap-4">
			<svg
				class="h-8 w-8 animate-spin text-primary"
				xmlns="http://www.w3.org/2000/svg"
				fill="none"
				viewBox="0 0 24 24"
			>
				<circle
					class="opacity-25"
					cx="12"
					cy="12"
					r="10"
					stroke="currentColor"
					stroke-width="4"
				></circle>
				<path
					class="opacity-75"
					fill="currentColor"
					d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
				></path>
			</svg>
			<p class="text-muted-foreground">로딩 중...</p>
		</div>
	{:else}
		<!-- 로그인 컴포넌트 표시 -->
		<UserLogin class="w-full max-w-md" />
	{/if}
</div>
```

## 6. 사용자 정보 표시 예제

### 6.1 홈페이지에서 사용자 정보 표시

**파일 경로:** `src/routes/+page.svelte` (예제)

**내용:**

```svelte
<script lang="ts">
	/**
	 * 홈페이지 - 사용자 정보 표시 예제
	 */

	import { authStore } from '$lib/stores/auth.svelte';
	import { signOut } from '$lib/utils/auth-helpers';
	import { Button } from '$lib/components/ui/button/index.js';
	import { goto } from '$app/navigation';

	/**
	 * 로그아웃 처리
	 */
	async function handleSignOut() {
		try {
			await signOut();
			goto('/user/login');
		} catch (error) {
			console.error('로그아웃 실패:', error);
		}
	}
</script>

<div class="container mx-auto px-4 py-8">
	{#if authStore.loading}
		<p>로딩 중...</p>
	{:else if authStore.isAuthenticated}
		<!-- 로그인된 사용자 정보 표시 -->
		<div class="flex flex-col gap-4">
			<h1 class="text-2xl font-bold">환영합니다!</h1>

			<div class="rounded-lg border p-4">
				<p class="mb-2">
					<strong>이름:</strong>
					{authStore.user?.displayName || '없음'}
				</p>
				<p class="mb-2">
					<strong>이메일:</strong>
					{authStore.user?.email || '없음'}
				</p>
				<p class="mb-4">
					<strong>UID:</strong>
					{authStore.user?.uid || '없음'}
				</p>

				{#if authStore.user?.photoURL}
					<img
						src={authStore.user.photoURL}
						alt="프로필 사진"
						class="h-20 w-20 rounded-full"
					/>
				{/if}
			</div>

			<Button onclick={handleSignOut} variant="outline">
				로그아웃
			</Button>
		</div>
	{:else}
		<!-- 비로그인 상태 -->
		<div class="flex flex-col gap-4">
			<h1 class="text-2xl font-bold">Sonub에 오신 것을 환영합니다</h1>
			<p>로그인이 필요합니다.</p>
			<Button onclick={() => goto('/user/login')}>로그인</Button>
		</div>
	{/if}
</div>
```

## 7. 검증 방법

### 7.1 설치 검증 체크리스트

**필수 검증 단계:**

1. **Firebase 설정 확인**
   ```bash
   # Firebase SDK 설치 확인
   npm list firebase
   ```

   **예상 출력:**
   ```
   sonub@0.0.1
   └── firebase@11.0.0
   ```

2. **파일 생성 확인**
   ```bash
   # 모든 필수 파일이 생성되었는지 확인
   ls -la src/lib/utils/auth-helpers.ts
   ls -la src/lib/stores/auth.svelte.ts
   ls -la src/lib/components/user-login.svelte
   ls -la src/routes/user/login/+page.svelte
   ```

3. **TypeScript 컴파일 확인**
   ```bash
   npm run check
   ```

   **예상 출력:**
   ```
   No errors found
   ```

4. **개발 서버 실행**
   ```bash
   npm run dev
   ```

   **확인 사항:**
   - 컴파일 에러 없음
   - 브라우저 콘솔에 Firebase 관련 에러 없음

### 7.2 기능 검증

#### 7.2.1 Google 로그인 테스트

```
1. 브라우저에서 http://localhost:5173/user/login 접속
2. "Google로 로그인" 버튼 클릭
3. Google 로그인 팝업 창 확인
4. 언어 설정 확인:
   - 브라우저 언어가 ko, ja, zh인 경우 해당 언어로 표시
   - 그 외의 경우 영어로 표시
5. Google 계정으로 로그인
6. 로그인 성공 시:
   - 홈페이지(/)로 자동 리다이렉트
   - authStore.user에 사용자 정보 저장됨
   - Firebase Console > Authentication > Users에서 사용자 확인
7. 에러 처리 테스트:
   - 팝업 닫기: "로그인 창이 닫혔습니다" 메시지 표시
   - 네트워크 끊기: "네트워크 연결을 확인하고 다시 시도해주세요" 표시
```

#### 7.2.2 Apple 로그인 테스트

```
1. 브라우저에서 http://localhost:5173/user/login 접속
2. "Apple로 로그인" 버튼 클릭
3. Apple 로그인 팝업 창 확인
4. 언어 설정 확인 (Google과 동일)
5. Apple 계정으로 로그인
6. 로그인 성공 시:
   - 홈페이지(/)로 자동 리다이렉트
   - authStore.user에 사용자 정보 저장됨
   - Firebase Console > Authentication > Users에서 사용자 확인
7. 에러 처리 테스트 (Google과 동일)
```

#### 7.2.3 인증 상태 관리 테스트

```
1. 로그인 후 페이지 새로고침
   - authStore.user가 유지되는지 확인
   - 로딩 상태가 올바르게 표시되는지 확인

2. 여러 탭에서 로그인/로그아웃
   - 모든 탭에서 인증 상태가 동기화되는지 확인

3. 로그아웃 테스트
   - 로그아웃 버튼 클릭
   - authStore.user가 null로 변경되는지 확인
   - 로그인 페이지로 리다이렉트되는지 확인
```

#### 7.2.4 다국어 지원 테스트

```
1. 브라우저 언어 설정 변경:
   - Chrome: 설정 > 언어 > 기본 언어 설정

2. 각 언어별 테스트:
   - ko (한국어): 로그인 화면이 한국어로 표시
   - ja (일본어): 로그인 화면이 일본어로 표시
   - zh (중국어): 로그인 화면이 중국어로 표시
   - en (영어) 또는 기타: 로그인 화면이 영어로 표시

3. 확인:
   - getAuthLanguage() 함수가 올바른 언어 코드 반환
   - provider.setCustomParameters()에 올바른 locale 전달
```

### 7.3 보안 검증

```
1. Firebase Console에서 보안 규칙 확인
2. 승인된 도메인 목록 확인
3. API 키가 .env 파일에만 존재하고 코드에 하드코딩되지 않았는지 확인
4. .gitignore에 .env 파일이 포함되어 있는지 확인
```

## 8. 문제 해결

### 8.1 일반적인 문제

**문제 1: 팝업이 차단됨**

```
증상: "auth/popup-blocked" 에러 발생
원인: 브라우저의 팝업 차단 설정
해결:
1. 브라우저 주소창 오른쪽의 팝업 차단 아이콘 클릭
2. "항상 허용" 선택
3. 페이지 새로고침 후 재시도
```

**문제 2: 로그인 제공업체가 활성화되지 않음**

```
증상: "auth/operation-not-allowed" 에러
원인: Firebase Console에서 Google 또는 Apple 로그인 미활성화
해결:
1. Firebase Console > Authentication > Sign-in method
2. Google 또는 Apple 제공업체 확인
3. "사용 설정" 토글이 ON인지 확인
4. 저장 후 재시도
```

**문제 3: 도메인이 승인되지 않음**

```
증상: "auth/unauthorized-domain" 에러
원인: 현재 도메인이 Firebase 승인 목록에 없음
해결:
1. Firebase Console > Authentication > Settings > Authorized domains
2. 현재 도메인 추가 (localhost, 127.0.0.1, 또는 실제 도메인)
3. 저장 후 재시도
```

**문제 4: 언어 설정이 적용되지 않음**

```
증상: 로그인 화면이 항상 영어로 표시됨
원인: 언어 코드 감지 실패 또는 잘못된 locale 전달
해결:
1. 브라우저 콘솔에서 navigator.language 확인
2. getAuthLanguage() 함수의 반환값 확인
3. provider.setCustomParameters()에 전달되는 locale 확인
4. 브라우저 언어 설정 확인 및 변경
```

### 8.2 Apple 로그인 관련 문제

**문제 1: Apple 로그인 실패**

```
증상: Apple 로그인 시 에러 발생
원인: Services ID 미설정 또는 잘못된 설정
해결:
1. 개발 중에는 Firebase가 자동 생성한 Services ID 사용 가능
2. 프로덕션 배포 시:
   - Apple Developer Console에서 Services ID 생성
   - Firebase Console에 등록
   - Return URL 설정
```

**문제 2: 사용자 정보가 null**

```
증상: Apple 로그인 후 displayName 또는 email이 null
원인: Apple은 첫 로그인 시에만 사용자 정보 제공
해결:
1. 첫 로그인 시 사용자 정보를 Firestore에 저장
2. 이후 로그인 시 저장된 정보 사용
3. 테스트 중에는 Apple ID 설정에서 앱 연동 해제 후 재시도
```

## 9. 추가 기능 구현 가이드

### 9.1 로그인 후 사용자 프로필 저장

Firestore에 사용자 프로필을 저장하려면 다음과 같이 구현:

**파일 경로:** `src/lib/utils/auth-helpers.ts`에 추가

```typescript
import { db } from '$lib/firebase';
import { doc, setDoc, serverTimestamp } from 'firebase/firestore';

/**
 * 사용자 프로필을 Firestore에 저장
 */
export async function saveUserProfile(user: User): Promise<void> {
	try {
		const userRef = doc(db, 'users', user.uid);

		await setDoc(
			userRef,
			{
				uid: user.uid,
				email: user.email,
				displayName: user.displayName,
				photoURL: user.photoURL,
				lastLogin: serverTimestamp(),
				updatedAt: serverTimestamp()
			},
			{ merge: true } // 기존 데이터 병합
		);

		console.log('사용자 프로필 저장 완료');
	} catch (error) {
		console.error('사용자 프로필 저장 실패:', error);
		throw error;
	}
}
```

로그인 성공 시 호출:

```typescript
// signInWithGoogle() 또는 signInWithApple() 함수에서
const result = await signInWithPopup(auth, provider);
await saveUserProfile(result.user);
```

### 9.2 로그인 리다이렉션 개선

이전 페이지로 돌아가기:

```typescript
// 로그인 페이지에서
const redirectUrl = new URL(window.location.href).searchParams.get('redirect') || '/';

// 로그인 성공 시
goto(redirectUrl);
```

## 10. 승인 기준

### 10.1 설치 완료 조건

다음 모든 조건을 만족해야 설치가 완료된 것으로 간주합니다:

- ✅ Firebase Console에서 Google 및 Apple 로그인 활성화
- ✅ `src/lib/utils/auth-helpers.ts` 파일 생성 및 모든 함수 구현
- ✅ `src/lib/stores/auth.svelte.ts` 파일 생성 및 인증 스토어 구현
- ✅ `src/lib/components/user-login.svelte` 컴포넌트 생성
- ✅ `src/routes/user/login/+page.svelte` 페이지 생성
- ✅ `npm run check` 통과 (TypeScript 오류 없음)
- ✅ `npm run dev` 실행 가능
- ✅ Google 로그인 기능 동작 확인
- ✅ Apple 로그인 기능 동작 확인
- ✅ 언어 설정 (ko, ja, zh, en) 동작 확인
- ✅ 에러 처리 및 로딩 상태 표시 확인
- ✅ 로그인 후 리다이렉션 동작 확인
- ✅ 로그아웃 기능 동작 확인
- ✅ Firebase Console에서 로그인된 사용자 확인 가능

### 10.2 품질 기준

- **보안**: Firebase 보안 규칙이 적절히 설정됨
- **타입 안전성**: TypeScript 타입 정의 완료, any 사용 최소화
- **에러 처리**: 모든 Firebase 작업에 try-catch 구현
- **사용자 경험**: 로딩 상태, 에러 메시지 명확히 표시
- **코드 품질**: 주석 작성, 일관된 코딩 스타일
- **반응성**: Svelte 5 runes를 사용한 반응형 상태 관리
- **다국어**: ko, ja, zh, en 언어 지원 확인

## 11. 참고 자료

### 11.1 공식 문서

- **Firebase Google Sign-in**: https://firebase.google.com/docs/auth/web/google-signin
- **Firebase Apple Sign-in**: https://firebase.google.com/docs/auth/web/apple
- **Firebase Authentication**: https://firebase.google.com/docs/auth/web/start
- **Svelte 5 Documentation**: https://svelte.dev/docs/svelte/overview
- **shadcn-svelte**: https://www.shadcn-svelte.com/

### 11.2 추가 리소스

- **Firebase Authentication Limits**: https://firebase.google.com/docs/auth/limits
- **Apple Developer - Sign in with Apple**: https://developer.apple.com/sign-in-with-apple/
- **Google Identity Platform**: https://developers.google.com/identity

## 12. 변경 이력

| 버전  | 날짜       | 작성자      | 변경 내용                                       |
| ----- | ---------- | ----------- | ----------------------------------------------- |
| 1.0.0 | 2025-01-09 | JaeHo Song  | 초기 명세서 작성                                |
| 1.1.0 | 2025-01-09 | JaeHo Song  | 실제 구현 내용 반영 (UI 컴포넌트, 패키지 정보) |

---

**주의사항:**

- 이 명세서는 SED(Spec-Exact Development) 원칙에 따라 작성되었습니다.
- AI는 이 명세서에 명시된 내용만 정확히 실행해야 합니다.
- 명세서에 없는 추가 설정이나 변경은 개발자의 명시적 승인이 필요합니다.
- 모든 파일은 UTF-8 인코딩(BOM 없음)으로 저장되어야 합니다.
- 이메일/비밀번호 로그인은 구현하지 않습니다.
- 지원 언어는 ko, ja, zh이며, 그 외는 en(영어)를 사용합니다.

**승인:**

- [ ] 개발자 승인 필요
- [ ] Firebase Console 설정 완료
- [ ] 로그인 기능 테스트 완료
- [ ] 다국어 지원 테스트 완료
- [ ] 문서 검토 완료
