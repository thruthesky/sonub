<?php

/**
 * 받은 친구 요청 목록 페이지
 */

// 위젯 로드
require_once __DIR__ . '/../../../widgets/page-header.php';

$user = login();
$requests = [];
$receivedCount = 0;
$sentCount = 0;

if ($user) {
    $requests = get_friend_requests_received([
        'me' => $user->id,
        'limit' => 50,
    ]);
    $receivedCount = count_friend_requests_received(['me' => $user->id]);
    $sentCount = count_friend_requests_sent(['me' => $user->id]);
}

/**
 * 받은 친구 요청 페이지 다국어 번역 주입
 */
function inject_friend_request_received_list_language(): void
{
    t()->inject([
        '받은_친구_요청' => [
            'ko' => '받은 친구 요청',
            'en' => 'Received Friend Requests',
            'ja' => '受信した友達申請',
            'zh' => '收到的好友请求',
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
        '총_받은_요청' => [
            'ko' => '총 받은 요청',
            'en' => 'Total received requests',
            'ja' => '受信した申請の合計',
            'zh' => '收到的请求总数',
        ],
        '받은_친구_요청이_없습니다' => [
            'ko' => '받은 친구 요청이 없습니다',
            'en' => 'There are no received friend requests',
            'ja' => '受信した友達申請がありません',
            'zh' => '没有收到好友请求',
        ],
        '이름_정보_없음' => [
            'ko' => '이름 정보 없음',
            'en' => 'No name provided',
            'ja' => '名前情報なし',
            'zh' => '无姓名信息',
        ],
        '요청_시간' => [
            'ko' => '요청 시간',
            'en' => 'Requested at',
            'ja' => '申請時間',
            'zh' => '请求时间',
        ],
        '수락' => [
            'ko' => '수락',
            'en' => 'Accept',
            'ja' => '承認',
            'zh' => '接受',
        ],
        '거절' => [
            'ko' => '거절',
            'en' => 'Reject',
            'ja' => '拒否',
            'zh' => '拒绝',
        ],
        '처리중' => [
            'ko' => '처리중...',
            'en' => 'Processing...',
            'ja' => '処理中...',
            'zh' => '处理中...',
        ],
        '친구_요청을_수락했습니다' => [
            'ko' => '친구 요청을 수락했습니다',
            'en' => 'Friend request accepted',
            'ja' => '友達申請を承認しました',
            'zh' => '已接受好友请求',
        ],
        '친구_요청을_거절하시겠습니까' => [
            'ko' => '친구 요청을 거절하시겠습니까?',
            'en' => 'Do you want to reject this friend request?',
            'ja' => '友達申請を拒否しますか？',
            'zh' => '您要拒绝此好友请求吗？',
        ],
        '친구_요청을_거절했습니다' => [
            'ko' => '친구 요청을 거절했습니다',
            'en' => 'Friend request rejected',
            'ja' => '友達申請を拒否しました',
            'zh' => '已拒绝好友请求',
        ],
        '로그인_후_이용_가능' => [
            'ko' => '로그인 후 친구 요청을 확인할 수 있습니다',
            'en' => 'You can check friend requests after logging in',
            'ja' => 'ログイン後に友達申請を確認できます',
            'zh' => '登录后可以查看好友请求',
        ],
        '새로운_요청이_오면_표시' => [
            'ko' => '새로운 요청이 오면 여기에 표시됩니다',
            'en' => 'New requests will appear here',
            'ja' => '新しい申請がここに表示されます',
            'zh' => '新请求将显示在这里',
        ],
    ]);
}

inject_friend_request_received_list_language();
?>

<?php
// 헤더 위젯 표시
page_header([
    'title' => t()->받은_친구_요청,
    'icon' => 'fa-user-plus',
    'breadcrumbs' => [
        ['label' => t()->홈, 'url' => href()->home, 'icon' => 'fa-house']
    ],
]);
?>

<div class="row">
    <div class="col-12">
        <?php if (!$user): ?>
            <!-- 로그인 필요 경고 -->
            <div class="border-0 ">
                <div class="p-4 text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-lock text-warning" style="font-size: 48px;"></i>
                    </div>
                    <h3 class="mb-2"><?= t()->로그인이_필요합니다 ?></h5>
                        <p class="text-muted mb-4"><?= t()->로그인_후_이용_가능 ?></p>
                        <a class="btn btn-primary" href="<?= href()->user->login ?>">
                            <i class="fa-solid fa-sign-in-alt me-2"></i>
                            <?= t()->로그인_페이지로_이동 ?>
                        </a>
                </div>
            </div>
        <?php else: ?>
            <?php if (empty($requests)): ?>
                <!-- 빈 상태 -->
                <div class="border-0 ">
                    <div class="p-5 text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-inbox text-muted" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="mb-2"><?= t()->받은_친구_요청이_없습니다 ?></h5>
                        <p class="text-muted mb-0"><?= t()->새로운_요청이_오면_표시 ?></p>
                    </div>
                </div>
            <?php else: ?>
                <!-- 친구 요청 목록 -->
                <div class="card border-0 shadow-sm">
                    <div class="list-group list-group-flush" id="received-requests-list">
                        <?php foreach ($requests as $request): ?>
                            <?php
                            // 전체 이름 생성
                            $name_parts = [];
                            if (!empty($request['first_name'])) $name_parts[] = $request['first_name'];
                            if (!empty($request['middle_name'])) $name_parts[] = $request['middle_name'];
                            if (!empty($request['last_name'])) $name_parts[] = $request['last_name'];
                            $full_name = !empty($name_parts) ? implode(' ', $name_parts) : t()->이름_정보_없음;
                            ?>
                            <div class="list-group-item p-3 p-sm-4" data-request-id="<?= $request['user_id'] ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- 프로필 이미지 -->
                                    <a href="<?= href()->user->profile ?>?id=<?= $request['user_id'] ?>" class="flex-shrink-0">
                                        <?php if (!empty($request['photo_url'])): ?>
                                            <img src="<?= htmlspecialchars($request['photo_url']) ?>"
                                                alt="<?= htmlspecialchars($full_name) ?>"
                                                class="rounded-circle"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="fa-solid fa-user text-secondary" style="font-size: 24px;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>

                                    <!-- 사용자 정보 -->
                                    <div class="flex-grow-1 min-w-0">
                                        <a href="<?= href()->user->profile ?>?id=<?= $request['user_id'] ?>"
                                            class="text-decoration-none d-block mb-1"
                                            style="color: inherit;"
                                            onmouseover="this.style.textDecoration='underline'"
                                            onmouseout="this.style.textDecoration='none'">
                                            <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($full_name) ?></h6>
                                        </a>
                                        <div class="text-muted small">
                                            <i class="fa-regular fa-clock me-1"></i>
                                            <?= t()->요청_시간 ?>: <?= date('Y-m-d H:i', $request['updated_at'] ?: $request['created_at']) ?>
                                        </div>
                                    </div>

                                    <!-- 액션 버튼 -->
                                    <div class="d-flex gap-2 flex-shrink-0">
                                        <button class="btn btn-success accept-btn"
                                            data-user-id="<?= $request['user_id'] ?>"
                                            title="<?= t()->수락 ?>">
                                            <i class="fa-solid fa-check me-1"></i>
                                            <span class="d-none d-sm-inline"><?= t()->수락 ?></span>
                                        </button>
                                        <button class="btn btn-outline-danger reject-btn"
                                            data-user-id="<?= $request['user_id'] ?>"
                                            title="<?= t()->거절 ?>">
                                            <i class="fa-solid fa-times me-1"></i>
                                            <span class="d-none d-sm-inline"><?= t()->거절 ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    ready(() => {
        // 수락 버튼 이벤트
        document.querySelectorAll('.accept-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = parseInt(this.dataset.userId);
                const listItem = this.closest('[data-request-id]');

                try {
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> <?= t()->처리중 ?>';

                    // 친구 요청 수락
                    await func('accept_friend', {
                        me: <?= $user ? $user->id : 0 ?>,
                        other: userId,
                        auth: true
                    });

                    // 리스트에서 제거 (애니메이션)
                    listItem.style.transition = 'opacity 0.3s, transform 0.3s';
                    listItem.style.opacity = '0';
                    listItem.style.transform = 'translateX(100%)';

                    setTimeout(() => {
                        listItem.remove();

                        // 남은 요청이 없으면 메시지 표시
                        const remainingRequests = document.querySelectorAll('#received-requests-list [data-request-id]');
                        if (remainingRequests.length === 0) {
                            location.reload();
                        }
                    }, 300);

                    alert('<?= t()->친구_요청을_수락했습니다 ?>');
                } catch (error) {
                    console.error('친구 요청 수락 오류:', error);
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-check-circle"></i> <?= t()->수락 ?>';
                }
            });
        });

        // 거절 버튼 이벤트
        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = parseInt(this.dataset.userId);
                const listItem = this.closest('[data-request-id]');

                if (!confirm('<?= t()->친구_요청을_거절하시겠습니까 ?>')) {
                    return;
                }

                try {
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> <?= t()->처리중 ?>';

                    // 친구 요청 거절
                    await func('reject_friend', {
                        me: <?= $user ? $user->id : 0 ?>,
                        other: userId,
                        auth: true
                    });

                    // 리스트에서 제거 (애니메이션)
                    listItem.style.transition = 'opacity 0.3s, transform 0.3s';
                    listItem.style.opacity = '0';
                    listItem.style.transform = 'translateX(-100%)';

                    setTimeout(() => {
                        listItem.remove();

                        // 남은 요청이 없으면 메시지 표시
                        const remainingRequests = document.querySelectorAll('#received-requests-list [data-request-id]');
                        if (remainingRequests.length === 0) {
                            location.reload();
                        }
                    }, 300);

                    alert('<?= t()->친구_요청을_거절했습니다 ?>');
                } catch (error) {
                    console.error('친구 요청 거절 오류:', error);
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-x-circle"></i> <?= t()->거절 ?>';
                }
            });
        });
    });
</script>

<?php
