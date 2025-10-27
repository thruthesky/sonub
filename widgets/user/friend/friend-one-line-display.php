<?php

/**
 * 친구 한 줄 표시 위젯
 *
 * 로그인한 사용자의 친구 목록을 한 줄로 표시합니다.
 * 각 친구는 프로필 사진(위)과 이름(아래)으로 표시되며, 클릭 시 프로필 페이지로 이동합니다.
 * 로그인하지 않았거나 친구가 없으면 아무것도 표시하지 않습니다.
 *
 * @param int $limit 표시할 최대 친구 수 (기본값: 5)
 */
if (!login()) {
    return;
}

// 다국어 번역 주입
inject_friend_one_line_display_language();

$limit = isset($limit) ? (int)$limit : 5;
if ($limit <= 0) {
    $limit = 5;
}

$displayFriends = get_friends([
    'me' => login()->id,
    'limit' => $limit,
]);

if (empty($displayFriends)) {
    return;
}
?>

<!-- My Friends Section -->
<div class="friend-one-line-widget mb-4">
    <h5 class="fw-semibold mb-3"><?= t()->내_친구_목록 ?></h5>

    <div class="d-flex gap-3 overflow-auto pb-2">
        <?php foreach ($displayFriends as $friend): ?>
            <?php
            // 친구 이름 조합 (first_name, middle_name, last_name)
            $name_parts = [];
            if (!empty($friend['first_name'])) $name_parts[] = $friend['first_name'];
            if (!empty($friend['middle_name'])) $name_parts[] = $friend['middle_name'];
            if (!empty($friend['last_name'])) $name_parts[] = $friend['last_name'];
            $full_name = !empty($name_parts) ? implode(' ', $name_parts) : t()->이름_정보_없음;
            ?>
            <a href="<?= href()->user->profile ?>?id=<?= $friend['id'] ?>"
                class="friend-one-line-item text-decoration-none d-flex flex-column align-items-center">
                <?php if (!empty($friend['photo_url'])): ?>
                    <!-- 프로필 사진이 있는 경우 -->
                    <img src="<?= thumbnail($friend['photo_url']) ?>"
                        alt="<?= htmlspecialchars($full_name) ?>"
                        class="friend-one-line-photo rounded-circle mb-2">
                <?php else: ?>
                    <!-- 프로필 사진이 없는 경우 기본 아이콘 -->
                    <div class="friend-one-line-placeholder rounded-circle mb-2 bg-light d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-user text-secondary"></i>
                    </div>
                <?php endif; ?>

                <!-- 친구 이름 -->
                <span class="friend-one-line-name text-center text-dark"><?= htmlspecialchars($full_name) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .friend-one-line-item {
        flex-shrink: 0;
        width: 80px;
    }

    .friend-one-line-photo {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border: 2px solid var(--bs-border-color);
    }

    .friend-one-line-placeholder {
        width: 60px;
        height: 60px;
        border: 2px solid var(--bs-border-color);
    }

    .friend-one-line-placeholder i {
        font-size: 1.5rem;
    }

    .friend-one-line-name {
        font-size: 0.8125rem;
        line-height: 1.2;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-word;
    }

    .friend-one-line-item:hover .friend-one-line-name {
        color: var(--bs-primary) !important;
    }
</style>


<?php
/**
 * 친구 한 줄 표시 위젯 다국어 번역 주입
 */
function inject_friend_one_line_display_language(): void
{
    t()->inject([
        '내_친구_목록' => [
            'ko' => '내 친구 목록',
            'en' => 'My Friends',
            'ja' => '友達リスト',
            'zh' => '我的好友',
        ],
        '이름_정보_없음' => [
            'ko' => '이름 정보 없음',
            'en' => 'No name provided',
            'ja' => '名前情報なし',
            'zh' => '无姓名信息',
        ],
    ]);
}
