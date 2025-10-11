<?php

/**
 * 카테고리 목록 페이지
 *
 * 1차 카테고리(그룹)와 2차 카테고리(항목)를 계층적으로 표시합니다.
 */

// 카테고리 데이터 로드

$categories = get_all_categories();
?>

<div class="container py-5">
    <!-- 페이지 헤더 -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">카테고리</h1>
        <p class="text-muted">관심 있는 카테고리를 선택하여 게시글을 탐색하세요</p>
    </div>

    <!-- 1차 카테고리 그리드 -->
    <div class="row g-4">
        <?php foreach ($categories->getGroups() as $group): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <!-- 1차 카테고리 카드 -->
                <div class="card h-100 shadow-sm border-0">
                    <!-- 카드 헤더 -->
                    <div class="card-header bg-primary bg-gradient text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-folder-fill me-2"></i>
                            <?= htmlspecialchars($group->display_name) ?>
                        </h5>
                    </div>

                    <!-- 카드 바디 -->
                    <div class="card-body">
                        <!-- 2차 카테고리 리스트 -->
                        <div class="list-group list-group-flush">
                            <?php foreach ($group->getCategories() as $category): ?>
                                <a href="<?= href()->post->list(category: $category->category) ?>"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2">
                                    <span>
                                        <i class="bi bi-tag me-2 text-primary"></i>
                                        <?= htmlspecialchars($category->name) ?>
                                    </span>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 카드 푸터 -->
                    <div class="card-footer bg-transparent border-0">
                        <small class="text-muted">
                            <i class="bi bi-list-ul me-1"></i>
                            <?= count($group->getCategories()) ?>개 카테고리
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 통계 정보 -->
    <div class="text-center mt-5 pt-4 border-top">
        <div class="row">
            <div class="col-md-4">
                <div class="p-3">
                    <h3 class="text-primary fw-bold"><?= count($categories->getGroups()) ?></h3>
                    <p class="text-muted mb-0">1차 카테고리</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <?php
                    $total_categories = 0;
                    foreach ($categories->getGroups() as $group) {
                        $total_categories += count($group->getCategories());
                    }
                    ?>
                    <h3 class="text-primary fw-bold"><?= $total_categories ?></h3>
                    <p class="text-muted mb-0">2차 카테고리</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <h3 class="text-primary fw-bold"><i class="bi bi-infinity"></i></h3>
                    <p class="text-muted mb-0">게시글</p>
                </div>
            </div>
        </div>
    </div>
</div>