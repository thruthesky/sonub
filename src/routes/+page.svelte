<script lang="ts">
	/**
	 * 홈페이지
	 *
	 * Sonub 프로젝트의 메인 랜딩 페이지입니다.
	 *
	 * ## TODO: 향후 개발 기능 목록
	 *
	 * ### 1. 새로운 메시지 뱃지 증가 및 방 입장시 초기화
	 * - 채팅방 목록에서 읽지 않은 메시지 개수 뱃지 표시
	 * - 채팅방 입장 시 자동으로 뱃지 카운트 초기화
	 * - `/chat-joins/{uid}/{roomId}/newMessageCount` 필드 활용
	 *
	 * ### 2. 북마크 기능 (북마크 폴더로 관리)
	 * - 채팅방을 북마크에 추가/제거
	 * - 북마크 폴더 생성 및 관리 (예: 업무, 친구, 가족 등)
	 * - 북마크된 채팅방을 폴더별로 그룹화하여 표시
	 * - Firebase 경로: `/user-bookmarks/{uid}/{folderId}/{roomId}`
	 *
	 * ### 3. 채팅 핀(Chat Pin) 기능
	 * - 중요한 채팅방을 상단에 고정
	 * - 핀 설정/해제 토글 버튼
	 * - 핀된 채팅방은 항상 목록 최상단에 표시
	 * - Firebase 경로: `/chat-joins/{uid}/{roomId}/pinned: boolean`
	 *
	 * ### 4. 푸시 알림: FCM 클라이언트 설정
	 * - Firebase Cloud Messaging (FCM) Permission 요청
	 * - FCM 토큰 생성 및 저장
	 * - Firebase 경로: `/fcm-tokens/{uid}/{tokenId}`
	 * - 토큰 갱신 처리 및 만료된 토큰 정리
	 *
	 * ### 5. 새로운 채팅 메시지 푸시 알림
	 * - Cloud Functions에서 새 메시지 감지 시 FCM 전송
	 * - 알림 페이로드: 발신자 이름, 메시지 내용 미리보기, 채팅방 ID
	 * - 앱이 포그라운드/백그라운드일 때 각각 다른 처리
	 * - 알림 클릭 시 해당 채팅방으로 이동
	 *
	 * ### 6. 그룹 채팅 기능
	 *
	 * #### 6-1. 오픈 채팅 기능
	 * - 공개 채팅방 생성 (누구나 입장 가능)
	 * - 오픈 채팅 목록 페이지 (`/chat/open`)
	 * - 채팅방 이름, 설명, 태그로 검색 가능
	 * - Firebase 경로: `/open-chats/{roomId}`
	 * - 최대 참여 인원 설정 옵션
	 *
	 * #### 6-2. 일반 그룹 채팅
	 * - 초대 기반 비공개 그룹 채팅
	 * - 오픈 채팅 메뉴에 표시되지 않음
	 * - 검색 불가능 (초대 링크 또는 직접 초대만 가능)
	 * - Firebase 경로: `/group-chats/{roomId}`
	 * - 멤버 관리: 초대, 추방, 나가기
	 *
	 * ### 7. 그룹 채팅 비밀번호 기능
	 * - 오픈 채팅에 비밀번호 설정 옵션
	 * - 입장 시 비밀번호 입력 모달
	 * - Firebase 경로: `/open-chats/{roomId}/password` (해시 저장)
	 * - Cloud Functions에서 비밀번호 검증 수행
	 *
	 * ### 8. 채팅 메시지 입력 박스에서 "Post" 타입 선택
	 *
	 * #### 8-1. 카테고리 선택
	 * - 메시지 입력창 옆 드롭다운에서 타입 선택: "message" 또는 "post"
	 * - "post" 선택 시 카테고리 선택 UI 표시
	 * - 카테고리 목록: 공지사항, 자유게시판, Q&A, 갤러리 등
	 *
	 * #### 8-2. 제목, 내용, 사진 업로드 후 저장
	 * - 제목 입력 필드 추가 (필수)
	 * - 내용 입력 (rich text editor 또는 마크다운)
	 * - 다중 이미지 업로드 지원
	 * - Firebase Storage에 이미지 저장
	 * - Firebase 경로: `/chat-messages/{messageId}` (type: "post")
	 *
	 * #### 8-3. 카테고리별 게시판 메뉴
	 * - 홈페이지 메뉴에 카테고리별 페이지 추가 (예: `/board/notice`, `/board/free`)
	 * - DatabaseListView 사용하여 실시간 목록 표시
	 * - orderBy: "createdAt", orderPrefix로 카테고리 필터링
	 * - 게시글 상세 페이지: `/board/{category}/{postId}`
	 *
	 * #### 8-4. 댓글 기능 (게시판처럼 보이게)
	 * - 게시글 상세 페이지에서 댓글 목록 표시
	 * - 댓글 작성, 수정, 삭제 기능
	 * - Firebase 경로: `/chat-messages/{messageId}` (parentId: postId)
	 * - 대댓글 지원 (최대 1단계)
	 * - DatabaseListView 사용하여 실시간 댓글 동기화
	 */

	import { Button } from '$lib/components/ui/button/index.js';
	import * as Card from '$lib/components/ui/card/index.js';
	import { authStore } from '$lib/stores/auth.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { m } from '$lib/paraglide/messages';
</script>

<svelte:head>
	<title>{m.pageTitleHome()}</title>
</svelte:head>

<div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center">
	<div class="mx-auto max-w-4xl space-y-8 text-center">
		<!-- 메인 타이틀 -->
		<div class="space-y-4">
			<h1 class="text-4xl font-bold text-gray-900 md:text-6xl">{m.authWelcomeMessage()}</h1>
			<p class="text-lg text-gray-600 md:text-xl">
				{m.authIntro()}
			</p>
		</div>

		<!-- 사용자 환영 메시지 또는 로그인 유도 -->
		{#if authStore.loading}
			<Card.Root class="mx-auto max-w-md">
				<Card.Content class="pt-6">
					<p class="text-center text-gray-600">{m.commonLoading()}</p>
				</Card.Content>
			</Card.Root>
		{:else if authStore.isAuthenticated}
			<Card.Root class="mx-auto max-w-md">
				<Card.Header>
					<Card.Title>{m.authWelcome()}</Card.Title>
					<Card.Description>
						{m.authWelcomeUser({ name: authStore.user?.displayName || authStore.user?.email || m.commonUser() })}
					</Card.Description>
				</Card.Header>
				<Card.Content>
					<div class="flex items-center justify-center gap-4">
						{#if authStore.user?.uid}
							<Avatar uid={authStore.user.uid} size={64} class="shadow-sm" />
						{:else}
							<div class="h-16 w-16 rounded-full bg-gray-200" aria-hidden="true"></div>
						{/if}
					</div>
				</Card.Content>
			</Card.Root>
		{:else}
			<Card.Root class="mx-auto max-w-md">
				<Card.Header>
					<Card.Title>{m.authGetStarted()}</Card.Title>
					<Card.Description>{m.authSignInGuideStart()}</Card.Description>
				</Card.Header>
				<Card.Content>
					<Button class="w-full" href="/user/login">{m.authSignInAction()}</Button>
				</Card.Content>
			</Card.Root>
		{/if}

		<!-- 기능 소개 -->
		<div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">{m.featureSveltekit5()}</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">
						{m.featureSveltekit5Desc()}
					</p>
				</Card.Content>
			</Card.Root>

			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">{m.featureFirebaseAuth()}</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">{m.featureFirebaseAuthDesc()}</p>
				</Card.Content>
			</Card.Root>

			<Card.Root>
				<Card.Header>
					<Card.Title class="text-lg">{m.featureTailwindCss()}</Card.Title>
				</Card.Header>
				<Card.Content>
					<p class="text-sm text-gray-600">
						{m.featureTailwindCssDesc()}
					</p>
				</Card.Content>
			</Card.Root>
		</div>

		<!-- 링크 -->
		<div
			class="flex flex-col items-center justify-center gap-4 text-sm text-gray-600 sm:flex-row"
		>
			<a
				href="https://svelte.dev/docs/kit"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				{m.linkSvelteKitDocs()}
			</a>
			<span class="hidden sm:inline">•</span>
			<a
				href="https://firebase.google.com/docs"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				{m.linkFirebaseDocs()}
			</a>
			<span class="hidden sm:inline">•</span>
			<a
				href="https://www.shadcn-svelte.com"
				target="_blank"
				rel="noopener noreferrer"
				class="hover:text-gray-900"
			>
				{m.linkShadcnSvelte()}
			</a>
		</div>
	</div>
</div>
