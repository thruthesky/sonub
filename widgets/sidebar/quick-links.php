<?php

/**
 * Quick Links Widget
 *
 * Displays quick access links for important pages.
 */
?>

<div class="card shadow-sm">
    <!-- 카드 헤더 -->
    <div class="card-header d-flex align-items-center gap-2 border-bottom-0 bg-white">
        <i class="fa-solid fa-link text-primary fs-5"></i>
        <h6 class="mb-0 fw-bold flex-grow-1">Quick Links</h6>
    </div>

    <!-- 카드 바디 -->
    <div class="card-body">
        <!-- Links Container -->
        <div class="d-flex flex-wrap" style="gap: 1rem;">
            <a href="<?= href()->help->privacy ?>" class="text-decoration-none text-secondary fw-bold small" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Privacy</span>
            </a>
            <a href="<?= href()->help->terms_and_conditions ?>" class="text-decoration-none text-secondary fw-bold small" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                <i class="fa-solid fa-file-contract"></i>
                <span>Terms</span>
            </a>
            <a href="<?= href()->adv->intro ?>" class="text-decoration-none text-secondary fw-bold small" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Advertising</span>
            </a>
            <a href="<?= href()->help->about ?>" class="text-decoration-none text-secondary fw-bold small" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                <i class="fa-solid fa-ellipsis"></i>
                <span>About</span>
            </a>
        </div>
    </div>
</div>