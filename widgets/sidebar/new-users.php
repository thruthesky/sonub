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
$displayUsers = array_slice($users, 0, 9);
$userCount = count($displayUsers);

// 사용자 검색 컴포넌트 자동 로드
load_deferred_js('vue-components/user-search.component');
?>

<!-- 사용자 검색 컴포넌트 (자동 마운트) -->
<div class="user-search-component"></div>

<!-- 신규 회원 카드 위젯 -->
<div class="mb-4">
    <!-- 헤더 -->
    <div class="mb-3">
        <h5 class="mb-1 fw-bold text-dark"><?= t()->신규_회원 ?></h5>
        <p class="mb-0 text-muted" style="font-size: 0.875rem;"><?= t()->최근_가입자 ?></p>
    </div>

    <?php if ($userCount === 0): ?>
        <!-- 빈 상태 -->
        <div class="alert alert-info d-flex align-items-center gap-2 py-3">
            <i class="fa-regular fa-circle-user fs-5"></i>
            <div>
                <div><?= t()->아직_회원이_없습니다 ?></div>
                <small class="text-muted"><?= t()->곧_다시_확인해주세요 ?></small>
            </div>
        </div>
    <?php else: ?>
        <!-- 사용자 카드 그리드 -->
        <div class="row g-2">
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
                <div class="col-6 col-md-4">
                    <a href="<?= href()->user->profile ?>?id=<?= $user['id'] ?>" class="text-decoration-none new-user-card">
                        <div class="card border-0 shadow-sm h-100 new-user-card-inner">
                            <!-- 사용자 아바타 이미지 또는 플레이스홀더 -->
                            <?php if ($photo_url): ?>
                                <img src="<?= $photo_url ?>"
                                     alt="<?= htmlspecialchars($full_name) ?>"
                                     class="card-img-top new-user-avatar"
                                     style="height: 140px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top new-user-avatar bg-light d-flex align-items-center justify-content-center"
                                     style="height: 140px; background: linear-gradient(135deg, #f5f5f5 0%, #e9ecef 100%);">
                                    <i class="fa-solid fa-user text-muted" style="font-size: 2.5rem;"></i>
                                </div>
                            <?php endif; ?>

                            <!-- 사용자 이름 -->
                            <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                <h6 class="card-title mb-0 text-center text-truncate text-dark"
                                    title="<?= htmlspecialchars($full_name) ?>"
                                    style="font-size: 0.875rem; font-weight: 500;">
                                    <?= htmlspecialchars($full_name) ?>
                                </h6>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- 사용자 카드 스타일 -->
<style>
.new-user-card {
    color: inherit;
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
}

.new-user-card:hover {
    text-decoration: none;
}

.new-user-card:hover .new-user-card-inner {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-4px);
}

.new-user-avatar {
    border-radius: 0.375rem 0.375rem 0 0;
}

/* 반응형 조정 */
@media (max-width: 576px) {
    .new-user-avatar {
        height: 100px !important;
    }
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