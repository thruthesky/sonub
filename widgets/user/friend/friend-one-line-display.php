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
<div class="">
    <h5 class="section-title"><?= t()->내_친구_목록 ?></h5>

    <div class="widget-friends-row d-flex gap-3">
        <?php foreach ($displayFriends as $friend): ?>
            <a href="<?= href()->user->profile ?>?id=<?= $friend['id'] ?>" class=" d-flex flex-column align-items-center">
                <?php if (!empty($friend['photo_url'])): ?>
                    <!-- 프로필 사진이 있는 경우 -->
                    <img src="<?= thumbnail($friend['photo_url']) ?>"
                        alt="<?= htmlspecialchars($friend['display_name']) ?>"
                        class="widget-friend-photo w-100 rounded-circle">
                <?php else: ?>
                    <!-- 프로필 사진이 없는 경우 기본 아이콘 -->
                    <div class="widget-friend-placeholder">
                        <i class="bi bi-person fs-4"></i>
                    </div>
                <?php endif; ?>

                <!-- 친구 이름 -->
                <span class="text-center sm"><?= htmlspecialchars($friend['display_name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Divider -->
<hr class="my-4">