<?php
/**
 * New Users Widget
 *
 * Displays the 16 most recently registered users in a responsive grid.
 */

// Get 16 most recent users
$result = list_users(['page' => 1, 'per_page' => 16]);
$users = $result['users'] ?? [];
$userCount = count($users);
?>

<div class="new-users-widget">
    <div class="widget-header">
        <div class="widget-heading">
            <span class="widget-eyebrow">Latest arrivals</span>
            <h5 class="widget-title">New Members</h5>
        </div>
        <span class="widget-count">
            <?php if ($userCount > 0): ?>
                <?= $userCount ?> <?= $userCount === 1 ? 'new member' : 'new members' ?>
            <?php else: ?>
                Be the first to join
            <?php endif; ?>
        </span>
    </div>

    <?php if ($userCount === 0): ?>
        <div class="empty-state">
            <i class="fa-regular fa-circle-user"></i>
            <p>No members yet.<br><span>Check back soon.</span></p>
        </div>
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
    position: relative;
    background: linear-gradient(155deg, rgba(99, 102, 241, 0.12), rgba(59, 130, 246, 0.04));
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 16px;
    padding: 1.75rem 1.5rem;
    box-shadow: 0 18px 45px -32px rgba(15, 23, 42, 0.55);
    overflow: hidden;
    backdrop-filter: blur(6px);
}

.new-users-widget::after {
    content: "";
    position: absolute;
    top: -60px;
    right: -30px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.28), transparent 65%);
    opacity: 0.6;
    pointer-events: none;
}

.new-users-widget .widget-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 1;
}

.new-users-widget .widget-heading {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.new-users-widget .widget-eyebrow {
    display: inline-block;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.18em;
    font-weight: 600;
    color: rgba(15, 23, 42, 0.55);
}

.new-users-widget .widget-title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--bs-emphasis-color);
}

.new-users-widget .widget-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(15, 23, 42, 0.1);
    font-size: 0.8rem;
    font-weight: 500;
    color: rgba(15, 23, 42, 0.65);
}

.new-users-widget .empty-state {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 2rem;
    text-align: center;
    color: rgba(15, 23, 42, 0.6);
    background: rgba(255, 255, 255, 0.82);
    border-radius: 14px;
    border: 1px dashed rgba(15, 23, 42, 0.12);
}

.new-users-widget .empty-state i {
    font-size: 2rem;
    color: var(--bs-primary);
}

.new-users-widget .empty-state span {
    font-size: 0.8rem;
    color: rgba(15, 23, 42, 0.45);
}

/* Grid layout */
.new-users-widget .users-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1.2rem;
}

/* User item */
.new-users-widget .user-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 1rem;
    text-decoration: none;
    color: var(--bs-body-color);
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(15, 23, 42, 0.1);
    border-radius: 14px;
    box-shadow: 0 12px 30px -26px rgba(15, 23, 42, 0.5);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, color 0.25s ease;
}

.new-users-widget .user-item:hover {
    transform: translateY(-6px);
    border-color: rgba(59, 130, 246, 0.4);
    box-shadow: 0 18px 32px -24px rgba(59, 130, 246, 0.45);
    color: var(--bs-primary);
}

/* User avatar */
.new-users-widget .user-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    overflow: hidden;
    border: 1px solid rgba(59, 130, 246, 0.25);
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.24), rgba(99, 102, 241, 0.14));
    box-shadow: inset 0 3px 6px -4px rgba(15, 23, 42, 0.4);
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
    background: rgba(255, 255, 255, 0.6);
    color: rgba(59, 130, 246, 0.65);
    font-size: 1.5rem;
}

/* User name */
.new-users-widget .user-name {
    font-size: 0.82rem;
    font-weight: 600;
    text-align: center;
    color: var(--bs-emphasis-color);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
    transition: color 0.25s ease;
}

.new-users-widget .user-item:hover .user-name {
    color: var(--bs-primary);
}

/* Responsive design */
@media (max-width: 992px) {
    .new-users-widget {
        padding: 1.5rem;
    }

    .new-users-widget .users-grid {
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .new-users-widget .widget-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .new-users-widget .widget-count {
        align-self: flex-start;
    }

    .new-users-widget .users-grid {
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    }
}

@media (max-width: 576px) {
    .new-users-widget {
        padding: 1.25rem;
    }

    .new-users-widget .users-grid {
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
        gap: 0.85rem;
    }

    .new-users-widget .user-avatar {
        width: 56px;
        height: 56px;
    }

    .new-users-widget .user-item {
        padding: 0.75rem;
    }
}
</style>
