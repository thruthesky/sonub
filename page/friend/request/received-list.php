<?php

/**
 * 받은 친구 요청 목록 페이지
 */

$user = login();
$requests = [];
$receivedCount = 0;

if ($user) {
    $requests = get_friend_requests_received([
        'me' => $user->id,
        'limit' => 50,
    ]);
    $receivedCount = count_friend_requests_received(['me' => $user->id]);
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
    ]);
}

inject_friend_request_received_list_language();
?>
TODO: Improve the UI Design
<div class="container ">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="mb-4"><?= t()->받은_친구_요청 ?></h1>

            <?php if (!$user): ?>
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                        <div class="flex-grow-1"><?= t()->로그인이_필요합니다 ?></div>
                        <a class="btn btn-sm btn-primary mt-3 mt-sm-0 ms-sm-3" href="<?= href()->user->login ?>">
                            <?= t()->로그인_페이지로_이동 ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm mb-4 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body d-flex justify-content-between align-items-center py-4">
                        <div class="text-white">
                            <div class="small opacity-75 mb-1"><?= t()->총_받은_요청 ?></div>
                            <div class="fs-3 fw-bold"><?= number_format($receivedCount) ?></div>
                        </div>
                        <div class="text-white opacity-75">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>

                <?php if (empty($requests)): ?>
                    <div class="alert alert-info" role="alert">
                        <?= t()->받은_친구_요청이_없습니다 ?>
                    </div>
                <?php else: ?>
                    <div class="list-group shadow-sm" id="received-requests-list">
                        <?php foreach ($requests as $request): ?>
                            <div class="list-group-item border-0 border-bottom py-3 px-4 hover-bg-light" data-request-id="<?= $request['user_id'] ?>" style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <!-- 프로필 이미지 -->
                                    <div class="flex-shrink-0">
                                        <?php if (!empty($request['photo_url'])): ?>
                                            <img src="<?= htmlspecialchars($request['photo_url']) ?>"
                                                alt="<?= htmlspecialchars($request['display_name']) ?>"
                                                class="rounded-circle border border-2 border-white shadow-sm"
                                                style="width: 56px; height: 56px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light border border-2 border-white shadow-sm d-flex align-items-center justify-content-center"
                                                style="width: 56px; height: 56px;">
                                                <i class="bi bi-person fs-4 text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- 사용자 정보 -->
                                    <div class="flex-grow-1 min-w-0">
                                        <a href="<?= href()->user->profile ?>?id=<?= $request['user_id'] ?>"
                                            class="text-decoration-none fw-semibold text-dark d-block mb-1 text-truncate">
                                            <?= htmlspecialchars($request['display_name'] ?: t()->이름_정보_없음) ?>
                                        </a>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('Y-m-d H:i', $request['updated_at'] ?: $request['created_at']) ?>
                                        </div>
                                    </div>

                                    <!-- 액션 버튼 -->
                                    <div class="d-flex gap-2 flex-shrink-0">
                                        <button class="btn btn-sm btn-success accept-btn d-flex align-items-center gap-1 px-3"
                                            data-user-id="<?= $request['user_id'] ?>"
                                            title="<?= t()->수락 ?>">
                                            <i class="bi bi-check-circle"></i>
                                            <span class="d-none d-sm-inline"><?= t()->수락 ?></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger reject-btn d-flex align-items-center gap-1 px-3"
                                            data-user-id="<?= $request['user_id'] ?>"
                                            title="<?= t()->거절 ?>">
                                            <i class="bi bi-x-circle"></i>
                                            <span class="d-none d-sm-inline"><?= t()->거절 ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
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
