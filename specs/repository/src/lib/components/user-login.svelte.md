---
name: user-login.svelte
description: user-login 컴포넌트
version: 1.0.0
type: svelte-component
category: component
original_path: src/lib/components/user-login.svelte
---

# user-login.svelte

## 개요

**파일 경로**: `src/lib/components/user-login.svelte`
**파일 타입**: svelte-component
**카테고리**: component

user-login 컴포넌트

## 소스 코드

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
	import { goto } from '$app/navigation';
	import type { HTMLAttributes } from 'svelte/elements';
	import { m } from '$lib/paraglide/messages';

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
			<Card.Title class="text-xl">{m.authWelcome()}</Card.Title>
			<Card.Description>
				{m.authSignInGuide()}
			</Card.Description>
		</Card.Header>
		<Card.Content>
			<div class="flex flex-col gap-4">
				<!-- 에러 메시지 표시 -->
				{#if errorMessage}
					<Alert.Root variant="destructive">
						<Alert.Title>{m.authSignInFailed()}</Alert.Title>
						<Alert.Description class="flex items-center justify-between">
							<span>{errorMessage}</span>
							<button
								onclick={dismissError}
								class="ml-2 text-sm underline hover:no-underline"
								type="button"
							>
								{m.commonClose()}
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
						{m.authSigningIn()}
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
						{m.authSignInWithGoogle()}
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
						{m.authSigningIn()}
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
						{m.authSignInWithApple()}
					{/if}
				</Button>
			</div>
		</Card.Content>
	</Card.Root>
</div>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
