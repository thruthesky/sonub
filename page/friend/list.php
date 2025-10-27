<?php

/**
 * 친구 목록 페이지
 *
 * 로그인한 사용자의 친구 목록을 표시합니다.
 */

// 위젯 로드
require_once __DIR__ . '/../../widgets/page-header.php';

$user = login();
$friends = [];
$friendCount = 0;

if ($user) {
    $friends = get_friends([
        'me' => $user->id,
        'limit' => 50,
    ]);
}

/**
 * 친구 목록 페이지 다국어 번역 주입
 */
function inject_friend_list_language(): void
{
    t()->inject([
        '내_친구_목록' => [
            'ko' => '내 친구 목록',
            'en' => 'My Friends',
            'ja' => '友達リスト',
            'zh' => '我的好友',
        ],
        '로그인이_필요합니다' => [
            'ko' => '로그인이 필요합니다',
            'en' => 'Login is required',
            'ja' => 'ログインが必要です',
            'zh' => '需要登录',
        ],
        '로그인_페이지로_이동' => [
            'ko' => '로그인 페이지로 이동',
            'en' => 'Go to login page',
            'ja' => 'ログインページへ移動',
            'zh' => '前往登录页面',
        ],
        '친구가_없습니다' => [
            'ko' => '아직 친구가 없습니다',
            'en' => 'You have no friends yet',
            'ja' => 'まだ友達がいません',
            'zh' => '还没有好友',
        ],
        '친구를_찾아보세요' => [
            'ko' => '새로운 친구를 찾아보세요',
            'en' => 'Find new friends',
            'ja' => '新しい友達を見つけましょう',
            'zh' => '寻找新朋友',
        ],
        '친구_찾기' => [
            'ko' => '친구 찾기',
            'en' => 'Find Friends',
            'ja' => '友達を探す',
            'zh' => '查找好友',
        ],
        '로그인_후_이용_가능' => [
            'ko' => '로그인 후 친구 목록을 확인할 수 있습니다',
            'en' => 'You can check your friends after logging in',
            'ja' => 'ログイン後に友達リストを確認できます',
            'zh' => '登录后可以查看好友列表',
        ],
        '이름_정보_없음' => [
            'ko' => '이름 정보 없음',
            'en' => 'No name provided',
            'ja' => '名前情報なし',
            'zh' => '无姓名信息',
        ],
        '총_친구_수' => [
            'ko' => '총 친구',
            'en' => 'Total friends',
            'ja' => '友達の合計',
            'zh' => '好友总数',
        ],
        '명' => [
            'ko' => '명',
            'en' => '',
            'ja' => '人',
            'zh' => '个',
        ],
        '친구_이후' => [
            'ko' => '친구 이후',
            'en' => 'Friends since',
            'ja' => '友達になってから',
            'zh' => '成为好友后',
        ],
        '메시지_보내기' => [
            'ko' => '메시지 보내기',
            'en' => 'Send Message',
            'ja' => 'メッセージを送る',
            'zh' => '发送消息',
        ],
        '프로필_보기' => [
            'ko' => '프로필 보기',
            'en' => 'View Profile',
            'ja' => 'プロフィールを見る',
            'zh' => '查看资料',
        ],
        '친구_삭제' => [
            'ko' => '친구 삭제',
            'en' => 'Unfriend',
            'ja' => '友達を削除',
            'zh' => '删除好友',
        ],
    ]);
}

inject_friend_list_language();
?>

<?php
// 헤더 위젯 표시
page_header([
    'title' => t()->내_친구_목록,
    'icon' => 'fa-user-group',
    'breadcrumbs' => [
        ['label' => t()->홈, 'url' => href()->home, 'icon' => 'fa-house']
    ],
]);
?>

<div class="row justify-content-center">
    <div class="col-12">
        <?php if (!$user): ?>
            <!-- 로그인 필요 경고 -->
            <div class="border-0">
                <div class="p-4 text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-lock text-warning" style="font-size: 48px;"></i>
                    </div>
                    <h5 class="mb-2"><?= t()->로그인이_필요합니다 ?></h5>
                    <p class="text-muted mb-4"><?= t()->로그인_후_이용_가능 ?></p>
                    <a class="btn btn-primary" href="<?= href()->user->login ?>">
                        <i class="fa-solid fa-sign-in-alt me-2"></i>
                        <?= t()->로그인_페이지로_이동 ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php if (empty($friends)): ?>
                <!-- 빈 상태 -->
                <div class="border-0">
                    <div class="p-5 text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-user-group text-muted" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="mb-2"><?= t()->친구가_없습니다 ?></h5>
                        <p class="text-muted mb-4"><?= t()->친구를_찾아보세요 ?></p>
                        <a class="btn btn-primary" href="<?= href()->friend->find_friend ?>">
                            <i class="fa-solid fa-user-plus me-2"></i>
                            <?= t()->친구_찾기 ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- 친구 목록 -->
                <div class="row g-3">
                    <?php foreach ($friends as $friend): ?>
                        <?php
                        // 전체 이름 생성
                        $name_parts = [];
                        if (!empty($friend['first_name'])) $name_parts[] = $friend['first_name'];
                        if (!empty($friend['middle_name'])) $name_parts[] = $friend['middle_name'];
                        if (!empty($friend['last_name'])) $name_parts[] = $friend['last_name'];
                        $full_name = !empty($name_parts) ? implode(' ', $name_parts) : t()->이름_정보_없음;
                        ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3 d-flex flex-column align-items-center text-center">
                                    <!-- 프로필 이미지 -->
                                    <a href="<?= href()->user->profile ?>?id=<?= $friend['id'] ?>" class="mb-3">
                                        <?php if (!empty($friend['photo_url'])): ?>
                                            <img src="<?= htmlspecialchars($friend['photo_url']) ?>"
                                                alt="<?= htmlspecialchars($full_name) ?>"
                                                class="rounded-circle"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                style="width: 80px; height: 80px;">
                                                <i class="fa-solid fa-user text-secondary" style="font-size: 32px;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>

                                    <!-- 친구 이름 -->
                                    <a href="<?= href()->user->profile ?>?id=<?= $friend['id'] ?>"
                                        class="text-decoration-none d-block mb-2"
                                        style="color: inherit;"
                                        onmouseover="this.style.textDecoration='underline'"
                                        onmouseout="this.style.textDecoration='none'">
                                        <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($full_name) ?></h6>
                                    </a>

                                    <!-- 친구 이후 -->
                                    <div class="text-muted small mb-3">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?= t()->친구_이후 ?> <?= date('Y-m-d', $friend['created_at'] ?? time()) ?>
                                    </div>

                                    <!-- 액션 버튼 -->
                                    <div class="d-flex gap-2 w-100">
                                        <a href="<?= href()->user->profile ?>?id=<?= $friend['id'] ?>"
                                            class="btn btn-sm btn-outline-primary flex-grow-1"
                                            title="<?= t()->프로필_보기 ?>">
                                            <i class="fa-solid fa-user"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary flex-grow-1 message-btn"
                                            data-user-id="<?= $friend['id'] ?>"
                                            title="<?= t()->메시지_보내기 ?>">
                                            <i class="fa-solid fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger unfriend-btn"
                                            data-user-id="<?= $friend['id'] ?>"
                                            data-user-name="<?= htmlspecialchars($full_name) ?>"
                                            title="<?= t()->친구_삭제 ?>">
                                            <i class="fa-solid fa-user-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    ready(() => {
        // 메시지 보내기 버튼 (추후 구현)
        document.querySelectorAll('.message-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.userId;
                alert('메시지 기능은 추후 구현 예정입니다. (User ID: ' + userId + ')');
            });
        });

        // 친구 삭제 버튼 (추후 구현)
        document.querySelectorAll('.unfriend-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;

                if (confirm(userName + '님을 친구 목록에서 삭제하시겠습니까?')) {
                    alert('친구 삭제 기능은 추후 구현 예정입니다. (User ID: ' + userId + ')');
                }
            });
        });
    });
</script>