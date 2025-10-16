<?php
/**
 * New Users Widget
 *
 * Displays the 16 most recently registered users in a 4x4 grid.
 */

// Get 16 most recent users
$result = list_users(['page' => 1, 'per_page' => 16]);
$users = $result['users'] ?? [];
?>

<div class="new-users-widget">
    <h5 class="widget-title mb-3">New Members</h5>

    <?php if (empty($users)): ?>
        <p class="text-muted text-center">No members yet.</p>
    <?php else: ?>
        <div class="users-grid">
            <?php foreach ($users as $user): ?>
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
/* New users widget styles */
.new-users-widget {
    background-color: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 1.5rem;
}

.new-users-widget .widget-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
    margin-bottom: 1rem;
}

/* 4x4 grid layout */
.new-users-widget .users-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

/* User item */
.new-users-widget .user-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: var(--bs-body-color);
    transition: transform 0.2s ease;
}

.new-users-widget .user-item:hover {
    transform: translateY(-4px);
}

/* User avatar */
.new-users-widget .user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--bs-border-color);
    margin-bottom: 0.5rem;
    background-color: white;
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
    background-color: var(--bs-light);
    color: var(--bs-secondary);
}

.new-users-widget .avatar-placeholder i {
    font-size: 1.5rem;
}

/* User name */
.new-users-widget .user-name {
    font-size: 0.75rem;
    font-weight: 500;
    text-align: center;
    color: var(--bs-body-color);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
}

.new-users-widget .user-item:hover .user-name {
    color: var(--bs-primary);
}

/* Responsive design */
@media (max-width: 768px) {
    .new-users-widget .users-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }

    .new-users-widget .user-avatar {
        width: 50px;
        height: 50px;
    }

    .new-users-widget .user-name {
        font-size: 0.7rem;
    }
}

@media (max-width: 576px) {
    .new-users-widget .users-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
    }

    .new-users-widget .user-avatar {
        width: 45px;
        height: 45px;
    }
}
</style>
