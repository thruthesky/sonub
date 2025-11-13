---
title: +page.svelte
type: component
status: active
version: 1.0.0
last_updated: 2025-11-13
---

## 개요

이 파일은 +page.svelte의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 홈페이지
	 *
	 * Sonub 프로젝트의 메인 랜딩 페이지입니다.
	 *
	 * ## TODO: 향후 개발 기능 목록
	 *
	 * ### 1. 그룹 채팅 기능
	 *
	 * #### 1-1. 오픈 채팅 기능
	 * - 공개 채팅방 생성 (누구나 입장 가능)
	 * - 오픈 채팅 목록 페이지 (`/chat/open`)
	 * - 채팅방 이름, 설명, 태그로 검색 가능
	 * - Firebase 경로: `/open-chats/{roomId}`
	 * - 최대 참여 인원 설정 옵션
	 *
	 * #### 1-2. 일반 그룹 채팅
	 * - 초대 기반 비공개 그룹 채팅
	 * - 오픈 채팅 메뉴에 표시되지 않음
	 * - 검색 불가능 (초대 링크 또는 직접 초대만 가능)
	 * - Firebase 경로: `/group-chats/{roomId}`
	 * - 멤버 관리: 초대, 추방, 나가기
	 *
	 * ### 2. 새로운 메시지 뱃지 증가 및 방 입장시 초기화
	 * - 채팅방 목록에서 읽지 않은 메시지 개수 뱃지 표시
	 * - 채팅방 입장 시 자동으로 뱃지 카운트 초기화
	 * - `/chat-joins/{uid}/{roomId}/newMessageCount` 필드 활용
	 *
	 * ### 3. 즐겨찾기 기능 (폴더관리)
	 * - 채팅방을 즐겨찾기에 추가/제거
	 * - 즐겨찾기 폴더 생성 및 관리 (예: 업무, 친구, 가족 등)
	 * - 즐겨찾기된 채팅방을 폴더별로 그룹화하여 표시
	 * - Firebase 경로: `/user-favorites/{uid}/{folderId}/{roomId}`
	 *
	 * ### 4. 채팅 핀(Chat Pin) 기능
	 * - 중요한 채팅방을 상단에 고정
	 * - 핀 설정/해제 토글 버튼
	 * - 핀된 채팅방은 항상 목록 최상단에 표시
	 * - Firebase 경로: `/chat-joins/{uid}/{roomId}/pinned: boolean`
	 *
	 * ### 5. 푸시 알림: FCM 클라이언트 설정
	 * - Firebase Cloud Messaging (FCM) Permission 요청
	 * - FCM 토큰 생성 및 저장
	 * - Firebase 경로: `/fcm-tokens/{uid}/{tokenId}`
	 * - 토큰 갱신 처리 및 만료된 토큰 정리
	 *
	 * ### 6. 새로운 채팅 메시지 푸시 알림
	 * - Cloud Functions에서 새 메시지 감지 시 FCM 전송
	 * - 알림 페이로드: 발신자 이름, 메시지 내용 미리보기, 채팅방 ID
	 * - 앱이 포그라운드/백그라운드일 때 각각 다른 처리
	 * - 알림 클릭 시 해당 채팅방으로 이동
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
	 *
	 * ### 9. 게시판 글이 채팅방 메시지 목록에 표시
	 * - 게시판에서 글을 작성하면 해당 채팅방의 메시지 목록에 자동으로 표시
	 * - type: "post"인 메시지를 채팅방 메시지 목록에 통합하여 표시
	 * - 채팅방 화면에서 일반 메시지와 게시글을 구분하여 렌더링
	 * - 게시글은 제목, 카테고리, 미리보기 형태로 표시
	 * - 클릭 시 게시글 상세 페이지로 이동
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

<div class="mx-auto max-w-7xl space-y-8">
	<!-- 메인 타이틀 -->
	<div class="space-y-4 text-center">
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

	<!-- TODO 리스트 -->
	<div class="mt-16">
		<h2 class="mb-8 text-3xl font-bold text-gray-900">향후 개발 기능 목록</h2>
		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<!-- 1. 그룹 채팅 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">1</span>
						<div class="flex flex-col gap-1">
							<Card.Title class="text-xl">그룹 채팅 기능</Card.Title>
							<span class="todo-badge todo-badge--done">완료</span>
						</div>
					</div>
				</Card.Header>
				<Card.Content>
					<div class="space-y-4">
						<div>
							<h4 class="todo-subtitle">1-1. 오픈 채팅 기능</h4>
							<ul class="todo-list">
								<li>공개 채팅방 생성 (누구나 입장 가능)</li>
								<li>오픈 채팅 목록 페이지 (<code>/chat/open</code>)</li>
								<li>채팅방 이름, 설명, 태그로 검색 가능</li>
								<li><code>/open-chats/{'{roomId}'}</code></li>
								<li>최대 참여 인원 설정 옵션</li>
							</ul>
						</div>
						<div>
							<h4 class="todo-subtitle">1-2. 일반 그룹 채팅</h4>
							<ul class="todo-list">
								<li>초대 기반 비공개 그룹 채팅</li>
								<li>오픈 채팅 메뉴에 표시되지 않음</li>
								<li>검색 불가능 (초대 링크 또는 직접 초대만 가능)</li>
								<li><code>/group-chats/{'{roomId}'}</code></li>
								<li>멤버 관리: 초대, 추방, 나가기</li>
							</ul>
						</div>
					</div>
				</Card.Content>
			</Card.Root>

			<!-- 2. 메시지 뱃지 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">2</span>
						<div class="flex flex-col gap-1">
							<Card.Title class="text-xl">새로운 메시지 뱃지 증가 및 방 입장시 초기화</Card.Title>
							<span class="todo-badge todo-badge--done">완료</span>
						</div>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>채팅방 목록에서 읽지 않은 메시지 개수 뱃지 표시</li>
						<li>채팅방 입장 시 자동으로 뱃지 카운트 초기화</li>
						<li><code>/chat-joins/{'{uid}'}/{'{roomId}'}/newMessageCount</code> 필드 활용</li>
					</ul>
				</Card.Content>
			</Card.Root>

			<!-- 3. 즐겨찾기 기능 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">3</span>
						<Card.Title class="text-xl">즐겨찾기 기능 (폴더관리)</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>채팅방을 즐겨찾기에 추가/제거</li>
						<li>즐겨찾기 폴더 생성 및 관리 (예: 업무, 친구, 가족 등)</li>
						<li>즐겨찾기된 채팅방을 폴더별로 그룹화하여 표시</li>
						<li><code>/user-favorites/{'{uid}'}/{'{folderId}'}/{'{roomId}'}</code></li>
					</ul>
				</Card.Content>
			</Card.Root>

			<!-- 4. 채팅 핀 기능 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">4</span>
						<Card.Title class="text-xl">채팅 핀(Chat Pin) 기능</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>중요한 채팅방을 상단에 고정</li>
						<li>핀 설정/해제 토글 버튼</li>
						<li>핀된 채팅방은 항상 목록 최상단에 표시</li>
						<li><code>/chat-joins/{'{uid}'}/{'{roomId}'}/pinned: boolean</code></li>
					</ul>
				</Card.Content>
			</Card.Root>

			<!-- 5. FCM 클라이언트 설정 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">5</span>
						<Card.Title class="text-xl">푸시 알림: FCM 클라이언트 설정</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>Firebase Cloud Messaging (FCM) Permission 요청</li>
						<li>FCM 토큰 생성 및 저장</li>
						<li><code>/fcm-tokens/{'{uid}'}/{'{tokenId}'}</code></li>
						<li>토큰 갱신 처리 및 만료된 토큰 정리</li>
					</ul>
				</Card.Content>
			</Card.Root>

			<!-- 6. 푸시 알림 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">6</span>
						<Card.Title class="text-xl">새로운 채팅 메시지 푸시 알림</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>Cloud Functions에서 새 메시지 감지 시 FCM 전송</li>
						<li>알림 페이로드: 발신자 이름, 메시지 내용 미리보기, 채팅방 ID</li>
						<li>앱이 포그라운드/백그라운드일 때 각각 다른 처리</li>
						<li>알림 클릭 시 해당 채팅방으로 이동</li>
					</ul>
				</Card.Content>
			</Card.Root>

			<!-- 7. 비밀번호 기능 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">7</span>
						<Card.Title class="text-xl">그룹 채팅 비밀번호 기능</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>오픈 채팅에 비밀번호 설정 옵션</li>
						<li>입장 시 비밀번호 입력 모달</li>
						<li><code>/open-chats/{'{roomId}'}/password</code> (해시 저장)</li>
						<li>Cloud Functions에서 비밀번호 검증 수행</li>
					</ul>
				</Card.Content>
			</Card.Root>

			<!-- 8. Post 타입 메시지 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">8</span>
						<Card.Title class="text-xl">채팅 메시지 "Post" 타입 선택</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<div class="space-y-4">
						<div>
							<h4 class="todo-subtitle">8-1. 카테고리 선택</h4>
							<ul class="todo-list">
								<li>메시지 입력창 옆 드롭다운에서 타입 선택: "message" 또는 "post"</li>
								<li>"post" 선택 시 카테고리 선택 UI 표시</li>
								<li>카테고리 목록: 공지사항, 자유게시판, Q&A, 갤러리 등</li>
							</ul>
						</div>
						<div>
							<h4 class="todo-subtitle">8-2. 제목, 내용, 사진 업로드</h4>
							<ul class="todo-list">
								<li>제목 입력 필드 추가 (필수)</li>
								<li>내용 입력 (rich text editor 또는 마크다운)</li>
								<li>다중 이미지 업로드 지원</li>
								<li>Firebase Storage에 이미지 저장</li>
								<li><code>/chat-messages/{'{messageId}'}</code> (type: "post")</li>
							</ul>
						</div>
						<div>
							<h4 class="todo-subtitle">8-3. 카테고리별 게시판 메뉴</h4>
							<ul class="todo-list">
								<li>홈페이지 메뉴에 카테고리별 페이지 추가</li>
								<li>DatabaseListView 사용하여 실시간 목록 표시</li>
								<li>orderBy: "createdAt", orderPrefix로 카테고리 필터링</li>
								<li>게시글 상세 페이지: <code>/board/{'{category}'}/{'{postId}'}</code></li>
							</ul>
						</div>
						<div>
							<h4 class="todo-subtitle">8-4. 댓글 기능 (게시판처럼 보이게)</h4>
							<ul class="todo-list">
								<li>게시글 상세 페이지에서 댓글 목록 표시</li>
								<li>댓글 작성, 수정, 삭제 기능</li>
								<li><code>/chat-messages/{'{messageId}'}</code> (parentId: postId)</li>
								<li>대댓글 지원 (최대 1단계)</li>
								<li>DatabaseListView 사용하여 실시간 댓글 동기화</li>
							</ul>
						</div>
					</div>
				</Card.Content>
			</Card.Root>

			<!-- 9. 게시판 글이 채팅방 메시지 목록에 표시 -->
			<Card.Root class="todo-card">
				<Card.Header>
					<div class="flex items-start gap-3">
						<span class="todo-number">9</span>
						<Card.Title class="text-xl">게시판 글이 채팅방 메시지 목록에 표시</Card.Title>
					</div>
				</Card.Header>
				<Card.Content>
					<ul class="todo-list">
						<li>게시판에서 글을 작성하면 해당 채팅방의 메시지 목록에 자동으로 표시</li>
						<li>type: "post"인 메시지를 채팅방 메시지 목록에 통합하여 표시</li>
						<li>채팅방 화면에서 일반 메시지와 게시글을 구분하여 렌더링</li>
						<li>게시글은 제목, 카테고리, 미리보기 형태로 표시</li>
						<li>클릭 시 게시글 상세 페이지로 이동</li>
					</ul>
				</Card.Content>
			</Card.Root>
		</div>
	</div>
</div>

<style>
	@import 'tailwindcss' reference;

	/* TODO 카드 스타일 */
	:global(.todo-card) {
		@apply transition-shadow duration-200 hover:shadow-lg;
	}

	.todo-number {
		@apply flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white;
	}

	.todo-list {
		@apply space-y-2 text-sm text-gray-700;
	}

	.todo-list li {
		@apply relative pl-5;
	}

	.todo-list li::before {
		content: '•';
		@apply absolute left-0 top-0 font-bold text-indigo-600;
	}

	.todo-list code {
		@apply rounded bg-gray-100 px-1.5 py-0.5 text-xs font-mono text-indigo-600;
	}

	.todo-subtitle {
		@apply mb-2 font-semibold text-gray-900;
	}

	.todo-badge {
		@apply inline-flex w-fit items-center gap-1 rounded-full px-3 py-0.5 text-xs font-semibold uppercase tracking-wide;
	}

	.todo-badge--done {
		@apply bg-green-100 text-green-700;
	}
</style>

```

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
