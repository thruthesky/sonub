<?php

/**
 * 다국어 번역 주입
 *
 * 이 함수는 위젯에서 사용하는 모든 텍스트를 4개 국어로 번역합니다.
 *
 * @return void
 */
function inject_quick_user_menu_language(): void
{
    t()->inject([
        '내_프로필' => [
            'ko' => '내 프로필',
            'en' => 'My Profile',
            'ja' => 'マイプロフィール',
            'zh' => '我的资料'
        ],
        '내_친구' => [
            'ko' => '내 친구',
            'en' => 'My Friends',
            'ja' => 'マイフレンド',
            'zh' => '我的朋友'
        ],
        '설정' => [
            'ko' => '설정',
            'en' => 'Settings',
            'ja' => '設定',
            'zh' => '设置'
        ],
        '로그인이_필요합니다' => [
            'ko' => '로그인이 필요합니다',
            'en' => 'Login required',
            'ja' => 'ログインが必要です',
            'zh' => '需要登录'
        ],
        '로그인' => [
            'ko' => '로그인',
            'en' => 'Login',
            'ja' => 'ログイン',
            'zh' => '登录'
        ],
    ]);
}
inject_quick_user_menu_language();

// 로그인 체크
$user = login();
?>

<!-- Bootstrap Card Component -->
<div class="card border-0">
    <div class="card-body">
        <?php if ($user): ?>
            <!-- 메뉴 항목 리스트 -->
            <nav class="d-flex flex-column gap-2">
                <!-- 사용자 프로필 -->
                <a href="<?= href()->user->profile ?>?id=<?= $user->id ?>" class="menu-item d-flex align-items-center p-2 rounded text-decoration-none">
                    <div class="menu-icon icon-user d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; margin-right: 12px; overflow: hidden;">
                        <?php login_user_profile_photo() ?>
                    </div>
                    <span class="menu-text fw-medium" style="font-size: 15px; color: #050505;"><?= htmlspecialchars($user->displayDisplayName()) ?></span>
                </a>

                <!-- 내 친구 -->
                <a href="<?= href()->friend->list ?>" class="menu-item d-flex align-items-center p-2 rounded text-decoration-none">
                    <div class="menu-icon icon-friends d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; margin-right: 12px;">
                        <i class="fa-solid fa-user-group text-white" style="font-size: 18px;"></i>
                    </div>
                    <span class="menu-text fw-medium" style="font-size: 15px; color: #050505;"><?= t()->내_친구 ?></span>
                </a>

                <!-- 설정 -->
                <a href="<?= href()->user->settings ?>" class="menu-item d-flex align-items-center p-2 rounded text-decoration-none">
                    <div class="menu-icon icon-settings d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; margin-right: 12px;">
                        <i class="fa-solid fa-gear text-white" style="font-size: 18px;"></i>
                    </div>
                    <span class="menu-text fw-medium" style="font-size: 15px; color: #050505;"><?= t()->설정 ?></span>
                </a>
            </nav>
        <?php else: ?>
            <!-- 로그인 안 한 경우 -->
            <div class="text-center py-4">
                <i class="fa-solid fa-user-lock text-muted mb-3" style="font-size: 48px;"></i>
                <p class="text-muted mb-3" style="font-size: 14px;"><?= t()->로그인이_필요합니다 ?></p>
                <a href="<?= href()->user->login ?>" class="btn btn-primary btn-sm">
                    <?= t()->로그인 ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* 메뉴 항목 호버 효과 */
    .menu-item {
        transition: background-color 0.2s ease;
    }

    .menu-item:hover {
        background-color: #f8f9fa;
    }

    /* 아이콘 배경 그라디언트 */
    .icon-friends {
        background: linear-gradient(135deg, #5b9dd9, #4a8dc9);
    }

    .icon-settings {
        background: linear-gradient(135deg, #8b9dc3, #6b7fa3);
    }

    .icon-user {
        background: linear-gradient(135deg, #65676b, #6b7fa3);
    }
</style>