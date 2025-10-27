<?php

/**
 * @param array $options {
 *     @type string $title 페이지 제목
 *     @type string $subtitle 부제목 (선택)
 *     @type string $icon Font Awesome 아이콘 클래스 (기본값: fa-file-lines)
 *     @type array $breadcrumbs Breadcrumb 항목들 (선택)
 *         각 항목: ['label' => '라벨', 'url' => 'URL', 'icon' => '아이콘 클래스']
 * }
 */
function page_header(array $options = []): void
{
    $title = $options['title'] ?? '';
    $subtitle = $options['subtitle'] ?? '';
    $icon = $options['icon'] ?? 'fa-file-lines';

    // Breadcrumb 기본값 설정 (홈만)
    $breadcrumbs = $options['breadcrumbs'] ?? [
        ['label' => t()->홈, 'url' => href()->home, 'icon' => 'fa-house']
    ];

    // 다국어 번역 주입
    inject_page_header_language();
?>

    <!-- Modern Facebook-inspired Header -->
    <div class="page-header-widget">
        <!-- Breadcrumbs - Clean and minimal with icons -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0 page-breadcrumb">
                <?php foreach ($breadcrumbs as $index => $item): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= $item['url'] ?>" class="text-decoration-none text-muted">
                            <?php if (!empty($item['icon'])): ?>
                                <i class="fa-solid <?= $item['icon'] ?> me-1"></i>
                            <?php endif; ?>
                            <?= $item['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">
                    <?= $title ?>
                </li>
            </ol>
        </nav>

        <!-- Page Title - Modern and clean with icon -->
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="page-header-icon-wrapper">
                    <i class="fa-solid <?= $icon ?>"></i>
                </div>
                <div>
                    <h1 class="page-header-title mb-2"><?= $title ?></h1>
                    <?php if (!empty($subtitle)): ?>
                        <p class="text-muted mb-0 page-header-subtitle">
                            <?= $subtitle ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Facebook-inspired Header Widget Styles */

        .page-header-widget {
            background-color: white;
        }

        /* Clean breadcrumb with icons */
        .page-breadcrumb {
            background: none;
            padding: 0;
            font-size: 0.875rem;
        }

        .page-breadcrumb .breadcrumb-item+.breadcrumb-item::before {
            color: var(--bs-secondary-color);
            content: "›";
            font-size: 1.1rem;
        }

        .page-breadcrumb a:hover {
            color: var(--bs-primary) !important;
        }

        .page-breadcrumb i {
            font-size: 0.8125rem;
        }

        /* Icon wrapper - Modern circular badge */
        .page-header-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: var(--bs-primary);
            background: linear-gradient(135deg, var(--bs-primary) 0%, #0056d2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .page-header-icon-wrapper i {
            color: white;
            font-size: 1.25rem;
        }

        /* Modern title styling */
        .page-header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--bs-emphasis-color);
            margin: 0;
            line-height: 1.2;
        }

        .page-header-subtitle {
            font-size: 0.9375rem;
            color: var(--bs-secondary-color);
        }

        .page-header-subtitle i {
            font-size: 0.875rem;
        }

        /* Minimal border */
        .page-header-widget .border-bottom {
            border-color: var(--bs-border-color) !important;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .page-header-icon-wrapper {
                width: 40px;
                height: 40px;
            }

            .page-header-icon-wrapper i {
                font-size: 1.125rem;
            }

            .page-header-title {
                font-size: 1.5rem;
            }

            .page-header-subtitle {
                font-size: 0.875rem;
            }
        }
    </style>

<?php
}


function inject_page_header_language(): void
{
    t()->inject([
        '친구_요청_관리' => [
            'ko' => '친구 요청 관리',
            'en' => 'Manage Friend Requests',
            'ja' => '友達申請を管理',
            'zh' => '管理好友请求',
        ],
        '보낸_요청' => [
            'ko' => '보낸 요청',
            'en' => 'Sent',
            'ja' => '送信済み',
            'zh' => '已发送',
        ],
        '받은_요청' => [
            'ko' => '받은 요청',
            'en' => 'Received',
            'ja' => '受信済み',
            'zh' => '已接收',
        ],
        '홈' => [
            'ko' => '홈',
            'en' => 'Home',
            'ja' => 'ホーム',
            'zh' => '首页',
        ],
        '사용자_목록' => [
            'ko' => '사용자 목록',
            'en' => 'User List',
            'ja' => 'ユーザーリスト',
            'zh' => '用户列表',
        ],
    ]);
}
