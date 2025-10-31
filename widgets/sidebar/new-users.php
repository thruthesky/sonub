<?php

/**
 * New Users Widget (신규 회원 위젯)
 *
 * 최근 가입한 16명의 사용자를 4x4 그리드로 표시합니다.
 */

// 다국어 번역 함수 호출 (반드시 페이지 로직 전에 호출)
inject_new_users_widget_language();

// 최근 가입한 사용자 16명 가져오기
$result = list_users(['page' => 1, 'per_page' => 6]);
$users = $result['users'] ?? [];
$displayUsers = array_slice($users, 0, 6);
$userCount = count($displayUsers);

// 사용자 검색 컴포넌트 자동 로드
load_deferred_js('vue-components/user-search.component');
?>

<!-- 사용자 검색 컴포넌트 (자동 마운트) -->

<div class="card border-0 bg-transparent">
    <div class="card-body p-3">
        <!-- Header -->
        <h6 class="fw-semibold text-secondary mb-3"><?= t()->신규_회원 ?></h6>

        <?php if ($userCount === 0): ?>
            <!-- Empty state -->
            <div class="text-center text-muted py-3">
                <i class="fa-regular fa-circle-user fs-3 mb-2 d-block"></i>
                <span class="small"><?= t()->아직_회원이_없습니다 ?></span>
            </div>
        <?php else: ?>
            <!-- Users list -->
            <div class="d-flex flex-column gap-2">
                <?php foreach ($displayUsers as $user): ?>
                    <?php
                    $name_parts = [];
                    if (!empty($user['first_name'])) $name_parts[] = $user['first_name'];
                    if (!empty($user['middle_name'])) $name_parts[] = $user['middle_name'];
                    if (!empty($user['last_name'])) $name_parts[] = $user['last_name'];
                    $full_name = !empty($name_parts) ? implode(' ', $name_parts) : t()->익명;
                    $photo_url = !empty($user['photo_url']) ? htmlspecialchars($user['photo_url']) : null;
                    ?>
                    <a href="<?= href()->user->profile ?>?id=<?= $user['id'] ?>"
                        class="d-flex align-items-center gap-3 p-2 rounded text-decoration-none text-dark">
                        <!-- Profile photo -->
                        <?php if ($photo_url): ?>
                            <img src="<?= $photo_url ?>"
                                alt="<?= htmlspecialchars($full_name) ?>"
                                class="rounded-circle flex-shrink-0"
                                style="width: 36px; height: 36px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-user text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <!-- User name -->
                        <div class="flex-grow-1">
                            <div class="fw-semibold small"><?= htmlspecialchars($full_name) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

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