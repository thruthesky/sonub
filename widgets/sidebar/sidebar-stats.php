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

// 실제 데이터 조회
$total_users = count_users();
$total_posts = count_posts();
$total_comments = count_comments();
?>

<!-- 통계 위젯 - Bootstrap Card -->
<div class="card mb-4">
    <!-- 카드 헤더 -->
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fa-solid fa-chart-simple text-primary fs-5"></i>
        <h6 class="mb-0 fw-bold flex-grow-1"><?= t()->통계 ?></h6>
    </div>

    <!-- 카드 바디 -->
    <div class="card-body">
        <!-- 통계 리스트 -->
        <div class="d-flex flex-column gap-3">
            <!-- 사용자 -->
            <div class="d-flex align-items-center justify-content-between rounded">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-users text-secondary"></i>
                    <span class="text-dark"><?= t()->사용자 ?></span>
                </div>
                <span class="badge bg-primary rounded-pill"><?= number_format($total_users) ?></span>
            </div>

            <!-- 게시글 -->
            <div class="d-flex align-items-center justify-content-between rounded">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-lines text-secondary"></i>
                    <span class="text-dark"><?= t()->게시글 ?></span>
                </div>
                <span class="badge bg-primary rounded-pill"><?= number_format($total_posts) ?></span>
            </div>

            <!-- 댓글 -->
            <div class="d-flex align-items-center justify-content-between rounded">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-comments text-secondary"></i>
                    <span class="text-dark"><?= t()->댓글 ?></span>
                </div>
                <span class="badge bg-primary rounded-pill"><?= number_format($total_comments) ?></span>
            </div>
        </div>
    </div>
</div>