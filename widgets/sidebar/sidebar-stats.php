<?php
/**
 * 사이드바 통계 위젯
 *
 * 사용자, 게시글, 댓글 수를 실시간으로 표시합니다.
 * 심플하고 단조로운 디자인으로 현대적인 느낌을 제공합니다.
 *
 * @package Sonub
 * @subpackage Widgets
 * @since 1.0
 */

// 실제 데이터 조회
$total_users = count_users();
$total_posts = count_posts();
$total_comments = count_comments();
?>

<!-- Bootstrap 레이아웃 유틸리티 클래스 사용 -->
<div class="sidebar-stats-widget">
    <div class="d-flex align-items-center mb-3">
        <i class="fa-solid fa-chart-simple me-2 stats-icon"></i>
        <h6 class="stats-title mb-0"><?= t()->통계 ?></h6>
    </div>

    <!-- 통계 항목들 -->
    <div class="d-flex flex-column gap-3">
        <!-- 사용자 -->
        <div class="d-flex align-items-center justify-content-between stat-item">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-users stat-item-icon"></i>
                <span class="stat-label"><?= t()->사용자 ?></span>
            </div>
            <span class="stat-value"><?= number_format($total_users) ?></span>
        </div>

        <!-- 게시글 -->
        <div class="d-flex align-items-center justify-content-between stat-item">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-file-lines stat-item-icon"></i>
                <span class="stat-label"><?= t()->게시글 ?></span>
            </div>
            <span class="stat-value"><?= number_format($total_posts) ?></span>
        </div>

        <!-- 댓글 -->
        <div class="d-flex align-items-center justify-content-between stat-item">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-comments stat-item-icon"></i>
                <span class="stat-label"><?= t()->댓글 ?></span>
            </div>
            <span class="stat-value"><?= number_format($total_comments) ?></span>
        </div>
    </div>
</div>

<!-- 위젯 전용 스타일 - 심플하고 단조로운 디자인 -->
<style>
/*
 * 사이드바 통계 위젯 스타일
 * - 심플하고 단조로운 디자인
 * - Bootstrap 기본 색상 변수 사용
 * - Shadow 최소화
 * - 충분한 여백
 */

.sidebar-stats-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.25rem;
    /* Shadow 최소화 - 미세한 그림자만 */
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* 제목 영역 */
.sidebar-stats-widget .stats-title {
    color: var(--bs-emphasis-color);
    font-size: 1rem;
    font-weight: 600;
}

.sidebar-stats-widget .stats-icon {
    color: var(--bs-primary);
    font-size: 1.1rem;
}

/* 통계 항목 */
.sidebar-stats-widget .stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--bs-border-color);
}

.sidebar-stats-widget .stat-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.sidebar-stats-widget .stat-item-icon {
    color: var(--bs-secondary);
    font-size: 0.9rem;
    width: 1.2rem;
    text-align: center;
}

.sidebar-stats-widget .stat-label {
    color: var(--bs-body-color);
    font-size: 0.9rem;
}

.sidebar-stats-widget .stat-value {
    color: var(--bs-emphasis-color);
    font-size: 1rem;
    font-weight: 600;
}

/* 호버 효과 - 미세한 변화만 */
.sidebar-stats-widget .stat-item:hover {
    background-color: var(--bs-light);
    border-radius: 4px;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    margin-left: -0.5rem;
    margin-right: -0.5rem;
    transition: all 0.2s ease;
}

.sidebar-stats-widget .stat-item:hover .stat-item-icon {
    color: var(--bs-primary);
    transition: color 0.2s ease;
}
</style>

<?php
/**
 * 다국어 번역 주입
 *
 * 이 함수는 위젯에서 사용하는 모든 텍스트를 4개 국어로 번역합니다.
 *
 * @return void
 */
function inject_sidebar_stats_language(): void
{
    t()->inject([
        '통계' => [
            'ko' => '통계',
            'en' => 'Statistics',
            'ja' => '統計',
            'zh' => '统计'
        ],
        '사용자' => [
            'ko' => '사용자',
            'en' => 'Users',
            'ja' => 'ユーザー',
            'zh' => '用户'
        ],
        '게시글' => [
            'ko' => '게시글',
            'en' => 'Posts',
            'ja' => '投稿',
            'zh' => '帖子'
        ],
        '댓글' => [
            'ko' => '댓글',
            'en' => 'Comments',
            'ja' => 'コメント',
            'zh' => '评论'
        ],
    ]);
}
inject_sidebar_stats_language();