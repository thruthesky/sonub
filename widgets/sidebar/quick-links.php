<?php

/**
 * Quick Links Widget
 *
 * Displays quick access links for important pages.
 */
?>

<div class="quick-links-widget">
    <h5 class="widget-title mb-3">Quick Links</h5>

    <div class="links-container">
        <a href="<?= href()->help->privacy ?>" class="quick-link-item">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Privacy</span>
        </a>
        <a href="<?= href()->help->terms_and_conditions ?>" class="quick-link-item">
            <i class="fa-solid fa-file-contract"></i>
            <span>Terms</span>
        </a>
        <a href="<?= href()->adv->intro ?>" class="quick-link-item">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Advertising</span>
        </a>
        <a href="<?= href()->help->about ?>" class="quick-link-item">
            <i class="fa-solid fa-ellipsis"></i>
            <span>About</span>
        </a>
    </div>
</div>

<style>
    /* Quick Links Widget - Compact and Clean Design */
    .quick-links-widget {
        background-color: white;
        padding: 0;
    }

    .quick-links-widget .widget-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--bs-emphasis-color);
        margin-bottom: 0;
        padding: 0;
    }

    /* Links Container - Inline with wrapping */
    .quick-links-widget .links-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1rem;
        margin-top: 0.5rem;
    }

    /* Quick Link Item - Inline compact */
    .quick-links-widget .quick-link-item {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0;
        text-decoration: none;
        color: var(--bs-body-color);
        transition: color 0.2s ease;
        white-space: nowrap;
    }

    .quick-links-widget .quick-link-item:hover {
        color: var(--bs-primary);
    }

    /* Icon */
    .quick-links-widget .quick-link-item i {
        font-size: 0.875rem;
        width: 16px;
        text-align: center;
        color: var(--bs-secondary);
    }

    .quick-links-widget .quick-link-item:hover i {
        color: var(--bs-primary);
    }

    /* Link Text */
    .quick-links-widget .quick-link-item span {
        font-size: 0.8rem;
        font-weight: 400;
    }
</style>