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
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
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
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><?= t()->총_받은_요청 ?></span>
                        <span class="fs-5"><?= number_format($receivedCount) ?></span>
                    </div>
                </div>

                <?php if (empty($requests)): ?>
                    <div class="alert alert-info" role="alert">
                        <?= t()->받은_친구_요청이_없습니다 ?>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($requests as $request): ?>
                            <div class="list-group-item list-group-item-action py-3">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($request['photo_url'])): ?>
                                        <img src="<?= htmlspecialchars($request['photo_url']) ?>" alt="<?= htmlspecialchars($request['display_name']) ?>" class="rounded-circle flex-shrink-0 friend-request-avatar object-fit-cover">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center flex-shrink-0 friend-request-avatar">
                                            <i class="bi bi-person fs-4 text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="ms-3 flex-grow-1">
                                        <a href="<?= href()->user->profile ?>?id=<?= $request['user_id'] ?>" class="text-decoration-none fw-semibold">
                                            <?= htmlspecialchars($request['display_name'] ?: t()->이름_정보_없음) ?>
                                        </a>
                                        <div class="text-muted small mt-1">
                                            <?= t()->요청_시간 ?>:
                                            <?= date('Y-m-d H:i', $request['updated_at'] ?: $request['created_at']) ?>
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
</div>

<?php
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
    ]);
}
