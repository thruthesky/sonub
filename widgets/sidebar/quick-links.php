<?php

/**
 * Quick Links Widget
 *
 * Displays quick access links for important pages.
 */
?>

<div class="card shadow-sm">
    <div class="card-body">
        <!-- 헤더 영역 -->
        <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
            <i class="fa-solid fa-link text-primary fs-5"></i>
            <div class="flex-grow-1">
                <h6 class="card-title mb-0 fw-bold">Quick Links</h6>
            </div>
        </div>

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