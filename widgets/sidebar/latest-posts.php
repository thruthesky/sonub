<?php

/**
 * 최신 게시글 위젯
 *
 * list_posts() 함수를 사용하여 최신 게시글을 표시합니다.
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
function inject_latest_posts_language(): void
{
    t()->inject([
        '최신_게시글' => [
            'ko' => '최신 게시글',
            'en' => 'Latest Posts',
            'ja' => '最新の投稿',
            'zh' => '最新帖子'
        ],
        '게시글이_없습니다' => [
            'ko' => '게시글이 없습니다',
            'en' => 'No posts yet',
            'ja' => '投稿がありません',
            'zh' => '暂无帖子'
        ],
    ]);
}
inject_latest_posts_language();

// 최신 10개 게시글 조회
$result = list_posts(['limit' => 10, 'visibility' => 'public']);
?>

<!-- Facebook-style Latest Posts Card -->
<div class="card border-0 border-0 bg-transparent">
    <div class="card-body p-3">
        <!-- Header -->
        <h6 class="fw-semibold text-secondary mb-3"><?= t()->최신_게시글 ?></h6>

        <?php if (empty($result->posts)): ?>
            <!-- Empty state -->
            <div class="text-center text-muted py-3">
                <i class="fa-regular fa-folder-open fs-3 mb-2 d-block"></i>
                <span class="small"><?= t()->게시글이_없습니다 ?></span>
            </div>
        <?php else: ?>
            <!-- Posts list -->
            <div class="d-flex flex-column gap-2">
                <?php foreach ($result->posts as $post): ?>
                    <?php
                    $displayText = !empty($post->title) ? $post->title : strip_tags($post->content);
                    ?>
                    <a href="<?= href()->post->view($post->id) ?>"
                        class="d-flex align-items-center gap-2 p-2 rounded text-decoration-none text-dark hover-bg">
                        <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                            <div class="small lh-sm text-truncate">
                                <?= htmlspecialchars($displayText ?: 'Image post') ?>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-secondary" style="font-size: 0.75rem;"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-bg:hover {
        background-color: var(--bs-light);
    }
</style>