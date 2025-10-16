<?php
/**
 * New Users Widget
 *
 * Displays the 16 most recently registered users in a compact 4x4 grid.
 */

// Get 16 most recent users
$result = list_users(['page' => 1, 'per_page' => 16]);
$users = $result['users'] ?? [];
$displayUsers = array_slice($users, 0, 16);
$userCount = count($displayUsers);
?>

<div class="new-users-widget">
    <div class="widget-header">
        <div class="widget-heading">
            <h5 class="widget-title">New Members</h5>
            <p class="widget-subtitle">Latest arrivals</p>
        </div>
        <?php if ($userCount > 0): ?>
            <span class="widget-count"><?= $userCount ?></span>
        <?php endif; ?>
    </div>

    <?php if ($userCount === 0): ?>
        <div class="empty-state">
            <i class="fa-regular fa-circle-user"></i>
            <p>No members yet.<br><span>Check back soon.</span></p>
        </div>
    <?php else: ?>
        <div class="users-grid">
            <?php foreach ($displayUsers as $user): ?>
                <a href="<?= href()->user->profile ?>?id=<?= $user['id'] ?>" class="user-item">
                    <div class="user-avatar">
                        <?php if (!empty($user['photo_url'])): ?>
                            <img src="<?= htmlspecialchars($user['photo_url']) ?>"
                                 alt="<?= htmlspecialchars($user['display_name']) ?>">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="user-name"><?= htmlspecialchars($user['display_name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* New users widget styles - Clean and Modern Design */
.new-users-widget {
    background: white;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 0;
    overflow: hidden;
}

.new-users-widget .widget-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.25rem 0.75rem 1.25rem;
    background: var(--bs-light);
    border-bottom: 1px solid var(--bs-border-color);
}

.new-users-widget .widget-heading {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.new-users-widget .widget-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
}

.new-users-widget .widget-subtitle {
    margin: 0;
    text-transform: uppercase;
    font-size: 0.65rem;
    letter-spacing: 0.1em;
    font-weight: 500;
    color: var(--bs-secondary);
}

.new-users-widget .widget-count {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 2.25rem;
    height: 2.25rem;
    padding: 0 0.5rem;
    border-radius: 50%;
    background: var(--bs-primary);
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
}

.new-users-widget .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.5rem;
    text-align: center;
    color: rgba(15, 23, 42, 0.6);
    background: rgba(148, 163, 184, 0.08);
    border-radius: 10px;
    border: 1px dashed rgba(15, 23, 42, 0.12);
}

.new-users-widget .empty-state i {
    font-size: 1.8rem;
    color: rgba(59, 130, 246, 0.65);
}

.new-users-widget .empty-state span {
    font-size: 0.8rem;
    color: rgba(15, 23, 42, 0.45);
}

/* Grid layout - No gaps, clean borders */
.new-users-widget .users-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    padding: 0;
}

/* User item - Minimal design */
.new-users-widget .user-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 0.5rem;
    text-decoration: none;
    color: var(--bs-body-color);
    background: white;
    border-right: 1px solid var(--bs-border-color);
    border-bottom: 1px solid var(--bs-border-color);
    transition: background-color 0.2s ease, color 0.2s ease;
}

.new-users-widget .user-item:nth-child(4n) {
    border-right: none;
}

.new-users-widget .user-item:nth-last-child(-n+4) {
    border-bottom: none;
}

.new-users-widget .user-item:hover {
    background-color: var(--bs-light);
    color: var(--bs-primary);
}

/* User avatar - Larger and cleaner */
.new-users-widget .user-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--bs-border-color);
    background: var(--bs-light);
}

.new-users-widget .user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Avatar placeholder (no photo) */
.new-users-widget .avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bs-light);
    color: var(--bs-secondary);
    font-size: 1.5rem;
}

/* User name - More visible and clear */
.new-users-widget .user-name {
    font-size: 0.8rem;
    font-weight: 500;
    text-align: center;
    color: var(--bs-body-color);
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    max-width: 100%;
    transition: color 0.2s ease;
}

.new-users-widget .user-item:hover .user-name {
    color: var(--bs-primary);
    font-weight: 600;
}

/* Responsive design */
@media (max-width: 768px) {
    .new-users-widget .widget-header {
        padding: 1rem 1rem 0.625rem 1rem;
    }

    .new-users-widget .user-item {
        padding: 0.875rem 0.4rem;
    }

    .new-users-widget .user-avatar {
        width: 48px;
        height: 48px;
    }

    .new-users-widget .user-name {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .new-users-widget .widget-header {
        padding: 0.875rem 0.875rem 0.5rem 0.875rem;
    }

    .new-users-widget .user-item {
        padding: 0.75rem 0.3rem;
    }

    .new-users-widget .user-avatar {
        width: 44px;
        height: 44px;
    }

    .new-users-widget .user-name {
        font-size: 0.7rem;
    }
}
</style>
