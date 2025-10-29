<?php

/**
 * 카테고리 목록 페이지
 *
 * 1차 카테고리(그룹)와 2차 카테고리(항목)를 계층적으로 표시합니다.
 * 심플하고 단조로운 디자인으로 현대적인 느낌을 제공합니다.
 *
 * @package Sonub
 * @subpackage Pages
 * @since 1.0
 */

// 번역 함수 호출
inject_categories_language();

// 카테고리 데이터 로드
$categories = get_all_categories();
?>

<!-- Bootstrap 레이아웃 유틸리티 클래스 사용 -->
<!-- 페이지 헤더 -->
<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fa-solid fa-layer-group text-primary" style="font-size: 1.5rem;"></i>
        <h1 class="mb-0" style="font-size: 1.75rem;"><?= t()->카테고리 ?></h1>
    </div>
    <p class="text-muted mb-0"><?= t()->관심_있는_카테고리를_선택하여_게시글을_탐색하세요 ?></p>
</div>

<!-- 1차 카테고리 섹션들 - 각 섹션을 한 줄씩 표시 -->
<div class="d-flex flex-column gap-3">
    <?php foreach ($categories->getGroups() as $group): ?>
        <!-- 1차 카테고리 카드 -->
        <div class="card shadow-sm">
            <!-- 카드 헤더 - 1차 카테고리 제목 -->
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fa-solid fa-folder text-primary"></i>
                <h2 class="mb-0 h6 fw-semibold flex-grow-1"><?= htmlspecialchars($group->display_name) ?></h2>
                <span class="badge bg-primary rounded-pill"><?= count($group->getCategories()) ?></span>
            </div>

            <!-- 카드 바디 - 2차 카테고리들 -->
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($group->getCategories() as $category): ?>
                        <a href="<?= href()->post->list(category: $category->category) ?>"
                            class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 text-decoration-none text-dark">
                            <i class="fa-solid fa-angle-right small"></i>
                            <span><?= htmlspecialchars($category->name) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
/**
 * 다국어 번역 주입
 *
 * 이 함수는 페이지에서 사용하는 모든 텍스트를 4개 국어로 번역합니다.
 *
 * @return void
 */
function inject_categories_language(): void
{
    t()->inject([
        '카테고리' => [
            'ko' => '카테고리',
            'en' => 'Categories',
            'ja' => 'カテゴリー',
            'zh' => '分类'
        ],
        '관심_있는_카테고리를_선택하여_게시글을_탐색하세요' => [
            'ko' => '관심 있는 카테고리를 선택하여 게시글을 탐색하세요',
            'en' => 'Select a category to explore posts',
            'ja' => '興味のあるカテゴリーを選択して投稿を探索してください',
            'zh' => '选择感兴趣的分类浏览帖子'
        ],
    ]);
}
