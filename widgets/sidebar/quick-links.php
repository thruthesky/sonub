<?php

/**
 * Quick Links Widget
 *
 * Displays quick access links for important pages.
 */
?>

<div class="card border-0 bg-transparent">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= href()->help->privacy ?>"
                class="text-decoration-none text-secondary small">
                Privacy
            </a>
            <span class="text-secondary">·</span>
            <a href="<?= href()->help->terms_and_conditions ?>"
                class="text-decoration-none text-secondary small">
                Terms
            </a>
            <span class="text-secondary">·</span>
            <a href="<?= href()->adv->intro ?>"
                class="text-decoration-none text-secondary small">
                Advertising
            </a>
            <span class="text-secondary">·</span>
            <a href="<?= href()->help->about ?>"
                class="text-decoration-none text-secondary small">
                About
            </a>
        </div>
    </div>
</div>