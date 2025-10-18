<?php
/**
 * 사이드바 통계 위젯
 *
 * 사용자, 게시글, 댓글 수를 실시간으로 표시합니다.
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

<div class="card">
    <div class="card-body">
        <h6 class="card-title"><?= t()->통계 ?></h6>
        <dl class="row small">
            <dt class="col-6"><?= t()->사용자 ?></dt>
            <dd class="col-6"><?= number_format($total_users) ?></dd>
            <dt class="col-6"><?= t()->게시글 ?></dt>
            <dd class="col-6"><?= number_format($total_posts) ?></dd>
            <dt class="col-6"><?= t()->댓글 ?></dt>
            <dd class="col-6"><?= number_format($total_comments) ?></dd>
        </dl>
    </div>
</div>

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