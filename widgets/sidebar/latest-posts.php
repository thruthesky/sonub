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
$result = list_posts(['limit' => 10]);
?>

<!-- Bootstrap 레이아웃 유틸리티 클래스 사용 -->
<div class="latest-posts-widget">
    <!-- 제목 영역 -->
    <div class="d-flex align-items-center mb-3">
        <i class="fa-solid fa-newspaper me-2 widget-icon"></i>
        <h6 class="widget-title mb-0"><?= t()->최신_게시글 ?></h6>
    </div>

    <?php if (empty($result->posts)): ?>
        <!-- 게시글이 없을 때 -->
        <div class="d-flex align-items-center justify-content-center empty-state">
            <i class="fa-regular fa-folder-open me-2"></i>
            <span><?= t()->게시글이_없습니다 ?></span>
        </div>
    <?php else: ?>
        <!-- 게시글 목록 -->
        <div class="d-flex flex-column gap-2">
            <?php foreach ($result->posts as $post): ?>
                <?php
                // 제목이 있으면 제목, 없으면 내용 사용
                $displayText = !empty($post->title) ? $post->title : strip_tags($post->content);
                ?>
                <a href="<?= href()->post->view($post->id) ?>" class="post-item d-flex align-items-start gap-2">
                    <i class="fa-solid fa-angle-right post-icon"></i>
                    <span class="post-title"><?= htmlspecialchars($displayText) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- 위젯 전용 스타일 - 심플하고 단조로운 디자인 -->
<style>
/*
 * 최신 게시글 위젯 스타일
 * - 심플하고 단조로운 디자인
 * - Bootstrap 기본 색상 변수 사용
 * - Shadow 최소화
 * - 충분한 여백
 */

.latest-posts-widget {
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.25rem;
    /* Shadow 최소화 - 미세한 그림자만 */
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* 제목 영역 */
.latest-posts-widget .widget-title {
    color: var(--bs-emphasis-color);
    font-size: 1rem;
    font-weight: 600;
}

.latest-posts-widget .widget-icon {
    color: var(--bs-primary);
    font-size: 1.1rem;
}

/* 빈 상태 */
.latest-posts-widget .empty-state {
    padding: 2rem 0;
    color: var(--bs-secondary);
    font-size: 0.9rem;
}

.latest-posts-widget .empty-state i {
    font-size: 1.2rem;
}

/* 게시글 항목 */
.latest-posts-widget .post-item {
    text-decoration: none;
    color: var(--bs-body-color);
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.latest-posts-widget .post-item:hover {
    background-color: var(--bs-light);
    color: var(--bs-primary);
}

/* 게시글 아이콘 */
.latest-posts-widget .post-icon {
    color: var(--bs-secondary);
    font-size: 0.8rem;
    margin-top: 0.2rem;
    flex-shrink: 0;
}

.latest-posts-widget .post-item:hover .post-icon {
    color: var(--bs-primary);
}

/* 게시글 제목 */
.latest-posts-widget .post-title {
    flex: 1;
    font-size: 0.875rem;
    line-height: 1.5;
    /* 2줄까지 표시, 나머지는 ... */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    .latest-posts-widget {
        padding: 1rem;
    }

    .latest-posts-widget .post-title {
        font-size: 0.8rem;
    }
}
</style>