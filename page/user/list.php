<?php
/**
 * 사용자 목록 페이지
 *
 * 최근 가입한 20명의 사용자를 표시합니다.
 */

// 사용자 목록 조회 (최근 가입한 20명)
$result = list_users(['per_page' => 20, 'page' => 1]);
$users = $result['users'] ?? [];
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">사용자 목록</h1>

            <div class="row g-3">
                <?php if (empty($users)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            등록된 사용자가 없습니다.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <a href="<?= href()->user->profile ?>?id=<?= $user['id'] ?>" class="text-decoration-none">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <!-- 프로필 사진 -->
                                        <?php if (!empty($user['photo_url'])): ?>
                                            <img src="<?= htmlspecialchars($user['photo_url']) ?>"
                                                 class="rounded-circle mb-3"
                                                 style="width: 80px; height: 80px; object-fit: cover;"
                                                 alt="<?= htmlspecialchars($user['display_name']) ?>">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3"
                                                 style="width: 80px; height: 80px;">
                                                <i class="bi bi-person fs-1 text-secondary"></i>
                                            </div>
                                        <?php endif; ?>

                                        <!-- 사용자 이름 -->
                                        <h5 class="card-title mb-1"><?= htmlspecialchars($user['display_name']) ?></h5>

                                        <!-- 가입일 -->
                                        <p class="card-text text-muted small">
                                            가입일: <?= date('Y-m-d', $user['created_at']) ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
