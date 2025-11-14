---
name: +layout.svelte
description: +layout 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/+layout.svelte
---

# +layout.svelte

## 개요

**파일 경로**: `src/routes/+layout.svelte`
**파일 타입**: svelte-component
**카테고리**: route-page

+layout 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 루트 레이아웃 컴포넌트
	 *
	 * 전체 애플리케이션의 레이아웃을 정의합니다.
	 * 3컬럼 구조: 좌측 사이드바, 메인 콘텐츠, 우측 사이드바
	 *
	 * Paraglide i18n:
	 * - hooks.server.ts의 paraglideMiddleware가 서버에서 로케일 자동 감지/설정
	 * - 클라이언트는 쿠키 기반으로 자동 감지되어 설정됨
	 */

	import '../app.css';
	import TopBar from '$lib/components/top-bar.svelte';
	import LeftSidebar from '$lib/components/left-sidebar.svelte';
	import RightSidebar from '$lib/components/right-sidebar.svelte';
	import DevIcon from '$lib/components/dev/dev-icon.svelte';
	import FcmPermissionGate from '$lib/components/FcmPermissionGate.svelte';
	import { dev } from '$app/environment';
	import { Toaster, toast } from 'svelte-sonner';
	import { onMount } from 'svelte';
	import { registerServiceWorker, subscribeOnMessage } from '$lib/fcm';

	let { children } = $props();

	/**
	 * 앱 시작 시 초기화
	 * 1. 서비스 워커 미리 등록 (FCM 토큰 발급을 위해 필요)
	 * 2. 포그라운드 메시지 리스너 등록 (Toast 알림 표시)
	 */
	onMount(async () => {
		// 서비스 워커 등록
		await registerServiceWorker();

		// 포그라운드 메시지 수신 리스너 등록
		subscribeOnMessage((payload) => {
			console.log('[Layout] 포그라운드 메시지 수신:', payload);

			// Toast 알림 표시
			const title = payload.notification?.title ?? payload.data?.title ?? '새 알림';
			const body = payload.notification?.body ?? payload.data?.body ?? '';

			toast.success(title, {
				description: body,
				duration: 5000 // 5초 동안 표시
			});
		});
	});
</script>

<svelte:head>
	<!-- Favicon 설정 -->
	<link rel="icon" type="image/png" sizes="64x64" href="/favicon-64.png" />
	<link rel="icon" type="image/png" sizes="512x512" href="/favicon-512.png" />
	<link rel="apple-touch-icon" sizes="512x512" href="/favicon-512.png" />
	<!-- 기본 favicon (브라우저 호환성) -->
	<link rel="icon" type="image/png" href="/favicon-64.png" />
</svelte:head>

<div class="min-h-screen bg-gray-50">
	<TopBar />
	<div class="pt-20">
		<div class="container mx-auto px-4 py-8">
			<div class="flex gap-6">
				<!-- 좌측 사이드바 (데스크톱만) -->
				<LeftSidebar />

				<!-- 메인 콘텐츠 -->
				<main class="min-w-0 flex-1">
					{@render children()}
				</main>

				<!-- 우측 사이드바 (데스크톱만) -->
				<RightSidebar />
			</div>
		</div>
	</div>
</div>

<!-- 개발 모드에서만 표시되는 개발자 도구 -->
{#if dev}
	<DevIcon />
{/if}

<!-- 전역 Toast 알림 컴포넌트 -->
<Toaster position="top-center" richColors />

<!-- FCM 푸시 알림 권한 요청 가드 -->
<FcmPermissionGate />

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
