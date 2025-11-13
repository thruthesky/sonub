<script lang="ts">
	/**
	 * FCM 푸시 알림 권한 설정 안내 페이지
	 *
	 * 브라우저에서 푸시 알림 권한이 차단된 경우,
	 * 브라우저별로 권한을 다시 허용하는 방법을 안내합니다.
	 *
	 * 이 페이지에 접속하면 SessionStorage에 플래그를 저장하여
	 * 해당 세션 동안 권한 요청 팝업을 더 이상 표시하지 않습니다.
	 */

	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '$lib/components/ui/card';

	const DISMISSED_KEY = 'fcmPermissionDismissed';

	/**
	 * 페이지 마운트 시 SessionStorage에 플래그 저장
	 */
	onMount(() => {
		if (browser) {
			sessionStorage.setItem(DISMISSED_KEY, 'true');
		}
	});
</script>

<div class="container">
	<h1 class="page-title">푸시 알림 권한 설정 안내</h1>

	<Card class="mb-6">
		<CardHeader>
			<CardTitle>알림 권한이 차단되어 있습니다</CardTitle>
			<CardDescription>
				현재 이 사이트의 푸시 알림 권한이 브라우저에서 차단된 상태입니다. 채팅 알림, 새로운 메시지
				안내 등을 받으려면 아래 안내에 따라 권한을 다시 허용해 주세요.
			</CardDescription>
		</CardHeader>
		<CardContent>
			<p class="notice">
				브라우저에서 한 번 차단된 알림 권한은 사이트에서 직접 요청할 수 없으며, 사용자가 브라우저
				설정에서 직접 변경해야 합니다.
			</p>
		</CardContent>
	</Card>

	<!-- Chrome 데스크탑 -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>Chrome (데스크탑) 권한 허용 방법</CardTitle>
		</CardHeader>
		<CardContent>
			<ol class="instruction-list">
				<li>주소창 왼쪽의 <strong>자물쇠 아이콘(🔒)</strong> 또는 <strong>정보 아이콘(ℹ️)</strong>을 클릭합니다.</li>
				<li><strong>"알림(Notifications)"</strong> 항목을 찾습니다.</li>
				<li>설정을 <strong>"허용(Allow)"</strong>으로 변경합니다.</li>
				<li>페이지를 새로고침(F5 또는 Ctrl+R)한 뒤, 다시 서비스를 이용해 주세요.</li>
			</ol>
		</CardContent>
	</Card>

	<!-- Chrome Android -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>Chrome (Android) 권한 허용 방법</CardTitle>
		</CardHeader>
		<CardContent>
			<ol class="instruction-list">
				<li>브라우저 메뉴(⋮)를 탭합니다.</li>
				<li><strong>"사이트 설정(Site settings)"</strong>을 선택합니다.</li>
				<li><strong>"알림(Notifications)"</strong>을 선택합니다.</li>
				<li>차단된 사이트 목록에서 현재 사이트를 찾아 <strong>"허용"</strong>으로 변경합니다.</li>
				<li>페이지를 새로고침한 뒤, 다시 서비스를 이용해 주세요.</li>
			</ol>
		</CardContent>
	</Card>

	<!-- Safari macOS -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>Safari (macOS) 권한 허용 방법</CardTitle>
		</CardHeader>
		<CardContent>
			<ol class="instruction-list">
				<li><strong>Safari 메뉴 → 설정(Preferences)</strong>을 엽니다.</li>
				<li><strong>"웹사이트(Websites)"</strong> 탭을 선택합니다.</li>
				<li>왼쪽 목록에서 <strong>"알림(Notifications)"</strong>을 선택합니다.</li>
				<li>오른쪽 목록에서 현재 사이트를 찾아 <strong>"허용"</strong>으로 변경합니다.</li>
				<li>페이지를 새로고침한 뒤, 다시 서비스를 이용해 주세요.</li>
			</ol>
			<p class="note">
				<strong>참고:</strong> Safari iOS(iPhone/iPad)는 웹 푸시 알림을 지원하지 않습니다. iOS 16.4 이상에서는
				홈 화면에 추가된 웹 앱에서만 제한적으로 지원됩니다.
			</p>
		</CardContent>
	</Card>

	<!-- Firefox 데스크탑 -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>Firefox (데스크탑) 권한 허용 방법</CardTitle>
		</CardHeader>
		<CardContent>
			<ol class="instruction-list">
				<li>주소창 왼쪽의 <strong>자물쇠 아이콘(🔒)</strong>을 클릭합니다.</li>
				<li><strong>"연결이 안전함(Connection Secure)"</strong> 옆의 화살표를 클릭합니다.</li>
				<li><strong>"추가 정보(More Information)"</strong>를 선택합니다.</li>
				<li><strong>"권한(Permissions)"</strong> 탭을 선택합니다.</li>
				<li><strong>"알림 전송(Send Notifications)"</strong> 항목에서 <strong>"허용"</strong>을 선택합니다.</li>
				<li>페이지를 새로고침한 뒤, 다시 서비스를 이용해 주세요.</li>
			</ol>
		</CardContent>
	</Card>

	<!-- Edge -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>Edge (데스크탑) 권한 허용 방법</CardTitle>
		</CardHeader>
		<CardContent>
			<ol class="instruction-list">
				<li>주소창 왼쪽의 <strong>자물쇠 아이콘(🔒)</strong>을 클릭합니다.</li>
				<li><strong>"이 사이트의 권한(Permissions for this site)"</strong>을 선택합니다.</li>
				<li><strong>"알림(Notifications)"</strong> 항목을 <strong>"허용(Allow)"</strong>으로 변경합니다.</li>
				<li>페이지를 새로고침한 뒤, 다시 서비스를 이용해 주세요.</li>
			</ol>
		</CardContent>
	</Card>

	<!-- 추가 안내 -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>권한 허용 후 확인 방법</CardTitle>
		</CardHeader>
		<CardContent>
			<p class="notice">
				권한을 다시 허용하신 뒤에는 페이지를 새로고침하고, 채팅 목록 페이지 또는 앱 내에서 몇 번
				이동하시면 자동으로 푸시 알림이 다시 활성화됩니다.
			</p>
			<p class="notice">
				만약 권한을 허용했는데도 알림이 오지 않는다면, 브라우저를 완전히 종료한 후 다시 시작해
				보세요.
			</p>
		</CardContent>
	</Card>

	<!-- 문의 안내 -->
	<Card>
		<CardHeader>
			<CardTitle>추가 문의</CardTitle>
		</CardHeader>
		<CardContent>
			<p class="notice">
				위 방법을 따라 했는데도 문제가 해결되지 않는다면, 고객 지원팀으로 문의해 주세요.
			</p>
		</CardContent>
	</Card>
</div>

<style>
	@import 'tailwindcss' reference;

	/* 컨테이너 */
	.container {
		@apply mx-auto max-w-4xl p-4;
	}

	/* 페이지 제목 */
	.page-title {
		@apply text-3xl font-bold mb-6 text-gray-900;
	}

	/* 안내 문구 */
	.notice {
		@apply text-sm text-gray-700 mb-2;
	}

	/* 참고 문구 */
	.note {
		@apply text-sm text-gray-600 mt-4 p-3 bg-gray-50 rounded border border-gray-200;
	}

	/* 순서 목록 */
	.instruction-list {
		@apply list-decimal list-inside text-sm text-gray-700 space-y-2;
	}

	/* 목록 항목 */
	.instruction-list li {
		@apply leading-relaxed;
	}
</style>
