<?php

/**
 * 사용자 프로필 페이지
 *
 * 다른 사용자의 프로필을 조회하는 페이지입니다.
 * URL 파라미터로 사용자 ID를 받아서 해당 사용자의 정보를 표시합니다.
 */

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

// 다국어 텍스트 주입
inject_user_profile_language();
load_page_css();

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

<style>
    /* 프로필 커버 영역 */
    .profile-cover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 200px;
        border-radius: 8px;
        position: relative;
        margin-bottom: 80px;
    }

    /* 프로필 사진 래퍼 */
    .profile-photo-wrapper {
        position: absolute;
        bottom: -60px;
        left: 50%;
        transform: translateX(-50%);
    }

    /* 프로필 사진 */
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* 프로필 사진 플레이스홀더 */
    .profile-photo-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: white;
        border: 4px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-photo-placeholder i {
        font-size: 60px;
        color: #e4e6eb;
    }

    /* 프로필 정보 섹션 */
    .profile-info-section {
        background-color: white;
        border-radius: 8px;
        margin-top: 1rem;
    }

    /* 프로필 이름 */
    .profile-name {
        font-size: 2rem;
        font-weight: 700;
        color: #050505;
        margin: 0;
    }

    /* 프로필 메타 정보 */
    .profile-meta {
        font-size: 0.95rem;
    }

    /* 상세 정보 항목 */
    .detail-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* 상세 정보 아이콘 */
    .detail-icon {
        font-size: 20px;
        color: var(--bs-primary);
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* 상세 정보 내용 */
    .detail-content {
        flex: 1;
    }

    /* 상세 정보 라벨 */
    .detail-label {
        font-size: 0.875rem;
        color: #65676b;
        margin-bottom: 4px;
    }

    /* 상세 정보 값 */
    .detail-value {
        font-size: 1rem;
        color: #050505;
        font-weight: 500;
    }

    /* 반응형 디자인 */
    @media (max-width: 768px) {
        .profile-cover {
            height: 150px;
            margin-bottom: 70px;
        }

        .profile-photo-wrapper {
            bottom: -50px;
        }

        .profile-photo,
        .profile-photo-placeholder {
            width: 100px;
            height: 100px;
        }

        .profile-photo-placeholder i {
            font-size: 50px;
        }

        .profile-info-section {
            padding: 1.5rem 1rem;
        }

        .profile-name {
            font-size: 1.5rem;
        }
    }
</style>

<div id="profile-component" class="container py-4">
    <!-- 커버 영역 -->
    <div class="profile-cover">
        <!-- 프로필 사진 -->
        <div class="profile-photo-wrapper">
            <?php if (!empty($user->photo_url)): ?>
                <img src="<?= $user->photo_url ?>" alt="<?= $user->displayFullName() ?>" class="profile-photo">
            <?php else: ?>
                <div class="profile-photo-placeholder">
                    <i class="fa-solid fa-user"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 프로필 정보 영역 -->
    <div class="profile-info-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <!-- 이름 및 기본 정보 -->
            <div>
                <h1 class="profile-name mb-1"><?= $user->displayFullName() ?></h1>
                <p class="profile-meta text-muted mb-0">ID: <?= $user->id ?></p>
            </div>

            <!-- 액션 버튼 -->
            <div>
                <?php if ($is_me): ?>
                    <!-- 본인인 경우: 프로필 수정 버튼 -->
                    <a href="<?= href()->user->profile_edit ?>" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square me-2"></i><?= t()->프로필_수정 ?>
                    </a>
                <?php else: ?>
                    <!-- 다른 사용자인 경우: 친구 추가 버튼 (Vue.js) -->
                    <button @click="requestFriend(<?= $user->id ?>)"
                        class="btn btn-primary"
                        :disabled="requesting || isFriend"
                        v-cloak>
                        <span v-if="requesting">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <?= t()->요청_중 ?>
                        </span>
                        <span v-else-if="isFriend">
                            <i class="fa-solid fa-check me-2"></i><?= t()->친구_요청_전송_완료 ?>
                        </span>
                        <span v-else>
                            <i class="fa-solid fa-user-plus me-2"></i><?= t()->친구_추가 ?>
                        </span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- 구분선 -->
        <hr class="my-3">

        <!-- 상세 정보 -->
        <div class="profile-details">
            <div class="row g-3">
                <!-- 성별 정보 -->
                <?php if (!empty($user->gender)): ?>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <i class="fa-solid fa-venus-mars detail-icon"></i>
                            <div class="detail-content">
                                <div class="detail-label"><?= tr(['en' => 'Gender', 'ko' => '성별']) ?></div>
                                <div class="detail-value"><?= htmlspecialchars($gender_text) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 생년월일 정보 -->
                <?php if ($user->birthday > 0): ?>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <i class="fa-solid fa-cake-candles detail-icon"></i>
                            <div class="detail-content">
                                <div class="detail-label"><?= tr(['en' => 'Birthday', 'ko' => '생년월일']) ?></div>
                                <div class="detail-value"><?= htmlspecialchars($birthday_formatted) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 가입일 정보 -->
                <div class="col-md-4">
                    <div class="detail-item">
                        <i class="fa-solid fa-clock detail-icon"></i>
                        <div class="detail-content">
                            <div class="detail-label"><?= tr(['en' => 'Joined', 'ko' => '가입일']) ?></div>
                            <div class="detail-value"><?= htmlspecialchars($created_at_formatted) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    /**
     * 프로필 페이지 JavaScript (Vue.js)
     *
     * 사용자 프로필 페이지의 친구 추가 기능을 처리합니다.
     */

    ready(() => {
        // Vue.js 프로필 앱 생성
        Vue.createApp({
            data() {
                console.log('window.Store in profile:', window.Store);
                return {
                    // 친구 요청 상태
                    requesting: false,
                    isFriend: false,
                    state: window.Store?.state || {},
                };
            },
            methods: {
                /**
                 * 친구 추가 요청
                 * @param {number} otherUserId - 친구 요청을 보낼 사용자 ID
                 */
                async requestFriend(otherUserId) {
                    console.log(window.Store);
                    // 로그인 확인 - window.Store.user에서 로그인한 사용자 정보 가져오기
                    if (!this.state?.user?.id) {
                        alert('<?= t()->로그인이_필요합니다 ?>');
                        window.location.href = '<?= href()->user->login ?>';
                        return;
                    }

                    const myUserId = this.state.user.id;

                    // 자기 자신에게 친구 요청 방지
                    if (otherUserId === myUserId) {
                        alert('<?= t()->자기_자신에게는_친구_요청을_보낼_수_없습니다 ?>');
                        return;
                    }

                    // 이미 친구인 경우
                    if (this.isFriend) {
                        alert('<?= t()->이미_친구입니다 ?>');
                        return;
                    }

                    try {
                        // 요청 중 상태 설정
                        this.requesting = true;

                        console.log(`친구 요청 전송: 사용자 ID ${otherUserId}`);

                        // API 호출: request_friend 함수 사용
                        await func('request_friend', {
                            me: myUserId,
                            other: otherUserId,
                            auth: true // Firebase 인증 포함
                        });

                        // 성공: 친구 상태 업데이트
                        this.isFriend = true;
                        this.requesting = false;

                        console.log(`친구 요청 성공: 사용자 ID ${otherUserId}`);
                        alert('<?= t()->친구_요청_전송_완료 ?>');

                    } catch (error) {
                        console.error('친구 요청 실패:', error);
                        this.requesting = false;

                        // 에러 메시지 표시
                        const errorMessage = error.message || error.error_message || '<?= t()->친구_요청에_실패했습니다 ?>';
                        alert(`<?= t()->친구_요청_실패 ?>: ${errorMessage}`);
                    }
                }
            },
            mounted() {
                console.log('[profile] Vue.js 프로필 페이지 초기화 완료');
            }
        }).mount('#profile-component');
    });
</script>