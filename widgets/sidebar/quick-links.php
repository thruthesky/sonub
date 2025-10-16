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
        <a href="<?= href()->help->advertising ?>" class="quick-link-item">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Advertising</span>
        </a>
        <a href="<?= href()->help->ad_choices ?>" class="quick-link-item">
            <i class="fa-solid fa-sliders"></i>
            <span>Ad Choices</span>
        </a>
        <a href="<?= href()->help->cookies ?>" class="quick-link-item">
            <i class="fa-solid fa-cookie-bite"></i>
            <span>Cookies</span>
        </a>
        <a href="<?= href()->help->more ?>" class="quick-link-item">
            <i class="fa-solid fa-ellipsis"></i>
            <span>More</span>
        </a>
    </div>
</div>

<style>
/* Quick Links Widget - Simple and Clean Design */
.quick-links-widget {
    background-color: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.5rem;
}

.quick-links-widget .widget-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    margin-bottom: 1rem;
}

/* Links Container - Simple Grid Layout */
.quick-links-widget .links-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

/* Quick Link Item */
.quick-links-widget .quick-link-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background-color: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 6px;
    text-decoration: none;
    color: var(--bs-body-color);
    transition: all 0.2s ease;
}

.quick-links-widget .quick-link-item:hover {
    border-color: var(--bs-primary);
    color: var(--bs-primary);
}

/* Icon */
.quick-links-widget .quick-link-item i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

/* Link Text */
.quick-links-widget .quick-link-item span {
    font-size: 0.875rem;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .quick-links-widget .links-container {
        grid-template-columns: 1fr;
    }
}
</style>
