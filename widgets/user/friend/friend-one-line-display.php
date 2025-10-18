<?php

/**
 * 친구 한 줄 표시 위젯
 *
 * 사용자의 친구 목록을 한 줄로 표시합니다.
 * 각 친구는 프로필 사진(위)과 이름(아래)으로 표시되며, 클릭 시 프로필 페이지로 이동합니다.
 *
 * @param array $friends - 친구 목록 배열
 * @param int $limit - 표시할 최대 친구 수 (기본값: 5)
 *
 * @example
 * include __DIR__ . '/widgets/user/friend/friend-one-line-display.php';
 *
 * 필수 데이터 구조:
 * $friends = [
 *     ['id' => 1, 'display_name' => '홍길동', 'photo_url' => 'https://...'],
 *     ['id' => 2, 'display_name' => '김철수', 'photo_url' => ''],
 *     ...
 * ]
 */

// 친구 목록이 없으면 아무것도 표시하지 않음
if (empty($friends) || !is_array($friends)) {
    return;
}

// 표시할 최대 친구 수 설정 (기본값: 5)
$limit = $limit ?? 5;
$displayFriends = array_slice($friends, 0, $limit);

?>

<style>
    /* 친구 한 줄 표시 위젯 스타일 */
    .friend-one-line-display {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }

    .friend-one-line-display .friend-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        min-width: 80px;
        transition: opacity 0.2s;
    }

    .friend-one-line-display .friend-item:hover {
        opacity: 0.8;
    }

    .friend-one-line-display .friend-photo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.5rem;
        border: 2px solid #dee2e6;
    }

    .friend-one-line-display .friend-photo-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: rgba(108, 117, 125, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        border: 2px solid #dee2e6;
    }

    .friend-one-line-display .friend-name {
        font-size: 0.875rem;
        color: #212529;
        text-align: center;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* 스크롤바 스타일 (선택사항) */
    .friend-one-line-display::-webkit-scrollbar {
        height: 6px;
    }

    .friend-one-line-display::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .friend-one-line-display::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .friend-one-line-display::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<div class="friend-one-line-display">
    <?php foreach ($displayFriends as $friend): ?>
        <a href="<?= href()->user->profile ?>?id=<?= $friend['id'] ?>" class="friend-item">
            <?php if (!empty($friend['photo_url'])): ?>
                <!-- 프로필 사진이 있는 경우 -->
                <img src="<?= htmlspecialchars($friend['photo_url']) ?>"
                     alt="<?= htmlspecialchars($friend['display_name']) ?>"
                     class="friend-photo">
            <?php else: ?>
                <!-- 프로필 사진이 없는 경우 기본 아이콘 -->
                <div class="friend-photo-placeholder">
                    <i class="bi bi-person fs-4 text-secondary"></i>
                </div>
            <?php endif; ?>

            <!-- 친구 이름 -->
            <span class="friend-name"><?= htmlspecialchars($friend['display_name']) ?></span>
        </a>
    <?php endforeach; ?>
</div>
