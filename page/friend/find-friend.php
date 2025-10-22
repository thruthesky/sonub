<?php

/**
 * 친구 찾기 페이지
 *
 * 사용자 목록을 표시하고 성별, 나이, 이름으로 필터링할 수 있습니다.
 */

// list_users() 함수 호출 - $_GET을 직접 전달
$result = list_users(array_merge($_GET, ['per_page' => 10]));

// 결과 추출
$users = $result['users'];
$total_count = $result['total'];
$total_pages = $result['total_pages'];
$per_page = $result['per_page'];
$page = $result['page'];

// 화면 표시용 검색 파라미터
$gender = $_GET['gender'] ?? '';
$age_start = $_GET['age_start'] ?? '';
$age_end = $_GET['age_end'] ?? '';
$name = $_GET['name'] ?? '';

// UserModel 배열로 변환
$user_models = array_map(function ($user_data) {
    return new UserModel($user_data);
}, $users);

// 나이 계산 헬퍼 함수
function calculate_age($birthday)
{
    if (empty($birthday)) return null;
    $birth_date = new DateTime('@' . $birthday);
    $now = new DateTime();
    return $now->diff($birth_date)->y;
}
?>

<div class="container my-5">
    <h1 class="mb-4">친구 찾기</h1>

    <!-- 검색 필터 -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="get" action="" id="search-form">
                <div class="row g-2 align-items-end">
                    <!-- 성별 필터 -->
                    <div class="col-6 col-sm-4 col-md-2">
                        <label for="gender" class="form-label small mb-1">성별</label>
                        <select class="form-select form-select-sm" name="gender" id="gender">
                            <option value="">전체</option>
                            <option value="M" <?= $gender === 'M' ? 'selected' : '' ?>>남성</option>
                            <option value="F" <?= $gender === 'F' ? 'selected' : '' ?>>여성</option>
                        </select>
                    </div>

                    <!-- 나이 범위 -->
                    <div class="col-6 col-sm-4 col-md-3">
                        <label class="form-label small mb-1">나이</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="number"
                                class="form-control"
                                name="age_start"
                                id="age_start"
                                placeholder="24"
                                min="18"
                                max="100"
                                value="<?= $age_start ?? '' ?>">
                            <span class="input-group-text">~</span>
                            <input
                                type="number"
                                class="form-control"
                                name="age_end"
                                id="age_end"
                                placeholder="32"
                                min="18"
                                max="100"
                                value="<?= $age_end ?? '' ?>">
                        </div>
                    </div>

                    <!-- 이름 검색 -->
                    <div class="col-6 col-sm-4 col-md-3">
                        <label for="name" class="form-label small mb-1">이름</label>
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            name="name"
                            id="name"
                            placeholder="이름 입력"
                            value="<?= htmlspecialchars($name) ?>">
                    </div>

                    <!-- 버튼 그룹 -->
                    <div class="col-6 col-sm-12 col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-search me-1"></i>검색
                            </button>
                            <?php if ($gender !== '' || $age_start !== '' || $age_end !== '' || $name !== ''): ?>
                                <a href="?page=friend/find-friend" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 검색 결과 정보 -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="text-muted">전체 <?= number_format($total_count) ?>명</span>
            <?php if ($page > 1): ?>
                <span class="text-muted"> · 페이지 <?= $page ?> / <?= $total_pages ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 사용자 목록 -->
    <?php if (empty($user_models)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            검색 조건에 맞는 사용자가 없습니다.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($user_models as $user): ?>
                <?php $age = calculate_age($user->birthday); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <!-- 프로필 사진 -->
                            <div class="text-center mb-3">
                                <a href="?page=user/profile&id=<?= $user->id ?>">
                                    <?php if (!empty($user->photo_url)): ?>
                                        <img
                                            src="<?= htmlspecialchars($user->photo_url) ?>"
                                            alt="<?= $user->displayFullName() ?>"
                                            class="rounded-circle"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    <?php else: ?>
                                        <div
                                            class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-person-fill text-white" style="font-size: 40px;"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <!-- 사용자 정보 -->
                            <h5 class="card-title text-center mb-2">
                                <a href="?page=user/profile&id=<?= $user->id ?>" class="text-decoration-none">
                                    <?= $user->displayFullName() ?>
                                </a>
                            </h5>

                            <div class="text-center text-muted small">
                                <!-- 성별 -->
                                <span class="me-2">
                                    <i class="bi bi-gender-<?= $user->gender === 'M' ? 'male' : 'female' ?>"></i>
                                    <?= $user->gender === 'M' ? '남성' : '여성' ?>
                                </span>

                                <!-- 나이 -->
                                <?php if ($age !== null): ?>
                                    <span>
                                        <i class="bi bi-calendar-check"></i>
                                        <?= $age ?>세
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- 가입일 -->
                            <div class="text-center text-muted small mt-2">
                                <i class="bi bi-clock"></i>
                                가입일: <?= is_numeric($user->created_at) ? date('Y-m-d', (int)$user->created_at) : $user->created_at ?>
                            </div>
                        </div>

                        <!-- 카드 하단 액션 -->
                        <div class="card-footer bg-transparent border-top-0 text-center">
                            <a href="?page=user/profile&id=<?= $user->id ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person-circle me-1"></i>프로필 보기
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php basic_pagination($page, $total_pages, 7); ?>
    <?php endif; ?>
</div>