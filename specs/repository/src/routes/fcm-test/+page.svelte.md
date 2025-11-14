---
name: +page.svelte
description: +page 페이지
version: 1.0.0
type: svelte-component
category: route-page
original_path: src/routes/fcm-test/+page.svelte
---

# +page.svelte

## 개요

**파일 경로**: `src/routes/fcm-test/+page.svelte`
**파일 타입**: svelte-component
**카테고리**: route-page

+page 페이지

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * FCM (Firebase Cloud Messaging) 테스트 페이지
	 *
	 * 이 페이지에서 다음 기능을 테스트할 수 있습니다:
	 * - 알림 권한 요청
	 * - FCM 토큰 발급
	 * - 포그라운드 메시지 수신 (Toast 표시)
	 * - 백그라운드 메시지 수신 (브라우저 알림)
	 *
	 * 테스트 방법:
	 * 1. "푸시 알림 활성화" 버튼 클릭
	 * 2. 브라우저에서 알림 권한 허용
	 * 3. 발급된 FCM 토큰 복사
	 * 4. Firebase Console에서 테스트 메시지 전송
	 * 5. 포그라운드/백그라운드에서 메시지 수신 확인
	 */

	import { onMount } from 'svelte';
	import { requestFcmToken, subscribeOnMessage } from '$lib/fcm';
	import { toast } from 'svelte-sonner';
	import { Button } from '$lib/components/ui/button';
	import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '$lib/components/ui/card';
	import type { MessagePayload } from 'firebase/messaging';

	// 상태 관리 (Svelte 5 runes)
	let token: string | null = $state(null);
	let permission: NotificationPermission | 'unknown' = $state('unknown');
	let lastMessage: MessagePayload | null = $state(null);
	let isLoading = $state(false);

	/**
	 * 컴포넌트 마운트 시 실행
	 * - 알림 권한 상태 확인
	 * - 포그라운드 메시지 리스너 등록
	 */
	onMount(() => {
		// 브라우저 Notification API 지원 확인
		if (typeof Notification === 'undefined') {
			permission = 'unknown';
			toast.error('이 브라우저는 알림을 지원하지 않습니다.');
			return;
		}

		// 현재 알림 권한 상태
		permission = Notification.permission;

		// 포그라운드 메시지 수신 리스너 등록
		subscribeOnMessage((payload) => {
			console.log('[FCM Test] 포그라운드 메시지 수신:', payload);
			lastMessage = payload;

			// Toast 알림 표시
			const title = payload.notification?.title ?? '새 알림';
			const body = payload.notification?.body ?? '';
			toast.success(title, { description: body });
		});
	});

	/**
	 * 푸시 알림 활성화 버튼 클릭 핸들러
	 * 1. 브라우저 알림 권한 요청
	 * 2. FCM 토큰 발급 및 저장
	 */
	async function enablePush() {
		isLoading = true;

		try {
			// 1. Notification API 지원 확인
			if (typeof Notification === 'undefined') {
				toast.error('이 브라우저는 알림을 지원하지 않습니다.');
				return;
			}

			// 2. 알림 권한 요청
			const result = await Notification.requestPermission();
			permission = result;

			if (result !== 'granted') {
				toast.error('알림 권한이 거부되었습니다. 브라우저 설정에서 권한을 허용해주세요.');
				return;
			}

			toast.success('알림 권한이 허용되었습니다.');

			// 3. FCM 토큰 발급 및 저장
			const fcmToken = await requestFcmToken();

			if (fcmToken) {
				token = fcmToken;
				toast.success('푸시 알림이 활성화되었습니다!');
			} else {
				toast.error('FCM 토큰 발급에 실패했습니다. 콘솔을 확인해주세요.');
			}
		} catch (error) {
			console.error('[FCM Test] 푸시 활성화 실패:', error);
			toast.error('푸시 알림 활성화에 실패했습니다.');
		} finally {
			isLoading = false;
		}
	}
</script>

<div class="container mx-auto max-w-2xl p-4">
	<h1 class="mb-6 text-3xl font-bold">Firebase Cloud Messaging 테스트</h1>

	<!-- 알림 권한 상태 카드 -->
	<Card class="mb-4">
		<CardHeader>
			<CardTitle>알림 권한 상태</CardTitle>
			<CardDescription>현재 브라우저의 알림 권한 상태를 표시합니다</CardDescription>
		</CardHeader>
		<CardContent>
			<div class="flex items-center gap-2">
				<span class="font-semibold">현재 상태:</span>
				<span
					class="rounded px-2 py-1 text-sm font-medium"
					class:bg-green-100={permission === 'granted'}
					class:text-green-800={permission === 'granted'}
					class:bg-red-100={permission === 'denied'}
					class:text-red-800={permission === 'denied'}
					class:bg-gray-100={permission === 'default' || permission === 'unknown'}
					class:text-gray-800={permission === 'default' || permission === 'unknown'}
				>
					{permission === 'granted' ? '✅ 허용됨' : ''}
					{permission === 'denied' ? '❌ 거부됨' : ''}
					{permission === 'default' ? '⏳ 미결정' : ''}
					{permission === 'unknown' ? '❓ 알 수 없음' : ''}
				</span>
			</div>

			<Button onclick={enablePush} disabled={isLoading} class="mt-4">
				{isLoading ? '처리 중...' : '푸시 알림 활성화'}
			</Button>
		</CardContent>
	</Card>

	<!-- FCM 토큰 카드 -->
	{#if token}
		<Card class="mb-4">
			<CardHeader>
				<CardTitle>FCM 토큰</CardTitle>
				<CardDescription>Firebase Cloud Messaging 토큰 (RTDB에 자동 저장됨)</CardDescription>
			</CardHeader>
			<CardContent>
				<pre class="overflow-x-auto rounded bg-gray-100 p-3 text-xs">{token}</pre>
				<p class="mt-2 text-sm text-gray-600">
					이 토큰을 사용하여 Firebase Console 또는 Admin SDK에서 테스트 메시지를 보낼 수
					있습니다.
				</p>
			</CardContent>
		</Card>
	{/if}

	<!-- 마지막 메시지 카드 -->
	{#if lastMessage}
		<Card class="mb-4">
			<CardHeader>
				<CardTitle>마지막 수신 메시지</CardTitle>
				<CardDescription>포그라운드에서 수신한 가장 최근 메시지</CardDescription>
			</CardHeader>
			<CardContent>
				<pre class="overflow-x-auto rounded bg-gray-100 p-3 text-xs">
{JSON.stringify(lastMessage, null, 2)}
				</pre>
			</CardContent>
		</Card>
	{/if}

	<!-- 테스트 가이드 -->
	<Card class="mt-6">
		<CardHeader>
			<CardTitle>테스트 방법</CardTitle>
			<CardDescription>Firebase Console에서 테스트 메시지 보내기</CardDescription>
		</CardHeader>
		<CardContent>
			<ol class="ml-4 list-decimal space-y-2 text-sm">
				<li>위의 "푸시 알림 활성화" 버튼 클릭</li>
				<li>발급된 FCM 토큰 복사</li>
				<li>Firebase Console → Cloud Messaging → "Send your first message"</li>
				<li>"Notification title" 및 "Notification text" 입력</li>
				<li>"Send test message" 클릭 후 토큰 붙여넣기</li>
				<li>
					<strong>포그라운드 테스트</strong>: 이 페이지를 열어둔 상태에서 메시지 수신 →
					Toast 알림 표시
				</li>
				<li>
					<strong>백그라운드 테스트</strong>: 다른 탭으로 이동하거나 브라우저 최소화 →
					브라우저 알림 표시
				</li>
			</ol>
		</CardContent>
	</Card>
</div>

<style>
	/* pre 태그의 텍스트 줄바꿈 처리 */
	pre {
		@apply whitespace-pre-wrap break-words;
	}
</style>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
