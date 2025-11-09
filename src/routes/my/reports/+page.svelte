<script lang="ts">
	/**
	 * 내 신고 목록 페이지
	 *
	 * 현재 로그인한 사용자가 작성한 신고만 createdAt 순서로 표시합니다.
	 */
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';
	// import DatabaseListView from "$lib/components/DatabaseListView.svelte";
	// import type { ReportWithId } from "$lib/types/report";
	// import { removeReport } from "$lib/services/report";

	/**
	 * 간단한 번역 함수 (i18n 미구현 시 임시 사용)
	 *
	 * @param key - 번역 키
	 * @returns 번역된 문자열
	 */
	function t(key: string): string {
		const translations: Record<string, string> = {
			'신고사유_abuse': '욕설 및 비방',
			'신고사유_fake-news': '허위 정보',
			'신고사유_spam': '스팸',
			'신고사유_inappropriate': '부적절한 콘텐츠',
			'신고사유_other': '기타',
			'게시글': '게시글',
			'댓글': '댓글',
			'신고를취소하시겠습니까': '신고를 취소하시겠습니까?',
			'로그인필요': '로그인이 필요합니다',
			'로그인': '로그인',
			'내_신고_목록': '내 신고 목록',
			'내가_작성한_신고를_확인할_수_있습니다': '내가 작성한 신고를 확인할 수 있습니다'
		};
		return translations[key] || key;
	}

	/**
	 * 신고 사유를 한글로 변환하는 함수
	 *
	 * @param reason - 신고 사유 (abuse, fake-news, spam, inappropriate, other)
	 * @returns 한글 신고 사유
	 */
	function getReasonText(reason: string): string {
		return t(`신고사유_${reason}`);
	}

	/**
	 * 신고 타입을 한글로 변환하는 함수
	 *
	 * @param type - 신고 타입 (post, comment)
	 * @returns 한글 신고 타입
	 */
	function getTypeText(type: string): string {
		return type === 'post' ? t('게시글') : t('댓글');
	}

	/**
	 * 게시글/댓글로 이동하는 함수
	 *
	 * @param type - 신고 대상 타입
	 * @param nodeId - 신고 대상 ID
	 */
	function handleGoToNode(type: string, nodeId: string) {
		if (type === 'post') {
			// 게시글 상세 페이지로 이동
			goto(`/post/detail/${nodeId}`);
		} else {
			// 댓글은 게시글 상세 페이지로 이동 (댓글이 속한 게시글로 이동)
			// 댓글 ID로는 직접 이동할 수 없으므로, 게시글 목록으로 이동
			goto('/post/list');
		}
	}

	/**
	 * 신고 취소 핸들러
	 *
	 * @param reportId - 신고 ID
	 */
	async function handleCancelReport(reportId: string) {
		// 확인 다이얼로그
		if (!confirm(t('신고를취소하시겠습니까'))) {
			return;
		}

		// 향후 구현:
		// removeReport() API 호출
		// Toast 메시지 표시
		// DatabaseListView 실시간 업데이트
	}
</script>

<svelte:head>
	<title>내 신고 목록 - Sonub</title>
</svelte:head>

{#if !authStore.isAuthenticated}
	<!-- 로그인하지 않은 경우 -->
	<div class="my-report-list-page">
		<div class="empty-state">
			<p>{t('로그인필요')}</p>
			<a class="login-btn" href="/user/login">
				{t('로그인')}
			</a>
		</div>
	</div>
{:else}
	<!-- 로그인한 경우 -->
	<div class="my-report-list-page">
		<!-- 페이지 헤더 -->
		<div class="page-header">
			<h1 class="page-title">{t('내_신고_목록')}</h1>
			<p class="page-description">{t('내가_작성한_신고를_확인할_수_있습니다')}</p>
		</div>

		<!-- 신고 목록 -->
		<!--
			향후 구현:
			DatabaseListView 컴포넌트를 사용하여 실시간 신고 목록 표시
			- path="reports"
			- orderBy="createdAt"
			- limitToFirst={20}
			- filter={(item) => item.uid === authStore.user?.uid}
			- 페이지네이션 및 무한 스크롤 지원
		-->
		<div class="report-list-container">
			<p class="empty-message">신고 목록이 비어있습니다.</p>
		</div>
	</div>
{/if}

<style>
	/* 페이지 컨테이너 */
	.my-report-list-page {
		max-width: 900px;
		margin: 0 auto;
		padding: 2rem 1rem;
	}

	/* 빈 상태 (로그인 안 됨) */
	.empty-state {
		text-align: center;
		padding: 3rem 1rem;
	}

	.empty-state p {
		margin-bottom: 1.5rem;
		font-size: 1.1rem;
		color: #6b7280;
	}

	.login-btn {
		display: inline-block;
		padding: 0.75rem 2rem;
		background-color: #3b82f6;
		color: #ffffff;
		border-radius: 0.5rem;
		font-size: 1rem;
		font-weight: 600;
		cursor: pointer;
		transition: background-color 0.2s ease;
		text-decoration: none;
	}

	.login-btn:hover {
		background-color: #2563eb;
	}

	/* 페이지 헤더 */
	.page-header {
		margin-bottom: 2rem;
		padding-bottom: 1rem;
		border-bottom: 2px solid #e5e7eb;
	}

	.page-title {
		margin: 0 0 0.5rem 0;
		font-size: 2rem;
		font-weight: 700;
		color: #111827;
	}

	.page-description {
		margin: 0;
		font-size: 0.95rem;
		color: #6b7280;
	}

	/* 신고 목록 컨테이너 */
	.report-list-container {
		min-height: 400px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.empty-message {
		font-size: 1rem;
		color: #9ca3af;
	}

	/* 반응형 스타일 */
	@media (max-width: 768px) {
		.my-report-list-page {
			padding: 1rem 0.5rem;
		}

		.page-title {
			font-size: 1.5rem;
		}
	}
</style>
