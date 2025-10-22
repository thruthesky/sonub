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

<!-- Bootstrap 레이아웃 유틸리티 클래스 사용 -->
<div class="quick-user-menu-widget">
    <?php if ($user): ?>
        <!-- 메뉴 항목 리스트 -->
        <nav class="menu-list">
            <!-- 사용자 프로필 -->
            <a href="<?= href()->user->profile ?>?id=<?= $user->id ?>" class="menu-item">
                <div class="menu-icon">
                    <?php login_user_profile_photo() ?>
                </div>
                <span class="menu-text"><?= htmlspecialchars($user->display_name) ?></span>
            </a>

            <!-- 내 친구 -->
            <a href="<?= href()->friend->list ?>" class="menu-item">
                <div class="menu-icon icon-friends">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <span class="menu-text"><?= t()->내_친구 ?></span>
            </a>

            <!-- 설정 -->
            <a href="<?= href()->user->profile_edit ?>" class="menu-item">
                <div class="menu-icon icon-settings">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <span class="menu-text"><?= t()->설정 ?></span>
            </a>
        </nav>
    <?php else: ?>
        <!-- 로그인 안 한 경우 -->
        <div class="login-prompt">
            <i class="fa-solid fa-user-lock"></i>
            <p class="login-text"><?= t()->로그인이_필요합니다 ?></p>
            <a href="<?= href()->user->login ?>" class="btn btn-primary btn-sm">
                <?= t()->로그인 ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- 위젯 전용 스타일 - Facebook 스타일 디자인 -->
<style>
    /*
 * 빠른 사용자 메뉴 위젯 스타일
 * - Facebook 스타일 깔끔한 디자인
 * - Bootstrap 기본 색상 변수 사용
 * - Shadow 최소화
 */

    .quick-user-menu-widget {
        background-color: white;
        border-radius: 8px;
    }

    /* 메뉴 리스트 */
    .quick-user-menu-widget .menu-list {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 0;
        margin: 0;
    }

    /* 메뉴 항목 - Facebook 스타일 */
    .quick-user-menu-widget .menu-item {
        display: flex;
        align-items: center;
        padding: 8px;
        text-decoration: none;
        color: var(--bs-body-color);
        border-radius: 8px;
        transition: background-color 0.15s ease;
    }

    .quick-user-menu-widget .menu-item:hover {
        background-color: #f0f2f5;
    }

    /* 메뉴 아이콘 */
    .quick-user-menu-widget .menu-icon {
        width: 36px;
        height: 36px;
        margin-right: 12px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
    }

    /* 사용자 아바타 */
    .quick-user-menu-widget .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    .quick-user-menu-widget .user-avatar-placeholder {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #e4e6eb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quick-user-menu-widget .user-avatar-placeholder i {
        font-size: 18px;
        color: #65676b;
    }

    /* 아이콘 배경 - Facebook 스타일 컬러 */
    .quick-user-menu-widget .icon-friends {
        background: linear-gradient(135deg, #5b9dd9, #4a8dc9);
    }

    .quick-user-menu-widget .icon-settings {
        background: linear-gradient(135deg, #8b9dc3, #6b7fa3);
    }

    .quick-user-menu-widget .menu-icon i {
        font-size: 18px;
        color: white;
    }

    /* 메뉴 텍스트 */
    .quick-user-menu-widget .menu-text {
        font-size: 15px;
        font-weight: 500;
        color: #050505;
        line-height: 1.3333;
    }

    /* 로그인 프롬프트 */
    .quick-user-menu-widget .login-prompt {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        text-align: center;
    }

    .quick-user-menu-widget .login-prompt i {
        font-size: 48px;
        color: #65676b;
        margin-bottom: 12px;
    }

    .quick-user-menu-widget .login-text {
        font-size: 14px;
        color: #65676b;
        margin-bottom: 16px;
    }

    .quick-user-menu-widget .btn {
        font-size: 14px;
        font-weight: 600;
        padding: 8px 16px;
    }

    /* 반응형 디자인 */
    @media (max-width: 768px) {
        .quick-user-menu-widget .menu-item {
            padding: 6px;
        }

        .quick-user-menu-widget .menu-icon {
            width: 32px;
            height: 32px;
            margin-right: 10px;
        }

        .quick-user-menu-widget .user-avatar,
        .quick-user-menu-widget .user-avatar-placeholder {
            width: 32px;
            height: 32px;
        }

        .quick-user-menu-widget .menu-text {
            font-size: 14px;
        }
    }
</style>