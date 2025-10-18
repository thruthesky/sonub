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

<div id="profile-app">
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

        <!-- 프로필 액션 버튼 -->
        <div class="profile-actions">
            <?php if ($is_me): ?>
                <!-- 본인인 경우: 프로필 수정 버튼 -->
                <a href="<?= href()->user->profile_edit ?>" class="btn-edit-profile">
                    <i class="bi bi-pencil-square me-2"></i><?= t()->프로필_수정 ?>
                </a>
            <?php else: ?>
                <!-- 다른 사용자인 경우: 친구 추가 버튼 (Vue.js) -->
                <button @click="requestFriend(<?= $user->id ?>)"
                        class="btn-add-friend"
                        :disabled="requesting || isFriend">
                    <span v-if="requesting">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <?= t()->요청_중 ?>
                    </span>
                    <span v-else-if="isFriend">
                        <i class="bi bi-check-circle me-2"></i><?= t()->친구_요청_전송_완료 ?>
                    </span>
                    <span v-else>
                        <i class="bi bi-person-plus me-2"></i><?= t()->친구_추가 ?>
                    </span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<?php
/**
 * 다국어 텍스트 주입 함수
 */
function inject_user_profile_language()
{
    t()->inject([
        '사용자 프로필' => ['ko' => '사용자 프로필', 'en' => 'User Profile', 'ja' => 'ユーザープロフィール', 'zh' => '用户资料'],
        '사용자 ID가 필요합니다.' => ['ko' => '사용자 ID가 필요합니다.', 'en' => 'User ID is required.', 'ja' => 'ユーザーIDが必要です。', 'zh' => '需要用户ID。'],
        '사용자를 찾을 수 없습니다.' => ['ko' => '사용자를 찾을 수 없습니다.', 'en' => 'User not found.', 'ja' => 'ユーザーが見つかりません。', 'zh' => '找不到用户。'],
        '성별' => ['ko' => '성별', 'en' => 'Gender', 'ja' => '性別', 'zh' => '性别'],
        '남성' => ['ko' => '남성', 'en' => 'Male', 'ja' => '男性', 'zh' => '男性'],
        '여성' => ['ko' => '여성', 'en' => 'Female', 'ja' => '女性', 'zh' => '女性'],
        '생년월일' => ['ko' => '생년월일', 'en' => 'Birthday', 'ja' => '生年月日', 'zh' => '出生日期'],
        '가입일' => ['ko' => '가입일', 'en' => 'Joined', 'ja' => '登録日', 'zh' => '注册日期'],
        '프로필_수정' => ['ko' => '프로필 수정', 'en' => 'Edit Profile', 'ja' => 'プロフィール編集', 'zh' => '编辑资料'],
        '정보 없음' => ['ko' => '정보 없음', 'en' => 'Not provided', 'ja' => '情報なし', 'zh' => '无信息'],
        '친구_추가' => ['ko' => '친구 추가', 'en' => 'Add Friend', 'ja' => '友達追加', 'zh' => '添加好友'],
        '요청_중' => ['ko' => '요청 중...', 'en' => 'Requesting...', 'ja' => 'リクエスト中...', 'zh' => '请求中...'],
        '친구_요청_전송_완료' => ['ko' => '친구 요청을 보냈습니다.', 'en' => 'Friend request sent.', 'ja' => '友達リクエストを送信しました。', 'zh' => '已发送好友请求。'],
        '오류_발생' => ['ko' => '오류가 발생했습니다.', 'en' => 'An error occurred.', 'ja' => 'エラーが発生しました。', 'zh' => '发生错误。'],
        '로그인이_필요합니다' => ['ko' => '로그인이 필요합니다.', 'en' => 'Login required.', 'ja' => 'ログインが必要です。', 'zh' => '需要登录。'],
        '자기_자신에게는_친구_요청을_보낼_수_없습니다' => ['ko' => '자기 자신에게는 친구 요청을 보낼 수 없습니다.', 'en' => 'You cannot send a friend request to yourself.', 'ja' => '自分自身にはフレンドリクエストを送信できません。', 'zh' => '您不能向自己发送好友请求。'],
        '이미_친구입니다' => ['ko' => '이미 친구입니다.', 'en' => 'Already friends.', 'ja' => 'すでに友達です。', 'zh' => '已经是朋友了。'],
        '친구_요청에_실패했습니다' => ['ko' => '친구 요청에 실패했습니다.', 'en' => 'Friend request failed.', 'ja' => 'フレンドリクエストに失敗しました。', 'zh' => '好友请求失败。'],
        '친구_요청_실패' => ['ko' => '친구 요청 실패', 'en' => 'Friend request failed', 'ja' => 'フレンドリクエスト失敗', 'zh' => '好友请求失败'],
    ]);
}
?>