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

<!-- 최신 게시글 위젯 - Bootstrap Card -->
<div class="card shadow-sm">
    <!-- 카드 헤더 -->
    <div class="card-header d-flex align-items-center gap-2 border-bottom-0 bg-white">
        <i class="fa-solid fa-newspaper text-primary fs-5"></i>
        <h6 class="mb-0 fw-bold flex-grow-1"><?= t()->최신_게시글 ?></h6>
    </div>

    <!-- 카드 바디 -->
    <div class="card-body">
        <?php if (empty($result->posts)): ?>
            <!-- 게시글이 없을 때 -->
            <div class="text-center text-muted py-3">
                <i class="fa-regular fa-folder-open fs-3 mb-2 d-block"></i>
                <span class="small"><?= t()->게시글이_없습니다 ?></span>
            </div>
        <?php else: ?>
            <!-- 게시글 리스트 -->
            <div class="d-flex flex-column gap-3">
                <?php foreach ($result->posts as $post): ?>
                    <?php
                    // 제목이 있으면 제목, 없으면 내용 사용
                    $displayText = !empty($post->title) ? $post->title : strip_tags($post->content);
                    ?>
                    <a href="<?= href()->post->view($post->id) ?>"
                        class="d-flex gap-2 rounded text-decoration-none "
                        style="align-items: flex-start;">
                        <i class="fa-solid fa-angle-right text-secondary small" style="margin-top: 3px; flex-shrink: 0;"></i>
                        <div class="flex-grow-1">
                            <div class="text-dark small" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($displayText ?: 'Image post') ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>