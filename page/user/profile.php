<?php

/**
 * 사용자 프로필 페이지
 *
 * 다른 사용자의 프로필을 조회하는 페이지입니다.
 * URL 파라미터로 사용자 ID를 받아서 해당 사용자의 정보를 표시합니다.
 */

// 다국어 텍스트 주입
inject_user_profile_language();

// 사용자 ID 가져오기: URL 파라미터 'id' 또는 로그인한 사용자의 ID
$user_id = http_param('id') ?? login()->id ?? 0;


// 사용자 정보가 없으면 에러 표시
if (!$user_id) {
    echo '<div class="container mt-5"><div class="alert alert-danger">' . tr(['en' => 'User ID is required.', 'ko' => '사용자 ID가 필요합니다.']) . '</div></div>';
    return;
}

// 사용자 정보 로드
$user_data = get_user(['id' => $user_id]);

// 사용자를 찾을 수 없는 경우
if (isset($user_data['error_code'])) {
    return display_error(tr(['en' => 'User not found.', 'ko' => '사용자를 찾을 수 없습니다.']));
}

// UserModel 객체 생성
$user = new UserModel($user_data);

// 생년월일 포맷팅
$birthday_formatted = '';
if ($user->birthday > 0) {
    $birthday_formatted = date('Y년 m월 d일', $user->birthday);
}

// 성별 표시
$gender_text = '';
if ($user->gender === 'M') {
    $gender_text = tr(['en' => 'Male', 'ko' => '남성']);
} elseif ($user->gender === 'F') {
    $gender_text = tr(['en' => 'Female', 'ko' => '여성']);
}

// 가입일 포맷팅
$created_at_formatted = '';
if (!empty($user->created_at)) {
    $created_at_formatted = date('Y년 m월 d일', strtotime($user->created_at));
}

// 로그인한 사용자 본인인지 확인
$is_me = login() && login()->id === $user->id;
?>

<div class="profile-container">
    <div class="profile-card">
        <!-- 프로필 헤더 -->
        <div class="profile-header">
            <!-- 프로필 사진 -->
            <?php if (!empty($user->photo_url)): ?>
                <img src="<?= $user->photo_url ?>" alt="<?= htmlspecialchars($user->display_name) ?>" class="profile-photo">
            <?php else: ?>
                <div class="profile-photo-placeholder">
                    <i class="bi bi-person-fill"></i>
                </div>
            <?php endif; ?>

            <!-- 표시 이름 -->
            <h1 class="profile-display-name"><?= htmlspecialchars($user->display_name) ?></h1>

            <!-- 사용자 ID -->
            <p class="profile-user-id">ID: <?= $user->id ?></p>
        </div>

        <!-- 프로필 본문 -->
        <div class="profile-body">
            <!-- 성별 정보 -->
            <?php if (!empty($user->gender)): ?>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-gender-ambiguous"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label"><?= tr(['en' => 'Gender', 'ko' => '성별']) ?></div>
                        <div class="info-value"><?= htmlspecialchars($gender_text) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 생년월일 정보 -->
            <?php if ($user->birthday > 0): ?>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label"><?= tr(['en' => 'Birthday', 'ko' => '생년월일']) ?></div>
                        <div class="info-value"><?= htmlspecialchars($birthday_formatted) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 가입일 정보 -->
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="info-content">
                    <div class="info-label"><?= tr(['en' => 'Joined', 'ko' => '가입일']) ?></div>
                    <div class="info-value"><?= htmlspecialchars($created_at_formatted) ?></div>
                </div>
            </div>
        </div>

        <!-- 프로필 액션 버튼 (본인인 경우에만 표시) -->
        <?php if ($is_me): ?>
            <div class="profile-actions">
                <a href="<?= href()->user->profile_edit ?>" class="btn-edit-profile">
                    <i class="bi bi-pencil-square me-2"></i><?= tr(['en' => 'Edit Profile', 'ko' => '프로필 수정']) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * 다국어 텍스트 주입 함수
 */
function inject_user_profile_language()
{
    t()->inject([
        '사용자 프로필' => 'User Profile',
        '사용자 ID가 필요합니다.' => 'User ID is required.',
        '사용자를 찾을 수 없습니다.' => 'User not found.',
        '성별' => 'Gender',
        '남성' => 'Male',
        '여성' => 'Female',
        '생년월일' => 'Birthday',
        '가입일' => 'Joined',
        '프로필 수정' => 'Edit Profile',
        '정보 없음' => 'Not provided',
    ]);
}
?>