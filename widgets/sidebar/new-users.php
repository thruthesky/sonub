<?php

/**
 * New Users Widget (신규 회원 위젯)
 *
 * 최근 가입한 16명의 사용자를 4x4 그리드로 표시합니다.
 */

// 다국어 번역 함수 호출 (반드시 페이지 로직 전에 호출)
inject_new_users_widget_language();

// 최근 가입한 사용자 16명 가져오기
$result = list_users(['page' => 1, 'per_page' => 16]);
$users = $result['users'] ?? [];
$displayUsers = array_slice($users, 0, 6);
$userCount = count($displayUsers);

// 사용자 검색 컴포넌트 자동 로드
load_deferred_js('vue-components/user-search.component');
?>

<!-- 사용자 검색 컴포넌트 (자동 마운트) -->
<div class="user-search-component"></div>

<!-- 신규 회원 위젯 - Bootstrap Card -->
<div class="card shadow-sm">
    <!-- 카드 바디 -->
    <div class="card-body">
        <!-- 헤더 영역 -->
        <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
            <div class="flex-grow-1">
                <h6 class="card-title mb-1 fw-bold"><?= t()->신규_회원 ?></h6>
                <p class="card-text text-muted mb-0 small"><?= t()->최근_가입자 ?></p>
            </div>
            <i class="fa-solid fa-users text-primary fs-5"></i>
        </div>

        <?php if ($userCount === 0): ?>
            <!-- 빈 상태 -->
            <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                <i class="fa-regular fa-circle-user fs-5"></i>
                <div>
                    <div><?= t()->아직_회원이_없습니다 ?></div>
                    <small class="text-muted"><?= t()->곧_다시_확인해주세요 ?></small>
                </div>
            </div>
        <?php else: ?>
            <!-- 사용자 리스트 (최대 9명) -->
            <div class="d-flex flex-column gap-3">
                <?php foreach ($displayUsers as $user): ?>
                    <?php
                    // 전체 이름 생성
                    $name_parts = [];
                    if (!empty($user['first_name'])) $name_parts[] = $user['first_name'];
                    if (!empty($user['middle_name'])) $name_parts[] = $user['middle_name'];
                    if (!empty($user['last_name'])) $name_parts[] = $user['last_name'];
                    $full_name = !empty($name_parts) ? implode(' ', $name_parts) : t()->익명;
                    $photo_url = !empty($user['photo_url']) ? htmlspecialchars($user['photo_url']) : null;
                    ?>
                    <a href="<?= href()->user->profile ?>?id=<?= $user['id'] ?>"
                        class="d-flex align-items-center gap-2 rounded text-decoration-none user-item-link">
                        <!-- 프로필 이미지 (원형) -->
                        <?php if ($photo_url): ?>
                            <img src="<?= $photo_url ?>"
                                alt="<?= htmlspecialchars($full_name) ?>"
                                class="rounded-circle"
                                style="width: 48px; height: 48px; object-fit: cover; flex-shrink: 0;">
                        <?php else: ?>
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fa-solid fa-user text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <!-- 사용자 이름 -->
                        <div class="flex-grow-1">
                            <div class="fw-medium text-dark small"><?= htmlspecialchars($full_name) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .user-item-link {
        transition: all 0.2s ease;
    }

    .user-item-link:hover {
        background-color: #f8f9fa;
        transform: translateX(4px);
    }
</style>

<?php
/**
 * 신규 회원 위젯 다국어 번역 주입
 */
function inject_new_users_widget_language(): void
{
    t()->inject([
        '신규_회원' => [
            'ko' => '신규 회원',
            'en' => 'New Members',
            'ja' => '新規メンバー',
            'zh' => '新会员',
        ],
        '최근_가입자' => [
            'ko' => '최근 가입자',
            'en' => 'Latest arrivals',
            'ja' => '最新メンバー',
            'zh' => '最新成员',
        ],
        '아직_회원이_없습니다' => [
            'ko' => '아직 회원이 없습니다.',
            'en' => 'No members yet.',
            'ja' => 'まだメンバーがいません。',
            'zh' => '还没有会员。',
        ],
        '곧_다시_확인해주세요' => [
            'ko' => '곧 다시 확인해주세요.',
            'en' => 'Check back soon.',
            'ja' => 'もう一度確認してください。',
            'zh' => '请稍后再查看。',
        ],
        '사용자' => [
            'ko' => '사용자',
            'en' => 'User',
            'ja' => 'ユーザー',
            'zh' => '用户',
        ],
        '익명' => [
            'ko' => '익명',
            'en' => 'Anonymous',
            'ja' => '匿名',
            'zh' => '匿名',
        ],
    ]);
}
?>