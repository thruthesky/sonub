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

<div class="card border-0 bg-transparent">
    <div class="card-body p-2">
        <?php if ($user): ?>
            <!-- Menu items list -->
            <nav class="d-flex flex-column gap-1">
                <!-- User Profile -->
                <a href="<?= href()->user->profile ?>?id=<?= $user->id ?>"
                    class="d-flex align-items-center p-2 rounded text-decoration-none text-dark">
                    <div class="rounded-circle overflow-hidden flex-shrink-0 me-3"
                        style="width: 36px; height: 36px;">
                        <?php login_user_profile_photo() ?>
                    </div>
                    <span class="fw-semibold"><?= htmlspecialchars($user->displayDisplayName()) ?></span>
                </a>

                <!-- My Friends -->
                <a href="<?= href()->friend->list ?>"
                    class="d-flex align-items-center p-2 rounded text-decoration-none text-dark">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3"
                        style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-user-group text-primary fs-5"></i>
                    </div>
                    <span class="fw-semibold"><?= t()->내_친구 ?></span>
                </a>

                <!-- Settings -->
                <a href="<?= href()->user->settings ?>"
                    class="d-flex align-items-center p-2 rounded text-decoration-none text-dark">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3"
                        style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-gear text-secondary fs-5"></i>
                    </div>
                    <span class="fw-semibold"><?= t()->설정 ?></span>
                </a>
            </nav>
        <?php else: ?>
            <!-- Not logged in -->
            <div class="text-center py-4">
                <i class="fa-solid fa-user-lock text-muted mb-3 fs-1"></i>
                <p class="text-muted mb-3 small"><?= t()->로그인이_필요합니다 ?></p>
                <a href="<?= href()->user->login ?>" class="btn btn-primary btn-sm">
                    <?= t()->로그인 ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>