<?php

/**
 * Sonub 메뉴 페이지
 *
 * 사용자가 접근할 수 있는 모든 메뉴를 표시합니다.
 */
// 다국어 번역 함수 호출 (반드시 페이지 로직 전에 호출)
inject_menu_intro_language();

$user = login();
?>

<div class="row justify-content-center">
    <div class="col-12">
        <h1 class="text-center fw-bold mb-4"><?= t()->메뉴 ?></h1>

        <!-- 주요 메뉴 -->
        <section class="mb-4">
            <h2 class="h5 fw-semibold mb-3"><?= t()->주요_메뉴 ?></h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                <div class="col">
                    <a href="<?= href()->home ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-house-door"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->홈 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->메인_페이지로_이동 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->post->categories ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-grid"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->게시판 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->카테고리별_게시글_보기 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->user->list ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->사용자_목록 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->가입한_회원_보기 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->chat->rooms ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->채팅 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->채팅방_목록 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <?php if ($user): ?>
            <!-- 내 계정 -->
            <section class="mb-4">
                <h2 class="h5 fw-semibold mb-3"><?= t()->내_계정 ?></h2>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <div class="col">
                        <a href="<?= href()->user->profile ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->내_프로필 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->프로필_보기_및_수정 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?= href()->user->settings ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-gear"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->설정 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->계정_설정 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?= href()->post->list(1, 'my-wall') ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-journal-text"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->내_벽 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->내가_작성한_글_보기 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?= href()->comment->list($user->id) ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-chat-left-text"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->내_댓글 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->내가_작성한_댓글_보기 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?= href()->user->logout_submit ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->로그아웃 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->계정에서_로그아웃 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <!-- 계정 (비로그인) -->
            <section class="mb-4">
                <h2 class="h5 fw-semibold mb-3"><?= t()->계정 ?></h2>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <div class="col">
                        <a href="<?= href()->user->login ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->로그인 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->계정에_로그인 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?= href()->user->register ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-person-plus"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->회원가입 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->새_계정_만들기 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- 도움말 및 정보 -->
        <section class="mb-4">
            <h2 class="h5 fw-semibold mb-3"><?= t()->도움말_및_정보 ?></h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                <div class="col">
                    <a href="<?= href()->help->howto ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-question-circle"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->사용_방법 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->Sonub_사용_가이드 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->help->guideline ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->가이드라인 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->커뮤니티_규칙 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="/about" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->소개 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->Sonub에_대해 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->admin->contact ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->문의하기 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->관리자에게_문의 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->help->terms_and_conditions ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->이용약관 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->서비스_이용약관 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= href()->help->privacy ?>" class="text-decoration-none">
                        <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mb-2"
                                    style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h6 class="card-title fw-semibold mb-1 small"><?= t()->개인정보처리방침 ?></h6>
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->개인정보_보호_정책 ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <?php if ($user && $user->is_admin): ?>
            <!-- 관리자 -->
            <section class="mb-4">
                <h2 class="h5 fw-semibold mb-3"><?= t()->관리자 ?></h2>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <div class="col">
                        <a href="<?= href()->admin->dashboard ?>" class="text-decoration-none">
                            <div class="card shadow-sm h-100 text-center" style="transition: transform 0.2s ease;">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                                    <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mb-2"
                                        style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="bi bi-speedometer2"></i>
                                    </div>
                                    <h6 class="card-title fw-semibold mb-1 small"><?= t()->대시보드 ?></h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;"><?= t()->관리자_대시보드 ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * 메뉴 소개 페이지 다국어 번역 주입
 */
function inject_menu_intro_language(): void
{
    t()->inject([
        '메뉴' => [
            'ko' => '메뉴',
            'en' => 'Menu',
            'ja' => 'メニュー',
            'zh' => '菜单',
        ],
        '주요_메뉴' => [
            'ko' => '주요 메뉴',
            'en' => 'Main Menu',
            'ja' => '主要メニュー',
            'zh' => '主要菜单',
        ],
        '홈' => [
            'ko' => '홈',
            'en' => 'Home',
            'ja' => 'ホーム',
            'zh' => '首页',
        ],
        '메인_페이지로_이동' => [
            'ko' => '메인 페이지로 이동',
            'en' => 'Go to the main page',
            'ja' => 'メインページへ移動',
            'zh' => '前往主页',
        ],
        '게시판' => [
            'ko' => '게시판',
            'en' => 'Boards',
            'ja' => '掲示板',
            'zh' => '论坛',
        ],
        '카테고리별_게시글_보기' => [
            'ko' => '카테고리별 게시글 보기',
            'en' => 'Browse posts by category',
            'ja' => 'カテゴリ別の投稿を見る',
            'zh' => '按分类查看帖子',
        ],
        '사용자_목록' => [
            'ko' => '사용자 목록',
            'en' => 'User List',
            'ja' => 'ユーザー一覧',
            'zh' => '用户列表',
        ],
        '가입한_회원_보기' => [
            'ko' => '가입한 회원 보기',
            'en' => 'View registered members',
            'ja' => '登録したメンバーを見る',
            'zh' => '查看注册会员',
        ],
        '채팅' => [
            'ko' => '채팅',
            'en' => 'Chat',
            'ja' => 'チャット',
            'zh' => '聊天',
        ],
        '채팅방_목록' => [
            'ko' => '채팅방 목록',
            'en' => 'Chat rooms',
            'ja' => 'チャットルーム一覧',
            'zh' => '聊天室列表',
        ],
        '내_계정' => [
            'ko' => '내 계정',
            'en' => 'My Account',
            'ja' => 'マイアカウント',
            'zh' => '我的帐户',
        ],
        '내_프로필' => [
            'ko' => '내 프로필',
            'en' => 'My Profile',
            'ja' => 'マイプロフィール',
            'zh' => '我的资料',
        ],
        '프로필_보기_및_수정' => [
            'ko' => '프로필 보기 및 수정',
            'en' => 'View and edit profile',
            'ja' => 'プロフィールの確認と編集',
            'zh' => '查看并编辑个人资料',
        ],
        '설정' => [
            'ko' => '설정',
            'en' => 'Settings',
            'ja' => '設定',
            'zh' => '设置',
        ],
        '계정_설정' => [
            'ko' => '계정 설정',
            'en' => 'Account settings',
            'ja' => 'アカウント設定',
            'zh' => '帐户设置',
        ],
        '내_벽' => [
            'ko' => '내 벽',
            'en' => 'My Wall',
            'ja' => 'マイウォール',
            'zh' => '我的墙',
        ],
        '내가_작성한_글_보기' => [
            'ko' => '내가 작성한 글 보기',
            'en' => 'View my posts',
            'ja' => '自分の投稿を見る',
            'zh' => '查看我写的帖子',
        ],
        '내_댓글' => [
            'ko' => '내 댓글',
            'en' => 'My Comments',
            'ja' => '自分のコメント',
            'zh' => '我的评论',
        ],
        '내가_작성한_댓글_보기' => [
            'ko' => '내가 작성한 댓글 보기',
            'en' => 'View my comments',
            'ja' => '自分のコメントを見る',
            'zh' => '查看我写的评论',
        ],
        '로그아웃' => [
            'ko' => '로그아웃',
            'en' => 'Log out',
            'ja' => 'ログアウト',
            'zh' => '退出',
        ],
        '계정에서_로그아웃' => [
            'ko' => '계정에서 로그아웃',
            'en' => 'Log out of your account',
            'ja' => 'アカウントからログアウト',
            'zh' => '退出账户',
        ],
        '계정' => [
            'ko' => '계정',
            'en' => 'Account',
            'ja' => 'アカウント',
            'zh' => '帐户',
        ],
        '로그인' => [
            'ko' => '로그인',
            'en' => 'Log in',
            'ja' => 'ログイン',
            'zh' => '登录',
        ],
        '계정에_로그인' => [
            'ko' => '계정에 로그인',
            'en' => 'Log in to your account',
            'ja' => 'アカウントにログイン',
            'zh' => '登录账户',
        ],
        '회원가입' => [
            'ko' => '회원가입',
            'en' => 'Sign up',
            'ja' => '会員登録',
            'zh' => '注册',
        ],
        '새_계정_만들기' => [
            'ko' => '새 계정 만들기',
            'en' => 'Create a new account',
            'ja' => '新しいアカウントを作成',
            'zh' => '创建新账户',
        ],
        '도움말_및_정보' => [
            'ko' => '도움말 및 정보',
            'en' => 'Help & Information',
            'ja' => 'ヘルプと情報',
            'zh' => '帮助与信息',
        ],
        '사용_방법' => [
            'ko' => '사용 방법',
            'en' => 'How to Use',
            'ja' => '使い方',
            'zh' => '使用方法',
        ],
        'Sonub_사용_가이드' => [
            'ko' => 'Sonub 사용 가이드',
            'en' => 'Guide to using Sonub',
            'ja' => 'Sonub の使い方ガイド',
            'zh' => 'Sonub 使用指南',
        ],
        '가이드라인' => [
            'ko' => '가이드라인',
            'en' => 'Guidelines',
            'ja' => 'ガイドライン',
            'zh' => '指南',
        ],
        '커뮤니티_규칙' => [
            'ko' => '커뮤니티 규칙',
            'en' => 'Community rules',
            'ja' => 'コミュニティ規則',
            'zh' => '社区规则',
        ],
        '소개' => [
            'ko' => '소개',
            'en' => 'About',
            'ja' => '紹介',
            'zh' => '介绍',
        ],
        'Sonub에_대해' => [
            'ko' => 'Sonub에 대해',
            'en' => 'About Sonub',
            'ja' => 'Sonub について',
            'zh' => '关于 Sonub',
        ],
        '문의하기' => [
            'ko' => '문의하기',
            'en' => 'Contact',
            'ja' => 'お問い合わせ',
            'zh' => '联系',
        ],
        '관리자에게_문의' => [
            'ko' => '관리자에게 문의',
            'en' => 'Contact the administrator',
            'ja' => '管理者に問い合わせる',
            'zh' => '联系管理员',
        ],
        '이용약관' => [
            'ko' => '이용약관',
            'en' => 'Terms of Service',
            'ja' => '利用規約',
            'zh' => '服务条款',
        ],
        '서비스_이용약관' => [
            'ko' => '서비스 이용약관',
            'en' => 'Service terms',
            'ja' => 'サービス利用規約',
            'zh' => '服务使用条款',
        ],
        '개인정보처리방침' => [
            'ko' => '개인정보처리방침',
            'en' => 'Privacy Policy',
            'ja' => '個人情報処理方針',
            'zh' => '隐私政策',
        ],
        '개인정보_보호_정책' => [
            'ko' => '개인정보 보호 정책',
            'en' => 'Personal data protection policy',
            'ja' => '個人情報保護方針',
            'zh' => '个人信息保护政策',
        ],
        '관리자' => [
            'ko' => '관리자',
            'en' => 'Admin',
            'ja' => '管理者',
            'zh' => '管理员',
        ],
        '대시보드' => [
            'ko' => '대시보드',
            'en' => 'Dashboard',
            'ja' => 'ダッシュボード',
            'zh' => '仪表板',
        ],
        '관리자_대시보드' => [
            'ko' => '관리자 대시보드',
            'en' => 'Admin dashboard',
            'ja' => '管理者ダッシュボード',
            'zh' => '管理员仪表板',
        ],
    ]);
}
