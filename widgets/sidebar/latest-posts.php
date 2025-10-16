<?php
/**
 * Latest Posts Widget
 *
 * Displays the most recent posts using list_posts() function.
 */

// Get latest 10 posts
$result = list_posts(['limit' => 10]);
$posts = $result->posts ?? [];
?>

<div class="latest-posts-widget">
    <h5 class="widget-title">Latest Posts</h5>

    <?php if (empty($posts)): ?>
        <p class="empty-message">No posts yet.</p>
    <?php else: ?>
        <div class="posts-list">
            <?php foreach ($posts as $post): ?>
                <a href="<?= href()->post->view ?>?id=<?= $post['id'] ?>" class="post-item">
                    <div class="post-content">
                        <?php if (!empty($post['title'])): ?>
                            <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($post['content'])): ?>
                            <div class="post-excerpt">
                                <?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 60)) ?>
                                <?= mb_strlen(strip_tags($post['content'])) > 60 ? '...' : '' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="post-meta">
                        <span class="post-time"><?= date('M d', $post['created_at']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* Latest Posts Widget - Clean and Compact Design */
.latest-posts-widget {
    background: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.25rem;
}

.latest-posts-widget .widget-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    margin-bottom: 1rem;
}

.latest-posts-widget .empty-message {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    text-align: center;
    padding: 1rem 0;
    margin: 0;
}

/* Posts List */
.latest-posts-widget .posts-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Post Item */
.latest-posts-widget .post-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 6px;
    text-decoration: none;
    color: var(--bs-body-color);
    transition: all 0.2s ease;
}

.latest-posts-widget .post-item:hover {
    background: white;
    border-color: var(--bs-primary);
}

/* Post Content */
.latest-posts-widget .post-content {
    flex: 1;
    min-width: 0;
}

.latest-posts-widget .post-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.latest-posts-widget .post-item:hover .post-title {
    color: var(--bs-primary);
}

.latest-posts-widget .post-excerpt {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    line-height: 1.4;
}

/* Post Meta */
.latest-posts-widget .post-meta {
    flex-shrink: 0;
}

.latest-posts-widget .post-time {
    font-size: 0.7rem;
    color: var(--bs-secondary);
    white-space: nowrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .latest-posts-widget {
        padding: 1rem;
    }

    .latest-posts-widget .posts-list {
        gap: 0.5rem;
    }

    .latest-posts-widget .post-item {
        padding: 0.625rem;
    }
}
</style>
