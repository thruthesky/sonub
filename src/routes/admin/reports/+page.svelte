<script lang="ts">
	/**
	 * 관리자 신고 목록 페이지
	 *
	 * 모든 사용자의 신고를 createdAt 순서로 표시합니다.
	 * 관리자만 접근 가능합니다.
	 */
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	// import DatabaseListView from "$lib/components/DatabaseListView.svelte";
	// import type { ReportWithId } from "$lib/types/report";

	// 라우트 보호: 관리자만 접근 가능
	// 향후 구현: admin role 확인 후 접근 제어

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
			'관리자_신고_목록': '관리자 신고 목록',
			'모든_사용자의_신고를_확인할_수_있습니다': '모든 사용자의 신고를 확인할 수 있습니다'
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
	 * @param report - 신고 데이터
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
</script>

<svelte:head>
	<title>관리자 신고 목록 - Sonub</title>
</svelte:head>

<div class="admin-report-list-page">
	<!-- 페이지 헤더 -->
	<div class="page-header">
		<h1 class="page-title">{t('관리자_신고_목록')}</h1>
		<p class="page-description">{t('모든_사용자의_신고를_확인할_수_있습니다')}</p>
	</div>

	<!-- 신고 목록 -->
	<!--
		향후 구현:
		DatabaseListView 컴포넌트를 사용하여 실시간 신고 목록 표시
		- path="reports"
		- orderBy="createdAt"
		- limitToFirst={20}
		- 페이지네이션 및 무한 스크롤 지원
	-->
	<div class="report-list-container">
		<p class="empty-message">신고 목록이 비어있습니다.</p>
	</div>
</div>

<style>
	/* 페이지 컨테이너 */
	.admin-report-list-page {
		max-width: 900px;
		margin: 0 auto;
		padding: 2rem 1rem;
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
		.admin-report-list-page {
			padding: 1rem 0.5rem;
		}

		.page-title {
			font-size: 1.5rem;
		}
	}
</style>
