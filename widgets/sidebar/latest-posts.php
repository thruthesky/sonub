<?php

/**
 * Latest Posts Widget
 *
 * Displays the most recent posts using list_posts() function.
 */

// Get latest 10 posts
$result = list_posts(['limit' => 10]);
?>

<div class="latest-posts-widget">
    <h5 class="widget-title">Latest Posts</h5>

    <?php if (empty($result->posts)): ?>
        <p class="empty-message">No posts yet.</p>
    <?php else: ?>
        <div class="posts-list">
            <?php foreach ($result->posts as $post): ?>
                <a href="<?= href()->post->view($post->id) ?>" class="post-item">
                    <?php
                    $displayText = !empty($post->title) ? $post->title : strip_tags($post->content);
                    ?>
                    <span class="post-title"><?= htmlspecialchars($displayText) ?></span>
                    <span class="post-time"><?= date('M d', $post->created_at) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Latest Posts Widget - Ultra Compact Design */
    .latest-posts-widget {
        background: white;
        padding: 0;
    }

    .latest-posts-widget .widget-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--bs-emphasis-color);
        margin-bottom: 0.5rem;
        padding: 0;
    }

    .latest-posts-widget .empty-message {
        font-size: 0.8rem;
        color: var(--bs-secondary);
        padding: 0.5rem 0;
        margin: 0;
    }

    /* Posts List - No gaps */
    .latest-posts-widget .posts-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    /* Post Item - Single line, no borders */
    .latest-posts-widget .post-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding: 0.375rem 0;
        text-decoration: none;
        color: var(--bs-body-color);
        transition: color 0.2s ease;
    }

    .latest-posts-widget .post-item:hover {
        color: var(--bs-primary);
    }

    /* Post Title - Single line with ellipsis */
    .latest-posts-widget .post-title {
        flex: 1;
        font-size: 0.8rem;
        font-weight: 400;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .latest-posts-widget .post-item:hover .post-title {
        font-weight: 500;
    }

    /* Post Time - Compact */
    .latest-posts-widget .post-time {
        flex-shrink: 0;
        font-size: 0.7rem;
        color: var(--bs-secondary);
        white-space: nowrap;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .latest-posts-widget .post-item {
            padding: 0.3rem 0;
        }

        .latest-posts-widget .post-title {
            font-size: 0.75rem;
        }
    }
</style>